<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
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
		window.open('reporte_visado2.php?cedula='+document.formato.cedula.value+'&tipo_visado='+document.formato.tipo_visado.options[document.formato.tipo_visado.selectedIndex].value+'&id_visador='+document.formato.id_visador.options[document.formato.id_visador.selectedIndex].value,'VIFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Visado</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_visado2.php">
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
	<td align="right">Tipo Visado</td><td>
		<select name="tipo_visado">
			<option value=""></option>
			<option value="OFICIAL">OFICIAL</option>
			<option value="EXTERNO">EXTERNO</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Visador</td><td>
		<select name="id_visador">
			<option value=""></option>
<?php

$queryDB = "SELECT id_visador, nombre from visadores order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_visador"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
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
