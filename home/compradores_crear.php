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

$existe_nit = sqlsrv_query($link, "SELECT nit from compradores where nit = '".$_REQUEST["nit"]."'");

if (!(sqlsrv_num_rows($existe_nit)))
{
	sqlsrv_query($link, "insert into compradores (nit, nombre, nombre_corto) values ('".$_REQUEST["nit"]."', '".utf8_encode($_REQUEST["nombre"])."', '".utf8_encode($_REQUEST["nombre_corto"])."')");
	
	$mensaje = "Comprador creado exitosamente";
}
else
{
	$mensaje = "El NIT del comprador ya se encuentra registrado. Comprador NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'compradores.php';
</script>
