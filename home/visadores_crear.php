<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link,"INSERT into visadores (nombre) values ('".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Visador creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'visadores.php';
</script>
