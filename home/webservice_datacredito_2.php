<?php

$identificacion = $_REQUEST['identificacion'];
$apellido = $_REQUEST['apellido'];


//disable wsdl cache  
ini_set("soap.wsdl_cache_enabled", "0");

$servicio = "http://172.24.14.29:8080/dhws3/services/DH2PNClientesService_v1-4?wsdl"; //url del servicio
$trace = true;
$exceptions = true;

$client = new SoapClient($servicio, array('trace' => 1, 'exceptions' => 1, 'encoding' => 'utf-8'));

$wsdl = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:ws="http://ws.hc2.dc.com">
<soapenv:Header/>
<soapenv:Body>
<ws:consultarHC2 soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<xmlConsulta xsi:type="soapenc:string"
xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">&lt;?xml version="1.0" encoding="UTF-8"?&gt;&lt;
</xmlConsulta>
</ws:consultarHC2>
</soapenv:Body>
</soapenv:Envelope>';

$consulta = '<?xml version="1.0" encoding="utf-8"?><Solicitud>
<Solicitud clave="40VXS" identificacion="' . $identificacion . '" primerApellido="' . $apellido . '" producto="64" tipoIdentificacion="1" usuario="16741039" />
</Solicitud>';
   
try {
    $response = $client->consultarHC2($consulta);   
    //echo "Response: " . $response[0];    
} catch (Exception $exc) {
    echo "<p>Error: " . $exc->getMessage() . "</p>";
    return;
}

/*
echo "<p>Request Headers: " . $client->__getLastRequestHeaders() . "</p>";
echo "<p>Request: " . $client->__getLastRequest() . "</p>";
echo "<p>Response Headers: " . $client->__getLastResponseHeaders() . "</p>";
echo "<p>Response: " . $client->__getLastResponse() . "</p>";
*/

header("Content-type: text/xml");
//echo $client->__getLastResponse();
echo $response;

