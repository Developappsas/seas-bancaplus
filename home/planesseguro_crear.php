<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link,"INSERT into planes_seguro (nombre, valor) values ('".$_REQUEST["nombre"]."', '".$_REQUEST["valor"]."')");

$mensaje = "Plan creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'planesseguro.php';
</script>
