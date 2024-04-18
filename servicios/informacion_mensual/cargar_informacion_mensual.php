<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 libxml_use_internal_errors(true);
 require_once("../cors.php");
 require_once("../../functions.php");
 $link = conectar_utf();
$archivo_informacion_mensual = $_FILES['informacion_mensual'];
if(!empty($_POST['login']) && !empty($archivo_informacion_mensual)){
    $query_insert = sqlsrv_query($link,"Insert into cargues_mensuales_fondeador(nombre_archivo, fecha_creacion, usuario_creacion)value('".$archivo_informacion_mensual['name']."', GETDATE(), '".$_POST['login']."')");
    if($query_insert){
        // $id_cargue = sqlsrv_insert_id($link);
        $id_cargueQuery = sqlsrv_query($link "SELECT scope_identity() as id_cargue");
        $id_cargueArray= sqlsrv_fetch_array($id_cargueQuery);
        $id_cargue = $id_cargueArray['id_cargue'];
        $resultado = array("estado"=> 200,"mensaje"=>'cargue exitoso', "id_cargue"=>$id_cargue);
    }else{
        $resultado = array("estado"=> 300,"mensaje"=>'Insercion de Informacion Fallida');
    }
}else{
   $resultado = array("estado"=> 400,"mensaje"=>'No se reciben informacion esperada');
}

echo json_encode($resultado);


    

?>