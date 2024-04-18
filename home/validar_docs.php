<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.cedula, si.nombre, si.validado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado IN ('EST', 'DES')";

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

if ($_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION")
{
	$queryDB .= " AND si.validado IN ('0', '2')";
}

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1))
{
	exit;
}

if ($fila1["validado"] == "0")
{
	$titulo = "Establezca el tipo de validacion de los documentos de este credito";
}
else if ($fila1["validado"] == "2")
{
	$titulo = "Desea establecer que se validaron totalmente los documentos de este credito";
	
	$validado = "1";
}
else
{
	$titulo = "Desea anular la marcaci&oacute;n de documentos validados para este credito";
	
	$validado = "0";
}

?>
<?php include("top2.php"); ?>
<script language="JavaScript">
<!--
function chequeo_forma() {
	with (document.formato) {
		if (validado.value == "") {
			alert("Debe establecer el tipo de validacion");
			return false;
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
<tr>
	<td class="titulo"><center><b><?php echo $titulo ?><br><?php echo $fila1["cedula"]." ".$fila1["nombre"] ?>?</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="validar_docs2.php" onSubmit="return chequeo_forma()">
<table border="0" cellspacing=1 cellpadding=2>
<tr><td>
<?php

if ($fila1["validado"] == "0")
{

?>
		<select name="validado" style="background-color:#EAF1DD;">
			<option value=""></option>
			<option value="2">Validado con pendientes</option>
			<option value="1">Validado totalmente</option>
		</select>
<?php

}
else
{

?>
		<input type="hidden" name="validado" value="<?php echo $validado ?>">
<?php

}

?>
</td></tr>
<tr><td><br></td></tr>
<tr><td align="center">
	<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
	<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
	<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
	<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
	<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
	<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
	<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
	<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
	<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
	<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
	<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
	<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
	<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
	<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
	<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
	<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
	<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
	<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
	<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
	<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
	<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
	<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
	<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
	<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
	<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
	<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
	<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
	<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
	<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
	<input type="submit" value="Aceptar">&nbsp;&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="window.close();">
	</td>
</tr>
</table>
</form>
<?php include("bottom2.php"); ?>
