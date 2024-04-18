<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CONTABILIDAD"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.id_simulacion from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1))
{
	exit;
}

if ($_REQUEST["comision_pagada"] == "true")
{
	sqlsrv_query($link, "update simulaciones set comision_pagada = '1', fecha_comision_pagada = NOW() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	sqlsrv_query($link, "INSERT INTO pago_comisiones (id_simulacion,pagado,id_usuario,fecha) VALUES ('".$_REQUEST["id_simulacion"]."','s','".$_SESSION["S_IDUSUARIO"]."',CURRENT_TIMESTAMP())");
}
else
{
	sqlsrv_query($link, "update simulaciones set comision_pagada = '0', fecha_comision_pagada = NULL where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	sqlsrv_query($link, "INSERT INTO pago_comisiones (id_simulacion,pagado,id_usuario,fecha) VALUES ('".$_REQUEST["id_simulacion"]."','n','".$_SESSION["S_IDUSUARIO"]."',CURRENT_TIMESTAMP())");
}
$mensaje = "Proceso realizado";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'tesoreria_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>

