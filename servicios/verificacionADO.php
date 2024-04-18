<?php 
    include_once ('../functions.php');
    $link = conectar_utf();
    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    $json = file_get_contents('php://input',true);
    
    $data = json_decode($_POST["_Response"]);
    //var_dump($data->Extras->IdState);
    //echo $json;
    //$responseWS=json_decode($data["_Response"]);
    
    $consultarActualizar = "UPDATE historial_tokens_verificacion_id 
    SET id_transaccion = '".$data->TransactionId."',
     registro_nuevo = 0,
     estado = 1,
     estado_respuesta = '".$data->Extras->IdState."',
    fecha_respuesta=CURRENT_TIMESTAMP WHERE token = '".$_GET["token"]."'";

    sqlsrv_query($link, $consultarActualizar);
  
    $consultaCrearVerificacionADO = "INSERT INTO verificacion_ado (salida,salida2,salida3,salida4) VALUES ('" . ($json) . "','" . json_encode($_REQUEST) . "','" . (base64_encode($_GET["_Response"])) . "','" . json_encode($_POST["_Response"]) . "')";
    $consultaCrearVerificacionADO = "INSERT INTO verificacion_ado (salida, salida2, salida3, salida4) VALUES ('".($json)."','".json_encode($_REQUEST)."','".(base64_encode($_GET["_Response"]))."','".json_encode($_POST["_Response"])."')";

    echo "Tu Proceso de Verificación Ha finalizado...";
?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
    var mensaje = 'Tu Proceso de Verificación Ha finalizado...';
    Swal.fire({
      title: '¡My Bien!',
      text: mensaje,
      icon: 'success',
      showCancelButton: false,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'OK'
    }).then((result) => {
      window.location = 'https://kredit.com.co';
    })
</script>