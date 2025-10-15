<?php

require 'conexionpdo.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];

    $password = $_POST["password"];


    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");

    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];

        $_SESSION["username"] = $user["username"];

        header("Location: dashboard.php");

        exit;

    } else {

        echo "Usuario o contraseña incorrectos.";

    }

}

?>


<form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Iniciar Sesión</button>

</form>