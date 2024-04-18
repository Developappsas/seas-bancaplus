<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=GestionCertificaciones.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "PROSPECCION") || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || !$_SESSION["FUNC_AGENDA"])
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>C�dula</th>
	<th>Nombre</th>
	<th>Pagadur�a</th>
	<th>Comercial</th>
	<th>Entidad</th>
	<th>Estado Carta</th>
<!--	<th>F Sugerida</th>
	<th>F Solicitud</th>-->
	<th>F Entrega</th>
	<th>F Vencimiento</th>
</tr>
<?php

$queryDB = "SELECT ag.*, si.cedula, si.nombre, si.pagaduria, us.nombre as nombre_usuario, us.apellido from agenda ag INNER JOIN simulaciones si ON ag.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario where si.estado IN ('ING', 'EST') AND si.id_subestado IS NOT NULL AND si.id_subestado NOT IN (".$subestados_sin_concretar.") AND si.id_subestado IN (".$subestados_agenda.")";



if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

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

if ($_REQUEST["entidad"])
{
	$queryDB .= " AND ag.entidad = '".$_REQUEST["entidad"]."'";
}

if ($_REQUEST["estadocarta"])
{
	$queryDB .= " AND ag.estado = '".$_REQUEST["estadocarta"]."'";
}

if ($_REQUEST["fechasug_inicialbd"] && $_REQUEST["fechasug_inicialbm"] && $_REQUEST["fechasug_inicialba"])
{
	$queryDB .= " AND DATE(ag.fecha_sugerida) >= '".$fechasug_inicialba."-".$fechasug_inicialbm."-".$fechasug_inicialbd."'";
}

if ($_REQUEST["fechasug_finalbd"] && $_REQUEST["fechasug_finalbm"] && $_REQUEST["fechasug_finalba"])
{
	$queryDB .= " AND DATE(ag.fecha_sugerida) <= '".$fechasug_finalba."-".$fechasug_finalbm."-".$fechasug_finalbd."'";
}

if ($_REQUEST["fechasol_inicialbd"] && $_REQUEST["fechasol_inicialbm"] && $_REQUEST["fechasol_inicialba"])
{
	$queryDB .= " AND DATE(ag.fecha_solicitud) >= '".$fechasol_inicialba."-".$fechasol_inicialbm."-".$fechasol_inicialbd."'";
}

if ($_REQUEST["fechasol_finalbd"] && $_REQUEST["fechasol_finalbm"] && $_REQUEST["fechasol_finalba"])
{
	$queryDB .= " AND DATE(ag.fecha_solicitud) <= '".$fechasol_finalba."-".$fechasol_finalbm."-".$fechasol_finalbd."'";
}

if ($_REQUEST["fechaent_inicialbd"] && $_REQUEST["fechaent_inicialbm"] && $_REQUEST["fechaent_inicialba"])
{
	$queryDB .= " AND DATE(ag.fecha_entrega) >= '".$fechaent_inicialba."-".$fechaent_inicialbm."-".$fechaent_inicialbd."'";
}

if ($_REQUEST["fechaent_finalbd"] && $_REQUEST["fechaent_finalbm"] && $_REQUEST["fechaent_finalba"])
{
	$queryDB .= " AND DATE(ag.fecha_entrega) <= '".$fechaent_finalba."-".$fechaent_finalbm."-".$fechaent_finalbd."'";
}

if ($_REQUEST["fechaven_inicialbd"] && $_REQUEST["fechaven_inicialbm"] && $_REQUEST["fechaven_inicialba"])
{
	$queryDB .= " AND DATE(ag.fecha_vencimiento) >= '".$fechaven_inicialba."-".$fechaven_inicialbm."-".$fechaven_inicialbd."'";
}

if ($_REQUEST["fechaven_finalbd"] && $_REQUEST["fechaven_finalbm"] && $_REQUEST["fechaven_finalba"])
{
	$queryDB .= " AND DATE(ag.fecha_vencimiento) <= '".$fechaven_finalba."-".$fechaven_finalbm."-".$fechaven_finalbd."'";
}

$queryDB .= " order by ag.fecha_sugerida, ag.fecha_solicitud, ag.fecha_entrega, ag.entidad, si.nombre";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre_usuario"]." ".$fila["apellido"]) ?></td>
	<td><?php echo utf8_decode($fila["entidad"]) ?></td>
	<td><?php echo $fila["estado"] ?></td>
<!--	<td><?php echo $fila["fecha_sugerida"] ?></td>
	<td><?php echo $fila["fecha_solicitud"] ?></td>-->
	<td><?php echo $fila["fecha_entrega"] ?></td>
	<td><?php echo $fila["fecha_vencimiento"] ?></td>
</tr>
<?php

}

?>
</table>
