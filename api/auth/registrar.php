<?php
session_start();

require '../includes/conexionpdo.php';

// Mostrar errores o éxitos de sesiones previas
$error_message = $_SESSION['error'] ?? ($_POST['error_message'] ?? null);
$success_message = $_SESSION['success'] ?? ($_POST['success_message'] ?? null);

// Limpiar mensajes de sesión después de mostrarlos
if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) unset($_SESSION['success']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar si el usuario ya existe
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $check_stmt->execute([$_POST["username"], $_POST["email"]]);
        
        if ($check_stmt->fetchColumn() > 0) {
            $error_message = "El usuario o email ya está registrado.";
        } else {
            $username = trim($_POST["username"]);
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $email = trim($_POST["email"]);
            $role = $_POST["role"] ?? "student"; // Obtener rol seleccionado

            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $password, $email, $role])) {
                // Obtener el ID del nuevo usuario
                $user_id = $pdo->lastInsertId();
                
                // Guardar en sesión
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;
                $_SESSION["role"] = $role;
                $_SESSION["oauth_provider"] = null;
                $_SESSION["oauth_google_id"] = null;
                
                // Redirigir al dashboard según el rol
                if ($role === "teacher") {
                    header("Location: /dist/dashboard-profesor.php");
                } else {
                    header("Location: /dist/dashboard-alumno.php");
                }
                exit;
            } else {
                $error_message = "Error al registrar usuario. Por favor, intenta nuevamente.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error en el servidor. Por favor, intenta más tarde.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(-45deg, #0093E9, #80D0C7);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .btn-register {
            background: #0093E9;
            border: none;
            padding: 12px 0;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            background: #0077be;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert {
            border-radius: 15px;
        }
        .login-link {
            color: #0093E9;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .login-link:hover {
            color: #0077be;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Sistema de Escuela</h2>
        <p class="text-center text-muted mb-4">Crea tu cuenta</p>
        
        <form method="POST" class="needs-validation" novalidate>
            <!-- Selector de Rol -->
            <div class="mb-3">
                <label for="role" class="form-label"><i class="fas fa-graduation-cap me-2"></i>¿Eres alumno o profesor?</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">-- Selecciona tu rol --</option>
                    <option value="student">👨‍🎓 Alumno</option>
                    <option value="teacher">👨‍🏫 Profesor</option>
                </select>
                <div class="invalid-feedback">
                    Por favor selecciona un rol.
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username"><i class="fas fa-user me-2"></i>Usuario</label>
                <div class="invalid-feedback">
                    Por favor ingresa un nombre de usuario.
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                <div class="invalid-feedback">
                    Por favor ingresa un email válido.
                </div>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Contraseña</label>
                <div class="invalid-feedback">
                    Por favor ingresa una contraseña.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-register mb-3">
                <i class="fas fa-user-plus me-2"></i>Registrarse
            </button>

            <!-- Google OAuth Button -->
            <div class="text-center mb-3">
                <p class="text-muted mb-2" style="font-size: 14px;">O regístrate con:</p>
                <a href="/oauth/google-login.php" class="btn btn-danger w-100 mb-3">
                    <i class="fab fa-google me-2"></i>Regístrate con Google
                </a>
            </div>

            <div class="text-center">
                <p class="mb-0">¿Ya tienes una cuenta? <a href="login.php" class="login-link">Iniciar sesión</a></p>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de formulario Bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>

</body>
</html>


