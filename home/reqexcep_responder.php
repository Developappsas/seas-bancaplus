<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../functions.php'); 


if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

$queryDB = "select * from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
}

$queryDB .= " where re.estado != 'ANULADO' AND re.id_reqexcep = '".$_REQUEST["id_reqexcep"]."'";

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

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= "AND (
	        CASE WHEN re.id_area = '1' THEN 
	            CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL AND si.id_analista_riesgo_crediticio = '5463' THEN 1 
	                 WHEN si.id_analista_riesgo_operativo IS NOT NULL AND si.id_analista_riesgo_operativo = '5463' THEN 1 
	                 ELSE 0 END 
	        ELSE 1 END
	    ) = 1  ";

	
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!sqlsrv_num_rows($rs))
{
    exit;
}

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if (tipo_respuesta.value == "" || respuesta.value == "") {
			alert("Debe establecer el tipo y digitar la respuesta");
			return false;
		}
		
		ReplaceComilla(respuesta)
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td valign="top" width="18"><a href="reqexcep.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Ingresar Respuesta</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reqexcep_responder2.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_reqexcep" value="<?php echo $_REQUEST["id_reqexcep"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="reqexcepb" value="<?php echo $_REQUEST["reqexcepb"] ?>">
<input type="hidden" name="id_tipob" value="<?php echo $_REQUEST["id_tipob"] ?>">
<input type="hidden" name="id_areab" value="<?php echo $_REQUEST["id_areab"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
			<table border="0" cellspacing=1 cellpadding=2>
			<tr>
				<td align="right">Tipo Respuesta</td>
				<td><select name="tipo_respuesta" style="background-color:#EAF1DD;">
						<option value=""></option>
<?php

if ($fila["reqexcep"] == "REQUERIMIENTO")
{

?>
						<option value="RESUELTO">REQUERIMIENTO RESUELTO</option>
						<option value="DEVUELTO">REQUERIMIENTO DEVUELTO</option>
<?php

}
else if ($fila["reqexcep"] == "EXCEPCION")
{

?>
						<option value="APROBADA">EXCEPCION APROBADA</option>
						<option value="NEGADA">EXCEPCION NEGADA</option>
<?php

}

?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" style="vertical-align: top">Respuesta</td>
				<td><textarea name="respuesta" rows="5" cols="70" style="background-color:#EAF1DD;"></textarea></td>
			</tr>
			</table>
		</div>
	</td>
</tr>
</table>
<br>
<p align="center"><input type="submit" value="Ingresar"></p>
</form>
<?php include("bottom.php"); ?>
