<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

include("top.php"); 
?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((nombre.value == "") || (sector.value == "")) {
			alert("Debe digitar el nombre y seleccionar el sector");
			return false;
		}
		
		ReplaceComilla(nombre)
	}
}
function modificar(campo_sector, campo_plazo) {
		with (document.formato3) {

			if (campo_sector.value == "" || (campo_plazo.value == "" || campo_plazo.value <= 0 || campo_plazo.value > 168)) {

				if (campo_sector.value == "") {
					alert("Debe seleccionar el sector");
					return false;
				}

				if (campo_plazo.value == "") {
					alert("Debe seleccionar el plazo adecuado");
					return false;
				}
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
		<td class="titulo"><center><b>Pagadur&iacute;as</b><br><br></center></td>
	</tr>
</table>

<form name=formato method=post action="pagadurias_crear.php" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Nombre<br> <input type="text" name="nombre" maxlength="255" size="50"></td>
							<td valign="bottom">Plazo<br>  <input type="number" name="plazo" max="168" min="1"  value="168"></td>

							<td valign="bottom">Sector<br>
								<select name="sector" style="margin-top:4px;">
									<option value=""></option>
									<option value="PUBLICO">PUBLICO</option>
									<option value="PRIVADO">PRIVADO</option>
								</select>&nbsp;	
							</td>
							<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Pagadur&iacute;a"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>

<hr noshade size=1 width=350>

<form name="formato2" method="post" action="pagadurias.php">
	<table>
		<tr>
			<td>
				<div class="box1 oran clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Nombre<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
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

$queryDB = "select * from pagadurias where id_pagaduria IS NOT NULL";

$queryDB_count = "select COUNT(*) as c from pagadurias where id_pagaduria IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	
	$queryDB_count = $queryDB_count." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
}

$queryDB .= " order by nombre  OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";



$rs = sqlsrv_query($link,$queryDB);

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
				echo " <a href=\"pagadurias.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"pagadurias.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="pagadurias_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Nombre</th>
	<th>Sector</th>
	<th>Activo</th>
	<th>Asociar Usuarios Visado</th>
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
	<td><?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?></td>
	<td><select name="s<?php echo $fila["id_pagaduria"] ?>">
			<option value=""></option>
			<option value="PUBLICO"<?php if ($fila["sector"] == "PUBLICO") { echo " selected"; } ?>>PUBLICO</option>
			<option value="PRIVADO"<?php if ($fila["sector"] == "PRIVADO") { echo " selected"; } ?>>PRIVADO</option>
		</select>
	</td>
	<td align="center"><input type="checkbox" name="a<?php echo $fila["id_pagaduria"] ?>" value="1"<?php if ($fila["estado"]) { echo " checked"; } ?>></td>
	<td align="center"><a href="pagaduriasusuariosvisado.php?id_pagaduria=<?php echo $fila["id_pagaduria"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&page=<?php echo $_REQUEST["page"] ?>" >Asociar</a></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_pagaduria"] ?>'; modificar(document.formato3.s<?php echo $fila["id_pagaduria"] ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo $fila["id_pagaduria"] ?>" value="1"></td>
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
