<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	var flag = 0, todos_marcados = 1;

	with (document.formato) {
		for (i = 16; i <= elements.length - 2; i = i + 1) {
			if (elements[i].type == "checkbox" && elements[i].checked != true) {
				todos_marcados = 0;
			}
		}
		if (todos_marcados == 1) {
			if (confirm("Se han marcado todos los documento de cierre de la venta del credito, con esta accion quedara completo. Desea continuar?") == true) {
				completo.value = "1";
			}
			else {
				return false;
			}
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td valign="top" width="18"><a href="ventas_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_ventadetalle=<?php echo $_REQUEST["id_ventadetalle"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Documentos Cierre</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB = "SELECT vdd.*, dc.nombre, vd.completo from ventas_detalle_documentos".$sufijo." vdd INNER JOIN documentos_cierre dc ON vdd.id_documentocierre = dc.id_documentocierre INNER JOIN ventas_detalle".$sufijo." vd ON vdd.id_ventadetalle = vd.id_ventadetalle where vdd.id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."'";

$queryDB .= " order by dc.nombre";

$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{
?>
<form name="formato" method="post" action="ventas_documentos2.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="action" value="">
<input type="hidden" name="completo" value="0">
<input type="hidden" name="id_venta" value="<?php echo $_REQUEST["id_venta"] ?>">
<input type="hidden" name="id_ventadetalle" value="<?php echo $_REQUEST["id_ventadetalle"] ?>">
<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
<input type="hidden" name="id_compradorb" value="<?php echo $_REQUEST["id_compradorb"] ?>">
<input type="hidden" name="modalidadb" value="<?php echo $_REQUEST["modalidadb"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="descripcion_busqueda2" value="<?php echo $_REQUEST["descripcion_busqueda2"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>&nbsp;</th>
	<th>Documento</th>
	<th>Usuario</th>
	<th>Fecha</th>
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
		
		$completo = $fila["completo"];
		
?>
<tr <?php echo $tr_class ?>>
	<td align="center"><?php if ($fila["completo"] || ($fila["estado"] && $_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) { ?><img src="../images/chequeado.png"><input type="hidden" name="estado<?php echo $fila["id_documentocierre"] ?>" value="<?php echo $fila["estado"] ?>"><?php } else { ?><input type="checkbox" name="estado<?php echo $fila["id_documentocierre"] ?>" id="estado<?php echo $fila["id_documentocierre"] ?>" value="1"<?php if ($fila["estado"]) { ?> checked<?php } ?>><?php } ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="center"><?php if ($fila["estado"]) { echo utf8_decode($fila["usuario_modificacion"]); } else { echo "&nbsp;"; } ?></td>
	<td align="center"><?php if ($fila["estado"]) { echo $fila["fecha_modificacion"]; } else { echo "&nbsp;"; }  ?></td>
</tr>
<?php

		$j++;
	}

?>
</table>
<br>
<?php

	if (!$completo && $_SESSION["S_TIPO"] != "CONTABILIDAD")
	{

?>
<p align="center"><input type="submit" value="Actualizar" onClick="document.formato3.action.value='actualizar'"></p>
</form>
<?php

	}
}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
