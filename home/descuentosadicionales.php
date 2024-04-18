<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((pagaduria.value == "") || (nombre.value == "") || (porcentaje.value == "")) {
			alert("Todos los campos son obligatorios");
			return false;
		}
	}
}
function modificar(campon, campop) {
	with (document.formato3) {
		if (campon.value == "" || campop.value == "") {
			alert("Debe digitar nombre y porcentaje");
			return false;
		}
		else {
			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Descuentos Adicionales</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="descuentosadicionales_crear.php" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Pagadur&iacute;a<br>
		<select name="pagaduria" style="width:155px">
			<option value=""></option>
<?php

$queryDB = "select nombre as pagaduria from pagadurias where estado = '1' order by pagaduria";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
		</select>&nbsp;
	</td>
	<td valign="bottom">Nombre Descuento<br><input type="text" name="nombre" maxlength="20" size="20" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">Porcentaje<br><input type="text" name="porcentaje" maxlength="20" size="10" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Descuento"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="descuentosadicionales.php">
<table>
<tr>
<td>
<div class="box1 oran clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Pagadur&iacute;a/Nombre Descuento<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "select * from descuentos_adicionales where id_descuento IS NOT NULL";

$queryDB_count = "select COUNT(*) as c from descuentos_adicionales where id_descuento IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB = $queryDB." AND (UPPER(pagaduria) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	
	$queryDB_count = $queryDB_count." AND (UPPER(pagaduria) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
}

$queryDB .= " order by pagaduria, id_descuento DESC OFFSET ".$offset." ROWS";

$rs = sqlsrv_query($link, $queryDB);

$rs_count = sqlsrv_query($link, $queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if ($cuantos)
{
	if ($cuantos > $x_en_x)
	{
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
		
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos)
		{
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos)
			{
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"])
				{
				echo " <a href=\"descuentosadicionales.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
			}
			else
			{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page)
		{
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"descuentosadicionales.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="descuentosadicionales_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Pagadur&iacute;a</th>
	<th>Nombre Descuento</th>
	<th>Porcentaje</th>
	<th>Activo</th>
	<th>Modificar</th>
	<th>Borrar</th>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
?>
<tr <?php echo $tr_class ?>>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><input type="text" name="nombre<?php echo $fila["id_descuento"] ?>" value="<?php echo $fila["nombre"] ?>" maxlength="20" size="20" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="porcentaje<?php echo $fila["id_descuento"] ?>" value="<?php echo $fila["porcentaje"] ?>" maxlength="20" size="10" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
	<td align="center"><input type="checkbox" name="a<?php echo $fila["id_descuento"] ?>" value="1"<?php if ($fila["estado"]) { echo " checked"; } ?>></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_descuento"] ?>'; modificar(document.formato3.nombre<?php echo $fila["id_descuento"] ?>, document.formato3.porcentaje<?php echo $fila["id_descuento"] ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo $fila["id_descuento"] ?>" value="1"></td>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value='borrar'"></p>
</form>
<?php

}

?>
<?php include("bottom.php"); ?>
