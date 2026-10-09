/*!
 * Open Office Day — зочдын тур хуудас (/openday).
 *
 *  - Хэл солих (MN / EN): хуудсыг дахин ачаалахгүй, сонголтыг санана.
 *  - "Одоо / дараа нь": өдөрлөгийн өдөр хөтөлбөрийг Улаанбаатарын цагаар
 *    (UTC+8) харьцуулж, одоо болж буйг тодруулна. Шалгахдаа ?at=12:10
 *    (эсвэл ?at=2026-10-17T12:10) гэж цагийг дуурайж болно.
 *  - Байршлын товч (data-od-spot): схем рүү гүйлгэж, хэрэгтэй бол давхраа
 *    сольж, тухайн цэг рүү ойртоно. Схем нь Офис хуудасны viewer
 *    (assets/js/floorplan/public.js) — тэр файлыг өөрчлөөгүй, түүний
 *    нийтийн шинжүүдийг (hero.__fpViewers, давхрын товч) ашиглана.
 *  - Дээд цэс: аль хэсэгт байгааг тодруулна.
 */
(function (root) {
	"use strict";

	var doc = root.document;
	var html = doc.documentElement;

	var data = {};
	try {
		var dataEl = doc.querySelector("[data-od-data]");
		data = dataEl ? JSON.parse(dataEl.textContent) || {} : {};
	} catch (e) {
		data = {};
	}
	var spots = data.spots || {};

	function reducedMotion() {
		return !!(root.matchMedia && root.matchMedia("(prefers-reduced-motion: reduce)").matches);
	}

	/** Хоёр хэлтэй HTML (хэл солиход CSS нэгийг нь нууна). */
	function bi(mn, en) {
		return '<span class="od-mn">' + esc(mn) + '</span><span class="od-en" lang="en">' + esc(en || mn) + "</span>";
	}

	function esc(s) {
		return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	}

	/* ================================================================
	   Хэл
	   ================================================================ */

	function lang() {
		return html.getAttribute("data-lang") === "en" ? "en" : "mn";
	}

	function setLang(l) {
		l = l === "en" ? "en" : "mn";
		html.setAttribute("data-lang", l);
		html.lang = l;

		try { root.localStorage.setItem("od-lang", l); } catch (e) { /* хувийн цонх */ }

		/* хуваалцсан линк ч мөн тэр хэлээр нээгдэнэ */
		try {
			var u = new URL(root.location.href);
			if (l === "en") { u.searchParams.set("lang", "en"); } else { u.searchParams.delete("lang"); }
			root.history.replaceState(root.history.state, "", u.pathname + u.search + u.hash);
		} catch (e) { /* хуучин хөтөч */ }

		mapLang();
	}

	var langBtn = doc.querySelector("[data-od-lang]");
	if (langBtn) {
		langBtn.addEventListener("click", function () {
			setLang(lang() === "en" ? "mn" : "en");
		});
	}

	/* ================================================================
	   Цаг: одоо / дараа нь
	   ================================================================ */

	var UB_OFFSET = 8 * 3600 * 1000;   /* Улаанбаатар = UTC+8, зуны цаггүй */
	var loadedAt = Date.now();
	var fakeNow = null;

	function parseDate(s) {
		var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s || "");
		return m ? [+m[1], +m[2], +m[3]] : null;
	}

	/** "HH:MM" -> тухайн өдрийн Улаанбаатарын цаг (ms, UTC). */
	function at(dateParts, hhmm) {
		var m = /^(\d{2}):(\d{2})$/.exec(hhmm || "");
		if (!dateParts || !m) { return null; }
		return Date.UTC(dateParts[0], dateParts[1] - 1, dateParts[2], +m[1], +m[2]) - UB_OFFSET;
	}

	var eventDay = parseDate(data.date);

	(function readFakeNow() {
		/* ":" нь "%3A" болж ирж болно */
		var q = /[?&]at=([^&#]+)/.exec(root.location.search);
		if (!q) { return; }
		var v = "";
		try { v = decodeURIComponent(q[1]); } catch (e) { return; }
		var full = /^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})$/.exec(v);
		if (full) {
			fakeNow = at(parseDate(full[1]), full[2]);
		} else if (eventDay) {
			fakeNow = at(eventDay, v);
		}
	})();

	function now() {
		return fakeNow === null ? Date.now() : fakeNow + (Date.now() - loadedAt);
	}

	/** Мөр бүрийн [эхлэх, дуусах) хугацаа. Дуусах цаггүй бол дараагийн мөр хүртэл. */
	function windows(list, dayEnd) {
		var out = [];
		for (var i = 0; i < list.length; i++) {
			var s = at(eventDay, list[i].start);
			var e = at(eventDay, list[i].end);
			if (s === null) { out.push(null); continue; }
			if (e === null || e <= s) {
				e = null;
				for (var j = i + 1; j < list.length; j++) {
					var n = at(eventDay, list[j].start);
					if (n !== null && n > s) { e = n; break; }
				}
				if (e === null) { e = dayEnd !== null && dayEnd > s ? dayEnd : s + 30 * 60 * 1000; }
			}
			out.push({ s: s, e: e });
		}
		return out;
	}

	var agendaRows = Array.prototype.slice.call(doc.querySelectorAll(".od-ag[data-od-i]"));
	var stepRows = Array.prototype.slice.call(doc.querySelectorAll(".od-step[data-od-act]"));
	var live = doc.querySelector("[data-od-live]");

	function setBadge(row, kind) {
		var b = row.querySelector("[data-od-badge]");
		if (!b) { return; }
		if (!kind) {
			b.hidden = true;
			b.className = "od-badge";
			return;
		}
		b.hidden = false;
		b.className = "od-badge" + (kind === "next" ? " is-next" : "");
		b.innerHTML = kind === "next" ? bi("Дараа нь", "Up next") : bi("Одоо", "Now");
	}

	function timeText(row) {
		var s = row.querySelector(".od-ag-start");
		var e = row.querySelector(".od-ag-end");
		return (s ? s.textContent : "") + (e && e.textContent ? " – " + e.textContent : "");
	}

	/** Хөтөлбөрийн мөрийг "одоо / дараа нь" хайрцагт хуулна (хоёр хэлтэй HTML-ээр нь). */
	function fillLiveRow(box, row) {
		var body = box.querySelector(".od-live-body");
		var title = row.querySelector(".od-ag-title");
		var where = row.querySelector(".od-where");
		body.innerHTML = '<span class="od-live-time">' + esc(timeText(row)) + "</span>" +
			'<span class="od-live-title">' + (title ? title.innerHTML : "") + "</span>" +
			(where ? where.outerHTML : "");
		box.hidden = false;
	}

	function minutesText(ms) {
		var min = Math.max(1, Math.round(ms / 60000));
		if (min < 60) { return { mn: min + " минут", en: min + " min" }; }
		var h = Math.floor(min / 60);
		var m = min % 60;
		return {
			mn: h + " цаг" + (m ? " " + m + " минут" : ""),
			en: h + " h" + (m ? " " + m + " min" : "")
		};
	}

	function tick() {
		var list = data.agenda || [];
		var dayStart = at(eventDay, data.start);
		var dayEnd = at(eventDay, data.end);
		var t = now();
		var win = eventDay ? windows(list, dayEnd) : [];

		var first = null;
		var last = null;
		win.forEach(function (w) {
			if (!w) { return; }
			if (first === null || w.s < first) { first = w.s; }
			if (last === null || w.e > last) { last = w.e; }
		});
		var openAt = dayStart !== null ? dayStart : first;
		var closeAt = dayEnd !== null ? Math.max(dayEnd, last || 0) : last;

		/* зөвхөн өдөрлөгийн өдөр (Улаанбаатарын цагаар) */
		var dayKey = function (ms) { return new Date(ms + UB_OFFSET).toISOString().slice(0, 10); };
		var isDay = !!eventDay && openAt !== null && dayKey(t) === data.date;

		var nowIdx = -1;
		var nextIdx = -1;

		agendaRows.forEach(function (row) {
			var i = +row.getAttribute("data-od-i");
			var w = win[i];
			var state = "";
			if (isDay && w) {
				if (t >= w.e) { state = "past"; } else if (t >= w.s) { state = "now"; }
			}
			if (state === "now" && nowIdx < 0) { nowIdx = i; }
			if (isDay && w && t < w.s && (nextIdx < 0 || w.s < win[nextIdx].s)) { nextIdx = i; }
			row.classList.toggle("is-past", state === "past");
			row.classList.toggle("is-now", state === "now");
		});

		agendaRows.forEach(function (row) {
			var i = +row.getAttribute("data-od-i");
			row.classList.toggle("is-next", i === nextIdx);
			setBadge(row, row.classList.contains("is-now") ? "now" : (i === nextIdx ? "next" : ""));
		});

		/* маршрутын алхам: цагтай бол тэр хугацаандаа "Одоо" */
		var actWin = eventDay ? windows(data.activity || [], dayEnd) : [];
		stepRows.forEach(function (row) {
			var i = +row.getAttribute("data-od-act");
			var a = (data.activity || [])[i];
			var w = actWin[i];
			var on = isDay && a && a.start && w && t >= w.s && t < w.e;
			row.classList.toggle("is-now", !!on);
			setBadge(row, on ? "now" : "");
		});

		if (!live) { return; }

		var msg = live.querySelector("[data-od-live-msg]");
		var boxNow = live.querySelector("[data-od-live-now]");
		var boxNext = live.querySelector("[data-od-live-next]");
		boxNow.hidden = true;
		boxNext.hidden = true;
		msg.hidden = true;

		if (!isDay) {
			live.hidden = true;
			return;
		}
		live.hidden = false;

		var rowOf = function (i) {
			for (var k = 0; k < agendaRows.length; k++) {
				if (+agendaRows[k].getAttribute("data-od-i") === i) { return agendaRows[k]; }
			}
			return null;
		};

		if (closeAt !== null && t >= closeAt) {
			msg.innerHTML = bi("Өдөрлөг өндөрлөлөө. Ирсэнд баярлалаа!", "The open day has ended. Thank you for coming!");
			msg.hidden = false;
			return;
		}

		if (t < openAt) {
			var left = minutesText(openAt - t);
			msg.innerHTML = bi("Өдөрлөг эхлэхэд " + left.mn + " үлдлээ.", "Starts in " + left.en + ".");
			msg.hidden = false;
		} else if (nowIdx < 0 && nextIdx >= 0) {
			msg.innerHTML = bi("Дараагийн хөтөлбөр удахгүй эхэлнэ.", "The next session starts soon.");
			msg.hidden = false;
		}

		if (nowIdx >= 0 && rowOf(nowIdx)) { fillLiveRow(boxNow, rowOf(nowIdx)); }
		if (nextIdx >= 0 && rowOf(nextIdx)) { fillLiveRow(boxNext, rowOf(nextIdx)); }
	}

	tick();
	root.setInterval(tick, 20000);
	doc.addEventListener("visibilitychange", function () { if (!doc.hidden) { tick(); } });

	/* ================================================================
	   Схем
	   ================================================================ */

	var hero = doc.querySelector(".od-fp[data-fp-root]");
	var fpFloors = [];
	try {
		var fpEl = hero ? hero.querySelector("[data-fp-data]") : null;
		fpFloors = fpEl ? (JSON.parse(fpEl.textContent).floors || []) : [];
	} catch (e) {
		fpFloors = [];
	}

	function viewers() {
		return hero && hero.__fpViewers ? hero.__fpViewers : null;
	}

	function spotText(slug) {
		var s = spots[slug] || {};
		var en = lang() === "en";
		return {
			title: en ? (s.titleEn || s.titleMn || "") : (s.titleMn || s.titleEn || ""),
			body: en ? (s.bodyEn || s.bodyMn || "") : (s.bodyMn || s.bodyEn || "")
		};
	}

	/** Схемийн цэгийн нэр, тайлбар, картыг сонгосон хэлээр солино. */
	function mapLang() {
		var vs = viewers();
		if (!vs) { return; }

		vs.forEach(function (v, i) {
			var list = ((fpFloors[i] || {}).hotspots || []).map(function (h) {
				var t = spotText(h.slug);
				var copy = {};
				for (var k in h) { if (Object.prototype.hasOwnProperty.call(h, k)) { copy[k] = h[k]; } }
				copy.titleEn = t.title;
				copy.titleMn = "";
				copy.descriptionMn = t.body;
				copy.descriptionEn = "";
				return copy;
			});
			v.setData(list);
		});

		/* нээлттэй карт */
		var st = currentViewer() ? currentViewer().getState() : null;
		if (st && st.slug && hero.classList.contains("is-room")) {
			var t = spotText(st.slug);
			var elT = hero.querySelector("[data-fp-en]");
			var elD = hero.querySelector("[data-fp-desc]");
			if (elT) { elT.textContent = t.title; elT.hidden = t.title === ""; }
			if (elD) { elD.textContent = t.body; elD.hidden = t.body === ""; }
		}

		var hint = hero.querySelector("[data-fp-hint-text]");
		if (hint) {
			var touch = root.matchMedia && root.matchMedia("(pointer: coarse)").matches;
			hint.textContent = lang() === "en"
				? (touch ? "Drag to move · Pinch to zoom" : "Drag to move · Scroll to zoom")
				: (touch ? "Чирж хөдөлгөх · Хоёр хуруугаар томруулах" : "Чирж хөдөлгөх · Гүйлгэж томруулах");
		}
	}

	function layers() {
		return hero ? Array.prototype.slice.call(hero.querySelectorAll("[data-fp-floor]")) : [];
	}

	function activeIndex() {
		var ls = layers();
		for (var i = 0; i < ls.length; i++) {
			if (ls[i].classList.contains("is-active")) { return i; }
		}
		return 0;
	}

	function currentViewer() {
		var vs = viewers();
		return vs ? vs[activeIndex()] || null : null;
	}

	function floorIndexOf(slug) {
		var key = (spots[slug] || {}).floor;
		var ls = layers();
		for (var i = 0; i < ls.length; i++) {
			if (ls[i].getAttribute("data-fp-floor") === key) { return i; }
		}
		return -1;
	}

	/** Давхар солих хөдөлгөөн дуусахыг хүлээнэ. */
	function afterSwitch(fn) {
		var started = Date.now();
		(function wait() {
			if (!hero.classList.contains("is-switching") || Date.now() - started > 5000) {
				fn();
				return;
			}
			root.setTimeout(wait, 60);
		})();
	}

	function goToFloor(target, done, tries) {
		tries = tries || 0;
		if (activeIndex() === target || tries >= layers().length) {
			done();
			return;
		}
		var btn = hero.querySelector("[data-fp-floor-switch]");
		if (!btn || btn.disabled) {
			done();
			return;
		}
		btn.click();
		afterSwitch(function () { goToFloor(target, done, tries + 1); });
	}

	function inView(el) {
		var r = el.getBoundingClientRect();
		var vh = root.innerHeight || html.clientHeight;
		return r.top >= 0 && r.bottom <= vh;
	}

	function showSpot(slug) {
		var vs = viewers();
		var target = floorIndexOf(slug);
		if (!vs || target < 0) { return; }

		var needScroll = !inView(hero);
		if (needScroll) {
			hero.scrollIntoView({ behavior: reducedMotion() ? "auto" : "smooth", block: "center" });
		}

		/* гүйлгэлт дууссаны дараа ойртвол зочин хөдөлгөөнийг харна */
		root.setTimeout(function () {
			goToFloor(target, function () {
				var v = currentViewer();
				if (v) { v.select(slug); }
			});
		}, needScroll && !reducedMotion() ? 450 : 0);
	}

	doc.addEventListener("click", function (e) {
		var btn = e.target.closest ? e.target.closest("[data-od-spot]") : null;
		if (!btn || !hero) { return; }
		e.preventDefault();
		showSpot(btn.getAttribute("data-od-spot"));
	});

	/* жагсаалтад одоо схем дээр нээлттэй байгаа цэгийг тодруулна */
	function markPlaces(slug) {
		var places = doc.querySelectorAll(".od-place[data-od-spot]");
		for (var i = 0; i < places.length; i++) {
			places[i].classList.toggle("is-current", !!slug && places[i].getAttribute("data-od-spot") === slug);
		}
	}

	function bootMap() {
		var vs = viewers();
		if (!vs) { return false; }

		vs.forEach(function (v) {
			v.on("state", function (s) {
				if (v !== currentViewer()) { return; }
				var open = s.phase === "FOCUSING" || s.phase === "FOCUSED" || s.phase === "TRANSITIONING_ROOM";
				markPlaces(open ? s.slug : null);
			});
		});

		if (lang() === "en") { mapLang(); }

		/* ?area=... линкээр орж ирсэн бол схем рүү шууд гүйлгэнэ */
		if (/[?&]area=/.test(root.location.search) && hero.classList.contains("is-room")) {
			root.setTimeout(function () { hero.scrollIntoView({ block: "center" }); }, 0);
		}
		return true;
	}

	if (hero && !bootMap()) {
		/* public.js хараахан эхлээгүй бол (хуучин хөтөч) */
		doc.addEventListener("DOMContentLoaded", bootMap);
	}

	/* ================================================================
	   Дээд цэс: идэвхтэй хэсэг
	   ================================================================ */

	var tabs = Array.prototype.slice.call(doc.querySelectorAll("[data-od-tab]"));
	var tabBar = doc.querySelector(".od-tabs");

	if (tabs.length && root.IntersectionObserver) {
		var visible = {};
		var setActive = function (id) {
			tabs.forEach(function (a) {
				var on = a.getAttribute("data-od-tab") === id;
				a.classList.toggle("is-active", on);
				if (on && tabBar) {
					/* утсан дээр идэвхтэй цэс харагдах хэсэгт гарч ирнэ */
					var l = a.offsetLeft - (tabBar.clientWidth - a.offsetWidth) / 2;
					tabBar.scrollTo ? tabBar.scrollTo({ left: l, behavior: "smooth" }) : (tabBar.scrollLeft = l);
				}
			});
		};
		var current = null;
		var io = new root.IntersectionObserver(function (entries) {
			entries.forEach(function (en) { visible[en.target.id] = en.isIntersecting; });
			var pick = null;
			tabs.forEach(function (a) {
				var id = a.getAttribute("data-od-tab");
				if (!pick && visible[id]) { pick = id; }
			});
			if (pick !== current) {
				current = pick;
				setActive(pick);
			}
		}, { rootMargin: "-35% 0px -60% 0px" });

		tabs.forEach(function (a) {
			var sec = doc.getElementById(a.getAttribute("data-od-tab"));
			if (sec) { io.observe(sec); }
		});
	}
})(window);
