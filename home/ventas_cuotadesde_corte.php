<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

if ($_REQUEST["action"] == "crear")
{
	if (!$_REQUEST["ext"])
	{
		$queryDB = "select si.id_simulacion as id, cu.cuota from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN cuotas cu ON cu.id_simulacion = si.id_simulacion AND cu.fecha = '".$_REQUEST["fecha_corte"]."' where (si.estado = 'DES' OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN ('".$subestado_compras_desembolso."', '".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."') AND si.estado_tesoreria = 'PAR'))";
		
		if ($_REQUEST["tipo"] == "VENTA")
		{
			$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
		}
		else if ($_REQUEST["tipo"] == "TRASLADO")
		{
			$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
			
			if (!$_REQUEST["id_venta"] && !$_REQUEST["descripcion_busqueda3"])
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
		$queryDB = "select si.id_simulacion as id, cu.cuota from simulaciones".$sufijo." si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN cuotas".$sufijo." cu ON cu.id_simulacion = si.id_simulacion AND cu.fecha = '".$_REQUEST["fecha_corte"]."' where si.estado = 'DES'";
		
		if ($_REQUEST["tipo"] == "VENTA")
		{
			$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
		}
		else if ($_REQUEST["tipo"] == "TRASLADO")
		{
			$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
			
			if (!$_REQUEST["id_venta"] && !$_REQUEST["descripcion_busqueda3"])
				$queryDB .= " AND 1 = 0";
		}
	}

	if ($_REQUEST["id_venta"])
	{
		$queryDB .= " AND si.no_vender = '0'";
	}

	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_REQUEST["descripcion_busqueda3"])
	{
		$queryDB .= " AND (1 = 0";
		
		$cedulas = explode(",", $_REQUEST["descripcion_busqueda3"]);
		
		foreach ($cedulas as $ced)
		{
			$queryDB .= " OR si.cedula = '".trim($ced)."' OR si.nro_libranza = '".trim($ced)."'";
		}
		
		$queryDB .= ")";
	}

	$queryDB .= " order by abs(si.cedula), si.id_simulacion DESC";

}
else
{
	if (!$_REQUEST["ext"])
	{
		$queryDB = "select ved.id_ventadetalle as id, cu.cuota from ventas_detalle ved INNER JOIN simulaciones si ON ved.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN cuotas cu ON cu.id_simulacion = si.id_simulacion AND cu.fecha = '".$_REQUEST["fecha_corte"]."' WHERE ved.id_venta = '".$_REQUEST["id_venta"]."'";

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
		$queryDB = "select ved.id_ventadetalle as id, cu.cuota from ventas_detalle".$sufijo." ved INNER JOIN simulaciones".$sufijo." si ON ved.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN cuotas".$sufijo." cu ON cu.id_simulacion = si.id_simulacion AND cu.fecha = '".$_REQUEST["fecha_corte"]."' WHERE ved.id_venta = '".$_REQUEST["id_venta"]."'";
	}

	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	$queryDB .= " order by abs(si.cedula), ved.id_ventadetalle";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		echo "#cuota_desde".$fila["id"]."=".$fila["cuota"]."|";
	}
}

exit;

?>
