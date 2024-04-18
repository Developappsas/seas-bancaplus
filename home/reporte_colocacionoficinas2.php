<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Consultas.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Nombre Oficina</th>
	<th>Cantidad Creditos</th>
	<th>Suma Valor Credito</th>
	<th>Suma Valor Desembolso</th>
	<th>Mes</th>
</tr>
<?php

$queryDB = "SELECT FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod,oficinas.nombre as oficina,count(si.id_simulacion) as cantidad,sum(valor_credito) as valor_credito1,
			sum(case si.opcion_credito when 'CLI' then si.opcion_desembolso_cli when 'CCC' then si.opcion_desembolso_ccc when 'CMP' then si.opcion_desembolso_cmp when 'CSO' then si.opcion_desembolso_cso end) as valor_credito2
			from simulaciones si inner join pagadurias pa ON si.pagaduria = pa.nombre inner join usuarios us on si.id_comercial = us.id_usuario
			inner join oficinas on oficinas.id_oficina = si.id_oficina where si.fecha_cartera IS NOT NULL";

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

$queryDB .= " group by oficina, mes_prod";

echo $queryDB; 

$rs = sqlsrv_query($link, $queryDB);



while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))

{

?>
<tr<?php echo $tr_class ?>>
	<td><?php echo strtoupper($fila["oficina"]) ?></td>
	<td><?php echo $fila["cantidad"] ?></td>
	<td><?php echo $fila["valor_credito1"] ?></td>
	<td><?php echo $fila["valor_credito2"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
</tr>	
<?php

	$total_cantidad += $fila["cantidad"];
	$total_valor_credito1 += $fila["valor_credito1"];
	$total_valor_credito2 += $fila["valor_credito2"];
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td><b>TOTALES</b></td>
	<td><b><?php echo $total_cantidad ?></b></td>
	<td><b><?php echo $total_valor_credito1 ?></b></td>
	<td><b><?php echo $total_valor_credito2 ?></b></td>
	<td>&nbsp;</td>
</tr>
</table>
