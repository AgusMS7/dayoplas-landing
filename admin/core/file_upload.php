<?php
/*
 * ARCHIVO: file_upload.php
 * PROPÓSITO: Manejo de subida de archivos de imagen y PDF para el sistema CRUD
 * DESCRIPCIÓN: Funciones para validar, subir, redimensionar y gestionar archivos
 */

// Protección contra inclusión múltiple
if (!function_exists('upload_image')) {

/**
 * Subir imagen y generar nombre único
 * @param array $file - Array $_FILES['campo']
 * @param string $destination_dir - Directorio destino (relativo a la raíz web)
 * @param array $allowed_types - Tipos de archivo permitidos
 * @param int $max_size - Tamaño máximo en bytes
 * @return array - ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_image($file, $destination_dir = 'images/formaciones/', $allowed_types = ['jpg', 'jpeg', 'png', 'webp'], $max_size = 5242880) {
    // 5MB por defecto
    
    $result = ['success' => false, 'filename' => '', 'error' => ''];
    
    // Verificar si se subió archivo
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'No se subió ningún archivo o hubo un error en la subida.';
        return $result;
    }
    
    // Verificar tamaño
    if ($file['size'] > $max_size) {
        $result['error'] = 'El archivo es demasiado grande. Máximo permitido: ' . format_bytes($max_size);
        return $result;
    }
    
    // Obtener información del archivo
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension'] ?? '');
    
    // Verificar extensión
    if (!in_array($extension, $allowed_types)) {
        $result['error'] = 'Tipo de archivo no permitido. Permitidos: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Verificar que es una imagen real (con múltiples métodos de validación)
    $is_valid_image = false;
    $image_info = [800, 600]; // Valores por defecto
    
    // Método 1: Intentar getimagesize() si está disponible
    if (function_exists('getimagesize')) {
        $temp_image_info = @getimagesize($file['tmp_name']);
        if ($temp_image_info !== false) {
            $image_info = $temp_image_info;
            $is_valid_image = true;
            error_log('Imagen validada con getimagesize(): ' . $file['name']); // Depuración
        } else {
            error_log('getimagesize() falló para: ' . $file['name']); // Depuración
        }
    }
    
    // Método 2: Validar por MIME type usando fileinfo (fallback)
    if (!$is_valid_image && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mime_types = [
            'image/jpeg', 'image/jpg', 'image/png', 
            'image/gif', 'image/webp', 'image/bmp'
        ];
        
        if (in_array($mime_type, $allowed_mime_types)) {
            $is_valid_image = true;
            error_log('Imagen validada por MIME type: ' . $mime_type); // Depuración
        } else {
            error_log('MIME type no válido: ' . $mime_type); // Depuración
        }
    }
    
    // Método 3: Validación básica por extensión y tamaño (último recurso)
    if (!$is_valid_image) {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && $file['size'] > 100) {
            $is_valid_image = true;
            error_log('Imagen validada por extensión y tamaño: ' . $file['name']); // Depuración
        }
    }
    
    if (!$is_valid_image) {
        $result['error'] = 'El archivo no es una imagen válida o está corrupto.';
        error_log('Error: Todos los métodos de validación fallaron para: ' . $file['name']); // Depuración
        return $result;
    }

    // Crear directorio si no existe (path desde admin/core hacia raíz)
    $base_path = dirname(dirname(__DIR__)); // Sube dos niveles desde admin/core
    $full_destination_dir = $base_path . '/' . $destination_dir;
    if (!is_dir($full_destination_dir)) {
        if (!mkdir($full_destination_dir, 0755, true)) {
            $result['error'] = 'No se pudo crear el directorio de destino: ' . $full_destination_dir;
            error_log('Error: ' . $result['error']); // Depuración
            return $result;
        }
    }

    // Generar nombre único
    $filename = generate_unique_filename($file_info['filename'], $extension, $full_destination_dir);
    $full_path = $full_destination_dir . $filename;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        // Optimizar imagen si es necesario y GD está disponible
        if (function_exists('getimagesize') && function_exists('imagecreatefromjpeg')) {
            optimize_image($full_path, $image_info[0], $image_info[1]);
        } else {
            error_log('Advertencia: GD no disponible, optimización de imagen omitida.'); // Depuración
        }

        $result['success'] = true;
        $result['filename'] = $filename; // Solo el nombre del archivo
        error_log('Archivo subido exitosamente: ' . $full_path); // Depuración
    } else {
        $result['error'] = 'Error al mover el archivo al destino: ' . $full_path;
        error_log('Error: ' . $result['error']); // Depuración
    }
    
    return $result;
}

/**
 * Generar nombre único para evitar conflictos
 */
function generate_unique_filename($basename, $extension, $directory) {
    $counter = 0;
    $original_basename = $basename;
    
    do {
        $filename = ($counter > 0) 
            ? $original_basename . '_' . $counter . '.' . $extension
            : $original_basename . '.' . $extension;
        $counter++;
    } while (file_exists($directory . $filename));
    
    return $filename;
}

/**
 * Optimizar imagen (redimensionar si es muy grande)
 */
function optimize_image($filepath, $width, $height, $max_width = 1200, $max_height = 800, $quality = 85) {
    // Verificar que GD esté disponible
    if (!function_exists('imagecreatefromjpeg')) {
        error_log('Advertencia: GD no disponible, no se puede optimizar la imagen: ' . $filepath);
        return;
    }
    
    // Solo optimizar si la imagen es muy grande
    if ($width <= $max_width && $height <= $max_height) {
        return;
    }
    
    // Calcular nuevas dimensiones manteniendo aspecto
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = intval($width * $ratio);
    $new_height = intval($height * $ratio);
    
    // Crear imagen desde archivo
    $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($filepath);
            break;
        case 'png':
            $source = imagecreatefrompng($filepath);
            break;
        case 'webp':
            $source = imagecreatefromwebp($filepath);
            break;
        default:
            return;
    }
    
    if (!$source) return;
    
    // Crear imagen redimensionada
    $resized = imagecreatetruecolor($new_width, $new_height);
    
    // Preservar transparencia para PNG
    if ($extension === 'png') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
    }
    
    imagecopyresampled($resized, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Guardar imagen optimizada
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($resized, $filepath, $quality);
            break;
        case 'png':
            imagepng($resized, $filepath, intval(9 * (100 - $quality) / 100));
            break;
        case 'webp':
            imagewebp($resized, $filepath, $quality);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($resized);
}

/**
 * Eliminar imagen anterior
 */
function delete_old_image($image_path) {
    if (!$image_path) return;
    
    $full_path = __DIR__ . '/../../../' . $image_path;
    if (file_exists($full_path) && is_file($full_path)) {
        unlink($full_path);
    }
}

/**
 * Formatear bytes para mostrar tamaños
 */
function format_bytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $base = log($size, 1024);
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}

/**
 * Verificar si una imagen existe con compatibilidad total
 * MANEJA: Rutas antiguas y nuevas automáticamente
 */
function image_exists($image_path) {
    if (!$image_path) return false;
    
    // Intentar con formato NUEVO primero
    if (strpos($image_path, 'images/') === 0) {
        $full_path = __DIR__ . '/../../../' . $image_path;
        return file_exists($full_path) && is_file($full_path);
    }
    
    // Intentar con formato ANTIGUO
    $full_path = __DIR__ . '/../../../images/formaciones/' . ltrim($image_path, '/');
    return file_exists($full_path) && is_file($full_path);
}

/**
 * Obtener URL completa de imagen con compatibilidad total
 * MANEJA: Rutas antiguas y nuevas automáticamente
 */
function get_image_url($image_path) {
    if (!$image_path) return '';
    
    // Construir URL base (ajustar según tu configuración)
    $base_url = 'http://localhost/nati/8/one/';
    
    // COMPATIBILIDAD TOTAL: Manejar ambos formatos
    if (strpos($image_path, 'images/') === 0) {
        // Formato NUEVO: "images/formaciones/imagen.png" 
        return $base_url . $image_path;
    } else {
        // Formato ANTIGUO: "imagen.png" → "images/formaciones/imagen.png"
        return $base_url . 'images/formaciones/' . ltrim($image_path, '/');
    }
}

/**
 * Subir archivo PDF
 * @param array $file - Array $_FILES['campo']
 * @param string $destination_dir - Directorio destino
 * @param int $max_size - Tamaño máximo en bytes (10MB por defecto)
 * @return array - ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_pdf($file, $destination_dir = 'images/PDF/', $max_size = 10485760) {
    $result = ['success' => false, 'filename' => '', 'error' => ''];
    
    // Verificar si se subió archivo
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'No se subió ningún archivo PDF o hubo un error.';
        return $result;
    }
    
    // Verificar tamaño
    if ($file['size'] > $max_size) {
        $result['error'] = 'El archivo PDF es demasiado grande. Máximo: ' . format_bytes($max_size);
        return $result;
    }
    
    // Verificar extensión
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        $result['error'] = 'Solo se permiten archivos PDF.';
        return $result;
    }
    
    // Verificar tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if ($mime_type !== 'application/pdf') {
        $result['error'] = 'El archivo no es un PDF válido.';
        return $result;
    }
    
    // Crear directorio si no existe (path desde admin/core hacia raíz)
    $base_path = dirname(dirname(__DIR__)); // Sube dos niveles desde admin/core
    $full_destination_dir = $base_path . '/' . $destination_dir;
    if (!is_dir($full_destination_dir)) {
        if (!mkdir($full_destination_dir, 0755, true)) {
            $result['error'] = 'No se pudo crear el directorio de PDFs.';
            return $result;
        }
    }
    
    // Generar nombre único manteniendo nombre original
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
    $filename = $safe_name . '_' . uniqid() . '.pdf';
    $full_path = $full_destination_dir . $filename;
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        $result['success'] = true;
        $result['filename'] = $filename; // Solo el nombre del archivo
    } else {
        $result['error'] = 'Error al mover el archivo PDF.';
    }
    
    return $result;
}

/**
 * Eliminar archivo PDF anterior
 */
function delete_old_pdf($pdf_path) {
    if (!$pdf_path) return;
    
    // Intentar con formato NUEVO
    if (strpos($pdf_path, 'images/') === 0) {
        $full_path = __DIR__ . '/../../../' . $pdf_path;
    } else {
        // Formato ANTIGUO
        $full_path = __DIR__ . '/../../../images/PDF/' . ltrim($pdf_path, '/');
    }
    
    if (file_exists($full_path) && is_file($full_path)) {
        unlink($full_path);
    }
}

/**
 * Obtener URL del PDF
 */
function get_pdf_url($pdf_path) {
    if (!$pdf_path) return '';
    
    $base_url = 'http://localhost/nati/8/one/';
    
    if (strpos($pdf_path, 'images/') === 0) {
        return $base_url . $pdf_path;
    } else {
        return $base_url . 'images/PDF/' . ltrim($pdf_path, '/');
    }
}

/**
 * Verificar si un PDF existe
 */
function pdf_exists($pdf_path) {
    if (!$pdf_path) return false;
    
    if (strpos($pdf_path, 'images/') === 0) {
        $full_path = __DIR__ . '/../../../' . $pdf_path;
    } else {
        $full_path = __DIR__ . '/../../../images/PDF/' . ltrim($pdf_path, '/');
    }
    
    return file_exists($full_path) && is_file($full_path);
}

} // Fin de la protección contra inclusión múltiple
?>