/*
	MOBILE.JS - Funcionalidades JavaScript para dispositivos móviles
	Dayloplas-IPM-MZA Website
*/

(function($) {
	"use strict";

	// ========================================
	// INICIALIZACIÓN CUANDO EL DOM ESTÁ LISTO
	// ========================================
	$(document).ready(function() {
		initMobileMenu();
		initTouchCarousel();
		initMobileOptimizations();
		initResponsiveImages();
		console.log('🔥 Mobile.js cargado y funcionalidades iniciadas');
	});

	// ========================================
	// MENÚ HAMBURGUESA Y NAVEGACIÓN MÓVIL
	// ========================================
	function initMobileMenu() {
		// Crear estructura del menú móvil
		createMobileMenuStructure();
		
		// Event listeners para el menú
		$(document).on('click', '.mobile-menu-toggle', function(e) {
			e.preventDefault();
			toggleMobileMenu();
		});

		// Cerrar menú al hacer click fuera
		$(document).on('click', '.mobile-nav', function(e) {
			if (e.target === this) {
				closeMobileMenu();
			}
		});

		// Manejar submenús
		$(document).on('click', '.mobile-nav .has-submenu > a', function(e) {
			e.preventDefault();
			toggleSubmenu($(this).parent());
		});

		// Cerrar menú al redimensionar ventana
		$(window).resize(function() {
			if ($(window).width() > 960) {
				closeMobileMenu();
			}
		});

		// Cerrar menú con tecla ESC
		$(document).keydown(function(e) {
			if (e.keyCode === 27) { // ESC key
				closeMobileMenu();
			}
		});
	}

	function createMobileMenuStructure() {
		// Verificar si ya existe
		if ($('.mobile-menu-toggle').length) return;

		// Crear botón hamburguesa
		const toggleButton = `
			<button class="mobile-menu-toggle" aria-label="Menú de navegación">
				<div class="hamburger">
					<span></span>
					<span></span>
					<span></span>
				</div>
			</button>
		`;

		// Clonar el menú existente para crear la versión móvil
		const $originalNav = $('#nav');
		if ($originalNav.length) {
			const $mobileNav = $originalNav.clone();
			$mobileNav.attr('id', 'mobile-nav').addClass('mobile-nav');
			
			// Marcar elementos con submenús
			$mobileNav.find('li').each(function() {
				const $li = $(this);
				if ($li.find('ul').length > 0) {
					$li.addClass('has-submenu');
				}
			});

			// Agregar estructura al DOM
			$('body').append(toggleButton + '<nav id="mobile-nav" class="mobile-nav">' + $mobileNav.html() + '</nav>');
		}
	}

	function toggleMobileMenu() {
		const $toggle = $('.mobile-menu-toggle');
		const $mobileNav = $('.mobile-nav');
		
		$toggle.toggleClass('active');
		$mobileNav.toggleClass('active');
		
		// Prevenir scroll del body cuando el menú está abierto
		if ($mobileNav.hasClass('active')) {
			$('body').css('overflow', 'hidden');
		} else {
			$('body').css('overflow', '');
		}
	}

	function closeMobileMenu() {
		$('.mobile-menu-toggle').removeClass('active');
		$('.mobile-nav').removeClass('active');
		$('body').css('overflow', '');
	}

	function toggleSubmenu($li) {
		const $submenu = $li.find('ul').first();
		$li.toggleClass('active');
		$submenu.slideToggle(300);
	}

	// ========================================
	// CAROUSEL TÁCTIL OPTIMIZADO
	// ========================================
	function initTouchCarousel() {
		$('.carousel .reel').each(function() {
			const $reel = $(this);
			let isScrolling = false;
			let startX, startY, scrollLeft;

			// Mejorar el scroll táctil
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

				// Solo prevenir scroll vertical si el movimiento es mayormente horizontal
				if (Math.abs(walkX) > Math.abs(walkY)) {
					e.preventDefault();
					$reel.scrollLeft(scrollLeft - walkX);
				}
			});

			$reel.on('touchend', function() {
				isScrolling = false;
			});

			// Indicadores de scroll mejorados
			updateScrollIndicators($reel);
			$reel.on('scroll', function() {
				updateScrollIndicators($(this));
			});
		});
	}

	function updateScrollIndicators($reel) {
		const scrollLeft = $reel.scrollLeft();
		const scrollWidth = $reel[0].scrollWidth;
		const clientWidth = $reel[0].clientWidth;
		const $carousel = $reel.closest('.carousel');

		// Remover indicadores existentes
		$carousel.find('.scroll-indicator').remove();

		// Solo mostrar indicadores en móvil
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
		// Mejorar performance del scroll
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

		// Optimizar formularios para móvil
		optimizeForms();

		// Mejorar interacciones táctiles
		improveTouchInteractions();

		// Lazy loading para imágenes en móvil
		if ($(window).width() <= 768) {
			implementLazyLoading();
		}
	}

	function handleScroll() {
		const scrollTop = $(window).scrollTop();
		
		// Ocultar menú móvil al hacer scroll
		if (scrollTop > 100 && $('.mobile-nav').hasClass('active')) {
			closeMobileMenu();
		}

		// Animaciones de entrada para elementos visibles
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
		// Prevenir zoom en inputs en iOS
		$('input[type="text"], input[type="tel"], input[type="email"], textarea').attr('autocomplete', 'off');

		// Mejorar UX del formulario de consulta
		$('#formConsulta input, #formConsulta textarea').on('focus', function() {
			$(this).parent().addClass('focused');
		}).on('blur', function() {
			if (!$(this).val()) {
				$(this).parent().removeClass('focused');
			}
		});

		// Validación en tiempo real para móvil
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
		// Mejorar el feedback táctil en botones
		$('.button, .boton-principal, a[href]').on('touchstart', function() {
			$(this).addClass('touch-active');
		}).on('touchend touchcancel', function() {
			const $this = $(this);
			setTimeout(function() {
				$this.removeClass('touch-active');
			}, 150);
		});

		// Smooth scroll para enlaces internos
		$('a[href^="#"]').on('click', function(e) {
			e.preventDefault();
			const target = $($(this).attr('href'));
			if (target.length) {
				const offset = $(window).width() <= 768 ? 80 : 100;
				$('html, body').animate({
					scrollTop: target.offset().top - offset
				}, 600);
				
				// Cerrar menú móvil después de navegar
				setTimeout(closeMobileMenu, 300);
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
		
		// Ajustar imágenes del carousel según el dispositivo
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

		// Ajustar imágenes de cabecera
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
	// UTILIDADES PARA DEBUGGING
	// ========================================
	function logMobileInfo() {
		if (window.console) {
			console.log('📱 Información del dispositivo móvil:');
			console.log('Ancho de ventana:', $(window).width());
			console.log('Alto de ventana:', $(window).height());
			console.log('User Agent:', navigator.userAgent);
			console.log('Touch support:', 'ontouchstart' in window);
		}
	}

	// ========================================
	// MANEJO DE ORIENTACIÓN
	// ========================================
	$(window).on('orientationchange', function() {
		setTimeout(function() {
			// Reajustar elementos después del cambio de orientación
			handleResponsiveImages();
			
			// Cerrar menú móvil si está abierto
			closeMobileMenu();
			
			// Recalcular carousel
			$('.carousel .reel').each(function() {
				updateScrollIndicators($(this));
			});
		}, 300);
	});

	// ========================================
	// EXPOSER FUNCIONES PÚBLICAS
	// ========================================
	window.MobileUtils = {
		closeMobileMenu: closeMobileMenu,
		toggleMobileMenu: toggleMobileMenu,
		logMobileInfo: logMobileInfo
	};

})(jQuery);

// ========================================
// CSS DINÁMICO PARA INDICADORES DE SCROLL
// ========================================
$(document).ready(function() {
	const scrollIndicatorStyles = `
		<style id="mobile-scroll-indicators">
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
	
	$('head').append(scrollIndicatorStyles);
});