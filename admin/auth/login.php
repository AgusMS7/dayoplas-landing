<?php 
/*
 * ARCHIVO: login.php
 * PROPÓSITO: Página de autenticación para acceso al panel administrativo
 * DESCRIPCIÓN: Permite a usuarios registrados y autorizados acceder al sistema
 * mediante validación de credenciales (email y contraseña).
 * 
 * FLUJO DEL PROCESO:
 * 1. Usuario ingresa email y contraseña
 * 2. Sistema verifica credenciales en base de datos
 * 3. Si es válido: crea sesión y redirige al dashboard
 * 4. Si es inválido: muestra error y permite reintentar
 * 
 * CARACTERÍSTICAS DE SEGURIDAD:
 * - Protección CSRF contra ataques de falsificación
 * - Verificación de estado ACTIVO del usuario
 * - Contraseñas verificadas con password_verify()
 * - Redirección segura tras login exitoso
 */

// Inicializar sistema y verificar token CSRF
require __DIR__.'/../core/config.php'; 
csrf_check();

// PROCESAMIENTO DEL INTENTO DE LOGIN (solo POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ==========================================
    // INTENTO DE AUTENTICACIÓN
    // ==========================================
    
    // Llamar función de login del sistema core con credenciales proporcionadas
    $login_exitoso = login(
        $pdo,                           // Conexión a base de datos
        $_POST['email'] ?? '',          // Email ingresado por usuario
        $_POST['password'] ?? ''        // Contraseña ingresada por usuario
    );
    
    if ($login_exitoso) {
        // LOGIN EXITOSO: Redirigir al dashboard principal del admin
        header('Location: '.BASE_URL.'/'); 
        exit;
    }
    
    // LOGIN FALLIDO: Mostrar error y redirigir al formulario
    flash('err','Credenciales inválidas o usuario inactivo.');
    header('Location: login.php'); 
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Configuración básica de la página -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ingresar · <?=APP_NAME?></title>
    
    <!-- Hoja de estilos del panel administrativo -->
    <link rel="stylesheet" href="<?=ASSETS_URL?>/admin.css">
</head>
<body class="auth">
    <!-- FORMULARIO DE LOGIN -->
    <form method="post" class="card">
        <h1>Ingresar</h1>
        
        <!-- Campo CSRF para protección contra ataques -->
        <?=csrf_field()?>
        
        <!-- CAMPOS DE AUTENTICACIÓN -->
        <label>Email 
            <input type="email" name="email" required>
        </label>
        
        <label>Contraseña 
            <input type="password" name="password" required>
        </label>
        
        <!-- Botón de envío -->
        <button class="btn">Entrar</button>
        
        <!-- MENSAJE DE ERROR (si login falla) -->
        <?php if ($m=flash('err')): ?>
            <div class="alert err"><?=$m?></div>
        <?php endif; ?>
        
        <!-- Enlace a página de registro para nuevos usuarios -->
        <p class="muted">¿Sin cuenta? <a href="register.php">Registrate</a></p>
    </form>
</body>
</html>
