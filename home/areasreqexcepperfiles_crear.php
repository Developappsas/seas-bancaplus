<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$queryDB = "SELECT * from areas_reqexcep_perfiles where id_perfil = '" . $_REQUEST["id_perfil"] . "'";

$existe = sqlsrv_query($link, $queryDB);

if (!(sqlsrv_num_rows($existe))) {
	sqlsrv_query($link, "INSERT into areas_reqexcep_perfiles (id_area, id_perfil) VALUES ('" . $_REQUEST["id_area"] . "', '" . $_REQUEST["id_perfil"] . "')");

	$mensaje = "Perfil asociado exitosamente";
} else {
	$mensaje = "El perfil ya ha sido asociado a otra area. Por favor seleccione otro perfil para ser asociado";
}

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'areasreqexcepperfiles.php?id_area=<?php echo $_REQUEST["id_area"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>