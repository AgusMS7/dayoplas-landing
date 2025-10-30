<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

/* Datos */
$id_tipo_formacion = (int)($_POST['id_tipo_formacion'] ?? 0);
$idioma            = 'es'; // fijo
$titulo            = trim($_POST['titulo'] ?? '');
$subtitulo         = trim($_POST['subtitulo'] ?? '');
$descripcion_html  = trim($_POST['descripcion_html'] ?? '');
$pie_html          = trim($_POST['pie_html'] ?? '');
$descripcion_larga = trim($_POST['descripcion_larga'] ?? '');
$estado            = $_POST['estado'] ?? 'A';

if (!$id_tipo_formacion || $titulo==='') {
  flash('err','Datos incompletos'); header('Location: edit.php?id_tipo_formacion='.$id_tipo_formacion); exit;
}

/* NOT NULL en esquema: subtitulo, pie_html → aseguramos string vacío si no mandan nada. */
/* Campos según tabla real: tipo_formacion_traduccion. */
$sql = "INSERT INTO tipo_formacion_traduccion
          (id_tipo_formacion, idioma, titulo, subtitulo, descripcion_html, pie_html, descripcion_larga, estado)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
          titulo=VALUES(titulo),
          subtitulo=VALUES(subtitulo),
          descripcion_html=VALUES(descripcion_html),
          pie_html=VALUES(pie_html),
          descripcion_larga=VALUES(descripcion_larga),
          estado=VALUES(estado)";

$pdo->prepare($sql)->execute([
  $id_tipo_formacion, $idioma, $titulo,
  $subtitulo, ($descripcion_html !== '' ? $descripcion_html : null),
  $pie_html, ($descripcion_larga !== '' ? $descripcion_larga : null),
  $estado
]);

flash('ok','Texto guardado (es)');
header('Location: index.php');
