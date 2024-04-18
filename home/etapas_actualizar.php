<?php  
include ('../functions.php'); 

?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	if ($_REQUEST["a".$_REQUEST["id"]] != "1") {
		$_REQUEST["a".$_REQUEST["id"]] = "0";
	}
	
	sqlsrv_query($link, "update etapas set nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."', estado = '".$_REQUEST["a".$_REQUEST["id"]]."' where id_etapa = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Etapa actualizada exitosamente');</script>";
}
else if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "SELECT id_etapa, nombre from etapas where id_etapa IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	}
	
	$queryDB = $queryDB." order by nombre  OFFSET ".$offset." ROWS FETCH NEXT 10 ROWS Only";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_etapa"]] == "1") {
			sqlsrv_query($link, "delete from etapas_subestados where id_etapa = '".$fila["id_etapa"]."'");
			sqlsrv_query($link, "delete from etapas where id_etapa = '".$fila["id_etapa"]."'");
		}
	}
}

?>
<script>
window.location = 'etapas.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
