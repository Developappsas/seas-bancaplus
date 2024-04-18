<?php
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );
header('Content-Type: application/json; charset=utf-8');
include('../../functions.php');
include('../../function_blob_storage.php');
$link = conectar();
$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input);
$id_solicitud=$params->id_solicitud;
$nombre_archivo=$params->descripcion;
$base64=$params->formato;
$user_api = $params->user_api;
$api_key=$params->api_key;

if($user_api && $api_key){

	$login = sqlsrv_query($link, "SELECT * from proveedores where usr = '".$user_api."'  AND passwd = '".$api_key."' ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if($login){
		if(sqlsrv_num_rows($login)>0){
			if($id_solicitud!='' && $nombre_archivo!='' && $base64!=''){
				$queryRegistro="SELECT top 1 id_registro, id_simulacion, id_solicitud from asegurabilidad_colpensiones where id_solicitud ='".$id_solicitud."' and asegurado != 4 order by id_registro desc ";
				$respuestaQuery=sqlsrv_query($link,$queryRegistro);
				if($respuestaQuery){
					if(sqlsrv_num_rows($respuestaQuery)>0){

						$registro_solicitud =sqlsrv_fetch_array($respuestaQuery);

						// $insert = "INSERT into archivo_asegurabilidad_colpensiones(id_registro_solicitud, descripcion, formato, fecha_creacion) values (".$registro_solicitud['id_registro'].", '".$nombre_archivo."', '".$base64."', now())";

						$uniqueID = uniqid();
						$extension = explode("/", 'pdf');
						$nombreArc = md5(rand() + intval($registro_solicitud["id_solicitud"])) . "." . $extension[0];
						$f = finfo_open();
						$archivo = base64_decode($base64);
						$mime_type = finfo_buffer($f, $archivo, FILEINFO_MIME_TYPE);
						$fechaa = new DateTime();
						$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
						$metadata1 = array(
							'id_simulacion' => $registro_solicitud["id_simulacion"],
							'descripcion' => ($nombreArc),
							'usuario_creacion' => $user_api,
							'fecha_creacion' => $fechaFormateada
						);
						
						$cargado = false;
						
						try{
							$cargado = upload_file3($base64, "simulaciones", $registro_solicitud['id_simulacion'] . "/adjuntos/" . $nombreArc, $metadata1);
						} catch (ServiceException $exception) {
				            $mensaje = $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
				            throw $exception;
				        }
				        if($cargado){
				        	$insert = "INSERT into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion)value('".$registro_solicitud['id_simulacion']."', '75', 'Carta resultado Solicitud". $registro_solicitud['id_solicitud']."', '".$nombreArc."', '".$nombreArc."', '1', '".$user_api."', GETDATE())";

							 if(sqlsrv_query($link, $insert)){
							 	$response = array(
									"estado"=>200,
									"mensaje"=>"Inserccion de datos exitosaa"
								);
							 }else{
							 	$response = array(
									"estado"=>300,
									"mensaje"=>"Insert fallido",
									"insert"=>$insert
								);
							 }

						}else{
							$response = array(
								"estado"=>300,
								"mensaje"=>"archivo no cargado",
								"insert"=>$insert
							);
						}
					}else{
						$response = array(
							"estado"=>303,
							"mensaje"=>"No existen registro con el id solicitud recibida"
						);
					}
				}else{
					$response = array(
						"estado"=>400,
						"mensaje"=>"consulta fallida"
					);
				}
			}else{
				$response = array(
					"estado"=>404,
					"mensaje"=>"No se reciben datos esperados: id Solicitud, descripcion y/o formato"
				);
			}
		}else{
			$response = array(
				"estado"=>101,
				"mensaje"=>"Accesos Incorrectos"
			);
		}
	}else{
		$response = array(
			"estado"=>500,
			"mensaje"=>"Error consulta de accesos",
			
		);
	}
}else{
	$response = array(
		"estado"=>100,
		"mensaje"=>"No se reciben datos de accesos"
	);
}




echo json_encode($response);

?>