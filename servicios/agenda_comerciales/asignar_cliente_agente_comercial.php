<?php
    include ('../../functions.php');
    
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();

    if($_POST['operacion']=="ASIGNAR_AGENDA_COMERCIAL"){

        $opcion = '';
        
        

        $dato = '';
        $actualizarAgendaComercial="UPDATE detalle_estados_agenda_comercial SET vigente=0 WHERE id_agenda_comercial='".$_POST["id_agenda_comercial"]."'";
        mysqli_query($link,$actualizarAgendaComercial);
        $consultaCrearEstadosAgendaComercial="INSERT INTO detalle_estados_agenda_comercial (id_usuario_asignado,estado,observacion,fecha_creacion,vigente,id_agenda_comercial) VALUES ('".$_POST["usuario_asignar"]."','".$_POST["estadoAsignar"]."','".$_POST["observacion"]."',CURRENT_TIMESTAMP(),1,'".$_POST["id_agenda_comercial"]."')";

        
        if(mysqli_query($link,$consultaCrearEstadosAgendaComercial)){
       
            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
        }else{
            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo',"consulta"=>$consultaCrearEstadosAgendaComercial);
        }              
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>