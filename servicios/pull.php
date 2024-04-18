<?php
    //Mostrar errores
    
    
    include_once ('../functions.php');
    
    header("Content-Type: application/json; charset=utf-8");    
    $link = conectar_utf();
    date_default_timezone_set('Etc/UTC');

    $json_Input = file_get_contents('php://input');
    
    
    if( $json_Input){
        $parametros = json_decode($json_Input, true);
        
        
        $link = conectar_utf();
        $id_simulacion = $parametros["id_simulacion"];

        $mensaje = '';

        if ( !empty($id_simulacion) ) {
            $query = "SELECT fd.id as id_formulario, si.nombre as nombre_cliente,si.id_simulacion as nro_id_simulacion, format(convert(DATETIME, sol.fecha_vinculacion, 120),'d/m/Y')
            as format_fecha_vinculacion,
           format(Convert(DATETIME,sol.fecha,120),'d/m/Y') as format_fecha,format(CONVERT(DATETIME,sol.fecha_nacimiento,0),'d/m/Y') 
           as format_fecha_nacimiento,format(CONVERT(DATETIME,sol.fecha_expedicion, 120),'d/m/Y') 
           as format_fecha_expedicion,format(CONVERT(DATETIME,sol.conyugue_fecha_nacimiento, 120),'d/m/Y') 
           as format_conyugue_fecha_nacimiento,format(CONVERT(DATETIME,sol.conyugue_fecha_expedicion, 120),'d/m/Y') 
           as format_conyugue_fecha_expedicion,si.pagaduria,ci.departamento 
           as departamento_residencia,si.usuario_creacion, usr.nombre, usr.apellido, ofi.nombre 
           as nombre_oficina, si.nro_cuenta,si.nro_libranza, sol.*
                       FROM simulaciones si  
                INNER JOIN usuarios usr on si.usuario_creacion = usr.login 
                INNER JOIN oficinas ofi on ofi.id_oficina = si.id_oficina
                INNER JOIN solicitud sol on sol.id_simulacion = si.id_simulacion
                LEFT JOIN ciudades ci ON ci.cod_municipio=sol.ciudad 
                LEFT JOIN formulario_digital fd ON fd.id_simulacion=si.id_simulacion 
            WHERE 
                fd.id_simulacion = '".$parametros["id_simulacion"]."'";
        }else{              
            $query = "SELECT fd.id as id_formulario, si.nombre as nombre_cliente,si.id_simulacion as nro_id_simulacion, format(convert(DATETIME, sol.fecha_vinculacion, 120),'d/m/Y')
 as format_fecha_vinculacion,
format(Convert(DATETIME,sol.fecha,120),'d/m/Y') as format_fecha,format(CONVERT(DATETIME,sol.fecha_nacimiento,0),'d/m/Y') 
as format_fecha_nacimiento,format(CONVERT(DATETIME,sol.fecha_expedicion, 120),'d/m/Y') 
as format_fecha_expedicion,format(CONVERT(DATETIME,sol.conyugue_fecha_nacimiento, 120),'d/m/Y') 
as format_conyugue_fecha_nacimiento,format(CONVERT(DATETIME,sol.conyugue_fecha_expedicion, 120),'d/m/Y') 
as format_conyugue_fecha_expedicion,si.pagaduria,ci.departamento 
as departamento_residencia,si.usuario_creacion, usr.nombre, usr.apellido, ofi.nombre 
as nombre_oficina, si.nro_cuenta,si.nro_libranza, sol.*
            FROM simulaciones si  
                INNER JOIN usuarios usr on si.usuario_creacion = usr.login 
                INNER JOIN oficinas ofi on ofi.id_oficina = si.id_oficina
                INNER JOIN solicitud sol on sol.id_simulacion = si.id_simulacion
                LEFT JOIN ciudades ci ON ci.cod_municipio=sol.ciudad 
                LEFT JOIN formulario_digital fd ON fd.id_simulacion=si.id_simulacion 
            WHERE 
                fd.id = '".$parametros["id_formulario"]."'";
        }

        $responseQuery = sqlsrv_query($link,$query);
       

        if ($responseQuery == false) {
            if( ($errors = sqlsrv_errors() ) != null) {
                foreach( $errors as $error ) {
                   $response= "SQLSTATE: ".$error[ 'SQLSTATE']."<br />
                    code: ".$error[ 'code']."<br />
                    message: ".$error[ 'message']."<br />";
                }
                echo json_encode($response);
                exit;
            }
        }

        // if ( !$responseQuery ) {
        //     $response = array("error"=>sqlsrv_error($link));
        //     header("HTTP/2.0 500 Error de servicio");
        //     $response = array( "mensaje"=>$mensaje, "data"=>$response );
        //     echo json_encode($response);    
        //     exit;
        // }

        $fetchResponse = sqlsrv_fetch_array($responseQuery, SQLSRV_FETCH_ASSOC);

        $str_primer_apellido_solicitante=$fetchResponse["apellido1"];
        $str_segundo_apellido_solicitante=$fetchResponse["apellido2"];
        $str_segundo_nombre_solicitante=$fetchResponse["nombre2"];
        $str_primer_nombre_solicitante=$fetchResponse["nombre1"];

        $str_inicial_primer_nombre=substr($str_primer_nombre_solicitante, 0, 1);
        $str_inicial_segundo_nombre=substr($str_segundo_nombre_solicitante, 0, 1);
        $str_inicial_primer_apellido=substr($str_primer_apellido_solicitante, 0, 1);
        $str_inicial_segundo_apellido=substr($str_segundo_apellido_solicitante, 0, 1);

        $str_iniciales_nombre=$str_inicial_primer_nombre.$str_inicial_segundo_nombre.$str_inicial_primer_apellido.$str_inicial_segundo_apellido;

        $parametros["id_formulario"] = $fetchResponse["id_formulario"];


        $TIC = '';
        switch ($fetchResponse["conyugue_tipo_documento"]) {        
            case 'CEDULA': $TIC = "1"; break;
            case 'REGISTRO CIVIL': $TIC = "3"; break;
            case 'TARJETA IDENTIDAD': $TIC = "2"; break;
            case 'CEDULA EXTRANGERIA': $TIC = "4"; break;        
        } 


        $TD1 = '';
        switch ($fetchResponse["tipo_documento"]) {        
            case 'CEDULA': $TD1 = "1"; break;
            case 'REGISTRO CIVIL': $TD1 = "3"; break;
            case 'TARJETA IDENTIDAD': $TD1 = "2"; break;
            case 'CEDULA EXTRANGERIA': $TD1 = "4"; break;        
        } 

        $EC1 = '';
        switch ($fetchResponse["estado_civil"]) {
            case 'SOLTERO': $EC1 = '1'; break;
            case 'UNION LIBRE': $EC1 = '2'; break;
            case 'CASADO': $EC1 = '3'; break;
            case 'DIVORCIADO': $EC1 = '4'; break;
            case 'SEPARADO': $EC1 = '5'; break;
            case 'VIUDO': $EC1 = '6'; break;
        }

        $TV1 = '';
        switch ($fetchResponse["tipo_vivienda"]) {
            case 'FAMILIAR': $TV1 = '2'; break;
            case 'ARRENDADA': $TV1 = '1'; break;
            case 'PROPIA': $TV1 = '3'; break;
        }

        $NE1 = '';
        switch ($fetchResponse["nivel_estudios"]) {
            case 'PRIMARIA': $NE1 = '1' ; break;
            case 'BACHILLER': $NE1 = '2' ; break;
            case 'TECNICO': $NE1 = '3' ; break;
            case 'TECNOLOGO': $NE1 = '4' ; break;
            case 'UNIVERSITARIO': $NE1 = '5' ; break;
            case 'ESPECIALIZACION': $NE1 = '6' ; break;
            case 'MAESTRIA': $NE1 = '7' ; break;
            case 'DOCTORADO': $NE1 = '8' ; break;
        }

        $GEN = '';
        switch ($fetchResponse["sexo"]) {
            case 'F': $GEN = '2'; break;
            case 'M': $GEN = '1'; break;
        }

        $SXC = '';
        switch ($fetchResponse["conyugue_sexo"]) {
            case 'F': $SXC = '2'; break;
            case 'M': $SXC = '1'; break;
        }

        $OP = '';
        switch ($fetchResponse["ocupacion"]){
            case '1': $OP = '1'; break;
            case '3': $OP = '2'; break;
            case '5': $OP = '3'; break;
            case '4': $OP = '4'; break;
            case '6': $OP = '5'; break;
            case '7': $OP = '6'; break;
        }

        $OC = '';
        switch ($fetchResponse["conyugue_ocupacion"]){
            case 'EMPLEADO': $OC = '1'; break;
            case 'INDEPENDIENTE': $OC = '2'; break;
            case 'PENSIONADO': $OC = '3'; break;
            case 'AMA DE CASA': $OC = '4'; break;
            case 'ESTUDIANTE': $OC = '5'; break;
            case 'RENTISTA CAPITAL': $OC = '6'; break;
        }

        $TCEP = '';
        switch ($fetchResponse["tipo_contrato"]){
            case '1': $TCEP = '1'; break;
            case '4': $TCEP = '4'; break;
            case '5': $TCEP = '5'; break;
        }

        $DIRC = '';
        switch ($fetchResponse["lugar_correspondencia"]) {        
            case 'CASA': $DIRC = "1"; break;
            case 'OFICINA': $DIRC = "2"; break;
            case 'EMAIL': $DIRC = "3"; break;
        } 

        $DR = '';
        switch ($fetchResponse["declara_renta"]) {
            case 'SI': $DR = '1' ; break;
            case 'NO': $DR = '2' ; break;
            default: $DR="";
        }

        $CIP = '';
        switch ($fetchResponse["funcionario_publico"]) {
            case 'SI': $CIP = '1' ; break;
            case 'NO': $CIP = '2' ; break;
            default: $CIP="";
        } 

        $RPP = '';
        switch ($fetchResponse["recursos_publicos"]) {
            case 'SI': $RPP = '1' ; break;
            case 'NO': $RPP = '2' ; break;
            default: $RPP="";
        }  

        $SIRP = '';
        switch ($fetchResponse["personaje_publico"]) {
            case 'SI': $SIRP = '1' ; break;
            case 'NO': $SIRP = '2' ; break;
            default: $SIRP="";
        }  

        $tipo_transaccion_array = explode("|", utf8_decode($fetchResponse["tipo_transaccion"]));
        $tipo_transaccion_opciones = $tipo_transaccion_array[0];
        $tipo_transaccion_otra_cual = $tipo_transaccion_array[1];

        $response = array();
        //array_push($response, array("id"=>'PB1', 'value' => $fetchResponse["id_simulacion"], "isDisabled"=>"false"));

        $NSL1=is_null($fetchResponse["nro_id_simulacion"]) ? "" : $fetchResponse["nro_id_simulacion"];
        array_push($response, array("id"=>'NSL1', 'value' => $NSL1, "isDisabled"=>"false"));


        $DEPDIL=is_null($fetchResponse["ciudad"]) ? "" : substr(str_pad($fetchResponse["ciudad"], 5, "0", STR_PAD_LEFT), 0, 2);
        array_push($response, array("id"=>'DEPDIL', 'value' =>  $DEPDIL, "isDisabled"=>"false"));

        $TD1=is_null($TD1) ? "" : $TD1;
        array_push($response, array("id"=>'TD1', 'value' => $TD1, "isDisabled"=>"false"));

        $NI2=is_null($fetchResponse["cedula"]) ? "" : $fetchResponse["cedula"];
        array_push($response, array("id"=>'NI2', 'value' => $NI2, "isDisabled"=>"false")); //vacio porque no

        $PN7=is_null($fetchResponse["nombre1"]) ? "" : $fetchResponse["nombre1"];
        array_push($response, array("id"=>'PN7', 'value' => $PN7, "isDisabled"=>"false"));

        $SN8=is_null($fetchResponse["nombre2"]) ? "" : $fetchResponse["nombre2"];
        array_push($response, array("id"=>'SN8', 'value' => $SN8, "isDisabled"=>"false"));

        $PA9=is_null($fetchResponse["apellido1"]) ? "" : $fetchResponse["apellido1"];
        array_push($response, array("id"=>'PA9', 'value' => $PA9, "isDisabled"=>"false"));

        $SA10=is_null($fetchResponse["apellido2"]) ? "" : $fetchResponse["apellido2"];
        array_push($response, array("id"=>'SA10', 'value' => $SA10, "isDisabled"=>"false"));

        $CEL14=is_null($fetchResponse["celular"]) ? "" : $fetchResponse["celular"];
        array_push($response, array("id"=>'CEL14', 'value' => $CEL14, "isDisabled"=>"false"));

        $CCE17=is_null($fetchResponse["email"]) ? "" : $fetchResponse["email"];
        array_push($response, array("id"=>'CCE17', 'value' => $CCE17, "isDisabled"=>"false"));  

        //campo nuevo
        $NL1=is_null($fetchResponse["nro_libranza"]) ? "" : $fetchResponse["nro_libranza"];
        array_push($response, array("id"=>'NL1', 'value' => $NL1, "isDisabled"=>"false"));  

        $FEDIL=is_null($fetchResponse["format_fecha"]) ? "" : $fetchResponse["format_fecha"];
        array_push($response, array("id"=>'FEDIL', 'value' => $FEDIL, "isDisabled"=>"false"));
        //aplica para nuevo prd
        $CIUDIL=is_null($fetchResponse["ciudad"]) ? "" : str_pad($fetchResponse["ciudad"], 5, "0", STR_PAD_LEFT);
        array_push($response, array("id"=>'CIUDIL', 'value' => $CIUDIL, "isDisabled"=>"false"));// vacio porque este campo es dinamicos de acuerdo a la ubicacion del cliente.

        array_push($response, array("id"=>"NAC", 'value' => $fetchResponse["nombre"]." ".$fetchResponse["apellido"], "isDisabled"=>"false"));

        $OAC=is_null($fetchResponse["nombre_oficina"]) ? "" : $fetchResponse["nombre_oficina"];
        array_push($response, array("id"=>"OAC", 'value' => $OAC, "isDisabled"=>"false"));

        $FED3=is_null($fetchResponse["format_fecha_expedicion"]) ? "" : $fetchResponse["format_fecha_expedicion"];
        array_push($response, array("id"=>'FED3', 'value' => $FED3, "isDisabled"=>"false"));

        $LE1=is_null($fetchResponse["lugar_expedicion"]) ? "" : $fetchResponse["lugar_expedicion"];
        array_push($response, array("id"=>'LE1', 'value' => $LE1, "isDisabled"=>"false"));

        $FNP1=is_null($fetchResponse["format_fecha_nacimiento"]) ? "" : $fetchResponse["format_fecha_nacimiento"];
        array_push($response, array("id"=>'FNP1', 'value' => $FNP1, "isDisabled"=>"false"));

        $LN1=is_null($fetchResponse["lugar_nacimiento"]) ? "" : $fetchResponse["lugar_nacimiento"];
        array_push($response, array("id"=>'LN1', 'value' =>  $LN1, "isDisabled"=>"false"));

        $GEN=is_null($GEN) ? "" : $GEN;
        array_push($response, array("id"=>'GEN', 'value' => $GEN, "isDisabled"=>"false"));

        $EC1=is_null($EC1) ? "" : $EC1;
        array_push($response, array("id"=>'EC1', 'value' => $EC1, "isDisabled"=>"false"));

        $PRC1=is_null($fetchResponse["residencia_pais"]) ? "" : $fetchResponse["residencia_pais"];
        array_push($response, array("id"=>"PRC1", 'value' => $PRC1, "isDisabled"=>"false"));

        $CRC1=is_null($fetchResponse["ciudad"]) ? "" : str_pad($fetchResponse["ciudad"], 5, "0", STR_PAD_LEFT);
        array_push($response, array("id"=>"CRC1", 'value' => $CRC1, "isDisabled"=>"false"));

        $ET1=is_null($fetchResponse["residencia_estrato"]) ? "" : $fetchResponse["residencia_estrato"];
        array_push($response, array("id"=>'ET1', 'value' => $ET1, "isDisabled"=>"false"));

        $TV1=is_null($TV1) ? "" : $TV1;
        array_push($response, array("id"=>"TV1", 'value' => $TV1, "isDisabled"=>"false"));

        $NA1=is_null($fetchResponse["arrendador_nombre"]) || $fetchResponse["arrendador_nombre"]==0 ? "" : $fetchResponse["arrendador_nombre"];
        array_push($response, array("id"=>'NA1', 'value' => $NA1, "isDisabled"=>"false"));

        $TA1=is_null($fetchResponse["arrendador_telefono"]) || $fetchResponse["arrendador_telefono"]==0 ? "" : $fetchResponse["arrendador_telefono"];
        array_push($response, array("id"=>'TA1', 'value' => $TA1, "isDisabled"=>"false"));            

        $BR1=is_null($fetchResponse["residencia_barrio"]) ? "" : $fetchResponse["residencia_barrio"];
        array_push($response, array("id"=>"BR1", 'value'=> $BR1, "isDisabled"=>"false"));

        $DIR01=is_null($fetchResponse["direccion"]) ? "" : $fetchResponse["direccion"];
        array_push($response, array("id"=>'DIR01', 'value' => $DIR01, "isDisabled"=>"false"));

        $TELP=is_null($fetchResponse["tel_residencia"]) ? "" : $fetchResponse["tel_residencia"];
        array_push($response, array("id"=>'TELP', 'value' => $TELP, "isDisabled"=>"false"));

        //$DIRC=is_null($fetchResponse["lugar_correspondencia"]) ? "" : $fetchResponse["lugar_correspondencia"];
        array_push($response, array("id"=>'DIRC', 'value' => $DIRC, "isDisabled"=>"false"));

        $TRAA=is_null($fetchResponse["anios"]) ? "" : $fetchResponse["anios"];
        array_push($response, array("id"=>'TRAA', 'value' => $TRAA, "isDisabled"=>"false"));

        $TRAM=is_null($fetchResponse["meses"]) ? "" : $fetchResponse["meses"];
        array_push($response, array("id"=>'TRAM', 'value' => $TRAM, "isDisabled"=>"false"));

        $EPS1=is_null($fetchResponse["eps"]) ? "" : $fetchResponse["eps"];
        array_push($response, array("id"=>'EPS1', 'value' => $EPS1, "isDisabled"=>"false"));

        $NPC1=is_null($fetchResponse["personas_acargo_adultos"]) ? "" : $fetchResponse["personas_acargo_adultos"];
        array_push($response, array("id"=>'NPC1', 'value' => $NPC1, "isDisabled"=>"false"));

        $NPC2=is_null($fetchResponse["personas_acargo_menores"]) ? "" : $fetchResponse["personas_acargo_menores"];
        array_push($response, array("id"=>'NPC2', 'value' => $NPC2, "isDisabled"=>"false"));

        $PROF1=is_null($fetchResponse["profesion"]) ? "" : $fetchResponse["profesion"];
        array_push($response, array("id"=>'PROF1', 'value' => "659", "isDisabled"=>"false"));

        $NE=is_null($NE1) ? "" : $NE1;
        array_push($response, array("id"=>'NE', 'value' => $NE, "isDisabled"=>"false"));

        $PNC1=is_null($fetchResponse["nombre_conyugue"]) ? "" : $fetchResponse["nombre_conyugue"];
        array_push($response, array("id"=>'PNC1', 'value' => $PNC1, "isDisabled"=>"false"));

        $SNC1=is_null($fetchResponse["conyugue_nombre_2"]) || $fetchResponse["conyugue_nombre_2"]==0 ? "" : $fetchResponse["conyugue_nombre_2"];
        array_push($response, array("id"=>'SNC1', 'value' => $SNC1, "isDisabled"=>"false"));

        $PAC1=is_null($fetchResponse["conyugue_apellido_1"]) || $fetchResponse["conyugue_apellido_1"]==0 ? "" : $fetchResponse["conyugue_apellido_1"];
        array_push($response, array("id"=>'PAC1', 'value' => $PAC1, "isDisabled"=>"false"));

        $SAC1=is_null($fetchResponse["conyugue_apellido_2"]) || $fetchResponse["conyugue_apellido_2"]==0 ? "" : $fetchResponse["conyugue_apellido_2"];
        array_push($response, array("id"=>'SAC1', 'value' => $SAC1, "isDisabled"=>"false"));

        $TIC=is_null($TIC) ? "" : $TIC;
        array_push($response, array("id"=>'TIC', 'value' => $TIC, "isDisabled"=>"false"));

        $NIC1=is_null($fetchResponse["cedula_conyugue"]) ? "" : $fetchResponse["cedula_conyugue"];
        array_push($response, array("id"=>'NIC1', 'value' => $NIC1, "isDisabled"=>"false"));

        $FEDC1=is_null($fetchResponse["format_conyugue_fecha_expedicion"]) ? "" : $fetchResponse["format_conyugue_fecha_expedicion"];
        array_push($response, array("id"=>'FEDC1', 'value' => $FEDC1, "isDisabled"=>"false"));

        $LEDC1=is_null($fetchResponse["conyugue_lugar_expedicion"]) ? "" : $fetchResponse["conyugue_lugar_expedicion"];
        array_push($response, array("id"=>'LEDC1', 'value' => "", "isDisabled"=>"false"));

        $FNDC1=is_null($fetchResponse["format_conyugue_fecha_nacimiento"]) ? "" : $fetchResponse["format_conyugue_fecha_nacimiento"];
        array_push($response, array("id"=>'FNDC1', 'value' => $FNDC1, "isDisabled"=>"false"));

        $SXC=is_null($SXC) ? "" : $SXC;
        array_push($response, array("id"=>'SXC', 'value' => $SXC, "isDisabled"=>"false"));

        $LNC1=is_null($fetchResponse["conyugue_lugar_nacimiento"]) ? "" : $fetchResponse["conyugue_lugar_nacimiento"];
        array_push($response, array("id"=>'LNC1', 'value' => $LNC1, "isDisabled"=>"false"));

        $LTC1=is_null($fetchResponse["conyugue_nombre_empresa"]) ? "" : $fetchResponse["conyugue_nombre_empresa"];
        array_push($response, array("id"=>'LTC1', 'value' => $LTC1, "isDisabled"=>"false"));

        $OC=is_null($OC) ? "" : $OC;
        array_push($response, array("id"=>'OC', 'value' => $OC, "isDisabled"=>"false"));

        $DEC=is_null($fetchResponse["conyugue_dependencia"]) ? "" : $fetchResponse["conyugue_dependencia"];
        array_push($response, array("id"=>'DEC', 'value' => $DEC, "isDisabled"=>"false"));

        $TCC1=is_null($fetchResponse["conyugue_celular"]) ? "" : $fetchResponse["conyugue_celular"];
        array_push($response, array("id"=>'TCC1', 'value' => $TCC1, "isDisabled"=>"false"));

        $OP=is_null($OP) ? "" : $OP;
        array_push($response, array("id"=>'OP', 'value' => $OP, "isDisabled"=>"false"));

        array_push($response, array("id"=>'DR', 'value' => $DR, "isDisabled"=>"false"));

        array_push($response, array("id"=>'CIP', 'value' => $CIP, "isDisabled"=>"false"));

        array_push($response, array("id"=>'RPP', 'value' => $RPP, "isDisabled"=>"false"));

        array_push($response, array("id"=>'SIRP', 'value' => $SIRP, "isDisabled"=>"false"));

        $AEPP1=is_null($fetchResponse["actividad_economica_principal"]) ? "" : $fetchResponse["actividad_economica_principal"];
        array_push($response, array("id"=>'AEPP1', 'value' => $AEPP1, "isDisabled"=>"false"));

        $NPE1=is_null($fetchResponse["pagaduria"]) ? "" : $fetchResponse["pagaduria"];
        array_push($response, array("id"=>'NPE1', 'value' => $NPE1, "isDisabled"=>"false"));

        $CPE1=is_null($fetchResponse["cargo"]) ? "" : $fetchResponse["cargo"];
        array_push($response, array("id"=>'CPE1', 'value' => $CPE1, "isDisabled"=>"false"));

        $FVEP1=is_null($fetchResponse["format_fecha_vinculacion"]) ? "" : $fetchResponse["format_fecha_vinculacion"];
        array_push($response, array("id"=>'FVEP1', 'value' => $FVEP1, "isDisabled"=>"false"));

        $DEP1=is_null($fetchResponse["direccion_trabajo"]) ? "" : $fetchResponse["direccion_trabajo"];
        array_push($response, array("id"=>'DEP1', 'value' => $DEP1, "isDisabled"=>"false"));

        $CEP1=is_null($fetchResponse["ciudad_trabajo"]) ? "" : str_pad($fetchResponse["ciudad_trabajo"], 5, "0", STR_PAD_LEFT);
        array_push($response, array("id"=>'CEP1', 'value' => "", "isDisabled"=>"false"));


        $NEP1=is_null($fetchResponse["pagaduria"]) ? "" : $fetchResponse["pagaduria"];
        $consultarNITPagaduria="SELECT * FROM pagaduriaspa WHERE pagaduria='".$NEP1."'";
        $queryNITPagaduria=sqlsrv_query($link,$consultarNITPagaduria);
        $resNITPagaduria=sqlsrv_fetch_array($queryNITPagaduria, SQLSRV_FETCH_ASSOC);
        $NITPagaduria=is_null($resNITPagaduria["nit"]) ? "" : $resNITPagaduria["nit"];
        array_push($response, array("id"=>'NEP1', 'value' => $NITPagaduria, "isDisabled"=>"false"));


        $TEP1=is_null($fetchResponse["telefono_trabajo"]) ? "" : $fetchResponse["telefono_trabajo"];
        array_push($response, array("id"=>'TEP1', 'value' => $TEP1, "isDisabled"=>"false"));

        $EEP1=is_null($fetchResponse["extension"]) ? "" : $fetchResponse["extension"];
        array_push($response, array("id"=>'EEP1', 'value' => $EEP1, "isDisabled"=>"false"));

        $TIEP=is_null($fetchResponse["tipo_empresa"]) ? "" : $fetchResponse["tipo_empresa"];
        switch ($TIEP) {
            case 'PUBLICA': $TIEP = '1' ; break;
            case 'PRIVADA': $TIEP = '2' ; break;
            case 'MIXTA': $TIEP = '3' ; break;
        }  
        array_push($response, array("id"=>'TIEP', 'value' => $TIEP, "isDisabled"=>"false"));

        $AEEP=is_null($fetchResponse["actividad_economica_empresa"]) ? "" : $fetchResponse["actividad_economica_empresa"];
        switch ($AEEP) {
            case 'PUBLICA': $AEEP = '1' ; break;
            case 'PRIVADA': $AEEP = '2' ; break;
            case 'MIXTA': $AEEP = '3' ; break;
            default: $AEEP = "1"; 
        }  
        array_push($response, array("id"=>'AEEP', 'value' => $AEEP, "isDisabled"=>"false"));

        $TCEP=is_null($TCEP) ? "" : $TCEP;
        array_push($response, array("id"=>'TCEP', 'value' => $TCEP, "isDisabled"=>"false"));

        array_push($response, array("id"=>'OCP', 'value' => '2', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'CPO', 'value' => '', "isDisabled"=>"false")); //vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'VPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'PNP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'TIPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'IPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'NPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'EPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'CPP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'FDP1', 'value' => '', "isDisabled"=>"false"));//vacio porque esto no se captura en la interfaz seas

        $ILP1=is_null($fetchResponse["ingresos_laborales"]) ? "" : $fetchResponse["ingresos_laborales"];
        array_push($response, array("id"=>'ILP1', 'value' => $ILP1, "isDisabled"=>"false"));

        $GFP1=is_null($fetchResponse["gastos_familiares"]) ? "" : $fetchResponse["gastos_familiares"];
        array_push($response, array("id"=>'GFP1', 'value' => $GFP1, "isDisabled"=>"false"));

        $HCP1=is_null($fetchResponse["honorarios_comisiones"]) ? "" : $fetchResponse["honorarios_comisiones"];
        array_push($response, array("id"=>'HCP1', 'value' => $HCP1, "isDisabled"=>"false"));

        $ACVP1=is_null($fetchResponse["valor_arrendo"]) ? "" : $fetchResponse["valor_arrendo"];
        array_push($response, array("id"=>'ACVP1', 'value' => $ACVP1, "isDisabled"=>"false"));

        $OIP1=is_null($fetchResponse["otros_ingresos"]) ? "" : $fetchResponse["otros_ingresos"];
        array_push($response, array("id"=>'OIP1', 'value' => $OIP1, "isDisabled"=>"false"));

        $PFP1=is_null($fetchResponse["pasivos_financieros"]) ? "" : $fetchResponse["pasivos_financieros"];
        array_push($response, array("id"=>'PFP1', 'value' => $PFP1, "isDisabled"=>"false"));

        $PCP1=is_null($fetchResponse["pasivos_corrientes"]) ? "" : $fetchResponse["pasivos_corrientes"];
        array_push($response, array("id"=>'PCP1', 'value' => $PCP1, "isDisabled"=>"false"));

        $TIP1=is_null($fetchResponse["total_ingresos"]) ? "" : $fetchResponse["total_ingresos"];
        array_push($response, array("id"=>'TIP1', 'value' => $TIP1, "isDisabled"=>"false"));

        $AFP1=is_null($fetchResponse["activos_fijos"]) ? "" : $fetchResponse["activos_fijos"];
        array_push($response, array("id"=>'AFP1', 'value' => $AFP1, "isDisabled"=>"false"));

        $OFP1=is_null($fetchResponse["otros_pasivos"]) ? "" : $fetchResponse["otros_pasivos"];
        array_push($response, array("id"=>'OFP1', 'value' => $OFP1, "isDisabled"=>"false"));

        $TAP1=is_null($fetchResponse["total_activos"]) ? "" : $fetchResponse["total_activos"];
        array_push($response, array("id"=>'TAP1', 'value' => $TAP1, "isDisabled"=>"false"));

        $TPP1=is_null($fetchResponse["total_pasivos"]) ? "" : $fetchResponse["total_pasivos"];
        array_push($response, array("id"=>'TPP1', 'value' => $TPP1, "isDisabled"=>"false"));

        $NRF1=is_null($fetchResponse["nombre_familiar"]) ? "" : $fetchResponse["nombre_familiar"];
        array_push($response, array("id"=>'NRF1', 'value' => $NRF1, "isDisabled"=>"false"));

        $PRF1=is_null($fetchResponse["parentesco_familiar"]) ? "" : $fetchResponse["parentesco_familiar"];
        array_push($response, array("id"=>'PRF1', 'value' => $PRF1, "isDisabled"=>"false"));

        $TRF1=is_null($fetchResponse["telefono_familiar"]) ? "" : $fetchResponse["telefono_familiar"];
        array_push($response, array("id"=>'TRF1', 'value' => $TRF1, "isDisabled"=>"false"));

        $DRF1=is_null($fetchResponse["direccion_familiar"]) ? "" : $fetchResponse["direccion_familiar"];
        array_push($response, array("id"=>'DRF1', 'value' => $DRF1, "isDisabled"=>"false"));

        $CIRF1=is_null($fetchResponse["ciudad_familiar"]) ? "" : $fetchResponse["ciudad_familiar"];
        array_push($response, array("id"=>'CIRF1', 'value' => "", "isDisabled"=>"false"));

        $CRF1=is_null($fetchResponse["celular_familiar"]) ? "" : $fetchResponse["celular_familiar"];
        array_push($response, array("id"=>'CRF1', 'value' => $CRF1, "isDisabled"=>"false"));

        $NRP1=is_null($fetchResponse["nombre_personal"]) ? "" : $fetchResponse["nombre_personal"];
        array_push($response, array("id"=>'NRP1', 'value' => $NRP1, "isDisabled"=>"false"));

        $PRP1=is_null($fetchResponse["parentesco_personal"]) ? "" : $fetchResponse["parentesco_personal"];
        array_push($response, array("id"=>'PRP1', 'value' => $PRP1, "isDisabled"=>"false"));

        $TRP1=is_null($fetchResponse["telefono_personal"]) ? "" : $fetchResponse["telefono_personal"];
        array_push($response, array("id"=>'TRP1', 'value' => $TRP1, "isDisabled"=>"false"));

        $DRP1=is_null($fetchResponse["direccion_personal"]) ? "" : $fetchResponse["direccion_personal"];
        array_push($response, array("id"=>'DRP1', 'value' => $DRP1, "isDisabled"=>"false"));

        $CIRP1=is_null($fetchResponse["ciudad_personal"]) ? "" : $fetchResponse["ciudad_personal"];
        array_push($response, array("id"=>'CIRP1', 'value' => "", "isDisabled"=>"false"));

        $CRP1=is_null($fetchResponse["celular_personal"]) ? "" : $fetchResponse["celular_personal"];
        array_push($response, array("id"=>'CRP1', 'value' => $CRP1, "isDisabled"=>"false"));

        array_push($response, array("id"=>'ROME1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'TOME1', 'value' => (strpos($tipo_transaccion_opciones, "EXPORTACION") == true ? '1' : '2'), "isDisabled"=>"false"));// pendiente hacer split por comas
        array_push($response, array("id"=>'TOME2', 'value' => (strpos($tipo_transaccion_opciones, "IMPORTACION") == true ? '1' : '2'), "isDisabled"=>"false"));// pendiente hacer split por comas
        array_push($response, array("id"=>'TOME3', 'value' => (strpos($tipo_transaccion_opciones, "INVERSIONES") == true ? '1' : '2'), "isDisabled"=>"false"));// pendiente hacer split por comas
        array_push($response, array("id"=>'TOME4', 'value' => (strpos($tipo_transaccion_opciones, "PRESTAMO EN MONEDA EXTRANJERA") == true ? '1' : '2'), "isDisabled"=>"false"));// pendiente hacer split por comas

        $OTOME1=is_null($tipo_transaccion_otra_cual) ? "" : $tipo_transaccion_otra_cual;
        array_push($response, array("id"=>'OTOME1', 'value' => $OTOME1, "isDisabled"=>"false"));// pendiente hacer split por pipe

        array_push($response, array("id"=>'OTOME2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        $CE1=is_null($fetchResponse["cuentas_exterior"]) ? "" : $fetchResponse["cuentas_exterior"];
        $CE1 = '';
        switch ($fetchResponse["cuentas_exterior"]) {
            case 'SI': $CE1 = '1' ; break;
            case 'NO': $CE1 = '2' ; break;
            default: $CE1="";
        }    
        array_push($response, array("id"=>'CE1', 'value' => $CE1, "isDisabled"=>"false"));

        array_push($response, array("id"=>'ACRIP1', 'value' => '2', "isDisabled"=>"false")); //vacio porque esto no se captura en la interfaz seas
        array_push($response, array("id"=>'APFND1', 'value' => '2', "isDisabled"=>"false")); //vacio porque esto no se captura en la interfaz seas

        array_push($response, array("id"=>'DME1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        $NEME1=is_null($fetchResponse["banco"]) ? "" : $fetchResponse["banco"];
        array_push($response, array("id"=>'NEME1', 'value' => $NEME1, "isDisabled"=>"false"));

        $NCME1=is_null($fetchResponse["num_cuenta"]) ? "" : $fetchResponse["num_cuenta"];
        array_push($response, array("id"=>'NCME1', 'value' => $NCME1, "isDisabled"=>"false"));

        $TPME1=is_null($fetchResponse["tipo_producto_operaciones"]) ? "" : $fetchResponse["tipo_producto_operaciones"];
        array_push($response, array("id"=>'TPME1', 'value' => $TPME1, "isDisabled"=>"false"));

        $MOME1=is_null($fetchResponse["monto_operaciones"]) ? "" : $fetchResponse["monto_operaciones"];
        array_push($response, array("id"=>'MOME1', 'value' => $MOME1, "isDisabled"=>"false"));

        $TMME1=is_null($fetchResponse["moneda_operaciones"]) ? "" : $fetchResponse["moneda_operaciones"];
        array_push($response, array("id"=>'TMME1', 'value' => $TMME1, "isDisabled"=>"false"));

        $CMME1=is_null($fetchResponse["ciudad_operaciones"]) ? "" : $fetchResponse["ciudad_operaciones"];
        array_push($response, array("id"=>'CMME1', 'value' => "", "isDisabled"=>"false"));

        $PMME1=is_null($fetchResponse["pais_operaciones"]) ? "" : $fetchResponse["pais_operaciones"];
        array_push($response, array("id"=>'PMME1', 'value' => $PMME1, "isDisabled"=>"false"));


        array_push($response, array("id"=>'DME2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        $NEME2=is_null($fetchResponse["banco2"]) ? "" : $fetchResponse["banco2"];
        array_push($response, array("id"=>'NEME2', 'value' => $NEME2, "isDisabled"=>"false"));

        $NCME2=is_null($fetchResponse["num_cuenta2"]) ? "" : $fetchResponse["num_cuenta2"];
        array_push($response, array("id"=>'NCME2', 'value' => $NCME2, "isDisabled"=>"false"));

        $TPME2=is_null($fetchResponse["tipo_producto_operaciones2"]) ? "" : $fetchResponse["tipo_producto_operaciones2"];
        array_push($response, array("id"=>'TPME2', 'value' => $TPME2, "isDisabled"=>"false"));

        $MOME2=is_null($fetchResponse["monto_operaciones2"]) ? "" : $fetchResponse["monto_operaciones2"];
        array_push($response, array("id"=>'MOME2', 'value' => $MOME2, "isDisabled"=>"false"));

        $TMME2=is_null($fetchResponse["moneda_operaciones2"]) ? "" : $fetchResponse["moneda_operaciones2"];
        array_push($response, array("id"=>'TMME2', 'value' => $TMME2, "isDisabled"=>"false"));

        $CMME2=is_null($fetchResponse["ciudad_operaciones2"]) ? "" : $fetchResponse["ciudad_operaciones2"];
        array_push($response, array("id"=>'CMME2', 'value' => "", "isDisabled"=>"false"));

        $PMME2=is_null($fetchResponse["pais_operaciones2"]) ? "" : $fetchResponse["pais_operaciones2"];
        array_push($response, array("id"=>'PMME2', 'value' => $PMME2, "isDisabled"=>"false"));

        array_push($response, array("id"=>'DME3', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        $NEME3=is_null($fetchResponse["banco3"]) ? "" : $fetchResponse["banco3"];
        array_push($response, array("id"=>'NEME3', 'value' => $NEME3, "isDisabled"=>"false"));

        $NCME3=is_null($fetchResponse["num_cuenta3"]) ? "" : $fetchResponse["num_cuenta3"];
        array_push($response, array("id"=>'NCME3', 'value' => $NCME3, "isDisabled"=>"false"));

        $TPME3=is_null($fetchResponse["tipo_producto_operaciones3"]) ? "" : $fetchResponse["tipo_producto_operaciones3"];
        array_push($response, array("id"=>'TPME3', 'value' => $TPME3, "isDisabled"=>"false"));

        $MOME3=is_null($fetchResponse["monto_operaciones3"]) ? "" : $fetchResponse["monto_operaciones3"];
        array_push($response, array("id"=>'MOME3', 'value' => $MOME3, "isDisabled"=>"false"));

        $TMME3=is_null($fetchResponse["moneda_operaciones3"]) ? "" : $fetchResponse["moneda_operaciones3"];
        array_push($response, array("id"=>'TMME3', 'value' => $TMME3, "isDisabled"=>"false"));

        $CMME3=is_null($fetchResponse["ciudad_operaciones3"]) ? "" : $fetchResponse["ciudad_operaciones3"];
        array_push($response, array("id"=>'CMME3', 'value' => "", "isDisabled"=>"false"));

        $PMME3=is_null($fetchResponse["pais_operaciones3"]) ? "" : $fetchResponse["pais_operaciones3"];
        array_push($response, array("id"=>'PMME3', 'value' => $PMME3, "isDisabled"=>"false"));


        $COP1=is_null($fetchResponse["ciudadania_extranjera"]) ? "" : $fetchResponse["ciudadania_extranjera"];
        $COP1 = '';
        switch ($fetchResponse["ciudadania_extranjera"]) {
            case 'SI': $COP1 = '1' ; break;
            case 'NO': $COP1 = '2' ; break;
            default: $COP1="";
        }  
        array_push($response, array("id"=>'COP1', 'value' => $COP1, "isDisabled"=>"false"));

        $REU1=is_null($fetchResponse["residencia_extranjera"]) ? "" : $fetchResponse["residencia_extranjera"];

        $REU1 = '';
        switch ($fetchResponse["residencia_extranjera"]) {
            case 'SI': $REU1 = '1' ; break;
            case 'NO': $REU1 = '2' ; break;
            default: $REU1="";
        }  
        array_push($response, array("id"=>'REU1', 'value' => $REU1, "isDisabled"=>"false"));

        $SOTE1=is_null($fetchResponse["impuestos_extranjera"]) ? "" : $fetchResponse["impuestos_extranjera"];
        $SOTE1 = '';
        switch ($fetchResponse["impuestos_extranjera"]) {
            case 'SI': $SOTE1 = '1' ; break;
            case 'NO': $SOTE1 = '2' ; break;
            default: $SOTE1="";
        }  
        array_push($response, array("id"=>'SOTE1', 'value' => $SOTE1, "isDisabled"=>"false"));

        $OPRL1=is_null($fetchResponse["representacion_extranjera"]) ? "" : $fetchResponse["representacion_extranjera"];
        $OPRL1 = '';
        switch ($fetchResponse["representacion_extranjera"]) {
            case 'SI': $OPRL1 = '1' ; break;
            case 'NO': $OPRL1 = '2' ; break;
            default: $OPRL1="";
        }  
        array_push($response, array("id"=>'OPRL1', 'value' => $OPRL1, "isDisabled"=>"false"));


        array_push($response, array("id"=>'PE1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        $PPE1=is_null($fetchResponse["poder_pais1"]) ? "" : $fetchResponse["poder_pais1"];
        $PPE1 = '';
        switch ($fetchResponse["poder_pais1"]) {
            case 'SI': $PPE1 = '1' ; break;
            case 'NO': $PPE1 = '2' ; break;
            default: $PPE1="";
        }  


        array_push($response, array("id"=>'PPE1', 'value' => $PPE1, "isDisabled"=>"false"));

        $ITE1=is_null($fetchResponse["poder_identificacion1"]) ? "" : $fetchResponse["poder_identificacion1"];
        array_push($response, array("id"=>'ITE1', 'value' => $ITE1, "isDisabled"=>"false"));

        $OE1=is_null($fetchResponse["poder_objeto1"]) ? "" : $fetchResponse["poder_objeto1"];
        array_push($response, array("id"=>'OE1', 'value' => $OE1, "isDisabled"=>"false"));

        array_push($response, array("id"=>'PE2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        $PPE2=is_null($fetchResponse["poder_pais2"]) ? "" : $fetchResponse["poder_pais2"];
        array_push($response, array("id"=>'PPE2', 'value' => $PPE2, "isDisabled"=>"false"));

        $ITE2=is_null($fetchResponse["poder_identificacion2"]) ? "" : $fetchResponse["poder_identificacion2"];
        array_push($response, array("id"=>'ITE2', 'value' => $ITE2, "isDisabled"=>"false"));

        $OE2=is_null($fetchResponse["poder_objeto2"]) ? "" : $fetchResponse["poder_objeto2"];
        array_push($response, array("id"=>'OE2', 'value' => $OE2, "isDisabled"=>"false"));


        array_push($response, array("id"=>'AOB1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'ECC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        array_push($response, array("id"=>'AOB2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'ECC2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AOB3', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'ECC3', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC3', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC3', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC3', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AOB4', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'ECC4', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC4', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC4', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC4', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AOB5', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'ECC5', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC5', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC5', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC5', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AOB6', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'ECC6', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'NOCC6', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VECC6', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'VCCC6', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'DBD1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'BD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'NCD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'TCD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'GPD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas

        array_push($response, array("id"=>'DBD2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'BD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'NCD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'TCD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'GPD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas

        $AL1=is_null($fetchResponse["fuentes_actividades_licitas"]) ? "" : $fetchResponse["fuentes_actividades_licitas"];
        array_push($response, array("id"=>'AL1', 'value' => $AL1, "isDisabled"=>"false")); //vacio porque no


        $CDP1=is_null($fetchResponse["clave"]) ? "" : $fetchResponse["clave"];
        array_push($response, array("id"=>'CDP1', 'value' => $CDP1, "isDisabled"=>"false")); //vacio porque no
        array_push($response, array("id"=>'BFOD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'TCFOD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'NCFOD1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas

        array_push($response, array("id"=>'BFOD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas

        //nuevo campo para identificfacion
        array_push($response, array("id"=>'NCFOD2', 'value' => $parametros["id_formulario"], "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas
        array_push($response, array("id"=>'TCFOD2', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en seas







        array_push($response, array("id"=>'CCS1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'CCS2', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'CCS3', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'CCS4', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'CCS5', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'REC1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'DFE1', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'MFE1', 'value' => '12', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AFE1', 'value' => '2022', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'CEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'CCEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        array_push($response, array("id"=>'OEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        array_push($response, array("id"=>'IC1', 'value' => $str_iniciales_nombre, "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'DFEDIL', 'value' => '2', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        array_push($response, array("id"=>'MFEDIL', 'value' => '12', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'AFEDIL', 'value' => '2022', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'MFEDIL2', 'value' => '12', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        $DPES1=is_null($fetchResponse["lugar_expedicion"]) ? "" : substr($fetchResponse["lugar_expedicion"], 0, 2);
        array_push($response, array("id"=>'DPES1', 'value' => $DPES1, "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        //array_push($response, array("id"=>'TC', 'value' => '1', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        $DRC1=is_null($fetchResponse["ciudad"]) ? "" : substr($fetchResponse["ciudad"], 0, 2);
        array_push($response, array("id"=>'DRC1', 'value' => $DRC1, "isDisabled"=>"false"));


        $CEX1=is_null($fetchResponse["lugar_expedicion"]) ? "" : $fetchResponse["lugar_expedicion"];
        array_push($response, array("id"=>'CEX1', 'value' => $CEX1, "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'PARS1', 'value' => 'CO', "isDisabled"=>"false")); //vacio 
        array_push($response, array("id"=>'DPRS1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        //array_push($response, array("id"=>'DPEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'DPEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        array_push($response, array("id"=>'PAES1', 'value' => 'CO', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'PANC1', 'value' => 'CO', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico

        //array_push($response, array("id"=>'PAEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico
        array_push($response, array("id"=>'PAEC1', 'value' => '', "isDisabled"=>"false")); //vacio porque no se esta diligencian en formato fisico


        //ELIMINADOS
        /*$CP1=is_null($fetchResponse["cargo"]) ? "" : $fetchResponse["cargo"];            
        array_push($response, array("id"=>'CP1', 'value' => $CP1, "isDisabled"=>"false"));

        $OME1=is_null($fetchResponse["moneda_extranjera"]) ? "" : $fetchResponse["moneda_extranjera"];
        array_push($response, array("id"=>'OME1', 'value' => $OME1, "isDisabled"=>"false"));

        array_push($response, array("id"=>'DBNIE1', 'value' => '', "isDisabled"=>"false"));

        $CIU13=is_null($fetchResponse["ciudad"]) ? "" : $fetchResponse["ciudad"];
        array_push($response, array("id"=>'CIU13', 'value' => $CIU13, "isDisabled"=>"false"));

        $consultarDepartamentos="SELECT * FROM departamentos WHERE nombre='".$fetchResponse["departamento_residencia"]."'";
        $queryDepartamento=mysql_query($consultarDepartamentos,$link);
        $resDepartamento=mysql_fetch_array($queryDepartamento);
        $DPT12=is_null($resDepartamento["cod_departamento"]) ? "" : $resDepartamento["cod_departamento"];
        array_push($response, array("id"=>'DPT12', 'value' => $DPT12, "isDisabled"=>"false"));

        array_push($response, array("id"=>'NB1', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB1', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB1', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB1', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB1', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB2', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB2', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB2', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB2', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB2', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB3', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB3', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB3', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB3', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB3', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB4', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB4', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB4', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB4', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB4', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB5', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB5', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB5', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB5', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB5', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB6', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB6', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB6', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB6', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB6', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB7', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB7', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB7', 'value' => '', "isDisabled"=>"false"));// vac"isDisabled"=>"false"));io porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB7', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB7', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB8', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB8', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB8', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB8', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB8', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB9', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB9', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB9', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB9', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB9', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NB10', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TDB10', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NDB10', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PARB10', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'PB10', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'NCD', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'BCD', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        array_push($response, array("id"=>'TCD', 'value' => '', "isDisabled"=>"false"));// vacio porque este campo no se guarda en seas
        */

        //array_push($response, array("id"=>'TCD', 'value' => '', "isDisabled"=>"false"));

        header("HTTP/2.0 200 Servicio OK");

        //Generar cadena aleatoria.
        $token = openssl_random_pseudo_bytes(16);
        //Convertir el binario a data hexadecimal.
        $token = bin2hex($token);
        header('Authorization:'.$token);
        $data = base64_encode(json_encode($response,JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES));
        //echo "nombre:" .base64_encode(json_encode($response));
        //echo $data;
        $hash = md5($data.'-'.base64_encode('900387878'));
        $url_exoerian = 'https://kreditplus.experienciadigital.com/eScala/#/home?idFlow=51&idSub=900387878';

        $actualizarFormularioDigital="UPDATE formulario_digital SET url_formulario='".($url_exoerian.'&data='.$data.'&hash='.$hash)."' WHERE id='".$parametros["id_formulario"]."'";
        sqlsrv_query($link,$actualizarFormularioDigital);

        $respuesta = array(
            "code" => "200",
            "mensaje" => "Respuesta Exitosa",
            'id_formulario' => $parametros["id_formulario"],
            'nombre' => TRIM($fetchResponse["nombre_cliente"]),
            'pagaduria' => $fetchResponse["pagaduria"],
            'email' => trim($fetchResponse["email"]),
            'id_simulacion' => trim($fetchResponse["id_simulacion"]),
            'data' => $data,
            'version' => 1
        );
    }else{
        $response = array( "code"=>"404","mensaje"=>"No se recibieron parametros" );
    }

    echo  json_encode($respuesta);
