<?php
require __DIR__ . '/../../core/config.php';
require_any(['root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
flash('ok','Usuario eliminado definitivamente');
header('Location: index.php');
