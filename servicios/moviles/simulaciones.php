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
        case 'Detalle Simulacion':
            $id_simulacion = addslashes($_POST["id_simulacion"]);
            $query_select_simulaciones = ("SELECT simulaciones.id_simulacion as id_simulacion, simulaciones.cedula as cedula, simulaciones.nombre as nombre, simulaciones.fecha_estudio as fecha_estudio, simulaciones.telefono as telefono, empleados.mail as correo, simulaciones.observaciones as observaciones, simulaciones.estado as estado, simulaciones.pagaduria as Pagaduria, simulaciones.estado as estado, simulaciones.pagaduria as pagaduria, simulaciones.nivel_contratacion as nivel_contratacion, empleados.direccion as direccion, solicitud.celular, solicitud.ciudad FROM empleados INNER JOIN simulaciones ON empleados.cedula = simulaciones.cedula INNER JOIN solicitud ON solicitud.cedula = simulaciones.cedula and simulaciones.id_simulacion = solicitud.id_simulacion WHERE simulaciones.id_simulacion = '".$id_simulacion."' ORDER BY simulaciones.fecha_creacion DESC");
            
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
                            "pagaduria" => $resultado["pagaduria"],
                            "nivel_contratacion" => $resultado["nivel_contratacion"],
                            "direccion" => $resultado["direccion"],
                            "celular" => $resultado["celular"],
                            "ciudad" => $resultado["ciudad"],
                        );

                        $documentos[] = array(

                        );

                        $respuesta = array(
                            "estado" => "200", 
                            "descripcion" => "ejecucion satisfactoria", 
                            "mensaje" => "Informacion generada", 
                            "fecha" => date("Y-m-d"), 
                            "Proceso" => $proceso,
                            "datos" => $datos,
                            "documentos" => $documentos
                        );
                    }
                }else{
                    $respuesta = array("estado" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);
                }

            }else {    
                $respuesta = array("estado" => "500", "descripcion" => "Service Error", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso, "error" => "-->".sqlsrv_error($link)."<--" );
            }
            # code...
            break;
        
        
        default:
            header("HTTP/1.1 200 Success");
            $respuesta = array("estado" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);
        break;
    }

    echo json_encode($respuesta);
?>