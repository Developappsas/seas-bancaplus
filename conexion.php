<?php
	header('Content-Type: text/html; charset=utf-8');
    //$serverName = "serverName\sqlexpress"; //serverName\instanceName
    $serverName = "tcp:az-ias-use-dev-qa-engine-k.database.windows.net,1433";
    $user = "seas_kredit";
    $passwd = "cDrTrFgx%SIF#*8G*1996";
    $db = "SEAS_09_05_23";
    // Puesto que no se han especificado UID ni PWD en el array  $connectionInfo,
    // La conexión se intentará utilizando la autenticación Windows.
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 1);
	$link = sqlsrv_connect($serverName, $connectionInfo);
	if ($link) {
		echo "Conexión establecida.";
		return $link;
	}else{
		echo json_encode('Error al conectarse con la base de datos');
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
			}
		}
	}
?>