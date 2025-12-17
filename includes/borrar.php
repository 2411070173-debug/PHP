<?php

include 'conecion.php';

if (isset($_GET['eli_id'])) {

 $eli_sql="delete from vendedor where id='$_GET[eli_id]'";

 $con_sql=mysqli_query($conexion,$eli_sql);

}

?>