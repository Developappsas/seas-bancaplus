<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$existe_codigo = sqlsrv_query($link, "select codigo from bancos where codigo = '" . $_REQUEST["codigo"] . "'");

if (!(sqlsrv_num_rows($existe_codigo))) {
	sqlsrv_query($link, "INSERT into bancos (codigo, nombre) values ('" . $_REQUEST["codigo"] . "', '" . utf8_encode($_REQUEST["nombre"]) . "')");

	$mensaje = "Banco creado exitosamente";
} else {
	$mensaje = "El codigo del banco ya se encuentra registrado. Banco NO creado";
}

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'bancos.php';
</script>