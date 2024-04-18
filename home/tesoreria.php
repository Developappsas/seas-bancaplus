<?php
include ('../functions.php');



$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA"){
	exit;
}

if ($_SESSION["S_TIPO"] == "CONTABILIDAD"){
	$subestados_tesoreria .= ",'78'";
}

if ($_SESSION["S_TIPO"] == "TESORERIA")
{
	$queryDB = "SELECT count(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_subestado IN ('".$subestado_confirmado."')";
	
	if ($_SESSION["S_SECTOR"]){
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL"){
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}

	$rs_count_confirmados = sqlsrv_query($link,$queryDB);
	
	$count_confirmados = sqlsrv_fetch_array($rs_count_confirmados);
	
	if ($count_confirmados["c"])
	{
		$rs_nombre_confirmado = sqlsrv_query($link,"SELECT nombre from subestados where id_subestado IN ('".$subestado_confirmado."')");
		
		$nombre_confirmado = sqlsrv_fetch_array($rs_nombre_confirmado);
		
		$mensaje_tesoreria = "Actualmente hay ".$count_confirmados["c"]." credito(s) en subestado ".$nombre_confirmado["nombre"];
	}
	
	$queryDB = "SELECT count(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_subestado IN ('".$subestado_desembolso_cliente."')";
	
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

	$rs_count_desembolso_cliente = sqlsrv_query($link,$queryDB);
	
	$count_desembolso_cliente = sqlsrv_fetch_array($rs_count_desembolso_cliente);
	
	if ($count_desembolso_cliente["c"]){

		$rs_nombre_desembolso_cliente = sqlsrv_query($link,"SELECT nombre from subestados where id_subestado IN ('".$subestado_desembolso_cliente."')");
		
		$nombre_desembolso_cliente = sqlsrv_fetch_array($rs_nombre_desembolso_cliente);
		
		if ($mensaje_tesoreria)
			$mensaje_tesoreria .= " y ".$count_desembolso_cliente["c"]." credito(s) en subestado ".$nombre_desembolso_cliente["nombre"];
		else
			$mensaje_tesoreria = "Actualmente hay ".$count_desembolso_cliente["c"]." credito(s) en subestado ".$nombre_desembolso_cliente["nombre"];
	}
	
	if ($mensaje_tesoreria)
		echo "<script>alert('".$mensaje_tesoreria."')</script>";
}


?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>

<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/demo.min.css" rel="stylesheet"/>
	<link href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
	<link href="../plugins/DataTables/datatables.min.css?v=4" rel="stylesheet">
	<link href="../plugins/toastr/toastr.min.css" rel="stylesheet">

	<div class="col-12">
		<div class="container-xl">
			<div class="row">

				<div class="col-2 mb-3">
					<span class="form-selectgroup-label" id="input_cargar_compra_cartera">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
							<polyline points="7 9 12 4 17 9"></polyline>
							<line x1="12" y1="4" x2="12" y2="16"></line>
						</svg>
						Soporte Compra Cartera
					</span>
				</div>
				<?php
				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CONTABILIDAD") 
				{
					?>
					<div class="col-2 mb-3">
						<span class="form-selectgroup-label" id="input_cargar_desembolso_cliente">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
								<polyline points="7 9 12 4 17 9"></polyline>
								<line x1="12" y1="4" x2="12" y2="16"></line>
							</svg>
							Soporte Desembolso
						</span>
					</div>
					<?php
				}

				?>
				
			</div>
			<div class="col-2 mb-3">
					<span class="form-selectgroup-label" data-bs-toggle="modal" data-bs-target="#modalCreditosFacturados">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
							<polyline points="7 9 12 4 17 9"></polyline>
							<line x1="12" y1="4" x2="12" y2="16"></line>
						</svg>
						Creditos Facturados
					</span>
				</div>
            <div class="card" id="divlistaClientes" style="display:none;">
                <table class="table table-responsive hover" id="listaClientes">						
				</table>                                
            </div>
        </div>
	</div>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Tesorer&iacute;a</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="tesoreria.php">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
<?php
if (!$_SESSION["S_SECTOR"]){
?>
			<td valign="bottom">Sector<br>
				<select name="sectorb">
					<option value=""></option>
					<option value="PUBLICO">PUBLICO</option>
					<option value="PRIVADO">PRIVADO</option>
				</select>&nbsp;
			</td>
<?php
}
?>
			<td valign="bottom">Pagadur&iacute;a<br>
				<select name="pagaduriab">
					<option value=""></option>
<?php

$queryDB = "SELECT nombre as pagaduria from pagadurias where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = sqlsrv_query($link,$queryDB);


while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
				</select>&nbsp;
			</td>
			<td valign="bottom">Estado<br>
				<select name="estadob">
					<option value=""></option>
					<option value="ABI">ABIERTO</option>
					<option value="PAR">PARCIAL</option>
					<option value="CER">CERRADO</option>
				</select>&nbsp;
			</td>
<?php

if ($_SESSION["FUNC_SUBESTADOS"])
{

?>
			<td valign="bottom">Subestado<br>
				<select name="id_subestadob" style="width:110px">
					<option value=""></option>
<?php
	$queryDB = "select id_subestado, nombre from subestados where estado = '1' AND id_subestado IN (".$subestados_tesoreria.") order by nombre";
	
	$rs1 = sqlsrv_query($link,$queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_subestado"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
				</select>&nbsp;
			</td>
<?php

}

?>
			<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</form>
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT si.*, ag.fecha_proximo_vencimiento, se.nombre as nombre_subestado from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario LEFT JOIN (select ag.id_simulacion, MIN(ag.fecha_vencimiento) as fecha_proximo_vencimiento from agenda ag INNER JOIN tesoreria_cc tcc ON ag.id_simulacion = tcc.id_simulacion AND ag.consecutivo = tcc.consecutivo where tcc.pagada = '0' group by ag.id_simulacion) ag ON ag.id_simulacion = si.id_simulacion LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

$queryDB_count = "SELECT COUNT(*) as c from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo in  (0,1)";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
	
	$queryDB_count .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB_count .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB_count .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo in (0,1)";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB_count .= " AND si.telemercadeo = '1'";
}

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
	
	$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
}

if ($_REQUEST["sectorb"])
{
	$sectorb = $_REQUEST["sectorb"];
	
	$queryDB .= " AND pa.sector = '".$sectorb."'";
	
	$queryDB_count .= " AND pa.sector = '".$sectorb."'";
}

if ($_REQUEST["pagaduriab"])
{
	$pagaduriab = $_REQUEST["pagaduriab"];
	
	$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
	
	$queryDB_count .= " AND si.pagaduria = '".$pagaduriab."'";
}

if ($_REQUEST["estadob"])
{
	$estadob = $_REQUEST["estadob"];
	
	$queryDB .= " AND si.estado_tesoreria = '".$estadob."'";
	
	$queryDB_count .= " AND si.estado_tesoreria = '".$estadob."'";
}
else if (!$_REQUEST["buscar"])
{
	$queryDB .= " AND si.estado_tesoreria != 'CER'";
	
	$queryDB_count .= " AND si.estado_tesoreria != 'CER'";
}

if ($_REQUEST["id_subestadob"])
{
	$id_subestadob = $_REQUEST["id_subestadob"];
	
	$queryDB .= " AND si.id_subestado = '".$id_subestadob."'";
	
	$queryDB_count .= " AND si.id_subestado = '".$id_subestadob."'";
}

$queryDB .= " order by CASE WHEN ag.fecha_proximo_vencimiento IS NOT NULL THEN 1 ELSE 2 END, ag.fecha_proximo_vencimiento, si.fecha_tesoreria DESC, si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";



if($_REQUEST["buscar"]){
	$rs = sqlsrv_query($link,$queryDB);
	$rs_count = sqlsrv_query($link,$queryDB_count);
	$fila_count = sqlsrv_fetch_array($rs_count);
	$cuantos = $fila_count["c"];
}else{
	$cuantos = 0;
}




if ($cuantos){
	if ($cuantos > $x_en_x){
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";

		$i = 1;
		$final = 0;
		
		while ($final < $cuantos){
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos){
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"]){
				echo " <a href=\"tesoreria.php?descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&estadob=".$estadob."&id_subestadob=".$id_subestadob."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
			}
			else{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page){
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"tesoreria.php?descripcion_busqueda=".$descripcion_busqueda."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&estadob=".$estadob."&id_subestadob=".$id_subestadob."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="tesoreria.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
<input type="hidden" name="id_subestadob" value="<?php echo $id_subestadob ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="95%">
<tr>
	<th rowspan="2">C&eacute;dula</th>
	<th rowspan="2">Nombre</th>
	<th rowspan="2">Pagadur&iacute;a</th>
	<th rowspan="2">Vr Solicitado</th>
	<th rowspan="2">Retanqs.</th>
	<th rowspan="2">Comp. Cartera</th>
	<th rowspan="2">Desemb. Cliente</th>
	<th rowspan="2">Rete. Cuota</th>
	<th colspan="4">Giros Realiz</th>
	<th colspan="4">Saldo a Girar</th>	
	<th rowspan="2">Pr&oacute;ximo<br>Vcto.</th>
	<th rowspan="2">Estado</th>
	<th rowspan="2">Subestado</th>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") { ?><th rowspan="2"><img src="../images/planpagos.png" title="Plan de Pagos"></th><?php } ?>
</tr>
<tr>
	<th>Retanqs.</th>
	<th>Comp. Cartera</th>
	<th>Desemb. Cliente</th>
	<th>Rete. Cuota</th>
	<th>Retanqs.</th>
	<th>Comp. Cartera</th>
	<th>Desemb. Cliente</th>
	<th>Rete. Cuota</th>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) != 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"];
						break;
			case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"];
						break;
			case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"];
						break;
			case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"];
						break;
		}
		
		$compras_cartera = 0;
		
		$queryDB = "SELECT SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL or (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
		
		$rs1 = sqlsrv_query($link,$queryDB);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		if ($fila1["s"])
			$compras_cartera = $fila1["s"];
		
		if ($fila["opcion_credito"] == "CLI"){
			$fila["retanqueo_total"] = 0;
		}
			
		
		$intereses_anticipados = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00);

		$asesoria_financiera = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00, 0);
		$asesoria_financiera_base = $asesoria_financiera;
		$iva = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00, 0);
		$iva_porc = $fila["descuento3"];
		$valor_servicio_nube = 0;
		$asesoria_financiera_nueva = 0;

		if($fila["servicio_nube"]){
			$asesoria_financiera = $fila["descuento2_valor"];
			$valor_servicio_nube = $fila["descuento8_valor"];
			$asesoria_financiera_nueva = $fila["descuento9_valor"];
			$iva = $fila["descuento10_valor"];

			if($fila["descuento10_valor"] > 0){
				$iva_porc = round($iva / ($fila["valor_credito"] - $fila["retanqueo_total"]) * 1000, 2);
			}else{
				$iva_porc = 0;
			}
		}
		
		if($fila["servicio_nube"]){
			$desembolso_cliente = $fila["desembolso_cliente"];
		}else{
			$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];
		}
		
		if ($fila["tipo_producto"] == "1"){
			if ($fila["fidelizacion"]){
				$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			}
			else{
				$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			}
		}
		
		$descuentos_adicionales = sqlsrv_query($link,"select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
			$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
		}
		
		if ($fila["bloqueo_cuota"]){
			$retenciones_cuota = $fila["bloqueo_cuota_valor"];
		}
		else{
			$rs1 = sqlsrv_query($link,"SELECT SUM(cuota_retenida) as s from tesoreria_cc where id_simulacion = '".$fila["id_simulacion"]."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$retenciones_cuota = $fila1["s"];
		}
		
		$rs1 = sqlsrv_query($link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'DSC' and fecha_giro IS NOT NULL");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_dsc = $fila1["s"];
		
		$saldo_girar_dsc = round($desembolso_cliente) - $retenciones_cuota - $giros_realizados_dsc;
		
		$rs1 = sqlsrv_query($link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'CCA' and fecha_giro IS NOT NULL");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_cca = $fila1["s"];
		
		$saldo_girar_cca = round($compras_cartera) - $giros_realizados_cca;
		
		$rs1 = sqlsrv_query( $link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'GCR' and fecha_giro IS NOT NULL");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_gcr = $fila1["s"];
		
		$saldo_girar_gcr = round($retenciones_cuota) - $giros_realizados_gcr;
		
		$rs1 = sqlsrv_query($link,"select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'RET' and fecha_giro IS NOT NULL");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_ret = $fila1["s"];
		
		$saldo_girar_ret = round($fila["retanqueo_total"]) - $giros_realizados_ret;
		
		$estado = "";
		
		switch ($fila["estado_tesoreria"])
		{
			case "ABI":	$estado = "ABIERTO"; break;
			case "PAR":	$estado = "PARCIAL"; break;
			case "CER":	$estado = "CERRADO"; break;
		}
		
		$total_opcion_desembolso += round($opcion_desembolso);
		$total_retanqueos += round($fila["retanqueo_total"]);
		$total_compras_cartera += round($compras_cartera);
		$total_desembolso_cliente += round($desembolso_cliente);
		$total_retenciones_cuota += round($retenciones_cuota);
		$total_giros_realizados_ret += round($giros_realizados_ret);
		$total_giros_realizados_cca += round($giros_realizados_cca);
		$total_giros_realizados_dsc += round($giros_realizados_dsc);
		$total_giros_realizados_gcr += round($giros_realizados_gcr);
		$total_saldo_girar_ret += round($saldo_girar_ret);
		$total_saldo_girar_cca += round($saldo_girar_cca);
		$total_saldo_girar_dsc += round($saldo_girar_dsc);
		$total_saldo_girar_gcr += round($saldo_girar_gcr);
		
?>
<tr <?php echo $tr_class ?>>
	<td><a href="tesoreria_actualizar.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&id_subestadob=<?php echo $id_subestadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo number_format($opcion_desembolso, 0) ?></td>
	<td align="right"><?php echo number_format($fila["retanqueo_total"], 0) ?></td>
	<td align="right"><?php echo number_format($compras_cartera, 0) ?></td>
	<td align="right"><?php echo number_format($desembolso_cliente, 0) ?></td>
	<td align="right"><?php echo number_format($retenciones_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($giros_realizados_ret, 0) ?></td>
	<td align="right"><?php echo number_format($giros_realizados_cca, 0) ?></td>
	<td align="right"><?php echo number_format($giros_realizados_dsc, 0) ?></td>
	<td align="right"><?php echo number_format($giros_realizados_gcr, 0) ?></td>
	<td align="right"><?php echo number_format($saldo_girar_ret, 0) ?></td>
	<td align="right"><?php echo number_format($saldo_girar_cca, 0) ?></td>
	<td align="right"><?php echo number_format($saldo_girar_dsc, 0) ?></td>
	<td align="right"><?php echo number_format($saldo_girar_gcr, 0) ?></td>
	<td style="white-space:nowrap;"><?php echo $fila["fecha_proximo_vencimiento"] ?></td>
	<td align="center"><?php echo $estado ?></td>
	<td><?php echo utf8_decode($fila["nombre_subestado"]) ?></td>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") { ?><td align="center"><a href="planpagos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&id_subestadob=<?php echo $id_subestadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&back=tesoreria&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/planpagos.png" title="Plan de Pagos"></a></td><?php } ?>
</tr>
<?php

		$j++;
	}
	
?>
<tr class="tr_bold">
	<td colspan="3">&nbsp;</td>
	<td align="right"><b><?php echo number_format($total_opcion_desembolso, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_retanqueos, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_compras_cartera, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_desembolso_cliente, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_retenciones_cuota, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_giros_realizados_ret, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_giros_realizados_cca, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_giros_realizados_dsc, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_giros_realizados_gcr, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_saldo_girar_ret, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_saldo_girar_cca, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_saldo_girar_dsc, 0) ?></b></td>
	<td align="right"><b><?php echo number_format($total_saldo_girar_gcr, 0) ?></b></td>
	<td colspan="4">&nbsp;</td>
</tr>
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

<div class="modal modal-blur fade modal-tabler" id="modalCreditosFacturados" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">CREDITOS FACTURADOS</h5>
				<input type="hidden" id="idConveniosModal">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-3">
						<label class="form-label">Facturado</label>
						<select type="text" class="form-control" id="facturadoModalCreditosFacturados">
							<option value='2'>SELECCIONE UNA OPCION</option>
							<option value='1'>SI</option>
							<option value='0'>NO</option>
						</select>
					</div>

		
					<div class="col-lg-9">
						<label class="form-label">Soporte</label>
						<input type="file" class="form-control" id="soporteModalCreditosFacturados" name="soporteModalCreditosFacturados" />
					</div>

				
				</div>

		
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
					CANCELAR
				</a>
				<a name="add" id="btnCargarCreditosFacturados"  return false;" class="btn btn-primary ms-auto">
					<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
					<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<line x1="12" y1="5" x2="12" y2="19" />
						<line x1="5" y1="12" x2="19" y2="12" />
					</svg>
					GUARDAR
				</a>
			</div>
		</div>
	</div>
</div>
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>	
	<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
	<script type="text/javascript" src="../plugins/toastr/toastr.min.js"></script>
	<script type="text/javascript" src="../js/tesoreria/tesoreria.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script> 

	<!-- Tabler Core -->
	<script src="../plugins/tabler/libs/apexcharts/dist/apexcharts.min.js"></script>
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>
	
<?php 
	include("bottom.php");
?>