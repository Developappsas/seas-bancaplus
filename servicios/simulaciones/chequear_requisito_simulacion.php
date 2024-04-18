<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../../functions.php');

$link = conectar_utf();

if(isset($_POST['id_simulacion']) && isset($_POST['id']) && isset($_POST['chequeo'])){

    $idSimulacion = $_POST['id_simulacion'];
    $id = $_POST['id'];

    $queryBD = "SELECT * FROM simulaciones_requisitos a WHERE id = '" . $_POST['id'] . "'";
    $queryExiste = sqlsrv_query($link,$queryBD, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

    if ($queryExiste && sqlsrv_num_rows($queryExiste) > 0) {
    	$queryBD = "UPDATE simulaciones_requisitos SET estado = '".$_POST['chequeo']."', id_usuario_estado = '".$_SESSION['S_IDUSUARIO']."', fecha_estado = GETDATE() WHERE id = '".$_POST['id']."'";

    	if(sqlsrv_query($link,$queryBD)){
    		$data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
    	}else{
    		$data = array('code' => 500, 'mensaje' => 'Error al realizar la operacion', "error" => sqlsrv_error($link));
    	}
    }else{
    	$data = array('code' => 300, 'mensaje' => 'NO Existe El Registro, Recargue la Pagina');
	}
}else{
    $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
}

echo json_encode($data);
?>