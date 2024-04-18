<?php
// ini_set( 'display_errors', 1 );
// ini_set( 'display_startup_errors', 1 );
// error_reporting( E_ALL );
header('Content-Type: application/json; charset=utf-8');
include('../../functions.php');
$link = conectar();

$id_simulacion = $_POST['id_simulacion'];

if($id_simulacion != null || $id_simulacion != ''){
	$update ="update simulaciones set estado = 'NEG', decision='NEGADO',id_causal ='44', id_subestado = null where id_simulacion = '".$id_simulacion."'";
	if(sqlsrv_query($link, $update)){
		$response = array(
			"estado"=>200,
			"mensaje"=>"Credito Negado con exito"
		);
	}else{
		$response = array(
			"estado"=>300,
			"mensaje"=>"Error de Consullta"
		);
	}
}else{
	$response = array(
		"estado"=>404,
		"mensaje"=>"No se recibe ID de simulacion"
	);
}

echo json_encode($response);

?>