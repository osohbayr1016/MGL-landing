/* ------------------------------------------------------------------
   Бүртгэлийн хуудасны гүйлт.

   - Дэвсгэр зураг/видео нь CSS-ээр дэлгэцэнд наалдсан (position:fixed)
     тул энд юу ч хийхгүй — гүйлгэхэд өөрөө байрандаа үлдэнэ.
   - Хэсэг бүр дэлгэц дүүрэн snap хийнэ. Дэлгэцээс ӨНДӨР хэсэг (форм,
     урт хөтөлбөр) snap-аас чөлөөлөгдөнө — эс бөгөөс уншиж дуусгах
     боломжгүй болно.
   - Текст доороосоо зөөлөн гарч ирээд БАЙРАНДАА үлдэнэ.
   - Баруун талд хэсэг сонгох цэгүүд.

   Гуравдагч сан хэрэглээгүй.
   ------------------------------------------------------------------ */

(function () {
	"use strict";

	if (document.body.classList.contains("reg-editing")) {
		return;
	}

	var page = document.querySelector(".reg-page");
	var sections = Array.prototype.slice.call(document.querySelectorAll(".reg-scroll-section"));

	if (!page || !sections.length) {
		return;
	}

	var root   = document.documentElement;
	var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

	function contentOf(sec) {
		return sec.querySelector(".reg-scroll-content");
	}

	/* ------------------------------------------------------------------
	   1. Гарч ирэх (reveal) анимаци — нэг л удаа
	   ------------------------------------------------------------------ */

	document.body.classList.add("reg-scroll-ready");

	sections.forEach(function (sec) {
		var content = contentOf(sec);
		if (!content) {
			return;
		}

		var host = content.querySelector(".reg-wrap") || content;
		var kids = Array.prototype.slice.call(host.children);

		if (!kids.length) {
			kids = [host];
		}

		kids.forEach(function (el, i) {
			el.classList.add("reg-rv");
			el.style.setProperty("--reg-i", i);
		});
	});

	function reveal(content) {
		if (content) {
			content.classList.add("reg-in");
		}
	}

	if (window.IntersectionObserver) {
		var seen = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					reveal(entry.target);
					seen.unobserve(entry.target);
				}
			});
		}, { threshold: 0, rootMargin: "0px 0px -12% 0px" });

		sections.forEach(function (sec) {
			var content = contentOf(sec);
			if (content) {
				seen.observe(content);
			}
		});
	} else {
		sections.forEach(function (sec) {
			reveal(contentOf(sec));
		});
	}

	/* ------------------------------------------------------------------
	   2. Дэлгэц дүүрэн snap
	   ------------------------------------------------------------------ */

	var snapOn = page.getAttribute("data-reg-snap") === "1" && !reduce;

	function measure() {
		var vh = window.innerHeight;

		sections.forEach(function (sec) {
			var tall = sec.getBoundingClientRect().height > vh * 1.02;

			if (tall) {
				sec.classList.add("reg-snap-free");
			} else {
				sec.classList.remove("reg-snap-free");
			}
		});
	}

	if (snapOn) {
		root.classList.add("reg-snap");
		measure();
		window.addEventListener("load", measure);
	}

	/* ------------------------------------------------------------------
	   3. Баруун талын цэгэн навигац
	   ------------------------------------------------------------------ */

	var dots = [];

	if (page.getAttribute("data-reg-dots") === "1" && sections.length > 1) {
		var nav = document.createElement("nav");
		nav.className = "reg-dots";
		nav.setAttribute("aria-label", "Хуудасны хэсгүүд");

		sections.forEach(function (sec, i) {
			var head  = sec.querySelector("h1, h2, h3");
			var label = head ? head.textContent.replace(/\s+/g, " ").trim().slice(0, 40) : "";

			var b = document.createElement("button");
			b.type = "button";
			b.className = "reg-dot";
			b.title = label || ("Хэсэг " + (i + 1));
			b.setAttribute("aria-label", b.title);

			b.addEventListener("click", function () {
				sec.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
			});

			nav.appendChild(b);
			dots.push(b);
		});

		document.body.appendChild(nav);
	}

	/* ------------------------------------------------------------------
	   4. Гүйлтийн төлөв
	   ------------------------------------------------------------------ */

	var ticking = false;

	function update() {
		ticking = false;

		var y  = window.pageYOffset || document.documentElement.scrollTop;
		var vh = window.innerHeight;

		if (y > 40) {
			document.body.classList.add("reg-scrolled");
		} else {
			document.body.classList.remove("reg-scrolled");
		}

		if (!dots.length) {
			return;
		}

		var best = 0;
		var bestGap = Number.MAX_VALUE;

		sections.forEach(function (sec, i) {
			var r = sec.getBoundingClientRect();

			/* Дэлгэцээс өндөр хэсгийг дээд ирмэгээр нь, бусдыг голоор нь */
			var gap = r.height > vh
				? Math.abs(r.top)
				: Math.abs((r.top + r.height / 2) - vh / 2);

			if (gap < bestGap) {
				bestGap = gap;
				best = i;
			}
		});

		dots.forEach(function (b, i) {
			if (i === best) {
				b.classList.add("is-on");
			} else {
				b.classList.remove("is-on");
			}
		});
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		measure();
		onScroll();
	}, { passive: true });

	update();
}());
