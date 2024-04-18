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
	sqlsrv_query($link,"UPDATE parametros set valor = '".$_REQUEST["v".$_REQUEST["cod"]]."' where codigo = '".$_REQUEST["cod"]."'");
	
	echo "<script>alert('Parametro actualizado exitosamente');</script>";
}

?>
<script>
window.location = 'parametros.php';
</script>
