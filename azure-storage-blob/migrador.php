<?php
    
    $h = 'seas-kredit-pruebas.mysql.database.azure.com';	
	$u = 'adminkredit';
	$pw = 'sd"384b&sa/fgh8(6st4f';
	$db = 'consultas_externas';
	$link = mysqli_connect($h,$u,$pw,$db);

     if (!$link) {
        echo mysqli_error($link);
    }else{
        echo 'conexion exitosa '.rand();
        // $sql = "SHOW TABLES FROM consultas_externas";
        // $resultado = mysqli_query($sql);

        // if (!$resultado) {
        //     echo "Error de BD, no se pudieron listar las tablas\n";
        //     echo 'Error MySQL: ' . mysqli_error($link);
        //     exit;
        // }

        // while ($fila = mysqli_fetch_row($resultado)) {
        //     echo "Tabla: {$fila[0]}\n";
        // }

        // mysqli_free_result($resultado);
    }

?>