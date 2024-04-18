<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "INSERT into tipos_gestioncobro (nombre) values ('".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Tipo gestion cobro creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'tiposgestioncobro.php';
</script>
