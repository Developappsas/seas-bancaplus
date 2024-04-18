<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "INSERT into caracteristicas (nombre) values ('" . utf8_encode($_REQUEST["nombre"]) . "')");

$mensaje = "Caracteristica creada exitosamente";

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'caracteristicas.php';
</script>