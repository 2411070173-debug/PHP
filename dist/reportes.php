<?php
/**
 * reportes.php - Página de Reportes para Profesores
 * 
 * Características:
 * - Reportes de desempeño de alumnos
 * - Estadísticas de asistencia
 * - Descargar reportes
 * - Filtros por curso y fecha
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

// Datos de reportes simulados
$reportes = [
    [
        'curso' => 'Matemáticas I',
        'total_alumnos' => 30,
        'aprobados' => 27,
        'reprobados' => 3,
        'promedio_curso' => 85.5,
        'asistencia_promedio' => 92,
    ],
    [
        'curso' => 'Física Básica',
        'total_alumnos' => 28,
        'aprobados' => 25,
        'reprobados' => 3,
        'promedio_curso' => 82.3,
        'asistencia_promedio' => 88,
    ],
    [
        'curso' => 'Literatura Contemporánea',
        'total_alumnos' => 32,
        'aprobados' => 31,
        'reprobados' => 1,
        'promedio_curso' => 88.7,
        'asistencia_promedio' => 95,
    ],
    [
        'curso' => 'Historia Universal',
        'total_alumnos' => 25,
        'aprobados' => 24,
        'reprobados' => 1,
        'promedio_curso' => 86.2,
        'asistencia_promedio' => 91,
    ],
    [
        'curso' => 'Biología General',
        'total_alumnos' => 29,
        'aprobados' => 28,
        'reprobados' => 1,
        'promedio_curso' => 89.1,
        'asistencia_promedio' => 93,
    ],
    [
        'curso' => 'Inglés Avanzado',
        'total_alumnos' => 26,
        'aprobados' => 24,
        'reprobados' => 2,
        'promedio_curso' => 84.6,
        'asistencia_promedio' => 89,
    ],
];

// Manejar logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /phpweb/index.php");
    exit;
}

// Calcular totales
$total_alumnos_general = array_sum(array_column($reportes, 'total_alumnos'));
$total_aprobados = array_sum(array_column($reportes, 'aprobados'));
$promedio_general = array_sum(array_column($reportes, 'promedio_curso')) / count($reportes);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Profesor</title>
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

        .navbar-dark .navbar-brand,
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 22px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .navbar-dark .nav-link,
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 10px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .navbar-dark .nav-link:hover,
        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 8px 12px;
        }

        .navbar-dark .nav-link.active,
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
            background: rgba(102, 126, 234, 0.1);
            color: #667eea !important;
            border-left-color: #667eea;
        }

        .dashboard-container {
            margin-left: 270px;
            max-width: 1400px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .dashboard-container.expanded {
            margin-left: 0;
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

        .page-title {
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
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

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 30px;
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .progreso-barra {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progreso-lleno {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.3s;
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
            <a class="nav-link" href="/phpweb/dist/dashboard-profesor.php">
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
            <a class="nav-link active" href="/phpweb/dist/reportes.php">
                <i class="fas fa-file-excel"></i> Reportes
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <h1 class="page-title">📊 Reportes Académicos</h1>

        <!-- Estadísticas Generales -->
        <div class="stat-cards-row">
            <div class="stat-card">
                <div style="font-size: 24px;">👥</div>
                <div class="stat-number"><?php echo $total_alumnos_general; ?></div>
                <div class="stat-label">Total de Alumnos</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 24px;">✅</div>
                <div class="stat-number"><?php echo $total_aprobados; ?></div>
                <div class="stat-label">Alumnos Aprobados</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 24px;">📈</div>
                <div class="stat-number"><?php echo number_format($promedio_general, 2); ?></div>
                <div class="stat-label">Promedio General</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 24px;">📚</div>
                <div class="stat-number"><?php echo count($reportes); ?></div>
                <div class="stat-label">Cursos Activos</div>
            </div>
        </div>

        <!-- Tabla de Reportes por Curso -->
        <div class="table-container">
            <h3 style="color: #333; margin-bottom: 20px;">📝 Desempeño por Curso</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-book"></i> Curso</th>
                        <th><i class="fas fa-users"></i> Total</th>
                        <th><i class="fas fa-check"></i> Aprobados</th>
                        <th><i class="fas fa-times"></i> Reprobados</th>
                        <th><i class="fas fa-graduation-cap"></i> Promedio</th>
                        <th><i class="fas fa-calendar-check"></i> Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportes as $reporte): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($reporte['curso']); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo $reporte['total_alumnos']; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?php echo $reporte['aprobados']; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-danger"><?php echo $reporte['reprobados']; ?></span>
                            </td>
                            <td>
                                <strong><?php echo number_format($reporte['promedio_curso'], 2); ?></strong>
                            </td>
                            <td>
                                <div style="min-width: 100px;">
                                    <small><?php echo $reporte['asistencia_promedio']; ?>%</small>
                                    <div class="progreso-barra">
                                        <div class="progreso-lleno" style="width: <?php echo $reporte['asistencia_promedio']; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Botones de Descarga -->
        <div style="margin-top: 30px; text-align: center;">
            <button class="btn btn-primary btn-lg" onclick="alert('Función de descarga de reportes en desarrollo')">
                <i class="fas fa-download"></i> Descargar Reporte Completo
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        if (toggleBtn) {
            const sidebar = document.querySelector('.sidebar');
            const container = document.querySelector('.main-content');

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                if (container) container.classList.toggle('expanded');
            });

            if (window.innerWidth <= 768) {
                const sidebarLinks = sidebar.querySelectorAll('.nav-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        sidebar.classList.add('collapsed');
                        if (container) container.classList.remove('expanded');
                    });
                });
            }
        }
    </script>
</body>
</html>





