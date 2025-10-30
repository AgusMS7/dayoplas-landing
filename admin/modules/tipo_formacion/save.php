<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$clave = trim($_POST['clave'] ?? '');
$icono = trim($_POST['icono'] ?? '');
$imagen_cabecera = trim($_POST['imagen_cabecera'] ?? '');
$estado = $_POST['estado'] ?? 'A';

if ($clave === '') {
  flash('err','La clave es obligatoria');
  header('Location: '.($id?'edit.php?id='.$id:'edit.php')); exit;
}

if ($id) {
  $sql = "UPDATE tipo_formacion
          SET clave=?, icono=?, imagen_cabecera=?, estado=?
          WHERE id=?";
  $pdo->prepare($sql)->execute([$clave ?: null, $icono ?: null, $imagen_cabecera ?: null, $estado, $id]);
  flash('ok','Tipo actualizado');
} else {
  $sql = "INSERT INTO tipo_formacion (clave, icono, imagen_cabecera, estado)
          VALUES (?, ?, ?, 'A')";
  $pdo->prepare($sql)->execute([$clave ?: null, $icono ?: null, $imagen_cabecera ?: null]);
  flash('ok','Tipo creado');
}

header('Location: index.php');
