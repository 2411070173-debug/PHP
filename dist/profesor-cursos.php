<?php
/**
 * profesor-cursos.php - Gestión de Cursos para Profesores
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

// Cursos de ejemplo (datos falsos)
$cursos = [
    ['id' => 1, 'nombre' => 'Matemáticas I', 'creditos' => 4, 'alumnos' => 25, 'estado' => 'Activo'],
    ['id' => 2, 'nombre' => 'Física Básica', 'creditos' => 3, 'alumnos' => 28, 'estado' => 'Activo'],
    ['id' => 3, 'nombre' => 'Literatura Contemporánea', 'creditos' => 3, 'alumnos' => 30, 'estado' => 'Activo'],
    ['id' => 4, 'nombre' => 'Historia Universal', 'creditos' => 2, 'alumnos' => 22, 'estado' => 'Activo'],
    ['id' => 5, 'nombre' => 'Biología General', 'creditos' => 4, 'alumnos' => 24, 'estado' => 'Activo'],
    ['id' => 6, 'nombre' => 'Inglés Avanzado', 'creditos' => 3, 'alumnos' => 20, 'estado' => 'Activo'],
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
    <title>Gestionar Cursos - Sistema de Escuela</title>
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
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .curso-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s;
        }

        .curso-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .curso-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
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

        .curso-stat {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #555;
        }

        .curso-stat i {
            width: 20px;
            color: #667eea;
            font-size: 14px;
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

        .curso-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            gap: 10px;
        }

        .btn-accion {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
            color: white;
        }

        .btn-editar {
            background: #007bff;
        }

        .btn-editar:hover {
            background: #0056b3;
            color: white;
        }

        .btn-eliminar {
            background: #dc3545;
        }

        .btn-eliminar:hover {
            background: #c82333;
            color: white;
        }

        .btn-agregar-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-agregar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .btn-agregar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content { margin-left: 0;
                padding: 15px;
            }

        .main-content.expanded {
            margin-left: 200px;
        }

            .page-title {
                font-size: 24px;
            }

            .cursos-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 150px;
            }

            .main-content {
                margin-left: 150px;
            }

        .main-content.expanded {
            margin-left: 200px;
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
            <a class="nav-link active" href="/phpweb/dist/profesor-cursos.php">
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

    <!-- Contenido Principal -->
    <div class="dashboard-container">
        <div class="page-title">
            <i class="fas fa-book"></i>
            Gestionar Cursos
        </div>

        <div class="btn-agregar-container">
            <button class="btn-agregar">
                <i class="fas fa-plus"></i> Crear Nuevo Curso
            </button>
        </div>

        <div class="cursos-grid">
            <?php foreach ($cursos as $curso): ?>
                <div class="curso-card">
                    <div class="curso-header">
                        <h5 class="curso-nombre"><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                        <span class="curso-creditos"><?php echo $curso['creditos']; ?> créditos</span>
                    </div>
                    <div class="curso-body">
                        <div class="curso-stat">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong><?php echo $curso['alumnos']; ?> alumnos</strong> matriculados
                            </div>
                        </div>
                        <div class="curso-stat">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                Estado: <span class="estado-badge"><?php echo $curso['estado']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="curso-footer">
                        <button class="btn-accion btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-accion btn-eliminar" onclick="confirm('¿Estás seguro?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
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
            const container = document.querySelector('.dashboard-container');

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






