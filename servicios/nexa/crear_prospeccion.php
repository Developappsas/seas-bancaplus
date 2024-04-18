<?php 
include ('../../functions.php');
include ('../../cors.php');
include ('../../home/porcentajes_seguro.php'); 
header("Content-Type: application/json; charset=utf-8");
include ('porcentajes_seguro.php'); 
session_start();
$link = conectar_utf();
$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input,true);

if ($params["Action"]=="CREAR_PROSPECCION"){

    $val1=0;$val2=0;$val3=0;
    $val4=0;$val5=0;$val6=0;
    $val7=0;$val8=0;$val9=0;
    $val10=0;
    $mensaje="";

    if (!isset($params["nombre"])){    
        $val1=1;
        $mensaje.="Debe ingresar nombres. ";
    }else{            
        $nombres=trim($params["nombre"]);
    }
    
    if (!isset($params["apellido"])){
        $val2=1;
        $mensaje.="Debe ingresar apellidos. ";
    }else{
        $apellidos=trim($params["apellido"]);            
    }
    
    if (!isset($params["institucion"])){
        $mensaje.="Debe ingresar institucion. ";
        $val3=1;
    }else{
        $institucion=trim($params["institucion"]);
    }

    if (!isset($params["cedula"])){
        $mensaje.="Debe ingresar cedula. ";
        $val4=1;
    }else{
        $cedula=trim($params["cedula"]);
    }

    if (!isset($params["telefono"])){            
        $mensaje.="Debe ingresar telefono. ";
        $val5=1;
    }else{
        $telefono=trim($params["celular"]);
    }

    if (!isset($params["direccion"])){
        $mensaje.="Debe ingresar direccion. ";
        $val6=1;
    }else{
        $direccion=trim($params["direccion"]);
    }
    
    if (!isset($params["correo"])){            
        $mensaje.="Debe ingresar correo. ";
        $val7=1;
    }else{
        $correo=trim($params["correo"]);
    }

    if (!isset($params["fecha_nombramiento"])){
        $mensaje.="Debe ingresar fecha nombramiento. ";
        $val8=1;
    }else{
        $params["fecha_nombramiento"] = str_replace('/', '-', $params["fecha_nombramiento"]);
        $fecha_nombramiento = date("Y-m-d", strtotime($params["fecha_nombramiento"]));
    }

    if (!isset($params["fecha_nacimiento"])){
        $mensaje.="Debe ingresar fecha nacimiento. ";
        $val9=1;
    }else{
        $params["fecha_nacimiento"] = trim($params["fecha_nacimiento"]);
        $params["fecha_nacimiento"] = str_replace('/', '-', $params["fecha_nacimiento"]);
        $fecha_nacimiento = date("Y-m-d", strtotime($params["fecha_nacimiento"]));
    }

    if (!isset($params["ciudad"])){
        $mensaje.="Debe ingresar ciudad. ";
        $val10=1;
    }else{
        $ciudad=trim($params["ciudad"]);
    }

    if (!isset($params["nivel_contratacion"])){
        $mensaje.="Debe ingresar nivel_contratacion. ";
        $val10=1;
    }else{
        $nivel_contratacion=strtoupper(trim($params["nivel_contratacion"]));
    }

    if (!isset($params["grado"])){
        $mensaje.="Debe ingresar grado. ";
        $val10=1;
    }else{
        $grado=trim($params["grado"]);
    }

    if (!isset($params["cargo"])){
        $mensaje.="Debe ingresar cargo. ";
        $val10=1;
    }else{
        $cargo=trim($params["cargo"]);
    }

    if (!isset($params["genero"])){
        $mensaje.="Debe ingresar genero. ";
        $val10=1;
    }else{
        $genero=trim($params["genero"]);
    }

    if (!isset($params["tipo_cargo"])){
        $mensaje.="Debe ingresar tipo de cargo. ";
        $val10=1;
    }else{
        $tipo_cargo=trim($params["tipo_cargo"]);
    }

    if (!isset($params["salario_base"])){
        $mensaje.="Debe ingresar salario base. ";
        $val10=1;
    }else{
        $salario_base=trim($params["salario_base"]);
    }

    $id_tasa=$params["id_tasa"];
    $id_tasa2=$params["id_tasa2"];
    $valor_posible_cuota=$params["valor_posible_cuota"];
    $valor_posible_credito=$params["valor_posible_credito"];
    $aportes_posible=$params["aportes"];
    $plazo_posible=$params["plazo"];
    $otros_descuentos=$params["otros_descuentos"];


    //Compras de cartera
    $carteras_cc=$params["carteras_cc"];
    $valores_cc=$params["valores_cc"];
    $se_compra_cc=$params["se_compra_cc"];

    if ($val1==1 || $val2==1 || $val3==1 || $val4==1 || $val5==1 || $val6==1 || $val7==1 || $val8==1 || $val9==1 || $val10==1){
        header("HTTP/2.0 200 OK");
        $data = array("code"=>"403","message"=>$mensaje,"data" =>null);
        echo json_encode($data);      
    }
    else{
        
        crearProspeccion($nombres,$apellidos,$institucion,$cedula,$telefono,$direccion,$correo,$fecha_nombramiento,$fecha_nacimiento,$ciudad,$carteras_cc,$valores_cc,$se_compra_cc,$grado,$cargo,$genero,$nivel_contratacion,$tipo_cargo,$salario_base,$id_tasa,$id_tasa2,$valor_posible_cuota,$valor_posible_credito,$aportes_posible,$plazo_posible,$otros_descuentos);
    }

}else{
    header("HTTP/2.0 200 OK");
    $data = array("code"=>"404","message"=>"Servicio consultado no existe","data" =>null);
    echo json_encode($data);
}

function crearProspeccion($nombres,$apellidos,$institucion,$cedula,$telefono,$direccion,$correo,$fecha_nombramiento,$fecha_nacimiento,$ciudad,$carteras_cc,$valores_cc,$se_compra_cc,$grado,$cargo,$genero,$nivel_contratacion,$tipo_cargo,$salario_base,$id_tasa,$id_tasa2,$valor_posible_cuota,$valor_posible_credito,$aportes_posible,$plazo_posible,$otros_descuentos){

    global $link;
    $estado=0;
    $telemercadeo=0;
    $pagaduria="GENERICA";
    $medio_contacto="BASE DE DATOS";
    $query = "SELECT nombre FROM empleados WHERE cedula = '"
            . $cedula . "' AND pagaduria = '"
            . $pagaduria . "'";
    $resultado = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

    if (sqlsrv_num_rows($resultado) > 0) {
        // el registro no existe. Procedemos a ingresarlo
        $query = "INSERT INTO empleados VALUES ("
                . "'" . $cedula . "', "
                . "'" . strtoupper(trim($apellidos) . ' ' . trim($nombres)) . "', "
                . "'" . $pagaduria . "', "
                . "'" . strtoupper($institucion) . "', "
                . "'', "
                . "'', "
                . "'0', "
                . "'0', "
                . "'0', "
                . "'0', "
                . "'', "
                . "'" . strtoupper($correo) . "', "
                . "'" . $telefono . "', "
                . "'" . $correo . "', "
                . "'" . $fecha_nacimiento . "', "
                . "'" . strtoupper($nivel_contratacion) . "', "
                . "'', "
                . "'" . strtoupper($ciudad) . "', "
                . "'" . '1' . "', "
                . "'', "
                . "'" . strtoupper($ciudad) . "', "
                . "'" . $fecha_nombramiento . "', "
                . "'" . $medio_contacto . "')";
        
        sqlsrv_query($link, $query);
        
        $query = "INSERT INTO empleados_creacion ("
                . "cedula, "
                . "pagaduria, "
                . "id_usuario, "
                . "fecha_creacion) VALUES ("
                . "'" . $cedula . "', "
                . "'" . $pagaduria . "', "
                . "'1', "
                . "GETDATE())";
        sqlsrv_query($link, $query);
    }

    $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by codigo");
    $j = 0;

    while ($fila1 = sqlsrv_fetch_array($parametros)){
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

    $es_freelance = sqlsrv_query($link, "select * from usuarios where id_usuario = '".$id_comercial."' and (freelance = '1' OR outsourcing = '1')");

    if (sqlsrv_num_rows($es_freelance)){
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

    if ($telemercadeo != "1"){
        $telemercadeo = "0";
    }

    $existe_en_empleados_creacion = sqlsrv_query($link, "select * from empleados_creacion where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."'");

    if (sqlsrv_num_rows($existe_en_empleados_creacion)){
        $fila1 = sqlsrv_fetch_array($existe_en_empleados_creacion);
        
        if ($fila1["fecha_modificacion"])
            $empleado_manual = 0;
        else
            $empleado_manual = 1;
    }
    else{
        $empleado_manual = 0;
    }

    $existe_recien_creada = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND id_comercial = '".$id_comercial."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$id_comercial."') AND DATEDIFF(SECOND,'1970-01-01', GETDATE()) - DATEDIFF(SECOND,'1970-01-01', fecha_creacion()) <= 60");

    if (sqlsrv_num_rows($existe_recien_creada)){

        $res_existe_recien_creada=sqlsrv_fetch_array($existe_recien_creada);
        $estado=1;

        header("HTTP/2.0 200 OK");
        $data = array("code"=>"500","message"=>"Existe una Simulación Recien Creada");
        echo json_encode($data);
        
        exit;
    }
        
    $omitir_validacion_30_dias = 1;

    $existe_simulacion = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND DATEDIFF(day, GETDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST')");

    if (sqlsrv_num_rows($existe_simulacion)){

        $existe_simulacion2 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND DATEDIFF(DAY, GETDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST') AND id_comercial = '".$id_comercial."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$id_comercial."')");
        
        if (!sqlsrv_num_rows($existe_simulacion2))
            $omitir_validacion_30_dias = 0;
    }

    if (!sqlsrv_num_rows($existe_simulacion) || $omitir_validacion_30_dias){

        $plazo = $plazo_maximo;
        $plazo_maximo_segun_edad = $plazo_maximo;
        
        $rs1 = sqlsrv_query($link, "select sector from pagadurias where nombre = '".$pagaduria."'");
        $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
        $sector = $fila1["sector"];
        
        if ($sector == "PRIVADO"){
            $descuento_producto0 = $aval;
            $descuento1 = $aval;
            $descuento_producto1 = $aval_producto;
            $sufijo_sector = "_privado";
        }
        
        $estado=1;

        $queryDB = "SELECT ((DATEPART(YEAR , '".$fechaNacimiento."') +".$edad_maxima_activos.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_activos.",'".$fechaNacimiento."')) - DATEPART(MONTH , GETDATE()))as meses_antes_activos, 
        ((DATEPART(YEAR , '".$fechaNacimiento."') +".$edad_maxima_pnsionados.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_pensinado.",'".$fechaNacimiento."')) - DATEPART(MONTH , GETDATE())) as meses_antes_pensionados from empleados where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."'";
        
        $meses_antes_rs = sqlsrv_query($link, $queryDB);
        
        $fila = sqlsrv_fetch_array($meses_antes_rs);
        
        $diff_dias_ultimo_mes = date("j", strtotime($fecha_nacimiento)) - date("j", strtotime(date("Y-m-d")));
        
        if (strtoupper($nivel_contratacion) == "PENSIONADO")
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);
        else
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_activos"] : ($fila["meses_antes_activos"] - 1);
        
        if (strtoupper($nivel_contratacion) != "PENSIONADO"){
            if ($meses_antes < $plazo_maximo){
                
                if($plazo_posible > 0){
                    $plazo = $plazo_posible;
                }else{
                    $plazo = $meses_antes;
                }
            }
        }

        if ($meses_antes < 0){
            $plazo = 0;
        }
        
        if ($meses_antes == 1)
            $meses_antes .= " MES";
        
        if ($meses_antes > 1)
            $meses_antes .= " MESES";
        
        if ($meses_antes <= 0)
            $meses_antes = "0";
        
        //$id1 = explode("'0', '", $_SESSION["S_IDUNIDADNEGOCIO"]);
        //$id2 = explode("'", $id1[1]);
        $id_unidad_negocio = 1;
        //$id_unidad_negocio = $_REQUEST["id_unidad_negocio"];

        if($id_tasa <= 0){

            $rs_tasa = sqlsrv_query($link, "select id_tasa from tasas".$sufijo_sector." where plazoi <= '".$plazo."'  AND plazof >= '".$plazo."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($rs_tasa)){
                $fila_tasa = sqlsrv_fetch_array($rs_tasa);
                $id_tasa = $fila_tasa["id_tasa"];
            }
        }
        
        $queryDB = "select TRIM(t2.tasa_interes) + 0 as tasa_interes, TRIM(t2.descuento1) + 0 as descuento1, TRIM(t2.descuento2) + 0 as descuento2, TRIM(t2.descuento3) + 0 as descuento3 from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$id_tasa."'";

        $queryDB .= " AND t2.id_tasa2 = ".$id_tasa2;

        $queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
        $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
        
        if (strtoupper($nivel_contratacion) == "PENSIONADO")
            $queryDB .= " OR t2.solo_pensionados = '1'";
        else
            $queryDB .= " OR t2.solo_activos = '1'";
        
        $queryDB .= ") order by t2.tasa_interes DESC LIMIT 1";
        
        $rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        
        if (sqlsrv_num_rows($rs_tasa2)){
            $fila_tasa2 = sqlsrv_fetch_array($rs_tasa2);
            $tasa_interes = $fila_tasa2["tasa_interes"];
            $descuento1 = $fila_tasa2["descuento1"];
            $descuento2 = $fila_tasa2["descuento2"];
            $descuento3 = $fila_tasa2["descuento3"];
        }
        else{
            $tasa_interes = 0;
            $descuento1 = 0;
            $descuento2 = 0;
            $descuento3 = 0;
        } 

        if ($sector == "PRIVADO")
            $descuento3 += $descuento1 * $iva / 100;
        
        $rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf from unidades_negocio where id_unidad = '".$id_unidad_negocio."'");

        $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

        if (strtoupper($nivel_contratacion) == "PENSIONADO")
            if (strtoupper($pagaduria) == "COLPENSIONES")
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

        $total_aportes = $otros_descuentos + $aportes_posible;

        $queryinsert = "insert into simulaciones (id_comercial, id_oficina, telemercadeo, fecha_estudio, cedula, nombre, pagaduria, pa, ciudad, institucion, nivel_educativo, fecha_nacimiento, telefono, meses_antes_65, fecha_inicio_labor, medio_contacto, salario_basico, adicionales, bonificacion, total_ingresos, aportes, otros_aportes, total_aportes, total_egresos, salario_minimo, ingresos_menos_aportes, salario_libre, nivel_contratacion, embargo_actual, historial_embargos, embargo_alimentos, embargo_centrales, descuentos_por_fuera, cartera_mora, valor_cartera_mora, puntaje_datacredito, puntaje_cifin, valor_descuentos_por_fuera, id_unidad_negocio, tasa_interes, plazo, tipo_credito, suma_al_presupuesto, total_cuota, total_valor_pagar, retanqueo1_libranza, retanqueo1_cuota, retanqueo1_valor, retanqueo2_libranza, retanqueo2_cuota, retanqueo2_valor, retanqueo3_libranza, retanqueo3_cuota, retanqueo3_valor, retanqueo_total_cuota, retanqueo_total, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso, desembolso_cliente, decision, decision_sistema, valor_visado, bloqueo_cuota, bloqueo_cuota_valor, fecha_llamada_cliente, nro_cuenta, tipo_cuenta, id_banco, id_subestado, id_caracteristica, calificacion, dia_confirmacion, dia_vencimiento, status, valor_credito, resumen_ingreso, incor, comision, utilidad_neta, sobre_el_credito, estado, tipo_producto, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6, descuento_transferencia, porcentaje_seguro, valor_por_millon_seguro, porcentaje_extraprima, sin_aportes, empleado_manual, iva, frente_al_cliente, usuario_radicado, fecha_radicado, usuario_creacion, fecha_creacion) values ('".$id_comercial."', (select TOP 1 id_oficina from oficinas_usuarios where id_usuario = '".$id_comercial."'), '".$telemercadeo."', GETDATE(), '".$cedula."', '".strtoupper(trim($apellidos).' '.trim($nombres))."', '".$pagaduria."', (select pa from pagaduriaspa where pagaduria = '".$pagaduria."'), '".strtoupper($ciudad)."', '".strtoupper($institucion)."', '', '".$fecha_nacimiento."', '".$telefono."', '".$meses_antes."', '".$fecha_nombramiento."', '".$medio_contacto."', '".$salario_base."', '0', '0', '".$salario_base."', '".$aportes_posible."', '0', '".$aportes_posible."', '".$total_aportes."', (select salario_minimo from salario_minimo where ano = YEAR(GETDATE())), '0', '0', '".$nivel_contratacion."', 'NO', '0', 'NO', 'NO', 'NO', 'NO', '0', '-1', '-1', '0', '".$id_unidad_negocio."', '".$tasa_interes."', '".$plazo."', 'CREDITO NORMAL', '0', '0', '0', '', '0', '0', '', '0', '0', '', '0', '0', '0', '0', 'CSO', '0', '".(-1.00 * $descuento_transferencia)."', '0', '".(-1.00 * $descuento_transferencia)."', '0', '".(-1.00 * $descuento_transferencia)."', '".$valor_posible_cuota."', '".(-1.00 * $descuento_transferencia)."', '".(-1.00 * $descuento_transferencia)."', '".$label_viable."', '".$label_negado."', '0', '".$bloqueo_cuota."', '0', ".$fecha_llamada_cliente.", ".$nro_cuenta.", ".$tipo_cuenta.", ".$id_banco.", ".$id_subestado.", ".$id_caracteristica.", ".$calificacion.", ".$dia_confirmacion.", ".$dia_vencimiento.", ".$status.", ".$valor_posible_credito.", '0', '0', '0', '0', '0', 'ING', '0', '".$descuento1."', '".$descuento2."', '".$descuento3."', '".$descuento4."', '".$descuento5."', '".$descuento6."', '".$descuento_transferencia."', '".$porcentaje_seguro."', '".$valor_por_millon_seguro."', '0', '".$sin_aportes."', '".$empleado_manual."', '".$iva."', 'NO', 'nexa', GETDATE, 'nexa', GETDATE())";

        if(sqlsrv_query($link, $queryinsert)){

            $id_simul = sqlsrv_insert_id($link);

            if(is_numeric($id_simul)){
            
                sqlsrv_query($link,"INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) VALUES ($id_simul,197,197,CURRENT_TIMESTAMP(),'s',1,20);");

                sqlsrv_query($link, "INSERT INTO solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, email) values ('".$id_simul."', '".$cedula."', '".$fecha_nacimiento."', '".($telefono)."', '".($telefono)."', '".(strtoupper($direccion))."', '".($correo)."')");

                $descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$pagaduria."' and estado = '1' order by id_descuento");

                while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
                    sqlsrv_query($link, "insert into simulaciones_descuentos (id_simulacion, id_descuento, porcentaje) values ('".$id_simul."', '".$fila1["id_descuento"]."', '".$fila1["porcentaje"]."')");
                }
              
                for ($i=0; $i < count($carteras_cc); $i++) { 
                    $valores_cc[$i];
                    $se_compra_cc[$i];
                    sqlsrv_query($link, "insert into simulaciones_comprascartera (id_simulacion, consecutivo, entidad, cuota, se_compra, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".($i+1)."', '".trim($carteras_cc[$i])."', '".str_replace(",", "", $valores_cc[$i])."', '".$se_compra_cc[$i]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
                }

                header("HTTP/2.0 200 OK");
                $data = array("code"=>"200","message"=>"Guardado Satisfactoriamente", "probar" => $queryinsert, "data" =>$id_simul);
                echo json_encode($data);
            }else{
                header("HTTP/2.0 200 OK");
                $data = array("code"=>"500","message"=>"Error al crear la prospeccion");
                echo json_encode($data);
            }
        }else{
            header("HTTP/2.0 200 OK");
            $data = array("code"=>"500","message"=>"Error al crear la prospeccion");
            echo json_encode($data);
        }
    }else{
        header("HTTP/2.0 200 OK");
        $data = array("code"=>"403","message"=>"Existe un credito con estos datos en menos de 30 dias");
        echo json_encode($data);   
    }
}
?>