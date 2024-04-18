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

sqlsrv_query($link, "insert into documentos_cierre (nombre) values ('".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Documento creado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'documentoscierre.php';
</script>
