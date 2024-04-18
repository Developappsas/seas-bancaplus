<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once ('../functions.php');
    include_once ('../function_blob_storage.php');   
    include_once ('./cors.php');

    $link = conectar_utf();
    $headers = apache_request_headers();
    $token = $headers['Authorization'];
    $explode=explode(" ",$token);        
   
    $opciones = array(
        'http'=>array(
        'header'=> "Servicio: SetData"."\r\n". "Authorization: Bearer ".$explode[1]."\r\n" )
    );

    $contexto = stream_context_create($opciones);

    $json_Input = file_get_contents($urlPrincipal.'/servicios/validador.php', false, $contexto);

    $response=json_decode($json_Input);
    
    if($response->code==200) {
        $json = file_get_contents('php://input',true);
        $data = json_decode($json);
        $id_simulacion = $_REQUEST["id_simulacion"];

        $consultaCrearRegistroDataProveedores="INSERT INTO proveedores_data (data_body,data_request,data_post,data_get,fecha,id_usuario) VALUES ('".$json."','".json_encode($_REQUEST)."','".(json_encode($_POST))."','".json_encode($_GET)."',CURRENT_TIMESTAMP,'100')";

        $bin = base64_decode($_REQUEST["documento"], true);
        if (strpos($bin, '%PDF') !== 0) {
            $response = array( "code"=>"500","mensaje"=>"Error al Crear PDF Datos de entrada incorrectos.");
        }else{
            # Write the PDF contents to a local file
            $nombreArc = $id_simulacion.'.pdf';

            sqlsrv_query($link,"UPDATE historial_consultas_judiciales SET respuesta = 2, fecha_respuesta = GETDATE() WHERE id_simulacion= '".$id_simulacion."'");

            sqlsrv_query($link,"INSERT INTO adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$id_simulacion."', '74', 'JUDICIAL', '".$nombreArc."', '".$nombreArc."', '0', 'system', GETDATE())");

            file_put_contents('../formatos/judiciales/'.$nombreArc, $bin);

            $fechaa =new DateTime();
            $fechaFormateada = $fechaa->format("d-m-Y H:i:s");       
                            
            $metadata1 = array(
                'id_simulacion' => $id_simulacion,
                'descripcion' => ($nombreArc),
                'usuario_creacion' => "system",
                'fecha_creacion' => $fechaFormateada
            );

            upload_file2('../formatos/judiciales/'.$nombreArc, "simulaciones", $id_simulacion."/adjuntos/".$nombreArc, $metadata1);

            if (time_nanosleep(0, 500000000) === true) { //despues de 1/2 seg elimina el pdf de la carpeta Formato
                unlink('../formatos/judiciales/'.$nombreArc);
            }

            header("HTTP/2.0 200 Servicio OK");        
            if (sqlsrv_query($link,$consultaCrearRegistroDataProveedores)){        
                $response = array( "code"=>"200","mensaje"=>"Creado Satisfactoriamente");
                
            }else{
                $response = array( "code"=>"500","mensaje"=>"Error al guardar la informacion enviada al servicio", "Error"=> sqlsrv_error($link) );
            }
        }
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"500","mensaje"=>"Token Invalido","data"=>$json_Input );
    }

    echo json_encode($response);
?>