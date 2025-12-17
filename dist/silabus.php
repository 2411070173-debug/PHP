<?php
/**
 * silabus.php - Sílabo y Material de Cursos para Alumnos
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

// Sílabos de cursos (datos falsos)
$silabus = [
    [
        'id' => 1,
        'curso' => 'Matemáticas I',
        'profesor' => 'Lic. García',
        'descripcion' => 'Curso introductorio de cálculo con énfasis en funciones, límites y derivadas.',
        'objetivos' => 'Que el alumno comprenda y aplique los conceptos fundamentales del cálculo diferencial.',
        'temas' => 'Funciones, Límites, Continuidad, Derivadas, Aplicaciones de derivadas'
    ],
    [
        'id' => 2,
        'curso' => 'Física Básica',
        'profesor' => 'Ing. López',
        'descripcion' => 'Fundamentos de mecánica clásica, termodinámica y ondas.',
        'objetivos' => 'Desarrollar la comprensión de los principios fundamentales de la física.',
        'temas' => 'Cinemática, Dinámica, Trabajo y Energía, Termodinámica, Ondas'
    ],
    [
        'id' => 3,
        'curso' => 'Literatura Contemporánea',
        'profesor' => 'Lic. Martínez',
        'descripcion' => 'Análisis de obras literarias del siglo XX y XXI en diversos idiomas.',
        'objetivos' => 'Desarrollar la capacidad crítica y analítica ante textos literarios.',
        'temas' => 'Realismo, Modernismo, Vanguardias, Posmodernismo, Narrativa Contemporánea'
    ],
    [
        'id' => 4,
        'curso' => 'Historia Universal',
        'profesor' => 'Lic. Rodríguez',
        'descripcion' => 'Recorrido histórico desde la Edad Antigua hasta la era contemporánea.',
        'objetivos' => 'Comprender el desarrollo histórico de la humanidad y sus implicaciones.',
        'temas' => 'Edad Antigua, Medieval, Moderna, Contemporánea, Guerras Mundiales'
    ],
    [
        'id' => 5,
        'curso' => 'Biología General',
        'profesor' => 'Dra. Fernández',
        'descripcion' => 'Estudio de los organismos vivos, su estructura, función y evolución.',
        'objetivos' => 'Que el alumno comprenda los principios fundamentales de la biología.',
        'temas' => 'Célula, Genética, Evolución, Ecología, Fisiología de Sistemas'
    ],
    [
        'id' => 6,
        'curso' => 'Inglés Avanzado',
        'profesor' => 'Prof. Thompson',
        'descripcion' => 'Desarrollo de habilidades avanzadas en comprensión y expresión en inglés.',
        'objetivos' => 'Alcanzar nivel C1 en inglés académico y profesional.',
        'temas' => 'Escritura Académica, Presentaciones, Negociaciones, Literatura Inglesa'
    ],
];

// Manejar descarga de sílabo
if (isset($_GET['descargar']) && !empty($_GET['descargar'])) {
    $curso_id = intval($_GET['descargar']);
    $curso = null;
    
    foreach ($silabus as $s) {
        if ($s['id'] == $curso_id) {
            $curso = $s;
            break;
        }
    }
    
    if ($curso) {
        // Generar contenido del PDF simulado
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="silabus_' . str_replace(' ', '_', $curso['curso']) . '.pdf"');
        
        // Crear contenido simple (en una app real usarías librería como FPDF)
        $pdf_content = "SILABO DEL CURSO\n";
        $pdf_content .= "================\n\n";
        $pdf_content .= "Curso: " . $curso['curso'] . "\n";
        $pdf_content .= "Profesor: " . $curso['profesor'] . "\n";
        $pdf_content .= "Fecha: " . date('d/m/Y') . "\n\n";
        $pdf_content .= "DESCRIPCION:\n";
        $pdf_content .= $curso['descripcion'] . "\n\n";
        $pdf_content .= "OBJETIVOS:\n";
        $pdf_content .= $curso['objetivos'] . "\n\n";
        $pdf_content .= "TEMAS A CUBRIR:\n";
        $pdf_content .= $curso['temas'] . "\n\n";
        $pdf_content .= "EVALUACION:\n";
        $pdf_content .= "- Participación en clase: 10%\n";
        $pdf_content .= "- Tareas y trabajos: 30%\n";
        $pdf_content .= "- Examen parcial: 30%\n";
        $pdf_content .= "- Examen final: 30%\n";
        
        echo $pdf_content;
        exit;
    }
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
    <title>Sílabo - Sistema de Escuela</title>
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

        .silabus-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .silabus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .silabus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }

        .silabus-titulo {
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .silabus-profesor {
            color: #667eea;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-descargar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .btn-descargar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .silabus-section {
            margin-bottom: 20px;
        }

        .silabus-section-title {
            font-weight: 700;
            color: #667eea;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .silabus-section-content {
            color: #555;
            line-height: 1.6;
            margin-left: 25px;
        }

        .temas-list {
            list-style: none;
            padding-left: 0;
        }

        .temas-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #555;
        }

        .temas-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 24px;
            }

            .silabus-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .silabus-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .silabus-titulo {
                font-size: 18px;
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
            <a class="nav-link active" href="/phpweb/dist/silabus.php">
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
            <i class="fas fa-file-alt"></i>
            Sílabos de Cursos
        </div>

        <div class="silabus-grid">
            <?php foreach ($silabus as $s): ?>
                <div class="silabus-card">
                    <div class="silabus-header">
                        <h5 class="silabus-titulo"><?php echo htmlspecialchars($s['curso']); ?></h5>
                        <p class="silabus-profesor">👨‍🏫 <?php echo htmlspecialchars($s['profesor']); ?></p>
                    </div>
                    <div class="silabus-body">
                        <div class="silabus-section">
                            <span class="silabus-label">Descripción</span>
                            <p class="silabus-text"><?php echo htmlspecialchars($s['descripcion']); ?></p>
                        </div>
                        <div class="silabus-section">
                            <span class="silabus-label">Objetivos</span>
                            <p class="silabus-text"><?php echo htmlspecialchars($s['objetivos']); ?></p>
                        </div>
                        <div class="silabus-section">
                            <span class="silabus-label">Temas</span>
                            <p class="silabus-text"><?php echo htmlspecialchars($s['temas']); ?></p>
                        </div>
                    </div>
                    <div class="silabus-footer">
                        <a href="?descargar=<?php echo $s['id']; ?>" class="btn-descargar">
                            <i class="fas fa-download"></i> Descargar PDF
                        </a>
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






