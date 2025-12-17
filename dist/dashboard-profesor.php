<?php
/**
 * dashboard-profesor.php - Dashboard para Profesores
 * 
 * Características:
 * - Bienvenida personalizada
 * - Lista de alumnos conectados
 * - Gestión de calificaciones
 * - Registro de asistencia
 */

session_start();

// Verificar autenticación y que sea profesor
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'student') !== 'teacher') {
    header("Location: /phpweb/auth/login.php");
    exit;
}

require_once __DIR__ . '/../includes/conexionpdo.php';

// Obtener datos del profesor
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener lista de alumnos (estudiantes en la BD)
$stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE role = 'student' OR role IS NULL ORDER BY username");
$stmt->execute();
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /phpweb/index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Profesor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .navbar {
            background: linear-gradient(135deg, #0093E9 0%, #0077be 100%) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-bottom: 5px solid #005fa3;
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 22px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 10px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 8px 12px;
        }

        .dashboard-container {
            margin-left: 250px;
            max-width: 1400px;
            padding: 40px 30px;
            transition: margin-left 0.3s ease;
        }

        .dashboard-container.expanded {
            margin-left: 0;
        }

        .welcome-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .welcome-title {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .user-info {
            background: rgba(102, 126, 234, 0.1);
            border-left: 5px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }

        .alumnos-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 700;
            padding: 15px;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 15px;
            border-color: #eee;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .badge-alumno {
            background: #1565c0;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 10px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 8px 12px;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 8px 12px;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 56px;
            width: 250px;
            height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            z-index: 1000;
            transform: translateX(0);
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar .nav {
            flex-direction: column;
        }

        .sidebar .nav-link {
            color: #333 !important;
            margin: 0;
            padding: 15px 20px;
            border-left: 4px solid transparent;
            font-weight: 500;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            border-left-color: #667eea;
        }

        .sidebar .nav-link.active {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea !important;
            border-left-color: #667eea;
        }

        .toggle-sidebar-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-right: 15px;
        }

        .toggle-sidebar-btn:hover {
            opacity: 0.8;
        }

        .dashboard-container {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        .dashboard-container.expanded {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 24px;
            }

            .section-title {
                font-size: 22px;
            }

            .table {
                font-size: 14px;
            }

            .sidebar {
                width: 250px;
            }

            .dashboard-container {
                margin-left: 0;
            }

            .dashboard-container.expanded {
                margin-left: 250px;
            }

            .toggle-sidebar-btn {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 250px;
            }

            .dashboard-container {
                margin-left: 0;
            }

            .dashboard-container.expanded {
                margin-left: 250px;
            }

            .sidebar .nav-link {
                padding: 12px 15px;
                font-size: 13px;
            }

            .toggle-sidebar-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <button class="toggle-sidebar-btn" id="toggleSidebarBtn">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="/phpweb/dist/dashboard-profesor.php">
                <i class="fas fa-school"></i> Sistema de Escuela
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chalkboard-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> (Profesor)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="logout" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Izquierdo -->
    <div class="sidebar">
        <nav class="nav flex-column mt-3">
            <a class="nav-link active" href="/phpweb/dist/dashboard-profesor.php">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a class="nav-link" href="/phpweb/dist/profesor-alumnos.php">
                <i class="fas fa-users"></i> Gestionar Alumnos
            </a>
            <a class="nav-link" href="/phpweb/dist/profesor-cursos.php">
                <i class="fas fa-book"></i> Gestionar Cursos
            </a>
            <a class="nav-link" href="/phpweb/dist/profesor-asistencia.php">
                <i class="fas fa-check-circle"></i> Asistencia
            </a>
            <a class="nav-link" href="/phpweb/dist/reportes.php">
                <i class="fas fa-file-excel"></i> Reportes
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="welcome-title">
                👋 ¡Bienvenido Profesor!
            </div>
            <div class="welcome-subtitle">
                Accede a todas las herramientas de enseñanza
            </div>
            <div class="user-info">
                <strong>Profesor:</strong> <?php echo htmlspecialchars($profesor['username']); ?> | 
                <strong>Email:</strong> <?php echo htmlspecialchars($profesor['email']); ?>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($alumnos); ?></div>
                <div class="stat-label">Alumnos Registrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">6</div>
                <div class="stat-label">Cursos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div class="stat-label">Disponibilidad</div>
            </div>
        </div>

        <!-- Alumnos Section -->
        <div class="alumnos-section">
            <h2 class="section-title">👥 Lista de Alumnos (<?php echo count($alumnos); ?>)</h2>
            
            <?php if (!empty($alumnos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40%;"><i class="fas fa-user"></i> Nombre de Usuario</th>
                                <th style="width: 50%;"><i class="fas fa-envelope"></i> Email</th>
                                <th style="width: 10%; text-align: center;"><i class="fas fa-check-circle"></i> Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos as $alumno): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($alumno['username']); ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($alumno['email']); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge-alumno">
                                            <i class="fas fa-circle" style="color: #4CAF50;"></i> Registrado
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No hay alumnos registrados</h3>
                    <p>Aún no tienes alumnos en tu clase</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.querySelector('.sidebar');
        const container = document.querySelector('.dashboard-container');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('expanded');
        });

        // Cerrar sidebar al hacer click en un link en mobile
        if (window.innerWidth <= 768) {
            const sidebarLinks = sidebar.querySelectorAll('.nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    sidebar.classList.add('collapsed');
                    container.classList.remove('expanded');
                });
            });
        }
    </script>
</body>
</html>

