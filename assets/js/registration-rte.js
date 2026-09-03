/* ------------------------------------------------------------------
   Хуудсан дээрх текстийн багаж (зөвхөн засварлах горимд ачаалагдана).

   1. ХӨВӨГЧ TOOLBAR — "editor" төрлийн талбар дээр дарахад гарч ирнэ:
      үсгийн хэмжээ, тод/налуу/доогуур, өнгө, гарчиг, жагсаалт,
      зэрэгцүүлэлт, холбоос, загвар цэвэрлэх.

   2. БАЙРШЛЫН САМБАР — "Хөтөлбөр" хайрцгийн өргөн, байрлал, дотоод зай,
      дэвсгэр өнгө/тунгалагийг шууд хуудсан дээрээ тохируулна.

   Өөрчлөлт бүрийг registration-edit.js рүү дамжуулж (window.regEdit)
   "Хадгалах" товч дээр нэг дор хадгална. Гуравдагч сан хэрэглээгүй.
   ------------------------------------------------------------------ */

(function () {
	"use strict";

	var api = window.regEdit;
	if (!api) {
		return;
	}

	var SIZES  = [13, 14, 16, 18, 20, 24, 28, 32, 40, 48, 60];
	var BOTTOM = 64;   /* доод засварлагчийн мөрний өндөр */

	/* ------------------------------------------------------------------
	   Туслах
	   ------------------------------------------------------------------ */

	function el(tag, cls, html) {
		var n = document.createElement(tag);
		if (cls) {
			n.className = cls;
		}
		if (html !== undefined) {
			n.innerHTML = html;
		}
		return n;
	}

	function btn(cls, html, title) {
		var b = el("button", cls || "reg-rte-b", html);
		b.type = "button";
		if (title) {
			b.title = title;
		}
		return b;
	}

	function rgba(hex, pct) {
		hex = String(hex || "").replace("#", "");

		if (hex.length === 3) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}
		if (!/^[0-9a-f]{6}$/i.test(hex)) {
			return "";
		}

		var n = parseInt(hex, 16);

		return "rgba(" + ((n >> 16) & 255) + "," + ((n >> 8) & 255) + ","
			+ (n & 255) + "," + (Math.round(pct) / 100) + ")";
	}

	/* Хөвөгч цонхыг элементийн дэргэд байрлуулна */
	function place(box, rect, prefer) {
		box.classList.add("is-on");

		var bw = box.offsetWidth;
		var bh = box.offsetHeight;

		var left = rect.left + (rect.width / 2) - (bw / 2);
		left = Math.max(10, Math.min(left, window.innerWidth - bw - 10));

		var top = prefer === "below" ? rect.bottom + 10 : rect.top - bh - 10;

		if (top < 8) {
			top = rect.bottom + 10;
		}
		if (top + bh > window.innerHeight - BOTTOM) {
			top = rect.top - bh - 10;
		}

		/* Дэлгэцнээс гарахгүй байх — дээд/доод хоёр талаас нь барина */
		top = Math.max(8, Math.min(top, window.innerHeight - bh - BOTTOM));

		box.style.left = (left + window.pageXOffset) + "px";
		box.style.top  = (top + window.pageYOffset) + "px";
	}

	/* ------------------------------------------------------------------
	   1. Текстийн toolbar
	   ------------------------------------------------------------------ */

	var rich = [];

	Array.prototype.forEach.call(document.querySelectorAll("[data-reg-edit]"), function (node) {
		if ((node.getAttribute("data-reg-edit").split(":")[3] || "text") === "html") {
			rich.push(node);
		}
	});

	var bar     = el("div", "reg-rte-bar");
	var row     = el("div", "reg-rte-row");
	var current = null;
	var marks   = {};
	var saved   = null;   /* сүүлд тэмдэглэсэн текстийн сонголт */

	bar.appendChild(row);
	document.body.appendChild(bar);

	/* Багаж дээр дарахад сонголт алдагдахгүй байх */
	bar.addEventListener("mousedown", function (e) {
		if (e.target.tagName !== "SELECT" && e.target.tagName !== "INPUT") {
			e.preventDefault();
		}
	});

	/* Багаж дээр дарахад фокус алдагдвал сонголтоо буцааж сэргээнэ */
	function remember() {
		var sel = window.getSelection();

		if (sel && sel.rangeCount && current && current.contains(sel.anchorNode)) {
			saved = sel.getRangeAt(0).cloneRange();
		}
	}

	function restore() {
		if (!saved || !current) {
			return;
		}

		var sel = window.getSelection();

		try {
			sel.removeAllRanges();
			sel.addRange(saved);
		} catch (err) {}
	}

	function run(cmd, val) {
		if (!current) {
			return;
		}

		current.focus();
		restore();

		try {
			document.execCommand("styleWithCSS", false, true);
		} catch (err) {}

		document.execCommand(cmd, false, val === undefined ? null : val);
		commit();
	}

	function commit() {
		if (current) {
			api.sync(current);
		}
		refresh();
	}

	/* Үсгийн хэмжээ — execCommand нь <font size> үүсгэдэг тул span болгож солино */
	function setSize(px) {
		if (!current) {
			return;
		}

		current.focus();
		restore();

		try {
			document.execCommand("styleWithCSS", false, false);
		} catch (err) {}

		document.execCommand("fontSize", false, "7");

		Array.prototype.forEach.call(current.querySelectorAll('font[size="7"]'), function (f) {
			var sp = document.createElement("span");
			sp.style.fontSize = px + "px";

			while (f.firstChild) {
				sp.appendChild(f.firstChild);
			}

			f.parentNode.replaceChild(sp, f);
		});

		commit();
	}

	/* ---- Товчнууд ---- */

	var sizeSel = el("select", "reg-rte-sel");
	sizeSel.title = "Үсгийн хэмжээ";
	sizeSel.appendChild(new Option("Хэмжээ", ""));

	SIZES.forEach(function (px) {
		sizeSel.appendChild(new Option(px + " px", px));
	});

	sizeSel.addEventListener("change", function () {
		if (sizeSel.value) {
			setSize(parseInt(sizeSel.value, 10));
			sizeSel.value = "";
		}
	});

	row.appendChild(sizeSel);

	var styleSel = el("select", "reg-rte-sel");
	styleSel.title = "Загвар";

	[["", "Загвар"], ["P", "Энгийн"], ["H2", "Гарчиг том"], ["H3", "Гарчиг дунд"], ["H4", "Гарчиг жижиг"]]
		.forEach(function (o) {
			styleSel.appendChild(new Option(o[1], o[0]));
		});

	styleSel.addEventListener("change", function () {
		if (styleSel.value) {
			run("formatBlock", "<" + styleSel.value.toLowerCase() + ">");
			styleSel.value = "";
		}
	});

	row.appendChild(styleSel);
	row.appendChild(el("span", "reg-rte-sep"));

	[
		["bold",      '<i class="fa fa-bold"></i>',      "Тод (Ctrl+B)"],
		["italic",    '<i class="fa fa-italic"></i>',    "Налуу (Ctrl+I)"],
		["underline", '<i class="fa fa-underline"></i>', "Доогуур зураас"]
	].forEach(function (b) {
		var node = btn(null, b[1], b[2]);
		node.addEventListener("click", function () { run(b[0]); });
		marks[b[0]] = node;
		row.appendChild(node);
	});

	var color = el("input", "reg-rte-color");
	color.type = "color";
	color.title = "Үсгийн өнгө";
	color.value = "#ffffff";

	color.addEventListener("input", function () {
		run("foreColor", color.value);
	});

	row.appendChild(color);
	row.appendChild(el("span", "reg-rte-sep"));

	[
		["insertUnorderedList", '<i class="fa fa-list-ul"></i>', "Цэгэн жагсаалт"],
		["insertOrderedList",   '<i class="fa fa-list-ol"></i>', "Дугаарласан жагсаалт"]
	].forEach(function (b) {
		var node = btn(null, b[1], b[2]);
		node.addEventListener("click", function () { run(b[0]); });
		marks[b[0]] = node;
		row.appendChild(node);
	});

	row.appendChild(el("span", "reg-rte-sep"));

	[
		["justifyLeft",   '<i class="fa fa-align-left"></i>',   "Зүүн тийш"],
		["justifyCenter", '<i class="fa fa-align-center"></i>', "Голлуулах"],
		["justifyRight",  '<i class="fa fa-align-right"></i>',  "Баруун тийш"]
	].forEach(function (b) {
		var node = btn(null, b[1], b[2]);
		node.addEventListener("click", function () { run(b[0]); });
		marks[b[0]] = node;
		row.appendChild(node);
	});

	row.appendChild(el("span", "reg-rte-sep"));

	var linkBtn = btn(null, '<i class="fa fa-link"></i>', "Холбоос");

	linkBtn.addEventListener("click", function () {
		var url = window.prompt("Холбоосын хаяг:", "https://");

		if (!url) {
			return;
		}
		if (!/^(https?:|mailto:|tel:|#|\/)/i.test(url)) {
			url = "https://" + url.replace(/^\/+/, "");
		}

		run("createLink", url);
	});

	row.appendChild(linkBtn);

	var unlinkBtn = btn(null, '<i class="fa fa-chain-broken"></i>', "Холбоос салгах");
	unlinkBtn.addEventListener("click", function () { run("unlink"); });
	row.appendChild(unlinkBtn);

	var clearBtn = btn(null, '<i class="fa fa-eraser"></i>', "Загварыг цэвэрлэх");
	clearBtn.addEventListener("click", function () { run("removeFormat"); });
	row.appendChild(clearBtn);

	/* ---- Идэвхтэй товчийг тодруулах ---- */

	function refresh() {
		Object.keys(marks).forEach(function (cmd) {
			var on = false;

			try {
				on = document.queryCommandState(cmd);
			} catch (err) {}

			if (on) {
				marks[cmd].classList.add("is-on");
			} else {
				marks[cmd].classList.remove("is-on");
			}
		});
	}

	function showBar() {
		if (!current) {
			return;
		}

		var sel  = window.getSelection();
		var rect = null;

		if (sel && sel.rangeCount && current.contains(sel.anchorNode)) {
			rect = sel.getRangeAt(0).getBoundingClientRect();
		}

		if (!rect || (!rect.width && !rect.height)) {
			rect = current.getBoundingClientRect();
		}

		remember();
		place(bar, rect, "above");
		refresh();
	}

	function hideBar() {
		bar.classList.remove("is-on");
	}

	rich.forEach(function (node) {
		node.addEventListener("focus", function () {
			current = node;

			try {
				document.execCommand("defaultParagraphSeparator", false, "p");
			} catch (err) {}

			showBar();
		});

		node.addEventListener("keyup", showBar);
		node.addEventListener("mouseup", showBar);
	});

	document.addEventListener("selectionchange", function () {
		if (current && bar.classList.contains("is-on")) {
			remember();
			refresh();
		}
	});

	document.addEventListener("mousedown", function (e) {
		if (bar.contains(e.target)) {
			return;
		}
		if (current && current.contains(e.target)) {
			return;
		}
		if (rich.indexOf(e.target) === -1 && !e.target.closest("[data-reg-edit]")) {
			current = null;
			hideBar();
		}
	});

	window.addEventListener("resize", function () {
		if (current) {
			showBar();
		}
	});

	/* ------------------------------------------------------------------
	   2. Хайрцгийн байршлын самбар
	   ------------------------------------------------------------------ */

	var panels = document.querySelectorAll("[data-reg-panel]");
	if (!panels.length) {
		return;
	}

	var lay    = el("div", "reg-lay-panel");
	var layBox = null;   /* одоо тохируулж буй хайрцаг */
	var layBtn = null;

	document.body.appendChild(lay);

	lay.addEventListener("mousedown", function (e) {
		if (e.target.tagName !== "INPUT" && e.target.tagName !== "SELECT") {
			e.preventDefault();
		}
	});

	function stageVal(key, value) {
		if (layBox) {
			api.stage("block", layBox.getAttribute("data-reg-panel"), key, String(value));
		}
	}

	function paint() {
		if (!layBox) {
			return;
		}

		var pos   = layBox.getAttribute("data-pos");
		var width = parseInt(layBox.getAttribute("data-width"), 10) || 760;
		var pad   = parseInt(layBox.getAttribute("data-pad"), 10) || 0;
		var bg    = layBox.getAttribute("data-bg") || "";
		var op    = parseInt(layBox.getAttribute("data-opacity"), 10);

		if (isNaN(op)) {
			op = 70;
		}

		layBox.style.textAlign = layBox.getAttribute("data-align") || "left";
		layBox.style.maxWidth  = width + "px";
		layBox.style.padding   = pad + "px";

		layBox.style.marginLeft  = pos === "left" ? "0" : "auto";
		layBox.style.marginRight = pos === "right" ? "0" : "auto";

		var fill = bg === "" ? "" : rgba(bg, op);
		layBox.style.backgroundColor = fill;

		if (fill) {
			layBox.classList.add("reg-program-filled");
		} else {
			layBox.classList.remove("reg-program-filled");
		}
	}

	/* ---- Самбарын бүтэц ---- */

	lay.appendChild(el("div", "reg-lay-head", "Хайрцгийн тохиргоо"));

	function segRow(label, attr, key, opts) {
		var wrap = el("div", "reg-lay-row");
		wrap.appendChild(el("span", "reg-lay-lbl", label));

		var seg = el("div", "reg-lay-seg");

		opts.forEach(function (o) {
			var b = btn(null, o[1], o[2] || "");
			b.setAttribute("data-v", o[0]);

			b.addEventListener("click", function () {
				layBox.setAttribute(attr, o[0]);
				stageVal(key, o[0]);
				paint();
				sync();
			});

			seg.appendChild(b);
		});

		wrap.appendChild(seg);
		lay.appendChild(wrap);

		return seg;
	}

	var alignSeg = segRow("Текстийн зэрэгцүүлэлт", "data-align", "programAlign", [
		["left",   '<i class="fa fa-align-left"></i>',   "Зүүн"],
		["center", '<i class="fa fa-align-center"></i>', "Гол"],
		["right",  '<i class="fa fa-align-right"></i>',  "Баруун"]
	]);

	var posSeg = segRow("Хайрцгийн байрлал", "data-pos", "programPos", [
		["left",   '<i class="fa fa-chevron-left"></i>',  "Зүүн"],
		["center", '<i class="fa fa-arrows-h"></i>',      "Голд"],
		["right",  '<i class="fa fa-chevron-right"></i>', "Баруун"]
	]);

	function rangeRow(label, attr, key, min, max, step, suffix) {
		var wrap = el("div", "reg-lay-row");
		wrap.appendChild(el("span", "reg-lay-lbl", label));

		var line = el("div", "reg-lay-num");
		var inp  = el("input");
		inp.type = "range";
		inp.min  = min;
		inp.max  = max;
		inp.step = step;

		var out = el("span", "reg-lay-val");

		inp.addEventListener("input", function () {
			layBox.setAttribute(attr, inp.value);
			out.textContent = inp.value + suffix;
			paint();
		});

		inp.addEventListener("change", function () {
			stageVal(key, inp.value);
		});

		line.appendChild(inp);
		line.appendChild(out);
		wrap.appendChild(line);
		lay.appendChild(wrap);

		return { input: inp, out: out, suffix: suffix };
	}

	var widthRow = rangeRow("Өргөн", "data-width", "programWidth", 320, 1400, 20, "px");
	var padRow   = rangeRow("Дотоод зай", "data-pad", "programPad", 0, 80, 2, "px");
	var opRow    = rangeRow("Дэвсгэрийн тунгалаг", "data-opacity", "programOpacity", 0, 100, 5, "%");

	var bgWrap = el("div", "reg-lay-row");
	bgWrap.appendChild(el("span", "reg-lay-lbl", "Дэвсгэр өнгө"));

	var bgLine  = el("div", "reg-lay-color");
	var bgInput = el("input", "reg-rte-color");
	bgInput.type  = "color";
	bgInput.value = "#000000";

	bgInput.addEventListener("input", function () {
		layBox.setAttribute("data-bg", bgInput.value);
		paint();
	});

	bgInput.addEventListener("change", function () {
		stageVal("programBg", bgInput.value);
	});

	var bgClear = el("button", "reg-lay-clear", "Дэвсгэргүй");
	bgClear.type = "button";

	bgClear.addEventListener("click", function () {
		layBox.setAttribute("data-bg", "");
		stageVal("programBg", "");
		paint();
		sync();
	});

	bgLine.appendChild(bgInput);
	bgLine.appendChild(bgClear);
	bgWrap.appendChild(bgLine);
	lay.appendChild(bgWrap);

	/* ---- Самбарыг одоогийн утгуудаар дүүргэх ---- */

	function mark(seg, value) {
		Array.prototype.forEach.call(seg.children, function (b) {
			if (b.getAttribute("data-v") === value) {
				b.classList.add("is-on");
			} else {
				b.classList.remove("is-on");
			}
		});
	}

	function sync() {
		if (!layBox) {
			return;
		}

		mark(alignSeg, layBox.getAttribute("data-align") || "left");
		mark(posSeg, layBox.getAttribute("data-pos") || "center");

		[[widthRow, "data-width", 760], [padRow, "data-pad", 32], [opRow, "data-opacity", 70]]
			.forEach(function (r) {
				var v = parseInt(layBox.getAttribute(r[1]), 10);

				if (isNaN(v)) {
					v = r[2];
				}

				r[0].input.value = v;
				r[0].out.textContent = v + r[0].suffix;
			});

		var bg = layBox.getAttribute("data-bg") || "";
		if (/^#[0-9a-f]{6}$/i.test(bg)) {
			bgInput.value = bg;
		}
	}

	function openLay(box, opener) {
		layBox = box;

		if (layBtn) {
			layBtn.classList.remove("is-on");
		}

		layBtn = opener;
		layBtn.classList.add("is-on");

		sync();
		place(lay, opener.getBoundingClientRect(), "below");
	}

	function closeLay() {
		lay.classList.remove("is-on");

		if (layBtn) {
			layBtn.classList.remove("is-on");
			layBtn = null;
		}

		layBox = null;
	}

	Array.prototype.forEach.call(panels, function (box) {
		var opener = btn("reg-lay-open", '<i class="fa fa-sliders"></i>', "Хайрцгийн байрлал, өргөн, дэвсгэр");

		opener.addEventListener("click", function (e) {
			e.preventDefault();
			e.stopPropagation();

			if (layBox === box && lay.classList.contains("is-on")) {
				closeLay();
			} else {
				openLay(box, opener);
			}
		});

		box.appendChild(opener);
	});

	document.addEventListener("mousedown", function (e) {
		if (lay.contains(e.target) || (layBtn && layBtn.contains(e.target))) {
			return;
		}

		closeLay();
	});
}());
