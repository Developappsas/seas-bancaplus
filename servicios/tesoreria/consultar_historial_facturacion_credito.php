<?php
  include ('../../functions.php');
$link = conectar_utf();

if ($_POST["exe"]=="consultarHistorialFacturado") 
{
	$id_simulacion=$_POST["id_simulacion"];
	$data=array();	
	$consultarPagoComisiones = "SELECT CASE WHEN a.facturado='1' THEN 'SI' ELSE 'NO' END AS facturado_descripcion, a.*,concat(b.nombre,' ',b.apellido) as nombre_usuario FROM hst_facturacion_creditos a LEFT JOIN usuarios b ON a.id_usuario=b.id_usuario WHERE a.id_simulacion='".$id_simulacion."'";
	$rs = sqlsrv_query($link, $consultarPagoComisiones);
	while ($fila = sqlsrv_fetch_array($rs)) {
		$data[]=array(
			trim("facturado")=>trim($fila["facturado_descripcion"]),
			trim("nombre_usuario")=>trim($fila["nombre_usuario"]),
			trim("fecha")=>trim($fila["fecha"])
	  	);
	}
	
	$results = array(
		trim("sEcho") => trim("1"),
		trim("iTotalRecords") => trim(count($data)),
		trim("iTotalDisplayRecords") => trim(count($data)),
		trim("aaData") => $data
	);
	echo json_encode($results);
}

?>