<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$json = file_get_contents('php://input');

$data = json_decode($json, true);

$link = conectar();

if ($data["ext"])
	$sufijo = "_ext";
	
if ($data["tipo"] != "VENTA" && $data["tipo"] != "TRASLADO")
{
	exit;
}

if ($_SESSION["S_TIPO"] == "CONTABILIDAD" && $data["tipo"] == "TRASLADO")
{
	exit;
}

if ($data["id_venta"])
{
	$queryDB = "select id_venta from ventas".$sufijo." where id_venta = '".$data["id_venta"]."' AND estado IN ('ALI')";
	
	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	if (!sqlsrv_num_rows($rs))
	{
		exit;
	}
}

if ($data["action"] == "crear")
{
	if ($data["fecha_corte"])
		$fecha_corte = "'".$data["fecha_corte"]."'";
	else
		$fecha_corte = "NULL";
	
	sqlsrv_query($link,"exec spVentas('INSERTAR_VENTA', '".$data["ext"]."', '".$data["fecha_anuncio"]."', '".$data["fecha"]."', ".$fecha_corte.", '".$data["id_comprador"]."', '".$data["tasa_venta"]."', '".$data["modalidad_prima"]."', 'ALI', '".$data["tipo"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$_SESSION["S_LOGIN"]."', @id_venta)");
	
	$rs = sqlsrv_query($link,"select @id_venta as m");
	
	$fila =sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$id_venta = $fila["m"];
}

if ($data["action"] == "adicionar")
{
	$id_venta = $data["id_venta"];
}

if (!$data["ext"])
{
	$queryDB = "select si.id_simulacion from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado = 'DES' OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestado_compras_desembolso.", '".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."',".$subestados_desembolso_nuevos_tesoreria.") AND si.estado_tesoreria = 'PAR'))";
	
	if ($data["tipo"] == "VENTA")
	{
		$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
	}
	else if ($data["tipo"] == "TRASLADO")
	{
		$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
		
		if (!$data["id_venta"] && !$data["descripcion_busqueda3"])
			$queryDB .= " AND 1 = 0";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}
else
{
	$queryDB = "select si.id_simulacion from simulaciones".$sufijo." si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.estado = 'DES'";
	
	if ($data["tipo"] == "VENTA")
	{
		$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
	}
	else if ($data["tipo"] == "TRASLADO")
	{
		$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
		
		if (!$data["id_venta"] && !$data["descripcion_busqueda3"])
			$queryDB .= " AND 1 = 0";
	}
}

if ($data["id_venta"])
{
	$queryDB .= " AND si.no_vender = '0'";
}

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($data["descripcion_busqueda3"])
{
	$queryDB .= " AND (1 = 0";
	
	$cedulas = explode(",", $data["descripcion_busqueda3"]);
	
	foreach ($cedulas as $ced)
	{
		$queryDB .= " OR si.cedula = '".trim($ced)."' OR si.nro_libranza = '".trim($ced)."'";
	}	
	
	$queryDB .= ")";
}

$queryDB .= " order by abs(si.cedula), si.id_simulacion DESC";

$rs = sqlsrv_query($link,$queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	if ($data["action"] == "crear" || $data["action"] == "adicionar")
	{
		if ($data["chk".$fila["id_simulacion"]] == "1")
		{
			sqlsrv_query($link,"exec spVentas('INSERTAR_VENTA_DETALLE', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$id_venta."', '".$fila["id_simulacion"]."', '".$data["fecha_primer_pago".$fila["id_simulacion"]]."', '".$data["cuota_desde".$fila["id_simulacion"]]."', '".$data["cuota_hasta".$fila["id_simulacion"]]."', NULL, NULL, NULL, NULL, NULL, @id_venta)");
		}
	}
	
	if ($data["action"] == "no_vender")
	{
		if ($data["chknv".$fila["id_simulacion"]] == "1")
		{
			sqlsrv_query($link,"exec spVentas('ACTUALIZAR_SIMULACION_NOVENDER', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$fila["id_simulacion"]."', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, @id_venta)");
		}
		else
		{
			sqlsrv_query($link,"exec spVentas('ACTUALIZAR_SIMULACION_NOVENDER', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$fila["id_simulacion"]."', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, @id_venta)");
		}
	}
}

if ($data["action"] == "crear")
{
	sqlsrv_query($link,"COMMIT");
	
	if ($data["tipo"] == "VENTA")
		$mensaje = "Venta creada exitosamente";
	else if ($data["tipo"] == "TRASLADO")
		$mensaje = "Traslado creado exitosamente";
	
	$url = "ventas.php?ext=".$data["ext"];
}

if ($data["action"] == "adicionar")
{
	$mensaje = "Credito(s) adicionado(s) exitosamente";
	
	$url = "ventas.php?ext=".$data["ext"]."&fecha_inicialbd=".$data["fecha_inicialbd"]."&fecha_inicialbm=".$data["fecha_inicialbm"]."&fecha_inicialba=".$data["fecha_inicialba"]."&fecha_finalbd=".$data["fecha_finalbd"]."&fecha_finalbm=".$data["fecha_finalbm"]."&fecha_finalba=".$data["fecha_finalba"]."&id_compradorb=".$data["id_compradorb"]."&modalidadb=".$data["modalidadb"]."&descripcion_busqueda=".$data["descripcion_busqueda"]."&descripcion_busqueda2=".$data["descripcion_busqueda2"]."&estadob=".$data["estadob"]."&page=".$data["page"];
}

if ($data["action"] == "no_vender")
{
	$mensaje = "Marcacion exitosa";
	
	$url = "ventas_crear.php?tipo=".$data["tipo"]."&ext=".$data["ext"];
}

echo json_encode("mensaje->".$mensaje."|url->".$url);
		
?>
