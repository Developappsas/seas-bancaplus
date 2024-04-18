<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
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
		window.open('reporte_reqexcep2.php?cedula='+document.formato.cedula.value+'&reqexcep='+document.formato.reqexcep.options[document.formato.reqexcep.selectedIndex].value+'&id_tipo='+document.formato.id_tipo.options[document.formato.id_tipo.selectedIndex].value+'&id_area='+document.formato.id_area.options[document.formato.id_area.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value+'&fecha_inicialbd='+fecha_inicialbd.options[fecha_inicialbd.selectedIndex].value+'&fecha_inicialbm='+fecha_inicialbm.options[fecha_inicialbm.selectedIndex].value+'&fecha_inicialba='+fecha_inicialba.options[fecha_inicialba.selectedIndex].value+'&fecha_finalbd='+fecha_finalbd.options[fecha_finalbd.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value,'REQEXCEPFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}

function valor_tipos(x){return x.substring(0,x.indexOf('-'))}

function texto_tipos(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargartipos(reqexcep, objeto_tipos) {
	var num_tipos;
	var j, k = 1;

	num_tipos = 200;

	objeto_tipos.length = num_tipos;
<?php

$queryDB = "SELECT id_tipo, nombre from tipos_reqexcep where reqexcep = 'REQUERIMIENTO'";

$queryDB .= " order by nombre";

$datos_tipos_requerimiento = sqlsrv_query($link, $queryDB);

$padre_hija = "PHREQUERIMIENTO = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_requerimiento))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

$queryDB = "SELECT id_tipo, nombre from tipos_reqexcep where reqexcep = 'EXCEPCION'";

$queryDB .= " order by nombre";

$datos_tipos_excepcion = sqlsrv_query($link, $queryDB);

$padre_hija = "PHEXCEPCION = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_excepcion))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

?>
	switch(reqexcep) {
		case 'REQUERIMIENTO':
			num_tipos = PHREQUERIMIENTO.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHREQUERIMIENTO[j]);
				objeto_tipos.options[k].text = texto_tipos(PHREQUERIMIENTO[j]);
				k++;
			}
			break;

		case 'EXCEPCION':
			num_tipos = PHEXCEPCION.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHEXCEPCION[j]);
				objeto_tipos.options[k].text = texto_tipos(PHEXCEPCION[j]);
				k++;
			}
			break;

		default:
			num_tipos = 1;
			k=0;
	}

	objeto_tipos.selectedIndex = 0;
	objeto_tipos.length = num_tipos;

	return true;
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Requerimientos/Excepciones</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_reqexcep2.php">
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
	<td align="right">Req/Excep</td><td>
		<select name="reqexcep" onChange="Cargartipos(this.value, document.formato.id_tipo);">
			<option value=""></option>
			<option value="REQUERIMIENTO">REQUERIMIENTO</option>
			<option value="EXCEPCION">EXCEPCION</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Tipo</td><td>
		<select name="id_tipo">
			<option value=""></option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">&Aacute;rea</td><td>
		<select name="id_area">
			<option value=""></option>
<?php

$queryDB = "SELECT id_area, nombre from areas_reqexcep order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_area"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Estado</td><td>
		<select name="estado">
			<option value=""></option>
			<option value="PENDIENTE">PENDIENTE</option>
			<option value="RESPONDIDO">RESPONDIDO</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">F. Inicial</td><td>
		<input type="hidden" name="fecha_inicialb" size="10" maxlength="10">
		<select name="fecha_inicialbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
	echo "<option value=\"".$i."\">".$i."</option>";
}

?>
		</select>
		<a href="javascript:show_calendar('formato.fecha_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Final</td><td>
		<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
		<select name="fecha_finalbd">
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

for ($i = 2014; $i <= date("Y"); $i++)
{
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
<input type="button" value="Consultar"  onClick="chequeo_forma()"/>
</p>
</form>
<?php include("bottom.php"); ?>
