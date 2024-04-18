<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

include("top.php");

if ($_REQUEST["action"] == "actualizar") {
	$query = ("SELECT codigo from oficinas where codigo = '" . $_REQUEST["c" . $_REQUEST["id"]] . "' AND id_oficina != '" . $_REQUEST["id"] . "'");	
	$existe_codigo = sqlsrv_query($link, $query);

	if (!(sqlsrv_num_rows($existe_codigo))) {
		sqlsrv_query($link, "UPDATE oficinas set codigo = '" . $_REQUEST["c" . $_REQUEST["id"]] . "', nombre = '" . utf8_encode($_REQUEST["n" . $_REQUEST["id"]]) . "' where id_oficina = '" . $_REQUEST["id"] . "'");

		echo "<script>alert('Oficina actualizada exitosamente');</script>";
	} else {
		echo "<script>alert('El codigo de la oficina ya se encuentra registrado. Oficina NO actualizada');</script>";
	}
} else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	//var_dump($_REQUEST["page"]);

	$offset = $_REQUEST["page"] * $x_en_x;
	$queryDB = "SELECT id_oficina, nombre from oficinas where id_oficina IS NOT NULL";

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
	}
	
	$queryDB = $queryDB . " order by id_oficina ASC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_oficina"]] == "1") {

			$query_delete_oficina_usuario = ("delete from oficinas_usuarios where id_oficina = '" . $fila["id_oficina"] . "'");
			sqlsrv_query($link, $query_delete_oficina_usuario);
			$query_delete_oficinas = ("delete from oficinas where id_oficina = '" . $fila["id_oficina"] . "'");
			sqlsrv_query($link, $query_delete_oficinas);

		}
	}
}

?>

<script>
	window.location = 'oficinas.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>