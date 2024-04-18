<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    header("Content-Type: application/json; charset=utf-8");  
    
    $method = $_SERVER['REQUEST_METHOD'];

    if($method == "OPTIONS") {
        die();
    }
    
    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);
    
?>