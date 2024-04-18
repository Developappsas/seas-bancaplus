<?php 
include ('../functions.php'); 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
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
		if ((decision.value == "") || (nombre.value == "")) {
			alert("Debe seleccionar decision y digitar el nombre");
			return false;
		}
		
		ReplaceComilla(nombre)
	}
}
function modificar(campod, campon) {
	with (document.formato3) {
		if ((campod.value == "") || (campon.value == "")) {
			alert("Debe seleccionar decision y digitar el nombre");
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
	<td class="titulo"><center><b>Subestados</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="subestados_crear.php" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Decisi&oacute;n<br>
		<select name="decision">
			<option value=""></option>
			<option value="<?php echo $label_viable ?>"><?php echo $label_viable ?></option>
			<option value="<?php echo $label_negado ?>"><?php echo $label_negado ?></option>
		</select>&nbsp;
	</td>
	<td valign="bottom">Nombre<br><input type="text" name="nombre" maxlength="255" size="50"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Subestado"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="subestados.php">
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

$queryDB = "SELECT * from subestados where id_subestado IS NOT NULL";

$queryDB_count = "SELECT COUNT(*) as c from subestados where id_subestado IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	
	$queryDB_count = $queryDB_count." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
}

$queryDB .= " order by decision DESC, nombre OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

$rs = sqlsrv_query($link,$queryDB);

$rs_count = sqlsrv_query($link,$queryDB_count);

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
				echo " <a href=\"subestados.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"subestados.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="subestados_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Decisi&oacute;n</th>
	<th>Nombre</th>
	<th>Activo</th>
	<th>Asociar Usuarios</th>
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
	<td><select name="d<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>">
			<option value=""></option>
			<option value="<?php echo $label_viable ?>"<?php if ($fila["decision"] == $label_viable) { echo " selected"; } ?>><?php echo $label_viable ?></option>
			<option value="<?php echo $label_negado ?>"<?php if ($fila["decision"] == $label_negado) { echo " selected"; } ?>><?php echo $label_negado ?></option>
		</select>
	</td>
	<td><input type="text" name="n<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?>" maxlength="255" size="50"></td>
	<td align="center"><input type="checkbox" name="a<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>" value="1"<?php if ($fila["estado"]) { echo " checked"; } ?>></td>
	<td align="center"><a href="subestadosusuarios.php?id_subestado=<?php echo $fila["id_subestado"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&page=<?php echo $_REQUEST["page"] ?>" >Asociar</a></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>'; modificar(document.formato3.d<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>, document.formato3.n<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo str_replace(" ", "_", $fila["id_subestado"]) ?>" value="1"></td>
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
