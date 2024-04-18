<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "INSERT into etapas (nombre) values ('".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Etapa creada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'etapas.php';
</script>
