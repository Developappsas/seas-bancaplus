<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM"))
{
	exit;
}

$link = conectar();

?>
	<?php include("top.php"); ?>
	<meta http-equiv="refresh" content="60">
	<script language="JavaScript" src="../date.js"></script>
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td class="titulo"><center><b>Ingresos FDC</b><br><br></center></td>
		</tr>
	</table>
<form name="formato2" method="post" action="pilotofdc.php">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=0 cellpadding=0>
						<tr>
							<td valign="bottom">C&eacute;dula/Nombre<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:100px"></td>
							<?php

							if (!$_SESSION["S_SECTOR"])
							{

							?>
							<td valign="bottom">Sector<br>
								<select name="sectorb" style="width:155px">
									<option value=""></option>
									<option value="PUBLICO">PUBLICO</option>
									<option value="PRIVADO">PRIVADO</option>
								</select>&nbsp;
							</td>
							<?php

							}

							?>
							<td valign="bottom">Pagadur&iacute;a<br>
								<select name="pagaduriab" style="width:155px">
									<option value=""></option>
								<?php

								$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";

								if ($_SESSION["S_SECTOR"])
								{
									$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
								}

								$queryDB .= " order by pagaduria";

								$rs1 = mysqli_query($link, $queryDB);

								while ($fila1 = mysqli_fetch_assoc($rs1))
								{
									echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
								}

								?>
								</select>&nbsp;
							</td>
							<?php

							if ( $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA"){

							?>
							<td valign="bottom">Oficina<br>
								<select name="id_oficinab" style="width:155px">
									<option value=""></option>
							<?php

							$queryDB = "select id_oficina, codigo, nombre from oficinas";
							// 001
							if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION")
							{
								$queryDB .= " where id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
							}
							
							$queryDB .= " order by nombre";	
							
							$rs1 = mysqli_query($link, $queryDB);
							
							while ($fila1 = mysqli_fetch_assoc($rs1))
							{
								echo "<option value=\"".$fila1["id_oficina"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
							}
							
							?>

								</select>&nbsp;
							</td>
							<?php 

							}

							if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION")
							{

							?>
							<td valign="bottom">Comercial<br>
								<select name="id_comercialb" style="width:155px">
									<option value=""></option>
								<?php

								$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join usuarios_unidades uu on us.id_usuario = uu.id_usuario where us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";
								
								$queryDB .= " AND uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
								
								$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";

								$rs1 = mysqli_query($link, $queryDB);
								
								while ($fila1 = mysqli_fetch_assoc($rs1))
								{
									echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
								}
								
								?>
								</select>&nbsp;
							</td>
							<?php

							}
							else
							{
								if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
								{
								
								?>
								<td valign="bottom">Comercial<br>
									<select name="id_comercialb" style="width:155px">
										<option value=""></option>
										<?php

										$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial inner join unidades_negocio un on si.id_unidad_negocio = un.id_unidad where si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."') AND us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";
										
										$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

										if ($_SESSION["S_SUBTIPO"] == "PLANTA")
											$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
										
										if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
											$queryDB .= " AND si.telemercadeo = '0'";
										
										if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
											$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
										
										if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
											$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
										
										if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
											$queryDB .= " AND si.telemercadeo = '1'";
										
										$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";
										
										$rs1 = mysqli_query($link, $queryDB);
										
										while ($fila1 = mysqli_fetch_assoc($rs1))
										{
											echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
										}
										
										?>
									</select>&nbsp;
								</td>
								<?php

									}
							}

							?>
							<td valign="bottom">Decisi&oacute;n<br>
								<select name="decisionb" style="width:110px">
									<option value=""></option>
									<option value="<?php echo $label_viable ?>"><?php echo $label_viable ?></option>
									<option value="<?php echo $label_negado ?>"><?php echo $label_negado ?></option>
								</select>&nbsp;
							</td>
							<?php

							if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
							{

							?>
							<td valign="bottom">Visualizar<br>
								<select name="visualizarb" style="width:110px">
									<option value="1"<?php if ($_REQUEST["visualizarb"] || !$_REQUEST["buscar"]) { echo " selected"; } ?>>MIS CREDITOS</option>
									<option value="0"<?php if (!$_REQUEST["visualizarb"] && $_REQUEST["buscar"]) { echo " selected"; } ?>>TODO</option>
								</select>&nbsp;
							</td>
							<?php

							}
							else
							{

							?>
								<input type="hidden" name="visualizarb" value="0">
							<?php

							}

							?>
							<td valign="bottom"><br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
	<?php

	if ($_REQUEST["action"])
	{
		if (!$_REQUEST["page"])
		{
			$_REQUEST["page"] = 0;
		}
		
		$x_en_x = 100;
		
		$offset = $_REQUEST["page"] * $x_en_x;
		
		$queryDB = "select si.* from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";
		
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
		
		if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
		{
			$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
				$queryDB .= " AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
			
			if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
				$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
				$queryDB .= " AND si.telemercadeo = '1'";
		}
		
		if ($_REQUEST["descripcion_busqueda"])
		{
			$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
			
			$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
		}
		
		if ($_REQUEST["sectorb"])
		{
			$sectorb = $_REQUEST["sectorb"];
			
			$queryDB .= " AND pa.sector = '".$sectorb."'";
		}
		
		if ($_REQUEST["pagaduriab"])
		{
			$pagaduriab = $_REQUEST["pagaduriab"];
			
			$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
		}
		
		if ($_REQUEST["id_comercialb"])
		{
			$id_comercialb = $_REQUEST["id_comercialb"];
			
			$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
		}
		
		if ($_REQUEST["decisionb"])
		{
			$decisionb = $_REQUEST["decisionb"];
			
			$queryDB .= " AND si.decision = '".$decisionb."'";
		}
		
		if ($_REQUEST["id_oficinab"])
		{
			$id_oficinab = $_REQUEST["id_oficinab"];
			
			$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
		}
		
		if ($_REQUEST["visualizarb"])
		{
			$visualizarb = $_REQUEST["visualizarb"];
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
		else if (!$_REQUEST["buscar"])
		{
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
		
		$queryDB .= " order by si.id_simulacion LIMIT ".$x_en_x." OFFSET ".$offset;
		
		$rs = mysqli_query($link, $queryDB);
		//echo "asignaciones.....".$_REQUEST["action"];
		//echo "<br>".$queryDB;
		while ($fila = mysqli_fetch_assoc($rs))
		{
			
			
			if ($_REQUEST["chk".$fila["id_simulacion"]])
			{
				//echo $_REQUEST["action"];
				//echo $_REQUEST["chk".$fila["id_simulacion"]]."<br>";
				if ($_REQUEST["action"] == "desistir")
				{
					if ($fila["decision"] == $label_viable)
						//mysqli_query($link, "update simulaciones set estado = 'DST', id_subestado = NULL where id_simulacion = '".$fila["id_simulacion"]."'");
						mysqli_query($link, "update simulaciones set estado = 'DST' where id_simulacion = '".$fila["id_simulacion"]."'");
					else
						echo "<script>alert('La simulacion ".$fila["cedula"]." ".$fila["nombre"]." no puede ser Desistida. No cumple con las condiciones para realizar esta accion');</script>";
				}
				if ($_REQUEST["action"] == "anular")
				{
					//echo "ANULAR: ".$fila["id_simulacion"].",";
					mysqli_query($link, "update simulaciones set estado = 'ANU', id_subestado = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = NOW() where id_simulacion = '".$fila["id_simulacion"]."'");
				}

		


				
			}
		}
	}

	if (!$_REQUEST["page"])
	{
		$_REQUEST["page"] = 0;
	}

	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "select CASE WHEN (us.freelance=1 and us.outsourcing=0) then 'FREELANCE' WHEN (us.freelance=0 and us.outsourcing=1) then 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,si.id_simulacion, si.cedula, si.empleado_manual, si.nombre, si.pagaduria, us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, si.opcion_credito, si.opcion_desembolso_cli, si.opcion_desembolso_ccc, si.opcion_desembolso_cmp, si.opcion_desembolso_cso, si.retanqueo_total, si.valor_credito, si.decision, si.frente_al_cliente, si.fecha_radicado from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";

	$queryDB_count = "select COUNT(*) as c from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";

	$queryDB_suma = "select SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc WHEN 'CMP' THEN si.opcion_desembolso_cmp WHEN 'CSO' THEN si.opcion_desembolso_cso END) as s, SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc - si.retanqueo_total WHEN 'CMP' THEN si.opcion_desembolso_cmp - si.retanqueo_total WHEN 'CSO' THEN si.opcion_desembolso_cso - si.retanqueo_total END) as s2, SUM(si.valor_credito) as s3 from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";

	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		
		$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		
		$queryDB_suma .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		
		$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		
		$queryDB_suma .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		
		$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		
		$queryDB_suma .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}

	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
	{
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA")
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
			$queryDB .= " AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
			$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
			$queryDB .= " AND si.telemercadeo = '1'";
		
		$queryDB_count .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA")
			$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
			$queryDB_count .= " AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
			$queryDB_count .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
			$queryDB_count .= " AND si.telemercadeo = '1'";
		
		$queryDB_suma .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA")
			$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
			$queryDB_suma .= " AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
			$queryDB_suma .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
			$queryDB_suma .= " AND si.telemercadeo = '1'";
	}

	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
		
		$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
		
		$queryDB_suma .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}

	if ($_REQUEST["sectorb"])
	{
		$sectorb = $_REQUEST["sectorb"];
		
		$queryDB .= " AND pa.sector = '".$sectorb."'";
		
		$queryDB_count .= " AND pa.sector = '".$sectorb."'";
		
		$queryDB_suma .= " AND pa.sector = '".$sectorb."'";
	}

	if ($_REQUEST["pagaduriab"])
	{
		$pagaduriab = $_REQUEST["pagaduriab"];
		
		$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
		
		$queryDB_count .= " AND si.pagaduria = '".$pagaduriab."'";
		
		$queryDB_suma .= " AND si.pagaduria = '".$pagaduriab."'";
	}

	if ($_REQUEST["id_comercialb"])
	{
		$id_comercialb = $_REQUEST["id_comercialb"];
		
		$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
		
		$queryDB_count .= " AND si.id_comercial = '".$id_comercialb."'";
		
		$queryDB_suma .= " AND si.id_comercial = '".$id_comercialb."'";
	}

	if ($_REQUEST["decisionb"])
	{
		$decisionb = $_REQUEST["decisionb"];
		
		$queryDB .= " AND si.decision = '".$decisionb."'";
		
		$queryDB_count .= " AND si.decision = '".$decisionb."'";
		
		$queryDB_suma .= " AND si.decision = '".$decisionb."'";
	}

	if ($_REQUEST["id_oficinab"])
	{
		$id_oficinab = $_REQUEST["id_oficinab"];
		
		$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
		
		$queryDB_count .= " AND si.id_oficina = '".$id_oficinab."'";
		
		$queryDB_suma .= " AND si.id_oficina = '".$id_oficinab."'";
	}

	if ($_REQUEST["visualizarb"])
	{
		$visualizarb = $_REQUEST["visualizarb"];
		
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
		{
			$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			$queryDB_count .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			$queryDB_suma .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
		{
			$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
			
			$queryDB_count .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
			
			$queryDB_suma .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
	}
	else if (!$_REQUEST["buscar"])
	{
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
		{
			$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			$queryDB_count .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			$queryDB_suma .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
		{
			$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
			
			$queryDB_count .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
			
			$queryDB_suma .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
	}

	$queryDB .= " order by si.fecha_radicado LIMIT ".$x_en_x." OFFSET ".$offset;

	$rs = mysqli_query($link, $queryDB);

	$rs_count = mysqli_query($link, $queryDB_count);

	$fila_count = mysqli_fetch_assoc($rs_count);

	$cuantos = $fila_count["c"];

	$rs_suma = mysqli_query($link, $queryDB_suma);

	$fila_suma = mysqli_fetch_assoc($rs_suma);

	$suma = $fila_suma["s"];

	$suma2 = $fila_suma["s2"];

	$suma3 = $fila_suma["s3"];

	if ($cuantos)
	{
		if ($cuantos > $x_en_x)
		{
			echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
			
			$i = 1;
			$final = 0;
			
			while ($final < $cuantos)
			{
				$link_page = $i - 1;
				$final = $i * $x_en_x;
				$inicio = $final - ($x_en_x - 1);
				
				if ($final > $cuantos)
				{
					$final = $cuantos;
				}
				
				if ($link_page != $_REQUEST["page"])
				{
					echo " <a href=\"pilotofdc.php?descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&id_comercialb=".$id_comercialb."&decisionb=".$decisionb."&id_oficinab=".$id_oficinab."&visualizarb=".$visualizarb."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
				}
				else
				{
					echo " ".$i;
				}
				
				$i++;
			}
			
			if ($_REQUEST["page"] != $link_page)
			{
				$siguiente_page = $_REQUEST["page"] + 1;
				
				echo " <a href=\"pilotofdc.php?descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&id_comercialb=".$id_comercialb."&decisionb=".$decisionb."&id_oficinab=".$id_oficinab."&visualizarb=".$visualizarb."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
			}
			
			echo "</table><br>";
		}
	
		?>
		<form name="formato3" method="GET" action="pilotofdc.php">
			<input type="hidden" name="action" value="">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
			<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $id_comercialb ?>">
			<input type="hidden" name="decisionb" value="<?php echo $decisionb ?>">
			<input type="hidden" name="id_oficinab" value="<?php echo $id_oficinab ?>">
			<input type="hidden" name="visualizarb" value="<?php echo $visualizarb ?>">
			<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
			<table border="0" cellspacing=1 cellpadding=2>
				<tr>
					<td colspan="19" align="right"><b>TOTAL VR DESEMBOLSO: $<?php echo number_format($suma, 0) ?><br>TOTAL VR DESEMBOLSO MENOS RETANQUEOS: $<?php echo number_format($suma2, 0) ?><br>TOTAL VR CR&Eacute;DITO: $<?php echo number_format($suma3, 0) ?></b></td>
				</tr>
			</table>
			<table class="tab3">
				<tr>
					<th>C&eacute;dula</th>
					<th>Nombre</th>
					<th>Pagadur&iacute;a</th>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Comercial</th><?php } ?>
				<!--<th>SE AGREGA COLUMNA TIPO COMERCIAL EN TABLA PILOTO FDC. ING JAIRO ZAPATA FECHA 23/11/2021</th>-->	<?php  if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Tipo Comercial</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><th>Oficina</th><?php } ?>
					<th>Vr Desembolso</th>
					<th>Vr Desembolso Menos Retanqueos</th>
					<th>Vr Cr&eacute;dito</th>
					<th>Decisi&oacute;n</th>
					<th>F. Radicado</th>
					<th>Tiempo Prospecci&oacute;n</th>
					<th><img src="../images/frentecliente.png" title="Frente al Cliente"></th>
					<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><th><img src="../images/adjuntar.png" title="Adjuntos"></th><?php } ?>
					<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
				</tr>
				<?php

					$j = 1;
					
					while ($fila = mysqli_fetch_assoc($rs))
					{
						$tr_class = "";
						
						if (($j % 2) == 0)
						{
							$tr_class = " style='background-color:#F1F1F1;'";
						}
						
						switch ($fila["opcion_credito"])
						{
							case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"]; break;
							case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
							case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
							case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
						}
						
						if ($fila["opcion_credito"] == "CLI")
							$fila["retanqueo_total"] = 0;
						
						$tiempo_prospeccion_letras = "";
						
						$tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($fila["fecha_radicado"]);
						
						$tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);
						
						$tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);
						
						$tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
						
						if ($tiempo_prospeccion_horas)
							$tiempo_prospeccion_letras .= $tiempo_prospeccion_horas."h ";
						
						$tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos."m ";
						
						$tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos."s";
						
				?>
				<tr <?php echo $tr_class ?>>
					<td><a href="simulador.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&decisionb=<?php echo $decisionb ?>&id_oficinab=<?php echo $id_oficinab ?>&visualizarb=<?php echo $visualizarb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&back=pilotofdc&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
					<td<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && $fila["empleado_manual"]) { ?> style="color:#FF0000"<?php } ?>><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td><?php } ?>
					<!--<th>SE AGREGA COLUMNA TIPO COMERCIAL EN TABLA PILOTO FDC. ING JAIRO ZAPATA FECHA 23/11/2021</th>-->	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["tipo_comercial2"]) ?></td><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><td><?php echo utf8_decode($fila["oficina"]) ?></td><?php } ?>
					<td align="right"><?php echo number_format($opcion_desembolso, 0) ?></td>
					<td align="right"><?php echo number_format($opcion_desembolso - $fila["retanqueo_total"], 0) ?></td>
					<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
					<td align="center"><?php echo $fila["decision"] ?></td>
					<td align="center"><nobr><?php echo $fila["fecha_radicado"] ?></nobr></td>
					<td align="center"><nobr><?php echo $tiempo_prospeccion_letras ?></nobr></td>
					<td align="center"><?php if ($fila["frente_al_cliente"] == "SI") { ?><img src="../images/frentecliente.png" title="Frente al Cliente"><?php } ?></td>
					<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><td align="center"><a href="adjuntos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&decisionb=<?php echo $decisionb ?>&id_oficinab=<?php echo $id_oficinab ?>&visualizarb=<?php echo $visualizarb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&back=pilotofdc&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adjuntar.png" title="Adjuntos"></a></td><?php } ?>
					<?php 
					if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
					|| $_SESSION["S_TIPO"] == "OPERACIONES" 
					|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
					&& $_SESSION["S_SOLOLECTURA"] != "1") { ?>
					<td align="center"><input type="checkbox" name="chk<?php echo $fila["id_simulacion"] ?>" value="<?php echo $fila["id_simulacion"] ?>"></td><?php } ?>
				</tr>
				<?php

					$j++;
				}
				
				?>
			</table>
			<br>
			<?php

				if ($_SESSION["S_SOLOLECTURA"] != "1")
				{
				
			?>
		
				<br>
				<p align="center"><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><!--&nbsp;&nbsp;<input type="submit" value="Desistir" onClick="document.formato3.action.value='desistir'">&nbsp;&nbsp;-->&nbsp;&nbsp;<input type="submit" value="Anular" onClick="document.formato3.action.value='anular'">&nbsp;&nbsp;<?php } ?></p>
				<br>
	
			<?php
			

				}

			?>
			<br>
		</form>
		
	<?php

}
else
{
	$mensaje = "No se encontraron registros";
	
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
	