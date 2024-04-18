<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    include ('../../functions.php');
    $link = conectar_utf();

    if(isset($_POST['id_simulacion']) && isset($_POST['id_unidad_negocio']) && isset($_POST['tasa_interes'])){

        if(!empty($_POST["id_unidad_negocio"]) && !empty($_POST["tasa_interes"])){

             $sqlDatosComi="SELECT id_unidad_negocio, sin_seguro, id_subestado, tasa_interes, id_tasa_comision, id_tipo_tasa_comision, comision_pagada, retanqueo1_libranza, retanqueo3_libranza, retanqueo3_libranza FROM simulaciones WHERE id_simulacion = ".$_POST['id_simulacion'];
            $queryDatosComi=sqlsrv_query($link, $sqlDatosComi);
            $respDatosComi = sqlsrv_fetch_array($queryDatosComi);

            //if($respDatosComi["comision_pagada"] == 0){

                $id_unidad_negocio_tasa_comision = $_POST["id_unidad_negocio"];

                if ($id_unidad_negocio_tasa_comision == 4 || $id_unidad_negocio_tasa_comision == 11 || $id_unidad_negocio_tasa_comision == 14 || $id_unidad_negocio_tasa_comision == 19 || $id_unidad_negocio_tasa_comision == 23) {
                    $id_unidad_negocio_tasa_comision = 4; //Fianti
                }else if ($id_unidad_negocio_tasa_comision == 6 || $id_unidad_negocio_tasa_comision == 15 || $id_unidad_negocio_tasa_comision == 21) {
                    $id_unidad_negocio_tasa_comision = 6; //Atraccion
                }else if ($id_unidad_negocio_tasa_comision == 2 || $id_unidad_negocio_tasa_comision == 12 || $id_unidad_negocio_tasa_comision == 16 || $id_unidad_negocio_tasa_comision == 22) {
                    $id_unidad_negocio_tasa_comision = 2; //Salvamento
                }else if ($id_unidad_negocio_tasa_comision == 1 || $id_unidad_negocio_tasa_comision == 10 || $id_unidad_negocio_tasa_comision == 17 || $id_unidad_negocio_tasa_comision == 20) {
                    $id_unidad_negocio_tasa_comision = 1; //Kredit
                }

                $consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$_POST["id_simulacion"]."' AND se_compra='SI'";
                $queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
                    $consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$_POST["id_simulacion"]."' AND se_compra='SI'";
                    $queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
                    $resComprasCC=sqlsrv_fetch_array($queryComprasCC, SQLSRV_FETCH_ASSOC);
                    
                    if ($resComprasCC["cuota"]>0){
                        if ($respDatosComi["retanqueo1_libranza"]=="" && $respDatosComi["retanqueo2_libranza"]=="" && $respDatosComi["retanqueo3_libranza"]==""){
                            $tipo_crediton="COMPRAS DE CARTERA";    
                            $subestadoBloqueoComision = 78;
                        }else{
                            $tipo_crediton="COMPRAS CON RETANQUEO";
                            $subestadoBloqueoComision = 48;
                        }   
                    }else{
                        if ($resComprasCC["valor_pagar"]>0){
                            $tipo_crediton="LIBRE CON SANEAMIENTO";
                            $subestadoBloqueoComision = 78;
                        }else{
                            $subestadoBloqueoComision = '';
                        }                    
                    }           
                }else{
                    $tipo_crediton="LIBRE INVERSION";                    
                    $subestadoBloqueoComision = 46;
                }

                $sqlEstBloqueoComision="SELECT a.id_subestado from simulaciones_subestados a
                 WHERE a.id_simulacion = ".$_POST["id_simulacion"]." AND a.id_subestado IN($subestadoBloqueoComision)";
                $queryEstBloqueoComision=sqlsrv_query($link, $sqlEstBloqueoComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                if (@sqlsrv_num_rows($queryEstBloqueoComision) == 0){//No ha Pasado por estados de bloqueo de acuerdo al tipo de credito
                
                    $sqlTasaComision = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente, 0 AS actual FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = ". $id_unidad_negocio_tasa_comision ." AND a.tasa = ".$_POST["tasa_interes"]." AND  (FORMAT(GETDATE(), 'Y-m-d') >= format(a.fecha_inicio,'Y-m-d' )
                    AND FORMAT(GETDATE(), 'Y-m-d') <= format(a.fecha_fin, 'Y-m-d')) OR a.vigente = 1";
                }else{
                    $sqlTasaComision = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente, 0 AS actual FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_tasa_comision = ". $respDatosComi["id_tasa_comision"]; 
                }

                $conTasasComisiones = sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)); 
                        
                $tasas = array();            
                if (sqlsrv_num_rows($conTasasComisiones) > 0) {
                    while ($response = sqlsrv_fetch_array($conTasasComisiones)) {

                        $tasas[] = array(
                            "id_tasa_comision" => $response["id_tasa_comision"],
                            "id_unidad_negocio" => $response["id_unidad_negocio"],
                            "unidad_negocio" => $response["unidad_negocio"],
                            "marca_unidad_negocio" => $response["marca_unidad_negocio"],
                            "tasa_interes" => $response["tasa_interes"],
                            "kp_plus" => $response["kp_plus"],
                            "id_tipo" => $response["id_tipo"],
                            "fecha_inicio" => $response["fecha_inicio"],
                            "fecha_fin" => $response["fecha_fin"],
                            "vigente" => $response["vigente"],
                            "actual" =>  $response["actual"]
                        );
                    }

                    $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas);                
                }else{
                    $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para mostrar');
                }
            /*}else{
                $data = array('code' => 201, 'mensaje' => 'Comision Pagada');
            }*/
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No llegaron Datos');
    }
    
    echo json_encode($data);
?>