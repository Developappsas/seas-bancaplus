<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "GERENTECOMERCIAL")
{
	exit;
}

$queryDB = "SELECT si.* from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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
}

$queryDB .= " AND si.decision = '".$label_viable."' AND si.id_subestado = '".$subestado_procesado."'";

$simulacion_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs))
{
	exit;
}

?>
<?php include("top2.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if (observacion.value == "") {
			alert("Debe establecer motivo de la solicitud");
			return false;
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Solicitar Segunda Revisi&oacute;n</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="simulacion_segunda_revision2.php" onSubmit="return chequeo_forma()">
<table>
<tr>
	<td>
		Establezca el motivo de la solicitud<br>
		<textarea name="observacion" rows="3" cols="50" style="width:100%; background-color:#EAF1DD;"></textarea>
	</td>
</tr>
</table>
<br>
<p align="center">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
<input type="hidden" name="back" value="<?php echo $_REQUEST["back"] ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<input type="submit" value="Aceptar">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="window.close()">
</p>
</form>
<?php include("bottom2.php"); ?>
