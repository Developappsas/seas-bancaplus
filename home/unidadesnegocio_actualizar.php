<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR"){
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar"){
	if ($_REQUEST["gmf".$_REQUEST["id"]] != "1"){
		$_REQUEST["gmf".$_REQUEST["id"]] = "0";
	}
	
	if ($_REQUEST["a".$_REQUEST["id"]] != "1"){
		$_REQUEST["a".$_REQUEST["id"]] = "0";
	}
	
	sqlsrv_query($link,"update unidades_negocio set nombre = '".$_REQUEST["nombre".$_REQUEST["id"]]."', id_empresa = '".$_REQUEST["empresa".$_REQUEST["id"]]."', valor_por_millon_seguro_activos = '".$_REQUEST["valor_por_millon_seguro_activos".$_REQUEST["id"]]."', valor_por_millon_seguro_pensionados = '".$_REQUEST["valor_por_millon_seguro_pensionados".$_REQUEST["id"]]."', valor_por_millon_seguro_colpensiones = '".$_REQUEST["valor_por_millon_seguro_colpensiones".$_REQUEST["id"]]."', gmf = '".$_REQUEST["gmf".$_REQUEST["id"]]."', prefijo_libranza = '".$_REQUEST["prefijo_libranza".$_REQUEST["id"]]."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE(), valor_por_millon_seguro_activos_parcial = '".$_REQUEST["valor_por_millon_seguro_activos_parcial".$_REQUEST["id"]]."', valor_por_millon_seguro_pensionados_parcial = '".$_REQUEST["valor_por_millon_seguro_pensionados_parcial".$_REQUEST["id"]]."', valor_por_millon_seguro_colpensiones_parcial = '".$_REQUEST["valor_por_millon_seguro_colpensiones_parcial".$_REQUEST["id"]]."' where id_unidad = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Unidad de negocio actualizada exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar"){
	if (!$_REQUEST["page"]){
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	$offset = $_REQUEST["page"] * $x_en_x;
	$queryDB = "select id_unidad, nombre from unidades_negocio where id_unidad IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"]){
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by id_unidad OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){

		if ($_REQUEST["b".$fila["id_unidad"]] == "1"){
			$existe_en_simulaciones = sqlsrv_query($link,"select id_unidad_negocio from simulaciones where id_unidad_negocio = '".$fila["id_unidad"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_en_simulaciones)){
				echo "<script>alert('La unidad de negocio ".utf8_decode($fila["nombre"])." no puede ser borrada (Existen tablas con registros asociados)')</script>";
			}
			else{
				sqlsrv_query($link,"delete from tasas2_unidades where id_unidad_negocio = '".$fila["id_unidad"]."'");
				sqlsrv_query($link,"delete from usuarios_unidades where id_unidad_negocio = '".$fila["id_unidad"]."'");	
				sqlsrv_query($link,"delete from unidades_negocio where id_unidad = '".$fila["id_unidad"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'unidadesnegocio.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
