<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.fecha_incorporacion from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND se.cod_interno >= '".$cod_interno_subestado_aprobado_pdte_visado."' AND se.cod_interno < 999))";

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

switch($simulacion["opcion_credito"])
{
	case "CLI":	$opcion_cuota = $simulacion["opcion_cuota_cli"];
				break;
	case "CCC":	$opcion_cuota = $simulacion["opcion_cuota_ccc"];
				break;
	case "CMP":	$opcion_cuota = $simulacion["opcion_cuota_cmp"];
				break;
	case "CSO":	$opcion_cuota = $simulacion["opcion_cuota_cso"];
				break;
}

if ($_REQUEST["action"] == "actualizar")
{
	if ($_REQUEST["estado".$_REQUEST["id"]] == "APROBADA")
	{
		$queryDB_suma = "SELECT SUM(valor_cuota) as s from simulaciones_incorporacion where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND estado = 'APROBADA'";
		
		$rs_suma = sqlsrv_query($link, $queryDB_suma);
		
		$fila_suma = sqlsrv_fetch_array($rs_suma);
		
		$suma = $fila_suma["s"];
		
		if (!$suma)
			$suma = 0;
		
		if ($suma + $_REQUEST["valor_cuota".$_REQUEST["id"]] > $opcion_cuota)
		{
			echo "<script>alert('El valor aprobado hace que el total incorporado supere el valor de la cuota. Novedad NO actualizada');</script>";
			
			$no_actualizar = 1;
		}
		elseif ($suma + $_REQUEST["valor_cuota".$_REQUEST["id"]] == $opcion_cuota)
		{
			$actualizar_fecha_incorporacion = 1;
		}
	}
	
	if (!$no_actualizar)
	{
		sqlsrv_query($link, "UPDATE simulaciones_incorporacion set estado = '".$_REQUEST["estado".$_REQUEST["id"]]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE() where id_incorporacion = '".$_REQUEST["id"]."'");
		
		if ($actualizar_fecha_incorporacion)
			sqlsrv_query($link, "UPDATE simulaciones set incorporado = '1', usuario_incorporacion = '".$_SESSION["S_LOGIN"]."', fecha_incorporacion = GETDATE() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		echo "<script>alert('Novedad actualizada exitosamente');</script>";
	}
}
else if ($_REQUEST["action"] == "borrar")
{
    $queryDB = "SELECT * from simulaciones_incorporacion where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
    $queryDB .= " order by id_incorporacion";
	
    $rs = sqlsrv_query($link, $queryDB);
	
    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
        if ($_REQUEST["chk".$fila["id_incorporacion"]] == "1")
		{
            if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1")
			{
				if ($fila["id_observacion"])
	                sqlsrv_query($link, "UPDATE simulaciones_observaciones set usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_observacion = '".$fila["id_observacion"]."'");
				
                sqlsrv_query($link, "delete from simulaciones_incorporacion where id_incorporacion = '".$fila["id_incorporacion"]."'");
            }
        }
    }
}

$queryDB_suma = "SELECT SUM(valor_cuota) as s from simulaciones_incorporacion where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND (estado IS NULL OR estado = 'APROBADA')";

$rs_suma = sqlsrv_query($link, $queryDB_suma);

$fila_suma = sqlsrv_fetch_array($rs_suma);

$suma = $fila_suma["s"];

if (!$suma)
	$suma = 0;

$faltante_cuota = $opcion_cuota - $suma;

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((nro_incorporacion.value == "") || (valor_cuota.value == "")) {
			alert("Los campos marcados con asterisco(*) son obligatorios");
			return false;
		}
		if (parseInt(valor_cuota.value.replace(/\,/g, '')) > <?php echo $faltante_cuota ?>) {
			alert("El valor a incorporar no puede ser mayor al valor pendiente de aprobacion ($<?php echo number_format($faltante_cuota, 0, ".", ",") ?>)");
			return false;
		}
		
		ReplaceComilla(nro_incorporacion)
		ReplaceComilla(observacion)
	}
}
function modificar(campoe) {
	with (document.formato3) {
		if (campoe.value == "") {
			alert("Debe seleccionar el estado");
			return false;
		}
		else {
			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="visadoincorp.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Incorporaci&oacute;n</b><br><br></center></td>
</tr>
</table>
<?php

if ($faltante_cuota > 0)
{

?>
<form name=formato method=post action="incorporacion_crear.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
    <table>
        <tr>
            <td>
                <div class="box1 clearfix">
                    <table border="0" cellspacing=1 cellpadding=2>
                        <tr>
                            <td valign="bottom">* No. Incorporaci&oacute;n<br><input type="text" name="nro_incorporacion" size="20" style="background-color:#EAF1DD;">&nbsp;&nbsp;</td>
                            <td valign="bottom">* Valor Cuota<br><input type="text" name="valor_cuota" size="10" style="background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value=''; return false} else { separador_miles(this); }">&nbsp;&nbsp;</td>
                            <td valign="bottom">Observaci&oacute;n<br><textarea name="observacion" rows="2" cols="50" style="background-color:#EAF1DD;"></textarea>&nbsp;&nbsp;</td>
                            <td valign="bottom">&nbsp;<br><input type="submit" value="Ingresar Novedad"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</form>
<hr noshade size=1 width=350>
<br>
<?php

}

$queryDB = "SELECT * from simulaciones_incorporacion where id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$queryDB .= " order by id_incorporacion";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
    <form name="formato3" method="post" action="incorporacion_actualizar.php">
        <input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
	    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
                <th>Nro. Incorporaci&oacute;n</th>
                <th>Valor Cuota</th>
                <th>Observaci&oacute;n</th>
				<th>Estado</th>
				<th>Usuario</th>
				<th width="70">Fecha</th>
<?php

	if (!$simulacion["fecha_incorporacion"] && !$actualizar_fecha_incorporacion)
	{
	
?>
				<th>Modificar</th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/delete.png" title="Borrar"></th><?php } ?>
<?php

	}
	
?>
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
		
		if ($fila["estado"] == "APROBADA")
			$total_aprobado += $fila["valor_cuota"];
		
?>
			<tr <?php echo $tr_class ?>>
				<td style="vertical-align:top;" align="center"><?php echo $fila["nro_incorporacion"] ?></td>
				<td style="vertical-align:top;" align="right"><?php echo number_format($fila["valor_cuota"], 0) ?><input type="hidden" name="valor_cuota<?php echo $fila["id_incorporacion"] ?>" value="<?php echo $fila["valor_cuota"] ?>"></td>
				<td><?php echo utf8_decode(str_replace(chr(13), "<br>", $fila["observacion"])) ?></td>
				<td><select name="estado<?php echo $fila["id_incorporacion"] ?>" style="background-color:#EAF1DD;">
						<option value=""></option>
						<option value="APROBADA"<?php if ($fila["estado"] == "APROBADA") { ?> selected<?php } ?>>APROBADA</option>
						<option value="NEGADA"<?php if ($fila["estado"] == "NEGADA") { ?> selected<?php } ?>>NEGADA</option>
					</select>
				</td>
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["usuario_modificacion"]) ?></td>
				<td style="vertical-align:top;"><?php echo $fila["fecha_modificacion"] ?></td>
<?php

		if (!$simulacion["fecha_incorporacion"] && !$actualizar_fecha_incorporacion)
		{
		
?>
				<td><?php if (!$fila["estado"] || ($fila["estado"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1")) { ?><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_incorporacion"] ?>'; modificar(document.formato3.estado<?php echo $fila["id_incorporacion"] ?>)"><?php } ?></td>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="checkbox" name="chk<?php echo $fila["id_incorporacion"] ?>" value="1"></td><?php } ?>
<?php

		}
		
?>
			</tr>
<?php

		$j++;
	}
	
?>
			<tr class="tr_bold">
				<td>&nbsp;</td>
				<td align="right"><b><?php echo number_format($total_aprobado / $opcion_cuota * 100, 2) ?>%</b></td>
				<td colspan="6">&nbsp;</td>
			</tr>
        </table>
        <br>
<?php

	if (!$simulacion["fecha_incorporacion"] && !$actualizar_fecha_incorporacion)
	{
		if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1")
		{
		
?>
		<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value = 'borrar'"></p>
<?php

		}
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
