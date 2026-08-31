/* Phone helpers: keep carousels sized after the mobile CSS overlay. */
(function () {
  if (!window.matchMedia || !window.matchMedia("(max-width: 768px)").matches) return;
  if (typeof jQuery === "undefined") return;

  jQuery(function ($) {
    function refresh() {
      $(".hero-slider-wrap, .image-teaser-carousel, .featured-news-wrap")
        .trigger("refresh.owl.carousel");
    }
    refresh();
    setTimeout(refresh, 400);
    $(window).on("load", refresh);
  });
})();
