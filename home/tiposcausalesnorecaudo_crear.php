<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "INSERT into tipos_causalesnorecaudo (nombre) values ('".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Tipo causal creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'tiposcausalesnorecaudo.php';
</script>
