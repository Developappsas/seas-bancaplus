<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"]){
	exit;
}

$link = conectar();
?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<script language="JavaScript">

	function chequeo_forma() {
		window.open('reporte_cuentas_contabilidad2.php?cedula='+document.formato.cedula.value+'&id_simulacion='+document.formato.id_simulacion.value+'&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?>+'&id_subestado='+document.formato.id_subestado.options[document.formato.id_subestado.selectedIndex].value<?php } ?>+'&fechacartera_inicialbm='+document.formato.fechacartera_inicialbm.options[document.formato.fechacartera_inicialbm.selectedIndex].value+'&fechacartera_inicialba='+document.formato.fechacartera_inicialba.options[document.formato.fechacartera_inicialba.selectedIndex].value+'&fechacartera_finalbm='+document.formato.fechacartera_finalbm.options[document.formato.fechacartera_finalbm.selectedIndex].value+'&fechacartera_finalba='+document.formato.fechacartera_finalba.options[document.formato.fechacartera_finalba.selectedIndex].value);
	}
</script>

<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo"><center><b>Reporte Contabilidad</b><br><br></center></td>
	</tr>
</table>

<form name=formato method=post action="reporte_contabilidad2.php">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>

						<tr>
							<td align="right">C&eacute;dula/Nombre/No. Libranza</td><td>
								<input type="text" name="cedula">
							</td>
						</tr>

						<tr>
							<td align="right">Id Simulacion</td><td>
								<input type="text" name="id_simulacion">
							</td>
						</tr>

						<tr>
							<td align="right">Pagadur&iacute;a</td><td>
								<select name="pagaduria" style="width:160px">
									<option value=""></option>
									<?php

									$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";

									$queryDB .= " order by pagaduria";

									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
										echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
									}

									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Estado</td><td>
								<select name="estado" style="width:160px">
									<option value=""></option>
									<option value="ING">INGRESADO</option>
									<option value="EST">EN ESTUDIO</option>
									<option value="NEG">NEGADO</option>
									<option value="DST">DESISTIDO</option>
									<!--<option value="DSS">DESISTIDO SISTEMA</option>-->
									<option value="DES">DESEMBOLSADO</option>
									<option value="CAN">CANCELADO</option>
									<option value="ANU">ANULADO</option>
								</select>
							</td>
						</tr>

						<?php

						if ($_SESSION["FUNC_SUBESTADOS"]){ ?>
							<tr>
								<td align="right">Subestado</td><td>
									<select name="id_subestado" style="width:160px">
										<option value=""></option>
										<?php

										$queryDB = "select id_subestado, decision, nombre from subestados where estado = '1' order by decision DESC, nombre";

										$rs1 = sqlsrv_query($link, $queryDB);

										while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
											echo "<option value=\"".$fila1["id_subestado"]."\">".substr($fila1["decision"], 0, 3)."-".utf8_decode($fila1["nombre"])."</option>\n";
										}

										?>
									</select>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td align="right">Mes Prod Inicial</td><td>
								<select name="fechacartera_inicialbm">
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
								<select name="fechacartera_inicialba">
									<option value="">A&ntilde;o</option>
									<?php
									for ($i = 2014; $i <= date("Y"); $i++){
										echo "<option value=\"".$i."\">".$i."</option>";
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Mes Prod Final</td><td>
								<select name="fechacartera_finalbm">
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
								<select name="fechacartera_finalba">
									<option value="">A&ntilde;o</option>
									<?php
									for ($i = 2014; $i <= date("Y"); $i++) {
										echo "<option value=\"".$i."\">".$i."</option>";
									}
									?>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Consultar"  onClick="chequeo_forma()"/>
	</p>
</form>
<?php include("bottom.php"); ?>