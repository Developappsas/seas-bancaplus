<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["fecha_compromiso"])
	$fecha_compromiso = "'".$_REQUEST["fecha_compromiso"]."'";
else
	$fecha_compromiso = "NULL";

sqlsrv_query($link, "START TRANSACTION");

sqlsrv_query($link, "INSERT into gestion_cobro".$sufijo." (id_simulacion, id_tipo, observacion, fecha_compromiso, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["id_tipo"]."', '".utf8_encode($_REQUEST["observacion"])."', ".$fecha_compromiso.", '".$_SESSION["S_LOGIN"]."', NOW())");

$rs = sqlsrv_query($link, "select MAX(id_gestion) as m from gestion_cobro".$sufijo);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_gestion = $fila["m"];

sqlsrv_query($link, "COMMIT");

if (strcmp($_FILES["archivo"]["name"], ""))
{
	$uniqueID = uniqid();
	
	sqlsrv_query($link, "update gestion_cobro".$sufijo." set nombre_original = '".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', nombre_grabado = '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."' where id_gestion = '".$id_gestion."'");
	
	$fechaa =new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
	
	$metadata1 = array(
		'id_simulacion' => $_REQUEST["id_simulacion"],
		'descripcion' => "Adjunto gestion de cobro",
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);
	
	upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"]."/varios/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
}

$mensaje = "Gestion ingresada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'gestioncobro.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
