<?php include ('../functions.php'); ?>
<?php

if (!($_SESSION["S_LOGIN"] ))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

$parametros = sqlsrv_query($link, "SELECT * from parametros where tipo = 'CAZADOR' order by codigo");

$j = 0;

while ($fila1 = sqlsrv_fetch_array($parametros))
{
	$parametro[$j] = $fila1["valor"];
	
	$j++;
}

$cupo_max_cazador = $parametro[0];
$cupo_min_cazador = $parametro[1];
$edad_max_prospectos = $parametro[3];
$num_prospectos_asignados = $parametro[3];


$queryDB = "SELECT top  ".$num_prospectos_asignados." id_cazador from cazador where cedula is not null and id_usuario = '0'  and telefono != '' order by rand()";

$rs = sqlsrv_query($link, $queryDB);
$id_modificar = "0";

while($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){

	$id_modificar .= ",".$fila["id_cazador"];
}

sqlsrv_query($link, "update cazador set id_usuario = '".$_SESSION["S_IDUSUARIO"]."', estado = '1' where id_cazador IN (".$id_modificar.")");

$mensaje = "Cazador Iniciado";



?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'cazador.php';
</script>
