/*!
 * CP Admin -> Өдөрлөг -> Хуудас засах (shell).
 *
 * Бүх агуулга нэг model-д байна. Доорх iframe-д нийтийн /openday хуудсыг
 * (ижил PHP template) ЭНЭ model-оор "засах горимоор" зуруулна
 * (canvas.php). iframe доторх canvas.js нь window.parent.OpenDayEditor-оор
 * дамжуулан model-ийг өөрчилнө:
 *   - текст бичихэд        -> set()        (дахин зурахгүй)
 *   - мөр нэмэх/зөөх/устгах -> op()/add()   (дахин зурна)
 *   - цэг чирэх            -> spotMove()   (дахин зурахгүй)
 * "Хадгалах" нь өмнөх шиг /userPost/openday руу нэг удаа илгээнэ.
 */
(function () {
	"use strict";

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
	var PAGE_OF = { agenda: "schedule", spot: "map", info: "info", faq: "faq" };

	function clean(it) {
		var o = {};
		FIELDS.forEach(function (f) {
			var v = it ? it[f] : undefined;
			o[f] = v === undefined || v === null ? (f === "enabled" ? true : (f === "x" || f === "y" ? 0.5 : "")) : v;
		});
		return o;
	}

	function fromCfg() {
		var m = { settings: {}, items: {} };
		Object.keys(cfg.settings || {}).forEach(function (k) { m.settings[k] = cfg.settings[k] == null ? "" : String(cfg.settings[k]); });
		KINDS.forEach(function (k) { m.items[k] = ((cfg.items || {})[k] || []).map(clean); });
		return m;
	}

	var model = fromCfg();
	var revision = cfg.revision || 0;
	var savedSnap = JSON.stringify(model);
	var saving = false;

	var hashPage = (window.location.hash || "").replace("#", "");
	var state = {
		page: ["home", "schedule", "map", "info", "faq"].indexOf(hashPage) >= 0 ? hashPage : "home",
		lang: "mn",
		device: "desktop",
		floor: null,
		scroll: {},
		focus: null,
		invalid: null
	};
	try {
		var saved = JSON.parse(localStorage.getItem("ode-view") || "{}");
		if (saved.device === "phone") { state.device = "phone"; }
		if (saved.lang === "en") { state.lang = "en"; }
	} catch (e) { /* ignore */ }

	function esc(s) {
		return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	}

	function alertBox(type, html) {
		$("ode-alerts").innerHTML = html ? '<div class="alert alert-' + type + '">' + html + "</div>" : "";
	}

	/* ---------------------------------------------------------------- */
	/* төлөв                                                             */
	/* ---------------------------------------------------------------- */

	function dirty() { return JSON.stringify(model) !== savedSnap; }

	function setStatus(text, cls) {
		var el = $("ode-status");
		el.textContent = text;
		el.className = "ode-status " + (cls || "text-muted");
	}

	function updateState() {
		var d = dirty();
		$("ode-save").disabled = saving || !d;
		$("ode-discard").disabled = saving || !d;
		if (!saving) {
			if (d) { setStatus("Хадгалаагүй өөрчлөлт байна", "is-dirty"); } else if ($("ode-status").className.indexOf("is-ok") < 0) { setStatus(""); }
		}
	}

	window.addEventListener("beforeunload", function (e) {
		if (dirty()) {
			e.preventDefault();
			e.returnValue = "";
		}
	});

	/* ---------------------------------------------------------------- */
	/* model-ийн зам: "s.titleMn", "agenda.3.titleMn"                     */
	/* ---------------------------------------------------------------- */

	function parsePath(path) {
		var p = String(path).split(".");
		if (p[0] === "s" && p.length === 2) { return { s: true, key: p[1] }; }
		if (p.length === 3 && model.items[p[0]] && model.items[p[0]][+p[1]]) {
			return { kind: p[0], i: +p[1], key: p[2], it: model.items[p[0]][+p[1]] };
		}
		return null;
	}

	function get(path) {
		var r = parsePath(path);
		if (!r) { return ""; }
		return r.s ? (model.settings[r.key] || "") : r.it[r.key];
	}

	function set(path, value) {
		var r = parsePath(path);
		if (!r) { return; }
		if (r.s) { model.settings[r.key] = value; } else { r.it[r.key] = value; }
		updateState();
	}

	function uniqueSlug() {
		var n = 1;
		var has = function (s) { return model.items.spot.some(function (o) { return o.slug === s; }); };
		while (has("spot-" + n)) { n++; }
		return "spot-" + n;
	}

	/* ---------------------------------------------------------------- */
	/* canvas (iframe) зурах — хуучин нь шинэ нь ачаалагдтал харагдана   */
	/* ---------------------------------------------------------------- */

	var device = $("ode-device");
	var frame = null;
	var renderSeq = 0;
	var renderTimer = 0;

	function canvasWin() {
		return frame && frame.contentWindow ? frame.contentWindow : null;
	}

	function rememberScroll() {
		var w = canvasWin();
		if (w) {
			try { state.scroll[state.page] = w.scrollY || 0; } catch (e) { /* ignore */ }
		}
	}

	/* хэд хэдэн өөрчлөлт зэрэг ирвэл нэг л удаа зурна */
	function render() {
		window.clearTimeout(renderTimer);
		renderTimer = window.setTimeout(doRender, 30);
	}

	function doRender() {
		var seq = ++renderSeq;
		rememberScroll();
		$("ode-loading").classList.add("is-on");

		var body = new URLSearchParams();
		body.set("csrf", cfg.csrf);
		body.set("page", state.page);
		body.set("payload", JSON.stringify(model));

		fetch(cfg.canvasUrl, {
			method: "POST",
			credentials: "same-origin",
			headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
			body: body.toString()
		}).then(function (r) {
			return r.text().then(function (t) { return { ok: r.ok, status: r.status, text: t }; });
		}).then(function (res) {
			if (seq !== renderSeq) { return; }
			if (!res.ok || res.text.indexOf("data-ode-meta") < 0) {
				$("ode-loading").classList.remove("is-on");
				alertBox("danger", "Хуудсыг зурж чадсангүй (HTTP " + res.status + "). " +
					(res.status === 403 ? "Нэвтрэлт эсвэл хуудасны хугацаа дууссан байж магадгүй — хуудсаа дахин ачаална уу." : esc(res.text.replace(/<[^>]*>/g, " ").slice(0, 300))));
				return;
			}

			var next = document.createElement("iframe");
			next.className = "ode-frame is-next";
			next.setAttribute("title", "Өдөрлөгийн хуудас — засах горим");
			next.addEventListener("load", function () {
				if (seq !== renderSeq) { if (next.parentNode) { next.parentNode.removeChild(next); } return; }
				var old = frame;
				frame = next;
				applyView();
				try { next.contentWindow.scrollTo(0, state.scroll[state.page] || 0); } catch (e) { /* ignore */ }
				next.classList.remove("is-next");
				if (old && old.parentNode) { old.parentNode.removeChild(old); }
				$("ode-loading").classList.remove("is-on");

				var c = next.contentWindow.ODECanvas;
				if (c && state.focus) { c.focusPath(state.focus); }
				if (c && state.invalid) { c.mark(state.invalid); }
				state.focus = null;
				state.invalid = null;
			});
			next.srcdoc = res.text;
			device.appendChild(next);
		}).catch(function () {
			if (seq !== renderSeq) { return; }
			$("ode-loading").classList.remove("is-on");
			alertBox("danger", "Сүлжээний алдаа — хуудсыг зурж чадсангүй. Дахин оролдоно уу.");
		});
	}

	/* ---------------------------------------------------------------- */
	/* toolbar: хуудас, хэл, дэлгэц                                       */
	/* ---------------------------------------------------------------- */

	function markButtons() {
		Array.prototype.forEach.call(document.querySelectorAll("#ode-pages [data-page]"), function (b) {
			b.className = "btn btn-sm " + (b.getAttribute("data-page") === state.page ? "btn-primary" : "btn-white");
		});
		Array.prototype.forEach.call(document.querySelectorAll("#ode-langs [data-lang]"), function (b) {
			b.className = "btn btn-sm " + (b.getAttribute("data-lang") === state.lang ? "btn-primary" : "btn-white");
		});
		Array.prototype.forEach.call(document.querySelectorAll("#ode-devices [data-device]"), function (b) {
			b.className = "btn btn-sm " + (b.getAttribute("data-device") === state.device ? "btn-primary" : "btn-white");
		});
		try { window.history.replaceState(null, "", "#" + state.page); } catch (e) { /* ignore */ }
	}

	function applyView() {
		markButtons();
		device.setAttribute("data-device", state.device);

		var w = canvasWin();
		if (w && w.ODECanvas) {
			w.ODECanvas.setLang(state.lang);
			w.ODECanvas.go(state.page);
		}
		try { localStorage.setItem("ode-view", JSON.stringify({ device: state.device, lang: state.lang })); } catch (e) { /* ignore */ }
	}

	function setPage(p) {
		if (p === state.page) { return; }
		rememberScroll();
		state.page = p;
		applyView();
		var w = canvasWin();
		if (w) { try { w.scrollTo(0, state.scroll[p] || 0); } catch (e) { /* ignore */ } }
	}

	Array.prototype.forEach.call(document.querySelectorAll("#ode-pages [data-page]"), function (b) {
		b.addEventListener("click", function () { setPage(b.getAttribute("data-page")); });
	});
	Array.prototype.forEach.call(document.querySelectorAll("#ode-langs [data-lang]"), function (b) {
		b.addEventListener("click", function () { state.lang = b.getAttribute("data-lang"); applyView(); });
	});
	Array.prototype.forEach.call(document.querySelectorAll("#ode-devices [data-device]"), function (b) {
		b.addEventListener("click", function () { state.device = b.getAttribute("data-device"); applyView(); });
	});

	/* ---------------------------------------------------------------- */
	/* canvas-д өгөх API                                                  */
	/* ---------------------------------------------------------------- */

	window.OpenDayEditor = {
		state: state,
		floors: cfg.floors || [],
		get: get,
		set: set,
		render: render,

		/* canvas дотор цэс дарахад */
		onPage: function (p) {
			if (p === state.page) { return; }
			rememberScroll();
			state.page = p;
			markButtons();
		},

		setLang: function (l) {
			state.lang = l === "en" ? "en" : "mn";
			applyView();
		},

		/* мөрийн үйлдэл: up / down / toggle / del */
		op: function (kind, i, op) {
			var list = model.items[kind];
			var it = list && list[i];
			if (!it) { return; }

			if (op === "up" || op === "down") {
				var j = op === "up" ? i - 1 : i + 1;
				if (j < 0 || j >= list.length) { return; }
				list[i] = list[j];
				list[j] = it;
			} else if (op === "toggle") {
				it.enabled = !it.enabled;
			} else if (op === "del") {
				var name = it.titleMn || it.titleEn || "Энэ мөр";
				var msg = "\"" + name + "\"-ийг устгах уу?";
				var used = 0;
				if (kind === "spot") {
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
				}
			} else {
				return;
			}
			updateState();
			render();
		},

		add: function (kind) {
			if (!model.items[kind] || kind === "spot") { return; }
			var it = clean(null);
			it.x = "";
			it.y = "";
			if (kind === "info") { it.icon = "info"; }
			model.items[kind].push(it);
			state.focus = kind + "." + (model.items[kind].length - 1) + ".title" + (state.lang === "en" ? "En" : "Mn");
			updateState();
			render();
		},

		spotMove: function (i, x, y) {
			var s = model.items.spot[i];
			if (!s) { return; }
			s.x = Math.round(x * 100000) / 100000;
			s.y = Math.round(y * 100000) / 100000;
			updateState();
		},

		spotAdd: function (floor, x, y) {
			var it = clean(null);
			it.slug = uniqueSlug();
			it.floor = floor;
			it.icon = "pin";
			it.x = Math.round(x * 100000) / 100000;
			it.y = Math.round(y * 100000) / 100000;
			model.items.spot.push(it);
			state.floor = floor;
			state.focus = "spot." + (model.items.spot.length - 1) + ".title" + (state.lang === "en" ? "En" : "Mn");
			updateState();
			render();
		},

		/* цэгийг өөр давхарт: тэр давхрын голд тавина */
		spotFloor: function (i, floor) {
			var s = model.items.spot[i];
			if (!s || s.floor === floor) { return; }
			s.floor = floor;
			s.x = 0.5;
			s.y = 0.5;
			state.floor = floor;
			updateState();
			render();
		},

		spots: function () {
			return model.items.spot.map(function (s, i) {
				return { i: i, slug: s.slug, floor: s.floor, titleMn: s.titleMn, titleEn: s.titleEn, enabled: s.enabled };
			});
		}
	};

	/* ---------------------------------------------------------------- */
	/* хадгалах                                                          */
	/* ---------------------------------------------------------------- */

	function validate() {
		var bad = [];
		var msgs = [];
		if (!(model.settings.titleMn || "").trim()) {
			bad.push("s.titleMn");
			msgs.push("Нүүр: гарчиг (MN) хоосон байна.");
		}
		var names = { agenda: "Хөтөлбөр", spot: "Схемийн цэг", info: "Мэдээлэл", faq: "Асуулт" };
		["agenda", "spot", "info", "faq"].forEach(function (k) {
			model.items[k].forEach(function (it, i) {
				if (!String(it.titleMn || "").trim() && !String(it.titleEn || "").trim()) {
					bad.push(k + "." + i + ".titleMn");
					msgs.push(names[k] + " #" + (i + 1) + ": гарчиг/нэр хоосон байна.");
				}
			});
		});
		return { paths: bad, msgs: msgs };
	}

	function pageOfPath(path) {
		var k = String(path).split(".")[0];
		if (k === "s") {
			var key = String(path).split(".")[1] || "";
			if (/^sched/.test(key)) { return "schedule"; }
			if (/^map/.test(key)) { return "map"; }
			if (/^info/.test(key)) { return "info"; }
			if (/^(faq|ask)/.test(key)) { return "faq"; }
			return "home";
		}
		return PAGE_OF[k] || "home";
	}

	$("ode-save").addEventListener("click", function () {
		var v = validate();
		if (v.paths.length) {
			alertBox("warning", "<b>Хадгалахаас өмнө улаанаар тэмдэглэсэн талбарыг бөглөнө үү:</b><br>" + v.msgs.map(esc).join("<br>"));
			state.lang = "mn";
			setPage(pageOfPath(v.paths[0]));
			applyView();
			var w = canvasWin();
			if (w && w.ODECanvas) { w.ODECanvas.mark(v.paths); }
			return;
		}
		alertBox("", "");
		saving = true;
		updateState();
		setStatus("Хадгалж байна…", "text-muted");

		var sent = JSON.stringify(model);
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

	$("ode-discard").addEventListener("click", function () {
		if (!window.confirm("Хадгалаагүй бүх өөрчлөлтийг болих уу?")) { return; }
		model = JSON.parse(savedSnap);
		alertBox("", "");
		updateState();
		render();
	});

	/* ---------------------------------------------------------------- */
	/* эхлэл                                                             */
	/* ---------------------------------------------------------------- */

	markButtons();
	device.setAttribute("data-device", state.device);
	updateState();
	render();
})();
