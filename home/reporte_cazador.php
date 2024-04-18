<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "TESORERIA")
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	

	with (document.formato) {
		if (resumidob.checked == true) {
			resumido = 1;
		}
		else {
			resumido = 0;
		}

		window.open('reporte_cazador2.php?id_comercial='+document.formato.id_comercial.options[document.formato.id_comercial.selectedIndex].value+'&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&ciudad='+document.formato.ciudad.options[document.formato.ciudad.selectedIndex].value+'&oficinas='+document.formato.oficinas.options[document.formato.oficinas.selectedIndex].value+'&resumidob='+resumidob,'SIMFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte Cazador</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reporte_cazador.php">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="right">Comercial</td><td>
		<select name="id_comercial">
			<option value=""></option>
<?php

	$queryDB = "SELECT distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial where si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."') AND us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";
	
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
	
	if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION")
	{
		$queryDB .= " UNION select id_usuario, nombre, apellido from usuarios where tipo <> 'MASTER' AND tipo = 'COMERCIAL'";
	}
	
	$queryDB .= " order by nombre, apellido, id_usuario";
	
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
	<td align="right">Pagadur&iacute;a</td><td>
		<select name="pagaduria">
			<option value=""></option>
<?php

$queryDB = "SELECT DISTINCT pagaduria from ".$prefijo_tablas."empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

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
		<select name="ciudad">
			<option value=""></option>
<?php

$queryDB = "SELECT DISTINCT ciudad from ".$prefijo_tablas."empleados where ciudad IS NOT NULL AND ciudad <> '' order by ciudad";

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
 <td align="right">Oficinas</td><td>
 	<select name="oficinas">
		<option value=""></option>

		 <?php
        	
        	$queryDB = " SELECT * from oficinas order by nombre";

        	$rs = sqlsrv_query($link, $queryDB);

        	while ($fila1 = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))

        	{
        		echo "<option value=\"".$fila1["nombre"]."\">".stripslashes(utf8_decode($fila1["nombre"]))."</option>\n";
        	}

		 ?> 		


 	</select>
 </td>
</tr>

<tr>
	<td align="right">Totales</td><td>
		<input type="checkbox" name="resumidob">
	</td>
</tr>


</table>
<p align="center">
<input type="button" value="Consultar" onClick="chequeo_forma()"/>
</p>
</form>
<?php include("bottom.php"); ?>
