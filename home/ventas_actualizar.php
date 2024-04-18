<?php include ('../functions.php');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "SELECT * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

?>
<?php include("top.php"); ?>





<!-- SweetAlert2 -->
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	var flag = 0, al_menos_uno = 0, al_menos_uno_marcado = 0;

	with (document.formato3) {
		if (action.value != "recomprar") {
			if ((fecha_anuncio.value == "") || (fecha.value == "") || (id_comprador.selectedIndex == 0) || (tasa_venta.value == "") || (modalidad_prima.selectedIndex == 0)) {
				alert("Los campos marcados con asterisco(*) son obligatorios");
				return false;
			}
			for (i = 27; i <= elements.length - 2; i = i + 5) {
				if (elements[i].checked == false) {
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
				else {
					al_menos_uno_marcado = 1;
				}
			}
			if (action.value == "actualizar" && al_menos_uno == 0) {
				//alert("Debe dejar al menos un credito");
				//return false;
			}
			if (action.value == "dividir" && al_menos_uno_marcado == 0) {
				alert("Debe seleccionar por lo menos un credito");
				return false;
			}
		}
		
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();
		
		setTimeout(function(){ enviar_forma(); }, 3000);
	}
}
<?php

if ($venta["estado"] == "ALI")
{

?>
function ReplicarFecha(fecha_primer_pago) {
	with (document.formato3) {
		for (i = 27; i <= elements.length - 2; i = i + 5) {
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
				with (document.formato3) {
					for (i = 27; i <= elements.length - 2; i = i + 5) {
						elements[i - 3].value = "";
					}
				}
				respuesta_split = response.split("|");
				
				for (i = 0; i < respuesta_split.length - 1; i++) {
					campo_split = respuesta_split[i].split("=");
					//alert(campo_split[0]+"--"+campo_split[1]);
					$(campo_split[0]).val(campo_split[1]);
				}
			}
		}
	});
}

function Chequear_Todos() {
	with (document.formato3) {
		for (i = 27; i <= elements.length - 2; i = i + 5) {
			if (chkall.checked == true)
				elements[i].checked = true;
			else
				elements[i].checked = false;
		}
	}
}
<?php

}

?>
function enviar_forma() {
	with (document.formato3) {
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
		url: "ventas_actualizar2.php",
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
	<td class="titulo"><center><b>Detalle Venta</b><br><br></center></td>
</tr>
</table>
<form name="formato3" method="post" action="ventas_actualizar2.php?ext=<?php echo $_REQUEST["ext"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
		<tr>
		<td><?php if ($venta["estado"] == "VEN" || $venta["estado"] == "CAN") { echo "* "; } ?>No. Venta<br><input type="text" name="nro_venta" value="<?php echo $venta["nro_venta"] ?>" size="6" style="text-align:center; width: 261px;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<td>* F. Anuncio<br><input type="text" name="fecha_anuncio" value="<?php echo $venta["fecha_anuncio"] ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $venta["fecha_anuncio"] ?>'; return false}" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
	<td>* F. Venta<br><input type="text" name="fecha" value="<?php echo $venta["fecha"] ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $venta["fecha"] ?>'; return false}" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<td>F. Corte<br><input type="text" name="fecha_corte" value="<?php echo $venta["fecha_corte"] ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}<?php if ($venta["estado"] == "ALI") { ?> else { ventas_cuotadesde_corte('actualizar', '<?php echo $_REQUEST["ext"] ?>', '<?php echo $_REQUEST["tipo"] ?>', '<?php echo $_REQUEST["descripcion_busqueda3"] ?>', '<?php echo $_REQUEST["id_venta"] ?>', this.value) }<?php } ?>" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
	<td>* Comprador<br>
		<select name="id_comprador" style="width:155px;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>>
			<option value=""></option>







<?php

$queryDB = "SELECT id_comprador, nombre from compradores order by nombre";

$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	if ($fila1["id_comprador"] == $venta["id_comprador"])
		$selected = " selected";
	else
		$selected = "";
	
	echo "<option value=\"".$fila1["id_comprador"]."\"".$selected.">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>







		</select>&nbsp;&nbsp;&nbsp;
	</td>
	<td>* Tasa Venta<br><input type="text" name="tasa_venta" value="<?php echo $venta["tasa_venta"] ?>" size="8" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $venta["tasa_venta"] ?>'; return false}" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<td>* Modalidad Prima<br>
		<select name="modalidad_prima" <?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?>style="background-color:#EAF1DD"<?php } else { ?> readonly<?php } ?>>
			<option value=""></option>
<?php

switch($venta["modalidad_prima"])
{
	case "ANT":	$selected_ant = " selected"; break;
	case "MDI":	$selected_mdi = " selected"; break;
	case "MDC":	$selected_mdc = " selected"; break;
}

?>









			<option value="ANT"<?php echo $selected_ant ?>>PRIMA ANTICIPADA</option>
			<option value="MDI"<?php echo $selected_mdi ?>>PRIMA MENSUAL DIFERENCIA EN INTERESES</option>
			<option value="MDC"<?php echo $selected_mdc ?>>PRIMA MENSUAL DIFERENCIA EN CUOTA</option>
		</select>
	</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<hr noshade size=1 width=350>
<?php

if ($venta["estado"] == "ALI")
{

?>
<iframe id="frm_busqueda" src="ventas_busqueda.php?tipo=<?php echo $_REQUEST["tipo"] ?>&ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>&action=ventas_actualizar" width="630px" height="84px"></iframe>
<?php

}

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT ved.*, si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.decision, si.id_subestado, si.estado_tesoreria, dbo.fn_total_recaudado(si.id_simulacion, 0) as total_recaudado 
		FROM ventas_detalle ved 
		INNER JOIN simulaciones si ON ved.id_simulacion = si.id_simulacion 
		INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
		WHERE ved.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
	
}
else
{
	$queryDB = "SELECT ved.*, si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, '".$label_viable."' as decision, NULL as id_subestado, 'CER' as estado_tesoreria, dbo.fn_total_recaudado(si.id_simulacion, 1) as total_recaudado from ventas_detalle".$sufijo." ved INNER JOIN simulaciones".$sufijo." si ON ved.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre WHERE ved.id_venta = '".$_REQUEST["id_venta"]."'";
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

$queryDB .= " order by abs(si.cedula), ved.id_ventadetalle";



$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));



if (sqlsrv_num_rows($rs))
{

?>
<input type="hidden" name="action" value="">
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
<input type="hidden" name="descripcion_busqueda3" value="<?php echo $_REQUEST["descripcion_busqueda3"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>
		Id
	</th>
	<th>Id Simulacion</th>
	<th>C&eacute;dula</th>
	
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>Estado</th>
	<?php if ($venta["estado"] == "VEN") { ?><th>Completo</th><?php } ?>
	<th>F. Primer Pago</th>
	<?php if ($venta["estado"] == "ALI") { ?><th>Cuota<br>Desde<br>Cartera</th><?php } ?>
	<th>Cuota Desde</th>
	<th>Cuota Hasta</th>
	<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $venta["estado"] == "ALI" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>Seleccionar<br><input type="checkbox" name="chkall" onClick="Chequear_Todos();"></th><?php } ?>
	<?php if ($venta["estado"] == "VEN") { ?><th><img src="../images/planpagos.png" title="Plan de Pagos"></th><?php } ?>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $venta["estado"] == "VEN" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/recomprar.png" title="Recomprar"></th><?php } ?>
</tr>
<?php

	$j = 1;
	
	$primer_registro = 1;
	$consecutivo = 1;
	
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
		
		switch ($fila["estado"])
		{
			case "EST":	$estado = "PARCIAL"; break;
			case "DES":	$estado = "DESEMBOLSADO"; break;
			case "CAN":	$estado = "CANCELADO"; break;
		}
		
		if ($fila["completo"] == "1")
			$completo = "SI";
		else
			$completo = "NO";
		
		$total_opcion_cuota += round($opcion_cuota);
		$total_valor_credito += round($fila["valor_credito"]);
		
		$cuota_desde = ceil($fila["total_recaudado"] / $opcion_cuota) + 1;
		
?>
<tr <?php echo $tr_class ?>>
<td> <?php echo $consecutivo; ?> </td>

	<td> <?php echo $fila["id_simulacion"]; ?> </td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td align="right"><?php echo $fila["tasa_interes"] ?></td>
	<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo $fila["plazo"] ?></td>
	<td align="center"><?php echo $estado ?></td>
	<?php if ($venta["estado"] == "VEN") { ?><td align="center"><a href="ventas_documentos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_ventadetalle=<?php echo $fila["id_ventadetalle"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo $completo ?></a></td><?php } ?>
	<td align="center"><input type="text" name="fecha_primer_pago<?php echo $fila["id_ventadetalle"] ?>" value="<?php echo $fila["fecha_primer_pago"] ?>" size="10" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fila["fecha_primer_pago"] ?>'; return false}<?php if ($primer_registro) { ?> else { ReplicarFecha(this.value); }<?php } ?>" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<?php if ($venta["estado"] == "ALI") { ?><td align="right"><?php echo $cuota_desde ?></td><?php } ?>
	<td align="center"><input type="text" id="cuota_desde<?php echo $fila["id_ventadetalle"] ?>" name="cuota_desde<?php echo $fila["id_ventadetalle"] ?>" value="<?php echo $fila["cuota_desde"] ?>" size="10" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $fila["cuota_desde"] ?>'; return false}" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<td align="center"><input type="text" name="cuota_hasta<?php echo $fila["id_ventadetalle"] ?>" value="<?php echo $fila["cuota_hasta"] ?>" size="10" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $fila["cuota_hasta"] ?>'; return false}" style="text-align:center;<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SOLOLECTURA"] != "1") { ?> background-color:#EAF1DD"<?php } else { ?>" readonly<?php } ?>></td>
	<?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $venta["estado"] == "ALI" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="hidden" name="plazoh" value="<?php echo $fila["plazo"] ?>"><input type="checkbox" name="chk<?php echo $fila["id_ventadetalle"] ?>" value="1"></td><?php } ?>
	<?php if ($venta["estado"] == "VEN") { ?><td align="center"><a href="planpagos.php?ext=<?php echo $_REQUEST["ext"] ?>&id_ventadetalle=<?php echo $fila["id_ventadetalle"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&back=ventas_actualizar&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/planpagos.png" title="Plan de Pagos"></a></td><?php } ?>
	<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $venta["estado"] == "VEN" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if (($fila["estado"] == "DES" || ($fila["estado"] == "EST" AND $fila["decision"] == $label_viable AND ($fila["id_subestado"] == $subestado_compras_desembolso OR $fila["id_subestado"] == $subestado_desembolso OR $fila["id_subestado"] == $subestado_desembolso_cliente OR $fila["id_subestado"] == $subestado_desembolso_pdte_bloqueo) AND $fila["estado_tesoreria"] == "PAR")) && !$fila["recomprado"]) { ?><input type="checkbox" name="recomp<?php echo $fila["id_ventadetalle"] ?>" value="1"><?php } else if ($fila["recomprado"]) { ?><img src="../images/recomprar.png" title="Recomprado"><?php } else { ?>&nbsp;<?php } ?></td><?php } ?>
</tr>
<?php
$consecutivo = $consecutivo +1;

		if ($primer_registro)
			$primer_registro = 0;
		
		$j++;
	}
	
?>
<tr class="tr_bold">
	<td colspan="4">&nbsp;</td>
	<td align="right"><b><?php echo number_format($total_opcion_cuota, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_valor_credito, 0) ?></b></td>
	<td colspan="9">&nbsp;</td>
</tr>
</table>
<br>
<p align="center"><?php if ($_SESSION["S_TIPO"] != "CONTABILIDAD" && $venta["estado"] == "ALI" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><input type="button" value="Actualizar / Eliminar" onClick="document.formato3.action.value='actualizar'; chequeo_forma();">&nbsp;&nbsp;<input type="button" value="Dividir" onClick="document.formato3.action.value='dividir'; chequeo_forma();"><?php } ?><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") && $venta["estado"] == "VEN" && $_SESSION["S_SOLOLECTURA"] != "1") { ?><input type="button" value="Recomprar" onClick="document.formato3.action.value='recomprar'; chequeo_forma();"><?php } ?></p>
<!-- SweetAlert2 -->
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
</form>
<?php

}


else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
