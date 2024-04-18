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
        case 'Deshabilitar Analistas':
     if ($params["jornada_laboral"]=="s"){
            $usuariosDeshabilitar=$params["analistas"];
            $usuariosDeshabilitarDecode=json_decode($usuariosDeshabilitar);
        
        if (count($usuariosDeshabilitarDecode)>0){

            foreach ($usuariosDeshabilitarDecode as $usuariosDeshabilitarEach) {      
                if ($params["id_empresa"]<>"ANTIFRAUDE")
                {
                    $consultarUsuarioEmpresa="SELECT id FROM empresa_usuario_fdc WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."'";
                    $consultarNuevaUsuarioEmpresa=$consultarUsuarioEmpresa."  and id_empresa='".$usuariosDeshabilitarEach->id_empresa."'";
                    $queryNuevaUsuarioEmpresa=sqlsrv_query($link,$consultarNuevaUsuarioEmpresa, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    if (sqlsrv_num_rows($queryNuevaUsuarioEmpresa)==0)
                    {
                        $consultarOtrasEmpresas=$consultarUsuarioEmpresa." and id_empresa<>'".$usuariosDeshabilitarEach->id_empresa."'";
                        $queryOtraUsuarioEmpresa=sqlsrv_query($link,$consultarOtrasEmpresas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        if (sqlsrv_num_rows($queryOtraUsuarioEmpresa)>0)
                        {
                            sqlsrv_query($link,"DELETE FROM empresa_usuario_fdc WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."' and id_empresa<>'".$usuariosDeshabilitarEach->id_empresa."'");
                        }

                        sqlsrv_query($link,"INSERT INTO empresa_usuario_fdc (id_usuario,id_empresa) VALUES ('".$usuariosDeshabilitarEach->id_analista."','".$usuariosDeshabilitarEach->id_empresa."')");

                    }
  
                }
                

                $consultaActualizarUsuarios=sqlsrv_query($link, "UPDATE usuarios SET disponible='".$usuariosDeshabilitarEach->estado."',cantidad_creditos='".$usuariosDeshabilitarEach->cantidad_creditos."' WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."'");
            }
        }


            $mensaje="Proceso ejecutado Satisfactoriamente";
            
        }else{
            $mensaje="Jornada laboral inactiva";
        }
        
            $response = array('codigo' => 200, 'mensaje' => $mensaje);  

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