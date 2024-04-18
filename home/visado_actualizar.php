<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.estado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND se.cod_interno >= '".$cod_interno_subestado_aprobado_pdte_visado."' AND se.cod_interno < 999))";

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

$simulacion_rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs))
{
	exit;
}

if ($_REQUEST["action"])
{
    $queryDB = "SELECT * from simulaciones_visado where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
    $queryDB .= " order by id_visado DESC";
	
    $rs = sqlsrv_query($link,$queryDB);
	
    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
        if ($_REQUEST["chk".$fila["id_visado"]] == "1")
		{
            if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1")
			{
                $archivo = sqlsrv_query($link,"select nombre_grabado from simulaciones_visado where id_visado = '".$fila["id_visado"]."'");
				
                $fila1 = sqlsrv_fetch_array($archivo);
				
                if ($fila1["nombre_grabado"])
					delete_file("simulaciones", $_REQUEST["id_simulacion"]."/varios/".$fila1["nombre_grabado"]);
				
				if ($fila["id_observacion"])
	                sqlsrv_query($link,"update simulaciones_observaciones set usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_observacion = '".$fila["id_observacion"]."'");
				
                sqlsrv_query($link,"delete from simulaciones_visado where id_visado = '".$fila["id_visado"]."'");
            }
        }
    }
}

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((valor_visado.value == "") || (id_visador.value == "") || (tipo_visado.value == "")) {
			alert("Los campos marcados con asterisco(*) son obligatorios");
			return false;
		}
		
		ReplaceComilla(observacion)
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="visadoincorp.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Visado</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB_count = "SELECT COUNT(*) as c from simulaciones_visado where id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$rs_count = sqlsrv_query($link,$queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if (!$cuantos)
{

?>
<form name=formato method=post action="visado_crear.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
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
							<td valign="bottom">* Tipo Visado<br>
								<select name="tipo_visado" style="background-color:#EAF1DD;">
									<option value=""></option>
									<option value="OFICIAL">OFICIAL</option>
									<option value="EXTERNO">EXTERNO</option>
								</select>&nbsp;&nbsp;&nbsp;
							</td>
                            <td valign="bottom">* Valor Visado<br><input type="text" name="valor_visado" size="10" style="background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value=''; return false} else { separador_miles(this); }">&nbsp;&nbsp;</td>
							<td valign="bottom">* Visador<br>
								<select name="id_visador" style="background-color:#EAF1DD;">
									<option value=""></option>
<?php

	$queryDB = "SELECT id_visador, nombre from visadores where estado = '1' order by nombre";
	
	$rs1 = sqlsrv_query($link,$queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_visador"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
								</select>&nbsp;&nbsp;&nbsp;
							</td>
                            <td valign="bottom">Observaci&oacute;n<br><textarea name="observacion" rows="2" cols="50" style="background-color:#EAF1DD;"></textarea>&nbsp;&nbsp;</td>
							<td valign="bottom">Adjunto<br><input type="file" name="archivo" style="text-align:center; background-color:#EAF1DD;">&nbsp;&nbsp;&nbsp;</td>
                            <td valign="bottom">&nbsp;<br><input type="submit" value="Ingresar"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</form>
<!--<hr noshade size=1 width=350>
<br>-->
<?php

}

$queryDB = "SELECT sv.*, vi.nombre from simulaciones_visado sv INNER JOIN visadores vi ON sv.id_visador = vi.id_visador where sv.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$queryDB .= " order by sv.id_visado DESC";

$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
    <form name="formato3" method="post" action="visado_actualizar.php">
        <input type="hidden" name="action" value="">
	    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
                <th>Tipo Visado</th>
                <th>Valor Visado</th>
                <th>Visador</th>
                <th>Observaci&oacute;n</th>
				<th>Adjunto</th>
				<th>Usuario</th>
				<th width="70">Fecha</th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/delete.png" title="Borrar"></th><?php } ?>
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
				<td style="vertical-align:top;"><?php echo $fila["tipo_visado"] ?></td>
				<td style="vertical-align:top;" align="right"><?php echo number_format($fila["valor_visado"], 0) ?></td>
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["nombre"]) ?></td>
				<td><?php echo utf8_decode(str_replace(chr(13), "<br>", $fila["observacion"])) ?></td>
				<td style="vertical-align:top;"><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$_REQUEST["id_simulacion"]."/varios/".$fila["nombre_grabado"]) ?>', 'ADJUNTOVI<?php echo $fila["id_visado"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><?php echo utf8_decode($fila["nombre_original"]) ?></a></td>
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
				<td style="vertical-align:top;"><?php echo $fila["fecha_creacion"] ?></td>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="checkbox" name="chk<?php echo $fila["id_visado"] ?>" value="1"></td><?php } ?>
			</tr>
<?php

		$j++;
	}
	
?>
        </table>
        <br>
<?php

	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
		<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value = 'borrar'"></p>
<?php

	}
	
?>
    </form>
<?php

}
else
{
	//echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
