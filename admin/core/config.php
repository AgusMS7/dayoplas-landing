<?php
/*
 * ARCHIVO: config.php
 * PROPÓSITO: Configuración central y bootstrap del panel administrativo
 * DESCRIPCIÓN: Inicializa el sistema completo del panel admin, incluyendo:
 * - Configuración de sesiones PHP
 * - Definición de constantes del sistema
 * - Carga de todos los módulos core
 * - Prevención de inicialización múltiple
 * 
 * RESPONSABILIDADES:
 * 1. Bootstrap único del sistema (evita doble inicialización)
 * 2. Configuración de constantes de rutas y URLs
 * 3. Manejo de sesiones PHP
 * 4. Carga ordenada de dependencias core
 */

// Habilitar modo estricto de PHP para mayor seguridad de tipos
declare(strict_types=1);

/* 
 * ==========================================
 * PREVENCIÓN DE INICIALIZACIÓN MÚLTIPLE
 * ==========================================
 * 
 * Evita que el sistema se inicialice múltiples veces si config.php
 * es incluido desde varios archivos. Esto previene:
 * - Redefinición de constantes (error fatal)
 * - Múltiples session_start() (warnings)
 * - Carga duplicada de archivos core
 * - Conflictos de estado global
 */
if (!defined('APP_BOOTSTRAPPED')) {
    // Marcar que el sistema ya ha sido inicializado
    define('APP_BOOTSTRAPPED', true);

    /* 
     * ==========================================
     * CONFIGURACIÓN DE SESIONES PHP
     * ==========================================
     * 
     * Gestión inteligente de sesiones que verifica si ya hay una sesión
     * activa antes de intentar iniciar una nueva, evitando warnings.
     */
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    /* 
     * ==========================================
     * DEFINICIÓN DE CONSTANTES DEL SISTEMA
     * ==========================================
     * 
     * Constantes fundamentales que definen la estructura y configuración
     * del panel administrativo. Se definen solo si no existen previamente.
     */
    
    /**
     * APP_NAME: Nombre de la aplicación
     * Usado en títulos, headers, y identificación del sistema
     */
    if (!defined('APP_NAME'))   define('APP_NAME', 'Dayloplas Admin');
    
    /**
     * BASE_PATH: Ruta física absoluta del directorio admin
     * Usado para inclusión de archivos, escritura de logs, etc.
     * Ejemplo: /var/www/html/nati/8/one/admin
     */
    if (!defined('BASE_PATH'))  define('BASE_PATH', dirname(__DIR__));
    
    /**
     * BASE_URL: URL base del panel administrativo
     * CRÍTICO: Debe coincidir con la ruta real del servidor web
     * Usado para enlaces, redirecciones, formularios
     * 
     * NOTA: Ajustar esta URL según el entorno:
     * - Desarrollo: http://localhost/nati/8/one/admin
     * - Producción: https://tudominio.com/admin
     */    
    if (!defined('BASE_URL'))   define('BASE_URL', 'https://dayloplasipmmza.com.ar/admin');
    
    /**
     * ASSETS_URL: URL para recursos estáticos (CSS, JS, imágenes)
     * Construida automáticamente basada en BASE_URL
     * Ejemplo: http://localhost/nati/8/one/admin/assets
     */
    if (!defined('ASSETS_URL')) define('ASSETS_URL', BASE_URL . '/assets');

    

    /* 
     * ==========================================
     * CARGA DE MÓDULOS CORE
     * ==========================================
     * 
     * Carga ordenada de todos los componentes esenciales del sistema.
     * El orden es CRÍTICO debido a las dependencias entre módulos.
     * 
     * DEPENDENCIAS:
     * - db.php: Base de todas las operaciones de datos
     * - csrf.php: Protección de formularios
     * - flash.php: Sistema de mensajes
     * - auth.php: Funciones de autenticación (requiere db.php)
     * - guard.php: Control de acceso (requiere auth.php)
     * - utils.php: Utilidades generales
     */
    
    // 1. Base de datos - DEBE ser primero para operaciones de datos
    require_once __DIR__ . '/db.php';
    
    // 2. Protección CSRF - Independiente, puede ir temprano
    require_once __DIR__ . '/csrf.php';
    
    // 3. Sistema de mensajes flash - Independiente
    require_once __DIR__ . '/flash.php';
    
    // 4. Autenticación - Requiere db.php para consultas de usuario
    require_once __DIR__ . '/auth.php';
    
    // 5. Control de acceso - Requiere auth.php para verificación de roles
    require_once __DIR__ . '/guard.php';
    
    // 6. Utilidades generales - Puede ir al final
    require_once __DIR__ . '/utils.php';
    
    // 7. Sistema de subida de archivos
    require_once __DIR__ . '/file_upload.php';
}

/*
 * ==========================================
 * NOTAS DE CONFIGURACIÓN Y MANTENIMIENTO
 * ==========================================
 * 
 * 1. CONFIGURACIÓN POR ENTORNO:
 *    - Desarrollo: URLs localhost, debug habilitado
 *    - Staging: URLs de prueba, logs detallados
 *    - Producción: URLs finales, optimizaciones activas
 * 
 * 2. VARIABLES DE ENTORNO RECOMENDADAS:
 *    Considerar uso de $_ENV para configuración sensible:
 *    - DATABASE_URL
 *    - APP_ENV (development/production)
 *    - SECRET_KEY para tokens
 * 
 * 3. CONFIGURACIONES ADICIONALES SUGERIDAS:
 *    - Zona horaria: date_default_timezone_set('America/Argentina/Mendoza')
 *    - Límites de memoria y tiempo de ejecución
 *    - Configuración de errores por entorno
 *    - Configuración de uploads
 * 
 * 4. SEGURIDAD:
 *    - BASE_URL debe usar HTTPS en producción
 *    - Verificar permisos de directorio admin/
 *    - Configurar .htaccess para proteger archivos core/
 * 
 * 5. DEBUGGING:
 *    Para problemas de inicialización:
 *    - Verificar que todas las rutas existen
 *    - Confirmar permisos de archivos
 *    - Revisar logs de errores PHP
 *    - Validar configuración de sesiones
 * 
 * 6. EXTENSIBILIDAD:
 *    Para agregar nuevos módulos:
 *    - Añadir require_once en orden de dependencias
 *    - Considerar lazy loading para módulos pesados
 *    - Mantener separation of concerns
 * 
 * EJEMPLO DE USO EN OTROS ARCHIVOS:
 * 
 * <?php
 * require_once __DIR__ . '/core/config.php';
 * 
 * // Sistema ya inicializado y listo para usar:
 * echo APP_NAME; // "Dayloplas Admin"
 * csrf_check();  // Función disponible
 * require_login(); // Sistema de auth listo
 */
?>