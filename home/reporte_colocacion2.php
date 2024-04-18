
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('../functions.php'); 
echo "prueba";
var_dump($_SESSION);
if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Consultas.xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
<tr>

	<th>Mes Prod</th>
	<th>Comercial</th>
	<th>Tipo Comercial</th>
	<th>Contrato</th>
	<th>Oficina</th>
	<th>Numero de Creditos</th>
	<th>Suma Valor Credito</th>
	<th>Suma Valor Desembolso</th>
	<th>Cantidad con Comision por Venta (Retanqueos)</th>
	<th>Suma con Comision por Venta (Retanqueos)</th>
	<th>Cantidad TripleA</th>
	<th>Suma TripleA</th>
</tr>
<?php

$queryDB = "SELECT o.nombre as oficina, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, concat(us.nombre,' ', us.apellido) as nombre, us.contrato, us.freelance, us.outsourcing,  count(id_simulacion) as conteo, sum(valor_credito) as colocacion,
			sum(case si.opcion_credito when 'CLI' then si.opcion_desembolso_cli when 'CCC' then si.opcion_desembolso_ccc when 'CMP' then si.opcion_desembolso_cmp when 'CSO' then si.opcion_desembolso_cso end) as valor_desembolso,
			sum(case when tipo_producto = '1' then 1 else 0 end) as recuperate,
			sum(case when tipo_producto = '1' then valor_credito else 0 end) as suma_recuperate,
			sum(case when tipo_producto = '0' then 1 else 0 end) as triplea,
			sum(case when tipo_producto = '0' then valor_credito else 0 end) as suma_triplea 
			from simulaciones si inner join pagadurias pa ON si.pagaduria = pa.nombre inner join usuarios us on si.id_comercial = us.id_usuario
			inner join oficinas o on o.id_oficina = si.id_oficina 
			where si.id_simulacion IS NOT NULL";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio  IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

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

if ($_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
	{
		$fechaprod_inicialbm = $_REQUEST["fecha_inicialbm"];
		
		$fechaprod_inicialba = $_REQUEST["fecha_inicialba"];
		
		$queryDB .= " AND si.fecha_cartera >= '".$fechaprod_inicialba."-".$fechaprod_inicialbm."-01'";
	}
	
	if ($_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		$fechaprod_finalbm = $_REQUEST["fecha_finalbm"];
		
		$fechaprod_finalba = $_REQUEST["fecha_finalba"];
		
		$queryDB .= " AND si.fecha_cartera <= '".$fechaprod_finalba."-".$fechaprod_finalbm."-01'";
	}	

$queryDB .= " order by pa.nombre";





$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))

{
	$tipo_comercial = 'PLANTA';
	
	if ($fila["freelance"]) {
		$tipo_comercial = 'FREELANCE';
	}
	
	if ($fila["outsourcing"]) {
		$tipo_comercial = 'OUTSOURCING';
	}
	
?>
<tr<?php echo $tr_class ?>>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo strtoupper($fila["nombre"]) ?></td>
	<td><?php echo $tipo_comercial ?></td>
	<td><?php echo $fila["contrato"] ?></td>
	<td><?php echo strtoupper($fila["oficina"]) ?></td>
	<td><?php echo strtoupper($fila["conteo"]) ?></td>
	<td><?php echo strtoupper($fila["colocacion"]) ?></td>
	<td><?php echo strtoupper($fila["valor_desembolso"]) ?></td>
	<td><?php echo strtoupper($fila["recuperate"]) ?></td>
	<td><?php echo strtoupper($fila["suma_recuperate"]) ?></td>
	<td><?php echo strtoupper($fila["triplea"]) ?></td>
	<td><?php echo strtoupper($fila["suma_triplea"]) ?></td>
</tr>	
<?php

	$total_conteo += $fila["conteo"];
	$total_colocacion += $fila["colocacion"];
	$total_valor_desembolso += $fila["valor_desembolso"];
	$total_recuperate += $fila["recuperate"];
	$total_suma_recuperate += $fila["suma_recuperate"];
	$total_triplea += $fila["triplea"];
	$total_suma_triplea += $fila["suma_triplea"];
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="5"><b>TOTALES</b></td>
	<td><b><?php echo $total_conteo ?></b></td>
	<td><b><?php echo $total_colocacion ?></b></td>
	<td><b><?php echo $total_valor_desembolso ?></b></td>
	<td><b><?php echo $total_recuperate ?></b></td>
	<td><b><?php echo $total_suma_recuperate ?></b></td>
	<td><b><?php echo $total_triplea ?></b></td>
	<td><b><?php echo $total_suma_triplea ?></b></td>
</tr>
</table>

