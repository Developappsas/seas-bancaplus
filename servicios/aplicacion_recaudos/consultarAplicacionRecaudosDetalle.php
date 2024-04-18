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

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Consultar Aplicacion Recaudos Detalle':
            $data=array();	
            $data2=array();	
            $consultarDetalleAplicacionRecaudo = "select rpd.*, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo from recaudosplanos_detalle rpd LEFT JOIN simulaciones si ON rpd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON rpd.pagaduria = pa.nombre where rpd.id_recaudoplano = '" . $params["id_Recaudo"] . "'";

            if ($params["tipo"] == "COMERCIAL") {
                $consultarDetalleAplicacionRecaudo .= " AND si.id_comercial = '" . $params["usuario_Id"] . "'";
            } else {
                $consultarDetalleAplicacionRecaudo .= " AND (si.id_unidad_negocio IN (" . $params["unidades_negocio"] . ") OR si.id_unidad_negocio IS NULL)";
            }

            if ($_SESSION["S_SECTOR"]) {
                $consultarDetalleAplicacionRecaudo .= " AND (pa.sector = '" . $params["sector"] . "' OR pa.sector IS NULL)";
            }

            $consultarDetalleAplicacionRecaudo . " order by rpd.id_recaudoplanodetalle";
            
            $queryDetalleAplicacionRecaudo=sqlsrv_query($link,$consultarDetalleAplicacionRecaudo);
            

            while ($resDetalleAplicacionRecaudo = sqlsrv_fetch_array($queryDetalleAplicacionRecaudo, SQLSRV_FETCH_ASSOC)){
                  
             
               
				$opcion_cuota = "0";

				switch ($resDetalleAplicacionRecaudo["opcion_credito"]) {
					case "CLI":
						$opcion_cuota = $resDetalleAplicacionRecaudo["opcion_cuota_cli"];
						break;
					case "CCC":
						$opcion_cuota = $resDetalleAplicacionRecaudo["opcion_cuota_ccc"];
						break;
					case "CMP":
						$opcion_cuota = $resDetalleAplicacionRecaudo["opcion_cuota_cmp"];
						break;
					case "CSO":
						$opcion_cuota = $resDetalleAplicacionRecaudo["opcion_cuota_cso"];
						break;
				}
                        
                    $data[]=array(trim("nombre")=>trim($resDetalleAplicacionRecaudo["nombre"]),
                    trim("nro_libranza")=>trim($resDetalleAplicacionRecaudo["nro_libranza"]),
                    trim("tasa_interes")=>trim($resDetalleAplicacionRecaudo["tasa_interes"]),
                    trim("valor_credito")=>trim($resDetalleAplicacionRecaudo["valor_credito"]),
                    trim("pagaduria")=>trim($resDetalleAplicacionRecaudo["pagaduria"]),
                    trim("plazo")=>trim($resDetalleAplicacionRecaudo["plazo"]),
                    trim("cedula")=>trim($resDetalleAplicacionRecaudo["cedula"]),
                    trim("fecha")=>trim($resDetalleAplicacionRecaudo["fecha"]),
                    trim("valor")=>trim($resDetalleAplicacionRecaudo["valor"]),
                    trim("observacion")=>trim($resDetalleAplicacionRecaudo["observacion"]),
                    trim("opcion_cuota")=>trim($opcion_cuota),

                );
                }
            
            $results = array(
                trim("sEcho") => trim("1"),
                trim("iTotalRecords") => trim(count($data)),
                trim("iTotalDisplayRecords") => trim(count($data)),
                trim("aaData") => $data,
                trim("consulta")=>$consultarDetalleAplicacionRecaudo
            );
            
            $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente','datos'=>$results);
        

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

