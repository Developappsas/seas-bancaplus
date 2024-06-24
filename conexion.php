<?php
	header('Content-Type: text/html; charset=utf-8');
    //$serverName = "serverName\sqlexpress"; //serverName\instanceName
    $serverName = "database";
    $user = "sa";
    $passwd = 'Password12345';
    $db = "seas";
    // Puesto que no se han especificado UID ni PWD en el array  $connectionInfo,
    // La conexión se intentará utilizando la autenticación Windows.
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 0);
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