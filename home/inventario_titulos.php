<?php
	include ('../functions.php');
	include ('./top.php');

	$link = conectar_utf();
	if (!$_SESSION["S_LOGIN"] && ($_SESSION["S_TIPO"] != "ADMINISTRADOR" || $_SESSION["S_TIPO"] != "OPERACIONES" || $_SESSION["S_TIPO"] !== "CARTERA" || $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
	{
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
							<li class="nav-item" style="font-weight: bold;">INVENTARIO DE TITULOS</li>
							<input id="textoSimulaciones" type="hidden"/>
						</ul>						
					</div>

					<div class="card-body " >
						<div class="row">
							<div class="col-md-6" style="margin-bottom:20px;">
								<label class="form-label">Libranza,Nombre,Identificacion</label>
								<input type="text" class="form-control" id="filtrosBusqueda"/>
							</div>	
							<br><br>		

							<div class="col-md-12">
								<div class="card-table table-responsive">
									<table class="table" id="tablaGestionInventarioTitulos">
										<thead>
											<tr>
                                                <th>Id Simulacion</th>
												<th>Nombre</th>
												<th>Identificacion</th>
												<th>Libranza</th>
												<th>Pagaduria</th>
												<th>Subestado</th>
												<th>Legajo</th>
												<th>Estado</th>
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

	<div class="modal modal-blur fade modal-tabler" id="modalAddNovedadTitulo" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="titulo-ModalNovedadTitulo">NOVEDAD</h5>
					<input type="hidden" id="idSimulacionNovedadTitulo">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
                        <div class="col-lg-2">
								<label class="form-label">Id</label>
								<input disabled type="text" class="form-control" id="idSimulacionNovedadTituloModal"/>
						</div>

						<div class="col-lg-6">
								<label class="form-label">Cliente</label>
								<input disabledtype="text" class="form-control" id="clienteNovedadTituloModal"/>
						</div>

						<div class="col-lg-4">
								<label class="form-label">Libranza</label>
								<input disabled type="text" class="form-control" id="libranzaNovedadTituloModal"/>
						</div>

					</div>


                    <div class="row">
						<div class="col-lg-6">
								<label class="form-label">Tipificacion</label>
								<select id="tipificacionTituloModal" class="form-select">
							
								</select>
						</div>
						<div class="col-lg-6">
								<label class="form-label">Legajo</label>
								<input type="text" class="form-control" id="legajoNovedadTituloModal"/>
						</div>

						<div class="col-lg-12">
                            <label class="form-label">Observacion</label>
								<textarea id="observacionNovedadTituloModal" class="form-select"></textarea>
						</div>

						
					</div>


						
				</div>					

				<div class="modal-footer">
					<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
						CANCELAR
					</a>
					<a name="add" id="btnSaveModal" onclick="addNovedadTitulo(); return false;" class="btn btn-primary ms-auto">
						<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
						GUARDAR
					</a>
				</div>
			</div>
		</div>
	</div>



	<div class="modal modal-blur fade modal-tabler" id="modalObservacionesTitulo" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">OBSERVACIONES</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<div class="row">
							<div class="col-md-12">
								<div class="card-table table-responsive">
									
											
											<table class="table" id="tablaObservacionesTitulo">
												<thead>
													<tr>
														<th>Estado</th>
														<th>Observacion</th>
                                                        <th>Usuario</th>
														<th>Fecha</th>
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

		$(document).ready( function () {
			loadTabla(0);	
		});

		function openModalNovedadTitulo(id_titulo){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			

		

	
				$("#btnSaveModal").attr("name", "EDITAR");
				if (id_titulo!="masivo")
				{
					$.ajax({
						url: '../servicios/inventario_titulos/consultar_titulos.php',
						type: 'POST',
						data: { titulo:id_titulo,filtro:2},
						dataType : 'json',
						success: function(json) {

							if(json.code == 200){
								Swal.close();
								json.data.forEach(function(inventario, index) {
									$("#idSimulacionNovedadTituloModal").val(inventario.id_simulacion);
									$("#clienteNovedadTituloModal").val(inventario.nombre);
									$("#libranzaNovedadTituloModal").val(inventario.libranza);
									$("#observacionNovedadTituloModal").val("");
									$("#legajoNovedadTituloModal").val(inventario.legajo);
									
									
									//alert(inventario.id_estado_inventario_credito);
									llenarSelectsTipificaciones(inventario.id_estado_inventario_credito);
									
								});
							}else{
								Swal.fire('Error al Consultar Información de Credito', '', 'error');
							}
						}
					});
					
				}else{
					Swal.close();
					$("#idSimulacionNovedadTituloModal").val("MASIVO");
									$("#clienteNovedadTituloModal").val("MASIVO");
									$("#libranzaNovedadTituloModal").val("MASIVO");
									$("#observacionNovedadTituloModal").val("");
									$("#legajoNovedadTituloModal").val();
					llenarSelectsTipificaciones("");
				}	
				
			
						
		}



		function openModalObservacionesTitulos(id_simulacion){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			

			Swal.close();

			
				llenarTablaObservacionesTitulo(id_simulacion);

						
		}




        function llenarSelectsTipificaciones(estado_inventario_credito){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			$.ajax({
				url: '../servicios/inventario_titulos/consultar_tipificaciones.php',
				type: 'POST',
			
				dataType : 'json',
				success: function(json) {
					
					if(json.code == 200 || json.code == 300){
						if(json.code == 200){
							rowsHtml += '<option value="" selected>Selecione</option>';
							json.data.forEach(function(inventario, index) {
								if(inventario.id == estado_inventario_credito) {
									rowsHtml += '<option selected value="'+inventario.id+'">'+inventario.descripcion+'</option>';
								}else{
									rowsHtml += '<option value="'+inventario.id+'">'+inventario.descripcion+'</option>';
								}
							});
						}											
					}
					$("#tipificacionTituloModal").html(rowsHtml);

					Swal.close();

					return false;
				}
			});
		}

		function llenarTablaObservacionesTitulo(id_simulacion){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();

			var rowsHtml = '';

			if ($.fn.DataTable.isDataTable('#tablaObservacionesTitulo')) {
				$('#tablaObservacionesTitulo').DataTable().destroy();
			}

			$("#tablaObservacionesTitulo tbody").html(rowsHtml);

			$.ajax({
				url: '../servicios/inventario_titulos/consultar_observaciones_titulos.php',
				type: 'POST',
				dataType : 'json',
				data:"id_simulacion="+id_simulacion,
				success: function(json) {
                    //alert(json);
					
					if(json.code == 200 || json.code == 300){

						if(json.code == 200){
							json.data.forEach(function(convenio, index) {
				

								rowsHtml += '<tr>';
									rowsHtml += '<td>'+convenio.estado+'</td>';
									rowsHtml += '<td>'+convenio.observacion+'</td>';
									rowsHtml += '<td>'+convenio.usuario+'</td>';
									rowsHtml += '<td>'+convenio.fecha+'</td>';
								rowsHtml += '</tr>';
							});
						}											
					}

					$("#tablaObservacionesTitulo tbody").html(rowsHtml);
						
					$('#tablaObservacionesTitulo').DataTable({
					
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



		function loadTabla(titulos){

			if ($.fn.DataTable.isDataTable('#tablaGestionInventarioTitulos')) {
				$('#tablaGestionInventarioTitulos').DataTable().destroy();
			}

            if (titulos==0)
            {
                var rowsHtml = '';
                
            }else{
                var rowsHtml = titulos;
            }

            $("#tablaGestionInventarioTitulos tbody").html(rowsHtml);
			        
          
					$('#tablaGestionInventarioTitulos').DataTable({
						dom: 'Bfrtip',
						buttons: [{	
							extend: 'excelHtml5',
							title: 'FDC',
							footer:false,
						},{
							text: '<button>Actualizar</button>',
							action: function ( e, dt, node, config ) {
								loadTabla(0);
							}
						},{text: 'Cargar Titulos',
							action: function ( e, dt, node, config ) {
							openModalCargarTitulos();

                            }
                        },{text: 'Novedad Masiva',
							action: function ( e, dt, node, config ) {
								
							openModalNovedadTitulo("masivo");
							

                            },attr: {
                                titie: 'add a new contact',
                                'data-bs-toggle': 'modal',
                                'data-bs-target': '#modalAddNovedadTitulo'
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

				
		}

		


		function addNovedadTitulo(){
			//alert($("#idSimulacionNovedadTituloModal").val());
			if ($("#idSimulacionNovedadTituloModal").val()=="MASIVO")
			{
				if (!$('#tablaGestionInventarioTitulos').DataTable().data().count())
				{
					alert("No hay datos cargados");
				}else{
					var error = false;
					if($("#tipificacionTituloModal").val() == ''){
						error = true;
						Swal.fire('Debe ingresar Tipificacion', '', 'error')
					}
					if(error){ return false; }
					var rowsHtml="";
					var simulaciones="";
					Swal.fire({
						title: 'Por favor aguarde unos segundos',
						text: 'Procesando...'
					});

					Swal.showLoading();
					$('#tablaGestionInventarioTitulos').DataTable().rows().every(function()
					{
						var data2=this.data();
						//alert(data[0])

						var data = {
							
							tipificacion : $("#tipificacionTituloModal").val(),
							observacion : $("#observacionNovedadTituloModal").val(),
							legajo : $("#legajoNovedadTituloModal").val(),
							
							id_simulacion : data2[0]
						}

						$.ajax({
							url: '../servicios/inventario_titulos/agregar_novedad_inventario.php',
							type: 'POST',
							data: data,
							dataType : 'json',
							async:false,
							success: function(json) {
								
								if(json.code == 200){
									//$(".btn-close").trigger("click");
									
									
									
									
									simulaciones += data2[0]+",";
									
										
						
					
							
								}else {
									Swal.fire(json.mensaje, '', 'error')
								}
							}
						});
					});
					$("#textoSimulaciones").val(simulaciones.substring(0, simulaciones.length - 1));
					const myArray=$("#textoSimulaciones").val().split(",");
					$.each(myArray, function (index, value) { 
						$.ajax({
							url: '../servicios/inventario_titulos/consultar_titulos.php',
							type: 'POST',
							data: 'titulo='+value+"&filtro=2",
							dataType : 'json',
							async:false,
							success: function(json) {
								if(json.code == 200 || json.code == 300){
									if(json.code == 200){
										json.data.forEach(function(inventario, index) {
											rowsHtml += '<tr>';
											rowsHtml += '<td>'+inventario.id_simulacion+'</td>';
											rowsHtml += '<td>'+inventario.nombre+'</td>';
											rowsHtml += '<td>'+inventario.identificacion+'</td>';
											rowsHtml += '<td>'+inventario.libranza+'</td>';
											rowsHtml += '<td>'+inventario.pagaduria+'</td>';
											rowsHtml += '<td>'+inventario.subestado+'</td>';
											rowsHtml += '<td>'+inventario.legajo+'</td>';
											rowsHtml += '<td>'+inventario.estado_inventario_credito+'</td>';
											rowsHtml += '<td>'+inventario.opciones+'</td>';
											rowsHtml += '</tr>';
											simulaciones += inventario.id_simulacion+",";
										});
									}											
								}
								return false;
							}
						});
					}); 
					loadTabla(rowsHtml);
					Swal.close();
				}
			}
			else
			{
				var error = false;

				if($("#tipificacionTituloModal").val() == ''){
					error = true;
					Swal.fire('Debe ingresar Tipificacion', '', 'error')
				}
				
				if(error){ return false; }
				
				var data = {
					
					tipificacion : $("#tipificacionTituloModal").val(),
					observacion : $("#observacionNovedadTituloModal").val(),
					legajo : $("#legajoNovedadTituloModal").val(),
					
					id_simulacion : $("#idSimulacionNovedadTituloModal").val()
				}

				$.ajax({
					url: '../servicios/inventario_titulos/agregar_novedad_inventario.php',
					type: 'POST',
					data: data,
					dataType : 'json',
					success: function(json) {
						
						if(json.code == 200){
							$(".btn-close").trigger("click");
							Swal.fire('Guardado Exitosamente', '', 'success');
							var rowsHtml="";
							var simulaciones="";
							
							const myArray=$("#textoSimulaciones").val().split(",");
							$.each(myArray, function (index, value) { 
								$.ajax({
									url: '../servicios/inventario_titulos/consultar_titulos.php',
									type: 'POST',
									data: 'titulo='+value+"&filtro=2",
									dataType : 'json',
									async:false,
									success: function(json) {
										if(json.code == 200 || json.code == 300){
											if(json.code == 200){
												json.data.forEach(function(inventario, index) {
													rowsHtml += '<tr>';
													rowsHtml += '<td>'+inventario.id_simulacion+'</td>';
													rowsHtml += '<td>'+inventario.nombre+'</td>';
													rowsHtml += '<td>'+inventario.identificacion+'</td>';
													rowsHtml += '<td>'+inventario.libranza+'</td>';
													rowsHtml += '<td>'+inventario.pagaduria+'</td>';
													rowsHtml += '<td>'+inventario.subestado+'</td>';
													rowsHtml += '<td>'+inventario.legajo+'</td>';
													rowsHtml += '<td>'+inventario.estado_inventario_credito+'</td>';
													rowsHtml += '<td>'+inventario.opciones+'</td>';
													rowsHtml += '</tr>';
													simulaciones += inventario.id_simulacion+",";
												});
											}											
										}
										return false;
									}
								});
							}); 
								
				
					$("#textoSimulaciones").val(simulaciones.substring(0, simulaciones.length - 1));
					loadTabla(rowsHtml);
					//Swal.close();
						}else {
							Swal.fire(json.mensaje, '', 'error')
						}
					}
				});
			}
			
		}

		function cambiarEstadoTitulos(id){

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
						data: { id_pagaduria : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Eliminado Exitosamente', '', 'success');
								loadTabla(0);
							}else {
								Swal.fire(json.mensaje, '', 'error')
							}
						}
					});
				} else {
					Swal.fire('No se Pudo Eliminar', '', 'error')
				}
			})
		}


		function eliminarObservacionTitulos(id){

			Swal.fire({
				title: '¿Está seguro de eliminar esta Observacion?',
				showConfirmButton: false,
				showDenyButton: true,
				showCancelButton: true,
				denyButtonText: `Continuar`,
			}).then((result) => {
				if (result.isDenied) {

					$.ajax({
						url: '../servicios/pagadurias/eliminar_convenio.php',
						type: 'POST',
						data: { id_convenio : id },
						dataType : 'json',
						success: function(json) {
							
							if(json.code == 200){
								Swal.fire('Eliminado Exitosamente', '', 'success');
								loadTablaConvenios($("#idConveniosModal").val());
							}else {
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


<script type="text/javascript">
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


    function openModalCargarTitulos()
    {
		
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
							//For IE Browser.
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

    function ProcessExcel(data) {
        
        var workbook = XLSX.read(data, {
            type: 'binary'
        });
 
        var firstSheet = workbook.SheetNames[0];
 
        var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);
		var json_object = JSON.stringify(excelRows);

		//alert(json_object);
        var rowsHtml = '';
		var simulaciones="";
        for (var i = 0; i < excelRows.length; i++) {
      		let posicion = rowsHtml.indexOf(excelRows[i].titulo);
			if (posicion !== -1)
			{
				//console.log("La palabra está en la posición " + posicion);
			}	
			else
			{
				
				$.ajax({
					url: '../servicios/inventario_titulos/consultar_titulos.php',
					type: 'POST',
					data: 'titulo='+excelRows[i].titulo+"&filtro=1",
					dataType : 'json',
					async:false,
					success: function(json) {
						if(json.code == 200 || json.code == 300){
							if(json.code == 200){
								json.data.forEach(function(inventario, index) {
									rowsHtml += '<tr>';
									rowsHtml += '<td>'+inventario.id_simulacion+'</td>';
									rowsHtml += '<td>'+inventario.nombre+'</td>';
									rowsHtml += '<td>'+inventario.identificacion+'</td>';
									rowsHtml += '<td>'+inventario.libranza+'</td>';
									rowsHtml += '<td>'+inventario.pagaduria+'</td>';
									rowsHtml += '<td>'+inventario.subestado+'</td>';
									rowsHtml += '<td>'+inventario.legajo+'</td>';
									rowsHtml += '<td>'+inventario.estado_inventario_credito+'</td>';
									rowsHtml += '<td>'+inventario.opciones+'</td>';
									rowsHtml += '</tr>';
									simulaciones += inventario.id_simulacion+",";
								});
							}											
						}
						return false;
					}
				});	
			}
        }
		$("#textoSimulaciones").val(simulaciones.substring(0, simulaciones.length - 1));
	    loadTabla(rowsHtml);
		Swal.close();
        
    };
	$('#filtrosBusqueda').on('keypress',function(e){
		if (e.which===13 || e.which===9){
			e.preventDefault();
			var rowsHtml="";
			var simulaciones="";
			Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...'
				});

				Swal.showLoading();			
			const myArray=$("#filtrosBusqueda").val().split(",");
			$.each(myArray, function (index, value) { 
				$.ajax({
					url: '../servicios/inventario_titulos/consultar_titulos.php',
					type: 'POST',
					data: 'titulo='+value+"&filtro=1",
					dataType : 'json',
					async:false,
					success: function(json) {
						if(json.code == 200 || json.code == 300){
							if(json.code == 200){
								json.data.forEach(function(inventario, index) {
									rowsHtml += '<tr>';
									rowsHtml += '<td>'+inventario.id_simulacion+'</td>';
									rowsHtml += '<td>'+inventario.nombre+'</td>';
									rowsHtml += '<td>'+inventario.identificacion+'</td>';
									rowsHtml += '<td>'+inventario.libranza+'</td>';
									rowsHtml += '<td>'+inventario.pagaduria+'</td>';
									rowsHtml += '<td>'+inventario.subestado+'</td>';
									rowsHtml += '<td>'+inventario.legajo+'</td>';
									rowsHtml += '<td>'+inventario.estado_inventario_credito+'</td>';
									rowsHtml += '<td>'+inventario.opciones+'</td>';
									rowsHtml += '</tr>';
									simulaciones += inventario.id_simulacion+",";
								});
							}											
						}
						return false;
					}
				});
			}); 
							
			
        
				$("#textoSimulaciones").val(simulaciones.substring(0, simulaciones.length - 1));
	    		loadTabla(rowsHtml);
				Swal.close();
		}
	});	
</script>

	
<?php 
	include("bottom.php");
?>