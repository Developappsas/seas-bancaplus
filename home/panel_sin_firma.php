<?php
	include ('../functions.php');
	include ('./top.php');

	$link = conectar_utf();
	if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR"){
		exit;
	}
?>
	<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/demo.min.css" rel="stylesheet"/>
	<link href="../plugins/DataTables/datatables.min.css?v=4" rel="stylesheet">
	<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
	<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">
	<style type="text/css">
		.tab-pane {
			padding: 20px !important;
		}

		.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
		    color: #ffffff !important;
		    background-color: #4299e1 !important;
		}
	</style>

  	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-12">	
				<form action="" class="card">		
					<div class="card-header">
						<ul class="nav nav-pills card-header-pills">
							<li class="nav-item" style="font-weight: bold;">CREDITOS PENDIENTES DE FIRMA</li>
						</ul>						
					</div>

					<div class="card-body " >
						<div class="row">
							<div class="col-md-12">

								<div class="box1 oran clearfix">
									<h2><b>CREDITOS PENDIENTES DE FIRMA</b></h2>

									<table border="0" cellspacing=1 cellpadding=2 width="95%">
										<!--<tr>
											<td>PRIMER NOMBRE</td>
											<td><input type="text" name="primerNombreCambioDatos" id="primerNombreCambioDatos" style='background-color:#EAF1DD;' size="32"></td>
											<td>SEGUNDO NOMBRE</td>
											<td><input type="text" name="segundoNombreCambioDatos" id="segundoNombreCambioDatos" style='background-color:#EAF1DD;' size="32"></td>
										</tr>-->
									</table>
								</div>

								<div class="card-table table-responsive">
									<div class="tab-pane active" id="tab_kredit" role="tabpanel">
										<table class="table" id="tablaTasasKredit">
											<thead>
												<tr>
													<th>id_simulacion</th>
													<th>nombre</th>
													<th>cedula</th>
													<th>pagaduria</th>
													<th>subestado</th>
													<th>unidad_negocio</th>
													<th>nombre_empresa</th>
													<th>nro_libranza</th>
													<th>pagare_deceval</th>
													<th>fecha_pagare_deceval</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
									</div>							
								</div>
							</div>
						</div>	
					</div>
				</form>			
			</div>
		</div>		
	</div>

	<div class="modal modal-blur fade modal-tabler" id="modal_firmar_credito" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">MARCAR CREDITO COMO FIRMADO</h5>
					<input type="hidden" id="id_simulacion">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-lg-5">
							<div class="mb-1">
								<label class="form-label">SUBESTADO ACTUAL</label>
								<input type="text" id="id_subestado" class="form-control" readonly>
							</div>
						</div>

						<div class="col-lg-5">
							<div class="mb-1">
								<label class="form-label">TOKEN</label>
								<input type="text" id="token" class="form-control" readonly>
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">EST TOKEN</label>
								<input type="number" id="estado_token" class="form-control">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">FIRMADO</label>
								<select id="formato_digital" class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="mb-1">
								<label class="form-label">FECHA ENVIO</label>
								<input id="fecha_envio" type="text" class="form-control" placeholder="yyyy-mm-dd hh:mm:ss">
							</div>
						</div>
						
						<div class="col-lg-4">
							<div class="mb-1">
								<label class="form-label">FECHA LEÍDO</label>
								<input id="fecha_leido" type="text" class="form-control" placeholder="yyyy-mm-dd hh:mm:ss">
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">INTENTOS</label>
								<input id="intentos" type="number" class="form-control" placeholder="0" readonly>
							</div>
						</div>
					</div>		

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">EN PROGRESO</label>
								<select id="en_progreso" class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-5">
							<div class="mb-1">
								<label class="form-label">PAGARÉ DEC</label>
								<input type="text" id="pagare_deceval" class="form-control">
							</div>
						</div>

						<div class="col-lg-4">
							<div class="mb-1">
								<label class="form-label">FECHA PAGARÉ</label>
								<input id="fecha_pagare_deceval" type="text" class="form-control" placeholder="yyyy-mm-dd hh:mm:ss">
							</div>
						</div>	
					</div>	

					<div class="row">
						<div class="col-lg-5">
							<div class="mb-1">
								<label class="form-label">FIRMAR EXP</label>
								<input type="text" id="firma_experian" class="form-control">
							</div>
						</div>

						<div class="col-lg-5">
							<div class="mb-1">
								<label class="form-label">SUBESTADO TRX TRANSACCION</label>
								<input type="text" id="sub_estado_trx" class="form-control" >
							</div>
						</div>	
					</div>	

					<div class="row">
						<div class="col-lg-12">
							<div class="mb-1">
								<label class="form-label">RESPUESTA PAGARE</label>
								<input id="observacion_pagare" type="text" class="form-control">
							</div>
						</div>
					</div>	

					<div class="row">
						<div class="col-lg-12">
							<div class="mb-1">
								<label class="form-label">RESPUESTA GIRADOR</label>
								<input id="observacion_girador" type="text" class="form-control">
							</div>
						</div>
					</div>	

					<div class="row">
						<div class="col-lg-12">
							<div class="mb-1">
								<label class="form-label">RESPUESTA FIRMA</label>
								<input id="observacion_firma" type="text" class="form-control">
							</div>
						</div>
					</div>		
				</div>

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="credito_firmado(); return false;" class="btn btn-primary ms-auto">
						<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
						GUARDAR
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
	<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>

	
	<!-- Tabler Core -->
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>


	<script type="text/javascript">

		$(document).ready( function () {
			loadTabla('');	
		});

		function loadTabla(opcion){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';
			
			tablaContrato = 'tablaTasasKredit';

			if ($.fn.DataTable.isDataTable('#'+tablaContrato)) {
				$('#'+tablaContrato).DataTable().destroy();
			}

			$("#"+tablaContrato+" tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/firma_digital/consultar_creditos.php',
				type: 'POST',
				data: { opcion : opcion},
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){
							json.data.forEach(function(credito, index) {
								if(credito.id_simulacion == null){ credito.id_simulacion = ''; }
								if(credito.nombre == null){ credito.nombre = ''; }
								if(credito.cedula == null){ credito.cedula = ''; }
								if(credito.pagaduria == null){ credito.pagaduria = ''; }
								if(credito.subestado == null){ credito.subestado = ''; }
								if(credito.unidad_negocio == null){ credito.unidad_negocio = ''; }
								if(credito.nombre_empresa == null){ credito.nombre_empresa = ''; }
								if(credito.nro_libranza == null){ credito.nro_libranza = ''; }
								if(credito.pagare_deceval == null){ credito.pagare_deceval = ''; }
								if(credito.fecha_pagare_deceval == null){ credito.fecha_pagare_deceval = ''; }

								rowsHtml += '<tr>';
									rowsHtml += '<td>'+credito.id_simulacion+'</td>';
									rowsHtml += '<td>'+credito.nombre+'</td>';
									rowsHtml += '<td>'+credito.cedula+'</td>';
									rowsHtml += '<td>'+credito.pagaduria+'</td>';
									rowsHtml += '<td>'+credito.subestado+'</td>';
									rowsHtml += '<td>'+credito.unidad_negocio+'</td>';
									rowsHtml += '<td>'+credito.nombre_empresa+'</td>';
									rowsHtml += '<td>'+credito.nro_libranza+'</td>';
									rowsHtml += '<td>'+credito.pagare_deceval+'</td>';
									rowsHtml += '<td>'+credito.fecha_pagare_deceval+'</td>';									
									rowsHtml += '<td class="text-end" style="display: revert; flex-direction:row;">'+credito.opciones+'</td>';
								rowsHtml += '</tr>';
							});
						}											
					}

					$("#"+tablaContrato+" tbody").html(rowsHtml);
						
					$('#'+tablaContrato).DataTable({
						dom: 'Bfrtip',
						buttons: [{	
							extend: 'excelHtml5',
							title: 'FDC',
							footer:false,
						},{
							text: '<button>Actualizar</button>',
							action: function ( e, dt, node, config ) {
								loadTabla('');
							}
						}],
						"destroy":true,
						initComplete: function(settings, json) {
							Swal.close();
						},
						"bPaginate":true,
						"bFilter" : true,   
						"bProcessing": true,
						"pageLength": 40,
						"orderable": false,
						"language": {"sProcessing":     "Procesando...","sLengthMenu":     "Mostrar _MENU_ registros","sZeroRecords":    "No se encontraron resultados","sEmptyTable":     "Ningún dato disponible en esta tabla","sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros","sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros","sInfoFiltered":   "(filtrado de un total de _MAX_ registros)","sInfoPostFix":    "","sSearch":         "Buscar:","sUrl":            "","sInfoThousands":  ",","sLoadingRecords": "Cargando...","oPaginate": {"sFirst":    "Primero","sLast":     "Último","sNext":     "Siguiente","sPrevious": "Anterior"},"oAria": {"sSortAscending":  ": Activar para ordenar la columna de manera ascendente","sSortDescending": ": Activar para ordenar la columna de manera descendente"}}
					});

					return false;
				}
			});
		}

		function llenarSelectsTasas(tasa_interes){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/tasas/consultar_tasas.php',
				type: 'POST',
				data: { opcion : "agrupar_tasa_interes", id_unidad_negocio : $("#idMarcaUnidadNegocioModal").val() },
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){
						if(json.code == 200){
							rowsHtml += '<option value="" selected>Selecione</option>';
							json.data.forEach(function(tasa, index) {
								if(tasa.tasa_interes == tasa_interes) {
									rowsHtml += '<option selected value="'+tasa.tasa_interes+'">'+tasa.tasa_interes+'</option>';
								}else{
									rowsHtml += '<option value="'+tasa.tasa_interes+'">'+tasa.tasa_interes+'</option>';
								}
							});
						}											
					}

					$("#idTasaModal").html(rowsHtml);

					Swal.close();

					return false;
				}
			});
		}

		function consultarInformacionFirma(id_simulacion, element){

			$("#id_subestado").val('');
			$("#id_subestado").attr("id_subestado", '');
			$("#token").val('');
			$("#estado_token").val('');

			$("#formato_digital").val('');
			$("#fecha_envio").val('');
			$("#fecha_leido").val('');
			$("#intentos").val(0);

			$("#en_progreso").val('');
			$("#pagare_deceval").val('');
			$("#fecha_pagare_deceval").val('');

			$("#firma_eperiam").val('');
			$("#sub_estado_trx").val('');

			$("#observacion_pagare").val('');
			$("#observacion_girador").val('');
			$("#observacion_firma").val('');

			$("#id_simulacion").val('');

			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/firma_digital/consultar_firma_credito.php',
				type: 'POST',
				data: { id_simulacion : id_simulacion, opcion: 'firma_digital' },
				dataType : 'json',
				success: function(json) {
					
					Swal.close();
					
					if(json.code == 200 || json.code == 300){
						if(json.code == 200){
							$("#id_simulacion").val(id_simulacion);

							$("#id_subestado").val(json.subestado);
							$("#id_subestado").attr("id_subestado", json.subestado);
							$("#token").val(json.token);
							$("#estado_token").val(json.estado_token);

							$("#formato_digital").val(json.formato_digital);
							$("#fecha_envio").val(json.fecha_envio);
							$("#fecha_leido").val(json.fecha_leido);
							$("#intentos").val(json.intentos);

							$("#en_progreso").val(json.en_progreso);
							$("#pagare_deceval").val(json.pagare_deceval);
							$("#fecha_pagare_deceval").val(json.fecha_pagare_deceval);

							$("#firma_experian").val(json.firma_experian);
							$("#sub_estado_trx").val(json.sub_estado_trx);

							$("#observacion_pagare").val(json.observacion_crear_pagare);
							$("#observacion_girador").val(json.observacion_crear_girador);
							$("#observacion_firma").val(json.observacion_firma_pagare);
						}else{
							Swal.fire({
								title: 'No se encontró información para esta simulación',
								text: ''
							});
						}
					}else{
						Swal.fire({
							title: 'Error al consultar la información',
							text: ''
						});
					}

					return false;
				}
			});
		}

		function credito_firmado(){

			Swal.fire({
				title: '¿Está seguro de marcar como firmado este credito?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					id_simulacion = $("#id_simulacion").val();
					estado_token = $("#estado_token").val();
					fecha_envio = $("#fecha_envio").val();
					fecha_leido = $("#fecha_leido").val();

					en_progreso = $("#en_progreso").val();
					pagare_deceval = $("#pagare_deceval").val();
					fecha_pagare_deceval = $("#fecha_pagare_deceval").val();

					firma_experian = $("#firma_experian").val();
					sub_estado_trx = $("#sub_estado_trx").val();

					observacion_crear_pagare = $("#observacion_pagare").val();
					observacion_crear_girador = $("#observacion_girador").val();
					observacion_firma_pagare = $("#observacion_firma").val();

					$.ajax({
						url: '../servicios/firma_digital/credito_firmado.php',
						type: 'POST',
						data: { id_simulacion : id_simulacion, estado_token : estado_token, fecha_envio : fecha_envio, fecha_leido : fecha_leido, en_progreso : en_progreso, pagare_deceval : pagare_deceval, fecha_pagare_deceval : fecha_pagare_deceval, firma_experian : firma_experian, sub_estado_trx : sub_estado_trx, observacion_crear_pagare : observacion_crear_pagare, observacion_crear_girador : observacion_crear_girador, observacion_firma_pagare : observacion_firma_pagare },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Firmado Exitosamente', '', 'success');
								loadTabla('');
							}else {
								Swal.fire('No se Pudo Eliminar', '', 'error')
							}
						}
					});
				} else {

				}
			})
		}
	</script>
	
<?php 
	include("bottom.php");
?>