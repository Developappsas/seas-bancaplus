<?php include('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	if ($_REQUEST["solo_activos" . $_REQUEST["id"]] != "1") {
		$_REQUEST["solo_activos" . $_REQUEST["id"]] = "0";
	}

	if ($_REQUEST["solo_pensionados" . $_REQUEST["id"]] != "1") {
		$_REQUEST["solo_pensionados" . $_REQUEST["id"]] = "0";
	}

	if ($_REQUEST["sin_seguro" . $_REQUEST["id"]] != "1") {
		$_REQUEST["sin_seguro" . $_REQUEST["id"]] = "0";
	}

	$queryDB1 = "select tasa_interes from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "'";

	$queryDB1 .= " AND tasa_interes = '" . $_REQUEST["tasa_interes" . $_REQUEST["id"]] . "' AND descuento1 = '" . $_REQUEST["descuento1" . $_REQUEST["id"]] . "' AND descuento1_producto = '" . $_REQUEST["descuento1_producto" . $_REQUEST["id"]] . "' AND descuento2 = '" . $_REQUEST["descuento2" . $_REQUEST["id"]] . "' AND id_tasa2 != '" . $_REQUEST["id"] . "'";

	$existe_tasa = sqlsrv_query($link, $queryDB1, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	if (!(sqlsrv_num_rows($existe_tasa))) {
		$parametros = sqlsrv_query($link, "select valor from parametros where codigo IN ('POIVA')");

		$fila1 = sqlsrv_fetch_assoc($parametros);

		$iva = $fila1[0];

		$descuento3 = $_REQUEST["descuento2" . $_REQUEST["id"]] * ($iva / 100.00);

		sqlsrv_query($link, "update tasas2" . $sufijo_sector . " set tasa_interes = '" . $_REQUEST["tasa_interes" . $_REQUEST["id"]] . "', descuento1 = '" . $_REQUEST["descuento1" . $_REQUEST["id"]] . "', descuento1_producto = '" . $_REQUEST["descuento1_producto" . $_REQUEST["id"]] . "', descuento2 = '" . $_REQUEST["descuento2" . $_REQUEST["id"]] . "', descuento3 = '" . $descuento3 . "', solo_activos = '" . $_REQUEST["solo_activos" . $_REQUEST["id"]] . "', solo_pensionados = '" . $_REQUEST["solo_pensionados" . $_REQUEST["id"]] . "', sin_seguro = '" . $_REQUEST["sin_seguro" . $_REQUEST["id"]] . "' where id_tasa2 = '" . $_REQUEST["id"] . "'");

		echo "<script>alert('Tasa actualizada exitosamente');</script>";
	} else {
		echo "<script>alert('Ya existe una tasa igual para este periodo');</script>";
	}
} else if ($_REQUEST["action"] == "borrar") {
	$queryDB = "select * from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "'";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["chk" . $fila["id_tasa2"]] == "1") {
			sqlsrv_query($link, "delete from tasas2_unidades where id_tasa2 = '" . $fila["id_tasa2"] . "'");

			sqlsrv_query($link, "delete from tasas2" . $sufijo_sector . " where id_tasa2 = '" . $fila["id_tasa2"] . "'");
		}
	}
}

?>
<script>
	window.location = 'tasas2.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>';
</script>