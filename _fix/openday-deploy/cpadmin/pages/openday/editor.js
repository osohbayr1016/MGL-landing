/*!
 * CP Admin -> Өдөрлөг: /openday тур хуудасны засварлагч.
 *
 * Бүх агуулга (ерөнхий мэдээлэл + хөтөлбөр, схемийн цэг, хийх зүйлс,
 * мэдээлэл, асуулт) нэг model-д байна; "Хадгалах" нэг удаа илгээнэ.
 * Схемийн цэгийг Офис схемийн viewer-ийн (FloorPlanViewer) editable
 * горимоор чирж байрлуулна.
 */
(function () {
	"use strict";

	var C = window.FloorPlanCore;
	var Viewer = window.FloorPlanViewer;

	function $(id) { return document.getElementById(id); }

	var cfg;
	try {
		cfg = JSON.parse($("ode-data").textContent);
	} catch (e) {
		$("ode-alerts").innerHTML = '<div class="alert alert-danger">Засварлагчийн өгөгдлийг уншиж чадсангүй. Хуудсаа дахин ачаална уу.</div>';
		return;
	}

	var KINDS = ["agenda", "spot", "activity", "info", "faq"];
	var FIELDS = ["enabled", "slug", "titleMn", "titleEn", "bodyMn", "bodyEn", "start", "end", "spot", "floor", "x", "y", "icon"];

	var uidSeq = 0;
	var model = { settings: {}, items: {} };
	var revision = cfg.revision || 0;
	var savedSnap = "";
	var saving = false;
	var openRows = {};

	Object.keys(cfg.settings || {}).forEach(function (k) { model.settings[k] = cfg.settings[k] == null ? "" : String(cfg.settings[k]); });
	KINDS.forEach(function (k) {
		model.items[k] = ((cfg.items || {})[k] || []).map(function (it) {
			var o = { _uid: ++uidSeq };
			FIELDS.forEach(function (f) { o[f] = it[f] === undefined ? (f === "enabled" ? true : "") : it[f]; });
			return o;
		});
	});

	var floors = cfg.floors || [];
	var icons = cfg.icons || [];

	/* ---------------------------------------------------------------- */
	/* туслахууд                                                         */
	/* ---------------------------------------------------------------- */

	function esc(s) {
		return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	}

	function payload() {
		var items = {};
		KINDS.forEach(function (k) {
			items[k] = model.items[k].map(function (it) {
				var o = {};
				FIELDS.forEach(function (f) { o[f] = it[f]; });
				return o;
			});
		});
		return { settings: model.settings, items: items };
	}

	function snap() { return JSON.stringify(payload()); }

	function floorOf(key) {
		for (var i = 0; i < floors.length; i++) { if (floors[i].key === key) { return floors[i]; } }
		return null;
	}

	function floorLabel(key) {
		var f = floorOf(key);
		return f ? f.number + "F" : key;
	}

	function spotBySlug(slug) {
		var list = model.items.spot;
		for (var i = 0; i < list.length; i++) { if (list[i].slug === slug) { return list[i]; } }
		return null;
	}

	function uniqueSlug(base) {
		var n = 1;
		while (spotBySlug(base + "-" + n)) { n++; }
		return base + "-" + n;
	}

	function title(it) {
		return it.titleMn || it.titleEn || "";
	}

	function timeRange(it) {
		if (it.start && it.end) { return it.start + "–" + it.end; }
		return it.start || it.end || "";
	}

	function summary(kind, it) {
		var t = title(it) || "(гарчиггүй)";
		if (kind === "agenda" || kind === "activity") {
			var tr = timeRange(it) || (kind === "activity" ? "Өдөржин" : "--:--");
			return '<b class="ode-sum-time">' + esc(tr) + "</b> " + esc(t);
		}
		if (kind === "spot") {
			return '<i class="fa ' + esc(iconFa(it.icon)) + '"></i> ' + esc(t);
		}
		if (kind === "info") {
			return '<i class="fa ' + esc(iconFa(it.icon)) + '"></i> ' + esc(t);
		}
		return esc(t);
	}

	function iconFa(key) {
		for (var i = 0; i < icons.length; i++) { if (icons[i].key === key) { return icons[i].fa; } }
		return "fa-map-marker";
	}

	/* ---------------------------------------------------------------- */
	/* төлөв (хадгалаагүй өөрчлөлт)                                       */
	/* ---------------------------------------------------------------- */

	function setStatus(text, cls) {
		var el = $("ode-status");
		el.textContent = text;
		el.className = "ode-status " + (cls || "text-muted");
	}

	function dirty() { return snap() !== savedSnap; }

	function updateState() {
		var d = dirty();
		$("ode-save").disabled = saving || !d;
		if (!saving) {
			if (d) { setStatus("Хадгалаагүй өөрчлөлт байна", "is-dirty"); } else if ($("ode-status").className.indexOf("is-ok") < 0) { setStatus(""); }
		}
		KINDS.forEach(function (k) {
			var b = document.querySelector('[data-count="' + k + '"]');
			if (b) { b.textContent = model.items[k].length; }
		});
	}

	window.addEventListener("beforeunload", function (e) {
		if (dirty()) {
			e.preventDefault();
			e.returnValue = "";
		}
	});

	function alertBox(type, html) {
		$("ode-alerts").innerHTML = html ? '<div class="alert alert-' + type + '">' + html + "</div>" : "";
	}

	/* ---------------------------------------------------------------- */
	/* tab-ууд                                                           */
	/* ---------------------------------------------------------------- */

	var tabLinks = Array.prototype.slice.call(document.querySelectorAll(".ode-tabs a[data-pane]"));

	function showPane(name) {
		tabLinks.forEach(function (a) {
			var on = a.getAttribute("data-pane") === name;
			a.parentNode.classList.toggle("active", on);
			$("ode-" + a.getAttribute("data-pane")).classList.toggle("active", on);
		});
		try { window.history.replaceState(null, "", "#" + name); } catch (e) { /* ignore */ }
		if (name === "spot") {
			if (!viewer) { mountFloor(curFloor); } else { viewer.relayout(); }
		}
	}

	tabLinks.forEach(function (a) {
		a.addEventListener("click", function (e) {
			e.preventDefault();
			showPane(a.getAttribute("data-pane"));
		});
	});

	/* ---------------------------------------------------------------- */
	/* ерөнхий                                                           */
	/* ---------------------------------------------------------------- */

	Array.prototype.forEach.call(document.querySelectorAll("[data-set]"), function (el) {
		var k = el.getAttribute("data-set");
		el.value = model.settings[k] || "";
		el.addEventListener("input", function () {
			model.settings[k] = el.value;
			updateState();
		});
	});

	/* ---------------------------------------------------------------- */
	/* мөрийн жагсаалт                                                    */
	/* ---------------------------------------------------------------- */

	var LABELS = {
		agenda:   { title: "Гарчиг", body: "Тайлбар" },
		activity: { title: "Гарчиг", body: "Тайлбар" },
		info:     { title: "Гарчиг", body: "Агуулга" },
		faq:      { title: "Асуулт", body: "Хариулт" },
		spot:     { title: "Нэр", body: "Тайлбар (схем дээр цэг дарахад гарна)" }
	};

	function spotOptions(sel) {
		var html = '<option value="">— байршилгүй —</option>';
		floors.forEach(function (f) {
			var list = model.items.spot.filter(function (s) { return s.floor === f.key; });
			if (!list.length) { return; }
			html += '<optgroup label="' + esc(f.number + "F") + '">';
			list.forEach(function (s) {
				html += '<option value="' + esc(s.slug) + '"' + (s.slug === sel ? " selected" : "") + ">" + esc(title(s) || s.slug) + "</option>";
			});
			html += "</optgroup>";
		});
		return html;
	}

	function iconOptions(sel) {
		return icons.map(function (ic) {
			return '<option value="' + esc(ic.key) + '"' + (ic.key === sel ? " selected" : "") + ">" + esc(ic.label) + "</option>";
		}).join("");
	}

	function floorOptions(sel) {
		return floors.map(function (f) {
			return '<option value="' + esc(f.key) + '"' + (f.key === sel ? " selected" : "") + ">" + esc(f.number + "F") + "</option>";
		}).join("");
	}

	function pair(kind, base, multi, maxlen) {
		var lab = LABELS[kind][base];
		var input = function (lang) {
			var f = base + lang;
			return multi
				? '<textarea class="form-control" rows="3" maxlength="' + maxlen + '" data-f="' + f + '"></textarea>'
				: '<input type="text" class="form-control" maxlength="' + maxlen + '" data-f="' + f + '">';
		};
		return '<div class="ode-pair">' +
			'<div class="form-group"><label>' + esc(lab) + " — MN" + (base === "title" ? ' <span class="text-danger">*</span>' : "") + "</label>" + input("Mn") + "</div>" +
			'<div class="form-group"><label>' + esc(lab) + " — EN</label>" + input("En") + "</div>" +
			"</div>";
	}

	function rowBody(kind, it) {
		var html = "";
		if (kind === "agenda" || kind === "activity") {
			html += '<div class="ode-pair ode-times">' +
				'<div class="form-group"><label>Эхлэх цаг' + (kind === "activity" ? ' <span class="text-muted">(хоосон = өдөржин)</span>' : "") + '</label><input type="time" class="form-control" data-f="start"></div>' +
				'<div class="form-group"><label>Дуусах цаг</label><input type="time" class="form-control" data-f="end"></div>' +
				"</div>";
		}
		if (kind === "info" || kind === "spot") {
			html += '<div class="ode-pair">' +
				'<div class="form-group"><label>Icon / ангилал</label><select class="form-control" data-f="icon">' + iconOptions(it.icon) + "</select></div>" +
				(kind === "spot"
					? '<div class="form-group"><label>Давхар</label><select class="form-control" data-f="floor">' + floorOptions(it.floor) + "</select></div>"
					: "<div></div>") +
				"</div>";
		}
		html += pair(kind, "title", false, 160);
		html += pair(kind, "body", true, 1200);
		if (kind === "agenda" || kind === "activity" || kind === "info") {
			html += '<div class="form-group"><label>Байршил <span class="text-muted">(схемийн цэг — зочин "харах" товчоор шууд очно)</span></label>' +
				'<select class="form-control" data-f="spot">' + spotOptions(it.spot) + "</select></div>";
		}
		if (kind === "spot") {
			html += '<p class="help-block ode-help">Линк: <code>/openday?area=' + esc(it.slug) + "</code> — энэ цэгийг нээсэн байдлаар хуудсыг нээнэ.</p>";
		}
		return html;
	}

	function renderList(kind) {
		var host = document.querySelector('[data-list="' + kind + '"]');
		if (!host) { return; }
		var list = model.items[kind];
		var fk = floors[curFloor] ? floors[curFloor].key : null;
		var shown = kind === "spot" && fk !== null ? list.filter(function (s) { return s.floor === fk; }) : list;

		var html = "";
		if (kind === "spot") {
			html += '<div class="ode-list-head">' + esc(fk !== null ? floors[curFloor].number + "F" : "") + " дээрх цэгүүд <span class=\"text-muted\">(" + shown.length + ")</span></div>";
		}
		if (!shown.length) {
			html += '<div class="ode-empty text-muted">' + (kind === "spot" ? "Энэ давхарт цэг алга. \"Цэг нэмэх\" дараад схем дээр дарна уу." : "Одоогоор хоосон байна.") + "</div>";
		}
		shown.forEach(function (it) {
			var idx = list.indexOf(it);
			html += '<div class="ode-row' + (openRows[it._uid] ? " is-open" : "") + (it.enabled ? "" : " is-off") + (kind === "spot" && it.slug === selSpot ? " is-selected" : "") + '" data-uid="' + it._uid + '">' +
				'<div class="ode-row-head">' +
				'<span class="ode-row-n">' + (idx + 1) + "</span>" +
				'<button type="button" class="ode-row-sum" data-op="toggle">' + summary(kind, it) + "</button>" +
				'<label class="ode-row-on" title="Хуудсанд харуулах"><input type="checkbox" data-f="enabled"' + (it.enabled ? " checked" : "") + "> Харуулах</label>" +
				'<span class="btn-group btn-group-xs">' +
				'<button type="button" class="btn btn-white" data-op="up" title="Дээш"' + (idx === 0 ? " disabled" : "") + '><i class="fa fa-arrow-up"></i></button>' +
				'<button type="button" class="btn btn-white" data-op="down" title="Доош"' + (idx === list.length - 1 ? " disabled" : "") + '><i class="fa fa-arrow-down"></i></button>' +
				'<button type="button" class="btn btn-white" data-op="del" title="Устгах"><i class="fa fa-trash text-danger"></i></button>' +
				"</span></div>" +
				'<div class="ode-row-body">' + (openRows[it._uid] ? rowBody(kind, it) : "") + "</div>" +
				"</div>";
		});
		host.innerHTML = html;

		/* утгуудыг DOM-оос биш, model-оос тавина (escape хийх шаардлагагүй) */
		Array.prototype.forEach.call(host.querySelectorAll(".ode-row.is-open"), function (row) {
			fillRow(kind, row);
		});
	}

	function itemOf(kind, row) {
		var uid = +row.getAttribute("data-uid");
		var list = model.items[kind];
		for (var i = 0; i < list.length; i++) { if (list[i]._uid === uid) { return list[i]; } }
		return null;
	}

	function fillRow(kind, row) {
		var it = itemOf(kind, row);
		Array.prototype.forEach.call(row.querySelectorAll(".ode-row-body [data-f]"), function (el) {
			var f = el.getAttribute("data-f");
			el.value = it[f] == null ? "" : it[f];
		});
	}

	function renderAll() {
		KINDS.forEach(renderList);
		updateState();
	}

	/* ---- жагсаалтын үйлдлүүд (event delegation) ---- */

	Array.prototype.forEach.call(document.querySelectorAll("[data-list]"), function (host) {
		var kind = host.getAttribute("data-list");

		host.addEventListener("click", function (e) {
			var btn = e.target.closest("[data-op]");
			var row = e.target.closest(".ode-row");
			if (!btn || !row) { return; }
			var it = itemOf(kind, row);
			var list = model.items[kind];
			var i = list.indexOf(it);
			var op = btn.getAttribute("data-op");

			if (op === "toggle") {
				openRows[it._uid] = !openRows[it._uid];
				if (kind === "spot") { selectSpot(it.slug, false); }
				renderList(kind);
				return;
			}
			if (op === "up" || op === "down") {
				var j = op === "up" ? i - 1 : i + 1;
				if (j < 0 || j >= list.length) { return; }
				list[i] = list[j];
				list[j] = it;
			}
			if (op === "del") {
				var msg = "\"" + (title(it) || "Энэ мөр") + "\"-ийг устгах уу?";
				if (kind === "spot") {
					var used = 0;
					["agenda", "activity", "info"].forEach(function (k) {
						model.items[k].forEach(function (o) { if (o.spot === it.slug) { used++; } });
					});
					if (used) { msg += "\n\nЭнэ цэгийг " + used + " мөр байршлаар ашиглаж байгаа — тэдгээрийн байршил хоосорно."; }
				}
				if (!window.confirm(msg)) { return; }
				list.splice(i, 1);
				if (kind === "spot") {
					["agenda", "activity", "info"].forEach(function (k) {
						model.items[k].forEach(function (o) { if (o.spot === it.slug) { o.spot = ""; } });
					});
					if (selSpot === it.slug) { selSpot = null; }
					refreshViewer();
				}
			}
			renderAll();
		});

		var onField = function (e) {
			var el = e.target;
			var f = el.getAttribute && el.getAttribute("data-f");
			var row = el.closest ? el.closest(".ode-row") : null;
			if (!f || !row) { return; }
			var it = itemOf(kind, row);
			it[f] = el.type === "checkbox" ? el.checked : el.value;

			if (f === "enabled") { row.classList.toggle("is-off", !el.checked); }
			row.querySelector(".ode-row-sum").innerHTML = summary(kind, it);

			if (kind === "spot") {
				if (f === "floor" && e.type === "change") {
					/* өөр давхарт шилжүүлбэл тэр давхрыг нээж, цэгийг голд нь тавина */
					it.x = 0.5;
					it.y = 0.5;
					selSpot = it.slug;
					for (var k = 0; k < floors.length; k++) { if (floors[k].key === it.floor) { mountFloor(k); } }
				} else if (f === "titleMn" || f === "titleEn" || f === "enabled") {
					refreshViewer();
				}
				/* нэр солигдвол бусад tab-ын "Байршил" сонголтыг шинэчилнэ */
				if (f === "titleMn" || f === "floor") { ["agenda", "activity", "info"].forEach(renderList); }
			}
			updateState();
		};
		host.addEventListener("input", onField);
		host.addEventListener("change", onField);
	});

	Array.prototype.forEach.call(document.querySelectorAll("[data-add]"), function (btn) {
		btn.addEventListener("click", function () {
			var kind = btn.getAttribute("data-add");
			var it = { _uid: ++uidSeq };
			FIELDS.forEach(function (f) { it[f] = f === "enabled" ? true : ""; });
			if (kind === "info") { it.icon = "info"; }
			model.items[kind].push(it);
			openRows[it._uid] = true;
			renderAll();
			var row = document.querySelector('[data-list="' + kind + '"] .ode-row[data-uid="' + it._uid + '"]');
			if (row) {
				row.scrollIntoView({ block: "center" });
				var first = row.querySelector('[data-f="titleMn"]');
				if (first) { first.focus(); }
			}
		});
	});

	/* ---------------------------------------------------------------- */
	/* схем                                                              */
	/* ---------------------------------------------------------------- */

	var stage = $("ode-stage");
	var floorHost = $("ode-floor-host");
	var viewer = null;
	var curFloor = 0;
	var selSpot = null;

	function viewerData() {
		var key = floors[curFloor] ? floors[curFloor].key : "";
		return model.items.spot.filter(function (s) { return s.floor === key; }).map(function (s, i) {
			return {
				slug: s.slug,
				order: i + 1,
				enabled: !!s.enabled,
				kind: "other",
				titleEn: title(s) || s.slug,
				titleMn: "",
				x: +s.x,
				y: +s.y
			};
		});
	}

	function refreshViewer() {
		if (!viewer) { return; }
		viewer.setData(viewerData());
		viewer.setSelectedEdit(selSpot);
	}

	function renderFloorTabs() {
		var host = $("ode-floor-tabs");
		host.innerHTML = "";
		floors.forEach(function (f, i) {
			var b = document.createElement("button");
			b.type = "button";
			b.className = "btn btn-sm " + (i === curFloor ? "btn-primary" : "btn-white");
			b.textContent = f.number + "F";
			b.addEventListener("click", function () { if (i !== curFloor) { selSpot = null; mountFloor(i); } });
			host.appendChild(b);
		});
	}

	function mountFloor(i) {
		if (!C || !Viewer || !floors.length) {
			$("ode-maphint").textContent = "Схемийн файл ачаалагдсангүй — цэгийн байршлыг засах боломжгүй.";
			return;
		}
		if (viewer) { viewer.destroy(); }
		curFloor = i;
		setAdd(false);

		var f = floors[i];
		floorHost.innerHTML =
			'<div class="fp-floor is-active is-ready">' +
			'<div class="fp-world" data-fp-world><img class="fp-image" alt="" draggable="false"></div>' +
			'<div class="fp-spots" data-fp-spots></div>' +
			"</div>";
		var layer = floorHost.firstChild;
		if (f.imageUrl) { layer.querySelector("img").src = f.imageUrl; }

		viewer = new Viewer(stage, {
			layer: layer,
			plan: { imageWidth: f.imageWidth, imageHeight: f.imageHeight, bounds: f.bounds, link: f.link },
			hotspots: viewerData(),
			editable: true,
			linkLabel: floors.length > 1 ? { text: floors[i === 0 ? 1 : i - 1].number, dir: i === 0 ? "up" : "down", aria: "Шат" } : null
		});
		viewer.setSelectedEdit(selSpot);

		viewer.on("select", function (slug) {
			if (slug) { selectSpot(slug, true); }
		});

		viewer.on("move", function (slug, x, y, final) {
			var s = spotBySlug(slug);
			if (!s) { return; }
			s.x = Math.round(x * 100000) / 100000;
			s.y = Math.round(y * 100000) / 100000;
			viewer.setHotspotPosition(slug, s.x, s.y);
			if (final) { updateState(); }
		});

		viewer.on("add", function (x, y) {
			setAdd(false);
			var it = { _uid: ++uidSeq };
			FIELDS.forEach(function (fl) { it[fl] = fl === "enabled" ? true : ""; });
			it.slug = uniqueSlug("spot");
			it.floor = floors[curFloor].key;
			it.icon = "pin";
			it.titleMn = "Шинэ цэг";
			it.x = Math.round(x * 100000) / 100000;
			it.y = Math.round(y * 100000) / 100000;
			model.items.spot.push(it);
			openRows[it._uid] = true;
			selSpot = it.slug;
			refreshViewer();
			renderAll();
			var inp = document.querySelector('[data-list="spot"] .ode-row[data-uid="' + it._uid + '"] [data-f="titleMn"]');
			if (inp) { inp.focus(); inp.select(); }
		});

		renderFloorTabs();
		renderList("spot");
	}

	function selectSpot(slug, fromMap) {
		selSpot = slug;
		if (viewer) { viewer.setSelectedEdit(slug); }
		if (fromMap) {
			var s = spotBySlug(slug);
			if (s) { openRows[s._uid] = true; }
			renderList("spot");
			/* зөвхөн жагсаалтын хайрцгийг гүйлгэнэ — хуудас хөдөлбөл чирж буй
			   цэг курсорын доороос зугтаана */
			var host = document.querySelector('[data-list="spot"]');
			var row = host.querySelector(".ode-row.is-selected");
			if (row) {
				var top = row.offsetTop;   /* host нь position:relative */
				if (top < host.scrollTop || top + row.offsetHeight > host.scrollTop + host.clientHeight) {
					host.scrollTop = Math.max(0, top - 8);
				}
			}
		} else {
			Array.prototype.forEach.call(document.querySelectorAll('[data-list="spot"] .ode-row'), function (r) {
				var it = itemOf("spot", r);
				r.classList.toggle("is-selected", !!it && it.slug === slug);
			});
		}
	}

	function setAdd(on) {
		if (viewer) { viewer.setAddMode(on); }
		$("ode-add-spot").classList.toggle("active", on);
		$("ode-maphint").textContent = on
			? "Схем дээр шинэ цэгийн байршлыг дарна уу. (Esc — болих)"
			: "Цэгийг чирж байрлуулна. Цэг нэмэхдээ товчийг дараад схем дээр дарна.";
	}

	$("ode-add-spot").addEventListener("click", function () { setAdd(!(viewer && viewer.addMode)); });
	document.addEventListener("keydown", function (e) { if (e.key === "Escape" && viewer && viewer.addMode) { setAdd(false); } });

	/* ---------------------------------------------------------------- */
	/* хадгалах                                                          */
	/* ---------------------------------------------------------------- */

	function validate() {
		var errs = [];
		if (!(model.settings.titleMn || "").trim()) { errs.push("Ерөнхий: гарчиг (MN) хоосон байна."); }
		var names = { agenda: "Хөтөлбөр", spot: "Схемийн цэг", activity: "Хийж болох зүйлс", info: "Практик мэдээлэл", faq: "Түгээмэл асуулт" };
		KINDS.forEach(function (k) {
			model.items[k].forEach(function (it, i) {
				if (!String(it.titleMn || "").trim() && !String(it.titleEn || "").trim()) {
					errs.push(names[k] + " #" + (i + 1) + ": гарчиг/нэр хоосон байна.");
				}
			});
		});
		return errs;
	}

	$("ode-save").addEventListener("click", function () {
		var errs = validate();
		if (errs.length) {
			alertBox("warning", "<b>Хадгалахаас өмнө засна уу:</b><br>" + errs.map(esc).join("<br>"));
			return;
		}
		alertBox("", "");
		saving = true;
		updateState();
		setStatus("Хадгалж байна…", "text-muted");

		var sent = snap();
		var body = new URLSearchParams();
		body.set("frmPost", "openDaySave");
		body.set("csrf", cfg.csrf);
		body.set("revision", String(revision));
		body.set("payload", sent);

		fetch(cfg.saveUrl, {
			method: "POST",
			credentials: "same-origin",
			headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
			body: body.toString()
		}).then(function (r) {
			return r.text().then(function (t) {
				var j = null;
				try { j = JSON.parse(t); } catch (e) { /* login хуудас гэх мэт */ }
				return { status: r.status, json: j };
			});
		}).then(function (res) {
			saving = false;
			if (res.json && res.json.ok) {
				revision = res.json.revision;
				savedSnap = sent;
				setStatus("Хадгалагдлаа ✓", "is-ok");
				updateState();
				return;
			}
			var msg = res.json && res.json.error ? res.json.error
				: "Сервер хариу буруу буцаалаа (HTTP " + res.status + "). Нэвтрэлт дууссан байж магадгүй — шинэ цонхонд нэвтэрч ороод дахин хадгална уу.";
			alertBox("danger", esc(msg));
			setStatus("Хадгалсангүй", "is-dirty");
			updateState();
		}).catch(function () {
			saving = false;
			alertBox("danger", "Сүлжээний алдаа — хадгалсангүй. Интернэтээ шалгаад дахин оролдоно уу.");
			setStatus("Хадгалсангүй", "is-dirty");
			updateState();
		});
	});

	/* ---------------------------------------------------------------- */
	/* эхлэл                                                             */
	/* ---------------------------------------------------------------- */

	savedSnap = snap();
	renderAll();

	var hash = (window.location.hash || "").replace("#", "");
	var startPane = tabLinks.some(function (a) { return a.getAttribute("data-pane") === hash; }) ? hash : "general";
	showPane(startPane);
})();
