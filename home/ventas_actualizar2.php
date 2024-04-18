<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$json = file_get_contents('php://input');

$data = json_decode($json, true);

$link = conectar();

if ($data["ext"])
	$sufijo = "_ext";
	
if ($data["action"] == "actualizar")
{
	if ($data["fecha_corte"])
		$fecha_corte = "'".$data["fecha_corte"]."'";
	else
		$fecha_corte = "NULL";
	
	if ($data["nro_venta"])
		$nro_venta = "'".$data["nro_venta"]."'";
	else
		$nro_venta = "NULL";

	 sqlsrv_query($link, "declare @id_venta int");
	 $exec = sqlsrv_query($link,"
	 declare @id_venta int;
	 EXEC spVentas 'ACTUALIZAR_VENTA', '".$data["ext"]."', '".$data["fecha_anuncio"]."', '".$data["fecha"]."', ".$fecha_corte.", '".$data["id_comprador"]."', '".$data["tasa_venta"]."', '".$data["modalidad_prima"]."', NULL, NULL, ".$nro_venta.", '".$data["id_venta"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, @id_venta OUTPUT");
	 if ($exec == false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
				echo "error ACTUALIZAR_VENTA";
			}
		}
	}

	
}

if ($data["action"] == "dividir")
{
	$rs1 = sqlsrv_query($link,"select fecha_anuncio, fecha, fecha_corte, id_comprador, tasa_venta, modalidad_prima, tipo from ventas".$sufijo." WHERE id_venta = '".$data["id_venta"]."'");

	

	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	if ($fila1["fecha_corte"])
		$fecha_corte = "'".$fila1["fecha_corte"]."'";
	else
		$fecha_corte = "NULL";
	

		
	$exec= sqlsrv_query($link,"
	declare @id_venta int;
	EXEC spVentas 'INSERTAR_VENTA', '".$data["ext"]."', '".$fila1["fecha_anuncio"]."', '".$fila1["fecha"]."', ".$fecha_corte.", '".$fila1["id_comprador"]."', '".$fila1["tasa_venta"]."', '".$fila1["modalidad_prima"]."', 'ALI', '".$fila1["tipo"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$_SESSION["S_LOGIN"]."', @id_venta OUTPUT;
	select @id_venta as m;
	");

	
	if ($exec == false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
				echo "error INSERTAR_VENTA";
			}
		}
	}

	

	$fila = sqlsrv_fetch_array($exec);
	
	$id_venta_division = $fila["m"];

	
}

$queryDB = "select ved.id_ventadetalle, ved.id_simulacion, ved.fecha_primer_pago, ved.cuota_desde, ved.cuota_hasta from ventas_detalle".$sufijo." ved INNER JOIN simulaciones".$sufijo." si ON ved.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre WHERE ved.id_venta = '".$data["id_venta"]."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if (!$data["ext"])
{
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}

if ($data["descripcion_busqueda3"])
{
	$queryDB .= " AND (1 = 0";
	
	$cedulas = explode(",", $data["descripcion_busqueda3"]);
	
	foreach ($cedulas as $ced)
	{
		$queryDB .= " OR si.cedula = '".trim($ced)."' OR si.nro_libranza = '".trim($ced)."'";
	}
	
	$queryDB .= ")";
}

$queryDB .= " order by abs(si.cedula), ved.id_ventadetalle";



$rs = sqlsrv_query($link,$queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	if ($data["action"] == "actualizar")
	{
		if ($data["chk".$fila["id_ventadetalle"]] == "1")
		{
			
			$exec=sqlsrv_query($link,"
			declare @id_venta int;
			EXEC spVentas 'ELIMINAR_VENTA_DETALLE', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$fila["id_ventadetalle"]."', NULL, @id_venta OUTPUT");
			if ($exec == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "error ELIMINAR_VENTA_DETALLE";
					}
				}
			}
		}
		else
		{
			
			$exec= sqlsrv_query( $link,"
			declare @id_venta int;
			EXEC spVentas 'ACTUALIZAR_VENTA_DETALLE', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$data["fecha_primer_pago".$fila["id_ventadetalle"]]."', '".$data["cuota_desde".$fila["id_ventadetalle"]]."', '".$data["cuota_hasta".$fila["id_ventadetalle"]]."', NULL, NULL, NULL, '".$fila["id_ventadetalle"]."', NULL, @id_venta OUTPUT");
			if ($exec == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "error ACTUALIZAR_VENTA_DETALLE";
					}
				}
			}
		}
	}
	else if ($data["action"] == "recomprar")
	{
		if ($data["recomp".$fila["id_ventadetalle"]] == "1")
		{
			
			$exec= sqlsrv_query($link,"
			declare @id_venta int;
			EXEC spVentas 'ACTUALIZAR_VENTA_DETALLE_RECOMPRAR', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '1', '".$fila["id_ventadetalle"]."', NULL, @id_venta OUTPUT");
			if ($exec == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "error ACTUALIZAR_VENTA_DETALLE_RECOMPRAR";
					}
				}
			}
		}
	}
	else if ($data["action"] == "dividir")
	{
		if ($data["chk".$fila["id_ventadetalle"]] == "1")
		{

			$exec= sqlsrv_query($link,"EXEC spVentas 'INSERTAR_VENTA_DETALLE', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$id_venta_division."', '".$fila["id_simulacion"]."', '".$fila["fecha_primer_pago"]."', '".$fila["cuota_desde"]."', '".$fila["cuota_hasta"]."', NULL, NULL, NULL, NULL, NULL, '".$data["id_venta"]."' ");
			if ($exec == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "error INSERTAR_VENTA_DETALLE";
					}
				}
			}
			
			$exec2= sqlsrv_query($link,"
			declare @id_venta int;
			EXEC spVentas 'ELIMINAR_VENTA_DETALLE', '".$data["ext"]."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$fila["id_ventadetalle"]."', NULL, @id_venta OUTPUT");
			if ($exec2 == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "error ELIMINAR_VENTA_DETALLE";
					}
				}
			}
		}
	}
}

if ($data["action"] == "actualizar")
{
	$mensaje = "Venta actualizada exitosamente";
	
	$url = "ventas_actualizar.php?ext=".$data["ext"]."&id_venta=".$data["id_venta"]."&fecha_inicialbd=".$data["fecha_inicialbd"]."&fecha_inicialbm=".$data["fecha_inicialbm"]."&fecha_inicialba=".$data["fecha_inicialba"]."&fecha_finalbd=".$data["fecha_finalbd"]."&fecha_finalbm=".$data["fecha_finalbm"]."&fecha_finalba=".$data["fecha_finalba"]."&id_compradorb=".$data["id_compradorb"]."&modalidadb=".$data["modalidadb"]."&descripcion_busqueda=".$data["descripcion_busqueda"]."&descripcion_busqueda2=".$data["descripcion_busqueda2"]."&descripcion_busqueda3=".$data["descripcion_busqueda3"]."&estadob=".$data["estadob"]."&page=".$data["page"];
}

if ($data["action"] == "recomprar")
{
	$mensaje = "Credito(s) recomprado(s) exitosamente";
	
	$url = "ventas_actualizar.php?ext=".$data["ext"]."&id_venta=".$data["id_venta"]."&fecha_inicialbd=".$data["fecha_inicialbd"]."&fecha_inicialbm=".$data["fecha_inicialbm"]."&fecha_inicialba=".$data["fecha_inicialba"]."&fecha_finalbd=".$data["fecha_finalbd"]."&fecha_finalbm=".$data["fecha_finalbm"]."&fecha_finalba=".$data["fecha_finalba"]."&id_compradorb=".$data["id_compradorb"]."&modalidadb=".$data["modalidadb"]."&descripcion_busqueda=".$data["descripcion_busqueda"]."&descripcion_busqueda2=".$data["descripcion_busqueda2"]."&estadob=".$data["estadob"]."&page=".$data["page"];
}

if ($data["action"] == "dividir")
{
	$mensaje = "Venta dividida exitosamente";
	
	$url = "ventas.php?ext=".$data["ext"];
}

echo json_encode("mensaje->".$mensaje."|url->".$url);

?>

