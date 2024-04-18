<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		// window.open('reporte_comprascartera2.php?cedula='+document.formato.cedula.value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&pys='+document.formato.estado.options[document.formato.pys.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value+'&fecha_finalbd='+fecha_finalbd.options[fecha_finalbd.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value,'CCFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Compras Cartera</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_comprascartera2.php">
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
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
<tr>
	<td align="right">Sector</td><td>
		<select name="sector">
			<option value=""></option>
			<option value="PUBLICO">PUBLICO</option>
			<option value="PRIVADO">PRIVADO</option>
		</select>
	</td>
</tr>
<?php

}

?>
<tr>
	<td align="right">Pagadur&iacute;a</td><td>
		<select name="pagaduria">
			<option value=""></option>
<?php

$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Paz y Salvo</td><td>
		<select name="pys">
			<option value=""></option>
			<option value="SI">SI</option>
			<option value="NO">NO</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Pagada</td><td>
		<select name="estado">
			<option value=""></option>
			<option value="SI">SI</option>
			<option value="NO">NO</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">F. Inicio Corte</td><td>
								<input type="hidden" name="fecha_inicialb" size="10" maxlength="10">
								<select name="fecha_inicialbd">
									<option value="">D&iacute;a</option>
									<?php
									for ($i = 1; $i <= 31; $i++){
										
										if (strlen($i) == 1){
											$j = "0".$i;
										}
										else{
											$j = $i;
										}

										echo "<option value=\"".$j."\">".$j."</option>";
									}
									?>
								</select>
								<select name="fecha_inicialbm">
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
								<select name="fecha_inicialba">
									<option value="">A&ntilde;o</option>
									<?php
									for ($i = 2014; $i <= date("Y"); $i++){
										echo "<option value=\"".$i."\">".$i."</option>";
									}
									?>
								</select>
								<a href="javascript:show_calendar('formato.fecha_inicialb');"><img src="../images/calendario.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="right">F. Fin Corte</td><td>
								<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
								<select name="fecha_finalbd">
									<option value="">D&iacute;a</option>
									<?php
									for ($i = 1; $i <= 31; $i++){
										
										if (strlen($i) == 1){
											$j = "0".$i;
										}
										else{
											$j = $i;
										}

										echo "<option value=\"".$j."\">".$j."</option>";
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
									for ($i = 2014; $i <= date("Y"); $i++){
										echo "<option value=\"".$i."\">".$i."</option>";
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
<?php
		if(isset($_SESSION['S_SECTOR'])){ ?>
			<input type="hidden" name="sector" value="<?=$_SESSION["S_SECTOR"]?>"> <?php
		} ?>
		<!-- Variables de session enviadas -->
		<input type="hidden" name="S_IDUNIDADNEGOCIO" value="<?=$_SESSION["S_IDUNIDADNEGOCIO"]?>">
		<input type="hidden" name="user" value="seas_reporte">
		<input type="hidden" name="password" value="S3a5k3d1t.2023*">

		<input type="submit" value="Consultar"  onClick="chequeo_forma()"/>
	</p>
</form>
<?php include("bottom.php"); ?>
