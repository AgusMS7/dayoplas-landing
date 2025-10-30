<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id_tipo_formacion = (int)($_GET['id_tipo_formacion'] ?? 0);
if (!$id_tipo_formacion) { header('Location:index.php'); exit; }

$pdo->prepare("UPDATE tipo_formacion_traduccion SET estado='I' WHERE id_tipo_formacion=? AND idioma='es'")
    ->execute([$id_tipo_formacion]);

flash('ok','Texto marcado como Inactivo (es)');
header('Location:index.php');
