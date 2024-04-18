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

$queryDB = "SELECT * from etapas_subestados where id_subestado = '".$_REQUEST["id_subestado"]."'";

$existe = sqlsrv_query($link, $queryDB);

if (!(sqlsrv_num_rows($existe)))
{
	sqlsrv_query($link, "INSERT into etapas_subestados (id_etapa, id_subestado) VALUES ('".$_REQUEST["id_etapa"]."', '".$_REQUEST["id_subestado"]."')");
	
	$mensaje = "Subestado asociado exitosamente";
}
else
{
	$mensaje = "El subestado ya ha sido asociado a otra etapa. Por favor seleccione otro subestado para ser asociado";

}

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'etapassubestados.php?id_etapa=<?php echo $_REQUEST["id_etapa"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
