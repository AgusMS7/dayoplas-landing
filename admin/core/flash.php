<?php
/*
 * ARCHIVO: flash.php
 * PROPÓSITO: Sistema de mensajes flash (temporales) para feedback del usuario
 * DESCRIPCIÓN: Implementa un mecanismo simple y eficiente para mostrar mensajes
 * temporales que persisten a través de redirecciones HTTP, comúnmente usados
 * para notificar el resultado de operaciones (éxito, error, advertencia).
 * 
 * FUNCIONAMIENTO:
 * Los mensajes flash se almacenan en la sesión ($_SESSION) y se consumen 
 * automáticamente cuando se leen, asegurando que solo se muestren una vez.
 * 
 * CASOS DE USO TÍPICOS:
 * - Confirmar guardado exitoso de formularios
 * - Notificar errores de validación
 * - Mostrar mensajes de advertencia
 * - Feedback después de redirecciones POST-REDIRECT-GET
 */

/**
 * FUNCIÓN: flash()
 * 
 * PROPÓSITO: Gestionar mensajes flash bidireccionales (escribir y leer)
 * 
 * FUNCIONAMIENTO DUAL:
 * 1. MODO ESCRITURA: Cuando se proporciona $value, almacena el mensaje
 * 2. MODO LECTURA: Cuando $value es null, recupera y elimina el mensaje
 * 
 * PARÁMETROS:
 * @param string $key - Identificador único del mensaje (ej: 'ok', 'err', 'warning')
 * @param string|null $value - Contenido del mensaje a almacenar (null para leer)
 * 
 * RETORNA:
 * - En modo escritura (con $value): void (no retorna nada)
 * - En modo lectura (sin $value): string|null - el mensaje almacenado o null
 * 
 * CARACTERÍSTICAS CLAVE:
 * - CONSUMO AUTOMÁTICO: El mensaje se elimina automáticamente al leerlo
 * - PERSISTENCIA TEMPORAL: Sobrevive a redirecciones HTTP
 * - CATEGORIZACIÓN: Soporte para diferentes tipos de mensajes mediante $key
 * - THREAD-SAFE: Usa sesiones PHP nativas para almacenamiento
 * 
 * EJEMPLOS DE USO:
 * 
 * // ESCRIBIR MENSAJE (típicamente después de procesar formulario)
 * flash('ok', 'Usuario creado exitosamente');
 * flash('err', 'Error: Email ya está en uso');
 * flash('warning', 'Contraseña próxima a vencer');
 * header('Location: usuarios.php'); // Redirigir
 * 
 * // LEER Y MOSTRAR MENSAJE (típicamente en la vista)
 * $mensaje_exito = flash('ok');     // Lee y elimina mensaje de éxito
 * $mensaje_error = flash('err');    // Lee y elimina mensaje de error
 * 
 * if ($mensaje_exito) {
 *     echo "<div class='alert success'>$mensaje_exito</div>";
 * }
 * 
 * PATRÓN DE IMPLEMENTACIÓN TÍPICO:
 * 
 * // En controlador (después de procesar formulario):
 * if ($operacion_exitosa) {
 *     flash('ok', 'Operación completada correctamente');
 * } else {
 *     flash('err', 'No se pudo completar la operación');
 * }
 * header('Location: pagina_destino.php');
 * exit;
 * 
 * // En vista (al mostrar la página):
 * <?php if ($msg = flash('ok')): ?>
 *     <div class="alert alert-success"><?= e($msg) ?></div>
 * <?php endif; ?>
 * 
 * <?php if ($msg = flash('err')): ?>
 *     <div class="alert alert-danger"><?= e($msg) ?></div>
 * <?php endif; ?>
 */
function flash(string $key, ?string $value=null) {
    // MODO LECTURA: Si no se proporciona valor, leer y eliminar mensaje
    if ($value === null) { 
        // Obtener mensaje desde la sesión (null si no existe)
        $mensaje = $_SESSION['flash'][$key] ?? null; 
        
        // CONSUMO AUTOMÁTICO: Eliminar el mensaje después de leerlo
        // Esto asegura que el mensaje solo se muestre una vez
        unset($_SESSION['flash'][$key]); 
        
        // Retornar el mensaje leído (o null si no existía)
        return $mensaje; 
    }
    
    // MODO ESCRITURA: Almacenar el mensaje en la sesión
    // Se almacena en $_SESSION['flash'][$key] para persistir a través de redirecciones
    $_SESSION['flash'][$key] = $value;
}

/*
 * ==========================================
 * NOTAS DE IMPLEMENTACIÓN Y MEJORES PRÁCTICAS
 * ==========================================
 * 
 * 1. CONVENCIONES DE CLAVES RECOMENDADAS:
 *    - 'ok' / 'success': Operaciones exitosas (color verde)
 *    - 'err' / 'error': Errores y fallos (color rojo)  
 *    - 'warning': Advertencias (color amarillo/naranja)
 *    - 'info': Información general (color azul)
 * 
 * 2. SEGURIDAD:
 *    - SIEMPRE usar e() al mostrar mensajes para prevenir XSS
 *    - No almacenar datos sensibles en mensajes flash
 *    - Validar contenido antes de almacenar
 * 
 * 3. RENDIMIENTO:
 *    - Los mensajes se almacenan en memoria de sesión (muy eficiente)
 *    - Consumo automático previene acumulación de mensajes antiguos
 *    - No requiere acceso a base de datos
 * 
 * 4. LIMITACIONES:
 *    - Requiere sesiones PHP activas (session_start())
 *    - Los mensajes se pierden si la sesión expira
 *    - No persistente entre diferentes navegadores/dispositivos
 * 
 * 5. EXTENSIONES SUGERIDAS:
 *    - Soporte para múltiples mensajes por clave
 *    - Expiración automática por tiempo
 *    - Niveles de prioridad para mensajes
 *    - Integración con logging para mensajes críticos
 * 
 * 6. PATRÓN POST-REDIRECT-GET:
 *    Este sistema está diseñado específicamente para el patrón PRG:
 *    POST (procesar) → Flash message → REDIRECT → GET (mostrar) → Flash consumed
 *    
 *    Esto previene reenvío accidental de formularios y mejora UX.
 */
?>
