<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php include ('../controles/validaciones.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA")
{
	exit;
}

$queryDB = "SELECT CONCAT(us.nombre,' ',us.apellido) as nombre_comercial,si.*, FORMAT(fecha_comision_pagada, 'Y-m-d') as fecha_comision_pagada_texto, ba.nombre as nombre_banco, FORMAT(si.fecha_cartera, 'Y-m') as mes_cartera, so.direccion, so.email as mail, pa.sector from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN bancos ba ON si.id_banco = ba.id_banco LEFT JOIN usuarios us ON us.id_usuario=si.id_comercial where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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

$rs = sqlsrvr_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrvr_fetch_array($rs);

if (!sqlsrvr_num_rows($rs))
{
	exit;
}

switch ($fila["estado_tesoreria"])
{
	case "ABI":	$estado = "ABIERTO"; break;
	case "PAR":	$estado = "PARCIAL"; break;
	case "CER":	$estado = "CERRADO"; break;
}

switch($fila["opcion_credito"])
{
	case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
				$opcion_desembolso = $fila["opcion_desembolso_cli"];
				break;
	case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
				$opcion_desembolso = $fila["opcion_desembolso_ccc"];
				break;
	case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
				$opcion_desembolso = $fila["opcion_desembolso_cmp"];
				break;
	case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
				$opcion_desembolso = $fila["opcion_desembolso_cso"];
				break;
}

if (!$fila["sin_seguro"])
	$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
else
	$seguro_vida = 0;

$cuota_corriente = $opcion_cuota - round($seguro_vida);

$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL or (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";

$rs1 = sqlsrvr_query($link,$queryDB);

$fila1 = sqlsrvr_fetch_array($rs1);

if ($fila1["s"])
	$compras_cartera = $fila1["s"];

if ($fila["opcion_credito"] == "CLI")
	$fila["retanqueo_total"] = 0;

$intereses_anticipados = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00;

$asesoria_financiera = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00;

$comision_venta = 0;

if ($fila["tipo_producto"] == "1")
{
	if ($fila["fecha_estudio"] < "2018-01-01")
	{
		$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
	}
	else
	{
		if ($fila["fidelizacion"])
			$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
		else
			$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
	}
}

$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;

$comision_venta_iva = 0;

if ($fila["tipo_producto"] == "1")
{
	if ($fila["fecha_estudio"] < "2018-01-01")
	{
		$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
	}
	else
	{
		if ($fila["fidelizacion"])
			$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
		else
			$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
	}
}

$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;

$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];

if ($fila["tipo_producto"] == "1")
	if ($fila["fidelizacion"])
		$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
	else
		$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;

$descuentos_adicionales = sqlsrvr_query($link,"select * from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");

while ($fila1 = sqlsrvr_fetch_array($descuentos_adicionales))
{
	$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
}

if ($fila["bloqueo_cuota"])
{
	$retenciones_cuota = $fila["bloqueo_cuota_valor"];
}
else
{
	$rs1 = sqlsrvr_query($link,"select SUM(cuota_retenida) as s from tesoreria_cc where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	
	$fila1 = sqlsrvr_fetch_array($rs1);
	
	$retenciones_cuota = $fila1["s"];
}

$rs1 = sqlsrvr_query($link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' and clasificacion = 'DSC' and fecha_giro IS NOT NULL");

$fila1 = sqlsrvr_fetch_array($rs1);

$giros_realizados = $fila1["s"];

$saldo_girar = round($desembolso_cliente) - $retenciones_cuota - $giros_realizados;

$rs1 = sqlsrvr_query($link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

$fila1 = sqlsrvr_fetch_array($rs1);

$giros_programados = $fila1["s"];

$giro_pendiente = $opcion_desembolso - $giros_programados;

$inconsistencia = ValidaValorCredito($_REQUEST["id_simulacion"], $link);

if (!$inconsistencia)
	$inconsistencia = ValidaValorDesembolso($_REQUEST["id_simulacion"], $link);

if ($inconsistencia)
	echo "<script>alert('Este credito parece tener inconsistencia en sus valores, el sistema no permitira ninguna accion hasta no resolverla')</script>";

?>
<?php include("top.php"); ?>
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<script language="JavaScript">
function chequeo_forma() {
	var fecha = new Date();
	
	day = fecha.getDate();
	month = fecha.getMonth() + 1;
	year = fecha.getFullYear();
	
	if (String(day).length == 1) {
		day = "0"+String(day);
	}
	
	if (String(month).length == 1) {
		month = "0"+String(month);
	}
	
	with (document.formato) {
		
<?php

$queryDB = "select consecutivo from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."' AND se_compra = 'SI' AND (id_entidad IS NOT NULL or(entidad IS NOT NULL AND entidad <> '')) order by consecutivo";

$rs1 = sqlsrvr_query($link,$queryDB);

while ($fila1 = sqlsrvr_fetch_array($rs1))
{

?>
		if (document.getElementById("cuota_retenida<?php echo $fila1["consecutivo"] ?>")) {
			if (parseInt(document.getElementById("cuota_retenida<?php echo $fila1["consecutivo"] ?>").value.replace(/\,/g, '')) > parseInt(document.getElementById("valor_pagar<?php echo $fila1["consecutivo"] ?>").value.replace(/\,/g, ''))) {
				alert("La cuota retenida no puede ser mayor que el valor a pagar ("+document.getElementById("entidad<?php echo $fila1["consecutivo"] ?>").value+")");
				return false;
			}
		}
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA")
	{

?>
		if (document.getElementById("pagada<?php echo $fila1["consecutivo"] ?>").checked == true && document.getElementById("fecha_girocc<?php echo $fila1["consecutivo"] ?>").value == "") {
			alert("Debe establecer la fecha de giro de la compra de cartera ("+document.getElementById("entidad<?php echo $fila1["consecutivo"] ?>").value+")");
			return false;
		}
<?php

	}
}

$queryDB = "select * from giros gi where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_giro";

$rs1 = sqlsrvr_query($link,$queryDB);

while ($fila1 = sqlsrvr_fetch_array($rs1))
{

?>
		if (fecha_giro<?php echo $fila1["id_giro"] ?>.value != "") {
			if (diffDate(year+"-"+month+"-"+day, fecha_giro<?php echo $fila1["id_giro"] ?>.value) > 0) {
				alert("La fecha de giro no puede ser mayor a la fecha actual (<?php echo str_replace("\"", "", utf8_decode($fila1["beneficiario"])) ?>)");
				return false;
			}
			else if (diffDate(fecha_giro<?php echo $fila1["id_giro"] ?>.value, "<?php echo $fila["fecha_tesoreria"] ?>") > 0) {
				alert("La fecha de giro no puede ser menor que la fecha de tesorer�a (<?php echo str_replace("\"", "", utf8_decode($fila1["beneficiario"])) ?>)");
				return false;
			}
			if ((forma_pago<?php echo $fila1["id_giro"] ?>.value == "CHEQUE" || forma_pago<?php echo $fila1["id_giro"] ?>.value == "CHEQUE GERENCIA") && nro_cheque<?php echo $fila1["id_giro"] ?>.value == "") {
				alert("Debe digitar el Nro. de cheque (<?php echo str_replace("\"", "", utf8_decode($fila1["beneficiario"])) ?>)");
				return false;
			}
			if (id_cuentabancaria<?php echo $fila1["id_giro"] ?>.value == "") {
				alert("Debe establecer la cuenta desde la que se hace el giro (<?php echo str_replace("\"", "", utf8_decode($fila1["beneficiario"])) ?>)");
				return false;
			}
		}
<?php

}

?>
	}
	
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
<tr>
	<td valign="top" width="18"><a href="tesoreria.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Detalle Tesorer&iacute;a</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="tesoreria_actualizar2.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="id_subestado" value="<?php echo $fila["id_subestado"] ?>">
<input type="hidden" name="fecha_primera_cuotah" value="<?php echo $fila["fecha_primera_cuota"] ?>">
<input type="hidden" name="valor_por_millon_seguro" value="<?php echo $fila["valor_por_millon_seguro"] ?>">
<input type="hidden" name="porcentaje_extraprima" value="<?php echo $fila["porcentaje_extraprima"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 align="center">
<tr>
	<td valign="top">
		<h2>DESEMBOLSO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 align="right">
		<tr>
			<td>NO LIBRANZA</td>
			<td><input type="text" name="no_libranza" value="<?php echo $fila["nro_libranza"] ?>" size="45" style="background-color:#8DB4E3;" readonly></td>
		</tr>
		<tr>
			<td>NOMBRE</td>
			<td><input type="text" name="nombre" value="<?php echo utf8_decode($fila["nombre"]) ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>N&Uacute;MERO DE C&Eacute;DULA</td>
			<td><input type="text" name="cedula" value="<?php echo $fila["cedula"] ?>"size="45" readonly></td>
		</tr>
		<tr>
			<td>DIRECCI&Oacute;N</td>
			<td><input type="text" name="direccion" value="<?php echo utf8_decode($fila["direccion"]) ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>TEL&Eacute;FONO</td>
			<td><input type="text" name="telefono" value="<?php echo utf8_decode($fila["telefono"]) ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>CORREO ELECTR&Oacute;NICO</td>
			<td><input type="text" name="mail" value="<?php echo utf8_decode($fila["mail"]) ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>CUENTA BANCARIA</td>
			<td><input type="text" name="cuenta_bancaria" value="<?php echo $fila["nombre_banco"]."-".$fila["tipo_cuenta"]."-".$fila["nro_cuenta"] ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>PAGADUR&Iacute;A</td>
			<td><input type="text" name="pagaduria" value="<?php echo utf8_decode($fila["pagaduria"]) ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>ESTADO</td>
			<td><input type="text" name="estado" value="<?php echo $estado ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>FECHA</td>
			<td><input type="text" name="fecha_tesoreria" value="<?php echo $fila["fecha_tesoreria"] ?>" size="45" readonly></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td>SOLICITADO</td>
			<td><input type="text" name="opcion_desembolso" value="<?php echo number_format($opcion_desembolso, 0, ".", ",") ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>PLAZO</td>
			<td><input type="text" name="plazo" value="<?php echo $fila["plazo"] ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>TASA DE INTER&Eacute;S DEL CR&Eacute;DITO</td>
			<td><input type="text" name="tasa_interes" value="<?php echo $fila["tasa_interes"] ?>" size="45" style=" text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>KP PLUS</td>
			<td><input type="text" name="sin_seguro" value="<?php if ($fila["sin_seguro"]) { echo "SI"; } else { echo "NO"; } ?>" size="45" readonly></td>
		</tr>
		<tr>
			<td>CUOTA CORRIENTE</td>
			<td><input type="text" name="cuota_corriente" value="<?php echo number_format($cuota_corriente, 0, ".", ",") ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>SEGURO DE VIDA</td>
			<td><input type="text" name="seguro_vida" value="<?php echo number_format($seguro_vida, 0, ".", ",") ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>CUOTA TOTAL</td>
			<td><input type="text" name="opcion_cuota" value="<?php echo number_format($opcion_cuota, 0, ".", ",") ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>COMERCIAL</td>
			<td><input type="text" name="nombre_comercial" value="<?php echo $fila["nombre_comercial"] ?>" size="45" style="text-align:right;" readonly></td>
		</tr>
		</table>
		</div>
	</td>
	<td>&nbsp;</td>
	<td valign="top">
		<h2>LIQUIDACI&Oacute;N</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td>FECHA DESEMBOLSO</td>
			<td><input type="text" name="fecha_desembolso" value="<?php echo $fila["fecha_desembolso"] ?>" size="15" style="text-align:center;" readonly></td>
		</tr>
		<tr>
			<td>VALOR CR&Eacute;DITO</td>
			<td><input type="text" name="valor_credito" value="<?php echo number_format($fila["valor_credito"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- RETANQUEOS</td>
			<td><input type="text" name="retanqueo_total" value="<?php echo number_format($fila["retanqueo_total"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- COMPRAS DE CARTERA</td>
			<td><input type="text" name="compras_cartera" value="<?php echo number_format($compras_cartera, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- <?php if ($fila["sector"] == "PUBLICO") { echo "INTERESES ANTICIPADOS"; } else { echo "AVAL"; } ?></td>
			<td><input type="text" name="intereses_anticipados" value="<?php echo number_format($intereses_anticipados, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- ASESOR&Iacute;A FINANCIERA</td>
			<td><input type="text" name="asesoria_financiera" value="<?php echo number_format(0, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- IVA</td>
			<td><input type="text" name="iva" value="<?php echo number_format(0, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>

		<tr>
			<td>- SERVICIO EN LA NUBE</td>
			<td><input type="text" name="servicio_nube" value="<?php echo number_format($iva + $asesoria_financiera, 0, ".", ",")?>" size="15" style="text-align:right;" readonly></td>
		</tr>

		<tr>
			<td>- GMF</td>
			<td><input type="text" name="gmf" value="<?php echo number_format($gmf, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
<?php

$descuentos_adicionales = sqlsrvr_query($link,"select da.nombre, sd.id_descuento, sd.porcentaje from simulaciones_descuentos sd INNER JOIN descuentos_adicionales da ON sd.id_descuento = da.id_descuento where sd. id_simulacion = '".$_REQUEST["id_simulacion"]."' order by sd.id_descuento");

while ($fila1 = sqlsrvr_fetch_array($descuentos_adicionales))
{

?>
		<tr>
			<td>- <?php echo $fila1["nombre"] ?></td>
			<td><input type="text" name="descuentoadicional<?php echo $fila1["id_descuento"] ?>" value="<?php echo number_format(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
<?php

}

if ($fila["fecha_estudio"] >= "2018-01-01")
{

?>
		<tr>
			<td>- COMISI&Oacute;N POR VENTA (RETANQUEOS)</td>
			<td><input type="text" name="comision_venta" value="<?php echo number_format($comision_venta, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- IVA (COMISI&Oacute;N POR VENTA)</td>
			<td><input type="text" name="comision_venta_iva" value="<?php echo number_format($comision_venta_iva, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
<?php

}

?>
		<tr>
			<td>- TRANSFERENCIA</td>
			<td><input type="text" name="transferencia" value="<?php echo number_format($fila["descuento_transferencia"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td>DESEMBOLSO CLIENTE</td>
			<td><input type="text" name="desembolso_cliente" value="<?php echo number_format($desembolso_cliente, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- RETENCI&Oacute;N DE CUOTA</td>
			<td><input type="text" name="retencion_cuota" value="<?php echo number_format($retenciones_cuota, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>- GIROS REALIZADOS</td>
			<td><input type="text" name="giros_realizados" value="<?php echo number_format($giros_realizados, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>SALDO A GIRAR</td>
			<td><input type="text" name="saldo_girar" value="<?php echo number_format($saldo_girar, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td>COMISI&Oacute;N PAGADA</td>
			<td><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CONTABILIDAD") 
			{ ?>
				<input type="checkbox" name="comision_pagada" id="comision_pagada" value="1"<?php if ($fila["comision_pagada"]) 
				{ ?> checked<?php } ?> onClick="window.open('comision_pagada.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&comision_pagada='+this.checked, 'COMISION_PAGADA<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=550,height=400,top=0,left=0');" <?php if ($fila["comision_pagada"] &&  $_SESSION["S_TIPO"] == "CONTABILIDAD") 
				{ ?> disabled<?php } ?>>
				
				&nbsp;
				
				<input type="text" name="fecha_comision_pagada_texto" value="<?php echo $fila["fecha_comision_pagada_texto"] ?>" size="10" style="text-align:center;" readonly>
				
				<?php 
			} else 
				{ ?><input type="text" name="comision_pagada_texto" value="<?php if ($fila["comision_pagada"]) { echo "SI   ".$fila["fecha_comision_pagada_texto"]; } else { echo "NO"; } ?>" size="15" style="text-align:center;" readonly><input type="hidden" name="comision_pagada" value="<?php echo $fila["comision_pagada"] ?>"><?php
				 } ?>
				 
				 <br>
				 <a href="#" id="verHistorialComisionPagada" name="<?php echo $fila["id_simulacion"] ?>">Ver Historial</a></td>
		</tr>
		<tr>
			<td>COMISION A DESCONTAR</td>
			<td><input type="text" name="valor_comision_descontar" value="<?php echo number_format($fila["valor_comision_descontar"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td>MES PROD</td>
			<td><input type="text" name="mes_cartera" value="<?php echo $fila["mes_cartera"] ?>" size="15" style="text-align:center;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA") && !$inconsistencia) { ?> background-color:#EAF1DD;" onChange="if(validarfechacorta(this.value)==false) {this.value=''; return false}"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
<br>
<br>
<h2>COMPRAS DE CARTERA</h2>
<table border="0" cellspacing=1 cellpadding=2" align="center" class="tab1">
<tr>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
	<th>Entidad</th>
	<?php if ($fila["fecha_estudio"] < "2018-01-01") { ?><th>Cuota Retenida</th><?php } ?>
	<th>Valor a Pagar</th>
	<th>F Giro</th>
	<th>F Certificaci&oacute;n</th>
	<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><th><img src="../images/estadocuenta.png" title="Certificaci&oacute;n"></th><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><th><img src="../images/chequefirmado.png" title="Cheque Firmado"></th><?php } ?>
	<th><img src="../images/pazysalvo.png" title="Paz y Salvo"></th>
</tr>
<?php

$queryDB = "SELECT scc.consecutivo, ent.nombre as nombre_entidad, scc.se_compra, scc.id_entidad, scc.entidad, scc.valor_pagar, ad.nombre_grabado from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad LEFT JOIN adjuntos ad ON scc.id_adjunto = ad.id_adjunto where scc.id_simulacion = '".$fila["id_simulacion"]."' order by scc.consecutivo";

$rs2 = sqlsrvr_query($link,$queryDB);

while ($fila2 = sqlsrvr_fetch_array($rs2))
{
	$fecha_vencimiento = "";
	
	$pagada = "0";
	
	$cuota_retenida = "0";
	
	$fecha_girocc = "";
	
	$nombre_grabado = "";
	
	$id_adjunto = "0";
	
	if ($fila2["se_compra"] == "SI" && ($fila2["id_entidad"] || $fila2["entidad"]))
	{
		$agenda_tmp = sqlsrvr_query($link,"select fecha_vencimiento from agenda where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila2["consecutivo"]."'");
		
		if (sqlsrvr_fetch_array($agenda_tmp))
		{
			$fila1 = sqlsrvr_fetch_array($agenda_tmp);
			
			$fecha_vencimiento = $fila1["fecha_vencimiento"];
		}
		
		$cc_tmp = sqlsrvr_query($link,"select tcc.pagada, tcc.cuota_retenida, tcc.fecha_giro as fecha_girocc, ad.nombre_grabado, tcc.id_adjunto, tcc.usuario_firma_cheque, tcc.fecha_firma_cheque from tesoreria_cc tcc LEFT JOIN adjuntos ad ON tcc.id_adjunto = ad.id_adjunto where tcc.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND tcc.consecutivo = '".$fila2["consecutivo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrvr_num_rows($cc_tmp))
		{
			$fila1 = sqlsrvr_fetch_array($cc_tmp);
			
			$pagada = $fila1["pagada"];
			
			$cuota_retenida = $fila1["cuota_retenida"];
			
			$fecha_girocc = $fila1["fecha_girocc"];
			
			$nombre_grabado = $fila1["nombre_grabado"];
			
			$id_adjunto = $fila1["id_adjunto"];
			
			if ($fila1["fecha_firma_cheque"])
			{
				$cheque_firmado = $fila1["fecha_firma_cheque"].",<br>".utf8_decode($fila1["usuario_firma_cheque"]);
			}
			else
			{
				$cheque_firmado = "";
			}
		}
		
		$total_cuota_retenida += $cuota_retenida;
		
?>
<tr>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="checkbox" name="pagada<?php echo $fila2["consecutivo"] ?>" id="pagada<?php echo $fila2["consecutivo"] ?>" value="1"<?php if ($pagada) { ?> checked<?php } ?>></td><?php } else { ?><input type="hidden" name="pagada<?php echo $fila2["consecutivo"] ?>" value="<?php echo $pagada ?>"><?php } ?>
	<td><input type="text" id="entidad<?php echo $fila2["consecutivo"] ?>" name="entidad<?php echo $fila2["consecutivo"] ?>" value="<?php echo str_replace("\"", "&#34;", utf8_decode($fila2["nombre_entidad"]." ".$fila2["entidad"])) ?>" style="width:400px;" readonly></td>
	<?php if ($fila["fecha_estudio"] < "2018-01-01") { ?><td><input type="text" id="cuota_retenida<?php echo $fila2["consecutivo"] ?>" name="cuota_retenida<?php echo $fila2["consecutivo"] ?>" value="<?php echo number_format($cuota_retenida, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } separador_miles(this); }" style="text-align:right;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td><?php } else { ?><input type="hidden" name="cuota_retenida<?php echo $fila2["consecutivo"] ?>" value="0"><?php } ?>
	<td><input type="text" id="valor_pagar<?php echo $fila2["consecutivo"] ?>" name="valor_pagar<?php echo $fila2["consecutivo"] ?>" value="<?php echo number_format($fila2["valor_pagar"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
	<td align="center"><input type="text" name="fecha_girocc<?php echo $fila2["consecutivo"] ?>" id="fecha_girocc<?php echo $fila2["consecutivo"] ?>" value="<?php echo $fecha_girocc ?>" size="10"<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_girocc ?>'; return false}"<?php } else { ?> readonly<?php } ?>></td>
	
	<!--<td align="center"><input type="text" name="fecha_vencimiento<?php echo $fila2["consecutivo"] ?>" value="<?php echo $fecha_vencimiento ?>" size="10" style="text-align:center;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> background-color:#EAF1DD;" onChange="if (this.value != '') { if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_vencimiento ?>'; return false} }"<?php } else { ?>" readonly<?php } ?>></td>-->
	
	<td align="center"><input type="text" name="fecha_vencimiento<?php echo $fila2["consecutivo"] ?>" value="<?php echo $fecha_vencimiento ?>" size="10" style="text-align:center;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> background-color:#EAF1DD;" onChange="if (this.value != '') { if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_vencimiento ?>'; return false} }"<?php } else { ?>" readonly<?php } ?>></td>

	<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><td align="center"><?php if ($fila2["nombre_grabado"]) { ?><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$_REQUEST["id_simulacion"]."/adjuntos/".$fila2["nombre_grabado"]) ?>', 'CERTIFICACION<?php echo $fila2["consecutivo"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/estadocuenta.png" title="Certificaci&oacute;n"></a><?php } else { echo "&nbsp;"; } ?></td><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><td align="center"><?php if ($fila["estado_tesoreria"] != "CER") { ?><input type="checkbox" name="cheque_firmado<?php echo $fila2["consecutivo"] ?>" id="cheque_firmado<?php echo $fila2["consecutivo"] ?>" value="1"<?php if ($cheque_firmado) { ?> checked<?php } if (!$inconsistencia) { ?> onClick="window.open('cheque_firmado.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&consecutivo=<?php echo $fila2["consecutivo"] ?>&cheque_firmado='+this.checked, 'CHEQUE_FIRMADO<?php echo $fila2["consecutivo"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=550,height=400,top=0,left=0');"><?php } } ?><?php echo $cheque_firmado ?></td><?php } ?>
	<td align="center"><?php if ($nombre_grabado) { ?><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$_REQUEST["id_simulacion"]."/adjuntos/".$nombre_grabado) ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/pazysalvo.png" title="Paz y Salvo"></a><?php } else if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && ($fila["id_subestado"] == $subestado_desembolso || $fila["id_subestado"] == $subestado_desembolso_cliente || $fila["estado"] == "DES") && $_SESSION["S_SOLOLECTURA"] != "1" && !$inconsistencia) { ?><input type="file" name="archivo<?php echo $fila2["consecutivo"] ?>" style="text-align:center; background-color:#EAF1DD;"><?php } else { echo "&nbsp;"; } ?></td>
</tr>
<?php

	}
}

?>
<tr>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center">&nbsp;</td><?php } ?>
	<td align="center"><?php if (!($_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO")) { ?><a href="#" onClick="window.open('reporte_comprascartera2.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>', 'EXPORTARCOMPRASCARTERA', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0')"><img src="../images/excel.png"></a><?php } ?></td>
	<?php if ($fila["fecha_estudio"] < "2018-01-01") { ?><td><input type="text" name="total_cuota_retenida" value="<?php echo number_format($total_cuota_retenida, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td><?php } ?>
	<td><input type="text" name="total_valor_pagar" value="<?php echo number_format($compras_cartera, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
	<td colspan="5">&nbsp;</td>
</tr>
</table>
<br>
<br>
<h2>GIROS</h2>
<table border="0" cellspacing=1 cellpadding=2 align="center" class="tab3" width="95%">
<tr>
	<th>Beneficiario</th>
	<th>Nit</th>
	<th>Forma Pago</th>
	<th>Valor a Girar</th>
	<th>Banco</th>
	<th>Tipo Cuenta</th>
	<th>Nro Cuenta</th>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") { ?><th>Nro Cheque</th><?php } ?>
	<th>Clasificaci&oacute;n</th>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") { ?><th>Cuenta Giro</th><?php } ?>
	<th>F Giro</th>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || (($_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && !$giros_realizados)) && ($fila["estado_tesoreria"] != "CER") && ($_SESSION["S_SOLOLECTURA"] != "1") && !$inconsistencia) { ?><th><img src="../images/delete.png" title="Borrar"></th><?php } ?>
</tr>
<?php

$queryDB = "select gi.*, ba.nombre as banco from giros gi LEFT JOIN bancos ba ON gi.id_banco = ba.id_banco where gi.id_simulacion = '".$_REQUEST["id_simulacion"]."' order by gi.id_giro";

$rs1 = sqlsrvr_query($link,$queryDB);

while ($fila1 =sqlsrvr_fetch_array($rs1))
{
	$tipo_cuenta = "";
	
	switch ($fila1["tipo_cuenta"])
	{
		case "AHO":	$tipo_cuenta = "AHORROS"; break;
		case "CTE":	$tipo_cuenta = "CORRIENTE"; break;
	}
	
	switch ($fila1["forma_pago"])
	{
		case "CHE":	$forma_pago = "CHEQUE"; break;
		case "CHG":	$forma_pago = "CHEQUE GERENCIA"; break;
		case "EFE":	$forma_pago = "EFECTIVO"; break;
		case "TRA":	$forma_pago = "TRANSFERENCIA"; break;
	}
	
	switch ($fila1["clasificacion"])
	{
		case "CCA":	$clasificacion = "COMPRA DE CARTERA"; break;
		case "DSC":	$clasificacion = "DESEMBOLSO CLIENTE"; break;
		case "GCR":	$clasificacion = "GIRO CUOTA RETENIDA"; break;
		case "RET":	$clasificacion = "RETANQUEO"; break;
	}
	
	$total_valor_girar += $fila1["valor_girar"];
	
?>
<tr>
	<td><input type="text" name="beneficiario<?php echo $fila1["id_giro"] ?>" value="<?php echo str_replace("\"", "&#34;", utf8_decode($fila1["beneficiario"])) ?>" size="25" readonly></td>
	<td><input type="text" name="identificacion<?php echo $fila1["id_giro"] ?>" value="<?php echo $fila1["identificacion"] ?>" size="12" readonly></td>
	<td><input type="text" name="forma_pago<?php echo $fila1["id_giro"] ?>" value="<?php echo $forma_pago ?>" size="18" readonly></td>
	<td><input type="text" name="valor_girar<?php echo $fila1["id_giro"] ?>" value="<?php echo number_format($fila1["valor_girar"], 0, ".", ",") ?>" size="10" style="text-align:right;" readonly></td>
	<td><input type="text" name="banco<?php echo $fila1["id_giro"] ?>" value="<?php echo $fila1["banco"] ?>" size="22" readonly></td>
	<td><input type="text" name="tipo_cuenta<?php echo $fila1["id_giro"] ?>" value="<?php echo $tipo_cuenta ?>" size="10" readonly></td>
	<td><input type="text" name="nro_cuenta<?php echo $fila1["id_giro"] ?>" value="<?php echo str_replace("\"", "&#34;", utf8_decode($fila1["nro_cuenta"])) ?>" size="15" readonly></td>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") { ?><td><input type="text" name="nro_cheque<?php echo $fila1["id_giro"] ?>" value="<?php echo str_replace("\"", "&#34;", utf8_decode($fila1["nro_cheque"])) ?>" size="10"<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> style="text-align:center; background-color:#EAF1DD;"<?php } else { ?> readonly<?php } ?>></td><?php } ?>
	<td><input type="text" name="clasificacion<?php echo $fila1["id_giro"] ?>" value="<?php echo $clasificacion ?>" size="22" readonly></td>
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO")
	{
	
?>
	<td><select name="id_cuentabancaria<?php echo $fila1["id_giro"] ?>" style="width:100px;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>>
			<option value=""></option>
<?php

		$queryDB = "select cb.* from cuentas_bancarias cb INNER JOIN bancos ba ON cb.id_banco = ba.id_banco where cb.id_cuenta IS NOT NULL";
		
		if (($fila1["fecha_giro"] && $fila1["id_cuentabancaria"]) || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA"))
			$queryDB .= " AND id_cuenta = '".$fila1["id_cuentabancaria"]."'";
		
		$queryDB .= " order by cb.nombre, cb.nro_cuenta";
		
		$rs2 = sqlsrvr_query($link,$queryDB);
		
		while ($fila2 = sqlsrvr_fetch_array($rs2))
		{
			$selected = "";
			
			if ($fila2["id_cuenta"] == $fila1["id_cuentabancaria"])
				$selected = " selected";
				
			echo "<option value=\"".$fila2["id_cuenta"]."\"".$selected.">".utf8_decode($fila2["nombre"])."</option>\n";
		}
		
?>
		</select>
	</td>
<?php

	}
	
?>
	<td><input type="text" name="fecha_giro<?php echo $fila1["id_giro"] ?>" value="<?php echo $fila1["fecha_giro"] ?>" size="10"<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && ($fila["estado_tesoreria"] != "CER") && !$inconsistencia) { ?> style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fila1["fecha_giro"] ?>'; return false}"<?php } else { ?> readonly<?php } ?>></td>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || (($_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && !$giros_realizados)) && ($fila["estado_tesoreria"] != "CER") && ($_SESSION["S_SOLOLECTURA"] != "1") && !$inconsistencia) { ?><td align="center"><?php if (!$fila1["fecha_giro"]) { ?><input type="checkbox" name="chk<?php echo $fila1["id_giro"] ?>" value="1"><?php } else { ?>&nbsp;<?php } ?></td><?php } ?>
</tr>
<?php

}

?>
<tr>
	<td><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && (strpos($subestados_tesoreria_no_giro, "'".$fila["id_subestado"]."'") === false) && $fila["estado_tesoreria"] != "CER" && $giro_pendiente > 0 && $_SESSION["S_SOLOLECTURA"] != "1" && !$inconsistencia) { ?><input type="button" value="Adicionar Giro" onClick="window.open('tesoreria_crear.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'ADICIONARGIRO','toolbars=yes,scrollbars=yes,resizable=yes,width=700,height=500,top=0,left=0');"><?php } ?></td>
	<td colspan="2" align="right">&nbsp;</td>
	<td><input type="text" name="total_valor_girar" value="<?php echo number_format($total_valor_girar, 0, ".", ",") ?>" size="10" style="text-align:right; font-weight:bold;" readonly></td>
	<td colspan="7">&nbsp;</td>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || (($_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && !$giros_realizados)) && ($fila["estado_tesoreria"] != "CER") && ($_SESSION["S_SOLOLECTURA"] != "1") && !$inconsistencia) { ?><td align="center">&nbsp;</td><?php } ?>
</tr>
</table>
<?php if (((($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO") && $fila["estado_tesoreria"] != "CER") || (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && ($fila["id_subestado"] == $subestado_desembolso || $fila["id_subestado"] == $subestado_desembolso_cliente || $fila["estado"] == "DES"))) && $_SESSION["S_SOLOLECTURA"] != "1" && !$inconsistencia) { ?><br><input type=submit value="  Actualizar  "><?php } ?>
</form>




<div class="modal" id="modalHistorialPagoComisiones" data-animation="slideInOutLeft">
	<div class="modal-dialog">
    	<header class="modal-header">
        	Historial Pago Comisiones
        	<button type="button" class="close-modal" data-close>x</button>
        </header>
        
		<section class="modal-content">
			<div id="divTablaHistorialPagoComisiones">
				<table id="tablaHistorialPagoComisiones">
				</table>
			</div>
        </section>
        <footer class="modal-footer">
        	Derechos reservados Kredit 2021
        </footer>
    </div>
</div>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../plugins/modal/modal.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>


<script type="text/javascript"> 

$('#verHistorialComisionPagada').click(function(e)
	{
		cargarHistorialComisionPagada($('#verHistorialComisionPagada').attr("name"));
	});
function cargarHistorialComisionPagada(id_simulacion)
{
	//alert(id_simulacion);
	$.ajax({
				type: 'POST',
				url: '../bd/consultasTablas.php',
				data: "exe=consultarHistorialPagoComisiones&id_simulacion="+id_simulacion,
				cache: false,

				success: function(data) {
				//alert(data);
					var arrayJSON=JSON.parse(data);
					Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
				Swal.showLoading();
					$('#tablaHistorialPagoComisiones').DataTable( {
						scrollX: true,
						
						"destroy":true,
						"data":arrayJSON.aaData,
						initComplete: function(settings, json) {
						Swal.close();	
							},
							
						"bPaginate":true,
						"bFilter" : true,   
						"bProcessing": true,
						"pageLength": 40,
						"columns": [
						{ title: 'Pagado', mData: 'comision_pagado', orderable: false},
						{ title: 'Usuario', mData: 'nombre_usuario', orderable: false},
						{ title: 'Fecha', mData: 'fecha',}
						
					],

					order: [[2, 'desc']],
						"language": {"sProcessing":     "Procesando...","sLengthMenu":     "Mostrar _MENU_ registros","sZeroRecords":    "No se encontraron resultados","sEmptyTable":     "Ningún dato disponible en esta tabla","sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros","sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros","sInfoFiltered":   "(filtrado de un total de _MAX_ registros)","sInfoPostFix":    "","sSearch":         "Buscar:","sUrl":            "","sInfoThousands":  ",","sLoadingRecords": "Cargando...","oPaginate": {"sFirst":    "Primero","sLast":     "Último","sNext":     "Siguiente","sPrevious": "Anterior"},"oAria": {"sSortAscending":  ": Activar para ordenar la columna de manera ascendente","sSortDescending": ": Activar para ordenar la columna de manera descendente"}}
					});

					return false;
				}
			});
			$("#modalHistorialPagoComisiones").addClass('is-visible');
}
	



		</script>
<?php include("bottom.php"); ?>
