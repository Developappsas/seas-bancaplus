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

	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td class="titulo"><center><b>Rangos de Edad Para Seguro</b><br><br></center></td>
		</tr>
	</table>

	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Estado<br> 
								<select id="estado" name="estado" style="margin: 2px 10px 6px 0; width: 100px;">
									<option selected value="1">ACTIVO</option>
									<option value="0">INACTIVO</option>
								</select>
							</td>

							<td valign="bottom">Edad Inicio<br><input type="text" id="edad_inicio" name="edad_inicio" maxlength="10" size="8" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

							<td valign="bottom">Edad Fin<br><input type="text" id="edad_fin" name="edad_fin" maxlength="10" size="8" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

							<td valign="bottom">Seguro Activos<br><input type="text" id="valor_por_millon_seguro" name="valor_por_millon_seguro" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

							<td valign="bottom">PARCIAL<br><input type="text" id="valor_por_millon_seguro_parcial" name="valor_por_millon_seguro_parcial" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

							<td valign="bottom">&nbsp;<br><input onclick="addRangoEdad();" type="button" value="Crear Rango"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>


  	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-12">	
				<form action="" class="card">			
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<div class="card-table table-responsive">
									<!--<button type="button" data-bs-toggle="modal" data-bs-target="#modalAddTasa" class="btn btn-success btn-sm" onclick="modalSaveTasas(0, 'add', 0);"><span>Agregar Rango</span></button>-->
									<table class="table" id="tabla" style="text-align: center;">
										<thead>
											<tr>
												<th>Rango Incio</th>
												<th>Rango Final</th>
												<th>Valor X Millon</th>
												<th>Valor X Millon Parcial</th>
												<th>Estado</th>
												<th>F. Creación</th>
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
				</form>			
			</div>
		</div>		
	</div>

	<div class="modal modal-blur fade modal-tabler" id="modalAddTasa" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">EDITAR RANGO <span id="nombreRango"></span> </h5>
					<input type="hidden" id="idEdadRangoSeguro">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">ESTADO</label>
								<select id="estadoModal" class="form-select">
									<option></option>
									<option value="1">ACTIVO</option>
									<option value="0">INACTIVO</option>
								</select>
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">Edad Inicio</label>
								<input type="number" id="rangoEdadInicio" class="form-control">
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">Edad Fin</label>
								<input type="number" id="rangoEdadFin" class="form-control">
							</div>
						</div>

						<div class="col-lg-2">
							<div class="mb-1">
								<label class="form-label">X MILLON</label>
								<input type="number" id="valorPorMillonSeguro" class="form-control">
							</div>
						</div>

						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">X MILLON PARCIAL</label>
								<input type="number" id="valorPorMillonSeguroParcial" class="form-control">
							</div>
						</div>
					</div>		
				</div>

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="editarRangoEdad(); return false;" class="btn btn-primary ms-auto">
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
			loadTabla();	
		});

		function modalSaveRangoEdad(id_edad_rango_seguro){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			$("#idEdadRangoSeguro").val('');
			$("#rangoEdadInicio").val('');
			$("#rangoEdadFin").val('');
			$("#valorPorMillonSeguro").val('');
			$("#valorPorMillonSeguroParcial").val('');
			$("#estadoModal").val('');

			$("#id_edad_rango_seguro").val(id_edad_rango_seguro);

			$.ajax({
				url: '../servicios/Simulaciones/consultar_rangos_edad_seguro.php',
				type: 'POST',
				data: { id_edad_rango_seguro : id_edad_rango_seguro },
				dataType : 'json',
				success: function(json) {

					if(json.codigo == 200){
						json.data.forEach(function(rangos, index) {
							$("#idEdadRangoSeguro").val(rangos.id_edad_rango_seguro);
							$("#rangoEdadInicio").val(rangos.edad_rango_inicio);
							$("#rangoEdadFin").val(rangos.edad_rango_fin);
							$("#valorPorMillonSeguro").val(rangos.valor_por_millon);
							$("#valorPorMillonSeguroParcial").val(rangos.valor_por_millon_parcial);
							$("#estadoModal").val(rangos.estado);
						});
					}else{
						Swal.fire('Error al Consultar Información de Rango de Edad', '', 'error');
					}

					Swal.close();
				}
			});	
		}

		function loadTabla(){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			if ($.fn.DataTable.isDataTable('#tabla')) {
				$('#tabla').DataTable().destroy();
			}

			$("#tabla tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/Simulaciones/consultar_rangos_edad_seguro.php',
				type: 'POST',
				data: null,
				dataType : 'json',
				success: function(json) {
					
					if(json.codigo == 200 || json.codigo == 300){

						if(json.codigo == 200){
							json.data.forEach(function(rangos, index) {
								if(rangos.estado == 1){ var texto_estado = 'ACTIVO'; }else { texto_estado = 'INACTIVO'; }

								rowsHtml += '<tr>';
									rowsHtml += '<td>'+rangos.edad_rango_inicio+' Años</td>';
									rowsHtml += '<td>'+rangos.edad_rango_fin+' Años</td>';
									rowsHtml += '<td>'+rangos.valor_por_millon+'</td>';
									rowsHtml += '<td>'+rangos.valor_por_millon_parcial+'</td>';
									rowsHtml += '<td estado="'+rangos.estado+'">'+texto_estado+'</td>';
									rowsHtml += '<td>'+rangos.fecha_creacion+'</td>';
									rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">'+rangos.opciones+'</td>';
								rowsHtml += '</tr>';
							});
						}											
					}

					$("#tabla"+" tbody").html(rowsHtml);
						
					$('#tabla').DataTable({
						dom: 'Bfrtip',
						buttons: [{	
							extend: 'excelHtml5',
							title: 'FDC',
							footer:false,
						},{
							text: '<button>Actualizar</button>',
							action: function ( e, dt, node, config ) {
								loadTabla();
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

		function addRangoEdad(){

			var error = false;

			if($("#estado").val() == ''){
				error = true;
				Swal.fire('Debe completar Estado', '', 'error')
			}
			else if($("#edad_inicio").val() == ''){
				error = true;
				Swal.fire('Debe completar Edad Inicio', '', 'error')
			}
			else if($("#edad_fin").val() == ''){
				error = true;
				Swal.fire('Debe completar Edad Fin', '', 'error')
			}
			else if($("#valor_por_millon_seguro").val() == ''){
				error = true;
				Swal.fire('Debe completar Valor Por millon', '', 'error')
			}
			else if($("#valor_por_millon_seguro_parcial").val() == ''){
				error = true;
				Swal.fire('Debe completar Valor Por millon Parcial', '', 'error')
			}

			if(error){ return false; }
			
			var data = {
				edad_rango_inicio : $("#edad_inicio").val(),
				edad_rango_fin : $("#edad_fin").val(),
				valor_por_millon : $("#valor_por_millon_seguro").val(),
				valor_por_millon_parcial : $("#valor_por_millon_seguro_parcial").val(),
				estado : $("#estado").val()
			}

			$.ajax({
				url: '../servicios/Simulaciones/agregar_rango_edad_seguro.php',
				type: 'POST',
				data: data,
				dataType : 'json',
				success: function(json) {
					
					if(json.codigo == 200){
						$("#edad_inicio").val("");
						$("#edad_fin").val("");
						$("#valor_por_millon_seguro").val("");
						$("#valor_por_millon_seguro_parcial").val("");
						$("#estado").val("1");

						Swal.fire('Guardado Exitosamente', 'success');
						loadTabla();
					}else {
						Swal.fire(json.mensaje, '', 'error')
					}
				}
			});
		}

		function editarRangoEdad(){

			var error = false;

			if($("#estadoModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Estado', '', 'error')
			}
			else if($("#rangoEdadInicio").val() == ''){
				error = true;
				Swal.fire('Debe completar Edad Inicio', '', 'error')
			}
			else if($("#rangoEdadFin").val() == ''){
				error = true;
				Swal.fire('Debe completar Edad Fin', '', 'error')
			}
			else if($("#valorPorMillonSeguro").val() == ''){
				error = true;
				Swal.fire('Debe completar Valor Por millon', '', 'error')
			}
			else if($("#valorPorMillonSeguroParcial").val() == ''){
				error = true;
				Swal.fire('Debe completar Valor Por millon Parcial', '', 'error')
			}

			if(error){ return false; }
			
			var data = {
				id_edad_rango_seguro : $("#idEdadRangoSeguro").val(),
				edad_rango_inicio : $("#rangoEdadInicio").val(),
				edad_rango_fin : $("#rangoEdadFin").val(),
				valor_por_millon : $("#valorPorMillonSeguro").val(),
				valor_por_millon_parcial : $("#valorPorMillonSeguroParcial").val(),
				estado : $("#estadoModal").val()
			}

			$.ajax({
				url: '../servicios/Simulaciones/actualizar_rango_edad_seguro.php',
				type: 'POST',
				data: data,
				dataType : 'json',
				success: function(json) {
					
					if(json.codigo == 200){
						$(".btn-close").trigger("click");
						Swal.fire('Guardado Exitosamente', 'success');
						loadTabla();
					}else {
						Swal.fire(json.mensaje, '', 'error')
					}
				}
			});
		}

		function deleteRangoEdad(id_edad_rango_seguro){

			Swal.fire({
				title: '¿Está seguro de eliminar este Rango de Edad?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/Simulaciones/eliminar_rango_edad_seguro.php',
						type: 'POST',
						data: { id_edad_rango_seguro : id_edad_rango_seguro },
						dataType : 'json',
						success: function(json) {
							
							if(json.codigo == 200){
								Swal.fire('Eliminado Exitosamente', '', 'success');
								loadTabla();
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