<?php
//Mostrar errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include ('../functions.php');
include ('cors.php');
include ('../function_blob_storage.php');

header("Content-Type: application/json; charset=utf-8");

$link = conectar_utf();
$archivo = null;
$archivo=$_FILES["archivo"];
$id_registro=$_POST["id_simulacion"];
$tipo_archivo=$_POST["tipo_archivo"];
cargueArchivos($archivo,$tipo_archivo,$id_registro);

function cargueArchivos($archivo,$tipo_archivo,$id_registro) {

    global $link;  

    $val1=0;$val2=0;$val3=0;$val4=0;$val5=0;$val6=0;$val7=0;
    $val8=0;$val9=0;$val10=0;$val11=0;$val12=0;$val13=0;$val14=0;
    $response = array();
    $data = array();
    $mensaje="";

    if ($tipo_archivo == null) {
        $val1=1;
        $mensaje.="Debe Especificar Tipo De Adjunto. ";
    }else{
        $val1=0;
    }

    if ($id_registro == null) {
        $val2=1;
        $mensaje.="Debe Especificar ID Simulacion. ";
    }else{
        $val2=0;
    }

    if (strcmp($archivo["name"], "")) {
        $val3=0;
        $upmax_rs = sqlsrv_query($link,"select valor from parametros where codigo IN ('UPMAX') order by codigo");

        $fila1 = sqlsrv_fetch_array($upmax_rs, SQLSRV_FETCH_ASSOC);

        $upmax = $fila1["valor"];
        
        if (($archivo["size"] / 1024) <= $upmax){
            $val4=0;
        }
        else{
            $val4=1;
            $mensaje.="Tamaño de archivo supera el limite. upmax".$upmax." - peso:".($archivo["size"] / 1024);
        }
    }else{
        $val3=1;
        $mensaje.="No se ha recibido archivo. ";
    }

    if ($val1 == 1 || $val2 == 1 || $val3 == 1 || $val4== 1){

        header("HTTP/2.0 200 OK");
        $response =array("codigo"=>"403", "message"=>"Conexion no valida con el servicio. Err: ".$mensaje);
    }else{

        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        $explode=explode(" ",$token);

        $opciones = array(
            'http'=>array(
                'header'=>  "Servicio: SetFiles"."\r\n".
                "Authorization: Bearer ".$explode[1]."\r\n"
            )
        );

        $contexto = stream_context_create($opciones);

        //$json_Input = file_get_contents('https://seas-pruebas-v1.azurewebsites.net/servicios/validador.php', false, $contexto);
        //$parametros=json_decode($json_Input);

        //if ($parametros->code==200){
            $extension=explode("/",$archivo['type']);
            $nombreArc=md5(rand()+$id_registro).".".$extension[1];
            sqlsrv_query($link,"insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$id_registro."', '".$tipo_archivo."', 'FOTO CARGADA DESDE SERVICIO SEAS', '".$nombreArc."', '".$nombreArc."', '0', '197', GETDATE())");
            $fechaa =new DateTime();
            $fechaFormateada = $fechaa->format("d-m-Y H:i:s");

            $metadata1 = array(
                'id_simulacion' => $id_registro,
                'descripcion' => ($nombreArc),
                'usuario_creacion' => $_SESSION["S_LOGIN"],
                'fecha_creacion' => $fechaFormateada
            );

            upload_file($archivo, "simulaciones", $id_registro."/adjuntos/".$nombreArc, $metadata1);
            //upload_file($_FILES["archivo"], "qasimulaciones", $_REQUEST["id_simulacion"] . "/adjuntos/" . $nombreArc, $metadata1);
        
            $response = array( "codigo"=>"200","mensaje"=>"Archivo Cargado Satisfactoriamente","id_simulacion"=>$id_registro,"query"=>"insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$id_registro."', '".$tipo_archivo."', 'FOTO CARGADA DESDE SERVICIO SEAS', '".$nombreArc."', '".$nombreArc."', '0', '197', NOW())");
        //}else{
            //header("HTTP/2.0 200 OK");
            //$response = array( "codigo"=>"500","mensaje"=>"Token Invalido" );
        //}
    }

    //$response = array( "codigo"=>"200","mensaje"=>"Satisfactrio" );
    header("HTTP/2.0 200 OK");
    echo json_encode($response);
}
?>