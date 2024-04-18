<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

sqlsrv_query($link, "UPDATE simulaciones set estado = 'DST', id_causal = '".$_REQUEST["id_causal"]."', fecha_cartera = NULL, usuario_desistimiento = '".$_SESSION["S_LOGIN"]."', fecha_desistimiento = GETDATE() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

$rs2 = sqlsrv_query($link, "select cedula, pagaduria, retanqueo1_libranza, retanqueo2_libranza, retanqueo3_libranza from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

for ($i = 1; $i <= 3; $i++)
{
	if ($fila2["retanqueo".$i."_libranza"])
	{
		$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila2["cedula"]."' AND pagaduria = '".$fila2["pagaduria"]."' AND nro_libranza = '".$fila2["retanqueo".$i."_libranza"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($rs1))
		{
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			sqlsrv_query($link, "UPDATE simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
		}
	}
}

//$consultarSimulacion=mysqli_query($link, "SELECT * FROM simulaciones WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'");
//$resSimulacion=mysql_fetch_array($consultarSimulacion);
sqlsrv_query($link, "UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$_REQUEST["id_simulacion"]."'");
sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) values ('".$_REQUEST["id_simulacion"]."',197,197, current_timestamp,'s',6,8)");

$mensaje = "Simulacion desistida exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = '<?php echo $_REQUEST["back"] ?>.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>
