<?php
require __DIR__ . '/../../core/config.php';
require_any(['admin','root']); // solo admin o root acceden

// Obtener usuarios
$stmt = $pdo->query("
    SELECT u.id, u.nombre, u.email, u.estado, u.creado_at,
           GROUP_CONCAT(r.slug SEPARATOR ', ') AS roles
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r ON r.id = ur.role_id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$usuarios = $stmt->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>

<h2 class="h2">Gestión de Usuarios</h2>
<a class="btn" href="edit.php">➕ Nuevo usuario</a>

<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Email</th>
      <th>Estado</th>
      <th>Roles</th>
      <th>Creado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($usuarios as $u): ?>
      <tr>
        <td><?= e($u['id']) ?></td>
        <td><?= e($u['nombre']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td>
          <?php if ($u['estado']==='ACTIVO'): ?>
            <span class="badge ok">Activo</span>
          <?php elseif ($u['estado']==='PENDIENTE'): ?>
            <span class="badge warn">Pendiente</span>
          <?php else: ?>
            <span class="badge err">Bloqueado</span>
          <?php endif; ?>
        </td>
        <td><?= e($u['roles'] ?: '-') ?></td>
        <td><?= e($u['creado_at']) ?></td>
        <td class="actions">
          <a class="btn-sm" href="edit.php?id=<?= $u['id'] ?>">✏️ Editar</a>
          <?php if (can_change_state()): ?>
            <a class="btn-sm" href="change_state.php?id=<?= $u['id'] ?>">🔄 Estado</a>
            <a class="btn-sm" href="assign_role.php?id=<?= $u['id'] ?>">🎭 Rol</a>
          <?php endif; ?>
          <?php if (can_delete_physical()): ?>
            <a class="btn-sm danger" href="delete.php?id=<?= $u['id'] ?>"
               onclick="return confirm('¿Eliminar definitivamente este usuario?')">🗑 Eliminar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
