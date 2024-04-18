<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
include('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["solo_activos"] != "1") {
	$_REQUEST["solo_activos"] = "0";
}

if ($_REQUEST["solo_pensionados"] != "1") {
	$_REQUEST["solo_pensionados"] = "0";
}

if ($_REQUEST["sin_seguro"] != "1") {
	$_REQUEST["sin_seguro"] = "0";
}

$queryDB = "SELECT tasa_interes from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "'";

$queryDB .= " AND tasa_interes = '" . $_REQUEST["tasa_interes"] . "' AND descuento1 = '" . $_REQUEST["descuento1"] . "' AND descuento1_producto = '" . $_REQUEST["descuento1_producto"] . "' AND descuento2 = '" . $_REQUEST["descuento2"] . "'";

$existe_tasa = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
echo $queryDB;

if (!(sqlsrv_num_rows($existe_tasa))) {
	$parametros = sqlsrv_query($link, "select valor from parametros where codigo IN ('POIVA')");

	$fila1 = sqlsrv_fetch_array($parametros);

	$iva = $fila1[0];

	$descuento3 = $_REQUEST["descuento2"] * ($iva / 100.00);

	sqlsrv_query($link, "insert into tasas2" . $sufijo_sector . " (id_tasa, tasa_interes, descuento1, descuento1_producto, descuento2, descuento3, solo_activos, solo_pensionados, sin_seguro) VALUES ('" . $_REQUEST["id_tasa"] . "', '" . $_REQUEST["tasa_interes"] . "', '" . $_REQUEST["descuento1"] . "', '" . $_REQUEST["descuento1_producto"] . "', '" . $_REQUEST["descuento2"] . "', '" . $descuento3 . "', '" . $_REQUEST["solo_activos"] . "', '" . $_REQUEST["solo_pensionados"] . "', '" . $_REQUEST["sin_seguro"] . "')");
		
	$mensaje = "Tasa creada exitosamente";


} else {
	$mensaje = "La tasa digitada ya se encuentra registrada en este periodo. Tasa NO creada";
}

?>
<script>
	alert("<?php echo $mensaje ?>")

	window.location = 'tasas2.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>';
</script>