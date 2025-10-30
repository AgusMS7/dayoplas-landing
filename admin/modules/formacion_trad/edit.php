<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

$formacion_id = (int)($_GET['formacion_id'] ?? 0);
if (!$formacion_id) { flash('err','Falta formación'); header('Location:index.php'); exit; }

$base = $pdo->prepare("SELECT f.*, tf.clave AS tipo FROM formacion f LEFT JOIN tipo_formacion tf ON tf.id=f.tipo_formacion_id WHERE f.id=?");
$base->execute([$formacion_id]);
$f = $base->fetch();
if (!$f) { flash('err','Formación no encontrada'); header('Location:index.php'); exit; }

$st = $pdo->prepare("SELECT titulo, descripcion, boton FROM formacion_trad WHERE formacion_id=? AND idioma_id='es'");
$st->execute([$formacion_id]);
$trad = $st->fetch();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Texto (es) · <?= e($f['tipo'].' · '.$f['slug']) ?></h2>

<form method="post" action="save.php" class="card" style="max-width:900px">
  <?= csrf_field() ?>
  <input type="hidden" name="formacion_id" value="<?=$formacion_id?>">
  <label>Título
    <input name="titulo" required value="<?= e($trad['titulo'] ?? '') ?>">
  </label>
  <label>Descripción
    <textarea name="descripcion" rows="6" required><?= e($trad['descripcion'] ?? '') ?></textarea>
  </label>
  <label>Texto del botón
    <input name="boton" value="<?= e($trad['boton'] ?? 'Inscribite') ?>">
  </label>
  <button class="btn">Guardar</button>
</form>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
