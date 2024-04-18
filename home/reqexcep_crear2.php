<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "START TRANSACTION");

sqlsrv_query($link, "INSERT into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '[".$_REQUEST["reqexcep"]."] ".utf8_encode($_REQUEST["observacion"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$rs = sqlsrv_query($link, "SELECT MAX(id_observacion) as m from simulaciones_observaciones");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_observacion_pregunta = $fila["m"];

if ($_REQUEST["fecha_vencimiento"])
	$fecha_vencimiento = "'".$_REQUEST["fecha_vencimiento"]."'";
else
	$fecha_vencimiento = "NULL";

sqlsrv_query($link, "INSERT into req_excep (id_simulacion, reqexcep, id_tipo, id_area, fecha_vencimiento, observacion, id_observacion_pregunta, estado, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["reqexcep"]."', '".$_REQUEST["id_tipo"]."', '".$_REQUEST["id_area"]."', ".$fecha_vencimiento.", '".utf8_encode($_REQUEST["observacion"])."', '".$id_observacion_pregunta."', 'PENDIENTE', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$rs = sqlsrv_query($link, "select MAX(id_reqexcep) as m from req_excep");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_reqexcep = $fila["m"];

sqlsrv_query($link, "COMMIT");

for ($i = 1; $i <= 5; $i++)
{
	if (strcmp($_FILES["archivo".$i]["name"], ""))
	{
		$uniqueID = uniqid();
		
		sqlsrv_query($link, "INSERT into req_excep_adjuntos (id_reqexcep, descripcion, nombre_original, nombre_grabado, usuario_creacion, fecha_creacion) VALUES ('".$id_reqexcep."', '".utf8_encode($_REQUEST["descripcion".$i])."', '".reemplazar_caracteres_no_utf($_FILES["archivo".$i]["name"])."', '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo".$i]["name"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
		
		$fechaa =new DateTime();
		$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
		
		$metadata1 = array(
			'id_simulacion' => $_REQUEST["id_simulacion"],
			'descripcion' => reemplazar_caracteres_no_utf($_REQUEST["descripcion".$i]),
			'usuario_creacion' => $_SESSION["S_LOGIN"],
			'fecha_creacion' => $fechaFormateada
		);
		
		upload_file($_FILES["archivo".$i], "simulaciones", $_REQUEST["id_simulacion"]."/varios/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo".$i]["name"]), $metadata1);
	}
}

$mensaje = "Registro ingresado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
