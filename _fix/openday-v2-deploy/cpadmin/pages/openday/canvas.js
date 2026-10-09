/*!
 * CP Admin -> Өдөрлөг: iframe доторх "хуудсан дээр засах" багаж.
 *
 * Хуудас нь нийтийн /openday-ийн ЯГ өөрийнх нь HTML (засах горимтой).
 * Энд:
 *   - [data-ode-f]      текст дээр дарж шууд бичнэ (MN / EN тус тусдаа)
 *   - [data-ode-row]    мөр дээр ↑ ↓ нуух/харуулах устгах товч
 *   - [data-ode-add]    "+ ... нэмэх"
 *   - [data-ode-pick]   байршил, icon, давхар, огноо, цаг сонгогч
 *   - [data-ode-toggle] асаах/унтраах (асуулт хүлээн авах)
 *   - схем              цэгийг чирэх, "Цэг нэмэх", давхар солих
 * Өөрчлөлт бүр window.parent.OpenDayEditor (editor.js) руу очно.
 */
(function () {
	"use strict";

	var P = window.parent && window.parent !== window ? window.parent.OpenDayEditor : null;
	if (!P) { return; }

	var doc = document;
	var html = doc.documentElement;

	var meta = {};
	try { meta = JSON.parse(doc.querySelector("[data-ode-meta]").textContent) || {}; } catch (e) { meta = {}; }

	function esc(s) {
		return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	}

	function lang() { return html.getAttribute("data-lang") === "en" ? "en" : "mn"; }

	/* ================================================================
	   Хуудас, хэл
	   ================================================================ */

	var views = {};
	Array.prototype.forEach.call(doc.querySelectorAll("[data-od-view]"), function (v) {
		views[v.getAttribute("data-od-view")] = v;
	});

	function go(page) {
		if (!views[page]) { page = "home"; }
		Object.keys(views).forEach(function (k) { views[k].hidden = k !== page; });
		doc.body.setAttribute("data-od-page", page);
		Array.prototype.forEach.call(doc.querySelectorAll(".od-tabs [data-od-go]"), function (a) {
			a.classList.toggle("is-active", a.getAttribute("data-od-go") === page);
		});
		P.onPage(page);
		if (page === "map" && viewer) { viewer.relayout(); }
	}

	function setLang(l) {
		html.setAttribute("data-lang", l === "en" ? "en" : "mn");
		html.lang = l === "en" ? "en" : "mn";
		if (viewer) { viewer.setData(viewerData()); viewer.setSelectedEdit(selSlug); }
	}

	doc.addEventListener("click", function (e) {
		var a = e.target.closest("[data-od-go]");
		if (a) {
			e.preventDefault();
			go(a.getAttribute("data-od-go"));
			window.scrollTo(0, 0);
			return;
		}
		if (e.target.closest("[data-od-lang]")) {
			e.preventDefault();
			P.setLang(lang() === "en" ? "mn" : "en");
			return;
		}
		/* засах горимд бусад линк хаашаа ч явахгүй */
		var link = e.target.closest("a[href]");
		if (link) { e.preventDefault(); }
	});

	/* ================================================================
	   Текст засах
	   ================================================================ */

	var plainOnly = (function () {
		var d = doc.createElement("div");
		try { d.contentEditable = "plaintext-only"; } catch (e) { return false; }
		return d.contentEditable === "plaintext-only";
	})();

	function textOf(el) {
		var t = (el.innerText || "").replace(/ /g, " ").replace(/\r/g, "");
		if (el.getAttribute("data-ode-multi") !== "1") { t = t.replace(/\s*\n\s*/g, " "); }
		return t.replace(/\n+$/, "");
	}

	function normTime(v) {
		v = String(v || "").trim().replace(/[.,]/, ":");
		if (v === "") { return ""; }
		var m = /^(\d{1,2})(?::?(\d{2}))?$/.exec(v);
		if (!m || +m[1] > 23 || (m[2] && +m[2] > 59)) { return null; }
		return (m[1].length === 1 ? "0" : "") + m[1] + ":" + (m[2] || "00");
	}

	/* бусад газарт харагддаг текст өөрчлөгдвөл blur дээр дахин зурна */
	function needsRender(path) {
		return /^spot\.\d+\.title/.test(path) || /^s\.(sched|map|info|faq)Title/.test(path);
	}

	Array.prototype.forEach.call(doc.querySelectorAll("[data-ode-f]"), function (el) {
		var path = el.getAttribute("data-ode-f");
		var isTime = el.getAttribute("data-ode-type") === "time";
		var multi = el.getAttribute("data-ode-multi") === "1";

		el.setAttribute("contenteditable", plainOnly ? "plaintext-only" : "true");
		el.setAttribute("spellcheck", "false");
		el.setAttribute("role", "textbox");
		if (multi) { el.setAttribute("aria-multiline", "true"); }
		el.setAttribute("title", isTime ? "Цаг (ж: 11:30). Хоосон байж болно." : (multi ? "Дарж бичнэ. Enter — шинэ мөр." : "Дарж бичнэ."));

		var before = "";

		el.addEventListener("focus", function () {
			before = textOf(el);
		});

		el.addEventListener("input", function () {
			var v = textOf(el);
			el.classList.remove("ode-invalid");
			if (v === "" && el.innerHTML !== "") { el.innerHTML = ""; }
			if (!isTime) { P.set(path, v); }
		});

		el.addEventListener("keydown", function (e) {
			if (e.key === "Enter" && (!multi || isTime)) {
				e.preventDefault();
				el.blur();
			} else if (e.key === "Escape") {
				e.preventDefault();
				el.textContent = before;
				if (!isTime) { P.set(path, before); }
				el.blur();
			}
		});

		el.addEventListener("paste", function (e) {
			e.preventDefault();
			var t = (e.clipboardData || window.clipboardData).getData("text") || "";
			if (!multi) { t = t.replace(/\s*\n\s*/g, " "); }
			doc.execCommand("insertText", false, t);
		});

		el.addEventListener("blur", function () {
			var v = textOf(el);
			if (isTime) {
				var n = normTime(v);
				if (n === null) {
					el.textContent = before;
					el.classList.add("ode-flash");
					window.setTimeout(function () { el.classList.remove("ode-flash"); }, 900);
					return;
				}
				el.textContent = n;
				if (n !== before) { P.set(path, n); }
				return;
			}
			if (v !== before && needsRender(path)) { P.render(); }
		});
	});

	/* ================================================================
	   Мөрийн товчлуур
	   ================================================================ */

	Array.prototype.forEach.call(doc.querySelectorAll("[data-ode-row]"), function (row) {
		var kind = row.getAttribute("data-ode-row");
		var i = +row.getAttribute("data-ode-i");
		var off = row.classList.contains("od-off");
		var tools = doc.createElement("div");
		tools.className = "ode-tools";
		tools.innerHTML =
			'<button type="button" data-op="up" title="Дээш"><i class="fa fa-arrow-up"></i></button>' +
			'<button type="button" data-op="down" title="Доош"><i class="fa fa-arrow-down"></i></button>' +
			'<button type="button" data-op="toggle" title="' + (off ? "Харуулах" : "Зочдод нуух") + '"><i class="fa ' + (off ? "fa-eye" : "fa-eye-slash") + '"></i></button>' +
			'<button type="button" data-op="del" title="Устгах" class="is-danger"><i class="fa fa-trash"></i></button>';
		tools.addEventListener("click", function (e) {
			var b = e.target.closest("[data-op]");
			if (!b) { return; }
			e.preventDefault();
			e.stopPropagation();
			P.op(kind, i, b.getAttribute("data-op"));
		});
		row.appendChild(tools);
	});

	Array.prototype.forEach.call(doc.querySelectorAll("[data-ode-add]"), function (b) {
		b.addEventListener("click", function () { P.add(b.getAttribute("data-ode-add")); });
	});

	Array.prototype.forEach.call(doc.querySelectorAll("[data-ode-toggle]"), function (c) {
		c.addEventListener("change", function () {
			P.set(c.getAttribute("data-ode-toggle"), c.checked ? "1" : "0");
			P.render();
		});
	});

	/* ================================================================
	   Сонгогч цонх (popover)
	   ================================================================ */

	var pop = null;

	function closePop() {
		if (pop && pop.parentNode) { pop.parentNode.removeChild(pop); }
		pop = null;
	}

	function openPop(anchor, htmlStr, onClick) {
		closePop();
		pop = doc.createElement("div");
		pop.className = "ode-pop";
		pop.innerHTML = htmlStr;
		doc.body.appendChild(pop);

		var r = anchor.getBoundingClientRect();
		var w = pop.offsetWidth;
		var left = Math.max(8, Math.min(r.left + window.scrollX, window.scrollX + doc.documentElement.clientWidth - w - 8));
		pop.style.left = left + "px";
		pop.style.top = (r.bottom + window.scrollY + 6) + "px";

		pop.addEventListener("click", function (e) {
			var b = e.target.closest("[data-v]");
			if (b) { onClick(b.getAttribute("data-v"), pop); }
		});
		var first = pop.querySelector("input, [data-v].is-on, [data-v]");
		if (first) { try { first.focus(); } catch (e) { /* ignore */ } }
	}

	doc.addEventListener("mousedown", function (e) {
		if (pop && !pop.contains(e.target) && !e.target.closest("[data-ode-pick]")) { closePop(); }
	});
	doc.addEventListener("keydown", function (e) { if (e.key === "Escape") { closePop(); setAdd(false); } });

	function spotLabel(s) {
		var t = lang() === "en" ? (s.titleEn || s.titleMn) : (s.titleMn || s.titleEn);
		return t || s.slug;
	}

	doc.addEventListener("click", function (e) {
		var a = e.target.closest("[data-ode-pick]");
		if (!a) { return; }
		e.preventDefault();
		e.stopPropagation();
		var kind = a.getAttribute("data-ode-pick");
		var path = a.getAttribute("data-ode-path");

		if (kind === "spot") {
			var cur = P.get(path);
			var spots = P.spots();
			var h = '<div class="ode-pop-h">Байршил сонгох</div><div class="ode-pop-list">' +
				'<button type="button" data-v=""' + (cur === "" ? ' class="is-on"' : "") + '><i class="fa fa-ban"></i> Байршилгүй</button>';
			(meta.floors || []).forEach(function (f) {
				var list = spots.filter(function (s) { return s.floor === f.key; });
				if (!list.length) { return; }
				h += '<div class="ode-pop-sub">' + esc(f.number) + "F</div>";
				list.forEach(function (s) {
					h += '<button type="button" data-v="' + esc(s.slug) + '"' + (s.slug === cur ? ' class="is-on"' : "") + '><i class="fa fa-map-marker"></i> ' + esc(spotLabel(s)) + "</button>";
				});
			});
			h += '</div><div class="ode-pop-foot">Шинэ байршил нэмэх бол "Схем" хуудсанд "Цэг нэмэх"-ийг дарна.</div>';
			openPop(a, h, function (v) { closePop(); P.set(path, v); P.render(); });
		}

		if (kind === "icon") {
			var curI = P.get(path);
			var hi = '<div class="ode-pop-h">Icon сонгох</div><div class="ode-pop-grid">';
			(meta.icons || []).forEach(function (ic) {
				hi += '<button type="button" data-v="' + esc(ic.key) + '"' + (ic.key === curI ? ' class="is-on"' : "") + ' title="' + esc(ic.label) + '"><i class="fa ' + esc(ic.fa) + '"></i><span>' + esc(ic.label) + "</span></button>";
			});
			hi += "</div>";
			openPop(a, hi, function (v) { closePop(); P.set(path, v); P.render(); });
		}

		if (kind === "floor") {
			var i = +String(path).split(".")[1];
			var curF = P.get(path);
			var hf = '<div class="ode-pop-h">Давхар солих</div><div class="ode-pop-list">';
			(meta.floors || []).forEach(function (f) {
				hf += '<button type="button" data-v="' + esc(f.key) + '"' + (f.key === curF ? ' class="is-on"' : "") + ">" + esc(f.number) + "F</button>";
			});
			hf += '</div><div class="ode-pop-foot">Өөр давхарт шилжүүлбэл цэг тэр давхрын голд гарна — дараа нь чирж байрлуулна.</div>';
			openPop(a, hf, function (v) { closePop(); P.spotFloor(i, v); });
		}

		if (kind === "date") {
			openPop(a,
				'<div class="ode-pop-h">Өдөрлөгийн огноо</div>' +
				'<div class="ode-pop-form"><input type="date" value="' + esc(P.get("s.eventDate")) + '">' +
				'<button type="button" class="ode-pop-ok" data-v="ok">OK</button></div>',
				function (v, p) {
					P.set("s.eventDate", p.querySelector("input").value || "");
					closePop();
					P.render();
				});
		}

		if (kind === "hours") {
			openPop(a,
				'<div class="ode-pop-h">Эхлэх — дуусах цаг</div>' +
				'<div class="ode-pop-form"><input type="time" data-k="startTime" value="' + esc(P.get("s.startTime")) + '">' +
				'<span>–</span><input type="time" data-k="endTime" value="' + esc(P.get("s.endTime")) + '">' +
				'<button type="button" class="ode-pop-ok" data-v="ok">OK</button></div>' +
				'<div class="ode-pop-foot">"Одоо / дараа нь" хэсэг энэ цаг, огноогоор ажиллана.</div>',
				function (v, p) {
					Array.prototype.forEach.call(p.querySelectorAll("input[data-k]"), function (inp) {
						P.set("s." + inp.getAttribute("data-k"), inp.value || "");
					});
					closePop();
					P.render();
				});
		}
	}, true);

	/* ================================================================
	   Схем: цэг чирэх, нэмэх, давхар солих
	   ================================================================ */

	var C = window.FloorPlanCore;
	var Viewer = window.FloorPlanViewer;
	var mapEl = doc.querySelector("[data-ode-map]");
	var viewer = null;
	var floorKey = null;
	var selSlug = null;
	var fpFloors = [];

	try { fpFloors = JSON.parse(mapEl.querySelector("[data-fp-data]").textContent).floors || []; } catch (e) { fpFloors = []; }

	function floorDef(key) {
		for (var i = 0; i < fpFloors.length; i++) { if (fpFloors[i].key === key) { return fpFloors[i]; } }
		return null;
	}

	function viewerData() {
		return P.spots().filter(function (s) { return s.floor === floorKey; }).map(function (s, n) {
			var full = (meta.spots || []).filter(function (m) { return m.slug === s.slug; })[0] || {};
			return {
				slug: s.slug,
				order: n + 1,
				enabled: !!s.enabled,
				kind: "other",
				titleEn: spotLabel(s),
				titleMn: "",
				x: +full.x,
				y: +full.y
			};
		}).filter(function (h) { return isFinite(h.x) && isFinite(h.y); });
	}

	/* чирсэн байрлалыг meta-д ч тусгана (хэл солиход хуучин байрлал руу үсрэхгүй) */
	function metaMove(slug, x, y) {
		(meta.spots || []).forEach(function (m) { if (m.slug === slug) { m.x = x; m.y = y; } });
	}

	function indexOfSlug(slug) {
		var list = P.spots();
		for (var i = 0; i < list.length; i++) { if (list[i].slug === slug) { return list[i].i; } }
		return -1;
	}

	function highlight(slug) {
		selSlug = slug;
		if (viewer) { viewer.setSelectedEdit(slug); }
		Array.prototype.forEach.call(doc.querySelectorAll(".ode-spot[data-ode-slug]"), function (li) {
			li.classList.toggle("is-sel", li.getAttribute("data-ode-slug") === slug);
		});
	}

	function setAdd(on) {
		if (!viewer) { return; }
		viewer.setAddMode(on);
		var b = doc.querySelector("[data-ode-addspot]");
		var h = doc.querySelector("[data-ode-maphint]");
		if (b) { b.classList.toggle("is-on", on); }
		if (h) {
			h.textContent = on
				? "Схем дээр шинэ цэгийн байршлыг дарна уу (Esc — болих)."
				: "Цэгийг чирж байрлуулна. Давхар солихдоо схемийн дээд талын давхрын нэр дээр дарна.";
		}
	}

	function mountFloor(key) {
		var f = floorDef(key);
		if (!f || !Viewer) { return; }
		if (viewer) { viewer.destroy(); }
		floorKey = key;
		P.state.floor = key;

		var layers = mapEl.querySelectorAll("[data-fp-floor]");
		var layer = null;
		Array.prototype.forEach.call(layers, function (l) {
			var on = l.getAttribute("data-fp-floor") === key;
			l.classList.toggle("is-active", on);
			if (on) { layer = l; l.removeAttribute("aria-hidden"); } else { l.setAttribute("aria-hidden", "true"); }
			/* хуучин viewer-ийн цэгүүдийг цэвэрлэнэ */
			var sp = l.querySelector("[data-fp-spots]");
			if (sp) { sp.innerHTML = ""; }
		});

		var idx = fpFloors.indexOf(f);
		var other = fpFloors.length > 1 ? fpFloors[idx === 0 ? 1 : idx - 1] : null;

		viewer = new Viewer(mapEl.querySelector("[data-fp-stage]"), {
			layer: layer,
			plan: f,
			hotspots: viewerData(),
			editable: true,
			linkLabel: other ? { text: String(other.number), dir: +other.number > +f.number ? "up" : "down", aria: "Шат" } : null
		});

		viewer.on("select", function (slug) { if (slug) { highlight(slug); } });
		viewer.on("move", function (slug, x, y, final) {
			viewer.setHotspotPosition(slug, x, y);
			metaMove(slug, x, y);
			if (final) {
				var i = indexOfSlug(slug);
				if (i >= 0) { P.spotMove(i, x, y); }
			}
		});
		viewer.on("add", function (x, y) {
			setAdd(false);
			P.spotAdd(floorKey, x, y);
		});

		var title = mapEl.querySelector("[data-fp-title]");
		var next = mapEl.querySelector("[data-fp-title-next]");
		var goEl = mapEl.querySelector("[data-fp-title-go]");
		if (title) { title.textContent = f.floorTitle; }
		if (next && other) { next.textContent = other.number; }
		if (goEl && other) { goEl.setAttribute("data-dir", +other.number > +f.number ? "up" : "down"); }

		viewer.setSelectedEdit(selSlug);
	}

	if (mapEl && C && Viewer && fpFloors.length) {
		var startKey = P.state.floor && floorDef(P.state.floor) ? P.state.floor : fpFloors[0].key;
		mountFloor(startKey);

		var sw = mapEl.querySelector("[data-fp-floor-switch]");
		if (sw) {
			sw.addEventListener("click", function () {
				var idx = fpFloors.indexOf(floorDef(floorKey));
				var nextF = fpFloors[(idx + 1) % fpFloors.length];
				if (nextF && nextF.key !== floorKey) { setAdd(false); mountFloor(nextF.key); }
			});
		}

		var addBtn = doc.querySelector("[data-ode-addspot]");
		if (addBtn) {
			addBtn.addEventListener("click", function () { setAdd(!(viewer && viewer.addMode)); });
		}

		/* жагсаалтын цэг дээр дарвал схем дээр тодруулна (хэрэгтэй бол давхраа солино) */
		Array.prototype.forEach.call(doc.querySelectorAll(".ode-spot[data-ode-slug]"), function (li) {
			li.addEventListener("mousedown", function (e) {
				if (e.target.closest("[data-ode-f], [data-ode-pick], .ode-tools")) { return; }
				var slug = li.getAttribute("data-ode-slug");
				var s = P.spots().filter(function (o) { return o.slug === slug; })[0];
				if (s && s.floor !== floorKey) { mountFloor(s.floor); }
				highlight(slug);
			});
		});
	}

	/* ================================================================
	   editor.js-ийн дуудах функцууд
	   ================================================================ */

	function fieldOf(path) {
		return doc.querySelector('[data-ode-f="' + String(path).replace(/"/g, "") + '"]');
	}

	function caretEnd(el) {
		try {
			var r = doc.createRange();
			r.selectNodeContents(el);
			r.collapse(false);
			var s = window.getSelection();
			s.removeAllRanges();
			s.addRange(r);
		} catch (e) { /* ignore */ }
	}

	window.ODECanvas = {
		go: go,
		setLang: setLang,

		focusPath: function (path) {
			var el = fieldOf(path);
			if (!el) { return; }
			el.scrollIntoView({ block: "center" });
			el.focus();
			caretEnd(el);
		},

		mark: function (paths) {
			var first = null;
			(paths || []).forEach(function (p) {
				var el = fieldOf(p);
				if (el) {
					el.classList.add("ode-invalid");
					if (!first) { first = el; }
				}
			});
			if (first) {
				first.scrollIntoView({ block: "center" });
				first.focus();
			}
		}
	};

	window.addEventListener("scroll", function () {
		P.state.scroll[doc.body.getAttribute("data-od-page") || "home"] = window.scrollY;
	}, { passive: true });

	setLang(P.state.lang);
	go(P.state.page);
})();
