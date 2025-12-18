<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba del Sistema - Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .test-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-bottom: 20px;
        }
        .test-title {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 32px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .test-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .test-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .test-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .test-icon {
            margin-right: 15px;
            font-size: 20px;
            min-width: 25px;
        }
        .test-item.success .test-icon {
            color: #28a745;
        }
        .test-item.error .test-icon {
            color: #dc3545;
        }
        .test-content {
            flex: 1;
        }
        .test-label {
            font-weight: 600;
            color: #333;
        }
        .test-desc {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .btn-action {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: inline-block;
            min-width: 150px;
        }
        .btn-action.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-action.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-action.secondary {
            background: #6c757d;
            color: white;
        }
        .btn-action.secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }
        .section {
            margin-top: 40px;
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0093E9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .info-box strong {
            color: #0093E9;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-card">
            <div class="test-title">
                <i class="fas fa-check-circle"></i>
                Centro de Pruebas - Sistema de Gestión de Usuarios
            </div>

            <!-- Estado del Sistema -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-heartbeat"></i> Estado del Sistema
                </div>

                <?php
                $tests = [
                    [
                        'label' => 'Sesiones PHP',
                        'desc' => 'Verificando soporte de sesiones',
                        'status' => function_exists('session_start') ? 'success' : 'error'
                    ],
                    [
                        'label' => 'Extensión PDO',
                        'desc' => 'Verificando disponibilidad de PDO',
                        'status' => extension_loaded('pdo') ? 'success' : 'error'
                    ],
                    [
                        'label' => 'Extensión GD (Imágenes)',
                        'desc' => 'Verificando soporte de procesamiento de imágenes',
                        'status' => extension_loaded('gd') ? 'success' : 'error'
                    ],
                    [
                        'label' => 'Carpeta de Carga',
                        'desc' => 'Verificando existencia de /uploads/profiles',
                        'status' => is_dir(__DIR__ . '/uploads/profiles') ? 'success' : 'error'
                    ],
                    [
                        'label' => 'Permisos de Escritura',
                        'desc' => 'Verificando permisos en /uploads/profiles',
                        'status' => is_writable(__DIR__ . '/uploads/profiles') ? 'success' : 'error'
                    ]
                ];

                foreach ($tests as $test) {
                    $icon = $test['status'] === 'success' ? 'fas fa-check' : 'fas fa-times';
                    $class = $test['status'];
                    echo "
                    <div class='test-item $class'>
                        <div class='test-icon'><i class='$icon'></i></div>
                        <div class='test-content'>
                            <div class='test-label'>{$test['label']}</div>
                            <div class='test-desc'>{$test['desc']}</div>
                        </div>
                    </div>";
                }
                ?>
            </div>

            <!-- Información Importante -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Información Importante
                </div>

                <div class="info-box">
                    <strong>✅ Sistema Listo</strong><br>
                    Tu sistema de gestión de usuarios con autenticación está completamente configurado y listo para usar.
                </div>

                <div class="info-box">
                    <strong>🔐 Acceso Público</strong><br>
                    La tabla de usuarios en <code>menu.php</code> es accesible SIN necesidad de login. Los botones de crear/editar/eliminar requieren autenticación.
                </div>

                <div class="info-box">
                    <strong>👤 Usuarios Existentes</strong><br>
                    Hay 5 usuarios en la base de datos. Puedes usarlos para probar el login, o crear uno nuevo.
                </div>

                <div class="info-box">
                    <strong>📸 Fotos de Perfil</strong><br>
                    Las fotos se guardan automáticamente en <code>/uploads/profiles/</code>. Formato: JPG, PNG, GIF (máx. 5MB).
                </div>
            </div>

            <!-- Flujo de Prueba -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-clipboard-list"></i> Flujo Recomendado de Prueba
                </div>

                <div style="counter-reset: step-counter">
                    <div class="test-item" style="margin-bottom: 15px;">
                        <div style="background: #667eea; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">1</div>
                        <div class="test-content">
                            <div class="test-label">Ver tabla pública de usuarios</div>
                            <div class="test-desc">Sin necesidad de autenticación</div>
                        </div>
                    </div>
                    <div class="test-item" style="margin-bottom: 15px;">
                        <div style="background: #667eea; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">2</div>
                        <div class="test-content">
                            <div class="test-label">Registrarse con una cuenta nueva</div>
                            <div class="test-desc">O iniciar sesión con un usuario existente</div>
                        </div>
                    </div>
                    <div class="test-item" style="margin-bottom: 15px;">
                        <div style="background: #667eea; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">3</div>
                        <div class="test-content">
                            <div class="test-label">Acceder al Dashboard personal</div>
                            <div class="test-desc">Ver tu información de perfil y cambiar foto</div>
                        </div>
                    </div>
                    <div class="test-item" style="margin-bottom: 15px;">
                        <div style="background: #667eea; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">4</div>
                        <div class="test-content">
                            <div class="test-label">Acceder al CRUD completo</div>
                            <div class="test-desc">Crear, editar, eliminar y buscar usuarios</div>
                        </div>
                    </div>
                    <div class="test-item">
                        <div style="background: #667eea; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">5</div>
                        <div class="test-content">
                            <div class="test-label">Descargar PDF de usuarios</div>
                            <div class="test-desc">Y cerrar sesión</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acceso Rápido -->
            <div class="action-buttons">
                <a href="menu.php" class="btn-action primary">
                    <i class="fas fa-home"></i> Ir a Inicio
                </a>
                <a href="login.php" class="btn-action primary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="registrar.php" class="btn-action primary">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
                <a href="dashboard.php" class="btn-action secondary">
                    <i class="fas fa-user-circle"></i> Mi Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
