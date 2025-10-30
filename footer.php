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
        <!-- Título para la sección de redes sociales -->
        <h3>Seguinos en nuestras Redes y Enterate de Todas las Novedades .</h3>
        <br>
    <!-- Lista de iconos de redes sociales -->
    <ul class="icons">
        <li>
            <a href="https://www.facebook.com/dayloplasmendoza" target="_blank" class="icono-red-social">
                <img src="images/iconos/face.png" alt="Facebook" class="img-red-social">
            </a>
        </li>
        <li>
            <a href="https://www.instagram.com/dayloplasmendoza" target="_blank" class="icono-red-social">
                <img src="images/iconos/insta.png" alt="Instagram" class="img-red-social">
            </a>
        </li>
    </ul>
    </section>

<!-- Copyright - Sección de derechos de autor y elementos adicionales -->
    <div class="copyright">
        <ul class="menu footer-menu" aria-hidden="true"></ul>
        <a href="admin/auth/login.php" class="btn-login-footer icono-tuerca" title="Iniciar sesión">
            <img src="images/iconos/tuerca.png" alt="Iniciar sesión">
        </a>
        <button id="btn-consultar-ahora">Consultar ahora</button>
        <button id="btn-volver-inicio">Volver a inicio</button>
        <div class="mobile-buttons-container">
            <button id="btn-consultar-ahora-mobile">Consultar ahora</button>
            <button id="btn-volver-inicio-mobile">Volver a inicio</button>
        </div>
    </div>

    <!-- Logos PDF Mirabai y Libra en el footer -->
    <div class="pdf-logos-section">
        <div class="pdf-logos-instruction">
            Haz clic en los logos para ver o descargar los catálogos en PDF
        </div>
        <div class="logo-pdf-container logo-pdf-left">
            <span class="logo-pdf-footer">
                <img src="images/logos/mirabai.png" alt="Logo Mirabai" class="logo-mirabai">
            </span>
            <div class="pdf-links">
                <a href="images/PDF/Mirabai 2023.pdf" target="_blank" class="pdf-link">Ver PDF</a>
                <a href="images/PDF/Mirabai 2023.pdf" download class="pdf-link">Descargar</a>
            </div>
        </div>
        <div class="logo-pdf-container logo-pdf-right">
            <span class="logo-pdf-footer">
                <img src="images/logos/libra.png" alt="Logo Libra" class="logo-libra">
            </span>
            <div class="pdf-links">
                <a href="images/PDF/LIBRA 2025.pdf" target="_blank" class="pdf-link">Ver PDF</a>
                <a href="images/PDF/LIBRA 2025.pdf" download class="pdf-link">Descargar</a>
            </div>
        </div>
    </div>

    <!-- Copyright y nombre bien al pie del footer -->
    <footer class="site-footer-bottom">
        <div>&copy; E.N.A.Desarrollo de sitios web. Todos los derechos reservados</div>
        <div>"Dayloplas-I.P.M. Mendoza"</div>
    </footer>

    <!-- Script para funcionalidad de botones del footer -->
    <script>
        function volverAlInicio() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function consultarAhora() {
            const formulario = document.getElementById('formulario-consulta');

            if (formulario) {
                formulario.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                setTimeout(() => {
                    formulario.style.transform = 'scale(1.02)';
                    formulario.style.transition = 'transform 0.3s ease';
                    setTimeout(() => {
                        formulario.style.transform = 'scale(1)';
                    }, 500);
                }, 500);
            } else {
                window.location.href = 'index.php#formulario-consulta';
            }
        }

        document.getElementById('btn-volver-inicio').onclick = volverAlInicio;
        document.getElementById('btn-consultar-ahora').onclick = consultarAhora;

        document.getElementById('btn-volver-inicio-mobile').onclick = volverAlInicio;
        document.getElementById('btn-consultar-ahora-mobile').onclick = consultarAhora;

        // Mobile cleanup: remove legacy footer menu and suppress duplicate carousel hints
        document.addEventListener('DOMContentLoaded', function () {
            try {
                if (window.innerWidth <= 768) {
                    var fm = document.querySelector('.footer-menu');
                    if (fm) fm.remove();

                    // Extra safeguard: hide any .menu inside footer area
                    document.querySelectorAll('#footer .menu, .copyright .menu').forEach(function (el) {
                        el.style.display = 'none';
                        el.setAttribute('aria-hidden', 'true');
                    });

                    // Add a body class to allow CSS to suppress duplicated carousel hints
                    document.body.classList.add('mobile-no-carousel-after');
                }
            } catch (e) {
                console.warn('Mobile footer cleanup failed', e);
            }
        });
    </script>
