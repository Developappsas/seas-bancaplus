<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$existe_descuento = sqlsrv_query($link, "select nombre from descuentos_adicionales where pagaduria = '".utf8_encode($_REQUEST["pagaduria"])."' AND nombre = '".$_REQUEST["nombre"]."'");

if (!(sqlsrv_num_rows($existe_descuento))) {
	sqlsrv_query($link, "insert into descuentos_adicionales (pagaduria, nombre, porcentaje) values ('".utf8_encode($_REQUEST["pagaduria"])."', '".$_REQUEST["nombre"]."', '".$_REQUEST["porcentaje"]."')");
	
	$mensaje = "Descuento creado exitosamente";
}
else
{
	$mensaje = "Ya existe un descuento nombrado de la misma forma asociado a la pagaduria. Descuento NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'descuentosadicionales.php';
</script>
