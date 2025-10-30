<!-- Contact - Sección de contacto del footer -->
    <section class="contact">
        <!-- Encabezado de la sección de contacto -->
        <header>
            <h2>¿QUERÉS SABER MÁS?</h2>
            <p>Contactanos y te contamos todo lonque necesitas saber.</p>
        </header>
        <!-- Botón de WhatsApp con enlace directo y animación -->
    <a href="https://api.whatsapp.com/send?phone=5492613433032" class="botton" target="_blank">
                <img src="images/iconos/wat.png" alt="WhatsApp" class="whatsapp-animado">
        </a>
        <br>
        <br>
        <!-- Estilos CSS para la animación del icono de WhatsApp -->
        <style>
        /* Animación de rebote para el icono de WhatsApp */
        @keyframes whatsapp-bounce {
            0%, 100% { transform: translateY(0); }      /* Posición normal */
            50% { transform: translateY(-3px); }        /* Sube solo 3px, mucho más sutil */
        }
        /* Aplica la animación al icono de WhatsApp - MÁS SUAVE */
        .whatsapp-animado {
            animation: whatsapp-bounce 3s ease-in-out infinite;  /* 3 segundos, más lento y suave */
            transition: filter 0.2s;                   /* Transición suave para efectos hover */
        }
        /* Efecto hover: brillo y sombra verde cuando se pasa el mouse */
        .whatsapp-animado:hover {
            filter: brightness(1.2) drop-shadow(0 0 8px #25d366);
            animation-play-state: paused; /* Pausa la animación cuando haces hover */
        }
        </style>
        <!-- Título para la sección de redes sociales -->
        <h3>Seguinos en nuestras Redes y Enterate de Todas las Novedades .</h3>
        <br>
        <!-- Lista de iconos de redes sociales -->
        <ul class="icons">
            <li>
                <!-- Enlace a Facebook con icono personalizado -->
                <a href="https://www.facebook.com/dayloplasmendoza" target="_blank" class="icono-red-social">
                    <img src="images/iconos/face.png" alt="Facebook" class="img-red-social">
                </a>
            </li>
            <li>
                <!-- Enlace a Instagram con icono personalizado -->
                <a href="https://www.instagram.com/dayloplasmendoza" target="_blank" class="icono-red-social">
                    <img src="images/iconos/insta.png" alt="Instagram" class="img-red-social">
                </a>
            </li>
        <!-- Estilos CSS para los iconos de redes sociales -->
        <style>
        /* Estilo base para las imágenes de redes sociales */
        .img-red-social {
            height: 38px;                              /* Altura fija */
            width: 38px;                               /* Ancho fijo */
            transition: transform 0.25s, filter 0.2s; /* Transiciones suaves */
            vertical-align: middle;                    /* Alineación vertical */
        }
        /* Efectos hover: escala, rotación y sombra azul */
        .icono-red-social:hover .img-red-social {
            transform: scale(1.22) rotate(-8deg);      /* Agranda y rota ligeramente */
            filter: drop-shadow(0 0 8px #4e73df);     /* Sombra azul brillante */
        }
        </style>
        </ul>
    </section>

<!-- Copyright - Sección de derechos de autor y elementos adicionales -->
    <div class="copyright">
        <!-- Información de copyright -->
        <!-- El texto de copyright se movió debajo de los logos -->
        <ul class="menu"></ul>
        <!-- Botón secreto de login para administradores (icono de tuerca) -->
        <a href="admin/auth/login.php" class="btn-login-footer icono-tuerca" title="Iniciar sesión">
            <img src="images/iconos/tuerca.png" alt="Iniciar sesión">
        </a>
    <!-- Botón para ir al formulario de consulta -->
    <button id="btn-consultar-ahora">Consultar ahora</button>
    
    <!-- Botón para volver al inicio de la página con scroll suave -->
    <button id="btn-volver-inicio">Volver a inicio</button>
    
    <!-- Contenedor para botones móviles -->
    <div class="mobile-buttons-container">
        <button id="btn-consultar-ahora-mobile">Consultar ahora</button>
        
        <button id="btn-volver-inicio-mobile">Volver a inicio</button>
    </div>
        <!-- JavaScript para el funcionamiento de los botones -->
        <script>
        // Función que hace scroll suave hacia arriba cuando se hace clic en "Volver a inicio"
        function volverAlInicio() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Función que hace scroll suave al formulario cuando se hace clic en "Consultar ahora"
        function consultarAhora() {
            // Primero verificar si estamos en la página principal
            const formulario = document.getElementById('formulario-consulta');
            
            if (formulario) {
                // Si el formulario está en la página actual, hacer scroll
                formulario.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'center'
                });
                
                // Efecto de resaltado
                setTimeout(() => {
                    formulario.style.transform = 'scale(1.02)';
                    formulario.style.transition = 'transform 0.3s ease';
                    setTimeout(() => {
                        formulario.style.transform = 'scale(1)';
                    }, 500);
                }, 500);
            } else {
                // Si no está en la página actual, redirigir a index.php con anchor
                window.location.href = 'index.php#formulario-consulta';
            }
        }
        
        // Asignar eventos a botones desktop
        document.getElementById('btn-volver-inicio').onclick = volverAlInicio;
        document.getElementById('btn-consultar-ahora').onclick = consultarAhora;
        
        // Asignar eventos a botones móviles
        document.getElementById('btn-volver-inicio-mobile').onclick = volverAlInicio;
        document.getElementById('btn-consultar-ahora-mobile').onclick = consultarAhora;
        </script>
        <!-- Estilos CSS para los elementos del footer -->
        <style>
        /* Estilo para el botón de login (icono de tuerca) */
        .btn-login-footer.icono-tuerca {
            position: absolute;                         /* Posicionamiento absoluto */
            left: 24px;                                /* 24px desde la izquierda */
            bottom: 18px;                              /* 18px desde abajo */
            background: transparent;                    /* Fondo transparente */
            padding: 0;                                /* Sin relleno */
            border-radius: 50%;                        /* Forma circular */
            box-shadow: none;                          /* Sin sombra */
            width: 32px;                               /* Ancho fijo */
            height: 32px;                              /* Alto fijo */
            display: flex;                             /* Flexbox para centrar */
            align-items: center;                       /* Centra verticalmente */
            justify-content: center;                   /* Centra horizontalmente */
            z-index: 10;                               /* Por encima de otros elementos */
        }
        /* Estilo para la imagen del icono de tuerca */
        .btn-login-footer.icono-tuerca img {
            height: 28px;                              /* Altura de la imagen */
            width: 28px;                               /* Ancho de la imagen */
            opacity: 0.7;                              /* Transparencia */
            transition: opacity 0.2s, transform 0.2s;  /* Transiciones suaves */
        }
        /* Efectos hover: más opaco, rotación y escala */
        .btn-login-footer.icono-tuerca:hover img {
            opacity: 1;                                /* Completamente opaco */
            transform: rotate(30deg) scale(1.12);      /* Rota 30° y agranda 12% */
        }
        
        /* Estilos para el botón "Consultar ahora" */
        #btn-consultar-ahora {
            animation: pulso-consultar 3s infinite;
        }
        
        #btn-consultar-ahora:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
            animation: none; /* Pausa la animación durante hover */
        }
        
        /* Animación llamativa para el botón "Consultar ahora" */
        @keyframes pulso-consultar {
            0% { 
                background: linear-gradient(90deg, #2d3e50 60%, #4e73df 100%);
                transform: scale(1);
            }
            25% { 
                background: linear-gradient(90deg, #ff6b35 60%, #f39c12 100%);
                transform: scale(1.05);
            }
            50% { 
                background: linear-gradient(90deg, #2d3e50 60%, #4e73df 100%);
                transform: scale(1);
            }
            75% { 
                background: linear-gradient(90deg, #28a745 60%, #20c997 100%);
                transform: scale(1.05);
            }
            100% { 
                background: linear-gradient(90deg, #2d3e50 60%, #4e73df 100%);
                transform: scale(1);
            }
        }
        
        /* Estilos para el botón "Volver a inicio" */
        #btn-volver-inicio:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }
        
        /* Estilos responsive para botones móviles */
        @media screen and (max-width: 768px) {
            #btn-consultar-ahora,
            #btn-volver-inicio {
                display: none !important;
            }
            
            .mobile-buttons-container {
                display: block !important;
                text-align: center !important;
                margin: 20px 0 !important;
                padding: 0 20px !important;
            }
            
            #btn-consultar-ahora-mobile,
            #btn-volver-inicio-mobile {
                margin: 10px auto !important;
                display: block !important;
                width: 90% !important;
                max-width: 280px !important;
            }
            
            #btn-consultar-ahora-mobile:hover,
            #btn-volver-inicio-mobile:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
            }
        }
    /* Comentario: .volver-inicio-btn eliminado para restablecer el estilo original */
        </style>
    </div>

    <!-- Indicador de clic y logos Mirabai y Libra en el footer -->
    <div class="pdf-logos-section">
        <div class="pdf-logos-instruction">
            Haz clic en los logos para ver o descargar los catálogos en PDF
        </div>
        <div class="logo-pdf-container logo-pdf-left">
            <span class="logo-pdf-footer">
                <img src="images/logos/mirabai.png" alt="Logo Mirabai" class="logo-mirabai" />
            </span>
            <div class="pdf-links">
                <a href="images/PDF/Mirabai 2023.pdf" target="_blank" class="pdf-link" style="margin-right:18px;">Ver PDF</a>
                <a href="images/PDF/Mirabai 2023.pdf" download class="pdf-link">Descargar</a>
            </div>
        </div>
        <div class="logo-pdf-container logo-pdf-right">
            <span class="logo-pdf-footer">
                <img src="images/logos/libra.png" alt="Logo Libra" class="logo-libra" />
            </span>
            <div class="pdf-links">
                <a href="images/PDF/LIBRA 2025.pdf" target="_blank" class="pdf-link" style="margin-right:18px;">Ver PDF</a>
                <a href="images/PDF/LIBRA 2025.pdf" download class="pdf-link">Descargar</a>
            </div>
        </div>
        <style>
        .logo-pdf-footer img {
            transition: transform 0.22s, box-shadow 0.22s;
            cursor: pointer;
        }
        .logo-pdf-footer:hover img {
            transform: scale(1.08) rotate(-3deg);
            box-shadow: 0 0 16px #f44336, 0 2px 12px rgba(44,62,80,0.13);
        }
        </style>
    </div>
    <style>
    .logo-pdf-footer img {
        transition: transform 0.22s, box-shadow 0.22s;
        cursor: pointer;
    }
    .logo-pdf-footer:hover img {
        transform: scale(1.08) rotate(-3deg);
        box-shadow: 0 0 16px #f44336, 0 2px 12px rgba(44,62,80,0.13);
    }
    </style>

    <!-- Copyright y nombre bien al pie del footer -->
    <footer class="site-footer-bottom">
        <div style="margin-bottom: 2px;">&copy; E.N.A.Desarrollo de sitios web. Todos los derechos reservados</div>
        <div>"Dayloplas-I.P.M. Mendoza"</div>
    </footer>
