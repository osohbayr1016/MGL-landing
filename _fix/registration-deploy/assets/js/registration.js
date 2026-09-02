/* ------------------------------------------------------------------
   Бүртгэлийн хуудасны JS — гуравдагч сан шаардахгүй (vanilla).
   1) Countdown тоолуур
   2) Формыг давхар илгээхээс сэргийлэх
   3) Алдаатай эхний талбар руу гүйлгэх
   ------------------------------------------------------------------ */

(function () {
	"use strict";

	/* ---------- Countdown ---------- */

	function pad(n) {
		return n < 10 ? "0" + n : String(n);
	}

	function startCountdown(box) {
		var target = parseInt(box.getAttribute("data-reg-countdown"), 10);
		if (!target) {
			return;
		}

		var cells = {
			d: box.querySelector('[data-cd="d"]'),
			h: box.querySelector('[data-cd="h"]'),
			m: box.querySelector('[data-cd="m"]'),
			s: box.querySelector('[data-cd="s"]')
		};

		function tick() {
			var left = target - Math.floor(Date.now() / 1000);
			if (left < 0) {
				left = 0;
			}

			var d = Math.floor(left / 86400);
			var h = Math.floor((left % 86400) / 3600);
			var m = Math.floor((left % 3600) / 60);
			var s = left % 60;

			if (cells.d) { cells.d.textContent = String(d); }
			if (cells.h) { cells.h.textContent = pad(h); }
			if (cells.m) { cells.m.textContent = pad(m); }
			if (cells.s) { cells.s.textContent = pad(s); }
		}

		tick();
		setInterval(tick, 1000);
	}

	var boxes = document.querySelectorAll("[data-reg-countdown]");
	for (var i = 0; i < boxes.length; i++) {
		startCountdown(boxes[i]);
	}

	/* ---------- Форм ---------- */

	var form = document.querySelector(".reg-form");

	if (form) {
		form.addEventListener("submit", function () {
			var btn = form.querySelector(".reg-submit");
			if (!btn) {
				return;
			}

			/* Сүлжээ удаан үед хоёр удаа дарахаас сэргийлнэ */
			setTimeout(function () {
				btn.setAttribute("disabled", "disabled");
				btn.textContent = "Илгээж байна...";
			}, 0);
		});
	}

	/* ---------- Алдаа руу гүйлгэх ---------- */

	var firstError = document.querySelector(".reg-has-error, .reg-alert");
	if (firstError && window.location.search.indexOf("ok=1") === -1) {
		firstError.scrollIntoView({ behavior: "smooth", block: "center" });
	}
}());
