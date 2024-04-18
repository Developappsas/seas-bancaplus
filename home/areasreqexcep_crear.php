<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

include("top.php");

sqlsrv_query($link, "insert into areas_reqexcep (nombre) values ('" . utf8_encode($_REQUEST["nombre"]) . "')");

$mensaje = "Area creada exitosamente";

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'areasreqexcep.php';
</script>