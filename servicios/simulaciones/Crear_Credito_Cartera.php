<?php
include ('../../functions.php');
include ('../cors.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

//var_dump($params); die;
if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Crear Credito Cartera':
            $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by id");
            $j=0;
            while ($fila1 = sqlsrv_fetch_array($parametros, SQLSRV_FETCH_ASSOC)) {
                $parametro[$j] = $fila1["valor"];
            
                $j++;
            }
            $edad_maxima_administrativos_hombres = $parametro[5];
            $edad_maxima_administrativos_mujeres = $parametro[6];
            $edad_maxima_activos = $parametro[7];
            $edad_maxima_pensionados = $parametro[8];
            $descuento_transferencia = $parametro[3];
            $iva = $parametro[16];  
            $sin_aportes = "0";
            $nro_libranza = "NULL";
            $fecha_llamada_cliente = "NULL";
            $nro_cuenta = "NULL";
            $tipo_cuenta = "NULL";
            $id_banco = "NULL";
            $id_caracteristica = "NULL";
            $calificacion = "NULL";
            $dia_confirmacion = "NULL";
            $dia_vencimiento = "NULL";
            $status = "NULL";
            $empleado_manual = 0;
            $bloqueo_cuota = "0";

            $id_unidad_negocio=73;//sofaneg
            $id_subestado=46;//6.5 desembolso cliente

            $valor_por_millon_seguro  = 0;

            $porcentaje_seguro = 0;
            /*$diff_dias_ultimo_mes = date("j", strtotime($params["fecha_nacimiento"])) - date("j", strtotime(date("Y-m-d")));
            $queryDB = "SELECT (EXTRACT(YEAR FROM '" . $params["fecha_nacimiento"] . "' + interval " . $edad_maxima_activos . " YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM '" . $params["fecha_nacimiento"] . "' + interval " . $edad_maxima_activos . " YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_activos, (EXTRACT(YEAR FROM '" . $params["fecha_nacimiento"] . "' + interval " . $edad_maxima_pensionados . " YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM '" . $params["fecha_nacimiento"] . "' + interval " . $edad_maxima_pensionados . " YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_pensionados from empleados where cedula = '" . trim($params["cedula"]) . "' AND pagaduria = '" . $params["pagaduria"] . "'";

            $meses_antes_rs = sqlsrv_query($link, $queryDB);
            $fila = sqlsrv_fetch_array($meses_antes_rs, SQLSRV_FETCH_ASSOC);
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);*/
            $meses_antes = 0;
            $consultarPagaduria="SELECT * from pagadurias where nombre='".$params["pagaduria"]."'";
            $queryPagaduria=sqlsrv_query($link,$consultarPagaduria, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if (sqlsrv_num_rows($queryPagaduria)==0){
                $crearPagaduria="INSERT INTO pagadurias (nombre,nombre_corto,sector,estado) VALUES ('".$params["pagaduria"]."',' ','PUBLICO',1)";
                sqlsrv_query($link,$crearPagaduria);
            }

            $descuento1 = 0;
            $descuento2 = 0;
            $descuento3 = 0;
            $descuento4 = 0;
            $descuento5 = 0;
            $descuento6 = 0;
            $descuento7 = 0;
            $params["direccion"] = '';
            $params["correo"] = '';
            $params['fecha_vinculacion'] = '';
            $params['ciudad'] = '';

            $params["frente_al_cliente"] = '';
                $crearSimulacion="INSERT INTO  simulaciones (id_subestado,nro_libranza,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,sin_seguro,fecha_primera_cuota,fecha_desembolso,servicio_nube,id_comercial, id_oficina, telemercadeo, fecha_estudio, cedula, nombre, pagaduria, pa, ciudad, institucion, nivel_educativo, fecha_nacimiento, telefono, meses_antes_65, fecha_inicio_labor, medio_contacto, salario_basico, adicionales, bonificacion, total_ingresos, aportes, otros_aportes, total_aportes, total_egresos, salario_minimo, ingresos_menos_aportes, salario_libre, nivel_contratacion, embargo_actual, historial_embargos, embargo_alimentos, embargo_centrales, descuentos_por_fuera, cartera_mora, valor_cartera_mora, puntaje_datacredito, puntaje_cifin, valor_descuentos_por_fuera, id_unidad_negocio, tasa_interes, plazo, tipo_credito, suma_al_presupuesto, total_cuota, total_valor_pagar, retanqueo1_libranza, retanqueo1_cuota, retanqueo1_valor, retanqueo2_libranza, retanqueo2_cuota, retanqueo2_valor, retanqueo3_libranza, retanqueo3_cuota, retanqueo3_valor, retanqueo_total_cuota, retanqueo_total, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso, desembolso_cliente, decision, decision_sistema, valor_visado, bloqueo_cuota, bloqueo_cuota_valor, fecha_llamada_cliente, nro_cuenta, tipo_cuenta, id_banco,  id_caracteristica, calificacion, dia_confirmacion, dia_vencimiento, status, valor_credito, resumen_ingreso, incor, comision, utilidad_neta, sobre_el_credito, estado, tipo_producto, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6,descuento7, descuento_transferencia, porcentaje_seguro, valor_por_millon_seguro, porcentaje_extraprima, sin_aportes, empleado_manual, iva, frente_al_cliente, usuario_radicado, fecha_radicado, usuario_creacion, fecha_creacion,  proposito_credito, fecha_cartera, id_origen)values ('".$id_subestado."', 'SOF ".$params["libranza"]."','".$params['primer_nombre']."','".$params['segundo_nombre']."','".$params['primer_apellido']."','".$params['segundo_apellido']."',1,'".$params["fecha_primera_cuota"]."','".$params["fecha_desembolso"]."','0','1', 121, '0', CURDATE(), '" . trim($params["cedula"]) . "', '" . strtoupper(trim($params['primer_apellido'].' '.$params['segundo_apellido']) . ' ' . trim($params['primer_nombre'].' '.$params['segundo_nombre'])) . "', '" . $params["pagaduria"] . "', 'ESEFECTIVO', '" . strtoupper($params["ciudad"]) . "', '" . strtoupper($params["institucion"]) . "', '', '" . $params["fecha_nacimiento"] . "', '" . $params["telefono"] . "', '" . $meses_antes . "', CURDATE(), 'BASE DE DATOS', '0', '0', '0', '0', '0', '0', '0', '0', (SELECT salario_minimo from salario_minimo where ano = YEAR(CURDATE())), '0', '0', 'PENSIONADO', 'NO', '0', 'NO', 'NO', 'NO', 'NO', '0', '-1', '-1', '0', '".$id_unidad_negocio."', '" . $params["tasa"] . "', '" . $params["plazo"] . "', 'CREDITO NORMAL', '0', '0', '0', '', '0', '0', '', '0', '0', '', '0', '0', '0', '0', 'CSO', '0', '" . (-1.00 * $descuento_transferencia) . "', '0', '" . (-1.00 * $descuento_transferencia) . "', '0', '" . (-1.00 * $descuento_transferencia) . "', '".($params["cuota"])."', '" . ($params["desembolso"]) . "', '" . ($params["desembolso"]) . "', '" . $label_viable . "', '" . $label_negado . "', '0', '" . $bloqueo_cuota . "', '0', " . $fecha_llamada_cliente . ", " . $nro_cuenta . ", " . $tipo_cuenta . ", " . $id_banco . ",  " . $id_caracteristica . ", " . $calificacion . ", " . $dia_confirmacion . ", " . $dia_vencimiento . ", " . $status . ", '".$params["valor_credito"]."', '0', '0', '0', '0', '0', 'DES', '0', '" . $descuento1 . "', '" . $descuento2 . "', '" . $descuento3 . "', '" . $descuento4 . "', '" . $descuento5 . "', '" . $descuento6 . "', '" . $descuento7 . "', '" . $descuento_transferencia . "', '" . $porcentaje_seguro . "', '" . $valor_por_millon_seguro . "', '0', '" . $sin_aportes . "', '" . $empleado_manual . "', '" . $iva . "', '" . $params["frente_al_cliente"] . "', 'system', getdate(), 'system', getdate(),  '9', '".$params["mes_prod"]."', 3)";

                     $info_cuota="";

            if (sqlsrv_query($link,$crearSimulacion)){

                $id_simul = sqlsrv_insert_id($link);
                $seguro= str_replace(",", "", $params["seguro"]);
                $valor_cuota = str_replace(",", "", $params["cuota"]) - round($seguro);
                $valor_cuota_total = str_replace(",", "", $params["cuota"]);
                $saldo = str_replace(",", "", $params["valor_credito"]);
                $fecha_primer_giro = date("Y", strtotime($params["fecha_primera_cuota"]))."-".date("m", strtotime($params["fecha_primera_cuota"]))."-01";
                $fecha_primera_cuota = $params["fecha_primera_cuota"];
                $rs1 = sqlsrv_query($link, "SELECT * from cuotas where id_simulacion = '".$id_simul."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                $j = 1;
                $consultaCuotas="";
                $cuotas_pagadas=$params["cuotas_pagadas"];

                for ($j = 1; $j <= $params["plazo"]; $j++) {

                    if (!sqlsrv_num_rows($rs1)) {
                        $interes = $saldo * $params["tasa"] / 100.00;
                        $capital = $valor_cuota- $interes;
                        $saldo -= $capital;
                        
                        if ($j == $params["plazo"]) {
                            $valor_cuota += $saldo;
                            $capital = $valor_cuota - $interes;
                            $saldo = 0;
                        }
                        
                        $pagado=0;
                        sqlsrv_query($link, "INSERT INTO cuotas (pagada,id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$pagado."','".$id_simul."', '".$j."', '".$fecha_primera_cuota."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($valor_cuota_total)."', '".round($valor_cuota_total)."')");
                    } else {
                        sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha_primera_cuota->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cuota = '" . $j . "'");
                    }

                    $fecha_primera_cuota = date("Y-m-d", strtotime("+1 month",strtotime($fecha_primera_cuota)));  
                }

                $query = "SELECT nombre FROM empleados WHERE cedula = '"
                . trim($params['cedula']) . "' AND pagaduria = '"
                . $params['pagaduria'] . "'";

                $resultado = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($resultado) == 0) {
                    $query = "INSERT INTO empleados VALUES ("
                        . "'" . trim($params['cedula']) . "', "
                        . "'" . strtoupper(trim($params['primer_apellido'].' '.$params['segundo_apellido'])) . ' ' . strtoupper(trim($params['primer_nombre'].' '.$params['segundo_nombre'])) . "', "
                        . "'" . $params['pagaduria'] . "', "
                        . "'', "
                        . "'', "
                        . "'', "
                        . "'0', "
                        . "'0', "
                        . "'0', "
                        . "'0', "
                        . "'', "
                        . "'" . strtoupper($params['direccion']) . "', "
                        . "'" . $params['telefono'] . "', "
                        . "'" . $params['correo'] . "', "
                        . "'" . $params['fecha_nacimiento'] . "', "
                        . "'', "
                        . "'', "
                        . "'" . strtoupper($params['ciudad']) . "', "
                        . "'" . '1' . "', "
                        . "'', "
                        . "'" . strtoupper($params['ciudad']) . "', "
                        . "'" . $params['fecha_vinculacion'] . "', "
                        . "'')";
                    sqlsrv_query($link, $query);

                    $query = "INSERT INTO empleados_creacion ("
                        . "cedula, "
                        . "pagaduria, "
                        . "id_usuario, "
                        . "fecha_creacion) VALUES ("
                        . "'" . trim($params['cedula']) . "', "
                        . "'" . $params['pagaduria'] . "', "
                        . "'1', "
                        . "getdate())";

                    sqlsrv_query($link, $query);
                }

                sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, email) values ('" . $id_simul . "', '" . trim($params["cedula"]) . "', '" . $params["fecha_nacimiento"] . "', '" . ($params["telefono"]) . "', '" . ($params["telefono"]) . "', '" . (strtoupper($params["direccion"])) . "', '" . ($params["correo"]) . "')");

                $crearSubestado="INSERT INTO simulaciones_subestados (id_simulacion,id_subestado,usuario_creacion,fecha_creacion) VALUES ('".$id_simul."',46,'".$_SESSION["S_LOGIN"]."',CURRENT_TIMESTAMP)";
                sqlsrv_query($link,$crearSubestado);

                $fecha_primera_cuota_tmp = date("Y", strtotime($params["fecha_primera_cuota"]))."-".date("m", strtotime($params["fecha_primera_cuota"]))."-01";

                $fecha_primera_cuota = new DateTime($fecha_primera_cuota_tmp);

                $consultarCuotasPagadas="SELECT count(id_simulacion) as cuotas_pagadas FROM cuotas WHERE pagada=1 and id_simulacion='".$id_simul."'";
                $queryCuotasPagadas=sqlsrv_query($link,$consultarCuotasPagadas);
                $resCuotasPagadas=sqlsrv_fetch_array($queryCuotasPagadas, SQLSRV_FETCH_ASSOC);

                if (($params["plazo"]-$resCuotasPagadas["cuotas_pagadas"])==0){
                    $actualizarEstadoCredito="UPDATE simulaciones SET estado='CAN' WHERE id_simulacion='".$id_simul."'";
                    sqlsrv_query($link,$actualizarEstadoCredito);
                }

                $codigo=200;        
                $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente',"id_simulacion"=>$id_simul);
            }else{
                $codigo=404;        
                $response = array('operacion' => 'Error al actualizar credito', 'codigo' => $codigo, 'mensaje' => $crearSimulacion);
            }
        break;
        
        case 'Crear Pago Cuotas':
            if ($params["ESTADOPREPAGO"]=="TERMINADO  NORMAL") {
                $consultarCuotasCredito="SELECT * FROM cuotas WHERE id_simulacion='".$params["id_simulacion"]."'";
                $queryCuotasCredito=sqlsrv_query($link,$consultarCuotasCredito);
                
                while ($resCuotasCredito=sqlsrv_fetch_array($queryCuotasCredito, SQLSRV_FETCH_ASSOC)){
                    sqlsrv_query($link, "UPDATE  cuotas SET pagada=1, saldo_cuota=0 WHERE id_simulacion='".$params["id_simulacion"]."' and cuota='".$resCuotasCredito["cuota"]."'");
                    sqlsrv_query($link, "insert into pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) values ('".$params["id_simulacion"]."', '".$resCuotasCredito["cuota"]."', '".$resCuotasCredito["fecha"]."', '".$resCuotasCredito["valor_cuota"]."', '1', 'NOMINA', 'system', getdate())");
                    sqlsrv_query($link, "insert into pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('".$params["id_simulacion"]."', '".$resCuotasCredito["cuota"]."','".$resCuotasCredito["cuota"]."', '" . round($resCuotasCredito["valor_cuota"]) . "', '" . round($resCuotasCredito["valor_cuota"]) . "')");
                }

                $consultarCuotasPagadas="SELECT count(id_simulacion) as cuotas_pagadas FROM cuotas WHERE pagada=1 and id_simulacion='".$params["id_simulacion"]."'";
                $queryCuotasPagadas=sqlsrv_query($link,$consultarCuotasPagadas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                $resCuotasPagadas=sqlsrv_fetch_array($queryCuotasPagadas, SQLSRV_FETCH_ASSOC);

                if (($params["plazo"]-$resCuotasPagadas["cuotas_pagadas"])==0) {
                    $actualizarEstadoCredito="UPDATE simulaciones SET estado='CAN' WHERE id_simulacion='".$params["id_simulacion"]."'";
                    sqlsrv_query($link,$actualizarEstadoCredito);
                }

                $codigo=200;        
                $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente','id_simulacion'=>$params["id_simulacion"],'cantidad_cuotas'=>sqlsrv_num_rows($queryCuotasCredito));
            }else{

                $valor_abono = $params["valor_credito"] - $params["valor_pendiente"];

                $queryDB = "SELECT cu.*, si.* FROM cuotas cu INNER JOIN simulaciones si ON cu.id_simulacion = si.id_simulacion WHERE cu.id_simulacion = '".$params["id_simulacion"]."' AND cu.saldo_cuota > 0 AND cu.cuota = 1 ORDER BY cu.cuota";

                if($rs = sqlsrv_query($link, $queryDB)){
                    $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

                    if(sqlsrv_query($link, "INSERT INTO pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) VALUES ('".$params["id_simulacion"]."', '1', '".$fila["fecha"]."', '".$fila["valor_cuota"]."', '1', 'NOMINA', 'system', getdate())")){

                        sqlsrv_query($link, "INSERT INTO pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('".$params["id_simulacion"]."', 1, '".$fila["cuota"]."', '".$fila["valor_cuota"]."', '".$fila["valor_cuota"]."')");
                        
                        $valor_abono -= intval($fila["capital"]);

                        sqlsrv_query($link, "UPDATE cuotas set saldo_cuota = 0, pagada = '1' where id_simulacion = '".$params["id_simulacion"]."' and cuota = 1");

                        $fecha = new DateTime($fila["fecha"]);

                        sqlsrv_query($link, "UPDATE simulaciones set fecha_primera_cuota = '".$fecha->format('Y-m-t')."' where id_simulacion = '".$params["id_simulacion"]."'");

                        sqlsrv_query($link, "INSERT INTO simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('".$params["id_simulacion"]."', '".$fecha->format('Y-m-t')."', 'system', getdate())");

                        for ($j = 1; $j <= $fila["plazo"]; $j++) {
                            $fecha = new DateTime($fecha->format('Y-m-01'));

                            sqlsrv_query($link, "UPDATE cuotas SET fecha = '".$fecha->format('Y-m-t')."' where id_simulacion = '".$params["id_simulacion"]."' AND cuota = '".$j."'");

                            $fecha->add(new DateInterval('P1M'));
                        }
                    }

                    $valor_cuota = $fila["opcion_cuota_cso"] - $fila["seguro"];
                    $tasa_interes = $fila["tasa_interes"];
                    $queryDB = "SELECT SUM(capital) as s from cuotas where id_simulacion = '" . $params["id_simulacion"] . "' AND saldo_cuota = valor_cuota";
                    $rs1 = sqlsrv_query($link, $queryDB);
                    $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
                    $saldo_capital = $fila1["s"];
                    $saldo = $saldo_capital - str_replace(",", "", $valor_abono);

                    sqlsrv_query($link, "INSERT INTO pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) VALUES ('".$params["id_simulacion"]."', '2', '2023-04-30', '".$valor_abono."', '1', 'ABONOCAPITAL', 'system', getdate())");

                    sqlsrv_query($link, "INSERT into pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $params["id_simulacion"] . "', '2', '0', '" .$valor_abono. "', '" . $saldo_capital . "')");

                    
                    $queryDB = "SELECT * from cuotas where id_simulacion = '" . $params["id_simulacion"] . "' and saldo_cuota = valor_cuota order by cuota";

                    $rs = sqlsrv_query($link, $queryDB);

                    $primera_iteracion = 1;

                    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
                        if ($primera_iteracion) {
                            sqlsrv_query($link, "UPDATE cuotas set abono_capital = abono_capital + " . str_replace(",", "", $valor_abono) . " where id_simulacion = '" . $params["id_simulacion"] . "' and cuota = '" . ($fila["cuota"] - 1) . "'");

                            $primera_iteracion = 0;
                        }

                        if ($saldo > 0) {
                            $interes = $saldo * $tasa_interes / 100.00;
                            $capital = $valor_cuota - round($interes);
                            $seguro = $fila["seguro"];
                            $saldo -= $capital;

                            if ($saldo < 0) {
                                $capital += $saldo;
                                $saldo = 0;
                            }

                            $pagada = 0;
                        } else {
                            $interes = 0;
                            $capital = 0;
                            $seguro = 0;
                            $pagada = 1;
                        }

                        $total_cuota = round($capital) + round($interes) + round($seguro);
                        $saldo_cuota = $total_cuota;
                        sqlsrv_query($link, "UPDATE cuotas set capital_org = (CASE WHEN capital_org IS NULL THEN capital ELSE capital_org END), interes_org = (CASE WHEN interes_org IS NULL THEN interes ELSE interes_org END), capital = '" . round($capital) . "', interes = '" . round($interes) . "', seguro = '" . round($seguro) . "', valor_cuota = '" . round($total_cuota) . "', saldo_cuota = '" . round($saldo_cuota) . "', pagada = '" . $pagada . "' where id_simulacion = '" . $params["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
                    }

                    $codigo=200;        
                    $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente','id_simulacion'=>$params["id_simulacion"]);
                }else{
                    $response = array('operacion' => 'No tiene plan de pagos Disponible', 'codigo' => "300", 'mensaje' => 'No tiene plan de pagos Disponible','id_simulacion'=>$params["id_simulacion"]);
                }

                /*$consultarCuotasCredito="SELECT * FROM cuotas WHERE id_simulacion='".$params["id_simulacion"]."'";
                $queryCuotasCredito=sqlsrv_query($link,$consultarCuotasCredito);

                sqlsrv_query($link, "INSERT INTO pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) VALUES ('".$params["id_simulacion"]."', '1', '2023-04-30', '".$valor_abono."', '1', 'ABONOCAPITAL', 'system', getdate())");

                sqlsrv_query($link, "INSERT INTO pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('".$params["id_simulacion"]."', '1','".$params["cuotas_pagadas"]."', '".$valor_abono."', '".$params["valor_credito"]."')");

                $cont = 1;

                while ($resCuotasCredito=sqlsrv_fetch_array($queryCuotasCredito, SQLSRV_FETCH_ASSOC)){
                    if($cont <= $params["cuotas_pagadas"]){
                        sqlsrv_query($link, "UPDATE  cuotas SET pagada=1, capital = 0, interes =0, seguro = 0, valor_cuota = 0, saldo_cuota = 0  WHERE id_simulacion='".$params["id_simulacion"]."' and cuota='".$resCuotasCredito["cuota"]."'");
                        $cont++;
                    }
                }

                sqlsrv_query($link, "UPDATE cuotas SET abono_capital = '".$valor_abono."' WHERE id_simulacion='".$params["id_simulacion"]."' and cuota='".$params["cuotas_pagadas"]."'");

                $cont = 1;

                $queryDB = "SELECT si.*, cu.seguro from simulaciones si INNER JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '" . $params["id_simulacion"] . "' AND cu.cuota = '1'";

                $rs = sqlsrv_query($link, $queryDB);
                $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                $tasa_interes = $fila["tasa_interes"];*/
            }
        break;
        
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada 2', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;   
    }  
}else{
    $codigo=404;        
    $response = array('operacion' => 'Operacion errada 1', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
http_response_code("200");
echo json_encode($response);
?>