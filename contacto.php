<?php
/*
 * ARCHIVO: contacto.php
 * PROPÓSITO: Página dedicada de contacto para DAYLOPLAS-IPM-MZA
 * DESCRIPCIÓN: Proporciona información de contacto completa incluyendo:
 * - Enlace directo a WhatsApp
 * - Dirección física de la institución
 * - Mapa integrado de Google Maps
 * - Número de teléfono
 * 
 * Esta página es esencial para la comunicación con potenciales estudiantes
 * y facilita múltiples formas de contacto para maximizar las conversiones.
 */

// Cargar archivo de configuración con parámetros del sistema
require_once 'conexion.php';
?>
<!DOCTYPE HTML>
<html>
<head>
    <!-- Metadatos y configuración de la página -->
    <title>Contacto | DAYLOPLAS-IPM-MZA</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    
    <!-- Hojas de estilo para el diseño y presentación -->
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/naty.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    
    <!-- 🎨 Estilos elegantes para gradientes dinámicos - DEFINITIVO -->
    <style>
        /* Header con gradientes profesionales */
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
        
        /* Texto optimizado para gradientes */
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
        
        #header nav ul li a:hover, #header nav ul li a.active {
            color: #1a4fff !important;
            font-size: 1.13em;
            background-color: rgba(255,255,255,0.3) !important;
            border-radius: 5px;
            padding: 5px 10px;
        }
        
        #header p {
            color: #111 !important;
            text-shadow: 0 1px 2px #fff !important;
            font-weight: bold !important;
            font-size: 1.18em;
        }
        
        /* Logos con animación */
        .logo {
            width: 150px;
            margin: 0 10px;
            opacity: 0;
            transform: scale(1);
            animation: fadeIn 0.6s ease-out forwards;
        }
        .logo:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .logo:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        /* Logo container centrado */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 1rem auto;
            text-align: center;
        }
        
        /* Responsive */
        @media screen and (max-width: 736px) {
            .logo-container {
                flex-direction: column;
                gap: 10px;
            }
            .logo {
                width: 120px;
            }
        }
    </style>
    
    <script>
        // 🎨 Gradientes elegantes dinámicos para contacto - DEFINITIVO
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
                header.style.background = gradientes[index];
                header.style.backgroundAttachment = 'fixed';
                header.style.transition = 'background 1.5s ease-in-out';
                index = (index + 1) % gradientes.length;
            }

            cambiarFondo(); // aplicar el primer gradiente
            setInterval(cambiarFondo, 6000); // cambiar cada 6 segundos
        });
    </script>
</head>
<body>
    <div id="page-wrapper">
        <!-- Encabezado con logos institucionales y navegación -->
        <div id="header">
            <div class="inner">
                <header>
                    <!-- Contenedor de logos institucionales -->
                    <div class="logo-container">
                        <img class="logo bounce-logo" src="images/logos/logo_dayloplas.png" alt="">
                        <img class="logo bounce-logo" src="images/logos/logo_ipm.png" alt="">
                    </div>
                    <!-- Título principal y descripción institucional -->
                    <h1><a href="index.php" id="logo">Daylo-IPM-Mendoza</a></h1>
                    <hr />
                    <p>"Una institución pensada para quienes desean destacarse en el mundo de la estética, la podología y el bienestar."<br />"Educación de calidad con salida laboral real."</p>
                </header>
            </div>
            <!-- Menú de navegación principal -->
            <nav id="nav">
                <ul>
                    <li><a href="index.php">INICIO</a></li>
                    <li><a href="#" onclick="abrirModalNosotros(); return false;">NOSOTROS</a></li>
                    <li><a href="contacto.php" class="active">CONTACTO</a></li>
                </ul>
            </nav>
        </div>
        <!-- Sección principal de contacto -->
        <section id="contacto" class="contact-section">
            <!-- Título de la sección -->
            <h2 class="contact-title">Contacto</h2>
            
            <!-- Enlace directo a WhatsApp Business -->
            <a href="https://api.whatsapp.com/send?phone=5492613433032" target="_blank" class="contact-whatsapp-link">
                <img src="images/iconos/wat.png" alt="WhatsApp" class="whatsapp-icon-large">
                <span class="contact-whatsapp-text">Contactar por WhatsApp</span>
            </a>
            
            <!-- Dirección física de la institución -->
            <div class="contact-address">
                Rioja 867, Ciudad de Mendoza, Argentina
            </div>
            
            <!-- Mapa integrado de Google Maps -->
            <div class="contact-map-wrapper">
                <iframe src="https://www.google.com/maps?q=Rioja+867,+Ciudad+de+Mendoza,+Argentina&output=embed" width="100%" height="260" class="contact-map" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            
            <!-- Número de teléfono posicionado en esquina superior derecha -->
            <span class="contact-phone-badge">Teléfono: +54 9 2613 43-3032</span>
        </section>
        <!-- Pie de página -->
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
    
    <!-- Scripts JavaScript para funcionalidad del sitio -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- CSS y JS para modales -->
    <link rel="stylesheet" href="assets/css/modales.css" />
    <script src="assets/js/modales.js"></script>

    <!-- Incluir modal de Nosotros -->
    <?php include('modal_nosotros.php'); ?>
</body>
</html>
