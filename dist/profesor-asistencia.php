<?php
/**
 * profesor-asistencia.php - Registro de Asistencia para Profesores
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

// Alumnos para registro de asistencia (datos falsos)
$alumnos = [
    ['id' => 1, 'nombre' => 'Juan García'],
    ['id' => 2, 'nombre' => 'María López'],
    ['id' => 3, 'nombre' => 'Carlos Martínez'],
    ['id' => 4, 'nombre' => 'Ana Rodríguez'],
    ['id' => 5, 'nombre' => 'Pedro Fernández'],
    ['id' => 6, 'nombre' => 'Rosa Sánchez'],
];

// Manejar logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /phpweb/index.php");
    exit;
}

// Manejar envío de asistencia
if (isset($_POST['guardar_asistencia'])) {
    // Aquí iría la lógica para guardar en BD
    // Por ahora es solo un placeholder
    $mensaje = "✓ Asistencia guardada correctamente";
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

        .asistencia-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .form-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .asistencia-tabla {
            margin-top: 25px;
        }

        .tabla-titulo {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .tabla-asistencia table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-asistencia thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .tabla-asistencia th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
        }

        .tabla-asistencia td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }

        .tabla-asistencia tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .alumno-nombre {
            text-align: left;
            font-weight: 600;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="radio"],
        .checkbox-item input[type="checkbox"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-guardar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            margin-top: 20px;
        }

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .alert {
            margin-top: 15px;
            border-radius: 8px;
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

            .checkbox-container {
                flex-wrap: wrap;
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

            .tabla-asistencia th,
            .tabla-asistencia td {
                padding: 10px;
                font-size: 12px;
            }

            .checkbox-container {
                gap: 10px;
            }

            .checkbox-item label {
                font-size: 12px;
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
            <a class="nav-link" href="/phpweb/dist/profesor-cursos.php">
                <i class="fas fa-book"></i> Gestionar Cursos
            </a>
            <a class="nav-link active" href="/phpweb/dist/profesor-asistencia.php">
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
            <i class="fas fa-check-circle"></i>
            Registro de Asistencia
        </div>

        <div class="asistencia-container">
            <form method="POST">
                <!-- Sección de filtros -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">📅 Selecciona Fecha</label>
                            <input type="date" class="form-control" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">📚 Selecciona Curso</label>
                            <select class="form-control" name="curso">
                                <option>Matemáticas I</option>
                                <option>Física Básica</option>
                                <option>Literatura Contemporánea</option>
                                <option>Historia Universal</option>
                                <option>Biología General</option>
                                <option>Inglés Avanzado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">🕐 Hora de Clase</label>
                            <input type="time" class="form-control" name="hora" value="08:00">
                        </div>
                    </div>
                </div>

                <!-- Tabla de asistencia -->
                <div class="asistencia-tabla">
                    <div class="tabla-titulo">📋 Registrar Asistencia de Alumnos</div>
                    <div class="tabla-asistencia">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 40%; text-align: left;">👤 Alumno</th>
                                    <th style="width: 60%; text-align: center;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alumnos as $alumno): ?>
                                    <tr>
                                        <td class="alumno-nombre"><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                                        <td>
                                            <div class="checkbox-container">
                                                <div class="checkbox-item">
                                                    <input type="radio" id="presente_<?php echo $alumno['id']; ?>" name="asistencia_<?php echo $alumno['id']; ?>" value="presente" checked>
                                                    <label for="presente_<?php echo $alumno['id']; ?>">✓ Presente</label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <input type="radio" id="tardio_<?php echo $alumno['id']; ?>" name="asistencia_<?php echo $alumno['id']; ?>" value="tardio">
                                                    <label for="tardio_<?php echo $alumno['id']; ?>">⏱ Tardío</label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <input type="radio" id="ausente_<?php echo $alumno['id']; ?>" name="asistencia_<?php echo $alumno['id']; ?>" value="ausente">
                                                    <label for="ausente_<?php echo $alumno['id']; ?>">✗ Ausente</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botón guardar -->
                <button type="submit" name="guardar_asistencia" class="btn-guardar">
                    <i class="fas fa-save"></i> Guardar Asistencia
                </button>

                <?php if (isset($mensaje)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>✓ Éxito!</strong> <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </form>
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






