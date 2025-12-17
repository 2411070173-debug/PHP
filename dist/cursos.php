<?php
/**
 * cursos.php - Listado de Cursos para Alumnos
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

// Cursos de prueba con más información
$cursos = [
    ['id' => 1, 'nombre' => 'Matemáticas I', 'profesor' => 'Lic. García', 'creditos' => 4, 'sala' => '101', 'horario' => 'Lun-Mié 8:00-10:00'],
    ['id' => 2, 'nombre' => 'Física Básica', 'profesor' => 'Ing. López', 'creditos' => 3, 'sala' => '205', 'horario' => 'Mar-Jue 10:00-12:00'],
    ['id' => 3, 'nombre' => 'Literatura Contemporánea', 'profesor' => 'Lic. Martínez', 'creditos' => 3, 'sala' => '302', 'horario' => 'Lun-Mié 14:00-16:00'],
    ['id' => 4, 'nombre' => 'Historia Universal', 'profesor' => 'Lic. Rodríguez', 'creditos' => 2, 'sala' => '103', 'horario' => 'Mar-Jue 16:00-18:00'],
    ['id' => 5, 'nombre' => 'Biología General', 'profesor' => 'Dra. Fernández', 'creditos' => 4, 'sala' => '401', 'horario' => 'Lun-Mié-Vie 9:00-11:00'],
    ['id' => 6, 'nombre' => 'Inglés Avanzado', 'profesor' => 'Prof. Thompson', 'creditos' => 3, 'sala' => '104', 'horario' => 'Mar-Jue 13:00-15:00'],
];

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
    <title>Mis Cursos - Sistema de Escuela</title>
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

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .curso-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }

        .curso-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .curso-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .curso-nombre {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .curso-creditos {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .curso-body {
            padding: 20px;
        }

        .curso-detail {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }

        .curso-detail i {
            width: 20px;
            color: #667eea;
            font-size: 14px;
        }

        .curso-detail strong {
            color: #333;
        }

        .estado-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .main-content.expanded {
                margin-left: 200px;
            }

            .toggle-sidebar-btn {
                display: block;
            }

            .cursos-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 24px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 150px;
            }

            .main-content {
                margin-left: 150px;
            }

            .sidebar .nav-link {
                padding: 12px 15px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
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
            <a class="nav-link active" href="/phpweb/dist/cursos.php">
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
            <a class="nav-link" href="/phpweb/dist/calificaciones.php">
                <i class="fas fa-star"></i> Calificaciones
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-book"></i>
            Mis Cursos
        </div>

        <div class="cursos-grid">
            <?php foreach ($cursos as $curso): ?>
                <div class="curso-card">
                    <div class="curso-header">
                        <h5 class="curso-nombre"><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                        <span class="curso-creditos"><?php echo $curso['creditos']; ?> créditos</span>
                    </div>
                    <div class="curso-body">
                        <div class="curso-detail">
                            <i class="fas fa-chalkboard-user"></i>
                            <div>
                                <strong>Profesor:</strong> <?php echo htmlspecialchars($curso['profesor']); ?>
                            </div>
                        </div>
                        <div class="curso-detail">
                            <i class="fas fa-door-open"></i>
                            <div>
                                <strong>Sala:</strong> <?php echo htmlspecialchars($curso['sala']); ?>
                            </div>
                        </div>
                        <div class="curso-detail">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Horario:</strong> <?php echo htmlspecialchars($curso['horario']); ?>
                            </div>
                        </div>
                        <div class="curso-detail" style="margin-top: 15px;">
                            <span class="estado-badge">✓ Matriculado</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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




