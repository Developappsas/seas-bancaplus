<?php 
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();
include("top.php"); 

if ($_REQUEST["action"] == "actualizar")
{
	if ($_REQUEST["a".$_REQUEST["id"]] != "1")
	{
		$_REQUEST["a".$_REQUEST["id"]] = "0";
	}
	
	sqlsrv_query($link,"UPDATE planes_seguro set nombre = '".$_REQUEST["nombre".$_REQUEST["id"]]."', valor = '".$_REQUEST["valor".$_REQUEST["id"]]."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_plan = '".$_REQUEST["id"]."'");

	
	
	echo "<script>alert('Plan actualizado exitosamente');</script>";

	
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT id_plan, nombre from planes_seguro where id_plan IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by nombre, id_plan ASC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_plan"]] == "1")
		{
			$existe_en_simulaciones = sqlsrv_query($link,"SELECT id_plan_seguro from simulaciones where id_plan_seguro = '".$fila["id_plan"]."'");
			
			if (sqlsrv_num_rows($existe_en_simulaciones))
			{
				echo "<script>alert('El plan ".utf8_decode($fila["nombre"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				
				sqlsrv_query($link,"delete from planes_seguro where id_plan = '".$fila["id_plan"]."'");
				
			}
		}
	}
}

?>
<script>
window.location = 'planesseguro.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
