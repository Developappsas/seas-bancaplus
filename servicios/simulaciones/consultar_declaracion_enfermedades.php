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
		$enfermedad = sqlsrv_fetch_array($respuesta_enfermedades, SQLSRV_FETCH_ASSOC);
		$param =array(
			'asma'=>$enfermedad['asma'],
			'diabetes'=>$enfermedad['diabetes'],
			'cancer'=>$enfermedad['cancer'],
			'vih'=>$enfermedad['vih'],
			'hipertension_arterial'=>$enfermedad['hipertension_arterial'],
			'tiroides'=>$enfermedad['tiroides'],
			'cirugia_bariatrica'=>$enfermedad['cirugia_bariatrica'],
			'tabaquismo'=>$enfermedad['tabaquismo'],
			'enfermedad_pulmonar'=>$enfermedad['enfermedad_pulmonar'],
			'enfermedad_corazon'=>$enfermedad['enfermedad_corazon'],
			'artritis'=>$enfermedad['artritis'],
			'glaucoma'=>$enfermedad['glaucoma'],
			'hepatitis'=>$enfermedad['hepatitis'],
			'otra'=>$enfermedad['otra'],
			'hospitalizado_ultimo_ano'=>$enfermedad['hospitalizado_ultimo_ano'],
			'operado_ultimos_dos_anos'=>$enfermedad['operado_ultimos_dos_anos']
		);

		$response = array(
			'estado'=>200,
			'mensaje'=>'consulta exitosa',
			'datos'=>$param
		);

	}else{
		$response = array(
			'estado'=>300,
			'mensaje'=>'Cliente sin registro'
		);
	}

}else{
	$response = array(
		'estado'=>404,
		'mensaje'=>'No se reciben datos necesarios'
	);	
}

echo json_encode($response);



?>