<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

// obtener usuario
$st = $pdo->prepare("SELECT * FROM users WHERE id=?");
$st->execute([$id]);
$user = $st->fetch();

if (!$user) { flash('err','Usuario no encontrado'); header('Location:index.php'); exit; }

// roles disponibles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

// roles actuales
$st2 = $pdo->prepare("SELECT r.id FROM user_roles ur JOIN roles r ON r.id=ur.role_id WHERE ur.user_id=?");
$st2->execute([$id]);
$roles_actuales = array_column($st2->fetchAll(),'id');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nuevo_rol = (int)($_POST['rol_id'] ?? 0);

    if ($nuevo_rol) {
        // eliminar roles anteriores y asignar uno nuevo
        $pdo->prepare("DELETE FROM user_roles WHERE user_id=?")->execute([$id]);
        $pdo->prepare("INSERT INTO user_roles(user_id,role_id) VALUES (?,?)")->execute([$id,$nuevo_rol]);
        flash('ok','Rol actualizado');
    }
    header('Location: index.php'); exit;
}

include __DIR__ . '/../../partials/layout.php';
?>

<h2 class="h2">Asignar rol a <?= e($user['nombre']) ?></h2>

<form method="post">
  <?= csrf_field() ?>
  <label>Rol
    <select name="rol_id" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($roles as $r): ?>
        <?php if ($r['slug']==='root' && !has_role('root')) continue; ?>
        <option value="<?= $r['id'] ?>" <?= in_array($r['id'],$roles_actuales)?'selected':'' ?>>
          <?= e($r['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="btn">Guardar</button>
</form>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
