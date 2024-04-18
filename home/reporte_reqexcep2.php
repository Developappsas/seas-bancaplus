	<?php 
// header("Pragma: no-cache");
// header('Content-type: application/vnd.ms-excel');
// header("Content-Disposition: attachment; filename=ReqExcep.xls");
// header("Expires: 0");
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();
?>
<table border="0">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Pagadur&iacute;a</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Req/Excep</th>
	<th>Tipo</th>
	<th>&Aacute;rea</th>
	<th>F Vencimiento</th>
	<th>Descripci&oacute;n</th>
	<th>Tipo Respuesta</th>
	<th>Respuesta</th>
	<th>Estado</th>
	<th>Usuario</th>
	<th>Fecha</th>
	<th>Usuario Respuesta</th>
	<th>Fecha Respuesta</th>
	<th>Unidad Negocio</th>
	<th>Comercial</th>
	<th>Oficina</th>
	<th>Analista</th>
</tr>
<?php

$queryDB = "SELECT si.id_analista_riesgo_crediticio,si.id_analista_riesgo_operativo,si.id_analista_gestion_comercial,un.nombre as unidad_negocio, ofi.nombre as nombre_oficina, concat(us.nombre,' ',us.apellido) as nombre_comercial,re.*, si.cedula, si.nombre, si.nro_libranza, si.pagaduria, si.valor_credito, ti.nombre as tipo, ar.nombre as area from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_reqexcep ti ON re.id_tipo = ti.id_tipo INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area INNER JOIN oficinas ofi ON ofi.id_oficina=si.id_oficina INNER JOIN usuarios us ON us.id_usuario=si.id_comercial INNER JOIN unidades_negocio un ON un.id_unidad=si.id_unidad_negocio where re.estado != 'ANULADO'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["reqexcep"])
{
	$queryDB .= " AND re.reqexcep = '".$_REQUEST["reqexcep"]."'";
}

if ($_REQUEST["id_tipo"])
{
	$queryDB .= " AND re.id_tipo = '".$_REQUEST["id_tipo"]."'";
}

if ($_REQUEST["id_area"])
{
	$queryDB .= " AND re.id_area = '".$_REQUEST["id_area"]."'";
}

if ($_REQUEST["estado"])
{
	$queryDB .= " AND re.estado = '".$_REQUEST["estado"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND DATE(re.fecha_creacion) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND DATE(re.fecha_creacion) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

$queryDB .= " order by re.fecha_creacion DESC";
echo $queryDB;

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $fila["reqexcep"] ?></td>
	<td><?php echo utf8_decode($fila["tipo"]) ?></td>
	<td><?php echo utf8_decode($fila["area"]) ?></td>
	<td><?php echo $fila["fecha_vencimiento"] ?></td>
	<td><?php echo utf8_decode($fila["observacion"]) ?></td>
	<td><?php echo $fila["tipo_respuesta"] ?></td>
	<td><?php echo utf8_decode($fila["respuesta"]) ?></td>
	<td><?php echo $fila["estado"] ?></td>
	<td><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
	<td><?php echo utf8_decode($fila["usuario_respuesta"]) ?></td>
	<td><?php echo $fila["fecha_respuesta"] ?></td>
	<td><?php echo $fila["unidad_negocio"] ?></td>
	<td><?php echo $fila["nombre_comercial"] ?></td>
	<td><?php echo $fila["nombre_oficina"] ?></td>
	<td>
		<?php 
			if ($fila['id_analista_riesgo_crediticio'] <> null) {
				$id_analista = $fila['id_analista_riesgo_crediticio'];
			}else{
				if ( $fila['id_analista_riesgo_operativo'] <> null) {
					$id_analista = $fila['id_analista_riesgo_operativo'];
				}else{
					$id_analista = $fila["id_analista_gestion_comercial"];
				}
			}
			
			$queryDB = "select id_usuario, nombre, apellido from usuarios 
				where id_usuario = '".$id_analista."' order by nombre, apellido, id_usuario";
			$rs1 = sqlsrv_query($link, $queryDB);
			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
				echo ($fila1["nombre"]." ".$fila1["apellido"]);
			}
		?>
	</td>
</tr>
<?php

}

?>
</table>
