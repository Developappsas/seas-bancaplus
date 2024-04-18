<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if (!$_REQUEST["ext"])
	$tipo_cartera = "Originaci&oacute;n";
else
{
	$tipo_cartera = "Externa";
	$sufijo = "_ext";
}

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Venta Cartera <?php echo $tipo_cartera ?></b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td><a href="ventas_crear.php?tipo=VENTA&ext=<?php echo $_REQUEST["ext"] ?>"><?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?>Nueva Venta<?php } else { ?>Cr&eacute;ditos por Vender<?php } ?></a></td>
<?php

	if ($_SESSION["S_TIPO"] != "CONTABILIDAD")
	{
	
?>
	<td>/</td>
	<td><a href="ventas_crear.php?tipo=TRASLADO&ext=<?php echo $_REQUEST["ext"] ?>">Nuevo Traslado</a></td>
	<td>/</td>
	<td><a href="ventas_unificar.php?ext=<?php echo $_REQUEST["ext"] ?>">Unificaci&oacuten Ventas - Traslados</a></td>
<?php

	}
	
	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
	<td>/</td>
	<td><a href="aplicacionpagoscomprador.php?ext=<?php echo $_REQUEST["ext"] ?>">Aplicaci&oacute;n Pagos</a></td>
	<td>/</td>
	<td><a href="carguepagosfondeador.php?ext=<?php echo $_REQUEST["ext"] ?>">Cargue Pagos Fondeador</a></td>
<?php

	}
	
?>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=0 cellpadding=0>
<tr>
	<td valign="bottom" style="white-space:nowrap;">F. Venta Inicial<br>
		<input type="hidden" name="fecha_inicialb" size="10" maxlength="10">
		<select name="fecha_inicialbd">
			<option value="">D&iacute;a</option>
<?php

for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}

?>
		</select>
		<select name="fecha_inicialbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fecha_inicialba">
			<option value="">A&ntilde;o</option>
<?php

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato2.fecha_inicialb');"><img src="../images/calendario.gif" border=0></a>&nbsp
	</td>
	<td valign="bottom" style="white-space:nowrap;">F. Venta Final<br>
		<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
		<select name="fecha_finalbd">
			<option value="">D&iacute;a</option>
<?php

for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}

?>
		</select>
		<select name="fecha_finalbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fecha_finalba">
			<option value="">A&ntilde;o</option>
<?php

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato2.fecha_finalb');"><img src="../images/calendario.gif" border=0></a>&nbsp;
	</td>
	<td valign="bottom">Comprador<br>
		<select name="id_compradorb" style="width:155px">
			<option value=""></option>
<?php

	$queryDB = "SELECT id_comprador, nombre from compradores order by nombre";
	
	$rs1 = sqlsrv_query($link,$queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_comprador"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>

		</select>
	</td>
	<td valign="bottom">Modalidad Prima<br>
		<select name="modalidadb" style="width:155px">
			<option value=""></option>
			<option value="ANT">PRIMA ANTICIPADA</option>
			<option value="MDI">PRIMA MENSUAL DIFERENCIA EN INTERESES</option>
			<option value="MDC">PRIMA MENSUAL DIFERENCIA EN CUOTA</option>
		</select>
	</td>
	<td valign="bottom">No. Venta<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:140px"></td>
	<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda2" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td>
	<td valign="bottom">Estado<br>
		<select name="estadob" style="width:155px">
			<option value=""></option>
			<option value="ALI">ALISTADA</option>
			<option value="VEN">VENDIDA</option>
			<option value="ANU">ANULADA</option>
		</select>
	</td>
	<td valign="bottom"><br><input type="submit" value="Buscar"></td>
</tr>
</table>
</div>
</td>
</tr>
</form>
<?php

if ($_REQUEST["action"])
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT ve.*, co.nombre from ventas".$sufijo." ve INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.id_venta IS NOT NULL";
	
	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
	{
		$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
		
		$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
		
		$fecha_inicialba = $_REQUEST["fecha_inicialba"];
		
		$queryDB .= " AND ve.fecha >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		$fecha_finalbd = $_REQUEST["fecha_finalbd"];
		
		$fecha_finalbm = $_REQUEST["fecha_finalbm"];
		
		$fecha_finalba = $_REQUEST["fecha_finalba"];
		
		$queryDB .= " AND ve.fecha <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
	}
	
	if ($_REQUEST["id_compradorb"])
	{
		$id_compradorb = $_REQUEST["id_compradorb"];
		
		$queryDB .= " AND ve.id_comprador = '".$id_compradorb."'";
	}
	
	if ($_REQUEST["modalidadb"])
	{
		$modalidadb = $_REQUEST["modalidadb"];
		
		$queryDB .= " AND ve.modalidad_prima = '".$modalidadb."'";
	}
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND ve.nro_venta = '".$descripcion_busqueda."'";
	}
	
	if ($_REQUEST["descripcion_busqueda2"])
	{
		$descripcion_busqueda2 = $_REQUEST["descripcion_busqueda2"];
		
		$queryDB .= " AND ve.id_venta IN (select DISTINCT vd.id_venta from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where si.cedula = '".$descripcion_busqueda2."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda2))."%' OR si.nro_libranza like '%".$descripcion_busqueda2."%')";
	}
	
	if ($_REQUEST["estadob"])
	{
		$estadob = $_REQUEST["estadob"];
		
		$queryDB .= " AND ve.estado = '".$estadob."'";
	}
	
	$queryDB .= " order by ve.estado, ve.fecha DESC, ve.fecha_anuncio DESC, CASE WHEN ve.nro_venta IS NULL THEN '999999999' ELSE ve.nro_venta END DESC, ve.id_venta DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila["id_venta"]] == "1")
		{
			if ($_REQUEST["action"] == "confirmar")
			{
				if ($fila["nro_venta"])
				{
					sqlsrv_query($link,"START TRANSACTION");
					
					$queryDB = "SELECT vd.id_ventadetalle, vd.id_simulacion, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.plazo, si.valor_credito, DATEDIFF(vd.fecha_primer_pago, '".$fila["fecha"]."') as dias_primer_vcto, SUM(cu.capital) as saldo_capital from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where vd.id_venta = '".$fila["id_venta"]."' group by vd.id_ventadetalle, vd.id_simulacion, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, si.plazo, si.valor_credito order by si.cedula, vd.id_ventadetalle";
					
					$rs1 = sqlsrv_query($link,$queryDB);
					
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
					{
						switch($fila["modalidad_prima"])
						{
							case "ANT":	sqlsrv_query($link,"insert into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) select '".$fila1["id_ventadetalle"]."', cuota, DATEADD(MONTH,  (cuota - '".$fila1["cuota_desde"]."'), '".$fila1["fecha_primer_pago"]."'), capital, interes, '0', capital + interes, capital + interes from cuotas where id_simulacion = '".$fila1["id_simulacion"]."' AND cuota >= '".$fila1["cuota_desde"]."' AND cuota <= '".$fila1["cuota_hasta"]."' order by cuota"); break;
							
							case "MDI":	$saldo = $fila1["saldo_capital"];
										
										if ($fila1["cuotas_vendidas"] == $fila1["plazo"])
											$saldo = $fila1["valor_credito"];
										
										$tasa_interes = $fila["tasa_venta"];
										
										$queryDB = "SELECT cuota, capital from cuotas".$sufijo." where id_simulacion = '".$fila1["id_simulacion"]."' AND cuota >= '".$fila1["cuota_desde"]."' AND cuota <= '".$fila1["cuota_hasta"]."' order by cuota";
										
										$rs2 = sqlsrv_query($link,$queryDB);
										
										while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC))
										{
											if ($fila2["cuota"] == $fila1["cuota_desde"])
												$dias_vcto = $fila1["dias_primer_vcto"];
											else
												$dias_vcto = 30;
											
											$interes = $saldo * ($tasa_interes / 100.00) * ($dias_vcto / 30.00);
											
											$valor_cuota = $fila2["capital"] + $interes;
											
											sqlsrv_query($link,"insert into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$fila1["id_ventadetalle"]."', '".$fila2["cuota"]."',DATEADD(MONTH,  ('".$fila2["cuota"]."' - '".$fila1["cuota_desde"]."'), '".$fila1["fecha_primer_pago"]."'), '".round($fila2["capital"])."', '".round($interes)."', '0', '".round($valor_cuota)."', '".round($valor_cuota)."')");
											
											$saldo -= $fila2["capital"];
										}
										
										break;
							
							case "MDC":	$saldo = $fila1["saldo_capital"];
										
										if ($fila1["cuotas_vendidas"] == $fila1["plazo"])
											$saldo = $fila1["valor_credito"];
										
										$tasa_interes = $fila["tasa_venta"];
										
										$valor_cuota = $saldo * ($tasa_interes / 100) / (1 - pow(1 + ($tasa_interes / 100), -1 * $fila1["cuotas_vendidas"]));
										
										$j = 1;
										
										for ($j = $fila1["cuota_desde"]; $j <= $fila1["cuota_hasta"]; $j++)
										{
											$interes = $saldo * $tasa_interes / 100.00;
											
											$capital = $valor_cuota - $interes;
											
											$saldo -= $capital;
											
											if ($j == $fila1["cuota_hasta"])
											{
												$valor_cuota += $saldo;
												
												$capital = $valor_cuota - $interes;
												
												$saldo = 0;
											}
											
											sqlsrv_query( $link,"INSERT into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$fila1["id_ventadetalle"]."', '".$j."',DATEADD(MONTH,  ('".$j."' - '".$fila1["cuota_desde"]."'), '".$fila1["fecha_primer_pago"]."'), '".round($capital)."', '".round($interes)."', '0', '".round($valor_cuota)."', '".round($valor_cuota)."')");
										}
										
										break;
						}
					}
					
					$queryDB = "SELECT id_ventadetalle from ventas_detalle".$sufijo." where recomprado_temp = '1' AND id_simulacion IN (select id_simulacion from ventas_detalle".$sufijo." where id_venta = '".$fila["id_venta"]."')";
					
					$rs1 = sqlsrv_query($link,$queryDB);
					
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
					{
						sqlsrv_query($link,"UPDATE ventas_detalle".$sufijo." set recomprado_temp = '0' where id_ventadetalle = '".$fila1["id_ventadetalle"]."'");
						
						sqlsrv_query( $link,"UPDATE ventas_cuotas".$sufijo." set saldo_cuota = '0' , pagada = '1' where id_ventadetalle = '".$fila1["id_ventadetalle"]."'");
					}
					
					$queryDB = "SELECT id_ventadetalle, id_simulacion from ventas_detalle".$sufijo." where id_venta = '".$fila["id_venta"]."'";
					
					$rs1 = sqlsrv_query( $link,$queryDB);
					
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
					{
						sqlsrv_query($link,"INSERT into ventas_detalle_documentos".$sufijo." (id_ventadetalle, id_documentocierre) select '".$fila1["id_ventadetalle"]."', id_documentocierre from documentos_cierre where estado = '1' order by id_documentocierre");
						
						if ($fila["tipo"] ==  "TRASLADO")
						{
							sqlsrv_query( $link,"UPDATE ventas_detalle set recomprado = '1' where id_simulacion = '".$fila1["id_simulacion"]."' AND recomprado = '0' AND id_venta IN (select id_venta from ventas where tipo = 'VENTA' AND estado = 'VEN')");
						}
					}
					
		 			sqlsrv_query($link,"UPDATE ventas".$sufijo." set tipo = 'VENTA', estado = 'VEN' where id_venta = '".$fila["id_venta"]."'");
					
					sqlsrv_query($link,"COMMIT");
				}
				else
					echo "<script>alert('La venta de fecha ".$fila["fecha"]." de comprador ".$fila["nombre"]." no puede ser Confirmada. Debe establece el No. de venta');</script>";
			}
			
			if ($_REQUEST["action"] == "anular")
			{
				$queryDB = "select id_ventadetalle from ventas_detalle".$sufijo." where recomprado_temp = '1' AND id_simulacion IN (select id_simulacion from ventas_detalle".$sufijo." where id_venta = '".$fila["id_venta"]."')";
				
				$rs1 = sqlsrv_query($link,$queryDB);
				
				while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
				{
					sqlsrv_query($link,"update ventas_detalle".$sufijo." set recomprado = '0', recomprado_temp = '0' where id_ventadetalle = '".$fila1["id_ventadetalle"]."'");
				}
				
				sqlsrv_query( $link,"update ventas".$sufijo." set estado = 'ANU', nro_venta = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_venta = '".$fila["id_venta"]."'");
			}
		}
	}
}

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT ve.id_venta, ve.tipo, CASE WHEN ve.nro_venta IS NULL THEN '999999999' ELSE ve.nro_venta END as nro_venta, ve.fecha_anuncio, ve.fecha, ve.fecha_corte, ve.tasa_venta, ve.modalidad_prima, ve.estado, co.nombre from ventas".$sufijo." ve INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.id_venta IS NOT NULL";

$queryDB_count = "select COUNT(*) as c from ventas".$sufijo." ve INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.id_venta IS NOT NULL";

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
	
	$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
	
	$fecha_inicialba = $_REQUEST["fecha_inicialba"];
	
	$queryDB .= " AND ve.fecha >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
	
	$queryDB_count .= " AND ve.fecha >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$fecha_finalbd = $_REQUEST["fecha_finalbd"];
	
	$fecha_finalbm = $_REQUEST["fecha_finalbm"];
	
	$fecha_finalba = $_REQUEST["fecha_finalba"];
	
	$queryDB .= " AND ve.fecha <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
	
	$queryDB_count .= " AND ve.fecha <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
}

if ($_REQUEST["id_compradorb"])
{
	$id_compradorb = $_REQUEST["id_compradorb"];
	
	$queryDB .= " AND ve.id_comprador = '".$id_compradorb."'";
	
	$queryDB_count .= " AND ve.id_comprador = '".$id_compradorb."'";
}

if ($_REQUEST["modalidadb"])
{
	$modalidadb = $_REQUEST["modalidadb"];
	
	$queryDB .= " AND ve.modalidad_prima = '".$modalidadb."'";
	
	$queryDB_count .= " AND ve.modalidad_prima = '".$modalidadb."'";
}

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (ve.nro_venta = '".$descripcion_busqueda."')";
	
	$queryDB_count .= " AND (ve.nro_venta = '".$descripcion_busqueda."')";
}

if ($_REQUEST["descripcion_busqueda2"])
{
	$descripcion_busqueda2 = $_REQUEST["descripcion_busqueda2"];
	
	$queryDB .= " AND ve.id_venta IN (select DISTINCT vd.id_venta from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where si.cedula = '".$descripcion_busqueda2."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda2))."%' OR si.nro_libranza like '%".$descripcion_busqueda2."%')";
	
	$queryDB_count .= " AND ve.id_venta IN (select DISTINCT vd.id_venta from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where si.cedula = '".$descripcion_busqueda2."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda2))."%' OR si.nro_libranza like '%".$descripcion_busqueda2."%')";
}

if ($_REQUEST["estadob"])
{
	$estadob = $_REQUEST["estadob"];
	
	$queryDB .= " AND ve.estado = '".$estadob."'";
	
	$queryDB_count .= " AND ve.estado = '".$estadob."'";
}
else
{
	$queryDB .= " AND ve.estado != 'ANU'";
	
	$queryDB_count .= " AND ve.estado != 'ANU'";
}

$queryDB .= " order by ve.fecha desc, ve.fecha_anuncio ASC, CASE WHEN ve.nro_venta IS NULL THEN '999999999' ELSE ve.nro_venta END ASC, ve.id_venta DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

$rs = sqlsrv_query($link,$queryDB);

$rs_count = sqlsrv_query($link,$queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if ($cuantos)
{
	if ($cuantos > $x_en_x)
	{
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
		
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos)
		{
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos)
			{
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"])
				{
				echo " <a href=\"ventas.php?ext=".$_REQUEST["ext"]."&fecha_inicialbd=".$fecha_inicialbd."&fecha_inicialbm=".$fecha_inicialbm."&fecha_inicialba=".$fecha_inicialba."&fecha_finalbd=".$fecha_finalbd."&fecha_finalbm=".$fecha_finalbm."&fecha_finalba=".$fecha_finalba."&id_compradorb=".$id_compradorb."&modalidadb=".$modalidadb."&descripcion_busqueda=".$descripcion_busqueda."&descripcion_busqueda2=".$descripcion_busqueda2."&estadob=".$estadob."&page=$link_page\">$i</a>";
			}
			else
			{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page)
		{
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"ventas.php?ext=".$_REQUEST["ext"]."&fecha_inicialbd=".$fecha_inicialbd."&fecha_inicialbm=".$fecha_inicialbm."&fecha_inicialba=".$fecha_inicialba."&fecha_finalbd=".$fecha_finalbd."&fecha_finalbm=".$fecha_finalbm."&fecha_finalba=".$fecha_finalba."&id_compradorb=".$id_compradorb."&modalidadb=".$modalidadb."&descripcion_busqueda=".$descripcion_busqueda."&descripcion_busqueda2=".$descripcion_busqueda2."&estadob=".$estadob."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="fecha_inicialbd" value="<?php echo $fecha_inicialbd ?>">
<input type="hidden" name="fecha_inicialbm" value="<?php echo $fecha_inicialbm ?>">
<input type="hidden" name="fecha_inicialba" value="<?php echo $fecha_inicialba ?>">
<input type="hidden" name="fecha_finalbd" value="<?php echo $fecha_finalbd ?>">
<input type="hidden" name="fecha_finalbm" value="<?php echo $fecha_finalbm ?>">
<input type="hidden" name="fecha_finalba" value="<?php echo $fecha_finalba ?>">
<input type="hidden" name="id_compradorb" value="<?php echo $id_compradorb ?>">
<input type="hidden" name="modalidadb" value="<?php echo $modalidadb ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="descripcion_busqueda2" value="<?php echo $descripcion_busqueda2 ?>">
<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table class="tab3">
<tr>
	<th>No.</th>
	<th>Id Venta</th>
	<th>Tipo</th>
	<th>F. Anuncio</th>
	<th>F. Venta</th>
	<th>F. Corte</th>
	<th>Comprador</th>
	<th>Tasa</th>
	<th>Modalidad</th>
	<th>Estado</th>
	<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/adicionar.png" title="Adicionar Cr&eacute;ditos"></th><?php } ?>
	<th><img src="../images/archivos.png" title="Archivos Comprador"></th>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
		switch ($fila["modalidad_prima"])
		{
			case "ANT":	$modalidad = "PRIMA ANTICIPADA"; break;
			case "MDI":	$modalidad = "PRIMA MENSUAL DIFERENCIA EN INTERESES"; break;
			case "MDC":	$modalidad = "PRIMA MENSUAL DIFERENCIA EN CUOTA"; break;
		}
		
		switch ($fila["estado"])
		{
			case "ALI":	$estado = "ALISTADA"; break;
			case "VEN":	$estado = "VENDIDA"; break;
			case "ANU":	$estado = "ANULADA"; break;
		}
		
?>
<tr <?php echo $tr_class ?>>
	<td align="center"><a href="ventas_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $fila["id_venta"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&id_compradorb=<?php echo $id_compradorb ?>&modalidadb=<?php echo $modalidadb ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&descripcion_busqueda2=<?php echo $descripcion_busqueda2 ?>&estadob=<?php echo $estadob ?>&page=<?php echo $_REQUEST["page"] ?>"><?php if ($fila["nro_venta"] == "999999999") { echo "-"; } else { echo $fila["nro_venta"]; } ?></a></td>
	<td><?php echo $fila["id_venta"]; ?></td>
	<td align="center"><?php echo $fila["tipo"] ?></td>
	<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_anuncio"] ?></td>
	<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha"] ?></td>
	<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_corte"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="right"><?php echo number_format($fila["tasa_venta"], 4) ?></td>
	<td align="center"><?php echo $modalidad ?></td>
	<td align="center"><?php echo $estado ?></td>
	<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if ($estado == "ALISTADA") { ?><a href="ventas_crear.php?tipo=<?php echo $fila["tipo"] ?>&ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $fila["id_venta"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&id_compradorb=<?php echo $id_compradorb ?>&modalidadb=<?php echo $modalidadb ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&descripcion_busqueda2=<?php echo $descripcion_busqueda2 ?>&estadob=<?php echo $estadob ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adicionar.png" title="Adicionar Cr&eacute;ditos"></a><?php } ?></td><?php } ?>
	<td align="center"><a href="ventas_archivos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $fila["id_venta"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&id_compradorb=<?php echo $id_compradorb ?>&modalidadb=<?php echo $modalidadb ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&descripcion_busqueda2=<?php echo $descripcion_busqueda2 ?>&estadob=<?php echo $estadob ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/archivos.png" title="Archivos Comprador"></a></td>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if ($fila["estado"] == "ALI") { ?><input type="checkbox" name="chk<?php echo $fila["id_venta"] ?>" value="1"><?php } ?></td><?php  } ?>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<?php

	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
<br>
<p align="center"><input type="submit" value="Confirmar" onClick="document.formato3.action.value='confirmar'">&nbsp;&nbsp;&nbsp;<input type="submit" value="Anular" onClick="document.formato3.action.value='anular'"></p>
<?php

	}
	
?>
</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
