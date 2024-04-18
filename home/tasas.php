<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((plazoi.value == "") || (plazof.value == "")) {
			alert("Debe digitar plazo inicial y final");
			return false;
		}
		if (parseInt(plazoi.value) > parseInt(plazof.value)) {
			alert("El plazo final debe ser mayor que el inicial");
			return false;
		}
	}
}
function modificar(campopi, campopf) {
	with (document.formato3) {
		if ((campopi.value == "") || (campopf.value == "")) {
			alert("Debe digitar plazo inicial y final");
			return false;
		}
		else if (parseInt(campopi.value) > parseInt(campopf.value)) {
			alert("El plazo final debe ser mayor que el inicial");
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
	<td class="titulo"><center><b>Plazos/Tasas Sector <?php echo ucfirst(strtolower($_REQUEST["sector"])) ?></b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="tasas_crear.php?sector=<?php echo $_REQUEST["sector"] ?>" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Plazo Ini<br><input type="text" name="plazoi" maxlength="3" size="6" style="text-align:center;" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">Plazo Fin<br><input type="text" name="plazof" maxlength="3" size="6" style="text-align:center;" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Periodo"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT id_tasa, plazoi, plazof from tasas".$sufijo_sector." where id_tasa IS NOT NULL";

$queryDB_count = "SELECT COUNT(*) as c from tasas".$sufijo_sector." where id_tasa IS NOT NULL";

$queryDB .= " order by plazoi  OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

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
				echo " <a href=\"tasas.php?sector=".$_REQUEST["sector"]."&page=$link_page\">$i</a>";
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
			
			echo " <a href=\"tasas.php?sector=".$_REQUEST["sector"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="tasas_actualizar.php?sector=<?php echo $_REQUEST["sector"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Plazo Ini</th>
	<th>Plazo Fin</th>
	<th>Tasas</th>
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
	<td><input type="text" name="pi<?php echo $fila["id_tasa"] ?>" value="<?php echo $fila["plazoi"] ?>" maxlength="3" size="6" style="text-align:center;" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="pf<?php echo $fila["id_tasa"] ?>" value="<?php echo $fila["plazof"] ?>" maxlength="3" size="6" style="text-align:center;" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td align="center"><a href="tasas2.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $fila["id_tasa"] ?>">Tasas</a></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_tasa"] ?>'; modificar(document.formato3.pi<?php echo $fila["id_tasa"] ?>, document.formato3.pf<?php echo $fila["id_tasa"] ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo $fila["id_tasa"] ?>" value="1"></td>
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
