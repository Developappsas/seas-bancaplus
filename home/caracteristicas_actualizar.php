<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar") {
	if ($_REQUEST["a" . $_REQUEST["id"]] != "1") {
		$_REQUEST["a" . $_REQUEST["id"]] = "0";
	}

	sqlsrv_query($link, "UPDATE caracteristicas set nombre = '" . utf8_encode($_REQUEST["n" . $_REQUEST["id"]]) . "', estado = '" . $_REQUEST["a" . $_REQUEST["id"]] . "' where id_caracteristica = '" . $_REQUEST["id"] . "'");

	echo "<script>alert('Caracteristica actualizada exitosamente');</script>";
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "SELECT id_caracteristica, nombre from caracteristicas where id_caracteristica IS NOT NULL";

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

		$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
	}

	$queryDB = $queryDB . " order by nombre DESC OFFSET ".$offset." ROWS";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b" . $fila["id_caracteristica"]] == "1") {
			$existe_en_simulaciones = sqlsrv_query($link, "SELECT id_caracteristica from simulaciones where id_caracteristica = '" . $fila["id_caracteristica"] . "'");

			if (sqlsrv_num_rows($existe_en_simulaciones)) {
				echo "<script>alert('La caracteristica " . utf8_decode($fila["nombre"]) . " no puede ser borrada (Existen tablas con registros asociados)')</script>";
			} else {
				sqlsrv_query($link, "DELETE from caracteristicas where id_caracteristica = '" . $fila["id_caracteristica"] . "'");
			}
		}
	}
}

?>
<script>
	window.location = 'caracteristicas.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>