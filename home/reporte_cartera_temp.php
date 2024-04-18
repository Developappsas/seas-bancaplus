<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")){
	exit;
}

$link = conectar();
?>

<?php include("top.php"); ?>

<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">
	/*
	function chequeo_forma() {
		with (document.formato) {
			window.open('reporte_cartera2.php?cedula='+document.formato.cedula.value+'<?php if (!$_SESSION["S_SECTOR"]) { ?>&sector='+document.formato.sector.options[document.formato.sector.selectedIndex].value+'<?php } ?>&pagaduria='+document.formato.pagaduria.options[document.formato.pagaduria.selectedIndex].value+'&incorporacion='+document.formato.incorporacion.options[document.formato.incorporacion.selectedIndex].value+'&estado='+document.formato.estado.options[document.formato.estado.selectedIndex].value+'&calificacion='+document.formato.calificacion.options[document.formato.calificacion.selectedIndex].value+'&tipo='+document.formato.tipo.options[document.formato.tipo.selectedIndex].value+'&fecha_finalbd='+fecha_finalbd.options[fecha_finalbd.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value,'CARFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
		}
	}*/
</script>

<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo"><center><b>Reporte Cartera</b><br><br></center></td>
	</tr>
</table>

<form name="formato" method="post">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td align="right">C&eacute;dula/Nombre/No. Libranza</td><td>
								<input type="text" name="cedula" id="cedula">
							</td>
						</tr>
						<?php if (!$_SESSION["S_SECTOR"]){ ?>
							<tr>
								<td align="right">Sector</td><td>
									<select id="sector" name="sector">
										<option value=""></option>
										<option value="PUBLICO">PUBLICO</option>
										<option value="PRIVADO">PRIVADO</option>
									</select>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<td align="right">Pagadur&iacute;a</td><td>
								<select id="pagaduria" name="pagaduria">
									<option value=""></option>
									<?php
									$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";

									if ($_SESSION["S_SECTOR"]){
										$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
									}

									$queryDB .= " order by pagaduria";

									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_assoc($rs1)){
										echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
									}

									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Incorporaci&oacute;n</td><td>
								<select id="incorporacion" name="incorporacion">
									<option value=""></option>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Estado</td><td>
								<select id="estado" name="estado">
									<option value=""></option>
									<option value="DES">VIGENTE</option>
									<option value="CAN">CANCELADO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Calificaci&oacute;n</td><td>
								<select id="calificacion" name="calificacion">
									<option value=""></option>
									<option value="0">AL DIA</option>
									<option value="-1">CANCELADO</option>
									<?php
									for ($i = 1; $i <= 12; $i++){
										$limite1_calificacion = ($i * 30) - 29;
										$limite2_calificacion = $i * 30;

										$calificacion = $limite1_calificacion." a ".$limite2_calificacion;

										echo "<option value=\"".$i."\">".$calificacion."</option>";
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Tipo Cartera</td><td>
								<select id="tipo" name="tipo">
									<option value="ORI">ORIGINACI&Oacute;N</option>
									<option value="EXT">EXTERNA</option>
									<option value="ALL">TODA</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">F. Corte</td><td>
								<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
								<select id="fecha_finalbd" name="fecha_finalbd">
									<option value="">D&iacute;a</option>
									<?php
									for ($i = 1; $i <= 31; $i++) {
										if (strlen($i) == 1) {
											$j = "0".$i;
										}
										else {
											$j = $i;
										}

										echo "<option value=\"".$j."\">".$j."</option>";
									}
									?>
								</select>
								<select id="fecha_finalbm" name="fecha_finalbm">
									<option value="">Mes</option>
									<option value="01">Ene</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>	
									<option value="04">Abr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Ago</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dic</option>
								</select>
								<select id="fecha_finalba" name="fecha_finalba">
									<option value="">A&ntilde;o</option>
									<?php
									for ($i = 2014; $i <= date("Y"); $i++){
										echo "<option value=\"".$i."\">".$i."</option>";
									}
									?>
								</select>
								<a href="javascript:show_calendar('formato.fecha_finalb');"><img src="../images/calendario.gif" border=0></a>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Consultar"  onClick="reporteCartera()"/>
	</p>
</form>

<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script src="../plugins/sheetjs/xlsx.mini.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../plugins/sheetjs/FileSaver.min.js"></script>
<script type="text/javascript">

	function reporteCartera() {

		var cedula = $("#cedula").val();
		var sector = $("#sector").val();
		var pagaduria = $("#pagaduria").val();
		var incorporacion = $("#incorporacion").val();
		var estado = $("#estado").val();
		var calificacion = $("#calificacion").val();
		var tipo = $("#tipo").val();
		var fecha_finalbd = $("#fecha_finalbd").val();
		var fecha_finalbm = $("#fecha_finalbm").val();
		var fecha_finalba = $("#fecha_finalba").val();

		fechaTemp = fecha_finalba+"-"+fecha_finalbm+"-"+fecha_finalbd;

		if(fecha_finalba != "" && fecha_finalbm != "" && fecha_finalba == 2014){
			if(fecha_finalbm < 4){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'La Fecha NO Puede Ser Inferior a 01-ABRIL-2014'
				});

				return false;
			}
		}

		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();

		var rowsHtml = '';

		$.ajax({
			url: '../servicios/reportes/reporte_cartera.php',
			type: 'POST',
			data: { 
				cedula : cedula,
				sector : sector,
				pagaduria : pagaduria,
				incorporacion : incorporacion,
				estado : estado,
				calificacion : calificacion,
				tipo : tipo,
				fecha_finalbd : fecha_finalbd,
				fecha_finalbm : fecha_finalbm,
				fecha_finalba : fecha_finalba
			},
			dataType : 'json',
			success: function(json) {

				if(json.code == 200){
					var wb = XLSX.utils.book_new();

					wb.Props = {
		                Title: "Cartera",
		                Subject: "Reporte Cartera",
		                Author: "System SEAS",
		                CreatedDate: new Date()
			        };

			        wb.SheetNames.push("Test Sheet");			        
			        var ws = XLSX.utils.aoa_to_sheet(json.data);			        
			        wb.Sheets["Test Sheet"] = ws;
			        var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
			        
			        function s2ab(s) {

			        	var buf = new ArrayBuffer(s.length);
			        	var view = new Uint8Array(buf);
			        	for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
			        		return buf;
			        }

			        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'test.xlsx');								
				}

				Swal.close();

				return false;
			}
		});
	}
</script>

<?php include("bottom.php"); ?>
