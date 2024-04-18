<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link,"START TRANSACTION");

sqlsrv_query($link,"INSERT into servicio_cliente (id_simulacion, id_actividad, observacion, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["id_actividad"]."', '".utf8_encode($_REQUEST["observacion"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$rs = sqlsrv_query($link,"select MAX(id_gestion) as m from servicio_cliente");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_gestion = $fila["m"];

sqlsrv_query($link,"COMMIT");


if (strcmp($_FILES["archivo"]["name"], ""))
{  
	
	$uniqueID = uniqid();
	


	sqlsrv_query($link,"UPDATE servicio_cliente set nombre_original = '".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', nombre_grabado = '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."' where id_gestion = '".$id_gestion."'");

	$fechaa =new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");


	
	$metadata1 = array(
		'id_simulacion' => $_REQUEST["id_simulacion"],
		'descripcion' => "Adjunto solicitud servicio al cliente",
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);
	
	upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"]."/varios/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
	
	echo "prueb";
}

$mensaje = "Registro ingresado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'serviciocliente_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
