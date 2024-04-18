<?php
    include ('../../functions.php');
    
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();

    if($_POST['operacion']=="CREAR_AGENDA_COMERCIAL"){

        $opcion = '';
        
        

        $dato = '';
        if ($_POST["observacion"]=="")
        {
            $observacion="SIN NOVEDAD";
        }else{
            $observacion=$_POST['observacion'];
        }
        $consultaCrearCliente="INSERT INTO agenda_comerciales (nombre, apellido,telefono, correo,fecha_creacion,id_usuario_creacion) VALUES ('".$_POST["nombre"]."', '".$_POST["apellido"]."','".$_POST["telefono"]."','".$_POST["correo"]."',CURRENT_TIMESTAMP(),'".$_POST["id_usuario"]."')";

        
        if(mysqli_query($link,$consultaCrearCliente)){
            $id=mysqli_insert_id($link);
            
            $consultaCrearEstadosAgendaComercial="INSERT INTO detalle_estados_agenda_comercial (estado,observacion,fecha_creacion,vigente,id_agenda_comercial) VALUES (1,'".$_POST["observacion"]."',CURRENT_TIMESTAMP(),1,'".$id."')";
            mysqli_query($link,$consultaCrearEstadosAgendaComercial);
            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente',"consulta"=>$consultaCrearEstadosAgendaComercial);
        }else{
            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
        }              
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>