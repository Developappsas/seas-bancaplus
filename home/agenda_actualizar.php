<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || !$_SESSION["FUNC_AGENDA"]) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	$id = explode("_", $_REQUEST["id"]);

	if ($_REQUEST["fecha_solicitudcarta" . $_REQUEST["id"]])
		$fecha_solicitudcarta = "'" . $_REQUEST["fecha_solicitudcarta" . $_REQUEST["id"]] . "'";
	else
		$fecha_solicitudcarta = "NULL";

	if ($_REQUEST["fecha_entrega" . $_REQUEST["id"]])
		$fecha_entrega = "'" . $_REQUEST["fecha_entrega" . $_REQUEST["id"]] . "'";
	else
		$fecha_entrega = "NULL";

	if ($_REQUEST["fecha_vencimiento" . $_REQUEST["id"]])
		$fecha_vencimiento = "'" . $_REQUEST["fecha_vencimiento" . $_REQUEST["id"]] . "'";
	else
		$fecha_vencimiento = "NULL";

	sqlsrv_query($link, "update agenda set estado = '" . $_REQUEST["estadocarta" . $_REQUEST["id"]] . "', fecha_solicitud = " . $fecha_solicitudcarta . ", fecha_entrega = " . $fecha_entrega . ", fecha_vencimiento = " . $fecha_vencimiento . " where id_simulacion = '" . $id[0] . "' AND consecutivo = '" . $id[1] . "'");

	sqlsrv_query($link, "update simulaciones set dia_confirmacion = (select MAX(fecha_entrega) from agenda where id_simulacion = '" . $id[0] . "'), dia_vencimiento = (select MIN(fecha_vencimiento) from agenda where id_simulacion = '" . $id[0] . "'), status = (CASE WHEN (select count(*) from agenda where estado NOT IN ('CONFIRMADA', 'PAGADA') and id_simulacion = '" . $id[0] . "') > 0 THEN 'PROCESO' ELSE 'PARA RADICAR' END) where id_simulacion = '" . $id[0] . "'");

	echo "<script>alert('Registro actualizado exitosamente');</script>";
}

?>
<script>
	window.location = 'agenda.php?fechasug_inicialbd=<?php echo $_REQUEST["fechasug_inicialbd"] ?>&fechasug_inicialbm=<?php echo $_REQUEST["fechasug_inicialbm"] ?>&fechasug_inicialba=<?php echo $_REQUEST["fechasug_inicialba"] ?>&fechasug_finalbd=<?php echo $_REQUEST["fechasug_finalbd"] ?>&fechasug_finalbm=<?php echo $_REQUEST["fechasug_finalbm"] ?>&fechasug_finalba=<?php echo $_REQUEST["fechasug_finalba"] ?>&fechasol_inicialbd=<?php echo $_REQUEST["fechasol_inicialbd"] ?>&fechasol_inicialbm=<?php echo $_REQUEST["fechasol_inicialbm"] ?>&fechasol_inicialba=<?php echo $_REQUEST["fechasol_inicialba"] ?>&fechasol_finalbd=<?php echo $_REQUEST["fechasol_finalbd"] ?>&fechasol_finalbm=<?php echo $_REQUEST["fechasol_finalbm"] ?>&fechasol_finalba=<?php echo $_REQUEST["fechasol_finalba"] ?>&fechaent_inicialbd=<?php echo $_REQUEST["fechaent_inicialbd"] ?>&fechaent_inicialbm=<?php echo $_REQUEST["fechaent_inicialbm"] ?>&fechaent_inicialba=<?php echo $_REQUEST["fechaent_inicialba"] ?>&fechaent_finalbd=<?php echo $_REQUEST["fechaent_finalbd"] ?>&fechaent_finalbm=<?php echo $_REQUEST["fechaent_finalbm"] ?>&fechaent_finalba=<?php echo $_REQUEST["fechaent_finalba"] ?>&fechaven_inicialbd=<?php echo $_REQUEST["fechaven_inicialbd"] ?>&fechaven_inicialbm=<?php echo $_REQUEST["fechaven_inicialbm"] ?>&fechaven_inicialba=<?php echo $_REQUEST["fechaven_inicialba"] ?>&fechaven_finalbd=<?php echo $_REQUEST["fechaven_finalbd"] ?>&fechaven_finalbm=<?php echo $_REQUEST["fechaven_finalbm"] ?>&fechaven_finalba=<?php echo $_REQUEST["fechaven_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&entidadb=<?php echo $_REQUEST["entidadb"] ?>&estadocartab=<?php echo $_REQUEST["estadocartab"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>