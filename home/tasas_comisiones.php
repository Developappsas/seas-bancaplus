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
							<li class="nav-item" style="font-weight: bold;">TASAS COMISIONES</li>
						</ul>						
					</div>

					<div class="card-body " >
						<div class="row">
							<div class="col-md-12">
								<div class="card-table table-responsive">
									<ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
										<li class="nav-item" role="presentation" id="menu_tab_kredit" onclick="loadTabla(0); return false;">
											<a href="#tab_kredit" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab"><b>KREDIT</b></a>
										</li>
										<li class="nav-item" role="presentation" id="menu_tab_fianti" onclick="loadTabla(1); return false;">
											<a href="#tab_fianti" class="nav-link" data-bs-toggle="tab" aria-selected="true" role="tab"><b>FIANTI</b></a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="tab_kredit" role="tabpanel">
											<button type="button" data-bs-toggle="modal" data-bs-target="#modalAddTasa" class="btn btn-success btn-sm" onclick="modalSaveTasas(0, 'add', 0);"><span>Agregar Tasa</span></button>
											<table class="table" id="tablaTasasKredit">
												<thead>
													<tr>
														<th>tipo</th>
														<th>ID</th>
														<th>Und Negocio</th>
														<th>Tasa</th>
														<th>KP</th>
														<th>F. Inicial</th>
														<th>F. Final</th>
														<th>Vig</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>	

										<div class="tab-pane" id="tab_fianti" role="tabpanel">
											<button type="button" data-bs-toggle="modal" data-bs-target="#modalAddTasa" class="btn btn-success btn-sm" onclick="modalSaveTasas(1, 'add', 0);"><span>Agregar Tasa</span></button>
											<table class="table" id="tablaTasasFianti">
												<thead>
													<tr>
														<th>tipo</th>
														<th>ID</th>
														<th>Und Negocio</th>
														<th>Tasa</th>
														<th>KP</th>
														<th>F. Inicial</th>
														<th>F. Final</th>
														<th>Vig</th>
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
					</div>
				</form>			
			</div>
		</div>		
	</div>

	<div class="modal modal-blur fade modal-tabler" id="modalAddTasa" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">NUEVA TASA</h5>
					<input type="hidden" id="idTasaComisionModal">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">MARCA</label>
								<select disabled id="idMarcaUnidadNegocioModal" class="form-select">
									<option value="0">KREDIT</option>
									<option value="1">FIANTI</option>
								</select>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">UNIDAD NEGOCIO</label>
								<select id="idUnidadNegocioModal" class="form-select">
									<option value="1">KREDIT</option>
								</select>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">TIPO</label>
								<input type="number" id="idTipoModal" class="form-control">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">TASA</label>
								<select id="idTasaModal" class="form-select">
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">VIGENTE</label>
								<select id="vigenciaModal" class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">INICIO VIG</label>
								<input id="fechaInicioModal" type="date" class="form-control" placeholder="yyyy-mm-dd">
							</div>
						</div>
						
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">FIN VIG</label>
								<input id="fechaFinModal" type="date" class="form-control" placeholder="yyyy-mm-dd">
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">KP PLUS</label>
								<select id="kpplusModal" class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>
					</div>					
				</div>

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="addTasas(); return false;" class="btn btn-primary ms-auto">
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
			loadTabla(0);	
		});

		function modalSaveTasas(id_unidad_negocio, opcion, id){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			$("#idMarcaUnidadNegocioModal").val(id_unidad_negocio);

			Swal.close();

			if(opcion == 'add'){
				llenarSelectsTasas('');
				llenarSelectUnidades('');
				$("#btnSaveModal").attr("name", "add");
			}else{
				$("#btnSaveModal").attr("name", "edit");
				$.ajax({
					url: '../servicios/comisiones/consultar_tasas.php',
					type: 'POST',
					data: { id_unidad_negocio : id_unidad_negocio, id_tasa_comision : id, opcion : 'tasa_comision'},
					dataType : 'json',
					success: function(json) {

						if(json.code == 200){
							json.data.forEach(function(tasa, index) {
								$("#idTasaComisionModal").val(tasa.id_tasa_comision);
								$("#idTipoModal").val(tasa.id_tipo);
								$("#vigenciaModal").val(tasa.vigente);
								$("#fechaInicioModal").val(tasa.fecha_inicio);
								$("#fechaFinModal").val(tasa.fecha_fin);
								$("#kpplusModal").val(tasa.kp_plus);

								llenarSelectsTasas(tasa.tasa_interes);
								llenarSelectUnidades(tasa.id_unidad_negocio);
							});
						}else{
							Swal.fire('Error al Consultar Información de Tasa', '', 'error');
						}
					}
				});
			}			
		}

		function loadTabla(id_unidad_negocio){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			switch (id_unidad_negocio) {
				case 0:
					tablaContrato = 'tablaTasasKredit';
				break;

				case 1:
					tablaContrato = 'tablaTasasFianti';
				break;
			}

			if ($.fn.DataTable.isDataTable('#'+tablaContrato)) {
				$('#'+tablaContrato).DataTable().destroy();
			}

			$("#"+tablaContrato+" tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/comisiones/consultar_tasas.php',
				type: 'POST',
				data: { id_unidad_negocio : id_unidad_negocio},
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){
							json.data.forEach(function(tasa, index) {
								if(tasa.vigente == 1){ var text_vigente = 'SI'; }else { text_vigente = 'NO'; }
								if(tasa.kp_plus == 1){ var text_kp_plus = 'SI'; }else { text_kp_plus = 'NO'; }
								if(id_unidad_negocio == 1){ var text_tipo = 'F'; }else { text_tipo = 'K'; }

								rowsHtml += '<tr>';
									rowsHtml += '<td>'+text_tipo+' '+tasa.id_tipo+'</td>';
									rowsHtml += '<td>'+tasa.id_tasa_comision+'</td>';
									rowsHtml += '<td>'+tasa.unidad_negocio+'</td>';
									rowsHtml += '<td>'+tasa.tasa_interes+'</td>';
									rowsHtml += '<td kp_plus="'+text_kp_plus+'">'+text_kp_plus+'</td>';
									rowsHtml += '<td>'+tasa.fecha_inicio+'</td>';
									rowsHtml += '<td>'+tasa.fecha_fin+'</td>';
									rowsHtml += '<td vigente="'+tasa.vigente+'">'+text_vigente+'</td>';
									rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">'+tasa.opciones+'</td>';
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
								loadTabla(id_unidad_negocio);
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

		function llenarSelectUnidades(id){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/unidades_negocio/consultar_unidades.php',
				type: 'POST',
				data: { id_unidad_negocio : $("#idMarcaUnidadNegocioModal").val() },
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){
						if(json.code == 200){
							rowsHtml += '<option value="" selected>Selecione</option>';
							json.data.forEach(function(unidad, index) {
								if(unidad.id_unidad == id){
									rowsHtml += '<option selected value="'+unidad.id_unidad+'">'+unidad.unidad+'</option>';
								}else{
									rowsHtml += '<option value="'+unidad.id_unidad+'">'+unidad.unidad+'</option>';
								}
							});
						}											
					}

					$("#idUnidadNegocioModal").html(rowsHtml);

					Swal.close();

					return false;
				}
			});
		}

		function addTasas(){

			var error = false;

			if($("#idTipoModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Tipo de tasa', '', 'error')
			}
			else if($("#idTasaModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Tasa', '', 'error')
			}
			else if($("#vigenciaModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el campo vigente', '', 'error')
			}
			else if($("#fechaInicioModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el Inicio de Vigencia', '', 'error')
			}
			/*else if($("#fechaFinModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el Fin de la vigencia', '', 'error')
			}*/
			else if($("#kpplusModal").val() == ''){
				error = true;
				Swal.fire('Debe completar si tiene o no KP PLUS', '', 'error')
			}

			if(error){ return false; }
			
			var data = {
				id_marca_unidad_negocio : $("#idMarcaUnidadNegocioModal").val(),
				id_unidad_negocio : $("#idUnidadNegocioModal").val(),
				id_tipo : $("#idTipoModal").val(),
				tasa : $("#idTasaModal").val(),
				vigente : $("#vigenciaModal").val(),
				fecha_inicio : $("#fechaInicioModal").val(),
				fecha_fin : $("#fechaFinModal").val(),
				kp_plus : $("#kpplusModal").val(),
				opcion : $("#btnSaveModal").attr("name"),
				id_tasa_comision : $("#idTasaComisionModal").val()
			}

			$.ajax({
				url: '../servicios/comisiones/agregar_tasas.php',
				type: 'POST',
				data: data,
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200){
						$(".btn-close").trigger("click");
						Swal.fire('Guardado Exitosamente', '', 'success');
						loadTabla($("#idMarcaUnidadNegocioModal").val());
					}else {
						Swal.fire(json.mensaje, '', 'error')
					}
				}
			});
		}

		function deleteTasa(id, idMarcaUnidad){

			Swal.fire({
				title: '¿Está seguro de eliminar esta Tasa?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/comisiones/eliminar_tasas.php',
						type: 'POST',
						data: { id_tasa_comision : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Eliminado Exitosamente', '', 'success');
								loadTabla(idMarcaUnidad);
							}else {
								Swal.fire('No se Pudo Eliminar', '', 'error')
							}
						}
					});
				} else {
					Swal.fire('No se Pudo Eliminar', '', 'error')
				}
			})
		}

	</script>
	
<?php 
	include("bottom.php");
?>