<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.estado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado != 'ANU'";

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
    $queryDB = "SELECT sc.* from servicio_cliente sc INNER JOIN simulaciones si ON sc.id_simulacion = si.id_simulacion where sc.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado != 'ANU'";
	
    $queryDB .= " order by sc.id_gestion DESC";
	
    $rs = sqlsrv_query($link,$queryDB);
	
    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
        if ($_REQUEST["chk".$fila["id_gestion"]] == "1")
		{
            if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1")
			{
                $archivo = sqlsrv_query( $link,"SELECT nombre_grabado from servicio_cliente where id_gestion = '".$fila["id_gestion"]."'");
				
                $fila1 = sqlsrv_fetch_array($archivo);
				
                if ($fila1["nombre_grabado"])
					delete_file("simulaciones", $_REQUEST["id_simulacion"]."/varios/".$fila1["nombre_grabado"]);
				
                sqlsrv_query($link,"DELETE from servicio_cliente where id_gestion = '".$fila["id_gestion"]."'");
            }
        }
    }
}

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((id_actividad.value == "") || (observacion.value == "")) {
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
        <td valign="top" width="18"><a href="serviciocliente.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Detalle Servicio al Cliente</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="serviciocliente_crear.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
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
							<td valign="bottom">* Actividad/Solicitud<br>
								<select name="id_actividad" style="background-color:#EAF1DD;">
									<option value=""></option>
<?php

$queryDB = "select id_actividad, nombre from actividadessc where estado = '1' order by orden";

$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_actividad"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
						
								</select>&nbsp;&nbsp;&nbsp;
							</td>
                            <td valign="bottom">* Observaci&oacute;n<br><textarea name="observacion" rows="2" cols="50" style="background-color:#EAF1DD;"></textarea>&nbsp;&nbsp;</td>
							<td>Adjunto<br><input type="file" name="archivo" style="text-align:center; background-color:#EAF1DD;"></td>
                            <td valign="bottom">&nbsp;<br><input type="submit" value="Ingresar"></td>
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

$queryDB = "SELECT sc.*, ac.nombre from servicio_cliente sc INNER JOIN simulaciones si ON sc.id_simulacion = si.id_simulacion INNER JOIN actividadessc ac ON sc.id_actividad = ac.id_actividad where sc.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado != 'ANU'";

$queryDB .= " order by sc.id_gestion DESC";

$rs = sqlsrv_query( $link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
    <form name="formato3" method="post" action="serviciocliente_actualizar.php">
        <input type="hidden" name="action" value="">
	    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
                <th>Actividad/Solicitud</th>
                <th>Observaci&oacute;n</th>
				<th>Adjunto</th>
				<th>Usuario</th>
				<th width="70">Fecha</th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/delete.png" title="Borrar"></th><?php } ?>
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
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["nombre"]) ?></td>
				<td><?php echo utf8_decode(str_replace(chr(13), "<br>", $fila["observacion"])) ?></td>
				<td style="vertical-align:top;"><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$_REQUEST["id_simulacion"]."/varios/".$fila["nombre_grabado"]) ?>', 'ADJUNTOSC<?php echo $fila["id_gestion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><?php echo utf8_decode($fila["nombre_original"]) ?></a></td>
				<td style="vertical-align:top;"><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
				<td style="vertical-align:top;"><?php echo $fila["fecha_creacion"] ?></td>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center" style="vertical-align:top;"><input type="checkbox" name="chk<?php echo $fila["id_gestion"] ?>" value="1"></td><?php } ?>
			</tr>
<?php

		$j++;
	}
	
?>
        </table>
        <br>
<?php

	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1")
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
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
