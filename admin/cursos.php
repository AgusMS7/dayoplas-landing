<?php
// admin/cursos.php - Panel CRUD de cursos/talleres/jornadas para admin/usuarios autorizados
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Administrador') {
    header('Location: ../login.php');
    exit;
}
// Verificar estado del usuario (debe ser ACTIVO)
require_once '../conexion.php';
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT estado FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row['estado'] !== 'ACTIVO') {
            session_destroy();
            header('Location: ../login.php?error=autorizacion');
            exit;
        }
    } catch (Exception $e) {
        session_destroy();
        header('Location: ../login.php?error=autorizacion');
        exit;
    }
}
require_once '../model_daylo.php';
require_once '../conexion.php';

$Modelo = new ModelDaylo($host, $db, $user, $pass);
$idioma = 'es';

// Procesar acciones CRUD
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $Modelo->crearFormacion(
            $_POST['tipo_formacion_id'], $_POST['slug'], $_POST['imagen'], $_POST['fecha_inicio'],
            $_POST['duracion'], $_POST['horarios'], $_POST['dias_cursado'], $_POST['carga_horaria'],
            $_POST['recurso_pdf'], $_POST['recurso_imagen'], $_POST['destacado'], $_POST['imagen_cabecera'], $_POST['estado']
        );
        $mensaje = 'Curso/Taller/Jornada creado correctamente.';
    } elseif (isset($_POST['eliminar'])) {
        $Modelo->eliminarFormacion($_POST['id']);
        $mensaje = 'Eliminado correctamente.';
    } elseif (isset($_POST['editar'])) {
        $Modelo->actualizarFormacion(
            $_POST['id'], $_POST['slug'], $_POST['imagen'], $_POST['fecha_inicio'],
            $_POST['duracion'], $_POST['horarios'], $_POST['dias_cursado'], $_POST['carga_horaria'],
            $_POST['recurso_pdf'], $_POST['recurso_imagen'], $_POST['destacado'], $_POST['imagen_cabecera'], $_POST['estado']
        );
        $mensaje = 'Actualizado correctamente.';
    }
}

// Búsqueda
$busqueda = $_GET['busqueda'] ?? '';
if ($busqueda) {
    $cursos = array_filter($Modelo->obtenerFormaciones(), function($curso) use ($busqueda) {
        return stripos($curso['slug'], $busqueda) !== false;
    });
} else {
    $cursos = $Modelo->obtenerFormaciones();
}
$tipos = $Modelo->obtenerTiposFormacion($idioma);

// Si se va a editar, obtener datos
$cursoEditar = null;
if (isset($_GET['editar_id'])) {
    foreach ($cursos as $c) {
        if ($c['id'] == $_GET['editar_id']) {
            $cursoEditar = $c;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Cursos/Talleres/Jornadas</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/naty.css">
    <style>
        body { background: #f7f7f7; }
        .panel-container { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 2em; }
        h2 { text-align: center; margin-bottom: 1.5em; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2em; }
        th, td { border: 1px solid #ccc; padding: 0.6em; text-align: left; }
        th { background: #eaeaea; }
        .acciones { display: flex; gap: 0.5em; }
        .acciones form { display: inline; }
        .form-nuevo, .form-editar { margin-bottom: 2em; background: #f2f6ff; padding: 1.2em; border-radius: 10px; }
        .form-nuevo input, .form-nuevo select, .form-editar input, .form-editar select { margin-bottom: 0.7em; width: 100%; padding: 0.5em; border-radius: 6px; border: 1px solid #bbb; }
        .form-nuevo button, .form-editar button { background: #1a4fff; color: #fff; border: none; padding: 0.7em 1.5em; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .form-nuevo button:hover, .form-editar button:hover { background: #0d2e8b; }
        .mensaje { color: green; text-align: center; margin-bottom: 1em; }
        .busqueda-form { margin-bottom: 1.5em; text-align: right; }
        .busqueda-form input[type="text"] { padding: 0.5em; border-radius: 6px; border: 1px solid #bbb; }
        .busqueda-form button { padding: 0.5em 1em; border-radius: 6px; border: none; background: #1a4fff; color: #fff; font-weight: bold; }
    </style>
</head>
<body>
<div class="panel-container">
    <h2>Administrar Cursos, Talleres y Jornadas</h2>
    <?php if ($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <form class="busqueda-form" method="get">
        <input type="text" name="busqueda" placeholder="Buscar por slug..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>
    <?php if ($cursoEditar): ?>
    <form class="form-editar" method="post">
        <h3>Editar Curso/Taller/Jornada</h3>
        <input type="hidden" name="id" value="<?= $cursoEditar['id'] ?>">
        <select name="tipo_formacion_id" required disabled>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= $tipo['id'] ?>" <?= $tipo['id'] == $cursoEditar['tipo_formacion_id'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['titulo']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="slug" placeholder="Slug" value="<?= htmlspecialchars($cursoEditar['slug']) ?>" required>
        <input type="text" name="imagen" placeholder="Imagen" value="<?= htmlspecialchars($cursoEditar['imagen']) ?>">
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($cursoEditar['fecha_inicio']) ?>">
        <input type="text" name="duracion" placeholder="Duración" value="<?= htmlspecialchars($cursoEditar['duracion']) ?>">
        <input type="text" name="horarios" placeholder="Horarios" value="<?= htmlspecialchars($cursoEditar['horarios']) ?>">
        <input type="text" name="dias_cursado" placeholder="Días de cursado" value="<?= htmlspecialchars($cursoEditar['dias_cursado']) ?>">
        <input type="text" name="carga_horaria" placeholder="Carga horaria" value="<?= htmlspecialchars($cursoEditar['carga_horaria']) ?>">
        <input type="text" name="recurso_pdf" placeholder="Recurso PDF" value="<?= htmlspecialchars($cursoEditar['recurso_pdf']) ?>">
        <input type="text" name="recurso_imagen" placeholder="Recurso Imagen" value="<?= htmlspecialchars($cursoEditar['recurso_imagen']) ?>">
        <input type="text" name="destacado" placeholder="Destacado" value="<?= htmlspecialchars($cursoEditar['destacado']) ?>">
        <input type="text" name="imagen_cabecera" placeholder="Imagen cabecera" value="<?= htmlspecialchars($cursoEditar['imagen_cabecera']) ?>">
        <input type="text" name="estado" placeholder="Estado" value="<?= htmlspecialchars($cursoEditar['estado']) ?>" required>
        <button type="submit" name="editar">Guardar Cambios</button>
        <a href="cursos.php" style="margin-left:1em;">Cancelar</a>
    </form>
    <?php else: ?>
    <form class="form-nuevo" method="post">
        <h3>Nuevo Curso/Taller/Jornada</h3>
        <select name="tipo_formacion_id" required>
            <option value="">Tipo de formación</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['titulo']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="slug" placeholder="Slug (ej: peluqueria)" required>
        <input type="text" name="imagen" placeholder="Nombre de imagen (ej: peluqueria.png)">
        <input type="date" name="fecha_inicio" placeholder="Fecha de inicio">
        <input type="text" name="duracion" placeholder="Duración">
        <input type="text" name="horarios" placeholder="Horarios">
        <input type="text" name="dias_cursado" placeholder="Días de cursado">
        <input type="text" name="carga_horaria" placeholder="Carga horaria">
        <input type="text" name="recurso_pdf" placeholder="Recurso PDF">
        <input type="text" name="recurso_imagen" placeholder="Recurso Imagen">
        <input type="text" name="destacado" placeholder="Destacado (0 o 1)">
        <input type="text" name="imagen_cabecera" placeholder="Imagen cabecera">
        <input type="text" name="estado" placeholder="Estado (A/I)" required>
        <button type="submit" name="crear">Crear</button>
    </form>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Slug</th>
                <th>Imagen</th>
                <th>Fecha Inicio</th>
                <th>Duración</th>
                <th>Horarios</th>
                <th>Días</th>
                <th>Carga Horaria</th>
                <th>PDF</th>
                <th>Imagen Recurso</th>
                <th>Destacado</th>
                <th>Cabecera</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <body>
            <div class="panel-container">
                <div style="text-align:right; margin-bottom:1em;">
                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Administrador'): ?>
                    <a href="usuarios.php" style="background:#1a4fff;color:#fff;padding:0.5em 1em;border-radius:6px;text-decoration:none;font-weight:bold;">Panel de Usuarios</a>
                <?php endif; ?>
                </div>
                <h2>Administrar Cursos, Talleres y Jornadas</h2>
                <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
                <td><?= $curso['tipo_formacion_id'] ?></td>
                <td><?= htmlspecialchars($curso['slug']) ?></td>
                <td><?= htmlspecialchars($curso['imagen']) ?></td>
                <td><?= htmlspecialchars($curso['fecha_inicio']) ?></td>
                <td><?= htmlspecialchars($curso['duracion']) ?></td>
                <td><?= htmlspecialchars($curso['horarios']) ?></td>
                <td><?= htmlspecialchars($curso['dias_cursado']) ?></td>
                <td><?= htmlspecialchars($curso['carga_horaria']) ?></td>
                <td><?= htmlspecialchars($curso['recurso_pdf']) ?></td>
                <td><?= htmlspecialchars($curso['recurso_imagen']) ?></td>
                <td><?= htmlspecialchars($curso['destacado']) ?></td>
                <td><?= htmlspecialchars($curso['imagen_cabecera']) ?></td>
                <td><?= htmlspecialchars($curso['estado']) ?></td>
                <td class="acciones">
                    <form method="post" onsubmit="return confirm('¿Eliminar este curso/taller/jornada?');">
                        <input type="hidden" name="id" value="<?= $curso['id'] ?>">
                        <button type="submit" name="eliminar" style="background:#c00;color:#fff;padding:0.4em 1em;border-radius:6px;">Eliminar</button>
                    </form>
                    <form method="get" style="display:inline;">
                        <input type="hidden" name="editar_id" value="<?= $curso['id'] ?>">
                        <button type="submit" style="background:#1a4fff;color:#fff;padding:0.4em 1em;border-radius:6px;">Editar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
