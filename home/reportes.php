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
				<form action="">
					<div class="tab-pane active" id="tab_kredit" role="tabpanel">
						<button type="button" data-bs-toggle="modal" data-bs-target="#modalAddReport" class="btn btn-success btn-sm" onclick="modalSaveReport(0, 'add');"><span>Agregar Reporte</span></button>
						<table class="table" id="tablaReportes">
							<thead>
								<tr>
									<th>ID</th>
									<th>Tipo</th>
									<th>Reporte</th>
									<th>URL</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>		
				</form>			
			</div>
		</div>		
	</div>

	<div class="modal modal-blur fade modal-tabler" id="modalAddReport" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">NUEVO REPORTE</h5>
					<input type="hidden" id="idReporteModal">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-lg-3">
							<div class="mb-1">
								<label class="form-label">TIPO REPORTE</label>
								<select id="idTipoModal" class="form-select">
									<option value="1">SEAS</option>
									<option value="2">POWER BI</option>
								</select>
							</div>
						</div>

						<div class="col-lg-9">
							<div class="mb-1">
								<label class="form-label">DESCRIPCION</label>
								<input id="descripcionModal" type="text" class="form-control">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="mb-3">
								<label class="form-label">URL</label>
								<textarea style="width: 100%;" rows="3" id="urlModal"></textarea>
							</div>
						</div>
					</div>					
				</div>

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="addReport(); return false;" class="btn btn-primary ms-auto">
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

		function modalSaveReport(id_reporte, opcion){

			if(opcion == 'add'){
				$("#btnSaveModal").attr("name", "add");
			}else{
				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...'
				});

				Swal.showLoading();

				$("#btnSaveModal").attr("name", "edit");
				
				$.ajax({
					url: '../servicios/configuracion/reportes/consultar_reporte.php',
					type: 'POST',
					data: { id_reporte : id_reporte, opcion : 'consultar_reporte'},
					dataType : 'json',
					success: function(json) {

						if(json.code == 200){
							json.data.forEach(function(reporte, index) {
								$("#idReporteModal").val(reporte.id_reporte);
								$("#idTipoModal").val(reporte.tipo_reporte);
								$("#descripcionModal").val(reporte.descripcion);
								$("#urlModal").val(reporte.url);
							});
						}else{
							Swal.fire('Error al Consultar Información de Reporte', '', 'error');
						}

						Swal.close();
					}
				});
			}			
		}

		function loadTabla(){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$("#tablaReportes tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/configuracion/reportes/consultar_reportes.php',
				type: 'POST',
				data: { opcion : 'consultar_reportes'},
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){
							json.data.forEach(function(reporte, index) {
								if(reporte.tipo_reporte == 1){
									text_tipo = 'SEAS';
								}else if(reporte.tipo_reporte == 2){
									text_tipo = 'POWER BI';
								}
								rowsHtml += '<tr>';
									rowsHtml += '<td>'+reporte.id_reporte+'</td>';
									rowsHtml += '<td>'+text_tipo+'</td>';
									rowsHtml += '<td>'+reporte.descripcion+'</td>';
									rowsHtml += '<td>'+reporte.url+'</td>';
									rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">'+reporte.opciones+'</td>';
								rowsHtml += '</tr>';
							});
						}											
					}
					if ( $.fn.DataTable.isDataTable('#tablaReportes') ) {
					  $('#tablaReportes').DataTable().destroy();
					}

					$('#tablaReportes tbody').empty();

					$("#tablaReportes"+" tbody").html(rowsHtml);
						
					$('#tablaReportes').DataTable({
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

		function addReport(){

			var error = false;

			if($("#idTipoModal").val() == ''){
				error = true;
				Swal.fire('Debe completar Tipo de Reporte', '', 'error')
			}
			else if($("#descripcionModal").val() == ''){
				error = true;
				Swal.fire('Debe completar la Descripción', '', 'error')
			}
			else if($("#urlModal").val() == ''){
				error = true;
				Swal.fire('Debe completar la URL', '', 'error')
			}

			if(error){ return false; }
			
			var data = {
				tipo_reporte : $("#idTipoModal").val(),
				descripcion : $("#descripcionModal").val(),
				url : $("#urlModal").val(),
				opcion : $("#btnSaveModal").attr("name"),
				id_reporte : $("#idReporteModal").val()
			}

			$.ajax({
				url: '../servicios/configuracion/reportes/agregar_reporte.php',
				type: 'POST',
				data: data,
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200){
						$(".btn-close").trigger("click");
						Swal.fire('Guardado Exitosamente', '', 'success');
						loadTabla();
					}else {
						Swal.fire(json.mensaje, '', 'error')
					}
				}
			});
		}

		function deleteReport(id){

			Swal.fire({
				title: '¿Está seguro de eliminar este Reporte?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/configuracion/reportes/eliminar_reporte.php',
						type: 'POST',
						data: { id_reporte : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
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