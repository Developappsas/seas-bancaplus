<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "PROSPECCION") || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || !$_SESSION["FUNC_AGENDA"])
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
		window.open('reporte_agenda2.php?entidad='+document.formato.entidad.options[document.formato.entidad.selectedIndex].value+'&estadocarta='+document.formato.estadocarta.options[document.formato.estadocarta.selectedIndex].value+'&fechaent_inicialbd='+fechaent_inicialbd.options[fechaent_inicialbd.selectedIndex].value+'&fechaent_inicialbm='+fechaent_inicialbm.options[fechaent_inicialbm.selectedIndex].value+'&fechaent_inicialba='+fechaent_inicialba.options[fechaent_inicialba.selectedIndex].value+'&fechaent_finalbd='+fechaent_finalbd.options[fechaent_finalbd.selectedIndex].value+'&fechaent_finalbm='+fechaent_finalbm.options[fechaent_finalbm.selectedIndex].value+'&fechaent_finalba='+fechaent_finalba.options[fechaent_finalba.selectedIndex].value+'&fechaven_inicialbd='+fechaven_inicialbd.options[fechaven_inicialbd.selectedIndex].value+'&fechaven_inicialbm='+fechaven_inicialbm.options[fechaven_inicialbm.selectedIndex].value+'&fechaven_inicialba='+fechaven_inicialba.options[fechaven_inicialba.selectedIndex].value+'&fechaven_finalbd='+fechaven_finalbd.options[fechaven_finalbd.selectedIndex].value+'&fechaven_finalbm='+fechaven_finalbm.options[fechaven_finalbm.selectedIndex].value+'&fechaven_finalba='+fechaven_finalba.options[fechaven_finalba.selectedIndex].value,'AGENDAFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Gesti&oacute;n Certificaciones</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_agenda2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td align="right">Entidad</td><td>
		<select name="entidad" style="width:160px">
			<option value=""></option>
<?php

// $queryDB = "SELECT DISTINCT entidad from agenda order by entidad";
$queryDB = "SELECT top 100 entidad from agenda group by entidad order by entidad";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["entidad"]."\">".stripslashes(utf8_decode($fila1["entidad"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Estado Carta</td><td>
		<select name="estadocarta" style="width:160px">
			<option value=""></option>
			<option value="NO SOLICITADA">NO SOLICITADA</option>
			<option value="SOLICITADA">SOLICITADA</option>
			<option value="ENTREGADA">ENTREGADA</option>
			<option value="CONFIRMADA">CONFIRMADA</option>
			<option value="PAGADA">PAGADA</option>
		</select>
	</td>
</tr>
<!--<tr>
	<td align="right">F. Sugerida Inicial</td><td>
		<input type="hidden" name="fechasug_inicialb" size="10" maxlength="10">
		<select name="fechasug_inicialbd">
			<option value="">D&iacute;a</option>-->
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
<!--		</select>
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
			<option value="">A&ntilde;o</option>-->
<?php

/*for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
<!--		</select>
		<a href="javascript:show_calendar('formato.fechasug_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Sugerida Final</td><td>
		<input type="hidden" name="fechasug_finalb" size="10" maxlength="10">
		<select name="fechasug_finalbd">
			<option value="">D&iacute;a</option>-->
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
<!--		</select>
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
			<option value="">A&ntilde;o</option>-->
<?php

/*for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
<!--		</select>
		<a href="javascript:show_calendar('formato.fechasug_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Solicitud Inicial</td><td>
		<input type="hidden" name="fechasol_inicialb" size="10" maxlength="10">
		<select name="fechasol_inicialbd">
			<option value="">D&iacute;a</option>-->
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
<!--		</select>
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
			<option value="">A&ntilde;o</option>-->
<?php

/*for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
<!--		</select>
		<a href="javascript:show_calendar('formato.fechasol_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Solicitud Final</td><td>
		<input type="hidden" name="fechasol_finalb" size="10" maxlength="10">
		<select name="fechasol_finalbd">
			<option value="">D&iacute;a</option>-->
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
<!--		</select>
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
			<option value="">A&ntilde;o</option>-->
<?php

/*for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}*/

?>
<!--		</select>
		<a href="javascript:show_calendar('formato.fechasol_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>-->
<tr>
	<td align="right">F. Entrega Inicial</td><td>
		<input type="hidden" name="fechaent_inicialb" size="10" maxlength="10">
		<select name="fechaent_inicialbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato.fechaent_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Entrega Final</td><td>
		<input type="hidden" name="fechaent_finalb" size="10" maxlength="10">
		<select name="fechaent_finalbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato.fechaent_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Vencimiento Inicial</td><td>
		<input type="hidden" name="fechaven_inicialb" size="10" maxlength="10">
		<select name="fechaven_inicialbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato.fechaven_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Vencimiento Final</td><td>
		<input type="hidden" name="fechaven_finalb" size="10" maxlength="10">
		<select name="fechaven_finalbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato.fechaven_finalb');"><img src="../images/calendario.gif" border=0></a>
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
