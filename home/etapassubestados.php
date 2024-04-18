<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((id_subestado.value == "")) {
			alert("Debe seleccionar un subestado para asociarlo");
			return false;
		}		
	}
}
//-->
</script>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
    <td valign="top" width="18"><a href="etapas.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Asociar Subestados</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="etapassubestados_crear.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_etapa" value="<?php echo $_REQUEST["id_etapa"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td valign="bottom"><br>
		<select name="id_subestado">
			<option value=""></option>
<?php

$queryDB = "SELECT id_subestado, nombre from subestados where estado = '1' AND NOT (id_subestado IN (select id_subestado from etapas_subestados)) order by nombre, id_subestado";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_subestado"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>

			</select>&nbsp;
	
	<td valign="bottom">&nbsp;<br><input type="submit" value="Asociar Subestado"></td>
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
	$queryDB = "SELECT * from etapas_subestados es INNER JOIN subestados su ON es.id_subestado = su.id_subestado where es.id_etapa = '".$_REQUEST["id_etapa"]."'";
	
    $queryDB .= " order by su.nombre";
	
	$rs = sqlsrv_query($link, $queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila["id"]]== "1")
		{
			if ($_REQUEST["action"] == "borrar")
			{
				sqlsrv_query($link, "delete from etapas_subestados where id = '".$fila["id"]."'");
			}
		}
	}
} 

$queryDB = "SELECT * from etapas_subestados es INNER JOIN subestados su ON es.id_subestado = su.id_subestado where es.id_etapa = '".$_REQUEST["id_etapa"]."'";

$queryDB .= " order by su.nombre";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(sqlsrv_num_rows($rs)) {

?>
<form name="formato3" method="post" action="etapassubestados.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_etapa" value="<?php echo $_REQUEST["id_etapa"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Subestado</th>
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
