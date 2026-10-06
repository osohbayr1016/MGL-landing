<script>
$(function () {
	var $track = $(".pdetail-hero__track");
	if (!$track.length || typeof $.fn.owlCarousel !== "function") return;

	var count = $track.children(".pdetail-hero__slide").length;

	$track.owlCarousel({
		items: 1,
		loop: count > 1,
		autoplay: count > 1,
		autoplayTimeout: 6000,
		autoplaySpeed: 900,
		autoplayHoverPause: true,
		smartSpeed: 700,
		mouseDrag: true,
		touchDrag: true,
		pullDrag: true,
		autoHeight: true,
		nav: false,
		dots: count > 1
	});

	// Зураг ачаалагдахад autoHeight-ийг дахин тооцоолно (эхний slide-аас бусад нь
	// init хийх үед хараахан ачаалагдаагүй байж болно).
	$track.find("img").one("load", function () {
		$track.trigger("refresh.owl.carousel");
	});
});
</script>
