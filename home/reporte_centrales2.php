<?php 
// header('Content-type: application/vnd.ms-excel');
// header("Content-Disposition: attachment; filename=".$_REQUEST["central"].".xls");
// header("Pragma: no-cache");
// header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
<?php

if ($_REQUEST["central"] == "DATACREDITO")
{

?>
	<th>TIPO DE IDENTIFICACION</th>
	<th>NUMERO DE IDENTIFICACION</th>
	<th>NUMERO DE LA CUENTA U OBLIGACION</th>
	<th>NOMBRE COMPLETO</th>
	<th>SITUACION DEL TITULAR</th>
	<th>FECHA APERTURA</th>
	<th>FECHA VENCIMIENTO</th>
	<th>RESPONSABLE</th>
	<th>FORMA DE PAGO</th>
	<th>NOVEDAD</th>
	<th>ESTADO ORIGEN DE LA CUENTA</th>
	<th>FECHA ESTADO ORIGEN</th>
	<th>ESTADO DE LA CUENTA</th>
	<th>FECHA ESTADO DE LA CUENTA</th>
	<th>ADJETIVO</th>
	<th>FECHA DE ADJETIVO</th>
	<th>CALIFICACION</th>
	<th>EDAD DE MORA</th>
	<th>VALOR INICIAL</th>
	<th>VALOR SALDO DEUDA</th>
	<th>VALOR DISPONIBLE</th>
	<th>V. CUOTA MENSUAL</th>
	<th>VALOR SALDO MORA</th>
	<th>TOTAL CUOTAS</th>
	<th>CUOTAS CANCELADAS</th>
	<th>CUOTAS EN MORA</th>
	<th>CLAUSULA</th>
	<th>FECHA CLAUSULA</th>
	<th>FECHA LIMITE DE PAGO</th>
	<th>FECHA DE PAGO</th>
	<!--<th>CIUDAD CORRESPONDENCIA</th>-->
	<th>CODIGO DANE CIUDAD CORRESPONDENCIA</th>
	<th>DEPARTAMENTO DE CORRESPONDENCIA</th>
	<th>DIRECCION DE CORRESPONDENCIA</th>
	<th>CORREO ELECTRONICO</th>
	<th>CELULAR</th>
<?php

}

if ($_REQUEST["central"] == "CIFIN")
{

?>
	<th>TIPO IDENT</th>
	<th>No IDENTIFICACION</th>
	<th>NOMBRE TERCERO</th>
	
	<th>FECHA LIMITE DE PAGO</th>
	<th>NUMERO OBLIGACION</th>
	<th>CODIGO SUCURSAL</th>
	<th>CALIDAD</th>
	<!--<th>CALIFICACI�N</th>-->
	<th>ESTADO DE OBLIGACION</th>
	<th>EDAD DE MORA</th>
	<th>ANIOS EN MORA</th>
	<th>FECHA DE CORTE</th>
	<th>FECHA INICIO</th>
	<th>FECHA TERMINACION</th>
	<th>FECHA DE EXIGIBILIDAD</th>
	<th>FECHA DE PRESCRIPCION</th>
	<th>FECHA DE PAGO</th>
	<th>MODO EXTINCION</th>
	<th>TIPO DE PAGO</th>
	<th>PERIODICIDAD</th>
	<th>PROBABILIDAD DE NO PAGO</th>
	<th>NUMERO DE CUOTAS PAGADAS</th>
	<th>NUMERO DE CUOTAS PACTADAS</th>
	<th>CUOTAS EN MORA</th>
	<th>CLAUSULA</th>
	<th>FECHA CLAUSULA</th>
	<th>VALOR INICIAL</th>
	<th>VALOR DE MORA</th>
	<th>VALOR DEL SALDO</th>
	<th>VALOR DE LA CUOTA</th>
	<th>VALOR DE CARGO FIJO</th>
	<th>LINEA DE CREDITO</th>
	<th>CLAUSULA DE PERMANENCIA</th>
	<th>TIPO DE CONTRATO</th>
	<th>ESTADO DE CONTRATO</th>
	<th>TERMINO O VIGENCIA DEL CONTRATO</th>
	<th>NUMERO DE MESES DEL CONTRATO</th>
	<th>NATURALEZA JURUDICA</th>
	<th>MODALIDAD DE CREDITO</th>
	<th>TIPO DE MONEDA</th>
	<th>TIPO DE GARANTIA</th>
	<th>VALOR DE LA GARANTIA</th>
	<th>OBLIGACION REESTRUCTURADA</th>
	<th>NATURALEZA DE LA REESTRUCTURACION</th>
	<th>NUMERO DE REESTRUCTURACIONES</th>
	<th>CLASE  TARJETA</th>
	<th>NO DE CHEQUES DEVUELTOS</th>
	<th>CATEGORIA SERVICIOS</th>
	<th>PLAZO</th>
	<th>DIAS DE CARTERA</th>
	<th>TIPO DE CUENTA</th>
	<th>DIRECCION CASA DEL TERCERO</th>
	<th>TELEFONO CASA DEL TERCERO</th>
	<th>CODIGO CIUDAD CASA DEL TERCERO</th>
	<th>CIUDAD CASA DEL TERCERO</th>
	<th>CODIGO DEPARTAMENTO DEL TERCERO</th>
	<th>DEPARTAMENTO CASA DEL TERCERO</th>
	<th>NOMBRE EMPRESA</th>
	<th>DIRECCION DE LA EMPRESA</th>
	<th>TELEFONO DE LA EMPRESA</th>
	<th>CODIGO CIUDAD EMPRESA DEL TERCERO</th>
	<th>CIUDAD EMPRESA DEL TERCERO</th>
	<th>CODIGO DEPARTAMENTO EMPRESA DEL TERCERO</th>
	<th>DEPARTAMENTO EMPRESA DEL TERCERO</th>
<?php

}

?>
</tr>
<?php

if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB = "SELECT '1' as tipo_identificacion, si.cedula as nro_identificacion, si.nro_libranza as nro_obligacion, CASE WHEN so.apellido1 IS NULL THEN si.nombre ELSE CONCAT(so.apellido1, ' ', so.apellido2, ' ', so.nombre1, ' ', so.nombre2) END as nombre_completo, '0' as situacion_titular, si.fecha_desembolso, DATEADD(MONTH,  (si.plazo - 1), si.fecha_primera_cuota) as fecha_vencimiento, '0' as responsable, '00' as forma_pago, '0' as estado_origen, si.fecha_desembolso as fecha_estado_origen, si.estado, '0' as adjetivo, '0' as fecha_adjetivo, cm.fecha_cuota_mora, si.valor_credito, '0' as valor_disponible, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.plazo as total_cuotas, dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 0) as fecha_pago, ci.municipio as ciudad, so.ciudad as codigo_ciudad, ci.departamento, so.direccion, so.email, so.celular, '1' as codigo_sucursal, 'P' as calidad, '1' as estado_obligacion, '5' as periodicidad, '20' as linea_credito, '1' as tipo_contrato, '1' as estado_contrato, '1' as vigencia_contrato, '2' as obligacion_reestructurada, so.tel_residencia as telefono, SUBSTRING(so.ciudad, 1, 2) as codigo_departamento, so.nombre_empresa, so.direccion_trabajo, so.telefono_trabajo, '' as codigo_ciudad_trabajo, so.ciudad_trabajo, '' as codigo_departamento_trabajo, '' as departamento_trabajo, si.fecha_creacion, si.sin_seguro, si.valor_por_millon_seguro, si.porcentaje_extraprima, SUM(CASE WHEN cu.fecha <= getdate() AND cu.pagada = '0' THEN 1 ELSE 0 END) as cuotas_mora, SUM(CASE WHEN cu.pagada = '1' THEN cu.capital + cu.abono_capital ELSE CASE WHEN cu.valor_cuota <> cu.saldo_cuota THEN iIF (cu.valor_cuota - cu.saldo_cuota - cu.interes - cu.seguro > 0, cu.valor_cuota - cu.saldo_cuota - cu.interes - cu.seguro + cu.abono_capital, cu.abono_capital) ELSE 0 END END) as capital_recaudado, SUM(CASE WHEN cu.fecha <= getdate() AND cu.pagada = '0' THEN cu.saldo_cuota ELSE 0 END) as saldo_mora, SUM(CASE WHEN cu.fecha <= getdate() AND cu.pagada = '1' THEN 1 ELSE 0 END) as cuotas_pagadas, SUM(CASE WHEN cu.fecha <= getdate() THEN 1 ELSE 0 END) as cuotas_causadas from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion LEFT JOIN (select id_simulacion, MIN(fecha) as fecha_cuota_mora from cuotas where fecha <= getdate() AND pagada = '0' group by id_simulacion) cm ON cm.id_simulacion = si.id_simulacion where (si.estado IN ('DES') OR (si.estado IN ('CAN') AND ((si.fecha_prepago >= DATEADD( DAY, -EXTRACT(DAY,EOMONTH(DATEADD(MONTH, -1, GETDATE()))) - 1, EOMONTH(DATEADD(MONTH, -1, GETDATE()))) AND si.fecha_prepago <= EOMONTH(DATEADD(MONTH, -1, GETDATE())) OR (dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 0) >= DATEADD(DAY, - EXTRACT(DAY, EOMONTH(DATEADD(EOMONTH, -1, getdate())) -1),EOMONTH(DATEADD(EOMONTH, -1, GETDATE())))AND dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 0) <= EOMONTH(DATEADD(MONTH, -1, GETDATE())))))))"; //Simulaciones desembolsadas o canceladas cuya fecha de prepago o el �ltimo recaudo est� en el mes anterior
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
	{
		$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."'";
	}

	if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
	{
		$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."'";
	}


	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

	$queryDB .= " group by si.id_simulacion";
}

if ($_REQUEST["tipo"] == "ALL")
{
	$queryDB .= " UNION ";
}

if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB .= "SELECT '1' as tipo_identificacion, si.cedula as nro_identificacion, si.nro_libranza as nro_obligacion, si.nombre as nombre_completo, '0' as situacion_titular, si.fecha_desembolso, DATEADD(MONTH, (si.plazo - 1), si.fecha_primera_cuota) as fecha_vencimiento, '00' as responsable, '00' as forma_pago, '4' as estado_origen, si.fecha_desembolso as fecha_estado_origen, si.estado, '0' as adjetivo, '0' as fecha_adjetivo, cm.fecha_cuota_mora, si.valor_credito, '0' as valor_disponible, si.opcion_credito, 0 as opcion_cuota_cli, 0 as opcion_cuota_ccc, 0 as opcion_cuota_cmp, 	si.opcion_cuota_cso, si.plazo as total_cuotas, dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 1) as fecha_pago, si.ciudad, '' as codigo_ciudad, si.departamento, si.direccion, si.email, si.celular, '1' as codigo_sucursal, 'P' as calidad, '1' as estado_obligacion, '5' as periodicidad, '20' as linea_credito, '1' as tipo_contrato, '1' as estado_contrato, '1' as vigencia_contrato, '2' as obligacion_reestructurada,si.telefono, '' as codigo_departamento, '' as nombre_empresa, '' as direccion_trabajo, '' as telefono_trabajo, '' as codigo_ciudad_trabajo, '' as ciudad_trabajo, '' as codigo_departamento_trabajo, '' as departamento_trabajo, si.fecha_creacion, '0' as sin_seguro, si.valor_por_millon_seguro, si.porcentaje_extraprima, SUM(CASE WHEN cu.fecha <= GETDATE() AND cu.pagada = '0' THEN 1 ELSE 0 END) as cuotas_mora, SUM(CASE WHEN cu.pagada = '1' THEN cu.capital + cu.abono_capital ELSE CASE WHEN cu.valor_cuota <> cu.saldo_cuota THEN iIF (cu.valor_cuota - cu.saldo_cuota - cu.interes - cu.seguro > 0, cu.valor_cuota - cu.saldo_cuota - cu.interes - cu.seguro + cu.abono_capital, cu.abono_capital) ELSE 0 END END) as capital_recaudado, SUM(CASE WHEN cu.fecha <= GETDATE() AND cu.pagada = '0' THEN cu.saldo_cuota ELSE 0 END) as saldo_mora, SUM(CASE WHEN cu.fecha <= GETDATE() AND cu.pagada = '1' THEN 1 ELSE 0 END) as cuotas_pagadas, SUM(CASE WHEN cu.fecha <= GETDATE() THEN 1 ELSE 0 END) as cuotas_causadas from simulaciones_ext si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas_ext cu ON si.id_simulacion = cu.id_simulacion LEFT JOIN (select id_simulacion, MIN(fecha) as fecha_cuota_mora from cuotas_ext where fecha <= getdate() AND pagada = '0' group by id_simulacion) cm ON cm.id_simulacion = si.id_simulacion where (si.estado IN ('DES') OR (si.estado IN ('CAN') AND ((si.fecha_prepago >= DATEADD( DAY, -EXTRACT(DAY,EOMONTH(DATEADD(MONTH, -1, GETDATE()))) - 1, EOMONTH(DATEADD(MONTH, -1, GETDATE()))) AND si.fecha_prepago <= EOMONTH(DATEADD(MONTH, -1, GETDATE())) OR (dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 1) >= DATEADD(DAY, - EXTRACT(DAY, EOMONTH(DATEADD(EOMONTH, -1, getdate())) -1),EOMONTH(DATEADD(EOMONTH, -1, GETDATE())))AND dbo.fn_fecha_ultimo_recaudo(si.id_simulacion, 1) <= EOMONTH(DATEADD(MONTH, -1, GETDATE())))))))";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
	{
		$queryDB .= " AND FORMAT(si.fecha_cartera,'yyyy-MM') >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."'";
	}

	if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
	{
		$queryDB .= " AND FORMAT(si.fecha_cartera,'yyyy-MM') <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."'";
	}


	$queryDB .= " group by si.id_simulacion";
}
$queryDB .= " order by fecha_desembolso, fecha_creacion";
exit($queryDB);

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$fecha_vcto = new DateTime($fila["fecha_vencimiento"]);
	
	if ($fila["estado"] == "CAN")
	{
		$novedad = "05"; //Pago Voluntario o Pago Total
		$edad_mora_cifin = 0;
	}
	else
	{
		switch ($fila["cuotas_mora"])
		{
			case "0":	$novedad = "01"; //Al d�a
						$edad_mora_cifin = 0;
						break;
			case "1":	$novedad = "01"; //1 a 30 d�as
						$edad_mora_cifin = 0;
						break;
			case "2":	$novedad = "06"; //31 a 60 d�as
						$edad_mora_cifin = 1;
						break;
			case "3":	$novedad = "07"; //61 a 90 d�as
						$edad_mora_cifin = 2;
						break;
			case "4":	$novedad = "08"; //91 a 120 d�as
						$edad_mora_cifin = 3;
						break;
			case "5":	$novedad = "09"; //121 a 150 d�as
						$edad_mora_cifin = 4;
						break;
			case "6":	$novedad = "09"; //151 a 180 d�as
						$edad_mora_cifin = 5;
						break;
			case "7":	$novedad = "09"; //181 a 210 d�as
						$edad_mora_cifin = 6;
						break;
			default:	$novedad = "09"; //121 o m�s d�as
						$edad_mora_cifin = 6;
		}
	}
	
	if ($fila["estado"] == "CAN")
		$estado_cuenta = "3";
	else if ($fila["cuotas_mora"] == "0" || $fila["cuotas_mora"] == "1")
		$estado_cuenta = "1";
	else if ($fila["cuotas_mora"] == "2" || $fila["cuotas_mora"] == "3")
		$estado_cuenta = "2";
	else if ($fila["cuotas_mora"] == "4" || $fila["cuotas_mora"] == "5" || $fila["cuotas_mora"] == "6")
		$estado_cuenta = "5";
	else
		$estado_cuenta = "6";
	
	$fecha_estado_cuenta = new DateTime(date("Y-m-d"));
	
	$fecha_estado_cuenta = new DateTime($fecha_estado_cuenta->format('Y-m-01'));
	
	$fecha_estado_cuenta->sub(new DateInterval("P1M"));
	
	if ($fila["estado"] == "CAN")
		$calificacion = "";
	else if ($fila["cuotas_mora"] == "0" || $fila["cuotas_mora"] == "1") //Al d�a o de 1 a 30 d�as
		$calificacion = "A";
	else if ($fila["cuotas_mora"] == "2" || $fila["cuotas_mora"] == "3") //De 31 a 60 d�as o de 61 a 90 d�as
		$calificacion = "B";
	else if ($fila["cuotas_mora"] == "4") //De 91 a 120 d�as
		$calificacion = "C";
	else if ($fila["cuotas_mora"] == "5") //De 121 a 150 d�as
		$calificacion = "D";
	else //De 151 en adelante
		$calificacion = "E";
	
	//Dias exactos entre la fecha de la cuota y la fecha de corte
	/*$fecha_corte = new DateTime($fecha_estado_cuenta->format('Ymt'));
	$fecha_cuota_mora = new DateTime($fila["fecha_cuota_mora"]);
	$edad_mora_datacredito = $fecha_corte->diff($fecha_cuota_mora);*/
	
	if ($fila["cuotas_mora"])
		$edad_mora_datacredito = $fila["cuotas_mora"] * 30 - 30;
	else
		$edad_mora_datacredito = 0;
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}
	
	if ($fila["sin_seguro"])
		$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $fila["cuotas_causadas"];

	if ($fila["estado"] != "CAN")
		$saldo_capital = $fila["valor_credito"] - $fila["capital_recaudado"] + $seguro_causado;
	else
		$saldo_capital = 0;
	
	if ($fila["total_cuotas"] <= 12)
		$plazo_cifin = 7;
	else if ($fila["total_cuotas"] > 12 && $fila["total_cuotas"] <= 24)
		$plazo_cifin = 8;
	else if ($fila["total_cuotas"] > 24 && $fila["total_cuotas"] <= 36)
		$plazo_cifin = 9;
	else
		$plazo_cifin = 10;
	
	if ($_REQUEST["central"] == "DATACREDITO")
	{
	
?>
<tr>
	<td><?php echo $fila["tipo_identificacion"] ?></td>
	<td><?php echo $fila["nro_identificacion"] ?></td>
	<td><?php echo $fila["nro_obligacion"] ?></td>
	<td><?php echo utf8_decode(substr($fila["nombre_completo"], 0, 45)) ?></td>
	<td><?php echo $fila["situacion_titular"] ?></td>
	<td><?php if ($fila["fecha_desembolso"]) { echo date('Ymd',strtotime($fila["fecha_desembolso"])); } ?></td>
	<td><?php echo $fecha_vcto->format('Ymt') ?></td>
	<td style="mso-number-format:'@';"><?php echo $fila["responsable"] ?></td>
	<td><?php echo $fila["forma_pago"] ?></td>
	<td style="mso-number-format:'@';"><?php echo $novedad ?></td>
	<td><?php echo $fila["estado_origen"] ?></td>
	<td><?php if ($fila["fecha_estado_origen"]) { echo date('Ymd',strtotime($fila["fecha_estado_origen"])); } ?></td>
	<td><?php echo $estado_cuenta ?></td>
	<td><?php echo $fecha_estado_cuenta->format('Ymt') ?></td>
	<td style="mso-number-format:'@';"><?php echo $fila["adjetivo"] ?></td>
	<td><?php echo $fila["fecha_adjetivo"] ?></td>
	<td><?php echo $calificacion ?></td>
	<td><?php echo $edad_mora_datacredito; //$edad_mora_datacredito->days ?></td>
	<td><?php echo round($fila["valor_credito"] / 1000) ?></td>
	<td><?php echo round($saldo_capital / 1000) ?></td>
	<td><?php echo $fila["valor_disponible"] ?></td>
	<td><?php echo round($opcion_cuota / 1000) ?></td>
	<td><?php echo round($fila["saldo_mora"] / 1000) ?></td>
	<td><?php echo $fila["total_cuotas"] ?></td>
	<td><?php echo $fila["cuotas_pagadas"] ?></td>
	<td><?php echo $fila["cuotas_mora"] ?></td>
	<td>0</td>
	<td>000000</td>
	<td><?php echo $fecha_estado_cuenta->format('Ymt') //date("Ymt") ?></td>
	<td><?php if ($fila["fecha_pago"]) { echo date('Ymd',strtotime($fila["fecha_pago"])); } ?></td>
	<!--<td><?php echo utf8_decode($fila["ciudad"]) ?></td>-->
	<td><?php echo $fila["codigo_ciudad"] ?></td>
	<td><?php echo utf8_decode($fila["departamento"]) ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["email"]) ?></td>
	<td><?php echo utf8_decode($fila["celular"]) ?></td>
</tr>
<?php

	}
	
	if ($_REQUEST["central"] == "CIFIN")
	{
	
?>
<tr>
	<td><?php echo $fila["tipo_identificacion"] ?></td>
	<td><?php echo $fila["nro_identificacion"] ?></td>
	<td><?php echo utf8_decode(substr($fila["nombre_completo"], 0, 45)) ?></td>
	<td><?php echo $fecha_estado_cuenta->format('Ymt') //date("Ymt") ?></td>
	<td><?php echo $fila["nro_obligacion"] ?></td>
	<!--<td><?php echo $fila["codigo_sucursal"] ?></td>-->
	<td style="mso-number-format:'@';">066</td>
	<td><?php echo $fila["calidad"] ?></td>
	<!--<td><?php echo $calificacion ?></td>-->
	<td><?php echo $fila["estado_obligacion"] ?></td>
	<td><?php echo $edad_mora_cifin ?></td>
	<td></td>
	<td><?php echo $fecha_estado_cuenta->format('Ymt') ?></td>
	<td><?php if ($fila["fecha_desembolso"]) { echo date('Ymd',strtotime($fila["fecha_desembolso"])); } ?></td>
	<td><?php echo $fecha_vcto->format('Ymt') ?></td>
	<td></td>
	<td></td>
	<td><?php if ($fila["fecha_pago"]) { echo date('Ymd',strtotime($fila["fecha_pago"])); } ?></td>
	<td></td>
	<td></td>
	<td><?php echo $fila["periodicidad"] ?></td>



	<td></td>
	<td><?php echo $fila["cuotas_pagadas"] ?></td>
	<td><?php echo $fila["total_cuotas"] ?></td>
	<td><?php echo $fila["cuotas_mora"] ?></td>
	<td></td>
	<td></td>
	<td><?php echo round($fila["valor_credito"] / 1000) ?></td>
	<td><?php echo round($fila["saldo_mora"] / 1000) ?></td>
	<td><?php echo round($saldo_capital / 1000) ?></td>
	<td><?php echo round($opcion_cuota / 1000) ?></td>
	<td></td>
	<td>20</td>

	<td></td>
	<td><?php echo $fila["tipo_contrato"] ?></td>
	<td><?php echo $fila["estado_contrato"] ?></td>
	<td><?php echo $fila["vigencia_contrato"] ?></td>
	<td><?php echo $fila["total_cuotas"] ?></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><?php echo $fila["obligacion_reestructurada"] ?></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><?php echo $plazo_cifin ?></td>
	<td></td>
	<td></td>
	
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo $fila["codigo_ciudad"] ?></td>
	<td><?php echo utf8_decode($fila["ciudad"]) ?></td>
	<td><?php echo $fila["codigo_departamento"] ?></td>
	<td><?php echo utf8_decode($fila["departamento"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre_empresa"]) ?></td>
	<td><?php echo utf8_decode($fila["direccion_trabajo"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono_trabajo"]) ?></td>
	<td><?php echo $fila["codigo_ciudad_trabajo"] ?></td>
	<td><?php echo utf8_decode($fila["ciudad_trabajo"]) ?></td>
	<td><?php echo $fila["codigo_departamento_trabajo"] ?></td>
	<td><?php echo utf8_decode($fila["departamento_trabajo"]) ?></td>
</tr>
<?php

	}
}

?>
</table>
