<?php include ('functions.php'); ?>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$link = conectar();

$fecha_inicial = '2021-01-31';
	
$fecha = new DateTime($fecha_inicial);

while ($fecha->format('Y-m-t') <  date('Y-m-d')){

	$fecha = new DateTime($fecha->format('Y-m-01'));
	
	$queryDB = "INSERT INTO cuotas_norecaudadas (id_simulacion, fecha, usuario_creacion, fecha_creacion) SELECT si.id_simulacion, '".$fecha->format('Y-m-t')."', 'system', GETDATE() FROM simulaciones si inner join cuotas cu ON si.id_simulacion = cu.id_simulacion AND FORMAT(cu.fecha, 'yyyy-MM') = '".$fecha->format('Y-m')."' LEFT JOIN (SELECT si.id_simulacion, cu.valor_cuota, SUM(pd.valor) as total_recaudo FROM simulaciones si inner join pagos pa on si.id_simulacion = pa.id_simulacion inner join pagos_detalle pd on pa.id_simulacion = pd.id_simulacion and pa.consecutivo = pd.consecutivo inner join cuotas cu ON si.id_simulacion = cu.id_simulacion AND FORMAT(cu.fecha, 'yyyy-MM') = '".$fecha->format('Y-m')."' where (si.estado IN ('DES') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN (".$subestado_desembolso.", ".$subestado_desembolso_cliente.", ".$subestado_desembolso_pdte_bloqueo."))))) and FORMAT(pa.fecha, 'yyyy-MM') = '".$fecha->format('Y-m')."' GROUP BY si.id_simulacion, cu.valor_cuota HAVING (SUM(pd.valor)) >= 0) recaudo ON recaudo.id_simulacion = si.id_simulacion LEFT JOIN cuotas_norecaudadas nr ON nr.id_simulacion = si.id_simulacion AND nr.fecha = '".$fecha->format('Y-m-t')."' where (si.estado IN ('DES') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN (".$subestado_desembolso.", ".$subestado_desembolso_cliente.", ".$subestado_desembolso_pdte_bloqueo."))))) AND FORMAT(si.fecha_primera_cuota, 'yyyy-MM') <= '".$fecha->format('Y-m')."' AND (recaudo.id_simulacion IS NULL OR (recaudo.total_recaudo < recaudo.valor_cuota)) AND nr.id_simulacion IS NULL";
	
	
	$rs = sqlsrv_query($link, $queryDB);

	if($rs){
		echo "Ejecuto El insert de: ".$fecha->format('Y-m-01')."<br>";
	}else{
		echo "Algo ocurrió en : ".$fecha->format('Y-m-01')."<br>";
	}

	$fecha->add(new DateInterval('P1M'));
}

$queryDB = "DELETE cuotas_norecaudadas FROM cuotas_norecaudadas INNER JOIN simulaciones ON cuotas_norecaudadas.id_simulacion = simulaciones.id_simulacion where FORMAT(cuotas_norecaudadas.fecha, 'yyyy-MM') < DATE_FORMAT(simulaciones.fecha_primera_cuota, 'yyyy-MM')";

$rs = sqlsrv_query($link, $queryDB);

?>
