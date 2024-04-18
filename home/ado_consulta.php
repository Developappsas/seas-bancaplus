<?php include('/disco350/seas2/public_html/functions.php'); ?>
<?php

$link = conectar();

$queryDB = "SELECT id_simulacion from simulaciones where fecha_ado IS NOT NULL AND response_ado IS NULL order by fecha_ado";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	$get_data = callAPI("GET", "https://adocolumbia.ado-tech.com/Kredit/api/Kredit/ValidationAssociated/", false, $fila["id_simulacion"]);
	//$get_data = callAPI("GET", "https://adocolumbia.ado-tech.com/Kredit/api/Kredit/ValidationAssociated/", false, "66666");

	if ($get_data != "ERROR") {
		$response = json_decode($get_data, true);

		if ($response["Scores"]) {
			if (strtoupper($response["Scores"][0]["StateName"]) != "PENDIENTE") {
				sqlsrv_query($link, "UPDATE simulaciones set response_ado = '" . $get_data . "', score_ado = '" . utf8_decode(strtoupper($response["Scores"][0]["StateName"])) . "' where id_simulacion = '" . $fila["id_simulacion"] . "'");
			} else {
				sqlsrv_query($link, "UPDATE simulaciones set score_ado = 'PENDIENTE' where id_simulacion = '" . $fila["id_simulacion"] . "'");
			}
		} else {
			sqlsrv_query($link, "UPDATE simulaciones set score_ado = 'PENDIENTE' where id_simulacion = '" . $fila["id_simulacion"] . "'");
		}
	} else {
		sqlsrv_query($link, "UPDATE simulaciones set score_ado = 'ERROR' where id_simulacion = '" . $fila["id_simulacion"] . "'");
	}
}

?>
