<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "select * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

$venta_rs = sqlsrv_query($link,$queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td valign="top" width="18"><a href="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Archivos Venta</b><br><br></center></td>
</tr>
</table>
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Archivo</th>
	<th>&nbsp;</th>
</tr>
<?php

if ($_SESSION["S_TIPO"] != "CONTABILIDAD") {
	switch ($venta["id_comprador"])	{
	
		case "35":
			echo "<tr>
				<td width='150'>Base Venta</td>
				<td align='center'>
					<a href='archivo_iris.php?ext=".$_REQUEST["ext"]."&id_venta=".$venta["id_venta"]."'>
						<img src='../images/excel.png' title='Exportar'>
					</a>
				</td>
			</tr>";
		break;

		case "1":	//FIC COLECTIVO II
		
?>
<tr>
	<td width="150">Base Venta</td>
	<td align="center"><a href="archivocol2_baseventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "2":	//FIC PENSIONES II
		
?>
<tr>
	<td width="150">Base Venta</td>
	<td align="center"><a href="archivopen2_baseventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "3":	//FONDO DE INVERSION COLECTIVA CERRADO PROGRESION RENTAMAS
		
?>
<tr>
	<td width="150">Liquidaci&oacute;n Venta</td>
	<td align="center"><a href="archivopro_liqventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "4":	//GIROS Y FINANZAS
		
?>
<tr>
	<td width="150">Base Venta</td>
	<td align="center"><a href="archivogyf_baseventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="150" style='background-color:#F1F1F1;'>Reporte Clientes</td>
	<td align="center"><a href="archivogyf_repclientes.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="150">Reporte Direcciones</td>
	<td align="center"><a href="archivogyf_repdirecciones.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="150" style='background-color:#F1F1F1;'>Reporte Cr&eacute;ditos</td>
	<td align="center"><a href="archivogyf_repcreditos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="150">Reporte Cuotas</td>
	<td align="center"><a href="archivogyf_repcuotas.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="150" style='background-color:#F1F1F1;'>Liquidaci&oacute;n Venta</td>
	<td align="center"><a href="archivogyf_liqventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "5":	//FONDO DE INVERSION COLECTIVA CERRADO PROGRESION LIBRANZAS
		
?>
<tr>
	<td width="150">Liquidaci&oacute;n Venta</td>
	<td align="center"><a href="archivopro_liqventa.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "6":	//COOMEVA
		
?>
<tr>
	<td width="150">Vinculaci&oacute;n</td>
	<td align="center"><a href="archivocoo_vinculacion.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/archivo.png" title="Exportar"></a></td>
</tr>
<?php

					break;
					
		case "11":	//COLTEFINANCIERA
		
?>
<tr>
	<td width="200">Base Venta Activos</td>
	<!--
	Reemplazo de formato segun solicitud de Cristina Soto	
	<td align="center"><a href="archivocolt_baseventaactivos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>!-->
	<td align="center"><a href="archivocolt_baseventaactivos_v2.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200">Base Venta Pensionados</td>
	<!--
	Reemplazo de formato segun solicitud de Cristina Soto	
	<td align="center"><a href="archivocolt_baseventapensionados.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>!-->
	<td align="center"><a href="archivocolt_baseventapensionados_v2.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200">Clientes</td>
	<td align="center"><a href="archivocolt_clientes.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200">Clientes (Nueva)</td>
	<td align="center"><a href="archivocolt_clientesnew.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200" hidden>Datos Desembolsos</td>
	<td align="center" hidden><a href="archivocolt_datosdesembolsos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200" hidden>Datos Adicionales</td>
	<td align="center" hidden><a href="archivocolt_datosadicionales.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<tr>
	<td width="200">Datos Desembolsos (Nueva)</td>
	<td align="center"><a href="archivocolt_datosdesembolsosnew.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
<?php

					break;
	}
}

?>
<tr>
	<td width="150">Notificaci&oacute;n Fiduciaria</td>
	<td align="center"><a href="archivo_notificacionfidu.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $venta["id_venta"] ?>"><img src="../images/excel.png" title="Exportar"></a></td>
</tr>
</table>
<br>

<?php include("bottom.php"); ?>
