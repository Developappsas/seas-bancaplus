<?php 

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Usuarios.xls");
header("Pragma: no-cache");
header("Expires: 0");



include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();

$todas_las_unidades = "'0'";

$rs1 = sqlsrv_query($link, "SELECT id_unidad from unidades_negocio order by id_unidad");

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
	$todas_las_unidades .= ", '".$fila1["id_unidad"]."'";
}
?>
<table border="0">
<tr>
	<th>Nombre</th>
	<th>E-mail</th>
	<th>Usuario</th>
	<th>Perfil</th>
	<th>Oficina</th>
	<th>Usuario Creacion</th>
	<th>F Creacion</th>
	<th>Usuario Inactivacion</th>
	<th>F Inactivacion</th>
	<th>Estado</th>
	<th>Unidades de Negocio</th>
	<th>Tipo de Comercial</th>
</tr>
<?php
$queryDB = "SELECT DISTINCT CASE WHEN (us.freelance=1 AND us.outsourcing=0) THEN 'FREELANCE' WHEN (us.freelance=0 AND us.outsourcing=1) THEN 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,us.id_usuario, us.nombre as nombre_comercial, us.apellido, us.email, us.login, us.tipo, us.subtipo, ofi.nombre as oficina, us.usuario_creacion, us.fecha_creacion, us.usuario_inactivacion, us.fecha_inactivacion, us.estado from usuarios us left join oficinas_usuarios ou on us.id_usuario = ou.id_usuario left join oficinas ofi on ou.id_oficina = ofi.id_oficina left join usuarios_unidades uu on us.id_usuario = uu.id_usuario where us.tipo <> 'MASTER'";

$queryDB .= " AND (uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR uu.id_unidad_negocio IS NULL";

$queryDB .= ") order by us.nombre, us.apellido";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	if ($fila["subtipo"] == "ANALISTA_REFERENCIA")
		$fila["subtipo"] = "ANALISTA REFERENCIACION";
	
	if ($fila["subtipo"] == "ANALISTA_GEST_COM")
		$fila["subtipo"] = "ANALISTA GESTION COMERCIAL";
	
	if ($fila["subtipo"] == "ANALISTA_VEN_CARTERA")
		$fila["subtipo"] = "ANALISTA VENTA CARTERA";
	
	if ($fila["subtipo"] == "ANALISTA_BD")
		$fila["subtipo"] = "ANALISTA BASE DE DATOS";
	
	if ($fila["subtipo"] == "COORD_PROSPECCION")
		$fila["subtipo"] = "COORDINADOR PROSPECCION";
	
	if ($fila["subtipo"] == "COORD_VISADO")
		$fila["subtipo"] = "COORDINADOR VISADO";
	
	if ($fila["subtipo"] == "COORD_CREDITO")
		$fila["subtipo"] = "COORDINADOR CREDITO";
	
	if ($fila["tipo"] == "GERENTECOMERCIAL")
		$fila["tipo"] = "GERENTE REGIONAL";
	
	if ($fila["tipo"] == "DIRECTOROFICINA")
		$fila["tipo"] = "DIRECTOR OFICINA";
	
	if ($fila["tipo"] == "CARTERA")
		$fila["tipo"] = "DIRECTOR DE CARTERA";
	
	if ($fila["tipo"] == "OPERACIONES")
		$fila["tipo"] = "DIRECTOR DE OPERACIONES";
	
	$i = 0;
	
	$unidades_asociadas = "";
	
	$queryDB = "SELECT un.nombre from usuarios_unidades uu INNER JOIN unidades_negocio un ON uu.id_unidad_negocio = un.id_unidad where uu.id_usuario = '".$fila["id_usuario"]."'";

	$queryDB .= " AND uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

	$queryDB .= " order by un.id_unidad";

	$rs1 = sqlsrv_query($link, $queryDB);

	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($i)
			$unidades_asociadas .= ", ";
		
		$unidades_asociadas .= utf8_decode($fila1["nombre"]);
		
		$i++;
	}
	
	switch ($fila["estado"])
	{
		case '1':	$estado = "ACTIVO"; break;
		case '0':	$estado = "INACTIVO"; break;
	}
	
?>
<tr>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo utf8_decode($fila["email"]) ?></td>
	<td><?php echo utf8_decode($fila["login"]) ?></td>
	<td><?php echo $fila["tipo"] ?><?php if ($fila["subtipo"]) { echo "/".$fila["subtipo"]; } ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td><?php echo $fila["usuario_creacion"] ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
	<td><?php echo $fila["usuario_inactivacion"] ?></td>
	<td><?php echo $fila["fecha_inactivacion"] ?></td>
	<td><?php echo $estado ?></td>
	<td><?php echo utf8_decode($unidades_asociadas) ?></td>
	<td><?php echo $fila["tipo_comercial2"] ?></td>
</tr>
<?php

}

?>
</table>
