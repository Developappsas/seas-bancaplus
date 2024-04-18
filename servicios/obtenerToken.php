<?php

$ruta = "https://cloud.uipath.com/";

$data = array('grant_type'=> 'client_credentials',
	'client_id'=>'e2b08a17-288d-4076-93e8-1a7671c0cdd0',
	'client_secret'=>'D!kCc3H%7B^iCewY',
	'scope'=>'OR.Queues');

$content = http_build_query($data);
$opciones = array(
			'http'=>array(
				'method' => 'POST',
				'header' => "Content-Type:  application/x-www-form-urlencoded"."\r\n".
				"Content-Length: ".strlen($content)."\r\n".
                "User-Agent:MyAgent/1.0\r\n",
				"content" => $content
			)
		);

$contexto = stream_context_create($opciones);
$json_Input = file_get_contents($ruta.'identity_/connect/token', true, $contexto);

if($json_Input){
	$data = json_decode($json_Input);
	
	if(isset($data->access_token)){
		$token = $data->access_token;			
		header("HTTP/2.0 200 OK");
		$response = array( "code"=>"200","mensaje"=>"Token obtenido.", "token" => $token);
	}
}else{
	header("HTTP/2.0 200 OK");
	$response = array( "code"=>"406","mensaje"=>"No se encuentra contenido del servidor");
}

echo json_encode($response);
?>