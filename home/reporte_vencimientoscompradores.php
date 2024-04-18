<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
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
window.open('reporte_vencimientoscompradores2.php?id_comprador='+document.formato.id_comprador.options[document.formato.id_comprador.selectedIndex].value+'&nro_venta='+document.formato.nro_venta.value+'&tipo='+document.formato.tipo.options[document.formato.tipo.selectedIndex].value+'&fechavcto_inicialbm='+fechavcto_inicialbm.options[fechavcto_inicialbm.selectedIndex].value+'&fechavcto_inicialba='+fechavcto_inicialba.options[fechavcto_inicialba.selectedIndex].value+'&fechavcto_finalbm='+fechavcto_finalbm.options[fechavcto_finalbm.selectedIndex].value+'&fechavcto_finalba='+fechavcto_finalba.options[fechavcto_finalba.selectedIndex].value+'&fechacartera_inicialbm='+fechacartera_inicialbm.options[fechacartera_inicialbm.selectedIndex].value+'&fechacartera_inicialba='+fechacartera_inicialba.options[fechacartera_inicialba.selectedIndex].value+'&fechacartera_finalbm='+fechacartera_finalbm.options[fechacartera_finalbm.selectedIndex].value+'&fechacartera_finalba='+fechacartera_finalba.options[fechacartera_finalba.selectedIndex].value,'VCTOCOMPFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Vencimientos Compradores</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_vencimientoscompradores2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="right">Comprador</td><td>
		<select name="id_comprador" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "select id_comprador, nombre from compradores WHERE id_comprador IS NOT NULL";

$queryDB .= " order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_comprador"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">No. Venta</td><td>
		<input type="text" name="nro_venta">
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
	<td align="right">Mes Vencimiento Inicial</td><td>
		<select name="fechavcto_inicialbm">
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
		<select name="fechavcto_inicialba">
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
	<td align="right">Mes Vencimiento Final</td><td>
		<select name="fechavcto_finalbm">
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
		<select name="fechavcto_finalba">
			<option value="">A&ntilde;o</option>
<?php

for ($i = 2014; $i <= date("Y") + 10; $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
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
