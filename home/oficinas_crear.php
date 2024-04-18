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

$existe_codigo = sqlsrv_query($link, "select codigo from oficinas where codigo = '".$_REQUEST["codigo"]."'");

if (!(sqlsrv_num_rows($existe_codigo))) {
	sqlsrv_query($link, "insert into oficinas (id_zona,codigo, nombre) values ('".$_REQUEST["zona_oficina"]."','".$_REQUEST["codigo"]."', '".utf8_encode($_REQUEST["nombre"])."')");
	
	$mensaje = "Oficina creada exitosamente";
}
else
{
	$mensaje = "El codigo de la oficina ya se encuentra registrado. Oficina NO creada";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'oficinas.php';
</script>
