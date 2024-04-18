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
        case 'Lista Prospecciones':
            $usuario = addslashes($_POST["usuario"]);
            $query_select_simulaciones = ("SELECT simulaciones.id_simulacion as id_simulacion, simulaciones.cedula as cedula, simulaciones.nombre as nombre, simulaciones.fecha_estudio as fecha_estudio, simulaciones.telefono as telefono, empleados.mail as correo, simulaciones.observaciones as observaciones, simulaciones.estado as estado, simulaciones.pagaduria as Pagaduria FROM empleados INNER JOIN simulaciones ON empleados.cedula = simulaciones.cedula WHERE simulaciones.usuario_creacion = '".$usuario."' ORDER BY simulaciones.fecha_creacion DESC");
            
            $ejecutar_query_select_simulaciones = sqlsrv_query($link, $query_select_simulaciones, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if ($ejecutar_query_select_simulaciones) {
                if (sqlsrv_num_rows($ejecutar_query_select_simulaciones) > 0 ) {
                    while ( $resultado = sqlsrv_fetch_array($ejecutar_query_select_simulaciones) ) {
                        switch ($resultado["estado"]){
							case "ING":	$estado = "INGRESADO"; break;
							case "EST":	$estado = "EN ESTUDIO"; break;
							case "NEG":	$estado = "NEGADO"; break;
							case "DST":	$estado = "DESISTIDO"; break;
							case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
							case "DES":	$estado = "DESEMBOLSADO"; break;
							case "CAN":	$estado = "CANCELADO"; break;
							case "ANU":	$estado = "ANULADO"; break;
						}

                        $datos[] = array(
                            "id_Simulacion" => $resultado["id_simulacion"],
                            "cedula" => $resultado["cedula"],
                            "nombre" => $resultado["nombre"],
                            "fecha_estudio" =>$resultado["fecha_estudio"],
                            "telefono" => $resultado["telefono"],
                            "observaciones" => $resultado["observaciones"],
                            "correo" => $resultado["correo"],
                            "estado" => $estado,
                        );

                        $respuesta = array(
                            "estado" => "200", 
                            "descripcion" => "ejecucion satisfactoria", 
                            "mensaje" => "Informacion generada", 
                            "fecha" => date("Y-m-d"), 
                            "Proceso" => $proceso,
                            "datos" => $datos
                        );
                    }
                }else{
                    $respuesta = array("estado" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);
                }

            }else {    
                $respuesta = array("estado" => "500", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso, "error" => "-->".sqlsrv_error($link)."<--" );
            }
            
            break;

        case 'Nueva':
            $numero_identificacion = $_POST["numero_identificacion"];
            $primer_nombre = $_POST["primer_nombre"];
            $segundo_nombre = $_POST["segundo_nombre"];
            $primer_apellido = $_POST["primer_apellido"];
            $segundo_apellido = $_POST["segundo_apellido"];
            $celular = $_POST["celular"];
            $telefono = $_POST["telefono"];
            $correo = $_POST["correo"];
            $fecha_nacimiento = $_POST["fecha_nacimiento"];
            $fecha_vinculacion = $_POST["fecha_vinculacion"];
            $pagaduria = $_POST["pagaduria"];
            $nivel_contratacion = $_POST["nivel_contratacion"];
            $medio_contacto = $_POST["medio_contacto"];
            $direccion_residencia = $_POST["direccion_residencia"];
            $ciudad_residencia = $_POST["ciudad_residencia"];
            $id_usuario_comercial = $_POST["id_usuario_comercial"];
            
            $query_insert_simulacion = ("CALL spCrearProspeccion('".$primer_nombre."', '".$segundo_nombre."', '".$primer_apellido."', '".$segundo_apellido
                ."', '".$numero_identificacion."', '".$fecha_nacimiento."', '".$direccion_residencia."', '".$ciudad_residencia."', '".$correo."', '".$telefono
                ."', '".$celular."', 1, '".$id_usuario_comercial."');"
            );

            $ejecutar_query_insert_simulacion = sqlsrv_query($link, $query_insert_simulacion);
            if ($ejecutar_query_insert_simulacion) {
                while ( $resultado = sqlsrv_fetch_array($ejecutar_query_insert_simulacion) ) {
                    $respuesta = array("estado"=>"200", "descripcion"=>"Simulacion generada", "id_Simulacion" => $resultado["id_cliente"] );
                }
            }else{
                $respuesta = array("estado"=>"203", "descripcion"=>"Simulacion no generada", "id_Simulacion" => $id_simulacion );
            }

        break;
        
        default:
            header("HTTP/1.1 200 Success");
            $respuesta = array("estado" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);
        break;
    }

    echo json_encode($respuesta);

?>