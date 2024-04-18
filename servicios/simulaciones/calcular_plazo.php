<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    include ('../../functions.php');

    $link = conectar_utf();

    if(isset($_POST['pagaduria']) && isset($_POST['fecha_nacimiento']) && isset($_POST['sexo']) && isset($_POST['nivel_contratacion'])){

        $idSimulacion = $_POST['id_simulacion'];

        $pagaduria = $_POST['pagaduria'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $nivel_contratacion = $_POST['nivel_contratacion'];

		$plazos = array();

		$plazo_credito = 0;
		$plazo_escogido = 'Plazo NO Escogido';

        $queryPlazoPag = "SELECT TOP 1 p.nombre, p.sector, p.plazo FROM pagadurias p WHERE p.nombre = '".$_POST['pagaduria']."'";
		
        $queryPlazoNivC = "SELECT TOP 1 nc.plazo FROM nivel_contratacion nc WHERE nc.nivel_Contratacion_Descripcion = '".$_POST['nivel_contratacion']."'";
		$queryPlazoEdad = "SELECT TOP 1 emc.descripcion, datediff(YEAR, '".$_POST['fecha_nacimiento']."', GETDATE()) AS edad_actual, DATEDIFF(MONTH, '".$_POST['fecha_nacimiento']."', GETDATE()) AS edad_actual_meses, emc.edad_maxima, (emc.edad_maxima * 12) AS meses_edad_maxima,  (emc.edad_maxima * 12) - DATEDIFF(MONTH, '".$_POST['fecha_nacimiento']."', GETDATE()) AS plazo_diferencia, emc.plazo_minimo, emc.monto_maximo FROM edad_maxima_credito emc WHERE emc.sexo = '".$_POST['sexo']."' AND emc.nivel_contratacion = '".$_POST['nivel_contratacion']."' AND (emc.edad_maxima * 12) - DATEDIFF(MONTH, '".$_POST['fecha_nacimiento']."', GETDATE()) > emc.plazo_minimo AND DATEDIFF(YEAR, '".$_POST['fecha_nacimiento']."', GETDATE()) >= emc.edad_maxima_inicio AND DATEDIFF(YEAR, '".$_POST['fecha_nacimiento']."', GETDATE()) < emc.edad_maxima_fin ";

        $conPlazoPag = sqlsrv_query($link, $queryPlazoPag, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $queryPlazoNivC = sqlsrv_query($link, $queryPlazoNivC, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $queryPlazoEdad = sqlsrv_query($link, $queryPlazoEdad, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if ($conPlazoPag && sqlsrv_num_rows($conPlazoPag) > 0) {
        	$datosPlazo = sqlsrv_fetch_array($conPlazoPag, SQLSRV_FETCH_ASSOC);
        	$plazos["plazo"][0] = $datosPlazo["plazo"];
        	$plazos["desc_plazo"][0] = 'Pagaduria';
        }else{
        	$plazos["plazo"][0] = $datosPlazo["plazo"];
        	$plazos["desc_plazo"][0] = 'Pagaduria';
        }

        if ($queryPlazoNivC && sqlsrv_num_rows($queryPlazoNivC) > 0) {
			$datosPlazoNivC = sqlsrv_fetch_array($queryPlazoNivC, SQLSRV_FETCH_ASSOC);
			$plazos["plazo"][1] = $datosPlazoNivC["plazo"];
        	$plazos["desc_plazo"][1] = 'Nivel Contratacion';
        }else{
        	$plazos["plazo"][1] = 0;
        	$plazos["desc_plazo"][1] = 'Nivel Contratacion';
        }

        $monto_maximo = 0;

        if ($queryPlazoEdad && sqlsrv_num_rows($queryPlazoEdad) > 0) {
			$datosPlazoEdad = sqlsrv_fetch_array($queryPlazoEdad, SQLSRV_FETCH_ASSOC);
			$datosPlazoEdad["plazo_diferencia"];

			$plazos["desc_plazo"][2] = $datosPlazoEdad["descripcion"];
			$plazos["plazo"][2] = $datosPlazoEdad["plazo_diferencia"];
			$monto_maximo = intval($datosPlazoEdad["monto_maximo"]);
			
			$supera_rango_edad = 'NO';
        }else{
        	$plazos["desc_plazo"][2] = 'Edad';
			$plazos["plazo"][2] = 0;
			$supera_rango_edad = 'SI';
        }

       	$plazo_menor = $plazos["plazo"][0];

		for($i=0; $i<count($plazos["plazo"]); $i++){
		    if($plazos["plazo"][$i] <= $plazo_menor && $plazos["plazo"][$i] > 0){
		        $plazo_credito = $plazos["plazo"][$i];
		        $plazo_escogido = $plazos["desc_plazo"][$i];
		        $plazo_menor = $plazos["plazo"][$i];
		    }
		}

		if($supera_rango_edad == 'SI'){
			$mensaje_simulador = 'No Existe una Politica para la Edad del cliente';
		}else{
			if($monto_maximo > 0){
				$monto_maximo_p = puntuar_miles($monto_maximo);
			}else{
				$monto_maximo_p = 0;
			}
			$mensaje_simulador = 'El Plazo Sugerido es: <b style="font-weight: bold;">'.$plazo_credito.' meses</b> <br>Monto máximo de <b style="font-weight: bold;">$'.$monto_maximo_p.'</b><br>Plazo escogido por: '.$plazo_escogido;
		}

        $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'mensaje_simulador' => $mensaje_simulador, 'plazo' => $plazo_credito, 'plazo_escogido' => $plazo_escogido, 'monto_maximo' => $monto_maximo);
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }

    echo json_encode($data);
?>