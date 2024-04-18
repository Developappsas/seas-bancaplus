<?php
header( 'Content-Type: text/html; charset=UTF-8' );
include ( '../../functions.php' );
require( '../../plugins/nusoap2/src/nusoap.php' );
$link = conectar_utf();
// $wsdl = "https://syssastpa.com/tpacandidatos/WebService/105/syssastpa105.php?wsdl";
$wsdl = "https://pruebas.syssastpa.com/tpacandidatos/WebService/105/syssastpa105.php?wsdl";
$client = new nusoap_client($wsdl, 'wsdl');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = true;

$id_solicitud = $_POST['IdSolicitudI'];
$id_simulacion = $_POST['id_simulacion'];
$id_usuario = $_POST['id_usuario'];

if($_POST['metodo'] == "CrearSolicitud" ){
    // Datos solicitud
        $queryDB = "SELECT  si.id_simulacion, so.tipo_documento, si.cedula, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.fecha_nacimiento, so.sexo, so.estado_civil, so.celular, so.telefono_personal, trim(so.email) as email , so.ciudad, so.direccion, so.estatura, so.peso, si.valor_credito, FORMAT(GETDATE(), 'yyyy-MM-dd') as fecha_consulta
            from simulaciones si
            INNER JOIN solicitud so ON si.id_simulacion = so.id_simulacion
            WHERE si.id_simulacion = '".$id_simulacion."'";

            

        $queryRes = sqlsrv_query($link, $queryDB);
        $resDatos = sqlsrv_fetch_array($queryRes);
        switch ($resDatos['sexo']) {
          case 'F':
              $IdGenero =2;
              break;
          
          default:
              $IdGenero=1;
              break;
        }
        switch ($resDatos['estado_civil']) {
            case 'SOLTERO':
                $EstadoCivil=1;
                break;
            case 'CASADO':    
                $EstadoCivil=2;
                break;
            case 'UNION LIBRE':
                $EstadoCivil=3;
                break;
            case 'VIUDO':
                $EstadoCivil=4;
                break;
            case 'SEPARADO':
                $EstadoCivil=5;
                break;
            case 'DIVORCIADO':
                $EstadoCivil=5;
                break;
            default:
                $EstadoCivil=0;
                break;      
        }
        switch ($resDatos['tipo_documento']) {
            case 'CEDULA':
                $TipoIdentificacion =  "CC" ;           
                break;
            case 'CEDULA EXTRANJERIA':
                $TipoIdentificacion =  "CE";            
                break;
            case 'TARJETA IDENTIDAD':
                $TipoIdentificacion =  "TI";            
                break;
        }
        if($resDatos['telefono_personal']==''|| $resDatos['telefono_personal']===null){
            $telefonoPersonal = $resDatos['celular'];
        }else{
            $telefonoPersonal =$resDatos['telefono_personal'];
        }

        $valorCumulo = sqlsrv_query($link, "SELECT SUM(s.valor_credito) - (SUM(s.retanqueo1_valor) + SUM(s.retanqueo2_valor) + SUM(s.retanqueo3_valor)) AS valor_cumulo FROM simulaciones s WHERE s.cedula = '".$resDatos['cedula']."' AND s.estado in ('DES', 'EST')");
        $valorCumuloRes = sqlsrv_fetch_array($valorCumulo, SQLSRV_FETCH_ASSOC);

        if($valorCumuloRes['valor_cumulo']!=null || $valorCumuloRes['valor_cumulo']!=''){
            $valorCumuloParcial = $valorCumuloRes['valor_cumulo']- $resDatos['valor_credito'];
            $valorCumuloTotal = $valorCumuloRes['valor_cumulo'];
            if($valorCumuloTotal<0){
                $valorCumuloTotal = 0;
            }
            if($valorCumuloParcial<0){
                $valorCumuloParcial = 0;
            }
        }else{
            $valorCumuloParcial=0;
            $valorCumuloTotal = 0;
        }

        $valorAsegurado = intval(str_replace(',', '', $resDatos['valor_credito']));

        if($resDatos['estatura']!=''|| $resDatos!==null){
            $estatura = $resDatos['estatura'] / 100;
        }

        $apellidos = utf8_encode($resDatos['apellido1']." ".$resDatos['apellido2']);
        $nombres = utf8_encode($resDatos['nombre1']." ".$resDatos['nombre2']);
    
        // Ajuste parametros fin
            

        $param = array(
            'IdSolicitudI' => $id_solicitud,
            'IdCandidatoI' =>  0,
            'FechaSolicitud' => $resDatos['fecha_consulta'],
            'IdProducto' => 1,
            'TipoIdentificacion' => $TipoIdentificacion,
            'Identificacion' => $resDatos['cedula'],
            'Apellidos' => $apellidos,
            'Nombres' => $nombres,
            'FechaNacimiento' => $resDatos['fecha_nacimiento'],
            'IdGenero' => $IdGenero,
            'IdEstadoCivil' => $EstadoCivil,
            'Telefono' => $telefonoPersonal,
            'Celular' => $resDatos['celular'],
            'CorreoCandidato' => $resDatos['email'],
            'CorreoAlterno' => $resDatos['email'],
            'Ciudad' => $resDatos['ciudad'],
            'Direccion' => $resDatos['direccion'],
            'ValorCumulo' => $valorCumuloParcial,
            'ValorAsegurado' => $valorAsegurado,
            'IdPensionado' => 0,
            'Estatura' => $estatura,
            'Peso' => $resDatos['peso'],
            'Observacion' => 'Rutina Kredit',
            'IdConsume' => $id_usuario
        );
        
        $respuesta = $client->call('CrearSolicitud', $param);
        if ( $client->fault ) {
            echo 'No se pudo completar la operacion';
            exit();
        } else {
            $error = $client->getError();
            if ( $error ) {
                echo 'Error: ' . $error;
                exit();
            }
        } 

        if ($respuesta['Bandera'] == "OK"){

            $insert= sqlsrv_query($link,"INSERT into asegurabilidad_colpensiones(id_simulacion, cedula, id_candidato, id_solicitud, respuesta, usuario_creacion, fecha_creacion, valor_asegurado, valor_cumulo) values (".$id_simulacion.", ".$resDatos['cedula'].", ".$respuesta['IdCandidatoO']." ,".$respuesta['IdSolicitudO'].",'".$respuesta['Respuesta']."', '".$id_usuario."', GETDATE(), '".$valorAsegurado."', '".$valorCumuloTotal."')"); 

            if($insert){
                if(str_contains($respuesta['Respuesta'], 'Riesgo Estandar')){
                    $idSolicitudImagen = intval($respuesta['IdSolicitudO']);
                    $paramImg=array(
                    'IdSolicitud'=>$idSolicitudImagen
                    );
                    $enviarImagen = $client->call('EnviarImagen', $paramImg);
                    if($enviarImagen['Bandera']=='OK'){
                        $archivos=1;
                    }else{
                        $archivos=0;
                    }
                }else{
                    $archivos=0;
                }
                $resultado = array( "estado"=>200, "mensaje"=>$respuesta["Respuesta"], 'archivos'=>$archivos);
            }else{
                $resultado = array( "estado"=>300,"mensaje"=> $respuesta["Respuesta"], "mensajeError"=>"Consulta exitoso, Insert Fallido", "error"=>sqlsrv_errors(), "query"=> $insert);  
            }
        }else if($respuesta["Bandera"]=="ERROR"){
            $resultado = array( "estado"=>404, "mensaje"=>$respuesta["Respuesta"], "parametro"=>$param);
        }

}else{


    $param = array('IdSolicitud' =>$_POST['IdSolicitud'] );

    if($_POST['metodo']=="PeticionesCrear"){
        $param["Peticion"]= $_POST['Peticion'];
    }else if($_POST['metodo']=="CerrarSolicitud"){
        $param["Motivo"]= $_POST['Motivo'];
    }

    $respuesta = $client->call($_POST['metodo'],$param );

    if ( $client->fault ) {echo 'No se pudo completar la operacion';exit();} else {$error = $client->getError();if ( $error ) {echo 'Error: ' . $error;exit();}} 

    foreach ($respuesta as $key => $value) {
        $respuesta[$key] = preg_replace('/\s+/', ' ', $respuesta[$key]);
        $respuesta[$key] = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $respuesta[$key]);
        $respuesta[$key] = strtr($respuesta[$key], ["\n" => " ", "\r" => ""]);
    }

    if($respuesta['Bandera'] == "OK"){

        if($_POST['metodo']== "CerrarSolicitud"){
            sqlsrv_query($link, "update asegurabilidad_colpensiones set asegurado = 4 , usuario_cancelacion = '".$_POST['id_usuario']."' , fecha_cancelacion = GETDATE() where id_simulacion = '".$_POST['id_simulacion']."' AND id_solicitud ='".$_POST['IdSolicitud']."'");
        }else if($_POST['metodo']== "EstadoSolicitud"){
            $eventos = json_decode($respuesta['Eventos']);
            $eventos2=end($eventos);
            $negar_credito=0;
            if($eventos2->IdCalificacion!=null){
                if($eventos2->IdCalificacion=='2' ||$eventos2->IdCalificacion=='3' ||$eventos2->IdCalificacion=='5' ||$eventos2->IdCalificacion=='7' ||$eventos2->IdCalificacion=='18' ||$eventos2->IdCalificacion=='19' ||$eventos2->IdCalificacion=='24' ||$eventos2->IdCalificacion=='25' ){
                    $asegurado = 1;
                    $negar_credito = 0;
                }else if($eventos2->IdCalificacion=='6' ||$eventos2->IdCalificacion=='9' ||$eventos2->IdCalificacion=='10' ||$eventos2->IdCalificacion=='11' ||$eventos2->IdCalificacion=='12' ||$eventos2->IdCalificacion=='13' ||$eventos2->IdCalificacion=='17' ||$eventos2->IdCalificacion=='22' ||$eventos2->IdCalificacion=='23' ||$eventos2->IdCalificacion=='28'){
                    $asegurado = 2;
                    $negar_credito = 1;
                }else{
                    $asegurado = 3;
                    $negar_credito = 0;
                }
                $updateCalificacion = "update asegurabilidad_colpensiones set id_calificacion ='".$eventos2->IdCalificacion."', asegurado = '".$asegurado."' where id_solicitud = '".$_POST['IdSolicitud']."'";

                sqlsrv_query($link,$updateCalificacion);
            }
        }
    
       $resultado = array("estado"=>200, "mensaje"=>$respuesta, "negar_credito"=>$negar_credito); 
   }else{
       $resultado = array("estado"=>404, "mensaje"=>$respuesta, "parametros"=> $param);
   }

}
    
echo json_encode($resultado, true);
    
    

?>