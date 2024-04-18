<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once ('../functions.php');
$link = conectar_utf();

if(isset($_POST['token'])){
  $mensaje = "";
  $val=0;
  $token=$_POST["token"];
  if(isset($_POST["estadoToken"])){ $estadoToken = $_POST["estadoToken"]; } else { $estadoToken = 0; }

  $consultarInformacionToken="SELECT * FROM historial_tokens_verificacion_id WHERE token='".$token."' and estado!=1 ";
  $queryInformacionToken=sqlsrv_query($link,$consultarInformacionToken, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

  

  if (sqlsrv_num_rows($queryInformacionToken)> 0) {

    if(!$estadoToken){
      $resInformacionToken=sqlsrv_fetch_array($queryInformacionToken, SQLSRV_FETCH_ASSOC);
      $actualizarToken="UPDATE historial_tokens_verificacion_id SET fecha_visto= CURRENT_TIMESTAMP WHERE id='".$resInformacionToken["id"]."'";
      $url="";
      
      if (sqlsrv_query($link,$actualizarToken)){
        header("HTTP/2.0 200 OK");
        $response = array("code"=>"200", "mensaje"=>"Proceso de Satisfactorio");
      }else{
        header("HTTP/2.0 200 OK");
        $response = array("code"=>"400", "mensaje"=>"Error Al Actualizar token");
      }
    }else{
      header("HTTP/2.0 200 OK");
      $response = array("code"=>"200", "mensaje"=>"Token Activo");
    }
  }else{
    header("HTTP/2.0 200 OK");
    $response = array("code"=>"400", "mensaje"=>"Este Token Ha sido utilizado o fue deshabilitado. Contactese con su asesor.");
  }
  
}else{
  header("HTTP/2.0 200 OK");
  $response = array("code"=>"404", "mensaje"=>"Datos No encontrados", "respuesta"=>0);
}

echo json_encode($response);
?>