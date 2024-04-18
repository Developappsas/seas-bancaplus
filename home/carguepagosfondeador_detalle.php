<?php 
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select procesado from planoscuotasfondeador".$sufijo." where id_planocuotafondeador = '".$_REQUEST["id_planocuotafondeador"]."'";

$planocuotasfondeador_rs = sqlsrv_query($link, $queryDB);

$planocuotasfondeador = sqlsrv_fetch_array($planocuotasfondeador_rs);

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function Chequear_Todos() {
	with (document.formato3) {
		for (i = 2; i <= elements.length - 2; i++) {
			elements[i].checked = true;
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td valign="top" width="18"><a href="carguepagosfondeador.php?ext=<?php echo $_REQUEST["ext"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Detalle Cargue</b><br><br></center></td>
</tr>
</table>
<?php

if ($_REQUEST["action"])
{
	sqlsrv_query($link, "BEGIN");
	
	$queryDB = "select * from planoscuotasfondeador_detalle".$sufijo." where id_planocuotafondeador = '".$_REQUEST["id_planocuotafondeador"]."'";
	
	$queryDB .= " order by id_planocuotafondeadordetalle";
	
	$rs2 = sqlsrv_query($link, $queryDB);
	
	while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila2["id_planocuotafondeadordetalle"]] == "1")
		{
			if ($_REQUEST["action"] == "aplicar" && $fila2["id_simulacion"])
			{
				$queryDB = "select * from ventas_cuotas_fondeador".$sufijo." where id_simulacion = '".$fila2["id_simulacion"]."' and cuota = '".$fila2["cuota"]."'";
				
				$rs = sqlsrv_query($link, $queryDB);
				
				if (sqlsrv_num_rows($rs))
				{
					sqlsrv_query($link, "update ventas_cuotas_fondeador".$sufijo." set pago_fondeador = '".$fila2["valor"]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = NOW() where id_simulacion = '".$fila2["id_simulacion"]."' and cuota = '".$fila2["cuota"]."'");
				}
				else
				{
					sqlsrv_query($link, "insert into ventas_cuotas_fondeador".$sufijo." (id_simulacion, cuota, pago_fondeador, usuario_creacion, fecha_creacion) values ('".$fila2["id_simulacion"]."', '".$fila2["cuota"]."', '".$fila2["valor"]."', '".$_SESSION["S_LOGIN"]."', NOW())");
				}
				
				sqlsrv_query($link, "update planoscuotasfondeador_detalle".$sufijo." set aplicado = '1' where id_planocuotafondeadordetalle = '".$fila2["id_planocuotafondeadordetalle"]."'");
			}
		}
	}
	
	sqlsrv_query($link, "update planoscuotasfondeador".$sufijo." set procesado = '1' where id_planocuotafondeador = '".$_REQUEST["id_planocuotafondeador"]."'");
	
	sqlsrv_query($link, "COMMIT");
	
	if ($_REQUEST["action"] == "aplicar")
	{
		echo "<script>alert('Los pagos al fondeador marcados fueron cargados'); window.location.href='carguepagosfondeador.php?ext=".$_REQUEST["ext"]."';</script>";
	}
}

if (!$_REQUEST["ext"])
{
	$queryDB = "select pcfd.*, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo from planoscuotasfondeador_detalle pcfd LEFT JOIN simulaciones si ON pcfd.id_simulacion = si.id_simulacion where pcfd.id_planocuotafondeador = '".$_REQUEST["id_planocuotafondeador"]."' order by pcfd.id_planocuotafondeadordetalle";
}
else
{
	$queryDB = "select pcfd.*, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo from planoscuotasfondeador_detalle".$sufijo." pcfd LEFT JOIN simulaciones".$sufijo." si ON pcfd.id_simulacion = si.id_simulacion where pcfd.id_planocuotafondeador = '".$_REQUEST["id_planocuotafondeador"]."' order by pcfd.id_planocuotafondeadordetalle";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{

?>
<form name="formato3" method="post" action="carguepagosfondeador_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_planocuotafondeador" value="<?php echo $_REQUEST["id_planocuotafondeador"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Vr Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>No Cuota</th>
	<th>Pago Fondeador</th>
	<th>Observaci&oacute;n</th>
	<th>Aplicar<br><input type="checkbox" name="chkall" onClick="Chequear_Todos();"></th>
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
		
		$opcion_cuota = "0";
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
						break;
			case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
						break;
			case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
						break;
			case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
						break;
		}
		
		$total_valor += round($fila["valor"]);
		
?>
<tr <?php echo $tr_class ?>>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td align="right"><?php echo $fila["tasa_interes"] ?></td>
	<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo $fila["plazo"] ?></td>
	<td align="center"><?php echo $fila["cuota"] ?></td>
	<td align="right"><?php echo number_format($fila["valor"], 0) ?></td>
	<td><?php echo utf8_decode($fila["observacion"]) ?></td>
	<td align="center"><?php if ($fila["observacion"] == "OK") { ?><input type="checkbox" name="chk<?php echo $fila["id_planocuotafondeadordetalle"] ?>" value="1"<?php if ($fila["aplicado"]) { ?> checked<?php } ?><?php if ($recaudoplano["procesado"]) { ?> disabled<?php } ?>><?php } else { echo "&nbsp;"; } ?></td>
</tr>
<?php

		$j++;
	}
	
?>
<tr class="tr_bold">
	<td colspan="9">&nbsp;</td>
	<td align="right"><b><?php echo number_format($total_valor, 0) ?></b></td>
	<td colspan="2">&nbsp;</td>
</tr>
</table>
<br>
<?php

	if (!$planocuotasfondeador["procesado"])
	{
	
?>
<p align="center"><input type="submit" value="Aplicar" onClick="document.formato3.action.value='aplicar'"></p>
<?php

	}
	
?>
</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
