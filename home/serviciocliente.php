<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Servicio al Cliente</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="serviciocliente.php">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
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
	$queryDB = "select si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.estado != 'ANU'";
	
	$queryDB_count = "select COUNT(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.estado != 'ANU'";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		
		$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

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
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
		
		$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
	}
	
	$queryDB .= " order by si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	$rs_count = sqlsrv_query($link,$queryDB_count);
	
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
				echo " <a href=\"serviciocliente.php?descripcion_busqueda=".$descripcion_busqueda."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"serviciocliente.php?descripcion_busqueda=".$descripcion_busqueda."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}






	
?>
<form name="formato3" method="post" action="serviciocliente.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>Estado</th>
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
		
		switch ($fila["estado"])
		{
			case "ING":	$estado = "INGRESADO"; break;
			case "EST":	$estado = "EN ESTUDIO"; break;
			case "NEG":	$estado = "NEGADO"; break;
			case "DST":	$estado = "DESISTIDO"; break;
			case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
			case "DES":	$estado = "DESEMBOLSADO"; break;
			case "CAN":	$estado = "CANCELADO"; break;
			case "ANU":	$estado = "ANULADO"; break;
		}
		
?>
<tr <?php echo $tr_class ?>>
	<td><a href="serviciocliente_actualizar.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td align="right"><?php echo $fila["tasa_interes"] ?></td>
	<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo $fila["plazo"] ?></td>
	<td align="center"><?php echo $estado ?></td>
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
