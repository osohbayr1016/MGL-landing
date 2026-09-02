/* Phone helpers: keep carousels sized after the mobile CSS overlay. */
(function () {
  if (!window.matchMedia || !window.matchMedia("(max-width: 768px)").matches) return;
  if (typeof jQuery === "undefined") return;

  jQuery(function ($) {
    function refresh() {
      var $carousels = $(".hero-slider-wrap, .image-teaser-carousel, .featured-news-wrap");
      $carousels.trigger("refresh.owl.carousel");
      if (typeof news_carousel !== "undefined") {
        var $news = $(".featured-news-wrap");
        if ($news.length && news_carousel.canAutoplay($news.children(".featured-news-item").length, news_carousel.getVisibleItems())) {
          news_carousel.startAutoplay($news);
        }
      }
    }
    refresh();
    setTimeout(refresh, 400);
    $(window).on("load", refresh);
  });
})();
