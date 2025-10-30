<?php
require __DIR__ . '/../../core/config.php';
require_login();

$rows = $pdo->query("
  SELECT id, clave, icono, imagen_cabecera, estado
  FROM tipo_formacion
  ORDER BY id DESC
")->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Tipos de formación</h2>

<div style="margin-bottom:10px">
  <?php if (can_edit()): ?>
    <a class="btn" href="edit.php">➕ Nuevo tipo</a>
  <?php endif; ?>
</div>

<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Clave</th>
      <th>Ícono</th>
      <th>Imagen cabecera</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['id']) ?></td>
        <td><?= e($r['clave']) ?></td>
        <td><?= e($r['icono']) ?></td>
        <td><?= e($r['imagen_cabecera']) ?></td>
        <td>
          <?php if ($r['estado']==='A'): ?>
            <span class="badge ok">Activo</span>
          <?php else: ?>
            <span class="badge err">Inactivo</span>
          <?php endif; ?>
        </td>
        <td class="actions">
          <?php if (can_edit()): ?>
            <a class="btn-sm" href="edit.php?id=<?= $r['id'] ?>">✏️ Editar</a>
          <?php endif; ?>

          <?php if (can_change_state()): ?>
            <a class="btn-sm" href="toggle.php?id=<?= $r['id'] ?>">🔄 <?= $r['estado']==='A' ? 'Desactivar' : 'Activar' ?></a>
            <a class="btn-sm danger" href="delete.php?id=<?= $r['id'] ?>"
               onclick="return confirm('¿Eliminar lógicamente este tipo? (quedará Inactivo)')">🗑 Eliminar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
