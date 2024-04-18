<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$queryDB = "select id_banco,nro_cuenta from cuentas_bancarias where id_banco = '".$_REQUEST["id_banco"]."'";

$queryDB .= " AND nro_cuenta = '".$_REQUEST["nro_cuenta"]."'";

$existe_cuenta = sqlsrv_query($link, $queryDB);

if (!(sqlsrv_num_rows($existe_cuenta)))
{
	sqlsrv_query($link, "insert into cuentas_bancarias (nombre, id_banco, tipo_cuenta, nro_cuenta) VALUES ('".$_REQUEST["nombre"]."', '".$_REQUEST["id_banco"]."', '".$_REQUEST["tipo_cuenta"]."', '".$_REQUEST["nro_cuenta"]."')");
	
	$mensaje = "Cuenta creada exitosamente";
}
else
{
	$mensaje = "El numero de cuenta ya se encuentra registrado. Cuenta NO creada";
}

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'cuentasbancarias.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
