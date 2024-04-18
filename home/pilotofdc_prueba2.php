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
<?php
			if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
			|| $_SESSION["S_TIPO"] == "OPERACIONES" 
			|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
			&& $_SESSION["S_SOLOLECTURA"] != "1") { ?>
		<tr>
		<td><a href="deshabilitar_usuarios_fdc.php">Deshabilitar Usuarios</a></td>
		</tr>
		<?php
			}
		?>
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

								$rs1 = sqlsrv_query($queryDB, $link);

								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
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
							
							$rs1 = sqlsrv_query($queryDB, $link);
							
							while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
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

								$rs1 = sqlsrv_query($queryDB, $link);
								
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
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
										
										$rs1 = sqlsrv_query($queryDB, $link);
										
										while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
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
		
		$queryDB = "select si.* from simulaciones si 
		INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
		INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
		INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
		INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";
		
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
		
		$rs = sqlsrv_query($queryDB, $link);
		//echo "asignaciones.....".$_REQUEST["action"];
		//echo "<br>".$queryDB;
		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
		{
			
			
			if ($_REQUEST["chk".$fila["id_simulacion"]])
			{
				//echo $_REQUEST["action"];
				//echo $_REQUEST["chk".$fila["id_simulacion"]]."<br>";
				if ($_REQUEST["action"] == "desistir")
				{
					if ($fila["decision"] == $label_viable)
						//sqlsrv_query("update simulaciones set estado = 'DST', id_subestado = NULL where id_simulacion = '".$fila["id_simulacion"]."'", $link);
						sqlsrv_query("update simulaciones set estado = 'DST' where id_simulacion = '".$fila["id_simulacion"]."'", $link);
					else
						echo "<script>alert('La simulacion ".$fila["cedula"]." ".$fila["nombre"]." no puede ser Desistida. No cumple con las condiciones para realizar esta accion');</script>";
				}
				if ($_REQUEST["action"] == "anular")
				{
					//echo "ANULAR: ".$fila["id_simulacion"].",";
					sqlsrv_query("update simulaciones set estado = 'ANU', id_subestado = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = NOW() where id_simulacion = '".$fila["id_simulacion"]."'", $link);
				}
		
			}
		}
	}

	if (!$_REQUEST["page"])
	{
		$_REQUEST["page"] = 0;
	}
	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
	|| $_SESSION["S_TIPO"] == "OPERACIONES" 
	|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
	&& $_SESSION["S_SOLOLECTURA"] != "1") { 
	}else{
		?>
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<?php 
				$consultarMinCreditos="SELECT a.cantidad_creditos,a.id_usuario,a.login,a.nombre,a.apellido,CASE WHEN a.disponible='s' THEN 'DISPONIBLE' WHEN a.disponible='n' THEN 'NO DISPONIBLE' WHEN a.disponible='g' THEN 'EN GESTION' ELSE 'NO DISPONIBLE' END AS estado_usuario,a.disponible AS estado
				FROM
				usuarios a
				
				WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."'";
				$queryMinCreditos=sqlsrv_query($consultarMinCreditos,$link);
				$resMinCreditos = sqlsrv_fetch_array($queryMinCreditos);

				$consultarCantidadCreditos="SELECT count(id) as cantidad FROM simulaciones_fdc WHERE id_subestado<>'28' AND estado=4 AND DATE_FORMAT(fecha_creacion,'%Y-%m-%d')=CURRENT_DATE() AND id_usuario_creacion='".$_SESSION["S_IDUSUARIO"]."'";
				$queryCantidadCreditos=sqlsrv_query($consultarCantidadCreditos,$link);
				$resCantidadCreditos=sqlsrv_fetch_array($queryCantidadCreditos);
			?>

			<td colspan="19" align="right"><b>Cantidad Creditos Minimo: <?php echo $resMinCreditos["cantidad_creditos"];?>
			<br>Cantidad Creditos Hoy: <?php echo $resCantidadCreditos["cantidad"]; ?></b></td>
		</tr>
	</table>
		<?php
	}
	
	$x_en_x = 100;

	$offset = $_REQUEST["page"] * $x_en_x;

	$queryDB = "select 
	CASE WHEN (us.freelance=1 and us.outsourcing=0) then 'FREELANCE' WHEN (us.freelance=0 and us.outsourcing=1) then 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,
	sfd.estado as estado_sfd,si.id_analista_riesgo_operativo,si.id_simulacion, si.cedula, si.empleado_manual, si.nombre, si.pagaduria, 
	us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, si.opcion_credito, si.opcion_desembolso_cli, si.opcion_desembolso_ccc, 
	si.opcion_desembolso_cmp, si.opcion_desembolso_cso, si.retanqueo_total, si.valor_credito, si.decision, si.frente_al_cliente, 
	si.fecha_radicado 
	from simulaciones si 
	INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
	INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
	LEFT JOIN simulaciones_fdc sfd ON sfd.id_simulacion=si.id_simulacion where sfd.estado in (1,2,5,3) and sfd.vigente='s'";

	$queryDB_count = "select COUNT(*) as c from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN simulaciones_fdc sfd ON sfd.id_simulacion=si.id_simulacion where sfd.estado in (1,2,5,3) and sfd.vigente='s'";

	$queryDB_suma = "select SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc WHEN 'CMP' THEN si.opcion_desembolso_cmp WHEN 'CSO' THEN si.opcion_desembolso_cso END) as s, SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc - si.retanqueo_total WHEN 'CMP' THEN si.opcion_desembolso_cmp - si.retanqueo_total WHEN 'CSO' THEN si.opcion_desembolso_cso - si.retanqueo_total END) as s2, SUM(si.valor_credito) as s3 from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN simulaciones_fdc sfd ON sfd.id_simulacion=si.id_simulacion where sfd.estado in (1,2,5,3) and sfd.vigente='s'";


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

	$queryDB .= " order by si.id_simulacion ASC LIMIT ".$x_en_x." OFFSET ".$offset;

	$rs = sqlsrv_query($queryDB, $link);

	$rs_count = sqlsrv_query($queryDB_count, $link);

	$fila_count = sqlsrv_fetch_array($rs_count);

	$cuantos = $fila_count["c"];

	$rs_suma = sqlsrv_query($queryDB_suma, $link);

	$fila_suma = sqlsrv_fetch_array($rs_suma);

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

				<div id="divTablaFDC">
			<table class="tab3" id="tablaFDC">
				<thead>
				<tr>
				<th>ID Simulacion</th>
					<th>C&eacute;dula</th>
					<th>Nombre</th>
					<th>Pagadur&iacute;a</th>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Comercial</th><?php } ?>
				<!--<th>SE AGREGA COLUMNA TIPO COMERCIAL EN TABLA PILOTO FDC. ING JAIRO ZAPATA FECHA 23/11/2021</th>-->	<?php  if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Tipo Comercial</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><th>Oficina</th><?php } ?>
					<th>F. Radicado</th>
					<th>Tiempo Prospecci&oacute;n</th>
					<th>Estado</th>
					<th><img src="../images/frentecliente.png" title="Frente al Cliente"></th>
					
					<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><th><img src="../images/adjuntar.png" title="Adjuntos"></th><?php } ?>
					<th>&nbsp;</th>
					<th>Est.</th>
					<th>Hist.</th>
					<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th style="width:100%;">&nbsp;</th><?php } ?>
			
				</tr>
				</thead>
				<tbody>
				<?php

					$j = 1;
					
					while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
					{
						$reprocesos="";
						$contarReprocesos="SELECT id_simulacionsubestado,id_subestado FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."'";
    					$queryReprocesos=sqlsrv_query($contarReprocesos,$link);
    					if (sqlsrv_num_rows($queryReprocesos)==0)
						{
							$reprocesos="NUEVO";
						}else{
							$contarReprocesos2=sqlsrv_query($contarReprocesos." and id_subestado in (70,72)",$link);
							if (sqlsrv_num_rows($contarReprocesos2)==1)
							{
								$reprocesos="REPROCESO 1";
							}else if (sqlsrv_num_rows($contarReprocesos2)>1){
								$reprocesos="REPROCESOS";
							}else{
								$reprocesos="NUEVO";
							}
						}
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

						$consultarTiempoRadicado=sqlsrv_query("SELECT * FROM simulaciones_fdc WHERE id=(SELECT max(id) FROM simulaciones_fdc where estado=2 and id_simulacion='".$fila["id_simulacion"]."')",$link);
						if (sqlsrv_num_rows($consultarTiempoRadicado)>0){
							$resTiempoRadicado=sqlsrv_fetch_array($consultarTiempoRadicado);
							$tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($resTiempoRadicado["fecha_creacion"]);
							$fecha_prospeccion = $resTiempoRadicado["fecha_creacion"];
						}else{
							$tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($fila["fecha_radicado"]);
							$fecha_prospeccion = $fila["fecha_radicado"];
						}

						
						
						
						
						$tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);
						
						$tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);
						
						$tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
						
						if ($tiempo_prospeccion_horas)
							$tiempo_prospeccion_letras .= $tiempo_prospeccion_horas."h ";
						
						$tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos."m ";
						
						$tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos."s";
						$id_analista_riesgo_operativo=$fila["id_analista_riesgo_operativo"];
						
				?>
				<tr <?php echo $tr_class ?>>
				<td><?php echo $fila["id_simulacion"];?></td>
					<td><a href="simulador.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&decisionb=<?php echo $decisionb ?>&id_oficinab=<?php echo $id_oficinab ?>&visualizarb=<?php echo $visualizarb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&back=pilotofdc&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
					<td<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && $fila["empleado_manual"]) { ?> style="color:#FF0000"<?php } ?>><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td><?php } ?>
					<!--<th>SE AGREGA COLUMNA TIPO COMERCIAL EN TABLA PILOTO FDC. ING JAIRO ZAPATA FECHA 23/11/2021</th>-->	<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["tipo_comercial2"]) ?></td><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><td><?php echo utf8_decode($fila["oficina"]) ?></td><?php } ?>
					<td align="center"><nobr><?php echo $fecha_prospeccion ?></nobr></td>
					<td align="center"><nobr><?php echo $tiempo_prospeccion_letras ?></nobr></td>
					<td align="center"><nobr><?php echo $reprocesos ?></nobr></td>
					<td align="center"><?php if ($fila["frente_al_cliente"] == "SI") { ?><img src="../images/frentecliente.png" title="Frente al Cliente"><?php } ?></td>
					<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><td align="center"><a href="adjuntos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&id_comercialb=<?php echo $id_comercialb ?>&decisionb=<?php echo $decisionb ?>&id_oficinab=<?php echo $id_oficinab ?>&visualizarb=<?php echo $visualizarb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&back=pilotofdc&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adjuntar.png" title="Adjuntos"></a></td><?php } ?>
					<?php 
						if ($fila["estado_sfd"]==3)
						{
							?>
							<td align="center"><a id="btnDetenerProceso" name="<?php echo $fila["id_simulacion"];?>"><img src="../images/dot_azul.png" title="Detener Proceso"></a></td>
							<?php
						}else if ($fila["estado_sfd"]==2){
							?>
							<td align="center"><a id="btnDetenerProceso" name="<?php echo $fila["id_simulacion"];?>"><img src="../images/dot_rojo.png" title="Detener Proceso"></a></td>
							<?php
						}else{
							?>
							<td>&nbsp</td>
							<?php
						} 
						?>
							<td>
					<div class="badge-success" id="btnModalEstados" name="<?php echo $fila['id_simulacion'];?>" type="button"
                                        class="open-modal" data-open="modal1">
					<center><img src="../images/solicitud.png" title="Estados"></center></div>
					</td>
					<td>
					<div class="badge-success" id="btnModalHistorial" name="<?php echo $fila['id_simulacion'];?>" type="button"
                                        class="open-modal" data-open="modal1"><center>
					<img src="../images/proceso.png" title="Historial"></center></div></td>
					<?php
					if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
					|| $_SESSION["S_TIPO"] == "OPERACIONES" 
					|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
					&& $_SESSION["S_SOLOLECTURA"] != "1") { ?>
					<td align="center" style="width:100%;">

					<select id="usuarios_riesgo_operativo" name="<?php echo $fila["id_simulacion"];?>" style=" background-color:#EAF1DD">
					<?php
					?>
					
					<?php
						$queryOficinaNegocioEspecial=sqlsrv_query("SELECT id FROM definicion_tipos where id_tipo=3 and descripcion=(select id_unidad_negocio from simulaciones where id_simulacion=".$fila['id_simulacion'].")",$link);
						if (sqlsrv_num_rows($queryOficinaNegocioEspecial)>0)
						{
							$consultarUsuariosInicio="(SELECT a.* 
							FROM 
							usuarios a 
							WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and DATE_FORMAT(a.fecha_ultimo_acceso,'%Y-%m-%d')=CURRENT_DATE() AND a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g'))";
						}else{
							$consultarUsuariosInicio="(SELECT a.* 
							FROM 
							usuarios a 
							WHERE a.id_usuario not IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and DATE_FORMAT(a.fecha_ultimo_acceso,'%Y-%m-%d')=CURRENT_DATE() AND a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g'))";
						}
						

						if ($id_analista_riesgo_operativo==null)
						{
							
						}else{
							$consultarUsuariosInicio.=" UNION (SELECT * FROM usuarios where id_usuario='".$id_analista_riesgo_operativo."')";
						}
							
	
							$rs1 = sqlsrv_query($consultarUsuariosInicio, $link);
							if (sqlsrv_num_rows($rs1)<=0)
							{
								?>
								<option value="">SIN ANALISTAS DISPONIBLES</option>
								<?php
							}else{
								?>
								<option value=""></option>
								<?php
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
								{
								
									if ($fila1["id_usuario"] == $id_analista_riesgo_operativo)
									{
										$selected_ciudad = " selected";
									}
									else
									{
										$selected_ciudad = "";
									}
									echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_ciudad.">".$fila1["nombre"]." ".$fila1["apellido"]."</option>\n";
								}
							}
	
	
?>
					</select>
					</td><?php } ?>			
									</tr>
				<?php

					$j++;
				}
				
				?>
</tbody>
			</table>
</div>
			<br>
			<?php

				if ($_SESSION["S_SOLOLECTURA"] != "1")
				{
				
			?>
		
				<br>
					<table>
					<tr>
						
						<td><p align="center"><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><!--&nbsp;&nbsp;<input type="submit" value="Desistir" onClick="document.formato3.action.value='desistir'">&nbsp;&nbsp;-->&nbsp;&nbsp;<input type="submit" value="Asignar" id="btnAsignarUsuarios">&nbsp;&nbsp;<?php } ?></p></td>
					</tr>
				</table>
				
				
				<?php
					
					}

			?>
			<br>
		</form>
		
<div class="modal" id="modal1" data-animation="slideInOutLeft">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                Documento solicitado
                                <button type="button" class="close-modal" data-close>
                                    x
                                </button>
                            </header>
                            <section class="modal-content">
							<iframe id="iframe_servicios" width="500" height="300"></iframe>
                            </section>
                            <footer class="modal-footer">
                                Derechos reservados Kredit 2021
                            </footer>
                        </div>
                    </div>
		
		
	<?php

}
else
{
	$mensaje = "No se encontraron registros";
	
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

?>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/modal/modal.js"></script>

<script type="text/javascript">

$("#divTablaFDC").on('click','div',function(){
	var opcion=$(this).attr('name');
	var action=$(this).attr('id');
	if(action=="btnModalEstados"){
		var id_Simulacion = opcion;
        var url = ("contenidoModalEstadosCredito.php?idSimulacion=" + id_Simulacion);
        $("#iframe_servicios").attr("src", url);
	}else if(action=="btnModalHistorial"){
		var id_Simulacion = opcion;
        var url = ("contenidoModalHistorialCredito.php?idSimulacion=" + id_Simulacion);
        $("#iframe_servicios").attr("src", url);
	}
});
	 

$("#divTablaFDC").on('click','a',function(){
	var opcion=$(this).attr('name');
	var action=$(this).attr('id');
	if (action=="btnDetenerProceso")
	{
		var frmAsignarAnalistas =  "exe=detenerProceso&idSimulacion="+opcion+"&idUsuario="+<?php echo $_SESSION["S_IDUSUARIO"];?>;
			
			$.ajax({
				type: 'POST',
				url: 'pilotofdc_funcion.php',
				data: frmAsignarAnalistas,

		
				success: function(data) {
					//alert(data);
					if (data==1)
					{
						location.reload();
					}else{
						alert("NO PUEDE REALIZAR ESTA ACCION POR EL ESTADO ACTUAL");
					}
						


					return false;
				}


			});
	}
});
	 
$('#btnAsignarUsuarios').click(function(e)
	{
		e.preventDefault();
		var simulacionesAnalistas=[];
		$("#tablaFDC tbody tr").each(function(){
			var attr		=$(this).closest("tr").find('select :selected').val();
			var attr2		=$(this).closest("tr").find('select').attr("name");
			
			var data2={};
			data2.idSimulacion=attr2;
			data2.idAnalista=attr;
			simulacionesAnalistas.push(data2);
			
		});

		//alert(JSON.stringify(simulacionesAnalistas));
		var frmAsignarAnalistas =  "exe=asignarAnalistas&asignacionSimulacionAnalista="+JSON.stringify(simulacionesAnalistas);
			
			$.ajax({
				type: 'POST',
				url: 'pilotofdc_funcion.php',
				data: frmAsignarAnalistas,

		
				success: function(data) {
					//alert(data);
					if (data==1)
					{
						location.reload();
					}else{
						alert("ERROR AL EJECUTAR ACCION");
					}
						


					return false;
				}


			});
		


	});
	
</script>

<?php include("bottom.php"); ?>

