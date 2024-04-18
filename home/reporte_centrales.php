<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
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
		window.open('reporte_centrales2.php?central='+document.formato.central.options[document.formato.central.selectedIndex].value+'&tipo='+document.formato.tipo.options[document.formato.tipo.selectedIndex].value+'&fechacartera_inicialbm='+fechacartera_inicialbm.options[fechacartera_inicialbm.selectedIndex].value+'&fechacartera_inicialba='+fechacartera_inicialba.options[fechacartera_inicialba.selectedIndex].value+'&fechacartera_finalbm='+fechacartera_finalbm.options[fechacartera_finalbm.selectedIndex].value+'&fechacartera_finalba='+fechacartera_finalba.options[fechacartera_finalba.selectedIndex].value,'CENTRALESFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Centrales</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_centrales2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="right">Central de Riesgo</td><td>
		<select name="central">
			<option value="DATACREDITO">DataCr&eacute;dito</option>
			<option value="CIFIN">CIFIN</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Tipo Cartera</td><td>
		<select name="tipo">
			<option value="ORI">ORIGINACI&Oacute;N</option>
			<option value="EXT">EXTERNA</option>
			<option value="ALL">TODA</option>
		</select>
	</td>
</tr>


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

for ($i = 2014; $i <= date("Y"); $i++)
{
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

for ($i = 2014; $i <= date("Y"); $i++)
{
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
