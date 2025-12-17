<?php
/**
 * test_crud.php - Script de prueba CRUD
 * 
 * Este archivo prueba todas las funciones del sistema CRUD
 * Para usar: http://localhost/phpweb/test_crud.php
 */

session_start();
require_once __DIR__ . '/crud_handler.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas CRUD - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(-45deg, #0093E9, #80D0C7);
            min-height: 100vh;
            padding: 30px 0;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .test-item {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #0093E9;
            background: #f8f9ff;
            border-radius: 5px;
        }
        .test-item.success {
            border-left-color: #28A745;
            background: #d4edda;
        }
        .test-item.error {
            border-left-color: #DC3545;
            background: #f8d7da;
        }
        .test-item.warning {
            border-left-color: #FFC107;
            background: #fff3cd;
        }
        .btn-test {
            width: 100%;
            margin: 5px 0;
        }
        h1 {
            color: #0093E9;
            margin-bottom: 30px;
            border-bottom: 3px solid #0093E9;
            padding-bottom: 15px;
        }
        h3 {
            color: #0077be;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #0093E9, #80D0C7);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }
        code {
            background: #f4f4f4;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container-lg">
        <div class="container">
            <h1>
                <i class="bi bi-bug"></i> Pruebas del Sistema CRUD
            </h1>

            <?php
            // ============================================================================
            // PRUEBAS
            // ============================================================================
            
            $tests = [];
            $test_counter = 0;
            
            // Test 1: Conexión a BD
            try {
                global $pdo;
                if ($pdo) {
                    $tests[] = [
                        'name' => 'Conexión a Base de Datos',
                        'status' => 'success',
                        'message' => 'Conexión exitosa a MySQL (phpweb)',
                        'details' => 'PDO conectado correctamente'
                    ];
                }
            } catch (Exception $e) {
                $tests[] = [
                    'name' => 'Conexión a Base de Datos',
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage(),
                    'details' => 'Verifica includes/conexionpdo.php'
                ];
            }
            
            // Test 2: Tabla users existe
            try {
                global $pdo;
                $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $tests[] = [
                        'name' => 'Tabla "users" existe',
                        'status' => 'success',
                        'message' => 'Tabla users encontrada',
                        'details' => 'La estructura de BD es correcta'
                    ];
                } else {
                    $tests[] = [
                        'name' => 'Tabla "users" existe',
                        'status' => 'error',
                        'message' => 'Tabla users no existe',
                        'details' => 'Ejecuta el SQL de creación de tabla'
                    ];
                }
            } catch (Exception $e) {
                $tests[] = [
                    'name' => 'Tabla "users" existe',
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage(),
                    'details' => 'Verifica la estructura de BD'
                ];
            }
            
            // Test 3: Función getAllUsers()
            try {
                $users = getAllUsers();
                $total = count($users);
                $tests[] = [
                    'name' => 'Función getAllUsers()',
                    'status' => 'success',
                    'message' => "Se obtuvieron $total usuarios",
                    'details' => 'La función CRUD funciona correctamente'
                ];
            } catch (Exception $e) {
                $tests[] = [
                    'name' => 'Función getAllUsers()',
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage(),
                    'details' => 'Verifica crud_handler.php'
                ];
            }
            
            // Test 4: Estadísticas
            try {
                $stats = getUserStats();
                $tests[] = [
                    'name' => 'Estadísticas de usuarios',
                    'status' => 'success',
                    'message' => 'Total de usuarios: ' . $stats['total_users'],
                    'details' => 'Función getUserStats() funcionando'
                ];
            } catch (Exception $e) {
                $tests[] = [
                    'name' => 'Estadísticas de usuarios',
                    'status' => 'warning',
                    'message' => 'Advertencia: ' . $e->getMessage(),
                    'details' => 'Puedes continuar usando el sistema'
                ];
            }
            
            // Test 5: Archivos esenciales
            $essential_files = [
                'crud_handler.php' => 'Manejador CRUD',
                'pdf_generator.php' => 'Generador PDF',
                'includes/conexionpdo.php' => 'Conexión PDO',
                'auth/login.php' => 'Página Login',
                'auth/registrar.php' => 'Página Registro'
            ];
            
            $missing = [];
            foreach ($essential_files as $file => $desc) {
                if (!file_exists(__DIR__ . '/' . $file)) {
                    $missing[] = "$file ($desc)";
                }
            }
            
            if (empty($missing)) {
                $tests[] = [
                    'name' => 'Archivos esenciales',
                    'status' => 'success',
                    'message' => 'Todos los archivos están presentes',
                    'details' => count($essential_files) . ' archivos verificados'
                ];
            } else {
                $tests[] = [
                    'name' => 'Archivos esenciales',
                    'status' => 'error',
                    'message' => 'Faltan archivos: ' . implode(', ', $missing),
                    'details' => 'Verifica la estructura del proyecto'
                ];
            }
            
            ?>

            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-card">
                    <i class="bi bi-people" style="font-size: 24px;"></i>
                    <div class="stat-number">
                        <?php 
                        try {
                            $stats = getUserStats();
                            echo $stats['total_users'];
                        } catch (Exception $e) {
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-check2-circle" style="font-size: 24px;"></i>
                    <div class="stat-number"><?php echo array_sum(array_map(function($t) { return $t['status'] === 'success' ? 1 : 0; }, $tests)); ?></div>
                    <div class="stat-label">Tests Exitosos</div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-exclamation-circle" style="font-size: 24px;"></i>
                    <div class="stat-number"><?php echo array_sum(array_map(function($t) { return $t['status'] === 'error' ? 1 : 0; }, $tests)); ?></div>
                    <div class="stat-label">Tests Fallidos</div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-calendar" style="font-size: 24px;"></i>
                    <div class="stat-number"><?php echo date('d'); ?></div>
                    <div class="stat-label"><?php echo date('M Y'); ?></div>
                </div>
            </div>

            <!-- Resultados -->
            <h3><i class="bi bi-list-check"></i> Resultados de Pruebas</h3>
            
            <?php foreach ($tests as $test): ?>
                <div class="test-item <?php echo $test['status']; ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <strong><?php echo htmlspecialchars($test['name']); ?></strong>
                        <span class="badge bg-<?php echo $test['status'] === 'success' ? 'success' : ($test['status'] === 'error' ? 'danger' : 'warning'); ?>">
                            <i class="bi bi-<?php echo $test['status'] === 'success' ? 'check-circle' : ($test['status'] === 'error' ? 'x-circle' : 'exclamation-circle'); ?>"></i>
                            <?php echo strtoupper($test['status']); ?>
                        </span>
                    </div>
                    <p style="margin: 0; font-size: 14px;">
                        <?php echo htmlspecialchars($test['message']); ?>
                    </p>
                    <small style="color: #666;">
                        <i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($test['details']); ?>
                    </small>
                </div>
            <?php endforeach; ?>

            <!-- Pruebas Manuales -->
            <h3><i class="bi bi-play-fill"></i> Pruebas Manuales</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Pruebas Rápidas</h5>
                    <p>
                        <a href="menu.php" class="btn btn-primary btn-test">
                            <i class="bi bi-arrow-right"></i> Ir a la página principal
                        </a>
                    </p>
                    <p>
                        <a href="auth/login.php" class="btn btn-success btn-test">
                            <i class="bi bi-box-arrow-in-right"></i> Ir a Login
                        </a>
                    </p>
                    <p>
                        <a href="auth/registrar.php" class="btn btn-info btn-test text-white">
                            <i class="bi bi-person-plus"></i> Ir a Registro
                        </a>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Documentación</h5>
                    <p>
                        <a href="CRUD_DOCUMENTACION.md" class="btn btn-warning btn-test text-dark" target="_blank">
                            <i class="bi bi-file-text"></i> Leer Documentación Completa
                        </a>
                    </p>
                    <p>
                        <a href="INSTALACION_RAPIDA.txt" class="btn btn-dark btn-test" target="_blank">
                            <i class="bi bi-download"></i> Guía de Instalación
                        </a>
                    </p>
                </div>
            </div>

            <!-- Información del Sistema -->
            <h3><i class="bi bi-gear"></i> Información del Sistema</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Versión PHP:</strong> <?php echo phpversion(); ?> <br>
                    <strong>Sistema Operativo:</strong> <?php echo php_uname(); ?> <br>
                    <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?> <br>
                </div>
                <div class="col-md-6">
                    <strong>Extensión PDO:</strong> 
                    <?php echo extension_loaded('pdo') ? '✅ Disponible' : '❌ No disponible'; ?> <br>
                    <strong>Extensión MySQL PDO:</strong> 
                    <?php echo extension_loaded('pdo_mysql') ? '✅ Disponible' : '❌ No disponible'; ?> <br>
                    <strong>Sesiones:</strong> 
                    <?php echo isset($_SESSION) ? '✅ Disponible' : '❌ No disponible'; ?> <br>
                </div>
            </div>

            <!-- Footer -->
            <hr style="margin-top: 30px;">
            <p style="text-align: center; color: #666; font-size: 13px;">
                <i class="bi bi-check-all"></i> 
                Sistema CRUD - Gestión de Usuarios | 
                Versión 1.0 | 
                <?php echo date('Y'); ?> | 
                Todas las funciones disponibles
            </p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
