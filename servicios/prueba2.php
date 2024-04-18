<?php
$data=array(
    'token' => "pr321",
    'pagare' => "NO",
    'id_usuario'=>"123",
    "id_simulacion"=>"1321"
        
);
$opciones = array(
    'http'=>array(
        'method' => 'POST',
        'content' => json_encode($data)
            
    )
);

$contexto = stream_context_create($opciones);

$json_Input = file_get_contents($urlPrincipal.'/servicios/prueba.php', false, $contexto);


$parametros=json_decode($json_Input);

echo $json_Input;
?>