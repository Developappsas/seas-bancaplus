<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(true);

    require_once("../cors.php");
    require_once("../../functions.php");

    $json_Input = file_get_contents('php://input');
    $parametros = json_decode($json_Input);
    $respuesta = array();
    
    $link = conectar_utf();
    
    $query = "select count(s.id_oficina) as num_creditos, o.nombre, year(s.fecha_radicado) as anio_radicado, month(s.fecha_radicado) as mes_radicado
        from simulaciones s inner join oficinas o on o.id_oficina = s.id_oficina
        group by o.nombre, year(s.fecha_radicado), month(s.fecha_radicado);";

    $ejecutar = sqlsrv_query($link, $query);

    if ($ejecutar) {        
        while ($response = sqlsrv_fetch_array($ejecutar, SQLSRV_FETCH_ASSOC)) {
            $result[] = array(
                "num_creditos"=> $response["num_creditos"],
                "nombre"=> $response["nombre"],
                "anio_radicado"=> $response["anio_radicado"],
                "mes_radicado"=> $response["mes_radicado"],
                "promedio_anio" => $response[""],
                "promedio_mes" => $response[""],
                "estimaod_tiempo_prospecciones" => $response[""],
                "minimo_anio" => $response[""],
                "maximo_anio" => $response[""],

            );
        }
        
        $data = array('code' => 200, 'mensaje' =>'Se ejecuto el servicio de manera satisfactoria.', 'data' => $result);
    }else{
        $data = array('code' => 200, 'mensaje' => 'Ocurrio un error al ejecutar el servicio.', 'data' => $result );    
    }

    echo json_encode($data);
?>