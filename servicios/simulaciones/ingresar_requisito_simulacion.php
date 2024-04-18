<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../../functions.php');

$link = conectar_utf();

if(isset($_POST['id_simulacion']) && isset($_POST['id_requisito']) && isset($_POST['id_usuario'])){

    $id_simulacion = $_POST['id_simulacion'];
    $id_requisito = $_POST['id_requisito'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_app_comercial.'Requerimientos/Crear_Requerimientos',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{ "credito_Id": '.$id_simulacion.', "simulacion_Id": '.$id_simulacion.', "tipo_Requerimiento": '.$id_requisito.', "observaciones_Requerimiento":"'.$_POST["observacion"].'", "usuario_Externo": 1, "usuario_Id": '.$_POST['id_usuario'].' }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer CMETYMICXZ',
            'Cookie: ARRAffinity=a111ebcedc0a60e992fbce189d9819c72663447f45b10c6fdd43ab459f5275d0; ARRAffinitySameSite=a111ebcedc0a60e992fbce189d9819c72663447f45b10c6fdd43ab459f5275d0'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if($response){
        $reponseReq = json_decode($response);

        if($reponseReq->codigo == 200){
            $queryBD = "INSERT INTO simulaciones_requisitos (id_simulacion, id_requisito, requerimiento_id, observacion, id_usuario_creacion) VALUES ('".$id_simulacion."', '".$id_requisito."', '".$reponseReq->data."', '".$_POST["observacion"]."', '".$_POST['id_usuario']."')";

            if(sqlsrv_query($link,$queryBD)){
                $id1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                $id2= sqlsrv_fetch_array($id1, SQLSRV_FETCH_ASSOC);
                $id = $id2['id'];
                $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'id' => $id);
            }else{
                $data = array('code' => 500, 'mensaje' => 'Error Al ingresar Requisito', "error" => sqlsrv_errors());
            }
        }else{
            $data = array('code' => 500, 'mensaje' => 'Error al crear Requerimiento en la Aplicacion Comercial, '.$reponseReq->mensaje);
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos de la Aplicacion comercial');
    }
}else{
    $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
}

    echo json_encode($data);
?>