<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$queryDB = "select id_banco,nro_cuenta from entidades_cuentas where id_banco = '".$_REQUEST["id_banco"]."'";

$queryDB .= " AND nro_cuenta = '".$_REQUEST["nro_cuenta"]."'";
echo $queryDB;

$existe_cuenta = sqlsrv_query($link, $queryDB);

if (!(sqlsrv_num_rows($existe_cuenta))) {
	sqlsrv_query($link, "INSERT into entidades_cuentas (id_entidad, id_banco,tipo_cuenta, nro_cuenta, estado, usuario_creacion, fecha_creacion ) VALUES ('".$_REQUEST["id_entidad"]."', '".$_REQUEST["id_banco"]."', '".$_REQUEST["tipo_cuenta"]."', '".$_REQUEST["nro_cuenta"]."', '1', '".$_SESSION["S_LOGIN"]."', getdate())");
	$mensaje = "Cuenta creada exitosamente";
} else {
	$mensaje = "El numero de cuenta ya se encuentra registrado. Cuenta NO creada";
}

?>	

<script>
alert("<?php echo $mensaje ?>");

window.location = 'entidadescuentas.php?id_entidad=<?php echo $_REQUEST["id_entidad"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>