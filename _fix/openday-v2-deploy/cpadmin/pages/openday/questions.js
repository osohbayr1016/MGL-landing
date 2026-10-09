/*!
 * CP Admin -> Өдөрлөг -> Ирсэн асуулт.
 * Зочид /openday хуудасны "Асуултаа асуух" формоор илгээсэн асуултыг
 * 4 секунд тутам шалгаж (shared hosting дээр websocket байхгүй тул polling),
 * шинэ асуултыг дээр нь нэмж тодруулна.
 */
(function () {
	"use strict";

	function $(id) { return document.getElementById(id); }

	var cfg = JSON.parse($("odq-data").textContent);
	var POLL_MS = 4000;

	var items = {};        /* id -> {id, text, name, lang, status, at} */
	var lastId = 0;
	var filter = "new";
	var firstLoad = true;
	var failCount = 0;
	var baseTitle = document.title;

	function esc(s) {
		return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	}

	/* "2026-10-17 12:41:05" -> "12:41" (өнөөдөр) эсвэл "10.16 12:41" */
	function when(at) {
		var m = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/.exec(at || "");
		if (!m) { return at || ""; }
		var d = new Date();
		var today = d.getFullYear() === +m[1] && d.getMonth() + 1 === +m[2] && d.getDate() === +m[3];
		return (today ? "" : m[2] + "." + m[3] + " ") + m[4] + ":" + m[5];
	}

	function setLive(ok, text) {
		$("odq-live").className = "odq-live" + (ok ? " is-on" : " is-off");
		$("odq-live-text").textContent = text;
	}

	/* ---------------- дуут дохио (хүсвэл) ---------------- */

	var audio = null;
	try { $("odq-sound").checked = localStorage.getItem("odq-sound") === "1"; } catch (e) { /* ignore */ }
	$("odq-sound").addEventListener("change", function () {
		try { localStorage.setItem("odq-sound", this.checked ? "1" : "0"); } catch (e) { /* ignore */ }
		if (this.checked) { beep(); }
	});

	function beep() {
		if (!$("odq-sound").checked) { return; }
		try {
			audio = audio || new (window.AudioContext || window.webkitAudioContext)();
			var o = audio.createOscillator();
			var g = audio.createGain();
			o.type = "sine";
			o.frequency.value = 880;
			g.gain.setValueAtTime(0.0001, audio.currentTime);
			g.gain.exponentialRampToValueAtTime(0.2, audio.currentTime + 0.02);
			g.gain.exponentialRampToValueAtTime(0.0001, audio.currentTime + 0.35);
			o.connect(g);
			g.connect(audio.destination);
			o.start();
			o.stop(audio.currentTime + 0.4);
		} catch (e) { /* дуу дэмжихгүй */ }
	}

	/* ---------------- зурах ---------------- */

	function counts() {
		var c = { "new": 0, done: 0, all: 0 };
		Object.keys(items).forEach(function (k) {
			c.all++;
			if (items[k].status === 1) { c.done++; } else { c["new"]++; }
		});
		return c;
	}

	function render(freshIds) {
		var list = Object.keys(items).map(function (k) { return items[k]; })
			.filter(function (q) { return filter === "all" || (filter === "done" ? q.status === 1 : q.status !== 1); })
			.sort(function (a, b) { return b.id - a.id; });

		var fresh = {};
		(freshIds || []).forEach(function (id) { fresh[id] = true; });

		$("odq-list").innerHTML = list.map(function (q) {
			return '<div class="odq-item' + (q.status === 1 ? " is-done" : "") + (fresh[q.id] ? " is-fresh" : "") + '" data-id="' + q.id + '">' +
				'<div class="odq-meta">' +
				'<span class="odq-n">#' + q.id + "</span>" +
				'<span class="odq-time"><i class="fa fa-clock-o"></i> ' + esc(when(q.at)) + "</span>" +
				(q.name ? '<span class="odq-name"><i class="fa fa-user"></i> ' + esc(q.name) + "</span>" : '<span class="odq-name text-muted">нэргүй</span>') +
				'<span class="label ' + (q.lang === "en" ? "label-info" : "label-default") + '">' + (q.lang === "en" ? "EN" : "MN") + "</span>" +
				"</div>" +
				'<div class="odq-text">' + esc(q.text).replace(/\n/g, "<br>") + "</div>" +
				'<div class="odq-actions">' +
				(q.status === 1
					? '<button type="button" class="btn btn-xs btn-white" data-op="new"><i class="fa fa-undo"></i> Шинэ болгох</button>'
					: '<button type="button" class="btn btn-xs btn-primary" data-op="done"><i class="fa fa-check"></i> Хариулсан</button>') +
				' <button type="button" class="btn btn-xs btn-white" data-op="delete"><i class="fa fa-trash text-danger"></i> Устгах</button>' +
				"</div></div>";
		}).join("");

		$("odq-empty").hidden = list.length > 0;
		$("odq-empty").textContent = filter === "new" ? "Шинэ асуулт алга." : (filter === "done" ? "Хариулсан асуулт алга." : "Одоогоор асуулт алга.");

		var c = counts();
		["new", "done", "all"].forEach(function (k) {
			document.querySelector('[data-count="' + k + '"]').textContent = c[k];
		});
		document.title = (c["new"] > 0 ? "(" + c["new"] + ") " : "") + baseTitle;
	}

	/* ---------------- шүүлтүүр ---------------- */

	Array.prototype.forEach.call(document.querySelectorAll("#odq-filter [data-filter]"), function (b) {
		b.addEventListener("click", function () {
			filter = b.getAttribute("data-filter");
			Array.prototype.forEach.call(document.querySelectorAll("#odq-filter [data-filter]"), function (x) {
				x.className = "btn btn-sm " + (x === b ? "btn-primary" : "btn-white");
			});
			render();
		});
	});

	/* ---------------- үйлдэл ---------------- */

	$("odq-list").addEventListener("click", function (e) {
		var b = e.target.closest("[data-op]");
		var row = e.target.closest(".odq-item");
		if (!b || !row) { return; }
		var id = +row.getAttribute("data-id");
		var op = b.getAttribute("data-op");
		if (op === "delete" && !window.confirm("#" + id + " асуултыг устгах уу?")) { return; }

		b.disabled = true;
		var body = new URLSearchParams();
		body.set("frmPost", "openDayQuestion");
		body.set("csrf", cfg.csrf);
		body.set("op", op);
		body.set("id", String(id));

		fetch(cfg.postUrl, {
			method: "POST",
			credentials: "same-origin",
			headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
			body: body.toString()
		}).then(function (r) { return r.json(); }).then(function (j) {
			if (!j || !j.ok) { throw new Error(j && j.error ? j.error : "fail"); }
			if (op === "delete") { delete items[id]; } else if (items[id]) { items[id].status = op === "done" ? 1 : 0; }
			$("odq-alerts").innerHTML = "";
			render();
		}).catch(function (err) {
			b.disabled = false;
			$("odq-alerts").innerHTML = '<div class="alert alert-danger">' + esc(err && err.message !== "fail" ? err.message : "Хадгалж чадсангүй. Нэвтрэлт дууссан байж магадгүй — хуудсаа дахин ачаална уу.") + "</div>";
		});
	});

	/* ---------------- шууд шинэчлэлт ---------------- */

	function poll() {
		fetch(cfg.feedUrl + "&after=" + lastId + "&_=" + Date.now(), { credentials: "same-origin", cache: "no-store" })
			.then(function (r) {
				if (!r.ok) { throw new Error("HTTP " + r.status); }
				return r.json();
			})
			.then(function (j) {
				if (!j || !j.ok) { throw new Error("bad"); }
				failCount = 0;

				var fresh = [];
				(j.items || []).forEach(function (q) {
					if (!items[q.id]) { fresh.push(q.id); }
					items[q.id] = q;
					if (q.id > lastId) { lastId = q.id; }
				});

				/* өөр админ тэмдэглэсэн / устгасан */
				var alive = {};
				(j.state || []).forEach(function (p) {
					alive[p[0]] = true;
					if (items[p[0]]) { items[p[0]].status = p[1]; }
				});
				Object.keys(items).forEach(function (k) { if (!alive[k]) { delete items[k]; } });

				if (!firstLoad && fresh.length) { beep(); }
				render(firstLoad ? [] : fresh);
				firstLoad = false;
				setLive(true, "Шууд — " + (j.now || "").slice(11, 16) + "-д шинэчлэгдсэн");
			})
			.catch(function () {
				failCount++;
				setLive(false, failCount > 2 ? "Холболт тасарсан — дахин оролдож байна…" : "Холбогдож байна…");
			})
			.then(function () {
				/* нуугдсан tab-д ч ажиллана (хөтөч удаашруулж болно) */
				window.setTimeout(poll, failCount > 2 ? POLL_MS * 3 : POLL_MS);
			});
	}

	poll();
})();
