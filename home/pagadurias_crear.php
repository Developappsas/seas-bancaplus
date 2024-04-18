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

$existe_nombre = sqlsrv_query( $link,"select id_pagaduria from pagadurias where nombre = '".utf8_encode($_REQUEST["nombre"])."'");

if (!(sqlsrv_num_rows($existe_nombre)))
{
	sqlsrv_query( $link,"insert into pagadurias (nombre, sector, plazo) values ('".utf8_encode($_REQUEST["nombre"])."', '".$_REQUEST["sector"]."', ".$_REQUEST["plazo"].")");
	
	$mensaje = "Pagaduria creada exitosamente";
}
else{
	$mensaje = "La pagaduria ya existe. Pagaduria NO creada";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'pagadurias.php';
</script>
