<?php
	include ('../functions.php');
	include ('./top.php');

	$link = conectar_utf();
	//if (!$_SESSION["S_LOGIN"] && ($_SESSION["S_TIPO"] != "ADMINISTRADOR" || $_SESSION["S_TIPO"] != "OPERACIONES" || $_SESSION["S_TIPO"] !== "CARTERA" || $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
	//{
		//exit;
	//}
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
							<li class="nav-item" style="font-weight: bold;">AGDENDA COMERCIALES</li>
							<input id="textoSimulaciones" type="hidden"/>
						</ul>						
					</div>

					<div class="card-body " >
						<div class="row">
							

							<div class="col-md-12">
								<div class="card-table table-responsive">
									<table class="table" id="tablaAgendaComerciales">
									
									<thead>
										<tr>
										<th>ID Registro</th>
											<th>Nombre</th>
											<th>Telefono</th>
											<th>Correo</th>
											<th>Fecha Creacion</th>
											<th>Estado</th>
											<th>Asignado A</th>
											
									
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

	<div class="modal modal-blur fade modal-tabler" id="modalAgendaComercial" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="titulo-ModalAgendaComercial">REGISTRAR CLIENTE</h5>
					<input type="hidden" id="idRegistroAgendaComercial">
					<button type="button" id="btnCloseClienteAgendaComercial" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

				

                    <div class="row">
						<div class="col-lg-4">
								<label class="form-label">Nombre</label>
								<input type="text" class="form-control" id="nombreAgendaComercial"/>
						</div>
						<div class="col-lg-4">
								<label class="form-label">Apellido</label>
								<input type="text" class="form-control" id="apellidoAgendaComercial"/>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Telefono</label>
							<input type="text" class="form-control" id="telefonoAgendaComercial"/>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Correo</label>
							<input type="text" class="form-control" id="correoAgendaComercial"/>
						</div>

						
					</div>
					<div class="row">
						<div class="col-lg-12">
						<label class="form-label">Observacion</label>
								<textarea class="form-control" id="observacionAgendaComercial" rows="3"></textarea>
						</div>
					</div>

						
				</div>					

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="addAgendaComercial(); return false;" class="btn btn-primary ms-auto">
						<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
						GUARDAR
					</a>
				</div>
			</div>
		</div>
	</div>


	<div class="modal modal-blur fade modal-tabler" id="modalAsignarAgendaComercial" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="titulo-ModalAgendaComercial">ASIGNAR CLIENTE</h5>
					<input type="hidden" id="idRegistroAsignarAgendaComercial">
					<input type="hidden" id="estadoAsignarAgendaComercial">
					<button type="button" id="btnCloseAsignarClienteAgendaComercial" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

				

                    <div class="row">
						<div class="col-lg-12">
								<label class="form-label">Usuario</label>
								<select id="usuarioAsignarClienteAgendaComercial" class="form-select">
								</select>
						</div>
						
						
					</div>
					<div class="row">
						<div class="col-lg-12">
						<label class="form-label">Observacion</label>
								<textarea class="form-control" id="observacionAsignarClienteAgendaComercial" rows="3"></textarea>
						</div>
					</div>

						
				</div>					

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="addAsignarAgendaComercial(); return false;" class="btn btn-primary ms-auto">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script> 

	
	<!-- Tabler Core -->
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>


	<script type="text/javascript">
	$(document).ready(function() {
		loadTabla(0);
	});



		function loadTabla() {
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			if ($.fn.DataTable.isDataTable('#tablaAgendaComerciales')) {
				$('#tablaAgendaComerciales').DataTable().destroy();
			}

			$("#tablaAgendaComerciales tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/agenda_comerciales/consultar_agenda_comerciales.php',
				type: 'POST',
				data: 'operacion=Consultar_Informacion_Tabla&id_usuario=<?php echo $_SESSION["S_IDUSUARIO"]?>',
				dataType: 'json',
				success: function(json) {
					//alert(json);
					console.log(json)

					if (json.codigo == 200 || json.codigo == 300) {

						if (json.codigo == 200) {
							json.data.forEach(function(agenda_comercial, index) {
								rowsHtml += '<tr>';
								rowsHtml += '<td>' + agenda_comercial.id_agenda_comercial + '</td>';
								rowsHtml += '<td>' + agenda_comercial.nombre_cliente + '</td>';
								rowsHtml += '<td>' + agenda_comercial.telefono + '</td>';
								rowsHtml += '<td>' + agenda_comercial.correo + '</td>';
								rowsHtml += '<td>' + agenda_comercial.fecha_creacion + '</td>';
								rowsHtml += '<td>' + agenda_comercial.descripcion_estado + '</td>';
							
								rowsHtml += '<td>' + agenda_comercial.asignado_a + '</td>';
								
								
								rowsHtml += '</tr>';
							});
						}
					}

					$("#tablaAgendaComerciales tbody").html(rowsHtml);

					$('#tablaAgendaComerciales').DataTable({
						dom: 'Bfrtip',
						buttons: [{
							extend: 'excelHtml5',
							title: 'FDC',
							footer: false,
						}, {
							text: '<button>Actualizar</button>',
							action: function(e, dt, node, config) {
								loadTabla(id_unidad_negocio);
							}
						}, {
							text: 'Crear Cliente',
							action: function(e, dt, node, config) {
								openModalAgendaComercial("CREAR", "");

							},
							attr: {
								titie: 'add a new contact',
								'data-bs-toggle': 'modal',
								'data-bs-target': '#modalAgendaComercial'
							}
						}],
						"destroy": true,
						initComplete: function(settings, json) {
							Swal.close();
						},
						"bPaginate": true,
						"bFilter": true,
						"bProcessing": true,
						"pageLength": 40,
						"orderable": false,
						"language": {
							"sProcessing": "Procesando...",
							"sLengthMenu": "Mostrar _MENU_ registros",
							"sZeroRecords": "No se encontraron resultados",
							"sEmptyTable": "Ningún dato disponible en esta tabla",
							"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
							"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
							"sInfoPostFix": "",
							"sSearch": "Buscar:",
							"sUrl": "",
							"sInfoThousands": ",",
							"sLoadingRecords": "Cargando...",
							"oPaginate": {
								"sFirst": "Primero",
								"sLast": "Último",
								"sNext": "Siguiente",
								"sPrevious": "Anterior"
							},
							"oAria": {
								"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
								"sSortDescending": ": Activar para ordenar la columna de manera descendente"
							}
						}
					});

					return false;
				}
			});
		}

		function openModalAgendaComercial(opcion, id) {
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			Swal.close();

			if (opcion == 'CREAR') {

				$("#btnSaveModal").attr("name", "CREAR");
				$("#titulo-ModalAgendaComercial").html("CREAR CLIENTE");
				$("#nombreAgendaComercial").val("");
				$("#identificacionAgendaComercial").val("");
				$("#telefonoAgendaComercial").val("");
				$("#correoAgendaComercial").val("");
				
			} else {
				
			}
		}

		
		function addAgendaComercial(){
			//alert($("#idSimulacionNovedadTituloModal").val());

				var error = false;

				if($("#nombreAgendaComercial").val() == ''){
					error = true;
					Swal.fire('Debe ingresar Nombre', '', 'error')
				}

				if($("#telefonoAgendaComercial").val() == ''){
					error = true;
					Swal.fire('Debe ingresar Telefono', '', 'error')
				}

				if($("#correoAgendaComercial").val() == ''){
					error = true;
					Swal.fire('Debe ingresar Correo', '', 'error')
				}

				if($("#apellidoAgendaComercial").val() == ''){
					error = true;
					Swal.fire('Debe ingresar Apellido', '', 'error')
				}
				
				if(error){ return false; }
				
				var data = {
					
					nombre : $("#nombreAgendaComercial").val(),
					telefono : $("#telefonoAgendaComercial").val(),
					apellido : $("#apellidoAgendaComercial").val(),
					correo : $("#correoAgendaComercial").val(),
					observacion : $("#observacionAgendaComercial").val(),
					operacion:"CREAR_AGENDA_COMERCIAL",
					id_usuario:<?php echo $_SESSION["S_IDUSUARIO"]?>
				}
				console.log(data)
				$.ajax({
							url: '../servicios/agenda_comerciales/crear_cliente_agenda_comercial.php',
							type: 'POST',
							data: data,
							dataType : 'json',
							async:false,
							success: function(json) {
								console.log(json)
								if(json.code == 200){
									$("#btnCloseClienteAgendaComercial").trigger("click");
									loadTabla(0);
							
									
										
						
					
							
								}else {
									Swal.fire(json.mensaje, '', 'error')
								}
							}
						});
			
			
		}

		function llenarSelectsUsuarios(tipo_usuario,idRegsitroAgendaComercial){
			
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/agenda_comerciales/consultar_usuarios_agenda_comercial.php',
				type: 'POST',
				data:"tipo_usuario="+tipo_usuario+"&operacion=Consultar_Tipo_Usuario&id_registro="+idRegsitroAgendaComercial,
				dataType : 'json',
				success: function(json) {
					
					if(json.codigo == 200 || json.codigo == 300){
						if(json.codigo == 200){
							rowsHtml += '<option value="" selected>Selecione</option>';
							json.data.forEach(function(usuario, index) {
								//if(inventario.id == estado_inventario_credito) {
									//rowsHtml += '<option selected value="'+usuario.usuario_id+'">'+usuario.nombre_usuario+'</option>';
								//}else{
									rowsHtml += '<option value="'+usuario.usuario_id+'">'+usuario.nombre_usuario+'</option>';
								//}
							});
						}											
					}
					$("#idRegistroAsignarAgendaComercial").val(idRegsitroAgendaComercial);
					$("#estadoAsignarAgendaComercial").val(tipo_usuario);
					
					$("#usuarioAsignarClienteAgendaComercial").html(rowsHtml);

					Swal.close();

					return false;
				}
			});
		}
		
		function addAsignarAgendaComercial(){
			//alert($("#idSimulacionNovedadTituloModal").val());

				var error = false;

				if($("#usuarioAsignarClienteAgendaComercial").val() == ''){
					error = true;
					Swal.fire('Debe Seleccionar un usuario', '', 'error')
				}

				
				if(error){ return false; }
				
				var data = {
					
					usuario_asignar : $("#usuarioAsignarClienteAgendaComercial").val(),
					observacion : $("#observacionAsignarClienteAgendaComercial").val(),
					id_agenda_comercial:$("#idRegistroAsignarAgendaComercial").val(),
					estadoAsignar:$("#estadoAsignarAgendaComercial").val(),
					operacion:"ASIGNAR_AGENDA_COMERCIAL"
				}
				console.log(data)
				$.ajax({
							url: '../servicios/agenda_comerciales/asignar_cliente_agente_comercial.php',
							type: 'POST',
							data: data,
							dataType : 'json',
							async:false,
							success: function(json) {
								console.log(json)
								if(json.code == 200){
									$("#btnCloseAsignarClienteAgendaComercial").trigger("click");
									loadTabla(0);
							
									
										
						
					
							
								}else {
									Swal.fire(json.mensaje, '', 'error')
								}
							}
						});
			
			
		}
	</script>




	
<?php 
	include("bottom.php");
?>