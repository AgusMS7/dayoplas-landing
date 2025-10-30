<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$est = $pdo->query("SELECT estado FROM formacion WHERE id=$id")->fetchColumn();
if ($est===false) { flash('err','No encontrada'); header('Location:index.php'); exit; }

$new = ($est==='A')?'I':'A';
$pdo->prepare("UPDATE formacion SET estado=? WHERE id=?")->execute([$new,$id]);

flash('ok','Estado actualizado');
header('Location: index.php');
