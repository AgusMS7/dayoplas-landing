<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

$formacion_id = (int)($_POST['formacion_id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$boton = trim($_POST['boton'] ?? '');
$idioma = 'es';

if (!$formacion_id || $titulo==='') {
  flash('err','Datos incompletos'); header('Location: edit.php?formacion_id='.$formacion_id); exit;
}

$sql = "INSERT INTO formacion_trad (formacion_id, idioma_id, titulo, descripcion, boton, estado)
        VALUES (?, ?, ?, ?, ?, 'A')
        ON DUPLICATE KEY UPDATE
          titulo=VALUES(titulo),
          descripcion=VALUES(descripcion),
          boton=VALUES(boton),
          estado='A'";
$pdo->prepare($sql)->execute([$formacion_id, $idioma, $titulo, $descripcion, $boton ?: null]);

flash('ok','Texto guardado (es)');
header('Location: ../formacion/index.php');
