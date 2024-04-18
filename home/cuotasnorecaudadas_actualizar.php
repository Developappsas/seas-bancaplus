<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.estado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

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

$simulacion_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs))
{
	exit;
}


if ($_REQUEST["action"] == "actualizar")
{
	sqlsrv_query($link, "update cuotas_norecaudadas set id_causal = '".$_REQUEST["id_causal".$_REQUEST["id"]]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE() where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND fecha = '".str_replace("_", "-", $_REQUEST["id"])."'");
	
	echo "<script>alert('Causal actualizada exitosamente');</script>";
}

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function modificar(campoc) {
	with (document.formato3) {
		if (campoc.value == "") {
			alert("Debe seleccionar la causal");
			return false;
		}
		else {
			submit();
		}
	}
}
<?php

echo js_padre_hija("tipos_causalesnorecaudo", "id_tipo", "nombre", "causales_norecaudo", "id_tipo", "id_causal", "nombre", $link);

?>
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Cuotas No Recaudadas</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB = "SELECT cu.cuota, cu.fecha, cu.valor_cuota, CASE WHEN dbo.fn_total_recaudado_mes(nr.id_simulacion, 0, cu.fecha) IS NULL THEN 0 ELSE dbo.fn_total_recaudado_mes(nr.id_simulacion, 0, cu.fecha) END as recaudado, cnr.id_tipo, nr.id_causal, nr.usuario_modificacion, nr.fecha_modificacion from cuotas_norecaudadas nr INNER JOIN cuotas cu ON nr.id_simulacion = cu.id_simulacion AND nr.fecha = cu.fecha LEFT JOIN causales_norecaudo cnr ON nr.id_causal = cnr.id_causal where nr.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$queryDB .= " order by cu.fecha";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
    <form name="formato3" method="post" action="cuotasnorecaudadas_actualizar.php">
        <input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
	    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
                <th>No. Cuota</th>
                <th>Fecha Cuota</th>
                <th>Valor Cuota</th>
                <th>Valor Recaudado</th>
				<th>Tipo Causal</th>
				<th>Causal</th>
				<th>Usuario</th>
				<th width="70">Fecha</th>
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
		
?>
			<tr <?php echo $tr_class ?>>
				<td style="vertical-align:top;" align="center"><?php echo $fila["cuota"] ?></td>
				<td style="vertical-align:top;" align="center"><?php echo $fila["fecha"] ?></td>
				<td style="vertical-align:top;" align="right"><?php echo number_format($fila["valor_cuota"], 0) ?></td>
				<td style="vertical-align:top;" align="right"><?php echo number_format($fila["recaudado"], 0) ?></td>
				<td><select name="id_tipo<?php echo str_replace("-", "_", $fila["fecha"]) ?>" onChange="Cargarcausales_norecaudo(this.value, document.formato3.id_causal<?php echo str_replace("-", "_", $fila["fecha"]) ?>);" style="background-color:#EAF1DD;">
						<option value=""></option>
<?php

		$queryDB = "select id_tipo, nombre from tipos_causalesnorecaudo where estado = '1' OR id_tipo = '".$fila["id_tipo"]."' order by nombre";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["id_tipo"] == $fila["id_tipo"])
				$selected_tipo = " selected";
			else
				$selected_tipo = "";
				
			echo "<option value=\"".$fila1["id_tipo"]."\"".$selected_tipo.">".utf8_decode($fila1["nombre"])."</option>\n";
		}
		
?>
					</select>
				</td>
				<td><select name="id_causal<?php echo str_replace("-", "_", $fila["fecha"]) ?>" style="background-color:#EAF1DD;">
						<option value=""></option>
<?php

		$queryDB = "select id_causal, nombre from causales_norecaudo where id_tipo = '".$fila["id_tipo"]."' AND (estado = '1' OR id_causal = '".$fila["id_causal"]."') order by nombre";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["id_causal"] == $fila["id_causal"])
				$selected_causal = " selected";
			else
				$selected_causal = "";
				
			echo "<option value=\"".$fila1["id_causal"]."\"".$selected_causal.">".utf8_decode($fila1["nombre"])."</option>\n";
		}
		
?>
					</select>
				</td>
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["usuario_modificacion"]) ?></td>
				<td style="vertical-align:top;"><?php echo $fila["fecha_modificacion"] ?></td>
				<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo str_replace("-", "_", $fila["fecha"]) ?>'; modificar(document.formato3.id_causal<?php echo str_replace("-", "_", $fila["fecha"]) ?>)"></td>
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
