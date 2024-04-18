<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(true);
 
    require_once("../cors.php");
    require_once("../../functions.php");
 
    $json_Input = file_get_contents('php://input');
    $parametros = json_decode($json_Input);
    $resultado = array();
    $proceso = $parametros->proceso;
 
    $link = conectar_utf();

    $queryDB = "SELECT s.cedula, s.id_simulacion, s.nombre, so.fecha_nacimiento,
    DATEDIFF(YEAR, so.fecha_nacimiento, GETDATE()) AS edad, ofi.nombre as oficina, us.nombre as nombre_comercial, us.apellido as apellido_comercial,so.celular,  s.ciudad,  s.pagaduria, un.nombre as unidad_negocio , s.valor_credito, s.estado,  se.nombre AS subestado, s.fecha_radicado , s.fecha_creacion, s.fecha_desembolso, DATEDIFF(day,s.fecha_desembolso, s.fecha_creacion) AS tiempo_desembolso, pc.proposito 
    from simulaciones s 
       LEFT JOIN propositos_credito pc ON s.proposito_credito = pc.id_proposito 
       INNER JOIN unidades_negocio un ON s.id_unidad_negocio = un.id_unidad
       INNER JOIN oficinas ofi ON s.id_oficina = ofi.id_oficina
       INNER JOIN usuarios us ON s.id_comercial = us.id_usuario
       INNER JOIN pagadurias pa ON s.pagaduria = pa.nombre
       LEFT join solicitud so on s.id_simulacion = so.id_simulacion 
       LEFT JOIN subestados se ON s.id_subestado = se.id_subestado 
    WHERE s.estado != 'DST' and  s.estado != 'NEG'";

       if(!empty($_POST['cedula'])){
            $queryDB  .= " AND s.cedula =  '".$_POST['cedula']."'  ";
       }

       if($_POST['pagaduria']!=="0" && $_POST['pagaduria']!== null){
            $queryDB  .= " AND s.pagaduria ='".trim($_POST['pagaduria'])."'";
       }

       if($_POST['ciudad']!== "0" && $_POST['ciudad']!== null ){
            $queryDB  .= " AND s.ciudad ='".trim($_POST['ciudad'])."'";

       }

       if($_POST['estado']!== "0" && $_POST['estado']!== null ){       
            $queryDB  .= " AND s.estado ='".trim($_POST['estado'])."'";

       } 

    $queryDB .= "  order by s.id_simulacion desc";
   
    $respuestaQueryDB = sqlsrv_query($link, $queryDB);

    while($fila = sqlsrv_fetch_array($respuestaQueryDB)){

        // Sub estado pre aprobado
        $subQuery1 = "SELECT top 1 fecha_creacion FROM simulaciones_subestados WHERE id_simulacion = '".$fila['id_simulacion']."' and (id_subestado = 1 OR id_subestado = 69 OR id_subestado = 71) ORDER BY fecha_creacion asc";
        $preaprobado = sqlsrv_query($link, $subQuery1);
        $fechaPreaprobado = sqlsrv_fetch_array($preaprobado);

        // 

        // radicado fabrica
        $subQuery2 = "SELECT top 1 fecha_creacion FROM simulaciones_subestados WHERE id_simulacion = '".$fila['id_simulacion']."' and (id_subestado = 70) ORDER BY fecha_creacion asc";
        $radicado = sqlsrv_query($link, $subQuery2);
        $fechaFabrica = sqlsrv_fetch_array($radicado);


        // 

        // 4 Aprobado
        $subQuery3 = "SELECT top 1  fecha_creacion FROM simulaciones_subestados WHERE id_simulacion = '".$fila['id_simulacion']."' and (id_subestado = 19) ORDER BY fecha_creacion asc";
        $aprobado = sqlsrv_query($link, $subQuery3);
        $fechaaprobado = sqlsrv_fetch_array($aprobado);
        
        // 
        $diasRadicado ="";
        $diasAprobado ="";

        if(!empty($fechaPreaprobado['fecha_creacion'])  &&  !empty($fechaFabrica['fecha_creacion']) ){
            
            $diff = date_diff(new DateTime($fechaPreaprobado['fecha_creacion']), new DateTime($fechaFabrica['fecha_creacion']));
            $diasRadicado = $diff->days;
            
        }

        if($fechaFabrica['fecha_creacion'] && $fechaAprobado['fecha_creacion']){
            $diff2 = date_diff(new DateTime($fechaFabrica['fecha_creacion']), new DateTime($fechaAprobado['fecha_creacion']));
            $diasAprobado = $diff2->days;
        }

        $resultado[] = array(
            'Cedula'=> $fila['cedula'],
            'ID simulacion'=> $fila['id_simulacion'],    
            'Nombre' => $fila['nombre'],
            'fecha de nacimiento'=>$fila['fecha_nacimiento'],
            'Edad'=>$fila['edad'],
            'Telefono' => $fila['celular'],
            'Ciudad' => $fila['ciudad'],
            'Oficina'=> $fila['oficina'],
            'Comercial'=> $fila['nombre_comercial']. " ". $fila['apellido_comercial'],
            'Pagaduria' => $fila['pagaduria'],
            'Unidad de negocio' => $fila['unidad_negocio'],
            'monto del Credito' => $fila['valor_credito'],
            'estado' => $fila['estado'],
            'subestado' => $fila['subestado'],
            // 'fechaRadicado' => $fila['fecha_radicado'],
            'fecha de creacion' => $fila['fecha_creacion'],
            'fecha de desembolso' => $fila['fecha_desembolso'],
            'dias de desembolso' => $fila['tiempo_desembolso'],
            'fecha de preaprobado' => $fechaPreaprobado['fecha_creacion'],
            'fecha de radicado' => $fechaFabrica ['fecha_creacion'],
            'dias de Radicado' => $diasRadicado,
            'fecha de Aprobado' => $fechaAprobado['fecha_creacion'],
            'dias de Aprobado' => $diasAprobado,
            'proposito' => $fila['proposito']
            
        );
    }
    
    echo json_encode($resultado);

?>