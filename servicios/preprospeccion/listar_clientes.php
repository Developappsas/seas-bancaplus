<?php
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    $clientes = array();

    $query_select_cliente =("SELECT p.id_preprospeccion, p.identificacion, p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido, p.telefono, p.email, c.municipio, c.departamento, p.fecha FROM preprospectar p left join ciudades c on c.id = p.ciudad where p.estado = 0");
    
    $ejecutar_select = sqlsrv_query($link, $query_select_cliente);
    if ($ejecutar_select) {       
        while ($response = sqlsrv_fetch_array($ejecutar_select)) {
            $clientes[] = array(
                "id_preprospeccion" => $response["id_preprospeccion"],
                "identificacion" => $response["identificacion"],
                "nombres" => $response["primer_nombre"]." ".$response["segundo_nombre"],
                "apellidos" => $response["primer_apellido"]." ".$response["segundo_apellido"],
                "telefono" => $response["telefono"],
                "email" => $response["email"],
                "ciudad" => $response["departamento"]." - ".$response["municipio"],
                "fecha" => $response["fecha"],
                "opciones" => 
                    "<td class='text-end' style='display: flex; flex-direction:row;'>
                        <div style='text-align: center;'>
                            <a class='btn btn-info btn-icon' aria-label='button' id='ver_detalle' name='".$response["id_preprospeccion"]."' href=$urlPrincipal.'/home/prospeccion_crear.php?token=".$response["id_preprospeccion"]."' >
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-user-plus' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'></path>
                                    <path d='M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0'></path>
                                    <path d='M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2'></path>
                                    <path d='M16 11h6m-3 -3v6'></path>
                                </svg>
                            </a>   
                        </div>                                     
                    </td>"
            );
        }
        $data = array('estado' => 200, 'mensaje' => 'Resultado satisfactorio', 'cedula'=> $params , 'data' => $clientes);
    }else{
        $data = array('estado' => 503, 'mensaje' => 'Servicio no disponible, comuniquese con el administrador del sistema', 'data' => null);
    }

    echo json_encode($data);
?>