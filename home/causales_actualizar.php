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
	
	sqlsrv_query( $link,"update causales set tipo_causal = '".$_REQUEST["t".$_REQUEST["id"]]."', nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_causal = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Causal actualizada exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT id_causal, nombre from causales where id_causal IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by tipo_causal DESC, nombre DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_causal"]] == "1")
		{
			$existe_en_simulaciones = sqlsrv_query( $link,"SELECT id_causal from simulaciones where id_causal = '".$fila["id_causal"]."'");
			
			if (sqlsrv_num_rows($existe_en_simulaciones))
			{
				echo "<script>alert('La causal ".utf8_decode($fila["nombre"])." no puede ser borrada (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query( $link,"delete from causales where id_causal = '".$fila["id_causal"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'causales.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
