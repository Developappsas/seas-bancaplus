<?php
include ('../functions.php');
include ('./top.php');

$link = conectar_utf();
if (!$_SESSION["S_LOGIN"] && ($_SESSION["S_TIPO"] != "ADMINISTRADOR")) {
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
						<li class="nav-item" style="font-weight: bold;">REGISTRO MASIVO CREDITOS</li>
						<input id="textoSimulaciones" type="hidden"/>
					</ul>						
				</div>
				<div class="card-body " >
					<div class="row">
						<div class="col-md-12">
							<div class="card-table table-responsive">
								<table class="table" id="tablaGestionInventarioTitulos">
									<thead>
										<tr>
											<td>ID SIMULACION</td>
											<td>RESULTADO</td>
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

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script> 
<script src="../plugins/tabler/js/tabler.min.js"></script>
<script src="../plugins/tabler/js/demo.min.js"></script>
<script type="text/javascript">

	var tabla;
	var data2=[];

	$(document).ready( function () {
		loadTabla(0);	
	});

	function loadTabla(titulos){

		if ($.fn.DataTable.isDataTable('#tablaGestionInventarioTitulos')) {
			$('#tablaGestionInventarioTitulos').DataTable().destroy();
		}

		if (titulos==0) {
			var rowsHtml = '';                
		}else{
			var rowsHtml = titulos;
		}

		$("#tablaGestionInventarioTitulos tbody").html(rowsHtml);			        

		tabla = $('#tablaGestionInventarioTitulos').DataTable({
			dom: 'Bfrtip',
			buttons: [{
				text: 'Cargar Creditos',
				action: function ( e, dt, node, config ) {
					openModalCargarCreditos();
				}
			},{
				text: '<button id="bottomEjecutar">Ejecutar Plan Pagos</button>',
				action: function ( e, dt, node, config ) {
					CrearCreditos();
				}
			},{	
				extend: 'excelHtml5',
				title: 'log-plandepagos-masivos',
				footer:false,
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
	}

	$("body").on("click", "#upload", function () {
        //Reference the FileUpload element.
		var fileUpload = $("#fileUpload")[0];

        //Validate whether File is valid Excel file.
		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
		if (regex.test(fileUpload.value.toLowerCase())) {
			if (typeof (FileReader) != "undefined") {
				
				var reader = new FileReader();
                //For Browsers other than IE.
				if (reader.readAsBinaryString) {
					reader.onload = function (e) {
						ProcessExcel(e.target.result);
					};
					reader.readAsBinaryString(fileUpload.files[0]);
				} else {
                    //For IE Browser.
					reader.onload = function (e) {
						var data = "";
						var bytes = new Uint8Array(e.target.result);
						for (var i = 0; i < bytes.byteLength; i++) {
							data += String.fromCharCode(bytes[i]);
						}
						ProcessExcel(data);
					};
					reader.readAsArrayBuffer(fileUpload.files[0]);
				}
			} else {
				alert("This browser does not support HTML5.");
			}
		} else {
			alert("Please upload a valid Excel file.");
		}
	});

	function openModalCargarCreditos() {
		
		var input = document.createElement('input');
		input.type = 'file';
		input.setAttribute("accept","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.openxmlformats-officedocument .spreadsheetml, application/vnd.ms-excel");
		input.setAttribute("name","soportes[]");
		input.click();
		input.onchange = e => {

			var fileUpload = e.target.files[0];	
			var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
			
			if (typeof (FileReader) != "undefined") {
				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...'
				});

				Swal.showLoading();
				var reader = new FileReader();

				if (reader.readAsBinaryString) {
					reader.onload = function (e) {
						ProcessExcel(e.target.result);								
					};
					reader.readAsBinaryString(fileUpload);
				} else {
					reader.onload = function (e) {
						var data = "";
						var bytes = new Uint8Array(e.target.result);
						for (var i = 0; i < bytes.byteLength; i++) {
							data += String.fromCharCode(bytes[i]);
						}
						ProcessExcel(data);
					};
					reader.readAsArrayBuffer(fileUpload);
				}
			} else {
				alert("This browser does not support HTML5.");
			}      
		}       
	}

	function CrearCreditos() {
		$("#bottomEjecutar").prop("disabled", true);

		if (!$('#tablaGestionInventarioTitulos').DataTable().data().count()){
			alert("debe ingresar creditos")
		}
		else {

            var peticion = 0;
            var item = 1;

            Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});
			Swal.showLoading();

			enviarAjax();
            function enviarAjax(){
            	if(peticion < data2.length){
            		Swal.update({
						title: 'Cargando...',
						text: 'Ejecutado ' + item + ' de ' + data2.length
					});
					Swal.showLoading();

					if(data2[peticion]){

						$.ajax({
							url: '../servicios/cartera/generarPlanDePagoConRecaudos.php',
							type: 'POST',
							data: { id_simulacion : data2[peticion], id_usuario: '<?=$_SESSION["S_IDUSUARIO"]?>' },
							dataType : 'json',
							method: 'POST',
							success: function(json) {
								tabla.cell("#fila-" + json.data[0].credito + " td:eq(1)").data(json.data[0].mensaje);
								peticion++;
								item++;
								enviarAjax();							
							},
							error:function(json) {
								tabla.cell("#fila-" + data2[peticion] + " td:eq(1)").data("Error al procesar");
								peticion++;
								item++;
								enviarAjax();
							}
						});
					}else{
						Swal.fire({
							title: 'Error al procesar filas',
							icon: 'error',
							allowOutsideClick: false,
							showCancelButton: false,
							showConfirmButton: true
						});
					}
				}else{
					
					Swal.fire({
						title: 'Proceso Ejecutado',
						icon: 'warning',
						allowOutsideClick: false,
						showCancelButton: false,
						showConfirmButton: true
					});
				}
			}
		}   
	}

	function ProcessExcel(data) {

		var workbook = XLSX.read(data, {
			type: 'binary'
		});

		var firstSheet = workbook.SheetNames[0];
		var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);
		var json_object = JSON.stringify(excelRows);
		var rowsHtml = '';
		var simulaciones="";
		for (var i = 0; i < excelRows.length; i++) {
			let columnas = Object.values(excelRows[i]);
			data2.push(columnas[0]);
			
			rowsHtml += '<tr id="fila-'+columnas[0]+'">';
			rowsHtml += '<td class="id">'+columnas[0]+'</td>';
			rowsHtml += '<td class="resultado"></td>'
			rowsHtml += '</tr>';
		}
		loadTabla(rowsHtml);
		Swal.close();        
	};
</script>
<?php 
include("bottom.php");
?>