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
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((id_unidad_negocio.value == "")) {
			alert("Debe seleccionar una unidad de negocio para asociarla");
			return false;
		}
		
		
	}
}
//-->
</script>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
    <td valign="top" width="18"><a href="tasas2.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Asociar Unidades de Negocio</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="tasas2unidadesnegocio_crear.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_tasa2" value="<?php echo $_REQUEST["id_tasa2"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td valign="bottom"><br>
		<select name="id_unidad_negocio">
			<option value=""></option>
<?php

$queryDB = "SELECT id_unidad, nombre from unidades_negocio where estado = '1' AND NOT (id_unidad IN (select id_unidad_negocio from tasas2_unidades".$sufijo_sector." where id_tasa2 = '".$_REQUEST["id_tasa2"]."')) order by id_unidad";

$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_unidad"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>

			</select>&nbsp;
	
	<td valign="bottom">&nbsp;<br><input type="submit" value="Asociar Unidad"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<br>

<?php

if ($_REQUEST["action"])
{
	$queryDB = "SELECT * from tasas2_unidades".$sufijo_sector." tu INNER JOIN unidades_negocio un ON tu.id_unidad_negocio = un.id_unidad where tu.id_tasa2 = '".$_REQUEST["id_tasa2"]."'";
	
    $queryDB .= " order by un.id_unidad";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila["id"]]== "1")
		{
			if ($_REQUEST["action"] == "borrar")
			{
				sqlsrv_query( $link,"DELETE from tasas2_unidades".$sufijo_sector." where id = '".$fila["id"]."'");
			}
		}
	}
} 

$queryDB = "SELECT * from tasas2_unidades".$sufijo_sector." tu INNER JOIN unidades_negocio un ON tu.id_unidad_negocio = un.id_unidad where tu.id_tasa2 = '".$_REQUEST["id_tasa2"]."'";

$queryDB .= " order by un.id_unidad";

$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(sqlsrv_num_rows($rs))
{

?>
<form name="formato3" method="post" action="tasas2unidadesnegocio.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_tasa2" value="<?php echo $_REQUEST["id_tasa2"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Unidad de Negocio</th>
	<th>Desasociar</th>
</tr>
<?php

	$j = 1;
	
	while ($fila1 = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";	
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
?>

<tr <?php echo $tr_class ?>>
	<td><?php echo utf8_decode($fila1["nombre"]) ?></td>
	<td align="center"><input type="checkbox" name="chk<?php echo $fila1["id"] ?>" value="1"></td>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
<p align="center"><input type="submit" value="Desasociar" onClick="document.formato3.action.value='borrar'"></p>

</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
