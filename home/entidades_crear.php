<?php 
	include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA")) {
	exit;
}

$link = conectar();

include("top.php"); 
$existe_nit = sqlsrv_query($link, "select nit from entidades_desembolso where nit = '".$_REQUEST["nit"]."'");

if (!(sqlsrv_num_rows($existe_nit))) {
	sqlsrv_query($link, "insert into entidades_desembolso (nit, nombre) values ('".$_REQUEST["nit"]."', '".utf8_encode($_REQUEST["nombre"])."')");
	$mensaje = "Entidad creada exitosamente";
} else {
	$mensaje = "El NIT de la entidad ya se encuentra registrado. Entidad NO creada";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'entidades.php';
</script>
