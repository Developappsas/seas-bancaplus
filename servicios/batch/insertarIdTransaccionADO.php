<?php 
  include_once ('../../functions.php');
  $link = conectar_utf();

  $consulta = sqlsrv_query($link, "SELECT respuesta, id FROM historial_tokens_verificacion_id where respuesta is not null and id_transaccion is null");

  while($fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){

    $data = json_decode($fila["respuesta"]);

    $consultarActualizar="UPDATE historial_tokens_verificacion_id SET id_transaccion = '".$data->TransactionId."' WHERE id = ".$fila['id'];
    echo "procesdada la transaccion ".$data->TransactionId;
    sqlsrv_query($link,$consultarActualizar);
  }

  echo "Tu Proceso de Verificación Ha finalizado...";
?>