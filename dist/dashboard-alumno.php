<?php
/**
 * dashboard-alumno.php - Dashboard para Alumnos
 * 
 * Características:
 * - Bienvenida personalizada
 * - Acceso a Cursos
 * - Proyectos
 * - Horario
 * - Asistencia
 * - Sílabo
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

// Cursos de prueba (decorativos)
$cursos = [
    ['id' => 1, 'nombre' => 'Matemáticas I', 'profesor' => 'Lic. García', 'creditos' => 4],
    ['id' => 2, 'nombre' => 'Física Básica', 'profesor' => 'Ing. López', 'creditos' => 3],
    ['id' => 3, 'nombre' => 'Literatura Contemporánea', 'profesor' => 'Lic. Martínez', 'creditos' => 3],
    ['id' => 4, 'nombre' => 'Historia Universal', 'profesor' => 'Lic. Rodríguez', 'creditos' => 2],
    ['id' => 5, 'nombre' => 'Biología General', 'profesor' => 'Dra. Fernández', 'creditos' => 4],
    ['id' => 6, 'nombre' => 'Inglés Avanzado', 'profesor' => 'Prof. Thompson', 'creditos' => 3],
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
    <title>Dashboard - Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  padding: 20px 0;
}

.navbar {
  background: linear-gradient(135deg, #0093e9 0%, #0077be 100%) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  border-bottom: 5px solid #005fa3;
}

.navbar-brand {
  font-weight: 700;
  color: white !important;
  font-size: 22px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.menu-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  padding: 30px;
  text-align: center;
  transition: all 0.3s;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.menu-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

.menu-title {
  font-size: 18px;
  font-weight: 700;
  color: #333;
  margin-bottom: 10px;
}

.menu-desc {
  font-size: 13px;
  color: #999;
}

.cursos-section {
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

.curso-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s;
}

.curso-card:hover {
  transform: translateX(5px);
  box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.curso-info h5 {
  margin: 0 0 5px 0;
  font-weight: 700;
}

.curso-info p {
  margin: 0;
  font-size: 13px;
  opacity: 0.9;
}

.curso-creditos {
  background: rgba(255, 255, 255, 0.2);
  padding: 8px 15px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 12px;
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

.logout-btn {
  background: #dc3545;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: #c82333;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .dashboard-container {
    margin-left: 0;
    padding: 20px 15px;
  }

  .dashboard-container.expanded {
    margin-left: 250px;
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
            <a class="nav-link active" href="/phpweb/dist/dashboard-alumno.php">
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
            <a class="nav-link" href="/phpweb/dist/calificaciones.php">
                <i class="fas fa-star"></i> Calificaciones
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="welcome-title">
                👋 ¡Bienvenido Alumno!
            </div>
            <div class="welcome-subtitle">
                Accede a todos tus recursos académicos
            </div>
            <div class="user-info">
                <strong>Alumno:</strong> <?php echo htmlspecialchars($alumno['username']); ?> | 
                <strong>Email:</strong> <?php echo htmlspecialchars($alumno['email']); ?>
            </div>
        </div>

        <!-- Menu de Opciones -->
        <div class="menu-grid">
            <div class="menu-card">
                <div class="menu-icon">📚</div>
                <div class="menu-title">Cursos</div>
                <div class="menu-desc">Ver mis cursos matriculados</div>
            </div>
            <div class="menu-card">
                <div class="menu-icon">📋</div>
                <div class="menu-title">Proyectos</div>
                <div class="menu-desc">Trabajos y proyectos asignados</div>
            </div>
            <div class="menu-card">
                <div class="menu-icon">⏰</div>
                <div class="menu-title">Horario</div>
                <div class="menu-desc">Mi horario de clases</div>
            </div>
            <div class="menu-card">
                <div class="menu-icon">✅</div>
                <div class="menu-title">Asistencia</div>
                <div class="menu-desc">Registro de asistencia</div>
            </div>
            <div class="menu-card">
                <div class="menu-icon">📖</div>
                <div class="menu-title">Sílabo</div>
                <div class="menu-desc">Información del curso</div>
            </div>
            <div class="menu-card">
                <div class="menu-icon">📊</div>
                <div class="menu-title">Calificaciones</div>
                <div class="menu-desc">Mis notas y resultados</div>
            </div>
        </div>

        <!-- Cursos Section -->
        <div class="cursos-section">
            <h2 class="section-title">📚 Mis Cursos (<?php echo count($cursos); ?>)</h2>
            
            <?php foreach ($cursos as $curso): ?>
                <div class="curso-card">
                    <div class="curso-info">
                        <h5><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                        <p><strong>Profesor:</strong> <?php echo htmlspecialchars($curso['profesor']); ?></p>
                    </div>
                    <div class="curso-creditos">
                        <?php echo $curso['creditos']; ?> créditos
                    </div>
                </div>
            <?php endforeach; ?>
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

