/* ==============
 ========= js documentation ==========================

 * template name: Mozzo
 * version: 1.0
 * date: 10.8.2025
 * description: Online Food Order HTML5 Template
 * author: 
 * author-url: https://themeforest.net/user/

    ==================================================

     01. Mobile Menu
     -------------------------------------------------
     02. sidebar toggle 
     -------------------------------------------------
     03. open offcanvas nav
     -------------------------------------------------
     04. sticky header
     -------------------------------------------------
     05. video popup
     -------------------------------------------------
     06. video iframe
     -------------------------------------------------
     07. counter up
     -------------------------------------------------
     08. wow animation
     -------------------------------------------------
     09. nice select
     -------------------------------------------------
     10. testimonial slider
     -------------------------------------------------
     11. banner slider
     -------------------------------------------------
     12. apartment slider
     -------------------------------------------------
     13. offering slider
     -------------------------------------------------
     14. city slider
     -------------------------------------------------
     15. city slider two
     -------------------------------------------------
     16. add active class to the current link
     -------------------------------------------------
     17. on window resize functions
     -------------------------------------------------
     18. project slider
     -------------------------------------------------
     19. project slider two
     -------------------------------------------------
     20. testimonial slider two
     -------------------------------------------------
     21. progress bar
     -------------------------------------------------
     22. back to top
     -------------------------------------------------
     23. price range 
     -------------------------------------------------
     24. increment and decrement
     -------------------------------------------------
     25. preloader
     -------------------------------------------------
     26. countdown
     -------------------------------------------------
     27. counter up
     -------------------------------------------------
     29. title animation
     -------------------------------------------------
     30. template options
     -------------------------------------------------
     
    ==================================================
============== */

(function ($) {
	("use strict");

	$(document).ready(function () {
		//<< Menu Offcanvas >>//
		$(".menu-style-offcanvas .submenu").hide();
		$(".menu-style-offcanvas").on("click", ".clickAble", function (e) {
			e.preventDefault();
			$(this).next(".submenu").slideToggle(200);
		});

		//<< 01 >> Mobile Menu Js Start <<//
		$("#mobile-menu").meanmenu({
			meanMenuContainer: ".mobile-menu",
			meanScreenWidth: "1199",
			meanExpand: ['<i class="far fa-plus"></i>'],
		});

		//<< 02 >> Sidebar Toggle Js Start <<//
		$(document).on(
			"click",
			".offcanvas__close, .offcanvas__overlay",
			function () {
				$(".offcanvas__info").removeClass("info-open");
				$(".offcanvas__overlay").removeClass("overlay-open");
			}
		);

		$(document).on("click", ".sidebar__toggle", function () {
			$(".offcanvas__info").addClass("info-open");
			$(".offcanvas__overlay").addClass("overlay-open");
		});

		//<< 03 >> Body Overlay Js Start <<//
		$(document).on("click", ".body-overlay", function () {
			$(".offcanvas__area, .df-search-area").removeClass(
				"offcanvas-opened opened"
			);
			$(".body-overlay").removeClass("opened");
		});

		//<< 04 >> Sticky Header Js Start <<//
		$(window).on("scroll", function () {
			if ($(this).scrollTop() > 450) {
				$("#header-sticky").addClass("sticky");
			} else {
				$("#header-sticky").removeClass("sticky");
			}
		});

		//<< 05 >> Video Popup Start <<//
		$(".img-popup").magnificPopup({
			type: "image",
			gallery: {
				enabled: true,
			},
		});

		//<< 06 >> Video iframe Start <<//
		$(".video-popup").magnificPopup({
			type: "iframe",
			callbacks: {},
		});

		//<< 07 >> Counter up Start <<//
		$(".count").counterUp({
			delay: 15,
			time: 4000,
		});

		//<< 08 >> Wow Animation Start <<//
		new WOW().init();

		//<< 09 >> Nice Select Start <<//
		$("select").niceSelect();

		//>> 10 Search Popup Start <<//
		const $searchWrap = $(".search-wrap");
		const $navSearch = $(".nav-search");
		const $searchClose = $("#search-close");

		$(".search-trigger").on("click", function (e) {
			e.preventDefault();
			$searchWrap.animate({ opacity: "toggle" }, 500);
			$navSearch.add($searchClose).addClass("open");
		});

		$(".search-close").on("click", function (e) {
			e.preventDefault();
			$searchWrap.animate({ opacity: "toggle" }, 500);
			$navSearch.add($searchClose).removeClass("open");
		});

		function closeSearch() {
			$searchWrap.fadeOut(200);
			$navSearch.add($searchClose).removeClass("open");
		}

		$(document.body).on("click", function (e) {
			closeSearch();
		});

		$(".search-trigger, .main-search-input").on("click", function (e) {
			e.stopPropagation();
		});

		//<< Mozzo >>//
		var swiper = new Swiper(".mySwiper", {
			loop: true,
			spaceBetween: 10,
			freeMode: true,
			slidesPerView: 2,
			watchSlidesProgress: true,
			breakpoints: {
				470: {
					slidesPerView: 4,
				},
			},
		});
		var swiper2 = new Swiper(".mySwiper2", {
			loop: true,
			spaceBetween: 10,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			thumbs: {
				swiper: swiper,
			},
		});

		//<< Super Deals Slide >>//
		const superDealsSlider = new Swiper(".superDeals-slider", {
			spaceBetween: 24,
			speed: 1000,
			loop: true,
			autoplay: {
				delay: 1000,
				// disableOnInteraction: false,
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 4,
				},
				991: {
					slidesPerView: 3,
				},
				767: {
					slidesPerView: 2,
				},
				575: {
					slidesPerView: 2,
				},
				400: {
					slidesPerView: 1,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});

		//<< Super Deals Slide >>//
		const aboutSaysSlider = new Swiper(".about-says-slider", {
			spaceBetween: 24,
			speed: 1000,
			loop: true,
			autoplay: {
				delay: 1000,
				// disableOnInteraction: false,
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 3,
				},
				991: {
					slidesPerView: 3,
				},
				767: {
					slidesPerView: 2,
				},
				575: {
					slidesPerView: 2,
				},
				400: {
					slidesPerView: 1,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});

		//<< Super Deals Slide >>//
		const menuSlidWrap = new Swiper(".menu-slid-wrap", {
			spaceBetween: 24,
			speed: 1000,
			loop: true,
			autoplay: {
				delay: 1000,
				// disableOnInteraction: false,
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 7,
				},
				991: {
					slidesPerView: 7,
				},
				767: {
					slidesPerView: 6,
				},
				575: {
					slidesPerView: 4,
				},
				400: {
					slidesPerView: 3,
				},
				0: {
					slidesPerView: 2,
				},
			},
		});

		//<< 10 >> Testimonial Slider Start <<//
		if ($(".testimonial-slider").length > 0) {
			const testimonialSlider = new Swiper(".testimonial-slider", {
				spaceBetween: 30,
				speed: 2000,
				loop: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".array-prev",
					prevEl: ".array-next",
				},
				pagination: {
					el: ".swiper-dot",
				},
				breakpoints: {
					991: {
						slidesPerView: 1,
					},
					767: {
						slidesPerView: 1,
					},
					575: {
						slidesPerView: 1,
					},
					400: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		//<< 16 >> Brand Slider Start <<//
		if ($(".brand-slider").length > 0) {
			const brandSlider = new Swiper(".brand-slider", {
				spaceBetween: 30,
				speed: 2000,
				loop: true,
				centeredSlides: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				breakpoints: {
					1199: {
						slidesPerView: 7,
					},
					991: {
						slidesPerView: 6,
					},
					767: {
						slidesPerView: 5,
					},
					575: {
						slidesPerView: 4,
					},
					400: {
						slidesPerView: 3,
					},
					0: {
						slidesPerView: 2,
					},
				},
			});
		}

		//<< 19 >> Testimonial Slider Start <<//
		if ($(".testimonial-wrap02").length > 0) {
			const testimonialWrap02 = new Swiper(".testimonial-wrap02", {
				spaceBetween: 24,
				speed: 1000,
				loop: true,
				autoplay: {
					delay: 1000,
					disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".array-prev",
					prevEl: ".array-next",
				},
				breakpoints: {
					991: {
						slidesPerView: 2,
					},
					767: {
						slidesPerView: 2,
					},
					575: {
						slidesPerView: 1,
					},
					400: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		//--Text Custom Slide
		const sponsor__text__slide = new Swiper(".sponsor-text-slide", {
			speed: 6000,
			loop: true,
			slidesPerView: "auto",
			centeredSlides: true,
			autoplay: {
				delay: 1,
				disableOnInteraction: false,
			},
			breakpoints: {
				991: {
					spaceBetween: 12,
				},
				600: {
					spaceBetween: 12,
				},
				400: {
					spaceBetween: 12,
				},
				0: {
					spaceBetween: 12,
				},
			},
		});

		const sponsor__text__slide2 = new Swiper(".sponsor-text-slide2", {
			speed: 6000,
			loop: true,
			slidesPerView: "auto",
			centeredSlides: true,
			autoplay: {
				delay: 1,
				disableOnInteraction: false,
			},
			breakpoints: {
				991: {
					spaceBetween: 12,
				},
				600: {
					spaceBetween: 12,
				},
				400: {
					spaceBetween: 12,
				},
				0: {
					spaceBetween: 12,
				},
			},
		});

		//<< 11 >> Banner Slider1
		const heroSLider = new Swiper(".hero-slider", {
			// Optional parameters
			speed: 4500,
			loop: true,
			slidesPerView: 1,
			simulateTouch: false,
			autoplay: true,
			effect: "fade",
			breakpoints: {
				1600: {
					slidesPerView: 1,
				},
				1400: {
					slidesPerView: 1,
				},
				1200: {
					slidesPerView: 1,
				},
				992: {
					slidesPerView: 1,
				},
				768: {
					slidesPerView: 1,
				},
				576: {
					slidesPerView: 1,
				},
				0: {
					slidesPerView: 1,
				},

				a11y: false,
			},
			pagination: {
				el: ".dots",
				clickable: true,
			},

			navigation: {
				prevEl: ".array-next",
				nextEl: ".array-prev",
			},
		});

		//<< Mozzo >>//

		//<< 12 >> Apartment Slider Start <<//
		const apartmentSlider = new Swiper(".apartment-slider", {
			spaceBetween: 24,
			speed: 2000,
			loop: true,
			centeredSlides: true,
			autoplay: {
				delay: 2000,
				// disableOnInteraction: false,
			},
			pagination: {
				el: ".swiper-pagination-cus",
				type: "fraction",
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 1.5,
				},
				991: {
					slidesPerView: 1.2,
				},
				767: {
					slidesPerView: 1,
				},
				575: {
					slidesPerView: 1,
				},
				400: {
					slidesPerView: 1,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});

		//<< 13 >> Offering Slider Start <<//
		const offinformationSlider = new Swiper(".offinformation-slider", {
			spaceBetween: 24,
			speed: 2000,
			loop: true,
			centeredSlides: true,
			autoplay: {
				delay: 2000,
				// disableOnInteraction: false,
			},
			pagination: {
				el: ".swiper-pagination-cus",
				type: "fraction",
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 3.8,
				},
				991: {
					slidesPerView: 2.2,
				},
				767: {
					slidesPerView: 1.5,
				},
				575: {
					slidesPerView: 1.4,
				},
				400: {
					slidesPerView: 1.2,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});

		//<< 14 >> city-slider-wrap Start <<//
		const citySliderWrap = new Swiper(".city-slider-wrap", {
			spaceBetween: 0,
			speed: 2000,
			loop: true,
			autoplay: {
				delay: 2000,
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 3,
				},
				991: {
					slidesPerView: 3,
				},
				767: {
					slidesPerView: 3,
				},
				575: {
					slidesPerView: 2,
				},
				400: {
					slidesPerView: 2,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});

		//<< 15 >> city-slider-wrap2 Start <<//
		const citySliderWrap2 = new Swiper(".city-slider-wrap2", {
			spaceBetween: 24,
			speed: 2000,
			loop: true,
			centeredSlides: true,
			autoplay: {
				delay: 2000,
			},
			navigation: {
				nextEl: ".array-prev",
				prevEl: ".array-next",
			},
			breakpoints: {
				1199: {
					slidesPerView: 2.8,
				},
				991: {
					slidesPerView: 3,
					spaceBetween: 14,
				},
				767: {
					slidesPerView: 3,
					spaceBetween: 14,
				},
				575: {
					slidesPerView: 2,
					spaceBetween: 14,
				},
				400: {
					slidesPerView: 2,
					spaceBetween: 14,
				},
				0: {
					slidesPerView: 1,
					spaceBetween: 14,
				},
			},
		});

		//<< 16 >> Brand Slider Start <<//
		if ($(".brand-slider").length > 0) {
			const brandSlider = new Swiper(".brand-slider", {
				spaceBetween: 30,
				speed: 2000,
				loop: true,
				centeredSlides: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				breakpoints: {
					1199: {
						slidesPerView: 7,
					},
					991: {
						slidesPerView: 6,
					},
					767: {
						slidesPerView: 5,
					},
					575: {
						slidesPerView: 4,
					},
					400: {
						slidesPerView: 3,
					},
					0: {
						slidesPerView: 2,
					},
				},
			});
		}

		//<< 17 >> Project Slider Start <<//
		if ($(".project-slider").length > 0) {
			const projectSlider = new Swiper(".project-slider", {
				spaceBetween: 30,
				speed: 2000,
				loop: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".array-prev",
					prevEl: ".array-next",
				},
				breakpoints: {
					1199: {
						slidesPerView: 3,
					},
					991: {
						slidesPerView: 3,
					},
					767: {
						slidesPerView: 3,
					},
					575: {
						slidesPerView: 2,
					},
					400: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		//<< 18 >> Project Slider Start <<//
		if ($(".project-slider-2").length > 0) {
			const projectSlider2 = new Swiper(".project-slider-2", {
				spaceBetween: 30,
				speed: 2000,
				loop: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".array-prev",
					prevEl: ".array-next",
				},
				breakpoints: {
					1199: {
						slidesPerView: 4,
					},
					991: {
						slidesPerView: 3,
					},
					767: {
						slidesPerView: 2,
					},
					575: {
						slidesPerView: 1,
					},
					400: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		//<< 19 >> Testimonial Slider Start <<//
		if ($(".testimonial-slider-2").length > 0) {
			const testimonialSlider2 = new Swiper(".testimonial-slider-2", {
				spaceBetween: 30,
				speed: 1000,
				loop: true,
				autoplay: {
					delay: 1000,
					disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".array-prev",
					prevEl: ".array-next",
				},
				breakpoints: {
					991: {
						slidesPerView: 2,
					},
					767: {
						slidesPerView: 2,
					},
					575: {
						slidesPerView: 1,
					},
					400: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		//<< 20 >> Progress Bar Js Start <<//
		$(".progress-bar").waypoint(
			function () {
				$(".progress-bar").css({
					animation: "animate-positive 2.6s",
					opacity: "1",
				});
			},
			{ offset: "75%" }
		);

		//<< 21 >> Back To Top Slider Start <<//
		$(window).scroll(function () {
			if ($(this).scrollTop() > 20) {
				$("#back-top").addClass("show");
			} else {
				$("#back-top").removeClass("show");
			}
		});
		$("#back-top").on("click", function () {
			$("html, body").animate({ scrollTop: 0 }, 800);
			return false;
		});
	}); // End Document Ready Function

	//<< 22 >> Price Range Slideer
	document.addEventListener("DOMContentLoaded", function () {
		const minSlider = document.getElementById("min-slider");
		const maxSlider = document.getElementById("max-slider");
		const amount = document.getElementById("amount");

		function updateAmount() {
			const minValue = parseInt(minSlider.value, 10);
			const maxValue = parseInt(maxSlider.value, 10);

			// Ensure the minimum value is always lower than the maximum value
			if (minValue > maxValue) {
				minSlider.value = maxValue;
			}

			// Update the displayed price range
			amount.value = "Rs " + minSlider.value + " - Rs " + maxSlider.value;

			// Calculate the percentage positions of the sliders
			const minPercent =
				((minSlider.value - minSlider.min) /
					(minSlider.max - minSlider.min)) *
				100;
			const maxPercent =
				((maxSlider.value - maxSlider.min) /
					(maxSlider.max - maxSlider.min)) *
				100;

			// Update the background gradient to show the active track color
			minSlider.style.background = `linear-gradient(to right, #000 ${minPercent}%, #000 ${minPercent}%, #000 ${maxPercent}%, #000 ${maxPercent}%)`;
			maxSlider.style.background = `linear-gradient(to right, #000 ${minPercent}%, #000 ${minPercent}%, #000 ${maxPercent}%, #000 ${maxPercent}%)`;
		}

		// Initialize the sliders and track with default values
		amount && updateAmount();

		// if (minSlider && maxSlider) {

		// Add event listeners for both sliders
		minSlider && minSlider.addEventListener("input", updateAmount);
		maxSlider && maxSlider.addEventListener("input", updateAmount);
		// }
	});
	document.addEventListener("DOMContentLoaded", function () {
		const bookingForm = document.querySelector("#booking-form");

		if (bookingForm) {
			bookingForm.addEventListener("submit", function (e) {
				e.preventDefault();

				const checkIn = bookingForm.querySelector("#check-in").value;
				const checkOut = bookingForm.querySelector("#check-out").value;
				const adults = bookingForm.querySelector("#adults").value;
				const children = bookingForm.querySelector("#children").value;

				// Validate dates
				if (!checkIn || !checkOut) {
					alert("Please select both check-in and check-out dates.");
					return;
				}

				// Ensure check-out is after check-in
				if (new Date(checkOut) <= new Date(checkIn)) {
					alert("Check-out date must be after check-in date.");
					return;
				}

				alert(`Booking Details:
                Check-in: ${checkIn}
                Check-out: ${checkOut}
                Adults: ${adults}
                Children: ${children}`);
			});
		}
	});

	//<< 23 >> quntity increment and decrement
	$(document).on("click", ".quantityIncrement", function () {
		const input = $(this).siblings("input");
		input.val(parseInt(input.val(), 10) + 1);
	});
	$(document).on("click", ".quantityDecrement", function () {
		const input = $(this).siblings("input");
		const currentVal = parseInt(input.val(), 10);
		if (currentVal > 1) input.val(currentVal - 1);
	});

	//<< 24 >> Preloader
	function loader() {
		$(window).on("load", function () {
			// Animate loader off screen
			$(".preloader").addClass("loaded");
			$(".preloader").delay(600).fadeOut();
		});
	}

	$(document).ready(function () {
		// When accordion opens
		$(".accordion-collapse").on("show.bs.collapse", function () {
			$(this).closest(".accordion-item").addClass("active");
		});

		// When accordion closes
		$(".accordion-collapse").on("hide.bs.collapse", function () {
			$(this).closest(".accordion-item").removeClass("active");
		});

		// On page load: check if any item is already open
		$(".accordion-collapse.show").each(function () {
			$(this).closest(".accordion-item").addClass("active");
		});
	});

	loader();
})(jQuery); // End jQuery
