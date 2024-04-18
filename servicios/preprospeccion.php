<?php
    //Mostrar errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    
    include_once ('../functions.php');
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    
    $link = conectar_utf();
    $link2 =  conectar_utf();
    

    $primer_nombre=$_POST["primer_nombre"];
    $segundo_nombre=$_POST["segundo_nombre"];
    $primer_apellido=$_POST["primer_apellido"];
    $segundo_apellido=$_POST["segundo_apellido"];
    $identificacion=$_POST["identificacion"];
    $direccion=$_POST["direccion"];
    $ciudad=$_POST["ciudad"];
    $telefono=$_POST["telefono"];
    $celular=$_POST["celular"];
    $email=$_POST["email"];
    $entidad=$_POST["entidad"];
    $fecha_nacimiento=$_POST["fecha_nacimiento"];
    $fecha_vinculacion=$_POST["fecha_vinculacion"];
    $nivel_contratacion=$_POST["nivel_contratacion"];
    $origen=$_POST["origen"];
    $unidad_negocio=$_POST["unidad_negocio"];

    preProspectar($primer_nombre,
    $segundo_nombre,
    $primer_apellido,
    $segundo_apellido,
    $identificacion,
    $direccion,
    $ciudad,
    $telefono,
    $celular,
    $email,
    $entidad,
    $fecha_nacimiento,
    $fecha_vinculacion,
    $nivel_contratacion,
    $origen,$unidad_negocio);

    
   
    function preProspectar(
    $primer_nombre,
    $segundo_nombre,
    $primer_apellido,
    $segundo_apellido,
    $identificacion,
    $direccion,
    $ciudad,
    $telefono,
    $celular,
    $email,
    $entidad,
    $fecha_nacimiento,
    $fecha_vinculacion,
    $nivel_contratacion,$origen,$unidad_negocio) 
    {
        global $link;  
        $val1=0;$val2=0;$val3=0;$val4=0;$val5=0;$val6=0;$val7=0;
        $val8=0;$val9=0;$val10=0;$val11=0;$val12=0;$val13=0;$val14=0;$val15=0;
        $response = array();
        $data = array();

        if ($primer_nombre == null) 
        {
            $val1=1;
            $mensaje.="Debe Ingresar Primer Nombre. ";
        }else{
            $val1=0;
        }

        if ($segundo_nombre == null) 
        {
            $val2=1;
            $mensaje.="Debe Ingresar Segundo Nombre. ";
        }else{
            $val2=0;
        }

        if ($primer_apellido == null) 
        {
            $val3=1;
            $mensaje.="Debe Ingresar Primer Apellido. ";
        }else{
            $val3=0;
        }

        if ($segundo_apellido == null) 
        {
            $val4=1;
            $mensaje.="Debe Ingresar Segundo Apellido. ";
        }else{
            $val4=0;
        }


        if ($identificacion == null) 
        {
            $val5=1;
            $mensaje.="Debe Ingresar Identificacion. ";
        }else{
            $val5=0;
        }

        if ($direccion == null) 
        {
            $val6=1;
            $mensaje.="Debe Ingresar Direccion. ";
        }else{
            $val6=0;
        }

        if ($ciudad == null) 
        {
            $val7=1;
            $mensaje.="Debe Seleccionar Ciudad. ";
        }else{
            $val7=0;
        }

        if ($celular == null) 
        {
            $val8=1;
            $mensaje.="Debe Ingresar Celular. ";
        }else{
            $val8=0;
        }

        if ($email == null) 
        {
            $val9=1;
            $mensaje.="Debe Ingresar Email. ";
        }else{
            $val9=0;
        }

        if ($entidad == null) 
        {
            $val10=1;
            $mensaje.="Debe Ingresar Entidad. ";
        }else{
            $val10=0;
        }

        if ($fecha_nacimiento == null) 
        {
            $val11=1;
            $mensaje.="Debe Ingresar Fecha Nacimiento. ";
        }else{
            $val11=0;
        }

        if ($fecha_vinculacion == null) 
        {
            $val12=1;
            $mensaje.="Debe Ingresar Fecha Vincunacion. ";
        }else{
            $val12=0;
        }

        if ($nivel_contratacion == null) {
            $val13=1;
            $mensaje.="Debe Ingresar Nivel Contratacion. ";
        }else{
            $val13=0;
        }

        if ($origen == null){
            $val14=1;
            $mensaje.="Debe Ingresar Origen de la consulta. ";
        }else{
            $val14=0;
        }

        if ($unidad_negocio == null){
            $val15=1;
            $mensaje.="Debe Ingresar Unidad Negocio. ";
        }else{
            $val15=0;
        }

        if ($val1 == 1 || $val2 == 1 || $val3 == 1 || $val4== 1 || 
            $val5 == 1 || $val6 == 1 || $val7 == 1 || $val8== 1 || 
            $val9 == 1 || $val10 == 1 || $val11 == 1 || $val12== 1 || $val13==1 || $val14==1 || $val15==1)
        {
            header("HTTP/2.0 200 OK");
            $response =array("code"=>"403", "message"=>"Conexion no valida con el servicio. Err: ".$mensaje);
        }else{
            $headers = apache_request_headers();
            $token = $headers['Authorization'];
            $explode=explode(" ",$token);
        
            $opciones = array(
                'http'=>array(
                'header'=>      
                        "Servicio: GetCities"."\r\n".
                        "Authorization: Bearer ".$explode[1]."\r\n"
                )
            );

            $contexto = stream_context_create($opciones);
            $json_Input = file_get_contents($urlPrincipal.'/servicios/validador.php', false, $contexto);
            $parametros=json_decode($json_Input);

            if ($parametros->code==200){
                
                $mensaje = '';
                $query="SELECT * FROM preprospectar WHERE identificacion='".$identificacion."' and estado=0";
                $responseQuery = sqlsrv_query($link, $query);
                    
                if ( !$responseQuery ) {
                    
                    header("HTTP/2.0 200 OK");
                    $response = array( "code"=>"500","mensaje"=>"Error al ejecutar consulta. Err ".sqlsrv_error($link));
                    echo json_encode($response);    
                    exit;
                }
        
                if (sqlsrv_num_rows($responseQuery)>0){
                    header("HTTP/2.0 200 OK");
                    $response = array( "code"=>"500","mensaje"=>"Existe un registro pendiente para este cliente");
                }else{
                    if ($unidad_negocio==3){

                        sqlsrv_query($link2, "START TRANSACTION");
                        $query="INSERT INTO preprospectar (primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,identificacion,direccion,ciudad,telefono,celular,email,entidad,fecha_nacimiento,fecha_vinculacion,nivel_contratacion,id_usuario,fecha,estado,origen) VALUES ('".$primer_nombre."','".$segundo_nombre."','".$primer_apellido."','".$segundo_apellido."','".$identificacion."','".$direccion."','".$ciudad."','".$telefono."','".$celular."','".$email."','".$entidad."','".$fecha_nacimiento."','".$fecha_vinculacion."','".$nivel_contratacion."',197,CURRENT_TIMESTAMP,0,'".$origen."')";
          
                        if (sqlsrv_query($link, $query)){
                            sqlsrv_query($link2, "declare @id_preprospeccion int; SET @id_preprospeccion =  scope_identity()");
                            sqlsrv_query($link2, "COMMIT");

                            $consultaID="SELECT @id_preprospeccion as id_preprospeccion";
                            $queryMultiSet2=sqlsrv_query($link2, $consultaID);
                            $resMultiSet=sqlsrv_fetch_array($queryMultiSet2, SQLSRV_FETCH_ASSOC);
                        
                            $id_simul = $resMultiSet["id_preprospeccion"];
                            header("HTTP/2.0 200 OK");
                                //Generar cadena aleatoria.
                            $token = openssl_random_pseudo_bytes(16);
                                //Convertir el binario a data hexadecimal.
                            $token = bin2hex($token);
                            header('Authorization:'.$token);
                            $response = array("code"=>"200","mensaje"=>"Proceso ejecutado satisfactoriamente. No. de proceso: ".$id_simul);
                        }else{
                            header("HTTP/2.0 200 OK");                                
                                //Generar cadena aleatoria.
                            $response = array( "code"=>"504","mensaje"=>"Error al crear registro", "error" => sqlsrv_error($link));
                        }
                    }
                    else{
                        sqlsrv_query($link, "START TRANSACTION");
                    
                        $query="INSERT INTO preprospectar (primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,identificacion,direccion,ciudad,telefono,celular,email,entidad,fecha_nacimiento,fecha_vinculacion,nivel_contratacion,id_usuario,fecha,estado,origen) VALUES ('".$primer_nombre."','".$segundo_nombre."','".$primer_apellido."','".$segundo_apellido."','".$identificacion."','".$direccion."','".$ciudad."','".$telefono."','".$celular."','".$email."','".$entidad."','".$fecha_nacimiento."','".$fecha_vinculacion."','".$nivel_contratacion."',197,CURRENT_TIMESTAMP,0,'".$origen."')";
                   
                        if (sqlsrv_query($link, $query)){
                            sqlsrv_query($link, "declare @id_preprospeccion int; SET @id_preprospeccion =  scope_identity()");
                            sqlsrv_query($link, "COMMIT");

                            $consultaID="SELECT @id_preprospeccion as id_preprospeccion";
                            $queryMultiSet2=sqlsrv_query($link, $consultaID);
                            $resMultiSet=sqlsrv_fetch_array($queryMultiSet2, SQLSRV_FETCH_ASSOC);
                        
                            $id_simul = $resMultiSet["id_preprospeccion"];
                            
                            header("HTTP/2.0 200 OK");                                
                                //Generar cadena aleatoria.
                            $token = openssl_random_pseudo_bytes(16);
                                //Convertir el binario a data hexadecimal.
                            $token = bin2hex($token);
                            header('Authorization:'.$token);
                            $response = array("code"=>"200","mensaje"=>"Proceso ejecutado satisfactoriamente. No. de proceso: ".$id_simul);
                        }else{
                            header("HTTP/2.0 200 OK");
                            $response = array( "code"=>"504","mensaje"=>"Error al crear registro", "error" => sqlsrv_error($link));
                        }
                    }
                }
            }else{
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"500","mensaje"=>"Token Invalido" );
            }
        }

        echo json_encode($response);
    }
?>