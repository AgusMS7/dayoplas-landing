<?php
require __DIR__ . '/../../core/config.php';
require_login();

$rows = $pdo->query("
  SELECT tf.id, tf.clave,
         tft.titulo, tft.subtitulo, tft.estado
  FROM tipo_formacion tf
  LEFT JOIN tipo_formacion_traduccion tft
    ON tft.id_tipo_formacion = tf.id AND tft.idioma = 'es'
  ORDER BY tf.id DESC
")->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Textos por tipo de formación (es)</h2>

<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Clave</th>
      <th>Título (es)</th>
      <th>Subtítulo</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?= e($r['id']) ?></td>
      <td><?= e($r['clave']) ?></td>
      <td><?= e($r['titulo'] ?? '—') ?></td>
      <td><?= e($r['subtitulo'] ?? '—') ?></td>
      <td><?= ($r['estado'] ?? 'A')==='A' ? '<span class="badge ok">Activo</span>' : '<span class="badge err">Inactivo</span>' ?></td>
      <td class="actions">
        <?php if (can_edit()): ?>
          <a class="btn-sm" href="edit.php?id_tipo_formacion=<?= $r['id'] ?>">📝 Editar</a>
        <?php endif; ?>
        <?php if (can_change_state()): ?>
          <a class="btn-sm" href="toggle.php?id_tipo_formacion=<?= $r['id'] ?>">🔄 Estado</a>
          <a class="btn-sm danger" href="delete.php?id_tipo_formacion=<?= $r['id'] ?>"
             onclick="return confirm('¿Marcar como Inactivo el texto (es) de este tipo?')">🗑 Eliminar</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
