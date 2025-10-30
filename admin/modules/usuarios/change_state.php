<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$estado = $pdo->query("SELECT estado FROM users WHERE id=$id")->fetchColumn();

$new = 'PENDIENTE';
if ($estado === 'PENDIENTE') $new = 'ACTIVO';
elseif ($estado === 'ACTIVO') $new = 'BLOQUEADO';
elseif ($estado === 'BLOQUEADO') $new = 'ACTIVO';

$pdo->prepare("UPDATE users SET estado=? WHERE id=?")->execute([$new,$id]);
flash('ok', "Estado cambiado a $new");
header('Location: index.php');
