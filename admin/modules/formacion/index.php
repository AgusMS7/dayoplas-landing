<?php
require __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/file_upload.php';
require_login();

$mostrar_inactivas = isset($_GET['inactivas']) && $_GET['inactivas'] === '1';

$sql = "
SELECT f.id, f.slug, f.imagen, f.recurso_pdf, f.fecha_inicio, f.estado,
       tf.clave AS tipo,
       ft.titulo
FROM formacion f
LEFT JOIN tipo_formacion tf ON tf.id=f.tipo_formacion_id
LEFT JOIN formacion_trad ft ON ft.formacion_id=f.id AND ft.idioma_id='es'
WHERE f.estado = '" . ($mostrar_inactivas ? 'I' : 'A') . "'
ORDER BY f.id DESC";
$rows = $pdo->query($sql)->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2">
  Formaciones <?= $mostrar_inactivas ? 'Inactivas' : 'Activas' ?>
  <span style="font-size: 0.7em; color: #666;">
    (<?= count($rows) ?> registros)
  </span>
</h2>

<div style="margin-bottom:15px; display: flex; gap: 10px; align-items: center;">
  <?php if (can_edit()): ?>
    <a class="btn" href="edit.php">➕ Nueva formación</a>
  <?php endif; ?>
  
  <div style="margin-left: auto; display: flex; gap: 8px;">
    <?php if (!$mostrar_inactivas): ?>
      <a class="btn-sm" href="?inactivas=1" style="background: #95a5a6;">
        👁️ Ver Inactivas
      </a>
    <?php else: ?>
      <a class="btn-sm" href="?" style="background: #27ae60;">
        ✅ Ver Activas
      </a>
    <?php endif; ?>
  </div>
</div>

<table class="table">
  <thead>
    <tr>
      <th>ID</th><th>Imagen</th><th>PDF</th><th>Tipo</th><th>Slug</th><th>Título (es)</th><th>Inicio</th><th>Estado</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?= e($r['id']) ?></td>
      <td style="text-align: center; padding: 8px;">
        <?php if ($r['imagen']): ?>
          <img src="<?= get_image_url($r['imagen']) ?>" 
               alt="Imagen de <?= e($r['slug']) ?>" 
               style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
               title="<?= e($r['imagen']) ?>">
          <span style="display: none; font-size: 0.8em; color: #999; padding: 2px 4px; background: #f0f0f0; border-radius: 3px;">Sin imagen</span>
        <?php else: ?>
          <span style="font-size: 0.8em; color: #999; padding: 2px 4px; background: #f0f0f0; border-radius: 3px;">Sin imagen</span>
        <?php endif; ?>
      </td>
      <td style="text-align: center; padding: 8px;">
        <?php if ($r['recurso_pdf']): ?>
          <span style="color: #27ae60; font-size: 1.1em; padding: 4px 8px; background: #d5f4e6; border-radius: 4px; border: 1px solid #27ae60;" 
                title="PDF disponible: <?= e(basename($r['recurso_pdf'])) ?>">
            📄 Disponible
          </span>
        <?php else: ?>
          <span style="font-size: 0.8em; color: #999; padding: 2px 4px; background: #f0f0f0; border-radius: 3px;">Sin PDF</span>
        <?php endif; ?>
      </td>
      <td><?= e($r['tipo'] ?? '-') ?></td>
      <td><?= e($r['slug']) ?></td>
      <td><?= e($r['titulo'] ?? '—') ?></td>
      <td><?= e($r['fecha_inicio'] ?? '') ?></td>
      <td>
        <?= $r['estado']==='A' ? '<span class="badge ok">Activo</span>' : '<span class="badge err">Inactivo</span>' ?>
      </td>
      <td class="actions">
        <?php if (can_edit()): ?>
          <a class="btn-sm" href="edit.php?id=<?= $r['id'] ?>">✏️ Editar</a>
        <?php endif; ?>
        <?php if (can_change_state()): ?>
          <a class="btn-sm" href="toggle.php?id=<?= $r['id'] ?>">🔄 <?= $r['estado']==='A'?'Desactivar':'Activar' ?></a>
          
          <!-- Eliminación suave (marcar inactivo) -->
          <a class="btn-sm warning" 
             href="delete.php?id=<?= $r['id'] ?>" 
             onclick="return confirm('¿Marcar como INACTIVO?\\n\\n⚠️ La formación seguirá en la base de datos pero no se mostrará a los visitantes.\\n\\nEsta acción es REVERSIBLE.')"
             title="Ocultar formación (reversible)">
            📦 Ocultar
          </a>
          
          <!-- Eliminación permanente -->
          <?php if (has_role('admin,root')): ?>
          <a class="btn-sm danger" 
             href="delete_permanent.php?id=<?= $r['id'] ?>" 
             onclick="return confirm('⚠️ ¿ELIMINAR PERMANENTEMENTE?\\n\\n🗑️ Esta acción borrará:\\n• El registro de la base de datos\\n• Todos los archivos (PDF, imágenes)\\n• Las traducciones asociadas\\n\\n❌ ESTA ACCIÓN NO ES REVERSIBLE\\n\\n¿Estás completamente seguro?')"
             title="Eliminar para siempre (NO reversible)"
             style="background: #c0392b;">
            ❌ Eliminar
          </a>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
