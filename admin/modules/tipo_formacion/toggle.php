<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$estado = $pdo->query("SELECT estado FROM tipo_formacion WHERE id=$id")->fetchColumn();
if ($estado === false) { flash('err','Tipo no encontrado'); header('Location:index.php'); exit; }

$new = ($estado === 'A') ? 'I' : 'A';
$pdo->prepare("UPDATE tipo_formacion SET estado=? WHERE id=?")->execute([$new, $id]);

flash('ok', 'Estado actualizado');
header('Location: index.php');
