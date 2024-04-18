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

if ($_REQUEST["action"] == "actualizar") {
	$queryDB1 = "select id_banco,nro_cuenta from entidades_cuentas where id_banco = '".$_REQUEST["id_banco".$_REQUEST["id"]]."'";
	
	$queryDB1 .= " AND nro_cuenta = '".$_REQUEST["nro_cuenta".$_REQUEST["id"]]."' AND id_entidadcuenta != '".str_replace("_", " ", $_REQUEST["id"])."'";
	
	$existe_cuenta1 = sqlsrv_query($link, $queryDB1);
	
	if (!(sqlsrv_num_rows($existe_cuenta1))) {
		sqlsrv_query($link, "update entidades_cuentas set id_banco = '".$_REQUEST["id_banco".$_REQUEST["id"]]."', tipo_cuenta = '".$_REQUEST["tipo_cuenta".$_REQUEST["id"]]."', nro_cuenta = '".$_REQUEST["nro_cuenta".$_REQUEST["id"]]."' where id_entidadcuenta = '".str_replace("_", " ", $_REQUEST["id"])."'");
		echo "<script>alert('Cuenta actualizada exitosamente');</script>";
	} else {
		echo "<script>alert('Ya existe un numero de cuenta para el banco seleccionado');</script>";
	}
} else if ($_REQUEST["action"] == "borrar"){

	$queryDB = "select * from entidades_cuentas";
     
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {		
		if ($_REQUEST["chk".$fila["id_entidadcuenta"]]== "1") {
			sqlsrv_query($link, "delete from entidades_cuentas where id_entidadcuenta = '".$fila["id_entidadcuenta"]."'");
		}
	}
}

?>
<script>

window.location = 'entidadescuentas.php?id_entidad=<?php echo $_REQUEST["id_entidad"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
