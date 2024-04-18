<?php
	include ('../functions.php');
	include ('../function_blob_storage.php');
	include ('../controles/validaciones.php');
	include ('./porcentajes_seguro.php');
	include ('./top.php');

	

	$link = conectar_utf();
	if (!$_SESSION["S_LOGIN"]) {
		exit;
	}
		
	if (!$_REQUEST["id_simulacion"] && ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'")){
		exit;
	}

?>

	<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet"/>
	<link href="../plugins/tabler/css/demo.min.css" rel="stylesheet"/>
	<link href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
	<link href="../plugins/DataTables/datatables.min.css?v=4" rel="stylesheet">
	<link href="../plugins/toastr/toastr.min.css" rel="stylesheet">

	<div class="col-12">
		<div class="navbar-expand-md row">
        	<div class="navbar-collapse" id="navbar-menu">
          		<div class="navbar navbar-light">
            		<div class="container-xl">
            			<ul class="navbar-nav">
							<li class="nav-item" style="margin-right: 1rem; margin-left: 1rem;">
								<span class="form-selectgroup-label" id="input_cargar_base">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
										<polyline points="7 9 12 4 17 9"></polyline>
										<line x1="12" y1="4" x2="12" y2="16"></line>
									</svg>
										Cargar Base
								</span>
							</li>

							<li class="nav-item">
								<span class="form-selectgroup-label" id="input_cargar_cartera">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M14 20h-8a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12v5"></path>
										<path d="M11 16h-5a2 2 0 0 0 -2 2"></path>
										<path d="M15 16l3 -3l3 3"></path>
										<path d="M18 13v9"></path>
									</svg>
									Cargar Carteras
								</span>
							</li>
              			</ul>
						
						<div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
							<div class="input-icon">
								<span class="input-icon-addon">
									<!-- Download SVG icon from http://tabler-icons.io/i/search -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<circle cx="10" cy="10" r="7"></circle>
										<line x1="21" y1="21" x2="15" y2="15"></line>
									</svg>
								</span>
								<input type="text" id="filtrar" class="form-control" placeholder="Buscar..." aria-label="Search in website" autocomplete="off">
							</div>
						</div>
					</div>
          		</div>
        	</div>
      	</div>
			
        <div class="card" id="divlistaClientes">
            <table class="table table-responsive hover" id="listaClientes">						
			</table>                                            
        </div>
	</div>

	<!-- Modal -->
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>	
	<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
	<script type="text/javascript" src="../plugins/toastr/toastr.min.js"></script>
	<script type="text/javascript" src="../js/nexa/nexa.js"></script>

	<!-- Tabler Core -->
	<script src="../plugins/tabler/libs/apexcharts/dist/apexcharts.min.js"></script>
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>
	
<?php 
	include("bottom.php");
?>