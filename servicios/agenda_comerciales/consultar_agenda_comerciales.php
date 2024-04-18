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
        case 'Consultar_Informacion_Tabla':
                $consultarInfoUsuario=sqlsrv_query($link,"SELECT * FROM usuarios WHERE id_usuario='".$_POST["id_usuario"]."'");
                $resInfoUsuario=sqlsrv_fetch_array($consultarInfoUsuario, SQLSRV_FETCH_ASSOC);

                $consultarAgendaComercial="SELECT b.estado,b.id_usuario_asignado,c.descripcion as descripcion_estado,a.id_agenda_comercial as id_registro,CONCAT(a.nombre,' ',a.apellido) as nombre_cliente,a.telefono,a.correo,a.fecha_creacion,b.estado FROM agenda_comerciales a LEFT JOIN detalle_estados_agenda_comercial b ON a.id_agenda_comercial=b.id_agenda_comercial LEFT JOIN estados_agenda_comercial c on c.id_estado_agenda_comercial=b.estado WHERE b.vigente='1'";

                if ($resInfoUsuario["tipo"]<>"ADMINISTRADOR"){
                    if ($resInfoUsuario["tipo"]=="DIRECTOROFICINA"){
                        $estado_asigncion=2;
                    }else if ($resInfoUsuario["tipo"]=="COMERCIAL"){
                        $estado_asigncion=3;
                    }else{
                        $estado_asigncion=1;
                    }

                    $consultarAgendaComercial.=" AND b.estado='".$estado_asigncion."' and b.id_usuario_asignado='".$_POST["id_usuario"]."'";
                }

                $data=array();
                $queryAgendaComercial=sqlsrv_query($link,$consultarAgendaComercial, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                if (sqlsrv_num_rows($queryAgendaComercial)>0){
                    $codigo=200;
                    $mensaje="Consulta OK";

                    while ($resAgendaComercial=sqlsrv_fetch_array($queryAgendaComercial, SQLSRV_FETCH_ASSOC)){
                        if ($resAgendaComercial["estado"]==1){
                            $asignado_a="<a class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#modalAsignarAgendaComercial' onclick='llenarSelectsUsuarios(2,".$resAgendaComercial["id_registro"].")' name='".$resAgendaComercial["id_agenda_comercial"]."' >ASIGNAR</a>";
                        }else{
                            if ($resAgendaComercial["estado"]==2){
                                $color="primary";
                            }else if ($resAgendaComercial["estado"]==3){
                                $color="secondary";
                            }

                            $consultarUsuario="SELECT * FROM usuarios WHERE id_usuario='".$resAgendaComercial["id_usuario_asignado"]."'";
                            $queryInfoUsuario=sqlsrv_query($link,$consultarUsuario);
                            $resInfoUsuario=sqlsrv_fetch_array($queryInfoUsuario, SQLSRV_FETCH_ASSOC);

                            $asignado_a="<a class='btn btn-".$color." btn-sm' data-bs-toggle='modal' data-bs-target='#modalAsignarAgendaComercial' onclick='llenarSelectsUsuarios(3,".$resAgendaComercial["id_registro"].")' name='".$resAgendaComercial["id_agenda_comercial"]."' >".$resInfoUsuario["nombre"]." ".$resInfoUsuario["apellido"]."</a>";
                        }
                        $data[] = array(
                            "id_agenda_comercial" => $resAgendaComercial["id_registro"],
                            "nombre_cliente" => $resAgendaComercial["nombre_cliente"],
                            "telefono" => $resAgendaComercial["telefono"],
                            "correo" => $resAgendaComercial["correo"],
                            "fecha_creacion" => $resAgendaComercial["fecha_creacion"],
                            "descripcion_estado" => $resAgendaComercial["descripcion_estado"],
                            "estado" => $resAgendaComercial["estado"],
                            "asignado_a"=>$asignado_a
                        );
                    }

                    
                    $results = array(
                        "sEcho" => 1,
                        "iTotalRecords" => count($data),
                        "iTotalDisplayRecords" => count($data),
                        "aaData" => $data, 
                        "consulta"=>$consultarAgendaComercial
                    );
                    $response = $results;
                    $response = array('codigo' => "200", 'mensaje' => 'Consulta OK',"data"=>$data,"consulta"=>$consultarAgendaComercial,"consulta2"=>"SELECT * FROM usuarios WHERE id_usuario='".$_POST["id_usuario"]."'");
                }else{
                    $results = array(
                        "sEcho" => 1,
                        "iTotalRecords" => count($data),
                        "iTotalDisplayRecords" => count($data),
                        "aaData" => $data       
                    );
                    $codigo=404;
                    $mensaje="No hay informacion para mostrar";
                    $response = array('codigo' => "404", 'mensaje' => 'No hay informacion para mostrar',"consulta"=>$consultarAgendaComercial);
                       
        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada',"operacion"=>$_POST["operacion"]);
        break;   
    }  
}
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");