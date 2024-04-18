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

$existe = sqlsrv_query($link, "select plazoi from tasas".$sufijo_sector." where (plazoi <= '".$_REQUEST["plazoi"]."' AND plazof >= '".$_REQUEST["plazoi"]."') OR (plazoi <= '".$_REQUEST["plazof"]."' AND plazof >= '".$_REQUEST["plazof"]."')");

if (!(sqlsrv_num_rows($existe)))
{
	sqlsrv_query($link, "insert into tasas".$sufijo_sector." (plazoi, plazof) values ('".$_REQUEST["plazoi"]."', '".$_REQUEST["plazof"]."')");
	
	$mensaje = "Registro creado exitosamente";
}
else
{
	$mensaje = "El plazo se traslapa con otro ya existente. Registro NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'tasas.php?sector=<?php echo $_REQUEST["sector"] ?>';
</script>
