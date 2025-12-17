<?php
/**
 * horario.php - Horario de Clases para Alumnos
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

// Horario de clases (datos falsos)
$horario = [
    ['hora' => '08:00 - 10:00', 'lunes' => ['curso' => 'Matemáticas I', 'sala' => '101', 'profesor' => 'Lic. García'], 'martes' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'miercoles' => ['curso' => 'Matemáticas I', 'sala' => '101', 'profesor' => 'Lic. García'], 'jueves' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'viernes' => ['curso' => 'Biología General', 'sala' => '401', 'profesor' => 'Dra. Fernández']],
    ['hora' => '10:00 - 12:00', 'lunes' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'martes' => ['curso' => 'Física Básica', 'sala' => '205', 'profesor' => 'Ing. López'], 'miercoles' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'jueves' => ['curso' => 'Física Básica', 'sala' => '205', 'profesor' => 'Ing. López'], 'viernes' => ['curso' => 'Biología General', 'sala' => '401', 'profesor' => 'Dra. Fernández']],
    ['hora' => '12:00 - 13:30', 'lunes' => ['curso' => 'ALMUERZO', 'sala' => 'CAFETERÍA', 'profesor' => ''], 'martes' => ['curso' => 'ALMUERZO', 'sala' => 'CAFETERÍA', 'profesor' => ''], 'miercoles' => ['curso' => 'ALMUERZO', 'sala' => 'CAFETERÍA', 'profesor' => ''], 'jueves' => ['curso' => 'ALMUERZO', 'sala' => 'CAFETERÍA', 'profesor' => ''], 'viernes' => ['curso' => 'ALMUERZO', 'sala' => 'CAFETERÍA', 'profesor' => '']],
    ['hora' => '13:30 - 15:30', 'lunes' => ['curso' => 'Literatura Contemporánea', 'sala' => '302', 'profesor' => 'Lic. Martínez'], 'martes' => ['curso' => 'Inglés Avanzado', 'sala' => '104', 'profesor' => 'Prof. Thompson'], 'miercoles' => ['curso' => 'Literatura Contemporánea', 'sala' => '302', 'profesor' => 'Lic. Martínez'], 'jueves' => ['curso' => 'Inglés Avanzado', 'sala' => '104', 'profesor' => 'Prof. Thompson'], 'viernes' => ['curso' => '', 'sala' => '', 'profesor' => '']],
    ['hora' => '15:30 - 17:30', 'lunes' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'martes' => ['curso' => 'Historia Universal', 'sala' => '103', 'profesor' => 'Lic. Rodríguez'], 'miercoles' => ['curso' => '', 'sala' => '', 'profesor' => ''], 'jueves' => ['curso' => 'Historia Universal', 'sala' => '103', 'profesor' => 'Lic. Rodríguez'], 'viernes' => ['curso' => '', 'sala' => '', 'profesor' => '']],
];

$dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes'];

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
    <title>Horario - Sistema de Escuela</title>
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

        .horario-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            overflow-x: auto;
        }

        .horario-tabla {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .horario-tabla thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .horario-tabla th {
            padding: 15px;
            font-weight: 700;
            text-align: center;
            border: 2px solid #667eea;
        }

        .horario-tabla td {
            padding: 15px;
            text-align: center;
            border: 2px solid #dee2e6;
            min-height: 100px;
            vertical-align: middle;
        }

        .horario-tabla tbody tr:nth-child(odd) {
            background: #f8f9fa;
        }

        .horario-tabla tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .hora-column {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            width: 120px;
            min-width: 120px;
        }

        .clase-vacia {
            background: #f5f5f5;
            color: #999;
        }

        .clase-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 8px;
            font-size: 12px;
        }

        .clase-nombre {
            font-weight: 700;
            margin-bottom: 3px;
        }

        .clase-detalle {
            font-size: 11px;
            opacity: 0.9;
            margin: 2px 0;
        }

        .almuerzo {
            background: linear-gradient(135deg, #f6b319 0%, #f29a00 100%);
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 24px;
            }

            .horario-tabla th,
            .horario-tabla td {
                padding: 10px;
                font-size: 12px;
            }

            .horario-container {
                padding: 15px;
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
            <a class="nav-link" href="/phpweb/dist/cursos.php">
                <i class="fas fa-book"></i> Cursos
            </a>
            <a class="nav-link" href="/phpweb/dist/asistencia.php">
                <i class="fas fa-check-circle"></i> Asistencia
            </a>
            <a class="nav-link active" href="/phpweb/dist/horario.php">
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
            <i class="fas fa-calendar-alt"></i>
            Mi Horario de Clases
        </div>

        <div class="horario-container">
            <table class="horario-tabla">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horario as $bloque): ?>
                        <tr>
                            <td class="hora-column">
                                <i class="fas fa-clock"></i> <?php echo $bloque['hora']; ?>
                            </td>
                            <?php foreach ($dias as $diaKey => $diaTexto): ?>
                                <td>
                                    <?php
                                    $clase = $bloque[$diaKey];
                                    if (!empty($clase['curso'])) {
                                        $cardClass = ($clase['curso'] === 'ALMUERZO') ? 'clase-card almuerzo' : 'clase-card';
                                        ?>
                                        <div class="<?php echo $cardClass; ?>">
                                            <div class="clase-nombre"><?php echo htmlspecialchars($clase['curso']); ?></div>
                                            <div class="clase-detalle">🚪 <?php echo htmlspecialchars($clase['sala']); ?></div>
                                            <?php if (!empty($clase['profesor'])): ?>
                                                <div class="clase-detalle">👨‍🏫 <?php echo htmlspecialchars($clase['profesor']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="clase-vacia">—</div>
                                        <?php
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
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






