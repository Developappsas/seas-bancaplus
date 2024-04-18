<?php
include ('../functions.php');
include ('./top.php');

if(!$_SESSION['S_LOGIN'] && $_SESSION['S_TIPO'] !='ADMINISTRADOR' ){
	exit('No cuenta con permisos a este sitio');
}

?>
	<input type="text" id="login" value="<?= $_SESSION['S_LOGIN'] ?>" hidden>
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
			<div class="card">		
				<div class="card-header">
					<ul class="nav nav-pills card-header-pills">
						<li class="nav-item" style="font-weight: bold;">Informe Mensual</li>
						<button id="nuevoArchivo"  style="margin-left: 80%;" onclick="nuevoArchivo();">Nuevo Archivo</button>
					</ul>
												
				</div>
				
					<div class="card-body " >
						<div class="row">
						    <div class="col-md-12">
							 <div class="card-table table-responsive">
								<table class="table" id="informesMensuales">
                            <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha Cargue</th>
                                            <th>Nombre Archivo</th>
											<!-- <th>cantidad de filas Compras y saldos</th>
											<th>cantidad de filas Movimientos</th> -->
                                            <th>acciones</th>   
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
</div>

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script> 
<script src="../plugins/tabler/js/tabler.min.js"></script>
<script src="../plugins/tabler/js/demo.min.js"></script>
<script src="../plugins/sheetjs/xlsx.mini.min.js"></script>
<script src="../js/informes_mensuales.js"></script>
<?php 
	include("bottom.php");
?>