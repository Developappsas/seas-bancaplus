<?php
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
    
    include_once ('../functions.php');
    include_once ('../function_blob_storage.php');
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();
    $id_usuario=$_POST["id_usuario"];
    $estado=$_POST["estado"];
 
    consultar_creditos($id_usuario);


    function consultar_creditos($id_usuario) 
    {
        global $link;  
        
        $val1=0;
        $response = array();
        $data = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $mensaje="";


        if ($id_usuario == null)
        {
            $val1=1;
            $mensaje.="Debe ingresar identificacion de cliente. ";
        }else{
            $val1=0;
        }




        
        if ($val1 == 1)
        {
            header("HTTP/2.0 200 OK");
            $response =array("data"=>"","code"=>"403", "message"=>"Conexion no valida con el servicio. Err: ".$mensaje);
        }else{
            $consultaCreditos="SELECT FORMAT(a.fecha_radicado,'Y-m-d') as fecha_radicad,a.fecha_desembolso,a.fecha_estudio,a.pagaduria,CONCAT(c.nombre,' ',c.apellido) as nombre_comercial,d.nombre as nombre_oficina, CASE WHEN a.id_unidad_negocio=4 then 'ALIADOS: FIANTI' else 'ALIADOS: KREDIT' end as nombre_unidad_negocio, a.*,b.nombre as unidad_negocio FROM simulaciones a LEFT JOIN unidades_negocio b ON a.id_unidad_negocio=b.id_unidad LEFT JOIN usuarios c ON c.id_usuario=a.id_comercial LEFT JOIN oficinas d ON d.id_oficina=a.id_oficina WHERE a.cedula='".$id_usuario."' ";

            $consultaCreditosDesembolsados=$consultaCreditos."AND a.estado IN ('DES') ORDER BY id_unidad_negocio desc";
     
            $consultarCreditoClientes=sqlsrv_query($link,$consultaCreditosDesembolsados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($consultarCreditoClientes)>0)
            {
                while ($resCreditosClientes=sqlsrv_fetch_array($consultarCreditoClientes, SQLSRV_FETCH_ASSOC))
                {
                    $fechaEtapaDesembolso=$resCreditosClientes["fecha_desembolso"];


                
                    $fechaEtapaRadicado=$resCreditosClientes["fecha_radicad"];

                  
                    $fechaEtapaEstudiado=$resCreditosClientes["fecha_estudio"];

                    $consultarComprasCartera="SELECT count(id_simulacion) as cantidad_compras_cartera,sum(valor_pagar) as valor_compras_cartera FROM simulaciones_comprascartera WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."' and se_compra='SI'";
                    $queryComprasCartera=sqlsrv_query($link,$consultarComprasCartera, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    //$cantidadComprasCartera=sqlsrv_num_rows($queryComprasCartera);
                    $resComprasCartera=sqlsrv_fetch_array($queryComprasCartera, SQLSRV_FETCH_ASSOC);

                    $consultaCuotas="SELECT * FROM cuotas WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."'";
                    $queryCuotas=sqlsrv_query($link,$consultaCuota, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)s);
                    $cantidadCuotas=sqlsrv_num_rows($queryCuotas);

                    $consultaCuotasPagadas=$consultaCuotas." AND pagada=1";
                    $queryCuotasPagadas=sqlsrv_query($link,$consultaCuotasPagadas);
                    $cantidadCuotasPagadas=sqlsrv_num_rows($queryCuotasPagadas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    $desembolso_cliente = $resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"] - $resComprasCartera["valor_compras_cartera"] - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento1"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento2"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento3"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento4"] / 100.00) - $resCreditosClientes["descuento_transferencia"];

                    if ($resCreditosClientes["tipo_producto"] == "1")
                    {
                        if ($resCreditosClientes["fidelizacion"])
                        {
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento6"] / 100.00;
                        }else{
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento6"] / 100.00;
                        }
                    }


                    array_push($data, array("id_simulacion"=>$resCreditosClientes["id_simulacion"],
                    "nro_libranza"=>$resCreditosClientes["nro_libranza"],
                    "unidad_negocio"=>$resCreditosClientes["nombre_unidad_negocio"],
                    "fecha_desembolso"=>$resCreditosClientes["fecha_desembolso"],
                    "valor_credito"=>$resCreditosClientes["valor_credito"],
                    "desembolso_cliente"=>$desembolso_cliente,
                    "cantidad_compras_cartera"=>$resComprasCartera["cantidad_compras_cartera"],
                    "nombre_comercial"=>$resCreditosClientes["nombre_comercial"],
                    "nombre_oficina"=>$resCreditosClientes["nombre_oficina"],
                    "pagaduria"=>$resCreditosClientes["pagaduria"],
                    "cuotas"=>$cantidadCuotasPagadas." de ".$cantidadCuotas,
                    "fecha_estudio"=>$fechaEtapaEstudiado,
                    "fecha_radicado"=>$fechaEtapaRadicado,
                    "fecha_desembolso"=>$fechaEtapaDesembolso
                
                    ));
                }

                
            }else{
                $data=null;
            }




            $consultaCreditosActivos=$consultaCreditos."AND a.estado IN ('ING','EST','VIA') ORDER BY id_unidad_negocio desc";
     
            $consultarCreditoClientes=sqlsrv_query($link,$consultaCreditosActivos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($consultarCreditoClientes)>0)
            {
                while ($resCreditosClientes=sqlsrv_fetch_array($consultarCreditoClientes, SQLSRV_FETCH_ASSOC))
                {
                    $consultarComprasCartera="SELECT count(id_simulacion) as cantidad_compras_cartera,sum(valor_pagar) as valor_compras_cartera FROM simulaciones_comprascartera WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."' and se_compra='SI'";
                    $queryComprasCartera=sqlsrv_query($link,$consultarComprasCartera);
                    //$cantidadComprasCartera=sqlsrv_num_rows($queryComprasCartera);
                    $resComprasCartera=sqlsrv_fetch_array($queryComprasCartera, SQLSRV_FETCH_ASSOC);
                    
                    $consultaCuotas="SELECT * FROM cuotas WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."'";
                    $queryCuotas=sqlsrv_query($link,$consultaCuotas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotas=sqlsrv_num_rows($queryCuotas);

                    $consultaCuotasPagadas=$consultaCuotas." AND pagada=1";
                    $queryCuotasPagadas=sqlsrv_query($link,$consultaCuotasPagadas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotasPagadas=sqlsrv_num_rows($queryCuotasPagadas);

                    $desembolso_cliente = $resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"] - $resComprasCartera["valor_compras_cartera"]- (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento1"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento2"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento3"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento4"] / 100.00) - $resCreditosClientes["descuento_transferencia"];

                    if ($resCreditosClientes["tipo_producto"] == "1")
                    {
                        if ($resCreditosClientes["fidelizacion"])
                        {
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento6"] / 100.00;
                        }else{
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento6"] / 100.00;
                        }
                    }


                    array_push($data1, array("id_simulacion"=>$resCreditosClientes["id_simulacion"],
                    "nro_libranza"=>$resCreditosClientes["nro_libranza"],
                    "unidad_negocio"=>$resCreditosClientes["nombre_unidad_negocio"],
                    "fecha_desembolso"=>$resCreditosClientes["fecha_desembolso"],
                    "valor_credito"=>$resCreditosClientes["valor_credito"],
                    "desembolso_cliente"=>$desembolso_cliente,
                    "nombre_comercial"=>$resCreditosClientes["nombre_comercial"],
                    "nombre_oficina"=>$resCreditosClientes["nombre_oficina"],
                    "pagaduria"=>$resCreditosClientes["pagaduria"],
                    "cantidad_compras_cartera"=>$resComprasCartera["cantidad_compras_cartera"],
                    "cuotas"=>$cantidadCuotasPagadas." de ".$cantidadCuotas));
                }

                
            }else{
                $data1= null;
            }


            $consultaCreditosAnulados="SELECT a.pagaduria,CONCAT(d.nombre,' ',d.apellido) as nombre_comercial,e.nombre as nombre_oficina,case when c.nombre is null then 'ANULADO' ELSE c.nombre END as causal_anulacion,CASE WHEN a.id_unidad_negocio=4 then 'ALIADOS: FIANTI' else 'ALIADOS: KREDIT' end as nombre_unidad_negocio, a.*,b.nombre as unidad_negocio FROM simulaciones a LEFT JOIN unidades_negocio b ON a.id_unidad_negocio=b.id_unidad LEFT JOIN causales c ON c.id_causal=a.id_causal LEFT JOIN usuarios d ON d.id_usuario=a.id_comercial LEFT JOIN oficinas e ON e.id_oficina=a.id_oficina WHERE a.cedula='".$id_usuario."' AND a.estado IN ('ANU','DST','NEG') ORDER BY id_unidad_negocio desc";
     
            $consultarCreditoClientes=sqlsrv_query($link,$consultaCreditosAnulados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($consultarCreditoClientes)>0)
            {
                while ($resCreditosClientes=sqlsrv_fetch_array($consultarCreditoClientes, SQLSRV_FETCH_ASSOC))
                {

                    $consultarComprasCartera="SELECT count(id_simulacion) as cantidad_compras_cartera,sum(valor_pagar) as valor_compras_cartera FROM simulaciones_comprascartera WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."' and se_compra='SI'";
                    $queryComprasCartera=sqlsrv_query($link,$consultarComprasCartera);
                    //$cantidadComprasCartera=sqlsrv_num_rows($queryComprasCartera);
                    $resComprasCartera=sqlsrv_fetch_array($queryComprasCartera, SQLSRV_FETCH_ASSOC);


                    $consultaCuotas="SELECT * FROM cuotas WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."'";
                    $queryCuotas=sqlsrv_query($link,$consultaCuotas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotas=sqlsrv_num_rows($queryCuotas);

                    $consultaCuotasPagadas=$consultaCuotas." AND pagada=1";
                    $queryCuotasPagadas=sqlsrv_query($link,$consultaCuotasPagadas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotasPagadas=sqlsrv_num_rows($queryCuotasPagadas);

                    $desembolso_cliente = $resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"] - $resComprasCartera["valor_compras_cartera"] - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento1"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento2"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento3"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento4"] / 100.00) - $resCreditosClientes["descuento_transferencia"];

                    if ($resCreditosClientes["tipo_producto"] == "1")
                    {
                        if ($resCreditosClientes["fidelizacion"])
                        {
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento6"] / 100.00;
                        }else{
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento6"] / 100.00;
                        }
                    }


                    array_push($data2, array("id_simulacion"=>$resCreditosClientes["id_simulacion"],
                    "nro_libranza"=>$resCreditosClientes["nro_libranza"],
                    "unidad_negocio"=>$resCreditosClientes["nombre_unidad_negocio"],
                    "fecha_desembolso"=>$resCreditosClientes["fecha_desembolso"],
                    "valor_credito"=>$resCreditosClientes["valor_credito"],
                    "desembolso_cliente"=>$desembolso_cliente,
                    "causal_anulacion"=>$resCreditosClientes["causal_anulacion"],
                    "nombre_comercial"=>$resCreditosClientes["nombre_comercial"],
                    "nombre_oficina"=>$resCreditosClientes["nombre_oficina"],
                    "pagaduria"=>$resCreditosClientes["pagaduria"],
                    "cantidad_compras_cartera"=>$resComprasCartera["cantidad_compras_cartera"],
                    "cuotas"=>$cantidadCuotasPagadas." de ".$cantidadCuotas));
                }

                
            }else{
                $data2= null;
            }


            $consultaCreditosCancelado=$consultaCreditos."AND a.estado IN ('CAN') ORDER BY id_unidad_negocio desc";
     
            $consultarCreditoClientes=sqlsrv_query($link,$consultaCreditosCancelado, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($consultarCreditoClientes)>0)
            {
                while ($resCreditosClientes=sqlsrv_fetch_array($consultarCreditoClientes, SQLSRV_FETCH_ASSOC))
                {
                    $consultarComprasCartera="SELECT count(id_simulacion) as cantidad_compras_cartera,sum(valor_pagar) as valor_compras_cartera FROM simulaciones_comprascartera WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."' and se_compra='SI'";
                    $queryComprasCartera=sqlsrv_query($link,$consultarComprasCartera);
                    //$cantidadComprasCartera=sqlsrv_num_rows($queryComprasCartera);
                    $resComprasCartera=sqlsrv_fetch_array($queryComprasCartera, SQLSRV_FETCH_ASSOC);

                    $consultaCuotas="SELECT * FROM cuotas WHERE id_simulacion='".$resCreditosClientes["id_simulacion"]."'";
                    $queryCuotas=sqlsrv_query($link,$consultaCuotas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotas=sqlsrv_num_rows($queryCuotas);

                    $consultaCuotasPagadas=$consultaCuotas." AND pagada=1";
                    $queryCuotasPagadas=sqlsrv_query($link,$consultaCuotasPagadas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $cantidadCuotasPagadas=sqlsrv_num_rows($queryCuotasPagadas);

                    $desembolso_cliente = $resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"] - $resComprasCartera["valor_compras_cartera"] - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento1"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento2"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento3"] / 100.00) - (($resCreditosClientes["valor_credito"] - $resCreditosClientes["retanqueo_total"]) * $resCreditosClientes["descuento4"] / 100.00) - $resCreditosClientes["descuento_transferencia"];

                    if ($resCreditosClientes["tipo_producto"] == "1")
                    {
                        if ($resCreditosClientes["fidelizacion"])
                        {
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["retanqueo_total"] * $resCreditosClientes["descuento6"] / 100.00;
                        }else{
                            $desembolso_cliente = $desembolso_cliente - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento5"] / 100.00 - $resCreditosClientes["valor_credito"] * $resCreditosClientes["descuento6"] / 100.00;
                        }
                    }


                    array_push($data3, array("id_simulacion"=>$resCreditosClientes["id_simulacion"],
                    "nro_libranza"=>$resCreditosClientes["nro_libranza"],
                    "unidad_negocio"=>$resCreditosClientes["nombre_unidad_negocio"],
                    "fecha_desembolso"=>$resCreditosClientes["fecha_desembolso"],
                    "valor_credito"=>$resCreditosClientes["valor_credito"],
                    "desembolso_cliente"=>$desembolso_cliente,
                    "cantidad_compras_cartera"=>$resComprasCartera["cantidad_compras_cartera"],
                    "nombre_comercial"=>$resCreditosClientes["nombre_comercial"],
                    "nombre_oficina"=>$resCreditosClientes["nombre_oficina"],
                    "pagaduria"=>$resCreditosClientes["pagaduria"],
                    "cuotas"=>$cantidadCuotasPagadas." de ".$cantidadCuotas));
                }

                
            }else{
                $data3=null;
            }

            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"200","mensaje"=>"Consulta Ejecutada Satisfactoriamente","desembolsados"=>$data,"activos"=>$data1,"anulados"=>$data2,"cancelados"=>$data3);      
            
        }


        echo json_encode($response);

    }

?>