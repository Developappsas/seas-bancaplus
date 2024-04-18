<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	$existe_nit = sqlsrv_query( $link,"SELECT nit from pagaduriaspa where nit = '".$_REQUEST["nit".$_REQUEST["id"]]."' AND id_pagaduriapa != '".$_REQUEST["id"]."'");
	
	if (!(sqlsrv_num_rows($existe_nit)))
	{
		sqlsrv_query($link,"update pagaduriaspa set nit = '".$_REQUEST["nit".$_REQUEST["id"]]."', pa = '".$_REQUEST["pa".$_REQUEST["id"]]."' where id_pagaduriapa = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Asociacion actualizada exitosamente');</script>";
	}
	else
	{
		echo "<script>alert('El NIT de la pagaduria ya se encuentra registrado. Pagaduria NO actualizada');</script>";
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
	
	$queryDB = "SELECT id_pagaduriapa, pagaduria from pagaduriaspa where id_pagaduriapa IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND (UPPER(pagaduria) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(pa) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB = $queryDB." order by pagaduria  OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_pagaduriapa"]] == "1")
		{
			sqlsrv_query($link,"delete from pagaduriaspa where id_pagaduriapa = '".$fila["id_pagaduriapa"]."'");
		}
	}
}

?>
<script>
window.location = 'pagaduriaspa.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
