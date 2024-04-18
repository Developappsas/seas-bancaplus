<?php include('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Usuarios</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="usuarios.php">
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td><a href="usuarios_crear.php">Crear Usuario</a></td>
		</tr>
	</table>
</form>
<hr noshade size=1 width=350>

<form>
	<div id="divTablaUsuarios" style="width: 98%; align: left">
		<table class="tab3" id="tablaUsuarios">

		</table>
	</div>

	<br>
	<p align="center"><input type="submit" value="Borrar" id='btnBorrarUsuarios'></p>
</form>


<div class="modal" id="modalAsociarOficinas" data-animation="slideInOutLeft">
	<div class="modal-dialog">
		<header class="modal-header">
			Asociar Oficinas
			<button type="button" class="close-modal" data-close>x</button>
		</header>
		<section class="modal-content">
			<div id="divTablaAsociarOficinasUsuarios" style="width: 98%; align: left">
				<table class="tab3" id="tablaAsociarOficinasUsuarios">
				</table>
			</div>
			<p align="center"><input type="submit" value="Guardar" id='btnAsociarOficinasUsuariosZonas'></p>
			<input type="hidden" id="idUsuarioAsociarOficinas">

		</section>
		<footer class="modal-footer">
			Derechos reservados Kredit 2021
		</footer>
	</div>
</div>



<div class="modal" id="modalAsociarUsuarios" data-animation="slideInOutLeft">
	<div class="modal-dialog">
		<header class="modal-header">
			Asociar Usuarios
			<button type="button" class="close-modal" data-close>x</button>
		</header>
		<section class="modal-content">
			<div id="divTablaAsociarUsuarios" style="width: 98%; align: left">
				<table class="tab3" id="tablaAsociarUsuarios">
				</table>
			</div>
			<p align="center"><input type="submit" value="Guardar" id='btnAsociarUsuarios'></p>
			<input type="hidden" id="idUsuarioAsociarUsuarios">

		</section>
		<footer class="modal-footer">
			Derechos reservados Kredit 2021
		</footer>
	</div>
</div>

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
<script src="../plugins/modal/modal.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();

		cargarTablaUsuarios();


	});

	function cargarOficinasUsuarios(idUsuario) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();


		$('#tablaAsociarOficinasUsuarios').DataTable({
			scrollX: true,

			"destroy": true,
			"ajax": {
				"url": '../bd/consultasTablas.php',
				"type": "POST",
				"data": function(d) {
					d.idUsuario = idUsuario;
					d.exe = "consultarOficinasUsuarios";

				}
			},

			initComplete: function(settings, json) {
				Swal.close();

			},
			"bPaginate": true,
			"bFilter": true,
			"bProcessing": true,
			"pageLength": 10,
			"columns": [{
					title: 'Oficina',
					mData: 'oficina'
				},
				{
					title: 'Zona',
					mData: 'zonas',
					orderable: false
				},
				{
					title: ' ',
					mData: 'selecc_oficina'
				},
				{
					title: 'IdOficina',
					mData: 'id_oficina',
					visible: false
				}

			],
			order: [
				[0, 'asc']
			],


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


	}




	function cargarTablaUsuarios() {
		$('#tablaUsuarios').DataTable({
			scrollX: true,
			"destroy": true,
			"ajax": {
				"url": '../bd/consultasTablas.php',
				"type": "POST",
				"data": function(d) {
					d.exe = "consultarUsuarios";
					d.tipo_consulta = "usuarios";

				}
			},
			"initComplete": function(settings, json) {
				Swal.close();
			},
			"bPaginate": true,
			"bFilter": true,
			"bProcessing": true,
			"pageLength": 10,
			"columns": [{
					title: 'Nombre',
					mData: 'nombre_usuario'
				},
				{
					title: 'Usuario',
					mData: 'login',
					orderable: false
				},
				{
					title: 'Correo',
					mData: 'email',
					orderable: false
				},
				{
					title: 'Tipo',
					mData: 'tipo',
					orderable: false
				},
				{
					title: 'Sector',
					mData: 'sector'
				},
				{
					title: 'Oficinas',
					mData: 'oficinas'
				},
				{
					title: 'Coordinar',
					mData: 'Coordinar'
				},
				{
					title: 'Fecha Creacion',
					mData: 'fecha_creacion'
				},
				{
					title: 'Fecha Inactivacion',
					mData: 'fecha_inactivacion'
				},
				{
					title: 'Fecha Ultimo Acceso',
					mData: 'fecha_ultimo_acceso'
				},
				{
					title: 'Estado',
					mData: 'estado'
				},
				{
					title: '  ',
					mData: 'selecc_usuario'
				},
				{
					title: 'IdUsuario',
					mData: 'id_usuario',
					visible: false
				}
			],
			order: [
				[0, 'asc']
			],

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



	}

	$("#divTablaUsuarios").on('click', 'a', function() {
		var opcion = $(this).attr('name');
		var action = $(this).attr('id');
		if (action == "btnAsociarOficinasUsuarios") {

			$("#idUsuarioAsociarOficinas").val(opcion);
			cargarOficinasUsuarios(opcion);
			$("#modalAsociarOficinas").addClass('is-visible');

		} else if (action == "btnCoordinarUsuarios") {

			$("#idUsuarioAsociarUsuarios").val(opcion);
			cargarCoordinarUsuarios(opcion);
			$("#modalAsociarUsuarios").addClass('is-visible');

		}
	});


	function cargarCoordinarUsuarios(idUsuario) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();

		$('#tablaAsociarUsuarios').DataTable({
			scrollX: true,

			"destroy": true,
			"ajax": {
				"url": '../bd/consultasTablas.php',
				"type": "POST",
				"data": function(d) {
					d.idUsuario = idUsuario;
					d.exe = "consultarUsuariosAsociar";
				}
			},

			initComplete: function(settings, json) {
				Swal.close();

			},
			"bPaginate": true,
			"bFilter": true,
			"bProcessing": true,
			"pageLength": 10,
			"columns": [{
					title: 'Usuario',
					mData: 'usuario'
				},
				{
					title: ' ',
					mData: 'selecc_usuarios'
				},
				{
					title: 'IdUsuario',
					mData: 'id_usuario',
					visible: false
				}

			],
			order: [
				[0, 'asc']
			],


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
	}


	$('#btnBorrarUsuarios').click(function(e) {
		e.preventDefault();
		var usuariosBorrar = [];
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();

		$('#tablaUsuarios').DataTable().rows().every(function() {
			var data = this.node();
			var data3 = this.data();
			var data2 = {};
			data2.IdUsuario = data3["id_usuario"];
			if ($(data).find('input').prop('checked')) {
				data2.check = "s";
			} else {
				data2.check = "n";
			}

			usuariosBorrar.push(data2);
		});


		//alert(JSON.stringify(asociarAnalistasPagadurias));
		var formBorrarUsuarios = "exe=borrarUsuario&usuariosBorrar=" + JSON.stringify(usuariosBorrar);

		$.ajax({
			type: 'POST',
			url: 'usuarios_funciones.php',
			data: formBorrarUsuarios,
			success: function(data) {
				Swal.close();
				Swal.fire({
					title: 'Resultado de Operacion',
					text: data
				});
				Swal.showLoading();
				return false;
			}
		});
	});




	$('#btnAsociarOficinasUsuariosZonas').click(function(e) {
		e.preventDefault();
		var asociarUsuariosOficinas = [];
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();

		$('#tablaAsociarOficinasUsuarios').DataTable().rows().every(function() {
			var data = this.node();
			var data3 = this.data();
			var data2 = {};
			data2.idOficina = data3["id_oficina"];
			data2.idZona = $(data).find('#zona_oficina option:selected').val();
			if ($(data).find('input').prop('checked')) {
				data2.check = "s";
			} else {
				data2.check = "n";
			}
			asociarUsuariosOficinas.push(data2);
		});


		//alert(JSON.stringify(asociarAnalistasPagadurias));
		var formAsociarUsuariosOficinas = "exe=asociarUsuarios&idUsuario=" + $('#idUsuarioAsociarOficinas').val() + "&asociarUsuariosOficinas=" + JSON.stringify(asociarUsuariosOficinas);
		$.ajax({
			type: 'POST',
			url: 'usuarios_funciones.php',
			data: formAsociarUsuariosOficinas,
			success: function(data) {
				if (data == 1) {
					Swal.close();
					alert("proceso ejecutado satisfactoriamente");
				}
				return false;
			}
		});
	});



	$('#btnAsociarUsuarios').click(function(e) {
		e.preventDefault();
		var asociarUsuarios = [];
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...',
			allowOutsideClick: false,
			allowEscapeKey: false
		});
		Swal.showLoading();

		$('#tablaAsociarUsuarios').DataTable().rows().every(function() {
			var data = this.node();
			var data3 = this.data();
			var data2 = {};
			data2.id_usuario = data3["id_usuario"];
			if ($(data).find('input').prop('checked')) {
				data2.check = "s";
			} else {
				data2.check = "n";
			}
			asociarUsuarios.push(data2);
		});


		//alert(JSON.stringify(asociarAnalistasPagadurias));
		var formAsociarUsuario = "exe=asociarUsuariosCoord&idUsuario=" + $('#idUsuarioAsociarUsuarios').val() + "&asociarUsuarios=" + JSON.stringify(asociarUsuarios);
		$.ajax({
			type: 'POST',
			url: 'usuarios_funciones.php',
			data: formAsociarUsuario,
			success: function(data) {
				if (data == 1) {
					Swal.close();
					alert("proceso ejecutado satisfactoriamente");
				}
				return false;
			}

		});


	});
</script>
<?php include("bottom.php"); ?>