<?php 
include ('../../functions.php');
include ('../../cors.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if(isset($params["id_usuario"])){

	$habilitar_prospeccion = 0; 
	$agenda = 0;
	$preprospeccion = 0;
	$solicitar_firma = 0;
	$inteligencia_negocios = 0;
	$revision_garantias = 0;
	$descargar_reportes = 0;
	$anular_firma_digital = 0;
	$causales_no_recaudo = 0;
	$visualizar_reportes = 0;
	$reporte_cartera = 0;

	$estado = 1;
	$coordinador = 0;
	$jefe_comercial = 0;
	$solo_lectura = 0;
	$meta_mes = 0;

	$sector = 'PUBLICO';
	$contrato = '';
	$maxconsdiarias = 0;

	if ($params["subtipo"]){
		$subtipo = "'".$params["subtipo"]."'";
	}else{
		if ($params["tipo"]=="EXTERNOS") {
			$subtipo =  "'ANALISTA_PREESTUDIO'";
		}else{
			$subtipo = "NULL";
		}
	}

	$existe_login = sqlsrv_query($link,"select [login] from usuarios where id_usuario = '".utf8_encode($params["login"])."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	if (!(sqlsrv_num_rows($existe_login))){

		$usuario_seas = sqlsrv_query($link,"select [login] from usuarios where id_usuario = '".$params["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

		if (!(sqlsrv_num_rows($usuario_seas))){

			$datosSesion = sqlsrv_fetch_array($existe_login);
			
			sqlsrv_query($link,"BEGIN TRANSACTION");

			$query_insert = ("INSERT INTO usuarios (reporte_cartera, causales_no_recaudo, anular_firma_digital,visualizar_reportes,habilitar_prospeccion, revision_garantias, inteligencia_negocios, solicitar_firma, preprospeccion, cargo,agenda,nombre, apellido, email, telefono, estado, tipo, subtipo, sector, cedula, contrato, login, password, maxconsdiarias, meta_mes, freelance, outsourcing, coordinador, jefe_comercial, solo_lectura, usuario_creacion, fecha_creacion) values ('".$reporte_cartera."','".$causales_no_recaudo."', '".$anular_firma_digital."','".$visualizar_reportes."','".$habilitar_prospeccion."','".$revision_garantias."', '".$inteligencia_negocios."', '".$solicitar_firma."', '".$preprospeccion."', '".$params["cargo"]."','".$agenda."','".utf8_encode($params["nombre"])."', '".utf8_encode($params["apellido"])."', '".utf8_encode($params["email"])."', '".utf8_encode($params["telefono"])."', '".$estado."', '".$params["tipo"]."', ".$subtipo.", ".$sector.", ".$params["cedula"].", ".$contrato.", '".utf8_encode($params["login"])."', '".MD5(utf8_encode($params["password"]))."', ".$maxconsdiarias.", '".$meta_mes."', '".$params["freelance"]."', '".$params["outsourcing"]."', '".$coordinador."', '".$jefe_comercial."', '".$solo_lectura."', '".$datosSesion["login"]."', GETDATE())");
			
			if(sqlsrv_query( $link, $query_insert)){
			
				$id_usr1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                    $id_usr2= sqlsrv_fetch_array($id_usr1);
                    $id_usr = $id_usr2['id'];


				sqlsrv_query($link,"INSERT INTO usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) SELECT '".$id_usr."', id_unidad, '".$params["id_usuario"]."', GETDATE() FROM unidades_negocio WHERE id_empresa = '".$params["id_empresa"]."'");

				if(count($params["oficinas"]) > 0){
					foreach ($params["oficinas"] as $oficina) {
						sqlsrv_query($link,"INSERT INTO oficinas_usuarios (id_usuario, id_oficina) VALUES ('".$id_usr."', '".$oficina["id_oficina"]."')");
					}
				}

				sqlsrv_query($link,"COMMIT");
			
				header("HTTP/2.0 200 OK");
		        $response = array( "code"=>"200","mensaje"=>"Usuario Creado Exitosamente");
		    }else{
		    	header("HTTP/2.0 200 OK");
    			$response = array( "code"=>"500","mensaje"=>"Error al crear Usuario");
		    }
	    }
		else{
			header("HTTP/2.0 200 OK");
	        $response = array( "code"=>"400","mensaje"=>"No tiene permisos para esta acción. (No Existe usuario)");
		}
	}
	else{
		header("HTTP/2.0 200 OK");
        $response = array( "code"=>"300","mensaje"=>"El nombre de Usuario ya se encuentra registrado. Usuario NO creado");
	}
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "code"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);
?>