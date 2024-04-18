<?php 
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    $query_select_cliente = ("SELECT * FROM nexa_clientes WHERE nexa_cliente_id = '".$params->id."'");

    $ejecutar_select = sqlsrv_query($link, $query_select_cliente);
    if ($ejecutar_select) {
        while ($response = sqlsrv_fetch_array($ejecutar_select)) {
            $clientes = array(
                "nexa_cliente_id" => $response["nexa_cliente_id"],
                "nexa_clientes_tipo_identificacion" => $response["nexa_clientes_tipo_identificacion"],
                "nexa_clientes_cedula" => $response["nexa_clientes_cedula"],
                "nexa_clientes_nombre_completo" => $response["nexa_clientes_primer_nombre"].' '.$response["nexa_clientes_segundo_nombre"].' '.$response["nexa_clientes_primer_apellido"].' '.$response["nexa_clientes_segundo_apellido"],
                "nexa_clientes_primer_apellido" => $response["nexa_clientes_primer_apellido"],
                "nexa_clientes_segundo_apellido" => $response["nexa_clientes_segundo_apellido"],
                "nexa_clientes_primer_nombre" => $response["nexa_clientes_primer_nombre"],
                "nexa_clientes_segundo_nombre" => $response["nexa_clientes_segundo_nombre"],
                "nexa_clientes_fecha_expedicion" => $response["nexa_clientes_fecha_expedicion"],
                "nexa_clientes_lugar_expedicion" => $response["nexa_clientes_lugar_expedicion"],
                "nexa_clientes_genero" => $response["nexa_clientes_genero"],
                "nexa_clientes_edad" => $response["nexa_clientes_edad"],
                "nexa_clientes_lugar_nacimiento" => $response["nexa_clientes_lugar_nacimiento"],
                "nexa_clientes_fecha_nacimiento" => $response["nexa_clientes_fecha_nacimiento"],
                "nexa_clientes_estado_civil" => $response["nexa_clientes_estado_civil"],
                "nexa_clientes_pais_residencia" => $response["nexa_clientes_pais_residencia"],
                "nexa_clientes_departamento" => $response["nexa_clientes_departamento"],
                "nexa_clientes_ciudad" => $response["nexa_clientes_ciudad"],                
                "nexa_clientes_direccion" => $response["nexa_clientes_direccion"],
                "nexa_clientes_telefono" => $response["nexa_clientes_telefono"],
                "nexa_clientes_celular" => $response["nexa_clientes_celular"],
                "nexa_clientes_email" => $response["nexa_clientes_email"],
                "nexa_clientes_grado" => $response["nexa_clientes_grado"],
                "nexa_clientes_cargo_tipo" => $response["nexa_clientes_cargo_tipo"],
                "nexa_clientes_cargoempresa" => $response["nexa_clientes_cargoempresa"],
                "nexa_clientes_nivel_contratacion" => $response["nexa_clientes_nivel_contratacion"],
                "nexa_clientes_fecha_nombramiento" => $response["nexa_clientes_fecha_nombramiento"],
                "nexa_clientes_salario_basico" => $response["nexa_clientes_salario_basico"],
                "nexa_clientes_centro_costos" => $response["nexa_clientes_centro_costos"],                
                "nexa_clientes_pagaduria" => $response["nexa_clientes_pagaduria"],
            );
        }

        $query_lista_carteras = ("SELECT * FROM nexa_carteras where nexa_carteras_cedula = (SELECT nexa_clientes_cedula FROM nexa_clientes WHERE nexa_cliente_id = '".$params->id."')");
        $ejecutar_lista_carteras = sqlsrv_query($link, $query_lista_carteras);
        $carteras = array();
        while($row = sqlsrv_fetch_array($ejecutar_lista_carteras)) {
            foreach($row as $key => $value) {
                if (trim($value) != null || trim($value) != '') {                    
                    $carteras[$row['name.""']][$key] = $value;
                }
            }
        }
        $data = array('estado' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $clientes, "carteras" => $carteras, "query" =>$query_lista_carteras);
    }else{
        $data = array('estado' => 503, 'mensaje' => 'Servicio no disponible, comuniquese con el administrador del sistema', 'data' => null);
    }
    
    echo json_encode($data);
?>