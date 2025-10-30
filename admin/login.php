<?php
session_start();
require_once '../conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Si ya está logueado como administrador, redirigir al panel CRUD
if (isset($_SESSION['user_id']) && $_SESSION['role_name'] === 'Administrador') {
    header('Location: panel_crud.php');
    exit();
}

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si existe la tabla users y crear usuario admin si no existe
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role_name = 'Administrador' AND estado = 'ACTIVO'");
    $admin_count = $stmt->fetchColumn();
    
    if ($admin_count == 0) {
        // Verificar si hay administradores pendientes y activarlos automáticamente
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role_name = 'Administrador' AND estado = 'PENDIENTE'");
        $pending_admin = $stmt->fetchColumn();
        
        if ($pending_admin > 0) {
            // Activar automáticamente administradores pendientes
            $activate_sql = "UPDATE users SET estado = 'ACTIVO' WHERE role_name = 'Administrador' AND estado = 'PENDIENTE'";
            $pdo->exec($activate_sql);
            $mensaje = 'Administradores activados automáticamente. Puedes iniciar sesión ahora.';
            $tipo_mensaje = 'success';
        } else {
            // Crear usuario administrador por defecto ya ACTIVO
            $admin_email = 'admin@dayloplas.com';
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $admin_name = 'Administrador';
            
            $insert_sql = "INSERT INTO users (nombre, email, password, role_name, estado) VALUES (?, ?, ?, 'Administrador', 'ACTIVO')";
            $stmt = $pdo->prepare($insert_sql);
            $stmt->execute([$admin_name, $admin_email, $admin_password]);
            
            $mensaje = 'Usuario administrador creado y activado exitosamente. Email: admin@dayloplas.com - Contraseña: admin123';
            $tipo_mensaje = 'success';
        }
    }
} catch (PDOException $e) {
    // Si hay error con la tabla users, mostrar mensaje informativo
    $mensaje = 'Nota: Verifica que la base de datos esté configurada correctamente.';
    $tipo_mensaje = 'info';
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $mensaje = 'Por favor ingresa email y contraseña.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, nombre, email, password, role_name FROM users WHERE email = ? AND estado = 'ACTIVO'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['role_name'] === 'Administrador') {
                    // Login exitoso
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['nombre'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_name'] = $user['role_name'];
                    
                    header('Location: panel_crud.php');
                    exit();
                } else {
                    $mensaje = 'Solo los administradores pueden acceder a esta sección.';
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'Email o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el login: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login Administrador - DAYLOPLAS-IPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4CAF50, #45a049);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 4em;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #666;
            font-size: 0.9em;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .input-group {
            position: relative;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
        }
        
        .default-credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #4CAF50;
            margin-bottom: 20px;
        }
        
        .default-credentials h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
        
        .default-credentials p {
            color: #666;
            font-size: 0.85em;
            margin: 5px 0;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .footer-links a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #45a049;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 1.5em;
            }
            
            .logo i {
                font-size: 3em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-user-shield"></i>
            <h1>Panel Administrativo</h1>
            <p>DAYLOPLAS-IPM</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert <?= $tipo_mensaje ?>">
                <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'error' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="default-credentials">
            <h4><i class="fas fa-key"></i> Credenciales por Defecto:</h4>
            <p><strong>Email:</strong> admin@dayloplas.com</p>
            <p><strong>Contraseña:</strong> admin123</p>
        </div>

        <form method="POST" autocomplete="off">
            <!-- Campos invisibles para confundir al autocompletado -->
            <input type="text" name="fake_username" autocomplete="username" style="display:none;" tabindex="-1">
            <input type="password" name="fake_password" autocomplete="current-password" style="display:none;" tabindex="-1">
            
            <div class="form-group">
                <label for="user_login_field">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <div class="input-group">
                    <input type="email" 
                           id="user_login_field" 
                           name="email" 
                           required 
                           placeholder="Ingresa tu email"
                           autocomplete="new-password"
                           value=""
                           data-form-type="other"
                           readonly onfocus="this.removeAttribute('readonly');"
                           data-lpignore="true"
                           data-form-type="other">
                </div>
            </div>

            <div class="form-group">
                <label for="user_pass_field">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <div class="input-group">
                    <input type="password" 
                           id="user_pass_field" 
                           name="password" 
                           required 
                           placeholder="Ingresa tu contraseña"
                           autocomplete="new-password"
                           value=""
                           data-form-type="other"
                           readonly onfocus="this.removeAttribute('readonly');"
                           data-lpignore="true"
                           data-form-type="other">
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>

        <div class="footer-links">
            <a href="register.php">
                <i class="fas fa-user-plus"></i> ¿No tienes cuenta? Regístrate como Administrador
            </a>
            <br><br>
            <a href="../index.php">
                <i class="fas fa-home"></i> Volver al Sitio Principal
            </a>
        </div>
    </div>

    <script>
        // FUNCIÓN ULTRA-AGRESIVA PARA LIMPIAR CAMPOS
        function limpiezaTotalCampos() {
            const emailField = document.getElementById('user_login_field');
            const passwordField = document.getElementById('user_pass_field');
            
            if (emailField) {
                emailField.value = '';
                emailField.setAttribute('value', '');
                emailField.defaultValue = '';
                emailField.removeAttribute('value');
            }
            if (passwordField) {
                passwordField.value = '';
                passwordField.setAttribute('value', '');
                passwordField.defaultValue = '';
                passwordField.removeAttribute('value');
            }
        }
        
        // FORZAR LIMPIEZA INMEDIATA Y REPETITIVA
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar inmediatamente
            limpiezaTotalCampos();
            
            // Limpiar cada 100ms durante los primeros 5 segundos
            let contador = 0;
            const limpiadorIntensivo = setInterval(function() {
                limpiezaTotalCampos();
                contador++;
                if (contador > 50) { // 50 x 100ms = 5 segundos
                    clearInterval(limpiadorIntensivo);
                }
            }, 100);
            
            // Focus después de la limpieza
            setTimeout(function() {
                document.getElementById('user_login_field').focus();
            }, 200);
        });
        
        // LIMPIAR CUANDO LA PÁGINA SE HACE VISIBLE
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(limpiezaTotalCampos, 50);
            }
        });
        
        // LIMPIAR EN CADA EVENTO POSIBLE
        ['load', 'pageshow', 'focus'].forEach(function(evento) {
            window.addEventListener(evento, function() {
                setTimeout(limpiezaTotalCampos, 50);
            });
        });
        
        // LIMPIAR CUANDO SE HACE FOCUS EN LOS CAMPOS
        setTimeout(function() {
            const emailField = document.getElementById('user_login_field');
            const passwordField = document.getElementById('user_pass_field');
            
            if (emailField) {
                emailField.addEventListener('focus', function() {
                    setTimeout(limpiezaTotalCampos, 10);
                });
                emailField.addEventListener('input', function() {
                    // Si el campo se llena automáticamente, limpiarlo después de un momento
                    if (this.value && !this.dataset.userTyped) {
                        setTimeout(limpiezaTotalCampos, 100);
                    }
                });
                emailField.addEventListener('keydown', function() {
                    this.dataset.userTyped = 'true';
                });
            }
            
            if (passwordField) {
                passwordField.addEventListener('focus', function() {
                    setTimeout(limpiezaTotalCampos, 10);
                });
                passwordField.addEventListener('input', function() {
                    if (this.value && !this.dataset.userTyped) {
                        setTimeout(limpiezaTotalCampos, 100);
                    }
                });
                passwordField.addEventListener('keydown', function() {
                    this.dataset.userTyped = 'true';
                });
            }
        }, 100);
        
        // LIMPIAR CADA 30 SEGUNDOS DE FORMA PERMANENTE
        setInterval(function() {
            limpiezaTotalCampos();
        }, 30000);
        
        // LIMPIAR AL SALIR
        window.addEventListener('beforeunload', function() {
            limpiezaTotalCampos();
        });
        
        // LIMPIAR SI HAY ERROR DE LOGIN
        <?php if ($tipo_mensaje === 'error'): ?>
        setTimeout(function() {
            limpiezaTotalCampos();
        }, 3000);
        <?php endif; ?>
        
        // OBSERVER PARA DETECTAR CAMBIOS AUTOMÁTICOS EN LOS CAMPOS
        setTimeout(function() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        setTimeout(limpiezaTotalCampos, 50);
                    }
                });
            });
            
            const emailField = document.getElementById('user_login_field');
            const passwordField = document.getElementById('user_pass_field');
            
            if (emailField) {
                observer.observe(emailField, { attributes: true });
            }
            if (passwordField) {
                observer.observe(passwordField, { attributes: true });
            }
        }, 200);
        
        // Animación de entrada
        document.querySelector('.login-container').style.opacity = '0';
        document.querySelector('.login-container').style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            document.querySelector('.login-container').style.transition = 'all 0.6s ease';
            document.querySelector('.login-container').style.opacity = '1';
            document.querySelector('.login-container').style.transform = 'translateY(0)';
        }, 100);
    </script>
</body>
</html>