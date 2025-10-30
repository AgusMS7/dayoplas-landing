<?php
// Incluye la configuración de la base de datos y el modelo de acceso a datos
require_once 'conexion.php';
require_once 'model_daylo.php';

// Variable para mostrar mensajes al usuario
$mensaje = '';
// Definición de los roles disponibles para el registro
$roles = [
    1 => 'Administrador',
    2 => 'Usuario'
];

// Si el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos enviados por el usuario
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $rol_id = $_POST['rol_id'] ?? 2;

    // Valida que los campos estén completos y el rol exista
    if ($correo && $clave && isset($roles[$rol_id])) {
        try {
            // Conexión a la base de datos
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Verifica si el correo ya está registrado
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$correo]);
            if ($stmt->fetch()) {
                $mensaje = 'El correo ya está registrado.';
            } else {
                // Inserta el nuevo usuario con la contraseña encriptada
                $hash = password_hash($clave, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
                $stmt->execute([$correo, $hash]);
                $user_id = $pdo->lastInsertId();
                // Asigna el rol seleccionado al usuario
                $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $rol_id]);
                $mensaje = 'Usuario registrado correctamente.';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
        }
    } else {
        $mensaje = 'Completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/naty.css">
    <style>
        body { background: #f7f7f7; }
        .register-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 2.5em 2em 2em 2em;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 1.5em;
            color: #222;
        }
        .register-container label {
            font-weight: bold;
            color: #222;
        }
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container select {
            width: 100%;
            padding: 0.7em;
            margin-bottom: 1.2em;
            border: 1px solid #bbb;
            border-radius: 8px;
            font-size: 1em;
        }
        .register-container button {
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
        .register-container button:hover {
            background: #0d2e8b;
        }
        .register-container .mensaje {
            color: #c00;
            text-align: center;
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="correo">Correo electrónico</label>
            <input type="email" name="correo" id="correo" required autofocus>
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" required>
            <label for="rol_id">Rol</label>
            <select name="rol_id" id="rol_id" required>
                <?php foreach ($roles as $id => $nombre): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nombre) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
