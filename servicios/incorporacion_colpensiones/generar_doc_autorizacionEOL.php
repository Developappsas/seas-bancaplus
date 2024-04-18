<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
include ('../../functions.php');
include('../../functions_blob_storage.php');
include('../../formatos/CifrasEnLetras.php');

$link = conectar_utf();

$user= $_POST['correo'];
$password =$_POST['password'];
$id_simulacion = $_POST['id_simulacion'];
$cedula = $_POST['cedula'];
// var_dump($_POST);
if($user && $password){
    $loginData = json_encode(array(
        'email'=>$user,
        'password'=>$password
    ));
    
    // SOLICITAMOS TOKEN
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://gestdocfirmsaas-qa.azurewebsites.net/api/documentos/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$loginData,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Cookie: ARRAffinity=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902; ARRAffinitySameSite=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902'
      ),
    ));
    $response = curl_exec($curl);
    $loginRes = json_decode($response);
     
    if(!$loginRes->errores){
        if($loginRes->parametros[0]->nombre == 'token'){   
            $token = $loginRes->parametros[0]->valor;

            $datoFallo = false;
            $datosErrores = array();
            if($_POST['apellido1']=== null || $_POST['apellido1']=='' ){
                $datoFallo = true;
                $datosErrores['apellido1'] = 'No se recibe primer apellido';

            }
            if($_POST['apellido2']=== null || $_POST['apellido2']=='' ){
                $datoFallo = true;
                $datosErrores['apellido2'] = 'No se recibe segundo apellido';

            }
             if($_POST['nombre1']=== null || $_POST['nombre1']=='' ){
                $datoFallo = true;
                $datosErrores['nombre1'] = 'No se recibe primer nombre';

            }
            if($_POST['nombre2']=== null || $_POST['nombre2']=='' ){
                $datoFallo = true;
                $datosErrores['nombre2'] = 'No se recibe segundo nombre';

            }
            if($_POST['tipo_documento']=== null || $_POST['tipo_documento']=='' ){
                $datoFallo = true;
                $datosErrores['tipo_documento'] = 'No se recibe tipo de documento del cliente';

            }
            if($_POST['cedula']=== null || $_POST['cedula']=='' ){
                $datoFallo = true;
                $datosErrores['cedula'] = 'No se recibe numero documento del cliente';

            }
            if($_POST['fecha_nacimiento']=== null || $_POST['fecha_nacimiento']=='' ){
                $datoFallo = true;
                $datosErrores['fecha_nacimiento'] = 'No se recibe fecha de nacimiento del cliente';

            }
            if($_POST['sexo']=== null || $_POST['sexo']=='' ){
                $datoFallo = true;
                $datosErrores['sexo'] = 'No se recibe sexo del cliente';

            }
            if($_POST['fecha_nacimiento']=== null || $_POST['fecha_nacimiento']=='' ){
                $datoFallo = true;
                $datosErrores['fecha_nacimiento'] = 'No se recibe fecha de expedicion del cliente';

            }
            if($_POST['direccion']=== null || $_POST['direccion']=='' ){
                $datoFallo = true;
                $datosErrores['direccion'] = 'No se recibe direccion de residencia del cliente';

            }
            if($_POST['codigo_departamento']=== null || $_POST['codigo_departamento']=='' ){
                $datoFallo = true;
                $datosErrores['codigo_departamento'] = 'No se recibe departamento de recidencia del cliente';

            }
            if($_POST['codigo_ciudad']=== null || $_POST['codigo_ciudad']=='' ){
                $datoFallo = true;
                $datosErrores['codigo_ciudad'] = 'No se recibe ciudad de residencia del cliente';

            }
            if($_POST['celular']=== null || $_POST['celular']=='' ){
                $datoFallo = true;
                $datosErrores['celular'] = 'No se recibe No se recibe numero de celular del cliente';

            }
            if($_POST['telefono_personal']=== null || $_POST['telefono_personal']=='' ){
                $datoFallo = true;
                $datosErrores['telefono_personal'] = 'No se recibe numero de telefono fijo del cliente';

            }
            if($_POST['nombre2']=== null || $_POST['nombre2']=='' ){
                $datoFallo = true;
                $datosErrores['nombre2'] = 'No se recibe segundo nombre';

            }
            if($_POST['email']=== null || $_POST['email']=='' ){
                $datoFallo = true;
                $datosErrores['email'] = 'No se recibe correo electronico del cliente';

            }
            if($_POST['valor_credito']=== null || $_POST['valor_credito']=='' ){
                $datoFallo = true;
                $datosErrores['valor_credito'] = 'No se recibe valor del credito correspondiente';

            }else{
                $valorCreditoLetras = CifrasEnLetras::convertirNumeroEnLetras(str_replace(".",",",$_POST['valor_credito']));    
            }
            if($_POST['nro_libranza']=== null || $_POST['nro_libranza']==''){
                $datoFallo = true;
                $datosErrores['nro_libranza'] = 'No se recibe numero de libranza del credito';

            }
            if($_POST['plazo']=== null || $_POST['plazo']=='' ){
                $datoFallo = true;
                $datosErrores['plazo'] = 'No se recibe plazo del credito correspondiente';

            } 
            if($_POST['valor_cuota']=== null || $_POST['valor_cuota']=='' ){
                $datoFallo = true;
                $datosErrores['valor_cuota'] = 'No se recibe valor de la cuota';

            }else{
               $valorCuotaLetras = CifrasEnLetras::convertirNumeroEnLetras(str_replace(".",",",$_POST['valor_cuota_letras']));  
            }
              
            
                 
            
            if(!$datoFallo){
                $data = json_encode(array(
                        "nitEntidad"=>"901076840",
                        "razonSocialEntidad"=>"PATRIMONIO AUTONOMO ESEFECTIVO",
                        "representanteLegalEntidad"=>"",
                        "tipoNovedad"=>"P",
                        "direccionEntidad"=>"cALLE 26 Torre 3",
                        "fechaDiligenciamiento"=>"2024-02-08",
                        "departamentoEntidad"=>"11",
                        "ciudadEntidad"=>"001",
                        "telefono1Entidad"=>"(601)7454098",
                        "telefono2Entidad"=>"",
                        "emailEntidad"=>"herramientasti@kredit.com.co",
                        "apellido1Pensionado"=>$_POST['apellido1'],
                        "apellido2Pensionado"=>$_POST['apellido2'],
                        "nombre1Pensionado"=>$_POST['nombre1'],
                        "nombre2Pensionado"=>$_POST['nombre2'],
                        "tipoDctoPensionado"=>$_POST['tipo_documento'],
                        "otroTipoDctoPensionado"=>"",
                        "nroDctoPensionado"=>$_POST['cedula'],
                        "fechaNacimiento"=>$_POST['fecha_nacimiento'],
                        "genero"=>$_POST['sexo'],
                        "fechaExpDocumento"=>$_POST['fecha_expedicion'],
                        "nroAfiliacionPensionado"=>"",
                        "direccionPensionado"=>$_POST['direccion'],
                        "departamentoPensionado"=>$_POST['codigo_departamento'],
                        "ciudadPensionado"=>$_POST['codigo_ciudad'],
                        "telefonoCelularPensionado"=>$_POST['celular'],
                        "telefonoFijoPensionado"=>$_POST['telefono_personal'],
                        "emailPensionado"=>$_POST['email'],
                        "nombApelRepresentanteCurador"=>"",
                        "tipoDocRepresentanteCurador"=>"",
                        "nroDocRepresentanteCurador"=>"",
                        "telefonoRepresentanteCurador"=>"",
                        "valorCuotaDescAfiliacion"=>"",
                        "valorCuotaDescAfiliacionLetras"=>"",
                        "valorTotalPrestamo"=>$_POST['valor_credito'],
                        "valorTotalPrestamoLetras"=>$valorCreditoLetras,
                        "nroLibranzaPrestamo"=>$_POST['nro_libranza'],
                        "nroCuotasPrestamo"=>$_POST['plazo'],
                        "valorCuotaMensualPrestamo"=>$_POST['valor_cuota'],
                        "valorCuotaMensualPrestamoLetras"=>$valorCuotaLetras
                ));
                
                // ENVIO DATOS DE SOLICITUD
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://gestdocfirmsaas-qa.azurewebsites.net/api/documentos/generarAutorizacionEOL',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$data,
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Cookie: ARRAffinity=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902; ARRAffinitySameSite=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902',
                    'Authorization: Bearer ' . $token
                    ),
                ));
                $responseGenerar = curl_exec($curl);
                $generarAutorizacionRes= json_decode($responseGenerar);
                
                if(!$generarAutorizacionRes->errores){
                    if($generarAutorizacionRes->parametros[0]->nombre == 'idTransaccion'){

                        $idTransaccion = $generarAutorizacionRes->parametros[0]->valor;
                        
                        // DATOS CONFIRMACION
                        $dataConfirmacion = json_encode(array(
                            "entidad"=>"901076840",
                            "idTransaccion"=>$idTransaccion,
                            "tokenFirmado"=>"1235"

                        ));
                        // ENVIO DATOS CONFIRMACION
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'https://gestdocfirmsaas-qa.azurewebsites.net/api/documentos/confirmarAutorizacionEOL',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS =>$dataConfirmacion,
                          CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Cookie: ARRAffinity=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902; ARRAffinitySameSite=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902',
                            'Authorization: Bearer ' . $token
                            ),
                        ));
                        $responseConfirmar = curl_exec($curl);
                        $confirmacionEOL = json_decode($responseConfirmar);

                        if(!$confirmacionEOL->errores){
                            if($confirmacionEOL->parametros[0]->nombre == 'archivoAntesDeFirma' && $confirmacionEOL->parametros[1]->nombre=='tokenConfirmacion'){
                                $base64 = $confirmacionEOL->parametros[0]->valor;
                                $tokenConfirmacion = $confirmacionEOL->parametros[1]->valor;
                                $datosFirmar = json_encode(array(
                                    'entidad'=>'901076840',
                                    'idTransaccion'=>intval($idTransaccion),
                                    'archivo'=>$base64
                                ));
                               
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                  CURLOPT_URL => 'https://gestdocfirmsaas-qa.azurewebsites.net/api/documentos/firmarDocumento',
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => '',
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 0,
                                  CURLOPT_FOLLOWLOCATION => true,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => 'POST',
                                  CURLOPT_POSTFIELDS =>$datosFirmar,
                                  CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json',
                                    'Cookie: ARRAffinity=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902; ARRAffinitySameSite=c015407f2340ab83319171108305fa1072c8452284bc5ef903dfd906b4fd7902',
                                    'Authorization: Bearer ' . $token
                                    ),
                                ));
                                $resFirma = curl_exec($curl);
                                $resFirma2 = json_decode($resFirma);
                                
                                if(!$resFirma2->errores){
                                    if($resFirma2->parametros[0]->nombre == 'idSolicitud' && $resFirma2->parametros[1]->nombre=='archivo'){

                                            $base64 = $resFirma2->parametros[1]->valor;
                                            $id_solicitud = $resFirma2->parametros[0]->valor;
                                            $uniqueID = uniqid();
                                            $extension = explode("/", 'pdf');
                                            $nombreArc = md5(rand() + intval($id_solicitud)) . "." . $extension[0];
                                            // $f = finfo_open();
                                            // $archivo = base64_decode($base64);
                                            // $mime_type = finfo_buffer($f, $archivo, FILEINFO_MIME_TYPE);
                                            $fechaa = new DateTime();
                                            $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
                                            $metadata1 = array(
                                                'id_simulacion' => $_POST['id_simulacion'],
                                                'descripcion' => ($nombreArc),
                                                'usuario_creacion' => $user_api,
                                                'fecha_creacion' => $fechaFormateada
                                            );
                                            
                                            $cargado = false;
                                            
                                            try{
                                                $cargado = upload_file3($base64, "simulaciones", $id_solicitud . "/adjuntos/" . $nombreArc, $metadata1);
                                            } catch (ServiceException $exception) {
                                                $mensaje = $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
                                                throw $exception;
                                            }

                                            if($cargado){
                                                $insertAdjuntos = "insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion)values('".$_POST['id_simulacion']."', '75', 'PRUEBA COLPENSIONES". $id_solicitud."', '".$nombreArc."', '".$nombreArc."', '1', '".$user_api."', GETDATE())";

                                                $insertado = sqlsrv_query($link, $insertAdjuntos);
                                                $id_adjunto3 = sqlsrv_query($link, "SELECT scope_identity() as id ");
                                                $id_adjunto2 = sqlsrv_fetch_array($id_adjunto3)
                                                $id_adjunto = $id_adjunto2['id'];

                                                if($insertado){
                                                    $insert_colpensiones = "insert into incorporacion_colpensiones(id_simulacion, token ,id_transaccion, otp_firmado, id_solicitud, id_adjunto, fecha_creacion)values('".$_POST['id_simulacion']."','".$token."' ,".$idTransaccion.",'".$_POST['otp_firmado']."','".$id_solicitud."', ".$id_adjunto.", GETDATE())";
                                                    $incorporado = sqlsrv_query($link, $insert_colpensiones);
                                                    if($incorporado){
                                                        $respuesta = array(
                                                            "estado"=>505,
                                                            "Mensaje"=>'incorporacion creada'
                                                        );
                                                    }else{
                                                        $respuesta = array(
                                                            "estado"=>505,
                                                            "Mensaje"=>'incorporacion no creada'
                                                        );
                                                    }

                                                }else{
                                                  $respuesta = array(
                                                    "estado"=>505,
                                                    "Mensaje"=>'Error al insertar el documento'
                                                   );  
                                                }

                                            }else{
                                               $respuesta = array(
                                                    "estado"=>505,
                                                    "Mensaje"=>'Error al cargar documento, por favor comunicarse con el equipo de sistema'
                                                ); 
                                            }

                                    }else{
                                        $respuesta = array(
                                            "estado"=>404,
                                            "Mensaje"=>'No se reciben datos esperados al momento de firmar el documento'
                                        );
                                    }
                                }else{
                                    $respuesta = array(
                                        "estado"=>502,
                                        "Mensaje"=>$resFirma2->descripcionErrores
                                    );
                                }
                            }else{
                                $respuesta = array(
                                    "estado"=>417,
                                    "Mensaje"=>"No se Recibe datos externos"
                                ); 
                            }
                        }else{
                            $respuesta = array(
                                "estado"=>501,
                                "Mensaje"=>"Error Servicio"
                            ); 
                        }
                    }else{
                       $respuesta = array(
                            "estado"=>500,
                            "Mensaje"=>$generarAutorizacionRes->descripcionErrores
                        ); 
                    }
                }else{
                    $respuesta = array(
                        "estado"=>303,
                        "Mensaje"=>$generarAutorizacionRes->descripcionErrores
                    );
                }
            }else{
                $respuesta = array(
                    "estado"=>417,
                    "Mensaje"=>"Datos de solicitud no recibidos",
                    "data"=>$datosErrores
                );
            }
        }else{
           $respuesta = array(
                "estado"=>405,
                "Mensaje"=>"no se recibe Token de acceso"
            ); 
        }
    }else{
        $respuesta = array(
            "estado"=>502,
            "Mensaje"=>$loginRes->descripcionErrores
        );  
    }   
}else{
    $respuesta = array(
        "estado"=>402,
        "Mensaje"=>"no se recibe datos de acceso"
    );
}

echo json_encode($respuesta)


?>