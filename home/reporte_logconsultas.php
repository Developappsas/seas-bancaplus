<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD") || !$_SESSION["FUNC_LOGCONSULTAS"])
{
	exit;
}

$link = conectar();

$todas_las_unidades = "'0'";

$rs1 = sqlsrv_query($link, "select id_unidad from unidades_negocio order by id_unidad");

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	$todas_las_unidades .= ", '".$fila1["id_unidad"]."'";

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		window.open('reporte_logconsultas2.php?id_usuario='+document.formato.id_usuario.options[document.formato.id_usuario.selectedIndex].value+'&id_oficina='+document.formato.id_oficina.options[document.formato.id_oficina.selectedIndex].value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&ciudad='+document.formato.ciudad.options[document.formato.ciudad.selectedIndex].value+'&institucion='+document.formato.institucion.options[document.formato.institucion.selectedIndex].value+'&fecha_inicialbd='+fecha_inicialbd.options[fecha_inicialbd.selectedIndex].value+'&fecha_inicialbm='+fecha_inicialbm.options[fecha_inicialbm.selectedIndex].value+'&fecha_inicialba='+fecha_inicialba.options[fecha_inicialba.selectedIndex].value+'&fecha_finalbd='+fecha_finalbd.options[fecha_finalbd.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value,'LCONSFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Consultas Realizadas</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_logconsultas2.php">
<table cellpadding="8">
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=8 cellpadding=2>
<tr>
	<td align="right">Usuario</td><td>
		<select name="id_usuario" style="width:180px">
			<option value=""></option>
<?php

$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us left join simulaciones si on us.id_usuario = si.id_comercial where us.tipo <> 'MASTER'";

$queryDB .= " AND (si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR si.id_unidad_negocio IS NULL";

$queryDB .= ")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND us.tipo = 'COMERCIAL' AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

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
	<td align="right">Oficina</td><td>
		<select name="id_oficina" style="width:180px">
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
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
<tr>
	<td align="right">Sector</td><td>
		<select name="sector" style="width:180px">
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
		<select name="pagaduria" style="width:180px">
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
	<td align="right">Ciudad</td><td>
		<select name="ciudad" style="width:180px">
			<option value=""></option>
<?php

$queryDB = "select DISTINCT emp.ciudad from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.ciudad IS NOT NULL AND emp.ciudad <> ''";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND (si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR si.id_unidad_negocio IS NULL";

$queryDB .= ") order by emp.ciudad";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["ciudad"]."\">".stripslashes(utf8_decode($fila1["ciudad"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Instituci&oacute;n</td><td>
		<select name="institucion" style="width:180px">
			<option value=""></option>
<?php

$queryDB = "select DISTINCT emp.institucion from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.institucion IS NOT NULL AND emp.institucion <> ''";

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
