<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION"  && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}

$link = conectar_utf();

if (!$_REQUEST["ext"])
	$tipo_cartera = "Originaci&oacute;n";
else
{
	$tipo_cartera = "Externa";
	$sufijo = "_ext";
}

?>
<?php include("top.php"); 
$link = conectar_utf();?>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Cartera <?php echo $tipo_cartera ?></b><br><br></center></td>
</tr>
</table>
<?php

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA")
{

?>
<form name=formato method=post action="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
<?php

	if ($_REQUEST["ext"] && $_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
	<td><a href="cargarcarteraext.php?ext=<?php echo $_REQUEST["ext"] ?>">Cargar Cartera</a></td>
	<td>/</td>
<?php

	}
	
?>
	<td><a href="aplicacionrecaudos.php?ext=<?php echo $_REQUEST["ext"] ?>">Aplicaci&oacute;n Recaudos</a></td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<?php

}

?>
<form name="formato2" method="post" action="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
			<td valign="bottom">Sector<br>
				<select name="sectorb">
					<option value=""></option>
					<option value="PUBLICO">PUBLICO</option>
					<option value="PRIVADO">PRIVADO</option>
				</select>&nbsp;
			</td>
<?php

}

?>
			<td valign="bottom">Pagadur&iacute;a<br>
				<select name="pagaduriab">
					<option value=""></option>
<?php

$queryDB = "SELECT nombre as pagaduria from pagadurias where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(($fila1["pagaduria"]))."</option>\n";
}

?>
				</select>&nbsp;
			</td>
			<td valign="bottom">Estado<br>
				<select name="estadob">
					<option value=""></option>
					<option value="EST">PARCIAL</option>
					<option value="DES">VIGENTE</option>
					<option value="CAN">CANCELADO</option>
				</select>&nbsp;
			</td>
			<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</form>
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

if ($_REQUEST["buscar"])
{
	if (!$_REQUEST["ext"])
	{
		$queryDB = "SELECT si.id_simulacion, si.cedula, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'yyy-MM') as mes_prod, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' WHEN si.estado = 'EST' THEN 'PARCIAL' ELSE 'VIGENTE' END as estado,  si.fecha_prepago, si.prepago_aprobado, SUM(CASE WHEN cu.pagada = '1' THEN 1 ELSE 0 END) as cuotas_pagadas, SUM(CASE WHEN cu.fecha <= GETDATE() THEN 1 ELSE 0 END) as cuotas_causadas, SUM(CASE WHEN cu.fecha < GETDATE() AND pagada = '0' THEN 1 ELSE 0 END) as cuotas_mora from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN (".$subestado_desembolso.", '78', ".$subestado_desembolso_cliente.", '".$subestado_desembolso_pdte_bloqueo."',".$subestados_desembolso_nuevos_tesoreria.")))))";
		
		$queryDB_count = "select COUNT(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN ('".$subestado_desembolso."', '78', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."')))))";
		
		if ($_SESSION["S_TIPO"] == "COMERCIAL")
		{
			$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		else
		{
			$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
			
			$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		}
	}
	else
	{
		$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, dbo.fn_total_recaudado(si.id_simulacion, 1) as total_recaudado, si.fecha_prepago, si.prepago_aprobado, SUM(CASE WHEN cu.pagada = '1' THEN 1 ELSE 0 END) as cuotas_pagadas, SUM(CASE WHEN cu.fecha <= GETDATE() THEN 1 ELSE 0 END) as cuotas_causadas, SUM(CASE WHEN cu.fecha < GETDATE() AND pagada = '0' THEN 1 ELSE 0 END) as cuotas_mora from simulaciones_ext si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion where si.estado IN ('DES', 'CAN')";
		
		$queryDB_count = "select COUNT(*) as c from simulaciones_ext si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.estado IN ('DES', 'CAN')";
	}
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		
		$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
		
		$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
	}
	
	if ($_REQUEST["sectorb"])
	{
		$sectorb = $_REQUEST["sectorb"];
		
		$queryDB .= " AND pa.sector = '".$sectorb."'";
		
		$queryDB_count .= " AND pa.sector = '".$sectorb."'";
	}
	
	if ($_REQUEST["pagaduriab"])
	{
		$pagaduriab = $_REQUEST["pagaduriab"];
		
		$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
		
		$queryDB_count .= " AND si.pagaduria = '".$pagaduriab."'";
	}
	
	if ($_REQUEST["estadob"])
	{
		$estadob = $_REQUEST["estadob"];
		
		$queryDB .= " AND si.estado = '".$estadob."'";
		
		$queryDB_count .= " AND si.estado = '".$estadob."'";
	}
	
	if (!$_REQUEST["ext"])
	{
		$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.fecha_prepago, si.prepago_aprobado order by si.fecha_desembolso DESC, si.id_simulacion  DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	}
	else
	{
		$queryDB .= " group by si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.fecha_prepago, si.prepago_aprobado order by si.fecha_desembolso DESC, si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	}

	
	$rs = sqlsrv_query( $link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	$rs_count = sqlsrv_query( $link,$queryDB_count);
	
	$fila_count = sqlsrv_fetch_array($rs_count);
	
	$cuantos = $fila_count["c"];
}

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
				echo " <a href=\"cartera.php?ext=".$_REQUEST["ext"]."&descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&estadob=".$estadob."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"cartera.php?ext=".$_REQUEST["ext"]."&descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&estadob=".$estadob."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<?php if (!$_REQUEST["ext"]) { ?><th>F. Desemb</th><?php } ?>
	<?php if (!$_REQUEST["ext"]) { ?><th>Mes Prod</th><?php } ?>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>Estado</th>
	<th>Total Recaud.</th>
	<th>Cuotas<br>Pag.</th>
	<th>Cuotas<br>Causad.</th>
	<th>Cuotas<br>Mora</th>
	<th><img src="../images/planpagos.png" title="Plan de Pagos"></th>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && !$_REQUEST["ext"]) { ?><th><img src="../images/norecaudo.png" title="Cuotas No Recaudadas"></th><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO") { ?><th><img src="../images/gestioncobro.png" title="Gesti&oacute;n de Cobro"></th><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><th><img src="../images/estadocuenta.png" title="Certificado de Deuda"></th><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") { ?><th><img src="../images/pazysalvo.png" title="Paz y Salvo"></th><?php } ?>
</tr>
<?php






	$j = 1;
	
	while ($fila =sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
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
		
		$prepago_pendiente = "";
		
		if ($fila["fecha_prepago"] && !$fila["prepago_aprobado"])
			$prepago_pendiente = "<br>(PREPAGO PEND)";
			
?>
<tr <?php echo $tr_class ?>>
	<td><a href="cartera_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
	<?php if (!$_REQUEST["ext"]) { ?><td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_desembolso"] ?></td><?php } ?>
	<?php if (!$_REQUEST["ext"]) { ?><td align="center" style="white-space:nowrap;"><?php echo $fila["mes_prod"] ?></td><?php } ?>
	<td><?php echo ($fila["nombre"]) ?></td>
	<td align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td align="right"><?php echo $fila["tasa_interes"] ?></td>
	<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td><?php echo ($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo $fila["plazo"] ?></td>
	<td align="center"><?php echo $fila["estado"].$prepago_pendiente ?></td>
	<td align="right"><?php echo number_format($fila["total_recaudado"], 0) ?></td>
	<td align="right"><?php echo $fila["cuotas_pagadas"] ?></td>
	<td align="right"><?php echo $fila["cuotas_causadas"] ?></td>
	<td align="right"><?php echo $fila["cuotas_mora"] ?></td>
	<td align="center"><a href="planpagos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&back=cartera&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/planpagos.png" title="Plan de Pagos"></a></td>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && !$_REQUEST["ext"]) { ?><td align="center"><a href="cuotasnorecaudadas_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/norecaudo.png" title="Cuotas No Recaudadas"></a></td><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><td align="center"><a href="gestioncobro.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/gestioncobro.png" title="Gesti&oacute;n de Cobro"></a></td><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><td align="center"><a href="#" onClick="window.open('certificacion_deudaform.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>', 'CERTIFICACION','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=420,top=0,left=0');"><img src="../images/estadocuenta.png" title="Certificado de Deuda"></a><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><a href="#" onClick="var fecha_venc = prompt('Por favor establezca la fecha de vencimiento (aaaa-mm-yy)'); if (fecha_venc) { window.open('certificacion_deudabarras.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_venc='+fecha_venc, 'CERTIFICACION','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0'); } else { alert('Debe establecer la fecha de vencimiento'); }">.</a><?php } ?></td><?php } ?>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") { ?><td align="center"><?php if ($fila["estado"] == "CANCELADO") { ?><a href="#" onClick="window.open('paz_salvo.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>', 'PAZYSALVO','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/pazysalvo.png" title="Paz y Salvo"></a><?php } else { ?>&nbsp;<?php } ?></td><?php } ?>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
</form>
<?php

}
else
{
	if ($_REQUEST["buscar"]) { $mensaje = "No se encontraron registros"; }
	
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
