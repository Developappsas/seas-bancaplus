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
	
	sqlsrv_query($link,"update pagadurias set sector = '".$_REQUEST["s".$_REQUEST["id"]]."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."', plazo  = ".$_REQUEST["plazo".$_REQUEST["id"]]." where id_pagaduria = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Pagaduria actualizada exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_pagaduria, nombre from pagadurias where id_pagaduria IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by nombre ASC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_pagaduria"]] == "1")
		{
			$existe_en_empleados = sqlsrv_query($link,"select pagaduria from empleados where pagaduria = '".$fila["nombre"]."'");
			
			$existe_en_descuentosadicionales = sqlsrv_query($link,"select pagaduria from descuentos_adicionales where pagaduria = '".$fila["nombre"]."'");
			
			$existe_en_simulaciones = sqlsrv_query( $link,"select pagaduria from simulaciones where pagaduria = '".$fila["nombre"]."'");
			
			$existe_en_pagaduriaspa = sqlsrv_query($link,"select pagaduria from pagaduriaspa where pagaduria = '".$fila["nombre"]."'");
			
			$existe_en_empleadoscreacion = sqlsrv_query( $link,"select pagaduria from empleados_creacion where pagaduria = '".$fila["nombre"]."'");
			
			if (sqlsrv_num_rows($existe_en_empleados) || sqlsrv_num_rows($existe_en_descuentosadicionales) || sqlsrv_num_rows($existe_en_simulaciones) || sqlsrv_num_rows($existe_en_pagaduriaspa) || sqlsrv_num_rows($existe_en_empleadoscreacion))
			{
				echo "<script>alert('La pagaduria ".utf8_decode($fila["nombre"])." no puede ser borrada (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link,"delete from pagadurias where id_pagaduria = '".$fila["id_pagaduria"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'pagadurias.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
