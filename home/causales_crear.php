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

sqlsrv_query($link,"insert into causales (tipo_causal, nombre) values ('".$_REQUEST["tipo_causal"]."', '".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Causal creada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'causales.php';
</script>
