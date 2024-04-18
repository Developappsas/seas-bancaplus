<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if (!$_REQUEST["ext"])
{
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs))
{
	exit;
}

?>
<?php include("top2.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if (fecha_venc.value == "") {
			alert("Debe establecer la fecha de vencimiento");
			return false;
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Certificado de Deuda</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="certificacion_deudanew.php" onSubmit="return chequeo_forma()">
<table>
<tr height="40">
	<td>Establezca la fecha de vencimiento</td>
	<td><input type="text" name="fecha_venc" size="14" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}"></td>
</tr>
<?php

if ($_SESSION["FUNC_BOLSAINCORPORACION"] && !$_REQUEST["ext"])
{

?>
<tr>
	<td>Descontar saldo bolsa incorporaci&oacute;n&nbsp;</td>
	<td valign="middle"><input type="checkbox" name="descontar_bolsa" value="1" style="text-align:center; background-color:#EAF1DD;"></td>
</tr>
<?php

}

?>
</table>
<br>
<p align="center">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="ext" value="<?php echo $_REQUEST["ext"] ?>">
<input type="submit" value="Generar">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="window.close()">
</p>
</form>
<?php include("bottom2.php"); ?>
