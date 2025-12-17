<?php
/**
 * calificaciones.php - Página de Calificaciones para Alumnos
 * 
 * Características:
 * - Ver calificaciones por curso
 * - Promedio general
 * - Historial de calificaciones
 * - Detalles por materia
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

// Datos de calificaciones (fake data para demostración)
$calificaciones = [
    ['curso' => 'Matemáticas I', 'profesor' => 'Lic. García', 'calificacion' => 85, 'estado' => 'Aprobado'],
    ['curso' => 'Física Básica', 'profesor' => 'Ing. López', 'calificacion' => 78, 'estado' => 'Aprobado'],
    ['curso' => 'Literatura Contemporánea', 'profesor' => 'Lic. Martínez', 'calificacion' => 92, 'estado' => 'Aprobado'],
    ['curso' => 'Historia Universal', 'profesor' => 'Lic. Rodríguez', 'calificacion' => 88, 'estado' => 'Aprobado'],
    ['curso' => 'Biología General', 'profesor' => 'Dra. Fernández', 'calificacion' => 95, 'estado' => 'Aprobado'],
    ['curso' => 'Inglés Avanzado', 'profesor' => 'Prof. Thompson', 'calificacion' => 82, 'estado' => 'Aprobado'],
];

// Calcular promedio
$promedio = array_reduce($calificaciones, fn($sum, $item) => $sum + $item['calificacion'], 0) / count($calificaciones);

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
    <title>Calificaciones - Alumno</title>
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

        .promedio-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .promedio-valor {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin: 20px 0;
        }

        .promedio-estado {
            font-size: 18px;
            color: #27ae60;
            font-weight: 600;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
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

        .badge-aprobado {
            background: #27ae60;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .calificacion-excelente {
            color: #27ae60;
            font-weight: 700;
            font-size: 18px;
        }

        .calificacion-buena {
            color: #f39c12;
            font-weight: 700;
            font-size: 18px;
        }

        .calificacion-regular {
            color: #e74c3c;
            font-weight: 700;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .dashboard-container {
                margin-left: 220px;
                max-width: calc(100% - 220px);
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 150px;
                padding: 10px;
            }
            .sidebar .nav-link {
                padding: 8px 10px !important;
                font-size: 13px;
            }
            .dashboard-container {
                margin-left: 170px;
                max-width: calc(100% - 170px);
                padding: 15px;
            }
            .page-title {
                font-size: 24px;
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
            <a class="nav-link" href="/phpweb/dist/asistencia.php">
                <i class="fas fa-check-circle"></i> Asistencia
            </a>
            <a class="nav-link" href="/phpweb/dist/horario.php">
                <i class="fas fa-calendar-alt"></i> Horario
            </a>
            <a class="nav-link" href="/phpweb/dist/silabus.php">
                <i class="fas fa-file-alt"></i> Sílabo
            </a>
            <a class="nav-link active" href="/phpweb/dist/calificaciones.php">
                <i class="fas fa-star"></i> Calificaciones
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <h1 class="page-title">⭐ Mis Calificaciones</h1>

        <!-- Promedio Card -->
        <div class="promedio-card">
            <h2>📊 Promedio General</h2>
            <div class="promedio-valor"><?php echo number_format($promedio, 2); ?></div>
            <div class="promedio-estado">✅ APROBADO</div>
            <p style="color: #666; margin-top: 15px;">Excelente desempeño académico</p>
        </div>

        <!-- Tabla de Calificaciones -->
        <div class="table-container">
            <h3 style="color: #333; margin-bottom: 20px;">📝 Detalles por Curso</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-book"></i> Curso</th>
                        <th><i class="fas fa-user"></i> Profesor</th>
                        <th><i class="fas fa-graduation-cap"></i> Calificación</th>
                        <th><i class="fas fa-check"></i> Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calificaciones as $cal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cal['curso']); ?></td>
                            <td><?php echo htmlspecialchars($cal['profesor']); ?></td>
                            <td>
                                <?php 
                                    $nota = $cal['calificacion'];
                                    if ($nota >= 90) {
                                        echo "<span class='calificacion-excelente'>{$nota}</span>";
                                    } elseif ($nota >= 80) {
                                        echo "<span class='calificacion-buena'>{$nota}</span>";
                                    } else {
                                        echo "<span class='calificacion-regular'>{$nota}</span>";
                                    }
                                ?>
                            </td>
                            <td><span class="badge-aprobado"><?php echo htmlspecialchars($cal['estado']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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





