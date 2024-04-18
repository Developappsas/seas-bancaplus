<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "BEGIN TRANSACTION");

sqlsrv_query($link, "INSERT into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) select id_simulacion, CONCAT('[RESPUESTA ', reqexcep, ': ".$_REQUEST["tipo_respuesta"]."] ', '".utf8_encode($_REQUEST["respuesta"])."'), '".$_SESSION["S_LOGIN"]."', GETDATE() from req_excep where id_reqexcep = '".$_REQUEST["id_reqexcep"]."'");

$rs = sqlsrv_query($link, "select MAX(id_observacion) as m from simulaciones_observaciones");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_observacion_respuesta = $fila["m"];

sqlsrv_query($link, "UPDATE req_excep set tipo_respuesta = '".$_REQUEST["tipo_respuesta"]."', respuesta = '".utf8_encode($_REQUEST["respuesta"])."', estado = 'RESPONDIDO', id_observacion_respuesta = '".$id_observacion_respuesta."', usuario_respuesta = '".$_SESSION["S_LOGIN"]."', fecha_respuesta = GETDATE() where id_reqexcep = '".$_REQUEST["id_reqexcep"]."'");

sqlsrv_query($link, "COMMIT");

$mensaje = "Respuesta ingresada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'reqexcep.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
