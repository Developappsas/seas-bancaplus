<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA"))
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
	
	sqlsrv_query($link, "UPDATE tipos_causalesnorecaudo set nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_tipo = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Tipo causal actualizado exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT id_tipo, nombre from tipos_causalesnorecaudo where id_tipo IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by nombre OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_tipo"]] == "1")
		{
			$existe_en_causales = sqlsrv_query($link, "SELECT id_tipo from causales_norecaudo where id_tipo = '".$fila["id_tipo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_en_causales))
			{
				echo "<script>alert('El tipo causal ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link, "DELETE from tipos_causalesnorecaudo where id_tipo = '".$fila["id_tipo"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'tiposcausalesnorecaudo.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
