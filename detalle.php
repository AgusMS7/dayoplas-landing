<?php
session_start();
require_once 'conexion.php';
require_once 'model_daylo.php';

$idioma = $_SESSION['idioma'] ?? 'es';
$Modelo = new ModelDaylo($host, $db, $user, $pass);

// Obtener parámetros
$slug = $_GET['slug'] ?? null;
$tipo_param = $_GET['tipo'] ?? null;

// Redirigir si no hay slug ni tipo
if (!$slug && !$tipo_param) {
	header('Location: index.php');
	exit;
}

$datos = null;
$tipo = null;
$tipos = $Modelo->obtenerTiposFormacion($idioma);
$formaciones = [];
$modo = null; // 'detalle' o 'listado'

// CASO 1: Se pasó un slug - mostrar detalle individual
if ($slug) {
	$datos = $Modelo->obtenerDetalleFormacion($slug, $idioma);
	if (!$datos) {
		header('Location: index.php');
		exit;
	}
	$tipo = $Modelo->obtenerTipoFormacionPorIdYIdioma($datos['tipo_formacion_id'], $idioma);
	$modo = 'detalle';
}

// CASO 2: Se pasó un tipo - mostrar listado de formaciones de ese tipo
if ($tipo_param) {
	// Buscar el tipo en la lista de tipos
	$tipo_encontrado = null;
	foreach ($tipos as $t) {
		if ($t['clave'] === $tipo_param) {
			$tipo_encontrado = $t;
			break;
		}
	}
	
	if ($tipo_encontrado) {
		$tipo = $tipo_encontrado;
		$formaciones = $Modelo->obtenerFormacionesPorTipo($tipo['id'], $idioma);
		$modo = 'listado';
	} else {
		header('Location: index.php');
		exit;
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?= htmlspecialchars($tipo['titulo'] ?? 'Dayloplas-IPM') ?> - DAYLOPLAS-IPM</title>
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
		<link rel="stylesheet" href="assets/css/gradientes-elegantes.css" />
		<?php if ($modo === 'listado'): ?>
		<link rel="stylesheet" href="assets/css/carousel-center.css" />
		<?php endif; ?>
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>

		<style>
			.formacion-detail-header {
				background: linear-gradient(135deg, rgba(135, 206, 235, 0.85) 0%, rgba(240, 248, 255, 0.95) 50%, rgba(255, 255, 255, 0.98) 100%);
				padding: 2rem 0;
				margin-bottom: 2rem;
			}

			.formacion-detail-header h1 {
				color: #181818;
				text-align: center;
				margin-bottom: 0.5rem;
			}

			.formacion-detail-content {
				display: grid;
				grid-template-columns: 2fr 1fr;
				gap: 2rem;
				margin-bottom: 2rem;
			}

			.formacion-detail-content .imagen-principal {
				width: 100%;
				border-radius: 10px;
				box-shadow: 0 6px 20px rgba(0,0,0,0.15);
				object-fit: cover;
				height: 400px;
			}

			.formacion-detalles-box {
				background: #f8f9fa;
				padding: 1.5rem;
				border-radius: 10px;
				border-left: 4px solid #87CEEB;
			}

			.formacion-detalles-box h3 {
				color: #1a4fff;
				margin-top: 0;
				margin-bottom: 1rem;
				font-size: 1.1rem;
			}

			.detalle-item {
				display: flex;
				align-items: flex-start;
				margin-bottom: 1rem;
				font-size: 0.95rem;
			}

			.detalle-item strong {
				color: #181818;
				min-width: 120px;
			}

			.detalle-item p {
				margin: 0;
				color: #555;
			}

			.boton-pdf {
				display: inline-block;
				background: #1a4fff;
				color: white;
				padding: 0.7rem 1.5rem;
				border-radius: 6px;
				text-decoration: none;
				transition: background 0.2s;
				margin-top: 1rem;
				font-size: 0.9rem;
			}

			.boton-pdf:hover {
				background: #0d2e8b;
			}

			/* Estilos para carousel listado */
			.carousel .reel {
				display: flex;
				flex-wrap: nowrap;
				width: max-content;
				overflow-x: auto;
				overflow-y: hidden;
				-webkit-overflow-scrolling: touch;
				scrollbar-width: none;
				-ms-overflow-style: none;
				position: relative;
			}

			.carousel .reel::-webkit-scrollbar {
				display: none;
			}

			.carousel .reel article {
				flex: 0 0 auto;
				width: 320px;
				margin-right: 2em;
				background: #ffffff;
				border: 1px solid rgba(135, 206, 235, 0.3);
				border-radius: 10px;
				overflow: hidden;
				box-shadow: 0 4px 12px rgba(135, 206, 235, 0.15);
				transition: transform 0.3s ease, box-shadow 0.3s ease;
			}

			.carousel .reel article:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 20px rgba(135, 206, 235, 0.25);
			}

			.carousel .reel article img {
				width: 100%;
				height: 220px;
				object-fit: contain;
				background: #ffffff;
				padding: 15px;
				border-radius: 8px;
				display: block;
			}

			.carousel .reel article header {
				padding: 1rem;
			}

			.carousel .reel article p {
				padding: 0 1rem;
				font-size: 0.9rem;
				color: #555;
			}

			.carousel .reel article .button {
				margin: 0.7rem 1rem 1rem 1rem;
				background-color: #1fa7ff;
				color: #fff;
				border-radius: 40px;
				font-weight: 600;
				font-size: 1em;
				padding: 0.7em 2.2em;
				box-shadow: 0 4px 8px rgba(0,0,0,0.08);
				transition: background-color 0.2s, color 0.2s, transform 0.2s;
				display: inline-block;
				text-decoration: none;
			}

			.carousel .reel article .button:hover {
				transform: scale(1.05);
				box-shadow: 0 6px 12px rgba(0,0,0,0.15);
			}

			.carousel .reel article .button-proximamente {
				background-color: #ff9800;
				cursor: not-allowed;
				opacity: 0.8;
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

				.formacion-detail-content {
					grid-template-columns: 1fr;
				}

				.formacion-detalles-box {
					order: -1;
				}

				.carousel .reel article {
					width: 280px;
					margin-right: 1.5em;
				}
			}
		</style>
	</head>
	<body class="is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<div id="header" class="header-gradientes">
				<div class="inner">
					<header>
						<div class="logo-container">
							<img class="logo bounce-logo" src="images/logos/logo_dayloplas.png" alt="">
							<img class="logo bounce-logo" src="images/logos/logo_ipm.png" alt="">
						</div>
						<h1><a href="index.php" id="logo" class="title-link">Dayloplas-I.P.M. Mendoza</a></h1>
						<hr />
						<p class="header-subtitle"><?= htmlspecialchars($tipo['subtitulo'] ?? '"Una institución pensada para quienes desean destacarse en el mundo de la estética, la podología y el bienestar."') ?></p>
					</header>
				</div>

				<!-- Navegación -->
				<?php include('partials/menu_cursos.php'); ?>
			</div>

			<!-- Contenido: Modo Detalle (Individual) -->
			<?php if ($modo === 'detalle'): ?>
			<div class="wrapper style1">
				<div class="container">
					<div class="formacion-detail-header">
						<h1><?= htmlspecialchars($datos['titulo']) ?></h1>
						<?php if (!empty($datos['descripcion'])): ?>
							<p style="text-align: center; color: #555; margin: 0;"><?= htmlspecialchars($datos['descripcion']) ?></p>
						<?php endif; ?>
					</div>

					<div class="formacion-detail-content">
						<!-- Contenido Principal -->
						<div>
							<?php if (!empty($datos['imagen_cabecera'])): ?>
								<img src="images/cabecera/<?= htmlspecialchars($datos['imagen_cabecera']) ?>" alt="<?= htmlspecialchars($datos['titulo']) ?>" class="imagen-principal">
							<?php elseif (!empty($datos['recurso_imagen'])): ?>
								<img src="images/formaciones/<?= htmlspecialchars($datos['recurso_imagen']) ?>" alt="<?= htmlspecialchars($datos['titulo']) ?>" class="imagen-principal">
							<?php else: ?>
								<img src="images/formaciones/<?= htmlspecialchars($datos['imagen']) ?>" alt="<?= htmlspecialchars($datos['titulo']) ?>" class="imagen-principal">
							<?php endif; ?>
						</div>

						<!-- Detalles Laterales -->
						<div class="formacion-detalles-box">
							<h3>Información</h3>

							<?php if (!empty($datos['carga_horaria'])): ?>
								<div class="detalle-item">
									<strong>Carga Horaria:</strong>
									<p><?= htmlspecialchars($datos['carga_horaria']) ?></p>
								</div>
							<?php endif; ?>

							<?php if (!empty($datos['duracion'])): ?>
								<div class="detalle-item">
									<strong>Duración:</strong>
									<p><?= htmlspecialchars($datos['duracion']) ?></p>
								</div>
							<?php endif; ?>

							<?php if (!empty($datos['dias_cursado'])): ?>
								<div class="detalle-item">
									<strong>Días:</strong>
									<p><?= htmlspecialchars($datos['dias_cursado']) ?></p>
								</div>
							<?php endif; ?>

							<?php if (!empty($datos['horarios'])): ?>
								<div class="detalle-item">
									<strong>Horarios:</strong>
									<p><?= htmlspecialchars($datos['horarios']) ?></p>
								</div>
							<?php endif; ?>

							<?php if (!empty($datos['fecha_inicio'])): ?>
								<div class="detalle-item">
									<strong>Inicio:</strong>
									<p><?= htmlspecialchars($datos['fecha_inicio']) ?></p>
								</div>
							<?php endif; ?>

							<?php if (!empty($datos['recurso_pdf'])): ?>
								<a href="images/PDF/<?= htmlspecialchars($datos['recurso_pdf']) ?>" target="_blank" class="boton-pdf">
									<i class="fas fa-file-pdf"></i> Descargar PDF
								</a>
							<?php endif; ?>
						</div>
					</div>

					<?php if (!empty($tipo['descripcion_larga'])): ?>
						<section style="margin: 2rem 0;">
							<h2><?= htmlspecialchars($tipo['titulo']) ?></h2>
							<div style="line-height: 1.8; color: #555;">
								<?= nl2br(htmlspecialchars($tipo['descripcion_larga'])) ?>
							</div>
						</section>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Contenido: Modo Listado (Por Tipo) -->
			<?php if ($modo === 'listado'): ?>
			<div class="wrapper style3">
				<div class="container">
					<div class="formacion-detail-header">
						<h1><?= htmlspecialchars($tipo['titulo']) ?></h1>
						<?php if (!empty($tipo['descripcion_html'])): ?>
							<p style="text-align: center; color: #555; margin: 0;"><?= htmlspecialchars($tipo['descripcion_html']) ?></p>
						<?php endif; ?>
					</div>

					<?php if (!empty($formaciones)): ?>
					<section class="carousel">
						<div class="reel">
							<?php foreach ($formaciones as $f): ?>
								<?php 
									$botonTexto = !empty($f['boton']) ? $f['boton'] : 'Ver más';
									$botonClase = 'button';
									$botonHref = "detalle.php?slug=" . urlencode($f['slug']);
									
									// Detectar "próximamente"
									if (preg_match('/pr[óoÓO]ximamente/iu', $botonTexto)) {
										$botonClase = 'button button-proximamente';
										$botonHref = '#';
									}
									
									// Imagen por defecto
									if (empty($f['imagen'])) {
										$f['imagen'] = 'default.png';
									}
								?>
								<article>
									<a href="detalle.php?slug=<?= urlencode($f['slug']) ?>" class="image">
										<img src="images/formaciones/<?= htmlspecialchars($f['imagen']) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($f['titulo']) ?>">
									</a>
									<header><h3><?= htmlspecialchars($f['titulo']) ?></h3></header>
									<p><?= htmlspecialchars($f['descripcion'] ?? '') ?></p>
									<a href="<?= $botonHref ?>" class="<?= $botonClase ?>"><?= $botonTexto ?></a>
								</article>
							<?php endforeach; ?>
						</div>
					</section>
					<?php else: ?>
					<p style="text-align: center; color: #666; margin: 2rem 0;">No hay formaciones disponibles en este momento.</p>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Footer -->
			<div id="footer">
				<div class="container">
					<div class="row">
						<div class="col-12">
							<?php include('footer.php'); ?>
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
		<script src="assets/js/gradientes-dinamicos.js"></script>

		<!-- CSS y JS para modales -->
		<link rel="stylesheet" href="assets/css/modales.css" />
		<script src="assets/js/modales.js"></script>
		<!-- Incluir modal de Nosotros -->
		<?php include('modal_nosotros.php'); ?>

		<?php if ($modo === 'listado'): ?>
		<script>
			$(document).ready(function() {
				setTimeout(function() {
					$('.carousel').each(function() {
						var $carousel = $(this);
						var $reel = $carousel.find('.reel');
						var $articles = $reel.find('article');
						
						var totalWidth = 0;
						$articles.each(function() {
							totalWidth += $(this).outerWidth(true);
						});
						
						$reel.css('width', totalWidth + 'px');
						console.log('✅ Carousel inicializado');
					});
				}, 500);
			});
		</script>
		<?php endif; ?>
	</body>
</html>
