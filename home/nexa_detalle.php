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

	<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  	<div class="container-xl">
	  	<div class="row row-cards">
			<div class="col-12">
				<form action="" class="card" id="formulario">
					<div class="card-header">
						<ul class="nav nav-pills card-header-pills">
							<li class="nav-item"> <strong>Pagaduria:</strong> <input type="text" readonly id="pagaduria"> </li>
							<li class="nav-item ms-auto"> <strong>No Solitud: </strong> <?php echo  base64_decode($_GET["id"]); ?> </li>
						</ul>
                  	</div>
					<div class="card-body form-fieldset" >
						<div class="row">										
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"> <strong>Número de cedula</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="numero_documento">
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Nombres</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="nombres">
								</div>
							</div>	
							
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Apellidos</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="apellidos">
								</div>
							</div>	
					  							
							<div class="col-md-1">
								<div class="mb-2">
									<label class="form-label"> <strong>Genero</strong></label>
									<input type="text" class="form-control is-valid mb-1" id="genero">
								</div>
							</div>

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Edad</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="" id="edad">
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Fecha de nacimiento</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="fecha_nacimiento">
								</div>
							</div>

							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Ciudad</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="ciudad">
								</div>
							</div>
					  	</div>

						<div class="row">
											
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Direccion</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="direccion">
								</div>
							</div>

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Teléfono</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="" id="telefono">
								</div>
							</div>

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Celular</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="" id="celular">
								</div>
							</div>														
					  							
							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Correo Electrónico</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="correo">
								</div>
							</div>

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Grado</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="" id="grado">
								</div>
							</div>

							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Tipo de Cargo</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="tipo_cargo">
								</div>
							</div>			  	

							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label"><strong>Cargo</strong></label>
									<input type="text" class="form-control is-valid mb-3" placeholder="" id="cargo">
								</div>
							</div>										
					  	</div>

						<div class="row">	

							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Nivel de contratación</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="nivel_contratacion">
								</div>
							</div>	

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Fecha de ingreso</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="" id="fecha_ingreso">
								</div>
							</div>				

							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Fecha de nombramiento</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="fecha_nombramiento">
								</div>
							</div>

							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label"><strong>Centro de Costos</strong></label>
									<input type="text" class="form-control is-valid mb-3" placeholder="" id="centro_costos">
								</div>
							</div>

							<div class="col-md-2">
								<div class="mb-2">
									<label class="form-label"><strong>Salario base</strong></label>
									<input type="text" class="form-control is-valid mb-2" placeholder="" id="salario_base">
								</div>
							</div>	

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Aportes</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="0" id="aportes">
								</div>
							</div>

							<div class="col-md-1">
								<div class="mb-1">
									<label class="form-label"><strong>Margen S.</strong></label>
									<input type="text" class="form-control is-valid mb-1" placeholder="0" id="margen_seguridad">
								</div>
							</div>	

					  	</div>
					</div>					
				</form>				
			</div>
		</div>
		<br>
		<div class="row row-cards">
			<div class="col-12">	
				<form action="" class="card">		
					<div class="card-header">
						<ul class="nav nav-pills card-header-pills">
							<li class="nav-item">Información Financiera</li>
						</ul>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="card-table table-responsive">
								<table class="table table-vcenter" id="tabla_carteras">
									<thead>
										<th>No.</th>
										<th>Entidad</th>
										<th>Valor</th>
										<th class="center">Se Compra?</th>
									</thead>
									<tbody>
									</tbody>
									<thead>
										<th colspan="2" style="text-align: right;">Total Carteras ($)</th>
										<th id="total_cartera">0</th>
										<th class="center" total_secompra="0" otros_descuentos="0" id="total_secompra">$ 0</th>
									</thead>
								</table>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card-table table-responsive">
								<ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
									<li class="nav-item" role="presentation" id="tabs_kredit_click">
										<a href="#tabs_kredit" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
											<svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="5 12 3 12 12 3 21 12 19 12"></polyline><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path></svg>
											KREDIT
										</a>
									</li>
									<li class="nav-item" role="presentation" id="tabs_fianti_click">
										<a href="#tabs_fianti" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/user -->
											<svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="7" r="4"></circle><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
											FIANTI
										</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active show" id="tabs_kredit" role="tabpanel">
										<table class="table table-vcenter" id="tabla_tasas_kredit">
											<thead>
												<tr>
													<th>Tasa</th>
													<th>Plazo</th>
													<th>Cuota</th>
													<th>Total Credito</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="tabs_fianti" role="tabpanel">
										<table class="table table-vcenter" id="tabla_tasas_fianti">
											<thead>
												<th>Tasa</th>
												<th>Plazo</th>
												<th>Cuota</th>
												<th>Total Credito</th>
												<th></th>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>								
								</div>								
							</div>
						</div>
					</div>	
				</form>			
			</div>
		</div>
		<br>
		<div class="d-flex">			
			<button type="button" class="btn btn-primary ms-auto" id="btn_simular">
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calculator" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
						<rect x="4" y="3" width="16" height="18" rx="2"></rect>
						<rect x="8" y="7" width="8" height="3" rx="1"></rect>
						<line x1="8" y1="14" x2="8" y2="14.01"></line>
						<line x1="12" y1="14" x2="12" y2="14.01"></line>
						<line x1="16" y1="14" x2="16" y2="14.01"></line>
						<line x1="8" y1="17" x2="8" y2="17.01"></line>
						<line x1="12" y1="17" x2="12" y2="17.01"></line>
						<line x1="16" y1="17" x2="16" y2="17.01"></line>
					</svg>
					Simular
				</span>
			</button>
		</div>
	</div>
  


	<!-- Modal -->
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script type="text/javascript" src="../js/nexa/nexa_detalle.js"></script>
	
	<!-- Tabler Core -->
	<script src="../plugins/tabler/libs/apexcharts/dist/apexcharts.min.js"></script>
	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script src="../plugins/tabler/js/demo.min.js"></script>
	
<?php 
	include("bottom.php");
?>