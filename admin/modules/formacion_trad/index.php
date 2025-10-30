<?php
require __DIR__ . '/../../core/config.php';
require_login();

$rows = $pdo->query("
  SELECT f.id, f.slug, tf.clave AS tipo, ft.titulo, ft.descripcion, ft.boton
  FROM formacion f
  LEFT JOIN tipo_formacion tf ON tf.id=f.tipo_formacion_id
  LEFT JOIN formacion_trad ft ON ft.formacion_id=f.id AND ft.idioma_id='es'
  ORDER BY f.id DESC
")->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">Textos de formación (es)</h2>
<table class="table">
  <thead><tr><th>ID</th><th>Tipo</th><th>Slug</th><th>Título</th><th>Acciones</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?=e($r['id'])?></td>
      <td><?=e($r['tipo']??'-')?></td>
      <td><?=e($r['slug'])?></td>
      <td><?=e($r['titulo']??'—')?></td>
      <td class="actions">
        <a class="btn-sm" href="edit.php?formacion_id=<?=$r['id']?>">📝 Editar</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../../partials/_end.php'; ?>
