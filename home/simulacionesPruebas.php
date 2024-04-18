<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	include ('../functions.php');
	/**
		* 2016-03-18 (jlvalencia)
		* Issue: los usuarios de subtipo prospección tienen acceso a TODOS los créditos
		* Se espera que vean SOLO los de la oficina a la que están vinculados
		* Modificaciones: 001 y 002 para filtrar el cuadro desplegable oficina y la consulta final 
	*/
	include("top.php"); 
	$link = conectar_utf();

?>
<style>
	.tooltip {
	  position: relative;
	  display: inline-block;
	  border-bottom: 1px dotted black;
	}

	.tooltip .tooltiptext {
	  visibility: hidden;
	  width: 120px;
	  background-color: black;
	  color: #fff;
	  text-align: center;
	  border-radius: 6px;
	  padding: 5px 0;

	  /* Position the tooltip */
	  position: absolute;
	  z-index: 1;
	}

	.tooltip:hover .tooltiptext {
	  visibility: visible;
	}
</style>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script language="JavaScript" src="../date.js"></script>
<?php
//echo $_SESSION["S_TIPO"]."---".$_SESSION["S_SUBTIPO"];
if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop") {

?>
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script language="JavaScript">

function registro_ado(id_simulacion) {
	var parametros = {
		"id_simulacion": id_simulacion
	};
	
	$.ajax({
    	type: "POST",
		async: false,
		url: "ado_registro.php",
		data: parametros
	});
}

function clickAndDisable(enlace) {
	// disable subsequent clicks
	enlace.onclick = function(event) {
		event.preventDefault();
	}
}   
//-->
</script>
<?php

}

?>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo"><center><b>Simulaciones</b><br><br></center></td>		
	</tr>
</table>
<?php	
	if ($_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SOLOLECTURA"] != "'1'" && $_SESSION["S_IDUNIDADNEGOCIO"] != "'0'")
	{
		if(isset($_REQUEST['libranza_busqueda'])){
			$_REQUEST['libranza_busqueda'] = intval(preg_replace('/[^0-9]+/', '', $_REQUEST['libranza_busqueda']), 10);
		}
	?>
		<form name=formato method=post action="simulaciones.php">
			<table border="0" cellspacing=1 cellpadding=2>
				<tr>
				<?php
				
				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" ||  $_SESSION["S_TIPO"] == "OUTSOURCING")) && $_SESSION["S_HABILITAR_PROSPECCION"] == 1)
				{
			
				?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) 
					{ 
					?>
					<td><a href="prospeccion_crear.php">Ingresar Prospección</a></td>
					<td>/</td><?php 
					} 
					?>
					<?php

				}
				
				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_PREPROSPECCION"] == "1") {
					echo '<td><a href="preprospeccion.php">Pre-Prospeccion</a></td>';
					echo '<td>/</td>';
				}


				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SOLICITAR_FIRMAS"] == "1") {
					echo '<td><a href="panel_sin_firma_comercial.php">Panel Sin Firma - Recuperar</a></td>';
					echo '<td>/</td>';
				}
	
				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OPERACIONES")
				{
	
				?>
					<td><a href="simulador.php">Simulador Oficina</a></td>
					<td>/</td>
				<?php

					}
					
				?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) 
					{ 
					?>
					<td><a href="simulador.php?tipo=COM">Simulador Comercial</a></td><?php } ?>
					<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || ($_SESSION["S_TIPO"] == "OFICINA" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA") || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OPERACIONES") { ?>
					<td>/</td>
					<td><a href="empleados.php">Ingreso Clientes</a></td>
					<?php } ?>
				
				<?php 
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR"){ 
							echo "<td>/</td>";
							echo "<td><a href='panel_sin_firma.php'>Verificar Firma Digital</a></td>";
						}
					?>	
				</tr>
			</table>
		</form>
	<hr noshade size=1 width=350>
	<?php

	}else if ($_SESSION["S_PREPROSPECCION"] == 1) {
		echo '<td><a href="preprospeccion.php">Pre-Prospeccion</a></td>';
	}

	?>
	<form name="formato2" method="post" action="simulaciones.php">

		<table>
			<tr>
				<td>
					<div class="box1 clearfix">
						<table border="0" cellspacing=0 cellpadding=0>
							<tr>
								<?php

								if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))
								{

								?>
								<td valign="bottom" style="white-space:nowrap;">F. Estudio Inicial<br>
									<input type="hidden" name="fecha_inicialb" size="10" maxlength="10">
									<select name="fecha_inicialbd">
										<option value="">D&iacute;a</option>
										<?php

											for ($i = 1; $i <= 31; $i++)
											{
												if (strlen($i) == 1)
												{
													$j = "0".$i;
												}
												else
												{
													$j = $i;
												}
												
												echo "<option value=\"".$j."\">".$j."</option>";
											}
											
										?>
									</select>
									<select name="fecha_inicialbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fecha_inicialba">
										<option value="">A&ntilde;o</option>
										<?php

											for ($i = 2014; $i <= date("Y"); $i++)
											{
												echo "<option value=\"".$i."\">".$i."</option>";
											}
											
										?>
									</select>
									<a href="javascript:show_calendar('formato2.fecha_inicialb');"><img src="../images/calendario.gif" border=0></a>&nbsp
								</td>
								<td valign="bottom" style="white-space:nowrap;">F. Estudio Final<br>
									<input type="hidden" name="fecha_finalb" size="10" maxlength="10">
									<select name="fecha_finalbd">
										<option value="">D&iacute;a</option>
										<?php

											for ($i = 1; $i <= 31; $i++)
											{
												if (strlen($i) == 1)
												{
													$j = "0".$i;
												}
												else
												{
													$j = $i;
												}
												
												echo "<option value=\"".$j."\">".$j."</option>";
											}
											
										?>
									</select>
									<select name="fecha_finalbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fecha_finalba">
										<option value="">A&ntilde;o</option>
										<?php

											for ($i = 2014; $i <= date("Y"); $i++) {
												echo "<option value=\"".$i."\">".$i."</option>";
											}
											
										?>
									</select>
									<a href="javascript:show_calendar('formato2.fecha_finalb');"><img src="../images/calendario.gif" border=0></a>&nbsp;
								</td>
								<?php

									if ($_SESSION["FUNC_FDESEMBOLSO"])
									{
									
								?>
								<td valign="bottom" style="white-space:nowrap;">F. Desembolso Inicial<br>
									<input type="hidden" name="fechades_inicialb" size="10" maxlength="10">
									<select name="fechades_inicialbd">
										<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++)
									{
										if (strlen($i) == 1)
										{
											$j = "0".$i;
										}
										else
										{
											$j = $i;
										}
										
										echo "<option value=\"".$j."\">".$j."</option>";
									}
									
									?>
									</select>
									<select name="fechades_inicialbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fechades_inicialba">
										<option value="">A&ntilde;o</option>
										<?php

										for ($i = 2014; $i <= date("Y"); $i++)
										{
											echo "<option value=\"".$i."\">".$i."</option>";
										}
										
										?>
									</select>
									<a href="javascript:show_calendar('formato2.fechades_inicialb');"><img src="../images/calendario.gif" border=0></a>
								</td>
								<td valign="bottom" style="white-space:nowrap;">F. Desembolso Final<br>
									<input type="hidden" name="fechades_finalb" size="10" maxlength="10">
									<select name="fechades_finalbd">
										<option value="">D&iacute;a</option>
									<?php

									for ($i = 1; $i <= 31; $i++)
									{
										if (strlen($i) == 1)
										{
											$j = "0".$i;
										}
										else
										{
											$j = $i;
										}
										
										echo "<option value=\"".$j."\">".$j."</option>";
									}
									
									?>
									</select>
									<select name="fechades_finalbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fechades_finalba">
										<option value="">A&ntilde;o</option>
										<?php

												for ($i = 2014; $i <= date("Y"); $i++)
												{
													echo "<option value=\"".$i."\">".$i."</option>";
												}
												
										?>
									</select>
									<a href="javascript:show_calendar('formato2.fechades_finalb');"><img src="../images/calendario.gif" border=0></a>&nbsp;
								</td>
								<?php

									}
									
								?>
								<td valign="bottom">Mes Prod Inicial<br>
									<select name="fechaprod_inicialbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fechaprod_inicialba">
										<option value="">A&ntilde;o</option>
										<?php

											for ($i = 2014; $i <= date("Y"); $i++)
											{
												echo "<option value=\"".$i."\">".$i."</option>";
											}
											
										?>
									</select>
								</td>
								<td valign="bottom">Mes Prod Final<br>
									<select name="fechaprod_finalbm">
										<option value="">Mes</option>
										<option value="01">Ene</option>
										<option value="02">Feb</option>
										<option value="03">Mar</option>	
										<option value="04">Abr</option>
										<option value="05">May</option>
										<option value="06">Jun</option>
										<option value="07">Jul</option>
										<option value="08">Ago</option>
										<option value="09">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dic</option>
									</select>
									<select name="fechaprod_finalba">
										<option value="">A&ntilde;o</option>
										<?php

											for ($i = 2014; $i <= date("Y"); $i++)
											{
												echo "<option value=\"".$i."\">".$i."</option>";
											}
											
										?>
									</select>
								</td>
								
								<!-- <td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td> -->
								<td valign="bottom">C&eacute;dula<br><input type="text" name="cedula_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td>
								<td valign="bottom">No. Libranza<br><input type="text" name="libranza_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td>	
								</tr>
							<tr>
								<td valign="bottom">Pagadur&iacute;a<br>
									<select name="pagaduriab" style="width:155px">

										<option value=""></option>
										<?php

											$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";
											
											if ($_SESSION["S_SECTOR"])
											{
												$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
											}
								
											$queryDB .= " order by pagaduria";

											echo $queryDB;
											
											// $rs1 = sqlsrv_query($link, $queryDB);
											// Inicio prueba
											if ($rs1 == false) {
												if( ($errors = sqlsrv_errors() ) != null) {
													foreach( $errors as $error ) {
													   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
														echo "code: ".$error[ 'code']."<br />";
													   echo "message: ".$error[ 'message']."<br />";
														   }
														}
											   }
											   // Fin prueba

											// while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
											// {
											// 	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(($fila1["pagaduria"]))."</option>\n";
											// }
											
										?>

									</select>
								</td>

								
								<?php

								if (!$_SESSION["S_SECTOR"])
								{
									
									?>
								<td valign="bottom">Sector<br>
									<select name="sectorb" style="width:155px">
										<option value=""></option>
										<option value="PUBLICO">PUBLICO</option>
										<option value="PRIVADO">PRIVADO</option>
									</select>
								</td>
								<?php

								}
									
								?>

								<td valign="bottom">Unidad de Negocio<br>
									<select name="unidadnegociob" style="width:155px">
										<option value=""></option>
										<?php

											$queryDB = "select id_unidad, nombre from unidades_negocio where 1 = 1";

											$queryDB .= " AND id_unidad IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

											$queryDB .= " order by id_unidad";
											echo $queryDB;

											// $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
											//Prueba Query
											if ($rs1 == false) {
												if( ($errors = sqlsrv_errors() ) != null) {
													foreach( $errors as $error ) {
													   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
														echo "code: ".$error[ 'code']."<br />";
													   echo "message: ".$error[ 'message']."<br />";
														   }
														}
											   }

											// while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
											// {
											// 	echo "<option value=\"".$fila1["id_unidad"]."\">".($fila1["nombre"])."</option>\n";
											// }

										?>
									</select>
								</td>
								<?php

								if ($_SESSION["S_TIPO"] != "COMERCIAL")
								{
							
								?>
								<td valign="bottom">Oficina<br>
									<select name="id_oficinab" style="width:155px">
										<option value=""></option>
									<?php

									$queryDB = "select id_oficina, codigo, nombre from oficinas";
									// 001
									if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
									{
										$queryDB .= " where id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
									}
									
									$queryDB .= " order by nombre";	
									echo $queryDB;
									
									// $rs1 = sqlsrv_query($link, $queryDB);
									//Prueba Query
									if ($rs1 == false) {
										if( ($errors = sqlsrv_errors() ) != null) {
											foreach( $errors as $error ) {
											   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
												echo "code: ".$error[ 'code']."<br />";
											   echo "message: ".$error[ 'message']."<br />";
												   }
												}
									   }
									
									// while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
									// {
									// 	echo "<option value=\"".$fila1["id_oficina"]."\">".($fila1["nombre"])."</option>\n";
									// }
									
									?>

									</select>
								</td>
								<?php 

								}

								if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_SUBTIPO"] != "PLANTA" && $_SESSION["S_SUBTIPO"] != "PLANTA_TELEMERCADEO" && $_SESSION["S_SUBTIPO"] != "EXTERNOS" && $_SESSION["S_SUBTIPO"] != "TELEMERCADEO")
								{
								
								?>
								<td valign="bottom">Tipo Comercial<br>
									<select name="tipo_comercialb" style="width:140px">
										<option value=""></option>
										<option value="PLANTA">PLANTA</option>
										<option value="EXTERNOS">EXTERNOS</option>
									</select>
								</td>
								<?php

								}
			
								if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION")
								{
								
								?>
								<td valign="bottom">Comercial<br>
									<select name="id_comercialb" style="width:140px">
										<option value=""></option>
										<?php
									
											
										
										$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join usuarios_unidades uu on us.id_usuario = uu.id_usuario where us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";
										
										if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM"){
											$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
										}

										$consultarCoordinacionUsuarios="SELECT * FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."'"; 

										echo $consultarCoordinacionUsuarios;
										// $queryCoordinacionUsuarios=sqlsrv_query($link, $consultarCoordinacionUsuarios, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

										//Prueba Query
										if ($queryCoordinacionUsuarios == false) {
											if( ($errors = sqlsrv_errors() ) != null) {
												foreach( $errors as $error ) {
												   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
													echo "code: ".$error[ 'code']."<br />";
												   echo "message: ".$error[ 'message']."<br />";
													   }
													}
										   }
										//Fin prueba Query

										if (sqlsrv_num_rows($queryCoordinacionUsuarios)>0)
										{
												
											$queryDB .= " AND us.id_usuario in (SELECT id_usuario_secundario FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."')";
												
											
										}

										$queryDB .= " AND uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
										
										$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";
										echo $queryDB;

										// $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

										//Prueba Query
										if ($rs1 == false) {
											if( ($errors = sqlsrv_errors() ) != null) {
												foreach( $errors as $error ) {
												   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
													echo "code: ".$error[ 'code']."<br />";
												   echo "message: ".$error[ 'message']."<br />";
													   }
													}
										   }
										  //Fin Prueba 
										
										
										// while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
										// {
										// 	echo "<option value=\"".$fila1["id_usuario"]."\">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
										// }
										
										?>


									</select>&nbsp;
								</td>
							<?php

							}
							else
							{
								if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
								{
								
									?>
								<td valign="bottom">Comercial<br>
									<select name="id_comercialb" style="width:140px">
										<option value=""></option>
										<?php

										$queryDB = "select distinct us.id_usuario, us.nombre, us.apellido from usuarios us inner join simulaciones si on us.id_usuario = si.id_comercial inner join unidades_negocio un on si.id_unidad_negocio = un.id_unidad where si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."') AND us.tipo <> 'MASTER' AND us.tipo = 'COMERCIAL'";
										
										$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

										if ($_SESSION["S_SUBTIPO"] == "PLANTA")
											$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
										
										if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
											$queryDB .= " AND si.telemercadeo in ('0','1')";
										
										if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
											$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
										
										if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
											$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
										
										if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
											$queryDB .= " AND si.telemercadeo = '1'";
										
										$queryDB .= " order by us.nombre, us.apellido, us.id_usuario";

										echo $queryDB;
										
										// $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
										//Prueba Query
										if ($rs1 == false) {
											if( ($errors = sqlsrv_errors() ) != null) {
												foreach( $errors as $error ) {
												   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
													echo "code: ".$error[ 'code']."<br />";
												   echo "message: ".$error[ 'message']."<br />";
													   }
													}
										   }
										  //Fin Prueba 
										
										while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
										{
											echo "<option value=\"".$fila1["id_usuario"]."\">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
										}
										
							?>
									</select>&nbsp;
								</td>
							<?php

								}
							}
							
							?>
								<td valign="bottom">Estado<br>
									<select name="estadob" style="width:140px">
										<option value=""></option>
										<option value="ING">INGRESADO</option>
										<option value="EST">EN ESTUDIO</option>
										<option value="NEG">NEGADO</option>
										<option value="DST">DESISTIDO</option>
										<!--<option value="DSS">DESISTIDO SISTEMA</option>-->
										<option value="DES">DESEMBOLSADO</option>
										<option value="CAN">CANCELADO</option>
										<option value="ANU">ANULADO</option>
									</select>&nbsp;
								</td>


								<td valign="bottom">Decisión<br>
									<select name="decisionb" style="width:110px">

										<option value=""></option>
										<option value="<?php echo $label_viable ?>"><?php echo $label_viable ?></option>
										<option value="<?php echo $label_negado ?>"><?php echo $label_negado ?></option>
									</select>&nbsp;
								</td>
								<?php

									if ($_SESSION["FUNC_SUBESTADOS"])
									{
									
								?>
								<td valign="bottom">Subestado<br>
									<select name="id_subestadob" style="width:150px">
										<option value=""></option>
									<?php

									$queryDB = "select id_subestado, decision, nombre from subestados where estado = '1' order by decision DESC, nombre"; 
									 echo $queryDB;
									// $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
									//Prueba Query
									if ($rs1 == false) {
										if( ($errors = sqlsrv_errors() ) != null) {
											foreach( $errors as $error ) {
											   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
												echo "code: ".$error[ 'code']."<br />";
											   echo "message: ".$error[ 'message']."<br />";
												   }
												}
									   }
									  //Fin Prueba 
									
									// while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
									// {
									// 	echo "<option value=\"".$fila1["id_subestado"]."\">".substr($fila1["decision"], 0, 3)."-".($fila1["nombre"])."</option>\n";
									// }
									
									?>
									</select>&nbsp;
								</td>
							<?php

								}
		
								if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
								{
								
							?>
								<td valign="bottom">Visualizar<br>
									<select name="visualizarb" style="width:110px">
										<option value="1"<?php if ($_REQUEST["visualizarb"] || !$_REQUEST["buscar"]) { echo " selected"; } ?>>MIS CREDITOS</option>
										<option value="0"<?php if (!$_REQUEST["visualizarb"] && $_REQUEST["buscar"]) { echo " selected"; } ?>>TODO</option>
									</select>&nbsp;
								</td>
							<?php

								}
								else
								{
								
							?>
								<input type="hidden" name="visualizarb" value="0">
							<?php

								}
		
							if ($_SESSION["FUNC_CALIFICACION"])
							{
							
							?>
							<td valign="bottom">Calificación<br>
								<select name="calificacionb" style="color:#F58F1F; width:110px">
									<option value=""></option>
									<option value="5">&#9733;&#9733;&#9733;&#9733;&#9733;</option>
									<option value="4">&#9733;&#9733;&#9733;&#9733;</option>
									<option value="3">&#9733;&#9733;&#9733;</option>
									<option value="2">&#9733;&#9733;</option>
									<option value="1">&#9733;</option>
								</select>&nbsp;
							</td>
							<?php

							}
		
							if ($_SESSION["FUNC_AGENDA"])
							{
							
							?>
							<!--	<td valign="bottom">Estado Certificaciones<br>
								<select name="statusb" style="width:155px">
									<option value=""></option>
									<option value="PROCESO">PROCESO</option>
									<option value="PARA RADICAR">PARA RADICAR</option>
								</select>
							</td>-->
							<?php

							}
													
							?>
							<?php

							}
							else
							{

							?>
								<!-- <td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:100px"></td> -->
								<td valign="bottom">C&eacute;dula<br><input type="text" name="cedula_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td>
							<td valign="bottom">No. Libranza<br><input type="text" name="libranza_busqueda" onBlur="ReplaceComilla(this)" maxlength="50" style="width:160px"></td>	
							<?php

							}
							?>
						
							<tr>
							<tr>
							<td valign="bottom">ID Simulacion<br><input type="text" name="id_simulacion_buscar" onBlur="ReplaceComilla(this)" maxlength="50" style="width:100px"></td>
							<td valign="bottom">Tipo Comercial<br>

							
								<select name="tipo_comercial_buscar" style="width:110px">
									<option value=""></option>
									<option value="FREELANCE">FREELANCE</option>
									<option value="OUTSOURCING">OUTSOURCING</option>
						
								</select>&nbsp;
							</td>
							<td valign="bottom">Tipo Pagare<br>

							
								<select name="tipo_pagare" style="width:110px">
									<option value=""></option>
									<option value="1">DIGITAL</option>
									<option value="0">FISICO</option>
						
								</select>&nbsp;
							</td>	
						</tr>
								<tr>
							<td valign="bottom"><br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
						</tr>
						</table>
					</div>
				</td>
			</tr>
			</table>
	</form>
		<?php

		if ($_REQUEST["action"])
		{
			if (!$_REQUEST["page"])
			{
				$_REQUEST["page"] = 0;
			}
			
			$x_en_x = 100;
			
			$offset = $_REQUEST["page"] * $x_en_x;
			
			$queryDB = "SELECT  si.fecha_incorporado, si.resp_gestion_cobranza,si.incorporacion,si.visado,CASE WHEN (us.freelance=1 AND us.outsourcing=0) THEN 'FREELANCE' WHEN (us.freelance=0 AND us.outsourcing=1) THEN 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,si.*, se.cod_interno FROM simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN (SELECT si.id_simulacion, co.nombre_corto AS comprador FROM ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador WHERE ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN causales cau ON si.id_causal = cau.id_causal WHERE si.id_simulacion IS NOT NULL";
					    
			
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
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
					$queryDB .= " AND si.telemercadeo in ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				
				if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
					$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
					$queryDB .= " AND si.telemercadeo = '1'";
			}
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
			{
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
			}
			
			if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
			{
				$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
				
				$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
				
				$fecha_inicialba = $_REQUEST["fecha_inicialba"];
				
				$queryDB .= " AND si.fecha_estudio >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
			}
			
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
			{
				$fecha_finalbd = $_REQUEST["fecha_finalbd"];
				
				$fecha_finalbm = $_REQUEST["fecha_finalbm"];
				
				$fecha_finalba = $_REQUEST["fecha_finalba"];
				
				$queryDB .= " AND si.fecha_estudio <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
			}
			
			if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"])
			{
				$fechades_inicialbd = $_REQUEST["fechades_inicialbd"];
				
				$fechades_inicialbm = $_REQUEST["fechades_inicialbm"];
				
				$fechades_inicialba = $_REQUEST["fechades_inicialba"];
				
				$queryDB .= " AND si.fecha_desembolso >= '".$fechades_inicialba."-".$fechades_inicialbm."-".$fechades_inicialbd."'";
			}
			
			if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"])
			{
				$fechades_finalbd = $_REQUEST["fechades_finalbd"];
				
				$fechades_finalbm = $_REQUEST["fechades_finalbm"];
				
				$fechades_finalba = $_REQUEST["fechades_finalba"];
				
				$queryDB .= " AND si.fecha_desembolso <= '".$fechades_finalba."-".$fechades_finalbm."-".$fechades_finalbd."'";
			}
			
			if ($_REQUEST["fechaprod_inicialbm"] && $_REQUEST["fechaprod_inicialba"])
			{
				$fechaprod_inicialbm = $_REQUEST["fechaprod_inicialbm"];
				
				$fechaprod_inicialba = $_REQUEST["fechaprod_inicialba"];
				
				$queryDB .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') >= '".$fechaprod_inicialba."-".$fechaprod_inicialbm."'";
			}
			
			if ($_REQUEST["fechaprod_finalbm"] && $_REQUEST["fechaprod_finalba"])
			{
				$fechaprod_finalbm = $_REQUEST["fechaprod_finalbm"];
				
				$fechaprod_finalba = $_REQUEST["fechaprod_finalba"];
				
				$queryDB .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') <= '".$fechaprod_finalba."-".$fechaprod_finalbm."'";
			}
			
			// if ($_REQUEST["descripcion_busqueda"])
			// {
			// 	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
				
			// 	$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
			// }

			if ($_REQUEST["cedula_busqueda"]) {
				$cedula_busqueda = $_REQUEST["cedula_busqueda"];
				$queryDB .= " AND (si.cedula = '".$cedula_busqueda."')";
			}
			
			if ($_REQUEST["libranza_busqueda"]) {
				$libranza_busqueda = $_REQUEST["libranza_busqueda"];
				$queryDB .= " AND (si.libranza = '".$libranza_busqueda."')";
			}
			
			if ($_REQUEST["id_simulacion_buscar"])
			{
				$id_simulacion_buscar = $_REQUEST["id_simulacion_buscar"];
				
				$queryDB .= " AND (si.id_simulacion = '".$id_simulacion_buscar."'";
			}

			if ($_REQUEST["unidadnegociob"])
			{
				$unidadnegociob = $_REQUEST["unidadnegociob"];
				
				$queryDB .= " AND si.id_unidad_negocio = '".$unidadnegociob."'";
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
			
			if ($_REQUEST["tipo_comercialb"])
			{
				$tipo_comercialb = $_REQUEST["tipo_comercialb"];
				
				if ($tipo_comercialb == "PLANTA")
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				else
					$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1')";
			}
			
			if ($_REQUEST["id_comercialb"])
			{
				$id_comercialb = $_REQUEST["id_comercialb"];
				
				$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
			}else{
				$consultarCoordinacionUsuarios="SELECT * FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."'";
				$queryCoordinacionUsuarios=sqlsrv_query($link, $consultarCoordinacionUsuarios, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				//Prueba Query
				if ($queryCoordinacionUsuarios == false) {
					if( ($errors = sqlsrv_errors() ) != null) {
						foreach( $errors as $error ) {
						   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
							echo "code: ".$error[ 'code']."<br />";
						   echo "message: ".$error[ 'message']."<br />";
							   }
							}
				   }
				  //Fin Prueba 

				if (sqlsrv_num_rows($queryCoordinacionUsuarios)>0)
				{
					$queryDB .= " AND si.id_comercial in (SELECT id_usuario_secundario FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."')";
				}
			}
			
			if ($_REQUEST["estadob"])
			{
				$estadob = $_REQUEST["estadob"];
				
				$queryDB .= " AND si.estado = '".$estadob."'";
			}
			else
			{
				
				if (!$_REQUEST["descripcion_busqueda"] && !$_REQUEST["id_simulacion_buscar"])
				{
					$queryDB .= " AND si.estado NOT IN ('ANU', 'DSS','DST')";
				}
			}
			
			if ($_REQUEST["decisionb"])
			{
				$decisionb = $_REQUEST["decisionb"];
				
				$queryDB .= " AND si.decision = '".$decisionb."'";
			}
			
			if ($_REQUEST["id_subestadob"])
			{
				$id_subestadob = $_REQUEST["id_subestadob"];
				
				$queryDB .= " AND si.id_subestado = '".$id_subestadob."'";
			}
			
			if ($_REQUEST["visualizarb"])
			{
				$visualizarb = $_REQUEST["visualizarb"];
				
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
					$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
				
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
					$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
			}
			
			if ($_REQUEST["calificacionb"])
			{
				$calificacionb = $_REQUEST["calificacionb"];
				
				$queryDB .= " AND si.calificacion = '".$calificacionb."'";
			}
			
			if ($_REQUEST["statusb"])
			{
				$statusb = $_REQUEST["statusb"];
				
				$queryDB .= " AND si.status = '".$statusb."'";
			}
			
			if ($_REQUEST["id_oficinab"])
			{
				$id_oficinab = $_REQUEST["id_oficinab"];
				
				$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
			}
					
			if($_REQUEST["tipo_pagareb"]){
				$tipo_pagareb = $_REQUEST["tipo_pagareb"];
				$queryDB .= " AND si.formato_digital = '".$tipo_pagareb."'";
			}


			$queryDB .= " order by si.fecha_radicado DESC OFFSET ".$offset. " ROWS";
			echo $queryDB;
			// $rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			//Prueba Query
			if ($rs == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
					   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
					   echo "message: ".$error[ 'message']."<br />";
						   }
						}
			   }
			  //Fin Prueba 
			
	

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				
				
				

				if ($_REQUEST["chk".$fila["id_simulacion"]] == "1")
				{
					if ($_REQUEST["action"] == "anular")
					{
						if ($fila["estado"] == "ING" || $fila["estado"] == "EST")
						{
							sqlsrv_query($link, "update simulaciones set estado = 'ANU', id_subestado = NULL, usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = GETDATE() where id_simulacion = '".$fila["id_simulacion"]."'");
							
							$rs2 = sqlsrv_query($link, "select cedula, pagaduria, retanqueo1_libranza, retanqueo2_libranza, retanqueo3_libranza from simulaciones where id_simulacion = '".$fila["id_simulacion"]."'",  array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET) );

							//Prueba Query
							if ($rs2 == false) {
								if( ($errors = sqlsrv_errors() ) != null) {
									foreach( $errors as $error ) {
									   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
										echo "code: ".$error[ 'code']."<br />";
									   echo "message: ".$error[ 'message']."<br />";
										   }
										}
							   }
							  //Fin Prueba 
							
							$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
							
							for ($i = 1; $i <= 3; $i++)
							{
								if ($fila2["retanqueo".$i."_libranza"])
								{
									$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila2["cedula"]."' AND pagaduria = '".$fila2["pagaduria"]."' AND nro_libranza = '".$fila2["retanqueo".$i."_libranza"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


									//Prueba Query
									if ($rs1 == false) {
										if( ($errors = sqlsrv_errors() ) != null) {
											foreach( $errors as $error ) {
											echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
												echo "code: ".$error[ 'code']."<br />";
											echo "message: ".$error[ 'message']."<br />";
												}
												}
									}
									//Fin Prueba 
									
									if (sqlsrv_num_rows($rs1))
									{
										$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
										
										sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
									}
								}
							}
							

							$ultima_caracterizacion = sqlsrv_query($link, "SELECT TOP 1 id_transaccion, cod_transaccion, observacion from contabilidad_transacciones where id_simulacion = '".$fila["id_simulacion"]."' AND id_origen = '1' order by id_transaccion DESC ");

							$fila2 = sqlsrv_fetch_array($ultima_caracterizacion);

							// aquii

							if ($fila2["cod_transaccion"])
							{
								$hay_para_reversar = sqlsrv_query($link, "SELECT tOP 1 id_transaccion_movimiento from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%'");
								
								if (sqlsrv_num_rows($hay_para_reversar))
								{
									sqlsrv_query($link, "START TRANSACTION");
									
									sqlsrv_query($link, "INSERT into contabilidad_transacciones (id_origen, id_simulacion, cod_transaccion, fecha, valor, observacion, estado, usuario_creacion, fecha_creacion) values ('1', '".$fila["id_simulacion"]."', UPPER(MD5('".$fila["id_simulacion"]."-".date("Y-m-d H:i:s")."')), GETDATE(), '0', '".$fila2["observacion"]."', 'PEN', '".$_SESSION["S_LOGIN"]."', GETDATE())");

									$rs4 = sqlsrv_query($link, "select MAX(id_transaccion) as m from contabilidad_transacciones");
									
									$fila4 = sqlsrv_fetch_array($rs4);
									
									$id_trans = $fila4["m"];
									
									sqlsrv_query($link, "COMMIT");
									
									sqlsrv_query($link, "update contabilidad_transacciones set cod_transaccion_previa = '".$fila2["cod_transaccion"]."' where id_transaccion = '".$id_trans."'");

									sqlsrv_query($link, "INSERT into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, id_entidad, auxiliar, debito, credito, observacion) select '".$id_trans."', id_simulacion_retanqueo, id_entidad, auxiliar, credito, debito, CONCAT('REVERSION - ', observacion) from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' order by id_transaccion_movimiento");
								}
							}
							
							sqlsrv_query($link, "UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$fila["id_simulacion"]."'");
							sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) values ('".$fila["id_simulacion"]."',197,197,current_timestamp,'s',6,7)");
							$queryCantCreditosAnalista=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion=".$fila["id_analista_riesgo_operativo"]." and estado=2 and vigente='s' and id_simulacion<>".$fila["id_simulacion"], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));




							if (sqlsrv_num_rows($queryCantCreditosAnalista)>0)
							{

							}else{
								sqlsrv_query($link, "UPDATE usuarios SET disponible='n' WHERE id_usuario='".$fila["id_analista_riesgo_operativo"]."'");
								//echo "<script>alert('UPDATE usuarios SET disponible='n' WHERE id_usuario='".$fila["id_analista_riesgo_operativo"]."');</script>";
							}
						}
					}else{
							echo "<script>alert('La simulacion ".$fila["cedula"]." ".$fila["nombre"]." no puede ser Anulada. No cumple con las condiciones para realizar esta accion');</script>";
					}
				}
			}
		}

		if (!$_REQUEST["page"])
		{
			$_REQUEST["page"] = 0;
		}

		$x_en_x = 100;

		$offset = $_REQUEST["page"] * $x_en_x;

		if ($_REQUEST["buscar"])
		{
			//JAIRO ZAPATA
			//10-11-2021 => SE AGREGA CAMPO id_unidad_negocio A LA CONSULTA PARA PODEjairoR ENVIARLA DENTRO DE LOS PARAMETROS EN EL ENLACE HACIA solicitud.php
			$queryDB = "SELECT si.fecha_incorporado, si.estado_venta_cartera,si.resp_gestion_cobranza,si.incorporacion,si.visado,CASE WHEN (us.freelance=1 AND us.outsourcing=0) THEN 'FREELANCE' WHEN (us.freelance=0 AND us.outsourcing=1) THEN 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,si.id_unidad_negocio,si.id_simulacion, us.freelance, us.outsourcing, si.fecha_estudio, si.fecha_desembolso,  FORMAT( si.fecha_cartera, 'yyyy-MM') as mes_prod, si.cedula, si.empleado_manual, si.nombre, si.pagaduria, us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.retanqueo_total, si.valor_credito, si.estado, si.calificacion, si.decision, et.nombre as nombre_etapa, si.id_subestado, se.nombre as nombre_subestado, se.cod_interno, cau.nombre as nombre_causal, si.usuario_desistimiento, CONVERT(DATE, si.fecha_desistimiento, 111) as fecha_desistimiento, vex.comprador, si.valor_visado, si.incorporado, si.validado, si.fecha_ado, si.score_ado, si.telemercadeo from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN (select si.id_simulacion, co.nombre_corto as comprador from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN causales cau ON si.id_causal = cau.id_causal where si.id_simulacion IS NOT NULL";

			
			
			$queryDB_count = "SELECT COUNT(*) as c from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN (select si.id_simulacion, co.nombre_corto as comprador from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN causales cau ON si.id_causal = cau.id_causal where si.id_simulacion IS NOT NULL";
			
			$queryDB_suma = "SELECT SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc WHEN 'CMP' THEN si.opcion_desembolso_cmp WHEN 'CSO' THEN si.opcion_desembolso_cso END) as s, SUM(CASE si.opcion_credito WHEN 'CLI' THEN si.opcion_desembolso_cli WHEN 'CCC' THEN si.opcion_desembolso_ccc - si.retanqueo_total WHEN 'CMP' THEN si.opcion_desembolso_cmp - si.retanqueo_total WHEN 'CSO' THEN si.opcion_desembolso_cso - si.retanqueo_total END) as s2, SUM(si.valor_credito) as s3 from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN (select si.id_simulacion, co.nombre_corto as comprador from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN causales cau ON si.id_causal = cau.id_causal where si.id_simulacion IS NOT NULL";

		
			
			if ($_SESSION["S_SECTOR"]){
				$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
				$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
				$queryDB_suma .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
			}
			
			if ($_SESSION["S_TIPO"] == "COMERCIAL"){
				$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
				$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
				$queryDB_suma .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
			}
			else{
				$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
				$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
				$queryDB_suma .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
			}
			
			if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION"){	
				$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA")
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
					$queryDB .= " AND si.telemercadeo in ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				
				if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
					$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
					$queryDB .= " AND si.telemercadeo = '1'";
				
				$queryDB_count .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA")
					$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
					$queryDB_count .= " AND si.telemercadeo in ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
					$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				
				if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
					$queryDB_count .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
					$queryDB_count .= " AND si.telemercadeo = '1'";
				
				$queryDB_suma .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA")
					$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo in ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
					$queryDB_suma .= " AND si.telemercadeo in ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
					$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				
				if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
					$queryDB_suma .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
				
				if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
					$queryDB_suma .= " AND si.telemercadeo = '1'";
			}
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM"){
				$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
			}
			
			if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
				$fecha_inicialbd = $_REQUEST["fecha_inicialbd"];
				$fecha_inicialbm = $_REQUEST["fecha_inicialbm"];
				$fecha_inicialba = $_REQUEST["fecha_inicialba"];
				$queryDB .= " AND si.fecha_estudio >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
				$queryDB_count .= " AND si.fecha_estudio >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
				$queryDB_suma .= " AND si.fecha_estudio >= '".$fecha_inicialba."-".$fecha_inicialbm."-".$fecha_inicialbd."'";
			}
			
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
				$fecha_finalbd = $_REQUEST["fecha_finalbd"];
				$fecha_finalbm = $_REQUEST["fecha_finalbm"];
				$fecha_finalba = $_REQUEST["fecha_finalba"];
				$queryDB .= " AND si.fecha_estudio <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
				$queryDB_count .= " AND si.fecha_estudio <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
				$queryDB_suma .= " AND si.fecha_estudio <= '".$fecha_finalba."-".$fecha_finalbm."-".$fecha_finalbd."'";
			}
			
			if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"])
			{
				$fechades_inicialbd = $_REQUEST["fechades_inicialbd"];
				$fechades_inicialbm = $_REQUEST["fechades_inicialbm"];
				$fechades_inicialba = $_REQUEST["fechades_inicialba"];
				$queryDB .= " AND si.fecha_desembolso >= '".$fechades_inicialba."-".$fechades_inicialbm."-".$fechades_inicialbd."'";
				$queryDB_count .= " AND si.fecha_desembolso >= '".$fechades_inicialba."-".$fechades_inicialbm."-".$fechades_inicialbd."'";
				$queryDB_suma .= " AND si.fecha_desembolso >= '".$fechades_inicialba."-".$fechades_inicialbm."-".$fechades_inicialbd."'";
			}
			
			if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"]){
				$fechades_finalbd = $_REQUEST["fechades_finalbd"];
				$fechades_finalbm = $_REQUEST["fechades_finalbm"];
				$fechades_finalba = $_REQUEST["fechades_finalba"];
				$queryDB .= " AND si.fecha_desembolso <= '".$fechades_finalba."-".$fechades_finalbm."-".$fechades_finalbd."'";
				$queryDB_count .= " AND si.fecha_desembolso <= '".$fechades_finalba."-".$fechades_finalbm."-".$fechades_finalbd."'";
				$queryDB_suma .= " AND si.fecha_desembolso <= '".$fechades_finalba."-".$fechades_finalbm."-".$fechades_finalbd."'";
			}
			
			if ($_REQUEST["fechaprod_inicialbm"] && $_REQUEST["fechaprod_inicialba"]){
				$fechaprod_inicialbm = $_REQUEST["fechaprod_inicialbm"];
				$fechaprod_inicialba = $_REQUEST["fechaprod_inicialba"];
				$queryDB .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') >='".$fechaprod_inicialba."-".$fechaprod_inicialbm."'";
				$queryDB_count .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') >= '".$fechaprod_inicialba."-".$fechaprod_inicialbm."'";
				$queryDB_suma .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') >= '".$fechaprod_inicialba."-".$fechaprod_inicialbm."'";
			}
			
			if ($_REQUEST["fechaprod_finalbm"] && $_REQUEST["fechaprod_finalba"])
			{
				$fechaprod_finalbm = $_REQUEST["fechaprod_finalbm"];
				
				$fechaprod_finalba = $_REQUEST["fechaprod_finalba"];
				
				$queryDB .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') <= '".$fechaprod_finalba."-".$fechaprod_finalbm."'";
				
				$queryDB_count .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') <= '".$fechaprod_finalba."-".$fechaprod_finalbm."'";
				
				$queryDB_suma .= " AND FORMAT( si.fecha_cartera, 'yyyy-MM') <= '".$fechaprod_finalba."-".$fechaprod_finalbm."'";
			}
			
			// if ($_REQUEST["descripcion_busqueda"])
			// {
			// 	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
				
			// 	$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";

				
			// 	$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
				
			// 	$queryDB_suma .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
			// }

			if ($_REQUEST["cedula_busqueda"]) {
				$cedula_busqueda = $_REQUEST["cedula_busqueda"];				
				$queryDB .= " AND (si.cedula = '".$cedula_busqueda."')";				
				$queryDB_count .= " AND (si.cedula = '".$cedula_busqueda."')";				
				$queryDB_suma .= " AND (si.cedula = '".$cedula_busqueda."')";
			}


			if ($_REQUEST["libranza_busqueda"])
			{
				$libranza_busqueda = $_REQUEST["libranza_busqueda"];				
				$queryDB .= " AND (si.libranza = '".$libranza_busqueda."')";				
				$queryDB_count .= " AND (si.libranza = '".$libranza_busqueda."')";				
				$queryDB_suma .= " AND (si.libranza = '".$libranza_busqueda."')";
			}

			if ($_REQUEST["id_simulacion_buscar"])
			{
				$id_simulacion_buscar = $_REQUEST["id_simulacion_buscar"];
				
				$queryDB .= " AND (si.id_simulacion = '".$id_simulacion_buscar."')";
				
				$queryDB_count .= " AND (si.id_simulacion = '".$id_simulacion_buscar."')";
				
				$queryDB_suma .= " AND (si.id_simulacion = '".$id_simulacion_buscar."')";
			}

			if ($_REQUEST["tipo_comercial_buscar"])
			{
				$tipo_comercial_buscar = $_REQUEST["tipo_comercial_buscar"];
	
				if ($tipo_comercial_buscar=="FREELANCE")
				{
					$queryDB .= " AND (us.freelance=1 and us.outsourcing=0)";
				
					$queryDB_count .= " AND (us.freelance=1 and us.outsourcing=0)";
					
					$queryDB_suma .= " AND (us.freelance=1 and us.outsourcing=0)";
				}
				else if ($tipo_comercial_buscar=="OUTSOURCING")
				{
					$queryDB .= " AND (us.freelance=0 and us.outsourcing=1)";
				
					$queryDB_count .= " AND (us.freelance=0 and us.outsourcing=1)";
					
					$queryDB_suma .= " AND (us.freelance=0 and us.outsourcing=1)";	
				}
			
			}
			
			if ($_REQUEST["unidadnegociob"])
			{
				$unidadnegociob = $_REQUEST["unidadnegociob"];
				
				$queryDB .= " AND si.id_unidad_negocio = '".$unidadnegociob."'";
				
				$queryDB_count .= " AND si.id_unidad_negocio = '".$unidadnegociob."'";
				
				$queryDB_suma .= " AND si.id_unidad_negocio = '".$unidadnegociob."'";
			}
			
			if ($_REQUEST["sectorb"])
			{
				$sectorb = $_REQUEST["sectorb"];
				
				$queryDB .= " AND pa.sector = '".$sectorb."'";
				
				$queryDB_count .= " AND pa.sector = '".$sectorb."'";
				
				$queryDB_suma .= " AND pa.sector = '".$sectorb."'";
			}
			
			if ($_REQUEST["pagaduriab"])
			{
				$pagaduriab = $_REQUEST["pagaduriab"];
				
				$queryDB .= " AND si.pagaduria = '".$pagaduriab."'";
				
				$queryDB_count .= " AND si.pagaduria = '".$pagaduriab."'";
				
				$queryDB_suma .= " AND si.pagaduria = '".$pagaduriab."'";
			}
			
			if ($_REQUEST["tipo_comercialb"])
			{
				$tipo_comercialb = $_REQUEST["tipo_comercialb"];
				
				if ($tipo_comercialb == "PLANTA")
				{
					$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
					
					$queryDB_count .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
					
					$queryDB_suma .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
				}
				else
				{
					$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1')";
					
					$queryDB_count .= " AND (us.freelance = '1' OR us.outsourcing = '1')";
					
					$queryDB_suma .= " AND (us.freelance = '1' OR us.outsourcing = '1')";
				}
			}
			
			if ($_REQUEST["id_comercialb"])
			{
				$id_comercialb = $_REQUEST["id_comercialb"];
				
				$queryDB .= " AND si.id_comercial = '".$id_comercialb."'";
				
				$queryDB_count .= " AND si.id_comercial = '".$id_comercialb."'";
				
				$queryDB_suma .= " AND si.id_comercial = '".$id_comercialb."'";
			}else{
				$consultarCoordinacionUsuarios="SELECT * FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."'";
				$queryCoordinacionUsuarios=sqlsrv_query($link, $consultarCoordinacionUsuarios, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				//Prueba Query
				if ($queryCoordinacionUsuarios == false) {
					if( ($errors = sqlsrv_errors() ) != null) {
						foreach( $errors as $error ) {
						   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
							echo "code: ".$error[ 'code']."<br />";
						   echo "message: ".$error[ 'message']."<br />";
						   echo "1517";
							   }
							}
				   }
				  //Fin Prueba 

				if (sqlsrv_num_rows($queryCoordinacionUsuarios)>0)
				{
					
					$queryDB .= " AND si.id_comercial in (SELECT id_usuario_secundario FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."')";
					
					$queryDB_count .= " AND si.id_comercial in (SELECT id_usuario_secundario FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."')";
					
					$queryDB_suma .= " AND si.id_comercial in (SELECT id_usuario_secundario FROM coordinacion_usuarios WHERE id_usuario_principal='".$_SESSION["S_IDUSUARIO"]."')";
				}
			}
			
			if ($_REQUEST["estadob"])
			{
				$estadob = $_REQUEST["estadob"];
				
				$queryDB .= " AND si.estado = '".$estadob."' ";
				
				$queryDB_count .= " AND si.estado = '".$estadob."'";
				
				$queryDB_suma .= " AND si.estado = '".$estadob."'";
			}
			else
			{
				if (!$_REQUEST["descripcion_busqueda"] && !$_REQUEST["id_simulacion_buscar"])
				{
					$queryDB .= " AND si.estado NOT IN ('ANU', 'DSS','DST')";
					
					$queryDB_count .= " AND si.estado NOT IN ('ANU', 'DSS','DST')";
					
					$queryDB_suma .= " AND si.estado NOT IN ('ANU', 'DSS','DST')";
				}
			}
			
			if ($_REQUEST["decisionb"])
			{
				$decisionb = $_REQUEST["decisionb"];
				
				$queryDB .= " AND si.decision = '".$decisionb."' ";
				
				$queryDB_count .= " AND si.decision = '".$decisionb."' ";
				
				$queryDB_suma .= " AND si.decision = '".$decisionb."' ";
			}
			
			if ($_REQUEST["id_subestadob"])
			{
				$id_subestadob = $_REQUEST["id_subestadob"];
				
				$queryDB .= " AND si.id_subestado = '".$id_subestadob."'";
				
				$queryDB_count .= " AND si.id_subestado = '".$id_subestadob."'";
				
				$queryDB_suma .= " AND si.id_subestado = '".$id_subestadob."'";
			}
			
			if ($_REQUEST["visualizarb"])
			{
				$visualizarb = $_REQUEST["visualizarb"];
				
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				{
					$queryDB .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
					
					$queryDB_count .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
					
					$queryDB_suma .= " AND si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
				}
				
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				{
					$queryDB .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
					
					$queryDB_count .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
					
					$queryDB_suma .= " AND (si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' OR si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."')";
				}
			}else{
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				{
					$consultarOficinasUsuario="SELECT * FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."'";
					$queryOficinasUsuarios=sqlsrv_query($link, $consultarOficinasUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					//Prueba Query
					if ($queryOficinasUsuarios == false) {
						if( ($errors = sqlsrv_errors() ) != null) {
							foreach( $errors as $error ) {
							   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
								echo "code: ".$error[ 'code']."<br />";
							   echo "message: ".$error[ 'message']."<br />";
							   echo "1612";
								   }
								}
					   }
					  //Fin Prueba 

					if (sqlsrv_num_rows($queryOficinasUsuarios)>0)
					{
						$queryDB.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						$queryDB_count.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						$queryDB_suma.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						
					}
				}
			}

			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
				{
					$consultarOficinasUsuario="SELECT * FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."'";
					$queryOficinasUsuarios=sqlsrv_query($link, $consultarOficinasUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					//Prueba Query
					if ($queryOficinasUsuarios == false) {
						if( ($errors = sqlsrv_errors() ) != null) {
							foreach( $errors as $error ) {
							   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
								echo "code: ".$error[ 'code']."<br />";
							   echo "message: ".$error[ 'message']."<br />";
							   echo "1640";
								   }
								}
					   }
					  //Fin Prueba 


					if (sqlsrv_num_rows($queryOficinasUsuarios)>0)
					{
						$queryDB.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						$queryDB_count.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						$queryDB_suma.=" AND si.id_oficina in (select id_oficina FROM oficinas_usuarios WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')";
						
					}
				}
			
			if ($_REQUEST["calificacionb"])
			{
				$calificacionb = $_REQUEST["calificacionb"];
				
				$queryDB .= " AND si.calificacion = '".$calificacionb."'";
				
				$queryDB_count .= " AND si.calificacion = '".$calificacionb."'";
				
				$queryDB_suma .= " AND si.calificacion = '".$calificacionb."'";
			}
			
			if ($_REQUEST["statusb"])
			{
				$statusb = $_REQUEST["statusb"];
				
				$queryDB .= " AND si.status = '".$statusb."'";
				
				$queryDB_count .= " AND si.status = '".$statusb."'";
				
				$queryDB_suma .= " AND si.status = '".$statusb."'";
			}
			
			
			if ($_REQUEST["id_oficinab"])
			{
				$id_oficinab = $_REQUEST["id_oficinab"];
				
				$queryDB .= " AND si.id_oficina = '".$id_oficinab."'";
				
				$queryDB_count .= " AND si.id_oficina = '".$id_oficinab."'";
				
				$queryDB_suma .= " AND si.id_oficina = '".$id_oficinab."'";
			}
			
			if($_REQUEST["tipo_pagareb"]){
				$tipo_pagareb = $_REQUEST["tipo_pagareb"];

				$queryDB .= " AND si.formato_digital = '".$tipo_pagareb."'";

				$queryDB_count .= " AND si.formato_digital = '".$tipo_pagareb."'";
				
				$queryDB_suma .= " AND si.formato_digital = '".$tipo_pagareb."'";
			}
			$queryDB .= " order by si.fecha_radicado DESC, si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
			
			echo $queryDB;
			// $rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if ($rs == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
						echo "1699";
						

					}
				}
			}
			//Fin prueba
			
			echo $queryDB_suma;
			echo $queryDB_count;
			$rs_count = sqlsrv_query($link, $queryDB_count);
			
			$fila_count = sqlsrv_fetch_array($rs_count);
			
			$cuantos = $fila_count["c"];
			
			$rs_suma = sqlsrv_query($link, $queryDB_suma);
			
			$fila_suma = sqlsrv_fetch_array($rs_suma);
			
			$suma = $fila_suma["s"];
			
			$suma2 = $fila_suma["s2"];
			
			$suma3 = $fila_suma["s3"];

			// Inicios Pruebas de Query
			if ($rs_count == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
					   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
					   echo "message: ".$error[ 'message']."<br />";
					   echo "1732";
						   }
						}
			   }
			   if ($rs_suma == false) {
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
					   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
					   echo "message: ".$error[ 'message']."<br />";
					   echo "1742";
						   }
						}
			   }
			//Fin pruebas de Query

		}
		//Fin consultas



		if ($cuantos)
		{
			if ($cuantos > $x_en_x)
			{
				echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
				
				$i = 1;
				$final = 0;
				
				while ($final < $cuantos)
				{
					$link_page = $i - 1;
					$final = $i * $x_en_x;
					$inicio = $final - ($x_en_x - 1);
					
					if ($final > $cuantos)
					{
						$final = $cuantos;
					}
					
					if ($link_page != $_REQUEST["page"])
					{
						echo " <a href=\"simulaciones.php?fecha_inicialbd=".$fecha_inicialbd."&fecha_inicialbm=".$fecha_inicialbm."&fecha_inicialba=".$fecha_inicialba."&fecha_finalbd=".$fecha_finalbd."&fecha_finalbm=".$fecha_finalbm."&fecha_finalba=".$fecha_finalba."&fechades_inicialbd=".$fechades_inicialbd."&fechades_inicialbm=".$fechades_inicialbm."&fechades_inicialba=".$fechades_inicialba."&fechades_finalbd=".$fechades_finalbd."&fechades_finalbm=".$fechades_finalbm."&fechades_finalba=".$fechades_finalba."&fechaprod_inicialbm=".$fechaprod_inicialbm."&fechaprod_inicialba=".$fechaprod_inicialba."&fechaprod_finalbm=".$fechaprod_finalbm."&fechaprod_finalba=".$fechaprod_finalba."&cedula_busqueda=".$cedula_busqueda."&libranza_busqueda=".$libranza_busqueda."&unidadnegociob=".$unidadnegociob."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&tipo_comercialb=".$tipo_comercialb."&id_comercialb=".$id_comercialb."&estadob=".$estadob."&decisionb=".$decisionb."&id_subestadob=".$id_subestadob."&visualizarb=".$visualizarb."&calificacionb=".$calificacionb."&statusb=".$statusb."&id_oficinab=".$id_oficinab."&tipo_pagareb=".$tipo_pagareb."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
					}
					else
					{
						echo " ".$i;
					}
					
					$i++;
				}
				
				if ($_REQUEST["page"] != $link_page)
				{
					$siguiente_page = $_REQUEST["page"] + 1;
					
					echo " <a href=\"simulaciones.php?fecha_inicialbd=".$fecha_inicialbd."&fecha_inicialbm=".$fecha_inicialbm."&fecha_inicialba=".$fecha_inicialba."&fecha_finalbd=".$fecha_finalbd."&fecha_finalbm=".$fecha_finalbm."&fecha_finalba=".$fecha_finalba."&fechades_inicialbd=".$fechades_inicialbd."&fechades_inicialbm=".$fechades_inicialbm."&fechades_inicialba=".$fechades_inicialba."&fechades_finalbd=".$fechades_finalbd."&fechades_finalbm=".$fechades_finalbm."&fechades_finalba=".$fechades_finalba."&fechaprod_inicialbm=".$fechaprod_inicialbm."&fechaprod_inicialba=".$fechaprod_inicialba."&fechaprod_finalbm=".$fechaprod_finalbm."&fechaprod_finalba=".$fechaprod_finalba."&cedula_busqueda=".$cedula_busqueda."&libranza_busqueda=".$libranza_busqueda."&unidadnegociob=".$unidadnegociob."&sectorb=".$sectorb."&pagaduriab=".$pagaduriab."&tipo_comercialb=".$tipo_comercialb."&id_comercialb=".$id_comercialb."&estadob=".$estadob."&decisionb=".$decisionb."&id_subestadob=".$id_subestadob."&visualizarb=".$visualizarb."&calificacionb=".$calificacionb."&statusb=".$statusb."&id_oficinab=".$id_oficinab."&tipo_pagareb=".$tipo_pagareb."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
				}
				
				echo "</table><br>";
			}
	
		?>
		<form name="formato3" method="post" action="simulaciones.php">
			<input type="hidden" name="action" value="">
			<input type="hidden" name="fecha_inicialbd" value="<?php echo $fecha_inicialbd ?>">
			<input type="hidden" name="fecha_inicialbm" value="<?php echo $fecha_inicialbm ?>">
			<input type="hidden" name="fecha_inicialba" value="<?php echo $fecha_inicialba ?>">
			<input type="hidden" name="fecha_finalbd" value="<?php echo $fecha_finalbd ?>">
			<input type="hidden" name="fecha_finalbm" value="<?php echo $fecha_finalbm ?>">
			<input type="hidden" name="fecha_finalba" value="<?php echo $fecha_finalba ?>">
			<input type="hidden" name="fechades_inicialbd" value="<?php echo $fechades_inicialbd ?>">
			<input type="hidden" name="fechades_inicialbm" value="<?php echo $fechades_inicialbm ?>">
			<input type="hidden" name="fechades_inicialba" value="<?php echo $fechades_inicialba ?>">
			<input type="hidden" name="fechades_finalbd" value="<?php echo $fechades_finalbd ?>">
			<input type="hidden" name="fechades_finalbm" value="<?php echo $fechades_finalbm ?>">
			<input type="hidden" name="fechades_finalba" value="<?php echo $fechades_finalba ?>">
			<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $fechaprod_inicialbm ?>">
			<input type="hidden" name="fechaprod_inicialba" value="<?php echo $fechaprod_inicialba ?>">
			<input type="hidden" name="fechaprod_finalbm" value="<?php echo $fechaprod_finalbm ?>">
			<input type="hidden" name="fechaprod_finalba" value="<?php echo $fechaprod_finalba ?>">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
			<input type="hidden" name="unidadnegociob" value="<?php echo $unidadnegociob ?>">
			<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
			<input type="hidden" name="tipo_comercialb" value="<?php echo $tipo_comercialb ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $id_comercialb ?>">
			<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
			<input type="hidden" name="decisionb" value="<?php echo $decisionb ?>">
			<input type="hidden" name="id_subestadob" value="<?php echo $id_subestadob ?>">
			<input type="hidden" name="id_oficinab" value="<?php echo $id_oficinab ?>">
			<input type="hidden" name="tipo_pagareb" value="<?php echo $tipo_pagareb ?>">
			<input type="hidden" name="visualizarb" value="<?php echo $visualizarb ?>">
			<input type="hidden" name="calificacionb" value="<?php echo $calificacionb ?>">
			<input type="hidden" name="statusb" value="<?php echo $statusb ?>">
			<input type="hidden" name="id_simulacion_buscar" value="<?php echo $id_simulacion_buscar ?>">
			<input type="hidden" name="tipo_comercial_buscar" value="<?php echo $tipo_comercial_buscar ?>">
			<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
			<table border="0" cellspacing=1 cellpadding=2>
				<tr>
					<td colspan="19" align="right"><b><?php  if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><!--TOTAL VR DESEMBOLSO: $<?php echo number_format($suma, 0) ?> (<?php echo $cuantos ?> registros)<br>--><?php } ?>TOTAL VR DESEMBOLSO MENOS RETANQUEOS: $<?php echo number_format($suma2, 0) ?> (<?php echo $cuantos ?> registros)<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><br>TOTAL VR CR&Eacute;DITO: $<?php echo number_format($suma3, 0) ?> (<?php echo $cuantos ?> registros)<?php } ?></b></td>
				</tr>
			</table>
			<div id="divTablaSimulaciones">
			<table class="tab3">
				<tr>
					<?php  if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>F. Estudio</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Id. Simulación</th><?php } ?>
					<?php if ($_SESSION["FUNC_FDESEMBOLSO"] && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<th>F. Desemb</th>--><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Mes Prod</th><?php } ?>
					<th>C&eacute;dula</th>
					<th>Nombre</th>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Pagadur&iacute;a</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Comercial</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Tipo Comercial</th><?php } ?>
					<!--<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><th>Oficina</th><?php } ?>-->
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><th>Oficina</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><!--<th>Vr Desembolso</th>--><?php } ?>
					<th>Vr Desembolso Menos Retanqueos</th>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Vr Cr&eacute;dito</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Estado</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Decisión</th><?php } ?>
					<?php if ($_SESSION["FUNC_SUBESTADOS"] && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<th>Etapa</th>--><?php } ?>
					<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><th>Subestado</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Causal</th><?php } ?>
					<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA") { ?><th>Comprador</th><?php } ?>
					<th>Cobranza</th>
					<?php if ($_SESSION["FUNC_CALIFICACION"]) { ?><th>Calificación</th><?php } ?>
					<?php if (!(DeviceDetect() <> "desktop")) { ?><th><img src="../images/telemercadeo.png" title="Telemercadeo"></th><?php } ?>
					<?php if ($_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<th><img src="../images/preaprobado.png" title="Carta Preaprobado"></th>--><?php } ?>
					<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() == "desktop") || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "NEXA") { ?><th><img src="../images/solicitud.png" title="Solicitud Cr&eacute;dito"></th><?php } ?>
					<!--<th><img src="../images/sello.png" title="Visado"></th>-->
					<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><th><img src="../images/excel.png" title="Archivo Visado"></th><?php } ?>
		

						
					<?php //if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><!--<th><img src="../images/incorporado.png" title="Incorporado">Visado</th>--><?php //} ?>
					
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Visado</th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th>Incorporado</th><?php } ?>

					<th>Doc. Digital</th>

					<th>Vent. Cartera</th>

					<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") { ?><th><img src="../images/planpagos.png" title="Plan de Pagos"></th><?php } ?>
					<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><th><img src="../images/reqexcep.png" title="Ingresar Requerimiento/Excepción"></th><?php } ?>
					<?php if ($_SESSION["FUNC_ADJUNTOS"]) { ?><th><img src="../images/adjuntar.png" title="Adjuntos"></th><?php } ?>
					<?php if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop") { ?><th><img src="../images/ado.png" title="Lanzar sistema ADO" height="16"></th><?php } ?>
					<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
				</tr>
				<?php

					$j = 1;
					
					while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
					{
						 $verificarDocumentacionDigital="SELECT case when pagare_deceval is null then 'n' else 's' END as firma_digital FROM formulario_digital WHERE id_simulacion='".$fila["id_simulacion"]."'";
						$queryDocumentacionDigital=sqlsrv_query($link, $verificarDocumentacionDigital, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET) );
						
						//Prueba Query
					if ($queryDocumentacionDigital == false) {
						if( ($errors = sqlsrv_errors() ) != null) {
							foreach( $errors as $error ) {
							   echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
								echo "code: ".$error[ 'code']."<br />";
							   echo "message: ".$error[ 'message']."<br />";
								   }
								}
					   }
					  //Fin Prueba 

						$resDocumentacionDigital=sqlsrv_fetch_array($queryDocumentacionDigital);
						$tr_class = "";
						
						if (($j % 2) == 0)
						{
							$tr_class = " style='background-color:#F1F1F1;'";
						}
						
						$tipo_comercial = "PLANTA";
						
						if ($fila["freelance"])
							$tipo_comercial = "FREELANCE";
						
						if ($fila["outsourcing"])
							$tipo_comercial = "OUTSOURCING";
						
						switch ($fila["opcion_credito"])
						{
							case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
										$opcion_desembolso = $fila["opcion_desembolso_cli"];
										break;
							case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
										$opcion_desembolso = $fila["opcion_desembolso_ccc"];
										break;
							case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
										$opcion_desembolso = $fila["opcion_desembolso_cmp"];
										break;
							case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
										$opcion_desembolso = $fila["opcion_desembolso_cso"];
										break;
						}
						
						if ($fila["opcion_credito"] == "CLI")
							$fila["retanqueo_total"] = 0;
						
						switch ($fila["estado"])
						{
							case "ING":	$estado = "INGRESADO"; break;
							case "EST":	$estado = "EN ESTUDIO"; break;
							case "NEG":	$estado = "NEGADO"; break;
							case "DST":	$estado = "DESISTIDO"; break;
							case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
							case "DES":	$estado = "DESEMBOLSADO"; break;
							case "CAN":	$estado = "CANCELADO"; break;
							case "ANU":	$estado = "ANULADO"; break;
							case "PIN":  $estado = "PREINGRESADO"; break;
						}
						
						$calificacion = "";
						
						for ($i = 1; $i <= $fila["calificacion"]; $i++)
						{
							$calificacion .= "<img src=\"../images/estrella.png\">";
						}
						
						$info_incorporacion = "";
						
						if ($fila["incorporado"])
						{
							$info_incorporacion = "<img src=\"../images/incorporado.png\" title=\"Incorporado\">";
						}
						else
						{
							$queryDB_count1 = "select COUNT(*) as c from simulaciones_incorporacion where id_simulacion = '".$fila["id_simulacion"]."'";
							
							$rs_count1 = sqlsrv_query($link, $queryDB_count1);
						//Prueba Query
								if ($queryOficinasUsuarios == false) {
									if( ($errors = sqlsrv_errors() ) != null) {
										foreach( $errors as $error ) {
										echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
											echo "code: ".$error[ 'code']."<br />";
										echo "message: ".$error[ 'message']."<br />";
											}
											}
								}
					  //Fin Prueba 
							
							$fila_count1 = sqlsrv_fetch_array($rs_count1);
							
							$cuantos1 = $fila_count1["c"];
							
							if ($cuantos1)
							{
								$queryDB_suma1 = "select SUM(valor_cuota) as s from simulaciones_incorporacion where id_simulacion = '".$fila["id_simulacion"]."' AND estado = 'APROBADA'";
								
								$rs_suma1 = sqlsrv_query($link, $queryDB_suma1);

								//Prueba Query
								if ($rs_suma1  == false) {
									if( ($errors = sqlsrv_errors() ) != null) {
										foreach( $errors as $error ) {
										echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
											echo "code: ".$error[ 'code']."<br />";
										echo "message: ".$error[ 'message']."<br />";
											}
											}
								}
					           //Fin Prueba 
								
								$fila_suma1 = sqlsrv_fetch_array($rs_suma1);
								
								$suma1 = $fila_suma1["s"];
								
								if (!$suma1)
									$suma1 = 0;
									
								$info_incorporacion = number_format($suma1 / $opcion_cuota * 100, 2);
								
								$info_incorporacion .= "%";
							}
							else
							{
								if ($fila["estado"] == "EST" && $fila["cod_interno"])
								{
									if ($fila["cod_interno"] >= $cod_interno_subestado_aprobado_pdte_visado && $fila["cod_interno"] < 999)
										$info_incorporacion = "0.00%";
									else
										$info_incorporacion = "";
								}
								else
								{
									$info_incorporacion = "";
								}
							}
						}
						
							?>
							<tr <?php echo $tr_class ?>>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_estudio"] ?></td><?php } ?>
								<!--cambiar por enlace para modificar datos del cliente-->
								<?php 
								if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
								{
									if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td <?php if ($cantVencimiento>0){ ?>style="color:#FF0000" <?php }?>align="center"><a href="#" id="modalCambioDatos" name="<?php echo $fila["id_simulacion"];?>"><?php echo $fila["id_simulacion"] ?></a></td><?php }
								}else{
									if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td <?php if ($cantVencimiento>0){ ?>style="color:#FF0000" <?php }?>align="center"><?php echo $fila["id_simulacion"] ?></td><?php }
								}
								 ?>
								<?php 
								if ($_SESSION["FUNC_FDESEMBOLSO"] && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_desembolso"] ?></td>--><?php } ?>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center" style="white-space:nowrap;"><?php echo $fila["mes_prod"] ?></td><?php } ?>
								<td><a href="simulador.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&tipo_comercial_buscar=<?php echo $tipo_comercial_buscar?>&id_simulacion_buscar=<?php echo $id_simulacion_buscar?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&id_simulacion_buscar=<?php echo $id_simulacion_buscar ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo $fila["cedula"] ?></a></td>
								<td<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && $fila["empleado_manual"]) { ?> style="color:#FF0000"<?php } ?>><?php echo ($fila["nombre"]) ?></td>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td><?php echo ($fila["pagaduria"]) ?></td><?php } ?>
								<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo ($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td><?php } ?>
								<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo ($fila["tipo_comercial2"]) ?></td><?php } ?>
								
								
								<!--<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") { ?><td><?php echo ($fila["oficina"]) ?></td><?php } ?>-->
								<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?><td><?php echo ($fila["oficina"]) ?></td><?php } ?>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><!--<td align="right"><?php echo number_format($opcion_desembolso, 0) ?></td>--><?php } ?>
								<td align="right"><?php echo number_format($opcion_desembolso - $fila["retanqueo_total"], 0) ?></td>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td><?php } ?>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center" style="white-space:nowrap;"><?php echo $estado ?><?php if ($fila["estado"] == "DST" && $fila["fecha_desistimiento"]) { echo "<br>(".$fila["fecha_desistimiento"].", ".$fila["usuario_desistimiento"].")"; } ?></td><?php } ?>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center"><?php echo $fila["decision"] ?></td><?php } ?>
								<?php if ($_SESSION["FUNC_SUBESTADOS"] && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<td>&nbsp;<?php echo ($fila["nombre_etapa"]) ?>&nbsp;</td>--><?php } ?>
								<?php if ($_SESSION["FUNC_SUBESTADOS"]) { ?><td><a href="simulaciones_subestados.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&id_simulacion_buscar=<?php echo $id_simulacion_buscar ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">&nbsp;<?php if ($fila["id_subestado"]==0 || $fila["id_subestado"]==""){}else{echo ($fila["nombre_subestado"]);} ?>&nbsp;</a></td><?php } ?>
								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center"><?php echo ($fila["nombre_causal"]) ?></td><?php } ?>
								<?php if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA") { ?><td><?php echo ($fila["comprador"]) ?></td><?php } ?>
								<?php
								if ($fila["resp_gestion_cobranza"]=="")
								{
									$cobranza="NO APLICA";
								}else{
									$consultaCobranza="SELECT * FROM resp_gestion_cobros WHERE id_resp_cobros='".$fila["resp_gestion_cobranza"]."'";
									$queryCobranza=sqlsrv_query($con, $consultaCobranza);

									//Prueba Query
										if ($queryCobranza == false) {
											if( ($errors = sqlsrv_errors() ) != null) {
												foreach( $errors as $error ) {
												echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
													echo "code: ".$error[ 'code']."<br />";
												echo "message: ".$error[ 'message']."<br />";
													}
													}
										}
										//Fin Prueba 
									$resCobranza=sqlsrv_fetch_array($queryCobranza);
									$cobranza=$resCobranza." - ".$fila["detalle_resp_gestion_cobranza"];
								}
								
								?>
								<td><?php echo $cobranza ?></td>
								<?php if ($_SESSION["FUNC_CALIFICACION"]) { ?><td><?php echo $calificacion ?></td><?php } ?>
								<?php if (!(DeviceDetect() <> "desktop")) { ?><td align="center"><?php if ($fila["telemercadeo"]) { ?><img src="../images/telemercadeo.png" title="Telemercadeo"><?php } ?></td><?php } ?>
								<?php if ($_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))) { ?><!--<td align="center"><?php if ($fila["decision"] == $label_viable) { ?><a href="#" onClick="window.open('carta_preaprobado.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>', 'CARTAPREAPROBADO','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/preaprobado.png" title="Carta Preaprobado"></a><?php } ?></td>--><?php } ?>
								<?php 
								//JAIRO ZAPATA
								//10-11-2021 => SE AGREGA CAMPO id_unidad_negocio A LA CONSULTA PARA PODER ENVIARLA DENTRO DE LOS PARAMETROS EN EL ENLACE HACIA solicitud.php
								if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() == "desktop") || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "NEXA") { ?><td align="center"><a href="solicitud.php?id_unidad_negocio=<?php echo $fila["id_unidad_negocio"];?>&id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/solicitud.png" title="Solicitud Cr&eacute;dito"></a></td><?php } ?>
								<!--<td align="center"><?php if ($fila["valor_visado"] != 0) { ?><img src="../images/sello.png" title="Visado"><?php } ?></td>-->
								<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><td align="center"><a href="archivosim_visado.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>"><img src="../images/excel.png" title="Archivo Visado"></a></td><?php } ?>



								<?php //cambiar funcion INCORPORACIONES if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><!--<td align="center"><?php //echo $info_incorporacion ?></td>--><?php //} ?>


								
								<?php //funcion VISADO CHECK 
								if ($fila["visado"]=="s"){
									$imagen2="auditado.png";
								}else{
									$imagen2="novalidado.png";
								}
								if ($_SESSION["S_SUBTIPO"]=="ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"]=="COORD_VISADO")
								{
									?>
									<td align="center"><a href="#" id="btnVisado" name="<?php echo $fila["id_simulacion"];?>"><img src="../images/<?php echo $imagen2 ?>"></a></td>
									
									<?php	
								}
								else if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && ($_SESSION["S_SUBTIPO"]<>"ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"]<>"COOR_VISADO") && DeviceDetect() <> "desktop")) { 
									
									?>
								<td align="center"><a id="btnVisado2" name="<?php echo $fila["id_simulacion"];?>"><img src="../images/<?php echo $imagen2 ?>"></a></td>
								
								<?php } 
								
								if ($fila["incorporacion"]=="s"){
									$imagen2="auditado.png";
								}else{
									$imagen2="novalidado.png";
								}
								if ($_SESSION["S_SUBTIPO"]=="ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"]=="COORD_VISADO")
								{
									?>
									<td align="center">
										<a href="#" <?php if ($fila["incorporacion"]=="s"){?>class="tooltip"<?php }?> id="btnIncorporado" name="<?php echo $fila["id_simulacion"];?>">
											<?php if ($fila["incorporacion"]=="s"){?><span class="tooltiptext"><?php  echo $fila["fecha_incorporado"] ?></span><?php }?>
											<img src="../images/<?php echo $imagen2 ?>">
										</a>
									</td>
									
									<?php	
								}
								else if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && ($_SESSION["S_SUBTIPO"]<>"ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"]<>"COOR_VISADO") && DeviceDetect() <> "desktop")) { 
									
									?>
								<td align="center"><a id="btnIncorporado2" <?php if ($fila["incorporacion"]=="s"){?>class="tooltip"<?php }?> name="<?php echo $fila["id_simulacion"];?>">
								
								<?php if ($fila["incorporacion"]=="s"){?><span class="tooltiptext"><?php  echo $fila["fecha_incorporado"] ?></span><?php }?>
								
								<img src="../images/<?php echo $imagen2 ?>"></a></td>
								
								<?php } ?>

								<?php //funcion firma digital CHECK 
								
								if ($resDocumentacionDigital['firma_digital'] == 's' ){
									$imagen3="auditado.png";
								}else{
									$imagen3="novalidado.png";
								}
								?>

								<td align="center"><img src="../images/<?php echo $imagen3 ?>"></td>

								

								<?php //funcion VISADO CHECK 
								if ($_SESSION["S_SUBTIPO"]=="ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"]=="ANALISTA_VEN_CARTERA"){
									?>
									<td align="center">
									<?php
								
									if ($fila["estado_venta_cartera"]=="0"){
										$opciones='<option value="0" selected>NO RECIBIDO</option>
										<option value="1">RECIBIDO</option>
										<option value="2">VALIDADO</option>
										<option value="3">INCONSISTENTE</option>';
									}else if ($fila["estado_venta_cartera"]=="1"){
										$opciones='<option value="0" >NO RECIBIDO</option>
										<option value="1" selected>RECIBIDO</option>
										<option value="2">VALIDADO</option>
										<option value="3">INCONSISTENTE</option>';
									}else if ($fila["estado_venta_cartera"]=="2"){
										$opciones='<option value="0" >NO RECIBIDO</option>
										<option value="1" >RECIBIDO</option>
										<option value="2" selected>VALIDADO</option>
										<option value="3">INSONSISTENTE</option>';
									}else if ($fila["estado_venta_cartera"]=="3"){
										$opciones='<option value="0" >NO RECIBIDO</option>
										<option value="1" >RECIBIDO</option>
										<option value="2">VALIDADO</option>
										<option value="3" selected>INCONSISTENTE</option>';
									}
									?>
									<select id="opcion_est_venta_cartera" name="<?php echo $fila["id_simulacion"];?>" >
									<?php echo $opciones;
									?>
								</select>
									</td>
									<?php
								}else{
									if ($fila["estado_venta_cartera"]=="0"){
										$imagen4="NO RECIBIDO";
									}else if ($fila["estado_venta_cartera"]=="1"){
										$imagen4="RECIBIDO";
									}else if ($fila["estado_venta_cartera"]=="2"){
										$imagen4="VALIDADO";
									}else if ($fila["estado_venta_cartera"]=="3"){
										$imagen4="INCONSISTENTE";
									}
									?>
									<td align="center"><?php echo $imagen4 ?></td>
									<?php
								}
								
								?>
								

								<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") { ?><td align="center"><?php if ($fila["estado"] == "DES" || $fila["estado"] == "CAN") { ?><a href="planpagos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&back=simulaciones&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/planpagos.png" title="Plan de Pagos"></a><?php } ?></td><?php } ?>



								<?php if (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")) { ?><td align="center">
                                                                        <?php
                                    if( $fila['permite_requerimiento'] == 1 || $_SESSION["S_TIPO"] =='ADMINISTRADOR'){  
                                            if ($fila["estado"] != "ANU") { ?>
                                            <a href="reqexcep_crear.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">
                                            <img src="../images/reqexcep.png" title="Ingresar Requerimiento/Excepción"></a>                            
                                            <?php } else { echo "&nbsp;"; }                                    
                                    }else{
                                        echo '<a  onclick="alertReq()"><img src="../images/reqexcep.png" title="Ingresar Requerimiento/Excepción"></a>';
                                    }
                                    ?>
                                     </td>
                                <?php }
								
								if ($_SESSION["FUNC_ADJUNTOS"]) { ?><td align="center"><a href="adjuntos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&fecha_inicialbd=<?php echo $fecha_inicialbd ?>&fecha_inicialbm=<?php echo $fecha_inicialbm ?>&fecha_inicialba=<?php echo $fecha_inicialba ?>&fecha_finalbd=<?php echo $fecha_finalbd ?>&fecha_finalbm=<?php echo $fecha_finalbm ?>&fecha_finalba=<?php echo $fecha_finalba ?>&fechades_inicialbd=<?php echo $fechades_inicialbd ?>&fechades_inicialbm=<?php echo $fechades_inicialbm ?>&fechades_inicialba=<?php echo $fechades_inicialba ?>&fechades_finalbd=<?php echo $fechades_finalbd ?>&fechades_finalbm=<?php echo $fechades_finalbm ?>&fechades_finalba=<?php echo $fechades_finalba ?>&fechaprod_inicialbm=<?php echo $fechaprod_inicialbm ?>&fechaprod_inicialba=<?php echo $fechaprod_inicialba ?>&fechaprod_finalbm=<?php echo $fechaprod_finalbm ?>&fechaprod_finalba=<?php echo $fechaprod_finalba ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&unidadnegociob=<?php echo $unidadnegociob ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&tipo_comercialb=<?php echo $tipo_comercialb ?>&id_comercialb=<?php echo $id_comercialb ?>&estadob=<?php echo $estadob ?>&decisionb=<?php echo $decisionb ?>&id_subestadob=<?php echo $id_subestadob ?>&id_oficinab=<?php echo $id_oficinab ?>&tipo_pagareb=<?php echo $tipo_pagareb ?>&visualizarb=<?php echo $visualizarb ?>&calificacionb=<?php echo $calificacionb ?>&statusb=<?php echo $statusb ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adjuntar.png" title="Adjuntos"></a></td><?php } ?>
								<?php if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop") { ?><td align="center"><?php if ((($fila["decision"] == $label_viable && $fila["nombre_subestado"]) || ($fila["decision"] == $label_negado && $fila["id_subestado"] == $subestado_negociar_cartera)) && (!$fila["fecha_ado"] || ($fila["fecha_ado"] && $fila["score_ado"] == "CAPTURA ERRADA"))) { ?><a href="#" onClick="clickAndDisable(this); registro_ado('<?php echo $fila["id_simulacion"] ?>'); location.href='intent://adocolumbia.ado-tech.com//<?php echo $fila["id_simulacion"] ?>#Intent;scheme=http;package=com.mabel_tech.adodemo;end';"><img src="../images/ado.png" title="Lanzar sistema ADO" height="16"></a><?php } else { echo "&nbsp;"; } ?></td><?php } ?>
								<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && ($fila["estado"] == "ING" || $fila["estado"] == "EST")) { ?><input type="checkbox" name="chk<?php echo $fila["id_simulacion"] ?>" value="1"><?php } ?></td><?php } ?>
							</tr>
							<?php

						$j++;
					}
					
				?>
			</table>
				</div>
			<br>
			<?php

				if ($_SESSION["S_SOLOLECTURA"] != "1")
				{
				
			?>
					<p align="center"><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?>&nbsp;&nbsp;<input type="submit" value="Anular" onClick="document.formato3.action.value='anular'">&nbsp;&nbsp;<?php } ?></p>
			<?php

			}

			?>
	</form>
<?php

}
else
{
	if ($_REQUEST["buscar"]) { $mensaje = "No se encontraron registros"; }
	
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

?>
<div class="modal" id="modalCambioDatosSimulacion" data-animation="slideInOutLeft">
	<div class="modal-dialog">
    	<header class="modal-header">
        	Cambio Datos Cliente
        	<button type="button" class="close-modal" data-close>x</button>
        </header>
        
		<section class="modal-content">
			<div>
				<div class="box1 oran clearfix">
					<h2><b>INFORMACION GENERAL</b></h2>
					<table border="0" cellspacing=1 cellpadding=2 width="95%">
						<tr>
							<td>PRIMER NOMBRE</td>
							<td><input type="text" name="primerNombreCambioDatos" id="primerNombreCambioDatos" size="32" readonly></td>
							<td>SEGUNDO NOMBRE</td>
							<td><input type="text" name="segundoNombreCambioDatos" id="segundoNombreCambioDatos" size="32" readonly></td>
						</tr>
						<tr>
							<td>PRIMER APELLIDO</td>
							<td><input type="text" name="primerApellidoCambioDatos" id="primerApellidoCambioDatos" size="32" readonly></td>
							<td>SEGUNDO APELLIDO</td>
							<td><input type="text" name="segundoApellidoCambioDatos" id="segundoApellidoCambioDatos" size="32" readonly></td>
						</tr>
						<tr>
							<td>CEDULA</td>
							<td><input type="text" readonly name="cedula" name="cedulaCambioDatos" id="cedulaCambioDatos" size="32" readonly></td>
							<td>FECHA NACIMIENTO</td>
							<td><input type="text" name="fechaNacimientoCambioDatos" id="fechaNacimientoCambioDatos" size="32" readonly ></td>
						</tr>
						
						<tr>
							<td>PAGADURIA</td>
							<td><select name="pagaduriaCambioDatos" id="pagaduriaCambioDatos" style="width: 230px; pointer-events:none; user-select:none;" >
				                	 <?php

									$queryDB = "select nombre as pagaduria from pagadurias where estado = '1'";

									if ($_SESSION["S_SECTOR"])
									{
										$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
									}

									$queryDB .= " order by pagaduria";

									$rs1 =sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
									{
										echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
									}

									?>
								</select>
								<input type="text" style="display:none;" />
							</td>
							
							<td>MESES ANTES DE EDAD LIMITE</td>
							<td align="right"><input <?php if ($_SESSION["S_SUBTIPO"] == "COORD_CREDITO"){ ?> readonly <?php }else{ ?>  style='background-color:#EAF1DD;' <?php }?> type="text" name="mesesAntesLimiteCambioDatos" id="mesesAntesLimiteCambioDatos" size="32" ></td>
						</tr>
							<tr>
							<td style="vertical-align:top">INSTITUCION / ASOCIACION</td>
							<td><input type="text"  name="institucionCambioDatos" id="institucionCambioDatos" size="32" readonly></td>
							<td>DIRECCI&Oacute;N</td>
							<td><input type="text" name="direccionCambioDatos" id="direccionCambioDatos" maxlength="255" size="32" readonly></td>
						</tr>
						<tr>
							<td>TEL&Eacute;FONO</td>
							<td><input type="text" name="telefonoCambioDatos" id="telefonoCambioDatos" maxlength="50" size="32" readonly ></td>
							<td>CELULAR</td>
							<td><input type="text" name="celularCambioDatos" id="celularCambioDatos" maxlength="50" size="32" readonly></td>
						</tr>
						
						<tr>
							<td>E-MAIL</td>
							<td><input type="text"  name="emailCambioDatos" id="emailCambioDatos" maxlength="50" size="32" readonly></td>
							<td>NIVEL DE CONTRATACION</td>
							<td>
								<select name="nivelContratacionCambioDatos" id="nivelContratacionCambioDatos" style="width:160px; background-color:#EAF1DD;">
									<?php
									$queryDB = "SELECT nivel_Contratacion_Descripcion FROM nivel_contratacion";
									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){ ?>
										<option value="<?=$fila1["nivel_Contratacion_Descripcion"]?>"><?=$fila1["nivel_Contratacion_Descripcion"]?></option>
										<?php
									}
									?>
					            </select>
							</td>
						</tr>
						<tr>
							
							<td>COMERCIAL</td>
							<td>
								<select name="comercialCambioDatos" id="comercialCambioDatos" style="width:160px; background-color:#EAF1DD;  pointer-events: none; ">
				                </select>
							</td>
							<td width="100">TELEMERCADEO</td>
							<td>
								<input type="checkbox" id="telemercadeoCambioDatos" value="1">
							</td>
						</tr>
					</table>
					<input type="button" name="guardar" id="btnGuardarCambiosDatos" value="Guardar">
					<input type="hidden" id="idSimulacionCambioDatos">
				</div>
			</div>
        </section>
        <footer class="modal-footer">
        	Derechos reservados Kredit 2021
        </footer>
    </div>
</div>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../plugins/modal/modal.js"></script>

<script type="text/javascript">

	function alertReq(){
        Swal.fire({
            icon: 'warning',
            title: 'Accion no permitida',
            text: 'Durante este estado no se puede ingresar requerimientos',
        });
    }

	$('#btnGuardarCambiosDatos').click(function(e)
	{
		e.preventDefault();
		
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();


		//alert(JSON.stringify(asociarAnalistasPagadurias));
		var frmCambioDatos =  "exe=cambiarDatosCredito"
		+"&idSimulacionCambioDatos="+$("#idSimulacionCambioDatos").val()
		+"&primerNombreCambioDatos="+$("#primerNombreCambioDatos").val()
		+"&segundoNombreCambioDatos="+$("#segundoNombreCambioDatos").val()
		+"&primerApellidoCambioDatos="+$("#primerApellidoCambioDatos").val()
		+"&segundoApellidoCambioDatos="+$("#segundoApellidoCambioDatos").val()
		+"&celularCambioDatos="+$("#celularCambioDatos").val()
		+"&telefonoCambioDatos="+$("#telefonoCambioDatos").val()
		+"&cedulaCambioDatos="+$("#cedulaCambioDatos").val()
		+"&institucionCambioDatos="+$("#institucionCambioDatos").val()
		+"&mesesAntesLimiteCambioDatos="+$("#mesesAntesLimiteCambioDatos").val()
		+"&direccionCambioDatos="+$("#direccionCambioDatos").val()
		+"&emailCambioDatos="+$("#emailCambioDatos").val()
		+"&fechaNacimientoCambioDatos="+$("#fechaNacimientoCambioDatos").val()
		+"&pagaduriaCambioDatos="+$("#pagaduriaCambioDatos option:selected").val()
		+"&nivelContratacionCambioDatos="+$("#nivelContratacionCambioDatos option:selected").val()
		+"&comercialCambioDatos="+$("#comercialCambioDatos option:selected").val()
		+"&telemercadeoCambioDatos="+$("#telemercadeoCambioDatos").val();

		
		if ($("#telemercadeoCambioDatos").is(":checked"))
			{
				frmCambioDatos+="&telemercadeoChecked=1";
			}else{
				frmCambioDatos+="&telemercadeoChecked=0";
			}
			$.ajax({
				type: 'POST',
				url: '../controles/consultas_basicas.php',
				data: frmCambioDatos,

		
				success: function(data) {
					Swal.close();
					
					if (data==1)
					{
						alert("cambio realizado satisfactoriamente");
					}else if (data==2)
					{
						alert("Error al realizar el proceso");
					}else if (data==3)
					{
						alert("no existe el credito a modificar");
					}else if (data==3)
					{
						alert("no existe sesion abierta");
					}
						


					return false;
				}


			});		
	});


	$('#divTablaSimulaciones').on("change",'select', function(){
		var opcion = $(this).attr('name');
		var action = $(this).attr('id');
		if (action=="opcion_est_venta_cartera") {
			$.ajax({
				type: 'POST',
				url: '../controles/consultas_basicas.php',
				data: "exe=cambiarEstadoVentaCartera&idSimulacion="+opcion+"&opcionSeleccionada="+$(this).val(),
				cache: false,
				success: function(data) {
					//alert(data);

					if (data==1)
					{
						alert("Estado de credito cambiado");
					}else{
						alert("Error al guardar Estado de credito");
					}
					return false;
				}
			});
		}		
	});
	

	$("#divTablaSimulaciones").on('click','a',function(){
		var opcion=$(this).attr('name');
		var action=$(this).attr('id');
		if(action=="modalCambioDatos"){
		
			$.ajax({
			type: 'POST',
			url: '../controles/consultas_basicas.php',
			data: "exe=consultarInformacionCredito&idSimulacion="+opcion,
			cache: false,
			success: function(data) {
				//alert(data);
				var arrayJSON=JSON.parse(data);
				$("#primerNombreCambioDatos").val(arrayJSON.primer_nombre);
				$("#segundoNombreCambioDatos").val(arrayJSON.segundo_nombre);
				$("#primerApellidoCambioDatos").val(arrayJSON.primer_apellido);
				$("#segundoApellidoCambioDatos").val(arrayJSON.segundo_apellido);
				$("#celularCambioDatos").val(arrayJSON.celular);
				$("#telefonoCambioDatos").val(arrayJSON.telefono);
				$("#cedulaCambioDatos").val(arrayJSON.cedula);
				$("#institucionCambioDatos").val(arrayJSON.institucion);
				$("#mesesAntesLimiteCambioDatos").val(arrayJSON.meses_antes_65);
				$("#direccionCambioDatos").val(arrayJSON.direccion);
				$("#emailCambioDatos").val(arrayJSON.mail);
				$("#fechaNacimientoCambioDatos").val(arrayJSON.fecha_nacimiento);
				$("#pagaduriaCambioDatos").val(arrayJSON.pagaduria).change();
				$("#nivelContratacionCambioDatos").val(arrayJSON.nivel_contratacion).change();
				$("#comercialCambioDatos").html(arrayJSON.opciones_comerciales);
				if (arrayJSON.telemercadeo==1)
				{
					$("#telemercadeoCambioDatos").prop( "checked", true );
				}else{
					$("#telemercadeoCambioDatos").prop( "checked", false );
				}
				return false;
			}
		});
			$("#idSimulacionCambioDatos").val(opcion);
			$("#modalCambioDatosSimulacion").addClass('is-visible');
		}else if (action=="btnVisado")
		{
			Swal.fire({
			title: 'Desea cambiar el estado de Visado?',
			text: "Seleccione la opcion deseada",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'NO',
			confirmButtonText: 'SI'
			}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					type: 'POST',
					url: '../controles/consultas_basicas.php',
					data: "exe=modificarEstadoVisado&id_simulacion="+opcion,
					cache: false,
					success: function(data) {
						
						if (data==1)
						{
							Swal.fire(
							'Registro actualizado!',
							'',
							'success'
							)
						}else{
							Swal.fire(
							'Error al ejecutar proceso',
							'',
							'error'
							)
						}
						return false;
					}
				});
				
			}
			})
		}else if (action=="btnIncorporado")
		{
			Swal.fire({
			title: 'Desea cambiar el estado de Incorporacion?',
			text: "Seleccione la opcion deseada",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'NO',
			confirmButtonText: 'SI'
			}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					type: 'POST',
					url: '../controles/consultas_basicas.php',
					data: "exe=modificarEstadoIncorporacion&id_simulacion="+opcion,
					cache: false,
					success: function(data) {
						
						if (data==1)
						{
							Swal.fire(
							'Registro actualizado!',
							'',
							'success'
							)
						}else{
							Swal.fire(
							'Error al ejecutar proceso',
							'',
							'error'
							)
						}
						return false;
					}
				});
				
			}
			})
		}
	});
</script>

<?php include("bottom.php"); ?>