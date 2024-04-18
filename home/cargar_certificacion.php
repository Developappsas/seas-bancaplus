<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_ADJUNTOS"]) {
	exit;
}

if (!$_REQUEST["id_simulacion"] && ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO")) {
	exit;
}

$link = conectar();

$queryDB = "SELECT si.id_simulacion from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND si.estado IN ('ING', 'EST')";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1)) {
	exit;
}

$queryDB = "SELECT ent.nombre, scc.entidad from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND scc.consecutivo = '" . $_REQUEST["consecutivo"] . "'";

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1)) {
	echo "<script>alert('Por favor guardar formulario para carga de archivos'); window.close();</script>";

	exit;
}

?>
<?php include("top2.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if (archivo.value == "") {
				alert("Debe seleccionar la certificacion");
				return false;
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
	<tr>
		<td class="titulo">
			<center><b>Seleccione la certificaci&oacute;n<br>(<?php echo utf8_decode($fila1["nombre"] . " " . $fila1["entidad"]) ?>)</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="cargar_certificacion2.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td><input type="file" name="archivo"></td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
		<tr>
			<td align="center">
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
				<input type="hidden" name="consecutivo" value="<?php echo $_REQUEST["consecutivo"] ?>">
				<input type="submit" value="Cargar Certificaci&oacute;n">
			</td>
		</tr>
	</table>
</form>
<?php include("bottom2.php"); ?>