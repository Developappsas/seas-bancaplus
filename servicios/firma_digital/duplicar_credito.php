<?php
    include ('../../functions.php');
    include('../../home/porcentajes_seguro.php');

    $link = conectar_utf();

    if(isset($_POST['id_simulacion'])){

        $idSimulacion = $_POST['id_simulacion'];

        $queryArmaSimul = "SELECT * FROM simulaciones WHERE id_simulacion = ".$_POST['id_simulacion'];
        $conArmaSimul = sqlsrv_query($link, $queryArmaSimul, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        $noIncluirSimul = array('id_simulacion', 'formato_digital', 'fecha_radicado', 'reportado_colmena');
        $camposSimul = '';
        $valuesSimul = '';

        if(sqlsrv_num_rows($conArmaSimul) > 0){
            
            $infoSimulacion = sqlsrv_fetch_array($conArmaSimul);
            
            $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by id");

            $j = 0;

            while ($fila1 = sqlsrv_fetch_array($parametros)) {
                $parametro[$j] = $fila1["valor"];

                $j++;
            }

            $cartera_castigada_permitida = $parametro[0];
            $cobertura = $parametro[1];
            $cuota_manejo = $parametro[2];
            $descuento_transferencia = $parametro[3];
            $dias_ajuste = $parametro[4];
            $edad_maxima_administrativos_hombres = $parametro[5];
            $edad_maxima_administrativos_mujeres = $parametro[6];
            $edad_maxima_activos = $parametro[7];
            $edad_maxima_pensionados = $parametro[8];
            $aval = $parametro[9];
            $aval_producto = $parametro[10];
            $plazo_maximo = $parametro[11];
            $plazo_maximo_administrador = $parametro[12];
            $descuento_freelance2 = $parametro[13];
            $descuento_freelance3 = $parametro[14];
            $descuento_producto1 = $parametro[15];
            $iva = $parametro[16];
            $porcentaje_aportes_activos = $parametro[17];
            $porcentaje_comision = $parametro[18];
            $descuento1 = $parametro[19];
            $descuento2 = $parametro[20];
            $descuento3 = $parametro[21];
            $descuento4 = $parametro[22];
            $descuento5 = $parametro[23];
            $descuento6 = $parametro[24];
            $servicio_nube = $parametro[40];

            $porcentaje_aportes_pensionados = $parametro[25];
            $porcentaje_incorporacion = $parametro[26];
            $porcentaje_sobre_util = $parametro[27];
            $porcentaje_sobre_desm1 = $parametro[28];
            $porcentaje_sobre_desm2 = $parametro[29];
            $puntaje_cifin_minimo = $parametro[30];
            $puntaje_datacredito_minimo = $parametro[31];
            $salario_minimo = $parametro[32];
            $seguro = $parametro[33];
            $tasa_efectiva_fondeo = $parametro[34];
            $tasa_interes_maxima = $parametro[35];
            $tasa_interes_a = $parametro[36];
            $tasa_interes_b = $parametro[37];
            $tasa_interes_c = $parametro[38];
            $tasa_usura = $parametro[39];

            $descuento_producto0 = $descuento1;

            $id_comercial = $_SESSION["S_IDUSUARIO"];

            $es_freelance = sqlsrv_query($link, "select * from usuarios where id_usuario = '" . $id_comercial . "' and (freelance = '1' OR outsourcing = '1')" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($es_freelance)) {
                $descuento2 = $descuento_freelance2;
                $descuento3 = $descuento_freelance3;
            }

            $sin_aportes = "0";

            $nro_libranza = "NULL";

            $fecha_llamada_cliente = "NULL";

            $nro_cuenta = "NULL";

            $tipo_cuenta = "NULL";

            $id_banco = "NULL";

            $id_subestado = "NULL";

            $id_caracteristica = "NULL";

            $calificacion = "NULL";

            $dia_confirmacion = "NULL";

            $dia_vencimiento = "NULL";

            $status = "NULL";

            $bloqueo_cuota = "0";

            if ( isset($infoSimulacion["telemercadeo"]) && $infoSimulacion["telemercadeo"] == "1") {
                $infoSimulacion["telemercadeo"] = "1";
            }else{
                $infoSimulacion["telemercadeo"] = "0";
            }

            $existe_en_empleados_creacion = sqlsrv_query($link, "select * from empleados_creacion where cedula = '" . trim(trim($infoSimulacion["cedula"])) . "' AND pagaduria = '" . $infoSimulacion["pagaduria"] . "'" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($existe_en_empleados_creacion)) {
                $fila1 = sqlsrv_fetch_array($existe_en_empleados_creacion);

                if ($fila1["fecha_modificacion"])
                    $empleado_manual = 0;
                else
                    $empleado_manual = 1;
            } else {
                $empleado_manual = 0;
            }

            $existe_recien_creada = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '" . trim($infoSimulacion["cedula"]) . "' AND pagaduria = '" . $infoSimulacion["pagaduria"] . "' AND id_comercial = '" . $id_comercial . "' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '" . $id_comercial . "') AND UNIX_TIMESTAMP(GETDATE()) - UNIX_TIMESTAMP(fecha_creacion) <= 60", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($existe_recien_creada)) {
                $res_existe_recien_creada = sqlsrv_fetch_array($existe_recien_creada);
                $estado = 1;

                echo "<script>function myFunction() { alert('Simulacion guardada exitosamente'); window.location = 'simulaciones.php?descripcion_busqueda=" . trim($infoSimulacion["cedula"]) . "&buscar=1'; } setTimeout(myFunction, 1000)</script>";

                exit;
            }

            $omitir_validacion_30_dias = 1;
            $omitir_validacion_credito_estudio = 1;

            $oficina_ado = 0;
            $oficina_gattaca = 0;

            $query_oficina = sqlsrv_query($link, "SELECT IF(b.ado IS NULL, 0, b.ado) AS ado, IF(b.gattaca IS NULL, 0, b.gattaca) AS gattaca  FROM oficinas_usuarios a JOIN oficinas b ON a.id_oficina = b.id_oficina WHERE a.id_usuario = '" . $id_comercial . "' LIMIT 1", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($query_oficina) > 0) {
                $datos_ofi = sqlsrv_fetch_array($query_oficina);
                $oficina_ado = $datos_ofi["ado"];
                $oficina_gattaca = $datos_ofi["gattaca"];
            }

            $plazo = $plazo_maximo;
            $plazo_maximo_segun_edad = $plazo_maximo;

            $rs1 = sqlsrv_query($link, "select sector from pagadurias where nombre = '" . $infoSimulacion["pagaduria"] . "'");

            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

            $sector = $fila1["sector"];

            $sufijo_sector = '';
            if ($sector == "PRIVADO") {
                $descuento_producto0 = $aval;

                $descuento1 = $aval;

                $descuento_producto1 = $aval_producto;

                $sufijo_sector = "_privado";
            }

            $queryDB = "select ((DATEPART(YEAR , Format('".$infoSimulacion["fecha_nacimiento"]."', 'Y-m-d')) +".$edad_maxima_activos.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_activos.",Format('".$infoSimulacion["fecha_nacimiento"]."', 'Y-m-d'))) - DATEPART(MONTH , GETDATE()))as meses_antes_activos, 
            ((DATEPART(YEAR , Format('".$infoSimulacion["fecha_nacimiento"]."', 'Y-m-d')) +".$edad_maxima_pnsionados.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_pensinado.",Format('".$infoSimulacion["fecha_nacimiento"]."', 'Y-m-d'))) - DATEPART(MONTH , GETDATE())) as meses_antes_pensionados from empleados where cedula = '" . trim($infoSimulacion["cedula"]) . "' AND pagaduria = '" . $infoSimulacion["pagaduria"] . "'";

            $meses_antes_rs = sqlsrv_query($link, $queryDB);

            $fila = sqlsrv_fetch_array($meses_antes_rs);

            $diff_dias_ultimo_mes = date("j", strtotime($infoSimulacion["fecha_nacimiento"])) - date("j", strtotime(date("Y-m-d")));

            if (strtoupper($infoSimulacion["nivel_contratacion"]) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
                $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);
            else
                $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_activos"] : ($fila["meses_antes_activos"] - 1);

            if (strtoupper($infoSimulacion["nivel_contratacion"]) != "PENSIONADO") {
                if ($meses_antes < $plazo_maximo) {
                    $plazo = $meses_antes;
                }
            }

            if ($meses_antes < 0) {
                $plazo = 0;
            }

            if ($meses_antes == 1)
                $meses_antes .= " MES";

            if ($meses_antes > 1)
                $meses_antes .= " MESES";

            if ($meses_antes <= 0)
                $meses_antes = "0";

            $id1 = explode("'0', '", $_SESSION["S_IDUNIDADNEGOCIO"]);

            $id2 = explode("'", $id1[1]);

            $id_unidad_negocio = $id2[0];
            //$id_unidad_negocio = $infoSimulacion["id_unidad_negocio"];

            $rs_tasa = sqlsrv_query($link, "select id_tasa from tasas" . $sufijo_sector . " where plazoi <= '" . $plazo . "' AND plazof >= '" . $plazo . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($rs_tasa)) {
                $fila_tasa = sqlsrv_fetch_array($rs_tasa);

                $queryDB = "select TRIM(t2.tasa_interes) + 0 as tasa_interes, TRIM(t2.descuento1) + 0 as descuento1, TRIM(t2.descuento2) + 0 as descuento2, TRIM(t2.descuento3) + 0 as descuento3 from tasas2" . $sufijo_sector . " as t2 INNER JOIN tasas2_unidades" . $sufijo_sector . " as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '" . $fila_tasa["id_tasa"] . "'";

                $queryDB .= " AND t2u.id_unidad_negocio = '" . $id_unidad_negocio . "'";

                $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";

                if (strtoupper($infoSimulacion["nivel_contratacion"]) == "PENSIONADO")
                    $queryDB .= " OR t2.solo_pensionados = '1'";
                else
                    $queryDB .= " OR t2.solo_activos = '1'";

                $queryDB .= ") order by t2.tasa_interes DESC LIMIT 1";

                $rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($rs_tasa2)) {
                    $fila_tasa2 = sqlsrv_fetch_array($rs_tasa2);

                    $tasa_interes = $fila_tasa2["tasa_interes"];

                    $descuento1 = $fila_tasa2["descuento1"];

                    $descuento2 = $fila_tasa2["descuento2"];

                    $descuento3 = $fila_tasa2["descuento3"];
                } else {
                    $tasa_interes = 0;

                    $descuento1 = 0;

                    $descuento2 = 0;

                    $descuento3 = 0;
                }
            } else {
                $tasa_interes = 0;

                $descuento1 = 0;

                $descuento2 = 0;

                $descuento3 = 0;
            }

            if ($sector == "PRIVADO")
                $descuento3 += $descuento1 * $iva / 100;

            $rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf from unidades_negocio where id_unidad = '" . $id_unidad_negocio . "'");

            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

            if (strtoupper($infoSimulacion["nivel_contratacion"]) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
                if (strtoupper($infoSimulacion["pagaduria"]) == "COLPENSIONES")
                    $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones"];
                else
                    $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados"];
            else
                $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos"];

            if ($plazo)
                $porcentaje_seguro = PorcentajeSeguro($valor_por_millon_seguro, $plazo, $tasa_interes, 0, 0);
            else
                $porcentaje_seguro = 0;

            if (!$fila1["gmf"])
                $descuento4 = 0;


            if ($servicio_nube == '1') {
                $descuento7 = $descuento2 + $descuento3;
                $descuento2 = 0;
                $descuento3 = 0;
            } else {
                $descuento7 = 0;
            }

            //Tasa Comisones
            $id_unidad_negocio_tasa_comision = $id_unidad_negocio;

            if ($id_unidad_negocio_tasa_comision == 4 || $id_unidad_negocio_tasa_comision == 11 || $id_unidad_negocio_tasa_comision == 14 || $id_unidad_negocio_tasa_comision == 19 || $id_unidad_negocio_tasa_comision == 23) {
                $id_unidad_negocio_tasa_comision = 4; //Fianti
            } else if ($id_unidad_negocio_tasa_comision == 6 || $id_unidad_negocio_tasa_comision == 15 || $id_unidad_negocio_tasa_comision == 21) {
                $id_unidad_negocio_tasa_comision = 6; //Atraccion
            } else if ($id_unidad_negocio_tasa_comision == 2 || $id_unidad_negocio_tasa_comision == 12 || $id_unidad_negocio_tasa_comision == 16 || $id_unidad_negocio_tasa_comision == 22) {
                $id_unidad_negocio_tasa_comision = 2; //Salvamento
            } else if ($id_unidad_negocio_tasa_comision == 1 || $id_unidad_negocio_tasa_comision == 10 || $id_unidad_negocio_tasa_comision == 17 || $id_unidad_negocio_tasa_comision == 20) {
                $id_unidad_negocio_tasa_comision = 1; //Kredit
            }

            $sqlTasaComision = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = " . $id_unidad_negocio_tasa_comision . " AND a.tasa = $tasa_interes AND GETDATE() >= a.fecha_inicio AND (a.fecha_fin IN('01-01-0001') OR a.fecha_fin IS NULL OR a.fecha_fin <= GETDATE())";

            $queryTasaComision = sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (@sqlsrv_num_rows($queryTasaComision) > 0) {
                $respTasaComision = sqlsrv_fetch_array($queryTasaComision);
                $id_tasa_comision = $respTasaComision["id_tasa_comision"];
                $tipo_tasa_comision = $respTasaComision["id_tipo"];
            } else {
                $id_tasa_comision = 0;
                $tipo_tasa_comision = 0;
            }

            $queryInsert = "INSERT into simulaciones (servicio_nube,id_comercial, id_oficina, telemercadeo, fecha_estudio, cedula, nombre, pagaduria, pa, ciudad, institucion, nivel_educativo, fecha_nacimiento, telefono, meses_antes_65, fecha_inicio_labor, medio_contacto, salario_basico, adicionales, bonificacion, total_ingresos, aportes, otros_aportes, total_aportes, total_egresos, salario_minimo, ingresos_menos_aportes, salario_libre, nivel_contratacion, embargo_actual, historial_embargos, embargo_alimentos, embargo_centrales, descuentos_por_fuera, cartera_mora, valor_cartera_mora, puntaje_datacredito, puntaje_cifin, valor_descuentos_por_fuera, id_unidad_negocio, tasa_interes, plazo, tipo_credito, suma_al_presupuesto, total_cuota, total_valor_pagar, retanqueo1_libranza, retanqueo1_cuota, retanqueo1_valor, retanqueo2_libranza, retanqueo2_cuota, retanqueo2_valor, retanqueo3_libranza, retanqueo3_cuota, retanqueo3_valor, retanqueo_total_cuota, retanqueo_total, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso, desembolso_cliente, decision, decision_sistema, valor_visado, bloqueo_cuota, bloqueo_cuota_valor, fecha_llamada_cliente, nro_cuenta, tipo_cuenta, id_banco, id_subestado, id_caracteristica, calificacion, dia_confirmacion, dia_vencimiento, status, valor_credito, resumen_ingreso, incor, comision, utilidad_neta, sobre_el_credito, estado, tipo_producto, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6,descuento7, descuento_transferencia, porcentaje_seguro, valor_por_millon_seguro, porcentaje_extraprima, sin_aportes, empleado_manual, iva, frente_al_cliente, usuario_radicado, fecha_radicado, usuario_creacion, fecha_creacion, id_tasa_comision, id_tipo_tasa_comision, proposito_credito) values ('" . $servicio_nube . "','" . $id_comercial . "', (select top 1 id_oficina from oficinas_usuarios where id_usuario = '" . $id_comercial . "'), '" . $infoSimulacion["telemercadeo"] . "', GETDATE(), '" . trim($infoSimulacion["cedula"]) . "', '" . strtoupper(trim($infoSimulacion['nombre'])) . "', '" . $infoSimulacion["pagaduria"] . "', (select pa from pagaduriaspa where pagaduria = '" . $infoSimulacion["pagaduria"] . "'), '" . strtoupper($infoSimulacion["ciudad"]) . "', '" . strtoupper($infoSimulacion["institucion"]) . "', '', '" . $infoSimulacion["fecha_nacimiento"] . "', '" . $infoSimulacion["telefono"] . "', '" . $meses_antes . "', '" . $infoSimulacion["fecha_inicio_labor"] . "', '" . $infoSimulacion["medio_contacto"] . "', '0', '0', '0', '0', '0', '0', '0', '0', (select salario_minimo from salario_minimo where ano = YEAR(GETDATE())), '0', '0', '" . $infoSimulacion["nivel_contratacion"] . "', 'NO', '0', 'NO', 'NO', 'NO', 'NO', '0', '-1', '-1', '0', '" . $id_unidad_negocio . "', '" . $tasa_interes . "', '" . $plazo . "', 'CREDITO NORMAL', '0', '0', '0', '', '0', '0', '', '0', '0', '', '0', '0', '0', '0', 'CCC', '0', '" . (-1.00 * $descuento_transferencia) . "', '0', '" . (-1.00 * $descuento_transferencia) . "', '0', '" . (-1.00 * $descuento_transferencia) . "', '0', '" . (-1.00 * $descuento_transferencia) . "', '" . (-1.00 * $descuento_transferencia) . "', '" . $label_viable . "', '" . $label_negado . "', '0', '" . $bloqueo_cuota . "', '0', " . $fecha_llamada_cliente . ", " . $nro_cuenta . ", " . $tipo_cuenta . ", " . $id_banco . ", " . $id_subestado . ", " . $id_caracteristica . ", " . $calificacion . ", " . $dia_confirmacion . ", " . $dia_vencimiento . ", " . $status . ", '0', '0', '0', '0', '0', '0', 'ING', '0', '" . $descuento1 . "', '" . $descuento2 . "', '" . $descuento3 . "', '" . $descuento4 . "', '" . $descuento5 . "', '" . $descuento6 . "', '" . $descuento7 . "', '" . $descuento_transferencia . "', '" . $porcentaje_seguro . "', '" . $valor_por_millon_seguro . "', '0', '" . $sin_aportes . "', '" . $empleado_manual . "', '" . $iva . "', '" . $infoSimulacion["frente_al_cliente"] . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE(), '" . $_SESSION["S_LOGIN"] . "', GETDATE(), $id_tasa_comision, '" . $tipo_tasa_comision . "', '" . $infoSimulacion["proposito_credito"] . "')";

            if (sqlsrv_query($link, $queryInsert)) {


                $id=sqlsrv_query($link, "SELECT SCOPE_IDENTITY() as idS;");
                $id = sqlsrv_fetch_array($id, SQLSRV_FETCH_ASSOC) 
                $id_simul = $id['idS'];
                $idSimulNueva = $id['idS'];

                
                sqlsrv_query($link, "UPDATE simulaciones SET solicitar_firma = 2 WHERE id_simulacion = '".$idSimulacion."'");

               sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) VALUES ($idSimulNueva,197,197,CURRENT_TIMESTAMP(),'s',1,99);");

                $descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '" . $infoSimulacion["pagaduria"] . "' and estado = '1' order by id_descuento");

                while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
                    sqlsrv_query($link, "insert into simulaciones_descuentos (id_simulacion, id_descuento, porcentaje) values ('" . $id_simul . "', '" . $fila1["id_descuento"] . "', '" . $fila1["porcentaje"] . "')");
                }

                $queryArmaSolicitud = "SELECT * FROM solicitud WHERE id_simulacion = ".$_POST['id_simulacion'];
                $conArmaSolicitud= sqlsrv_query($link, $queryArmaSolicitud);

                $noIncluirSolicitud = array('id_simulacion');
                $camposSolicitud = '';
                $valuesSolicitud = '';

                if(sqlsrv_num_rows($conArmaSolicitud) > 0){
                    
                    $infoSolicitud = sqlsrv_fetch_array($conArmaSolicitud);

                    $camposSolicitud = 'id_simulacion';
                    $valuesSolicitud = "'".$idSimulNueva."'";
                    
                    foreach ($infoSolicitud as $key => $value) {
                        
                        if (!in_array($key, $noIncluirSolicitud)){
                            if($value != '' && $value !== null){    
                                $camposSolicitud .= ', ' . $key;
                                $valuesSolicitud .= ", '" . $value."'";
                            }
                        }
                    }

                    sqlsrv_query($link, "INSERT INTO solicitud (".$camposSolicitud.") VALUES(".$valuesSolicitud.")");
                }else{
                    sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, email, sexo) values ('" . $id_simul . "', '" . trim($infoSimulacion["cedula"]) . "', '" . $infoSimulacion["fecha_nacimiento"] . "', '" . ($infoSimulacion["telefono"]) . "', '" . ($infoSimulacion["celular"]) . "', '" . (strtoupper($infoSimulacion["direccion"])) . "', '" . ($infoSimulacion["email"]) . "', '" . (strtoupper($infoSimulacion["sexo"])) . "')");
                }

                $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'id_simulacion' => $idSimulNueva, 'error' => sqlsrv_error($link));
            }else{
                $data = array('code' => 500, 'mensaje' => 'No se pudo Reprospectar el credito.', 'info' => sqlsrv_error($link) . ". Query: " . $queryInsert);
            }
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>