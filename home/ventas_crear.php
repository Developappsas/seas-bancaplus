<?php 
include ('../functions.php'); 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
ini_set('max_execution_time', 0);
set_time_limit(0);

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
if ($_REQUEST["tipo"] != "VENTA" && $_REQUEST["tipo"] != "TRASLADO")
{
	exit;
}

if ($_SESSION["S_TIPO"] == "CONTABILIDAD" && $_REQUEST["tipo"] == "TRASLADO")
{
	exit;
}

if ($_REQUEST["id_venta"])
{
	$queryDB = "SELECT id_venta from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."' AND estado IN ('ALI')";
	
	$rs = sqlsrv_query( $link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	if (!sqlsrv_num_rows($rs))
	{
		exit;
	}
}
if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_REQUEST["tipo"] != "TRASLADO")
{
	if (!$_REQUEST["id_venta"])
	{
		$i_inicial = 16;
		$i_final = 3;
		$i_incremento = 6;
	}
	else
	{
		$i_inicial = 21;
		$i_final = 2;
		$i_incremento = 5;
	}
}
else
{
	if (!$_REQUEST["id_venta"])
	{
		$i_inicial = ($_REQUEST["tipo"] == "TRASLADO") ? "14" : "14";
		$i_final = 2;
		$i_incremento = 5;
	}
	else
	{
		$i_inicial = 21;
		$i_final = 2;
		$i_incremento = 5;
	}
}

if ($_REQUEST["tipo"] == "VENTA")
{
	$titulo_crear = "Nueva Venta";
	$boton_crear = "Crear Venta";
}
else if ($_REQUEST["tipo"] == "TRASLADO")
{
	$titulo_crear = "Nuevo Traslado";
	$boton_crear = "Crear Traslado";
}

?>
<?php include("top.php"); ?>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	var flag = 0, al_menos_uno = 0;

	with (document.formato) {
<?php

if (!($_REQUEST["tipo"] == "TRASLADO" && !$_REQUEST["id_venta"] && !$_REQUEST["descripcion_busqueda3"]))
{

?>
		if (action.value == "crear" || action.value == "adicionar") {
<?php

	if (!$_REQUEST["id_venta"])
	{
	
?>
			if ((fecha_anuncio.value == "") || (fecha.value == "") || (id_comprador.selectedIndex == 0) || (tasa_venta.value == "") || (modalidad_prima.selectedIndex == 0)) {
				alert("Los campos marcados con asterisco(*) son obligatorios");
				return false;
			}
<?php

	}
	
?>
			for (i = <?php echo $i_inicial; ?>; i <= elements.length - <?php echo $i_final; ?>; i = i + <?php echo $i_incremento; ?>) {
				if (elements[i].checked == true) {
					al_menos_uno = 1;
					
	 				if (elements[i - 2].value == "" || elements[i - 3].value == "" || elements[i - 4].value == "") {
		 				alert("Debe establecer la fecha de primer pago, la cuota desde y la cuota hasta para los creditos seleccionados");
						elements[i].focus();
						return false;
					}
					if (parseInt(elements[i - 2].value) > parseInt(elements[i - 1].value)) {
		 				alert("La cuota hasta no debe ser mayor al plazo del credito");
						elements[i - 2].focus();
						return false;
					}
					if (parseInt(elements[i - 3].value) > parseInt(elements[i - 2].value)) {
		 				alert("La cuota desde no debe ser mayor a la cuota hasta");
						elements[i - 3].focus();
						return false;
					}
					if (parseInt(elements[i - 3].value) == "0") {
		 				alert("La cuota desde no puede ser cero");
						elements[i - 3].focus();
						return false;
					}
				}
			}
			if (al_menos_uno == 0) {
				alert("Debe seleccionar al menos un credito");
				return false;
			}
		}
<?php

}
else
{

?>
		if (al_menos_uno == 0) {
			alert("Debe seleccionar al menos un credito");
			return false;
		}
<?php

}

?>
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();
		
		setTimeout(function(){ enviar_forma(); }, 3000);
	}
}

function Chequear_Todos() {
	with (document.formato) {
		for (i = <?php echo $i_inicial; ?>; i <= elements.length - <?php echo $i_final; ?>; i = i + <?php echo $i_incremento; ?>) {
			if (chkall.checked == true)
				elements[i].checked = true;
			else
				elements[i].checked = false;
		}
	}
}

<?php

if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1" && !$_REQUEST["id_venta"] && $_REQUEST["tipo"] == "VENTA")
{

?>
function Chequear_TodosNV() {
	with (document.formato) {
		for (i = <?php echo $i_inicial; ?> - 5; i <= elements.length - <?php echo $i_final; ?>; i = i + <?php echo $i_incremento; ?>) {
			if (chkallnv.checked == true)
				elements[i].checked = true;
			else
				elements[i].checked = false;
		}
	}
}
<?php

}

?>

function ReplicarFecha(fecha_primer_pago) {
	with (document.formato) {
		for (i = <?php echo $i_inicial; ?>; i <= elements.length - <?php echo $i_final; ?>; i = i + <?php echo $i_incremento; ?>) {
			if (elements[i - 4].readOnly == false)
				elements[i - 4].value = fecha_primer_pago;
		}
	}
}

function ventas_cuotadesde_corte(action, ext, tipo, descripcion_busqueda3, id_venta, fecha_corte) {
	var parametros = {
		"action": action,
		"ext": ext,
		"tipo": tipo,
		"descripcion_busqueda3": descripcion_busqueda3,
		"id_venta": id_venta,
		"fecha_corte": fecha_corte
	};
		
	$.ajax({
    	type: "POST",
		async: false,
		url: "ventas_cuotadesde_corte.php",
		data: parametros,
		success: function( response ) {
			if (response) {
				//alert(response);
				with (document.formato) {
					for (i = <?php echo $i_inicial; ?>; i <= elements.length - <?php echo $i_final; ?>; i = i + <?php echo $i_incremento; ?>) {
						elements[i - 3].value = "";
					}
				}
				respuesta_split = response.trim().split("|");
				
				for (i = 0; i < respuesta_split.length - 1; i++) {
					campo_split = respuesta_split[i].split("=");
					
					$("#"+campo_split[0]).val(campo_split[1]);
				}
			}
		}
	});
}

function enviar_forma() {
	with (document.formato) {
		var datos_formulario = {
			"ext": "<?php echo $_REQUEST["ext"] ?>"
		}
		
		for (i = 0; i <= elements.length - 1; i++) {
			if (elements[i].type == "checkbox")
				if (elements[i].checked == true)
					valor = "1";
				else
					valor = "0";
			else
				valor = elements[i].value;
			
			datos_formulario[elements[i].name] = valor;
		}
	}
	
	//alert(JSON.stringify(datos_formulario));
	
	$.ajax({
    	type: "POST",
		url: "ventas_crear2.php",
		data: JSON.stringify(datos_formulario),
		dataType: "json",
		contentType: "application/json",
 		success: function( response ) {
			if (response) {
				Swal.close();
				
				respuesta_split = response.split("|");
				
				mensaje_split = respuesta_split[0].split("->");
				
				url_split = respuesta_split[1].split("->");
				
				alert(mensaje_split[1]);
				
				window.location.href = url_split[1];
			}
		},
		error: function(data) {
			Swal.close();
		}
	});
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td valign="top" width="18"><a href="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b><?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><?php if (!$_REQUEST["id_venta"]) { echo $titulo_crear; } else { ?>Adicionar Cr&eacute;ditos<?php } ?><?php } else { ?>Cr&eacute;ditos por Vender<?php } ?></b><br><br></center></td>
</tr>
</table>
<form name=formato id=formato method=post action="ventas_crear2.php?ext=<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="tipo" value="<?php echo $_REQUEST["tipo"] ?>">
<input type="hidden" name="descripcion_busqueda3" value="<?php echo $_REQUEST["descripcion_busqueda3"] ?>">
<?php

if ($_REQUEST["id_venta"])
{

?>
<input type="hidden" name="id_venta" value="<?php echo $_REQUEST["id_venta"] ?>">
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
<?php

}

if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1")
{



	if (!$_REQUEST["id_venta"] && ($_REQUEST["tipo"] == "VENTA" || ($_REQUEST["tipo"] == "TRASLADO" && $_REQUEST["descripcion_busqueda3"])))
	{
	
?>
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td>* F. Anuncio<br><input type="text" name="fecha_anuncio" value="<?php echo date("Y-m-d") ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" style="text-align:center; background-color:#EAF1DD;"></td>
	<td>* F. Venta<br><input type="text" name="fecha" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" style="text-align:center; background-color:#EAF1DD;"></td>
	<td>F. Corte<br><input type="text" name="fecha_corte" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false} else { ventas_cuotadesde_corte('crear', '<?php echo $_REQUEST["ext"] ?>', '<?php echo $_REQUEST["tipo"] ?>', '<?php echo $_REQUEST["descripcion_busqueda3"] ?>', '<?php echo $_REQUEST["id_venta"] ?>', this.value) }" style="text-align:center; background-color:#EAF1DD;"></td>
	<td>* Comprador<br>
		<select name="id_comprador" style="width:155px; background-color:#EAF1DD;">
			<option value=""></option>
<?php

		$queryDB = "SELECT id_comprador, nombre from compradores order by nombre";
		
		$rs1 = sqlsrv_query($link,$queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			echo "<option value=\"".$fila1["id_comprador"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
		}
		
?>

		</select>&nbsp;&nbsp;&nbsp;
	</td>
	<td>* Tasa Venta<br><input type="text" name="tasa_venta" size="8" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}" style="text-align:center; background-color:#EAF1DD;"></td>
	<td>* Modalidad Prima<br>
		<select name="modalidad_prima" style="background-color:#EAF1DD;">
			<option value=""></option>
			<option value="ANT">PRIMA ANTICIPADA</option>
			<option value="MDI">PRIMA MENSUAL DIFERENCIA EN INTERESES</option>
			<option value="MDC">PRIMA MENSUAL DIFERENCIA EN CUOTA</option>
		</select>
	</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<?php

	}
	
	if ($_REQUEST["id_venta"] || $_REQUEST["tipo"] == "TRASLADO")
	{
	
?>
<iframe id="frm_busqueda" src="ventas_busqueda.php?tipo=<?php echo $_REQUEST["tipo"] ?>&ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>&action=ventas_crear" width="630px" height="84px"></iframe>
<?php

	}
	
?>
<hr noshade size=1 width=350>
<?php

}

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.no_vender, dbo.fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado = 'DES' OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestado_compras_desembolso.", '".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."',".$subestados_desembolso_nuevos_tesoreria.") AND si.estado_tesoreria = 'PAR'))";
	
	if ($_REQUEST["tipo"] == "VENTA"){
		$val=1;
		$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
	}
	else if ($_REQUEST["tipo"] == "TRASLADO")
	{

		if ($_REQUEST["descripcion_busqueda3"]<>"")
		{
			$val=1;
		}else{
			$val=2;
		}
		$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle ved INNER JOIN ventas ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
		
		if (!$_REQUEST["id_venta"] && !$_REQUEST["descripcion_busqueda3"])
			$queryDB .= " AND 1 = 0";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
	// echo $queryDB;
}
else
{
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.no_vender, dbo.fn_total_recaudado(si.id_simulacion, 1) as total_recaudado from simulaciones".$sufijo." si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.estado = 'DES'";
	
	if ($_REQUEST["tipo"] == "VENTA")
	{
		$val=1;
		$queryDB .= " AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('ALI', 'VEN') AND ved.recomprado = '0')";
	}
	else if ($_REQUEST["tipo"] == "TRASLADO")
	{

		if ($_REQUEST["descripcion_busqueda3"]<>"")
		{
			$val=1;
		}else{
			$val=2;
		}
		$queryDB .= " AND si.id_simulacion IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND ved.recomprado = '0') AND si.id_simulacion NOT IN (select ved.id_simulacion from ventas_detalle".$sufijo." ved INNER JOIN ventas".$sufijo." ve ON ved.id_venta = ve.id_venta where ve.tipo = 'TRASLADO' AND ve.estado IN ('ALI'))";
		
		if (!$_REQUEST["id_venta"] && !$_REQUEST["descripcion_busqueda3"])
			$queryDB .= " AND 1 = 0";
	}
}

if ($_REQUEST["id_venta"])
{
	$queryDB .= " AND si.no_vender = '0'";
}

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["descripcion_busqueda3"])
{
	$queryDB .= " AND (1 = 0";
	
	$cedulas = explode(",", $_REQUEST["descripcion_busqueda3"]);
	
	foreach ($cedulas as $ced)
	{
		$queryDB .= " OR si.cedula = '".trim($ced)."' OR si.nro_libranza = '".trim($ced)."'";
	}
	
	$queryDB .= ")";
}

$queryDB .= " order by abs(si.cedula), si.id_simulacion DESC";


if ($val==1)
{
	$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (sqlsrv_num_rows($rs))
	{

	?>
	<table border="0" cellspacing=1 cellpadding=2 class="tab1">
	<tr>
		<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1" && !$_REQUEST["id_venta"] && $_REQUEST["tipo"] == "VENTA") { ?><th>NO Vender<br><input type="checkbox" name="chkallnv" onClick="Chequear_TodosNV();"></th><?php } ?>
		<th>C&eacute;dula</th>
		<th>Nombre</th>
		<th>No. Libranza</th>
		<th>Tasa</th>
		<th>Cuota</th>
		<th>Vr Cr&eacute;dito</th>
		<th>Pagadur&iacute;a</th>
		<th>Plazo</th>
		<th>Estado</th>
	<?php

		if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1")
		{
		
	?>
		<th>F. Primer Pago</th>
		<th>Cuota<br>Desde<br>Cartera</th>
		<th>Cuota Desde</th>
		<th>Cuota Hasta</th>
		<th>Seleccionar<br><input type="checkbox" name="chkall" onClick="Chequear_Todos();"></th>
	<?php

		}
		
	?>
	</tr>
	<?php

		$j = 1;
		
		$primer_registro = 1;
		
		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
		{
			//if (!($fila["no_vender"] && $_SESSION["S_TIPO"] == "CONTABILIDAD"))
			//{
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
				
				switch ($fila["estado"])
				{
					case "EST":	$estado = "PARCIAL"; break;
					case "DES":	$estado = "DESEMBOLSADO"; break;
					case "CAN":	$estado = "CANCELADO"; break;
				}
				
				if (!$fila["no_vender"])
				{
					$total_opcion_cuota += round($opcion_cuota);
					$total_valor_credito += round($fila["valor_credito"]);
				}
				
				$cuota_desde = ceil($fila["total_recaudado"] / $opcion_cuota) + 1;
				
	?>
	<tr <?php echo $tr_class ?>>
		<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1" && !$_REQUEST["id_venta"] && $_REQUEST["tipo"] == "VENTA") { ?><td align="center"><input type="checkbox" name="chknv<?php echo $fila["id_simulacion"] ?>" value="1"<?php if ($fila["no_vender"]) { ?> checked<?php } ?>></td><?php } ?>
		<td><?php echo $fila["cedula"] ?></td>
		<td><?php echo utf8_decode($fila["nombre"]) ?></td>
		<td align="center"><?php echo $fila["nro_libranza"] ?></td>
		<td align="right"><?php echo $fila["tasa_interes"] ?></td>
		<td align="right"><?php if ($fila["no_vender"]) { ?><strike><?php } ?><?php echo number_format($opcion_cuota, 0) ?></strike></td>
		<td align="right"><?php if ($fila["no_vender"]) { ?><strike><?php } ?><?php echo number_format($fila["valor_credito"], 0) ?></strike></td>
		<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
		<td align="right"><?php echo $fila["plazo"] ?></td>
		<td align="center"><?php echo $estado ?></td>
	<?php

				if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1")
				{
				
	?>
		<td align="center"><input type="text" name="fecha_primer_pago<?php echo $fila["id_simulacion"] ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}<?php if (!$fila["no_vender"] && $primer_registro) { ?> else { ReplicarFecha(this.value); }<?php } ?>" style="text-align:center;<?php if (!$fila["no_vender"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
		<td align="right"><?php if (!$fila["no_vender"]) { echo $cuota_desde; } ?></td>
		<td align="center"><input type="text" id="cuota_desde<?php echo $fila["id_simulacion"] ?>" name="cuota_desde<?php echo $fila["id_simulacion"] ?>" value="<?php if (!$fila["no_vender"]) { echo $cuota_desde; } ?>" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" style="text-align:center;<?php if (!$fila["no_vender"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
		<td align="center"><input type="text" name="cuota_hasta<?php echo $fila["id_simulacion"] ?>" value="<?php if (!$fila["no_vender"]) { echo $fila["plazo"]; } ?>" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" style="text-align:center;<?php if (!$fila["no_vender"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
		<td align="center"><input type="hidden" name="plazoh<?php echo $fila["id_simulacion"] ?>" value="<?php echo $fila["plazo"] ?>"><?php if (!$fila["no_vender"]) { ?><input type="checkbox" name="chk<?php echo $fila["id_simulacion"] ?>" value="1"><?php } else { ?><input type="hidden" name="chkx<?php echo $fila["id_simulacion"] ?>"><?php } ?></td>
	<?php

					if (!$fila["no_vender"] && $primer_registro)
						$primer_registro = 0;
				}
				
	?>
	</tr>
	<?php

				$j++;
			//}
		}
	?>
	<tr class="tr_bold">
		<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["S_SOLOLECTURA"] != "1" && !$_REQUEST["id_venta"] && $_REQUEST["tipo"] == "VENTA") { ?><td>&nbsp;</td><?php } ?>
		<td colspan="4">&nbsp;</td>
		<td align="right"><b><?php echo number_format($total_opcion_cuota, 0) ?></b></td>
		<td align="right"><b><?php echo number_format($total_valor_credito, 0) ?></b></td>
		<td colspan="8">&nbsp;</td>
	</tr>
	</table>
	<?php

		if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1")
		{
		
	?>
	<br>
	<p align="center">&nbsp;&nbsp;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && !$_REQUEST["id_venta"] && $_REQUEST["tipo"] == "VENTA") { ?><input type="button" value="No Vender" onClick="document.formato.action.value='no_vender'; chequeo_forma();">&nbsp;&nbsp;<?php } ?><?php if (!$_REQUEST["id_venta"]) { ?><input type="button" value="<?php echo $boton_crear ?>" onClick="document.formato.action.value='crear'; chequeo_forma();"><?php } else { ?><input type="button" value="Adicionar" onClick="document.formato.action.value='adicionar';  chequeo_forma();"><?php } ?>&nbsp;&nbsp;</p>
	<?php

		}
		
	?>
	<!-- SweetAlert2 -->
	<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	</form>
	<?php

		if ($_REQUEST["id_venta"])
		{
			$queryDB = "select * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

			$venta_rs = sqlsrv_query($link,$queryDB);

			$venta = sqlsrv_fetch_array($venta_rs);

			if ($venta["fecha_corte"])
			{
				
	?>
	<script>window.onload = ventas_cuotadesde_corte('crear', '<?php echo $_REQUEST["ext"] ?>', '<?php echo $_REQUEST["tipo"] ?>', '<?php echo $_REQUEST["descripcion_busqueda3"] ?>', '<?php echo $_REQUEST["id_venta"] ?>', '<?php echo $venta["fecha_corte"] ?>');</script>
	<?php

			}
		}
	}
	else
	{
		echo "<table><tr><td>No se encontraron registros</td></tr></table>";
	}

}

?>
<?php include("bottom.php"); ?>
