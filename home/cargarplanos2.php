<?php
include('../functions.php');
$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR" || !$_SESSION["FUNC_CARGUEPLANOS"]) {
    exit;
}

ini_set('max_execution_time', 10800); //300 seconds = 5 minutes

$mensaje = "";

$bas_name = $_FILES["bas"]["name"];
$loc_name = $_FILES["loc"]["name"];
$nac_name = $_FILES["nac"]["name"];
$emb_name = $_FILES["emb"]["name"];
$des_name = $_FILES["des"]["name"];
$rec_name = $_FILES["rec"]["name"];
$met_name = $_FILES["met"]["name"];
$ten_name = $_FILES["ten"]["name"];
$car_name = $_FILES["car"]["name"];
$pag_name = $_FILES["pag"]["name"];
$ven_name = $_FILES["ven"]["name"];




if ($bas_name) {
    sqlsrv_query($link, "UPDATE empleados set estado_cargue = '0' where pagaduria = '" . $_REQUEST["pagaduriabas"] . "'");

    $file = fopen($_FILES['bas']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $institucion = utf8_encode(trim($datos[0]));
                $nombre = utf8_encode(trim($datos[1]));
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[2])));
                $cargo = utf8_encode(trim($datos[3]));
                $grado = utf8_encode(trim($datos[4]));
                $pagaduria = utf8_encode(trim($datos[5]));
                $salario_basico = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[6]))));
                $ingresos = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[7]))));
                $egresos = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[8]))));
                $neto_pagar = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[9]))));
                $ciudad = utf8_encode(trim($datos[10]));
                $departamento = utf8_encode(trim($datos[11]));

                $existe = sqlsrv_query($link, "SELECT cedula from empleados where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriabas"] . "'");

                if (!sqlsrv_num_rows($existe)) {
                    sqlsrv_query($link, "INSERT into empleados (cedula, nombre, pagaduria, institucion, cargo, grado, salario_basico, ingresos, egresos, neto_pagar, ciudad, departamento, estado_cargue) values ('" . $cedula . "', '" . $nombre . "', '" . $_REQUEST["pagaduriabas"] . "', '" . $institucion . "', '" . $cargo . "', '" . $grado . "', '" . $salario_basico . "', '" . $ingresos . "', '" . $egresos . "', '" . $neto_pagar . "', '" . $ciudad . "', '" . $departamento . "', '1')");
                } else {
                    sqlsrv_query($link, "UPDATE empleados set nombre = '" . $nombre . "', pagaduria = '" . $_REQUEST["pagaduriabas"] . "', institucion = '" . $institucion . "', cargo = '" . $cargo . "', grado = '" . $grado . "', salario_basico = '" . $salario_basico . "', ingresos = '" . $ingresos . "', egresos = '" . $egresos . "', neto_pagar = '" . $neto_pagar . "', ciudad = '" . $ciudad . "', departamento = '" . $departamento . "', estado_cargue = '2' where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriabas"] . "'");

                    sqlsrv_query($link, "UPDATE empleados_creacion set fecha_modificacion = GETDATE() where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriabas"] . "'");
                }

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " .sqlsrv_errors($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Datos Basicos ha sido cargado satisfactoriamente\\n";

        $generar_log_cargue = 1;

        $archivo = "bas";
    }

    fclose($file);
}




if ($loc_name) {
    $file = fopen($_FILES['loc']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[1])));
                $nivel_educativo = utf8_encode(trim($datos[4]));
                $direccion = utf8_encode(trim($datos[5]));
                $telefono = utf8_encode(trim($datos[6]));;
                $mail = utf8_encode(trim($datos[7]));

                sqlsrv_query($link, "update empleados set nivel_educativo = '" . $nivel_educativo . "', direccion = '" . $direccion . "', telefono = '" . $telefono . "', mail = '" . $mail . "' where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagadurialoc"] . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Localizacion ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}

if ($nac_name) {
    $file = fopen($_FILES['nac']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
                $fecha_nac = trim($datos[8]);
                $fecha_inl = trim($datos[9]);

                //$fecha_nac = explode(" ", trim($datos[8]));
                //$fecha_nac = explode("/", $fecha_nac[0]);
                //$fecha_nacimiento = $fecha_nac[2] . "-" . $fecha_nac[0] . "-" . $fecha_nac[1];
                /*
                if (trim($datos[9])) {
                    $fecha_inl = explode(" ", trim($datos[9]));
                    $fecha_inl = explode("/", $fecha_inl[0]);
                    $fecha_inicio_labor = "'" . $fecha_inl[2] . "-" . $fecha_inl[0] . "-" . $fecha_inl[1] . "'";
                } else {
                    $fecha_inicio_labor = "NULL";
                }
                */
                $nivel_contratacion = utf8_encode(trim($datos[10]));

                //mysql_query("update empleados set fecha_nacimiento = '" . $fecha_nacimiento . "', fecha_inicio_labor = " . $fecha_inicio_labor . ", nivel_contratacion = '" . $nivel_contratacion . "' where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagadurianac"] . "'", $link);
                sqlsrv_query($link, "update empleados set fecha_nacimiento = '" . $fecha_nac . "', fecha_inicio_labor = '" . $fecha_inl . "', nivel_contratacion = '" . $nivel_contratacion . "' where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagadurianac"] . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_error($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Fechas de Nacimiento ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}

if ($emb_name) {
    sqlsrv_query($link, "DELETE from embargos where pagaduria = '" . $_REQUEST["pagaduriaemb"] . "'");

    $file = fopen($_FILES['emb']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
                $tipoembargo = utf8_encode(trim($datos[1]));

                if ($datos[5]) {
                    $fechaf = explode(" ", trim($datos[5]));
                    $fechaf = explode("/", $fechaf[0]);
                    $fechafin = "'" . $fechaf[2] . "-" . $fechaf[0] . "-" . $fechaf[1] . "'";
                } else {
                    $fechafin = "NULL";
                }

                sqlsrv_query($link, "insert into embargos (cedula, tipoembargo, fechafin, pagaduria) select '" . $cedula . "', '" . $tipoembargo . "', " . $fechafin . ", '" . $_REQUEST["pagaduriaemb"] . "' from empleados where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriaemb"] . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Embargos ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}


if ($des_name) {
    sqlsrv_query($link, "delete from descuentos where pagaduria = '" . $_REQUEST["pagaduriades"] . "'");

    $file = fopen($_FILES['des']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[1])));
                $codigo = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
                $entidad = utf8_encode(trim($datos[3]));
                $descuento = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[4]))));

                sqlsrv_query($link, "insert into descuentos (cedula, codigo, entidad, descuento, pagaduria) select '" . $cedula . "', '" . $codigo . "', '" . $entidad . "', '" . $descuento . "', '" . $_REQUEST["pagaduriades"] . "' from empleados where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriades"] . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Descuentos ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}



if ($rec_name) {
    sqlsrv_query($link, "delete from rechazos where pagaduria = '" . $_REQUEST["pagaduriarec"] . "'");

    $file = fopen($_FILES['rec']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if (is_numeric($datos[2])) {
                $cedula = trim(str_replace(".", "", str_replace(",", "", $datos[2])));
                $mensaje_rechazo = utf8_encode(trim($datos[10]));

                if (strpos(strtoupper($mensaje_rechazo), "VALORSUPERIORA4000000") === false)
                sqlsrv_query($link, "insert into rechazos (cedula, mensaje, pagaduria) select '" . $cedula . "', '" . $mensaje_rechazo . "', '" . $_REQUEST["pagaduriarec"] . "' from empleados where cedula = '" . $cedula . "' AND pagaduria = '" . $_REQUEST["pagaduriarec"] . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_error($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Rechazos ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}


if ($met_name) {
    $file = fopen($_FILES['met']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $login = trim($datos[0]);
                $meta_mes = trim(str_replace("$", "", str_replace(".", "", str_replace(",", "", $datos[1]))));

                sqlsrv_query($link, "update usuarios set meta_mes = '" . $meta_mes . "' where login = '" . $login . "'");

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_error($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Metas del mes ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}


if ($ten_name) {
    $file = fopen($_FILES['ten']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $entidad = utf8_encode(trim($datos[0]));
                $dias_entrega = trim($datos[1]);
                $dias_vigencia = trim($datos[2]);

                $existe = sqlsrv_query($link, "select entidad from entidades where entidad = '" . $entidad . "'");

                if (!sqlsrv_num_rows($existe)) {
                    sqlsrv_query($link, "insert into entidades (entidad, dias_entrega, dias_vigencia) values ('" . $entidad . "', '" . $dias_entrega . "', '" . $dias_vigencia . "')");
                } else {
                    sqlsrv_query($link, "update entidades set entidad = '" . $entidad . "', dias_entrega = '" . $dias_entrega . "', dias_vigencia = '" . $dias_vigencia . "' where entidad = '" . $entidad . "'");
                }

                if (sqlsrv_errors($link)) {
                    $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de Tiempos Entidades ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}

if ($car_name) {
    //Para comparar la cartera, comentar desde aqu� 1
    //mysql_query("delete from pagos_detalle", $link);
    //mysql_query("delete from pagos", $link);
    //mysql_query("delete from cuotas where id_simulacion NOT IN (2123, 2367)", $link);
    //Hasta aqu� 1

    sqlsrv_query($link, "delete from tmp_cartera");

    $file = fopen($_FILES['car']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim($datos[0]);
                $valor_credito = trim($datos[1]);
                $plazo = trim($datos[2]);
                $tasa_interes = trim($datos[3]);
                $opcion_cuota = trim($datos[4]);
                $seguro = trim($datos[5]);
                $fecha_produccion = trim($datos[6]);
                $fecha_primera_cuota = trim($datos[7]);

                //Para comparar la cartera, descomentar desde aqu� 2
                //mysql_query("insert into tmp_cartera values ('".($i - 1)."', '".$cedula."', '".$valor_credito."', '".$plazo."', '".($tasa_interes * 100)."', '".$opcion_cuota."', '".$seguro."', '".$fecha_produccion."', '".$fecha_primera_cuota."')", $link);
                //if (mysql_error($link)) { $mensaje = "Error en la linea [".$i."]: ".mysql_error($link)."\\n"; break; }
                //Hasta aqu� 2
                //Para comparar la cartera, comentar desde aqu� 3
                $saldo = $valor_credito;

                $valor_cuota = $opcion_cuota - $seguro;

                $rs = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '" . $cedula . "' AND estado IN ('EST', 'DES', 'CAN')");

                if (sqlsrv_num_rows($rs)) {
                    if (sqlsrv_num_rows($rs) > 1) {
                        $rs2 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '" . $cedula . "' AND estado IN ('EST', 'DES', 'CAN') AND valor_credito = '" . $valor_credito . "'");

                        if (sqlsrv_num_rows($rs2))
                            $fila = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
                        else
                            $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                    } else {
                        $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                    }

                    $id_simulacion = $fila["id_simulacion"];

                    //mysql_query("update simulaciones set fecha_produccion = '".$fecha_produccion."', fecha_primera_cuota = '".$fecha_primera_cuota."' where id_simulacion = '".$id_simulacion."'", $link);
                    sqlsrv_query($link, "update simulaciones set fecha_primera_cuota = '" . $fecha_primera_cuota . "' where id_simulacion = '" . $id_simulacion . "'");

                    $fecha_tmp = $fecha_primera_cuota;

                    $fecha = new DateTime($fecha_tmp);

                    for ($j = 1; $j <= $plazo; $j++) {
                        $fecha = new DateTime($fecha->format('Y-m-01'));

                        $interes = $saldo * $tasa_interes;

                        $capital = $valor_cuota - $interes;

                        $saldo -= $capital;

                        if ($j == $plazo) {
                            $valor_cuota += $saldo;

                            $capital = $valor_cuota - $interes;

                            $saldo = 0;
                        }

                        $saldo_cuota = round($opcion_cuota);
                        $pagada = 0;

                        //mysql_query("insert into cuotas (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota, pagada) values ('".$id_simulacion."', '".$j."', '".$fecha->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($opcion_cuota)."', '".$saldo_cuota."', '".$pagada."')", $link);
                        sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $id_simulacion . "' AND cuota = '" . $j . "'");

                        $fecha->add(new DateInterval('P1M'));
                    }

                    if (sqlsrv_errors($link)) {
                        $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                        break;
                    }
                }
                //Hasta aqu� 3
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de cartera ha sido cargado satisfactoriamente\\n";

        //Para comparar la cartera, descomentar desde aqu� 4
        //$generar_log_cargue = 1;
        //Hasta aqu� 4

        $archivo = "car";
    }

    fclose($file);
}


if ($pag_name) {
    sqlsrv_query($link, "delete from pagos_detalle");

    sqlsrv_query($link, "delete from pagos");

    $file = fopen($_FILES['pag']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[0]) {
                $cedula = trim($datos[0]);
                $fecha_recaudo = trim($datos[1]);
                $cuota = trim($datos[2]);
                $valor = trim($datos[3]);
                $valor_credito = trim($datos[4]);
                $pagaduria = trim($datos[5]);

                $rs = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '" . $cedula . "' AND estado IN ('EST', 'DES', 'CAN')");

                if (sqlsrv_num_rows($rs)) {
                    if (sqlsrv_num_rows($rs) > 1) {
                        $rs2 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '" . $cedula . "' AND estado IN ('EST', 'DES', 'CAN') AND valor_credito = '" . $valor_credito . "'");

                        if (sqlsrv_num_rows($rs2))
                            $fila = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
                        else
                            $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                    } else {
                        $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                    }

                    $id_simulacion = $fila["id_simulacion"];

                    $rs1 = sqlsrv_query($link, "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from pagos where id_simulacion = '" . $id_simulacion . "'");

                    $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

                    $consecutivo = $fila1["max_c"];

                    sqlsrv_query($link, "insert into pagos (id_simulacion, consecutivo, fecha, valor, usuario_creacion, fecha_creacion) values ('" . $id_simulacion . "', '" . $consecutivo . "', '" . $fecha_recaudo . "', '" . round($valor) . "', 'system', GETDATE())");

                    sqlsrv_query($link, "insert into pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $id_simulacion . "', '" . $consecutivo . "', '" . $cuota . "', '" . round($valor) . "', (SELECT saldo_cuota FROM cuotas WHERE id_simulacion = '" . $id_simulacion . "' AND cuota = '" . $cuota . "'))");

                    sqlsrv_query($link, "update cuotas set pagada = (CASE WHEN saldo_cuota - " . round($valor) . " <= 0 THEN '1' ELSE '0' END), saldo_cuota = saldo_cuota - " . round($valor) . " where id_simulacion = '" . $id_simulacion . "' AND cuota = '" . $cuota . "'");

                    if (sqlsrv_errors($link)) {
                        $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_error($link) . "\\n";
                        break;
                    }
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de pagos ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}

if ($ven_name) {
    $file = fopen($_FILES['ven']['tmp_name'], "r");

    $primer_registro = 1;

    $i = 0;

    while (!feof($file)) {
        $i++;

        $linea = fgets($file, 4096);

        $linea = str_replace(chr(10), "", $linea);

        $linea = str_replace(chr(13), "", $linea);

        if ($i != 1) {
            $datos = explode("\t", $linea);

            if ($datos[38]) //0
            {
                $cedula = trim($datos[1]);
                $valor_credito = trim($datos[20]);
                $pagaduria = trim($datos[21]);
                $fecha = trim($datos[39]); //28
                $fecha_primer_pago = trim($datos[41]); //30
                $cuotas_vendidas = trim($datos[44]); // 33
                $nro_venta = trim($datos[38]); //27
                $id_comprador = trim($datos[47]); // 36
                $modalidad_prima = trim($datos[48]); //37
                $tasa_venta = trim($datos[43]); //32
                $recomprado = trim($datos[46]); //35

                $rs = sqlsrv_query($link, "select id_simulacion, plazo from simulaciones where cedula = '" . $cedula . "' AND valor_credito = '" . $valor_credito . "' AND pagaduria = '" . $pagaduria . "' AND estado IN ('DES', 'CAN')");

                if (sqlsrv_num_rows($rs)) {
                    if (sqlsrv_num_rows($rs) == 1) {
                        $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

                        if (!($nro_venta == $nro_venta_tmp && $id_comprador == $id_comprador_tmp)) {
                            sqlsrv_query($link, "insert into ventas (fecha, id_comprador, tasa_venta, modalidad_prima, nro_venta, estado, tipo, usuario_creacion, fecha_creacion) values ('" . $fecha . "', '" . $id_comprador . "', '" . $tasa_venta . "', '" . $modalidad_prima . "', '" . $nro_venta . "', 'VEN', 'VENTA', 'system', GETDATE())");

                            $rs1 = sqlsrv_query($link, "select MAX(id_venta) as max_c from ventas");

                            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

                            $id_venta = $fila1["max_c"];

                            $nro_venta_tmp = $nro_venta;

                            $id_comprador_tmp = $id_comprador;
                        }

                        $cuota_desde = $fila["plazo"] - $cuotas_vendidas + 1;

                        sqlsrv_query($link, "insert into ventas_detalle (id_venta, id_simulacion, fecha_primer_pago, cuota_desde, cuota_hasta, recomprado) values ('" . $id_venta . "', '" . $fila["id_simulacion"] . "', '" . $fecha_primer_pago . "', '" . $cuota_desde . "', '" . $fila["plazo"] . "', '" . $recomprado . "')");

                        $rs3 = sqlsrv_query($link, "select MAX(id_ventadetalle) as max_c from ventas_detalle");

                        $fila3 = sqlsrv_fetch_array($rs3, SQLSRV_FETCH_ASSOC);

                        $id_ventadetalle = $fila3["max_c"];

                        $queryDB = "select DATEDIFF('" . $fecha_primer_pago . "', '" . $fecha . "') as dias_primer_vcto, SUM(cu.capital) as saldo_capital from simulaciones si LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= '" . $cuota_desde . "' AND cu.cuota <= '" . $fila["plazo"] . "' where si.id_simulacion = '" . $fila["id_simulacion"] . "'";

                        $rs1 = sqlsrv_query($link, $queryDB);

                        $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

                        switch ($modalidad_prima) {
                            case "ANT":
                                sqlsrv_query($link, "insert into ventas_cuotas (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) select '" . $id_ventadetalle . "', cuota, ADDDATE('" . $fecha_primer_pago . "', INTERVAL (cuota - '" . $cuota_desde . "') MONTH), capital, interes, '0', capital + interes, capital + interes from cuotas where id_simulacion = '" . $fila["id_simulacion"] . "' AND cuota >= '" . $cuota_desde . "' AND cuota <= '" . $fila["plazo"] . "' order by cuota");

                                break;

                            case "MDI":
                                $saldo = $fila1["saldo_capital"];

                                if ($cuotas_vendidas == $fila["plazo"])
                                    $saldo = $valor_credito;

                                $tasa_interes = $tasa_venta;

                                $queryDB = "select cuota, capital from cuotas where id_simulacion = '" . $fila["id_simulacion"] . "' AND cuota >= '" . $cuota_desde . "' AND cuota <= '" . $fila["plazo"] . "' order by cuota";

                                $rs2 = sqlsrv_query($link, $queryDB);

                                while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
                                    if ($fila2["cuota"] == $cuota_desde)
                                        $dias_vcto = $fila1["dias_primer_vcto"];
                                    else
                                        $dias_vcto = 30;

                                    $interes = $saldo * ($tasa_interes / 100.00) * ($dias_vcto / 30.00);

                                    $valor_cuota = $fila2["capital"] + $interes;

                                    sqlsrv_query($link, "insert into ventas_cuotas (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('" . $id_ventadetalle . "', '" . $fila2["cuota"] . "', ADDDATE('" . $fecha_primer_pago . "', INTERVAL ('" . $fila2["cuota"] . "' - '" . $cuota_desde . "') MONTH), '" . round($fila2["capital"]) . "', '" . round($interes) . "', '0', '" . round($valor_cuota) . "', '" . round($valor_cuota) . "')");

                                    $saldo -= $fila2["capital"];
                                }

                                break;

                            case "MDC":
                                $saldo = $fila1["saldo_capital"];

                                if ($cuotas_vendidas == $fila["plazo"])
                                    $saldo = $valor_credito;

                                $tasa_interes = $tasa_venta;

                                $valor_cuota = $saldo * ($tasa_interes / 100) / (1 - pow(1 + ($tasa_interes / 100), -1 * $cuotas_vendidas));

                                $j = 1;

                                for ($j = $cuota_desde; $j <= $fila["plazo"]; $j++) {
                                    $interes = $saldo * $tasa_interes / 100.00;

                                    $capital = $valor_cuota - $interes;

                                    $saldo -= $capital;

                                    if ($j == $fila["plazo"]) {
                                        $valor_cuota += $saldo;

                                        $capital = $valor_cuota - $interes;

                                        $saldo = 0;
                                    }

                                    sqlsrv_query($link, "insert into ventas_cuotas (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('" . $id_ventadetalle . "', '" . $j . "', ADDDATE('" . $fecha_primer_pago . "', INTERVAL ('" . $j . "' - '" . $cuota_desde . "') MONTH), '" . round($capital) . "', '" . round($interes) . "', '0', '" . round($valor_cuota) . "', '" . round($valor_cuota) . "')");
                                }

                                break;
                        }

                        if (sqlsrv_errors($link)) {
                            $mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
                            break;
                        }
                    } else {
                        $mensaje = "Error en la linea [" . $i . "]: Existen mas de dos creditos que coinciden con los parametros\\n";
                        break;
                    }
                } else {
                    $mensaje = "Error en la linea [" . $i . "]: No existe el credito\\n";
                    break;
                }
            }
        }
    }

    if (feof($file)) {
        $mensaje .= "- El archivo de ventas ha sido cargado satisfactoriamente\\n";
    }

    fclose($file);
}

?>
<script>
    alert("<?php echo $mensaje ?>");

    <?php if ($generar_log_cargue) { ?>window.open('log_cargue.php?archivo=<?php echo $archivo ?>&pagaduriabas=<?php echo $_REQUEST["pagaduriabas"] ?>','LOGCARGUE','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');<?php } ?>

    window.location = 'cargarplanos.php';
</script>