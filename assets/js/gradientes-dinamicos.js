/*
 * ARCHIVO: gradientes-dinamicos.js
 * PROPÓSITO: JavaScript para gradientes dinámicos profesionales
 * CREADO POR: GitHub Copilot
 * DESCRIPCIÓN: Maneja la rotación automática de gradientes elegantes
 *              en los headers de todas las páginas del sitio
 */

// ==========================================
// 🎨 CONFIGURACIÓN DE GRADIENTES ELEGANTES
// ==========================================

const GRADIENTES_PROFESIONALES = [
    // Celeste profesional suave - Perfecto para instituciones educativas
    'linear-gradient(135deg, rgba(135, 206, 235, 0.85) 0%, rgba(240, 248, 255, 0.95) 50%, rgba(255, 255, 255, 0.98) 100%)',
    
    // Azul institucional elegante - Transmite confianza y profesionalismo
    'linear-gradient(45deg, rgba(74, 144, 226, 0.75) 0%, rgba(135, 206, 235, 0.85) 50%, rgba(248, 249, 250, 0.95) 100%)',
    
    // Sunrise educativo cálido - Colores que inspiran crecimiento
    'linear-gradient(135deg, rgba(255, 229, 180, 0.8) 0%, rgba(255, 234, 167, 0.85) 30%, rgba(221, 214, 254, 0.8) 70%, rgba(168, 230, 207, 0.85) 100%)',
    
    // Gradiente radial moderno - Efecto contemporáneo y sofisticado
    'radial-gradient(ellipse at top, rgba(74, 144, 226, 0.6) 0%, rgba(135, 206, 235, 0.4) 40%, rgba(240, 248, 255, 0.9) 80%, rgba(255, 255, 255, 0.98) 100%)',
    
    // Degradado vertical suave - Transición armónica y relajante
    'linear-gradient(180deg, rgba(173, 216, 230, 0.7) 0%, rgba(224, 246, 255, 0.85) 50%, rgba(248, 249, 250, 0.95) 100%)'
];

// ==========================================
// ⚙️ CONFIGURACIÓN PERSONALIZABLE
// ==========================================

const CONFIG_GRADIENTES = {
    // Tiempo entre cambios de gradiente (en milisegundos)
    intervalo: 6000, // 6 segundos
    
    // Duración de la transición (debe coincidir con CSS)
    duracionTransicion: '1.5s',
    
    // Selector del elemento header
    selectorHeader: '#header',
    
    // Activar modo debug para consola
    debug: false
};

// ==========================================
// 🚀 FUNCIÓN PRINCIPAL
// ==========================================

function iniciarGradientesDinamicos() {
    let index = 0;
    const header = document.querySelector(CONFIG_GRADIENTES.selectorHeader);
    
    // Verificar que existe el elemento header
    if (!header) {
        if (CONFIG_GRADIENTES.debug) {
            console.warn('🎨 No se encontró el elemento header para gradientes dinámicos');
        }
        return;
    }
    
    if (CONFIG_GRADIENTES.debug) {
        console.log('🎨 Iniciando gradientes dinámicos profesionales');
    }
    
    // Función para cambiar el gradiente
    function cambiarGradiente() {
        const gradienteActual = GRADIENTES_PROFESIONALES[index];
        
        // Aplicar el gradiente
        header.style.background = gradienteActual;
        header.style.backgroundAttachment = 'fixed';
        header.style.transition = `background ${CONFIG_GRADIENTES.duracionTransicion} ease-in-out`;
        
        if (CONFIG_GRADIENTES.debug) {
            console.log(`🎨 Gradiente aplicado [${index + 1}/${GRADIENTES_PROFESIONALES.length}]:`, gradienteActual.substring(0, 50) + '...');
        }
        
        // Avanzar al siguiente gradiente (ciclo infinito)
        index = (index + 1) % GRADIENTES_PROFESIONALES.length;
    }
    
    // Aplicar el primer gradiente inmediatamente
    cambiarGradiente();
    
    // Configurar intervalo para cambios automáticos
    const intervalId = setInterval(cambiarGradiente, CONFIG_GRADIENTES.intervalo);
    
    // Devolver ID del intervalo para poder cancelarlo si es necesario
    return intervalId;
}

// ==========================================
// 🎯 FUNCIONES DE UTILIDAD
// ==========================================

// Función para pausar los gradientes dinámicos
function pausarGradientes(intervalId) {
    if (intervalId) {
        clearInterval(intervalId);
        if (CONFIG_GRADIENTES.debug) {
            console.log('⏸️ Gradientes dinámicos pausados');
        }
    }
}

// Función para aplicar un gradiente específico
function aplicarGradienteEspecifico(indice) {
    const header = document.querySelector(CONFIG_GRADIENTES.selectorHeader);
    if (header && indice >= 0 && indice < GRADIENTES_PROFESIONALES.length) {
        header.style.background = GRADIENTES_PROFESIONALES[indice];
        header.style.backgroundAttachment = 'fixed';
        header.style.transition = `background ${CONFIG_GRADIENTES.duracionTransicion} ease-in-out`;
        
        if (CONFIG_GRADIENTES.debug) {
            console.log(`🎯 Gradiente específico aplicado [${indice + 1}]:`, GRADIENTES_PROFESIONALES[indice].substring(0, 50) + '...');
        }
    }
}

// ==========================================
// 🏁 INICIALIZACIÓN AUTOMÁTICA
// ==========================================

// Iniciar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Pequeña demora para asegurar que todos los estilos estén cargados
    setTimeout(iniciarGradientesDinamicos, 100);
});

// ==========================================
// 📚 EXPORTAR PARA USO EXTERNO
// ==========================================

// Para uso en otros scripts si es necesario
window.GradientesDinamicos = {
    iniciar: iniciarGradientesDinamicos,
    pausar: pausarGradientes,
    aplicarEspecifico: aplicarGradienteEspecifico,
    gradientes: GRADIENTES_PROFESIONALES,
    config: CONFIG_GRADIENTES
};

// ==========================================
// 💡 GUÍA DE USO
// ==========================================

/*
 * CÓMO USAR ESTE ARCHIVO:
 * 
 * 1. INCLUSIÓN BÁSICA:
 *    <script src="assets/js/gradientes-dinamicos.js"></script>
 * 
 * 2. USO AUTOMÁTICO:
 *    - Se inicia automáticamente al cargar la página
 *    - Busca elemento con id="header"
 *    - Cambia gradientes cada 6 segundos
 * 
 * 3. CONTROL MANUAL:
 *    const intervalo = window.GradientesDinamicos.iniciar();
 *    window.GradientesDinamicos.pausar(intervalo);
 *    window.GradientesDinamicos.aplicarEspecifico(2);
 * 
 * 4. PERSONALIZACIÓN:
 *    - Modificar CONFIG_GRADIENTES para cambiar comportamiento
 *    - Agregar nuevos gradientes a GRADIENTES_PROFESIONALES
 *    - Cambiar CONFIG_GRADIENTES.debug = true para modo desarrollo
 */