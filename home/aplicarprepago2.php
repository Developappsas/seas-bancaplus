<?php
include('../functions.php');
include('../function_blob_storage.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

sqlsrv_query($link, "BEGIN");

sqlsrv_query($link, "update simulaciones" . $sufijo . " set id_compradorprep = '" . $_REQUEST["id_compradorprep"] . "', fecha_prepago = '" . $_REQUEST["fecha"] . "', valor_prepago = '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "', valor_liquidacion = '" . $_REQUEST["valor_liquidacion"] . "', prepago_intereses = '" . $_REQUEST["prepago_intereses"] . "', prepago_seguro = '" . $_REQUEST["prepago_seguro"] . "', prepago_cuotasmora = '" . $_REQUEST["prepago_cuotasmora"] . "', prepago_segurocausado = '" . $_REQUEST["prepago_segurocausado"] . "', prepago_gastoscobranza = '" . $_REQUEST["prepago_gastoscobranza"] . "', prepago_totalpagar = '" . $_REQUEST["prepago_totalpagar"] . "', usuario_creacionprep = '" . $_SESSION["S_LOGIN"] . "', fecha_creacionprep = GETDATE	() where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

//Ya no se hace lo siguiente aqui, ya que debe haber una aprobacion posterior
//mysql_query("update cuotas".$sufijo." set saldo_cuota = '0' , pagada = '1' where id_simulacion = '".$_REQUEST["id_simulacion"]."'", $link);	

//mysql_query("update simulaciones".$sufijo." set estado = 'CAN' where id_simulacion = '".$_REQUEST["id_simulacion"]."'", $link);

if (strcmp($_FILES["archivo"]["name"], "")) {
	$uniqueID = date("YmdHis");

	sqlsrv_query($link, "update simulaciones" . $sufijo . " set nombre_originalprep = '" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', nombre_grabadoprep = '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

	$fechaa = new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

	$metadata1 = array(
		'id_simulacion' => $_REQUEST["id_simulacion"],
		'descripcion' => "Soporte prepago",
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);

	upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
}

sqlsrv_query($link, "COMMIT");

$mensaje = "La informacion del prepago fue registrada. Informe a su superior para que el prepago sea aplicado en el sistema";

?>
<script>
	alert("<?php echo $mensaje ?>");

	opener.location.href = 'cartera_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

	window.close();
</script>