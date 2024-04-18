<?php
 // ini_set('display_errors', 1);
 // ini_set('display_startup_errors', 1);
 // error_reporting(E_ALL);
 // libxml_use_internal_errors(true);
 require_once("../cors.php");
 require_once("../../functions.php");
 $link = conectar_utf();

 if($_POST['action']=="consultar"){
        $consulta = sqlsrv_query($link, "select * from cargues_mensuales_fondeador");
        if($consulta){
             while($datos = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                $data[]= array(
                        "id_cargue_mensual"=> $datos['id_cargue_mensual'],
                        "nombre_archivo"=>$datos['nombre_archivo'],
                        "fecha_creacion"=>$datos['fecha_creacion'],
                        "cantidad_filas_compras_saldos"=>$datos['cantidad_filas_compras_saldos'],
                        "cantidad_filas_movimiento"=> $datos['cantidad_filas_movimiento'],
                        "usuario_creacion"=> $datos['usuario_creacion'],
                        "acciones"=>"<a id='".$datos['id_cargue_mensual']."' name='descargar' class='btn btn-sm btn-primary' style='margin-to: 1px;' boton='accion'>Descargar</a> 
                                     <a id='".$datos['id_cargue_mensual']."' name='eliminar' class='btn btn-sm btn-danger' style='margin-to: 1px;' boton='accion'>Eliminar</a>"
                );
            }
            $resultado = array("estado"=>200, "mensaje"=>"Consulta Exitosa", "data"=>$data);
        }else{
            $resultado = array("estado"=>300, "mensaje"=>"Consulta Fallida");
        }
 }else if($_POST['action']=="eliminar"){
    $borrar = sqlsrv_query($link, "delete from cargues_mensuales_fondeador where id_cargue_mensual = ".$_POST['id_cargue_mensual']);
    if($borrar){
        $resultado = array("estado"=>200, "mensaje"=>"Registro eliminado Exitosamente");
    }else{
        $resultado = array("estado"=>300, "mensaje"=>"No se pudo eliminar el registo seleccionado");
    }

 }else if($_POST['action'] =="descargar"){
    $compra_saldo_mensuales1 =sqlsrv_query($link, "select  
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
    from compra_saldos_mensuales where id_archivo =".$_POST['id_cargue_mensual']);

    $movimientos_contables1 =sqlsrv_query($link, "select cdp_numero_credito ,
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
        iap_indicador_cancelacion from movimientos_contables where id_archivo =".$_POST['id_cargue_mensual']);
  
  
       while($compra_saldo_mensuales=sqlsrv_fetch_array($compra_saldo_mensuales1)){
          $resultado_compra_saldos[]=array(  
            "id_numero_credito"=>$compra_saldo_mensuales['id_numero_credito'] ,
            "id_numero_identificacion_titular"=>$compra_saldo_mensuales['id_numero_identificacion_titular'] ,
            "id_nombres_deudor"=>$compra_saldo_mensuales['id_nombres_deudor'] ,
            "id_apellidos_deudor"=>$compra_saldo_mensuales['id_apellidos_deudor'] ,
            "id_ciudad_radicacion"=>$compra_saldo_mensuales['id_ciudad_radicacion'] ,
            "id_fecha_nacimiento"=>$compra_saldo_mensuales['id_fecha_nacimiento'] ,
            "id_salario_originacion"=>$compra_saldo_mensuales['id_salario_originacion'] ,
            "ip_id_pagaduria"=>$compra_saldo_mensuales['ip_id_pagaduria'] ,
            "ip_nombre_pagaduria"=>$compra_saldo_mensuales['ip_nombre_pagaduria'] ,
            "ifc_fecha_desembolso"=>$compra_saldo_mensuales['ifc_fecha_desembolso'] ,
            "ifc_plazo_credito"=>$compra_saldo_mensuales['ifc_plazo_credito'] ,
            "ifc_fecha_vencimiento_final"=>$compra_saldo_mensuales['ifc_fecha_vencimiento_final']  ,
            "ifc_plazo_restante"=>$compra_saldo_mensuales['ifc_plazo_restante'],
            "ifc_tasa_interes"=>$compra_saldo_mensuales['ifc_tasa_interes'] ,
            "ifc_valor_desemboslo"=>$compra_saldo_mensuales['ifc_valor_desemboslo'] ,
            "ifc_valor_cuota_corriente"=>$compra_saldo_mensuales['ifc_valor_cuota_corriente'], 
            "ifc_valor_cuota_mesual"=>$compra_saldo_mensuales['ifc_valor_cuota_mesual'] ,
            "ifc_dia_corte_credito"=>$compra_saldo_mensuales['ifc_dia_corte_credito'],
            "ifc_sistema_amortizacion"=>$compra_saldo_mensuales['ifc_sistema_amortizacion'] ,
            "se_saldo_capital_total"=>$compra_saldo_mensuales['se_saldo_capital_total'] ,
            "se_saldo_capital_vencido"=>$compra_saldo_mensuales['se_saldo_capital_vencido'] ,
            "se_saldo_intereses_corrientes_total"=>$compra_saldo_mensuales['se_saldo_intereses_corrientes_total'],
            "se_saldo_intereses_corrientes_vencidos"=>$compra_saldo_mensuales['se_saldo_intereses_corrientes_vencidos'] ,
            "se_saldo_intereses_mora"=>$compra_saldo_mensuales['se_saldo_intereses_mora'] ,
            "se_saldo_intereses_corrientes_mora"=>$compra_saldo_mensuales['se_saldo_intereses_corrientes_mora'] ,
            "se_saldo_seguro_vida"=>$compra_saldo_mensuales['se_saldo_seguro_vida'] ,
            "se_saldos_seguros_fianzas"=>$compra_saldo_mensuales['se_saldos_seguros_fianzas'] ,
            "se_saldo_gastos_legales"=>$compra_saldo_mensuales['se_saldo_gastos_legales'] ,
            "se_saldo_otros_conceptos"=>$compra_saldo_mensuales['se_saldo_otros_conceptos'] ,
            "se_total_deuda"=>$compra_saldo_mensuales['se_total_deuda'] ,
            "se_saldo_abono_diferido"=>$compra_saldo_mensuales['se_saldo_abono_diferido'] ,
            "se_altura_mora"=>$compra_saldo_mensuales['se_altura_mora'] ,
            "se_periodo_gracia"=>$compra_saldo_mensuales['se_periodo_gracia'], 
            "se_calificacion_credito"=>$compra_saldo_mensuales['se_calificacion_credito'], 
            "a_compañia_aseguradora"=>$compra_saldo_mensuales['a_compañia_aseguradora'], 
            "a_tipo_seguro_vida"=>$compra_saldo_mensuales['a_tipo_seguro_vida'], 
            "dias_mora"=>$compra_saldo_mensuales['dias_mora']
          );
       };

    while($movimeintos_contables = sqlsrv_fetch_array($movimientos_contables1)){
        $resultado_movimiento[] = array(
            "cdp_numero_credito"=>$movimeintos_contables['cdp_numero_credito'],
            "cdp_id_deudor"=>$movimeintos_contables['cdp_id_deudor'],
            "cdp_id_pagador"=>$movimeintos_contables['cdp_id_pagador'] ,
            "ic_causaciones_intereses_corriente"=>$movimeintos_contables['ic_causaciones_intereses_corriente']  ,
            "ic_causaciones_intereses_mora" =>$movimeintos_contables['ic_causaciones_intereses_mora'] ,
            "ic_causacion_seguros"=> $movimeintos_contables['ic_causacion_seguros'] ,
            "ic_causaciones_otros_conceptos"=>  $movimeintos_contables['ic_causaciones_otros_conceptos'],
            "iap_fecha_transaccion" =>  $movimeintos_contables['iap_fecha_transaccion'],
            "iap_fecha_pago" =>$movimeintos_contables['iap_fecha_pago'] ,
            "iap_valor_moviemiento_total"=> $movimeintos_contables['iap_valor_moviemiento_total'] ,
            "iap_valor_imputado_honorarios" => $movimeintos_contables['iap_valor_imputado_honorarios'],
            "iap_valor_imputado_judiciales"=> $movimeintos_contables['iap_valor_imputado_judiciales'] ,
            "iap_valor_imputado_seguro_vida" =>$movimeintos_contables['iap_valor_imputado_seguro_vida'] ,
            "iap_valor_imputado_mora_seguros" =>$movimeintos_contables['iap_valor_imputado_mora_seguros'] ,
            "iap_valor_imputado_mora" =>$movimeintos_contables['iap_valor_imputado_mora'] ,
            "iap_valor_imputado_intereses_corriente"=>$movimeintos_contables['iap_valor_imputado_intereses_corriente']  ,
            "iap_valor_imputado_capital" =>$movimeintos_contables['iap_valor_imputado_capital'] ,
            "iap_valor_imputado_abono_deferido"  =>$movimeintos_contables['iap_valor_imputado_abono_deferido'],
            "iap_saldo_favor" =>$movimeintos_contables['iap_saldo_favor'] ,
            "iap_tipo_transaccion"=>$movimeintos_contables['iap_tipo_transaccion'] ,
            "iap_indicador_abono_extraordinario"=>$movimeintos_contables['iap_indicador_abono_extraordinario']  ,
            "iap_indicador_cancelacion"=>$movimeintos_contables['iap_indicador_cancelacion']
        );

    }

    $resultado=array( "estado"=>200, "mensaje"=>"descargando archivo","movimientos_contables"=>$resultado_movimiento, "compra_saldos_mensuales"=>$resultado_compra_saldos);
 }
 echo json_encode($resultado);

?>