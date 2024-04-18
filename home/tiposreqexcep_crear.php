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

sqlsrv_query($link, "INSERT into tipos_reqexcep (reqexcep, nombre) values ('".$_REQUEST["reqexcep"]."', '".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Tipo creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'tiposreqexcep.php';
</script>
