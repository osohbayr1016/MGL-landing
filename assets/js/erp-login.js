/* ------------------------------------------------------------------
   ERP нэвтрэлтийн цонх.

   Товч дарахад цонх нээгдэж, форм илгээхэд /erp.login.php руу fetch хийнэ.
   Нууц үгийг сервер өөрөө шалгахгүй — ERP-ийн Worker руу дамжуулж
   шалгуулаад, зөв бол буцаасан хаяг руу шилжүүлнэ.
   ------------------------------------------------------------------ */

(function () {
	"use strict";

	var modal = document.getElementById("erpLogin");
	var form  = document.getElementById("erpLoginForm");

	if (!modal || !form) return;

	var errorBox   = document.getElementById("erpLoginError");
	var submitBtn  = form.querySelector(".erp-login__submit");
	var loginInput = form.querySelector('input[name="login"]');
	var passInput  = form.querySelector('input[name="password"]');
	var lastFocus  = null;

	/* --- нээх / хаах ------------------------------------------------ */

	function open(trigger) {
		lastFocus = trigger || document.activeElement;

		modal.hidden = false;
		document.documentElement.style.overflow = "hidden";

		/* hidden-ийг салгасны дараа нэг frame өгч байж шилжилт ажиллана */
		requestAnimationFrame(function () {
			modal.classList.add("is-open");
			if (loginInput) loginInput.focus();
		});
	}

	function close() {
		modal.classList.remove("is-open");
		document.documentElement.style.overflow = "";

		window.setTimeout(function () {
			modal.hidden = true;
			hideError();
			resetPeek();
			if (passInput) passInput.value = "";
			if (lastFocus && lastFocus.focus) lastFocus.focus();
		}, 280);
	}

	function isOpen() {
		return !modal.hidden;
	}

	/* Нууц үгийг дахин далдалж, нүдний дүрсийг анхны байдалд нь буцаана */
	function resetPeek() {
		var peek = form.querySelector("[data-erp-peek]");

		if (passInput) passInput.type = "password";
		if (!peek) return;

		peek.classList.remove("is-visible");
		peek.setAttribute("aria-pressed", "false");
		peek.setAttribute("aria-label", "Нууц үг харуулах");
	}

	/* --- алдааны мэдэгдэл ------------------------------------------- */

	function showError(msg) {
		if (!errorBox) return;
		errorBox.textContent = msg;
		errorBox.hidden = false;
	}

	function hideError() {
		if (!errorBox) return;
		errorBox.textContent = "";
		errorBox.hidden = true;
	}

	function busy(on) {
		modal.classList.toggle("is-busy", on);
		if (submitBtn) submitBtn.disabled = on;
	}

	/* --- үйл явдлууд ------------------------------------------------ */

	document.addEventListener("click", function (e) {
		if (!e.target || !e.target.closest) return;

		var opener = e.target.closest("[data-erp-open]");
		if (opener) {
			e.preventDefault();
			open(opener);
			return;
		}

		if (isOpen() && e.target.closest("[data-erp-close]")) {
			e.preventDefault();
			close();
			return;
		}

		var peek = e.target.closest("[data-erp-peek]");
		if (peek && passInput) {
			e.preventDefault();

			var show = passInput.type === "password";

			passInput.type = show ? "text" : "password";
			peek.classList.toggle("is-visible", show);
			peek.setAttribute("aria-pressed", show ? "true" : "false");
			peek.setAttribute("aria-label", show ? "Нууц үг нуух" : "Нууц үг харуулах");
			passInput.focus();
		}
	});

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape" && isOpen()) close();
	});

	/* Цонх дотор Tab-аар л явахаар барих */
	modal.addEventListener("keydown", function (e) {
		if (e.key !== "Tab") return;

		var items = modal.querySelectorAll(
			'button:not([disabled]), input:not([disabled]), a[href]'
		);
		if (!items.length) return;

		var first = items[0];
		var last  = items[items.length - 1];

		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	});

	/* --- илгээх ------------------------------------------------------ */

	form.addEventListener("submit", function (e) {
		e.preventDefault();
		hideError();

		var login = (loginInput && loginInput.value || "").trim();
		var pass  = passInput && passInput.value || "";

		if (!login || !pass) {
			showError("Нэвтрэх нэр болон нууц үгээ оруулна уу.");
			return;
		}

		busy(true);

		var body = new FormData(form);
		body.set("login", login);

		fetch(form.action, {
			method: "POST",
			body: body,
			credentials: "same-origin",
			headers: { "Accept": "application/json" }
		})
			.then(function (res) {
				return res.json().catch(function () {
					throw new Error("bad-json");
				});
			})
			.then(function (data) {
				if (data && data.ok && data.redirect) {
					/* Амжилттай — товч "Шилжиж байна" болоод ERP рүү орно */
					var label = form.querySelector(".erp-login__submit-text");
					if (label) label.textContent = "Шилжиж байна…";
					window.location.href = data.redirect;
					return;
				}

				busy(false);
				showError((data && data.error) || "Нэвтэрч чадсангүй. Дахин оролдоно уу.");
				if (passInput) {
					passInput.value = "";
					passInput.focus();
				}
			})
			.catch(function () {
				busy(false);
				showError("Сүлжээний алдаа гарлаа. Дахин оролдоно уу.");
			});
	});
})();
