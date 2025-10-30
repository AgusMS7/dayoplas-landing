<?php
/* 
 * ARCHIVO: detalle.php
 * PROPÓSITO: Muestra la información detallada de una formación específica basada en el slug recibido por GET.
 * Maneja múltiples idiomas y presenta información completa sobre cursos, talleres u otras formaciones
 */

// Este código está comentado pero parece ser para manejar cambios de idioma
// session_start();
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idioma'])) {
//     $_SESSION['idioma'] = $_POST['idioma'];
//     // Redirigimos para evitar resubmit en F5
//     header("Location: " . $_SERVER['PHP_SELF'] . '?slug='.$_POST['slug']);
//     // exit;
// }
?>
<!DOCTYPE HTML>
<html>
	<?php
		/* 
		 * INICIALIZACIÓN Y CARGA DE DEPENDENCIAS
		 * Se incluyen los archivos necesarios y se verifican parámetros esenciales
		 */
		// Cargar el modelo de datos y configuración de la conexión a la base de datos
		require_once 'model_daylo.php';
		require_once 'conexion.php';

		// Obtener el slug (identificador único de la formación) desde la URL
		$slug = $_GET['slug'] ?? null;	
		// Establecer el idioma actual desde la sesión, por defecto español
		$idioma = $_SESSION['idioma'] ?? 'es';		

		// Validar que se haya recibido un slug, de lo contrario redirigir al inicio
		if (!$slug) {
			// Redirigir al index principal si no llega slug
			header('Location: index.php');
			exit;
		}

	

        /* 
         * CONSULTA A BASE DE DATOS
         * Se obtiene la información detallada de la formación y tipos de formación 
         */
        // Crear instancia del modelo para acceder a la base de datos
        $formacion = new ModelDaylo($host, $db, $user, $pass, $port);
        // Obtener detalles de la formación según el slug e idioma
        $datos = $formacion->obtenerDetalleFormacion($slug, $idioma);
        if (!$datos) {
            echo "No hemos encontrado esta capacitación.";
           // header("Location: index.php");
            exit();
        }
        // Obtener todos los tipos de formación para el menú de navegación
        $tipos = $formacion->obtenerTiposFormacion($idioma);
        // Obtener el tipo específico de esta formación
        $tipo =  $formacion->obtenerTipoFormacionPorIdYIdioma($datos['tipo_formacion_id'], $idioma);

		// $relacionadas = $formacion->obtenerFormacionesPorTipo($datos['tipo_formacion_id'], $idioma);

	?>
	<head>
		<title>DAYLOPLAS-IPM</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<!-- HOJAS DE ESTILO PRINCIPALES -->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!-- <link rel="stylesheet" href="assets/css/colores.css" /> -->
		<!-- <link rel="stylesheet" href="assets/css/banderas.css" /> -->
		<link rel="stylesheet" href="assets/css/footer.css" />
		<!-- <link rel="stylesheet" href="assets/css/cta_contactos.css" /> -->
        <!-- <link rel="stylesheet" href="assets/css/naty.css" /> -->

		<!-- Hoja de estilo alternativa para navegadores sin JavaScript -->
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet"> -->
		<style>
			/* ESTILOS PERSONALIZADOS PARA LA PÁGINA DE DETALLE */
	
			/* Ajustes para alineación y distribución de elementos en filas */
			.row.gtr-50.align-items-stretch {
				display: flex;
				align-items: stretch;
			}

			/* Configuración de columnas para centrar contenido verticalmente */
			.col-4, .col-8 {
				display: flex;
				flex-direction: column;
				justify-content: center;
			}

			
			.row.gtr-50.align-items-center {
				align-items: stretch;
			}

			/* Estilos para imágenes en la barra lateral */
			.img-sidebar a img {
				width: 100%;
				height: 150px;
				object-fit: cover;
				border-radius: 4px;
			}
			/* Alineación de texto a la izquierda para ciertos elementos */
			.izquierda,
			.izquierda h4,
			.izquierda p, {
				text-align: left !important;
			}
			/* Estilo para separadores simples */
			.separador2 {
				height: 1px;
				background-color: #ccc; /* Podés ajustar el color */
				margin: 1rem 0; /* Espaciado arriba y abajo */
				width: 100%; /* o 90% si querés margen lateral */
			}

			/* Estilo para separadores con iconos en el centro */
			.separador-con-icono {
				text-align: center;
				margin: .6rem 0;
			}
			.separador-con-icono span {
				display: inline-block;
				padding: 0 1rem;
				position: relative;
				color: #aaa;
			}
			.separador-con-icono span::before,
			.separador-con-icono span::after {
				content: "";
				position: absolute;
				top: 50%;
				width: 120px;
				height: 1px;
				background: #ccc;
			}
			.separador-con-icono span::before {
				left: -130px;
			}
			.separador-con-icono span::after {
				right: -130px;
			}

			/* Estilos para elementos de navegación */
			#nav ul li a {
				color: var(--color-text-light) !important;
                
			}
			.bandera-btn span.detalle{
				color: var(--color-text-dark) !important;			
			}

			/* Ajuste de distribución del contenido principal */
			#main {
				display: flex !important;
				flex-direction: column !important;
				justify-content: flex-start !important;
			}

			/* 🎨 HEADER CON GRADIENTES ELEGANTES DINÁMICOS */
			#header {
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
				background-attachment: fixed;
				transition: background 1.5s ease-in-out;
				position: relative;
				min-height: 60vh;
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
				text-align: center;
			}

			/* Estilos optimizados para texto sobre gradientes */
			#header h1, #header h1 a, #header nav a, #header .inner header h1, #header .inner header h1 a {
				color: #000000 !important;
				text-shadow: 2px 2px 4px rgba(255,255,255,0.8) !important;
				font-weight: bold !important;
			}
			
			#header nav ul li a {
				color: #181818 !important;
				text-shadow: 1px 1px 3px rgba(255,255,255,0.7) !important;
				font-weight: bold !important;
				transition: color 0.2s, font-size 0.2s;
			}
			
			#header nav ul li a:hover {
				color: #1a4fff !important;
				font-size: 1.13em;
				background-color: rgba(255,255,255,0.3) !important;
				border-radius: 5px;
				padding: 5px 10px;
			}
	
		</style>
		
		<script>
			// 🎨 Gradientes elegantes dinámicos para detalle - DEFINITIVO
			document.addEventListener('DOMContentLoaded', function () {
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
					if (header) {
						header.style.background = gradientes[index];
						header.style.backgroundAttachment = 'fixed';
						header.style.transition = 'background 1.5s ease-in-out';
						index = (index + 1) % gradientes.length;
					}
				}

				cambiarFondo(); // aplicar el primer gradiente
				setInterval(cambiarFondo, 6000); // cambiar cada 6 segundos
			});
		</script>

	</head>
	<body class="right-sidebar is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			 	<?php 
				/* 
				 * CONFIGURACIÓN DE IMAGEN DE CABECERA
				 * Se define la imagen de fondo para el encabezado según los datos de la formación
				 */
				if (!empty($datos['imagen_cabecera'])) {
					$imagen = htmlspecialchars($datos['imagen_cabecera']);
					
				} else {
					$imagen = 'cursos_cabecera.png';
				}
			
				 ?>
				 
				<div id="header">

					<!-- Inner -->
						<div class="inner">
							<header>
								 <h1><a href="index.php" id="logo"><?= htmlspecialchars($tipo['titulo']) ?></a></h1>
							</header>
						</div>

					<!-- Nav -->
					<nav id="nav">
						<ul>
							<!-- Enlace a la página principal -->
							<li><a class="detalle" href="index.php">INICIO</a></li>

							<?php 
							/* 
							 * GENERACIÓN DEL MENÚ DE NAVEGACIÓN DINÁMICO
							 * Muestra los tipos de formación y sus respectivas formaciones
							 */
							foreach ($tipos as $tipo): 
								// Obtener formaciones por tipo e idioma
								$formaciones = $formacion->obtenerFormacionesPorTipo($tipo['id'], $idioma);
							?>
								<li>
									<a href="#" onclick="manejarEnlaceMenu(event, '<?= htmlspecialchars($tipo['clave']) ?>'); return false;">
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
							<?php endforeach; ?>

							<!-- Enlace a la página Nosotros con texto según idioma -->
							<li><a href="#" onclick="abrirModalNosotros(); return false;"><?= $idioma === 'en' ? 'ABOUT US' : ($idioma === 'pt' ? 'SOBRE NÓS' : 'NOSOTROS') ?></a></li>
							<li><a href="#" onclick="manejarContacto(event); return false;"><?= $idioma === 'en' ? 'CONTACT' : ($idioma === 'pt' ? 'CONTATO' : 'CONTACTO') ?></a></li>
						</ul>
					</nav>

				</div>

			<!-- Main -->
				<div class="wrapper style1">
					<div class="container">
						<div class="row gtr-200">
							<!-- Contenido principal -->
							<div class="col-8 col-12-mobile" id="content" >
								<section id="main" > 
                                    <!-- style="margin-top: 10px !important;" -->
									<header>
										<!-- Título y descripción de la formación -->
										<h2><?= htmlspecialchars($datos['titulo']) ?></h2>
										<p><?= htmlspecialchars($datos['descripcion']) ?></p>
									</header>
									<?php 
									/* 
									 * CONFIGURACIÓN DE IMAGEN PRINCIPAL
									 * Muestra la imagen de la formación o una por defecto si no existe
									 */
									if (!empty($datos['imagen'])) {
										$imagen_formacion = htmlspecialchars($datos['imagen']);
										
									} else {
										$imagen_formacion = 'pic01.jpg';
									}						
									
									?>
									<a href="#" class="image featured">
											<img src="images/formaciones/<?= $imagen_formacion ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($datos['titulo']) ?>" />
										</a>
									<ul>
										<!-- Información detallada de la formación -->
										Fecha de inicio:</strong> <?= date("d/m/Y", strtotime($datos['fecha_inicio'])) ?></li>
										<li><strong>Duración:</strong> <?= $datos['duracion'] ?></li>
										<li><strong>Horarios:</strong> <?= $datos['horarios'] ?></li>
										<li><strong>Días de cursado:</strong> <?= $datos['dias_cursado'] ?></li>
										<li><strong>Carga horaria:</strong> <?= $datos['carga_horaria'] ?></li>
										<?php if ($datos['destacado']): ?>
											<li><strong><?= $datos['destacado'] ?></strong></li>
										<?php endif; ?>
									</ul>
									<?php
									/* 
									 * VERIFICACIÓN Y ENLACE A RECURSOS PDF
									 * Muestra botones para ver o descargar PDF si existe
									 * Busca en múltiples carpetas según el tipo de formación
									 */
									$pdf_found = false;
									$pdf_path = '';
									
									if (!empty($datos['recurso_pdf'])) {
										// Array de posibles ubicaciones
										$posibles_rutas = [
											'images/PDF/' . $datos['recurso_pdf'],
											'images/PDF/descripcionCursos.PDF/' . $datos['recurso_pdf'],
											'images/PDF/descrpcionTalleres.PDF/' . $datos['recurso_pdf'],
											'images/PDF/descripcionJornada.PDF/' . $datos['recurso_pdf']
										];
										
										// Buscar el PDF en las posibles ubicaciones
										foreach ($posibles_rutas as $ruta) {
											if (file_exists($ruta)) {
												$pdf_path = $ruta;
												$pdf_found = true;
												break;
											}
										}
									}
									
									// Si no se encontró por recurso_pdf, intentar por slug
									if (!$pdf_found && !empty($datos['slug'])) {
										$posibles_rutas_slug = [
											'images/PDF/' . $datos['slug'] . '.pdf',
											'images/PDF/descripcionCursos.PDF/' . $datos['slug'] . '.pdf',
											'images/PDF/descrpcionTalleres.PDF/' . $datos['slug'] . '.pdf',
											'images/PDF/descripcionJornada.PDF/' . $datos['slug'] . '.pdf'
										];
										
										foreach ($posibles_rutas_slug as $ruta) {
											if (file_exists($ruta)) {
												$pdf_path = $ruta;
												$pdf_found = true;
												break;
											}
										}
									}

									if ($pdf_found):
									?>
										<div style="margin:1em 0;display:flex;gap:1em;flex-wrap:wrap;">
											<a href="<?= $pdf_path ?>" target="_blank" class="button icon solid fa-eye" style="background:#1a4fff;color:#fff;padding:0.8em 1.5em;text-decoration:none;border-radius:5px;">
												📄 Ver PDF
											</a>
											<a href="<?= $pdf_path ?>" download class="button icon solid fa-download" style="background:#28a745;color:#fff;padding:0.8em 1.5em;text-decoration:none;border-radius:5px;">
												Descargar PDF
											</a>
										</div>
									<?php else: ?>
										<div class="info-upcoming-note">
											📋 Información detallada disponible próximamente.
										</div>
									<?php endif; ?>
									</section>
							</div>
							<div class="col-4 col-12-mobile" id="sidebar">
								<hr class="first" />
								



								<hr />
						
							</div>
						</div>
						

				</div>

				<!-- CTA -->
		

			<!-- Footer -->
			<div id="footer" class="page-detail-footer-offset">
                <div class="container">
                
                    <div class="row">
                        <div class="col-12">

                            <!-- Inclusión del pie de página -->
                            <?php
                                include('footer.php');
                            ?>

                        </div>

                    </div>
                </div>
            </div>

		</div>

		<!-- Scripts -->
			<!-- Inclusión de bibliotecas JavaScript para funcionalidad del sitio -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
			
			<!-- CSS y JS para modales -->
			<link rel="stylesheet" href="assets/css/modales.css" />
			<script src="assets/js/modales.js"></script>

			<!-- Incluir modal de Nosotros -->
			<?php include('modal_nosotros.php'); ?>

	</body>
</html>
