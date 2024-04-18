<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR"){
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["gmf"] != "1")
{
	$_REQUEST["gmf"] = "0";
}

sqlsrv_query($link,"INSERT into unidades_negocio (nombre, id_empresa, valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf, prefijo_libranza, usuario_creacion, fecha_creacion) values ('".$_REQUEST["nombre"]."', '".$_REQUEST["empresa"]."', '".$_REQUEST["valor_por_millon_seguro_activos"]."', '".$_REQUEST["valor_por_millon_seguro_pensionados"]."', '".$_REQUEST["valor_por_millon_seguro_colpensiones"]."', '".$_REQUEST["gmf"]."', '".$_REQUEST["prefijo_libranza"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$mensaje = "Unidad de negocio creada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'unidadesnegocio.php';
</script>
