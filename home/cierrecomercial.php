<?php 
	include ('../functions.php'); 
	if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
		exit;
	}

	$link = conectar();

	$fecha_inicial = date_format(date_sub(date_create(date("Y-m-d")), date_interval_create_from_date_string('1 month')), 'Y-m');

	$fecha_final = date("Y-m");

	include("top.php"); 
?>
<script language="JavaScript">

function modificar(campomp) {
	with (document.formato3) {
		if (campomp.value == "") {
			alert("Debe digitar mes de produccion. Registro no actualizado");
			return false;
		}
		else if (campomp.value != "<?php echo $fecha_inicial ?>" && campomp.value != "<?php echo $fecha_final ?>") {
			alert("El mes de produccion solo puede ser <?php echo $fecha_inicial ?> o <?php echo $fecha_final ?>. Registro no actualizado");
			return false;
		}
		else {
			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Cierre Comercial</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="cierrecomercial.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">C&eacute;dula/Nombre<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
	<td valign="bottom">Pagadur&iacute;a<br>
		<select name="pagaduriab">
			<option value=""></option>
<?php

$queryDB = "select DISTINCT pagaduria from ".$prefijo_tablas."empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";


$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
		</select>&nbsp;
	</td>
<?php

if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") {

?>
	<td valign="bottom">Oficina<br>
		<select name="id_oficinab">
			<option value=""></option>
<?php

	$queryDB = "select id_oficina, codigo, nombre from oficinas";
	
	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION") {
	    $queryDB .= " where id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	}
	
	$queryDB .= " order by nombre";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
		echo "<option value=\"".$fila1["id_oficina"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>

		</select>&nbsp;
	</td>
<?php 

}

if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION"){

?>
	<td valign="bottom">Comercial<br>
		<select name="id_comercialb">
			<option value=""></option>
<?php

	$queryDB = "select id_usuario, nombre, apellido from usuarios where tipo <> 'MASTER' AND tipo = 'COMERCIAL' order by nombre, apellido, id_usuario";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
		echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
	}

?>
		</select>&nbsp;
	</td>
<?php

}else{

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {

?>
	<td valign="bottom">Comercial<br>
		<select name="id_comercialb">
			<option value=""></option>
<?php

	$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial where si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."') AND us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL' order by us.nombre, us.apellido, us.id_usuario";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
		echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
	}

}
}

?>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
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

$queryDB = "SELECT si.*, us.nombre as nombre_comercial, us.apellido, se.nombre as nombre_subestado, FORMAT(si.fecha_produccion, 'yyyy-MM') as mes_prod from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where si.id_simulacion IS NOT NULL AND FORMAT(si.fecha_produccion, 'yyyy-MM') >= '".$fecha_inicial."' AND FORMAT(si.fecha_produccion, 'yyyy-MM') <= '".$fecha_final."' AND si.decision != '".$label_negado."'";

$queryDB_count = "SELECT COUNT(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where si.id_simulacion IS NOT NULL AND FORMAT(si.fecha_produccion, 'yyyy-MM') >= '".$fecha_inicial."' AND FORMAT(si.fecha_produccion, 'yyyy-MM') <= '".$fecha_final."' AND si.decision != '".$label_negado."'";

$queryDB_suma = "SELECT SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc WHEN 'CMP' THEN si.opcion_desembolso_cmp WHEN 'CSO' THEN si.opcion_desembolso_cso END) as s, SUM(si.valor_credito) as s2 from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where si.id_simulacion IS NOT NULL AND FORMAT(si.fecha_produccion, 'yyyy-MM') >= '".$fecha_inicial."' AND FORMAT(si.fecha_produccion, 'yyyy-MM') <= '".$fecha_final."' AND si.decision != '".$label_negado."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_suma .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$queryDB_suma .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	$queryDB_suma .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
	
	$queryDB_count .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB_count .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB_count .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
	
	$queryDB_suma .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB_suma .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB_suma .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB_suma .= " AND si.telemercadeo = '1'";
}

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	
	$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	
	$queryDB_suma .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
}

if ($_REQUEST["pagaduriab"])
{
	$pagaduriab = $_REQUEST["pagaduriab"];
	
	$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
	
	$queryDB_count .= " AND si.pagaduria = '".$pagaduriab."'";
	
	$queryDB_suma .= " AND si.pagaduria = '".$pagaduriab."'";
}

if ($_REQUEST["id_comercialb"])
{
	$id_comercialb = $_REQUEST["id_comercialb"];
	
	$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
	
	$queryDB_count .= " AND si.id_comercial = '".$id_comercialb."'";
	
	$queryDB_suma .= " AND si.id_comercial = '".$id_comercialb."'";
}

if ($_REQUEST["id_oficinab"])
{
	$id_oficinab = $_REQUEST["id_oficinab"];
	
	$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
	
	$queryDB_count .= " AND si.id_oficina = '".$id_oficinab."'";
	
	$queryDB_suma .= " AND si.id_oficina = '".$id_oficinab."'";
}

$queryDB .= " order by si.fecha_estudio DESC, si.id_simulacion DESC OFFSET ".$offset." ROWS";



$rs = sqlsrv_query($link, $queryDB);

$rs_count = sqlsrv_query($link, $queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

$rs_suma = sqlsrv_query($link, $queryDB_suma);

$fila_suma = sqlsrv_fetch_array($rs_suma);

$suma = $fila_suma["s"];

$suma2 = $fila_suma["s2"];

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
				echo " <a href=\"cierrecomercial.php?descripcion_busqueda=".$descripcion_busqueda."&pagaduriab=".$pagaduriab."&id_comercialb=".$id_comercialb."&id_oficinab=".$id_oficinab."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"cierrecomercial.php?descripcion_busqueda=".$descripcion_busqueda."&pagaduriab=".$pagaduriab."&id_comercialb=".$id_comercialb."&page=".$siguiente_page."&id_oficinab=".$id_oficinab."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="cierrecomercial_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
<input type="hidden" name="id_comercialb" value="<?php echo $id_comercialb ?>">
<input type="hidden" name="id_oficinab" value="<?php echo $id_oficinab ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 >
<tr>
	<td colspan="18" align="right"><b>TOTAL VR CR&Eacute;DITO: $<?php echo number_format($suma, 0) ?><?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><br>TOTAL VR CR&Eacute;DITO 2: $<?php echo number_format($suma2, 0) ?><?php } ?></b></td>
</tr>
</table>
<table class="tab3">
<tr>
	<th>F. Estudio</th>
	<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?><th>F. Desemb</th><?php } ?>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>Pagadur&iacute;a</th>
	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Comercial</th><?php } ?>
	<th>Vr Cr&eacute;dito</th>
	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><th>Vr Cr&eacute;dito 2</th><?php } ?>
	<th>Estado</th>
	<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><th>Subestado</th><?php } ?>
	<th>Mes Prod</th>
	<th>Modificar</th>
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
		
		switch ($fila["opcion_credito"])
		{
			case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"]; break;
			case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
			case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
			case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
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
	<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_estudio"] ?></td>
	<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?><td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_desembolso"] ?></td><?php } ?>
	<td><a href="simulador.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&id_oficinab=<?php echo $id_oficinab ?>&back=cierrecomercial&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td><?php } ?>
	<td align="right"><?php echo number_format($opcion_desembolso, 0) ?></td>
	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td><?php } ?>
	<td align="center"><?php echo $estado ?></td>
	<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><td><a href="simulaciones_subestados.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&id_oficinab=<?php echo $id_oficinab ?>&back=cierrecomercial&page=<?php echo $_REQUEST["page"] ?>">&nbsp;<?php echo utf8_decode($fila["nombre_subestado"]) ?>&nbsp;</a></td><?php } ?>
	<td align="center"><input type="text" name="mes_prod<?php echo $fila["id_simulacion"] ?>" value="<?php echo $fila["mes_prod"] ?>" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfechacorta(this.value)==false) {this.value='<?php echo $fila["mes_prod"] ?>'; return false}"></td>
	<td align="center"><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_simulacion"] ?>'; modificar(document.formato3.mes_prod<?php echo $fila["id_simulacion"] ?>)"></td>
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
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
