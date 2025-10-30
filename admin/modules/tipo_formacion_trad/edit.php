<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

$id_tipo_formacion = (int)($_GET['id_tipo_formacion'] ?? 0);
if (!$id_tipo_formacion) { flash('err','Falta id_tipo_formacion'); header('Location:index.php'); exit; }

$base = $pdo->prepare("SELECT * FROM tipo_formacion WHERE id=?");
$base->execute([$id_tipo_formacion]);
$tf = $base->fetch();
if (!$tf) { flash('err','Tipo de formación no encontrado'); header('Location:index.php'); exit; }

$st = $pdo->prepare("
  SELECT titulo, subtitulo, descripcion_html, pie_html, descripcion_larga, estado
  FROM tipo_formacion_traduccion
  WHERE id_tipo_formacion=? AND idioma='es'
");
$st->execute([$id_tipo_formacion]);
$trad = $st->fetch();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Texto por tipo (es) · <?= e($tf['clave']) ?></h2>

<form method="post" action="save.php" class="card" style="max-width:1000px">
  <?= csrf_field() ?>
  <input type="hidden" name="id_tipo_formacion" value="<?=$id_tipo_formacion?>">
  <input type="hidden" name="idioma" value="es">

  <label>Título
    <input name="titulo" required value="<?= e($trad['titulo'] ?? '') ?>">
  </label>

  <label>Subtítulo
    <input name="subtitulo" value="<?= e($trad['subtitulo'] ?? '') ?>">
  </label>

  <label>Descripción (HTML)
    <textarea name="descripcion_html" rows="6"><?= e($trad['descripcion_html'] ?? '') ?></textarea>
  </label>

  <label>Pie (HTML)
    <textarea name="pie_html" rows="4"><?= e($trad['pie_html'] ?? '') ?></textarea>
  </label>

  <label>Descripción larga (opcional)
    <textarea name="descripcion_larga" rows="6"><?= e($trad['descripcion_larga'] ?? '') ?></textarea>
  </label>

  <?php if ($trad): ?>
    <label>Estado
      <select name="estado">
        <option value="A" <?= (($trad['estado'] ?? 'A')==='A')?'selected':'' ?>>Activo</option>
        <option value="I" <?= (($trad['estado'] ?? 'A')==='I')?'selected':'' ?>>Inactivo</option>
      </select>
    </label>
  <?php endif; ?>

  <button class="btn">Guardar</button>
</form>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
