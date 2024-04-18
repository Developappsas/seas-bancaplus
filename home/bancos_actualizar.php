<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	$existe_codigo = sqlsrv_query($link, "SELECT codigo from bancos where codigo = '" . $_REQUEST["c" . $_REQUEST["id"]] . "' AND id_banco != '" . $_REQUEST["id"] . "'");

	if (!(sqlsrv_num_rows($existe_codigo))) {
		sqlsrv_query($link, "UPDATE bancos set codigo = '" . $_REQUEST["c" . $_REQUEST["id"]] . "', nombre = '" . utf8_encode($_REQUEST["n" . $_REQUEST["id"]]) . "' where id_banco = '" . $_REQUEST["id"] . "'");

		echo "<script>alert('Banco actualizado exitosamente');</script>";
	} else {
		echo "<script>alert('El codigo del banco ya se encuentra registrado. Banco NO actualizado');</script>";
	}
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "SELECT id_banco, nombre from bancos where id_banco IS NOT NULL";

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

		$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
	}

	$queryDB = $queryDB . " order by nombre DESC OFFSET ".$offset." ROWS";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b" . $fila["id_banco"]] == "1") {
			$existe_en_cuentas = sqlsrv_query($link, "SELECT id_banco from entidades_cuentas where id_banco = '" . $fila["id_banco"] . "'");

			$existe_en_giros = sqlsrv_query($link, "SELECT id_banco from giros where id_banco = '" . $fila["id_banco"] . "'");

			if (sqlsrv_num_rows($existe_en_giros) || sqlsrv_num_rows($existe_en_cuentas)) {
				echo "<script>alert('El banco " . utf8_decode($fila["nombre"]) . " no puede ser borrado (Existen tablas con registros asociados)')</script>";
			} else {
				sqlsrv_query($link, "DELETE from bancos where id_banco = '" . $fila["id_banco"] . "'");
			}
		}
	}
}

?>
<script>
	window.location = 'bancos.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>