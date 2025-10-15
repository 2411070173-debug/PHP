<?php


$sql = "SELECT * FROM vendedor";

$con_sql = mysqli_query($conexion, $sql);


while ($rows = mysqli_fetch_array($con_sql)) {

    echo '

        <tr>

            <td>' . $rows['id'] . '</td>

            <td>' . $rows['vendedor'] . '</td>

            <td>' . $rows['direccion'] . '</td>

            <td>' . $rows['fechaventa'] . '</td>

            <td>

                <a class="btn btn-danger btn-xs" 

                   href="conexion_mostrar.php?eli_id=' . $rows['id'] . '">

                   Eliminar

                </a>

            </td>

            <td>

                <a class="btn btn-info btn-xs" 

                   href="conexion_detalle.php?det_id=' . $rows['id'] . '">

                   Detalle

                </a>

            </td>

        </tr>

    ';

}

?>