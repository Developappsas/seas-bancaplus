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

$existe_nit = sqlsrv_query($link,"select nit from vendedores where nit = '".$_REQUEST["nit"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!(sqlsrv_num_rows($existe_nit)))
{
	sqlsrv_query($link,"INSERT into vendedores (nit, nombre) values ('".$_REQUEST["nit"]."', '".utf8_encode($_REQUEST["nombre"])."')");
	
	$mensaje = "Vendedor creado exitosamente";
}
else
{
	$mensaje = "El NIT del vendedor ya se encuentra registrado. Vendedor NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'vendedores.php';
</script>
