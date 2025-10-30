<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = null;

if ($id) {
    $st = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $st->execute([$id]);
    $item = $st->fetch();
}

include __DIR__ . '/../../partials/layout.php';
?>

<h2 class="h2"><?= $id ? 'Editar' : 'Nuevo' ?> usuario</h2>

<form method="post" action="save.php">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">

  <label>Nombre
    <input type="text" name="nombre" required value="<?= e($item['nombre'] ?? '') ?>">
  </label>

  <label>Email
    <input type="email" name="email" required value="<?= e($item['email'] ?? '') ?>">
  </label>

  <?php if (!$id): ?>
    <label>Contraseña
      <input type="password" name="password" required>
    </label>
    <label>Repetir contraseña
      <input type="password" name="password2" required>
    </label>
  <?php endif; ?>

  <button class="btn">Guardar</button>
</form>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
