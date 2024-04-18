<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(true);

    require_once("../cors.php");
    require_once("../../functions.php");

    $json_Input = file_get_contents('php://input');
    $parametros = json_decode($json_Input);
    $respuesta = array();
    $proceso = $_POST["proceso"];
    $link = conectar_utf();
    $proveedor = '';

    switch ($proceso) {
        case 'Login':
            $usuario = addslashes($_POST["usuario"]);
            $clave = addslashes($_POST["clave"]);
            $query_login = ("SELECT *, DATE(fecha_creacion) AS fecha_vincula FROM usuarios WHERE login = '$usuario' AND password = MD5('$clave')");
            $ejecutar_query_login = sqlsrv_query($link, $query_login, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($ejecutar_query_login) >= 0) {
                $resultado = sqlsrv_fetch_array($ejecutar_query_login);
                if ($resultado["estado"] == 1) {
                    header("HTTP/1.1 200 Success");
                    $datos = array(
                        "id" => $resultado["id_usuario"],
                        "nombre" => $resultado["nombre"],
                        "apellido" => $resultado["apellido"],
                        "telefono" => $resultado["telefono"],
                        "tipo" => $resultado["tipo"],
                        "correo" => $resultado["email"],   
                        "cargo" => $resultado["cargo"],
                        "fecha_vinculacion" => $resultado["fecha_vincula"],
                    );

                    $query_pagadurias = ("SELECT * FROM pagadurias");
                    $ejecutar_query_pagadurias = sqlsrv_query($link, $query_pagadurias);
                    while ($respuesta_pagadurias = sqlsrv_fetch_array($ejecutar_query_pagadurias)) {
                        $pagadurias[] = array(
                            "id" => $respuesta_pagadurias["id_pagaduria"],
                            "nombre" => $respuesta_pagadurias["nombre"],
                        );
                    }
                    
                    $query_nivel_contratacion = ("SELECT * FROM nivel_contratacion");
                    $ejecutar_query_nivel_contratacion = sqlsrv_query($link, $query_nivel_contratacion);
                    while ($respuesta_nivel_contratacion = sqlsrv_fetch_array($ejecutar_query_nivel_contratacion)) {
                        $nivel_contratacion[] = array(
                            "id" => $respuesta_nivel_contratacion["nivel_Contratacion_Id"],
                            "descripcion" => $respuesta_nivel_contratacion["nivel_Contratacion_Descripcion"],
                        );
                    }

                    $query_medios_contacto = ("SELECT * FROM medios_contacto");
                    $ejecutar_query_medios_contacto = sqlsrv_query($link, $query_medios_contacto);
                    while ($respuesta_medios_contacto = sqlsrv_fetch_array($ejecutar_query_medios_contacto)) {
                        $medios_contacto[] = array(
                            "id" => $respuesta_medios_contacto["medio_Contacto_Id"],
                            "descripcion" => $respuesta_medios_contacto["medio_Contacto_Descripcion"],
                        );
                    }

                    $query_ciudades = ("SELECT * FROM ciudades");
                    $ejecutar_query_ciudades = sqlsrv_query($link, $query_ciudades);
                    while ($respuesta_ciudades = sqlsrv_fetch_array($ejecutar_query_ciudades)) {
                        $ciudades[] = array(
                            "id" => $respuesta_ciudades["cod_municipio"],
                            "descripcion" => $respuesta_ciudades["municipio"],
                        );
                    }

                    $respuesta = array(
                        "estado" => "200", 
                        "descripcion" => "ejecucion satisfactoria", 
                        "mensaje" => "Bienvenido ", 
                        "fecha" => date("Y-m-d"), 
                        "Proceso" => $proceso,
                        "datos" => $datos,
                        "pagadurias" => $pagadurias,
                        "nivel_contratacion" => $nivel_contratacion,
                        "medios_contacto" => $medios_contacto,
                        "ciudades" => $ciudades,
                    );

                    $actualizarInicioSesion = "UPDATE usuarios SET fecha_ultimo_acceso = CURRENT_TIMESTAMP WHERE id_usuario = '".$fila["id_usuario"]."'";
        			sqlsrv_query($link, $actualizarInicioSesion);	

                }else {
                    $respuesta = array(
                        "estado" => "403", 
                        "descripcion" => "usuario bloqueado", 
                        "mensaje" => "El usuario se encuentra inactivo o la clave es incorrecta, comunicate con soporte tecnico", 
                        "fecha" => date("Y-m-d"), 
                        "Proceso" => $proceso                        
                    );
                }
            }else{
                $respuesta = array("estado" => 203, "error" => sqlsrv_error($link), "query" => $query_login);
            }
            break;
        
        default:
            header("HTTP/1.1 200 Success");
            $respuesta = array("estado" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);                  
        break;
    }

    echo json_encode($respuesta);

?>