/* ------------------------------------------------------------------
   Бүртгэлийн хуудсыг ШУУД ХУУДСАН ДЭЭР нь засварлах.

   - Текст дээр дарж шууд бичнэ
   - Зураг / дэвсгэр дээр дарж шинэ зураг эсвэл видео байршуулна
     (Cloudflare R2 руу, тохируулаагүй бол серверийн диск рүү)
   - Блокийг дээш/доош зөөх, нуух/харуулах
   - Ctrl+S товчоор хадгална

   Гуравдагч сан хэрэглэхгүй (vanilla JS) — хуудас хөнгөн хэвээр.
   ------------------------------------------------------------------ */

(function () {
	"use strict";

	var bar = document.getElementById("regEditBar");
	if (!bar) {
		return;
	}

	var NONCE   = bar.getAttribute("data-nonce");
	var MAXB    = parseInt(bar.getAttribute("data-maxbytes"), 10) || 0;
	var MAXT    = bar.getAttribute("data-maxtext") || "";
	var msgEl   = document.getElementById("regEditMsg");
	var saveBtn = document.getElementById("regEditSave");
	var undoBtn = document.getElementById("regEditUndo");
	var fileEl  = document.getElementById("regEditFile");

	var pending = { block: {}, setting: {} };
	var dirty = 0;
	var busy = false;
	var mediaTarget = null;

	/* ---------------- Туслах ---------------- */

	function say(text, kind) {
		msgEl.textContent = text;
		msgEl.className = "reg-editbar-msg" + (kind ? " is-" + kind : "");
	}

	function markDirty() {
		dirty++;
		saveBtn.disabled = false;
		undoBtn.disabled = false;
		say(dirty + " өөрчлөлт хадгалагдаагүй байна", "warn");
	}

	function stage(scope, id, key, value) {
		if (scope === "setting") {
			pending.setting[key] = value;
		} else {
			if (!pending.block[id]) {
				pending.block[id] = {};
			}
			pending.block[id][key] = value;
		}
		markDirty();
	}

	/* Серверийн хариуг задлана. JSON биш бол шалтгааныг нь аль болох тодруулна. */
	function parseRes(xhr) {
		var text = xhr.responseText || "";

		try {
			return JSON.parse(text);
		} catch (e) {}

		/* Хөгжүүлэгчид зориулж бүтэн хариуг консолд үлдээнэ */
		if (window.console && console.warn) {
			console.warn("registration: JSON биш хариу", xhr.status, text.slice(0, 2000));
		}

		var why = "";

		if (xhr.status === 0) {
			why = "холболт тасарлаа";
		} else if (xhr.status === 413) {
			why = "файл серверийн хязгаараас том";
		} else if (xhr.status === 403 || xhr.status === 406) {
			why = "сервер хүсэлтийг хаалаа (mod_security)";
		} else if (xhr.status >= 500) {
			why = "серверт дотоод алдаа гарлаа";
		} else if (text.indexOf("<!DOCTYPE") === 0 || text.indexOf("<html") >= 0) {
			why = "хуудас бүхэлдээ буцлаа";
		}

		return {
			ok: 0,
			error: "Сервер хариу буруу буцаалаа (HTTP " + xhr.status + (why ? ", " + why : "") + ")."
		};
	}

	function send(body, done, onProgress) {
		var xhr = new XMLHttpRequest();

		xhr.open("POST", "/registration", true);
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

		if (onProgress && xhr.upload) {
			xhr.upload.onprogress = onProgress;
		}

		xhr.onload = function () {
			done(parseRes(xhr));
		};
		xhr.onerror = function () {
			done({ ok: 0, error: "Сүлжээний алдаа. Интернэт холболтоо шалгана уу." });
		};
		xhr.ontimeout = function () {
			done({ ok: 0, error: "Хугацаа хэтэрлээ. Файл жижигрүүлээд дахин оролдоно уу." });
		};

		xhr.send(body);
	}

	function post(data, done) {
		var body = new FormData();
		body.append("regNonce", NONCE);

		Object.keys(data).forEach(function (k) {
			body.append(k, data[k]);
		});

		send(body, done);
	}

	/* ---------------- Текст засварлах ---------------- */

	var editables = document.querySelectorAll("[data-reg-edit]");

	Array.prototype.forEach.call(editables, function (el) {
		var parts = el.getAttribute("data-reg-edit").split(":");
		var scope = parts[0], id = parts[1], key = parts[2], mode = parts[3] || "text";

		el.setAttribute("contenteditable", "true");
		el.setAttribute("spellcheck", "false");
		el.classList.add("reg-ed");

		if (mode === "text" && el.textContent.trim() === "") {
			el.classList.add("reg-ed-empty");
		}

		el._regOriginal = mode === "html" ? el.innerHTML : el.innerText;

		el.addEventListener("focus", function () {
			el.classList.remove("reg-ed-empty");
		});

		el.addEventListener("input", function () {
			el.classList.remove("reg-ed-empty");
		});

		el.addEventListener("blur", function () {
			var value = mode === "html" ? el.innerHTML : el.innerText;

			if (value !== el._regOriginal) {
				el._regOriginal = value;
				stage(scope, id, key, value);
			}

			if (mode === "text" && el.textContent.trim() === "") {
				el.classList.add("reg-ed-empty");
			}
		});

		/* Зөвхөн энгийн текст буулгана — гаднаас ирсэн загварыг оруулахгүй */
		el.addEventListener("paste", function (e) {
			if (mode === "html") {
				return;
			}
			e.preventDefault();
			var text = (e.clipboardData || window.clipboardData).getData("text");
			document.execCommand("insertText", false, text);
		});

		/* Enter дарахад форм илгээхгүй, мөр таслана */
		el.addEventListener("keydown", function (e) {
			if (e.key === "Escape") {
				el.blur();
			}
		});
	});

	/* Засварлаж байх үед линк / товч ажиллуулахгүй */
	document.addEventListener("click", function (e) {
		var hit = e.target.closest("[data-reg-edit], [data-reg-media]");
		if (!hit) {
			return;
		}

		var tag = hit.tagName;
		if (tag === "A" || tag === "BUTTON" || hit.closest("a") || hit.closest("button")) {
			e.preventDefault();
		}
	}, true);

	/* Формыг санамсаргүй илгээхээс сэргийлнэ */
	var regForm = document.querySelector(".reg-form");
	if (regForm) {
		regForm.addEventListener("submit", function (e) {
			e.preventDefault();
			say("Засварлах горимд байхад форм илгээгдэхгүй.", "warn");
		});
	}

	/* ---------------- Медиа солих ---------------- */

	var menu = document.createElement("div");
	menu.className = "reg-media-menu";
	menu.style.display = "none";
	document.body.appendChild(menu);

	function hideMenu() {
		menu.style.display = "none";
	}

	function openPicker(accept) {
		fileEl.value = "";
		fileEl.setAttribute("accept", accept);
		fileEl.click();
	}

	function showMenu(x, y, accept) {
		menu.innerHTML = "";

		var items = [];
		if (accept === "image" || accept === "both") {
			items.push(["fa fa-picture-o", "Зураг тавих", "image/*"]);
		}
		if (accept === "video" || accept === "both") {
			items.push(["fa fa-video-camera", "Видео тавих", "video/mp4,video/webm"]);
		}
		items.push(["fa fa-times", "Хоослох", ""]);

		items.forEach(function (it) {
			var b = document.createElement("button");
			b.type = "button";
			b.innerHTML = '<i class="' + it[0] + '"></i> ' + it[1];
			b.addEventListener("click", function () {
				hideMenu();
				if (it[2] === "") {
					clearMedia();
				} else {
					openPicker(it[2]);
				}
			});
			menu.appendChild(b);
		});

		menu.style.display = "block";

		var w = menu.offsetWidth || 180;
		var h = menu.offsetHeight || 140;
		var top = y + 8;

		/* Доод засварлагчийн мөрнөөс дээш эргүүлж гаргана */
		if (top + h > window.innerHeight - 8) {
			top = Math.max(8, y - h - 16);
		}

		menu.style.left = Math.max(8, Math.min(x, window.innerWidth - w - 8)) + "px";
		menu.style.top = (top + window.scrollY) + "px";
	}

	document.addEventListener("click", function (e) {
		if (!menu.contains(e.target)) {
			hideMenu();
		}
	});

	Array.prototype.forEach.call(document.querySelectorAll("[data-reg-media]"), function (el) {
		el.classList.add("reg-ed-media");

		el.addEventListener("click", function (e) {
			/* Текст засаж байгаа бол дэвсгэр солих цэс гарч ирэхгүй */
			if (e.target !== el && e.target.closest && e.target.closest("[data-reg-edit]")) {
				return;
			}

			e.preventDefault();
			e.stopPropagation();

			var parts = el.getAttribute("data-reg-media").split(":");
			mediaTarget = { el: el, scope: parts[0], id: parts[1], key: parts[2], accept: parts[3] || "image" };

			showMenu(e.clientX, e.clientY, mediaTarget.accept);
		});
	});

	/* Дэвсгэрийн зорилтот элемент ба түлхүүрүүд.
	   data-reg-bg="page" — хуудас даяарх ТОГТМОЛ дэвсгэр. */
	function bgTarget(el) {
		var isPage = el.getAttribute("data-reg-bg") === "page";

		return {
			page:   isPage,
			host:   isPage ? document.querySelector("#regPageBg .reg-page-bg-media") : el,
			picKey: isPage ? "pageBgPic" : "bgPic",
			vidKey: isPage ? "pageBgVideo" : "bgVideo",
			vidCls: isPage ? "reg-page-bg-video" : "reg-hero-video"
		};
	}

	function clearMedia() {
		if (!mediaTarget) {
			return;
		}

		var t = mediaTarget;

		if (t.el.hasAttribute("data-reg-bg")) {
			var bg = bgTarget(t.el);

			if (bg.host) {
				bg.host.style.backgroundImage = "";

				var vid = bg.host.querySelector("." + bg.vidCls);
				if (vid) {
					vid.parentNode.removeChild(vid);
				}
			}

			stage(t.scope, t.id, bg.picKey, "");
			stage(t.scope, t.id, bg.vidKey, "");
		} else if (t.el.tagName === "IMG") {
			t.el.removeAttribute("src");
			t.el.classList.add("reg-empty-media");
			stage(t.scope, t.id, t.key, "");
		} else {
			stage(t.scope, t.id, t.key, "");
		}

		say("Хоосон болголоо — Хадгалах дарна уу", "warn");
	}

	function sizeText(bytes) {
		if (bytes >= 1048576) {
			return Math.round(bytes / 1048576) + " MB";
		}
		if (bytes >= 1024) {
			return Math.round(bytes / 1024) + " KB";
		}

		return bytes + " B";
	}

	var ALLOWED = /\.(jpe?g|png|gif|webp|avif|svg|mp4|webm|ogv|mov)$/i;

	fileEl.addEventListener("change", function () {
		if (!fileEl.files || !fileEl.files[0] || !mediaTarget) {
			return;
		}

		var file = fileEl.files[0];
		var t = mediaTarget;

		if (busy) {
			return;
		}

		/* Сервер рүү явахаас өмнө илэрхий алдааг энд барина */
		if (!ALLOWED.test(file.name)) {
			say("Энэ төрлийн файл болохгүй. jpg, png, webp, gif, svg, mp4, webm сонгоно уу.", "err");
			fileEl.value = "";
			return;
		}

		if (MAXB > 0 && file.size > MAXB) {
			say("Файл хэт том (" + sizeText(file.size) + "). Сервер дээд тал нь "
				+ (MAXT || sizeText(MAXB)) + " хүлээж авна.", "err");
			fileEl.value = "";
			return;
		}

		busy = true;
		say("Байршуулж байна: " + file.name + " (" + sizeText(file.size) + ")", "busy");

		var body = new FormData();
		body.append("regNonce", NONCE);
		body.append("regAction", "upload");
		body.append("file", file);

		send(body, function (res) {
			busy = false;
			fileEl.value = "";

			if (!res.ok) {
				say(res.error || "Байршуулж чадсангүй.", "err");
				return;
			}

			applyMedia(t, res);
			say("Байршууллаа (" + (res.where === "local" ? "сервер" : "R2") + ") — Хадгалах дарна уу", "warn");
		}, function (e) {
			if (e.lengthComputable) {
				var pct = Math.round((e.loaded / e.total) * 100);
				say("Байршуулж байна " + pct + "%" + (pct === 100 ? " — сервер боловсруулж байна..." : ""), "busy");
			}
		});
	});

	function applyMedia(t, res) {
		var el = t.el;

		/* Дэвсгэр — зураг ба видеог автоматаар ялгана */
		if (el.hasAttribute("data-reg-bg")) {
			var bg = bgTarget(el);

			if (!bg.host) {
				stage(t.scope, t.id, t.key, res.url);
				return;
			}

			var old = bg.host.querySelector("." + bg.vidCls);
			if (old) {
				old.parentNode.removeChild(old);
			}

			if (res.kind === "video") {
				bg.host.style.backgroundImage = "";

				var v = document.createElement("video");
				v.className = bg.vidCls;
				v.autoplay = true; v.muted = true; v.loop = true;
				v.setAttribute("playsinline", "");
				v.src = res.src;
				bg.host.insertBefore(v, bg.host.firstChild);

				stage(t.scope, t.id, bg.vidKey, res.url);
				stage(t.scope, t.id, bg.picKey, "");
			} else {
				bg.host.style.backgroundImage = "url(" + JSON.stringify(res.src) + ")";
				stage(t.scope, t.id, bg.picKey, res.url);
				stage(t.scope, t.id, bg.vidKey, "");
			}

			document.body.classList.add("reg-has-bg");
			return;
		}

		/* Icon байсан бол зураг болгож солино */
		if (el.tagName === "I") {
			var img = document.createElement("img");
			img.className = "reg-info-pic reg-ed-media";
			img.src = res.src;
			img.setAttribute("data-reg-media", el.getAttribute("data-reg-media"));
			el.parentNode.replaceChild(img, el);

			img.addEventListener("click", function (e) {
				e.preventDefault();
				e.stopPropagation();
				mediaTarget = { el: img, scope: t.scope, id: t.id, key: t.key, accept: t.accept };
				showMenu(e.clientX, e.clientY, t.accept);
			});

			stage(t.scope, t.id, t.key, res.url);
			return;
		}

		if (el.tagName === "IMG") {
			el.src = res.src;
			el.classList.remove("reg-empty-media");
		}

		stage(t.scope, t.id, t.key, res.url);
	}

	/* ---------------- Блокийн үйлдлүүд ---------------- */

	Array.prototype.forEach.call(document.querySelectorAll(".reg-eb-btn"), function (btn) {
		btn.addEventListener("click", function () {
			var wrap = btn.closest("[data-reg-block]");
			if (!wrap || busy) {
				return;
			}

			if (dirty > 0 && !window.confirm("Хадгалаагүй өөрчлөлт байна. Үргэлжлүүлбэл алдагдана. Үргэлжлүүлэх үү?")) {
				return;
			}

			busy = true;
			say("Түр хүлээнэ үү...", "busy");

			post({
				regAction: "blockop",
				blockID: wrap.getAttribute("data-reg-block"),
				op: btn.getAttribute("data-op")
			}, function (res) {
				busy = false;
				if (res.ok) {
					window.location.reload();
				} else {
					say(res.error || "Болсонгүй.", "err");
				}
			});
		});
	});

	/* ---------------- Дэд мөр нэмэх / устгах / зөөх ---------------- */

	function subOp(op, id, ask) {
		if (busy) {
			return;
		}

		if (dirty > 0 && !window.confirm("Хадгалаагүй өөрчлөлт байна. Үргэлжлүүлбэл алдагдана. Үргэлжлүүлэх үү?")) {
			return;
		}

		if (ask && !window.confirm(ask)) {
			return;
		}

		busy = true;
		say("Түр хүлээнэ үү...", "busy");

		post({ regAction: "blockop", blockID: id, op: op }, function (res) {
			busy = false;

			if (res.ok) {
				window.location.reload();
			} else {
				say(res.error || "Болсонгүй.", "err");
			}
		});
	}

	Array.prototype.forEach.call(document.querySelectorAll("[data-reg-subop]"), function (b) {
		b.addEventListener("click", function (e) {
			e.preventDefault();
			e.stopPropagation();

			var op = b.getAttribute("data-reg-subop");

			subOp(
				op,
				b.getAttribute("data-reg-sub"),
				op === "subdel" ? "Энэ мөрийг устгах уу? Буцаах боломжгүй." : ""
			);
		});
	});

	/* ---------------- Хадгалах ---------------- */

	function save() {
		if (busy || dirty === 0) {
			return;
		}

		/* Фокустай талбарын сүүлийн өөрчлөлтийг барьж авна */
		if (document.activeElement && document.activeElement.hasAttribute
			&& document.activeElement.hasAttribute("data-reg-edit")) {
			document.activeElement.blur();
		}

		busy = true;
		saveBtn.disabled = true;
		say("Хадгалж байна...", "busy");

		post({ regAction: "save", payload: JSON.stringify(pending) }, function (res) {
			busy = false;

			if (!res.ok) {
				saveBtn.disabled = false;
				say(res.error || "Хадгалж чадсангүй.", "err");
				return;
			}

			pending = { block: {}, setting: {} };
			dirty = 0;
			saveBtn.disabled = true;
			undoBtn.disabled = true;
			say("Хадгаллаа ✓", "ok");
		});
	}

	saveBtn.addEventListener("click", save);

	undoBtn.addEventListener("click", function () {
		if (window.confirm("Хадгалаагүй бүх өөрчлөлтийг цуцлах уу?")) {
			window.location.reload();
		}
	});

	document.addEventListener("keydown", function (e) {
		if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "s") {
			e.preventDefault();
			save();
		}
	});

	window.addEventListener("beforeunload", function (e) {
		if (dirty > 0) {
			e.preventDefault();
			e.returnValue = "";
		}
	});

	/* ---------------- Хуудасны дэвсгэр солих товч ---------------- */

	var bgBtn = document.getElementById("regEditBg");
	var bgEl  = document.getElementById("regPageBg");

	if (bgBtn && bgEl) {
		bgBtn.addEventListener("click", function (e) {
			e.preventDefault();
			e.stopPropagation();

			var attr  = bgEl.getAttribute("data-reg-media") || "setting:0:pageBgPic:both";
			var parts = attr.split(":");

			mediaTarget = {
				el: bgEl,
				scope: parts[0],
				id: parts[1],
				key: parts[2],
				accept: parts[3] || "both"
			};

			var r = bgBtn.getBoundingClientRect();
			showMenu(r.left, r.bottom, mediaTarget.accept);
		});
	}

	/* ------------------------------------------------------------------
	   Гадаад холбоос — registration-rte.js (текстийн багаж) ашиглана.
	   Тусад нь файл болгосон нь: багаж нь зөвхөн засварлах горимд
	   ачаалагдах ба энэ файлын логикийг хөндөхгүй.
	   ------------------------------------------------------------------ */

	function syncEl(el) {
		if (!el || !el.getAttribute || !el.getAttribute("data-reg-edit")) {
			return;
		}

		var parts = el.getAttribute("data-reg-edit").split(":");
		var mode  = parts[3] || "text";
		var value = mode === "html" ? el.innerHTML : el.innerText;

		if (value !== el._regOriginal) {
			el._regOriginal = value;
			stage(parts[0], parts[1], parts[2], value);
		}
	}

	window.regEdit = {
		stage: stage,
		sync:  syncEl,
		say:   say
	};
}());
