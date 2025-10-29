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

        header("Location: /phpweb/dist/dashboard.php");

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
    <link rel='stylesheet' href="login2.css">
</head>
<body>
    <div class="login-container">
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form autocomplete='off' class='form' method='POST'>
            <div class='control'>
                <h1>
                    Sign In
                </h1>
            </div>
  <div class='control block-cube block-input'>
    <input name='username' placeholder='Username' type='text' required>
    <div class='bg-top'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg-right'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg'>
      <div class='bg-inner'></div>
    </div>
  </div>
  <div class='control block-cube block-input'>
    <input name='password' placeholder='Password' type='password' required>
    <div class='bg-top'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg-right'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg'>
      <div class='bg-inner'></div>
    </div>
  </div>
  <button class='btn block-cube block-cube-hover' type='submit'>
    <div class='bg-top'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg-right'>
      <div class='bg-inner'></div>
    </div>
    <div class='bg'>
      <div class='bg-inner'></div>
    </div>
    <div class='text'>
      Log In
    </div>
  </button>
  
</form>



<!-- <form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Iniciar Sesión</button>

</form> -->


</body>
</html>
