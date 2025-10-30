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

// Obtener estadísticas de la base de datos
$stats = [];
$tables = ['cursos' => 1, 'talleres' => 2, 'jornadas' => 3];

foreach ($tables as $nombre => $tipo_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM formacion WHERE tipo_formacion_id = ? AND estado = 'A'");
    $stmt->execute([$tipo_id]);
    $stats[$nombre] = $stmt->fetchColumn();
}

// Obtener total de formaciones
$stmt = $pdo->query("SELECT COUNT(*) FROM formacion WHERE estado = 'A'");
$total_formaciones = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel CRUD - DAYLOPLAS-IPM</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-admin {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header-admin h1 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 2.5em;
        }
        
        .header-admin p {
            color: #666;
            margin: 0;
            font-size: 1.1em;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .stat-card.cursos .stat-icon { color: #4CAF50; }
        .stat-card.talleres .stat-icon { color: #FF9800; }
        .stat-card.jornadas .stat-icon { color: #2196F3; }
        .stat-card.total .stat-icon { color: #9C27B0; }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .crud-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .crud-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .crud-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4CAF50, #45a049);
        }
        
        .crud-card.usuarios::before {
            background: linear-gradient(90deg, #9C27B0, #7B1FA2);
        }
        
        .crud-card.talleres::before {
            background: linear-gradient(90deg, #FF9800, #e68900);
        }
        
        .crud-card.jornadas::before {
            background: linear-gradient(90deg, #2196F3, #1976D2);
        }
        
        .crud-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .crud-icon {
            font-size: 4em;
            margin-bottom: 20px;
            color: #4CAF50;
        }
        
        .crud-card.usuarios .crud-icon { color: #9C27B0; }
        .crud-card.talleres .crud-icon { color: #FF9800; }
        .crud-card.jornadas .crud-icon { color: #2196F3; }
        
        .crud-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .crud-description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .crud-btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .crud-card.usuarios .crud-btn {
            background: linear-gradient(45deg, #9C27B0, #7B1FA2);
        }
        
        .crud-card.talleres .crud-btn {
            background: linear-gradient(45deg, #FF9800, #e68900);
        }
        
        .crud-card.jornadas .crud-btn {
            background: linear-gradient(45deg, #2196F3, #1976D2);
        }
        
        .crud-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .admin-footer {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 30px;
        }
        
        .admin-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        
        .admin-footer a:hover {
            color: #764ba2;
        }
        
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.9);
            padding: 10px 20px;
            border-radius: 25px;
            color: #333;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            
            .header-admin {
                padding: 20px;
            }
            
            .header-admin h1 {
                font-size: 2em;
            }
            
            .crud-card {
                padding: 20px;
            }
            
            .user-info {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 20px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['email'] ?? 'Usuario') ?> (Administrador)
    </div>

    <div class="admin-container">
        <!-- Header -->
        <div class="header-admin">
            <h1><i class="fas fa-cogs"></i> Panel de Control CRUD</h1>
            <p>Gestión completa de Cursos, Talleres y Jornadas</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card cursos">
                <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="stat-number"><?= $stats['cursos'] ?></div>
                <div class="stat-label">Cursos</div>
            </div>
            <div class="stat-card talleres">
                <div class="stat-icon"><i class="fas fa-tools"></i></div>
                <div class="stat-number"><?= $stats['talleres'] ?></div>
                <div class="stat-label">Talleres</div>
            </div>
            <div class="stat-card jornadas">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number"><?= $stats['jornadas'] ?></div>
                <div class="stat-label">Jornadas</div>
            </div>
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="stat-number"><?= $total_formaciones ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>

        <!-- CRUD Cards -->
        <div class="crud-grid">
            <!-- Gestión de Usuarios -->
            <div class="crud-card usuarios">
                <div class="crud-icon"><i class="fas fa-users-cog"></i></div>
                <div class="crud-title">Gestión de Usuarios</div>
                <div class="crud-description">
                    Administra el acceso de usuarios: activar, bloquear o cambiar 
                    estados para permitir acceso al sistema CRUD.
                </div>
                <a href="usuarios.php" class="crud-btn">
                    <i class="fas fa-user-shield"></i> Gestionar Usuarios
                </a>
            </div>

            <!-- Cursos CRUD -->
            <div class="crud-card cursos">
                <div class="crud-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="crud-title">Gestión de Cursos</div>
                <div class="crud-description">
                    Administra todos los cursos: agregar nuevos, modificar información, 
                    subir imágenes y PDFs, y gestionar el contenido completo.
                </div>
                <a href="cursos_crud.php" class="crud-btn">
                    <i class="fas fa-edit"></i> Gestionar Cursos
                </a>
            </div>

            <!-- Talleres CRUD -->
            <div class="crud-card talleres">
                <div class="crud-icon"><i class="fas fa-tools"></i></div>
                <div class="crud-title">Gestión de Talleres</div>
                <div class="crud-description">
                    Administra todos los talleres: crear, editar, eliminar, 
                    gestionar archivos multimedia y toda la información.
                </div>
                <a href="talleres_crud.php" class="crud-btn">
                    <i class="fas fa-edit"></i> Gestionar Talleres
                </a>
            </div>

            <!-- Jornadas CRUD -->
            <div class="crud-card jornadas">
                <div class="crud-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="crud-title">Gestión de Jornadas</div>
                <div class="crud-description">
                    Administra todas las jornadas: programar eventos, 
                    actualizar información, subir materiales y recursos.
                </div>
                <a href="jornadas_crud.php" class="crud-btn">
                    <i class="fas fa-edit"></i> Gestionar Jornadas
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="admin-footer">
            <a href="../index.php"><i class="fas fa-home"></i> Ir al Sitio Principal</a>
            <a href="../admin/index.php"><i class="fas fa-tachometer-alt"></i> Panel Administrativo</a>
            <a href="../login.php?logout=true"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <script>
        // Animaciones al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .crud-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>