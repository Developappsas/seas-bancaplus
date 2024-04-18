<?php include('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA")) {
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

	sqlsrv_query($link, "update actividadessc set nombre = '" . utf8_encode($_REQUEST["n" . $_REQUEST["id"]]) . "', estado = '" . $_REQUEST["a" . $_REQUEST["id"]] . "' where id_actividad = '" . $_REQUEST["id"] . "'");

	echo "<script>alert('Actividad/Solicitud actualizada exitosamente');</script>";
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "select id_actividad, nombre from actividadessc where id_actividad IS NOT NULL";

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

		$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
	}

	$queryDB = $queryDB . " order by nombre DESC OFFSET ".$offset." ROWS";
	

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b" . $fila["id_actividad"]] == "1") {
			$existe_en_sc = sqlsrv_query($link, "select id_actividad from servicio_cliente where id_actividad = '" . $fila["id_actividad"] . "'");

			if (sqlsrv_num_rows($existe_en_sc)) {
				echo "<script>alert('La actividad/solicitud " . utf8_decode($fila["nombre"]) . " no puede ser borrada (Existen tablas con registros asociados)')</script>";
			} else {
				sqlsrv_query($link, "delete from actividadessc where id_actividad = '" . $fila["id_actividad"] . "'");
			}
		}
	}
}

?>
<script>
	window.location = 'actividadessc.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>