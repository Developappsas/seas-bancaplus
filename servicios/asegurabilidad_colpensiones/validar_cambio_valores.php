<?php
// ini_set( 'display_errors', 1 );
// ini_set( 'display_startup_errors', 1 );
// error_reporting( E_ALL );
header('Content-Type: application/json; charset=utf-8');
// include("../cors.php");
include('../../functions.php');

$link = conectar();
$valorFirmar = 150000000;


if($_POST['cedula'] && $_POST['id_solicitud']){
	$asegurabilidad = "SELECT TOP 1 * from asegurabilidad_colpensiones where cedula = '".$_POST['cedula']."' and id_solicitud = '".$_POST['id_solicitud']."' and asegurado !=4 order by id_registro desc";

	$respuesta_asegurabilidad = sqlsrv_query($link, $asegurabilidad, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if(sqlsrv_num_rows($respuesta_asegurabilidad)==1){
		$datos_asegurabilidad = sqlsrv_fetch_array($respuesta_asegurabilidad, SQLSRV_FETCH_ASSOC);
		$valor_cumulo_total = $datos_asegurabilidad['valor_cumulo'] + $ $datos_asegurabilidad['valor_asegurado'];

		if($valor_cumulo_total<$valorFirmar){
			$nuevoCumuloQuery = "SELECT SUM(s.valor_credito) - (SUM(s.retanqueo1_valor) + SUM(s.retanqueo2_valor) + SUM(s.retanqueo3_valor)) AS valor_cumulo FROM simulaciones s WHERE s.cedula = '".$_POST['cedula']."' AND s.estado in ('DES', 'EST')";
			$resCumuloNuevo = sqlsrv_query($link, $nuevoCumuloQuery);
			if($resCumuloNuevo){
				$cumulo = sqlsrv_fetch_array($resCumuloNuevo, SQLSRV_FETCH_ASSOC);
				$nuevoCumulo = $cumulo['valor_cumulo'];
				$difCumulo = $cumulo['valor_cumulo'] - $valor_cumulo_total;
				if($nuevoCumulo != $valor_cumulo_total && $nuevoCumulo >= $valorFirmar){
					$updateAsegurabilidad = "update asegurabilidad_colpensiones set asegurado = 4, cambio_valores = 1 where cedula = '".$_POST['cedula']."' and id_solicitud ='".$_POST['id_solicitud']."'";
					$respuestaUpdate = sqlsrv_query($link, $updateAsegurabilidad);
					if($respuestaUpdate){
						$response =array(
							"code"=>200,
							"mensaje"=>"Credito requiere firma",
							"cumulo_anterio"=>$valor_cumulo_total,
							"cumulo_nuevo"=>$nuevoCumulo,
							"diferencia_cumulos"=>$difCumulo
						);
					}else{
						$response =array(
							"code"=>404,
							"mensaje"=>"Error Update"
						);
					}
				}else{
					$response =array(
						"code"=>201,
						"mensaje"=>"Credito no apto para nueva firma"
					);
				}
			}else{
				$response =array(
					"code"=>400,
					"mensaje"=>"Error al calcular valor cumulo"
				);
			}
		}else{
			$response =array(
				"code"=>202,
				"mensaje"=>"Credito firmado/en proceso de firma"
			);
		}
	}else{
		$response =array(
			"code"=>303,
			"mensaje"=>"Problemas al consultar asegurabilidad"
		);
	}
}else{
	$response =array(
		"code"=>404,
		"mensaje"=>"no se reciben datos necesarios para realizar la accion correspondiente"
	);
}

echo json_encode($response);

?>