/*!
 * Interactive floor plan — public hero UI.
 *
 * One FloorPlanViewer per floor (stacked layers in one stage). This file wires
 * them to the card, arrows, keyboard, the hint, the ?area= / ?floor= URL and the
 * floor change (dragging / zooming the plan itself lives in viewer.js): click the floor title or the staircase and the camera walks
 * into the stairs, the floor drops away, the next floor arrives at its own
 * stairwell and the camera pulls back to show the whole floor.
 */
(function (root) {
	"use strict";

	var C = root.FloorPlanCore;
	var Viewer = root.FloorPlanViewer;
	if (!C || !Viewer) { return; }

	var P = C.PHASE;

	/* floor change timing (ms) */
	var LEAVE_MS = 850;      /* camera walks into the stairs */
	var SWAP_AT_MS = 560;    /* when the next floor starts to arrive (walk ~80% done) */
	var ARRIVE_MS = 1100;    /* camera pulls back on the new floor */
	var STAIR_ZOOM = 3.2;    /* how close to the stairs (× the overview) */

	function init(hero) {
		if (hero.getAttribute("data-fp-ready") === "1") { return; }

		var stage = hero.querySelector("[data-fp-stage]");
		var dataEl = hero.querySelector("[data-fp-data]");
		if (!stage || !dataEl) { return; }

		var data;
		try {
			data = JSON.parse(dataEl.textContent);
		} catch (err) {
			return;
		}
		if (!data || !Array.isArray(data.floors) || data.floors.length === 0) { return; }

		hero.setAttribute("data-fp-ready", "1");

		var fallback = hero.querySelector("[data-fp-fallback]");
		if (fallback && fallback.parentNode) { fallback.parentNode.removeChild(fallback); }

		var floors = data.floors;
		var info = hero.querySelector("[data-fp-info]");
		var elEn = hero.querySelector("[data-fp-en]");
		var elMn = hero.querySelector("[data-fp-mn]");
		var elDesc = hero.querySelector("[data-fp-desc]");
		var btnPrev = hero.querySelector("[data-fp-prev]");
		var btnNext = hero.querySelector("[data-fp-next]");
		var btnBack = hero.querySelector("[data-fp-back]");
		var titleBtn = hero.querySelector("[data-fp-floor-switch]");
		var titleText = hero.querySelector("[data-fp-title]");
		var titleGo = hero.querySelector("[data-fp-title-go]");
		var titleNext = hero.querySelector("[data-fp-title-next]");
		var announce = hero.querySelector("[data-fp-announce]");
		var layers = Array.prototype.slice.call(stage.querySelectorAll("[data-fp-floor]"));

		/* ---------------- floors ---------------- */

		function floorIndexOfSlug(slug) {
			for (var i = 0; i < floors.length; i++) {
				var hs = floors[i].hotspots || [];
				for (var j = 0; j < hs.length; j++) {
					if (hs[j].slug === slug && hs[j].enabled !== false) { return i; }
				}
			}
			return -1;
		}

		function floorIndexOfParam(v) {
			for (var i = 0; i < floors.length; i++) {
				if (String(floors[i].number) === v || floors[i].key === v) { return i; }
			}
			return -1;
		}

		/** Where the stairs / title lead from floor i: up if there is a floor above, else down. */
		function targetOf(i) {
			if (floors.length < 2) { return -1; }
			return i + 1 < floors.length ? i + 1 : i - 1;
		}

		function dirTo(from, to) {
			var a = parseFloat(floors[from].number);
			var b = parseFloat(floors[to].number);
			if (isFinite(a) && isFinite(b)) { return b > a ? "up" : "down"; }
			return to > from ? "up" : "down";
		}

		function linkLabel(i) {
			var t = targetOf(i);
			if (t < 0) { return null; }
			var dir = dirTo(i, t);
			return {
				text: String(floors[t].number),
				dir: dir,
				aria: floors[t].floorTitle + " руу шат / Stairs to floor " + floors[t].number
			};
		}

		/* ---------------- deep link ---------------- */

		var params = null;
		try { params = new URLSearchParams(root.location.search); } catch (e) { /* old browser */ }
		var initialArea = params ? params.get("area") : null;
		var active = 0;
		var areaFloor = initialArea ? floorIndexOfSlug(initialArea) : -1;
		if (areaFloor >= 0) {
			active = areaFloor;
		} else if (params && params.get("floor")) {
			var pf = floorIndexOfParam(params.get("floor"));
			if (pf >= 0) { active = pf; }
		}

		/* ---------------- images ---------------- */

		var hiLoaded = {};

		function imgOf(i) { return layers[i].querySelector("img"); }

		function ensureImage(i) {
			var img = imgOf(i);
			var src = img.getAttribute("data-src");
			if (src && !img.getAttribute("src")) {
				img.setAttribute("src", src);
				img.removeAttribute("data-src");
			}
			watchImage(i);
		}

		function watchImage(i) {
			var img = imgOf(i);
			var layer = layers[i];
			if (layer.__fpWatched) { return; }
			layer.__fpWatched = true;
			if (!img.getAttribute("src")) { layer.__fpWatched = false; return; }
			var ready = function () { layer.classList.add("is-ready"); };
			var failed = function () { layer.classList.add("is-failed"); };
			if (img.complete && img.naturalWidth > 0) {
				ready();
			} else if (img.complete) {
				failed();
			} else {
				img.addEventListener("load", ready);
				img.addEventListener("error", failed);
			}
		}

		function hiUrl(i) {
			var p = floors[i];
			var coarse = root.matchMedia && root.matchMedia("(pointer: coarse)").matches;
			var mem = root.navigator.deviceMemory;
			var light = coarse || root.innerWidth < 900 || (mem && mem <= 4);
			return (light ? (p.imageMidUrl || p.imageFullUrl) : (p.imageFullUrl || p.imageMidUrl)) || "";
		}

		/** Swap to the sharp image once it is decoded (same aspect ratio -> no jump). */
		function loadHiRes(i) {
			if (hiLoaded[i]) { return; }
			hiLoaded[i] = true;
			var img = imgOf(i);
			var url = hiUrl(i);
			if (!url || url === img.getAttribute("src")) { return; }
			var hi = new Image();
			hi.decoding = "async";
			hi.onload = function () {
				var swap = function () {
					img.src = url;
					layers[i].classList.add("is-hires");
				};
				if (hi.decode) { hi.decode().then(swap, swap); } else { swap(); }
			};
			hi.onerror = function () { /* keep the preview; never break the page */ };
			hi.src = url;
		}

		var saveData = root.navigator.connection && root.navigator.connection.saveData;
		function whenIdle(fn, timeout) {
			if (root.requestIdleCallback) { root.requestIdleCallback(fn, { timeout: timeout || 3000 }); } else { root.setTimeout(fn, 1200); }
		}

		/* ---------------- viewers ---------------- */

		/* the server marks floor 0 active; a deep link may start on another floor */
		layers.forEach(function (layer, i) {
			layer.classList.toggle("is-active", i === active);
			if (i === active) { layer.removeAttribute("aria-hidden"); } else { layer.setAttribute("aria-hidden", "true"); }
		});
		ensureImage(active);

		var viewers = floors.map(function (f, i) {
			var v = new Viewer(stage, {
				layer: layers[i],
				plan: f,
				hotspots: f.hotspots || [],
				initialSlug: i === active && areaFloor === active ? initialArea : null,
				linkLabel: linkLabel(i)
			});
			v.on("state", function (s, prev) { if (i === active) { onState(s, prev); } });
			v.on("link", function () { if (i === active) { switchTo(targetOf(i)); } });
			return v;
		});
		hero.__fpViewers = viewers;
		hero.__fpViewer = viewers[active];

		function cur() { return viewers[active]; }

		var firstImg = imgOf(active);
		var afterFirstPaint = function () {
			if (!saveData) { whenIdle(function () { loadHiRes(active); }); }
			/* the other floors' previews, so the floor change never waits on the network */
			whenIdle(function () { for (var k = 0; k < floors.length; k++) { ensureImage(k); } }, 4000);
		};
		if (firstImg.complete) { afterFirstPaint(); } else { firstImg.addEventListener("load", afterFirstPaint, { once: true }); }

		/* ---------------- title / floor indicator ---------------- */

		function updateTitle() {
			titleText.textContent = floors[active].floorTitle;
			var t = targetOf(active);
			if (t < 0) {
				titleBtn.disabled = true;
				if (titleGo) { titleGo.hidden = true; }
				return;
			}
			titleBtn.disabled = false;
			titleGo.hidden = false;
			titleGo.setAttribute("data-dir", dirTo(active, t));
			titleNext.textContent = floors[t].number;
			titleBtn.setAttribute("aria-label", floors[active].floorTitle + " — " + floors[t].floorTitle + " руу шилжих / go to floor " + floors[t].number);
		}

		/* ---------------- info card ---------------- */

		var swapTimer = 0;
		var shownSlug = null;

		/* English page: English description (falls back to Mongolian), and vice versa */
		var langEn = data.lang === "en";
		function describe(h) {
			var a = langEn ? h.descriptionEn : h.descriptionMn;
			var b = langEn ? h.descriptionMn : h.descriptionEn;
			return a || b || "";
		}

		function fillInfo(slug) {
			var h = cur().getHotspot(slug);
			if (!h || !elEn) { return; }
			var desc = describe(h);
			elEn.textContent = h.titleEn;
			elMn.textContent = h.titleMn;
			elDesc.textContent = desc;
			elEn.hidden = h.titleEn === "";
			elMn.hidden = h.titleMn === "";
			elDesc.hidden = desc === "";
			shownSlug = slug;
		}

		function showInfo(slug, swap) {
			root.clearTimeout(swapTimer);
			if (swap && shownSlug && shownSlug !== slug) {
				info.classList.add("is-swap");
				swapTimer = root.setTimeout(function () {
					fillInfo(slug);
					info.classList.remove("is-swap");
				}, 230);
			} else {
				info.classList.remove("is-swap");
				fillInfo(slug);
			}
		}

		/* ---------------- URL ---------------- */

		function syncUrl() {
			try {
				var u = new URL(root.location.href);
				var s = cur().getState();
				if (C.isControlsVisible(s) && s.slug) {
					u.searchParams.set("area", s.slug);
					u.searchParams.delete("floor");
				} else {
					u.searchParams.delete("area");
					if (active > 0) { u.searchParams.set("floor", floors[active].number); } else { u.searchParams.delete("floor"); }
				}
				root.history.replaceState(root.history.state, "", u.pathname + u.search + u.hash);
			} catch (e) { /* ignore */ }
		}

		/* ---------------- state -> UI ---------------- */

		var lastSlug = null;

		function onState(s, prev) {
			var room = C.isControlsVisible(s);
			hero.classList.toggle("is-room", room);
			hero.setAttribute("data-fp-phase", s.phase);

			if (room) {
				loadHiRes(active);
				showInfo(s.slug, prev.phase === P.TRANSITIONING_ROOM || prev.phase === P.FOCUSED || prev.phase === P.FOCUSING);
				lastSlug = s.slug;
				syncUrl();
			} else if (s.phase === P.RETURNING || s.phase === P.OVERVIEW) {
				syncUrl();
				if (s.phase === P.RETURNING) {
					/* the arrows are about to disappear: hand focus back to the hotspot */
					var activeEl = document.activeElement;
					var node = lastSlug ? cur().getNode(lastSlug) : null;
					if (node && (!activeEl || activeEl === document.body || hero.contains(activeEl))) {
						try { node.focus({ preventScroll: true }); } catch (e) { /* ignore */ }
					}
				}
			}
		}

		/* deep link: the state starts FOCUSED without a transition */
		if (cur().getState().phase === P.FOCUSED) {
			hero.classList.add("is-room");
			hero.setAttribute("data-fp-phase", P.FOCUSED);
			fillInfo(cur().getState().slug);
			lastSlug = cur().getState().slug;
			loadHiRes(active);
		}
		updateTitle();

		/* ---------------- floor change ---------------- */

		var switching = false;

		function setLayerClasses(layer, add, remove) {
			remove.forEach(function (c) { layer.classList.remove(c); });
			add.forEach(function (c) { layer.classList.add(c); });
		}

		function reducedMotion() {
			return !!(root.matchMedia && root.matchMedia("(prefers-reduced-motion: reduce)").matches);
		}

		function finishSwitch(from, to) {
			var out = layers[from];
			setLayerClasses(out, [], ["is-leaving", "is-arriving", "is-active"]);
			out.removeAttribute("data-dir");
			out.setAttribute("aria-hidden", "true");
			viewers[from].panX = null;
			viewers[from].resetToOverview();
			viewers[from].setCamera(viewers[from].overviewCamera());

			setLayerClasses(layers[to], [], ["is-arriving"]);
			layers[to].removeAttribute("data-dir");
			hero.classList.remove("is-switching", "is-floor-in");
			switching = false;
			syncUrl();
		}

		/**
		 * Walk to the other floor: camera into the stairs -> the floor drops
		 * away (going up) / rises away (going down) while the next floor
		 * arrives at its stairwell -> the camera pulls back to its overview.
		 */
		function switchTo(to) {
			if (switching || to < 0 || to >= floors.length || to === active) { return false; }
			switching = true;

			var from = active;
			var dir = dirTo(from, to);
			var vOut = viewers[from];
			var vIn = viewers[to];
			var hadFocus = hero.contains(document.activeElement);

			ensureImage(to);
			loadHiRes(to);

			/* leaving: close any room */
			vOut.resetToOverview();
			hero.classList.remove("is-room");
			hero.setAttribute("data-fp-phase", P.OVERVIEW);
			hero.classList.add("is-switching");

			active = to;
			hero.__fpViewer = vIn;

			var outLayer = layers[from];
			var inLayer = layers[to];

			/* arriving floor: prepare at its own stairs, hidden */
			vIn.panX = null;
			vIn.relayout();
			vIn.resetToOverview();

			if (reducedMotion()) {
				updateTitle();
				if (announce) { announce.textContent = floors[to].floorTitle; }
				vIn.setCamera(vIn.overviewCamera());
				setLayerClasses(inLayer, ["is-active"], []);
				inLayer.removeAttribute("aria-hidden");
				finishSwitch(from, to);
				if (hadFocus) { focusTitle(); }
				return true;
			}

			vIn.setCamera(vIn.linkCamera(STAIR_ZOOM) || vIn.overviewCamera());

			/* 1. walk into the stairs */
			var leaving = vOut.flyTo(function () { return vOut.linkCamera(STAIR_ZOOM) || vOut.overviewCamera(); }, LEAVE_MS, { rho: 1.0, ease: C.easeInOutCubic });

			/* 2. the floors pass each other; the title changes with them */
			root.setTimeout(function () {
				updateTitle();
				hero.classList.add("is-floor-in");
				if (announce) { announce.textContent = floors[to].floorTitle; }
				outLayer.setAttribute("data-dir", dir);
				inLayer.setAttribute("data-dir", dir);
				setLayerClasses(inLayer, ["is-arriving"], []);
				inLayer.removeAttribute("aria-hidden");
				/* start position applied, then animate to rest */
				void inLayer.offsetWidth;
				setLayerClasses(outLayer, ["is-leaving"], ["is-active"]);
				setLayerClasses(inLayer, ["is-active"], []);

				/* 3. pull back to the whole floor */
				var arriving = vIn.flyTo(function () { return vIn.overviewCamera(); }, ARRIVE_MS, { rho: 1.2, ease: C.easeInOutQuint });

				Promise.all([leaving, arriving]).then(function () {
					finishSwitch(from, to);
					if (hadFocus) { focusTitle(); }
				});
			}, SWAP_AT_MS);

			return true;
		}

		function focusTitle() {
			try { titleBtn.focus({ preventScroll: true }); } catch (e) { /* ignore */ }
		}

		titleBtn.addEventListener("click", function () { switchTo(targetOf(active)); });

		/* ---------------- arrows ---------------- */

		if (btnPrev) { btnPrev.addEventListener("click", function () { cur().prev(); }); }
		if (btnNext) { btnNext.addEventListener("click", function () { cur().next(); }); }
		if (btnBack) { btnBack.addEventListener("click", function () { cur().back(); }); }

		/* ---------------- keyboard ---------------- */

		var inView = true;
		if (root.IntersectionObserver) {
			new root.IntersectionObserver(function (entries) {
				inView = entries[entries.length - 1].isIntersecting;
			}, { threshold: 0.2 }).observe(hero);
		}

		function isTyping(el) {
			if (!el || !el.tagName) { return false; }
			var tag = el.tagName.toLowerCase();
			return tag === "input" || tag === "textarea" || tag === "select" || el.isContentEditable === true;
		}

		document.addEventListener("keydown", function (e) {
			if (e.defaultPrevented || e.ctrlKey || e.metaKey || e.altKey || e.shiftKey) { return; }
			if (isTyping(e.target) || isTyping(document.activeElement) || switching) { return; }

			/* floors: only while the visitor is inside the hero, never hijack page scrolling */
			if ((e.key === "PageUp" || e.key === "PageDown") && hero.contains(document.activeElement)) {
				var t = e.key === "PageUp" ? active + 1 : active - 1;
				if (t >= 0 && t < floors.length) {
					switchTo(t);
					e.preventDefault();
				}
				return;
			}

			if (!C.isControlsVisible(cur().getState())) { return; }
			if (!inView && !hero.contains(document.activeElement)) { return; }

			switch (e.key) {
				case "ArrowRight": cur().next(); break;
				case "ArrowLeft": cur().prev(); break;
				case "ArrowDown":
				case "Escape": cur().back(); break;
				default: return;
			}
			e.preventDefault();
		});

		/* ---------------- hint: shown until the first drag / zoom ---------------- */

		var hint = hero.querySelector("[data-fp-hint]");
		var hintText = hero.querySelector("[data-fp-hint-text]");
		if (hint && hintText) {
			var touchFirst = root.matchMedia && root.matchMedia("(pointer: coarse)").matches;
			hintText.textContent = touchFirst
				? "Чирж хөдөлгөх · Хоёр хуруугаар томруулах"
				: "Чирж хөдөлгөх · Гүйлгэж томруулах";
		}
		function hideHint() { if (hint) { hint.classList.add("is-gone"); } }
		viewers.forEach(function (v) {
			v.on("gesture", hideHint);
			v.on("select", hideHint);
		});
		titleBtn.addEventListener("click", hideHint);

		syncUrl();
	}

	function boot() {
		var nodes = document.querySelectorAll("[data-fp-root]");
		for (var i = 0; i < nodes.length; i++) { init(nodes[i]); }
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", boot);
	} else {
		boot();
	}
})(window);
