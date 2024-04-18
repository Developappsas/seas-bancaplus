<?php
	include ('../functions.php');
	include ('./top.php');

	$link = conectar_utf();
	if (!$_SESSION["S_LOGIN"] || (($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_SOLICITAR_FIRMAS"] != "1"))) {	
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
													<th>unidad_negocio</th>
													<th>Nombre Empresa</th>
													<th>Nro Libranza</th>
													<th>Telefono</th>
													<th></th>
													<th></th>
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
				url: '../servicios/firma_digital/consultar_creditos_marcados.php',
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
								if(credito.unidad_negocio == null){ credito.unidad_negocio = ''; }
								if(credito.nombre_empresa == null){ credito.nombre_empresa = ''; }
								if(credito.nro_libranza == null){ credito.nro_libranza = ''; }
								if(credito.telefono == null){ credito.telefono = ''; }

								rowsHtml += '<tr>';
									rowsHtml += '<td>'+credito.id_simulacion+'</td>';
									rowsHtml += '<td>'+credito.nombre+'</td>';
									rowsHtml += '<td>'+credito.cedula+'</td>';
									rowsHtml += '<td>'+credito.pagaduria+'</td>';
									rowsHtml += '<td>'+credito.unidad_negocio+'</td>';
									rowsHtml += '<td>'+credito.nombre_empresa+'</td>';
									rowsHtml += '<td>'+credito.nro_libranza+'</td>';
									rowsHtml += '<td>'+credito.telefono+'</td>';
									rowsHtml += '<td class="text-end" style="display: revert; flex-direction:row;">'+credito.opciones+'</td>';
									rowsHtml += '<td class="text-end" style="display: revert; flex-direction:row;">'+credito.opciones2+'</td>';
									rowsHtml += '<td class="text-end" style="display: revert; flex-direction:row; max-width: 30px !important; width: 20px;">'+credito.opciones3+'</td>';
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

		function credito_firmado(id, elemento){

			Swal.fire({
				title: '¿Está seguro de marcar como firmado este credito?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/firma_digital/credito_firmado.php',
						type: 'POST',
						data: { id_simulacion : id },
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
					Swal.fire('No se Pudo Eliminar', '', 'error')
				}
			})
		}


		function duplicar_credito(id, elemento){

			Swal.fire({
				title: '¿Está seguro de reenviar a firmar este credito?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				allowOutsideClick: false,
            	allowEscapeKey: false,
				denyButtonText: 'Continuar',
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/firma_digital/duplicar_credito.php',
						type: 'POST',
						data: { id_simulacion : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Firmado Exitosamente', '', 'success');
								loadTabla('');
								var win = window.open("simulador.php?id_simulacion="+json.id_simulacion+"&amp;tipo_comercial_buscar=&amp;id_simulacion_buscar=528173&amp;fecha_inicialbd=&amp;fecha_inicialbm=&amp;fecha_inicialba=&amp;fecha_finalbd=&amp;fecha_finalbm=&amp;fecha_finalba=&amp;fechades_inicialbd=&amp;fechades_inicialbm=&amp;fechades_inicialba=&amp;fechades_finalbd=&amp;fechades_finalbm=&amp;fechades_finalba=&amp;fechaprod_inicialbm=&amp;fechaprod_inicialba=&amp;fechaprod_finalbm=&amp;fechaprod_finalba=&amp;descripcion_busqueda=&amp;id_simulacion_buscar="+json.id_simulacion+"&amp;unidadnegociob=&amp;sectorb=&amp;pagaduriab=&amp;tipo_comercialb=&amp;id_comercialb=&amp;estadob=&amp;decisionb=&amp;id_subestadob=&amp;id_oficinab=&amp;tipo_pagareb=&amp;visualizarb=&amp;calificacionb=&amp;statusb=&amp;buscar=1&amp;page=0", '_blank');								
							}else {
								Swal.fire('No se Pudo Eliminar', '', 'error')
							}
						}
					});
				}
			})
		}

		function SolicitarFirmar(id, correo, element){
			console.log(id);
			Swal.fire({
				title: 'Por favor confirmar correo',
				text: '' + correo,
				allowOutsideClick: false,
            	allowEscapeKey: false,
				showDenyButton: true,
				showCancelButton: true,
				confirmButtonText: 'Enviar',
				denyButtonText: `Cambiar Correo`,
				cancelButtonText: `Cancelar` 
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {
					Swal.fire({
			            title: 'Por favor aguarde unos segundos',
			            text: 'Procesando...',
			            allowOutsideClick: false,
			            allowEscapeKey: false
			        });
			        
			        Swal.showLoading();
			        
			        var info = JSON.stringify({ 'id_simulacion': id });
			        $.ajax({
			            url: '../servicios/firma_digital/enviar_correo_experian_firma.php',
			            data: info,
			            type: 'POST',
			            dataType: 'json',
			            success: function (json) {
			                
			                Swal.close();
			    
			                if (json.code == "200") {
			                    Swal.fire({
			                        position: 'center',
			                        icon: 'success',
			                        title: json.mensaje,
			                        showConfirmButton: false,
			                        timer: 1500
			                    });

			                } else {
			                    Swal.fire({
			                        position: 'center',
			                        icon: 'error',
			                        title: json.mensaje,
			                        showConfirmButton: false,
			                        timer: 1500
			                    });
			                }
			            },
			            error: function (xhr, status) {
			                alert('Disculpe, No se pudo enviar correo de validación de identidad del cliente.');
			            }
			        });
				} else if (result.isDenied) {
					Swal.fire({
						title: 'Ingrese el correo del cliente',
						input: 'text',						
						showCancelButton: true,
						allowOutsideClick: false,
            			allowEscapeKey: false,
						confirmButtonText: 'Actualizar',
						cancelButtonText: `Cancelar`, 
						showLoaderOnConfirm: true,
						inputAttributes: {
							input: 'email',
							required: 'true'
						},
						preConfirm: (email) => {
							if(validarEmail(email)){
								var datos = { id_simulacion : id, email : email };
								$.ajax({
									url: '../servicios/firma_digital/actualizar_correo.php',
									type: 'POST',
									data: datos,
									dataType : 'json',
									success: function(json) {
										Swal.close();
				    
						                if (json.code == "200") {
						                    Swal.fire({
						                        position: 'center',
						                        icon: 'success',
						                        title: json.mensaje,
						                        showConfirmButton: false,
						                        timer: 1500
						                    });

						                    loadTabla('');
						                } else {
						                    Swal.fire({
						                        position: 'center',
						                        icon: 'error',
						                        title: json.mensaje,
						                        showConfirmButton: false,
						                        timer: 1500
						                    });
						                }
									},
									error: function(json){									
										Swal.fire({
					                        position: 'center',
					                        icon: 'error',
					                        title: 'Algo ha pasado!',
					                        showConfirmButton: false,
					                        timer: 1500
					                    }); 
									}
								});
							} else {
								Swal.showValidationMessage('Correo Invalido');   
							}
						}
					})
				}
			})
		}

		function validarEmail(valor) {
			if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
				validacion = true;
			} else {
				validacion = false;
			}

			return validacion;
		}

	</script>
	
<?php 
	include("bottom.php");
?>