<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Clientes.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && ($_SESSION["S_TIPO"] != "OFICINA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION") && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();



$todas_las_unidades = "'0'";

$rs1 = sqlsrv_query($link, "SELECT id_unidad from unidades_negocio order by id_unidad");

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	$todas_las_unidades .= ", '".$fila1["id_unidad"]."'";

?>
<table border="0">
<tr>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Sector</th>
	<th>PagadurIa</th>
	<th>Ciudad</th>
	<th>Institucion</th>
	<th>Salario Basico</th>
	<th>Nivel Educativo</th>
	<th>Embargado</th>
	<th>F Nacimiento</th>
	<th>Direccion</th>
	<th>Telefono</th>
	<th>Email</th>
</tr>
<?php

$queryDB = "SELECT DISTINCT emp.cedula, emp.nombre, pa.sector, emp.pagaduria, emp.ciudad, emp.institucion, emp.salario_basico, emp.nivel_educativo, CASE WHEN emb.c IS NOT NULL THEN 'SI' ELSE 'NO' END as embargo_actual, emp.fecha_nacimiento, emp.direccion, emp.telefono, emp.mail from ".$prefijo_tablas."empleados emp LEFT JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria LEFT JOIN (select cedula, pagaduria, count(*) as c from ".$prefijo_tablas."embargos where fechafin IS NULL group by cedula, pagaduria) as emb ON emb.cedula = emp.cedula AND emb.pagaduria = emp.pagaduria where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND (si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR si.id_unidad_negocio IS NULL";

$queryDB .= ")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND emp.cedula = '".$_REQUEST["cedula"]."'";
}

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND emp.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["ciudad"])
{
	$queryDB .= " AND emp.ciudad = '".$_REQUEST["ciudad"]."'";
}

if ($_REQUEST["institucion"])
{
	$queryDB .= " AND emp.institucion = '".$_REQUEST["institucion"]."'";
}

if ($_REQUEST["edadd"])
{
	$queryDB .= " AND (GETDATE() - ".$_REQUEST["edadd"]." YEAR) >= emp.fecha_nacimiento";
}

if ($_REQUEST["edadh"])
{
	$queryDB .= " AND (GETDATE() -  ".$_REQUEST["edadh"]." YEAR) <= emp.fecha_nacimiento";
}

if ($_REQUEST["salario_basicod"])
{
	$queryDB .= " AND emp.salario_basico >= ".str_replace(",", "", $_REQUEST["salario_basicod"]);
}

if ($_REQUEST["salario_basicoh"])
{
	$queryDB .= " AND emp.salario_basico <= ".str_replace(",", "", $_REQUEST["salario_basicoh"]);
}

if ($_REQUEST["embargo_actual"])
{
	if ($_REQUEST["embargo_actual"] == "SI")
		$queryDB .= " AND emb.c IS NOT NULL";
	else
		$queryDB .= " AND emb.c IS NULL";
}

if ($_REQUEST["nivel_educativo"])
{
	$queryDB .= " AND emp.nivel_educativo = '".$_REQUEST["nivel_educativo"]."'";
}

$queryDB .= " order by emp.nombre, emp.cedula";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo utf8_decode($fila["cedula"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["ciudad"]) ?></td>
	<td><?php echo utf8_decode($fila["institucion"]) ?></td>
	<td><?php echo $fila["salario_basico"] ?></td>
	<td><?php echo utf8_decode($fila["nivel_educativo"]) ?></td>
	<td><?php echo $fila["embargo_actual"] ?></td>
	<td><?php echo $fila["fecha_nacimiento"] ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo utf8_decode($fila["mail"]) ?></td>
</tr>
<?php

}

?>
</table>
