<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	sqlsrv_query($link,"update salario_minimo set valor = '".str_replace(",", "", $_REQUEST["sm".$_REQUEST["id"]])."' where id_salariominimo = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Salario minimo actualizado exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_salariominimo from salario_minimo where ano IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND ano = '".$descripcion_busqueda."'";
	}
	
	$queryDB = $queryDB." order by ano DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link,$queryDB,  array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_salariominimo"]] == "1")
		{
			sqlsrv_query($link,"delete from salario_minimo where id_salariominimo = '".$fila["id_salariominimo"]."'");
		}
	}
}

?>
<script>
window.location = 'salariominimo.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
