<?php

if (!isset($_SESSION["S_LOGIN"])) {
	if(!isset($_GET["cambiar_clave"])) {
		header('Location: index.php');
		exit;
	}
}

$link = conectar();

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>S.E.A.S.<?php echo $label_title ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="../images/favicon.ico">
	<link rel="STYLESHEET" type="text/css" href="../sty.css?v=4">
	
	<meta charset="iso-8859-1" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">

	
	<script src="//www.google.com/jsapi" type="text/javascript"></script>
	<script language="JavaScript" src="../jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script language="JavaScript" src="../functions.js"></script>
</head>
<body>
 	
<div id="contenedor<?php if (DeviceDetect() <> "desktop") { echo "1"; } ?>">
	<div class="header" id="encabezado">
		<div class="logo"></div>
		<?php 
		if(isset($_SESSION["S_LOGIN"])){ 

			$rs1 = sqlsrv_query($link, "SELECT b.* FROM usuarios_reportes a RIGHT JOIN reportes b ON b.id = a.id_reporte and b.tipo_reporte = 1  WHERE a.id_usuario = ".$_SESSION["S_IDUSUARIO"]);
			$_SESSION["REPORTE_TESORERIA_USUARIO"] = 00;
			$_SESSION["REPORTE_GESTION_COBRO_USUARIO"] = 00;
			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
				if ($fila1["descripcion"] == "REPORTE TESORERIA") { $_SESSION["REPORTE_TESORERIA_USUARIO"] = 1; }
				if ($fila1["descripcion"] == "REPORTE GESTION COBROS") { $_SESSION["REPORTE_GESTION_COBRO_USUARIO"] = 1; }
			}
			?>
			
			<a class="loggedas" href="#"><?php if (DeviceDetect() <> "mobile") { ?>Usuario: <?php  echo strtoupper(utf8_decode($_SESSION["S_NOMBRE"])) ?><?php } ?></a>
			<a class="camcon" href="cambiarclave.php?s_login=<?=$_SESSION["S_LOGIN"]?>">CAMBIAR CONTRASE&Ntilde;A</a>
			<?php 
			$consultarUsuarioDisponible="SELECT * FROM usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."'";
			$queryUsuarioDisponible=sqlsrv_query($link,$consultarUsuarioDisponible);
			$resUsuarioDisponible=sqlsrv_fetch_array($queryUsuarioDisponible);
					
			if ($_SESSION["S_SUBTIPO"]=="ANALISTA_CREDITO"){

				$consultarCreditosAsignados="SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$_SESSION["S_IDUSUARIO"]."' and estado=2 and vigente='s'";
				$queryCreditosAsignados=sqlsrv_query($link,$consultarCreditosAsignados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if (sqlsrv_num_rows($queryCreditosAsignados)==0 && ($resUsuarioDisponible["disponible"]=="s" || $resUsuarioDisponible["disponible"]=="g")){
					?>
					<a style="color:green;" name="<?php echo $_SESSION["S_IDUSUARIO"];?>" class="btnPedirCredito" id="btnPedirCredito" onClick="actionPedirCredito();">Pedir Credito</a>
					<?php
				}
			} 
		} ?>
		<a class="salir" name="<?php echo $resUsuarioDisponible["disponible"];?>" id="btnSalir" href="salir.php">SALIR</a>
	</div>
	<div align="center">
	 	<?php if(isset($_SESSION["S_LOGIN"])){ ?>
		<div id="menu">
			<ul class="sf-menu">
				
				<?php 				
				if ( (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && ($_SESSION["S_SOLOLECTURA"] != "1"))

				 || $_SESSION['S_CAUSALES_NO_RECAUDO'] == 1 || $_SESSION['S_REPORTE_SIN_MES_PROD']==1) { ?><li>CONFIGURACI&Oacute;N
					<ul style="width:250px">
						<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='parametros.php'">Par&aacute;metros</li><?php } ?>
						<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) && $_SESSION["FUNC_CARGUEPLANOS"]) { ?><li onclick="document.location.href='cargarplanos.php'">Cargar Planos</li><?php } ?>
					    <?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) { ?><li onclick="document.location.href='salariominimo.php'">Salario M&iacute;nimo</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "TESORERIA")) { ?><li onclick="document.location.href='bancos.php'">Bancos</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "TESORERIA")) { ?><li onclick="document.location.href='cuentasbancarias.php'">Cuentas Bancarias</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA")) { ?><li onclick="document.location.href='entidades.php'">Entidades Desembolso</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "CARTERA")) { ?><li onclick="document.location.href='compradores.php'">Compradores Cartera</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "CARTERA")) { ?><li onclick="document.location.href='vendedores.php'">Vendedores Cartera</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='documentoscierre.php'">Documentos Cierre Ventas</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='caracteristicas.php'">Caracter&iacute;sticas</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='causales.php'">Causales Negaci&oacute;n/Desistimiento</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION['S_CAUSALES_NO_RECAUDO'] == 1) { ?><li>Maestros No Recaudo
							<ul style="width:125px">
								<li onclick="document.location.href='tiposcausalesnorecaudo.php'">Tipos Causales</li>
								<li onclick="document.location.href='causalesnorecaudo.php'">Causales</li>
							</ul>
						</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='tiposadjuntos.php'">Tipos Adjuntos</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?><li onclick="document.location.href='tiposgestioncobro.php'">Tipos Gesti&oacute;n Cobro</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") { ?><li onclick="document.location.href='visadores.php'">Visadores</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA") { ?><li onclick="document.location.href='actividadessc.php'">Actividades/Solicitudes Servicio al Cliente</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li>Maestros Requerimientos/Excepciones
							<ul style="width:125px">
								<li onclick="document.location.href='tiposreqexcep.php'">Tipos</li>
								<li onclick="document.location.href='areasreqexcep.php'">&Aacute;reas</li>
							</ul>
						</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) { ?><li onclick="document.location.href='unidadesnegocio.php'">Unidades de Negocio</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) { ?><li onclick="document.location.href='rangos_edad_seguro.php'">Edades Valor X Millon</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) { ?><li>Plazos/Tasas
							<ul style="width:125px">
							    <li onclick="document.location.href='tasas.php?sector=PUBLICO'">Sector P&uacute;blico</li>
					    		<li onclick="document.location.href='tasas.php?sector=PRIVADO'">Sector Privado</li>
							</ul>
						</li><?php } ?>
					    <?php if ((($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES" && $_SESSION["S_MASTERSISTEMA"] == 1) && $_SESSION["FUNC_SUBESTADOS"]) { ?><li onclick="document.location.href='subestados.php'">Subestados</li><?php } ?>
					    <?php if ((($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") && $_SESSION["FUNC_SUBESTADOS"]) { ?><li onclick="document.location.href='etapas.php'">Etapas</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) { ?><li onclick="document.location.href='pagadurias.php'">Pagadur&iacute;as</li><?php } ?>
						<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO") { ?><li onclick="document.location.href='gestion_pagadurias.php'">Gestion Pagadur&iacute;as</li><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?><li onclick="document.location.href='pagaduriaspa.php'">Pagadur&iacute;as - NIT/P.A.</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) { ?><li onclick="document.location.href='descuentosadicionales.php'">Descuentos Adicionales</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='planesseguro.php'">Planes Seguro</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='oficinas.php'">Oficinas</li><?php } ?>
					    <?php if ((($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1)) && $_SESSION["S_MASTERSISTEMA"] == 1) { ?><li onclick="document.location.href='usuarios.php'">Usuarios</li><?php } ?>
					    <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1) || $_SESSION["S_TIPO"] == "OPERACIONES") { ?>
						    <li onclick="document.location.href='tasas_comisiones.php?ext=1'">Tasas Comisiones</li>
						    <li onclick="document.location.href='percentiles_comisiones.php'"<?php if (strpos($_SERVER["PHP_SELF"], "percentiles_comisiones.php")) { ?> class="activo"<?php } ?>>Percentiles</li>
						<?php } ?>
						<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION['S_SOLOLECTURA']!=1))  { ?>
							<li onclick="document.location.href='reportes.php'"<?php if (strpos($_SERVER["PHP_SELF"], "reportes.php")) { ?> class="activo"<?php } ?>>Reportes</li>
						<?php } ?>
					</ul>
				</li><?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || ($_SESSION["S_TIPO"] == "EXTERNOS" && $_SESSION["S_SUBTIPO"] == "ANALISTA_PREESTUDIO" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) || ($_SESSION["S_TIPO"] == "EXTERNOS" && $_SESSION["S_SUBTIPO"] == "ANALISTA_PREESTUDIO" )) { ?>
					<li onclick="document.location.href='pilotofdc2.php'"<?php if (strpos($_SERVER["PHP_SELF"], "pilotofdc2.php")) { ?> class="activo"<?php } ?>>INGRESADOS</li>
				<?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><!--<li onclick="document.location.href='prospecciones.php'"<?php if (strpos($_SERVER["PHP_SELF"], "prospecciones.php")) { ?> class="activo"<?php } ?>>PROSPECCI&Oacute;N</li>--><?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_REVISION_GARANTIAS"] == "1" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) { ?>
					<li onclick="document.location.href='pilotofdc.php'"<?php if (strpos($_SERVER["PHP_SELF"], "pilotofdc.php")) { ?> class="activo"<?php } ?>>INGRESOS FDC</li><?php } ?>
					<li onclick="document.location.href='simulaciones.php'"<?php if (strpos($_SERVER["PHP_SELF"], "simulaciones.php")) { ?> class="activo"<?php } ?>>SIMULACIONES</li>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><!--<li onclick="document.location.href='cierrecomercial.php'"<?php if (strpos($_SERVER["PHP_SELF"], "cierrecomercial.php")) { ?> class="activo"<?php } ?>>CIERRE COMERCIAL</li>--><?php } ?>

				<?php if ($_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "CONTABILIDAD"  && $_SESSION["S_SUBTIPO"] != "ANALISTA_PREESTUDIO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && $_SESSION["FUNC_AGENDA"] && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" && $_SESSION["S_TIPO"] == "OPERACIONES" && $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" ) && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1 && DeviceDetect() <> "desktop"))) { ?><li onclick="document.location.href='agenda.php'"<?php if (strpos($_SERVER["PHP_SELF"], "agenda.php")) { ?> class="activo"<?php } ?>>GESTI&Oacute;N CERT</li><?php } ?>

				<?php if ( $_SESSION["S_TIPO"] == "OUTSOURCING" || $_SESSION["FUNC_INDICADORES"] && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) && DeviceDetect() <> "desktop"))) { ?>
					<li>INDICADORES
					<ul style="width:200px">
					    <li onclick="document.location.href='indicadores.php'">Indicadores Comerciales/F&aacute;brica</li>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><!--<li onclick="document.location.href='indicadores_fabrica.php'">Indicadores F&aacute;brica</li>--><?php } ?>
					    <?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")) { ?><li onclick="document.location.href='indicadores_cartera.php'">Indicadores Cartera</li><?php } ?>
					</ul>
				</li><?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) { ?><li onclick="document.location.href='visadoincorp.php'"<?php if (strpos($_SERVER["PHP_SELF"], "visadoincorp.php")) { ?> class="activo"<?php } ?>>VISADO/INCORP</li><?php } ?>
				<?php if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["S_SUBTIPO"] != "ANALISTA_PREESTUDIO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><li onclick="document.location.href='tesoreria.php'"<?php if (strpos($_SERVER["PHP_SELF"], "tesoreria.php")) { ?> class="activo"<?php } ?>>TESORER&Iacute;A</li><?php } ?>
				<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) && ($_SESSION["S_LOGIN"] != "dianale")) { ?><li>CARTERA
					<ul style="width:200px">
						<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $_SESSION["FUNC_BOLSAINCORPORACION"]) { ?><li onclick="document.location.href='bolsainc.php'">Bolsa Incorporaci&oacute;n</li><?php } ?>
					    <li onclick="document.location.href='cartera.php'">Cartera Originaci&oacute;n</li>
					    <li onclick="document.location.href='cartera.php?ext=1'">Cartera Externa</li>
					    <li onclick="document.location.href='registrar_creditos_masivos.php'">Cargar Base SOFANEG</li>
					</ul>
				</li><?php } ?>
				<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1)) { ?><li>VENTA CARTERA
					<ul style="width:200px">
					    <li onclick="document.location.href='ventas.php'">Venta Cartera Originaci&oacute;n</li>
					    <li onclick="document.location.href='ventas.php?ext=1'">Venta Cartera Externa</li>
					</ul>
				</li><?php } ?>
				<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) && DeviceDetect() <> "desktop")) { ?><li onclick="document.location.href='reqexcep.php'"<?php if (strpos($_SERVER["PHP_SELF"], "reqexcep.php")) { ?> class="activo"<?php } ?>>REQ/EXCEP</li><?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><li onclick="document.location.href='serviciocliente.php'"<?php if (strpos($_SERVER["PHP_SELF"], "serviciocliente.php")) { ?> class="activo"<?php } ?>>SAC</li><?php } ?>

				<?php if ($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><li onclick="document.location.href='agenda_comerciales.php'"<?php if (strpos($_SERVER["PHP_SELF"], "agenda_comerciales.php")) { ?> class="activo"<?php } ?>>AGENDA COMERCIALES</li><?php } ?>
				<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><li onclick="document.location.href='informacion_mensual.php'"<?php if (strpos($_SERVER["PHP_SELF"], "informacion_mensual.php")) { ?> class="activo"<?php } ?>>INFORMACION MENSUAL</li><?php } ?>


				<?php 
				if ($_SESSION["S_VISUALIZAR_REPORTES"]==1)
				{
					if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1) { ?><li onclick="document.location.href='inventario_titulos.php'"<?php if (strpos($_SERVER["PHP_SELF"], "inventario_titulos.php")) { ?> class="activo"<?php } ?>>INVENTARIO TITULOS</li><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" ||($_SESSION['S_REPORTE_CARTERA'] == 1)) { ?><li>REPORTES
						<ul style="width:200px">
							<?php if ($_SESSION["S_IDUSUARIO"] == "3852" || $_SESSION["S_IDUSUARIO"] == "458" || $_SESSION["S_IDUSUARIO"] == "4853") { ?><li onclick="document.location.href='reporte_tesoreria_comprascartera.php'">Reporte Nuevo tesoreria</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_usuarios.php'">Reporte Usuarios</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_comerciales.php'">Reporte Planta Comercial</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || ($_SESSION["S_TIPO"] == "OFICINA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION") || $_SESSION["S_TIPO"] == "OPERACIONES") { ?><li onclick="document.location.href='reporte_clientes.php'">Reporte Clientes</li><?php } ?>
							<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") && $_SESSION["FUNC_LOGCONSULTAS"]) { ?><li onclick="document.location.href='reporte_logconsultas.php'">Reporte Consultas Realizadas</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_comercial.php'">Reporte Consultas x Comercial</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_colocacion.php'">Reporte Desembolso x Comercial</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_colocacionoficinas.php'">Reporte Originacion x Oficinas</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_originadora.php'">Reporte Originacion</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_prospecciones.php'">Reporte Prospecci&oacute;n</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION['S_REPORTE_CARTERA']==1) { ?><li onclick="document.location.href='reporte_simulaciones.php'">Reporte Simulaciones</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><li onclick="document.location.href='reporte_simulaciones_compras_cartera.php'">Reporte Simulaciones C. Cartera</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") { ?><li onclick="document.location.href='reporte_resumen_tesoreria.php'">Reporte Resumen Tesoreria</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><li onclick="document.location.href='reporte_seguro.php'">Reporte de Seguro</li><?php } ?>
							<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && $_SESSION["FUNC_AGENDA"]) { ?><li onclick="document.location.href='reporte_agenda.php'">Reporte Gesti&oacute;n Certificaciones</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><li onclick="document.location.href='reporte_visado.php'">Reporte Visado</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><li onclick="document.location.href='reporte_incorporacion.php'">Reporte Incorporaci&oacute;n</li><?php } ?>
							
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["REPORTE_TESORERIA_USUARIO"] == 1 )) { ?><li onclick="document.location.href='reporte_tesoreria.php'">Reporte Tesorer&iacute;a</li><?php } ?>

							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" ||  $_SESSION["S_TIPO"] == "GERENTECOMERCIAL")) { ?><li onclick="document.location.href='reporte_cierre.php'">Reporte Cierre</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><li onclick="document.location.href='reporte_retanqueos.php'">Reporte Retanqueos</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA")) { ?><li onclick="document.location.href='reporte_desembolsos.php'">Reporte Desembolsos</li><?php } ?>
							<?php 
							if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA")) { ?><li onclick="document.location.href='reporte_desembolsos_comisiones.php'">Reporte Desembolso Comisio</li><?php }
							 ?>
							<?php 
							 if($_SESSION["S_TIPO"] == "ADMINISTRADOR"){
							 ?>
							 	<li onclick="document.location.href='reporte_sin_mes_produccion.php'" >Reporte Comision Sin Mes Produccion</li>
							 <?php
							 }
							?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")) { ?><li onclick="document.location.href='reporte_comprascartera.php'">Reporte Compras Cartera</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><li onclick="document.location.href='reporte_giroscliente.php'">Reporte Giros al Cliente</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REPORTE_CARTERA']==1)) 
							{ ?><li onclick="document.location.href='reporte_cartera.php'">Reporte Cartera</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><li onclick="document.location.href='reporte_ventacartera.php'">Reporte Venta de Cartera</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" ||  $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD")) { ?><li onclick="document.location.href='reporte_imputacionpagos.php'">Reporte Imputaci&oacute;n Pagos</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_cuotasnorecaudadas.php'">Reporte Cuotas No Recaudadas</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD"  || $_SESSION["REPORTE_GESTION_COBRO_USUARIO"] == 1 )) { ?><li onclick="document.location.href='reporte_gestioncobro.php'">Reporte Gesti&oacute;n Cobro</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD")) { ?><li onclick="document.location.href='reporte_vencimientoscompradores.php'">Reporte Vencimientos Compradores</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_logsubestados.php'">Reporte Historial Subestados</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_reqexcep.php'">Reporte Requerimientos/Excepciones</li><?php } ?>
							<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD") { ?><li onclick="document.location.href='reporte_sc.php'">Reporte Servicio al Cliente</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD")) { ?><li onclick="document.location.href='reporte_centrales.php'">Reporte Centrales de Riesgo</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD")) { ?><li onclick="document.location.href='reporte_fdc.php'">Reporte FDC</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA")) { ?><li onclick="document.location.href='reporte_inventario_titulos.php'">Reporte Inventario Titulo</li><?php } ?>
							<?php if ($_SESSION["FUNC_FULLSYSTEM"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")) { ?><li onclick="document.location.href='reporte_fiduciaria.php'">Reporte Fiduciaria</li><?php } ?>
							<?php 
								if($_SESSION["S_TIPO"] == "ADMINISTRADOR") {
									echo "<li onclick='document.location.href=\"reporte_marketing.php\"'>Reporte Marketing</li>";
								}
							?>
							<?php if ($_SESSION["S_IDUSUARIO"] == "3852" || $_SESSION["S_IDUSUARIO"] == "4553") { ?>
							<li onclick='document.location.href="reporte_cuentas_contabilidad.php"'>Reporte Cuentas Contables</li>
							<?php } ?>
						</ul>
					</li><?php } 
				}
				?>

				<?php if ($_SESSION["S_TIPO"] == "OUTSOURCING" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" || ($_SESSION["S_TIPO"] == "COMERCIAL" && $_SESSION["S_SUBTIPO"] == "NEXA") || ($_SESSION["S_TIPO"] == "DIRECTOROFICINA" && $_SESSION["S_SUBTIPO"] == "NEXA" && $_SESSION["S_INTELIGENCIA_NEGOCIO"] != 1)) { ?>
					<li onclick="document.location.href='nexa.php'"<?php if (strpos($_SERVER["PHP_SELF"], "nexa.php")) { ?> class="activo"<?php } ?>>NEXA</li>
				<?php } ?>
	
				<!--<li onclick="document.location.href='ranking.php'"<?php if (strpos($_SERVER["PHP_SELF"], "ranking.php")) { ?> class="activo"<?php } ?>>RANKING-->
			</ul>
		</div>	
		<?php } ?>	
		<br>