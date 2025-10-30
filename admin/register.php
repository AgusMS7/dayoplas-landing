<?php
session_start();
require_once '../conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar registro con activación automática para administradores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'Usuario'; // Por defecto Usuario, pero puede ser Administrador
    
    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'error';
    } elseif ($password !== $confirm_password) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'error';
    } elseif (strlen($password) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
        $tipo_mensaje = 'error';
    } else {
        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $mensaje = 'Este email ya está registrado.';
                $tipo_mensaje = 'error';
            } else {
                // Determinar el estado según el rol
                $estado = ($role === 'Administrador') ? 'ACTIVO' : 'PENDIENTE';
                
                // Registrar usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_sql = "INSERT INTO users (nombre, email, password, role_name, estado) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($insert_sql);
                $stmt->execute([$nombre, $email, $hashed_password, $role, $estado]);
                
                if ($role === 'Administrador') {
                    $mensaje = '¡Administrador registrado y activado exitosamente! Puedes iniciar sesión inmediatamente.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Usuario registrado exitosamente. Tu cuenta está pendiente de activación.';
                    $tipo_mensaje = 'success';
                }
            }
        } catch (PDOException $e) {
            $mensaje = 'Error al registrar: ' . $e->getMessage();
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
    <title>Registro de Usuario - DAYLOPLAS-IPM</title>
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
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }
        
        .register-container::before {
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
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .role-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 5px solid #4CAF50;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        
        .role-info.admin {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        
        .btn-register {
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
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
        }
        
        .footer-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .footer-links a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #45a049;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <i class="fas fa-user-plus"></i>
            <h1>Registro de Usuario</h1>
            <p>DAYLOPLAS-IPM</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert <?= $tipo_mensaje ?>">
                <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">
                        <i class="fas fa-user"></i> Nombre Completo
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           required 
                           value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>"
                           placeholder="Ingresa tu nombre completo">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                           placeholder="Ingresa tu email">
                </div>

                <div class="form-group">
                    <label for="role">
                        <i class="fas fa-user-tag"></i> Tipo de Usuario
                    </label>
                    <select id="role" name="role" onchange="updateRoleInfo()">
                        <option value="Usuario" <?= (isset($_POST['role']) && $_POST['role'] === 'Usuario') ? 'selected' : '' ?>>Usuario Normal</option>
                        <option value="Administrador" <?= (isset($_POST['role']) && $_POST['role'] === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>

                <div id="role-info" class="role-info">
                    <strong><i class="fas fa-info-circle"></i> Usuario Normal:</strong>
                    Tu cuenta quedará pendiente de activación por un administrador.
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           minlength="6"
                           placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirmar Contraseña
                    </label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required 
                           minlength="6"
                           placeholder="Confirma tu contraseña">
                </div>
            </div>

            <button type="submit" name="register" class="btn-register">
                <i class="fas fa-user-plus"></i> Registrar Usuario
            </button>
        </form>

        <div class="footer-links">
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> ¿Ya tienes cuenta? Inicia sesión
            </a>
            <a href="../index.php">
                <i class="fas fa-home"></i> Ir al Sitio
            </a>
        </div>
    </div>

    <script>
        function updateRoleInfo() {
            const role = document.getElementById('role').value;
            const roleInfo = document.getElementById('role-info');
            
            if (role === 'Administrador') {
                roleInfo.className = 'role-info admin';
                roleInfo.innerHTML = '<strong><i class="fas fa-crown"></i> Administrador:</strong> Tu cuenta se activará automáticamente y tendrás acceso completo al sistema CRUD.';
            } else {
                roleInfo.className = 'role-info';
                roleInfo.innerHTML = '<strong><i class="fas fa-info-circle"></i> Usuario Normal:</strong> Tu cuenta quedará pendiente de activación por un administrador.';
            }
        }
        
        // Validación de contraseñas
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#4CAF50';
            }
        });
    </script>
</body>
</html>