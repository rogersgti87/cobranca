(function($) {

	"use strict";


	//Hide Loading Box (Preloader)
	function handlePreloader() {
		if($('.preloader').length){
			$('.preloader').delay(200).fadeOut(500);
		}
	}


	//Update Header Style and Scroll to Top
	function headerStyle() {
		if($('.main-header').length){
			//var windowpos = $(window).scrollTop();
            var windowpos = '';
			var siteHeader = $('.main-header');
			var scrollLink = $('.scroll-to-top');
			if (windowpos >= 200) {
				siteHeader.addClass('fixed-header');
				scrollLink.fadeIn(300);
			} else {
				siteHeader.removeClass('fixed-header');
				scrollLink.fadeOut(300);
			}
		}
	}

	headerStyle();


	//Submenu Dropdown Toggle
	if($('.main-header .navigation li.dropdown ul').length){
		$('.main-header .navigation li.dropdown').append('<div class="dropdown-btn"><span class="fa fa-angle-down"></span></div>');

		//Dropdown Button
		$('.main-header li.dropdown .dropdown-btn').on('click', function() {
			$(this).prev('ul').slideToggle(500);
		});


		//Disable dropdown parent link
		$('.navigation li.dropdown > a').on('click', function(e) {
			e.preventDefault();
		});
	}

	//Hidden Bar Menu Config
	function hiddenBarMenuConfig() {
		var menuWrap = $('.hidden-bar .side-menu');
		// appending expander button
		menuWrap.find('.dropdown').children('a').append(function () {
			return '<button type="button" class="btn expander"><i class="icon fa fa-angle-down"></i></button>';
		});
		// hidding submenu
		menuWrap.find('.dropdown').children('ul').hide();
		// toggling child ul
		menuWrap.find('.btn.expander').each(function () {
			$(this).on('click', function () {
				$(this).parent() // return parent of .btn.expander (a)
					.parent() // return parent of a (li)
						.children('ul').slideToggle();

				// adding class to expander container
				$(this).parent().toggleClass('current');
				// toggling arrow of expander
				$(this).find('i').toggleClass('fa-angle-up fa-angle-down');

				return false;

			});
		});
	}

	hiddenBarMenuConfig();

	//Hidden Sidebar
	if ($('.hidden-bar').length) {
		var hiddenBar = $('.hidden-bar');
		var hiddenBarOpener = $('.hidden-bar-opener');
		var hiddenBarCloser = $('.hidden-bar-closer');
		$('.hidden-bar-wrapper').mCustomScrollbar();

		//Show Sidebar
		hiddenBarOpener.on('click', function () {
			hiddenBar.addClass('visible-sidebar');
		});

		//Hide Sidebar
		hiddenBarCloser.on('click', function () {
			hiddenBar.removeClass('visible-sidebar');
		});

	}


	//Main Slider
	if($('.main-slider').length){
		$('.main-slider').bxSlider({
			controls:true,
			nextText: '<span class="fa fa-arrow-right"></span>',
			prevText: '<span class="fa fa-arrow-left"></span>',
			adaptiveHeight:true,
			responsive:true,
			auto:true,
			autoHover:true,
			pause:7000,
			mode:'fade'
		});
	}


	//Custom Seclect Box
	if($('.custom-select-box').length){
		$('.custom-select-box').selectmenu().selectmenu('menuWidget').addClass('overflow');
	}


	//Three Items Carousel
	if ($('.three-items-carousel').length) {
		$('.three-items-carousel').owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			smartSpeed: 500,
			autoplay: 4000,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:2
				},
				1024:{
					items:2
				},
				1200:{
					items:3
				}
			}
		});
	}

	//Four Items Carousel
	if ($('.four-items-carousel').length) {
		$('.four-items-carousel').owlCarousel({
			loop:true,
			margin:25,
			nav:true,
			smartSpeed: 500,
			autoplay: 4000,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:2
				},
				800:{
					items:3
				},
				1024:{
					items:4
				},
				1200:{
					items:4
				}
			}
		});
	}


	//Team Carousel
	if ($('.team-carousel').length) {
		$('.team-carousel').owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			smartSpeed: 500,
			autoplay: 4000,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:2
				},
				800:{
					items:2
				},
				1024:{
					items:3
				},
				1200:{
					items:4
				}
			}
		});
	}


	//Fluid Gallery Carousel
	if ($('.fluid-gallery-carousel').length) {
		$('.fluid-gallery-carousel').owlCarousel({
			loop:true,
			margin:0,
			nav:true,
			smartSpeed: 500,
			autoplay: 4000,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:2
				},
				1024:{
					items:3
				},
				1400:{
					items:4
				}
			}
		});
	}


	//Single Item Carousel
	if ($('.single-item-carousel').length) {
		$('.single-item-carousel').owlCarousel({
			loop:true,
			margin:0,
			nav:true,
			smartSpeed: 700,
			autoplay: 4000,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});
	}


	//Testimonials Carousel Two
	if ($('.testimonials-carousel-two').length) {
		$('.testimonials-carousel-two').owlCarousel({
			loop:true,
			margin:0,
			nav:true,
			smartSpeed: 500,
			autoplay: 4000,
			navText: [ '<span>Previous</span>', '<span>Next</span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});
	}


	// Property Details Carousel
	if ($('.prod-image-carousel').length && $('.prod-thumbs-carousel').length) {

		var $sync1 = $(".prod-image-carousel"),
			$sync2 = $(".prod-thumbs-carousel"),
			flag = false,
			duration = 500;

			$sync1
				.owlCarousel({
					loop:false,
					items: 1,
					margin: 0,
					nav: true,
					navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
					dots: false,
					autoplay: true,
					autoplayTimeout: 5000
				})
				.on('changed.owl.carousel', function (e) {
					if (!flag) {
						flag = false;
						$sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
						flag = false;
					}
				});

			$sync2
				.owlCarousel({
					loop:false,
					margin: 10,
					items: 1,
					nav: false,
					navText: [ '<span class="icon flaticon-arrows-4"></span>', '<span class="icon flaticon-arrows-3"></span>' ],
					dots: false,
					center: false,
					autoplay: true,
					autoplayTimeout: 5000,
					responsive: {
						0:{
				            items:2,
				            autoWidth: false
				        },
				        400:{
				            items:3,
				            autoWidth: false
				        },
				        600:{
				            items:4,
				            autoWidth: false
				        },
				        1000:{
				            items:4,
				            autoWidth: false
				        },
						1200:{
				            items:4,
				            autoWidth: false
				        }
				    },
				})

		.on('click', '.owl-item', function () {
			$sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);
		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;
				$sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});

	}


	//Tabs Box
	if($('.tabs-box').length){
		$('.tabs-box .tab-buttons .tab-btn').on('click', function(e) {
			e.preventDefault();
			var target = $($(this).attr('data-tab'));

			if ($(target).is(':visible')){
				return false;
			}else{
				target.parents('.tabs-box').find('.tab-buttons').find('.tab-btn').removeClass('active-btn');
				$(this).addClass('active-btn');
				target.parents('.tabs-box').find('.tabs-content').find('.tab').fadeOut(0);
				target.parents('.tabs-box').find('.tabs-content').find('.tab').removeClass('active-tab');
				$(target).fadeIn(300);
				$(target).addClass('active-tab');
			}
		});
	}


	//Tabs Box
	if($('.tabs-with-slider').length){
		$('.tabs-with-slider .tab-buttons .tab-btn').on('click', function(e) {
			e.preventDefault();
			var target = $($(this).attr('data-tab'));

			if ($(target).hasClass('active-tab')){
				return false;
			}else{
				target.parents('.tabs-with-slider').find('.tab-buttons').find('.tab-btn').removeClass('active-btn');
				$(this).addClass('active-btn');
				target.parents('.tabs-with-slider').find('.tabs-content').find('.tab').removeClass('active-tab');
				$(target).addClass('active-tab');
			}
		});
	}


	//Accordion Box
	if($('.accordion-box').length){
		$(".accordion-box").on('click', '.acc-btn', function() {

			var target = $(this).parents('.accordion');

			if($(this).hasClass('active')!==true){
			$('.accordion .acc-btn').removeClass('active');

			}

			if ($(this).next('.acc-content').is(':visible')){
				return false;
			}else{
				$(this).addClass('active');
				$('.accordion').removeClass('active-block');
				$('.accordion .acc-content').slideUp(300);
				target.addClass('active-block');
				$(this).next('.acc-content').slideDown(300);
			}
		});
	}


	//Date Picker
	if($('.datepicker').length){
		$( '.datepicker' ).datepicker();
	}


	//LightBox / Fancybox
	if($('.lightbox-image').length) {
		$('.lightbox-image').fancybox({
			openEffect  : 'fade',
			closeEffect : 'fade',
			helpers : {
				media : {}
			}
		});
	}


	//Sortable Masonary with Filters
	function enableMasonry() {
		if($('.masonry-container').length){

			// Needed variables
			var $container=$('.masonry-container');

			$container.isotope({
				filter:'.masonry-item',
				 masonry: {
					columnWidth : 0
				 },
				animationOptions:{
					duration:500,
					easing:'linear'
				}
			});
		}
	}

	enableMasonry();


	//Mixitup Gallery
	if($('.filter-list').length){
		$('.filter-list').mixItUp({});
	}


	// //Contact Form Validation
	// if($('#contact-form').length){
	// 	$('#contact-form').validate({
	// 		rules: {
	// 			username: {
	// 				required: true
	// 			},
	// 			email: {
	// 				required: true,
	// 				email: true
	// 			},
	// 			subject: {
	// 				required: true
	// 			},
	// 			message: {
	// 				required: true
	// 			}
	// 		}
	// 	});
	// }


	// Scroll to a Specific Div
	if($('.scroll-to-target').length){
		$(".scroll-to-target").on('click', function() {
			var target = $(this).attr('data-target');
		   // animate
		   $('html, body').animate({
			   scrollTop: $(target).offset().top
			 }, 1000);

		});
	}


	// Elements Animation
	if($('.wow').length){
		var wow = new WOW(
		  {
			boxClass:     'wow',      // animated element css class (default is wow)
			animateClass: 'animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       false,       // trigger animations on mobile devices (default is true)
			live:         true       // act on asynchronously loaded content (default is true)
		  }
		);
		wow.init();
	}


/* ==========================================================================
   When document is Scrollig, do
   ========================================================================== */

	$(window).on('scroll', function() {
		headerStyle();
	});


/* ==========================================================================
   When document is loaded, do
   ========================================================================== */

	$(window).on('load', function() {
		handlePreloader();
		enableMasonry();
	});



})(window.jQuery);
