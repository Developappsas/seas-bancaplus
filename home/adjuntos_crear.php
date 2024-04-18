<?php
include('../functions.php');
include('../function_blob_storage.php');
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_ADJUNTOS"]) {
	exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$link = conectar_utf();

$upmax_rs = sqlsrv_query($link, "SELECT valor from parametros where codigo IN ('UPMAX') order by codigo");
$fila1 = sqlsrv_fetch_array($upmax_rs);
$upmax = $fila1['valor'];

?>
<?php include("top.php"); ?>
<?php

if (($_FILES["archivo"]["size"] / 1024) <= $upmax) {
	if ($_REQUEST["privado"] != "1") {
		$_REQUEST["privado"] = "0";
	}
	
	if (strcmp($_FILES["archivo"]["name"], "")) {
		
		$uniqueID = uniqid();
		$extension = explode("/", $_FILES["archivo"]['type']);
		$nombreArc = md5(rand() + intval($_REQUEST["id_simulacion"])) . "." . $extension[1];
		
	
	
		$fechaa = new DateTime();
		$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
		$metadata1 = array(
			'id_simulacion' => $_REQUEST["id_simulacion"],
			'descripcion' => ($nombreArc),
			'usuario_creacion' => $_SESSION["S_LOGIN"],
			'fecha_creacion' => $fechaFormateada
		);

		$cargado = false;
		try{
			$cargado = upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"] . "/adjuntos/" . $nombreArc, $metadata1);
		} catch (ServiceException $exception) {
            $mensaje = $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
            throw $exception;
       }
		
	}
		
	if($cargado){
    	$query = ("insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('" . $_REQUEST["id_simulacion"] . "', '" . $_REQUEST["id_tipo"] . "', '" . basename($_FILES["archivo"]['name'], "." . $extension[1]) . " / " . $_REQUEST["descripcion"] . "', '" . $nombreArc . "', '" . $nombreArc . "', '" . $_REQUEST["privado"] . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");
		sqlsrv_query($link, $query);
			
		$mensaje = "Archivo ingresado exitosamente";
     }else{
        $mensaje = "Error al Cargar el archivo";
     }
} else {
	$mensaje = "El tama&ntilde;o del archivo supera lo permitido (" . number_format($upmax / 1024, 2, ".", ",") . " MB). Archivo NO ingresado";
}

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'adjuntos.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>