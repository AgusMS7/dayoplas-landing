<?php
session_start();
require_once '../conexion.php';

// Verificar autenticación y permisos de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Administrador') {
    header('Location: ../login.php');
    exit();
}

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$mensaje = '';
$tipo_mensaje = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Directorios para archivos
$dir_imagenes = '../images/formaciones/';
$dir_pdf = '../images/PDF/';
$dir_cabeceras = '../images/cabeceras/';

// Manejar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar'])) {
        $data = [
            'slug' => $_POST['slug'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'duracion' => $_POST['duracion'], 
            'horarios' => $_POST['horarios'],
            'dias_cursado' => $_POST['dias_cursado'],
            'carga_horaria' => $_POST['carga_horaria'],
            'destacado' => $_POST['destacado'],
            'estado' => $_POST['estado']
        ];

        // Manejo de archivo de imagen
        $imagen_nombre = $_POST['imagen_actual'] ?? '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $imagen_temp = $_FILES['imagen']['tmp_name'];
            $imagen_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            
            if (in_array($imagen_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $imagen_nombre = $data['slug'] . '.' . $imagen_ext;
                $imagen_path = $dir_imagenes . $imagen_nombre;
                
                if (move_uploaded_file($imagen_temp, $imagen_path)) {
                    $data['imagen'] = $imagen_nombre;
                }
            }
        } else {
            $data['imagen'] = $imagen_nombre;
        }

        // Manejo de archivo PDF
        $pdf_nombre = $_POST['pdf_actual'] ?? '';
        if (isset($_FILES['recurso_pdf']) && $_FILES['recurso_pdf']['error'] === 0) {
            $pdf_temp = $_FILES['recurso_pdf']['tmp_name'];
            $pdf_ext = strtolower(pathinfo($_FILES['recurso_pdf']['name'], PATHINFO_EXTENSION));
            
            if ($pdf_ext === 'pdf') {
                $pdf_nombre = $data['slug'] . '.pdf';
                $pdf_path = $dir_pdf . $pdf_nombre;
                
                if (move_uploaded_file($pdf_temp, $pdf_path)) {
                    $data['recurso_pdf'] = $pdf_nombre;
                }
            }
        } else {
            $data['recurso_pdf'] = $pdf_nombre;
        }

        // Manejo de imagen de cabecera
        $cabecera_nombre = $_POST['cabecera_actual'] ?? 'cursos.png';
        if (isset($_FILES['imagen_cabecera']) && $_FILES['imagen_cabecera']['error'] === 0) {
            $cabecera_temp = $_FILES['imagen_cabecera']['tmp_name'];
            $cabecera_ext = strtolower(pathinfo($_FILES['imagen_cabecera']['name'], PATHINFO_EXTENSION));
            
            if (in_array($cabecera_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $cabecera_nombre = 'cabecera_' . $data['slug'] . '.' . $cabecera_ext;
                $cabecera_path = $dir_cabeceras . $cabecera_nombre;
                
                if (move_uploaded_file($cabecera_temp, $cabecera_path)) {
                    $data['imagen_cabecera'] = $cabecera_nombre;
                }
            }
        } else {
            $data['imagen_cabecera'] = $cabecera_nombre;
        }

        if ($id) {
            // Actualizar
            $sql = "UPDATE formacion SET slug=?, imagen=?, fecha_inicio=?, duracion=?, horarios=?, dias_cursado=?, carga_horaria=?, recurso_pdf=?, destacado=?, imagen_cabecera=?, estado=? WHERE id=? AND tipo_formacion_id=1";
            $params = array_values($data);
            $params[] = $id;
        } else {
            // Insertar
            $sql = "INSERT INTO formacion (tipo_formacion_id, slug, imagen, fecha_inicio, duracion, horarios, dias_cursado, carga_horaria, recurso_pdf, destacado, imagen_cabecera, estado) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array_values($data);
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $mensaje = $id ? 'Curso actualizado exitosamente' : 'Curso creado exitosamente';
            $tipo_mensaje = 'success';
            $action = 'list';
        } catch (PDOException $e) {
            $mensaje = 'Error al guardar: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
} elseif ($action === 'delete' && $id) {
    try {
        // Obtener información del curso antes de eliminar
        $stmt = $pdo->prepare("SELECT imagen, recurso_pdf, imagen_cabecera FROM formacion WHERE id=? AND tipo_formacion_id=1");
        $stmt->execute([$id]);
        $curso = $stmt->fetch();

        if ($curso) {
            // Eliminar archivos físicos
            if ($curso['imagen'] && file_exists($dir_imagenes . $curso['imagen'])) {
                unlink($dir_imagenes . $curso['imagen']);
            }
            if ($curso['recurso_pdf'] && file_exists($dir_pdf . $curso['recurso_pdf'])) {
                unlink($dir_pdf . $curso['recurso_pdf']);
            }
            if ($curso['imagen_cabecera'] && $curso['imagen_cabecera'] !== 'cursos.png' && file_exists($dir_cabeceras . $curso['imagen_cabecera'])) {
                unlink($dir_cabeceras . $curso['imagen_cabecera']);
            }

            // Eliminar de la base de datos
            $stmt = $pdo->prepare("DELETE FROM formacion WHERE id=? AND tipo_formacion_id=1");
            $stmt->execute([$id]);
            
            $mensaje = 'Curso eliminado exitosamente';
            $tipo_mensaje = 'success';
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al eliminar: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
    $action = 'list';
}

// Obtener datos para formulario
$curso = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM formacion WHERE id=? AND tipo_formacion_id=1");
    $stmt->execute([$id]);
    $curso = $stmt->fetch();
}

// Obtener lista de cursos
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $whereClause = "WHERE tipo_formacion_id=1";
    $params = [];
    
    if ($search) {
        $whereClause .= " AND (slug LIKE ? OR duracion LIKE ? OR dias_cursado LIKE ?)";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    
    $stmt = $pdo->prepare("SELECT * FROM formacion $whereClause ORDER BY id DESC");
    $stmt->execute($params);
    $cursos = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Cursos - DAYLOPLAS-IPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 2em;
        }
        
        .header .actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .search-form input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .search-form input:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .file-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .table-container {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Gestión de Cursos</h1>
            <div class="actions">
                <?php if ($action !== 'list'): ?>
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Ver Lista
                    </a>
                <?php endif; ?>
                <?php if ($action === 'list'): ?>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Curso
                    </a>
                <?php endif; ?>
                <a href="panel_crud.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="alert <?= $tipo_mensaje ?>">
                <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Lista de Cursos -->
            <div class="card">
                <div class="search-form">
                    <input type="text" 
                           placeholder="Buscar cursos por nombre, duración o días..." 
                           value="<?= htmlspecialchars($search) ?>"
                           onchange="window.location.href='?search=' + encodeURIComponent(this.value)">
                    <button class="btn btn-primary" onclick="window.location.href='?'">
                        <i class="fas fa-refresh"></i> Limpiar
                    </button>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Imagen</th>
                                <th>Duración</th>
                                <th>Horarios</th>
                                <th>Días</th>
                                <th>PDF</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><?= $curso['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($curso['slug']) ?></strong></td>
                                    <td>
                                        <?php if ($curso['imagen'] && file_exists($dir_imagenes . $curso['imagen'])): ?>
                                            <img src="<?= $dir_imagenes . $curso['imagen'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <span style="color: #999;">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($curso['duracion']) ?></td>
                                    <td><?= htmlspecialchars($curso['horarios']) ?></td>
                                    <td><?= htmlspecialchars($curso['dias_cursado']) ?></td>
                                    <td>
                                        <?php if ($curso['recurso_pdf'] && file_exists($dir_pdf . $curso['recurso_pdf'])): ?>
                                            <i class="fas fa-file-pdf" style="color: red;"></i> PDF
                                        <?php else: ?>
                                            <span style="color: #999;">Sin PDF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $curso['estado'] === 'A' ? 'active' : 'inactive' ?>">
                                            <?= $curso['estado'] === 'A' ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?action=edit&id=<?= $curso['id'] ?>" class="btn btn-warning" style="margin-right: 5px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?= $curso['id'] ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('¿Estás seguro de eliminar este curso?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($cursos)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; color: #999; padding: 40px;">
                                        <i class="fas fa-graduation-cap" style="font-size: 3em; margin-bottom: 15px;"></i><br>
                                        No se encontraron cursos
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- Formulario -->
            <div class="card">
                <h2>
                    <i class="fas fa-<?= $action === 'add' ? 'plus' : 'edit' ?>"></i>
                    <?= $action === 'add' ? 'Nuevo Curso' : 'Editar Curso' ?>
                </h2>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="slug">Nombre del Curso *</label>
                            <input type="text" id="slug" name="slug" required 
                                   value="<?= htmlspecialchars($curso['slug'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?= $curso['fecha_inicio'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="duracion">Duración</label>
                            <input type="text" id="duracion" name="duracion" 
                                   value="<?= htmlspecialchars($curso['duracion'] ?? '') ?>"
                                   placeholder="ej: 6 meses">
                        </div>

                        <div class="form-group">
                            <label for="carga_horaria">Carga Horaria</label>
                            <input type="text" id="carga_horaria" name="carga_horaria" 
                                   value="<?= htmlspecialchars($curso['carga_horaria'] ?? '') ?>"
                                   placeholder="ej: 120 hs">
                        </div>

                        <div class="form-group">
                            <label for="horarios">Horarios</label>
                            <input type="text" id="horarios" name="horarios" 
                                   value="<?= htmlspecialchars($curso['horarios'] ?? '') ?>"
                                   placeholder="ej: 18:00 - 21:00">
                        </div>

                        <div class="form-group">
                            <label for="dias_cursado">Días de Cursado</label>
                            <input type="text" id="dias_cursado" name="dias_cursado" 
                                   value="<?= htmlspecialchars($curso['dias_cursado'] ?? '') ?>"
                                   placeholder="ej: Lunes y Miércoles">
                        </div>

                        <div class="form-group full-width">
                            <label for="destacado">Texto Destacado</label>
                            <textarea id="destacado" name="destacado" rows="3"
                                      placeholder="ej: *Diploma habilitante*"><?= htmlspecialchars($curso['destacado'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="A" <?= ($curso['estado'] ?? 'A') === 'A' ? 'selected' : '' ?>>Activo</option>
                                <option value="I" <?= ($curso['estado'] ?? '') === 'I' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="imagen">Imagen Principal</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*">
                            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($curso['imagen'] ?? '') ?>">
                            <?php if ($curso && $curso['imagen'] && file_exists($dir_imagenes . $curso['imagen'])): ?>
                                <div class="file-preview">
                                    <strong>Imagen actual:</strong><br>
                                    <img src="<?= $dir_imagenes . $curso['imagen'] ?>?v=<?= time() ?>" alt="Imagen actual">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="recurso_pdf">Archivo PDF</label>
                            <input type="file" id="recurso_pdf" name="recurso_pdf" accept=".pdf">
                            <input type="hidden" name="pdf_actual" value="<?= htmlspecialchars($curso['recurso_pdf'] ?? '') ?>">
                            <?php if ($curso && $curso['recurso_pdf'] && file_exists($dir_pdf . $curso['recurso_pdf'])): ?>
                                <div class="file-preview">
                                    <strong>PDF actual:</strong><br>
                                    <i class="fas fa-file-pdf" style="color: red; font-size: 2em;"></i><br>
                                    <?= htmlspecialchars($curso['recurso_pdf']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="imagen_cabecera">Imagen de Cabecera</label>
                            <input type="file" id="imagen_cabecera" name="imagen_cabecera" accept="image/*">
                            <input type="hidden" name="cabecera_actual" value="<?= htmlspecialchars($curso['imagen_cabecera'] ?? 'cursos.png') ?>">
                            <?php if ($curso && $curso['imagen_cabecera'] && file_exists($dir_cabeceras . $curso['imagen_cabecera'])): ?>
                                <div class="file-preview">
                                    <strong>Cabecera actual:</strong><br>
                                    <img src="<?= $dir_cabeceras . $curso['imagen_cabecera'] ?>?v=<?= time() ?>" alt="Cabecera actual">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" name="guardar" class="btn btn-primary" style="margin-right: 15px;">
                            <i class="fas fa-save"></i> <?= $action === 'add' ? 'Crear Curso' : 'Guardar Cambios' ?>
                        </button>
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>