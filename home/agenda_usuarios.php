<?php include('../functions.php'); ?>
<?php

$link = conectar();

?>
<?php include("top.php"); ?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Agenda Usuarios</b><br><br></center>
		</td>
	</tr>
</table>

<div id="divTablaAgendaUsuarios" style="width: 98%; align: left">
	<table class="tab3" id="tablaAgendaUsuarios">

	</table>
</div>

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
<script src="../plugins/modal/modal.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();

		cargarTablaAgendaUsuarios();

	});

	function cargarTablaAgendaUsuarios() {

		$('#tablaAgendaUsuarios').DataTable({
			scrollX: true,

			"destroy": true,
			"ajax": {
				"url": '../bd/consultasTablas.php',
				"type": "POST",
				"data": function(d) {
					d.exe = "consultarUsuarios";
					d.tipo_consulta = "agenda";

				}
			},
			"initComplete": function(settings, json) {
				Swal.close();
			},
			"bPaginate": true,
			"bFilter": true,
			"bProcessing": true,
			"pageLength": 40,
			"columns": [{
					title: 'Nombre',
					mData: 'nombre_usuario2'
				},
				{
					title: 'Cargo',
					mData: 'cargo',
					orderable: false
				},
				{
					title: 'Telefono',
					mData: 'telefono',
					orderable: false
				},

				{
					title: 'Correo',
					mData: 'email',
					orderable: false
				},
				{
					title: 'Oficinas',
					mData: 'nombre_oficinas'
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
</script>
<?php include("bottom.php"); ?>