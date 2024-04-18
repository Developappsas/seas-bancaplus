<?php include ('../functions.php'); ?>
<?php
// 2016-12-12 SEBASTIAN SOLICITO INCLUIR ESTE REPORTE PARA WILLIAM ORJUELA 
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	// with (document.formato) {
	// 	window.open('reporte_desembolsos2.php?cedula='+document.formato.cedula.value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?>+'&fechades_inicialbd='+fechades_inicialbd.options[fechades_inicialbd.selectedIndex].value+'&fechades_inicialbm='+fechades_inicialbm.options[fechades_inicialbm.selectedIndex].value+'&fechades_inicialba='+fechades_inicialba.options[fechades_inicialba.selectedIndex].value+'&fechades_finalbd='+fechades_finalbd.options[fechades_finalbd.selectedIndex].value+'&fechades_finalbm='+fechades_finalbm.options[fechades_finalbm.selectedIndex].value+'&fechades_finalba='+fechades_finalba.options[fechades_finalba.selectedIndex].value<?php } ?>+'&fechacartera_inicialbm='+fechacartera_inicialbm.options[fechacartera_inicialbm.selectedIndex].value+'&fechacartera_inicialba='+fechacartera_inicialba.options[fechacartera_inicialba.selectedIndex].value+'&fechacartera_finalbm='+fechacartera_finalbm.options[fechacartera_finalbm.selectedIndex].value+'&fechacartera_finalba='+fechacartera_finalba.options[fechacartera_finalba.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value,'DESFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	// }
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Desembolsos</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_desembolsos2.php">
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

$queryDB = "SELECT nombre as pagaduria from pagadurias where 1 = 1";

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
<?php

if ($_SESSION["FUNC_FDESEMBOLSO"])
{

?>
<tr>
	<td align="right">F. Desembolso Inicial</td><td>
		<input type="hidden" name="fechades_inicialb" size="10" maxlength="10">
		<select name="fechades_inicialbd">
			<option value="">D&iacute;a</option>
<?php

	for ($i = 1; $i <= 31; $i++)
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
	}
	
?>
		</select>
		<select name="fechades_inicialbm">
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
		<select name="fechades_inicialba">
			<option value="">A&ntilde;o</option>
<?php

	for ($i = 2014; $i <= date("Y"); $i++)
	{
		echo "<option value=\"".$i."\">".$i."</option>";
	}
	
?>
		</select>
		<a href="javascript:show_calendar('formato.fechades_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Desembolso Final</td><td>
		<input type="hidden" name="fechades_finalb" size="10" maxlength="10">
		<select name="fechades_finalbd">
			<option value="">D&iacute;a</option>
<?php

	for ($i = 1; $i <= 31; $i++)
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
	}
	
?>
		</select>
		<select name="fechades_finalbm">
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
		<select name="fechades_finalba">
			<option value="">A&ntilde;o</option>
<?php

	for ($i = 2014; $i <= date("Y"); $i++)
	{
		echo "<option value=\"".$i."\">".$i."</option>";
	}
	
?>
		</select>
		<a href="javascript:show_calendar('formato.fechades_finalb');"><img src="../images/calendario.gif" border=0></a>
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
<tr>
	<td align="right">Estado</td><td>
		<select name="estado">
			<option value=""></option>
			<option value="ABI">ABIERTO</option>
			<option value="PAR">PARCIAL</option>
			<option value="CER">CERRADO</option>
		</select>
	</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<p align="center">
<input type="hidden" name="user" value="seas_reporte">
<input type="hidden" name="password" value="S3a5k3d1t.2023*">
<input type="hidden" name="S_TIPO" value="<?=$_SESSION['S_TIPO']?>">
<input type="hidden" name="S_SUBTIPO" value="<?=$_SESSION['S_SUBTIPO']?>">
<input type="hidden" name="S_IDUNIDADNEGOCIO" value="<?=$_SESSION['S_IDUNIDADNEGOCIO']?>">
<input type="hidden" name="S_IDUSUARIO" value="<?=$_SESSION['S_IDUSUARIO']?>"> 
<input type="button" value="Consultar"  onClick="chequeo_forma()"/>
</p>
</form>
<?php include("bottom.php"); ?>
