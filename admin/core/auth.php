<?php
/*
 * ARCHIVO: auth.php
 * PROPÓSITO: Sistema de autenticación y manejo de roles de usuario
 * DESCRIPCIÓN: Implementa la funcionalidad completa de autenticación incluyendo:
 * - Gestión de sesiones de usuario
 * - Verificación de credenciales
 * - Sistema de roles y permisos
 * - Login y logout seguros
 * 
 * COMPONENTES PRINCIPALES:
 * 1. Funciones de consulta de estado (user, roles, has_role)
 * 2. Proceso de login con verificación de credenciales
 * 3. Sistema de logout con limpieza de sesión
 */

/**
 * FUNCIÓN: user()
 * 
 * PROPÓSITO: Obtener información del usuario actualmente autenticado
 * 
 * FUNCIONAMIENTO:
 * - Consulta la sesión PHP actual ($_SESSION['user'])
 * - Retorna datos del usuario si está logueado
 * - Retorna null si no hay sesión activa
 * 
 * RETORNA:
 * - array|null: Información del usuario o null si no está autenticado
 * 
 * ESTRUCTURA TÍPICA DEL ARRAY RETORNADO:
 * [
 *     'id' => 123,
 *     'nombre' => 'Juan Pérez',
 *     'email' => 'juan@ejemplo.com'
 * ]
 * 
 * CASOS DE USO:
 * - Verificar si hay usuario logueado: if (user()) { ... }
 * - Mostrar nombre del usuario: echo user()['nombre'] ?? 'Invitado';
 * - Obtener ID para operaciones: $user_id = user()['id'];
 */
function user(): ?array { 
    return $_SESSION['user'] ?? null; 
}

/**
 * FUNCIÓN: roles()
 * 
 * PROPÓSITO: Obtener lista de roles del usuario actual
 * 
 * FUNCIONAMIENTO:
 * - Consulta la sesión PHP ($_SESSION['roles'])
 * - Retorna array de roles asignados al usuario
 * - Retorna array vacío si no hay roles o no está logueado
 * 
 * RETORNA:
 * - array: Lista de slugs de roles ['admin', 'consultor'] o []
 * 
 * EJEMPLOS DE ROLES TÍPICOS:
 * - ['root']: Superadministrador
 * - ['admin']: Administrador del sistema  
 * - ['consultor']: Editor de contenido
 * - []: Sin roles específicos (usuario básico)
 * 
 * NOTA: Un usuario puede tener múltiples roles simultáneamente
 */
function roles(): array { 
    return $_SESSION['roles'] ?? []; 
}

/**
 * FUNCIÓN: has_role()
 * 
 * PROPÓSITO: Verificar si el usuario actual tiene un rol específico
 * 
 * PARÁMETROS:
 * @param string $slug - Identificador único del rol a verificar
 * 
 * FUNCIONAMIENTO:
 * - Obtiene la lista actual de roles del usuario
 * - Verifica si el rol especificado está en la lista
 * - Usa comparación estricta (===) para mayor seguridad
 * 
 * RETORNA:
 * - bool: true si el usuario tiene el rol, false si no lo tiene
 * 
 * EJEMPLOS DE USO:
 * - has_role('admin'): ¿Es administrador?
 * - has_role('root'): ¿Es superusuario?
 * - has_role('consultor'): ¿Puede editar contenido?
 * 
 * SEGURIDAD:
 * - Usa in_array() con strict=true para evitar coerción de tipos
 * - Funciona correctamente incluso si no hay usuario logueado
 */
function has_role(string $slug): bool { 
    return in_array($slug, roles(), true); 
}

/**
 * FUNCIÓN: login()
 * 
 * PROPÓSITO: Autenticar usuario y establecer sesión segura
 * 
 * PARÁMETROS:
 * @param PDO $pdo - Conexión a base de datos para consultas
 * @param string $email - Email del usuario (se normaliza automáticamente)
 * @param string $password - Contraseña en texto plano
 * 
 * RETORNA:
 * - bool: true si login exitoso, false si falla autenticación
 * 
 * PROCESO DE AUTENTICACIÓN:
 * 1. NORMALIZACIÓN: Email convertido a minúsculas y sin espacios
 * 2. CONSULTA: Buscar usuario por email en base de datos
 * 3. VERIFICACIONES:
 *    - Usuario existe en la base de datos
 *    - Estado del usuario es 'ACTIVO' (no suspendido/eliminado)
 *    - Contraseña coincide con hash almacenado
 * 4. ESTABLECER SESIÓN: Almacenar datos de usuario y roles
 * 
 * SEGURIDAD IMPLEMENTADA:
 * - password_verify(): Verificación segura de contraseña hasheada
 * - Verificación de estado activo previene login de usuarios suspendidos
 * - Consulta preparada previene inyección SQL
 * - Solo información no sensible se almacena en sesión
 * 
 * ESTRUCTURA DE SESIÓN CREADA:
 * $_SESSION['user'] = ['id' => X, 'nombre' => 'Y', 'email' => 'Z'];
 * $_SESSION['roles'] = ['admin', 'consultor']; // o array vacío
 * 
 * CASOS DE FALLO:
 * - Usuario no existe: false
 * - Contraseña incorrecta: false
 * - Usuario inactivo/suspendido: false
 * - Error de base de datos: false
 */
function login(PDO $pdo, string $email, string $password): bool {
    // Preparar consulta para buscar usuario por email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([strtolower(trim($email))]); // Normalizar email
    $u = $stmt->fetch();

    // Verificar: usuario existe, está activo, contraseña correcta
    $ok = $u && $u['estado']==='ACTIVO' && password_verify($password, $u['password_hash']);
    if (!$ok) return false; // Fallo en autenticación

    // ESTABLECER SESIÓN DE USUARIO (solo datos no sensibles)
    $_SESSION['user'] = [
        'id' => $u['id'], 
        'nombre' => $u['nombre'], 
        'email' => $u['email']
    ];

    // CARGAR ROLES DEL USUARIO desde tabla de relaciones
    $r = $pdo->prepare("SELECT r.slug FROM user_roles ur JOIN roles r ON r.id=ur.role_id WHERE ur.user_id=?");
    $r->execute([$u['id']]);
    $_SESSION['roles'] = array_column($r->fetchAll(), 'slug');  // Puede estar vacío

    return true; // Login exitoso
}

/**
 * FUNCIÓN: logout()
 * 
 * PROPÓSITO: Cerrar sesión de usuario de forma segura y completa
 * 
 * FUNCIONAMIENTO:
 * 1. LIMPIAR VARIABLES: $_SESSION = [] vacía todas las variables de sesión
 * 2. DESTRUIR SESIÓN: session_destroy() elimina la sesión del servidor
 * 3. LIMPIEZA COMPLETA: Elimina tanto datos en memoria como en almacenamiento
 * 
 * SEGURIDAD:
 * - Limpieza completa de todos los datos de sesión
 * - Previene reutilización de sesiones existentes
 * - Fuerza re-autenticación para próximo acceso
 * 
 * CASOS DE USO:
 * - Botón "Cerrar Sesión" en interfaz
 * - Logout automático por timeout
 * - Limpieza después de cambio de contraseña
 * - Respuesta a actividad sospechosa
 * 
 * NOTA: Después de logout(), user() retornará null y has_role() será false
 */
function logout(): void { 
    $_SESSION = [];      // Limpiar todas las variables de sesión
    session_destroy();   // Destruir la sesión completamente
}

/*
 * ==========================================
 * NOTAS DE IMPLEMENTACIÓN Y SEGURIDAD
 * ==========================================
 * 
 * 1. ESTRUCTURA DE BASE DE DATOS REQUERIDA:
 * 
 * Tabla 'users':
 * - id (primary key)
 * - nombre (varchar)
 * - email (varchar, unique)
 * - password_hash (varchar) - NUNCA almacenar contraseñas en texto plano
 * - estado (enum: 'ACTIVO', 'INACTIVO', 'SUSPENDIDO')
 * 
 * Tabla 'roles':
 * - id (primary key)
 * - slug (varchar, unique) - ej: 'admin', 'consultor', 'root'
 * - nombre (varchar) - nombre descriptivo del rol
 * 
 * Tabla 'user_roles' (muchos a muchos):
 * - user_id (foreign key -> users.id)
 * - role_id (foreign key -> roles.id)
 * 
 * 2. MEJORES PRÁCTICAS DE SEGURIDAD:
 * 
 * - CONTRASEÑAS: Usar password_hash() y password_verify()
 * - SESIONES: Configurar session.cookie_secure=1 en HTTPS
 * - TIMEOUT: Implementar expiración automática de sesiones
 * - LOGS: Registrar intentos de login fallidos
 * - RATE LIMITING: Limitar intentos de login por IP/usuario
 * 
 * 3. FLUJO TÍPICO DE AUTENTICACIÓN:
 * 
 * // Página de login
 * if ($_POST['email']) {
 *     if (login($pdo, $_POST['email'], $_POST['password'])) {
 *         header('Location: dashboard.php');
 *     } else {
 *         $error = "Credenciales inválidas";
 *     }
 * }
 * 
 * // Páginas protegidas
 * require_once 'config.php';
 * if (!user()) {
 *     header('Location: login.php');
 *     exit;
 * }
 * 
 * // Verificación de permisos
 * if (!has_role('admin')) {
 *     http_response_code(403);
 *     exit('Sin permisos');
 * }
 * 
 * 4. CONSIDERACIONES DE ESCALABILIDAD:
 * 
 * - Para alta concurrencia: considerar cache de roles en Redis
 * - Para múltiples servidores: sesiones en base de datos o Redis
 * - Para microservicios: considerar JWT tokens en lugar de sesiones
 * 
 * 5. DEBUGGING COMÚN:
 * 
 * - Login falla: Verificar estado de usuario y hash de contraseña
 * - Roles no funcionan: Revisar tabla user_roles y consulta SQL
 * - Sesión se pierde: Verificar configuración de sesiones PHP
 * - Permisos incorrectos: Usar var_dump(roles()) para debug
 */
?>
