<?php
include ('../../functions.php');
include ('../cors.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Crear Registro Adjuntos':
            if (isset($params["id_simulacion"]) && isset($params["tipo_adjunto"]) && isset($params["nombre_archivo"]) && isset($params["privado"]) && isset($params["id_usuario"]) && isset($params["descripcion"]))
            {
                $crearRegistroAdjuntos="INSERT INTO adjuntos (id_simulacion,descripcion,nombre_original,nombre_grabado,privado,usuario_creacion,fecha_creacion,id_tipo) VALUES ('".$params["id_simulacion"]."','".$params["descripcion"]."','".$params["nombre_archivo"]."','".$params["nombre_archivo"]."','".$params["privado"]."',(SELECT login FROM usuarios where id_usuario='".$params["id_usuario"]."'),CURRENT_TIMESTAMP,'".$params["tipo_adjunto"]."')";
                if (sqlsrv_query($link,$crearRegistroAdjuntos))
                {
                    $id_adjunto1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                    $id_adjunto2= sqlsrv_fetch_array($id_adjunto1);
                    $id_adjunto = $id_adjunto2['id'];
                    $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente','data'=>"$id_adjunto");
                }else{
                    $response = array('codigo' => 400, 'mensaje' => 'Error al crear registro');
                }
                
            }else{
                $response = array('codigo' => 400, 'mensaje' => 'Error en datos recibidos');
            }
            
            
        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;  
            }
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");
?>