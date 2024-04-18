<?php
include ('../functions.php');

$link = conectar();
$successful = FALSE;    // determina si se ha hecho la inserción del registro

// verificamos que el registro no exite en la tabla EMPLEADOS
$query = "SELECT nombre FROM empleados WHERE cedula = '"
        . $_REQUEST['cedula'] . "' AND pagaduria = '"
        . $_REQUEST['pagaduria'] . "'";

$resultado = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!sqlsrv_num_rows($resultado)) {
    // el registro no existe. Procedemos a ingresarlo
    $query = "INSERT INTO empleados VALUES ("
            . "'" . $_REQUEST['cedula'] . "', "
            . "'" . strtoupper(trim($_REQUEST['apellidos']) . ' ' . trim($_REQUEST['nombres'])) . "', "
            . "'" . $_REQUEST['pagaduria'] . "', "
            . "'" . strtoupper($_REQUEST['institucion']) . "', "
            . "'" . strtoupper($_REQUEST['cargo']) . "', "
            . "'" . strtoupper($_REQUEST['grado']) . "', "
            . "'" . $_REQUEST['basico'] . "', "
            . "'" . $_REQUEST['ingresos'] . "', "
            . "'" . $_REQUEST['egresos'] . "', "
            . "'" . $_REQUEST['neto'] . "', "
            . "'" . strtoupper($_REQUEST['nivel_educativo']) . "', "
            . "'" . strtoupper($_REQUEST['direccion']) . "', "
            . "'" . $_REQUEST['telefono'] . "', "
            . "'" . $_REQUEST['correo'] . "', "
            . "'" . $_REQUEST['fecha_nacimiento'] . "', "
            . "'" . strtoupper($_REQUEST['nivel_contratacion']) . "', "
            . "'" . strtoupper($_REQUEST['departamento']) . "', "
            . "'" . strtoupper($_REQUEST['ciudad']) . "', "
            . "'" . '1' . "', "
            . "'" . $_REQUEST['sexo'] . "', "
            . "'" . strtoupper($_REQUEST['ciudad']) . "', "
            . "'" . $_REQUEST['fecha_inicio'] . "', "
            . "'" . $_REQUEST['medio_contacto'] . "')";

    sqlsrv_query($link, $query);

    $query = "INSERT INTO empleados_creacion ("
            . "cedula, "
            . "pagaduria, "
            . "id_usuario, "
            . "fecha_creacion) VALUES ("
            . "'" . $_REQUEST['cedula'] . "', "
            . "'" . $_REQUEST['pagaduria'] . "', "
            . "'" . $_SESSION["S_IDUSUARIO"] . "', "
            . "GETDATE())";

    sqlsrv_query($link, $query);
    $successful = TRUE;
}
?>

<?php include("top.php"); ?>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="empleados.php"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>
        <?php
        if ($successful) {
            echo "Los datos fueron registrados con &eacute;xito";
        } else {
            echo "El registro para la c&eacute;dula "
            . $_REQUEST["cedula"] . " en la pagaduria "
            . $_REQUEST["pagaduria"] . " ya existe. No se realizaron cambios.";
        }
        ?>
        </b><br><br></center></td>
</tr>
</table>


