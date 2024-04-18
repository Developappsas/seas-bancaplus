<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA"))
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
		if ((nombre.value == "" || id_banco.value == "" || tipo_cuenta.value == "" || nro_cuenta.value == "")) {
			alert("Debe digitar un nombre, seleccionar el banco, el tipo de cuenta y digitar el numero de cuenta");
			return false;
		}
		
		ReplaceComilla(nombre)
		ReplaceComilla(nro_cuenta)
	}
}
function modificar(campon) {
	with (document.formato3) {
		if (campon.value == "") {
			alert("Debe digitar un nombre");
			return false;
		}
		else {
			ReplaceComilla(campon)
			
			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Cuentas Bancarias</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="cuentasbancarias_crear.php" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td valign="bottom">Nombre<br><input type="text" name="nombre" maxlength="255" size="20"></td>
	<td valign="bottom">Banco<br>
		<select name="id_banco" style="width:200px">
			<option value=""></option>
<?php

$queryDB = "select id_banco, codigo, nombre from bancos order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>

			</select>&nbsp;
	<td valign="bottom">T.Cuenta<br>
		<select name="tipo_cuenta">
		    <option value=""></option>
			<option value="<?php echo $label_aho ?>"><?php echo $label_aho ?></option>
			<option value="<?php echo $label_cte ?>"><?php echo $label_cte ?></option>
		</select>&nbsp;
	</td>
	<td valign="bottom">No Cuenta<br><input type="text" name="nro_cuenta" maxlength="20" size="15"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Agregar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="cuentasbancarias.php">
<table>
<tr>
<td>
<div class="box1 oran clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Nombre/No Cuenta<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
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

$queryDB = "select cb.* from cuentas_bancarias cb INNER JOIN bancos ba ON cb.id_banco = ba.id_banco where cb.id_cuenta IS NOT NULL";

$queryDB_count = "select COUNT(*) as c from cuentas_bancarias cb INNER JOIN bancos ba ON cb.id_banco = ba.id_banco where cb.id_cuenta IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (UPPER(cb.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(cb.nro_cuenta) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	
	$queryDB_count .= " AND (UPPER(cb.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(cb.nro_cuenta) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
}

$queryDB .= " order by cb.nombre, cb.nro_cuenta DESC OFFSET ".$offset." ROWS";

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
				echo " <a href=\"cuentasbancarias.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"cuentasbancarias.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="cuentasbancarias_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Nombre</th>
	<th>Banco</th>
	<th>Tipo cuenta</th>
	<th>Numero de cuenta</th>
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
	<td><input type="text" name="nombre<?php echo $fila["id_cuenta"] ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?>" maxlength="20" size="20"></td>
    <td><select name="id_banco<?php echo $fila["id_cuenta"] ?>" style="width:200px">
			<option value=""></option>
<?php

		$queryDB = "select id_banco, codigo, nombre from bancos where id_banco IS NOT NULL";
		
		$queryDB .= " AND id_banco = '".$fila["id_banco"]."'";
		
		$queryDB .= "order by nombre";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) 
		{
			$selected = "";
			
			if ($fila1["id_banco"] == $fila["id_banco"])
				$selected = " selected";
				
			echo "<option value=\"".$fila1["id_banco"]."\"".$selected.">".utf8_decode($fila1["nombre"])."</option>\n";
		}
		
  ?>

		</select>
	</td>
	<td><select name="tipo_cuenta<?php echo $fila["id_cuenta"] ?>" style="width:60px">
			<option value=""></option>
			<?php if ($fila["tipo_cuenta"] == $label_aho) { ?><option value="<?php echo $label_aho ?>"<?php if ($fila["tipo_cuenta"] == $label_aho) { ?> selected<?php } ?>><?php echo $label_aho ?></option><?php } ?>
			<?php if ($fila["tipo_cuenta"] == $label_cte) { ?><option value="<?php echo $label_cte ?>"<?php if ($fila["tipo_cuenta"] == $label_cte) { ?> selected<?php } ?>><?php echo $label_cte ?></option><?php } ?>
		</select>
	</td>
	<td><input type="text" name="nro_cuenta<?php echo $fila["id_cuenta"] ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nro_cuenta"])) ?>" maxlength="20" size="20" readonly></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_cuenta"] ?>'; modificar(document.formato3.nombre<?php echo $fila["id_cuenta"] ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo $fila["id_cuenta"] ?>" value="1"></td>
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
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
