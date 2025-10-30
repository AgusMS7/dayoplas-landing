<?php
/*
 * ARCHIVO: guard.php
 * PROPÓSITO: Sistema de autenticación y autorización para el panel administrativo
 * DESCRIPCIÓN: Contiene funciones de seguridad que controlan el acceso a diferentes
 * secciones del sistema basándose en autenticación de usuarios y roles específicos.
 * 
 * JERARQUÍA DE ROLES (de menor a mayor privilegio):
 * - Invitado: Sin sesión iniciada
 * - Usuario autenticado: Puede ver contenido básico
 * - Consultor: Puede editar contenido
 * - Admin: Puede cambiar estados y gestión avanzada
 * - Root: Acceso total, incluyendo eliminación física
 */

/**
 * FUNCIÓN: require_login()
 * 
 * PROPÓSITO: Forzar autenticación de usuario antes de acceder a una página
 * 
 * FUNCIONAMIENTO:
 * - Verifica si existe un usuario autenticado activo
 * - Si NO hay usuario logueado, redirige automáticamente al login
 * - Si SÍ hay usuario, permite continuar con la ejecución
 * 
 * PARÁMETROS: Ninguno
 * RETORNA: void (no retorna nada, redirige o continúa)
 * 
 * USO TÍPICO:
 * require_login(); // Al inicio de páginas que requieren autenticación
 * 
 * NOTA: Usa la función user() para verificar sesión activa
 */
function require_login(): void {
    if (!user()) { 
        header('Location: '.BASE_URL.'/auth/login.php'); 
        exit; 
    }
}

/**
 * FUNCIÓN: require_any()
 * 
 * PROPÓSITO: Verificar que el usuario tenga al menos uno de los roles especificados
 * 
 * FUNCIONAMIENTO:
 * 1. Primero verifica que el usuario esté logueado (require_login)
 * 2. Recorre la lista de roles permitidos
 * 3. Si el usuario tiene algún rol de la lista, permite acceso
 * 4. Si NO tiene ningún rol válido, retorna error 403 Forbidden
 * 
 * PARÁMETROS:
 * @param array $roles - Lista de roles que tienen acceso permitido
 * 
 * RETORNA: void (permite acceso o termina con error 403)
 * 
 * EJEMPLOS DE USO:
 * require_any(['admin', 'root']); // Solo admin o root
 * require_any(['consultor','admin','root']); // Cualquiera con permisos de edición
 * 
 * CÓDIGOS HTTP:
 * - 403 Forbidden: El usuario está autenticado pero no tiene permisos suficientes
 */
function require_any(array $roles): void {
    require_login();
    foreach ($roles as $r) if (has_role($r)) return;
    http_response_code(403); 
    exit('Sin permisos');
}

/*
 * ==========================================
 * FUNCIONES DE VERIFICACIÓN DE PERMISOS
 * ==========================================
 * 
 * Las siguientes funciones proporcionan una interfaz simplificada
 * para verificar permisos específicos sin necesidad de conocer
 * los roles exactos requeridos para cada acción.
 */

/**
 * FUNCIÓN: can_view()
 * 
 * PROPÓSITO: Verificar si el usuario puede ver contenido básico
 * 
 * CRITERIO: Cualquier usuario autenticado puede ver
 * 
 * RETORNA: bool - true si puede ver, false si no está autenticado
 * 
 * USO TÍPICO: 
 * <?php if (can_view()): ?>
 *   <div>Contenido para usuarios autenticados</div>
 * <?php endif; ?>
 */
function can_view(): bool { 
    return (bool)user(); 
}

/**
 * FUNCIÓN: can_edit()
 * 
 * PROPÓSITO: Verificar si el usuario puede editar contenido
 * 
 * CRITERIO: Roles permitidos - consultor, admin, root
 * 
 * RETORNA: bool - true si tiene permisos de edición
 * 
 * CASOS DE USO:
 * - Mostrar/ocultar botones de edición
 * - Habilitar formularios de modificación
 * - Controlar acceso a páginas de administración básica
 * 
 * EJEMPLO:
 * <?php if (can_edit()): ?>
 *   <a href="edit.php">Editar</a>
 * <?php endif; ?>
 */
function can_edit(): bool { 
    return has_role('consultor') || has_role('admin') || has_role('root'); 
}

/**
 * FUNCIÓN: can_change_state()
 * 
 * PROPÓSITO: Verificar si el usuario puede cambiar estados de registros
 * 
 * CRITERIO: Roles permitidos - admin, root
 * 
 * ACCIONES INCLUIDAS:
 * - Activar/desactivar registros (estado A/I)
 * - Cambiar estados de formaciones
 * - Modificar configuraciones del sistema
 * - Gestión de usuarios de nivel inferior
 * 
 * RETORNA: bool - true si puede cambiar estados
 * 
 * EJEMPLO:
 * <?php if (can_change_state()): ?>
 *   <button onclick="toggleEstado()">Activar/Desactivar</button>
 * <?php endif; ?>
 */
function can_change_state(): bool { 
    return has_role('admin') || has_role('root'); 
}

/**
 * FUNCIÓN: can_delete_physical()
 * 
 * PROPÓSITO: Verificar si el usuario puede eliminar registros físicamente
 * 
 * CRITERIO: Solo el rol 'root' tiene este privilegio
 * 
 * NIVEL DE PELIGRO: MÁXIMO
 * - Eliminación permanente de registros de base de datos
 * - Borrado de archivos del servidor
 * - Operaciones irreversibles
 * 
 * RETORNA: bool - true solo si es root
 * 
 * RECOMENDACIONES DE SEGURIDAD:
 * - Usar confirmación doble para eliminaciones
 * - Registrar todas las eliminaciones en logs de auditoría
 * - Considerar "soft delete" antes que eliminación física
 * 
 * EJEMPLO:
 * <?php if (can_delete_physical()): ?>
 *   <button class="danger" onclick="confirmDelete()">Eliminar Permanentemente</button>
 * <?php endif; ?>
 */
function can_delete_physical(): bool { 
    return has_role('root'); 
}

/*
 * ==========================================
 * NOTAS DE IMPLEMENTACIÓN Y SEGURIDAD
 * ==========================================
 * 
 * 1. DEPENDENCIAS:
 *    - user(): Función que retorna el usuario actual (debe estar definida en config.php)
 *    - has_role(): Función que verifica roles (debe estar definida en config.php)
 *    - BASE_URL: Constante con la URL base del sistema
 * 
 * 2. FLUJO DE SEGURIDAD:
 *    Page Request → require_login() → require_any() → Función específica
 * 
 * 3. MEJORES PRÁCTICAS:
 *    - Siempre verificar permisos tanto en PHP como en la UI
 *    - No confiar solo en JavaScript para control de acceso
 *    - Registrar intentos de acceso no autorizado
 *    - Usar HTTPS en producción
 * 
 * 4. ESTRUCTURA DE ROLES RECOMENDADA:
 *    - root: Superadministrador (1 usuario máximo)
 *    - admin: Administradores del sistema (pocos usuarios)
 *    - consultor: Editores de contenido (usuarios operativos)
 *    - viewer: Solo lectura (si se necesita en el futuro)
 * 
 * 5. CONSIDERACIONES DE ESCALABILIDAD:
 *    - Para sistemas grandes, considerar implementar caché de roles
 *    - Evaluar uso de middleware para verificación automática
 *    - Implementar sistema de permisos granular por módulos
 */
?>
