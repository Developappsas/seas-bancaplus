<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
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
	
	sqlsrv_query($link,"UPDATE subestados set decision = '".$_REQUEST["d".$_REQUEST["id"]]."', nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_subestado = '".str_replace("_", " ", $_REQUEST["id"])."'");
	
	echo "<script>alert('Subestado actualizado exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT id_subestado, nombre from subestados where id_subestado IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by decision DESC, nombre  OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_subestado"]] == "1")
		{
			$existe_en_simulaciones = sqlsrv_query( $link,"SELECT id_simulacion from simulaciones where id_subestado = '".$fila["id_subestado"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_en_simulaciones))
			{
				echo "<script>alert('El subestado ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link,"DELETE from subestados_usuarios where id_subestado = '".$fila["id_subestado"]."'");
				
				sqlsrv_query($link,"DELETE from subestados where id_subestado = '".$fila["id_subestado"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'subestados.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
