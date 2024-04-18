<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(true);

    require_once("../cors.php");
    require_once("../../functions.php");

    $json_Input = file_get_contents('php://input');
    $parametros = json_decode($json_Input);
    $respuesta = array();
    $proceso = $_POST["proceso"];


    $link = conectar_utf();
    $proveedor = '';

    switch ($proceso) {
        case 'consultar':
            # code...
        break;

    }
    
    echo json_encode($respuesta);
?>