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
	<link href="../plugins/fontawesome/css/fontawesome.css" rel="stylesheet">
	
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
							<li class="nav-item" style="font-weight: bold;">PERCENTIL COMISIONES</li>
						</ul>						
					</div>

					<div class="card-body " >
						<div class="row form-fieldset">
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"> <strong>UNIDAD DE NEGOCIO</strong></label>
									<select class="form-control is-valid mb-2" onchange="loadTabla(1)" placeholder="" id="unidadNegocioTasas">
										<option value="0">KREDIT</option>
										<option value="1">FIANTI</option>
									</select>
								</div>
							</div>			
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="card-table table-responsive">
									<ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
										<li class="nav-item" role="presentation" id="menu_tab_planta" onclick="loadTabla(1); return false;">
											<a href="#tab_planta" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab"><b>PLANTA</b></a>
										</li>
										<li class="nav-item" role="presentation" id="menu_tab_freelance" onclick="loadTabla(2); return false;">
											<a href="#tab_freelance" class="nav-link" data-bs-toggle="tab" aria-selected="true" role="tab"><b>FREELANCE</b></a>
										</li>
										<li class="nav-item" role="presentation" id="menu_tab_outsoursing" onclick="loadTabla(3); return false;">
											<a href="#tab_outsoursing" class="nav-link" data-bs-toggle="tab" aria-selected="true" role="tab"><b>OUTSOURSING</b></a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="tab_planta" role="tabpanel">
											<button type="button" data-bs-toggle="modal" data-bs-target="#modalSavePercentil" class="btn btn-success btn-sm" onclick="modalSavePercentil(1, 'add', '');"><span>Agregar Percentil</span></button>
											<table class="table" id="tablaTasasPlanta">
												<thead>
													<tr>
														<th>tipo</th>
														<th>ID</th>
														<th>Und Negocio</th>
														<th>Tasa</th>
														<th>(Pn)</th>
														<th>Rango Inicio</th>
														<th>Rango Fin</th>
														<th>Valor</th>
														<th>Contrato</th>
														<th>KP</th>
														<th>F. Inicial</th><th>F. Final</th>
														<th>Vig</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>	

										<div class="tab-pane" id="tab_freelance" role="tabpanel">
											<button type="button" data-bs-toggle="modal" data-bs-target="#modalSavePercentil" class="btn btn-success btn-sm" onclick="modalSavePercentil(2, 'add', '');"><span>Agregar Percentil</span></button>
											<table class="table table-vcenter" id="tablaTasasFreelance">
												<thead>
													<tr>
														<th>tipo</th>
														<th>ID</th>
														<th>Und Negocio</th>
														<th>Tasa</th>
														<th>(Pn)</th>
														<th>Rango Inicio</th>
														<th>Rango Fin</th>
														<th>Valor</th>
														<th>Contrato</th>
														<th>KP</th>
														<th>F. Inicial</th><th>F. Final</th>
														<th>Vig</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>	

										<div class="tab-pane" id="tab_outsoursing" role="tabpanel">
											<button type="button" data-bs-toggle="modal" data-bs-target="#modalSavePercentil" class="btn btn-success btn-sm" onclick="modalSavePercentil(3, 'add', '');"><span>Agregar Percentil</span></button>
											<table class="table table-vcenter" id="tablaTasasOutsoursing">
												<thead>
													<tr>
														<th>tipo</th>
														<th>ID</th>
														<th>Und Negocio</th>
														<th>Tasa</th>
														<th>(Pn)</th>
														<th>Rango Inicio</th>
														<th>Rango Fin</th>
														<th>Valor</th>
														<th>Contrato</th>
														<th>KP</th>
														<th>F. Inicial</th><th>F. Final</th>
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

	<div class="modal modal-blur fade modal-tabler" id="modalSavePercentil" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><b>KREDIT /</b> NUEVO PERCENTIL</h5>
					<input type="hidden" id="idPercentilComisionModal">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">MARCA</label>
								<select disabled id="idUnidadNegocioModal" class="form-select">
									<option value="0">KREDIT</option>
									<option value="1">FIANTI</option>
								</select>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">TIPO</label>
								<select id="idTipoModal" class="form-select" onchange="llenarSelectsTasas('tasa_comision_tipo', ''); return false;">
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">TASA COMISIÓN</label>
								<select id="idTasaComisionModal" class="form-select" onchange="llenarSelectsTasas('tasa_comision', ''); return false;">
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">VIGENTE</label>
								<select id="vigenciaModal" disabled class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">INICIO VIG</label>
								<input id="fechaInicioModal" disabled type="date" class="form-control" placeholder="yyyy-mm-dd">
							</div>
						</div>
						
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">FIN VIG</label>
								<input id="fechaFinModal" disabled type="date" class="form-control" placeholder="yyyy-mm-dd">
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label" >KP PLUS</label>
								<select id="kpplusModal" disabled class="form-select">
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>
					</div>					
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-lg-4">
							<div class="mb-3">
								<label class="form-label">TIPO CONTRATO</label>
								<select id="idTipoContratoModal" class="form-select">
									<option value="1">PLANTA</option>
									<option value="2">FREELANCE</option>
									<option value="3">OUTSOURSING</option>
								</select>
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-3">
								<label class="form-label">PERCENTIL</label>
								<input style="background: yellow;" id="posicionModal" type="number" value="0" class="form-control">
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">RANGO <</label>
								<input id="rangoInicioModal" type="number" value="0" class="form-control" placeholder="0"  min="0" step="1000000" max="5000000000">
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">RANGO ></label>
								<input id="rangoFinModal" type="number" value="0" class="form-control"  placeholder="0"  min="0" step="1000000" max="5000000000">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">VALOR PAGO</label>
								<input  id="valorModal" value="0" type="number" class="form-control"   placeholder="0" value="0" class="form-control"  min="1000" step="1000" max="1000000">
							</div>
						</div>						
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a href="#" name="add" id="btnSaveModal" class="btn btn-primary ms-auto" onclick="addPercentil();">
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
	<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.js"></script>
	
	<!-- Tabler Core -->
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>


	<script type="text/javascript">

		$(document).ready( function () {
			loadTabla(1);	
		});

		$("#posicionModal").click(function() {
			if($("#btnSaveModal").attr("name") == 'add'){
				Swal.fire({
					title: 'Este es el número Sugerido para seguir el Orden de Percentiles, ¿desea Mdoficarlo?',
					showConfirmButton: false,
					showDenyButton: true,
					showCancelButton: true,
					denyButtonText: `Continuar`,
				}).then((result) => {
					if (result.isDenied) {
						$("#posicionModal").focus();
					}else{
						$("#rangoInicioModal").focus();
					}
				});
			}
		});

		function modalSavePercentil(id_tipo_contrato, opcion, id){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			$("#idTipoModal").val('');
			$("#idTasaComisionModal").html('');
			$("#fechaInicioModal").val('');
			$("#fechaFinModal").val('');
			$("#vigenciaModal").val('');
			$("#kpplusModal").val('');

			$("#idTipoContratoModal").val('');
			$("#posicionModal").val(0);
			$("#rangoInicioModal").val(0);
			$("#rangoFinModal").val(0);
			$("#valorModal").val(1000);
			$("#idPercentilComisionModal").val('');

			var id_unidad_negocio = $("#unidadNegocioTasas").val();

			$("#idTipoContratoModal").val(id_tipo_contrato);
			$("#idUnidadNegocioModal").val(id_unidad_negocio);

			if(opcion == 'add'){
				llenarSelectsTasas('agrupar_tipos', '');
				$("#btnSaveModal").attr("name", "add");
			}else{
				
				$("#btnSaveModal").attr("name", "edit");
				$("#idPercentilComisionModal").val(id);
				
				$.ajax({
					url: '../servicios/comisiones/consultar_percentiles.php',
					type: 'POST',
					data: { id_unidad_negocio : id_unidad_negocio, id_tipo_contrato : id_tipo_contrato, id_percentil : id, opcion : 'percentil_comision'},
					dataType : 'json',
					success: function(json) {

						if(json.code == 200){
							json.data.forEach(function(percentil, index) {
								llenarSelectsTasas('agrupar_tipos', percentil.id_tipo, percentil.id_tasa_comision);

								$("#idTipoContratoModal").val(percentil.id_tipo_contrato);
								$("#posicionModal").val(percentil.percentil);
								$("#rangoInicioModal").val(percentil.rango_inicial);
								$("#rangoFinModal").val(percentil.rango_final);
								$("#valorModal").val(percentil.valor);
							});
						}else{
							Swal.fire('Error al Consultar Información de Percentil', '', 'error');
						}
					}
				});
			}	
		}

		function loadTabla(id_tipo_contrato){

			$("#unidadNegocioTasas").attr("onchange", "loadTabla("+id_tipo_contrato+"); return false;");
			
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/comisiones/consultar_percentiles.php',
				type: 'POST',
				data: { id_unidad_negocio : $("#unidadNegocioTasas").val(), id_tipo_contrato : id_tipo_contrato },
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){

							json.data.forEach(function(tasa, index) {
								if(tasa.vigente == 1){ var text_vigente = 'SI'; }else { text_vigente = 'NO'; }
								if(tasa.kp_plus == 1){ var text_kp_plus = 'SI'; }else { text_kp_plus = 'NO'; }
								rowsHtml += '<tr id="percentil_'+tasa.id_percentil+'">';
									rowsHtml += '<td>TIPO '+tasa.id_tipo+'</td>';
									rowsHtml += '<td>'+tasa.id_tasa_comision+"-"+tasa.id_percentil+'</td>';
									rowsHtml += '<td>'+tasa.unidad_negocio+'</td>';
									rowsHtml += '<td>'+tasa.tasa_interes+'</td>';
									rowsHtml += '<td>'+tasa.percentil+'</td>';
									rowsHtml += '<td>'+tasa.rango_inicial+'</td>';
									rowsHtml += '<td>'+tasa.rango_final+'</td>';
									rowsHtml += '<td>'+tasa.valor+'</td>';
									rowsHtml += '<td><b>'+tasa.tipo_contrato+'</b></td>';
									rowsHtml += '<td kp_plus="'+text_kp_plus+'">'+text_kp_plus+'</td>';
									rowsHtml += '<td>'+tasa.fecha_inicio+'</td>';
									rowsHtml += '<td>'+tasa.fecha_fin+'</td>';
									rowsHtml += '<td vigente="'+tasa.vigente+'">'+text_vigente+'</td>';
									rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">'+tasa.opciones+'</td>';
								rowsHtml += '</tr>';
							});
						}										
					}

					switch (id_tipo_contrato) {
						case 1:
							tablaContrato = 'tablaTasasPlanta';
						break;

						case 2:
							tablaContrato = 'tablaTasasFreelance';
						break;

						case 3:
							tablaContrato = 'tablaTasasOutsoursing';
						break;
					}

					if ($.fn.DataTable.isDataTable("#"+tablaContrato)) {
						$("#"+tablaContrato).DataTable().destroy();
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
								loadTabla(id_tipo_contrato);
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

		function llenarSelectsTasas(opcion, id, id_2 = ''){
			
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/comisiones/consultar_tasas.php',
				type: 'POST',
				data: { id_unidad_negocio : $("#idUnidadNegocioModal").val(), opcion : opcion, id_tipo : $("#idTipoModal").val(), id_tasa_comision : $("#idTasaComisionModal").val()  },
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){
							
							switch (opcion) {
								case 'agrupar_tipos':
									
									rowsHtml += '<option value="">Selecione</option>';
									json.data.forEach(function(tasa, index) {
										rowsHtml += '<option value="'+tasa.id_tipo+'">TIPO '+tasa.id_tipo+'</option>';
									});
										
									$("#idTipoModal").html(rowsHtml);

									$("#idTipoModal").val(id);

									Swal.close();

									if(id_2 != ''){
										llenarSelectsTasas('tasa_comision_tipo', id_2);
									}
								break;

								case 'tasa_comision_tipo':
									
									rowsHtml += '<option value="" >Selecione</option>';
									json.data.forEach(function(tasa, index) {
										rowsHtml += '<option value="'+tasa.id_tasa_comision+'">'+tasa.unidad_negocio+' ('+ tasa.tasa_interes +')</option>';
									});

									$("#idTasaComisionModal").html(rowsHtml);

									Swal.close();

									$("#idTasaComisionModal").val(id).trigger('change');
								break;

								case 'tasa_comision':
									json.data.forEach(function(tasa, index) {
										$("#vigenciaModal").val(tasa.vigente);
										$("#fechaInicioModal").val(tasa.fecha_inicio);
										$("#fechaFinModal").val(tasa.fecha_fin);
										$("#kpplusModal").val(tasa.kp_plus);
									});

									if($("#btnSaveModal").attr("name") == "add"){
										$("#posicionModal").val(parseInt(json.dato));
									}

									Swal.close();
								break;

								case 3:
									selectModal = 'tablaTasasOutsoursing';
									Swal.close();
								break;
							}
						}else{
							Swal.close();
						}											
					}else{
						Swal.close();
					}

					return false;
				}
			});
		}

		function validarRango(){
			if($("#rangoInicioModal").val() <= 0 && $("#rangoFinModal").val() <= 0){
				alert("Ambos rangos no pueden ser 0, Verifique!");
				$("#rangoInicioModal").focus();
			}else{
				if($("#rangoInicioModal").val() <= 0 && $("#rangoFinModal").val() <= 1000000){
					alert("Si Rango Inicial es 0 Debe haber una diferencia en millones entre los valores.");
					$("#rangoInicioModal").focus();
				}

				if($("#rangoFinModal").val() <= 0 && $("#rangoInicioModal").val() <= 1000000){
					alert("Si Rango Final es 0 Debe haber una diferencia en millones entre los valores.");
					$("#rangoInicioModal").focus();
				}
			}

			return false;
		}

		function addPercentil(){

			var error = false;

			if($("#idTasaComisionModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Tipo de tasa', '', 'error')
			}
			else if($("#posicionModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Tasa', '', 'error')
			}
			else if($("#rangoInicioModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el campo vigente', '', 'error')
			}
			else if($("#rangoFinModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el Inicio de Vigencia', '', 'error')
			}
			else if($("#valorModal").val() == ''){
				error = true;
				Swal.fire('Debe completar el Fin de la vigencia', '', 'error')
			}
			else if($("#idTipoContratoModal").val() == ''){
				error = true;
				Swal.fire('Debe completar si tiene o no KP PLUS', '', 'error')
			}

			if(error){ return false; }
			
			var data = {
				id_tasa_comision : $("#idTasaComisionModal").val(),
				posicion : $("#posicionModal").val(),
				id_tipo : $("#idTipoModal").val(),
				rango_inicial : $("#rangoInicioModal").val(),
				rango_final : $("#rangoFinModal").val(),
				valor : $("#valorModal").val(),
				id_tipo_contrato : $("#idTipoContratoModal").val(),
				opcion : $("#btnSaveModal").attr("name"),
				id_percentil : $("#idPercentilComisionModal").val()
			}

			$.ajax({
				url: '../servicios/comisiones/agregar_percentil.php',
				type: 'POST',
				data: data,
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200){
						$(".btn-close").trigger("click");
						Swal.fire('Guardado Exitosamente', '', 'success');
						loadTabla($("#idTipoContratoModal").val());
					}else {
						Swal.fire(json.mensaje, '', 'error')
					}
				}
			});
		}

		function deletePercentil(id, id_tipo_contrato){

			Swal.fire({
				title: '¿Está seguro de eliminar este Percentil?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/comisiones/eliminar_percentil.php',
						type: 'POST',
						data: { id_percentil : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Eliminado Exitosamente', '', 'success');
								loadTabla(id_tipo_contrato);
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