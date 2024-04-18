<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	$queryDB = "select id_banco,nro_cuenta from cuentas_bancarias where id_banco = '".$_REQUEST["id_banco".$_REQUEST["id"]]."'";
	
	$queryDB .= " AND nro_cuenta = '".$_REQUEST["nro_cuenta".$_REQUEST["id"]]."' AND id_cuenta != '".$_REQUEST["id"]."'";
	
	$existe_cuenta = sqlsrv_query($link, $queryDB);
	
	if (!(sqlsrv_num_rows($existe_cuenta)))
	{
		sqlsrv_query($link, "update cuentas_bancarias set nombre = '".$_REQUEST["nombre".$_REQUEST["id"]]."', id_banco = '".$_REQUEST["id_banco".$_REQUEST["id"]]."', tipo_cuenta = '".$_REQUEST["tipo_cuenta".$_REQUEST["id"]]."', nro_cuenta = '".$_REQUEST["nro_cuenta".$_REQUEST["id"]]."' where id_cuenta = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Cuenta actualizada exitosamente');</script>";
	}
	else
	{
		echo "<script>alert('Ya existe un numero de cuenta para el banco seleccionado');</script>";
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
	
	$queryDB = "SELECT cb.* from cuentas_bancarias cb INNER JOIN bancos ba ON cb.id_banco = ba.id_banco where cb.id_cuenta IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (UPPER(cb.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(cb.nro_cuenta) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB .= " order by cb.nombre, cb.nro_cuenta DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_cuenta"]] == "1")
		{
			$existe_en_giros = sqlsrv_query($link, "SELECT id_cuentabancaria from giros where id_cuentabancaria = '".$fila["id_cuenta"]."'");
			
			if (sqlsrv_num_rows($existe_en_giros))
			{
				echo "<script>alert('La cuenta ".utf8_decode($fila["nombre"])." no puede ser borrada (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link, "delete from cuentas_bancarias where id_cuenta = '".$fila["id_cuenta"]."'");
			}
		}
	}
}





?>
<script>
window.location = 'cuentasbancarias.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
