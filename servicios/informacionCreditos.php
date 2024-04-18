<?php
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
    
    include_once ('../functions.php');
    include_once ('../function_blob_storage.php');
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();
    $id_simulacion=$_GET["id_simulacion"];

    consultar_creditos($id_simulacion);


    function consultar_creditos($id_simulacion) 
    {
        global $link;  
        $data = array();
        $consultarInformacionCredito="SELECT * FROM simulaciones WHERE id_simulacion='".$id_simulacion."'";
        $queryInformacionCredito=sqlsrv_query($link,$consultarInformacionCredito);
        $resInformacionCredito=sqlsrv_fetch_array($queryInformacionCredito, SQLSRV_FETCH_ASSOC);

        switch($resInformacionCredito["opcion_credito"])
			{
				case "CLI":	$opcion_cuota = $resInformacionCredito["opcion_cuota_cli"];
							break;
				case "CCC":	$opcion_cuota = $resInformacionCredito["opcion_cuota_ccc"];
							break;
				case "CMP":	$opcion_cuota = $resInformacionCredito["opcion_cuota_cmp"];
							break;
				case "CSO":	$opcion_cuota = $resInformacionCredito["opcion_cuota_cso"];
							break;
			}


        $data=array("id_simulacion"=>$resInformacionCredito["id_simulacion"],
        "nro_libranza"=>$resInformacionCredito["nro_libranza"],
        "plazo"=>$resInformacionCredito["plazo"],
        "tasa_interes"=>$resInformacionCredito["tasa_interes"],
        "valor_credito"=>$resInformacionCredito["valor_credito"],
        "opcion_cuota"=>($opcion_cuota),
        "cedula"=>$resInformacionCredito["cedula"]
        );

        header("HTTP/2.0 200 OK");
        echo json_encode($data);
    }

    ?>