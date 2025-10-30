<?php 
/*
 * ARCHIVO: register.php
 * PROPÓSITO: Página de registro de nuevos usuarios para el panel administrativo
 * DESCRIPCIÓN: Permite a nuevos usuarios crear cuentas que quedan pendientes
 * de aprobación por parte de un administrador.
 * 
 * FLUJO DEL PROCESO:
 * 1. Usuario llena formulario de registro
 * 2. Sistema valida datos y crea cuenta con estado 'PENDIENTE'
 * 3. Administrador debe activar manualmente la cuenta
 * 4. Usuario puede entonces hacer login
 * 
 * CARACTERÍSTICAS DE SEGURIDAD:
 * - Protección CSRF en formularios
 * - Validación de datos del lado servidor
 * - Contraseñas hasheadas con password_hash()
 * - Estado 'PENDIENTE' previene acceso automático
 */

// Inicializar sistema y verificar token CSRF
require __DIR__.'/../core/config.php'; 
csrf_check();

// PROCESAMIENTO DEL FORMULARIO DE REGISTRO (solo POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ==========================================
    // CAPTURA Y NORMALIZACIÓN DE DATOS
    // ==========================================
    
    // Obtener y limpiar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');           // Eliminar espacios extra
    $email  = strtolower(trim($_POST['email'] ?? '')); // Normalizar email
    $pass   = $_POST['password'] ?? '';               // Contraseña principal
    $pass2  = $_POST['password2'] ?? '';              // Confirmación contraseña

    // ==========================================
    // VALIDACIONES DEL LADO SERVIDOR
    // ==========================================
    
    // Verificar que todos los campos requeridos estén completos y válidos
    $validaciones_fallidas = (
        $nombre === '' ||                                    // Nombre vacío
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||      // Email inválido
        strlen($pass) < 6 ||                                // Contraseña muy corta
        $pass !== $pass2                                    // Contraseñas no coinciden
    );
    
    if ($validaciones_fallidas) {
        // Mostrar mensaje de error y redirigir para evitar reenvío
        flash('err','Datos inválidos. Revisá nombre, email y contraseñas.');
        header('Location: register.php'); 
        exit;
    }

    // ==========================================
    // CREACIÓN DE USUARIO EN BASE DE DATOS
    // ==========================================
    
    // Preparar consulta SQL para insertar nuevo usuario
    $sql = "INSERT INTO users (nombre,email,password_hash,estado) VALUES (?,?,?, 'PENDIENTE')";
    
    try {
        // Ejecutar inserción con contraseña hasheada de forma segura
        $pdo->prepare($sql)->execute([
            $nombre,
            $email,
            password_hash($pass, PASSWORD_DEFAULT)  // Hash seguro de contraseña
        ]);
        
        // REGISTRO EXITOSO
        flash('ok','Registro creado. Un administrador habilitará tu acceso.');
        header('Location: login.php'); 
        exit;
        
    } catch (PDOException $e) {
        // MANEJO DE ERRORES (típicamente email duplicado)
        flash('err','Ese email ya existe.');
        header('Location: register.php'); 
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Configuración básica de la página -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registro · <?=APP_NAME?></title>
    
    <!-- Hoja de estilos del panel administrativo -->
    <link rel="stylesheet" href="<?=ASSETS_URL?>/admin.css">
</head>
<body class="auth">
    <!-- FORMULARIO DE REGISTRO -->
    <form method="post" class="card">
        <h1>Crear cuenta</h1>
        
        <!-- Campo CSRF para protección contra ataques -->
        <?=csrf_field()?>
        
        <!-- CAMPOS DE ENTRADA DEL FORMULARIO -->
        <label>Nombre 
            <input name="nombre" required>
        </label>
        
        <label>Email 
            <input type="email" name="email" required>
        </label>
        
        <label>Contraseña 
            <input type="password" name="password" required>
        </label>
        
        <label>Repetir contraseña 
            <input type="password" name="password2" required>
        </label>
        
        <!-- Botón de envío -->
        <button class="btn">Registrarme</button>
        
        <!-- MENSAJES FLASH PARA FEEDBACK -->
        <?php if ($m=flash('ok')): ?>
            <div class="alert ok"><?=$m?></div>
        <?php endif; ?>
        
        <?php if ($m=flash('err')): ?>
            <div class="alert err"><?=$m?></div>
        <?php endif; ?>
        
        <!-- Enlace a página de login para usuarios existentes -->
        <p class="muted">¿Ya tenés cuenta? <a href="login.php">Ingresar</a></p>
    </form>
</body>
</html>
