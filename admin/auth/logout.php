<?php 
/*
 * ARCHIVO: logout.php
 * PROPÓSITO: Cerrar sesión de usuario del panel administrativo
 * DESCRIPCIÓN: Script minimalista que realiza logout seguro y redirige al login.
 * 
 * FUNCIONAMIENTO:
 * 1. Carga el sistema core (config.php)
 * 2. Ejecuta función logout() que limpia toda la sesión
 * 3. Redirige automáticamente a la página de login
 * 
 * CARACTERÍSTICAS:
 * - Logout inmediato sin confirmación
 * - Limpieza completa de datos de sesión
 * - Redirección automática a login
 * - Sin interfaz HTML (script de acción directa)
 * 
 * USO TÍPICO:
 * - Enlace "Cerrar Sesión" en el panel admin
 * - Logout automático por timeout
 * - Logout forzado por seguridad
 */

// Inicializar sistema core
require __DIR__.'/../core/config.php'; 

// Ejecutar logout seguro (limpia $_SESSION y destruye sesión)
logout(); 

// Redirigir inmediatamente a la página de login
header('Location: '.BASE_URL.'/auth/login.php');

/*
 * NOTAS DE IMPLEMENTACIÓN:
 * 
 * 1. SIN CONFIRMACIÓN:
 *    Este script ejecuta logout inmediatamente sin pedir confirmación.
 *    Es el comportamiento estándar para botones/enlaces de logout.
 * 
 * 2. SIN HTML:
 *    No hay interfaz visual porque es un script de acción directa.
 *    Todo el flujo es: cargar → logout → redirigir.
 * 
 * 3. SEGURIDAD:
 *    - logout() limpia completamente la sesión
 *    - Redirección previene acceso a páginas protegidas
 *    - No hay posibilidad de mantener sesión abierta accidentalmente
 * 
 * 4. CASOS DE USO:
 *    <!-- En el header del panel admin -->
 *    <a href="<?=BASE_URL?>/auth/logout.php">Cerrar Sesión</a>
 *    
 *    // Logout programático
 *    header('Location: '.BASE_URL.'/auth/logout.php');
 * 
 * 5. EXTENSIONES POSIBLES:
 *    - Logout con mensaje de confirmación
 *    - Registro de logout en logs de auditoría  
 *    - Cleanup adicional (cache, archivos temporales)
 *    - Notificación de logout a sistemas externos
 */
?>
