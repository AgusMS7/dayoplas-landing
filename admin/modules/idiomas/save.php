<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

/* Forzamos único idioma 'es' y estado activo */
$id = 'es';
$nombre  = trim($_POST['nombre'] ?? 'Español');
$bandera = trim($_POST['bandera'] ?? '');

if ($nombre === '') {
  flash('err','El nombre es obligatorio.');
  header('Location: index.php'); exit;
}

$sql = "
  INSERT INTO idioma (id, nombre, bandera, estado)
  VALUES ('es', ?, ?, 'A')
  ON DUPLICATE KEY UPDATE
    nombre=VALUES(nombre),
    bandera=VALUES(bandera),
    estado='A'
";
$pdo->prepare($sql)->execute([$nombre, $bandera ?: null]);

flash('ok','Idioma Español (es) guardado y activo.');
header('Location: index.php');
