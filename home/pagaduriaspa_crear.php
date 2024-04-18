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

$existe_nit = sqlsrv_query($link,"select nit from pagaduriaspa where nit = '".$_REQUEST["nit"]."'");

if (!(sqlsrv_num_rows($existe_nit)))
{
	sqlsrv_query($link,"insert into pagaduriaspa (pagaduria, nit, pa) values ('".utf8_encode($_REQUEST["pagaduria"])."', '".$_REQUEST["nit"]."', '".$_REQUEST["pa"]."')");

	sqlsrv_query($link,"update simulaciones set pa = '".$_REQUEST["pa"]."' where pagaduria = '".utf8_encode($_REQUEST["pagaduria"])."' AND (pa IS NULL OR pa = '')");

	$mensaje = "Asociacion creada exitosamente";
}
else
{
	$mensaje = "El NIT de la pagaduria ya se encuentra registrado. Asociacion NO realizada";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'pagaduriaspa.php';
</script>
