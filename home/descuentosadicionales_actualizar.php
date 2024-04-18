<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	$query = "select nombre from descuentos_adicionales where pagaduria IN (select pagaduria from descuentos_adicionales where id_descuento = '".$_REQUEST["id"]."') AND nombre = '".$_REQUEST["nombre".$_REQUEST["id"]]."' AND id_descuento != '".$_REQUEST["id"]."'";
	$existe_descuento = sqlsrv_query($link, $query);
	
	if (!(sqlsrv_num_rows($existe_descuento))) 	{
		if ($_REQUEST["a".$_REQUEST["id"]] != "1")
		{
			$_REQUEST["a".$_REQUEST["id"]] = "0";
		}
		$query = "update descuentos_adicionales set nombre = '".$_REQUEST["nombre".$_REQUEST["id"]]."', porcentaje = '".$_REQUEST["porcentaje".$_REQUEST["id"]]."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_descuento = '".$_REQUEST["id"]."'";
		sqlsrv_query($link, $query);
		
		echo "<script>alert('Descuento actualizado exitosamente');</script>";
	} else {
		echo "<script>alert('Ya existe un descuento nombrado de la misma forma asociado a la pagaduria. Descuento NO actualizado');</script>";
	}
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_descuento, nombre from descuentos_adicionales where id_descuento IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND (UPPER(pagaduria) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB = $queryDB." order by pagaduria, id_descuento DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_descuento"]] == "1") {
			$query = "select id_descuento from simulaciones_descuentos where id_descuento = '".$fila["id_descuento"]."'";
			$existe_en_simulaciones = sqlsrv_query($link, $query);
			
			if (sqlsrv_num_rows($existe_en_simulaciones)) {
				echo "<script>alert('El descuento ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			} else {
				$query = "delete from descuentos_adicionales where id_descuento = '".$fila["id_descuento"]."'";
				sqlsrv_query($link, $query);
			}
		}
	}
}

?>
<script>
window.location = 'descuentosadicionales.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
