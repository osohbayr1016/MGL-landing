var hero_slider = {
    init: function() {
        var s = $(".hero-slider-wrap");
        s.owlCarousel({
            items: 1,
            dots: !0,
            loop: !0,
            autoplay: !0,
            autoplayTimeout: 8e3,
            animateIn: "fadeIn",
            animateOut: "fadeOut",
            onInitialized: startProgressBar,
            onTranslate: resetProgressBar,
            onTranslated: startProgressBar,
            responsiveClass: !0,
            responsive: {
                0: {},
                769: {
                    dotsContainer: ".hero-slider-trigger-wrap"
                }
            }
        }), s.on("changed.owl.carousel", function(e) {
            var i = e.item.index,
                t = $(e.target).find(".owl-item").eq(i).find("video"),
                n = t.find("source");
           // t[0].play();
            var r;
            s.find("video");
            t.length && (r = n.attr("data-src"), n.attr("src", r), $(".trigger-link").on("click", function() {
                s.trigger("stop.owl.autoplay"), s.trigger("play.owl.autoplay"), resetProgressBar(), setTimeout(function() {
                    startProgressBar()
                }, 10), t[0].currentTime = 0
            }))
        })
    }
};
var koda_slider = {
    init: function() {
        var s = $(".koda-slider-wrap");
        s.owlCarousel({
            items: 1,
            dots: !0,
            loop: !0,
            autoplay: !0,
            autoplayTimeout: 8e3,
            animateIn: "fadeIn",
            animateOut: "fadeOut",
            onInitialized: startProgressBar,
            onTranslate: resetProgressBar,
            onTranslated: startProgressBar,
            responsiveClass: !0,
            responsive: {
                0: {},
                769: {
                    dotsContainer: ".koda-slider-trigger-wrap"
                }
            }
        }), s.on("changed.owl.carousel", function(e) {
            var i = e.item.index,
                t = $(e.target).find(".owl-item").eq(i).find("video"),
                n = t.find("source");
           // t[0].play();
            var r;
            s.find("video");
            t.length && (r = n.attr("data-src"), n.attr("src", r), $(".trigger-link").on("click", function() {
                s.trigger("stop.owl.autoplay"), s.trigger("play.owl.autoplay"), resetProgressBar(), setTimeout(function() {
                    startProgressBar()
                }, 10), t[0].currentTime = 0
            }))
        })
    }
};

function startProgressBar() {
    $(".slide-progress").css({
        width: "100%",
        transition: "width 8000ms"
    })
}

function resetProgressBar() {
    $(".slide-progress").css({
        width: 0,
        transition: "width 0s"
    })
}
var process_slider = {
    elements: {},
    init: function() {
        this.getElements(), this.processSliderFiltering(), this.processSliderInit()
    },
    getElements: function() {
        this.elements.processCarousel = $(".process-slider-wrap"), this.elements.carouselWithId = []
    },
    processSliderFiltering: function() {
        var e = this;
        if (e.elements.processCarousel.length) return e.elements.processCarousel.each(function() {
            $(this).attr("data-id") && e.elements.carouselWithId.push(this)
        }), e.elements.carouselWithId
    },
    processSliderInit: function() {
        var e, i = this;
        i.elements.processCarousel.each(function() {
            var r = $(this);
            e = i.elements.carouselWithId.includes(this) ? ".slider-trigger-" + r.attr("data-id") : ".slider-trigger";
            var s = r.siblings(".process-slider-trigger-wrap"),
                a = r.siblings(".progress-circle-wrap");
            $(this).owlCarousel({
                items: 1,
                loop: 1,
                animateIn: "fadeIn",
                animateOut: "fadeOut",
                responsiveClass: !0,
                responsive: {
                    0: {},
                    769: {
                        dotsContainer: e
                    }
                }
            }), $(window).scroll(function() {
                var e = s.offset().top,
                    i = s.offset().top + s.outerHeight(),
                    t = $(window).scrollTop() + $(window).innerHeight(),
                    n = $(window).scrollTop();
                e < t && n < i && (r.trigger("play.owl.autoplay", 6e3), a.addClass("animate"))
            }), r.on("changed.owl.carousel", function(e) {
                r.trigger("stop.owl.autoplay"), r.trigger("play.owl.autoplay", 6e3), a.removeClass("animate"), setTimeout(function() {
                    a.addClass("animate")
                }, 10)
            })
        })
    }
};

function startProgressCircle() {
    $(".progress-circle").empty(), new ProgressBar.Circle(".progress-circle", {
        strokeWidth: 6,
        easing: "easeInOut",
        duration: 6e3,
        color: "#000",
        trailColor: "#ccc",
        trailWidth: 1,
        svgStyle: null
    }).animate(1)
}

function resetProgressCircle() {
    $(".progress-circle").empty(), new ProgressBar.Circle(".progress-circle", {
        strokeWidth: 6,
        easing: "easeInOut",
        duration: 6e3,
        color: "#000",
        trailColor: "#ccc",
        trailWidth: 1,
        svgStyle: null
    }).animate(1)
}
var image_teaser_slider = {
        init: function() {
            $(".image-teaser-carousel").owlCarousel({
                margin: 40,
                nav: !0,
                loop: !0,
                stagePadding: 0,
                responsiveClass: !0,
                responsive: {
                    0: {
                        items: 1
                    },
                    769: {
                        items: 2
                    }
                }
            }), $(".image-teaser-carousel").on("resized.owl.carousel", function(e) {
                image_teaser_bottom_position.init()
            })
        }
    },
    accordion = {
        init: function() {
            $(".accordion-item-title").click(function(e) {
                $(this).closest(".accordion-item").toggleClass("active"), e.preventDefault()
            })
        }
    },
    opening_times = {
        init: function() {
            setTimeout(function() {
                $(".time-item").each(function() {
                    var e = $(this),
                        i = e.find(".timezone").text(),
                        n = e.find(".opening-time").text(),
                        r = e.find(".closing-time").text();
                    currentTimeFormatted = moment().utcOffset(i).format("HH:mm"), openTimeFormatted = moment(n, "HH:mm").format("HH:mm"), closeTimeFormatted = moment(r, "HH:mm").format("HH:mm"), currentTimeFormatted > openTimeFormatted && currentTimeFormatted < closeTimeFormatted ? e.addClass("open") : e.addClass("closed"), setInterval(function() {
                        t = (new Date).getTime(), e.find(".office-time .hours").html(moment(t).utcOffset(i).format("HH")), e.find(".office-time .minutes").html(moment(t).utcOffset(i).format("mm"))
                    }, 500)
                })
            }, 800)
        }
    },
    sliding_carousel = {
        init: function() {
            $(".sliding-carousel-no-copy-wrap").owlCarousel({
                items: 1,
                autoWidth: !0,
                margin: 40,
                loop: !0,
                stagePadding: 40,
                responsiveClass: !0
            })
        }
    },
    news_carousel = {
        getVisibleItems: function() {
            var w = window.innerWidth;
            return 1200 <= w ? 4 : 769 <= w ? 2 : 1
        },
        canAutoplay: function(e, i) {
            return !window.matchMedia("(prefers-reduced-motion: reduce)").matches && e > i
        },
        startAutoplay: function(e) {
            e.trigger("stop.owl.autoplay"), e.trigger("play.owl.autoplay", [6500, 900])
        },
        init: function() {
            var e = $(".featured-news-wrap");
            if (!e.length || e.hasClass("owl-loaded")) return;
            var i = e.children(".featured-news-item").length;
            if (!i) return;
            var t = news_carousel.getVisibleItems(),
                n = i >= 2 * t;
            e.owlCarousel({
                margin: 20,
                nav: !1,
                dots: !0,
                loop: n,
                rewind: !n,
                autoplay: news_carousel.canAutoplay(i, t),
                autoplayTimeout: 6500,
                autoplayHoverPause: !0,
                autoplaySpeed: 900,
                smartSpeed: 900,
                slideBy: 1,
                rtl: !1,
                stagePadding: 0,
                responsiveClass: !0,
                responsive: {
                    0: {
                        items: 1
                    },
                    769: {
                        items: 2
                    },
                    1200: {
                        items: 4
                    }
                }
            }), news_carousel.canAutoplay(i, t) && news_carousel.startAutoplay(e), $(window).on("load.newsCarousel", function() {
                e.trigger("refresh.owl.carousel"), news_carousel.canAutoplay(i, news_carousel.getVisibleItems()) && news_carousel.startAutoplay(e)
            })
        }
    },
    filterCollapse = {
        init: function() {
            var e = this;
            $(".filter-container .filter-collapse-expand").click(function() {
                e.toggleFilter(this), $(this).toggleClass("filter-open")
            })
        },
        toggleFilter: function(e) {
            $(e).parent().parent().parent().find(".filter").toggle()
        }
    },
    news_grid = {
        init: function() {
            $(".news-feed-wrap").length && new MagicGrid({
                container: ".news-feed-wrap",
                animate: !0,
                maxColumns: 2,
                gutter: 40,
                static: !0
            }).listen()
        }
    },
    search_bar = {
        init: function() {
            $(".search-icon").on("click", function() {
                return $("form#header-search-form").show(), $("#header-search-form input").focus(), $("div#menu-wrap-inner-wrap").hide(), $(".search-overlay").show(), $("body").removeClass("menu-open"), $(".menu-icon").removeClass("open"), !1
            }), $(".search-close, .search-overlay").on("click", function() {
                return $("form#header-search-form").hide(), $(".search-overlay").hide(), !1
            })
        }
    },
    figuresAnimation = {
        init: function() {
            var e;
            $(".figures-block-wrap").length && (e = new ScrollMagic.Controller, new ScrollMagic.Scene({
                triggerElement: ".trigger-figures-animation"
            }).addTo(e).on("enter", function(e) {
                $(".figure-content").each(function() {
                    var e = $(this);
                    if (isNaN(e.text())) return !0;
                    jQuery({
                        Counter: 0
                    }).animate({
                        Counter: e.text()
                    }, {
                        duration: 1500,
                        easing: "swing",
                        step: function() {
                            e.text(Math.ceil(this.Counter))
                        }
                    })
                })
            }))
        }
    },
    embed_trigger = {
        init: function() {
            var e;
            $("#trigger-embed-animation").length && (e = new ScrollMagic.Controller, new ScrollMagic.Scene({
                triggerElement: "#trigger-embed-animation"
            }).setClassToggle(".animated-embed", "animate").addTo(e))
        }
    },
    scrolljack_trigger = {
        init: function() {
            $.scrollify({
                section: ".scrolljack-item",
                updateHash: !1,
				scrollbars:false,
                scrollSpeed: 800,
				before:function(i,panels) {

				  var ref = panels[i].attr("data-section-name");

				  $(".aboutsubmenu .active").removeClass("active");

				  $(".aboutsubmenu").find("a[href=\"#" + ref + "\"]").addClass("active");
				},
				afterRender:function() {
				  var pagination = "<div class=\" headersubmenu\"><ul class=\"aboutsubmenu\">";
				  var activeClass = "";
				  $(".scrolljack-item").each(function(i) {
					activeClass = "";
					if(i===$.scrollify.currentIndex()) {
					  activeClass = "active";
					}
					pagination += "<li><a class=\"" + activeClass + "\" href=\"#" + $(this).attr("data-section-name") + "\"><span class=\"hover-text\">" + $(this).attr("data-section-title") + "</span></a></li>";
				  });

				  pagination += "</ul></div>";

				  $(".mainNavHeader").append(pagination);
				  /*

				  Tip: The two click events below are the same:

				  $(".pagination a").on("click",function() {
					$.scrollify.move($(this).attr("href"));
				  });

				  */
				  $(".aboutsubmenu a").on("click",$.scrollify.move);
				}
            })
        }
    },
    menu_trigger = {
        init: function() {
            $(".menu-icon").on("click", function() {
                $(this).toggleClass("open"), $("div#menu-wrap-inner-wrap").toggle(), $("body").toggleClass("menu-open")
            })
        }
    },
    heatmap_trigger = {
        init: function() {
            var e;
            $("#trigger-heatmap").length && (e = new ScrollMagic.Controller, new ScrollMagic.Scene({
                triggerElement: "#trigger-heatmap"
            }).setClassToggle(".heatmap-inner", "animate").addTo(e))
        }
    },
    embed_trigger = {
        init: function() {
            var e;
            $("#trigger-embed-animation").length && (e = new ScrollMagic.Controller, new ScrollMagic.Scene({
                triggerElement: "#trigger-embed-animation"
            }).setClassToggle(".animated-embed", "animate").addTo(e))
        }
    },
    factsheet_trigger = {
        init: function() {
            var e;
            $("#trigger-factsheet-animation").length && (e = new ScrollMagic.Controller, new ScrollMagic.Scene({
                triggerElement: "#trigger-factsheet-animation"
            }).setClassToggle(".stats-details-wrap", "animate").addTo(e))
        }
    },
    load_more = {
        init: function() {
            $(".load-more-wrap").each(function() {
                $(this).find($(".load-more-item")).slice(0, 3).show(), 0 == $(this).find($(".load-more-item:hidden")).length && $(this).parent().find($(".loadMore")).hide(), $(".loadMore").on("click", function(e) {
                    e.preventDefault(), $(this).parent().find($(".load-more-item:hidden")).slice(0, 3).slideDown(), 0 == $(this).parent().find($(".load-more-item:hidden")).length && $(this).delay(500).addClass("hide-btn")
                })
            })
        }
    },
    scrollLanguageMenu = {
        init: function() {
            var e = 0;
            $(".chevron-up").css("display", "none"), $("#header-languages-menu").on("scroll", function() {
                return $("#header-languages-menu").scrollTop() <= 0 ? $(".chevron-up").css("display", "none") : $(".chevron-up").css("display", "block"), e - 70 + $("#header-languages-menu").height() >= $("#header-languages-menu > ul").height() + 35 ? $(".chevron-down").css("display", "none") : $(".chevron-down").css("display", "block"), e = $("#header-languages-menu").scrollTop()
            }), $(".chevron-up").on("click", function() {
                return e = e <= 0 ? 0 : (e -= 70, $("#header-languages-menu").animate({
                    scrollTop: e
                }), $("#header-languages-menu").scrollTop())
            }), $(".chevron-down").on("click", function() {
                return e += 70, $("#header-languages-menu").animate({
                    scrollTop: e
                }), e = $("#header-languages-menu").scrollTop()
            })
        }
    },
    videoOverlay = {
        init: function() {
            $(".hero-banner-image, .hero-slider-video").each(function() {
                $(this).hasClass("video-available") ? $(".hero-banner-image video").on("loadeddata", function() {
                    $(this).siblings(".img-overlay").removeClass("hidden")
                }) : $(".hero-banner-image .img-overlay").removeClass("hidden")
            })
        }
    },
    image_teaser_bottom_position = {
        elements: {},
        init: function() {
            this.getElements(), this.getElementDimensions()
        },
        getElements: function() {
            this.elements.imageTeasers = $(".image-teaser-copy"), this.elements.imageTeasers.length && (this.elements.imageTeasers.margin = parseFloat(this.elements.imageTeasers.children("h4").css("margin-bottom")), this.elements.imageTeasers.padding = parseFloat(this.elements.imageTeasers.css("padding")), this.elements.imageTeasers.spacing = this.elements.imageTeasers.margin + this.elements.imageTeasers.padding)
        },
        getElementDimensions: function() {
            var e = this;
            e.elements.imageTeasers.length && e.elements.imageTeasers.each(function() {
                $(this).css("bottom", $(this).children("h4").height() + e.elements.imageTeasers.spacing)
            })
        }
    };
$(document).ready(function() {
	

    hero_slider.init(), koda_slider.init(), process_slider.init(), image_teaser_slider.init(), accordion.init(), opening_times.init(), news_grid.init(), search_bar.init(), menu_trigger.init(), heatmap_trigger.init(), embed_trigger.init(), factsheet_trigger.init(), load_more.init(), news_carousel.init(), scrollLanguageMenu.init(), videoOverlay.init(), figuresAnimation.init(), image_teaser_bottom_position.init(), 768 < $(window).width() && scrolljack_trigger.init(), filterCollapse.init(), $(".popup-trigger").featherlight({
        targetAttr: "href",
        beforeOpen: function(e) {
            $("html").addClass("people-popup")
        },
        afterOpen: function(e) {
            $(".featherlight").css("right", "0")
        },
        afterClose: function(e) {
            $("html").removeClass("people-popup")
        }
    }), setTimeout(function() {
        sliding_carousel.init()
    }, 1500)
});