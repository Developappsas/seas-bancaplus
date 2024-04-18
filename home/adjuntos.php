<?php
include('../functions.php');
include('../function_blob_storage.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/**
 * 2016-03-16  Los usuarios prefieren la opción PUBLICA por defecto
 * Removido ENABLE en: <input type="checkbox" name="privado" value="1">
 * jlvalencia
 * 2016-03-22 Se requiere que SUBTIPO ANALISTA_TESORERIA pueda borrar documentos
 * 001, procesamiento de la ACTION BORRAR, encabezados de columna borrar, filas checkbox y botón
 */
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_ADJUNTOS"]) {
	exit("Session Caducada");
}

$link = conectar_utf();
$upmax_rs = sqlsrv_query($link, "select valor from parametros where codigo IN ('UPMAX') order by codigo");

$fila1 = sqlsrv_fetch_array($upmax_rs);

$upmax = $fila1['valor'];

$queryDB = "select estado from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

$simulacion_rs = sqlsrv_query($link, $queryDB);

$simulacion = sqlsrv_fetch_array($simulacion_rs);

// 001
//$habilitar_borrar = ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION"|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST" || $simulacion["estado"] == "DES") && ($_SESSION["S_SOLOLECTURA"] != "1");


$habilitar_borrar = ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST" || $simulacion["estado"] == "DES") && ($_SESSION["S_SOLOLECTURA"] != "1");

?>
<?php include("top.php"); ?>
<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<style type="text/css">
	.image-upload>input {
		display: none;
	}

	.image-upload img {
		width: 16px;
		cursor: pointer;
	}

	.tab {
		overflow: hidden;
		border: 1px solid #f5f3f3;
		background-color: #fff;
	}

	/* Style the buttons that are used to open the tab content */
	.tab button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
	}

	/* Change background color of buttons on hover */
	.tab button:hover {
		background-color: #ddd;
	}

	/* Create an active/current tablink class */
	.tab button.active {
		background-color: #e3e7f7;
	}

	/* Style the tab content */
	.tabcontent {
		display: none;
		padding: 6px 12px;
	}

</style>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><?php if (!$_REQUEST["back"]) {
										$_REQUEST["back"] = "simulaciones";
									} ?><a href="<?php echo $_REQUEST["back"] ?>.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Adjuntos</b><br><br></center>
		</td>
	</tr>
</table>

<div class="tab" style="width:85%; margin-top: 8px;">
	<button class="tablinks active" onclick="openCity(event, 'adjuntos_fisicos')">ADJUNTOS FISICOS</button>
	<button class="tablinks" onclick="openCity(event, 'adjuntos_virtuales')">ADJUNTOS DIGITALES</button>
</div>

<div id="adjuntos_fisicos" style="display: block;" class="tabcontent">
	<?php
	if ($_SESSION["S_SOLOLECTURA"] != "1") {
		if ($_SESSION["S_SUBTIPO"] != "ANALISTA_BD") { ?>
			<form name=formato method=post action="adjuntos_crear.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
				<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
				<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
				<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
				<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
				<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
				<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
				<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
				<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
				<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
				<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
				<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
				<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
				<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
				<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
				<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
				<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
				<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
				<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
				<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
				<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
				<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
				<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
				<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
				<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
				<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
				<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
				<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
				<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
				<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
				<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
				<input type="hidden" name="back" value="<?php echo $_REQUEST["back"] ?>">
				<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
				<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
				<table>
					<tr>
						<td>
							<div class="box1 clearfix">
								<table border="0" cellspacing=1 cellpadding=2>
									<tr>
										<td valign="bottom"><?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?>Tipo Adjunto<br><?php } ?>
										<select name="id_tipo" <?php if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop") { ?> style="width:160px" <?php } ?>>
											<option value=""><?php if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop") { ?>Tipo Adjunto<?php } ?></option>
											<?php
											$queryDB = "SELECT id_tipo, nombre from tipos_adjuntos where estado = '1' AND id_tipo NOT IN (" . $tiposadjuntos_firmados . ") order by nombre";
											$rs1 = sqlsrv_query($link, $queryDB);

											while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
												echo "<option value=\"" . $fila1["id_tipo"] . "\">" . ($fila1["nombre"]) . "</option>\n";
											} ?>
										</select>&nbsp;&nbsp;&nbsp;
										</td>
										<td valign="bottom"><?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?>Descripci&oacute;n<br><?php } ?><input type="text" name="descripcion" maxlength="255" size="<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?>50<?php } else { ?>20" placeholder="Descripci&oacute;n<?php } ?>"></td>
										<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?>
											<td valign="bottom">Archivo (Tama&ntilde;o M&aacute;ximo: <?php echo number_format($upmax / 1024, 2, ".", ",") ?> MB)<br><input type="file" name="archivo" accept="image/*,.pdf"></td>
											<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "OPERACIONES") { ?>
												<td valign="bottom" align="center">Privado<br><input type="checkbox" name="privado" value="1"></td><?php } ?>
											<td valign="bottom">&nbsp;<br><input type="submit" value="Adjuntar archivo"></td>
										<?php } else { ?>
											<td>
												<div class="image-upload">
													<label for="archivo"><img src="../images/upload.png" alt="Click aqui para subir un adjunto" title="Click aqui para subir un adjunto"></label>
													<input id="archivo" name="archivo" type="file" accept="image/*,.pdf" onChange="alert('Adjunto seleccionado');" />
												</div>
											</td>
									</tr>
									<tr>
										<td colspan="3" align="center"><input type="submit" value="Adjuntar archivo"></td>
									<?php }	?>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</form>
			<hr noshade size=1 width=350>
			<br>
	<?php

		}
	}

	?>
	<?php
	//echo "action: ".$_REQUEST["action"];
	if ($_REQUEST["action"]) {
		$queryDB = "SELECT ad.*, ta.nombre from adjuntos ad INNER JOIN simulaciones si ON ad.id_simulacion = si.id_simulacion INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN tipos_adjuntos ta ON ad.id_tipo = ta.id_tipo where ad.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "' AND (ad.privado = '0' OR NOT (us.freelance = '1' OR us.outsourcing = '1'))";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
			$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "')";

			if ($_SESSION["S_SUBTIPO"] == "PLANTA")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";

			if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
				$queryDB .= " AND si.telemercadeo IN ('0','1')";

			if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";

			if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
				$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";

			if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
				$queryDB .= " AND si.telemercadeo = '1'";
		}

		$queryDB .= " order by ad.id_adjunto";
		//echo $queryDB;

		$rs = sqlsrv_query($link, $queryDB);

		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
			if ($_REQUEST["chk" . $fila["id_adjunto"]] == "1") {
				if ($_REQUEST["action"] == "borrar" && $habilitar_borrar) {
					
					$archivo = sqlsrv_query($link, "SELECT id_tipo, nombre_grabado from adjuntos where id_adjunto = '" . $fila["id_adjunto"] . "'");
					$fila1 = sqlsrv_fetch_array($archivo);
					// if ($fila1["nombre_grabado"]){
					// 	delete_file("simulaciones", $_REQUEST["id_simulacion"] . "/adjuntos/" . $fila1["nombre_grabado"]);
					// }
					sqlsrv_query($link, "UPDATE tesoreria_cc set id_adjunto = NULL, usuario_modificacion = '". $_SESSION["S_LOGIN"] ."', fecha_modificacion = GETDATE() where id_adjunto = '" . $fila["id_adjunto"] . "'");
					sqlsrv_query($link, "update simulaciones_comprascartera set id_adjunto = NULL where id_adjunto = '".$fila["id_adjunto"]."'");
					$elimino = sqlsrv_query($link, "update adjuntos set eliminado = 1,  fecha_eliminacion = GETDATE(),  usuario_eliminacion ='".$_SESSION["S_LOGIN"]."' where id_adjunto = '".$fila['id_adjunto']."'");
				}
			}
		}
	}

	$queryDB = "SELECT ad.*, ta.nombre from adjuntos ad INNER JOIN simulaciones si ON ad.id_simulacion = si.id_simulacion INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN tipos_adjuntos ta ON ad.id_tipo = ta.id_tipo where ad.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "' AND (ad.privado = '0' OR NOT (us.freelance = '1' OR us.outsourcing = '1'))";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "')";

		if ($_SESSION["S_SUBTIPO"] == "PLANTA"){
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
		}

		if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS"){
			$queryDB .= " AND si.telemercadeo IN ('0','1')";
		}

		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO"){
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
		}

		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS"){
			$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
		}

		if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO"){
			$queryDB .= " AND si.telemercadeo = '1'";
		}

		if ($_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "PROSPECCION"){
			$queryDB .= " AND ad.privado = '0' ";
		}
	}

	//echo $queryDB .= " order by ad.id_adjunto";


	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	if (sqlsrv_num_rows($rs)) { ?>
		<form name="formato3" id="formato3" method="post" action="adjuntos.php" >
			<input type="hidden" name="action" value="">
			<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
			<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
			<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
			<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
			<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
			<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
			<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
			<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
			<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
			<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
			<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
			<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
			<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
			<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
			<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
			<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
			<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
			<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
			<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
			<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
			<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
			<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
			<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
			<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
			<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
			<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
			<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
			<input type="hidden" name="back" value="<?php echo $_REQUEST["back"] ?>">
			<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
			<table border="0" cellspacing=1 cellpadding=2 class="tab1" id="tablaAdjuntosCargados" name="tablaAdjuntosCargados">
				
				<tr>
					<th>Tipo Adjunto</th>
					<th>Descripcion</th>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Archivo</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Usuario</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Fecha</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "OPERACIONES") { ?>
						<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Privado</th><?php } ?>
					<?php } ?>

					<th><img src="../images/archivo.png" title="Abrir Adjunto"></th>
					<?php
					if ($habilitar_borrar) {
					?>
						<th><img src="../images/delete.png" title="Borrar Adjunto"></th>
					<?php } ?>

				</tr>
				<?php
				$j = 1;

				while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
					$tr_class = "";

					if (($j % 2) == 0) {
						$tr_class = " style='background-color:#F1F1F1;'";
					}

					switch ($fila["privado"]) {
						case '1':
							$privado = "SI";
							break;
						case '0':
							$privado = "NO";
							break;
					}
				?>
					<tr id="adjunto" eliminado="<?=$fila['eliminado']?>">
						<td <?php if($fila['eliminado']==1){?> style="color: #dc0000;"  <?php } ?>><?php echo ($fila["nombre"]) ?></td>
						<td><?php echo ($fila["descripcion"]) ?></td>
						<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td><?php echo ($fila["nombre_original"]) ?></td><?php } ?>
						<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center"><?php echo ($fila["usuario_creacion"]) ?></td><?php } ?>
						<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center"><?php echo $fila["fecha_creacion"] ?></td><?php } ?>
						<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><td align="center"><?php echo $privado ?></td><?php } ?><?php } ?>
						<td align=center><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones", $_REQUEST["id_simulacion"] . "/adjuntos/" . $fila["nombre_grabado"]) ?>','ADJUNTO<?php echo $fila["id_adjunto"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Abrir Adjunto"></a>
							<spam style="font-size:0px;"><?php echo generateBlobDownloadLinkWithSAS("simulaciones", $_REQUEST["id_simulacion"] . "/adjuntos/" . $fila["nombre_grabado"]) ?></spam>
						</td>
						<?php
						if ($habilitar_borrar) {
							echo '<td align="center">';
								if (($_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL") || (($_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL") && $fila["id_tipo"] == $tipoadjunto_cdd)) {
									if($fila['eliminado']!=1){
										echo '<input type="checkbox" name="chk'.$fila["id_adjunto"].'" value="1">';
									}else{
										echo "Eliminado";
									}
								} else {
									echo "&nbsp;";
								}
							echo '</td>';
						}
						?>
					</tr>
				<?php
					$j++;
				}
				?>
			</table>
			<br>
			 <p align="center">
			 <input type="button" id="mostrarEliminados" value="Ocultar eliminados">
			<?php
			if ($habilitar_borrar) { ?>
				<input type="button"  id="borrar" value="Borrar" onClick="document.formato3.action.value = 'borrar'; ">
			<?php } ?>
			</p>
		</form>
	<?php
	} else {
		echo "<table><tr><td>No se encontraron registros</td></tr></table>";
	}
	?>
</div>

<div id="adjuntos_virtuales" class="tabcontent">
	<?php
	$val1 = 0;
	$val2 = 0;
	$val3 = 0;
	$queryADO = sqlsrv_query($link, "SELECT id_simulacion FROM historial_tokens_verificacion_id f WHERE f.id = (SELECT MAX(id) FROM historial_tokens_verificacion_id WHERE id_simulacion = f.id_simulacion) AND f.id_simulacion = " . $_REQUEST["id_simulacion"] . " AND f.estado = 1 AND estado_respuesta IS NOT NULL AND estado_respuesta IN(2,14)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$val1 = sqlsrv_num_rows($queryADO);

	$queryFormularioDigital = sqlsrv_query($link, "SELECT b.id_unidad_negocio, un.id_empresa, a.* FROM formulario_digital a JOIN simulaciones b ON b.id_simulacion = a.id_simulacion LEFT JOIN unidades_negocio un ON b.id_unidad_negocio = un.id_unidad WHERE a.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND b.formato_digital = 1", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$val2 = sqlsrv_num_rows($queryFormularioDigital);

	$queryOTP = sqlsrv_query($link, "SELECT id_simulacion FROM historial_sms_otp f WHERE f.id = (SELECT MAX(id) FROM historial_sms_otp WHERE id_simulacion = f.id_simulacion) AND f.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND f.estado = 1", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$val3 = sqlsrv_num_rows($queryOTP);

	if ($val1 <> 0 || $val2 <> 0 || $val3 <> 0) { ?>
		<h1 style="margin-bottom: 10px; margin-top: 10px;">ARCHIVOS GENERADOS ELECTRONICAMENTE</h1>
		<table border="0" cellspacing=1 cellpadding=2 class="tab2" id="tablaAdjuntosDigitales" name="tablaAdjuntosDigitales">
			<tr>
				<th>Tipo Adjunto</th>
				<th>Descripcion</th>
				<th><img src="../images/archivo.png" title="Abrir Adjunto"></th>
			</tr>
			<?php
			if ($val1 <> 0 || $val3 <> 0) { ?>
				<tr>
					<td>Autorización De Consulta</td>
					<td>Documento que certifica que el cliente autorizó la consulta a centrales de riesgo.</td>
					<td><a target="_blank" href="../formatos/formulario_autorizacion_cosultas.php?id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
			<?php
			}
			?>
			<?php
			if ($val2 <> 0) {
				$empresa = "kredit";
				$datosFrmDigital = sqlsrv_fetch_array($queryFormularioDigital);
				if ($datosFrmDigital["id_empresa"] == 2) {
					$empresa = "fianti";
				}
			?>
				<tr>
					<td>Conocimiento del Cliente</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=CONOCIMIENTO_CLIENTE&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Pagare</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=PAGARE&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Libranza</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=LIBRANZA&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Autorizacion Costos de Administracion</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=COSTO_ADMINISTRACION&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Plantilla Autorizacion Descuentos - Coomeva</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=PLANTILLA_AUTORIZACION&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Autorizacion de Descuento por Nomina de Pensionados</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=AUTORIZACION_DESCUENTO&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Certificado Individual Seguro de Vida - Colmena</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=INDIVIDUAL_SEGUROS&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Formato Novedades Beneficiarios</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=NOVEDADES_BENEFICIARIOS&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
				<tr>
					<td>Autorizacion Descuento COLFONDOS</td>
					<td>Formato generado con firma electronica a traves de eScala - Deceval.</td>
					<td><a target="_blank" href="../formatos/formulario_<?= $empresa ?>_Secciones.php?tipo_doc=AUTORIZACION_DESCUENTO_COLFONDOS&id_simulacion=<?= $_REQUEST['id_simulacion'] ?>"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
				</tr>
			<?php
			}
			?>
		</table>
	<?php
	} else { ?>
		<h1 style="margin-bottom: 10px; margin-top: 10px;">NO HAY DATOS PARA MOSTRAR</h1>

	<?php }  ?>
</div>

<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script language="JavaScript">

	function chequeo_forma() {
		with(document.formato) {
			if ((id_tipo.value == "") || (descripcion.value == "") || (archivo.value == "")) {
				alert("Debe seleccionar el tipo, digitar una descripcion y seleccionar el archivo");
				return false;
			}

			ReplaceComilla(observacion)			
		}
	}

	function openCity(evt, tabItem) {
		// Declare all variables
		var i, tabcontent, tablinks;

		// Get all elements with class="tabcontent" and hide them
		tabcontent = document.getElementsByClassName("tabcontent");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}

		// Get all elements with class="tablinks" and remove the class "active"
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}

		// Show the current tab, and add an "active" class to the button that opened the tab
		document.getElementById(tabItem).style.display = "block";
		evt.currentTarget.className += " active";
	}
	$(document).ready(function(){
		$('#mostrarEliminados').click(function(){
			$('tr[eliminado="1"]').toggle();
			const value = $(this).val();
			$(this).val(value === 'Mostrar eliminados' ? 'Ocultar eliminados' : 'Mostrar eliminados' )
		});
	});

	
	const borrar = document.getElementById('borrar');
	borrar.addEventListener('click', function() {
		const formulario = document.getElementById('formato3');
		Swal.fire({
		    title: '¿Deseas Eliminar ',
	        icon: 'warning',
	        html: 'Al elimanar este archivo, quedara visible marcado como eliminado',
	        showCancelButton: true,
	        confirmButtonText: 'Eliminar',
	        cancelButtonText: 'Cancelar',
	        confirmButtonColor: '#3E5ABB',
	        denyButtonColor: '#ff5200'
		}).then((result) => {
			if (result.isConfirmed) {
				formulario.submit();
			}else{
				return false;
			}
		});
	});
	
</script>
<?php include("bottom.php"); ?>
