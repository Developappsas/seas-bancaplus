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

	sqlsrv_query($link, "UPDATE areas_reqexcep set nombre = '" . utf8_encode($_REQUEST["n" . $_REQUEST["id"]]) . "', estado = '" . $_REQUEST["a" . $_REQUEST["id"]] . "' where id_area = '" . $_REQUEST["id"] . "'");

	echo "<script>alert('Area actualizada exitosamente');</script>";
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "SELECT id_area, nombre from areas_reqexcep where id_area IS NOT NULL";

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

		$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
	}

	$queryDB = $queryDB . " order by nombre DESC OFFSET ".$offset." ROWS";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b" . $fila["id_area"]] == "1") {
			$existe_en_req_excep = sqlsrv_query($link, "SELECT id_area from req_excep where id_area = '" . $fila["id_area"] . "'");

			if (sqlsrv_num_rows($existe_en_req_excep)) {
				echo "<script>alert('El area " . utf8_decode($fila["nombre"]) . " no puede ser borrada (Existen tablas con registros asociados)')</script>";
			} else {
				sqlsrv_query($link, "DELETE from areas_reqexcep_perfiles where id_area = '" . $fila["id_area"] . "'");
				sqlsrv_query($link, "DELETE from areas_reqexcep where id_area = '" . $fila["id_area"] . "'");
			}
		}
	}
}

?>
<script>
	window.location = 'areasreqexcep.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>