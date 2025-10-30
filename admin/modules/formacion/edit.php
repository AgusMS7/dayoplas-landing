<?php
require __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/file_upload.php';
require_any(['consultor','admin','root']);
csrf_check();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = null;

if ($id) {
  $st = $pdo->prepare("SELECT * FROM formacion WHERE id=?");
  $st->execute([$id]);
  $item = $st->fetch();
  if (!$item) { flash('err','Formación no encontrada'); header('Location: index.php'); exit; }
  
  // Obtener traducción en español
  $st_trad = $pdo->prepare("SELECT * FROM formacion_trad WHERE formacion_id=? AND idioma_id='es'");
  $st_trad->execute([$id]);
  $traduccion = $st_trad->fetch() ?: [];
} else {
  $traduccion = [];
}

$tipos = $pdo->query("SELECT id, clave FROM tipo_formacion WHERE estado='A' ORDER BY clave")->fetchAll();

include __DIR__ . '/../../partials/layout.php';
?>
<h2 class="h2"><?= $id ? 'Editar' : 'Nueva' ?> formación</h2>

<form method="post" action="save.php" enctype="multipart/form-data" class="card" style="max-width:880px">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <input type="hidden" name="recurso_pdf_actual" value="<?= e($item['recurso_pdf'] ?? '') ?>">

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <label>Tipo de formación
      <select name="tipo_formacion_id" required>
        <option value="">-- Seleccionar --</option>
        <?php foreach($tipos as $t): ?>
          <option value="<?=$t['id']?>" <?= ($item && $item['tipo_formacion_id']==$t['id'])?'selected':'' ?>>
            <?= e($t['clave']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Slug (URL)
      <input name="slug" required value="<?= e($item['slug'] ?? '') ?>" placeholder="curso-de-podocosmiatria">
    </label>

    <label>Fecha inicio
      <input type="date" name="fecha_inicio" value="<?= e($item['fecha_inicio'] ?? '') ?>">
    </label>

    <label>Duración
      <input name="duracion" value="<?= e($item['duracion'] ?? '') ?>" placeholder="10 meses">
    </label>

    <label>Horarios
      <input name="horarios" value="<?= e($item['horarios'] ?? '') ?>" placeholder="18:30–21:30">
    </label>

    <label>Días de cursado
      <input name="dias_cursado" value="<?= e($item['dias_cursado'] ?? '') ?>" placeholder="Martes y Jueves">
    </label>

    <label>Carga horaria
      <input name="carga_horaria" value="<?= e($item['carga_horaria'] ?? '') ?>" placeholder="120 hs">
    </label>

    <?php if ($id): ?>
      <label>Estado
        <select name="estado">
          <option value="A" <?= (($item['estado'] ?? '')==='A')?'selected':'' ?>>Activo</option>
          <option value="I" <?= (($item['estado'] ?? '')==='I')?'selected':'' ?>>Inactivo</option>
        </select>
      </label>
    <?php endif; ?>
  </div>

  <!-- SECCIÓN DE CONTENIDO EN ESPAÑOL -->
  <div style="margin-top: 20px; padding: 20px; border: 2px solid #27ae60; border-radius: 8px; background: #f0f8f0;">
    <h3 style="margin-top: 0; color: #27ae60;">📝 Contenido en Español</h3>
    
    <label style="display: block; margin-bottom: 15px;">
      <strong>Título:</strong>
      <input name="titulo_es" 
             value="<?= e($traduccion['titulo'] ?? '') ?>" 
             placeholder="Ej: Curso de Podología Integral"
             style="width: 100%; padding: 10px; border: 1px solid #27ae60; border-radius: 4px;">
    </label>

    <label style="display: block; margin-bottom: 15px;">
      <strong>Descripción:</strong>
      <textarea name="descripcion_es" 
                rows="4" 
                placeholder="Describe el curso, objetivos, duración, modalidad..."
                style="width: 100%; padding: 10px; border: 1px solid #27ae60; border-radius: 4px; resize: vertical;"><?= e($traduccion['descripcion'] ?? '') ?></textarea>
    </label>

    <label style="display: block; margin-bottom: 10px;">
      <strong>Texto del Botón:</strong>
      <input name="boton_es" 
             value="<?= e($traduccion['boton'] ?? '') ?>" 
             placeholder="Ej: Inscribirse, Ver más, Consultar"
             style="width: 100%; padding: 10px; border: 1px solid #27ae60; border-radius: 4px;">
    </label>
  </div>

  <!-- SECCIÓN DE IMAGEN MEJORADA -->
  <div style="margin-top: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
    <h3 style="margin-top: 0; color: #333;">🖼️ Gestión de Imagen</h3>
    
    <!-- Imagen actual -->
    <?php if ($item && $item['imagen']): ?>
      <div style="margin-bottom: 15px;">
        <h4 style="color: #666; margin-bottom: 8px;">Imagen actual:</h4>
        <div style="display: flex; align-items: center; gap: 15px;">
          <img src="<?= get_image_url($item['imagen']) ?>" 
               alt="Imagen actual" 
               style="max-width: 200px; max-height: 150px; border: 2px solid #ddd; border-radius: 4px;"
               onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDIwMCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIxNTAiIGZpbGw9IiNmMGYwZjAiLz48cGF0aCBkPSJNNTAgNzVMMTAwIDUwTDE1MCA3NUwxMjUgMTAwTDc1IDEwMEw1MCA3NVoiIGZpbGw9IiNjY2MiLz48L3N2Zz4='">
          <div>
            <p style="margin: 0; color: #666; font-size: 0.9em;">
              <strong>Archivo:</strong> <?= basename($item['imagen']) ?><br>
              <strong>Ruta:</strong> <?= $item['imagen'] ?>
            </p>
          </div>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Subir nueva imagen -->
    <div>
      <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
        <?= $item && $item['imagen'] ? 'Cambiar imagen:' : 'Subir imagen:' ?>
      </label>
      <input type="file" 
             name="imagen_file" 
             accept="image/*,.png,.jpg,.jpeg,.webp"
             style="margin-bottom: 8px;">
      <div style="font-size: 0.85em; color: #666;">
        <strong>Formatos permitidos:</strong> PNG, JPG, JPEG, WEBP<br>
        <strong>Tamaño máximo:</strong> 5MB<br>
        <strong>Recomendación:</strong> Imágenes de máximo 1200x800px para mejor rendimiento
      </div>
    </div>
    
    <!-- Opción de mantener imagen actual o usar ruta manual -->
    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
      <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
        O ingresar ruta manualmente:
      </label>
      <input name="imagen_manual" 
             value="<?= e($item['imagen'] ?? '') ?>" 
             placeholder="images/formaciones/mi-imagen.png"
             style="width: 100%;">
      <div style="font-size: 0.85em; color: #666; margin-top: 5px;">
        Si subes un archivo nuevo, se ignorará la ruta manual
      </div>
    </div>
  </div>

  <!-- SECCIÓN DE PDF -->
  <div style="margin-top: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
    <h3 style="margin-top: 0; color: #333;">📄 Gestión de PDF</h3>
    
    <!-- PDF actual -->
    <?php if ($item && $item['recurso_pdf']): ?>
      <div style="margin-bottom: 15px;">
        <h4 style="color: #666; margin-bottom: 8px;">PDF actual:</h4>
        <div style="display: flex; align-items: center; gap: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: white;">
          <div style="font-size: 2em; color: #e74c3c;">📄</div>
          <div>
            <p style="margin: 0; color: #666; font-size: 0.9em;">
              <strong>Archivo:</strong> <?= basename($item['recurso_pdf']) ?><br>
              <strong>Ruta:</strong> <?= $item['recurso_pdf'] ?>
            </p>
            <div style="margin-top: 8px; padding: 4px 8px; background: #d5f4e6; border-radius: 4px; border-left: 4px solid #27ae60;">
              <span style="color: #27ae60; font-size: 0.9em;">
                ✅ PDF configurado correctamente
              </span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Subir nuevo PDF desde computadora -->
    <div style="margin-top: 15px; padding: 15px; border: 2px dashed #3498db; border-radius: 8px; background: #f8f9ff;">
      <label style="display: block; margin-bottom: 12px; font-weight: bold; color: #2c3e50; font-size: 16px;">
        📂 <?= $item && $item['recurso_pdf'] ? 'Cambiar PDF:' : 'Subir PDF desde tu computadora:' ?>
      </label>
      <input type="file" 
             name="recurso_pdf" 
             accept=".pdf,application/pdf"
             style="margin-bottom: 12px; padding: 10px; border: 2px solid #3498db; border-radius: 6px; width: 100%; font-size: 14px; background: white;"
             title="Buscar archivo PDF en tu computadora">
      <div style="font-size: 0.9em; color: #34495e; background: #ecf0f1; padding: 10px; border-radius: 4px;">
        <strong>📋 Instrucciones:</strong><br>
        • <strong>Formato:</strong> Solo archivos PDF (.pdf)<br>
        • <strong>Tamaño máximo:</strong> 10MB<br>
        • <strong>Contenido sugerido:</strong> Programa de estudio, certificados, material complementario<br>
        • <strong>Ubicación:</strong> Haz clic en "Examinar" para buscar el archivo en tu escritorio o carpetas
      </div>
    </div>
    
    <!-- Opción alternativa: ruta manual -->
    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #bdc3c7;">
      <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #7f8c8d; font-size: 14px;">
        ⚙️ Opción avanzada - Ruta manual del PDF:
      </label>
      <input name="recurso_pdf_manual" 
             value="<?= e($item['recurso_pdf'] ?? '') ?>" 
             placeholder="images/PDF/programa-curso.pdf"
             style="width: 100%; padding: 8px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 14px;">
      <div style="font-size: 0.8em; color: #7f8c8d; margin-top: 8px; background: #f4f4f4; padding: 8px; border-radius: 4px;">
        <strong>⚠️ Nota:</strong> Solo usar si el PDF ya está en el servidor. Si subes un archivo nuevo desde tu computadora, esta ruta se ignorará automáticamente.
      </div>
    </div>
  </div>

  <button class="btn" style="margin-top: 20px;">Guardar</button>
</form>

<script>
// Vista previa de imagen seleccionada
document.querySelector('input[name="imagen_file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Crear o actualizar vista previa
            let preview = document.getElementById('image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.id = 'image-preview';
                preview.style.marginTop = '10px';
                e.target.parentNode.appendChild(preview);
            }
            
            preview.innerHTML = `
                <h4 style="color: #28a745; margin-bottom: 8px;">✅ Vista previa de la nueva imagen:</h4>
                <img src="${e.target.result}" 
                     alt="Vista previa" 
                     style="max-width: 200px; max-height: 150px; border: 2px solid #28a745; border-radius: 4px;">
                <p style="margin: 5px 0 0 0; font-size: 0.9em; color: #666;">
                    <strong>Archivo:</strong> ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)
                </p>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include __DIR__ . '/../../partials/_end.php'; ?>
