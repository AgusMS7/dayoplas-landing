<?php
require __DIR__ . '/../../core/config.php';
require_any(['consultor','admin','root']);
csrf_check();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = null;

if ($id) {
  $st = $pdo->prepare("SELECT * FROM tipo_formacion WHERE id=?");
  $st->execute([$id]);
  $item = $st->fetch();
  if (!$item) { flash('err','Tipo no encontrado'); header('Location: index.php'); exit; }
}

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2"><?= $id ? 'Editar' : 'Nuevo' ?> tipo de formación</h2>

<form method="post" action="save.php" class="card" style="max-width:720px">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">

  <label>Clave (ej: CURSO, TALLER, JORNADA)
    <input name="clave" required value="<?= e($item['clave'] ?? '') ?>">
  </label>

  <label>Ícono (clase o nombre de archivo)
    <input name="icono" value="<?= e($item['icono'] ?? '') ?>" placeholder="ej: icono.svg o clase-css">
  </label>

  <label>Imagen de cabecera (ruta/URL)
    <input name="imagen_cabecera" value="<?= e($item['imagen_cabecera'] ?? '') ?>" placeholder="images/headers/curso.jpg">
  </label>

  <?php if ($id): ?>
    <label>Estado
      <select name="estado">
        <option value="A" <?= (($item['estado'] ?? '')==='A')?'selected':'' ?>>Activo</option>
        <option value="I" <?= (($item['estado'] ?? '')==='I')?'selected':'' ?>>Inactivo</option>
      </select>
    </label>
  <?php endif; ?>

  <button class="btn">Guardar</button>
</form>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
