<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "GERENTECOMERCIAL")
{
	exit;
}

sqlsrv_query($link, "UPDATE simulaciones set id_subestado = ".$subestado_segunda_revision." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

sqlsrv_query($link, "INSERT into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$subestado_segunda_revision."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

sqlsrv_query($link, "INSERT into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '[SOLICITUD SEGUNDA REVISION] ".utf8_encode($_REQUEST["observacion"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$mensaje = "Simulacion enviada a segunda revision exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>
