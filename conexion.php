<?php

$conexion = new mysqli("mysql", "uno", "unoc", "bd-uno");


if ($conexion->connect_error) {

    die("Conexión fallida: " . $conexion->connect_error);

}


echo "¡Conexión exitosa a MySQL desde PHP!";

?>