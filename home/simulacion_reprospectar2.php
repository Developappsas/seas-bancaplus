<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "OUTSOURCING"))
{
	exit;
}

sqlsrv_query($link, "UPDATE simulaciones set usuario_modificacion=null,fecha_modificacion=null,estado = 'ING',decision='VIABLE', id_subestado = NULL, id_causal = NULL, usuario_prospeccion = NULL, fecha_prospeccion = NULL, usuario_radicado = '".$_SESSION["S_LOGIN"]."', fecha_radicado = GETDATE(), numero_reprospecciones = numero_reprospecciones + 1 where id_simulacion = '".$_REQUEST["id_simulacion"]."'");


$consultarSimulacionFdc="SELECT * from simulaciones_fdc WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'";
$consultarUltimoEstado=$consultarSimulacionFdc." and estado=4 and vigente='s'";
$queryUltEstadoSimulacionFDC=sqlsrv_query($link, $consultarUltimoEstado);

$consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

  
$resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
if (sqlsrv_num_rows($queryUltEstadoSimulacionFDC)>0)
{
	if ($resJornadaLaboral["descripcion"]=="s")
	{
		$consultarInformacionSimulacion=sqlsrv_query($link, "SELECT * FROM simulaciones where id_simulacion='".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		

		$resInformacionSimulacion=sqlsrv_fetch_array($consultarInformacionSimulacion);
		$querySimulacionFDC=sqlsrv_query($link, $consultarSimulacionFdc);
		
		if (sqlsrv_num_rows($querySimulacionFDC)>0)
		{
			sqlsrv_query($link, "UPDATE  simulaciones_fdc set vigente='n' where id_simulacion='".$_REQUEST["id_simulacion"]."'");
			
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",1973,CURRENT_TIMESTAMP,'n',1)");
			
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",".$resInformacionSimulacion["id_analista_riesgo_operativo"].",197,CURRENT_TIMESTAMP,'s',2)");
			
			sqlsrv_query($link, "UPDATE  simulaciones set id_analista_riesgo_operativo='".$resInformacionSimulacion["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$resInformacionSimulacion["id_analista_riesgo_operativo"]."' where id_simulacion='".$_REQUEST["id_simulacion"]."'");
			
		}else{
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",1974,CURRENT_TIMESTAMP,'n',1)");
			
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",".$resInformacionSimulacion["id_analista_riesgo_operativo"].",197,CURRENT_TIMESTAMP,'s',2)");
			
			sqlsrv_query($link, "UPDATE  simulaciones set id_analista_riesgo_operativo='".$resInformacionSimulacion["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$resInformacionSimulacion["id_analista_riesgo_operativo"]."' where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		}

	}else{
		$consultarInformacionSimulacion=sqlsrv_query($link, "SELECT * FROM simulaciones where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		$resInformacionSimulacion=sqlsrv_fetch_array($consultarInformacionSimulacion);
		$querySimulacionFDC=sqlsrv_query($link, $consultarSimulacionFdc);
		if (sqlsrv_num_rows($querySimulacionFDC)>0)
		{
			sqlsrv_query($link, "UPDATE  simulaciones_fdc set vigente='n' where id_simulacion='".$_REQUEST["id_simulacion"]."'");
			
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",197,CURRENT_TIMESTAMP,'s',1)");
			
			sqlsrv_query($link, "UPDATE  simulaciones set id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		}else{
			sqlsrv_query($link, "INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES (".$_REQUEST["id_simulacion"].",197,CURRENT_TIMESTAMP,'s',1)");
			
			sqlsrv_query($link, "UPDATE  simulaciones set id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null where id_simulacion='".$_REQUEST["id_simulacion"]."'");
			
		}

	}
	
	
}
	

$mensaje = "Simulacion enviada a prospeccion exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>
