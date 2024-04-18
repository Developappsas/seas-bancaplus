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

if (isset($_POST["operacion"])){
    switch ($_POST["operacion"]) {
        case 'Consultar_Tipo_Usuario':

            if ($_POST["tipo_usuario"]==2)
            {
                $consultarInfoUsuario.="SELECT * FROM usuarios WHERE estado=1 AND tipo='DIRECTOROFICINA' ORDER BY nombre desc";   
            }else  if ($_POST["tipo_usuario"]==3)
            {
                $consultarInfoUsuario.="SELECT *
                FROM oficinas_usuarios a 
                INNER JOIN usuarios b ON a.id_usuario=b.id_usuario 
                WHERE b.estado=1 AND b.tipo='COMERCIAL' AND a.id_oficina IN 
                (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario=(SELECT id_usuario_asignado FROM detalle_estados_agenda_comercial WHERE vigente=1 AND id_agenda_comercial='".$_POST["id_registro"]."')) ORDER BY a.nombre desc";   
            }
                

                
               
            $queryInfoUsuario=mysqli_query($link,$consultarInfoUsuario);
      
               

           
                $data=array();
                if (mysqli_num_rows($queryInfoUsuario)>0)
                {
                    $codigo=200;
                    
                    $mensaje="Consulta OK";
                    while ($resInfoUsuario=mysqli_fetch_assoc($queryInfoUsuario))
                    {
                   
                        $data[] = array(
                            "usuario_id" => $resInfoUsuario["id_usuario"],
                            "nombre_usuario" => $resInfoUsuario["nombre"]." ".$resInfoUsuario["apellido"],
                            
                        );
                    }

                 
                    $response = array('codigo' => "200", 'mensaje' => 'Consulta OK',"data"=>$data);
                }else{
                 
                    $codigo=404;
                    $mensaje="No hay informacion para mostrar";
                    $response = array('codigo' => "404", 'mensaje' => 'No hay informacion para mostrar');
                }

                
                
        break;

        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada',"operacion"=>$_POST["operacion"]);
        break;   
    }  
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");