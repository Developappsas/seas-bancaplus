<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || 
	($_SESSION["S_TIPO"] == "ANALISTA"  || $_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA" 
		&& $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OFICINA"
		&& $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" 
		&& $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION['S_REPORTE_CARTERA']!=1 ))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<script language="JavaScript">

function chequeo_forma() {
	var embargo = "";

	with (document.formato) {
		if (embargo_actual[0].checked == true) {
			embargo = embargo_actual[0].value;
		}
		else if (embargo_actual[1].checked == true) {
			embargo = embargo_actual[1].value;
		}

		if (solo_produccionb.checked == true) {
			solo_produccion = 1;
		}
		else {
			solo_produccion = 0;
		}

<?php

if ($_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION")
{

?>
		if (resumidob.checked == true) {
			resumido = 1;
		}
		else {
			resumido = 0;
		}
<?php

}

?>
		window.open('reporte_simulaciones2.php?<?php if (!$_SESSION["S_SECTOR"]) { ?>sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'&<?php } ?>pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&id_oficina='+document.formato.id_oficina.options[document.formato.id_oficina.selectedIndex].value+'&institucion='+document.formato.institucion.options[document.formato.institucion.selectedIndex].value+'&edadd='+document.formato.edadd.value+'&edadh='+document.formato.edadh.value+'&salario_basicod='+document.formato.salario_basicod.value+'&salario_basicoh='+document.formato.salario_basicoh.value+'&embargo_actual='+embargo+'&nivel_educativo='+document.formato.nivel_educativo.options[document.formato.nivel_educativo.selectedIndex].value+'&id_comercial='+document.formato.id_comercial.options[document.formato.id_comercial.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value+'&decision='+document.formato.decision.options[document.formato.decision.selectedIndex].value<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?>+'&id_subestado='+document.formato.id_subestado.options[document.formato.id_subestado.selectedIndex].value<?php } ?><?php if ($_SESSION["FUNC_CALIFICACION"]) { ?>+'&calificacion='+document.formato.calificacion.options[document.formato.calificacion.selectedIndex].value<?php } ?>+'&solo_produccionb='+solo_produccion+'&fecha_inicialbd='+fecha_inicialbd.options[fecha_inicialbd.selectedIndex].value+'&fecha_inicialbm='+fecha_inicialbm.options[fecha_inicialbm.selectedIndex].value+'&fecha_inicialba='+fecha_inicialba.options[fecha_inicialba.selectedIndex].value+'&fecha_finalbd='+fecha_finalbd.options[fecha_finalbd.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?>+'&fechades_inicialbd='+fechades_inicialbd.options[fechades_inicialbd.selectedIndex].value+'&fechades_inicialbm='+fechades_inicialbm.options[fechades_inicialbm.selectedIndex].value+'&fechades_inicialba='+fechades_inicialba.options[fechades_inicialba.selectedIndex].value+'&fechades_finalbd='+fechades_finalbd.options[fechades_finalbd.selectedIndex].value+'&fechades_finalbm='+fechades_finalbm.options[fechades_finalbm.selectedIndex].value+'&fechades_finalba='+fechades_finalba.options[fechades_finalba.selectedIndex].value<?php } ?>+'&fechacartera_inicialbm='+fechacartera_inicialbm.options[fechacartera_inicialbm.selectedIndex].value+'&fechacartera_inicialba='+fechacartera_inicialba.options[fechacartera_inicialba.selectedIndex].value+'&fechacartera_finalbm='+fechacartera_finalbm.options[fechacartera_finalbm.selectedIndex].value+'&fechacartera_finalba='+fechacartera_finalba.options[fechacartera_finalba.selectedIndex].value<?php if ($_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION") { ?>+'&resumidob='+resumido<?php } ?>,'SIMFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Simulaciones</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_simulaciones2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
<tr>
	<td align="right">Sector</td><td>
		<select name="sector" style="width:160px">
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
	<td align="right">Entidad</td><td>
		<select name="entidad" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "SELECT id_entidad, nombre from entidades_desembolso order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_entidad"]."\"".$selected_entidad.">".($fila1["nombre"])."</option>\n";
}

?>
		</select>
	</td>
</tr>

<tr>
	<td align="right">Pagadur&iacute;a</td><td>
		<select name="pagaduria" style="width:160px">
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
<tr>
	<td align="right">Oficina</td><td>
		<select name="id_oficina" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "select id_oficina, nombre from oficinas";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL")
	$queryDB .= " where id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

$queryDB .= " order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_oficina"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Instituci&oacute;n</td><td>
		<select name="institucion" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "SELECT DISTINCT emp.institucion from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.institucion IS NOT NULL AND emp.institucion <> ''";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND (si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR si.id_unidad_negocio IS NULL";

$queryDB .= ") order by emp.institucion";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["institucion"]."\">".stripslashes(utf8_decode($fila1["institucion"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Edad Entre</td><td>
		<input type="text" name="edadd" onBlur="if(isnumber(this.value)==false) {this.value=''; return false}"> y <input type="text" name="edadh" onBlur="if(isnumber(this.value)==false) {this.value=''; return false}">
	</td>
</tr>
<tr>
	<td align="right">Salario Entre</td><td>
		<input type="text" name="salario_basicod" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value=''; return false} else { separador_miles(this); }"> y <input type="text" name="salario_basicoh" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value=''; return false} else { separador_miles(this); }">
	</td>
</tr>
<tr>
	<td align="right">Embargado</td><td>
		<input type="radio" name="embargo_actual" value="SI">&nbsp;SI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="embargo_actual" value="NO">&nbsp;NO
	</td>
</tr>
<tr>
	<td align="right">Nivel Educativo</td><td>
		<select name="nivel_educativo" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "SELECT DISTINCT emp.nivel_educativo from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.nivel_educativo IS NOT NULL AND emp.nivel_educativo <> ''";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND (si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR si.id_unidad_negocio IS NULL";

$queryDB .= ") order by emp.nivel_educativo";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["nivel_educativo"]."\">".stripslashes(utf8_decode($fila1["nivel_educativo"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Comercial</td><td>
		<select name="id_comercial" style="width:160px">
			<option value=""></option>
<?php

$queryDB = "SELECT distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial where us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

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
}

$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
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
<tr>
	<td align="right">Decisi&oacute;n</td><td>
		<select name="decision" style="width:160px">
			<option value=""></option>
			<option value="<?php echo $label_viable ?>"><?php echo $label_viable ?></option>
			<option value="<?php echo $label_negado ?>"><?php echo $label_negado ?></option>
		</select>
	</td>
</tr>
<?php

if ($_SESSION["FUNC_SUBESTADOS"])
{

?>
<tr>
	<td align="right">Subestado</td><td>
		<select name="id_subestado" style="width:160px">
			<option value=""></option>
<?php

	$queryDB = "SELECT id_subestado, decision, nombre from subestados where estado = '1' order by decision DESC, nombre";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_subestado"]."\">".substr($fila1["decision"], 0, 3)."-".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
		</select>
	</td>
</tr>
<?php

}

if ($_SESSION["FUNC_CALIFICACION"])
{

?>
<tr>
	<td align="right">Calificaci&oacute;n</td><td>
		<select name="calificacion" style="color:#F58F1F;width:160px">
			<option value=""></option>
			<option value="5">&#9733;&#9733;&#9733;&#9733;&#9733;</option>
			<option value="4">&#9733;&#9733;&#9733;&#9733;</option>
			<option value="3">&#9733;&#9733;&#9733;</option>
			<option value="2">&#9733;&#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</td>
</tr>
<?php

}

?>
<tr>
	<td align="right">S&oacute;lo Produccion</td><td>
		<input type="checkbox" name="solo_produccionb">
	</td>
</tr>
<tr>
	<td align="right">F. Estudio Inicial</td><td>
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
	<td align="right">F. Estudio Final</td><td>
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
<?php

if ($_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION")
{

?>
<tr>
	<td align="right">Resumido</td><td>
		<input type="checkbox" name="resumidob">
	</td>
</tr>
<?php

}

?>
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
