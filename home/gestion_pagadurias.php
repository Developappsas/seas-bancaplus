<?php
include('../functions.php');
include('./top.php');

$link = conectar_utf();
if (!$_SESSION["S_LOGIN"] && ($_SESSION["S_TIPO"] != "ADMINISTRADOR" || $_SESSION["S_TIPO"] != "COORD_VISADO" || $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO")) {
	exit;
}
?>
<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/demo.min.css" rel="stylesheet" />
<link href="../plugins/DataTables/datatables.min.css?v=4" rel="stylesheet">
<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">
<style type="text/css">
	.tab-pane {
		padding: 20px !important;
	}

	.nav-tabs .nav-item.show .nav-link,
	.nav-tabs .nav-link.active {
		color: #ffffff !important;
		background-color: #4299e1 !important;
	}
	.modal-lg, .modal-xl {
		max-width: 800px;
	}
</style>


<div class="container-xl">
	<div class="row row-cards">
		<div class="col-12">
			<form action="" class="card">
				<div class="card-header">
					<ul class="nav nav-pills card-header-pills">
						<li class="nav-item" style="font-weight: bold;">PAGADURIAS</li>
					</ul>
				</div>

				<div class="card-body ">
					<div class="row">
						<div class="col-md-12">
							<div class="card-table table-responsive">
								<table class="table" id="tablaGestionPagadurias">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>Identificacion</th>
											<th>Plazo</th>
											<th>Visado</th>
											<th>Incorporacion</th>
											<th>Nombre Contacto</th>
											<th>Telefono Contacto</th>
											<th>Correo Contacto</th>
											<th>Ciudad</th>
											<th>Codigo Convenio</th>
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

<div class="modal modal-blur fade modal-tabler" id="modalAddPagaduria" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="titulo-ModalPagaduria">NUEVA PAGADURIA</h5>
				<input type="hidden" id="idPagaduriaModal">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<div class="row" style="margin-bottom: 10px;">
					<div class="col-lg-6">
						<label class="form-label">NOMBRE</label>
						<input type="text" class="form-control" id="nombrePagaduriaModal" />
					</div>

					<div class="col-lg-4">
						<label class="form-label">NOMBRE COMPLETO</label>
						<input type="text" class="form-control" id="nombreCompletoPagaduriaModal" />
					</div>

					<div class="col-lg-2">
						<label class="form-label">SECTOR</label>
						<select id="sectorPagaduriaModal" class="form-select">
							<option selected value="PUBLICO">PUBLICO</option>
							<option value="PRIVADO">PRIVADO</option>
						</select>
					</div>
				</div>

				<div class="row" style="margin-bottom: 10px;">
					<div class="col-lg-3">
						<label class="form-label">IDENTIFICACION</label>
						<input type="text" class="form-control" id="identificacionPagaduriaModal" />
					</div>

					<div class="col-lg-5">
						<label class="form-label">CIUDAD</label>
						<select id="ciudadPagaduriaModal" class="form-select">
						</select>
					</div>

					<div class="col-lg-4">
						<label class="form-label">DIRECCION</label>
						<input type="text" class="form-control" id="direccionPagaduriaModal" />
					</div>
				</div>

				<div class="row" style="margin-bottom: 10px;">
					<div class="col-lg-6">
						<label class="form-label">NOMBRE CONTACTO</label>
						<input type="text" class="form-control" id="nombreContactoPagaduriaModal" />
					</div>

					<div class="col-lg-6">
						<label class="form-label">CORREO CONTACTO</label>
						<input type="text" class="form-control" id="correoContactoPagaduriaModal" />
					</div>
				</div>

				<div class="row" style="margin-bottom: 10px;">
					<div class="col-lg-3">
						<label class="form-label">TELEFONO CONTACTO</label>
						<input type="text" class="form-control" id="telefonoContactoPagaduriaModal" />
					</div>

					<div class="col-lg-3">
						<label class="form-label">VISADO</label>
						<select id="visadoPagaduriaModal" class="form-select">
							<option value="1">VIRTUAL</option>
							<option value="2">OFICIAL</option>
							<option value="3">MIXTA</option>
							<option value="0">NO REQUIERE</option>
						</select>
					</div>

					<div class="col-lg-3">
						<label class="form-label">INCORPORACION</label>
						<select id="incorporacionPagaduriaModal" class="form-select">
							<option value="1">SI</option>
							<option value="0">NO</option>
						</select>
					</div>

					<div class="col-lg-3">
						<label class="form-label">CODIGO CONVENIO</label>
						<input type="text" class="form-control" id="codConvenioPagaduriaModal" />
					</div>
				</div>

				<div class="row" style="margin-bottom: 10px;">
					<div class="col-lg-4">
						<label class="form-label">PLAZO</label>
						<input type="num" class="form-control" id="plazoPagaduriaModal" />
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
					CANCELAR
				</a>
				<a name="add" id="btnSaveModal" onclick="addPagadurias(); return false;" class="btn btn-primary ms-auto">
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

<div class="modal modal-blur fade modal-tabler" id="modalConvenios" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">CONVENIOS</h5>
				<input type="hidden" id="idConveniosModal">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-4">
						<label class="form-label">Fecha Inicio</label>
						<input type="text" class="form-control" id="fechaInicialConvenioModal" />
					</div>

					<div class="col-lg-4">
						<label class="form-label">Fecha Fin</label>
						<input type="text" class="form-control" id="fechaFinConvenioModal" />
					</div>

					<div class="col-lg-4">
						<label class="form-label">Soporte</label>
						<input type="file" class="form-control" id="soporteConvenioModal" name="soporteConvenioModal" />
					</div>

					<div class="col-lg-4">
						<label class="form-label">&nbsp;&nbsp;</label>
						<button type="button" class="btn btn-success btn-sm" onclick="addConvenio();"><span>Agregar Convenio</span></button>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-md-12">
						<div class="card-table table-responsive">
							<table class="table" id="tablaGestionConvenios">
								<thead>
									<tr>
										<th>Fecha Inicio</th>
										<th>Fecha Fin</th>
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
	$(document).ready(function() {
		loadTabla(0);
	});

	function openModalConvenios(id_pagaduria) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();
		Swal.close();
		loadTablaConvenios(id_pagaduria);
		$("#fechaFinConvenioModal").val("");
		$("#fechaInicialConvenioModal").val("");
		$("#idConveniosModal").val(id_pagaduria);

	}


	function openModalPagadurias(opcion, id) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();



		Swal.close();

		if (opcion == 'CREAR') {
			llenarSelectsCiudades('');

			$("#btnSaveModal").attr("name", "CREAR");
			$("#titulo-ModalPagaduria").html("CREAR PAGADURIA");
			$("#nombrePagaduriaModal").val("");
			$("#identificacionPagaduriaModal").val("");
			$("#nombreCompletoPagaduriaModal").val("");
			$("#codConvenioPagaduriaModal").val("");
			$("#direccionPagaduriaModal").val("");
			$("#nombreContactoPagaduriaModal").val("");
			$("#telefonoContactoPagaduriaModal").val("");
			$("#correoContactoPagaduriaModal").val("");
			$("#visadoPagaduriaModal").val("");
			$("#incorporacionPagaduriaModal").val("");
			$("#idPagaduriaModal").val(0);
			$("#sectorPagaduriaModal").val("");
			$("#plazoPagaduriaModal").val("");
		} else {
			$("#idConveniosModal").val(id);
			$("#idPagaduriaModal").val(id);
			$("#titulo-ModalPagaduria").html("EDITAR PAGADURIA");
			$("#btnSaveModal").attr("name", "EDITAR");
			$.ajax({
				url: '../servicios/pagadurias/consultar_pagadurias.php',
				type: 'POST',
				data: {
					id_pagaduria: id
				},
				dataType: 'json',
				success: function(json) {

					if (json.code == 200) {
						json.data.forEach(function(pagadurias, index) {
							$("#nombrePagaduriaModal").val(pagadurias.nombre);
							$("#nombreCompletoPagaduriaModal").val(pagadurias.nombre_completo);
							$("#identificacionPagaduriaModal").val(pagadurias.identificacion);
							$("#codConvenioPagaduriaModal").val(pagadurias.codigo_convenio);
							$("#direccionPagaduriaModal").val(pagadurias.direccion);
							$("#nombreContactoPagaduriaModal").val(pagadurias.nombre_contacto);
							$("#telefonoContactoPagaduriaModal").val(pagadurias.telefono_contacto);
							$("#correoContactoPagaduriaModal").val(pagadurias.correo_contacto);
							$("#visadoPagaduriaModal").val(pagadurias.visado).change();
							$("#incorporacionPagaduriaModal").val(pagadurias.incorporacion).change();
							$("#sectorPagaduriaModal").val(pagadurias.sector).change();
							$("#plazoPagaduriaModal").val(pagadurias.plazo);

							llenarSelectsCiudades(pagadurias.id_municipio);

						});
					} else {
						Swal.fire('Error al Consultar Información de Tasa', '', 'error');
					}
				}
			});
		}
	}


	function llenarSelectsCiudades(ciudad) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();

		var rowsHtml = '';

		$.ajax({
			url: '../servicios/pagadurias/consultar_ciudades.php',
			type: 'POST',
			dataType: 'json',
			success: function(json) {
				if (json.code == 200 || json.code == 300) {
					if (json.code == 200) {
						rowsHtml += '<option value="" selected>Selecione</option>';
						json.data.forEach(function(ciudades, index) {
							if (ciudades.ciudad_id == ciudad) {
								rowsHtml += '<option selected value="' + ciudades.ciudad_id + '">' + ciudades.ciudad + '</option>';
							} else {
								rowsHtml += '<option value="' + ciudades.ciudad_id + '">' + ciudades.ciudad + '</option>';
							}
						});
					}
				}
				$("#ciudadPagaduriaModal").html(rowsHtml);

				Swal.close();

				return false;
			}
		});
	}

	function loadTablaConvenios(id_pagaduria) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();

		var rowsHtml = '';

		if ($.fn.DataTable.isDataTable('#tablaGestionConvenios')) {
			$('#tablaGestionConvenios').DataTable().destroy();
		}

		$("#tablaGestionConvenios tbody").html(rowsHtml);

		$.ajax({
			url: '../servicios/consultar_convenios.php',
			type: 'POST',
			dataType: 'json',
			data: "id_pagaduria=" + id_pagaduria,
			success: function(json) {
				//alert(json);

				if (json.code == 200 || json.code == 300) {

					if (json.code == 200) {
						json.data.forEach(function(convenio, index) {


							rowsHtml += '<tr>';
							rowsHtml += '<td>' + convenio.fecha_inicio + '</td>';
							rowsHtml += '<td>' + convenio.fecha_final + '</td>';

							rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">' + convenio.opciones + '</td>';
							rowsHtml += '</tr>';
						});
					}
				}

				$("#tablaGestionConvenios tbody").html(rowsHtml);

				$('#tablaGestionConvenios').DataTable({

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

	function loadTabla() {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});

		Swal.showLoading();

		var rowsHtml = '';

		if ($.fn.DataTable.isDataTable('#tablaGestionPagadurias')) {
			$('#tablaGestionPagadurias').DataTable().destroy();
		}

		$("#tablaGestionPagadurias tbody").html(rowsHtml);

		$.ajax({
			url: '../servicios/pagadurias/consultar_pagadurias.php',
			type: 'POST',
			dataType: 'json',
			success: function(json) {
				//alert(json);

				if (json.code == 200 || json.code == 300) {

					if (json.code == 200) {
						json.data.forEach(function(pagaduria, index) {


							rowsHtml += '<tr>';
							rowsHtml += '<td>' + pagaduria.nombre + '</td>';
							rowsHtml += '<td>' + pagaduria.identificacion + '</td>';
							rowsHtml += '<td>' + pagaduria.plazo + '</td>';
							rowsHtml += '<td>' + pagaduria.visado + '</td>';
							rowsHtml += '<td>' + pagaduria.incorporacion + '</td>';
							rowsHtml += '<td>' + pagaduria.nombre_contacto + '</td>';
							rowsHtml += '<td>' + pagaduria.telefono_contacto + '</td>';
							rowsHtml += '<td>' + pagaduria.correo_contacto + '</td>';
							rowsHtml += '<td>' + pagaduria.ciudad + '</td>';
							rowsHtml += '<td>' + pagaduria.codigo_convenio + '</td>';
							rowsHtml += '<td class="text-end" style="display: flex; flex-direction:row;">' + pagaduria.opciones + '</td>';
							rowsHtml += '</tr>';
						});
					}
				}

				$("#tablaGestionPagadurias tbody").html(rowsHtml);

				$('#tablaGestionPagadurias').DataTable({
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
						text: 'Crear Pagaduria',
						action: function(e, dt, node, config) {
							openModalPagadurias("CREAR", "");

						},
						attr: {
							titie: 'add a new contact',
							'data-bs-toggle': 'modal',
							'data-bs-target': '#modalAddPagaduria'
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

	function addConvenio() {

		var error = false;

		if ($("#fechaInicialConvenioModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Nombre Pagaduria', '', 'error')
		} else if ($("#fechaFinConvenioModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Identificacion', '', 'error')
		} else if ($("#soporteConvenioModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Incorporacion', '', 'error')
		}else if ($("#plazoPagaduriaModal").val() == '' || $("#plazoPagaduriaModal").val() <=0 || $("#plazoPagaduriaModal").val() > 168) {
			error = true;
			Swal.fire('Plazo fuera de Rango (1 - 168 meses)', '', 'error')
		} 
		if (error) {
			return false;
		}


		var formArchivoAnexo = new FormData();
		formArchivoAnexo.append("opcion", "CREAR");
		formArchivoAnexo.append("id_pagaduria", $("#idConveniosModal").val());
		formArchivoAnexo.append("fecha_inicio", $("#fechaInicialConvenioModal").val());
		formArchivoAnexo.append("fecha_final", $("#fechaFinConvenioModal").val());
		formArchivoAnexo.append("soporte", $("#soporteConvenioModal")[0].files[0]);
		$.ajax({
			url: '../servicios/agregar_convenio.php',
			type: 'POST',
			data: formArchivoAnexo,
			processData: false, // tell jQuery not to process the data
			contentType: false,
			success: function(json) {
				//alert(json);
				if (json.code == 200) {
					$("#fechaInicialConvenioModal").val("");
					$("#fechaFinConvenioModal").val("");
					$("#soporteConvenioModal").val("");
					Swal.fire('Guardado Exitosamente', '', 'success');
					loadTablaConvenios($("#idConveniosModal").val());
				} else {
					Swal.fire(json.mensaje, '', 'error')
				}
			}
		});
	}



	function addPagadurias() {

		var error = false;

		if ($("#nombrePagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Nombre Pagaduria', '', 'error')
		} else if ($("#identificacionPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Identificacion', '', 'error')
		} else if ($("#codConvenioPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Codigo Convenio', '', 'error')
		} else if ($("#direccionPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Direccion', '', 'error')
		}
		/*else if($("#fechaFinModal").val() == ''){
			error = true;
			Swal.fire('Debe completar el Fin de la vigencia', '', 'error')
		}*/
		else if ($("#ciudadPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Ciudad', '', 'error')
		} else if ($("#nombreContactoPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Nombre Contacto', '', 'error')
		} else if ($("#telefonoContactoPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Telefono Contacto', '', 'error')
		} else if ($("#correoContactoPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Correo Contacto', '', 'error')
		} else if ($("#visadoPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Visado', '', 'error')
		} else if ($("#incorporacionPagaduriaModal").val() == '') {
			error = true;
			Swal.fire('Debe completar Incorporacion', '', 'error')
		}
		if (error) {
			return false;
		}

		var data = {
			nombre: $("#nombrePagaduriaModal").val(),
			nombre_completo: $("#nombreCompletoPagaduriaModal").val(),
			identificacion: $("#identificacionPagaduriaModal").val(),
			codigo_convenio: $("#codConvenioPagaduriaModal").val(),
			direccion: $("#direccionPagaduriaModal").val(),
			ciudad: $("#ciudadPagaduriaModal").val(),
			nombre_contacto: $("#nombreContactoPagaduriaModal").val(),
			telefono_contacto: $("#telefonoContactoPagaduriaModal").val(),
			correo_contacto: $("#correoContactoPagaduriaModal").val(),
			visado: $("#visadoPagaduriaModal").val(),
			incorporacion: $("#incorporacionPagaduriaModal").val(),
			opcion: $("#btnSaveModal").attr("name"),
			id_pagaduria: $("#idPagaduriaModal").val(),
			sector: $("#sectorPagaduriaModal").val(),
			plazo: $("#plazoPagaduriaModal").val()
		}

		$.ajax({
			url: '../servicios/pagadurias/agregar_pagaduria.php',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(json) {

				if (json.code == 200) {
					$(".btn-close").trigger("click");
					Swal.fire('Guardado Exitosamente', '', 'success');
					loadTabla($("#idPagaduriaModal").val());
				} else {
					Swal.fire(json.mensaje, '', 'error')
				}
			}
		});
	}

	function deletePagaduria(id) {

		Swal.fire({
			title: '¿Está seguro de eliminar esta Pagaduria?',
			showConfirmButton: false,
			showDenyButton: true,
			showCancelButton: true,
			denyButtonText: `Continuar`,
		}).then((result) => {
			if (result.isDenied) {

				$.ajax({
					url: '../servicios/pagadurias/eliminar_pagaduria.php',
					type: 'POST',
					data: {
						id_pagaduria: id
					},
					dataType: 'json',
					success: function(json) {

						if (json.code == 200) {
							Swal.fire('Eliminado Exitosamente', '', 'success');
							loadTabla(0);
						} else {
							Swal.fire(json.mensaje, '', 'error')
						}
					}
				});
			} else {
				Swal.fire('No se Pudo Eliminar', '', 'error')
			}
		})
	}


	function habilitarPagaduria(id) {

		Swal.fire({
			title: '¿Está seguro de Habilitar/Deshabilitar esta Pagaduria?',
			showConfirmButton: false,
			showDenyButton: true,
			showCancelButton: true,
			denyButtonText: `Continuar`,
		}).then((result) => {
			if (result.isDenied) {

				$.ajax({
					url: '../servicios/pagadurias/habilitar_pagaduria.php',
					type: 'POST',
					data: {
						id_pagaduria: id
					},
					dataType: 'json',
					success: function(json) {

						if (json.code == 200) {
							Swal.fire('Eliminado Exitosamente', '', 'success');
							loadTabla(0);
						} else {
							Swal.fire(json.mensaje, '', 'error')
						}
					}
				});
			} else {
				Swal.fire('No se Pudo Eliminar', '', 'error')
			}
		})
	}

	function eliminarConvenio(id) {

		Swal.fire({
			title: '¿Está seguro de eliminar este Convenio?',
			showConfirmButton: false,
			showDenyButton: true,
			showCancelButton: true,
			denyButtonText: 'Continuar',
		}).then((result) => {
			if (result.isDenied) {

				$.ajax({
					url: '../servicios/eliminar_convenio.php',
					type: 'POST',
					data: {
						id_convenio: id
					},
					dataType: 'json',
					success: function(json) {

						if (json.code == 200) {
							Swal.fire('Eliminado Exitosamente', '', 'success');
							loadTablaConvenios($("#idConveniosModal").val());
						} else {
							Swal.fire(json.mensaje, '', 'error')
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