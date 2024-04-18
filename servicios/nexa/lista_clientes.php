<?php
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    $operacion = $_POST["operacion"];
    
        
    if (strlen($_POST["cedula"]) != null || strlen($_POST["cedula"]) > 0) {
        $query_select_cliente = ("SELECT * FROM nexa_clientes WHERE nexa_clientes_cedula like '%".$_POST["cedula"]."%'");    
    }else{
        $query_select_cliente =("SELECT * FROM nexa_clientes WHERE nexa_clientes_cedula like '".$_POST["cedula"]."'");
    }

    $ejecutar_select = sqlsrv_query($link, $query_select_cliente, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($ejecutar_select) {

        if (sqlsrv_num_rows($ejecutar_select) <= 0) {
            $clientes[] = array(
                "nexa_cliente_id"=>"", "nexa_clientes_cedula"=>"", "nexa_clientes_nombre"=>"", "nexa_clientes_primer_apellido"=>"", "nexa_clientes_segundo_apellido"=>"",
                "nexa_clientes_primer_nombre"=>"", "nexa_clientes_segundo_nombre"=>"", "nexa_clientes_fecha_nacimiento"=>"", "nexa_clientes_edad"=>"",
                "nexa_clientes_cargo"=>"", "nexa_clientes_grado"=>"", "nexa_clientes_salario_base"=>"", "nexa_clientes_fecha_nombramiento"=>"",
                "nexa_clientes_nivel_contratacion"=>"", "nexa_clientes_centro_costos"=>"", "nexa_clientes_genero"=>"", "nexa_clientes_telefono"=>"",
                "nexa_clientes_direccion"=>"", "nexa_clientes_email"=>"", "nexa_clientes_cargo_tipo"=>"", "nexa_clientes_opciones"=>""
            );
        }
        while ($response = sqlsrv_fetch_array($ejecutar_select)) {
            $clientes[] = array(
                "nexa_cliente_id" => $response["nexa_cliente_id"],
                "nexa_clientes_cedula" => $response["nexa_clientes_cedula"],
                "nexa_clientes_nombre" => $response["nexa_clientes_primer_apellido"].' '.$response["nexa_clientes_primer_nombre"],
                "nexa_clientes_primer_apellido" => $response["nexa_clientes_primer_apellido"],
                "nexa_clientes_segundo_apellido" => $response["nexa_clientes_segundo_apellido"],
                "nexa_clientes_primer_nombre" => $response["nexa_clientes_primer_nombre"],
                "nexa_clientes_segundo_nombre" => $response["nexa_clientes_segundo_nombre"],
                "nexa_clientes_fecha_nacimiento" => $response["nexa_clientes_fecha_nacimiento"],
                "nexa_clientes_edad" => $response["nexa_clientes_edad"],
                "nexa_clientes_cargo" => $response["nexa_clientes_cargoempresa"],
                "nexa_clientes_grado" => $response["nexa_clientes_grado"],
                "nexa_clientes_salario_base" => $response["nexa_clientes_salario_basico"],
                "nexa_clientes_fecha_nombramiento" => $response["nexa_clientes_fecha_nombramiento"],
                "nexa_clientes_nivel_contratacion" => $response["nexa_clientes_nivel_contratacion"],
                "nexa_clientes_centro_costos" => $response["nexa_clientes_centro_costos"],
                "nexa_clientes_genero" => $response["nexa_clientes_genero"],
                "nexa_clientes_telefono" => $response["nexa_clientes_telefono"],
                "nexa_clientes_direccion" => $response["nexa_clientes_direccion"],
                "nexa_clientes_email" => $response["nexa_clientes_email"],
                "nexa_clientes_cargo_tipo" => $response["nexa_clientes_cargo_tipo"],
                "nexa_clientes_opciones" => "<td class='text-end' style='display: flex; flex-direction:row;'>
                    <a class='btn btn-info btn-icon' aria-label='button' id='ver_detalle' name='".$response["nexa_cliente_id"]."' >
                    <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-receipt-2' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'></path>
                    <path d='M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2'></path>
                    <path d='M14 8h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5m2 0v1.5m0 -9v1.5'></path>
                 </svg>
                </a>
                
            </td>"
            );
        }
        $data = array('estado' => 200, 'mensaje' => 'Resultado satisfactorio', 'cedula'=> $params , 'data' => $clientes);
    }else{
        $data = array('estado' => 503, 'mensaje' => 'Servicio no disponible, comuniquese con el administrador del sistema', 'data' => null);
    }
    
    echo json_encode($data);
?>