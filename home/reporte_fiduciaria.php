<?php include('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			window.open('reporte_fiduciaria2.php?fecha_finalbd=' + fecha_finalbd.options[fecha_finalbd.selectedIndex].value + '&fecha_finalbm=' + fecha_finalbm.options[fecha_finalbm.selectedIndex].value + '&fecha_finalba=' + fecha_finalba.options[fecha_finalba.selectedIndex].value, 'CARFS', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Reporte Fiduciaria</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="reporte_fiduciaria2.php">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
					
					
				
					
						<tr>
					
					
							<td align="right">F. Corte</td>
							<td>
								<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
								<select name="fecha_finalbd">
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
								<select name="fecha_finalbm">
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
								<select name="fecha_finalba">
									<option value="">A&ntilde;o</option>
									<?php

									for ($i = 2014; $i <= date("Y"); $i++) {
										echo "<option value=\"" . $i . "\">" . $i . "</option>";
									}

									?>
								</select>
								<a href="javascript:show_calendar('formato.fecha_finalb');"><img src="../images/calendario.gif" border=0></a>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Consultar" onClick="chequeo_forma()" />
	</p>
</form>
<?php include("bottom.php"); ?>