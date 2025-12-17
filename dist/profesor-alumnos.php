<?php
/**
 * profesor-alumnos.php - Gestión de Alumnos para Profesores
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

// Manejar eliminación de alumno
if (isset($_POST['eliminar_alumno']) && isset($_POST['alumno_id'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND (role = 'student' OR role IS NULL)");
    $stmt->execute([$alumno_id]);
    header("Location: /phpweb/dist/profesor-alumnos.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Alumnos - Sistema de Escuela</title>
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

        .alumnos-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }

        .titulo {
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .btn-agregar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-agregar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .tabla-alumnos {
            width: 100%;
        }

        .tabla-alumnos table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-alumnos thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .tabla-alumnos th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
        }

        .tabla-alumnos td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .tabla-alumnos tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .estado-activo {
            background: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-accion {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 5px;
        }

        .btn-editar {
            background: #007bff;
            color: white;
        }

        .btn-editar:hover {
            background: #0056b3;
        }

        .btn-eliminar {
            background: #dc3545;
            color: white;
        }

        .btn-eliminar:hover {
            background: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
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

            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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

            .tabla-alumnos {
                overflow-x: auto;
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
            <a class="nav-link active" href="/phpweb/dist/profesor-alumnos.php">
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

    <!-- Contenido Principal -->
    <div class="dashboard-container">
        

        <div class="alumnos-container">
            <div class="header-section">
                <div class="titulo">📝 Lista de Alumnos Registrados</div>
                <a href="#" class="btn-agregar">
                    <i class="fas fa-plus"></i> Agregar Alumno
                </a>
            </div>

            <?php if (!empty($alumnos)): ?>
                <div class="tabla-alumnos">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 35%;"><i class="fas fa-user"></i> Nombre de Usuario</th>
                                <th style="width: 40%;"><i class="fas fa-envelope"></i> Email</th>
                                <th style="width: 10%;"><i class="fas fa-check-circle"></i> Estado</th>
                                <th style="width: 10%; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos as $index => $alumno): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($alumno['id']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($alumno['username']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($alumno['email']); ?>
                                    </td>
                                    <td>
                                        <span class="estado-activo">✓ Activo</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="btn-accion btn-editar" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este alumno?');">
                                            <input type="hidden" name="alumno_id" value="<?php echo $alumno['id']; ?>">
                                            <button type="submit" name="eliminar_alumno" class="btn-accion btn-eliminar" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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






