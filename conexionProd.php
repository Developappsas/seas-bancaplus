<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $serverName = "az-ias-use2-prd-exp-k.database.windows.net";
    $user = "apena";
    $passwd = '$2y$10$Oc9PfGBS/AYkgyeIjEoRn';
    $db = "seas";
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 1);
	$link = sqlsrv_connect($serverName, $connectionInfo);
	if ($link) {
		echo "Conexionn establecida4.";
		//return $link;
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


    




    $rs=sqlsrv_query($link, "select top 10000 * from simulaciones");
    while($array=sqlsrv_fetch_array($rs)){
        echo $array['id_simulacion']. "<br>";
    }    



  ?>  