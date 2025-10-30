<?php
/*
 * ARCHIVO: delete_permanent.php
 * PROPÓSITO: Eliminación PERMANENTE de formación (no recuperable)
 * FUNCIONES: Borra registro y archivos asociados completamente
 */

require __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/file_upload.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { 
    flash('err', 'ID de formación no válido');
    header('Location: index.php'); 
    exit; 
}

try {
    // Obtener datos de la formación antes de eliminar
    $st = $pdo->prepare("SELECT slug, imagen, recurso_pdf, recurso_imagen, imagen_cabecera FROM formacion WHERE id=?");
    $st->execute([$id]);
    $formacion = $st->fetch();
    
    if (!$formacion) {
        flash('err', 'Formación no encontrada');
        header('Location: index.php');
        exit;
    }
    
    // === ELIMINAR ARCHIVOS ASOCIADOS ===
    
    // Eliminar imagen principal
    if ($formacion['imagen']) {
        delete_old_image($formacion['imagen']);
    }
    
    // Eliminar PDF
    if ($formacion['recurso_pdf']) {
        delete_old_pdf($formacion['recurso_pdf']);
    }
    
    // Eliminar recurso imagen
    if ($formacion['recurso_imagen']) {
        delete_old_image($formacion['recurso_imagen']);
    }
    
    // Eliminar imagen de cabecera
    if ($formacion['imagen_cabecera']) {
        delete_old_image($formacion['imagen_cabecera']);
    }
    
    // === ELIMINAR REGISTROS DE BASE DE DATOS ===
    
    // Iniciar transacción para asegurar consistencia
    $pdo->beginTransaction();
    
    try {
        // 1. Eliminar traducciones
        $pdo->prepare("DELETE FROM formacion_trad WHERE formacion_id=?")->execute([$id]);
        
        // 2. Eliminar la formación principal
        $pdo->prepare("DELETE FROM formacion WHERE id=?")->execute([$id]);
        
        // Confirmar transacción
        $pdo->commit();
        
        flash('ok', "Formación '{$formacion['slug']}' eliminada PERMANENTEMENTE (archivos y datos)");
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        throw new Exception('Error al eliminar registros de base de datos: ' . $e->getMessage());
    }
    
} catch (Exception $e) {
    flash('err', 'Error al eliminar formación: ' . $e->getMessage());
    error_log("Error en delete_permanent.php: " . $e->getMessage());
}

header('Location: index.php');
exit;
?>