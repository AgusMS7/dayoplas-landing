<?php
// admin/usuarios.php - Panel para autorizar usuarios
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}
require_once '../conexion.php';

$mensaje = '';
$tipo_mensaje = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cambiar estado
    if (isset($_POST['autorizar_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET estado = 'ACTIVO' WHERE id = ?");
        $stmt->execute([$_POST['autorizar_id']]);
        $mensaje = 'Usuario autorizado correctamente.';
        $tipo_mensaje = 'success';
    }
    if (isset($_POST['bloquear_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET estado = 'BLOQUEADO' WHERE id = ?");
        $stmt->execute([$_POST['bloquear_id']]);
        $mensaje = 'Usuario bloqueado correctamente.';
        $tipo_mensaje = 'warning';
    }
    if (isset($_POST['pendiente_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET estado = 'PENDIENTE' WHERE id = ?");
        $stmt->execute([$_POST['pendiente_id']]);
        $mensaje = 'Usuario marcado como pendiente.';
        $tipo_mensaje = 'info';
    }

    // Listar usuarios - Corregir query para usar la estructura actual
    $stmt = $pdo->query("SELECT id, nombre, email, estado, role_name FROM users ORDER BY id DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensaje = 'Error: ' . $e->getMessage();
    $tipo_mensaje = 'error';
    $usuarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - DAYLOPLAS-IPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .usuarios-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header-section {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header-section h2 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        
        .header-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .content-section {
            padding: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #4CAF50;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        .alert.warning {
            background: #fff3cd;
            color: #856404;
            border-left: 5px solid #ffc107;
        }
        
        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 5px solid #17a2b8;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse;
            background: white;
        }
        
        th, td { 
            padding: 15px 20px; 
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }
        
        th { 
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .estado-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .estado-PENDIENTE { 
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .estado-ACTIVO { 
            background: #d4edda;
            color: #155724;
            border: 1px solid #4CAF50;
        }
        
        .estado-BLOQUEADO { 
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #dc3545;
        }
        
        .acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.85em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-autorizar {
            background: #4CAF50;
            color: white;
        }
        
        .btn-autorizar:hover {
            background: #45a049;
            transform: translateY(-1px);
        }
        
        .btn-bloquear {
            background: #dc3545;
            color: white;
        }
        
        .btn-bloquear:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .btn-pendiente {
            background: #ffc107;
            color: #856404;
        }
        
        .btn-pendiente:hover {
            background: #e0a800;
            transform: translateY(-1px);
        }
        
        .footer-actions {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .footer-actions a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s ease;
        }
        
        .footer-actions a:hover {
            color: #45a049;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        @media (max-width: 768px) {
            .usuarios-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header-section {
                padding: 20px;
            }
            
            .header-section h2 {
                font-size: 1.8em;
            }
            
            .content-section {
                padding: 20px;
            }
            
            .acciones {
                justify-content: center;
            }
            
            th, td {
                padding: 10px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="usuarios-container">
        <div class="header-section">
            <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
            <p>Administra el acceso de usuarios al sistema CRUD</p>
        </div>
        
        <div class="content-section">
            <?php if ($mensaje): ?>
                <div class="alert <?= $tipo_mensaje ?>">
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'error' ? 'exclamation-triangle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle')) ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($usuarios)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No hay usuarios registrados</h3>
                    <p>Los usuarios aparecerán aquí cuando se registren en el sistema</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($u['nombre'] ?? $u['email'], 0, 1)) ?>
                                            </div>
                                            <strong><?= htmlspecialchars($u['nombre'] ?? 'Sin nombre') ?></strong>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['role_name'] ?? 'Usuario') ?></td>
                                    <td>
                                        <span class="estado-badge estado-<?= $u['estado'] ?>">
                                            <?= $u['estado'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acciones">
                                            <?php if ($u['estado'] !== 'ACTIVO'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="autorizar_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-autorizar" onclick="return confirm('¿Activar este usuario?')">
                                                        <i class="fas fa-check"></i> Activar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($u['estado'] !== 'BLOQUEADO'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="bloquear_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-bloquear" onclick="return confirm('¿Bloquear este usuario?')">
                                                        <i class="fas fa-ban"></i> Bloquear
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($u['estado'] !== 'PENDIENTE'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="pendiente_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-pendiente" onclick="return confirm('¿Marcar como pendiente?')">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer-actions">
            <a href="panel_crud.php">
                <i class="fas fa-arrow-left"></i> Volver al Panel CRUD
            </a>
        </div>
    </div>

    <script>
        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.usuarios-container').style.opacity = '0';
            document.querySelector('.usuarios-container').style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                document.querySelector('.usuarios-container').style.transition = 'all 0.6s ease';
                document.querySelector('.usuarios-container').style.opacity = '1';
                document.querySelector('.usuarios-container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
