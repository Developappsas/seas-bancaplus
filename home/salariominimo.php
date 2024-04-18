<?php include ('../functions.php'); ?>

<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
	exit;
}
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((ano.value == "") || (salario_minimo.value == "")) {
			alert("Debe digitar el ano y el valor del salario minimo");
			return false;
		}
	}
}

function modificar(camposm) {
	with (document.formato3) {
		if (camposm.value == "") {
			alert("Debe digitar el salario minimo");
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
	<td class="titulo"><center><b>Salario M&iacute;nimo</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="salariominimo_crear.php" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">A&ntilde;o<br><input type="text" name="ano" maxlength="4" size="5" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">Valor<br><input type="text" name="salario_minimo" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Salario M&iacute;nimo"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<br>
<!--<form name="formato2" method="post" action="salariominimo.php">
<table>
<tr>
<td>
<div class="box1 oran clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">A&ntilde;o<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>-->
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT * from salario_minimo where ano IS NOT NULL";

$queryDB_count = "SELECT COUNT(*) as c from salario_minimo where ano IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"]) {
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	$queryDB = $queryDB." AND ano = '".$descripcion_busqueda."'";
	$queryDB_count = $queryDB_count." AND ano = '".$descripcion_busqueda."'";
}

$queryDB .= " order by ano DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

$rs = sqlsrv_query($link,$queryDB);

$rs_count = sqlsrv_query($link, $queryDB_count, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if ($cuantos) {
	if ($cuantos > $x_en_x) {
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
		
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos) {
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			if ($final > $cuantos) {
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"]) {
				echo " <a href=\"salario_minimo.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
			} else {
				echo " ".$i;
			}
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;			
			echo " <a href=\"salario_minimo.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}

?>
<form name="formato3" method="post" action="salariominimo_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>A&ntilde;o</th>
	<th>Valor</th>
	<!--<th>Modificar</th>-->
	<th>Borrar</th>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$tr_class = "";		
		if (($j % 2) == 0) {
			$tr_class = " style='background-color:#F1F1F1;'";
		}
?>
		<tr <?php echo $tr_class ?>>
	<td><input type="text" name="a<?php echo $fila["id_salariominimo"] ?>" value="<?php echo $fila["ano"] ?>" maxlength="4" size="5" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="sm<?php echo $fila["id_salariominimo"] ?>" value="<?php echo number_format($fila["salario_minimo"], 0, ".", ",") ?>" style="text-align:right;" maxlength="10" size="10"></td>
	<!--<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php //echo $fila["id_salariominimo"] ?>'; modificar(document.formato3.sm<?php //echo $fila["id_salariominimo"] ?>)"></td>-->
	<td align=center><input type=checkbox name="b<?php echo $fila["id_salariominimo"] ?>" value="1"></td>
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
