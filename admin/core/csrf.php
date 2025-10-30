<?php
/*
 * ARCHIVO: csrf.php
 * PROPÓSITO: Protección contra ataques CSRF (Cross-Site Request Forgery)
 * DESCRIPCIÓN: Implementa un sistema de tokens para verificar que las peticiones
 * POST provienen realmente del usuario autenticado y no de sitios maliciosos.
 * 
 * ¿QUÉ ES CSRF?
 * Un ataque donde un sitio malicioso engaña al navegador del usuario para que
 * envíe peticiones no autorizadas a un sitio donde el usuario está logueado.
 * 
 * PROTECCIÓN IMPLEMENTADA:
 * 1. Generar token único por sesión
 * 2. Incluir token en formularios
 * 3. Verificar token en peticiones POST
 */

/**
 * FUNCIÓN: csrf_token()
 * 
 * PROPÓSITO: Generar o recuperar el token CSRF único para la sesión actual
 * 
 * FUNCIONAMIENTO:
 * - Si no existe token en la sesión, genera uno nuevo de 64 caracteres hex
 * - Si ya existe, retorna el token existente (consistencia durante toda la sesión)
 * - Usa random_bytes(32) para máxima seguridad criptográfica
 * 
 * CARACTERÍSTICAS DE SEGURIDAD:
 * - 64 caracteres hexadecimales (256 bits de entropía)
 * - Criptográficamente seguro (random_bytes)
 * - Único por sesión de usuario
 * - Persiste durante toda la sesión activa
 * 
 * RETORNA: string - Token CSRF de 64 caracteres hexadecimales
 * 
 * EJEMPLO: "a1b2c3d4e5f6...789" (64 caracteres)
 */
function csrf_token(): string {
    // Si no existe token en la sesión actual, generar uno nuevo
    if (empty($_SESSION['csrf'])) { 
        // Generar 32 bytes aleatorios y convertir a hexadecimal (64 caracteres)
        $_SESSION['csrf'] = bin2hex(random_bytes(32)); 
    }
    // Retornar el token existente (consistente durante toda la sesión)
    return $_SESSION['csrf'];
}

/**
 * FUNCIÓN: csrf_field()
 * 
 * PROPÓSITO: Generar campo HTML oculto con el token CSRF para formularios
 * 
 * FUNCIONAMIENTO:
 * - Crea un input type="hidden" con name="_token"
 * - Incluye el token CSRF actual como value
 * - Escapa el token para prevenir inyección HTML
 * 
 * RETORNA: string - Campo HTML completo listo para insertar en formularios
 * 
 * EJEMPLO DE SALIDA:
 * <input type="hidden" name="_token" value="a1b2c3d4e5f6...789">
 * 
 * USO TÍPICO EN FORMULARIOS:
 * <form method="post" action="procesar.php">
 *     <?= csrf_field() ?>
 *     <input type="text" name="nombre" required>
 *     <button type="submit">Enviar</button>
 * </form>
 * 
 * SEGURIDAD:
 * - Usa htmlspecialchars() con ENT_QUOTES para escape completo
 * - Previene inyección HTML en el token
 */
function csrf_field(): string {
    return '<input type="hidden" name="_token" value="'.htmlspecialchars(csrf_token(),ENT_QUOTES).'">';
}

/**
 * FUNCIÓN: csrf_check()
 * 
 * PROPÓSITO: Verificar validez del token CSRF en peticiones POST
 * 
 * FUNCIONAMIENTO:
 * 1. Solo verifica en peticiones POST (GET no requiere protección CSRF)
 * 2. Compara token enviado ($_POST['_token']) con token de sesión
 * 3. Usa hash_equals() para prevenir ataques de timing
 * 4. Termina ejecución con error 419 si el token es inválido
 * 
 * VERIFICACIONES REALIZADAS:
 * - Método de petición es POST
 * - Existe el campo _token en $_POST
 * - Token enviado coincide exactamente con token de sesión
 * 
 * CÓDIGOS DE ERROR:
 * - 419 (Authentication Timeout): Token CSRF inválido o ausente
 * 
 * SEGURIDAD CRÍTICA:
 * - hash_equals(): Comparación segura que previene timing attacks
 * - Verificación estricta: falla si falta token o no coincide
 * - Termina inmediatamente si detecta intento malicioso
 * 
 * USO TÍPICO:
 * En el inicio de scripts que procesan formularios POST:
 * 
 * <?php
 * require_once 'config.php';
 * csrf_check(); // Verificar antes de procesar datos
 * 
 * // Procesar formulario solo si CSRF es válido
 * if ($_POST['nombre']) {
 *     // Código de procesamiento seguro...
 * }
 */
function csrf_check(): void {
    // Solo verificar en peticiones POST (GET/HEAD no requieren protección CSRF)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar que existe token enviado Y coincide con token de sesión
        $valid = isset($_POST['_token']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['_token']);
        
        // Si token inválido, terminar con error HTTP 419 (Authentication Timeout)
        if (!$valid) { 
            http_response_code(419); 
            exit('CSRF token inválido'); 
        }
    }
}

/*
 * ==========================================
 * GUÍA DE IMPLEMENTACIÓN CSRF
 * ==========================================
 * 
 * FLUJO TÍPICO DE PROTECCIÓN:
 * 
 * 1. MOSTRAR FORMULARIO (GET):
 *    - Generar token: csrf_token()
 *    - Incluir campo: echo csrf_field();
 * 
 * 2. PROCESAR FORMULARIO (POST):
 *    - Verificar token: csrf_check();
 *    - Procesar datos si verificación pasa
 * 
 * EJEMPLO COMPLETO:
 * 
 * // formulario.php (mostrar)
 * <?php require_once 'config.php'; ?>
 * <form method="post" action="procesar.php">
 *     <?= csrf_field() ?>
 *     <input name="dato" required>
 *     <button type="submit">Enviar</button>
 * </form>
 * 
 * // procesar.php (procesar)
 * <?php
 * require_once 'config.php';
 * csrf_check(); // Verificar ANTES de procesar
 * 
 * // Código de procesamiento seguro aquí...
 * echo "Datos procesados correctamente";
 * 
 * CONSIDERACIONES IMPORTANTES:
 * 
 * 1. CUÁNDO USAR:
 *    - SIEMPRE en formularios que modifican datos
 *    - Operaciones de crear, editar, eliminar
 *    - Cambios de configuración
 *    - NO necesario para consultas de solo lectura
 * 
 * 2. LIMITACIONES:
 *    - Requiere JavaScript habilitado para formularios dinámicos
 *    - Token expira con la sesión
 *    - No protege contra ataques de ingeniería social
 * 
 * 3. SEGURIDAD ADICIONAL:
 *    - Combinar con validación de referer
 *    - Implementar rate limiting
 *    - Usar HTTPS en producción
 *    - Validar datos además del token
 * 
 * 4. DEBUGGING:
 *    - Error 419 indica problema de token CSRF
 *    - Verificar que formulario incluye csrf_field()
 *    - Confirmar que sesiones están funcionando
 *    - Revisar configuración de cookies
 */
?>
