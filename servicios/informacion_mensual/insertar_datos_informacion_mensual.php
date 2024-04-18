<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 libxml_use_internal_errors(true);
 require_once("../cors.php");
 require_once("../../functions.php");
 $link = conectar_utf();

if(!empty($_POST['datos'])){
    if($_POST["hoja"]=="MOVIMIENTO"){

        $insert_movimientos_contables="insert into movimientos_contables (
        id_archivo,
        cdp_numero_credito ,
        cdp_id_deudor ,
        cdp_id_pagador ,
        ic_causaciones_intereses_corriente  ,
        ic_causaciones_intereses_mora  ,
        ic_causacion_seguros  ,
        ic_causaciones_otros_conceptos  ,
        iap_fecha_transaccion  ,
        iap_fecha_pago  ,
        iap_valor_moviemiento_total  ,
        iap_valor_imputado_honorarios  ,
        iap_valor_imputado_judiciales  ,
        iap_valor_imputado_seguro_vida  ,
        iap_valor_imputado_mora_seguros  ,
        iap_valor_imputado_mora  ,
        iap_valor_imputado_intereses_corriente  ,
        iap_valor_imputado_capital  ,
        iap_valor_imputado_abono_deferido  ,
        iap_saldo_favor  ,
        iap_tipo_transaccion ,
        iap_indicador_abono_extraordinario  ,
        iap_indicador_cancelacion)value(".$_POST['id_archivo'].",".$_POST['datos']["Número de crédito"].",".$_POST['datos']["Número de identificación del deudor"].", ".$_POST['datos']["Número de identificación del pagador"].", '".$_POST['datos']["Causacion intereses corriente"]."', '".$_POST['datos']["Causacion intereses mora"]."', '".$_POST['datos']["Causacion seguros"]."', '".$_POST['datos']["Causacion otros conceptos"]."', '".date("d-m-Y", mktime(0, 0, 0, 1,$_POST['datos']["Fecha de aplicación de la transacción (entrada al sistema)"]- 1, 1900))."', '".date("d-m-Y", mktime(0, 0, 0, 1,$_POST['datos']["Fecha de pago"]- 1, 1900))."','".$_POST['datos']["Valor  total del movimiento"]."', '".$_POST['datos']["Valor imputado a honorarios"]."', '".$_POST['datos']["Valor imputado gastos judiciales"]."','".$_POST['datos']["Valor imputado a seguros de vida"]."', '".$_POST['datos']["Valor movimiento imputado a intereses de mora sobre seguros"]."', '".$_POST['datos']["Valor movimiento imputado a intereses de mora en pesos"]."', '".$_POST['datos']["Valor movimiento imputado a intereses corrientes en pesos"]."', '".$_POST['datos']["Valor movimiento imputado a capital en pesos"]."', '".$_POST['datos']["Valor imputado Abono Diferido en pesos"]."', '".$_POST['datos']["Saldo a favor en pesos"]."', '".$_POST['datos']["Tipo de transacción"]."', '".intval($_POST['datos']["Indicador Abono Extraordinario"])."', '".intval($_POST['datos']["Indicador de cancelación"])."'  )";
           
        $ejecutar_insert = sqlsrv_query($link, $insert_movimientos_contables);
        if($ejecutar_insert){
            $exitoso = 1;
        }else{
            $exitoso = 0;
        }

    }else if($_POST["hoja"]=="COMPRA Y SALDOS MENSUALES"){

        $insert_compra_saldos_mensuales = "insert into compra_saldos_mensuales(
            id_archivo,
            id_numero_credito ,
            id_numero_identificacion_titular ,
            id_nombres_deudor ,
            id_apellidos_deudor ,
            id_ciudad_radicacion ,
            id_fecha_nacimiento ,
            id_salario_originacion ,
            ip_id_pagaduria ,
            ip_nombre_pagaduria ,
            ifc_fecha_desembolso ,
            ifc_plazo_credito ,
            ifc_fecha_vencimiento_final  ,
            ifc_plazo_restante, 
            ifc_tasa_interes ,
            ifc_valor_desemboslo ,
            ifc_valor_cuota_corriente, 
            ifc_valor_cuota_mesual ,
            ifc_dia_corte_credito,
            ifc_sistema_amortizacion ,
            se_saldo_capital_total ,
            se_saldo_capital_vencido ,
            se_saldo_intereses_corrientes_total,
            se_saldo_intereses_corrientes_vencidos ,
            se_saldo_intereses_mora ,
            se_saldo_intereses_corrientes_mora ,
            se_saldo_seguro_vida ,
            se_saldos_seguros_fianzas ,
            se_saldo_gastos_legales ,
            se_saldo_otros_conceptos ,
            se_total_deuda ,
            se_saldo_abono_diferido ,
            se_altura_mora ,
            se_periodo_gracia, 
            se_calificacion_credito, 
            a_compañia_aseguradora, 
            a_tipo_seguro_vida, 
            dias_mora
            ) value(".$_POST["id_archivo"].",
            '".$_POST['datos']["Número de crédito"]."',
            '".$_POST['datos']["Número de identificación"]."',
            '".$_POST['datos']["Nombres del Deudor"]."',
            '".$_POST['datos']["Apellidos del Deudor"]."' ,
            '".$_POST['datos']["Ciudad de Radicación"]."',
            '".date("d-m-Y", mktime(0, 0, 0, 1,$_POST['datos']["Fecha de nacimiento"] - 1, 1900))."',
            '".$_POST['datos']["Salario al momento de la originación"]."',
            '".$_POST['datos']["Identificación de la pagaduría"]."',
            '".$_POST['datos']["Nombre de la Pagaduría"]."',
            '".$_POST['datos']["Fecha de desembolso"]."',
            '".$_POST['datos']["Plazo del crédito"]."','".
            date("d-m-Y", mktime(0, 0, 0, 1,$_POST['datos']["Fecha de vencimiento final"] - 1, 1900))."',
            '".$_POST['datos']["Plazo restante"]."',
            '".$_POST['datos']["Tasa de interés"]."',
            '".$_POST['datos']["Valor del desembolso"]."',
            '".$_POST['datos']["Valor de la cuota corriente"]."' ,
            '".$_POST['datos']["Valor cuota mensual"]."',
            '".date("d-m-Y", mktime(0, 0, 0, 1,$_POST['datos']["Día de corte del crédito"] - 1, 1900))."',
            ".$_POST['datos']["Sistema de Amortización"].",
            '".$_POST['datos']["Saldo de capital total en pesos"]."',
            '".$_POST['datos']["Saldo de capital vencido en pesos"]."',
            '".$_POST['datos']["Saldo de intereses corrientes total en pesos"]."',
            '".$_POST['datos']["Saldo de intereses corrientes vencidos en pesos"]."',
            '".$_POST['datos']["Saldo de intereses de mora total en pesos"]."',
            '".$_POST['datos']["Saldo de intereses de corrientes y de mora en cuentas de orden
            (intereses con mora superior a 2 meses)"]."',
            '".$_POST['datos']["Saldo seguro de vida"]."',
            '".$_POST['datos']["Saldo de otros seguros y fianzas"]."',
            '".$_POST['datos']["Saldo por Gastos legales"]."',
            '".$_POST['datos']["Saldo por otros conceptos"]."',
            '".$_POST['datos']["Saldo total de la deuda en pesos"]."',
            '".$_POST['datos']["Saldo abono diferido en pesos"]."',
            '".$_POST['datos']["Altura de mora"]."',
            '".$_POST['datos']['Periodos de gracia']."',
            '".$_POST['datos']["Calificación del crédito"]."',
            '".$_POST['datos']['Compañía aseguradora']."',
            ".$_POST['datos']["Tipo de seguro de vida"].",
            ".intval($_POST['datos']["No. de días de mora"]).")";

        
            $ejecutar_insert = sqlsrv_query($link, $insert_compra_saldos_mensuales);
            if($ejecutar_insert){
                $exitoso = 1;
            }else{
                $exitoso = 0;
            }

    }
    if($exitoso ==0){
        $resultado=array("estado"=>300, "mensaje"=>"Lectura de datos fallida", "error"=>sqlsrv_error($link), "query"=>$insert);
        sqlsrv_query($link, "delete from cargues_mensuales_fondeador where id_cargue_mensual =". $_POST['id_archivo']);
        sqlsrv_query($link, "delete from movimientos_contables where id_archivo =". $_POST['id_archivo']);
        sqlsrv_query($link, "delete from compra_saldos_mensuales where id_archivo =". $_POST['id_archivo']);
    }else{
        $resultado=array("estado"=>200, "mensaje"=>"Lectura de datos Exitosa");
    }

}else{
    $resultado=array("estado"=>400, "mensaje"=>"No se recibieron datos Esperados");
}

echo json_encode($resultado);

 
?>