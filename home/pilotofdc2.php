<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PREESTUDIO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM"))
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
			<td class="titulo"><center><b>Ingresos FDC</b><br><br></center></td>
		</tr>
<?php
			if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
			|| $_SESSION["S_TIPO"] == "OPERACIONES" 
			|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
			&& $_SESSION["S_SOLOLECTURA"] != "1") { ?>
		<tr>
		<td><a href="deshabilitar_usuarios_fdc.php">Deshabilitar Usuarios</a></td>
		</tr>
		<?php
			}
		?>
	</table>
	<?php

	if ($_REQUEST["action"])
	{
		
		$queryDB = "select si.* from simulaciones si 
		INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
		INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
		INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
		INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina where si.estado IN ('ING')";
		
		if ($_SESSION["S_SECTOR"])
		{
			$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		}
		
		if ($_SESSION["S_TIPO"] == "COMERCIAL")
		{
			$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		else
		{
			$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		}
		
		if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
		{
			$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
				$queryDB .= " AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
			
			if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
				$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
			
			if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
				$queryDB .= " AND si.telemercadeo = '1'";
		}
		
		if ($_REQUEST["descripcion_busqueda"])
		{
			$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
			
			$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".(strtoupper($descripcion_busqueda))."%')";
		}
		
		if ($_REQUEST["sectorb"])
		{
			$sectorb = $_REQUEST["sectorb"];
			
			$queryDB .= " AND pa.sector = '".$sectorb."'";
		}
		
		if ($_REQUEST["pagaduriab"])
		{
			$pagaduriab = $_REQUEST["pagaduriab"];
			
			$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
		}
		
		if ($_REQUEST["id_comercialb"])
		{
			$id_comercialb = $_REQUEST["id_comercialb"];
			
			$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
		}
		
		if ($_REQUEST["decisionb"])
		{
			$decisionb = $_REQUEST["decisionb"];
			
			$queryDB .= " AND si.decision = '".$decisionb."'";
		}
		
		if ($_REQUEST["id_oficinab"])
		{
			$id_oficinab = $_REQUEST["id_oficinab"];
			
			$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
		}
		
		if ($_REQUEST["visualizarb"])
		{
			$visualizarb = $_REQUEST["visualizarb"];
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
		else if (!$_REQUEST["buscar"])
		{
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
		}
		
		$queryDB .= " order by si.id_simulacion";
		
		$rs = sqlsrv_query($link,$queryDB);
		//echo "asignaciones.....".$_REQUEST["action"];
		//echo "<br>".$queryDB;
		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
		{
			
			
			if ($_REQUEST["chk".$fila["id_simulacion"]])
			{
				//echo $_REQUEST["action"];
				//echo $_REQUEST["chk".$fila["id_simulacion"]]."<br>";
				if ($_REQUEST["action"] == "desistir")
				{
					if ($fila["decision"] == $label_viable)
						//sqlsrv_query($link,"update simulaciones set estado = 'DST', id_subestado = NULL where id_simulacion = '".$fila["id_simulacion"]."'");
						sqlsrv_query($link,"update simulaciones set estado = 'DST' where id_simulacion = '".$fila["id_simulacion"]."'");
					else
						echo "<script>alert('La simulacion ".$fila["cedula"]." ".$fila["nombre"]." no puede ser Desistida. No cumple con las condiciones para realizar esta accion');</script>";
				}
				if ($_REQUEST["action"] == "anular")
				{
					//echo "ANULAR: ".$fila["id_simulacion"].",";
					sqlsrv_query($link,"update simulaciones set estado = 'ANU', id_subestado = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_simulacion = '".$fila["id_simulacion"]."'");
				}
		
			}
		}
	}

	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" 
	|| $_SESSION["S_TIPO"] == "OPERACIONES" 
	|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") 
	&& $_SESSION["S_SOLOLECTURA"] != "1") { 
	}else{
		?>
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<?php 
				$consultarMinCreditos="SELECT a.cantidad_creditos,a.id_usuario,a.login,a.nombre,a.apellido,CASE WHEN a.disponible='s' THEN 'DISPONIBLE' WHEN a.disponible='n' THEN 'NO DISPONIBLE' WHEN a.disponible='g' THEN 'EN GESTION' ELSE 'NO DISPONIBLE' END AS estado_usuario,a.disponible AS estado
				FROM
				usuarios a
				
				WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."'";
				$queryMinCreditos=sqlsrv_query($link,$consultarMinCreditos);
				$resMinCreditos = sqlsrv_fetch_array($queryMinCreditos);

				$consultarCantidadCreditos="SELECT count(id) as cantidad FROM simulaciones_fdc WHERE id_subestado<>'28' AND estado=4 AND format(fecha_creacion,'Y-m-d')=CURRENT_TIMESTAMP AND id_usuario_creacion='".$_SESSION["S_IDUSUARIO"]."'";
				$queryCantidadCreditos=sqlsrv_query($link,$consultarCantidadCreditos);
				$resCantidadCreditos=sqlsrv_fetch_array($queryCantidadCreditos);
			?>

			<td colspan="19" align="right"><b>Cantidad Creditos Minimo: <?php echo $resMinCreditos["cantidad_creditos"];?>
			<br>Cantidad Creditos Hoy: <?php echo $resCantidadCreditos["cantidad"]; ?></b></td>
		</tr>
	</table>
		<?php
	}
?>
		<form name="formato3" method="GET" action="pilotofdc.php">
			<input type="hidden" name="action" value="">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
			<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $id_comercialb ?>">
			<input type="hidden" name="decisionb" value="<?php echo $decisionb ?>">
			<input type="hidden" name="id_oficinab" value="<?php echo $id_oficinab ?>">
			<input type="hidden" name="visualizarb" value="<?php echo $visualizarb ?>">
			<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">

				<div id="divTablaFDC" style="width: 98%; align:left">
			<table class="tab3" id="tablaFDC">
	
			</table>
</div>
			<br>
			<?php

				if ($_SESSION["S_SOLOLECTURA"] != "1")
				{
				
			?>
		
				<br>
					<table>
					<tr>
						
						<!--<td><p align="center"><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_LOGIN"] == "pemavarez") { ?>&nbsp;&nbsp;<input type="submit" value="Desistir" onClick="document.formato3.action.value='desistir'">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Asignar" id="btnAsignarUsuarios">&nbsp;&nbsp;<?php } ?></p></td>-->
					</tr>
				</table>
				
				
				<?php
					
					}

			?>
			<br>
		</form>
		
		<div class="modal" id="modal1" data-animation="slideInOutLeft">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                Documento solicitado
                                <button type="button" class="close-modal" data-close>
                                    x
                                </button>
                            </header>
                            <section class="modal-content">
							<iframe id="iframe_servicios" width="500" height="300"></iframe>
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
$(document).ready( function () {
	
	Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();
	
		cargarBandejaIngresos();

 
} );

function cargarBandejaIngresos() {
	$.ajax({
		type: 'POST',
		url: '../controles/consulta_ingresos_fdc2.php',
		data: "exe=consultarBandejaFDC",
		
		cache: false,
	
		success: function(data) {
//alert(data);
			var arrayJSON=JSON.parse(data);
			$('#tablaFDC').DataTable( {
				scrollX: true,
				dom: 'Bfrtip',
				buttons: [ {	
					extend: 'excelHtml5',
					title: 'FDC',
					footer:false,
					exportOptions: {
                    columns: [ 0, 1,2,3,4,5,6,7,8,9,15 ]
                }
				},
			{text: '<button>Actualizar</button>',
				action: function ( e, dt, node, config ) {
                location.reload();
            }} ],
				"destroy":true,
				"data":arrayJSON.aaData,
				"initComplete": function(settings, json) {
			Swal.close();	
				},
				"bPaginate":true,
				"bFilter" : true,   
				"bProcessing": true,
				"pageLength": 40,
				"columns": [
				{ title: 'Simulacion', mData: 'id_simulacion', orderable: false},
				{ title: 'Cedula', mData: 'cedula', orderable: false},
				{ title: 'Nombre', mData: 'nombre'},
				{ title: 'Pagaduria', mData: 'pagaduria'},
				{ title: 'Comercial', mData: 'comercial'},
				{ title: 'Tipo Comercial', mData: 'tipo_comercial2'},
				{ title: 'Oficina', mData: 'oficina'},
				{ title: 'F. Radicado', mData: 'fecha_radicado'},
				{ title: 'Tiempo Prospeccion', mData: 'tiempo_prospeccion'},
				{ title: 'Estado', mData: 'estado'},
				
				{ title: '<img src="../images/adjuntar.png" title="Adjuntos">', mData: 'adjuntos'}
		
			
			],
				order: [[7, 'asc']],


				"language": {"sProcessing":     "Procesando...","sLengthMenu":     "Mostrar _MENU_ registros","sZeroRecords":    "No se encontraron resultados","sEmptyTable":     "Ningún dato disponible en esta tabla","sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros","sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros","sInfoFiltered":   "(filtrado de un total de _MAX_ registros)","sInfoPostFix":    "","sSearch":         "Buscar:","sUrl":            "","sInfoThousands":  ",","sLoadingRecords": "Cargando...","oPaginate": {"sFirst":    "Primero","sLast":     "Último","sNext":     "Siguiente","sPrevious": "Anterior"},"oAria": {"sSortAscending":  ": Activar para ordenar la columna de manera ascendente","sSortDescending": ": Activar para ordenar la columna de manera descendente"}}
			});

			return false;
		}
	});
}




	 

</script>

<?php include("bottom.php"); ?>

