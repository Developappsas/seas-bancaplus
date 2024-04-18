<?php
$json_Input = file_get_contents('php://input');
//echo $json_Input;
$var=json_decode($json_Input,true);

echo $var["id_simulacion"];

?>