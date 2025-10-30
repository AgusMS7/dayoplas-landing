<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id_tipo_formacion = (int)($_GET['id_tipo_formacion'] ?? 0);
if (!$id_tipo_formacion) { header('Location:index.php'); exit; }

$est = $pdo->prepare("SELECT estado FROM tipo_formacion_traduccion WHERE id_tipo_formacion=? AND idioma='es'");
$est->execute([$id_tipo_formacion]);
$estado = $est->fetchColumn();

if ($estado === false) {
  // Si no existe aún, crear registro mínimo activo (subtitulo/pie_html NOT NULL -> vacíos)
  $pdo->prepare("INSERT INTO tipo_formacion_traduccion
    (id_tipo_formacion, idioma, titulo, subtitulo, descripcion_html, pie_html, descripcion_larga, estado)
    VALUES (?, 'es', '', '', NULL, '', NULL, 'A')")
    ->execute([$id_tipo_formacion]);
  flash('ok','Texto creado y activado (es)');
  header('Location:index.php'); exit;
}

$new = ($estado==='A') ? 'I' : 'A';
$pdo->prepare("UPDATE tipo_formacion_traduccion SET estado=? WHERE id_tipo_formacion=? AND idioma='es'")
    ->execute([$new, $id_tipo_formacion]);

flash('ok','Estado actualizado');
header('Location:index.php');
