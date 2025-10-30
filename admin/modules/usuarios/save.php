<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$email  = strtolower(trim($_POST['email'] ?? ''));

if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('err', 'Datos inválidos');
    header('Location: edit.php' . ($id ? '?id='.$id : ''));
    exit;
}

if ($id) {
    $sql = "UPDATE users SET nombre=?, email=? WHERE id=?";
    $pdo->prepare($sql)->execute([$nombre,$email,$id]);
    flash('ok', 'Usuario actualizado');
} else {
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if ($pass==='' || $pass!==$pass2) {
        flash('err','Las contraseñas no coinciden');
        header('Location: edit.php'); exit;
    }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (nombre,email,password_hash,estado) VALUES (?,?,?, 'PENDIENTE')";
    $pdo->prepare($sql)->execute([$nombre,$email,$hash]);
    flash('ok','Usuario creado con estado PENDIENTE');
}

header('Location: index.php');
