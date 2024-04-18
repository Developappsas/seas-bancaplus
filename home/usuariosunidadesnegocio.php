<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">
<!--
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
    <td valign="top" width="18"><a href="usuarios.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Asociar Unidades de Negocio</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="usuariosunidadesnegocio_crear.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_usuario" value="<?php echo $_REQUEST["id_usuario"] ?>">
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

$queryDB = "select id_unidad, nombre from unidades_negocio where estado = '1' AND NOT (id_unidad IN (select id_unidad_negocio from usuarios_unidades where id_usuario = '".$_REQUEST["id_usuario"]."')) order by id_unidad";
echo $queryDB;

$rs1 = sqlsrv_query($link, $queryDB);

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
	$queryDB = "SELECT * from usuarios_unidades uu INNER JOIN unidades_negocio un ON uu.id_unidad_negocio = un.id_unidad where uu.id_usuario = '".$_REQUEST["id_usuario"]."'";
	
    $queryDB .= " order by un.id_unidad";
	
	$rs = sqlsrv_query($queryDB, $link);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila["id"]]== "1")
		{
			if ($_REQUEST["action"] == "borrar")
			{
				sqlsrv_query("delete from usuarios_unidades where id = '".$fila["id"]."'", $link);
			}
		}
	}
} 

$queryDB = "select * from usuarios_unidades uu INNER JOIN unidades_negocio un ON uu.id_unidad_negocio = un.id_unidad where uu.id_usuario = '".$_REQUEST["id_usuario"]."'";

$queryDB .= " order by un.id_unidad";

$rs = sqlsrv_query($queryDB, $link);

if(sqlsrv_num_rows($rs))
{

?>
<form name="formato3" method="post" action="usuariosunidadesnegocio.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_usuario" value="<?php echo $_REQUEST["id_usuario"] ?>">
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
