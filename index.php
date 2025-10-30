<?php
	session_start();
	require_once 'conexion.php';
	require_once 'model_daylo.php';

	$Modelo = new ModelDaylo($host, $db, $user, $pass);
    
	$idioma = 'es';
	$tipos = $Modelo->obtenerTiposFormacion($idioma);
	$formacion = $Modelo->obtenerFormaciones($idioma);

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>DAYLOPLAS-IPM-MZA</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta name="format-detection" content="telephone=no" />
		<meta name="mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="default" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<link rel="stylesheet" href="assets/css/naty.css" />
	<link rel="stylesheet" href="assets/css/mobile.css" />
	<link rel="stylesheet" href="assets/css/responsive-fix.css" />
	<link rel="stylesheet" href="assets/css/footer-responsive.css" />
	<?php if (!isset($_GET['tipo']) || $_GET['tipo'] !== 'curso'): ?>
	<link rel="stylesheet" href="assets/css/carousel-center.css" />
	<?php endif; ?>
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<style>
					.boton-principal {
						background-color: hsla(215, 84.80%, 56.30%, 0.94);
						color: #fff;
						padding: .9em 2em;
						font-size: 1.1rem;
						border-radius: 40px;
						border: none;
						box-shadow: 0 4px 8px rgb(0,0,0,0.1);
						transition: all 0.3s ease;
						display: inline-flex;
						align-items: center;
						gap: .5em;
					}
					.boton-principal:hover {
						background-color: rgb(92, 89, 240);
						transform: scale(1.05);
						color: #000;
					}
					.logo {
						width: 150px;
						margin: 0 10px;
					}
					.centrado {
						display: flex;
						justify-content: center;     
					}
					
					/* 🎯 ESTILOS DE CENTRADO PARA EL HEADER */
					.logo-container {
						display: flex;
						justify-content: center;
						align-items: center;
						gap: 20px;
						margin: 1rem auto;
						text-align: center;
					}
					
					#header .inner header h1 {
						text-align: center;
						margin: 0.5rem auto;
						width: 100%;
						font-size: 2.5em;
						line-height: 1.1;
						font-weight: normal;
					}
					
					#header .inner header h1 a {
						color: inherit;
						text-decoration: none;
						display: inline;
						text-align: center;
					}
					
					#header .inner header p {
						text-align: center;
						max-width: 800px;
						margin: 1rem auto;
						line-height: 1.6;
					}
					
					/* Responsive para header */
					@media screen and (max-width: 736px) {
						#header .inner header h1 {
							font-size: 1.8em;
						}
						.logo-container {
							flex-direction: column;
							gap: 10px;
						}
						.logo {
							width: 120px;
						}
					}
					/* 🎨 Estilo elegante para gradientes dinámicos - DEFINITIVO */
					#header {
						background-size: cover;
						background-position: center;
						background-repeat: no-repeat;
						background-attachment: fixed;
						transition: background 1.5s ease-in-out;
						position: relative;
						min-height: 75vh;
					}
					
					/* Efecto de superposición sutil para mejor legibilidad */
					#header::before {
						content: '';
						position: absolute;
						top: 0;
						left: 0;
						right: 0;
						bottom: 0;
						background: rgba(255, 255, 255, 0.05);
						backdrop-filter: blur(1px);
						z-index: 1;
					}
					
					/* Asegurar que el contenido esté por encima */
					#header .inner {
						position: relative;
						z-index: 2;
					}
					#nav a {
						color: #181818 !important;
						font-weight: bold;
						transition: color 0.2s, font-size 0.2s;
					}
					#nav a:hover {
						color: #1a4fff !important;
						font-size: 1.13em;
					}
					#img_features {
						height: 250px;
						object-fit: cover;
						width: 100%;
						display: block;
					}
					
					/* ESTILOS PARA IMÁGENES DE CABECERA - FORZADO */
					.formacion-header img {
						display: block !important;
						width: 100% !important;
						max-width: 900px !important;
						height: 400px !important;
						object-fit: cover !important;
						margin: 0 auto 1.5rem auto !important;
						border-radius: 10px !important;
						box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
					}
					
					section.formacion-section .formacion-header img,
					.wrapper.style3 .formacion-header img,
					section[id] .formacion-header img {
						display: block !important;
						width: 100% !important;
						max-width: 900px !important;
						height: 400px !important;
						object-fit: cover !important;
						margin: 0 auto 1.5rem auto !important;
						border-radius: 10px !important;
						box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
					}
					
					@media screen and (max-width: 736px) {
						.formacion-header img,
						section.formacion-section .formacion-header img,
						.wrapper.style3 .formacion-header img,
						section[id] .formacion-header img {
							height: 300px !important;
							max-width: 100% !important;
						}
					}
					
					/* ESTILOS PARA IMÁGENES DE FORMACIONES EN CAROUSEL - PROFESIONALES */
					.carousel .reel article img {
						width: 100%;
						height: 220px;
						object-fit: contain;
						display: block;
						background: #ffffff;
						padding: 15px;
						border-radius: 8px;
						transition: transform 0.3s ease;
					}
					
					/* 🎠 CAROUSEL CON ARRASTRE MANUAL FUNCIONAL */
					.carousel .reel {
						display: flex !important;
						flex-wrap: nowrap !important;
						width: max-content !important;
						overflow-x: auto !important;
						overflow-y: hidden !important;
						-webkit-overflow-scrolling: touch !important;
						scrollbar-width: none !important;
						-ms-overflow-style: none !important;
						position: relative !important;
					}
					
					/* Ocultar scrollbar en WebKit browsers */
					.carousel .reel::-webkit-scrollbar {
						display: none !important;
					}
					
					.carousel .reel article {
						flex: 0 0 auto !important;
						width: 320px !important;
						margin-right: 2em !important;
						transition: transform 0.2s ease !important;
					}
					
					/* Efecto hover mejorado */
					.carousel .reel article:hover {
						transform: translateY(-5px) scale(1.02) !important;
						box-shadow: 0 10px 25px rgba(135, 206, 235, 0.3) !important;
					}
					
					/* ��� ESTILOS ESPECÍFICOS PARA DISPOSITIVOS TÁCTILES */
					@media (hover: none) and (pointer: coarse) {
						.carousel .reel article {
							width: 280px !important; /* Más pequeño en móvil */
							margin-right: 1.5em !important;
						}
					}
					
					/* 💡 INDICADORES VISUALES MEJORADOS */
					.carousel {
						position: relative;
					}
					
					.carousel::after {
						content: "⬅️➡️ Usa el scroll horizontal para navegar";
						position: absolute;
						bottom: -35px;
						left: 50%;
						transform: translateX(-50%);
						font-size: 11px;
						color: #666;
						background: rgba(255,255,255,0.9);
						padding: 4px 8px;
						border-radius: 12px;
						z-index: 10;
						pointer-events: none;
						opacity: 0.8;
						font-weight: 500;
					}
					
					@media (max-width: 767px) {
						.carousel::after {
							content: "👆 Desliza horizontalmente";
						}
					}
					
					/* Efecto visual normal - sin arrastre */
					.carousel .reel {
						cursor: default !important;
					}
					
					/* Contenedor de imagen optimizado */
					.carousel .reel article a.image {
						background: #f8f8f8 !important;
						display: block !important;
						min-height: 220px !important;
						max-height: 280px !important;
						border-radius: 10px 10px 0 0 !important;
						overflow: hidden !important;
						position: relative !important;
					}
					
					/* FIX SIMPLE: Solo agregar padding al final para mostrar última imagen completa */
					.carousel .reel::after {
						content: '';
						display: block;
						width: 2rem;
						flex-shrink: 0;
					}
					
					/* Artículo del carousel con estructura mejorada */
					.carousel .reel article {
						background: #ffffff !important;
						border: 1px solid rgba(135, 206, 235, 0.3) !important;
						border-radius: 10px !important;
						overflow: hidden !important;
						min-height: 380px !important;
						box-shadow: 0 4px 12px rgba(135, 206, 235, 0.15) !important;
						transition: transform 0.3s ease, box-shadow 0.3s ease !important;
					}
					
					/* Efecto hover para los artículos del carrusel */
					.carousel .reel article:hover {
						transform: translateY(-3px) !important;
						box-shadow: 0 8px 20px rgba(135, 206, 235, 0.25) !important;
					}
					
					/* Estilos finales para todas las imágenes del carousel */
					.carousel article img,
					.carousel .reel article img,
					section .carousel .reel article img {
						object-fit: contain !important;
						background: #ffffff !important;
						padding: 10px !important;
						height: auto !important;
						min-height: 180px !important;
						max-height: 250px !important;
						border: 1px solid rgba(135, 206, 235, 0.4) !important;
						border-radius: 6px !important;
						transition: border-color 0.3s ease !important;
					}
					
					/* Efecto hover para las imágenes */
					.carousel .reel article:hover img {
						border-color: rgba(135, 206, 235, 0.7) !important;
					}
					
					/* Estilos específicos para diferentes tipos de carousel */
					.carousel.cursos .reel article img,
					section[id="cursos"] .carousel .reel article img,
					section[id="talleres"] .carousel .reel article img,
					section[id="jornadas"] .carousel .reel article img {
						object-fit: contain !important;
						background: #ffffff !important;
						padding: 10px !important;
						border: 2px solid #87CEEB !important; /* Celeste suave */
						border-radius: 8px !important;
						box-shadow: 0 2px 6px rgba(135, 206, 235, 0.2) !important; /* Sombra celeste suave */
					}
					
					/* ELIMINAR DIVISIONES NEGRAS - FONDOS UNIFORMES */
					body, html {
						background-color: #ffffff !important;
					}
					
					/* Asegurar que todas las secciones tengan fondo blanco */
					.wrapper, .wrapper.style1, .wrapper.style2, .wrapper.style3 {
						background: #ffffff !important;
						border: none !important;
					}
					
					/* Eliminar cualquier borde negro */
					section, div, article {
						border-color: transparent !important;
					}
					
					/* Fondo específico para secciones de formación */
					.formacion-section {
						background: #ffffff !important;
						border-top: none !important;
						border-bottom: none !important;
					}
					
					/* DIVISIONES SUAVES ENTRE SECCIONES */
					.wrapper::after {
						content: '';
						display: block;
						height: 1px;
						background: linear-gradient(90deg, transparent 0%, rgba(135, 206, 235, 0.3) 50%, transparent 100%);
						margin: 2rem auto;
						width: 80%;
					}
					
					/* Separadores más visibles entre diferentes tipos de contenido */
					.formacion-section::after {
						content: '';
						display: block;
						height: 2px;
						background: linear-gradient(90deg, transparent 0%, rgba(135, 206, 235, 0.4) 20%, rgba(173, 216, 230, 0.6) 50%, rgba(135, 206, 235, 0.4) 80%, transparent 100%);
						margin: 3rem auto;
						width: 60%;
						border-radius: 1px;
					}
					
					/* Línea decorativa más sutil para el header */
					#header::after {
						content: '';
						display: block;
						height: 1px;
						background: linear-gradient(90deg, transparent 0%, rgba(135, 206, 235, 0.5) 50%, transparent 100%);
						margin: 0 auto;
						width: 90%;
					}
				</style>

		<script>
			// 🎨 Gradientes elegantes profesionales - VERSIÓN DEFINITIVA
			document.addEventListener('DOMContentLoaded', function () {
				// 🌟 Gradientes profesionales armónicos
				const gradientes = [
					// Celeste profesional suave
					'linear-gradient(135deg, rgba(135, 206, 235, 0.85) 0%, rgba(240, 248, 255, 0.95) 50%, rgba(255, 255, 255, 0.98) 100%)',
					
					// Azul institucional elegante  
					'linear-gradient(45deg, rgba(74, 144, 226, 0.75) 0%, rgba(135, 206, 235, 0.85) 50%, rgba(248, 249, 250, 0.95) 100%)',
					
					// Sunrise educativo cálido
					'linear-gradient(135deg, rgba(255, 229, 180, 0.8) 0%, rgba(255, 234, 167, 0.85) 30%, rgba(221, 214, 254, 0.8) 70%, rgba(168, 230, 207, 0.85) 100%)',
					
					// Gradiente radial moderno
					'radial-gradient(ellipse at top, rgba(74, 144, 226, 0.6) 0%, rgba(135, 206, 235, 0.4) 40%, rgba(240, 248, 255, 0.9) 80%, rgba(255, 255, 255, 0.98) 100%)',
					
					// Degradado vertical suave
					'linear-gradient(180deg, rgba(173, 216, 230, 0.7) 0%, rgba(224, 246, 255, 0.85) 50%, rgba(248, 249, 250, 0.95) 100%)'
				];

				let index = 0;
				const header = document.getElementById('header');

				function cambiarFondo() {
					header.style.background = gradientes[index];
					header.style.backgroundAttachment = 'fixed';
					header.style.transition = 'background 1.5s ease-in-out';
					index = (index + 1) % gradientes.length;
				}

				cambiarFondo(); // aplicar el primer gradiente al cargar
				setInterval(cambiarFondo, 6000); // cambiar cada 6 segundos (un poco más lento)
			});
		</script>
	</head>
	<body class="homepage is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<div id="header"> <!-- 👈 Aquí se aplicará el fondo dinámico -->

					<!-- Inner -->
					<div class="inner">
						<header>
							<div class="logo-container">
								<img class="logo bounce-logo" src="images/logos/logo_dayloplas.png" alt="">
								<img class="logo bounce-logo" src="images/logos/logo_ipm.png" alt="">
							</div>
							<h1><a href="index.html" id="logo" class="title-link">Dayloplas-I.P.M. Mendoza</a></h1>
							<hr />
							<p class="header-subtitle">"Una institución pensada para quienes desean destacarse en el mundo
								de la estética, la podología y el bienestar."
								<br />
									"Educación de calidad con salida laboral real."
							</p>
						</header>
						<footer>
							<a href="#features" class="boton-principal">
								<i class="icon solid fa-arrow-right"></i>
								Tu Futuro empieza aquí
							</a>
						</footer>
					</div> 					

					<!-- Nav -->
						<!-- <nav id="nav">
							<ul>
								<li><a class="detalle" href="index.php" >INICIO</a></li>
								<li>
									<a href="#cursos">CURSOS</a>
									<ul>
										<li ><a href="detalle.php?slug=peluqueria" target="_blank" style="display: block;">Curso de Peluquería</a></li>
										<li >
											<a href="detalle.php?slug=auriculoterapia" target="_blank" style="display: block;">Curso de Auriculoterápia</a>
										</li>

										<li >
											<a href="detalle.php?slug=cosmetologia" target="_blank" style="display: block;">Curso de Cosmetología</a>
										</li>

										<li >
											<a href="detalle.php?slug=extensiondepestañas" target="_blank" style="display: block;">Curso de Extensión de Pestañas</a>
										</li>

										<li >
											<a href="detalle.php?slug=auxiliardepodologia" target="_blank" style="display: block;">Curso de Auxiliar de Podología</a>
										</li>

										<li >
											<a href="detalle.php?slug=depilacionintegral" target="_blank" style="display: block;">Curso de Depilación Integral</a>
										</li>

										<li >
											<a href="detalle.php?slug=cejasypestañas" target="_blank" style="display: block;">Curso de Cejas y Pestañas</a>
										</li>

										<li >
											<a href="detalle.php?slug=digitopuntura" target="_blank" style="display: block;">Curso de Digitopuntura</a>
										</li>

										<li >
											<a href="detalle.php?slug=corteybarberia" target="_blank" style="display: block;">Curso de Corte y Barbería</a>
										</li>

										<li >
											<a href="detalle.php?slug=expertaenbellezadepies" target="_blank" style="display: block;">Curso de Experta en Belleza de Pies</a>
										</li>

										<li >
											<a href="detalle.php?slug=linfodrenaje" target="_blank" style="display: block;">Curso de Linfodrenaje</a>
										</li>

										<li >
											<a href="detalle.php?slug=manicuriaintegral" target="_blank" style="display: block;">Curso de Manicuría Integral</a>
										</li>

										<li >
											<a href="detalle.php?slug=microblading" target="_blank" style="display: block;">Curso de Microblading</a>
										</li>

										<li >
											<a href="detalle.php?slug=podocosmiatria" target="_blank" style="display: block;">Curso Podocosmiatría</a>
										</li>

										<li >
											<a href="detalle.php?slug=maquillajeintegral" target="_blank" style="display: block;">Curso de Maquillaje Integral</a>
										</li>

										<li >
											<a href="detalle.php?slug=reflexologia" target="_blank" style="display: block;">Curso de Reflexología</a>
										</li>

										<li >
											<a href="detalle.php?slug=masajeintegral" target="_blank" style="display: block;">Curso de Masaje Integral</a>
										</li>

										<li >
											<a href="detalle.php?slug=masajetailandes" target="_blank" style="display: block;">Curso de Masaje Tailandés</a>
										</li>
										
									</ul>
								</li>
								<li><a href="detalle.php?tipo=talleres">TALLERES</a></li>
								<li><a href="detalle.php?tipo=jornadas">JORNADAS</a></li>
								<li><a href="#" onclick="manejarContacto(event); return false;">CONTACTO</a></li>
								<li><a href="#" onclick="abrirModalNosotros(); return false;">NOSOTROS</a></li>
								<li><a href="login.php">INICIAR SESIÓN</a></li>
							</ul>
						</nav> -->

						<!-- Nav -->
						<nav id="nav">
							<ul>
								<li><a class="detalle" href="index.php" >INICIO</a></li>
								<?php foreach ($tipos as $tipo): 
									if (!empty($tipo)):
										$formaciones = $Modelo->obtenerFormacionesPorTipo($tipo['id'], $idioma);
										?>
										<li>
											<a href="#<?= htmlspecialchars($tipo['clave']) ?>">
												<?= htmlspecialchars($tipo['titulo']) ?>
											</a>
											<?php if (!empty($formaciones)): ?>
											<ul>
												<?php foreach ($formaciones as $f): ?>
													<li>
														<a href="detalle.php?slug=<?= urlencode($f['slug']) ?>" target="_blank">
															<?= htmlspecialchars($f['titulo']) ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
											<?php endif; ?>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
								<li><a href="#" onclick="manejarContacto(event); return false;">CONTACTO</a></li>
								<li><a href="#" onclick="abrirModalNosotros(); return false;">NOSOTROS</a></li>
								<?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Administrador') : ?>
									<li><a href="admin/usuarios.php">Panel Usuarios</a></li>
									<li><a href="admin/cursos.php">Panel Cursos</a></li>
									<li><a href="admin/panel_crud.php" class="admin-crud-link">
										<i class="fas fa-cogs"></i> Panel CRUD
									</a></li>
								<?php endif; ?>
							</ul>
						</nav>
				</div>

			<!-- Features -->
				<div class="wrapper style1">
					<section id="features" class="container special">
						<header>
							<h2>
								Conocé nuestra oferta educativa
							</h2>
							<p>
								Seleccioná la formación que más te interese
							</p>

						</header>
						<div class="row">
							<?php foreach ($tipos as $tipo): ?>
								<article class="col-4 col-12-mobile special">
									<a href="#<?= htmlspecialchars($tipo['clave']) ?>" class="image featured">
										<?php
                                            $img_src = ($tipo['clave'] === 'cursos')
                                                ? 'images/tipo_formacion/cursos_cabeza.png'
                                                : 'images/tipo_formacion/' . htmlspecialchars($tipo['clave']) . '.png';
                                        ?>
										<img id='img_features' src="<?= $img_src ?>" alt="<?= htmlspecialchars($tipo['clave']) ?>" />
									</a>
									<header>
										<h3><a href="#<?= htmlspecialchars($tipo['clave']) ?>"><strong><?= strtoupper($tipo['titulo']) ?></strong></a></h3>
									</header>
									<p><?= $tipo['descripcion_html'] ?? ''; ?></p>
									<p><?= $tipo['pie_html'] ?? ''; ?></p>
									
								</article>
							<?php endforeach; ?>
						</div>
					</section>
				</div>


			<!-- Formacion -->
			<?php foreach ($tipos as $tipo): 
				$formaciones = $Modelo->obtenerFormacionesPorTipo($tipo['id'], $idioma);
				if (empty($formaciones)) continue;
			?>
			<section id="<?= htmlspecialchars($tipo['clave']) ?>" class="formacion-section wrapper style3" data-tipo="<?= htmlspecialchars($tipo['clave']) ?>">
				<div class="formacion-header">
					<img src="images/cabecera/<?= htmlspecialchars($tipo['imagen_cabecera']) ?>" alt="<?= htmlspecialchars($tipo['titulo']) ?>">
					<h2><?= htmlspecialchars($tipo['titulo']) ?></h2>
					
					<p class="descripcion-cursos">
                    Descubrí nuestras opciones formativas intensivas y certificadas para transformar tu vocación en una profesión.
                </p>
				</div>

				<section class="carousel<?= strtolower($tipo['clave']) === 'cursos' ? ' cursos' : '' ?>">
					<div class="reel">
						<?php foreach ($formaciones as $f): ?>
								<?php 
									// TODOS los cursos tienen estado 'A' según tu confirmación
									// Por seguridad, no aplicamos filtro de estado aquí
									
									if (empty($f['imagen'])) {
										$f['imagen'] = 'default.png'; // Imagen por defecto si no hay imagen específica
									}	
									
									// Usar el texto del botón de la base de datos, o "Ver más" por defecto
									$botonTexto = !empty($f['boton']) ? $f['boton'] : 'Ver más';
									$botonClase = 'button-vermas';
									$botonHref = "detalle.php?slug=" . urlencode($f['slug']);
									$botonTarget = '_blank';
									$botonDisabled = '';
									$esProximamente = false;
									
									// Detectar "próximamente" en cualquier variante usando regex
									if (preg_match('/pr[óoÓO]ximamente/iu', $botonTexto)) {
										$botonClase = 'button-proximamente';
										$botonHref = '#';
										$botonTarget = '';
										$botonDisabled = 'style="pointer-events:none;opacity:0.8;"';
										$esProximamente = true;
									}
								?>
							<article>
								<a href="detalle.php?slug=<?= urlencode($f['slug']) ?>" target="_blank" class="image">
									<img src="images/formaciones/<?= htmlspecialchars($f['imagen']) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($f['titulo']) ?>">
								</a>
								<header><h3><?= htmlspecialchars($f['titulo']) ?></h3></header>
								<p><?= htmlspecialchars($f['descripcion']) ?></p>
								<a href="<?= $botonHref ?>" class="button <?= $botonClase ?> button-cta" <?= $botonTarget ? 'target="_blank"' : '' ?>><?= $botonTexto ?></a>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			</section>
			<?php endforeach; ?>

			<!-- 🎯 FORMULARIO DE CONSULTA PROFESIONAL -->
			<section id="formulario-consulta" class="wrapper style2 special consultation-section">
				<div class="container">
					<header class="major consultation-header">
						<h2 class="consultation-title">¿Te interesa alguna de nuestras formaciones?</h2>
						<p class="consultation-description">
							Consultanos y te asesoramos sin compromiso. Completá el formulario y nos pondremos en contacto contigo.
						</p>
					</header>

					<div class="row">
						<div class="col-8 col-12-medium form-column-center">
							<form id="formConsulta" class="consultation-form">
								<div class="row gtr-uniform gtr-50">
									<!-- Nombre -->
									<div class="col-6 col-12-xsmall">
										<input type="text" name="nombre" id="nombre" placeholder="Nombre completo *" required />
									</div>
									
									<!-- Teléfono -->
									<div class="col-6 col-12-xsmall">
										<input type="tel" name="telefono" id="telefono" placeholder="Teléfono *" required />
									</div>
									
									<!-- Formación de interés -->
									<div class="col-12">
										<input type="text" name="formacion" id="formacion" placeholder="¿Qué formación te interesa? *" required />
									</div>
									
									<!-- Mensaje -->
									<div class="col-12">
										<textarea name="mensaje" id="mensaje" placeholder="Mensaje adicional (opcional)" rows="4"></textarea>
									</div>
									
									<!-- Botón de envío -->
									<div class="col-12" class="submit-row center-align">
										<button type="submit" class="button primary large whatsapp-submit">
											<img src="images/iconos/wat.png" alt="WhatsApp" class="whatsapp-icon">
											Enviar por WhatsApp
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</section>

			<!-- Estilos y JavaScript para el formulario -->
			<style>
				/* Efectos hover para inputs */
				#formConsulta input:focus, #formConsulta select:focus, #formConsulta textarea:focus {
					border-color: #25d366 !important;
					outline: none;
					box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
				}
				
				/* Efecto hover del botón */
				#formConsulta button:hover {
					transform: translateY(-2px);
					box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
					background: linear-gradient(45deg, #128c7e, #25d366);
				}
				
				/* Responsive */
				@media screen and (max-width: 736px) {
					#formulario-consulta {
						padding: 2rem 0 !important;
					}
					#formConsulta {
						padding: 1.5rem !important;
					}
				}
			</style>

			<script>
				document.getElementById('formConsulta').addEventListener('submit', function(e) {
					e.preventDefault();
					
					// Obtener datos del formulario
					const nombre = document.getElementById('nombre').value.trim();
					const telefono = document.getElementById('telefono').value.trim();
					const formacion = document.getElementById('formacion').value;
					const mensaje = document.getElementById('mensaje').value.trim();
					
					// Validar campos requeridos
					if (!nombre || !telefono || !formacion) {
						alert('Por favor, completa todos los campos obligatorios (*)');
						return;
					}
					
					// Crear mensaje estructurado para WhatsApp
					let mensajeWhatsApp = '🎓 *NUEVA CONSULTA DE FORMACIÓN*\n\n';
					mensajeWhatsApp += `👤 *Nombre:* ${nombre}\n`;
					mensajeWhatsApp += `📱 *Teléfono:* ${telefono}\n`;
					mensajeWhatsApp += `🎯 *Formación de interés:* ${formacion}\n`;
					if (mensaje) mensajeWhatsApp += `�� *Mensaje:* ${mensaje}\n`;
					mensajeWhatsApp += '\n📍 *Enviado desde:* dayloplas.com';
					
					// Abrir WhatsApp
					const numeroWhatsApp = '5492613433032';
					const urlWhatsApp = `https://api.whatsapp.com/send?phone=${numeroWhatsApp}&text=${encodeURIComponent(mensajeWhatsApp)}`;
					
					window.open(urlWhatsApp, '_blank');
					
					// Opcional: Limpiar formulario después de enviar
					this.reset();
					alert('✅ Formulario enviado. Se abrirá WhatsApp para enviar tu consulta.');
				});
			</script>

			<!-- Sección de Contacto personalizada - OCULTA por defecto -->
			<section id="contacto" class="contact-section">
				<div class="container">
					<h2 class="contact-title">Contacto</h2>
					<a href="https://api.whatsapp.com/send?phone=5492613433032" target="_blank" class="contact-whatsapp-link">
						<img src="images/iconos/wat.png" alt="WhatsApp" class="whatsapp-icon-large">
						<span class="contact-whatsapp-text">Contactar por WhatsApp</span>
					</a>
					<!-- Dirección label removed as requested -->
					   <div class="contact-address">
						   Rioja 867, Ciudad de Mendoza, Argentina
					   </div>
					<div class="contact-map-wrapper">
						<iframe src="https://www.google.com/maps?q=Rioja+867,+Ciudad+de+Mendoza,+Argentina&output=embed" width="100%" height="260" class="contact-map" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
					<span class="contact-phone-badge">Teléfono: +54 9 2613 43-3032</span>
					
					<!-- Botón para cerrar -->
					<div class="contact-close">
						<a href="#" onclick="ocultarContacto(); return false;" class="contact-close-link">
							✕ Cerrar
						</a>
					</div>
				</div>
			</section>

			<!-- Sección de contacto eliminada. Ahora solo está en contacto.php -->

			<!-- Footer -->
				<div id="footer">
					<div class="container">
                    
						<div class="row">
							<div class="col-12">

                                
								<?php
									include('footer.php');
								?>

							</div>

						</div>
					</div>
				</div>

		</div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/mobile.js"></script>
			
			<!-- CAROUSEL SOLO CON SCROLL - VERSIÓN SIMPLIFICADA -->
			<script>
				$(document).ready(function() {
					// Esperar a que main.js se haya cargado completamente
					setTimeout(function() {
						$('.carousel').each(function() {
							var $carousel = $(this);
							var $reel = $carousel.find('.reel');
							var $articles = $reel.find('article');
							
							// Forzar el ancho correcto del reel
							var totalWidth = 0;
							$articles.each(function() {
								totalWidth += $(this).outerWidth(true);
							});
							
							$reel.css('width', totalWidth + 'px');
							
							// 🎯 CONFIGURAR SOLO SCROLL NORMAL
							$reel.css({
								'overflow-x': 'auto',
								'cursor': 'default',
								'scroll-behavior': 'smooth'
							});
							
							console.log('�� Carousel ' + $carousel.attr('class') + ' inicializado solo con scroll');
						});
					}, 500);
				});
			</script>

			<!-- CSS y JS para modales -->
			<link rel="stylesheet" href="assets/css/modales.css" />
			<script src="assets/js/modales.js"></script>
			<!-- Incluir modal de Nosotros -->
			<?php include('modal_nosotros.php'); ?>

	</body>
</html>
