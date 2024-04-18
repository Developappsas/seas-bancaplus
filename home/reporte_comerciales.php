<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Comerciales.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); 
?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



$todas_las_unidades = "'0'";

$rs1 = sqlsrv_query($link, "select id_unidad from unidades_negocio order by id_unidad");

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	$todas_las_unidades .= ", '".$fila1["id_unidad"]."'";

?>
<table border="0">
<tr>
	<th>Nombre</th>
	<th>Cedula</th>
	<th>Oficina</th>
	<th>E-mail</th>
	<th>Telefono</th>
	<th>Usuario</th>
	<!--<th>Jefe Comercial</th>-->
	<th>Activo</th>
	<th>Tipo</th>
	<th>Contrato</th>
</tr>
<?php

$queryDB = "select DISTINCT us.nombre as nombre_comercial, us.apellido, us.cedula, CASE WHEN us.freelance = 1 THEN 'SI' ELSE 'NO' END as freelance, CASE WHEN us.outsourcing = 1 THEN 'SI' ELSE 'NO' END as outsourcing, us.contrato, ofi.nombre as oficina, us.email, us.telefono, us.login, CASE WHEN us.jefe_comercial = 1 THEN 'SI' ELSE 'NO' END as jefe_comercial, CASE WHEN us.estado = 1 THEN 'SI' ELSE 'NO' END as estado from usuarios us left join oficinas_usuarios ou on us.id_usuario = ou.id_usuario left join oficinas ofi on ou.id_oficina = ofi.id_oficina left join usuarios_unidades uu on us.id_usuario = uu.id_usuario where us.tipo = 'COMERCIAL'";

$queryDB .= " AND (uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR uu.id_unidad_negocio IS NULL";

$queryDB .= ")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND ou.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')"; //AND si.telemercadeo = '0'";
	
	//if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
	//	$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1')"; //AND si.telemercadeo = '0'";
	
	//if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
	//	$queryDB .= " AND si.telemercadeo = '1'";
}

$queryDB .= " order by us.nombre, us.apellido";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$tipo_comercial = 'PLANTA';
	
	if ($fila["freelance"] == 'SI') {
		$tipo_comercial = 'FREELANCE';
	}
	
	if ($fila["outsourcing"] == 'SI') {
		$tipo_comercial = 'OUTSOURCING';
	}
	
?>
<tr>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td><?php echo utf8_decode($fila["email"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo utf8_decode($fila["login"]) ?></td>
	<!--<td><?php echo $fila["jefe_comercial"] ?></td>-->
	<td><?php echo $fila["estado"] ?></td>
	<td><?php echo $tipo_comercial ?></td>
	<td><?php echo $fila["contrato"] ?></td>
</tr>
<?php

}

?>
</table>
