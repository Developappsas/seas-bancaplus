<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<?php include("top2.php"); ?>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td class="titulo"><center><b>Historial Seguroo</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB = "SELECT ps.nombre as [plan], ss.valor_seguro, pe.nombre as perfil, ss.usuario_creacion, ss.fecha_creacion from simulaciones_seguro ss INNER JOIN simulaciones si ON ss.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN planes_seguro ps ON ss.id_plan_seguro = ps.id_plan INNER JOIN perfiles pe ON ss.id_perfil = pe.id_perfil where ss.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

$queryDB .= " order by ss.id_simulacionseg desc";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{
?>
<form name="formato3" method="post" action="simulaciones_seguro.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Plan</th>
	<th>Valor</th>
	<th>Usuario</th>
	<th>Perfil</th>
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
		
?>
<tr <?php echo $tr_class ?>>
	<td><?php echo $fila["plan"] ?></td>
	<td align="right"><?php echo number_format($fila["valor_seguro"], 0) ?></td>
	<td><?php echo $fila["usuario_creacion"] ?></td>
	<td><?php echo $fila["perfil"] ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>


</tr>
<?php

		$j++;
	}

?>
</table>
<br>


</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom2.php"); ?>
