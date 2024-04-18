<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	$existe_nit = sqlsrv_query($link,"select nit from vendedores where nit = '".$_REQUEST["nit".$_REQUEST["id"]]."' AND id_vendedor != '".$_REQUEST["id"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (!(sqlsrv_num_rows($existe_nit)))
	{
		sqlsrv_query($link,"update vendedores set nit = '".$_REQUEST["nit".$_REQUEST["id"]]."', nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."' where id_vendedor = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Vendedor actualizado exitosamente');</script>";
	}
	else
	{
		echo "<script>alert('El NIT del vendedor ya se encuentra registrado. Vendedor NO actualizado');</script>";
	}
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_vendedor, nombre from vendedores where id_vendedor IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND (nit like '%".$descripcion_busqueda."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB = $queryDB." order by nombre OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_vendedor"]] == "1")
		{
			$existe_en_simulaciones_ext = sqlsrv_query($link, "select id_vendedor from simulaciones_ext where id_vendedor = '".$fila["id_vendedor"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_en_simulaciones_ext))
			{
				echo "<script>alert('El vendedor ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link,"delete from vendedores where id_vendedor = '".$fila["id_vendedor"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'vendedores.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
