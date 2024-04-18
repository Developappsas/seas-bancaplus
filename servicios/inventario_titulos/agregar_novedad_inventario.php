<?php
    include ('../../functions.php');
    
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();

    if(isset($_POST['id_simulacion'])){

        $opcion = '';
        
        

        $dato = '';
        if ($_POST["observacion"]=="")
        {
            $observacion="SIN NOVEDAD";
        }else{
            $observacion=$_POST['observacion'];
        }

        sqlsrv_query($link, "UPDATE inventario_creditos SET vigente='n' WHERE id_simulacion='".$_POST['id_simulacion']."'");
        if(sqlsrv_query($link, "INSERT INTO inventario_creditos (id_simulacion, observacion,legajo, vigente,fecha,id_usuario,estado) VALUES (".$_POST['id_simulacion'].", '".$observacion."','".$_POST['legajo']."','s',current_timestamp, ".$_SESSION['S_IDUSUARIO'].",".$_POST["tipificacion"].")")){

            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
        }else{
            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo'."INSERT INTO inventario_creditos (id_simulacion, observacion,legajo, vigente,fecha,id_usuario,estado) VALUES (".$_POST['id_simulacion'].", '".$observacion."','".$_POST['legajo']."','s', current_timestamp, ".$_SESSION['S_IDUSUARIO'].",".$_POST["tipificacion"].")");
        }              
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>