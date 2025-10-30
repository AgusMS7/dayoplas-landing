/*
	MOBILE.JS - Funcionalidades JavaScript para dispositivos móviles
	Dayloplas-IPM-MZA Website
	
	Solo maneja optimizaciones de Mobile, NO la navegación
	(main.js ya maneja el menú con #navPanel)
*/

(function($) {
	"use strict";

	$(document).ready(function() {
		initTouchCarousel();
		initMobileOptimizations();
		initResponsiveImages();
		console.log('🔥 Mobile.js cargado');
	});

	// ========================================
	// CAROUSEL TÁCTIL OPTIMIZADO
	// ========================================
	function initTouchCarousel() {
		$('.carousel .reel').each(function() {
			const $reel = $(this);
			let isScrolling = false;
			let startX, startY, scrollLeft;

			$reel.on('touchstart', function(e) {
				isScrolling = true;
				startX = e.originalEvent.touches[0].pageX - $reel.offset().left;
				startY = e.originalEvent.touches[0].pageY - $reel.offset().top;
				scrollLeft = $reel.scrollLeft();
			});

			$reel.on('touchmove', function(e) {
				if (!isScrolling) return;
				
				const x = e.originalEvent.touches[0].pageX - $reel.offset().left;
				const y = e.originalEvent.touches[0].pageY - $reel.offset().top;
				const walkX = (x - startX) * 2;
				const walkY = (y - startY) * 2;

				if (Math.abs(walkX) > Math.abs(walkY)) {
					e.preventDefault();
					$reel.scrollLeft(scrollLeft - walkX);
				}
			});

			$reel.on('touchend', function() {
				isScrolling = false;
			});

			updateScrollIndicators($reel);
			$reel.on('scroll', function() {
				updateScrollIndicators($(this));
			});
		});
	}

	function updateScrollIndicators($reel) {
		// Avoid inserting indicators when we intentionally suppressed carousel after hints
		if (document && document.body && document.body.classList && document.body.classList.contains('mobile-no-carousel-after')) {
			$reel.closest('.carousel').find('.scroll-indicator').remove();
			return;
		}

		const scrollLeft = $reel.scrollLeft();
		const scrollWidth = $reel[0].scrollWidth;
		const clientWidth = $reel[0].clientWidth;
		const $carousel = $reel.closest('.carousel');

		$carousel.find('.scroll-indicator').remove();

		if ($(window).width() <= 768) {
			if (scrollLeft > 10) {
				$carousel.prepend('<div class="scroll-indicator left">←</div>');
			}
			if (scrollLeft < (scrollWidth - clientWidth - 10)) {
				$carousel.append('<div class="scroll-indicator right">→</div>');
			}
		}
	}

	// ========================================
	// OPTIMIZACIONES GENERALES PARA MÓVIL
	// ========================================
	function initMobileOptimizations() {
		let ticking = false;
		
		$(window).on('scroll', function() {
			if (!ticking) {
				requestAnimationFrame(function() {
					handleScroll();
					ticking = false;
				});
				ticking = true;
			}
		});

		optimizeForms();
		improveTouchInteractions();

		if ($(window).width() <= 768) {
			implementLazyLoading();
		}
	}

	function handleScroll() {
		const scrollTop = $(window).scrollTop();
		
		$('.formacion-section').each(function() {
			const $section = $(this);
			const sectionTop = $section.offset().top;
			const windowHeight = $(window).height();
			
			if (scrollTop + windowHeight > sectionTop + 100) {
				$section.addClass('visible');
			}
		});
	}

	function optimizeForms() {
		$('input[type="text"], input[type="tel"], input[type="email"], textarea').attr('autocomplete', 'off');

		$('#formConsulta input, #formConsulta textarea').on('focus', function() {
			$(this).parent().addClass('focused');
		}).on('blur', function() {
			if (!$(this).val()) {
				$(this).parent().removeClass('focused');
			}
		});

		$('#formConsulta input[required]').on('blur', function() {
			const $input = $(this);
			if ($input.val().trim() === '') {
				$input.addClass('error');
			} else {
				$input.removeClass('error');
			}
		});
	}

	function improveTouchInteractions() {
		$('.button, .boton-principal, a[href]').on('touchstart', function() {
			$(this).addClass('touch-active');
		}).on('touchend touchcancel', function() {
			const $this = $(this);
			setTimeout(function() {
				$this.removeClass('touch-active');
			}, 150);
		});

		$('a[href^="#"]').on('click', function(e) {
			e.preventDefault();
			const target = $($(this).attr('href'));
			if (target.length) {
				const offset = $(window).width() <= 768 ? 80 : 100;
				$('html, body').animate({
					scrollTop: target.offset().top - offset
				}, 600);
			}
		});
	}

	function implementLazyLoading() {
		$('img').each(function() {
			const $img = $(this);
			if (!$img.attr('data-lazy-loaded')) {
				$img.attr('loading', 'lazy');
				$img.attr('data-lazy-loaded', 'true');
			}
		});
	}

	// ========================================
	// GESTIÓN RESPONSIVE DE IMÁGENES
	// ========================================
	function initResponsiveImages() {
		$(window).on('load resize', function() {
			handleResponsiveImages();
		});
	}

	function handleResponsiveImages() {
		const isMobile = $(window).width() <= 768;
		
		$('.carousel .reel article img').each(function() {
			const $img = $(this);
			if (isMobile) {
				$img.css({
					'object-fit': 'contain',
					'height': '180px',
					'background': '#ffffff'
				});
			}
		});

		$('.formacion-header img').each(function() {
			const $img = $(this);
			if (isMobile) {
				$img.css('height', '250px');
			} else {
				$img.css('height', '400px');
			}
		});
	}

	// ========================================
	// MANEJO DE ORIENTACIÓN
	// ========================================
	$(window).on('orientationchange', function() {
		setTimeout(function() {
			handleResponsiveImages();
			
			$('.carousel .reel').each(function() {
				updateScrollIndicators($(this));
			});
		}, 300);
	});

	// ========================================
	// CSS DINÁMICO PARA INDICADORES Y EFECTOS
	// ========================================
	$(document).ready(function() {
		const dynamicStyles = `
			<style id="mobile-dynamic-styles">
			.scroll-indicator {
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				background: rgba(26, 79, 255, 0.8);
				color: white;
				padding: 8px 12px;
				border-radius: 20px;
				font-size: 14px;
				font-weight: bold;
				z-index: 10;
				pointer-events: none;
				animation: fadeInOut 2s ease-in-out;
			}
			
			.scroll-indicator.left {
				left: 10px;
			}
			
			.scroll-indicator.right {
				right: 10px;
			}
			
			@keyframes fadeInOut {
				0%, 100% { opacity: 0; }
				20%, 80% { opacity: 1; }
			}
			
			.touch-active {
				transform: scale(0.95) !important;
				opacity: 0.8 !important;
				transition: all 0.1s ease !important;
			}
			
			.formacion-section {
				opacity: 0;
				transform: translateY(30px);
				transition: all 0.6s ease;
			}
			
			.formacion-section.visible {
				opacity: 1;
				transform: translateY(0);
			}
			
			#formConsulta .focused {
				transform: scale(1.02);
				transition: transform 0.2s ease;
			}
			
			#formConsulta .error {
				border-color: #ff4444 !important;
				box-shadow: 0 0 5px rgba(255, 68, 68, 0.3) !important;
			}
			</style>
		`;
		
		$('head').append(dynamicStyles);
	});

})(jQuery);
