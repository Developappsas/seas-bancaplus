<?php
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );
header( 'Content-Type: text/html; charset=UTF-8' );
include ( '../../functions.php' );
$link = conectar();

if($_POST['id_simulacion']){

	$consultar_enfermedades ="SELECT * from declaracion_enfermedades where id_simulacion = '".$_POST['id_simulacion']."'";
	$respuesta_enfermedades = sqlsrv_query($link, $consultar_enfermedades, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if(sqlsrv_num_rows($respuesta_enfermedades)==1){
		$update_enfermedades = sqlsrv_query($link, "update declaracion_enfermedades set asma = ".$_POST['asma']. ",diabetes = ".$_POST['diabetes']. ", cancer = ".$_POST['cancer']. ", vih = ".$_POST['vih']. ", hipertension_arterial = ".$_POST['hipertension_arterial']. ", tiroides = ".$_POST['tiroides']. ", cirugia_bariatrica = ".$_POST['cirugia_bariatrica']. ", tabaquismo = ".$_POST['tabaquismo']. ", enfermedad_pulmonar = ".$_POST['enfermedad_pulmonar']. ",enfermedad_corazon = ".$_POST['enfermedad_corazon']. ",artritis = ".$_POST['artritis']. ",glaucoma = ".$_POST['glaucoma']. ",hepatitis = ".$_POST['hepatitis']. ",otra = '".$_POST['otra']. "',hospitalizado_ultimo_ano = '".$_POST['hospitalizado']. "',operado_ultimos_dos_anos = '".$_POST['operaciones']. "' where id_simulacion = '".$_POST['id_simulacion']."'");

		if($update_enfermedades){
			$response = array(
            'Estado'=>200,
            'Mensaje'=>'Declaracion de enfermedades actualizada'
        	);
		}else{
			$response = array(
            'Estado'=>300,
            'Mensaje'=>'Error al realizar la actualizacion'

        	);
		}

	}else{
		$insert = "INSERT INTO declaracion_enfermedades (id_simulacion,asma,diabetes,cancer,vih,hipertension_arterial,tiroides,cirugia_bariatrica,tabaquismo,enfermedad_pulmonar,enfermedad_corazon,artritis,glaucoma,hepatitis,otra, hospitalizado_ultimo_ano, operado_ultimos_dos_anos )value('".$_POST['id_simulacion']."',".$_POST['asma'].", ".$_POST['diabetes'].", ".$_POST['cancer'].", ".$_POST['vih'].", ".$_POST['hipertension_arterial'].", ".$_POST['tiroides'].", ".$_POST['cirugia_bariatrica'].", ".$_POST['tabaquismo'].", ".$_POST['enfermedad_pulmonar'].", ".$_POST['enfermedad_corazon'].", ".$_POST['artritis'].", ".$_POST['glaucoma'].",".$_POST['hepatitis'].", '".$_POST['otra']."', '".$_POST['hospitalizado_ultimo_ano']."', '".$_POST['operacion_ultimos_dos_anos']."')";

		if(sqlsrv_query($link, $insert)){
			$response = array(
            'Estado'=>202,
            'Mensaje'=>'Insercion de declaracion de enfermedades Exitosa'
        	);
		}else{
			$response = array(
            'Estado'=>303,
            'Mensaje'=>'Error al realizar la insercion',
            'insert'=>$insert
        	);
		}
	}
}else{
	$response = array(
            'Estado'=>404,
            'Mensaje'=>'no se reciben datos necesarios para habilitacion'
        );
}

echo json_encode($response);


?>