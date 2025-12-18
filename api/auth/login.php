<?php

require '../includes/conexionpdo.php';

session_start();

// Mostrar errores o éxitos de sesiones previas
$error_message = $_SESSION['error'] ?? null;
$success_message = $_SESSION['success'] ?? null;

// Limpiar mensajes de sesión después de mostrarlos
if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) unset($_SESSION['success']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];
    $selected_role = $_POST["role"] ?? "student"; // Rol seleccionado (student o teacher)

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validar usuario y contraseña
    if ($user && password_verify($password, $user["password"])) {
        // Validar que el rol coincida (o crear rol por defecto)
        $user_role = $user["role"] ?? "student";
        
        // Si la columna role no existe aún, usamos el role seleccionado
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $selected_role; // Guardar el rol seleccionado
        $_SESSION["oauth_provider"] = $user["oauth_provider"] ?? null;
        $_SESSION["oauth_google_id"] = $user["oauth_google_id"] ?? null;

        // Redirigir según el rol
        if ($selected_role === "teacher") {
            header("Location: /dist/dashboard-profesor.php");
        } else {
            header("Location: /dist/dashboard-alumno.php");
        }

        exit;

    } else {
        $error_message = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>loguearse</title>
    <link rel='stylesheet' href="/css/login2.css">
</head>
<body>
    <div class="login-container">
        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color: #ff6b6b; background: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                ❌ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message" style="color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                ✅ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form autocomplete='off' class='form' method='POST'>
            <div class='control'>
                <h1>
                    Sistema de Escuela
                </h1>
                <p style="color: #999; font-size: 14px; margin-top: 10px;">Inicia sesión en tu cuenta</p>
            </div>

            <!-- Selector de Rol -->
            <div class='control block-cube block-input' style="margin-bottom: 15px;">
                <select name='role' required style="width: 100%; padding: 12px; border: none; border-radius: 5px; background: white; font-size: 14px;">
                    <option value="student">👨‍🎓 Ingresar como Alumno</option>
                    <option value="teacher">👨‍🏫 Ingresar como Profesor</option>
                </select>
                <div class='bg-top'><div class='bg-inner'></div></div>
                <div class='bg-right'><div class='bg-inner'></div></div>
                <div class='bg'><div class='bg-inner'></div></div>
            </div>

            <div class='control block-cube block-input'>
                <input name='username' placeholder='Username' type='text' required>
                <div class='bg-top'><div class='bg-inner'></div></div>
                <div class='bg-right'><div class='bg-inner'></div></div>
                <div class='bg'><div class='bg-inner'></div></div>
            </div>

            <div class='control block-cube block-input'>
                <input name='password' placeholder='Password' type='password' required>
                <div class='bg-top'><div class='bg-inner'></div></div>
                <div class='bg-right'><div class='bg-inner'></div></div>
                <div class='bg'><div class='bg-inner'></div></div>
            </div>

            <button class='btn block-cube block-cube-hover' type='submit'>
                <div class='bg-top'><div class='bg-inner'></div></div>
                <div class='bg-right'><div class='bg-inner'></div></div>
                <div class='bg'><div class='bg-inner'></div></div>
                <div class='text'>Log In</div>
            </button>
  
            <!-- Google OAuth Button -->
            <div style="margin-top: 20px; text-align: center;">
                <p style="color: #999; margin: 15px 0; font-size: 14px;">O inicia sesión con:</p>
                <button type="button" id="googleLoginBtn" onclick="redirectToGoogleLogin()" style="display: inline-block; padding: 10px 20px; background: #DC3545; color: white; text-decoration: none; border-radius: 5px; width: 200px; text-align: center; font-weight: bold; transition: background 0.3s; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-google" style="margin-right: 8px;">🔴</i>Google
                </button>
            </div>

            <script>
                function redirectToGoogleLogin() {
                    const roleSelect = document.querySelector('select[name="role"]');
                    const selectedRole = roleSelect.value;
                    // Pasar el rol como parámetro en la URL
                    window.location.href = '/oauth/google-login.php?role=' + encodeURIComponent(selectedRole);
                }
            </script>

            <!-- Link a Registrar -->
            <div style="margin-top: 20px; text-align: center;">
                <p style="color: #999; font-size: 14px;">¿No tienes cuenta? <a href="/auth/registrar.php" style="color: #007bff; text-decoration: none;">Regístrate aquí</a></p>
            </div>
  
        </form>



<!-- <form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Iniciar Sesión</button>

</form> -->


</body>
</html>
