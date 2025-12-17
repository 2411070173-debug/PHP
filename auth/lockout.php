<?php

session_start();

session_destroy();

header("Location: /phpweb/auth/login.php");

exit;

?>