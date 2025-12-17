<?php
/**
 * asistencia.php - Registro de Asistencia para Alumnos
 */

session_start();

// Verificar autenticación y que sea alumno
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'student') !== 'student') {
    header("Location: /phpweb/auth/login.php");
    exit;
}

require_once __DIR__ . '/../includes/conexionpdo.php';

// Obtener datos del alumno
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Datos falsos de asistencia
$asistencia = [
    ['id' => 1, 'curso' => 'Matemáticas I', 'fecha' => '2025-12-08', 'estado' => 'Presente', 'porcentaje' => 95],
    ['id' => 2, 'curso' => 'Matemáticas I', 'fecha' => '2025-12-06', 'estado' => 'Presente', 'porcentaje' => 95],
    ['id' => 3, 'curso' => 'Física Básica', 'fecha' => '2025-12-09', 'estado' => 'Presente', 'porcentaje' => 92],
    ['id' => 4, 'curso' => 'Física Básica', 'fecha' => '2025-12-07', 'estado' => 'Tardío', 'porcentaje' => 92],
    ['id' => 5, 'curso' => 'Literatura Contemporánea', 'fecha' => '2025-12-08', 'estado' => 'Presente', 'porcentaje' => 88],
    ['id' => 6, 'curso' => 'Literatura Contemporánea', 'fecha' => '2025-12-06', 'estado' => 'Ausente', 'porcentaje' => 88],
    ['id' => 7, 'curso' => 'Historia Universal', 'fecha' => '2025-12-09', 'estado' => 'Presente', 'porcentaje' => 100],
    ['id' => 8, 'curso' => 'Historia Universal', 'fecha' => '2025-12-07', 'estado' => 'Presente', 'porcentaje' => 100],
    ['id' => 9, 'curso' => 'Biología General', 'fecha' => '2025-12-08', 'estado' => 'Presente', 'porcentaje' => 97],
    ['id' => 10, 'curso' => 'Inglés Avanzado', 'fecha' => '2025-12-09', 'estado' => 'Presente', 'porcentaje' => 85],
];

// Agrupar por curso
$asistenciaPorCurso = [];
foreach ($asistencia as $registro) {
    $curso = $registro['curso'];
    if (!isset($asistenciaPorCurso[$curso])) {
        $asistenciaPorCurso[$curso] = ['registros' => [], 'porcentaje' => 0];
    }
    $asistenciaPorCurso[$curso]['registros'][] = $registro;
    $asistenciaPorCurso[$curso]['porcentaje'] = $registro['porcentaje'];
}

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
    <title>Asistencia - Sistema de Escuela</title>
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

        .main-content {
            margin-left: 250px;
            max-width: 1400px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
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

        @media (max-width: 768px) {
            .toggle-sidebar-btn {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .sidebar {
                width: 250px;
            }
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .curso-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }

        .curso-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }

        .curso-titulo {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }

        .porcentaje-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 13px;
        }

        .tabla-asistencia {
            margin-top: 15px;
        }

        .tabla-asistencia table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-asistencia thead {
            background: #f8f9fa;
        }

        .tabla-asistencia th {
            color: #333;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        .tabla-asistencia td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .tabla-asistencia tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .estado-presente {
            color: #28a745;
            font-weight: 600;
        }

        .estado-tardio {
            color: #ffc107;
            font-weight: 600;
        }

        .estado-ausente {
            color: #dc3545;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 24px;
            }

            .curso-section {
                padding: 15px;
                margin-bottom: 15px;
            }

            .tabla-asistencia th,
            .tabla-asistencia td {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <button class="toggle-sidebar-btn" id="toggleSidebarBtn">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="/phpweb/dist/dashboard-alumno.php">
                <i class="fas fa-school"></i> Sistema de Escuela
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> (Alumno)
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
            <a class="nav-link" href="/phpweb/dist/dashboard-alumno.php">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a class="nav-link" href="/phpweb/dist/cursos.php">
                <i class="fas fa-book"></i> Cursos
            </a>
            <a class="nav-link active" href="/phpweb/dist/asistencia.php">
                <i class="fas fa-check-circle"></i> Asistencia
            </a>
            <a class="nav-link" href="/phpweb/dist/horario.php">
                <i class="fas fa-calendar-alt"></i> Horario
            </a>
            <a class="nav-link" href="/phpweb/dist/silabus.php">
                <i class="fas fa-file-alt"></i> Sílabo
            </a>
            <a class="nav-link" href="/phpweb/dist/calificaciones.php">
                <i class="fas fa-star"></i> Calificaciones
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-check-circle"></i>
            Registro de Asistencia
        </div>

        <?php foreach ($asistenciaPorCurso as $curso => $datos): ?>
            <div class="curso-section">
                <div class="curso-header">
                    <div class="curso-titulo"><?php echo htmlspecialchars($curso); ?></div>
                    <div class="porcentaje-badge">
                        📊 Asistencia: <?php echo $datos['porcentaje']; ?>%
                    </div>
                </div>

                <div class="tabla-asistencia">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['registros'] as $registro): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></td>
                                    <td>
                                        <?php
                                        $estadoClass = 'estado-presente';
                                        if ($registro['estado'] === 'Ausente') {
                                            $estadoClass = 'estado-ausente';
                                        } elseif ($registro['estado'] === 'Tardío') {
                                            $estadoClass = 'estado-tardio';
                                        }
                                        ?>
                                        <span class="estado-badge <?php echo $estadoClass; ?>">
                                            <?php echo $registro['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        if ($registro['estado'] === 'Presente') {
                                            echo '✓ Presente en clase';
                                        } elseif ($registro['estado'] === 'Ausente') {
                                            echo '✗ No asistió';
                                        } else {
                                            echo '⏱ Llegó tarde';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
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





