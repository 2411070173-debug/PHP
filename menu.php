<?php
/**
 * PAGINA PRINCIPAL
 * 
 * 
 * index.php - Página de inicio con CRUD
 * 
 * Características:
 * - Tabla de usuarios (ID, Username, Email, Teléfono)
 * - Crear nuevo usuario
 * - Editar usuario
 * - Eliminar usuario
 * - Generar PDF
 * - Búsqueda de usuarios
 * - Botones de Login y Registro
 */

session_start();

require_once __DIR__ . '/crud_handler.php';
require_once __DIR__ . '/pdf_generator.php';

// ============================================================================
// PROCESAR ACCIONES DEL CRUD
// ============================================================================

$message = ['type' => '', 'text' => ''];

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $result = createUser(
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['password'] ?? ''
    );
    
    $message = [
        'type' => $result['success'] ? 'success' : 'error',
        'text' => $result['message']
    ];
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $result = updateUser(
        $_POST['id'] ?? 0,
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['phone'] ?? ''
    );
    
    $message = [
        'type' => $result['success'] ? 'success' : 'error',
        'text' => $result['message']
    ];
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $result = deleteUser($_POST['id'] ?? 0);
    
    $message = [
        'type' => $result['success'] ? 'success' : 'error',
        'text' => $result['message']
    ];
}

// Obtener usuario para editar
$editing_user = null;
if (isset($_GET['edit'])) {
    $editing_user = getUserById($_GET['edit']);
}

// Búsqueda
$search = $_GET['search'] ?? '';
$users = getAllUsers($search);
$stats = getUserStats();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - CRUD</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0093E9;
            --secondary-color: #80D0C7;
            --danger-color: #DC3545;
            --success-color: #28A745;
            --warning-color: #FFC107;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(-45deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #0093E9 0%, #0077be 100%) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-bottom: 5px solid #005fa3;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 28px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }
        
        .navbar-brand i {
            margin-right: 12px;
            font-size: 32px;
        }

        /* Auth Buttons */
        .navbar-auth {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-right: 20px;
        }

        .btn-login, .btn-register {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid white;
            cursor: pointer;
            font-size: 14px;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            background: white;
            color: #0093E9;
        }

        .btn-login:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: #0077be;
        }

        .btn-register {
            background: #80D0C7;
            color: white;
            border-color: #80D0C7;
        }

        .btn-register:hover {
            background: #6ab5a7;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .user-profile-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .profile-avatar:hover {
            transform: scale(1.1);
        }

        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
        }

        .dropdown-item.logout {
            color: var(--danger-color);
        }

        .dropdown-item.logout:hover {
            background: var(--danger-color);
            color: white;
        }
        
        /* Header */
        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .page-header p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }
        
        /* Container */
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        /* Stats */
        .stats-box {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-box .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stats-box .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Botones */
        .btn-primary {
            background: var(--primary-color) !important;
            border: none;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #0077be !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 147, 233, 0.3);
        }
        
        .btn-danger {
            background: var(--danger-color) !important;
            border: none;
        }
        
        .btn-success {
            background: var(--success-color) !important;
            border: none;
        }
        
        .btn-warning {
            background: var(--warning-color) !important;
            border: none;
            color: black;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        /* Tabla */
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--primary-color), #0077be);
            color: white;
        }
        
        .table thead th {
            border: none;
            font-weight: 600;
            padding: 15px;
            text-align: center;
        }
        
        .table tbody td {
            vertical-align: middle;
            padding: 12px 15px;
            border-color: #eee;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Formulario */
        .form-card {
            background: #f8f9ff;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-card h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }
        
        .form-floating > label {
            color: var(--primary-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 147, 233, 0.25);
        }
        
        /* Alertas */
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Badge */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        
        /* Modal */
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-title {
            font-weight: 700;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        /* Búsqueda */
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-box input {
            border-radius: 25px;
            padding: 10px 20px;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 147, 233, 0.25);
        }
        
        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #999;
            margin: 20px 0;
        }
        
        .empty-state p {
            color: #bbb;
        }
        
        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            padding: 30px 0;
            margin-top: 40px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 0;
            }

            .navbar-brand {
                font-size: 20px;
            }

            .navbar-brand i {
                font-size: 24px;
            }

            .navbar-auth {
                gap: 8px;
                margin-right: 10px;
            }

            .btn-login, .btn-register {
                padding: 10px 15px;
                font-size: 12px;
            }

            .page-header {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 24px;
            }
            
            .table {
                font-size: 13px;
            }
            
            .table thead th {
                padding: 10px 5px;
            }
            
            .table tbody td {
                padding: 8px 5px;
            }
            
            .btn-sm {
                padding: 3px 6px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="menu.php">
                <i class="bi bi-school"></i>Sistema de Escuela
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto navbar-auth">
                    <a href="/phpweb/auth/login.php" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                    <a href="/phpweb/auth/registrar.php" class="btn btn-register">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-lg" style="margin-top: 30px; margin-bottom: 30px;">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="bi bi-table"></i> Gestión de Usuarios</h1>
            <p>Sistema CRUD con conexión a base de datos MySQL</p>
        </div>
        
        <!-- Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-box">
                    <i class="bi bi-people" style="font-size: 32px;"></i>
                    <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Total de Usuarios</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box">
                    <i class="bi bi-database" style="font-size: 32px;"></i>
                    <div class="stat-number"><?php echo count($users); ?></div>
                    <div class="stat-label">Usuarios Mostrados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box">
                    <i class="bi bi-calendar-event" style="font-size: 32px;"></i>
                    <div class="stat-number"><?php echo date('d/m/Y'); ?></div>
                    <div class="stat-label">Fecha Actual</div>
                </div>
            </div>
        </div>
        
        <!-- Main Container -->
        <div class="main-container">
            <!-- Mensajes -->
            <?php if (!empty($message['text'])): ?>
                <div class="alert alert-<?php echo $message['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?php echo $message['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message['text']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Toolbar -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por usuario, email o teléfono..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="bi bi-search"></i>Buscar
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus-circle"></i> Nuevo Usuario
                    </button>
                    <a href="pdf_generator.php?action=download" class="btn btn-warning" target="_blank">
                        <i class="bi bi-filetype-pdf"></i> Descargar PDF
                    </a>
                </div>
            </div>
            
            <!-- Tabla de Usuarios -->
            <div class="table-responsive">
                <?php if (!empty($users)): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 8%; text-align: center;">
                                    <i class="bi bi-hash"></i> ID
                                </th>
                                <th style="width: 20%;">
                                    <i class="bi bi-person"></i> Usuario
                                </th>
                                <th style="width: 28%;">
                                    <i class="bi bi-envelope"></i> Email
                                </th>
                                <th style="width: 16%;">
                                    <i class="bi bi-shield-lock"></i> Provider
                                </th>
                                <th style="width: 14%; text-align: center;">
                                    <i class="bi bi-gear"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <span class="badge bg-info">#<?php echo htmlspecialchars($user['id']); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </td>
                                    <td>
                                        <i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (!empty($user['oauth_provider'])): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-google me-1"></i><?php echo htmlspecialchars(ucfirst($user['oauth_provider'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-key me-1"></i>Local
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                onclick="setDeleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No hay usuarios</h3>
                        <p>No se encontraron usuarios. ¡Crea uno nuevo para comenzar!</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-plus-circle"></i> Crear Primer Usuario
                            </button>
                        <?php else: ?>
                            <p class="text-muted mt-3"><i class="bi bi-info-circle"></i> Inicia sesión para crear usuarios</p>
                            <a href="/phpweb/login.php" class="btn btn-primary mt-2">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal: Agregar Usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Nombre de Usuario *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope"></i> Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock"></i> Contraseña (opcional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Se generará automáticamente">
                            <small class="text-muted">Si dejas en blanco, se generará una contraseña aleatoria</small>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Crear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal: Editar Usuario -->
    <?php if ($editing_user): ?>
    <div class="modal fade show" id="editUserModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Usuario</h5>
                    <a href="menu.php" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $editing_user['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Nombre de Usuario *</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($editing_user['username']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope"></i> Email *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editing_user['email']); ?>" required>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="menu.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            });
        </script>
    </div>
    <?php endif; ?>
    
    <!-- Modal: Confirmar Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUsername"></strong>?</p>
                    <p class="text-danger">⚠️ Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar Definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sistema de Gestión de Usuarios | Desarrollado con PHP, MySQL y Bootstrap</p>
            <small>Todos los derechos reservados</small>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para establecer datos en el modal de eliminación
        function setDeleteUser(userId, username) {
            document.getElementById('deleteId').value = userId;
            document.getElementById('deleteUsername').textContent = username;
        }
        
        // Auto-dismiss alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
