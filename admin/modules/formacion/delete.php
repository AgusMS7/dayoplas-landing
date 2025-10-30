<?php
/*
 * ARCHIVO: delete.php
 * PROPÓSITO: Eliminación SUAVE de formación (reversible)
 * FUNCIÓN: Marca como inactiva pero mantiene datos y archivos
 */

require __DIR__ . '/../../core/config.php';
require_any(['admin','root']);
csrf_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { 
    flash('err', 'ID de formación no válido');
    header('Location: index.php'); 
    exit; 
}

try {
    // Obtener slug para el mensaje
    $st = $pdo->prepare("SELECT slug FROM formacion WHERE id=?");
    $st->execute([$id]);
    $formacion = $st->fetch();
    
    if ($formacion) {
        // Marcar como inactiva (eliminación suave)
        $pdo->prepare("UPDATE formacion SET estado='I' WHERE id=?")->execute([$id]);
        flash('ok', "Formación '{$formacion['slug']}' marcada como INACTIVA (datos conservados, reversible)");
    } else {
        flash('err', 'Formación no encontrada');
    }
    
} catch (Exception $e) {
    flash('err', 'Error al ocultar formación: ' . $e->getMessage());
    error_log("Error en delete.php: " . $e->getMessage());
}

header('Location: index.php');
exit;
