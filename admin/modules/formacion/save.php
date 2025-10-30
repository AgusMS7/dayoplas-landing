<?php
/**
 * ARCHIVO: save.php - Módulo Formación
 * PROPÓSITO: Procesar formularios de creación/edición de formaciones
 * INCLUYE: Validación, subida de imágenes/PDFs, operaciones CRUD
 */

// Incluir archivos de configuración
require_once '../../core/config.php';
require_once '../../core/auth.php';
require_once '../../core/csrf.php';
require_once '../../core/flash.php';
require_once '../../core/file_upload.php';
require_once '../../../conexion.php';
require_once '../../../model_daylo.php';

// Verificar autenticación y permisos
require_any(['consultor', 'admin', 'root']);

// Verificar token CSRF
csrf_check();

try {
    // === DEPURACIÓN: LOG DE DATOS RECIBIDOS ===
    error_log('=== INICIO DE SAVE.PHP ===');
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));
    
    // === CREAR CONEXIÓN A BASE DE DATOS ===
    $modelo = new ModelDaylo($host, $db, $user, $pass);
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // === OBTENER DATOS DEL FORMULARIO ===
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tipo_formacion_id = intval($_POST['tipo_formacion_id'] ?? 0);
    $slug = trim($_POST['slug'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $duracion = trim($_POST['duracion'] ?? '');
    $horarios = trim($_POST['horarios'] ?? '');
    $dias_cursado = trim($_POST['dias_cursado'] ?? '');
    $carga_horaria = intval($_POST['carga_horaria'] ?? 0);
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $estado = $_POST['estado'] ?? 'A';

    // Datos de contenido en español
    $titulo_es = trim($_POST['titulo_es'] ?? '');
    $descripcion_es = trim($_POST['descripcion_es'] ?? '');
    $boton_es = trim($_POST['boton_es'] ?? '');

    // === VALIDACIONES BÁSICAS ===
    if (empty($slug) || $tipo_formacion_id <= 0) {
        flash('err', 'El slug y tipo de formación son obligatorios.');
        header('Location: ' . ($id ? 'edit.php?id=' . $id : 'edit.php'));
        exit();
    }

    // === OBTENER ARCHIVOS ACTUALES SI ESTAMOS EDITANDO ===
    $old_imagen = null;
    $old_pdf = null;
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT imagen, recurso_pdf FROM formacion WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($current) {
            $old_imagen = $current['imagen'];
            $old_pdf = $current['recurso_pdf'];
        }
    }

    // === MANEJO DE IMAGEN PRINCIPAL ===
    $imagen_actual = $_POST['imagen_actual'] ?? '';
    $imagen_manual = trim($_POST['imagen_manual'] ?? '');
    $imagen_final = $imagen_actual;

    // Prioridad: 1) Archivo subido, 2) Ruta manual, 3) Imagen actual
    if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
        error_log('Subiendo imagen: ' . $_FILES['imagen_file']['name']); // Depuración
        $upload_result = upload_image($_FILES['imagen_file'], 'images/formaciones/');
        
        if ($upload_result['success']) {
            // Eliminar imagen anterior si existe
            if ($old_imagen && $old_imagen !== basename($upload_result['filename'])) {
                $old_path = '../../../images/formaciones/' . $old_imagen;
                if (file_exists($old_path)) {
                    unlink($old_path);
                    error_log('Imagen anterior eliminada: ' . $old_path); // Depuración
                }
            }
            // GUARDAR SOLO EL NOMBRE DEL ARCHIVO, NO LA RUTA COMPLETA
            $imagen_final = basename($upload_result['filename']);
            flash('ok', 'Imagen subida exitosamente: ' . $imagen_final);
        } else {
            flash('err', 'Error al subir imagen: ' . $upload_result['error']);
            error_log('Error al subir imagen: ' . $upload_result['error']); // Depuración
        }
    } elseif (!empty($imagen_manual) && $imagen_manual !== $imagen_actual) {
        // Usar ruta manual si se proporcionó y es diferente a la actual
        $imagen_final = $imagen_manual;
        flash('info', 'Imagen actualizada manualmente: ' . $imagen_manual);
    } else {
        $error_code = $_FILES['imagen_file']['error'] ?? 'N/A';
        error_log('No se subió ninguna imagen. Código de error: ' . $error_code); // Depuración
        if ($error_code !== UPLOAD_ERR_NO_FILE && $error_code !== 'N/A') {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'El archivo es más grande que upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'El archivo es más grande que MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo al disco',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
            ];
            $error_msg = $upload_errors[$error_code] ?? 'Error desconocido: ' . $error_code;
            flash('err', 'Error en subida de imagen: ' . $error_msg);
            error_log('Error detallado en subida: ' . $error_msg);
        }
    }

    // === MANEJO DE ARCHIVO PDF ===
    $pdf_actual = $_POST['recurso_pdf_actual'] ?? '';
    $pdf_manual = trim($_POST['recurso_pdf_manual'] ?? '');
    $pdf_final = $pdf_actual;

    // Prioridad: 1) Archivo subido, 2) Ruta manual, 3) PDF actual
    if (isset($_FILES['recurso_pdf']) && $_FILES['recurso_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_pdf($_FILES['recurso_pdf'], 'images/PDF/');
        
        if ($upload_result['success']) {
            // Eliminar PDF anterior si existe
            if ($old_pdf && $old_pdf !== basename($upload_result['filename'])) {
                $old_path = '../../../images/PDF/' . $old_pdf;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            // GUARDAR SOLO EL NOMBRE DEL ARCHIVO, NO LA RUTA COMPLETA
            $pdf_final = basename($upload_result['filename']);
            flash('ok', 'PDF subido exitosamente: ' . $pdf_final);
        } else {
            flash('err', 'Error al subir PDF: ' . $upload_result['error']);
        }
    } elseif (!empty($pdf_manual) && $pdf_manual !== $pdf_actual) {
        // Usar ruta manual si se proporcionó
        $pdf_final = $pdf_manual;
    }

    // === MANEJO DE IMAGEN DE CABECERA ===
    $imagen_cabecera_actual = $_POST['imagen_cabecera_actual'] ?? '';
    $imagen_cabecera_final = $imagen_cabecera_actual;

    if (isset($_FILES['imagen_cabecera']) && $_FILES['imagen_cabecera']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_image($_FILES['imagen_cabecera'], 'images/cabecera/');
        
        if ($upload_result['success']) {
            // Eliminar imagen de cabecera anterior si existe
            if ($imagen_cabecera_actual) {
                $old_path = '../../../images/cabecera/' . $imagen_cabecera_actual;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            // GUARDAR SOLO EL NOMBRE DEL ARCHIVO, NO LA RUTA COMPLETA
            $imagen_cabecera_final = basename($upload_result['filename']);
            flash('ok', 'Imagen de cabecera subida exitosamente');
        } else {
            flash('err', 'Error al subir imagen de cabecera: ' . $upload_result['error']);
        }
    }

    // === OPERACIONES EN BASE DE DATOS ===
    if ($id > 0) {
        // ACTUALIZAR formación existente
        $sql = "UPDATE formacion 
                SET tipo_formacion_id = ?, 
                    slug = ?, 
                    imagen = ?, 
                    recurso_pdf = ?, 
                    imagen_cabecera = ?, 
                    fecha_inicio = ?, 
                    duracion = ?, 
                    horarios = ?, 
                    dias_cursado = ?, 
                    carga_horaria = ?, 
                    destacado = ?, 
                    estado = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $tipo_formacion_id,
            $slug,
            $imagen_final ?: null,
            $pdf_final ?: null,
            $imagen_cabecera_final ?: null,
            $fecha_inicio,
            $duracion ?: null,
            $horarios ?: null,
            $dias_cursado ?: null,
            $carga_horaria ?: null,
            $destacado,
            $estado,
            $id
        ]);

        if ($result) {
            flash('ok', 'Formación actualizada exitosamente');
        } else {
            throw new Exception('Error al actualizar la formación en la base de datos');
        }
    } else {
        // CREAR nueva formación
        $sql = "INSERT INTO formacion (
                    tipo_formacion_id, slug, imagen, recurso_pdf, imagen_cabecera,
                    fecha_inicio, duracion, horarios, dias_cursado, carga_horaria,
                    destacado, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $tipo_formacion_id,
            $slug,
            $imagen_final ?: null,
            $pdf_final ?: null,
            $imagen_cabecera_final ?: null,
            $fecha_inicio,
            $duracion ?: null,
            $horarios ?: null,
            $dias_cursado ?: null,
            $carga_horaria ?: null,
            $destacado,
            $estado
        ]);

        if ($result) {
            $id = $pdo->lastInsertId();
            flash('ok', 'Formación creada exitosamente');
        } else {
            throw new Exception('Error al crear la formación en la base de datos');
        }
    }

    // === PROCESAR CONTENIDO EN ESPAÑOL ===
    if (!empty($titulo_es) || !empty($descripcion_es) || !empty($boton_es)) {
        // Verificar si ya existe traducción en español
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM formacion_trad WHERE formacion_id = ? AND idioma_id = 'es'");
        $stmt->execute([$id]);
        $existe_traduccion = $stmt->fetchColumn() > 0;

        if ($existe_traduccion) {
            // Actualizar traducción existente
            $sql = "UPDATE formacion_trad 
                    SET titulo = ?, descripcion = ?, boton = ?
                    WHERE formacion_id = ? AND idioma_id = 'es'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo_es, $descripcion_es, $boton_es, $id]);
        } else {
            // Crear nueva traducción
            $sql = "INSERT INTO formacion_trad (formacion_id, idioma_id, titulo, descripcion, boton) 
                    VALUES (?, 'es', ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $titulo_es, $descripcion_es, $boton_es]);
        }
    }

} catch (Exception $e) {
    // Manejo de errores
    flash('err', 'Error: ' . $e->getMessage());
    error_log("Error en save.php (formación): " . $e->getMessage());
    error_log("Error details: " . $e->getTraceAsString());
}

// Redireccionar al listado
header('Location: index.php');
exit();
?>