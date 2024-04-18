<?php
    include ('../../functions.php');
    
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();

    if(isset($_POST['id_simulacion'])){

        $facturado = $_POST['facturado'];
        
        

        $dato = '';
      

        if(sqlsrv_query($link, "INSERT INTO hst_facturacion_creditos (id_simulacion, facturado, fecha,id_usuario) VALUES (".$_POST['id_simulacion'].", '".$facturado."',current_timestamp, ".$_SESSION['S_IDUSUARIO'].")")){

            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
        }else{
            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
        }              
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>