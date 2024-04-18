<?php include ('../../functions.php'); ?>
<?php

if (isset($_SESSION["S_LOGIN"])){
	
	$link = conectar();
	if (isset($_REQUEST["id_simulacion"])){
		$rs = sqlsrv_query($link, "select * from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

		if(sqlsrv_num_rows($rs)>0){

			$fila = sqlsrv_fetch_assoc($rs);

			$id_simulacion = $fila["id_simulacion"];

			$consultarPlanDePago=sqlsrv_query($link,"SELECT * FROM cuotas WHERE id_simulacion='".$id_simulacion."'");
            if (sqlsrv_num_rows($consultarPlanDePago)>0)
            {
                $data = array('code' => 200, 'mensaje' => 'Plan de Pago creado Correctamente.');
            }else{
                $data = array('code' => 404, 'mensaje' => 'No existe plan de pago para este credito.');
            }
			
		}else{
			$data = array('code' => 300, 'mensaje' => 'Error, No se encontraron datos de la simulación');
		}
	}else{
		$data = array('code' => 500, 'mensaje' => 'Error, No hay datos de entrada');
	}
}else{
	$data = array('code' => 500, 'mensaje' => 'Error, Sessión Expirada, Vuelva iniciar sesión.');
}

echo json_encode($data);
?>
