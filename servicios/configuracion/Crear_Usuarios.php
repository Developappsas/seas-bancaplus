<?php
    include ('../../functions.php');
    include ('../cors.php');
    header("Content-Type: application/json; charset=utf-8");    
    $link = conectar_utf();
    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input,true);
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        if(isset($params["perfil"]))
        {
            $consultarPerfilesConfiguracion="SELECT * FROM perfiles_configuracion  WHERE perfil_Codigo='".$params["perfil"]."'";
            $queryPerfilesConfiguracion=sqlsrv_query($link,$consultarPerfilesConfiguracion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryPerfilesConfiguracion)>0)
            {
                $resPerfilesConfiguracion=sqlsrv_fetch_array($queryPerfilesConfiguracion, SQLSRV_FETCH_ASSOC);
                $configuracionPerfil=json_decode($resPerfilesConfiguracion["perfil_Configuracion"],true);
                $usuario= ($params["usuario_Primer_Nombre"][0]).$params["usuario_Primer_Apellido"];
                $existe_Usuario=0;
                $consecutivo=0;
                $consultarExisteUsuario="SELECT * FROM usuarios where login='".$usuario."'";
                $queryExisteUsuario=sqlsrv_query($link,$consultarExisteUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($queryExisteUsuario)>0)
                {
                    while ($existe_Usuario==0)
                    {
                        $consecutivo=$consecutivo+1;
                        $usuario=($params["usuario_Primer_Nombre"][0]).$params["usuario_Primer_Apellido"].$consecutivo;
                        $consultarExisteUsuario="SELECT * FROM usuarios where login='".$usuario."'";
                        $queryExisteUsuario=sqlsrv_query($link,$consultarExisteUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));   
                        if (sqlsrv_num_rows($queryExisteUsuario)==0) 
                        {
                            $existe_Usuario=1;
                        }
                    }
                    
                }

                $query_insert = ("INSERT INTO usuarios (reporte_cartera, causales_no_recaudo, anular_firma_digital,visualizar_reportes,habilitar_prospeccion, revision_garantias, inteligencia_negocios, solicitar_firma, preprospeccion, cargo,agenda,nombre, apellido, email, telefono, estado, tipo, subtipo, sector, cedula, contrato, login, password, maxconsdiarias, meta_mes, freelance, outsourcing, coordinador, jefe_comercial, solo_lectura, usuario_creacion, fecha_creacion) values ('".$configuracionPerfil["reporte_cartera"]."','".$configuracionPerfil["causales_no_recaudo"]."', '".$configuracionPerfil["anular_firma_digital"]."','".$configuracionPerfil["visualizar_reportes"]."','".$configuracionPerfil["habilitar_prospeccion"]."','".$configuracionPerfil["revision_garantias"]."', '".$configuracionPerfil["inteligencia_negocios"]."', '".$configuracionPerfil["solicitar_firma"]."', '".$configuracionPerfil["preprospeccion"]."', '".$params["perfil"]."','".$configuracionPerfil["agenda"]."','".utf8_encode($params["usuario_Primer_Nombre"]." ".$params["usuario_Segundo_Nombre"])."', '".utf8_encode($params["usuario_Primer_Apellido"]." ".$params["usuario_Segundo_Apellido"])."', '".utf8_encode($params["usuario_Correo"])."', '".utf8_encode($params["usuario_Telefono"])."', '1', '".$configuracionPerfil["tipo"]."', '".$configuracionPerfil["subtipo"]."', '".$configuracionPerfil["sector"]."', '".$params["usuario_Identificacion"]."', ".$configuracionPerfil["contrato"].", '".utf8_encode($usuario)."', MD5('".utf8_encode($params["usuario_Password"])."'), ".$configuracionPerfil["maxconsdiarias"].", '".$configuracionPerfil["meta_mes"]."', '".$configuracionPerfil["freelance"]."', '".$configuracionPerfil["outsourcing"]."', '".$configuracionPerfil["coordinador"]."', '".$configuracionPerfil["jefe_comercial"]."', '".$configuracionPerfil["solo_lectura"]."', 'system', GETDATE)");
			
                if(sqlsrv_query( $link, $query_insert)){
                
                    $id_usr = sqlsrv_insert_id($link);

                    sqlsrv_query($link,"INSERT INTO usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) SELECT '".$id_usr."', id_unidad, '197', GETDATE FROM unidades_negocio WHERE id_empresa = '".$params["id_empresa"]."'");

                  

                    $mostrar_oficina="";
                    foreach (($params["oficinas"]) as $v) {
                        sqlsrv_query($link,"INSERT INTO oficinas_usuarios (id_usuario, id_oficina) VALUES ('".$id_usr."', '".$v["id_oficina"]."')");
                    }
            
                    $data = array('codigo' => 200, 'mensaje' => $mostrar_oficina);

                    sqlsrv_query($link,"BEGIN");
                
                    header("HTTP/2.0 200 OK");
                    $data = array( "codigo"=>200,"mensaje"=>"Usuario Creado Exitosamente","data"=>intval($id_usr));
                }else{
                    header("HTTP/2.0 200 OK");
                    $data = array( "codigo"=>500,"mensaje"=>"Error al crear Usuario","query"=>$query_insert);
                }
              
            }else{
                $data = array('codigo' => 400, 'mensaje' => 'No se ha determinado perfil de usuario');    
            }
        
        }
        else
        {
            $data = array('codigo' => 400, 'mensaje' => 'No se ha determinado perfil de usuario');
        }
    }
    else
    {
        $data = array('codigo' => 400, 'mensaje' => 'Metodo No Correcto');
    }
    http_response_code("200");
    echo json_encode($data);
    
?>