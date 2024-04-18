<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('../functions.php'); 
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["unificar"] == "1")
{
	$venta_fuente = sqlsrv_query($link,"SELECT id_venta, tipo from ventas".$sufijo." where nro_venta = '".$_REQUEST["venta_fuente"]."' AND estado IN ('ALI')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (!sqlsrv_num_rows($venta_fuente))
	{
		echo "SELECT id_venta, tipo from ventas".$sufijo." where nro_venta = '".$_REQUEST["venta_destino"]."' AND estado IN ('ALI')";
		$mensaje = "La venta/traslado desde la cual se van a mover los creditos no existe o no esta en estado ALISTADA. Unificacion NO realizada";
	}
	else
	{
		if (sqlsrv_num_rows($venta_fuente) > 1){	
			echo "SELECT id_venta, tipo from ventas".$sufijo." where nro_venta = '".$_REQUEST["venta_destino"]."' AND estado IN ('ALI')";
			$mensaje = "La venta/traslado desde la cual se van a mover los creditos existe mas de una vez. Unificacion NO realizada";
		}
		
		
		$fila1 = sqlsrv_fetch_array($venta_fuente);
		
		$venta_fuente_id = $fila1["id_venta"];
		
		$venta_fuente_tipo = $fila1["tipo"];
	}
	
	$venta_destino = sqlsrv_query($link,"SELECT id_venta, tipo from ventas".$sufijo." where nro_venta = '".$_REQUEST["venta_destino"]."' AND estado IN ('ALI')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (!sqlsrv_num_rows($venta_destino))
	{		
			echo "SELECT id_venta, tipo from ventas".$sufijo." where nro_venta = '".$_REQUEST["venta_destino"]."' AND estado IN ('ALI')";
			$mensaje = "La venta/traslado en la cual se van a unificar los creditos no existe o no esta en estado ALISTADA. Unificacion NO realizada";
	}
	else
	{
		if (sqlsrv_num_rows($venta_destino) > 1)
			$mensaje = "La venta/traslado en la cual se van a unificar los creditos existe mas de una vez. Unificacion NO realizada";
		
		$fila1 = sqlsrv_fetch_array($venta_destino);
		
		$venta_destino_id = $fila1["id_venta"];
		
		$venta_destino_tipo = $fila1["tipo"];
	}
	
	if ($venta_fuente_tipo && $venta_destino_tipo && $venta_fuente_tipo != $venta_destino_tipo)
	{
		$mensaje = "Las ventas/traslados digitados no son del mismo tipo. Unificacion NO realizada";
	}
	
	if (!$mensaje)
	{
		sqlsrv_query($link,"INSERT into ventas_detalle".$sufijo." (id_venta, id_simulacion, fecha_primer_pago, cuota_desde, cuota_hasta) select '".$venta_destino_id."', id_simulacion, fecha_primer_pago, cuota_desde, cuota_hasta from ventas_detalle".$sufijo." where id_venta = '".$venta_fuente_id."'");
		
		sqlsrv_query($link,"UPDATE ventas".$sufijo." set estado = 'ANU', nro_venta = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_venta = '".$venta_fuente_id."'");
		
		echo "<script>alert('Unificacion exitosa'); window.location.href='ventas.php?ext=".$_REQUEST["ext"]."'</script>";
		
		exit;
	}
	else
	{
		echo "<script>alert('".$mensaje."');</script>";
	}
}

?>
<script language="JavaScript">
<!--
function chequeo_forma() {
	with (document.formato) {
		if ((venta_fuente.value == "") || (venta_destino.value == "")) {
			alert ('Debe digitar las dos ventas/traslados que se van a unificar');
			return false;
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
<tr>
	<td valign="top" width="18"><a href="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Unificaci&oacute;n Ventas/Traslados</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="ventas_unificar.php?ext=<?php echo $_REQUEST["ext"] ?>" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="right">Mover cr&eacute;ditos de la Venta/Traslado No.</td>
	<td><input name="venta_fuente" type="text" maxlength="20" size="25" style="background-color:#EAF1DD;"/></td>
</tr>
<tr>
	<td align="right">Hacia la Venta/Traslado No.</td>
	<td><input name="venta_destino" type="text" maxlength="20" size="25" style="background-color:#EAF1DD;"/></td>
</tr>
<tr>
	<td colspan="2" align="center"><br><input type="hidden" name="unificar" value="1"><input type="submit" value="Unificar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<?php include("bottom.php"); ?>
