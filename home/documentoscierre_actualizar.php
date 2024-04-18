<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	if ($_REQUEST["a".$_REQUEST["id"]] != "1")
	{
		$_REQUEST["a".$_REQUEST["id"]] = "0";
	}
	
	sqlsrv_query($link, "update documentos_cierre set nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_documentocierre = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Documento actualizado exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_documentocierre, nombre from documentos_cierre where id_documentocierre IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by nombre DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_documentocierre"]] == "1")
		{
			$existe_en_ventas_detalle_documentos = sqlsrv_query($link, "select id_documentocierre from ventas_detalle_documentos where id_documentocierre = '".$fila["id_documentocierre"]."'");
			
			$existe_en_ventas_detalle_documentos_ext = sqlsrv_query($link, "select id_documentocierre from ventas_detalle_documentos_ext where id_documentocierre = '".$fila["id_documentocierre"]."'");
			
			if (sqlsrv_num_rows($existe_en_ventas_detalle_documentos) || sqlsrv_num_rows($existe_en_ventas_detalle_documentos_ext))
			{
				echo "<script>alert('El documento ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link, "delete from documentos_cierre where id_documentocierre = '".$fila["id_documentocierre"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'documentoscierre.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
