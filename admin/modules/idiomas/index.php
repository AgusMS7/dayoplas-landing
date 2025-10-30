<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']); // solo admin/root

// Traer el idioma 'es'
$st = $pdo->prepare("SELECT id, nombre, bandera, estado FROM idioma WHERE id='es' LIMIT 1");
$st->execute();
$idioma = $st->fetch();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Idioma del sistema</h2>

<?php if (!$idioma): ?>
  <div class="alert warn">Aún no existe el idioma <strong>es</strong>. Crealo con el botón de abajo.</div>
  <form method="post" action="save.php" class="card" style="max-width:520px">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="es">
    <label>Nombre
      <input name="nombre" value="Español" required>
    </label>
    <label>Bandera (archivo SVG/PNG)
      <input name="bandera" value="argentina.svg" placeholder="argentina.svg">
    </label>
    <button class="btn">Crear Español (es)</button>
  </form>
<?php else: ?>
  <table class="table">
    <thead>
      <tr><th>Código</th><th>Nombre</th><th>Bandera</th><th>Estado</th></tr>
    </thead>
    <tbody>
      <tr>
        <td><?= e($idioma['id']) ?></td>
        <td><?= e($idioma['nombre']) ?></td>
        <td><?= e($idioma['bandera'] ?? '') ?></td>
        <td><span class="badge ok">Activo</span></td>
      </tr>
    </tbody>
  </table>

  <h3 class="h2" style="margin-top:16px">Editar Español</h3>
  <form method="post" action="save.php" class="card" style="max-width:520px">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="es">
    <label>Nombre
      <input name="nombre" value="<?= e($idioma['nombre']) ?>" required>
    </label>
    <label>Bandera (archivo SVG/PNG)
      <input name="bandera" value="<?= e($idioma['bandera'] ?? '') ?>" placeholder="argentina.svg">
    </label>
    <button class="btn">Guardar</button>
  </form>
<?php endif; ?>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
