<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR" ){
	exit;
}

$link = conectar();
include("top.php"); ?>

<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Marketing</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post >
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=0>
<tr>
	<td valing="buttom">C&eacute;dula
		<input id="cedula" type="text" name="cedula">
	</td>
</tr>
<tr>
	<td valing="buttom">Pagadur&iacute;a
		<select id="pagaduria" name="pagaduria" style="width:160px">
			<option value="0"></option>
<?php

	$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1 order by pagaduria";
	$rs1 = sqlsrv_query($link, $queryDB);
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
	}

?>
		</select>
	</td>
</tr>
<tr>
	<td valing="buttom">Ciudad
		<select id="ciudad"name="ciudad" style="width:160px">
			<option value="0"></option>
<?php

$queryDB = "select DISTINCT emp.ciudad from ".$prefijo_tablas."empleados emp INNER JOIN pagadurias pa ON emp.pagaduria = pa.nombre LEFT JOIN simulaciones si ON emp.cedula = si.cedula AND emp.pagaduria = si.pagaduria where emp.ciudad IS NOT NULL AND emp.ciudad <> '' order by emp.ciudad";

$rs1 = sqlsrv_query($link, $queryDB);
while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
	echo "<option value=\"".$fila1["ciudad"]."\">".stripslashes(utf8_decode($fila1["ciudad"]))."</option>\n";
}

?>
		</select>
	</td>
	
</tr>
<tr>
<td valing="buttom">Estado
<select id="estado" name="estadob" style="width:160px">
		<option value="0"></option>
		<option value="ING">INGRESADO</option>
		<option value="EST">EN ESTUDIO</option>
		<option value="NEG">NEGADO</option>
		<option value="DST">DESISTIDO</option>
		<option value="DES">DESEMBOLSADO</option>
		<option value="CAN">CANCELADO</option>
		<option value="ANU">ANULADO</option>
	</select>&nbsp;
</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<p align="center">
</p>
</form>
<input id="prueba" type="button" value="Consultar">
<?php include("bottom.php"); ?>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sheetjs/xlsx.mini.min.js"></script>
<script type="text/javascript" src="../js/marketing/reporte_marketing.js"></script>
