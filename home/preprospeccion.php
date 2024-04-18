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
	<!-- <script type="text/javascript" src="../js/nexa/nexa.js"></script> -->
    <script type="text/javascript" src="../js/preprospeccion/cargartabla.js?<?=rand()?>"></script>
    	<!-- Tabler Core -->
	<script src="../plugins/tabler/libs/apexcharts/dist/apexcharts.min.js"></script>
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>
	
<?php 
	include("bottom.php");
?>