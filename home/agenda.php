<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD"  || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || !$_SESSION["FUNC_AGENDA"]) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">
	function recalcular_agenda(numero, se_compra) {
		with(document.formato3) {
			if (se_compra == "FE") {
				//fecha_vencimiento = addDate(document.getElementById("fecha_entrega"+numero).value, parseInt(document.getElementById("dias_vigencia"+numero).value));

				//document.getElementById("fecha_vencimiento"+numero).value = fecha_vencimiento;
			} else {
				if (document.getElementById("fecha_solicitudcarta" + numero).value != "") {
					fecha_sugerida = document.getElementById("fecha_solicitudcarta" + numero).value;
				} else {
					fecha_sugerida = document.getElementById("fecha_sugerida" + numero).value;
				}

				fecha_entrega = addDate(fecha_sugerida, parseInt(document.getElementById("dias_entrega" + numero).value));
				fecha_vencimiento = addDate(fecha_entrega, parseInt(document.getElementById("dias_vigencia" + numero).value));

				document.getElementById("fecha_entrega" + numero).value = fecha_entrega;
				document.getElementById("fecha_vencimiento" + numero).value = fecha_vencimiento;
			}
		}
	}

	function modificar(campoe, campofs, campofe, campofv) {
		with(document.formato3) {
			/*		if (campofe.value == "") {
						alert("Debe digitar fecha de entrega. Gesti�n no guardada");
						return false;
					}
					else if (campoe.value != "NO SOLICITADA" && campofs.value == "") {
						alert("Debe digitar fecha solicitud. Gesti�n no guardada");
						return false;
					}
					else {
						submit();
					}*/
			if (campofe.value != "" && campofv.value != "") {
				if (diffDate(campofe.value, campofv.value) < 0) {
					alert("La fecha de entrega no puede ser mayor que la fecha de vencimiento. Gestion no guardada");
					return false;
				} else {
					submit();
				}
			} else {
				submit();
			}
		}
	}
	//-->
</script>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Gestion Certificaciones</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato2" method="post" action="agenda.php">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<!--	<td valign="bottom">F. Sugerida Inicial<br>
		<input type="hidden" name="fechasug_inicialb" size="10" maxlength="10">
		<select name="fechasug_inicialbd">
			<option value="">D&iacute;a</option>
<?php

/*for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}*/

?>
		</select>
		<select name="fechasug_inicialbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fechasug_inicialba">
			<option value="">A&ntilde;o</option>
<?php

/*for ($i = 2014; $i <= date("Y") + 1; $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
		</select>
		<a href="javascript:show_calendar('formato2.fechasug_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
	<td valign="bottom">F. Sugerida Final<br>
		<input type="hidden" name="fechasug_finalb" size="10" maxlength="10">
		<select name="fechasug_finalbd">
			<option value="">D&iacute;a</option>
<?php

/*for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}*/

?>
		</select>
		<select name="fechasug_finalbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fechasug_finalba">
			<option value="">A&ntilde;o</option>
<?php

/*for ($i = 2014; $i <= date("Y") + 1; $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
		</select>
		<a href="javascript:show_calendar('formato2.fechasug_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>
	<td valign="bottom">F. Solicitud Inicial<br>
		<input type="hidden" name="fechasol_inicialb" size="10" maxlength="10">
		<select name="fechasol_inicialbd">
			<option value="">D&iacute;a</option>
<?php

/*for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}*/

?>
		</select>
		<select name="fechasol_inicialbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fechasol_inicialba">
			<option value="">A&ntilde;o</option>
<?php

/*for ($i = 2014; $i <= date("Y") + 1; $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
		</select>
		<a href="javascript:show_calendar('formato2.fechasol_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
	<td valign="bottom">F. Solicitud Final<br>
		<input type="hidden" name="fechasol_finalb" size="10" maxlength="10">
		<select name="fechasol_finalbd">
			<option value="">D&iacute;a</option>
<?php

/*for ($i = 1; $i <= 31; $i++)
{
	if (strlen($i) == 1)
	{
		$j = "0".$i;
	}
	else
	{
		$j = $i;
	}
	
	echo "<option value=\"".$j."\">".$j."</option>";
}*/

?>
		</select>
		<select name="fechasol_finalbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select name="fechasol_finalba">
			<option value="">A&ntilde;o</option>
<?php

/*for ($i = 2014; $i <= date("Y") + 1; $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
		</select>
		<a href="javascript:show_calendar('formato2.fechasol_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>-->
							<td valign="bottom">F. Entrega Inicial<br>
								<input type="hidden" name="fechaent_inicialb" size="10" maxlength="10">
								<select name="fechaent_inicialbd">
									<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++) {
										if (strlen($i) == 1) {
											$j = "0" . $i;
										} else {
											$j = $i;
										}

										echo "<option value=\"" . $j . "\">" . $j . "</option>";
									}

									?>
								</select>
								<select name="fechaent_inicialbm">
									<option value="">Mes</option>
									<option value="01">Ene</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Abr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Ago</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dic</option>
								</select>
								<select name="fechaent_inicialba">
									<option value="">A&ntilde;o</option>
									<?php

									for ($i = 2014; $i <= date("Y") + 1; $i++) {
										echo "<option value=\"" . $i . "\">" . $i . "</option>";
									}

									?>
								</select>
								<a href="javascript:show_calendar('formato2.fechaent_inicialb');"><img src="../images/calendario.gif" border=0></a>
							</td>
							<td valign="bottom">F. Entrega Final<br>
								<input type="hidden" name="fechaent_finalb" size="10" maxlength="10">
								<select name="fechaent_finalbd">
									<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++) {
										if (strlen($i) == 1) {
											$j = "0" . $i;
										} else {
											$j = $i;
										}

										echo "<option value=\"" . $j . "\">" . $j . "</option>";
									}

									?>
								</select>
								<select name="fechaent_finalbm">
									<option value="">Mes</option>
									<option value="01">Ene</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Abr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Ago</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dic</option>
								</select>
								<select name="fechaent_finalba">
									<option value="">A&ntilde;o</option>
									<?php

									for ($i = 2014; $i <= date("Y") + 1; $i++) {
										echo "<option value=\"" . $i . "\">" . $i . "</option>";
									}

									?>
								</select>
								<a href="javascript:show_calendar('formato2.fechaent_finalb');"><img src="../images/calendario.gif" border=0></a>
							</td>
							<!--</tr>
<tr>-->
							<td valign="bottom">F. Vencimiento Inicial<br>
								<input type="hidden" name="fechaven_inicialb" size="10" maxlength="10">
								<select name="fechaven_inicialbd">
									<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++) {
										if (strlen($i) == 1) {
											$j = "0" . $i;
										} else {
											$j = $i;
										}

										echo "<option value=\"" . $j . "\">" . $j . "</option>";
									}

									?>
								</select>
								<select name="fechaven_inicialbm">
									<option value="">Mes</option>
									<option value="01">Ene</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Abr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Ago</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dic</option>
								</select>
								<select name="fechaven_inicialba">
									<option value="">A&ntilde;o</option>
									<?php

									for ($i = 2014; $i <= date("Y") + 1; $i++) {
										echo "<option value=\"" . $i . "\">" . $i . "</option>";
									}

									?>
								</select>
								<a href="javascript:show_calendar('formato2.fechaven_inicialb');"><img src="../images/calendario.gif" border=0></a>
							</td>
							<td valign="bottom">F. Vencimiento Final<br>
								<input type="hidden" name="fechaven_finalb" size="10" maxlength="10">
								<select name="fechaven_finalbd">
									<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++) {
										if (strlen($i) == 1) {
											$j = "0" . $i;
										} else {
											$j = $i;
										}

										echo "<option value=\"" . $j . "\">" . $j . "</option>";
									}

									?>
								</select>
								<select name="fechaven_finalbm">
									<option value="">Mes</option>
									<option value="01">Ene</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Abr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Ago</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dic</option>
								</select>
								<select name="fechaven_finalba">
									<option value="">A&ntilde;o</option>
									<?php

									for ($i = 2014; $i <= date("Y") + 1; $i++) {
										echo "<option value=\"" . $i . "\">" . $i . "</option>";
									}

									?>
								</select>
								<a href="javascript:show_calendar('formato2.fechaven_finalb');"><img src="../images/calendario.gif" border=0></a>
							</td>
							<td valign="bottom">Cedula/Nombre<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="19" maxlength="50"></td>
							<?php

							if (!$_SESSION["S_SECTOR"]) {

							?>
								<td valign="bottom">Sector<br>
									<select name="sectorb" style="width:155px">
										<option value=""></option>
										<option value="PUBLICO">PUBLICO</option>
										<option value="PRIVADO">PRIVADO</option>
									</select>
								</td>
							<?php

							}

							?>
						</tr>
						<tr>
							<td valign="bottom">Pagaduria<br>
								<select name="pagaduriab" style="width:155px">
									<option value=""></option>
									<?php

									$queryDB = "SELECT nombre as pagaduria from pagadurias where 1 = 1";

									if ($_SESSION["S_SECTOR"]) {
										$queryDB .= " AND sector = '" . $_SESSION["S_SECTOR"] . "'";
									}

									$queryDB .= " order by pagaduria";

									$rs1 = sqlsrv_query($link, $queryDB);
									

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
										echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
									}

									?>
								</select>
							</td>
							<?php

							if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION") {

							?>
								<td valign="bottom">Comercial<br>
									<select name="id_comercialb" style="width:155px">
										<option value=""></option>
										<?php

										$queryDB = "SELECT distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join usuarios_unidades uu on us.id_usuario = uu.id_usuario where us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";

										$queryDB .= " AND uu.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";

										$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";

										$rs1 = sqlsrv_query($link, $queryDB);

										while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
											echo "<option value=\"" . $fila1["id_usuario"] . "\">" . utf8_decode($fila1["nombre"]) . " " . utf8_decode($fila1["apellido"]) . "</option>\n";
										}

										?>
									</select>
								</td>
								<?php

							} else {
								if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {

								?>
									<td valign="bottom">Comercial<br>
										<select name="id_comercialb">
											<option value=""></option>
									<?php

									$queryDB = "SELECT distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial inner join unidades_negocio un on si.id_unidad_negocio = un.id_unidad where si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "') AND us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";

									$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";

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

									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
										echo "<option value=\"" . $fila1["id_usuario"] . "\">" . utf8_decode($fila1["nombre"]) . " " . utf8_decode($fila1["apellido"]) . "</option>\n";
									}
								}
							}

									?>
									<td valign="bottom">Estado Carta<br>
										<select name="estadocartab" style="width:155px">
											<option value=""></option>
											<option value="NO SOLICITADA">NO SOLICITADA</option>
											<option value="SOLICITADA">SOLICITADA</option>
											<option value="ENTREGADA">ENTREGADA</option>
											<option value="CONFIRMADA">CONFIRMADA</option>
											<option value="PAGADA">PAGADA</option>
										</select>
									</td>
									<!--</tr>
<tr>-->
									<?php

									if ($_SESSION["FUNC_SUBESTADOS"]) {

									?>

										<td valign="bottom">Subestado<br>
											<select name="id_subestadob" style="width:155px">
												<option value=""></option>
												<?php

												$queryDB = "SELECT id_subestado, decision, nombre from subestados where estado = '1' order by decision DESC, nombre";

												$rs1 = sqlsrv_query($link, $queryDB);

												while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
													echo "<option value=\"" . $fila1["id_subestado"] . "\">" . substr($fila1["decision"], 0, 3) . "-" . utf8_decode($fila1["nombre"]) . "</option>\n";
												}

												?>
											</select>
										</td>
									<?php

									}

									?>
									<!--	<td valign="bottom">Entidad<br>
		<select name="entidadb" style="width:155px">
			<option value=""></option>-->
									<?php

									/*$queryDB = "select DISTINCT entidad from agenda ag INNER JOIN simulaciones si ON ag.id_simulacion = si.id_simulacion where si.estado IN ('ING', 'EST') AND si.id_subestado IS NOT NULL AND si.id_subestado NOT IN (".$subestados_sin_concretar.") AND si.id_subestado IN (".$subestados_agenda.") order by ag.entidad";

$rs1 = mysql_query($queryDB, $link);

while ($fila1 = mysql_fetch_array($rs1))
{
	echo "<option value=\"".$fila1["entidad"]."\">".stripslashes(utf8_decode($fila1["entidad"]))."</option>\n";
}*/

									?>
									<!--		</select>
	</td>-->
									<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php

if (!$_REQUEST["page"]) {
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT ofi.nombre as nombre_oficina,un.nombre as unidad_negocio,ag.*, si.cedula, si.nombre, si.pagaduria, us.nombre as nombre_comercial, us.apellido, et.nombre as nombre_etapa, se.nombre as nombre_subestado from agenda ag INNER JOIN simulaciones si ON ag.id_simulacion = si.id_simulacion INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN oficinas ofi ON ofi.id_oficina=si.id_oficina where si.estado IN ('ING', 'EST') AND si.id_subestado IS NOT NULL AND si.id_subestado NOT IN (" . $subestados_sin_concretar . ") AND si.id_subestado IN (" . $subestados_agenda . ")";

$queryDB_count = "SELECT COUNT(*) as c from agenda ag INNER JOIN simulaciones si ON ag.id_simulacion = si.id_simulacion INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa where si.estado IN ('ING', 'EST') AND si.id_subestado IS NOT NULL AND si.id_subestado NOT IN (" . $subestados_sin_concretar . ") AND si.id_subestado IN (" . $subestados_agenda . ")";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";

	$queryDB_count .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";

	$queryDB_count .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";

	$queryDB_count .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "')";

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

	$queryDB_count .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "')";

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
}

if ($_REQUEST["fechasug_inicialbd"] && $_REQUEST["fechasug_inicialbm"] && $_REQUEST["fechasug_inicialba"]) {
	$fechasug_inicialbd = $_REQUEST["fechasug_inicialbd"];

	$fechasug_inicialbm = $_REQUEST["fechasug_inicialbm"];

	$fechasug_inicialba = $_REQUEST["fechasug_inicialba"];

	$queryDB .= " AND ag.fecha_sugerida >= '" . $fechasug_inicialba . "-" . $fechasug_inicialbm . "-" . $fechasug_inicialbd . "'";

	$queryDB_count .= " AND ag.fecha_sugerida >= '" . $fechasug_inicialba . "-" . $fechasug_inicialbm . "-" . $fechasug_inicialbd . "'";
}

if ($_REQUEST["fechasug_finalbd"] && $_REQUEST["fechasug_finalbm"] && $_REQUEST["fechasug_finalba"]) {
	$fechasug_finalbd = $_REQUEST["fechasug_finalbd"];

	$fechasug_finalbm = $_REQUEST["fechasug_finalbm"];

	$fechasug_finalba = $_REQUEST["fechasug_finalba"];

	$queryDB .= " AND ag.fecha_sugerida <= '" . $fechasug_finalba . "-" . $fechasug_finalbm . "-" . $fechasug_finalbd . "'";

	$queryDB_count .= " AND ag.fecha_sugerida <= '" . $fechasug_finalba . "-" . $fechasug_finalbm . "-" . $fechasug_finalbd . "'";
}

if ($_REQUEST["fechasol_inicialbd"] && $_REQUEST["fechasol_inicialbm"] && $_REQUEST["fechasol_inicialba"]) {
	$fechasol_inicialbd = $_REQUEST["fechasol_inicialbd"];

	$fechasol_inicialbm = $_REQUEST["fechasol_inicialbm"];

	$fechasol_inicialba = $_REQUEST["fechasol_inicialba"];

	$queryDB .= " AND ag.fecha_solicitud >= '" . $fechasol_inicialba . "-" . $fechasol_inicialbm . "-" . $fechasol_inicialbd . "'";

	$queryDB_count .= " AND ag.fecha_solicitud >= '" . $fechasol_inicialba . "-" . $fechasol_inicialbm . "-" . $fechasol_inicialbd . "'";
}

if ($_REQUEST["fechasol_finalbd"] && $_REQUEST["fechasol_finalbm"] && $_REQUEST["fechasol_finalba"]) {
	$fechasol_finalbd = $_REQUEST["fechasol_finalbd"];

	$fechasol_finalbm = $_REQUEST["fechasol_finalbm"];

	$fechasol_finalba = $_REQUEST["fechasol_finalba"];

	$queryDB .= " AND ag.fecha_solicitud <= '" . $fechasol_finalba . "-" . $fechasol_finalbm . "-" . $fechasol_finalbd . "'";

	$queryDB_count .= " AND ag.fecha_solicitud <= '" . $fechasol_finalba . "-" . $fechasol_finalbm . "-" . $fechasol_finalbd . "'";
}

if ($_REQUEST["fechaent_inicialbd"] && $_REQUEST["fechaent_inicialbm"] && $_REQUEST["fechaent_inicialba"]) {
	$fechaent_inicialbd = $_REQUEST["fechaent_inicialbd"];

	$fechaent_inicialbm = $_REQUEST["fechaent_inicialbm"];

	$fechaent_inicialba = $_REQUEST["fechaent_inicialba"];

	$queryDB .= " AND ag.fecha_entrega >= '" . $fechaent_inicialba . "-" . $fechaent_inicialbm . "-" . $fechaent_inicialbd . "'";

	$queryDB_count .= " AND ag.fecha_entrega >= '" . $fechaent_inicialba . "-" . $fechaent_inicialbm . "-" . $fechaent_inicialbd . "'";
}

if ($_REQUEST["fechaent_finalbd"] && $_REQUEST["fechaent_finalbm"] && $_REQUEST["fechaent_finalba"]) {
	$fechaent_finalbd = $_REQUEST["fechaent_finalbd"];

	$fechaent_finalbm = $_REQUEST["fechaent_finalbm"];

	$fechaent_finalba = $_REQUEST["fechaent_finalba"];

	$queryDB .= " AND ag.fecha_entrega <= '" . $fechaent_finalba . "-" . $fechaent_finalbm . "-" . $fechaent_finalbd . "'";

	$queryDB_count .= " AND ag.fecha_entrega <= '" . $fechaent_finalba . "-" . $fechaent_finalbm . "-" . $fechaent_finalbd . "'";
}

if ($_REQUEST["fechaven_inicialbd"] && $_REQUEST["fechaven_inicialbm"] && $_REQUEST["fechaven_inicialba"]) {
	$fechaven_inicialbd = $_REQUEST["fechaven_inicialbd"];

	$fechaven_inicialbm = $_REQUEST["fechaven_inicialbm"];

	$fechaven_inicialba = $_REQUEST["fechaven_inicialba"];

	$queryDB .= " AND ag.fecha_vencimiento >= '" . $fechaven_inicialba . "-" . $fechaven_inicialbm . "-" . $fechaven_inicialbd . "'";

	$queryDB_count .= " AND ag.fecha_vencimiento >= '" . $fechaven_inicialba . "-" . $fechaven_inicialbm . "-" . $fechaven_inicialbd . "'";
}

if ($_REQUEST["fechaven_finalbd"] && $_REQUEST["fechaven_finalbm"] && $_REQUEST["fechaven_finalba"]) {
	$fechaven_finalbd = $_REQUEST["fechaven_finalbd"];

	$fechaven_finalbm = $_REQUEST["fechaven_finalbm"];

	$fechaven_finalba = $_REQUEST["fechaven_finalba"];

	$queryDB .= " AND ag.fecha_vencimiento <= '" . $fechaven_finalba . "-" . $fechaven_finalbm . "-" . $fechaven_finalbd . "'";

	$queryDB_count .= " AND ag.fecha_vencimiento <= '" . $fechaven_finalba . "-" . $fechaven_finalbm . "-" . $fechaven_finalbd . "'";
}

if ($_REQUEST["descripcion_busqueda"]) {
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

	$queryDB .= " AND (si.cedula = '" . $descripcion_busqueda . "' OR UPPER(si.nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%')";

	$queryDB_count .= " AND (si.cedula = '" . $descripcion_busqueda . "' OR UPPER(si.nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%')";
}

if ($_REQUEST["sectorb"]) {
	$sectorb = $_REQUEST["sectorb"];

	$queryDB .= " AND pa.sector = '" . $sectorb . "'";

	$queryDB_count .= " AND pa.sector = '" . $sectorb . "'";
}

if ($_REQUEST["pagaduriab"]) {
	$pagaduriab = $_REQUEST["pagaduriab"];

	$queryDB .= " AND si.pagaduria = '" . $pagaduriab . "'";

	$queryDB_count .= " AND si.pagaduria = '" . $pagaduriab . "'";
}

if ($_REQUEST["id_comercialb"]) {
	$id_comercialb = $_REQUEST["id_comercialb"];

	$queryDB .= " AND si.id_comercial = '" . $id_comercialb . "'";

	$queryDB_count .= " AND si.id_comercial = '" . $id_comercialb . "'";
}

if ($_REQUEST["entidadb"]) {
	$entidadb = $_REQUEST["entidadb"];

	$queryDB .= " AND ag.entidad = '" . $entidadb . "'";

	$queryDB_count .= " AND ag.entidad = '" . $entidadb . "'";
}

if ($_REQUEST["estadocartab"]) {
	$estadocartab = $_REQUEST["estadocartab"];

	$queryDB .= " AND ag.estado = '" . $estadocartab . "'";

	$queryDB_count .= " AND ag.estado = '" . $estadocartab . "'";
}

if ($_REQUEST["id_subestadob"]) {
	$id_subestadob = $_REQUEST["id_subestadob"];

	$queryDB .= " AND si.id_subestado = '" . $id_subestadob . "'";

	$queryDB_count .= " AND si.id_subestado = '" . $id_subestadob . "'";
}

$queryDB .= " order by si.nombre, ag.entidad, ag.fecha_entrega, ag.fecha_vencimiento DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";


$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


$rs_count = sqlsrv_query($link, $queryDB_count);
$fila_count = sqlsrv_fetch_array($rs_count);
$cuantos = $fila_count["c"];
if ($cuantos){
	if ($cuantos > $x_en_x)  {
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";

		$i = 1;
		$final = 0;

		while ($final < $cuantos) {
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);

			if ($final > $cuantos) {
				$final = $cuantos;
			}

			if ($link_page != $_REQUEST["page"]) {
				echo " <a href=\"agenda.php?fechasug_inicialbd=" . $fechasug_inicialbd . "&fechasug_inicialbm=" . $fechasug_inicialbm . "&fechasug_inicialba=" . $fechasug_inicialba . "&fechasug_finalbd=" . $fechasug_finalbd . "&fechasug_finalbm=" . $fechasug_finalbm . "&fechasug_finalba=" . $fechasug_finalba . "&fechasol_inicialbd=" . $fechasol_inicialbd . "&fechasol_inicialbm=" . $fechasol_inicialbm . "&fechasol_inicialba=" . $fechasol_inicialba . "&fechasol_finalbd=" . $fechasol_finalbd . "&fechasol_finalbm=" . $fechasol_finalbm . "&fechasol_finalba=" . $fechasol_finalba . "&fechaent_inicialbd=" . $fechaent_inicialbd . "&fechaent_inicialbm=" . $fechaent_inicialbm . "&fechaent_inicialba=" . $fechaent_inicialba . "&fechaent_finalbd=" . $fechaent_finalbd . "&fechaent_finalbm=" . $fechaent_finalbm . "&fechaent_finalba=" . $fechaent_finalba . "&fechaven_inicialbd=" . $fechaven_inicialbd . "&fechaven_inicialbm=" . $fechaven_inicialbm . "&fechaven_inicialba=" . $fechaven_inicialba . "&fechaven_finalbd=" . $fechaven_finalbd . "&fechaven_finalbm=" . $fechaven_finalbm . "&fechaven_finalba=" . $fechaven_finalba . "&descripcion_busqueda=" . $descripcion_busqueda . "&sectorb=" . $sectorb . "&pagaduriab=" . $pagaduriab . "&id_comercialb=" . $id_comercialb . "&entidadb=" . $entidadb . "&estadocartab=" . $estadocartab . "&id_subestadob=" . $id_subestadob . "&page=$link_page\">$i</a>";
			} else {
				echo " " . $i;
			}

			$i++;
		}

		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;

			echo " <a href=\"agenda.php?fechasug_inicialbd=" . $fechasug_inicialbd . "&fechasug_inicialbm=" . $fechasug_inicialbm . "&fechasug_inicialba=" . $fechasug_inicialba . "&fechasug_finalbd=" . $fechasug_finalbd . "&fechasug_finalbm=" . $fechasug_finalbm . "&fechasug_finalba=" . $fechasug_finalba . "&fechasol_inicialbd=" . $fechasol_inicialbd . "&fechasol_inicialbm=" . $fechasol_inicialbm . "&fechasol_inicialba=" . $fechasol_inicialba . "&fechasol_finalbd=" . $fechasol_finalbd . "&fechasol_finalbm=" . $fechasol_finalbm . "&fechasol_finalba=" . $fechasol_finalba . "&fechaent_inicialbd=" . $fechaent_inicialbd . "&fechaent_inicialbm=" . $fechaent_inicialbm . "&fechaent_inicialba=" . $fechaent_inicialba . "&fechaent_finalbd=" . $fechaent_finalbd . "&fechaent_finalbm=" . $fechaent_finalbm . "&fechaent_finalba=" . $fechaent_finalba . "&fechaven_inicialbd=" . $fechaven_inicialbd . "&fechaven_inicialbm=" . $fechaven_inicialbm . "&fechaven_inicialba=" . $fechaven_inicialba . "&fechaven_finalbd=" . $fechaven_finalbd . "&fechaven_finalbm=" . $fechaven_finalbm . "&fechaven_finalba=" . $fechaven_finalba . "&descripcion_busqueda=" . $descripcion_busqueda . "&sectorb=" . $sectorb . "&pagaduriab=" . $pagaduriab . "&id_comercialb=" . $id_comercialb . "&entidadb=" . $entidadb . "&estadocartab=" . $estadocartab . "&id_subestadob=" . $id_subestadob . "&page=" . $siguiente_page . "\">Siguiente</a></p></td></tr>";
		}

		echo "</table><br>";
	}


	$j = 1;
	
	if (sqlsrv_num_rows($rs) > 0){
		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
			$tr_class = "";
			
			if (($j % 2) == 0){
				$tr_class = " style='background-color:#F1F1F1;'";
			}
			
			$estadocarta_nos = "";
			$estadocarta_sol = "";
			$estadocarta_ent = "";
			$estadocarta_con = "";
			$estadocarta_pag = "";
			
			switch($fila["estado"]){
				case "NO SOLICITADA":	$estadocarta_nos = " selected"; break;
				case "SOLICITADA":		$estadocarta_sol = " selected"; break;
				case "ENTREGADA":		$estadocarta_ent = " selected"; break;
				case "CONFIRMADA":		$estadocarta_con = " selected"; break;
				case "PAGADA":			$estadocarta_pag = " selected"; break;
			}
			?>
			<form name="formato3" method="post" action="agenda_actualizar.php">
			<input type="hidden" name="action" value="">
			<input type="hidden" name="id" value="">
			<input type="hidden" name="fechasug_inicialbd" value="<?php echo $fechasug_inicialbd ?>">
			<input type="hidden" name="fechasug_inicialbm" value="<?php echo $fechasug_inicialbm ?>">
			<input type="hidden" name="fechasug_inicialba" value="<?php echo $fechasug_inicialba ?>">
			<input type="hidden" name="fechasug_finalbd" value="<?php echo $fechasug_finalbd ?>">
			<input type="hidden" name="fechasug_finalbm" value="<?php echo $fechasug_finalbm ?>">
			<input type="hidden" name="fechasug_finalba" value="<?php echo $fechasug_finalba ?>">
			<input type="hidden" name="fechasol_inicialbd" value="<?php echo $fechasol_inicialbd ?>">
			<input type="hidden" name="fechasol_inicialbm" value="<?php echo $fechasol_inicialbm ?>">
			<input type="hidden" name="fechasol_inicialba" value="<?php echo $fechasol_inicialba ?>">
			<input type="hidden" name="fechasol_finalbd" value="<?php echo $fechasol_finalbd ?>">
			<input type="hidden" name="fechasol_finalbm" value="<?php echo $fechasol_finalbm ?>">
			<input type="hidden" name="fechasol_finalba" value="<?php echo $fechasol_finalba ?>">
			<input type="hidden" name="fechaent_inicialbd" value="<?php echo $fechaent_inicialbd ?>">
			<input type="hidden" name="fechaent_inicialbm" value="<?php echo $fechaent_inicialbm ?>">
			<input type="hidden" name="fechaent_inicialba" value="<?php echo $fechaent_inicialba ?>">
			<input type="hidden" name="fechaent_finalbd" value="<?php echo $fechaent_finalbd ?>">
			<input type="hidden" name="fechaent_finalbm" value="<?php echo $fechaent_finalbm ?>">
			<input type="hidden" name="fechaent_finalba" value="<?php echo $fechaent_finalba ?>">
			<input type="hidden" name="fechaven_inicialbd" value="<?php echo $fechaven_inicialbd ?>">
			<input type="hidden" name="fechaven_inicialbm" value="<?php echo $fechaven_inicialbm ?>">
			<input type="hidden" name="fechaven_inicialba" value="<?php echo $fechaven_inicialba ?>">
			<input type="hidden" name="fechaven_finalbd" value="<?php echo $fechaven_finalbd ?>">
			<input type="hidden" name="fechaven_finalbm" value="<?php echo $fechaven_finalbm ?>">
			<input type="hidden" name="fechaven_finalba" value="<?php echo $fechaven_finalba ?>">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
			<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $id_comercialb ?>">
			<input type="hidden" name="entidadb" value="<?php echo $entidadb ?>">
			<input type="hidden" name="estadocartab" value="<?php echo $estadocartab ?>">
			<input type="hidden" name="id_subestadob" value="<?php echo $id_subestadob ?>">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
			
			<table border="0" cellspacing=1 cellpadding=2 class="tab3">
				<tr>
					<th>Cedula</th>
					<th>Nombre</th>
					<th>Pagaduria</th>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Comercial</th><?php } ?>
					<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><th>Etapa</th><?php } ?>
					<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><th>Subestado</th><?php } ?>
					<th>Entidad</th>
					<th>Oficina</th>
					<th>Unidad Negocio</th>
					<th>Estado Carta</th>
					<!--<th>F Sugerida</th>
					<th>F Solicitada</th>-->
					<th>F Entrega</th>
					<th>F Vencimiento</th>
					<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><th>Modificar</th><?php } ?>
				</tr>
				<?php
				$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				$j = 1;
				
				while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
					
					$tr_class = "";

					if (($j % 2) == 0) {
						$tr_class = " style='background-color:#F1F1F1;'";
					}

					$estadocarta_nos = "";
					$estadocarta_sol = "";
					$estadocarta_ent = "";
					$estadocarta_con = "";
					$estadocarta_pag = "";

					switch ($fila["estado"]) {
						case "NO SOLICITADA":
							$estadocarta_nos = " selected";
							break;
						case "SOLICITADA":
							$estadocarta_sol = " selected";
							break;
						case "ENTREGADA":
							$estadocarta_ent = " selected";
							break;
						case "CONFIRMADA":
							$estadocarta_con = " selected";
							break;
						case "PAGADA":
							$estadocarta_pag = " selected";
							break;
					}
					?>

					<tr <?php echo $tr_class ?>>
						<input type="hidden" id="dias_entrega<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="dias_entrega<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["dias_entrega"] ?>">
						<input type="hidden" id="dias_vigencia<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="dias_vigencia<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["dias_vigencia"] ?>">
						<input type="hidden" id="fecha_sugerida<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_sugerida<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_sugerida"] ?>">
						<td><?php echo $fila["cedula"] ?></td>
						<td><?php echo utf8_decode($fila["nombre"]) ?></td>
						<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
						<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo utf8_decode($fila["nombre_comercial"] . " " . $fila["apellido"]) ?></td><?php } ?>
						<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><td><?php echo utf8_decode($fila["nombre_etapa"]) ?></td><?php } ?>
						<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><td><?php echo utf8_decode($fila["nombre_subestado"]) ?></td><?php } ?>
						<td><?php echo utf8_decode($fila["entidad"]) ?></td>
						<td><?php echo utf8_decode($fila["nombre_oficina"]) ?></td>
						<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
						<td align="center">
							<select id="estadocarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="estadocarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" style="background-color:#EAF1DD;" !onChange="recalcular_agenda('<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>', '')">
								<option value="NO SOLICITADA" <?php echo $estadocarta_nos ?>>NO SOLICITADA</option>
								<option value="SOLICITADA" <?php echo $estadocarta_sol ?>>SOLICITADA</option>
								<option value="ENTREGADA" <?php echo $estadocarta_ent ?>>ENTREGADA</option>
								<option value="CONFIRMADA" <?php echo $estadocarta_con ?>>CONFIRMADA</option>
								<option value="PAGADA" <?php echo $estadocarta_pag ?>>PAGADA</option>
							</select>
						</td>
						<!--<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_sugerida"] ?></td>
						<td align="center"><input type="text" id="fecha_solicitudcarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_solicitudcarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_solicitud"] ?>" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fila["fecha_solicitud"] ?>'; return false} else { recalcular_agenda('<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>', 'FS'); }"></td>-->
						<input type="hidden" id="fecha_solicitudcarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_solicitudcarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_solicitud"] ?>">
						<td align="center"><input type="text" id="fecha_entrega<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_entrega<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_entrega"] ?>" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if (this.value != '') { if(validarfecha(this.value)==false) {this.value='<?php echo $fila["fecha_entrega"] ?>'; return false} else { recalcular_agenda('<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>', 'FE'); } }"></td>
						<!--<td align="center"><input type="text" id="fecha_vencimiento<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_vencimiento<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_vencimiento"] ?>" size="10" style="text-align:center;" readonly></td>-->
						<td align="center"><input type="text" id="fecha_vencimiento<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" name="fecha_vencimiento<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>" value="<?php echo $fila["fecha_vencimiento"] ?>" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if (this.value != '') { if(validarfecha(this.value)==false) {this.value='<?php echo $fila["fecha_vencimiento"] ?>'; return false} }"></td>
						<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>'; modificar(document.formato3.estadocarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>, document.formato3.fecha_solicitudcarta<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>, document.formato3.fecha_entrega<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>, document.formato3.fecha_vencimiento<?php echo $fila["id_simulacion"] . "_" . $fila["consecutivo"] ?>)"></td><?php } ?>
					</tr>
					<?php

					$j++;
				} ?>
			</table>
			</form>
			<?php
		}
	}
} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

include("bottom.php");
?>