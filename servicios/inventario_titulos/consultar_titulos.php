<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

       $opcion = '';
        

        $dato = '';

        $queryConsulta = "SELECT a.pagaduria,a.id_simulacion,a.nro_libranza,a.cedula,a.nombre,b.nombre as subestado FROM simulaciones a LEFT JOIN subestados b ON a.id_subestado=b.id_subestado WHERE ";
        if ($_POST["filtro"]==1)
        {
            $queryConsulta.="a.nro_libranza='".$_POST["titulo"]."' or a.nombre='".$_POST["titulo"]."' or a.cedula='".$_POST["titulo"]."'";
        }else if ($_POST["filtro"]==2)
        {
            $queryConsulta.="a.id_simulacion in (".$_POST["titulo"].")";
        }
            
        
 
        $conTitulos = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conTitulos) > 0) {
            while ($response = sqlsrv_fetch_array($conTitulos)) {
                $consultarInventarioCredito="SELECT * FROM inventario_creditos a LEFT JOIN tipificacion_inventario_creditos b ON a.estado=b.id_tipificacion_credito WHERE id_simulacion='".$response["id_simulacion"]."' and vigente='s'";
                $queryInventarioCredito=sqlsrv_query($link, $consultarInventarioCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($queryInventarioCredito)>0)
                {
                    $resInventarioCredito=sqlsrv_fetch_assoc($queryInventarioCredito);
                    $estado=$resInventarioCredito["descripcion"];
                    $estado_id=$resInventarioCredito["estado"];
                    $legajo=$resInventarioCredito["legajo"];
                }
                else
                {
                    $estado="NO DEFINIDO";
                    $estado_id=0;
                    $legajo="NO DEFINIDO";
                }
                

              

                $tasas[] = array(
                    "nombre" => $response["nombre"],
                    "identificacion" => $response["cedula"],
                    "id_simulacion" => $response["id_simulacion"],
                    "libranza" =>  $response["nro_libranza"],
                    "pagaduria" => $response["pagaduria"],
                    "subestado" => $response["subestado"],
                    "estado_inventario_credito" => $estado,
                    "id_estado_inventario_credito" => $estado_id,
                    "legajo" => $legajo,
                    "opciones" =>   "<a class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddNovedadTitulo' onclick='openModalNovedadTitulo(".$response["id_simulacion"].")' name='".$response["id_simulacion"]."' >Novedad</a>
                    <a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalObservacionesTitulo' onclick='openModalObservacionesTitulos(".$response["id_simulacion"].")' name='".$response["id_simulacion"]."' >Observaciones</a>"
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar',"query"=>$queryConsulta);
        }
  
    
    echo json_encode($data);
?>