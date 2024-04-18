<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"]) {
	exit;
}

$link = conectar();

sqlsrv_query($link, "UPDATE simulaciones set fecha_ado = GETDATE(), response_ado = NULL, score_ado = NULL where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

exit;
