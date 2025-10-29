<?php
require 'conexionpdo.php';

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

            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$username, $password, $email])) {
                $success_message = "¡Registro exitoso! Ya puedes iniciar sesión.";
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

        <h2 class="text-center mb-4">Crear cuenta</h2>
        
        <form method="POST" class="needs-validation" novalidate>
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












    <!-- <form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="email" name="email" placeholder="Email" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Registrar</button>

    </form> -->
    <!-- <div class="login-container">
        <h1>REGISTRARSE</h1>
        
        <div class="input-group">
            <label for="username">USERNAME</label>
            <input type="username" id="username" placeholder="username">
        </div>

        <div class="input-group">
            <label for="email">EMAIL</label>
            <input type="email" id="email" placeholder="your@email.com">
        </div>
        
        
        <div class="input-group">
            <label for="password">PASSWORD</label>
            <input type="password" id="password" placeholder="••••••••">
        </div>
        
        <button type="submit">Registrarse</button>
        
        <div class="divider">OR</div>
        
        <div class="social-login">
            <div class="social-btn">G</div>
            <div class="social-btn">F</div>
            <div class="social-btn">X</div>
        </div>
        
        <div class="footer">
            Ya tienes una cuenta? <a href="login.php">Login</a>
        </div>
    </div> -->
</body>
</html>






<!-- <form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Registrar</button>

</form> -->

