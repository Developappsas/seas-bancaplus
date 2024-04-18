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
	$existe_nit = sqlsrv_query($link, "SELECT nit from compradores where nit = '".$_REQUEST["nit".$_REQUEST["id"]]."' AND id_comprador != '".$_REQUEST["id"]."'");
	
	if (!(sqlsrv_num_rows($existe_nit))) {
		sqlsrv_query($link, "update compradores set nit = '".$_REQUEST["nit".$_REQUEST["id"]]."', nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', nombre_corto = '".utf8_encode($_REQUEST["nc".$_REQUEST["id"]])."' where id_comprador = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Comprador actualizado exitosamente');</script>";
	} else {
		echo "<script>alert('El NIT del comprador ya se encuentra registrado. Comprador NO actualizado');</script>";
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
	
	$queryDB = "select id_comprador, nombre from compradores where id_comprador IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND (nit like '%".$descripcion_busqueda."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(nombre_corto) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB = $queryDB." order by nombre DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_comprador"]] == "1") {
			$existe_en_ventas = sqlsrv_query($link, "select id_comprador from ventas where id_comprador = '".$fila["id_comprador"]."'");
			
			if (sqlsrv_num_rows($existe_en_ventas)) {
				echo "<script>alert('El comprador ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			} else {
				sqlsrv_query($link, "delete from compradores where id_comprador = '".$fila["id_comprador"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'compradores.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
