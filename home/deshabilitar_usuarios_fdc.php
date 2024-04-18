<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM"))
{
	exit;
}

$link = conectar();

?>

<?php include("top.php"); ?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
	<script language="JavaScript" src="../date.js"></script>
   
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td class="titulo"><center><b>Deshabilitar Usuarios</b><br><br></center></td>
		</tr>

	</table>

    
    <form id="formato_deshabilitar_usuarios_fdc">
		<table style="font-weight: bold;">
			<tr>
				<td>Und. De Negocio</td>
				<td>
					<select onchange="cargarTabla(); return false;" id="UnidadNegocioFDC" name="UnidadNegocioFDC">
						<option value=0 selected>SELECCIONE UNA OPCION</option>
					<?php
						$consultarEmpresasFDC="SELECT * FROM empresas_fdc a left join empresa_usuario_fdc b on a.id_empresa_fdc=b.id_empresa where b.id_usuario ='".$_SESSION["S_IDUSUARIO"]."'";
						
						$queryEmpresasFDC=sqlsrv_query($link,$consultarEmpresasFDC);
						while ($resEmpresasFDC=sqlsrv_fetch_array($queryEmpresasFDC)){
							?>
							<option value="<?php echo $resEmpresasFDC["id_empresa_fdc"];?>"><?php echo $resEmpresasFDC["nombre"];?></option>
							<?php
						}					
					?>
					<option value="ANTIFRAUDE">ANTIFRAUDE</option>
					</select>
				</td>
				<td style="padding-left: 20px;">Jornada Laboral</td>
				<td>

					<select onchange="habilitarJornadaLaboral();" id="jornadaLaboralFDC" name="jornadaLaboralFDC">
						<?php
						$consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
						$resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
						if ($resJornadaLaboral["descripcion"]=="s"){?>
							<option selected value='s'>SI</option> 
							<option value='n'>NO</option>
						<?php
						}else{
							?>
							<option value='s'>SI</option>
							<option selected value='n'>NO</option>
						<?php
						}	?>						
					</select>
				</td>
			</tr>
		</table>
    	<input type="hidden" name="action" value="">
		<div id="divTablaUsuariosDeshabilitar" class="tab3">
			<table id="tablaUsuariosDeshabilitar" class="tab3">
			</table>
		</div>
        <br>
        <!--<input type="submit" value="Actualizar" id="btnDeshabilitarUsuarios">-->
    </form>

	<div class="modal" id="modalCreditosAnalista" data-animation="slideInOutLeft">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                Documento solicitado
                                <button type="button" class="close-modal" data-close>
                                    x
                                </button>
                            </header>
                            <section class="modal-content">
                                <iframe id="iframe_creditos_analista" width="1200" height="500"></iframe>
                            </section>
                            <footer class="modal-footer">
                                Derechos reservados Kredit 2021
                            </footer>
                        </div>
                    </div>
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	
	<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script src="../plugins/modal/modal.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>

<script type="text/javascript"> 
		$(document).ready( function () {
			cargarTabla();
		});

		function cargarTabla(){
			
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...',
				allowOutsideClick:false
			});

			Swal.showLoading();
			var SendInfo = {
				operacion: "Consultar Usuarios FDC",
				id_empresa:$("#UnidadNegocioFDC option:selected").val()

			};
			$.ajax({
				type: 'POST',
				url: '../servicios/FDC/consultarUsuariosFDC.php',
				data: JSON.stringify(SendInfo),
				contentType: "application/json; charset=utf-8",
				traditional: true,
				cache: false,
				success: function(data) {
					//console.log(data)
					
					$('#tablaUsuariosDeshabilitar').DataTable({
						scrollX: true,
						dom: 'Bfrtip',
						buttons: [ {	
							extend: 'excelHtml5',
							title: 'FDC',
							footer:false,
						},
						{text: '<button>Actualizar Analistas</button>',
						action: function ( e, dt, node, config ) {
							//cargarTabla()
							deshabilitarAnalistas();
						} },{text: '<button>Asignar Pendientes</button>',
						action: function ( e, dt, node, config ) {
							asignarCreditosPendientes();
						} }],
						"destroy":true,
						"data":data.datos.aaData,
						initComplete: function(settings, json) {
							Swal.close();	
						},
						"bPaginate":true,
						"bFilter" : true,   
						"bProcessing": true,
						"pageLength": 40,
						"columns": [
						{ title: 'Nombre', mData: 'nombre', orderable: false},
						{ title: 'Estado', mData: 'estado', orderable: false},
						{ title: 'Cant. Estudios Realizados	', mData: 'estudios_realizados'},
						{ title: 'Cant. Estudios Asignados	', mData: 'estudios_asignado'},
						{ title: 'Total.', mData: 'estudios_total'},
						{ title: 'Cant. Minimo', mData: 'cantidad_minimo'},
						{ title: 'Unidad Negocio', mData: 'unidad_negocio'},
						{ title: 'No. Disp Terminar Estudios Asignados', mData: 'no_disp_terminar'},
						{ title: 'No. Disp, Reasignar', mData: 'no_disp_reasignar'},
						{ title: 'Estado', mData: 'selecc_estado'}],
						"language": {"sProcessing":     "Procesando...","sLengthMenu":     "Mostrar _MENU_ registros","sZeroRecords":    "No se encontraron resultados","sEmptyTable":     "Ningún dato disponible en esta tabla","sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros","sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros","sInfoFiltered":   "(filtrado de un total de _MAX_ registros)","sInfoPostFix":    "","sSearch":         "Buscar:","sUrl":            "","sInfoThousands":  ",","sLoadingRecords": "Cargando...","oPaginate": {"sFirst":    "Primero","sLast":     "Último","sNext":     "Siguiente","sPrevious": "Anterior"},"oAria": {"sSortAscending":  ": Activar para ordenar la columna de manera ascendente","sSortDescending": ": Activar para ordenar la columna de manera descendente"}}
					});

					return false;
				}
			});
		}



		function habilitarJornadaLaboral()
		{
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...',
				allowOutsideClick:false
			});

			Swal.showLoading();
			var SendInfo = {
					operacion: "Habilitar Jornada Laboral",
					jornada_laboral: $("#jornadaLaboralFDC option:selected").val(),
				};
				$.ajax({
					type: 'POST',
					url: '../servicios/FDC/habilitarJornadaLaboral.php',
					data: JSON.stringify(SendInfo),
					contentType: "application/json; charset=utf-8",
					traditional: true,
					success: function(data) {
						Swal.close();
						
						Swal.fire({
							text: data.mensaje,
							confirmButtonText: 'Ok',
						}).then((result) => {
						/* Read more about isConfirmed, isDenied below */
						if (result.isConfirmed) {
							cargarTabla();
						}
						});
						return false;
					}
				});
		}


		function deshabilitarAnalistas()
		{
			if ($("#UnidadNegocioFDC option:selected").val()==0)
			{
				Swal.fire({
					text: "Debe seleccionar una empresa para ejecutar esta accion",
					confirmButtonText: 'Ok',
					}).then((result) => {
					/* Read more about isConfirmed, isDenied below */
					if (result.isConfirmed) {
						cargarTabla();
					}
				});
			
			}else{
				Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...',
				allowOutsideClick:false
				});
				
				Swal.showLoading();

				var usuariosDeshabilitar=[];
				$('#tablaUsuariosDeshabilitar').DataTable().rows().every(function(){
					var data=this.node();
					var data3=this.data();
					var data2={};		

					data2.id_analista=$(data).find("#cantidadMinimaUsuario").attr("name");
					data2.cantidad_creditos=$(data).find("#cantidadMinimaUsuario").val();
					data2.estado=$(data).find("#estado_actual_usuario option:selected").val();
					data2.id_empresa=$(data).find("#unidad_negocio_usuario_fdc option:selected").val();
					usuariosDeshabilitar.push(data2);
				});
				var SendInfo = {
						id_empresa: $("#UnidadNegocioFDC option:selected").val(),
						operacion: "Deshabilitar Analistas",
						analistas:JSON.stringify(usuariosDeshabilitar),
						jornada_laboral:$("#jornadaLaboralFDC option:selected").val()
					};
					
					$.ajax({
						type: 'POST',
						url: '../servicios/FDC/deshabilitarAnalistas.php',
						data: JSON.stringify(SendInfo),
						contentType: "application/json; charset=utf-8",
						traditional: true,
						success: function(data) {
							Swal.close();
							
							Swal.fire({
								text: data.mensaje,
								confirmButtonText: 'Ok',
							}).then((result) => {
							/* Read more about isConfirmed, isDenied below */
							if (result.isConfirmed) {
								cargarTabla();
							}
							});
							return false;
						}
					});
			}
		}


		function asignarCreditosPendientes()
		{
			if ($("#UnidadNegocioFDC option:selected").val()==0)
			{
				Swal.fire({
					text: "Debe seleccionar una empresa para ejecutar esta accion",
					confirmButtonText: 'Ok',
				}).then((result) => {
					/* Read more about isConfirmed, isDenied below */
					if (result.isConfirmed) {
						cargarTabla();
					}
				});
			}else{
				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...',
					allowOutsideClick:false
				});
				Swal.showLoading();
				var usuariosDeshabilitar=[];
				$('#tablaUsuariosDeshabilitar').DataTable().rows().every(function(){
					var data=this.node();
					var data3=this.data();
					var data2={};		

					data2.id_analista=$(data).find("#cantidadMinimaUsuario").attr("name");
					data2.cantidad_creditos=$(data).find("#cantidadMinimaUsuario").val();
					data2.estado=$(data).find("#estado_actual_usuario option:selected").val();
					data2.id_empresa=$(data).find("#unidad_negocio_usuario_fdc option:selected").val();
					usuariosDeshabilitar.push(data2);
				});
				var SendInfo = {
						id_empresa: $("#UnidadNegocioFDC option:selected").val(),
						operacion: "Asignar Creditos Pendientes",
						analistas:JSON.stringify(usuariosDeshabilitar),
						jornada_laboral:$("#jornadaLaboralFDC option:selected").val()
					};

				$.ajax({
					type: 'POST',
					url: '../servicios/FDC/asignarCreditosPendientes.php',
					data: JSON.stringify(SendInfo),
					contentType: "application/json; charset=utf-8",
					traditional: true,
					success: function(data) {
						Swal.close();
						
						Swal.fire({
							text: data.mensaje,
							confirmButtonText: 'Ok',
						}).then((result) => {
						/* Read more about isConfirmed, isDenied below */
						if (result.isConfirmed) {
							cargarTabla();
						}
						});
						return false;
					}
				});
			}
			
		}

		$("#divTablaUsuariosDeshabilitar").on('click','a',function(){
			var opcion=$(this).attr('name');
			var action=$(this).attr('id');

			if(action=="btnNoDispReasignar"){
				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...',
					allowOutsideClick:false
				});
				Swal.showLoading();
				var SendInfo = {
					id_usuario: opcion,
					operacion: "Usuario No Disponible Reasignar",
					id_empresa: $("#UnidadNegocioFDC option:selected").val(),
					jornada_laboral:$("#jornadaLaboralFDC option:selected").val()
				};
				$.ajax({
					type: 'POST',
					url: '../servicios/FDC/usuarioNoDisponibleReasignar.php',
					data: JSON.stringify(SendInfo),
					contentType: "application/json; charset=utf-8",
					traditional: true,
					success: function(data) {
						Swal.close();
						
						Swal.fire({
							text: data.mensaje,
							confirmButtonText: 'Ok',
						}).then((result) => {
						/* Read more about isConfirmed, isDenied below */
						if (result.isConfirmed) {
							cargarTabla();
						}
						});
						return false;
					}
				});
			}else if(action=="btnNoDispTerminar")
			{
				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...',
					allowOutsideClick:false
				});
				Swal.showLoading();
				var SendInfo = {
					id_usuario: opcion,
					operacion: "Usuario No Disponible Terminar"
				};
				$.ajax({
					type: 'POST',
					url: '../servicios/FDC/usuarioNoDisponibleTerminar.php',
					data: JSON.stringify(SendInfo),
					contentType: "application/json; charset=utf-8",
					traditional: true,
					success: function(data) {
						Swal.close();
						
						Swal.fire({
							text: data.mensaje,
							confirmButtonText: 'Ok',
						}).then((result) => {
						/* Read more about isConfirmed, isDenied below */
						if (result.isConfirmed) {
							cargarTabla();
						}
						});
						return false;
					}
				});
			}else if (action=="btnModalCreditosAnalista")
			{
				$.ajax({
					type: 'POST',
					url: '../bd/consultasTablas.php',
					data: "exe=consultarCreditosAsignadoUsuario&idUsuario="+opcion,
					success: function(data) {

						Swal.fire({
							heightAuto: false,
							width:'800px',
							height:'800px',
							title: 'Creditos Asignados a Analista',
							html:data,
						});

						return false;
					}
				});
			}else 	if (action=="btnModalCreditosTerminadosAnalista")
			{
				$.ajax({
					type: 'POST',
					url: '../bd/consultasTablas.php',
					data: "exe=consultarCreditosTerminadosUsuario&idUsuario="+opcion,
					success: function(data) {

						Swal.fire({
							heightAuto: false,
							width:'800px',
							height:'800px',
							title: 'Creditos Terminados a Analista',
							html:data,
						});

						return false;
					}
				});

			}else 	if (action=="btnModalCreditosTotalAnalista")
			{
				$.ajax({
					type: 'POST',
					url: '../bd/consultasTablas.php',
					data: "exe=consultarCreditosTotalUsuario&idUsuario="+opcion,
					success: function(data) {

						Swal.fire({
							heightAuto: false,
							width:'800px',
							height:'800px',
							title: 'Creditos Total a Analista',
							html:data,
						});

						return false;
					}
				});
			}
		});

		$('#btnDeshabilitarUsuarios').click(function(e){	
			if ($("#UnidadNegocioFDC option:selected").val()==0)
			{
				Swal.fire({
					text: "Debe seleccionar una empresa para ejecutar esta accion",
					confirmButtonText: 'Ok',
					}).then((result) => {
					/* Read more about isConfirmed, isDenied below */
					if (result.isConfirmed) {
						cargarTabla();
					}
				});
			
			}else{
				Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...',
				allowOutsideClick:false
				});
				
				Swal.showLoading();
				e.preventDefault();

				var usuariosDeshabilitar=[];
				$('#tablaUsuariosDeshabilitar').DataTable().rows().every(function(){
					var data=this.node();
					var data3=this.data();
					var data2={};		

					data2.id_analista=$(data).find("#cantidadMinimaUsuario").attr("name");
					data2.cantidad_creditos=$(data).find("#cantidadMinimaUsuario").val();
					data2.estado=$(data).find("#estado_actual_usuario option:selected").val();
					data2.id_empresa=$(data).find("#unidad_negocio_usuario_fdc option:selected").val();
					usuariosDeshabilitar.push(data2);
				});
				var SendInfo = {
						id_empresa: $("#UnidadNegocioFDC option:selected").val(),
						operacion: "Deshabilitar Analistas",
						analistas:JSON.stringify(usuariosDeshabilitar),
						jornada_laboral:$("#jornadaLaboralFDC option:selected").val()
					};
					
					$.ajax({
						type: 'POST',
						url: '../servicios/FDC/deshabilitarAnalistas.php',
						data: JSON.stringify(SendInfo),
						contentType: "application/json; charset=utf-8",
						traditional: true,
						success: function(data) {
							Swal.close();
							
							Swal.fire({
								text: data.mensaje,
								confirmButtonText: 'Ok',
							}).then((result) => {
							/* Read more about isConfirmed, isDenied below */
							if (result.isConfirmed) {
								cargarTabla();
							}
							});
							return false;
						}
					});
			}
			
		});
</script>


<?php include("bottom.php"); ?>
