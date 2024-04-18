<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

$queryDB = "SELECT * from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
}

$queryDB .= " where re.estado != 'ANULADO' AND re.id_reqexcep = '".$_REQUEST["id_reqexcep"]."'";

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

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " AND (CASE re.id_area ";
	$queryDB .= " 	WHEN '".$area_credito."' THEN";
	$queryDB .= " 		CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL THEN";
	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
	$queryDB .= " 		ELSE ";
	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_operativo IS NOT NULL THEN";
	$queryDB .= " 				CASE WHEN si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
	$queryDB .= " 			ELSE 1 = 0 END";
	$queryDB .= " 		END";
/*	$queryDB .= " 	WHEN '".$area_visado."' THEN";
	$queryDB .= " 		CASE WHEN si.pagaduria IN (select pa.nombre from pagadurias pa INNER JOIN pagadurias_usuarios_visado puv ON puv.id_pagaduria = pa.id_pagaduria AND puv.id_usuario = '".$_SESSION["S_IDUSUARIO"]."') THEN 1 = 1";
	$queryDB .= " 		ELSE 1 = 0 END";*/
/*	$queryDB .= " 	WHEN '".$area_gestion_comercial."' THEN";
	$queryDB .= " 		CASE WHEN si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1";
	$queryDB .= " 		ELSE 1 = 0 END";*/
	$queryDB .= " 	ELSE 1 = 1 END";
	$queryDB .= " )";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!sqlsrv_num_rows($rs))
{
    exit;
}

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="reqexcep.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Adjuntos Requerimiento/Excepci&oacute;n</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB = "SELECT * from req_excep_adjuntos where id_reqexcep = '".$_REQUEST["id_reqexcep"]."'";

$queryDB .= " order by id_adjunto";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
    <form name="formato3" method="post" action="reqexcep_adjuntos.php">
        <input type="hidden" name="action" value="">
		<input type="hidden" name="id_reqexcep" value="<?php echo $_REQUEST["id_reqexcep"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="reqexcepb" value="<?php echo $_REQUEST["reqexcepb"] ?>">
		<input type="hidden" name="id_tipob" value="<?php echo $_REQUEST["id_tipob"] ?>">
		<input type="hidden" name="id_areab" value="<?php echo $_REQUEST["id_areab"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
        <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
                <th>Descripci&oacute;n</th>
                <th>Archivo</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th><img src="../images/archivo.png" title="Abrir Adjunto"></th>
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
				<td><?php echo utf8_decode($fila["descripcion"]) ?></td>
				<td><?php echo utf8_decode($fila["nombre_original"]) ?></td>
				<td align="center"><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
				<td align="center"><?php echo $fila["fecha_creacion"] ?></td>
				<td align=center><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$fila["id_simulacion"]."/varios/".$fila["nombre_grabado"]) ?>', 'ADJUNTO<?php echo $fila["id_adjunto"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
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
