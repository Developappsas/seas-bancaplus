<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link,"insert into tasas2_unidades".$sufijo_sector." (id_tasa2, id_unidad_negocio, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_tasa2"]."', '".$_REQUEST["id_unidad_negocio"]."', '".$_SESSION["S_LOGIN"]."', getdate())");

$mensaje = "Unidad de negocio asociada exitosamente";

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'tasas2unidadesnegocio.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>&id_tasa2=<?php echo $_REQUEST["id_tasa2"] ?>';
</script>
