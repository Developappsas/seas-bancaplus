<?php
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    //echo json_encode($params);

    $operacion = $params->operacion;
        
    switch ($operacion) {
        case 'nuevo_cliente':
            $query_select = ("SELECT * FROM nexa_clientes WHERE nexa_clientes_cedula = '".$params->nexa_clientes_cedula."'");
            $ejecutar_select = mysqli_query($link, $query_select);
            if ($ejecutar_select) {               
                if ( mysqli_num_rows($ejecutar_select) <= 0 ) {
                    $query_insert = ("INSERT INTO nexa_clientes (nexa_clientes_cedula, nexa_clientes_primer_apellido, nexa_clientes_segundo_apellido, nexa_clientes_primer_nombre, nexa_clientes_segundo_nombre, nexa_clientes_fecha_ingreso, nexa_clientes_fecha_nacimiento, nexa_clientes_edad, nexa_clientes_cargoempresa, nexa_clientes_grado, nexa_clientes_salario_basico, nexa_clientes_fecha_nombramiento, nexa_clientes_nivel_contratacion, nexa_clientes_centro_costos, nexa_clientes_genero, nexa_clientes_telefono, nexa_clientes_celular, nexa_clientes_direccion, nexa_clientes_email, nexa_clientes_cargo_tipo, nexa_clientes_ciudad, nexa_clientes_pagaduria)
                    VALUES ('".$params->nexa_clientes_cedula."', '".
                    $params->nexa_clientes_primer_apellido."', '".
                    $params->nexa_clientes_segundo_apellido."', '".
                    $params->nexa_clientes_primer_nombre."', '".
                    $params->nexa_clientes_segundo_nombre."', '".
                    $params->nexa_clientes_fecha_ingreso."', '".
                    $params->nexa_clientes_fecha_nacimiento."', '".
                    $params->nexa_clientes_edad."', '".
                    $params->nexa_clientes_cargoempresa."', '".
                    $params->nexa_clientes_grado."', '".
                    $params->nexa_clientes_salario_basico."', '".
                    $params->nexa_clientes_fecha_nombramiento."', '".
                    $params->nexa_clientes_nivel_contratacion."', '".
                    $params->nexa_clientes_centro_costos."', '".
                    $params->nexa_clientes_genero."', '".
                    $params->nexa_clientes_telefono."', '".
                    $params->nexa_clientes_celular."', '".
                    $params->nexa_clientes_direccion."', '".
                    $params->nexa_clientes_email."', '".
                    $params->nexa_clientes_cargo_tipo."', '".
                    $params->nexa_clientes_ciudad."', '".
                    $params->nexa_clientes_pagaduria."')");
                    
                    $ejecutar_insert = mysqli_query($link, $query_insert);
                    if ($ejecutar_insert) {
                        $data = array('estado' => 200, 'mensaje' => 'Resultado satisfactorio');                        
                    }else{
                        $data = array('estado' => 503, 'mensaje' => 'Error al ejecutar el servicio, notifique al administrador del sistema.', 'data' => $query_select);
                    }                    
                }else{
                    $data = array('estado' => 409, 'mensaje' => 'La cedula numero '.$params->nexa_clientes_cedula.' ya se encuentra registrada.');
                }
            }else{
                $data = array('estado' => 503, 'mensaje' => 'Error al ejecutar el servicio, notifique al administrador del sistema.', 'data' => $query_select);
            }

            echo json_encode($data);
        break;

        case 'Nueva Cartera':
            if ($params->llave != 'CEDULA') {
                $query_Valida_campo = ("SHOW COLUMNS FROM nexa_carteras WHERE field = '".$params->llave."'");
                $ejecutar_validacion_campo = mysqli_query($link, $query_Valida_campo);
                if (mysqli_num_rows($ejecutar_validacion_campo) <= 0 ) {
                    $query_agregar_campo = ("ALTER TABLE nexa_carteras ADD ".$params->llave." VARCHAR(255)");
                    $query_ejecutar_agregar_campo = mysqli_query($link, $query_agregar_campo);                
                }
            }
                        
            $query_validar_existencia_cedula = ("SELECT * FROM nexa_carteras WHERE nexa_carteras_cedula = '".$params->cedula."'");
            $ejecutar_validar_existencia_cedula = mysqli_query($link, $query_validar_existencia_cedula);
            if ($ejecutar_validar_existencia_cedula) {
                if(mysqli_num_rows($ejecutar_validar_existencia_cedula) <= 0 ) {
                    $query_insert_datos = ("INSERT INTO nexa_carteras (nexa_carteras_cedula) VALUE ('".$params->cedula."')");
                    mysqli_query($link, $query_insert_datos);
                    $data = array(
                        'estado' => 200, 'mensaje' =>  'Ok', 'cedula' => $params->cedula, 'llave' => $params->llave, 
                        'valor' => $params->valor, 'query_agregar_campo' => $query_agregar_campo, 
                        'query_valida_campo' => $query_Valida_campo, 'query_insert_datos' => $query_insert_datos);
                }else{
                    $query_update_datos = ("UPDATE nexa_carteras SET ".$params->llave." = '".$params->valor."' WHERE nexa_carteras_cedula = '".$params->cedula."'");
                    mysqli_query($link, $query_update_datos);
                    $data = array(
                        'estado' => 200, 'mensaje' =>  'Ok', 'cedula' => $params->cedula, 'llave' => $params->llave, 
                        'valor' => $params->valor, 'query_agregar_campo' => $query_agregar_campo, 
                        'query_valida_campo' => $query_Valida_campo, 'query_insert_datos' => $query_insert_datos,
                        'query_update_datos' => $query_update_datos
                    );
                }
            }else{
                $data = array(
                    'estado' => 200, 'mensaje' =>  'Ok', 'cedula' => $params->cedula, 
                    'llave' => $params->llave, 'valor' => $params->valor, 'error' => mysqli_error($link),
                    "query" => $query_validar_existencia_cedula
                );
            }
            
            // $data = array(
            //     'estado' => 200, 
            //     'mensaje' =>  'Ok', 
            //     'cedula' => $params->cedula, 
            //     'llave' => $params->llave, 
            //     'valor' => $params->valor, 
            //     "query_agregar_campo" => $query_agregar_campo, 
            //     "query_valida_campo" => $query_Valida_campo,
            //     "query_insert_datos" => $query_insert_datos,
            //     "query_update_datos" => $query_update_datos
            // );

            echo json_encode($data);
        break;
        
        default:
            $data = array('estado' => 404, 'mensaje' => 'Operacion no controlada, notifique este error al Administrador.', 'data' => $query_insert);
            echo json_encode($data);
        break;
    }

?>