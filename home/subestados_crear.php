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

sqlsrv_query($link,"INSERT into subestados (decision, nombre, estado, cod_interno) values ('".$_REQUEST["decision"]."', '".utf8_encode($_REQUEST["nombre"])."')",1 );


$mensaje = "Subestado creado exitosamente";
exit;

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'subestados.php';
</script>
