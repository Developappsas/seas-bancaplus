<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
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
		window.open('reporte_giroscliente2.php?cedula='+document.formato.cedula.value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value,'GCFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Giros al Cliente</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_giroscliente2.php">
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
