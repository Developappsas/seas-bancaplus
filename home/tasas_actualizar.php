<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	$existe = sqlsrv_query($link, "select plazoi from tasas".$sufijo_sector." where ((plazoi <= '".$_REQUEST["pi".$_REQUEST["id"]]."' AND plazof >= '".$_REQUEST["pi".$_REQUEST["id"]]."') OR (plazoi <= '".$_REQUEST["pf".$_REQUEST["id"]]."' AND plazof >= '".$_REQUEST["pf".$_REQUEST["id"]]."')) AND id_tasa != '".$_REQUEST["id"]."'");
	
	if (!(sqlsrv_num_rows($existe)))
	{
		sqlsrv_query($link, "update tasas".$sufijo_sector." set plazoi = '".$_REQUEST["pi".$_REQUEST["id"]]."', plazof = '".$_REQUEST["pf".$_REQUEST["id"]]."' where id_tasa = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Registro actualizado exitosamente');</script>";
	}
	else
	{
		echo "<script>alert('El plazo se traslapa con otro ya existente. Registro NO actualizado');</script>";
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
	
	$queryDB = "select id_tasa from tasas".$sufijo_sector." where id_tasa IS NOT NULL";
	
	$queryDB = $queryDB." order by plazoi OFFSET ".$offset." ROWS FETCH NEXT 10 ROWS Only";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_tasa"]] == "1")
		{
			sqlsrv_query($link, "delete from tasas2".$sufijo_sector." where id_tasa = '".$fila["id_tasa"]."'");			
			sqlsrv_query($link, "delete from tasas".$sufijo_sector." where id_tasa = '".$fila["id_tasa"]."'");
		}
	}
}

?>
<script>
window.location = 'tasas.php?sector=<?php echo $_REQUEST["sector"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
