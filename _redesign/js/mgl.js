/* ============================================================================
   MGL E&C — front-end behaviour
   ----------------------------------------------------------------------------
   Replaces: jQuery 3.4.1, MagicGrid, ScrollMagic, TweenMax/GSAP, AOS, Scrollify,
   Owl Carousel and progressbar.js  (~9 CDN requests, ~400KB).
   Everything below is vanilla and roughly 5KB. Layout is pure CSS Grid, so no
   JS is involved in sizing anything — that is what makes the grid reflow
   correctly at every width instead of needing MagicGrid to recompute.
   ========================================================================== */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------- mobile navigation */
  function initNav() {
    var burger = document.querySelector('.navbar__burger');
    var drawer = document.querySelector('.mobile-nav');
    if (!burger || !drawer) return;

    function setOpen(open) {
      burger.setAttribute('aria-expanded', String(open));
      drawer.classList.toggle('is-open', open);
      document.body.classList.toggle('nav-open', open);
      drawer.setAttribute('aria-hidden', String(!open));
    }

    burger.addEventListener('click', function () {
      setOpen(burger.getAttribute('aria-expanded') !== 'true');
    });

    // Close on link tap, on Escape, and whenever we grow past the drawer's
    // breakpoint (otherwise a rotated phone can strand `overflow:hidden`).
    drawer.addEventListener('click', function (e) {
      if (e.target.closest('a')) setOpen(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) {
      if (e.matches) setOpen(false);
    });

    setOpen(false);
  }

  /* -------------------------------------------------- header scroll state */
  function initHeader() {
    var header = document.querySelector('.site-header');
    if (!header) return;

    var ticking = false;
    function update() {
      header.classList.toggle('is-scrolled', window.scrollY > 12);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();
  }

  /* ------------------------------------------------------ reveal on enter */
  function initReveal() {
    var items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    if (reduced || !('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in');
        io.unobserve(entry.target);       // one-shot: never re-animates
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });

    items.forEach(function (el) { io.observe(el); });
  }

  /* ------------------------------------------------------- project filter */
  function initFilter() {
    var bar = document.querySelector('.filterbar');
    if (!bar) return;

    var pills = bar.querySelectorAll('.fpill');
    var cards = document.querySelectorAll('[data-sector]');
    var count = document.querySelector('.filterbar__count');

    function apply(sector) {
      var shown = 0;
      cards.forEach(function (card) {
        var match = sector === 'all' || card.dataset.sector === sector;
        card.hidden = !match;
        if (match) shown++;
      });
      if (count) count.textContent = shown + ' project' + (shown === 1 ? '' : 's');
    }

    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        pills.forEach(function (p) { p.setAttribute('aria-pressed', 'false'); });
        pill.setAttribute('aria-pressed', 'true');
        apply(pill.dataset.filter);
        // Keep the active pill in view on a narrow, scrolling filter bar.
        pill.scrollIntoView({ inline: 'center', block: 'nearest', behavior: reduced ? 'auto' : 'smooth' });
      });
    });

    apply('all');
  }

  /* ------------------------------------------------------------ hero slider */
  function initHero() {
    var hero = document.querySelector('[data-hero]');
    if (!hero) return;

    var slides = hero.querySelectorAll('.hero__slide');
    var dots   = hero.querySelectorAll('.hero__dot');
    if (slides.length < 2) return;

    var i = 0;
    var timer = null;
    var DELAY = 7000;

    function show(n) {
      i = n % slides.length;
      slides.forEach(function (s, idx) {
        s.style.opacity = idx === i ? '1' : '0';
        s.setAttribute('aria-hidden', String(idx !== i));
      });
      dots.forEach(function (d, idx) { d.setAttribute('aria-current', String(idx === i)); });
    }

    function start() { stop(); if (!reduced) timer = setInterval(function () { show(i + 1); }, DELAY); }
    function stop()  { if (timer) { clearInterval(timer); timer = null; } }

    dots.forEach(function (dot, idx) {
      dot.addEventListener('click', function () { show(idx); start(); });
    });

    // Don't burn battery animating a hero nobody is looking at.
    document.addEventListener('visibilitychange', function () {
      document.hidden ? stop() : start();
    });

    show(0);
    start();
  }

  /* ------------------------------------------------------------------ boot */
  function boot() {
    initNav();
    initHeader();
    initReveal();
    initFilter();
    initHero();
  }

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', boot)
    : boot();
})();
