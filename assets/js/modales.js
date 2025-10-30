/* =================================
   FUNCIONES JAVASCRIPT PARA MODALES
   ================================= */

// Función para mostrar el modal de contacto
function mostrarContacto() {
    // Si estamos en la página de detalle, redirigir a contacto.php
    if (window.location.pathname.includes('detalle.php')) {
        window.location.href = 'contacto.php';
        return;
    }
    
    // Si estamos en index.php, mostrar la sección de contacto
    const seccionContacto = $('#contacto');
    if (seccionContacto.length > 0) {
        // Mostrar la sección de contacto con efecto de fade
        seccionContacto.fadeIn(300);
        
        // Hacer scroll suave hacia la sección de contacto
        $('html, body').animate({
            scrollTop: seccionContacto.offset().top - 60
        }, 500);
        
        // Agregar clase para el efecto de overlay
        $('body').addClass('contacto-activo');
    } else {
        // Si no existe la sección, redirigir a contacto.php
        window.location.href = 'contacto.php';
    }
}

// Función para ocultar el modal de contacto
function ocultarContacto() {
    // Ocultar la sección de contacto con efecto de fade
    $('#contacto').fadeOut(300);
    
    // Remover clase del overlay
    $('body').removeClass('contacto-activo');
    
    // Hacer scroll hacia arriba suavemente
    $('html, body').animate({
        scrollTop: 0
    }, 500);
}

// Función para abrir el modal de Nosotros
function abrirModalNosotros() {
    const modal = document.getElementById('modal-nosotros');
    if (modal) {
        modal.style.display = 'block';
        // Forzar reflow para que la transición funcione
        modal.offsetHeight;
        modal.classList.add('show');
    }
}

// Función para cerrar el modal de Nosotros
function cerrarModalNosotros(event) {
    // Si el evento existe y no es el target correcto, no hacer nada
    if (event && event.target !== event.currentTarget) return;
    
    const modal = document.getElementById('modal-nosotros');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300); // Esperar a que termine la transición
    }
}

// Función para manejar los enlaces del menú con modal
function manejarEnlaceMenu(event, tipo) {
    event.preventDefault();
    
    // Si estamos en la página de detalle, redirigir a index con el tipo
    if (window.location.pathname.includes('detalle.php')) {
        window.location.href = 'index.php#' + tipo;
        return;
    }
    
    // Si estamos en index, hacer scroll a la sección
    const seccion = document.getElementById(tipo);
    if (seccion) {
        $('html, body').animate({
            scrollTop: seccion.offsetTop - 60
        }, 500);
    }
}

// Función específica para manejar enlaces de contacto
function manejarContacto(event) {
    event.preventDefault();
    mostrarContacto();
}

// Eventos del DOM
$(document).ready(function() {
    // Cerrar contacto con tecla ESC
    $(document).keyup(function(e) {
        if (e.keyCode == 27) { // ESC key
            ocultarContacto();
        }
    });
    
    // Cerrar modal Nosotros con tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalNosotros();
        }
    });
    
    // Manejar el anchor del formulario de consulta
    if (window.location.hash === '#formulario-consulta') {
        setTimeout(() => {
            const formulario = document.getElementById('formulario-consulta');
            if (formulario) {
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
            }
        }, 100);
    }
});