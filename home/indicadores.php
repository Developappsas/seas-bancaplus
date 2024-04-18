<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_INDICADORES"] || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD")
{
	header('Location: https://seas.kredit.com.co/');
	exit;
}
$link = conectar_utf();

$parametros = sqlsrv_query($link,"SELECT * from parametros where codigo IN ('MMEFA', 'MPRCO', 'MPRFA')");

$fila = sqlsrv_fetch_array($parametros);

$meta = $fila["valor"];

$fila = sqlsrv_fetch_array($parametros);

$mprco = $fila["valor"];

$mprco = explode("-", $mprco);

$fila = sqlsrv_fetch_array($parametros);

$mprfa = $fila["valor"];

$mprfa = explode("-", $mprfa);

?>
<?php include("top.php"); ?>
<meta http-equiv="refresh" content="300">

<script type="text/javascript">
	function disableIE() {
	    if (document.all) {
	        return false;
	    }
	}
	function disableNS(e) {
	    if (document.layers || (document.getElementById && !document.all)) {
	        if (e.which==2 || e.which==3) {
	            return false;
	        }
	    }
	}
	if (document.layers) {
	    document.captureEvents(Event.MOUSEDOWN);
	    document.onmousedown = disableNS;
	} 
	else {
	    document.onmouseup = disableNS;
	    document.oncontextmenu = disableIE;
	}
	document.oncontextmenu=new Function("return false");

	$(document).keydown(function (event) {
	    if (event.keyCode == 123) { // Prevent F12
	        return false;
	    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
	        return false;
	    }
	});
</script>

<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet"/>
<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet"/>
<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet"/>
<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet"/>

<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Indicadores 1</b><br><br></center></td>
</tr>
</table>

<style type="text/css">
	iframe {
		margin:0;
		padding:0;
		height:1000px;
	}
	iframe {
		display:block;
		width:100%;
		border:none;
	}
	.tab-pane {
		padding: 20px !important;
	}
</style>

<div class="card-table table-responsive">
	<ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
		<li class="nav-item" role="presentation" id="menu_tab_1">
			<a href="#tab_1" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab"><b>PÁGINA INICIAL</b></a>
		</li>

		<?php
		
		$rs1 = sqlsrv_query($link, "SELECT b.* FROM usuarios_reportes a RIGHT JOIN reportes b ON b.id = a.id_reporte  and b.tipo_reporte = 2  WHERE a.id_usuario = ".$_SESSION['S_IDUSUARIO'], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));



		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){ ?>
			<li class="nav-item" role="presentation" id="menu_tab_2">
				<a href="#tab_reporte<?=$fila1["id"]?>" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"><b><?=$fila1['descripcion']?></b></a>
			</li>
			<?php
		}

	
	
		?>
	</ul>

	<div class="tab-content">

		<div class="tab-pane active" id="tab_1" role="tabpanel">
			
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>

			<form name="formato2" method="post" action="indicadores.php">
				<table>
					<tr>
						<td>
							<div class="box1 clearfix">
							<table border="0" cellspacing=1 cellpadding=2>
							<tr>
								<td valign="bottom">Unidad de Negocio<br>
									<select name="unidadnegociob" onChange="window.location.href='indicadores.php?unidadnegociob='+this.value;">
										<option value=""></option>
										<?php
										$queryDB = "select id_unidad, nombre from unidades_negocio where 1 = 1";
										$queryDB .= " AND id_unidad IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
										$queryDB .= " order by id_unidad";
										
										$rs1 = sqlsrv_query($link, $queryDB);


										while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
											if ($fila1["id_unidad"] == $_REQUEST["unidadnegociob"])
												$selected = " selected";
											else
												$selected = "";
											
											echo "<option value=\"".$fila1["id_unidad"]."\"".$selected.">".utf8_decode($fila1["nombre"])."</option>\n";
										}
										?>
									</select>&nbsp;&nbsp;&nbsp;

								</td
								>
								<?php if (!$_SESSION["S_SECTOR"]){ ?>
								<td valign="bottom">Sector<br>
									<select name="sectorb" onChange="window.location.href='indicadores.php?sectorb='+this.value;">
										<option value=""></option>
										<option value="PUBLICO"<?php if ($_REQUEST["sectorb"] == "PUBLICO") { echo " selected"; } ?>>PUBLICO</option>
										<option value="PRIVADO"<?php if ($_REQUEST["sectorb"] == "PRIVADO") { echo " selected"; } ?>>PRIVADO</option>
									</select>
								</td>
								<?php } ?>
							</tr>
							</table>
							</div>
						</td>
					</tr>
				</table>
			</form>

			<table border="0" cellspacing=1 cellpadding=2>
				<tr>
					<td valign="top" width="950">
					<?php

					$queryDB = "SELECT categoria, COUNT(*) as c, SUM(valor_credito) as s from vwgestioncomercial2 where id_simulacion IS NOT NULL";
					$queryDB_suma = "SELECT SUM(valor_credito) as s from vwgestioncomercial2 where id_simulacion IS NOT NULL";

					if ($_SESSION["S_SECTOR"]){
						$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
						$queryDB_suma .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
					}

					if ($_SESSION["S_TIPO"] == "COMERCIAL"){
						$queryDB .= " AND id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
						$queryDB_suma .= " AND id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
					}
					else{
						$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
						$queryDB_suma .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					}

					if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION"){
						$queryDB .= " AND id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
							$queryDB .= " AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
						
						if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
							$queryDB .= " AND (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
							$queryDB .= " AND telemercadeo = '1'";
						
						$queryDB_suma .= " AND id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA")
							$queryDB_suma .= " AND NOT (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
							$queryDB_suma .= " AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
							$queryDB_suma .= " AND NOT (freelance = '1' OR outsourcing = '1')";
						
						if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
							$queryDB_suma .= " AND (freelance = '1' OR outsourcing = '1') AND telemercadeo in ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
							$queryDB_suma .= " AND telemercadeo = '1'";
					}

					$_REQUEST["fecha_inicialbd"] = "01";
					$_REQUEST["fecha_inicialbm"] = $mprco[1];
					$_REQUEST["fecha_inicialba"] = $mprco[0];

					if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
						$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
						$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
						$fecha_inicialba = $_REQUEST["fecha_inicialba"];
						
						//	$queryDB .= " AND (left(fecha_estudio,7) = '".$fecha_inicialba."-".$fecha_inicialbm."' OR left(fecha_estudio_mas_un_mes,7) = '".$fecha_inicialba."-".$fecha_inicialbm."')";
						//	$queryDB_suma .= " AND (left(fecha_estudio,7) = '".$fecha_inicialba."-".$fecha_inicialbm."' OR left(fecha_estudio_mas_un_mes,7) = '".$fecha_inicialba."-".$fecha_inicialbm."')";
					}

					$_REQUEST["fecha_finalbd"] = date("d");
					$_REQUEST["fecha_finalbm"] = $mprco[1];
					$_REQUEST["fecha_finalba"] = $mprco[0];

					if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
						$fecha_finalbd = $_REQUEST["fecha_finalbd"];
						$fecha_finalbm = $_REQUEST["fecha_finalbm"];
						$fecha_finalba = $_REQUEST["fecha_finalba"];
						//	$queryDB .= " AND (left(fecha_estudio,7) = '".$fecha_finalba."-".$fecha_finalbm."' OR left(fecha_estudio_mas_un_mes,7) = '".$fecha_finalba."-".$fecha_finalbm."')";
						//	$queryDB_suma .= " AND (left(fecha_estudio,7) = '".$fecha_finalba."-".$fecha_finalbm."' OR left(fecha_estudio_mas_un_mes,7) = '".$fecha_finalba."-".$fecha_finalbm."')";
					}

					if ($_REQUEST["unidadnegociob"]){
						$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
						$queryDB_suma .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
					}

					if ($_REQUEST["sectorb"]){
						$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
						$queryDB_suma .= " AND sector = '".$_REQUEST["sectorb"]."'";
					}

					$queryDB .= " group by categoria order by categoria";
	

					$rs = sqlsrv_query( $link,$queryDB);
					if ($rs == false) {
						if( ($errors = sqlsrv_errors() ) != null) {
							foreach( $errors as $error ) {
								echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
								echo "code: ".$error[ 'code']."<br />";
								echo "message: ".$error[ 'message']."<br />";
								exit($queryDB);
							}
						}
					}
					$rs_suma = sqlsrv_query($link,$queryDB_suma);
					if ($rs_suma == false) {
						if( ($errors = sqlsrv_errors() ) != null) {
							foreach( $errors as $error ) {
								echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
								echo "code: ".$error[ 'code']."<br />";
								echo "message: ".$error[ 'message']."<br />";
								exit($queryDB_suma);
							}
						}
					}
					$fila_suma = sqlsrv_fetch_array($rs_suma);

					$suma = $fila_suma["s"];
					?>
					<script type="text/javascript">
						google.load("visualization", "1", {packages:["corechart"]});
						google.setOnLoadCallback(drawChart);
						
						function drawChart() {
							var data = google.visualization.arrayToDataTable([
								['Estado', 'Valor'], 
								<?php
								while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
									if ($fila["categoria"] != "  Ingresados")
										echo "['".trim(strtoupper($fila["categoria"]))."', ".$fila["s"]."],";
								}
								?>
								]);
								
								var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
								formatter.format(data, 1);
								
				   				var options = {
									title: 'COMERCIAL',
									is3D: true,
								};
								
								var chart = new google.visualization.PieChart(document.getElementById('gestioncomercial'));
								chart.draw(data, options);
							}
						</script>
						<div id="gestioncomercial" style="width:530px; float:left;"></div>
						
						<?php if (sqlsrv_num_rows($rs)){ ?>
							<div style="width:380px; float:left;">
								<br>
								<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="380">
								<tr>
									<th>&nbsp;</th>
									<th width="100">$</th>
									<th width="30">#</th>
									<?php if ($suma) { ?><th width="40">%</th><?php } ?>
								</tr>
								<?php
									sqlsrv_fetch($rs, SQLSRV_SCROLL_FIRST);
									$cuantos = 0;
									
									while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
										$cuantos += $fila["c"];  ?>
								<tr>
									<td><?php echo trim(strtoupper($fila["categoria"])) ?></td>
									<td align="right"><?php echo number_format($fila["s"], 0) ?></td>
									<td align="right"><?php echo number_format($fila["c"], 0) ?></td>
									<?php if ($suma) { ?><td align="right"><?php echo number_format($fila["s"] / $suma * 100.00, 2) ?></td><?php } ?>
								</tr>
									<?php } ?>
								<tr class="tr_bold">
									<td><b>TOTALES</b></td>
									<td align="right"><b><?php echo number_format($suma, 0) ?></b></td>
									<td align="right"><b><?php echo number_format($cuantos, 0) ?></b></td>
									<?php if ($suma) { ?><td align="right"><b>100.00</b></td><?php } ?>
								</tr>
								</table>
							</div>
						<?php } ?>
				 	</td>
				</tr>
				<tr>
					<td valign="top" width="950">
					<?php

					$queryDB = "select categoria, COUNT(*) as c, SUM(valor_credito) as s from vwproduccion_2 where id_simulacion IS NOT NULL";

					$queryDB_suma = "select SUM(valor_credito) as s from vwproduccion_2 where id_simulacion IS NOT NULL";

					if ($_SESSION["S_SECTOR"]){
						$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";	
						$queryDB_suma .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
					}

					if ($_SESSION["S_TIPO"] == "COMERCIAL"){
						$queryDB .= " AND id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
						$queryDB_suma .= " AND id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
					}
					else{
						$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
						$queryDB_suma .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					}

					if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION"){

						$queryDB .= " AND id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
							$queryDB .= " AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
						
						if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
							$queryDB .= " AND (freelance = '1' OR outsourcing = '1') AND telemercadeo in ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
							$queryDB .= " AND telemercadeo = '1'";
						
						$queryDB_suma .= " AND id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA")
							$queryDB_suma .= " AND NOT (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
							$queryDB_suma .= " AND telemercadeo = '0'";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
							$queryDB_suma .= " AND NOT (freelance = '1' OR outsourcing = '1')";
						
						if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
							$queryDB_suma .= " AND (freelance = '1' OR outsourcing = '1') AND telemercadeo in ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
							$queryDB_suma .= " AND telemercadeo = '1'";
					}

					$_REQUEST["fecha_inicialbd"] = "01";
					$_REQUEST["fecha_inicialbm"] = $mprfa[1];
					$_REQUEST["fecha_inicialba"] = $mprfa[0];

					if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
						$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
						$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
						$fecha_inicialba = $_REQUEST["fecha_inicialba"];
						$queryDB .= " AND left(fecha_cartera,7) = '".$fecha_inicialba."-".$fecha_inicialbm."'";
						$queryDB_suma .= " AND left(fecha_cartera,7) = '".$fecha_inicialba."-".$fecha_inicialbm."'";
					}

					$_REQUEST["fecha_finalbd"] = date("d");
					$_REQUEST["fecha_finalbm"] = $mprfa[1];
					$_REQUEST["fecha_finalba"] = $mprfa[0];

					if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
						$fecha_finalbd = $_REQUEST["fecha_finalbd"];	
						$fecha_finalbm = $_REQUEST["fecha_finalbm"];	
						$fecha_finalba = $_REQUEST["fecha_finalba"];	

						$queryDB .= " AND left(fecha_cartera,7) = '".$fecha_finalba."-".$fecha_finalbm."'";	
						$queryDB_suma .= " AND left(fecha_cartera,7) = '".$fecha_finalba."-".$fecha_finalbm."'";
					}

					if ($_REQUEST["unidadnegociob"]){
						$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";	
						$queryDB_suma .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
					}

					if ($_REQUEST["sectorb"]){
						$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";	
						$queryDB_suma .= " AND sector = '".$_REQUEST["sectorb"]."'";
					}

					$queryDB .= " group by categoria order by categoria";
					$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					$rs_suma = sqlsrv_query( $link,$queryDB_suma);
					$rs_suma = sqlsrv_query($link,$queryDB_suma);

					$fila_suma = sqlsrv_fetch_array($rs_suma);
					$suma = $fila_suma["s"];
					?>
						<script type="text/javascript">
							google.load("visualization", "1", {packages:["corechart"]});
							google.setOnLoadCallback(drawChart);
							
							function drawChart() {
								var data = google.visualization.arrayToDataTable([
									['Estado', 'Valor'],
									<?php
									while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
										echo "['".trim(strtoupper($fila["categoria"]))."', ".$fila["s"]."],";
									}
									?>
								]);
								
								var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
								formatter.format(data, 1);
								
								var options = {
									title: 'FABRICA',
									is3D: true,
								};
								
								var chart = new google.visualization.PieChart(document.getElementById('produccion'));
								chart.draw(data, options);
							}
						</script>
						<div id="produccion" style="width:530px; float:left;"></div>
						<?php
						if (sqlsrv_num_rows($rs)){ ?>
								<div style="width:380px; float:left;">
									<br>
									<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="380">
									<tr>
										<th>&nbsp;</th>
										<th width="100">$</th>
										<th width="30">#</th>
										<th width="40">%</th>
									</tr>
									<?php
										sqlsrv_fetch($rs,SQLSRV_SCROLL_FIRST);
										$cuantos = 0;
										while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
											$cuantos += $fila["c"];							
									?>
									<tr>
										<td><?php echo trim(strtoupper($fila["categoria"])) ?></td>
										<td align="right"><?php echo number_format($fila["s"], 0) ?></td>
										<td align="right"><?php echo number_format($fila["c"], 0) ?></td>
										<td align="right"><?php echo number_format($fila["s"] / $suma * 100.00, 2) ?></td>
									</tr>
									<?php } ?>
									<tr class="tr_bold">
										<td><b>TOTALES</b></td>
										<td align="right"><b><?php echo number_format($suma, 0) ?></b></td>
										<td align="right"><b><?php echo number_format($cuantos, 0) ?></b></td>
										<td align="right"><b>100.00</b></td>
									</tr>
									</table>
								</div>
						<?php } ?>
				 	</td>
				</tr>
				<tr>
					<td valign="top" width="950">
					<?php
					$queryDB = "select COUNT(*) as c, SUM(valor_credito) as s from vwproduccion where categoria IN ('".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."', 'DESEMBOLSADOS')";

					if ($_SESSION["S_SECTOR"]){
						$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
					}

					if ($_SESSION["S_TIPO"] == "COMERCIAL"){
						$queryDB .= " AND id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
					}
					else{
						$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					}

					if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION"){
						$queryDB .= " AND id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1') AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
							$queryDB .= " AND telemercadeo IN ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
							$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
						
						if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
							$queryDB .= " AND (freelance = '1' OR outsourcing = '1') AND telemercadeo in ('0','1')";
						
						if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
							$queryDB .= " AND telemercadeo = '1'";
					}

					$_REQUEST["fecha_inicialbd"] = "01";
					$_REQUEST["fecha_inicialbm"] = $mprfa[1];
					$_REQUEST["fecha_inicialba"] = $mprfa[0];

					if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
						$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
						$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
						$fecha_inicialba = $_REQUEST["fecha_inicialba"];

						$queryDB .= " AND left(fecha_cartera,7) = '".$fecha_inicialba."-".$fecha_inicialbm."'";
					}

					$_REQUEST["fecha_finalbd"] = date("d");
					$_REQUEST["fecha_finalbm"] = $mprfa[1];
					$_REQUEST["fecha_finalba"] = $mprfa[0];

					if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
						$fecha_finalbd = $_REQUEST["fecha_finalbd"];
						$fecha_finalbm = $_REQUEST["fecha_finalbm"];
						$fecha_finalba = $_REQUEST["fecha_finalba"];

						$queryDB .= " AND left(fecha_cartera,7) = '".$fecha_finalba."-".$fecha_finalbm."'";
					}

					if ($_REQUEST["unidadnegociob"]){
						$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
					}

					if ($_REQUEST["sectorb"]){
						$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
					}

					$rs = sqlsrv_query($link,$queryDB);
					$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

					$cuantos = $fila["c"];
					$suma = $fila["s"];

					if (!$suma){
						$suma = 0;
					}

					/*$queryDB = "select SUM(meta_mes) as s from usuarios where id_usuario IS NOT NULL";

					if ($_SESSION["S_TIPO"] == "COMERCIAL")
					{
						$queryDB .= " AND id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
					}

					if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
					{
						$queryDB .= " AND id_usuario IN (SELECT `uno`.`id_usuario` FROM `oficinas_usuarios` `uno` WHERE `uno`.`id_oficina` IN (SELECT `dos`.`id_oficina` FROM `oficinas_usuarios` `dos` WHERE `dos`.`id_usuario` = '".$_SESSION["S_IDUSUARIO"]."'))";
					}

					$rs = sqlsrv_query($link,$queryDB);

					$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

					$meta = $fila["s"];*/

					if ($meta){ ?>
						<script type="text/javascript">
							google.load("visualization", "1", {packages:["corechart"]});
							google.setOnLoadCallback(drawChart);
							
							function drawChart() {
								var data = google.visualization.arrayToDataTable([
									['Mes', 'META', 'DESEMBOLSADO'],
									['<?php echo $nombre_meses[intval($mprfa[1])]."/".$mprfa[0]; ?>', <?php echo $meta ?>, <?php echo $suma ?>],
								]);
								
								var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
								formatter.format(data, 1);
								formatter.format(data, 2);
								
								var options = {
									title: 'CUMPLIMIENTO',
									legend: { position: 'bottom', maxLines: 1 }
								};
								
								var chart = new google.visualization.BarChart(document.getElementById('cumplimiento'));
								chart.draw(data, options);
							}
						</script>
						<div id="cumplimiento" style="width:530px; float:left;"></div>
						<div style="width:380px; float:left;">
							<br><br>
							<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="380">
							<tr>
								<th>&nbsp;</th>
								<th width="100">$</th>
								<th width="30">#</th>
								<th width="40">%</th>
							</tr>
							<tr>
								<td>META</td>
								<td align="right"><?php echo number_format($meta, 0) ?></td>
								<td align="right">-</td>
								<td align="right">100.00</td>
							</tr>
							<tr>
								<td>DESEMBOLSADO</td>
								<td align="right"><?php echo number_format($suma, 0) ?></td>
								<td align="right"><?php echo number_format($cuantos, 0) ?></td>
								<td align="right"><?php if ($meta) { echo number_format($suma / $meta * 100.00, 2); } else { echo "-"; } ?></td>
							</tr>
							</table>
						</div>
						<?php
					} ?>
					</td>
				</tr>
			</table>
		</div>

		<?php
		$rs1 = sqlsrv_query($link,"SELECT b.* FROM usuarios_reportes a RIGHT JOIN reportes b ON b.id = a.id_reporte  and b.tipo_reporte = 2  WHERE a.id_usuario = ".$_SESSION['S_IDUSUARIO']);
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){ ?>
			<div class="tab-pane" id="tab_reporte<?=$fila1["id"]?>" role="tabpanel">
				<iframe src="<?=$fila1["url"]?>"></iframe>
			</div>
			<?php 
		} ?>
	</div>
</div>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/tabler/js/tabler.min.js"></script>
<script src="../plugins/tabler/js/demo.min.js"></script>

<?php include("bottom.php"); ?>