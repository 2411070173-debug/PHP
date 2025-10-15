<?php

require 'conexionpdo.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];

    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);


    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

    if ($stmt->execute([$username, $password])) {

        echo "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";

    } else {

        echo "Error al registrar usuario.";

    }

}

?>

<link rel="stylesheet" href="registro.css">
<form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Registrar</button>

</form>

<div class="login-container">
        <h1>LOGIN</h1>
        
        <div class="input-group">
            <label for="email">EMAIL</label>
            <input type="email" id="email" placeholder="your@email.com">
        </div>
        
        <div class="input-group">
            <label for="password">PASSWORD</label>
            <input type="password" id="password" placeholder="••••••••">
        </div>
        
        <button type="submit">SIGN IN</button>
        
        <div class="divider">OR</div>
        
        <div class="social-login">
            <div class="social-btn">G</div>
            <div class="social-btn">F</div>
            <div class="social-btn">X</div>
        </div>
        
        <div class="footer">
            Don't have an account? <a href="#">Sign up</a>
        </div>
    </div>