/*
 * CP Admin: Офис схем засварлагч (20, 21-р давхар).
 *
 * Сайтын нийтийн viewer-тэй ЯГ ИЖИЛ camera/viewer-ийг (FloorPlanCore,
 * FloorPlanViewer) editable горимд ажиллуулдаг тул урьдчилан харах нь
 * зочдод харагдах зүйлтэй адил. Давхар бүрийг табаар сольж засна; хадгалахад
 * бүх давхар нэг дор хадгалагдана. Хадгалалтыг сервер дахин шалгана
 * (эрх, CSRF, утгын хязгаар, revision).
 */
(function () {
	"use strict";

	var C = window.FloorPlanCore;
	var Viewer = window.FloorPlanViewer;
	var cfgEl = document.getElementById("fpe-data");
	if (!C || !Viewer || !cfgEl) { return; }

	var cfg = JSON.parse(cfgEl.textContent);
	var $ = function (id) { return document.getElementById(id); };
	var qa = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

	var frameWrap = $("fpe-framewrap");
	var frame = $("fpe-frame");
	var stage = frame.querySelector("[data-fp-stage]");
	var floorHost = $("fpe-floor-host");
	var form = $("fpe-form");
	var listEl = $("fpe-list");

	var FRAMES = { desktop: { w: 1440, h: 780 }, mobile: { w: 390, h: 780 } };
	var NUDGE = 0.0005;

	/* ---------------- model ---------------- */

	var model = {
		floors: cfg.floors.map(function (f) {
			return {
				key: f.key,
				number: String(f.number),
				floorTitle: f.floorTitle,
				enabled: f.enabled !== false,
				link: f.link ? { x: f.link.x, y: f.link.y } : null,
				hotspots: C.normalizeHotspots(f.hotspots).map(clone),
				image: { imageUrl: f.imageUrl, imageMidUrl: f.imageMidUrl, imageWidth: f.imageWidth, imageHeight: f.imageHeight, bounds: f.bounds }
			};
		}),
		revisions: cfg.revisions || {}
	};
	var cf = 0;                 /* current floor index */
	var savedSnap = snapshot();
	var selected = null;
	var mode = "overview";
	var undoStack = [];
	var dragOrigin = {};
	var linkOrigin = null;
	var saving = false;
	var viewer = null;

	function clone(o) { return JSON.parse(JSON.stringify(o)); }
	function F() { return model.floors[cf]; }
	function S() { return F().hotspots; }

	function toPayload() {
		return {
			floors: model.floors.map(function (f) {
				return {
					key: f.key,
					floorTitle: f.floorTitle,
					enabled: f.enabled,
					link: f.link,
					hotspots: f.hotspots.map(function (h, i) {
						var o = clone(h);
						delete o._new;
						o.order = i + 1;
						return o;
					})
				};
			})
		};
	}

	function snapshot() { return JSON.stringify(toPayload()); }
	function isDirty() { return snapshot() !== savedSnap; }
	function find(slug) {
		var list = S();
		for (var i = 0; i < list.length; i++) { if (list[i].slug === slug) { return list[i]; } }
		return null;
	}
	/** slugs are unique across ALL floors (?area= links) */
	function findAnywhere(slug) {
		for (var f = 0; f < model.floors.length; f++) {
			var list = model.floors[f].hotspots;
			for (var i = 0; i < list.length; i++) { if (list[i].slug === slug) { return list[i]; } }
		}
		return null;
	}
	function round(v, n) { var p = Math.pow(10, n); return Math.round(v * p) / p; }

	function targetOf(i) {
		if (model.floors.length < 2) { return -1; }
		return i + 1 < model.floors.length ? i + 1 : i - 1;
	}

	function linkLabel(i) {
		var t = targetOf(i);
		if (t < 0) { return null; }
		var up = parseFloat(model.floors[t].number) > parseFloat(model.floors[i].number);
		return { text: model.floors[t].number, dir: up ? "up" : "down", aria: "Шат -> " + model.floors[t].floorTitle };
	}

	/* ---------------- viewer (one per floor, rebuilt on floor change) ---------------- */

	function viewerData() {
		return S().map(function (h, i) {
			var o = clone(h);
			o.order = i + 1;
			return o;
		});
	}

	function planFor(f) {
		return { imageWidth: f.image.imageWidth, imageHeight: f.image.imageHeight, bounds: f.image.bounds, link: f.link };
	}

	function mountFloor(i) {
		if (viewer) { viewer.destroy(); }
		cf = i;
		selected = null;
		undoStack = [];
		dragOrigin = {};
		$("fpe-undo").disabled = true;

		var f = F();
		floorHost.innerHTML =
			'<div class="fp-floor is-active is-ready">' +
			'<div class="fp-world" data-fp-world><img class="fp-image" alt="" draggable="false"></div>' +
			'<div class="fp-spots" data-fp-spots></div>' +
			"</div>";
		var layer = floorHost.firstChild;
		var img = layer.querySelector("img");
		if (f.image.imageUrl) { img.src = f.image.imageUrl; }
		if (f.image.imageMidUrl && f.image.imageMidUrl !== f.image.imageUrl) {
			var hi = new Image();
			hi.onload = function () { if (layer.parentNode) { img.src = f.image.imageMidUrl; } };
			hi.src = f.image.imageMidUrl;
		}

		viewer = new Viewer(stage, { layer: layer, plan: planFor(f), hotspots: viewerData(), editable: true, linkLabel: linkLabel(i) });
		bindViewer();

		ftitle.value = f.floorTitle;
		penabled.checked = f.enabled;
		$("fpe-title-preview").textContent = f.floorTitle;
		$("fpe-floor-name").textContent = "— " + f.floorTitle;
		showStair();
		renderTabs();
		renderList();
		layoutFrame();
		setMode("overview");
		if (S().length) { selectHotspot(S()[0].slug, true); } else { fillForm(); }
		updateState();
	}

	function renderTabs() {
		var host = $("fpe-floor-tabs");
		host.innerHTML = "";
		model.floors.forEach(function (f, i) {
			var b = document.createElement("button");
			b.type = "button";
			b.className = "btn btn-sm " + (i === cf ? "btn-primary" : "btn-white");
			b.setAttribute("data-floor", f.key);
			b.textContent = f.floorTitle || f.number;
			b.addEventListener("click", function () { if (i !== cf) { mountFloor(i); } });
			host.appendChild(b);
		});
	}

	function showStair() {
		var l = F().link;
		$("fpe-stair-pos").textContent = l ? ("X " + l.x.toFixed(4) + ", Y " + l.y.toFixed(4)) : "байхгүй";
	}

	function syncViewer() { viewer.setData(viewerData()); }

	/* ---------------- frame sizing (overview / desktop / mobile) ---------------- */

	function layoutFrame() {
		var avail = frameWrap.clientWidth || 800;
		var w;
		var h;
		var k;

		if (mode === "overview") {
			w = avail;
			h = Math.round(avail / 1.75);
			k = 1;
		} else {
			var fr = FRAMES[mode];
			k = Math.min(1, avail / fr.w);
			w = fr.w;
			h = fr.h;
		}

		frame.style.width = w + "px";
		frame.style.height = h + "px";
		frame.style.transform = k === 1 ? "none" : "scale(" + k + ")";
		frame.style.left = Math.max(0, (avail - w * k) / 2) + "px";
		frameWrap.style.height = Math.round(h * k) + "px";
		if (viewer) { viewer.relayout(); }
	}

	window.addEventListener("resize", layoutFrame);

	function setMode(next) {
		mode = next;
		qa("[data-mode]").forEach(function (b) {
			var on = b.getAttribute("data-mode") === next;
			b.classList.toggle("btn-primary", on);
			b.classList.toggle("btn-white", !on);
		});
		qa(".fpe-cam").forEach(function (f) { f.classList.toggle("is-active-set", f.getAttribute("data-set") === next); });

		layoutFrame();

		var h = selected ? find(selected) : null;
		if (next === "overview") {
			viewer.back();
		} else if (h && h.enabled) {
			if (!viewer.select(h.slug)) { viewer.refreshCamera(); }
		} else {
			note("Урьдчилж харахын тулд идэвхтэй цэг сонгоно уу.");
		}
	}

	qa("[data-mode]").forEach(function (b) {
		b.addEventListener("click", function () { setMode(b.getAttribute("data-mode")); });
	});
	$("fpe-preview-desktop").addEventListener("click", function () { setMode("desktop"); });
	$("fpe-preview-mobile").addEventListener("click", function () { setMode("mobile"); });
	$("fpe-preview-overview").addEventListener("click", function () { setMode("overview"); });

	frame.querySelector("[data-fp-prev]").addEventListener("click", function () { viewer.prev(); });
	frame.querySelector("[data-fp-next]").addEventListener("click", function () { viewer.next(); });
	frame.querySelector("[data-fp-back]").addEventListener("click", function () { viewer.back(); });

	/* ---------------- selection / list ---------------- */

	function renderList() {
		listEl.innerHTML = "";
		S().forEach(function (h, i) {
			var li = document.createElement("li");
			li.className = (h.slug === selected ? "is-active " : "") + (h.enabled ? "" : "is-off");
			li.innerHTML = '<span class="fpe-num"></span><span class="fpe-name"></span>';
			li.querySelector(".fpe-num").textContent = i + 1;
			var nm = li.querySelector(".fpe-name");
			nm.textContent = h.titleEn || h.titleMn || h.slug;
			if (h.titleEn && h.titleMn) {
				var sm = document.createElement("small");
				sm.textContent = h.titleMn;
				nm.appendChild(sm);
			}
			if (h._new) {
				var tag = document.createElement("span");
				tag.className = "fpe-new";
				tag.textContent = "шинэ";
				li.appendChild(tag);
			}
			li.addEventListener("click", function () { selectHotspot(h.slug, false); });
			listEl.appendChild(li);
		});
		$("fpe-count").textContent = "(" + S().length + ")";
	}

	function selectHotspot(slug, fromViewer) {
		selected = slug;
		viewer.setSelectedEdit(slug);
		renderList();
		fillForm();
		if (!fromViewer && mode !== "overview") {
			var h = find(slug);
			if (h && h.enabled) { viewer.select(slug); }
		}
	}

	/* ---------------- form ---------------- */

	var fields = qa("[data-field]", form);
	var ranges = qa("[data-range-for]", form);

	function getPath(h, path) {
		var p = path.split(".");
		return p.length === 2 ? h[p[0]][p[1]] : h[path];
	}

	function setPath(h, path, v) {
		var p = path.split(".");
		if (p.length === 2) { h[p[0]][p[1]] = v; } else { h[path] = v; }
	}

	function fillForm() {
		var h = selected ? find(selected) : null;
		form.hidden = !h;
		if (!h) { return; }

		$("fpe-form-title").textContent = (h.titleEn || h.slug);

		fields.forEach(function (el) {
			var path = el.getAttribute("data-field");
			var set = path.split(".")[0];
			if (/\.focusDot$/.test(path)) {
				el.checked = h[set].focusX === null && h[set].focusY === null;
				return;
			}
			var v = getPath(h, path);
			if (el.type === "checkbox") {
				el.checked = !!v;
			} else if (/\.focus[XY]$/.test(path)) {
				el.value = v === null ? "" : v;
				el.disabled = h[set].focusX === null && h[set].focusY === null;
			} else {
				el.value = v === null || v === undefined ? "" : v;
			}
		});
		ranges.forEach(function (r) { r.value = getPath(h, r.getAttribute("data-range-for")); });

		/* an existing slug is a public URL (?area=slug): keep it stable */
		form.querySelector('[data-field="slug"]').disabled = !h._new;
		$("fpe-up").disabled = S().indexOf(h) === 0;
		$("fpe-down").disabled = S().indexOf(h) === S().length - 1;
	}

	function fillPosition(h) {
		form.querySelector('[data-field="x"]').value = h.x;
		form.querySelector('[data-field="y"]').value = h.y;
	}

	function onField(el) {
		var h = selected ? find(selected) : null;
		if (!h) { return; }
		var path = el.getAttribute("data-field");
		var set = path.split(".")[0];

		if (/\.focusDot$/.test(path)) {
			if (el.checked) {
				h[set].focusX = null;
				h[set].focusY = null;
			} else {
				h[set].focusX = round(h.x, 5);
				h[set].focusY = round(h.y, 5);
			}
			fillForm();
		} else if (path === "slug") {
			var s = el.value.toLowerCase().replace(/[^a-z0-9-]/g, "");
			if (s !== el.value) { el.value = s; }
			if (s !== "" && !findAnywhere(s)) {
				h.slug = s;
				selected = s;
			}
		} else if (el.type === "checkbox") {
			setPath(h, path, el.checked);
		} else if (el.type === "number") {
			var n = parseFloat(el.value);
			if (!isFinite(n)) { return; }
			setPath(h, path, clampField(path, n));
			var rg = form.querySelector('[data-range-for="' + path + '"]');
			if (rg) { rg.value = getPath(h, path); }
		} else {
			setPath(h, path, el.value);
		}

		/* editing a camera group previews that device */
		if (set === "desktop" || set === "mobile") {
			if (mode !== set) { setMode(set); }
		}

		afterEdit();
	}

	function clampField(path, n) {
		if (/\.zoom$/.test(path)) { return round(C.clamp(n, C.ZOOM_MIN, C.ZOOM_MAX), 3); }
		if (/\.offset[XY]$/.test(path)) { return round(C.clamp(n, -C.OFFSET_MAX, C.OFFSET_MAX), 4); }
		if (/\.focus[XY]$|^[xy]$/.test(path)) { return round(C.clamp(n, 0, 1), 5); }
		return n;
	}

	fields.forEach(function (el) {
		var ev = el.type === "checkbox" || el.tagName === "SELECT" ? "change" : "input";
		el.addEventListener(ev, function () { onField(el); });
	});

	ranges.forEach(function (r) {
		r.addEventListener("input", function () {
			var path = r.getAttribute("data-range-for");
			var num = form.querySelector('[data-field="' + path + '"]');
			num.value = r.value;
			onField(num);
		});
	});

	function afterEdit() {
		var h = selected ? find(selected) : null;
		syncViewer();
		viewer.setSelectedEdit(selected);
		renderList();
		if (h) { $("fpe-form-title").textContent = h.titleEn || h.slug; }
		updateState();
	}

	/* ---------------- floor settings ---------------- */

	var ftitle = $("fpe-floor-title");
	ftitle.addEventListener("input", function () {
		F().floorTitle = ftitle.value;
		$("fpe-title-preview").textContent = ftitle.value;
		$("fpe-floor-name").textContent = "— " + ftitle.value;
		renderTabs();
		updateState();
	});
	var penabled = $("fpe-plan-enabled");
	penabled.addEventListener("change", function () {
		F().enabled = penabled.checked;
		updateState();
	});

	/* ---------------- viewer events: drag, select, add, preview card ---------------- */

	function bindViewer() {
		viewer.on("select", function (slug) {
			/* fired by dragging a dot (pointerdown) and by viewer.select() */
			if (slug && slug !== selected) { selectHotspot(slug, true); }
		});

		/* the preview card/arrows behave like the public ones */
		viewer.on("state", function (s) {
			var room = C.isControlsVisible(s);
			frame.classList.toggle("is-room", room);
			if (room && s.slug) {
				var h = viewer.getHotspot(s.slug);
				if (h) {
					frame.querySelector("[data-fp-en]").textContent = h.titleEn;
					frame.querySelector("[data-fp-mn]").textContent = h.titleMn;
					frame.querySelector("[data-fp-desc]").textContent = h.descriptionMn;
				}
				if (s.slug !== selected) { selectHotspot(s.slug, true); }
			}
		});

		viewer.on("move", function (slug, x, y, final) {
			var h = find(slug);
			if (!h) { return; }
			if (!dragOrigin[slug]) { dragOrigin[slug] = { x: h.x, y: h.y }; }
			h.x = round(x, 5);
			h.y = round(y, 5);
			viewer.setHotspotPosition(slug, h.x, h.y);
			if (slug === selected) { fillPosition(h); }

			if (final) {
				var o = dragOrigin[slug];
				delete dragOrigin[slug];
				if (o && (o.x !== h.x || o.y !== h.y)) {
					undoStack.push({ slug: slug, x: o.x, y: o.y });
					$("fpe-undo").disabled = false;
				}
				afterEdit();
			} else {
				updateState();
			}
		});

		/* the staircase marker */
		viewer.on("linkmove", function (x, y, final) {
			var f = F();
			if (!f.link) { return; }
			if (!linkOrigin) { linkOrigin = { x: f.link.x, y: f.link.y }; }
			f.link = { x: round(x, 5), y: round(y, 5) };
			viewer.setLinkPosition(f.link.x, f.link.y);
			showStair();
			if (final) {
				if (linkOrigin.x !== f.link.x || linkOrigin.y !== f.link.y) {
					undoStack.push({ link: true, x: linkOrigin.x, y: linkOrigin.y });
					$("fpe-undo").disabled = false;
				}
				linkOrigin = null;
			}
			updateState();
		});

		viewer.on("add", function (x, y) {
			viewer.setAddMode(false);
			$("fpe-add").classList.remove("active");
			note("");
			addHotspot({
				_new: true,
				slug: uniqueSlug("area"),
				order: S().length + 1,
				enabled: true,
				kind: "other",
				titleEn: "New area",
				titleMn: "",
				descriptionMn: "",
				descriptionEn: "",
				x: round(x, 5),
				y: round(y, 5),
				desktop: { zoom: 2.8, focusX: null, focusY: null, offsetX: 0, offsetY: 0 },
				mobile: { zoom: 4.5, focusX: null, focusY: null, offsetX: 0, offsetY: 0 }
			});
		});
	}

	$("fpe-undo").addEventListener("click", function () {
		var u = undoStack.pop();
		if (!u) { return; }
		if (u.link) {
			F().link = { x: u.x, y: u.y };
			viewer.setLinkPosition(u.x, u.y);
			showStair();
			updateState();
		} else {
			var h = find(u.slug);
			if (h) {
				h.x = u.x;
				h.y = u.y;
				selectHotspot(u.slug, true);
				afterEdit();
			}
		}
		$("fpe-undo").disabled = undoStack.length === 0;
	});

	/* arrow keys nudge the selected dot while it has focus */
	stage.addEventListener("keydown", function (e) {
		if (!selected || !e.target.closest || !e.target.closest(".fp-spot")) { return; }
		var slug = e.target.getAttribute("data-slug");
		if (slug !== selected) { return; }
		var step = e.shiftKey ? NUDGE * 10 : NUDGE;
		var dx = e.key === "ArrowRight" ? step : e.key === "ArrowLeft" ? -step : 0;
		var dy = e.key === "ArrowDown" ? step : e.key === "ArrowUp" ? -step : 0;
		if (!dx && !dy) { return; }
		e.preventDefault();
		var h = find(slug);
		undoStack.push({ slug: slug, x: h.x, y: h.y });
		$("fpe-undo").disabled = false;
		h.x = round(C.clamp(h.x + dx, 0, 1), 5);
		h.y = round(C.clamp(h.y + dy, 0, 1), 5);
		viewer.setHotspotPosition(slug, h.x, h.y);
		fillPosition(h);
		updateState();
	});

	function uniqueSlug(base) {
		var s = base;
		var i = 2;
		while (findAnywhere(s)) { s = base + "-" + i++; }
		return s;
	}

	function addHotspot(h) {
		S().push(h);
		syncViewer();
		selectHotspot(h.slug, true);
		viewer.setSelectedEdit(h.slug);
		afterEdit();
	}

	$("fpe-add").addEventListener("click", function () {
		if (mode !== "overview") { setMode("overview"); }
		var on = !viewer.addMode;
		viewer.setAddMode(on);
		$("fpe-add").classList.toggle("active", on);
		note(on ? "Схем дээр дарж шинэ цэгийн байрлалыг сонгоно уу." : "");
	});

	$("fpe-dup").addEventListener("click", function () {
		var h = selected ? find(selected) : null;
		if (!h) { return; }
		var c = clone(h);
		c._new = true;
		c.slug = uniqueSlug(h.slug.replace(/-\d+$/, ""));
		c.x = round(C.clamp(h.x + 0.01, 0, 1), 5);
		c.y = round(C.clamp(h.y + 0.01, 0, 1), 5);
		c.titleEn = (h.titleEn || "Area") + " (copy)";
		addHotspot(c);
	});

	$("fpe-del").addEventListener("click", function () {
		var h = selected ? find(selected) : null;
		if (!h) { return; }
		if (!window.confirm("\"" + (h.titleEn || h.slug) + "\" цэгийг устгах уу?\nХадгалах хүртэл өгөгдлийн санд хэвээр байна.")) { return; }
		var list = S();
		var i = list.indexOf(h);
		list.splice(i, 1);
		undoStack = undoStack.filter(function (u) { return u.slug !== h.slug; });
		$("fpe-undo").disabled = undoStack.length === 0;
		var next = list[Math.min(i, list.length - 1)];
		selected = null;
		syncViewer();
		if (next) { selectHotspot(next.slug, true); } else { fillForm(); renderList(); }
		if (mode !== "overview") { setMode("overview"); }
		updateState();
	});

	function move(dir) {
		var h = selected ? find(selected) : null;
		if (!h) { return; }
		var list = S();
		var i = list.indexOf(h);
		var j = i + dir;
		if (j < 0 || j >= list.length) { return; }
		list.splice(i, 1);
		list.splice(j, 0, h);
		syncViewer();
		renderList();
		fillForm();
		updateState();
	}
	$("fpe-up").addEventListener("click", function () { move(-1); });
	$("fpe-down").addEventListener("click", function () { move(1); });

	/* ---------------- status / save ---------------- */

	function note(text) { $("fpe-hint").textContent = text || "Цэгийг чирж байрлуулна. Сонгосон цэг дээр сумны товчоор нарийн зөөнө (Shift = том алхам)."; }

	function updateState() {
		var dirty = isDirty();
		var st = $("fpe-status");
		st.textContent = dirty ? "Хадгалаагүй өөрчлөлт байна" : "";
		st.className = "fpe-status" + (dirty ? " is-dirty" : "");
		$("fpe-save").disabled = !dirty || saving;
		$("fpe-reset").disabled = !dirty || saving;
	}

	function showErrors(list) {
		var el = $("fpe-errors");
		el.innerHTML = "";
		if (!list || !list.length) { return; }
		var ul = document.createElement("ul");
		list.forEach(function (m) {
			var li = document.createElement("li");
			li.textContent = m;
			ul.appendChild(li);
		});
		el.appendChild(ul);
	}

	function validate() {
		var errs = [];
		var seen = {};
		model.floors.forEach(function (f) {
			var ft = f.floorTitle.trim() || f.number;
			if (f.floorTitle.trim() === "") { errs.push(f.number + "-р давхар: гарчиг хоосон байж болохгүй."); }
			f.hotspots.forEach(function (h, i) {
				var label = ft + " #" + (i + 1) + " " + (h.titleEn || h.slug);
				if (!/^[a-z0-9-]{1,48}$/.test(h.slug)) { errs.push(label + ": slug буруу (a-z, 0-9, -)."); }
				if (seen[h.slug]) { errs.push(label + ": slug давхардсан."); }
				seen[h.slug] = true;
				if (!h.titleEn.trim() && !h.titleMn.trim()) { errs.push(label + ": англи эсвэл монгол нэр оруулна уу."); }
			});
		});
		return errs;
	}

	$("fpe-save").addEventListener("click", function () {
		var errs = validate();
		showErrors(errs);
		if (errs.length) { return; }

		saving = true;
		updateState();
		$("fpe-status").textContent = "Хадгалж байна...";

		var body = new FormData();
		body.append("frmPost", "floorPlanSave");
		body.append("ajaxOrder", "1");
		body.append("csrf", cfg.csrf);
		body.append("revisions", JSON.stringify(model.revisions));
		body.append("payload", JSON.stringify(toPayload()));

		var sentSnap = snapshot();

		fetch(cfg.saveUrl, { method: "POST", body: body, credentials: "same-origin", headers: { "X-Requested-With": "fetch" } })
			.then(function (r) { return r.json().catch(function () { return { ok: 0, error: "Серверийн хариу уншигдсангүй (HTTP " + r.status + ")." }; }); })
			.then(function (res) {
				saving = false;
				if (res && res.ok) {
					model.revisions = res.revisions;
					model.floors.forEach(function (f) { f.hotspots.forEach(function (h) { delete h._new; }); });
					savedSnap = sentSnap;
					undoStack = [];
					$("fpe-undo").disabled = true;
					renderList();
					fillForm();
					updateState();
					var st = $("fpe-status");
					st.textContent = "Хадгаллаа";
					st.className = "fpe-status is-ok";
					window.setTimeout(function () { if (!isDirty()) { st.textContent = ""; } }, 3500);
					return;
				}
				var msgs = (res && res.errors && res.errors.length) ? res.errors : [(res && res.error) || "Хадгалж чадсангүй."];
				showErrors(msgs);
				updateState();
			})
			.catch(function () {
				/* nothing was applied optimistically, so there is nothing to roll back */
				saving = false;
				showErrors(["Сервертэй холбогдож чадсангүй. Интернэтээ шалгаад дахин оролдоно уу — таны өөрчлөлт хэвээр байна."]);
				updateState();
			});
	});

	$("fpe-reset").addEventListener("click", function () {
		if (!window.confirm("Хадгалаагүй бүх өөрчлөлтийг цуцлах уу?")) { return; }
		var snap = JSON.parse(savedSnap);
		snap.floors.forEach(function (sf) {
			model.floors.forEach(function (f) {
				if (f.key === sf.key) {
					f.floorTitle = sf.floorTitle;
					f.enabled = sf.enabled;
					f.link = sf.link;
					f.hotspots = C.normalizeHotspots(sf.hotspots).map(clone);
				}
			});
		});
		showErrors([]);
		mountFloor(cf);
	});

	window.addEventListener("beforeunload", function (e) {
		if (isDirty()) {
			e.preventDefault();
			e.returnValue = "";
		}
	});

	/* ---------------- start ---------------- */

	mountFloor(0);

	/* handle for the browser tests (tests/floorplan/e2e/editor-e2e.mjs) */
	window.__fpe = { model: model };
	Object.defineProperty(window.__fpe, "viewer", { get: function () { return viewer; } });
	Object.defineProperty(window.__fpe, "floor", { get: function () { return cf; } });
})();
