<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);   // admin y root pueden "eliminar" (lógico)
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$pdo->prepare("UPDATE tipo_formacion SET estado='I' WHERE id=?")->execute([$id]);
flash('ok','Tipo marcado como Inactivo');
header('Location: index.php');
