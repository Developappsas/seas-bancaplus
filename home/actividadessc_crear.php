<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "insert into actividadessc (nombre) values ('" . utf8_encode($_REQUEST["nombre"]) . "')");

$mensaje = "Actividad/Solicitud creada exitosamente";

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'actividadessc.php';
</script>