<?php
include ('../../functions.php');
include ('../cors.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);
//var_dump($_REQUEST);
if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Aplicar Recaudos Detalle':
            $consultarAplicacionRecaudoDetalle="SELECT * FROM recaudosplanos_detalle WHERE id_recaudoplanodetalle = '" . $params["id_recaudoplanodetalle"] . "'";
            $queryAplicacionRecaudoDetalle=sqlsrv_query($link,$consultarAplicacionRecaudoDetalle, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryAplicacionRecaudoDetalle)>0)
            {
                $resAplicacionRecaudoDetalle=sqlsrv_fetch_array($queryAplicacionRecaudoDetalle, SQLSRV_FETCH_ASSOC);

                if (strpos($resAplicacionRecaudoDetalle["observacion"], "bolsa de incorp") !== false) {
                    $queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from bolsainc_pagos where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'";
    
                    $rs1 = sqlsrv_query($link, $queryDB);
    
                    $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
    
                    $consecutivo = $fila1["max_c"];
    
                    sqlsrv_query($link, "insert into bolsainc_pagos (id_simulacion, consecutivo, fecha, valor, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $resAplicacionRecaudoDetalle["id_simulacion"] . "', '" . $consecutivo . "', '" . $resAplicacionRecaudoDetalle["fecha"] . "', '" . $resAplicacionRecaudoDetalle["valor"] . "', 'NOMINA', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");
    
                    sqlsrv_query($link, "update simulaciones set saldo_bolsa = saldo_bolsa + " . $resAplicacionRecaudoDetalle["valor"] . " where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'");
                } else {
                    $queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from pagos" . $sufijo . " where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'";
    
                    $rs1 = sqlsrv_query($link, $queryDB);
    
                    $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
    
                    $consecutivo = $fila1["max_c"];
    
                    sqlsrv_query($link, "insert into pagos" . $sufijo . " (id_simulacion, consecutivo, fecha, valor, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $resAplicacionRecaudoDetalle["id_simulacion"] . "', '" . $consecutivo . "', '" . $resAplicacionRecaudoDetalle["fecha"] . "', '" . $resAplicacionRecaudoDetalle["valor"] . "', 'NOMINA', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");
    
                    $valor_por_aplicar = $resAplicacionRecaudoDetalle["valor"];
    
                    $queryDB = "select cu.*, si.plazo, DATEDIFF(day, si.fecha_primera_cuota, EOMONTH('" . $resAplicacionRecaudoDetalle["fecha"] . "')) as diferencia_fecha_primera_cuota from cuotas" . $sufijo . " cu INNER JOIN simulaciones" . $sufijo . " si ON cu.id_simulacion = si.id_simulacion where cu.id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' and cu.saldo_cuota > 0 order by cu.cuota";
    
                    $rs = sqlsrv_query($link, $queryDB);
    
                    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
                        if ($valor_por_aplicar) {
                            if ($fila["saldo_cuota"] <= $valor_por_aplicar) {
                                $valor_aplicar_cuota = $fila["saldo_cuota"];
                                $pagada = "1";
                            } else {
                                $valor_aplicar_cuota = $valor_por_aplicar;
                                $pagada = "0";
                            }
    
                            sqlsrv_query($link, "insert into pagos_detalle" . $sufijo . " (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $resAplicacionRecaudoDetalle["id_simulacion"] . "', '" . $consecutivo . "', '" . $fila["cuota"] . "', '" . $valor_aplicar_cuota . "', '" . $fila["saldo_cuota"] . "')");
    
                            sqlsrv_query($link, "update cuotas" . $sufijo . " set saldo_cuota = saldo_cuota - " . $valor_aplicar_cuota . ", pagada = '" . $pagada . "' where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
    
                            $valor_por_aplicar -= $valor_aplicar_cuota;
    
                            //Si se recauda el 100% de la primera cuota, se ajusta fecha primera cuota
                            if (!$_REQUEST["ext"] && $fila["cuota"] == "1" && $pagada & $fila["diferencia_fecha_primera_cuota"] > 0) {
                                $fecha_tmp = $resAplicacionRecaudoDetalle["fecha"];
    
                                $fecha = new DateTime($fecha_tmp);
    
                                sqlsrv_query($link, "update simulaciones set fecha_primera_cuota = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'");
    
                                sqlsrv_query($link, "insert into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('" . $resAplicacionRecaudoDetalle["id_simulacion"] . "', '" . $fecha->format('Y-m-t') . "', 'system', GETDATE())");
    
                                for ($j = 1; $j <= $fila["plazo"]; $j++) {
                                    $fecha = new DateTime($fecha->format('Y-m-01'));
    
                                    sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' AND cuota = '" . $j . "'");
    
                                    $fecha->add(new DateInterval('P1M'));
                                }
                            }
                        } else {
                            break;
                        }
                    }
    
                    $queryDB = "select SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'";
    
                    $rs1 = sqlsrv_query($link, $queryDB);
    
                    $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
    
                    if ($fila1["s"] == 0) {
                        sqlsrv_query($link, "update simulaciones" . $sufijo . " set estado = 'CAN', retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "'");
                    }
    
                    if (!$_REQUEST["ext"]) {
                        //Para saber si ya hubo recaudo completo en el mes que se aplica el recaudo
                        $queryDB = "SELECT valor_cuota - CASE WHEN fn_total_recaudado_mes(" . $resAplicacionRecaudoDetalle["id_simulacion"] . ", 0, '" . $resAplicacionRecaudoDetalle["fecha"] . "') IS NULL THEN 0 ELSE fn_total_recaudado_mes(" . $resAplicacionRecaudoDetalle["id_simulacion"] . ", 0, '" . $resAplicacionRecaudoDetalle["fecha"] . "') END as s from cuotas where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' AND FORMAT(fecha, 'yyyy-MM') = FORMAT('" . $resAplicacionRecaudoDetalle["fecha"] . "', 'yyyy-MM')";
    
                        $rs1 = sqlsrv_query($link, $queryDB);
    
                        $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
    
                        if ($fila1["s"] <= 0) {
                            sqlsrv_query($link, "delete from cuotas_norecaudadas where id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' AND fecha = LAST_DAY('" . $resAplicacionRecaudoDetalle["fecha"] . "')");
                        }
                    }
    
                    if (!$_REQUEST["ext"]) {
                        $queryDB = "select vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
                    } else {
                        $queryDB = "select vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cso, fn_total_recaudado(si.id_simulacion, 1) as total_recaudado from ventas_detalle" . $sufijo . " vd INNER JOIN ventas" . $sufijo . " ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $resAplicacionRecaudoDetalle["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
                    }
    
                    $rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
                    if (sqlsrv_num_rows($rs)) {
                        $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
    
                        $opcion_cuota = "0";
    
                        switch ($fila["opcion_credito"]) {
                            case "CLI":
                                $opcion_cuota = $fila["opcion_cuota_cli"];
                                break;
                            case "CCC":
                                $opcion_cuota = $fila["opcion_cuota_ccc"];
                                break;
                            case "CMP":
                                $opcion_cuota = $fila["opcion_cuota_cmp"];
                                break;
                            case "CSO":
                                $opcion_cuota = $fila["opcion_cuota_cso"];
                                break;
                        }
    
                        $cuota_desde = ceil($fila["total_recaudado"] / $opcion_cuota) + 1;
    
                        sqlsrv_query($link, "update ventas_detalle" . $sufijo . " set cuota_desde = '" . $cuota_desde . "' where id_ventadetalle = '" . $fila["id_ventadetalle"] . "'");
                    }
                }  
                sqlsrv_query($link, "update recaudosplanos_detalle" . $sufijo . " set aplicado = '1' where id_recaudoplanodetalle = '" . $params["id_recaudoplanodetalle"] . "'"); 

            }
             
            $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente');
        

        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;  
        }
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");
?>

