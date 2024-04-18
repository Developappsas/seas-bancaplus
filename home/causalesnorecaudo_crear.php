<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query( $link,"insert into causales_norecaudo (id_tipo, nombre) values ('".$_REQUEST["id_tipo"]."', '".utf8_encode($_REQUEST["nombre"])."')");

$mensaje = "Causal creada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'causalesnorecaudo.php';
</script>
