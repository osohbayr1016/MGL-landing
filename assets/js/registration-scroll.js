/* ------------------------------------------------------------------
   Бүртгэлийн хуудасны гүйлт.

   - Дэвсгэр зураг/видео нь CSS-ээр дэлгэцэнд наалдсан (position:fixed)
     тул энд юу ч хийхгүй — гүйлгэхэд өөрөө байрандаа үлдэнэ.

   - КОМПЬЮТЕР дээр: хулганы дугуй эргүүлэх бүрд дараагийн хэсэг рүү
     ЖИГД, зөөлөн (easing) гулсана. Өмнө нь CSS scroll-snap ашигладаг
     байсан бөгөөд дугуйны товшилт болгонд хуудас таталддаг тул
     цочромтгой мэдрэгддэг байв.

   - ГАР УТАС дээр: төрөлх (native) гүйлтийг хөндөхгүй — хуруугаараа
     гүйлгэх нь аль хэдийн зөөлөн. Зөвхөн CSS snap-аар хэсэг бүрт
     тогтооно.

   - Дэлгэцээс ӨНДӨР хэсэг (форм, урт хөтөлбөр) дотроо чөлөөтэй гүйнэ.
     Ирмэгт нь хүрсний дараа л дараагийн хэсэг рүү шилжинэ.

   - Текст доороосоо зөөлөн гарч ирээд БАЙРАНДАА үлдэнэ.

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
	var coarse = window.matchMedia && window.matchMedia("(pointer: coarse)").matches;

	var DURATION = 750;   /* нэг хэсгээс нөгөө рүү гулсах хугацаа (мс) */
	var COOLDOWN = 250;   /* нэг дохиог нэг л удаа тоолохын тулд хүлээх */

	function contentOf(sec) {
		return sec.querySelector(".reg-scroll-content");
	}

	function scrollY() {
		return window.pageYOffset || root.scrollTop || 0;
	}

	function maxY() {
		return Math.max(
			document.body.scrollHeight,
			root.scrollHeight
		) - window.innerHeight;
	}

	function topOf(sec) {
		return sec.getBoundingClientRect().top + scrollY();
	}

	function isTall(sec) {
		return sec.getBoundingClientRect().height > window.innerHeight * 1.02;
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
	   2. Зөөлөн гулсуулагч
	   ------------------------------------------------------------------ */

	var snapOn = page.getAttribute("data-reg-snap") === "1";
	var engine = snapOn && !reduce && !coarse;

	var animing = false;
	var animRaf = 0;
	var animAt  = 0;
	var animFrom = 0;
	var animTo   = 0;
	var restUntil = 0;

	function ease(t) {
		/* easeInOutCubic — эхэлж, дуусахдаа зөөлөн */
		return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
	}

	function stop() {
		animing = false;
		if (animRaf) {
			window.cancelAnimationFrame(animRaf);
			animRaf = 0;
		}
	}

	function step(now) {
		if (!animAt) {
			animAt = now;
		}

		var t = Math.min(1, (now - animAt) / DURATION);
		window.scrollTo(0, animFrom + (animTo - animFrom) * ease(t));

		if (t < 1) {
			animRaf = window.requestAnimationFrame(step);
			return;
		}

		animing = false;
		animRaf = 0;
		restUntil = Date.now() + COOLDOWN;
	}

	function glideTo(y) {
		y = Math.max(0, Math.min(y, maxY()));

		if (Math.abs(y - scrollY()) < 2) {
			return;
		}

		stop();

		animFrom = scrollY();
		animTo   = y;
		animAt   = 0;
		animing  = true;
		animRaf  = window.requestAnimationFrame(step);
	}

	/* Дэлгэцийн голд аль хэсэг байна вэ */
	function currentIndex() {
		var mid = window.innerHeight / 2;

		for (var i = 0; i < sections.length; i++) {
			var r = sections[i].getBoundingClientRect();
			if (r.top <= mid && r.bottom > mid) {
				return i;
			}
		}

		var best = 0;
		var bestGap = Number.MAX_VALUE;

		sections.forEach(function (sec, i) {
			var gap = Math.abs(sec.getBoundingClientRect().top);
			if (gap < bestGap) {
				bestGap = gap;
				best = i;
			}
		});

		return best;
	}

	function goTo(i) {
		if (i < 0 || i >= sections.length) {
			return;
		}

		glideTo(topOf(sections[i]));
	}

	/* Урт хэсэг дотор байгаа бөгөөд ирмэгт нь хүрээгүй бол
	   төрөлх гүйлтэд саад болохгүй */
	function freeInside(sec, dir) {
		if (!isTall(sec)) {
			return false;
		}

		var r = sec.getBoundingClientRect();

		if (dir > 0) {
			return r.bottom > window.innerHeight + 2;
		}

		return r.top < -2;
	}

	if (engine) {
		root.classList.add("reg-smooth");

		window.addEventListener("wheel", function (e) {
			if (e.ctrlKey || Math.abs(e.deltaY) < 4) {
				return;
			}

			var dir = e.deltaY > 0 ? 1 : -1;

			/* Хамгийн сүүлийн хэсгээс доош — footer руу төрөлхөөр гүйнэ */
			var lastR = sections[sections.length - 1].getBoundingClientRect();
			if (lastR.bottom <= window.innerHeight * 0.5) {
				stop();
				return;
			}

			var idx = currentIndex();

			/* Урт хэсэг дотор — төрөлх гүйлтэд саад болохгүй */
			if (freeInside(sections[idx], dir)) {
				stop();
				return;
			}

			var to = idx + dir;

			/* Эхэн/төгсгөлөөс цааш — хөтөч өөрөө шийднэ */
			if (to < 0 || to >= sections.length) {
				return;
			}

			e.preventDefault();

			var now = Date.now();

			/* Trackpad нэг шудрахад олон арван дохио илгээдэг. Дохио
			   үргэлжилсээр байвал хүлээх хугацааг сунгаж, нэг шудралтыг
			   НЭГ шилжилт болгож тоолно. */
			if (animing || now < restUntil) {
				restUntil = Math.max(restUntil, now + COOLDOWN);
				return;
			}

			goTo(to);
		}, { passive: false });

		document.addEventListener("keydown", function (e) {
			var el = e.target;

			if (el && (el.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName))) {
				return;
			}

			var idx = currentIndex();
			var to = -1;

			if (e.key === "PageDown") {
				to = idx + 1;
			} else if (e.key === "PageUp") {
				to = idx - 1;
			} else if (e.key === "Home") {
				to = 0;
			} else if (e.key === "End") {
				to = sections.length - 1;
			}

			if (to >= 0 && to < sections.length) {
				e.preventDefault();
				goTo(to);
			}
		});
	} else if (snapOn && !reduce) {
		/* Гар утас — төрөлх гүйлт + CSS snap */
		root.classList.add("reg-snap-touch");
	}

	function measure() {
		var vh = window.innerHeight;

		sections.forEach(function (sec) {
			if (sec.getBoundingClientRect().height > vh * 1.02) {
				sec.classList.add("reg-snap-free");
			} else {
				sec.classList.remove("reg-snap-free");
			}
		});
	}

	measure();
	window.addEventListener("load", measure);

	/* Хуудсан доторх холбоосууд ч зөөлөн явна */
	Array.prototype.forEach.call(document.querySelectorAll('a[href^="#"]'), function (a) {
		a.addEventListener("click", function (e) {
			var id = a.getAttribute("href").slice(1);
			if (!id) {
				return;
			}

			var target = document.getElementById(id);
			if (!target) {
				return;
			}

			e.preventDefault();

			if (engine) {
				glideTo(target.getBoundingClientRect().top + scrollY());
			} else {
				target.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
			}
		});
	});

	/* Хэрэглэгч өөрөө гүйлгэж эхэлбэл анимацийг тасална */
	window.addEventListener("touchstart", stop, { passive: true });
	window.addEventListener("mousedown", function (e) {
		if (!e.target.closest || !e.target.closest(".reg-dots")) {
			stop();
		}
	}, { passive: true });

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
				if (engine) {
					glideTo(topOf(sec));
				} else {
					sec.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
				}
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

		var vh = window.innerHeight;

		if (scrollY() > 40) {
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
