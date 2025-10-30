<?php
// Inicia la sesión para poder guardar datos del usuario
session_start();
// Variable para mostrar mensajes de error
$error = '';
// Incluye la configuración de la base de datos
require_once 'conexion.php';
// Si el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos ingresados por el usuario
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    // Si ambos campos están completos
    if ($usuario && $clave) {
        try {
            // Conexión a la base de datos
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Busca el usuario y su rol en la base de datos
            $stmt = $pdo->prepare("SELECT u.id, u.email, u.password_hash, r.id as role_id, r.nombre as role_name FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE u.email = ?");
            $stmt->execute([$usuario]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verifica la contraseña
            if ($userRow && password_verify($clave, $userRow['password_hash'])) {
                // Si es correcto, guarda los datos en la sesión y redirige al inicio
                $_SESSION['usuario'] = $userRow['email'];
                $_SESSION['user_id'] = $userRow['id'];
                $_SESSION['role_id'] = $userRow['role_id'];
                $_SESSION['role_name'] = $userRow['role_name'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Enlaza los estilos principales -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/naty.css">
    <style>
        /* Estilos para el formulario de login */
        body { background: #f7f7f7; }
        .login-container {
            max-width: 370px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 2.5em 2em 2em 2em;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 1.5em;
            color: #222;
        }
        .login-container label {
            font-weight: bold;
            color: #222;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 0.7em;
            margin-bottom: 1.2em;
            border: 1px solid #bbb;
            border-radius: 8px;
            font-size: 1em;
        }
        .show-pass {
            display: flex;
            align-items: center;
            margin-bottom: 1.2em;
        }
        .show-pass input[type="checkbox"] {
            margin-right: 8px;
        }
        .login-container button {
            width: 100%;
            background: #1a4fff;
            color: #fff;
            border: none;
            padding: 0.8em;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-container button:hover {
            background: #0d2e8b;
        }
        .login-container .error {
            color: #c00;
            text-align: center;
            margin-bottom: 1em;
        }
        .register-link {
            display: block;
            margin-top: 1.5em;
            text-align: center;
        }
        .register-link a {
            color: #1a4fff;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.2s;
        }
        .register-link a:hover {
            color: #0d2e8b;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <!-- Muestra el mensaje de error si existe -->
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <!-- Formulario de login -->
        <form method="post">
            <label for="usuario">Correo electrónico</label>
            <input type="text" name="usuario" id="usuario" required autofocus>
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" required>
            <div class="show-pass">
                <input type="checkbox" id="togglePass">
                <label for="togglePass" class="toggle-pass-label">Mostrar contraseña</label>
            </div>
            <button type="submit">Entrar</button>
        </form>
        <!-- Enlace para registrarse -->
        <div class="register-link">
            <a href="register.php">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
    <script>
        // Script para mostrar/ocultar la contraseña
        const passInput = document.getElementById('clave');
        const togglePass = document.getElementById('togglePass');
        togglePass.addEventListener('change', function() {
            passInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
