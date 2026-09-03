(function () {
	"use strict";

	if (document.body.classList.contains("reg-editing")) {
		return;
	}

	var sections = Array.prototype.slice.call(document.querySelectorAll(".reg-scroll-section"));
	if (!sections.length) {
		return;
	}

	var ticking = false;

	function contentOf(sec) {
		return sec.querySelector(".reg-scroll-content");
	}

	function setState(el, state) {
		if (!el) {
			return;
		}
		el.classList.remove("reg-scroll-in", "reg-scroll-out-up", "reg-scroll-out-down");
		if (state) {
			el.classList.add(state);
		}
	}

	function visibleRatio(rect, vh) {
		var visible = Math.min(rect.bottom, vh) - Math.max(rect.top, 0);
		if (visible <= 0) {
			return 0;
		}
		return visible / Math.min(rect.height, vh);
	}

	function update() {
		ticking = false;
		var vh = window.innerHeight;

		sections.forEach(function (sec) {
			var el = contentOf(sec);
			if (!el) {
				return;
			}

			var rect = sec.getBoundingClientRect();
			var ratio = visibleRatio(rect, vh);

			if (sec.classList.contains("reg-scroll-section--fluid")) {
				setState(el, ratio > 0.08 ? "reg-scroll-in" : "reg-scroll-out-down");
				return;
			}

			if (ratio >= 0.28) {
				setState(el, "reg-scroll-in");
			} else if (rect.bottom <= vh * 0.15) {
				setState(el, "reg-scroll-out-up");
			} else if (rect.top >= vh * 0.92) {
				setState(el, "reg-scroll-out-down");
			} else if (rect.top < vh * 0.5) {
				setState(el, "reg-scroll-out-up");
			} else {
				setState(el, "reg-scroll-out-down");
			}
		});
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			requestAnimationFrame(update);
		}
	}

	document.body.classList.add("reg-scroll-ready");
	update();

	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", onScroll, { passive: true });
}());
