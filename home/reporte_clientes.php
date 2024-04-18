<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && ($_SESSION["S_TIPO"] != "OFICINA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION") && $_SESSION["S_TIPO"] != "OPERACIONES"))
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

		window.open('reporte_clientes2.php?cedula='+document.formato.cedula.value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&ciudad='+document.formato.ciudad.options[document.formato.ciudad.selectedIndex].value+'&institucion='+document.formato.institucion.options[document.formato.institucion.selectedIndex].value+'&edadd='+document.formato.edadd.value+'&edadh='+document.formato.edadh.value+'&salario_basicod='+document.formato.salario_basicod.value+'&salario_basicoh='+document.formato.salario_basicoh.value+'&embargo_actual='+embargo+'&nivel_educativo='+document.formato.nivel_educativo.options[document.formato.nivel_educativo.selectedIndex].value,'CLIFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Clientes</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_clientes2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=0>
<tr>
	<td align="right">C&eacute;dula</td><td>
		<input type="text" name="cedula">
	</td>
</tr>
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
	<td align="right">Pagadur&iacute;a</td><td>
		<select name="pagaduria" style="width:160px">
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
		<select name="ciudad" style="width:160px">
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
		<select name="institucion" style="width:160px">
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

$queryDB = "select DISTINCT emp.nivel_educativo from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.nivel_educativo IS NOT NULL AND emp.nivel_educativo <> ''";

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
