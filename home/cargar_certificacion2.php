<?php
include ('../functions.php'); 
include ('../function_blob_storage.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_ADJUNTOS"])
{
	exit;
}

if (!$_REQUEST["id_simulacion"] && ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.id_simulacion from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado IN ('ING', 'EST')";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1))
{
	exit;
}

$queryDB = "SELECT ent.nombre, scc.entidad from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND scc.consecutivo = '".$_REQUEST["consecutivo"]."'";

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1))
{
	exit;
}

sqlsrv_query($link, "BEGIN");

if (strcmp($_FILES["archivo"]["name"], ""))
{
	$uniqueID = uniqid();
	
	sqlsrv_query($link, "INSERT into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$tipoadjunto_cdd."', 'CERTIFICADO DE DEUDA ".$fila1["nombre"]." ".$fila1["entidad"]."', '".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', '".$uniqueID."_".$_REQUEST["id_simulacion"]."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', '0', '".$_SESSION["S_LOGIN"]."', GETDATE())");
	
	$rs2 = sqlsrv_query($link, "SELECT MAX(id_adjunto) as m from adjuntos");
	
	$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
	
	$id_adjun = $fila2["m"];
	
	$fechaa =new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
	
	$metadata1 = array(
		'id_simulacion' => $_REQUEST["id_simulacion"],
		'descripcion' => "CERTIFICADO DE DEUDA ".reemplazar_caracteres_no_utf(utf8_decode($fila1["nombre"]." ".$fila1["entidad"])),
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);
	
	upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"]."/adjuntos/".$uniqueID."_".$_REQUEST["id_simulacion"]."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
	
	sqlsrv_query($link, "UPDATE simulaciones_comprascartera set id_adjunto = '".$id_adjun."' where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$_REQUEST["consecutivo"]."'");
}

sqlsrv_query($link, "COMMIT");

$mensaje = "La certificacion fue cargada";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'simulador.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>

