<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 
if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")){
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<style type="text/css">
	fieldset {
	    font-family: sans-serif;
	    border: 5px solid #1F497D;
	    background: #fff;
	    border-radius: 5px;
	    padding: 15px;
	}

	fieldset legend {
	    background: #1F497D;
	    color: #fff;
	    padding: 5px 10px ;
	    font-size: 14px;
	    border-radius: 5px;
	    box-shadow: 0 0 0 5px #ddd;
	    margin-left: 20px;
	}

	input[type="checkbox"]{
		margin-left: 5px;
		margin-right: 5px;
	}

	.tabla-unidades label{
		margin-top: 9px;
		margin-bottom: 7px;
		font-weight: bold;
		color: firebrick;
	}
</style>
<script language="JavaScript">

	function chequeo_forma() {
		var flag = 0;

		with (document.formato) {
			var arroba = email.value.indexOf("@");
			var substr = email.value.substring(arroba + 1, 100);
			var otra_arroba = substr.indexOf("@");
			var espacio = email.value.indexOf(" ");
			var punto = email.value.lastIndexOf(".");
			var ultimo = email.value.length-1;

			if ((nombre.value == "") || (apellido.value == "") || (email.value == "") || (tipo.selectedIndex == 0) || (login.value == "") || (password.value == "")<?php if ($_SESSION["FUNC_INDICADORES"]) { ?> || (meta_mes.value == "")<?php } ?>) {
				alert("Los campos marcados con asterisco(*) son obligatorios");
				return false;
			}
			if (freelance.checked == false && outsourcing.checked == false && apellido.value.trim().indexOf(" ") == -1) {
				alert("Debe digitar el segundo apellido");
				return false;
			}
			if (tipo.value == "OFICINA" && subtipo.selectedIndex == 0) {
				alert("Debe establecer el subtipo");
				return false;
			}
			if (cedula.value == "") {
				alert("Debe digitar la cedula recuerde que será el enlace a las demas aplicaciones.");
				return false;
			}
			if ((tipo.value == "COMERCIAL" || tipo.value == "DIRECTOROFICINA") && contrato.value == "") {
				alert("Debe establecer el tipo de contrato");
				return false;
			}
			if ((email.value != "") && (arroba < 1 || otra_arroba != -1 || punto - arroba < 2 || ultimo - punto > 3 || ultimo - punto < 2 || espacio != -1)) {
				alert("El email no es valido. Debe corregir la informacion.");
				email.value = "";
				email.focus();
				return false;
			}

			ReplaceComilla(nombre);
			ReplaceComilla(apellido);
			ReplaceComilla(email);
			ReplaceComilla(telefono);
		}
	}

	function valor_subtipos(x){return x.substring(0,x.indexOf('-'))}

	function texto_subtipos(x){return x.substring(x.indexOf('-')+1,x.length)}

	function Cargarsubtipos(tipo, objeto_subtipos) {
		var num_subtipos;
		var j, k = 1;

		num_subtipos = 200;

		objeto_subtipos.length = num_subtipos;

		<?php

		$padre_hija = "PHOFICINA = [";

		$padre_hija .= "\"ANALISTA_PROSPECCION-ANALISTA PROSPECCION\",";
		$padre_hija .= "\"ANALISTA_GEST_COM-ANALISTA GESTION COMERCIAL\",";
		$padre_hija .= "\"ANALISTA_REFERENCIA-ANALISTA REFERENCIACION\",";
		$padre_hija .= "\"ANALISTA_VALIDACION-ANALISTA VALIDACION\",";
		$padre_hija .= "\"ANALISTA_CREDITO-ANALISTA CREDITO\",";

		if ($_SESSION["FUNC_FULLSYSTEM"]) {
			$padre_hija .= "\"ANALISTA_VISADO-ANALISTA VISADO\",";
			$padre_hija .= "\"ANALISTA_TESORERIA-ANALISTA TESORERIA\",";
			$padre_hija .= "\"ANALISTA_JURIDICO-ANALISTA JURIDICO\",";
			$padre_hija .= "\"ANALISTA_CARTERA-ANALISTA CARTERA\",";
			$padre_hija .= "\"ANALISTA_VEN_CARTERA-ANALISTA VENTA CARTERA\",";
			$padre_hija .= "\"ANALISTA_BD-ANALISTA BASE DE DATOS\",";
		}

		$padre_hija .= "\"AUXILIAR_OFICINA-AUXILIAR OFICINA\",";
		$padre_hija .= "\"COORD_PROSPECCION-COORDINADOR PROSPECCION\",";
		$padre_hija .= "\"COORD_VISADO-COORDINADOR VISADO\",";
		$padre_hija .= "\"COORD_CREDITO-COORDINADOR CREDITO\",";

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;

		$padre_hija = "PHGERENTECOMERCIAL = [";

		$padre_hija .= "\"PLANTA-PLANTA\",";
		$padre_hija .= "\"PLANTA_EXTERNOS-PLANTA_EXTERNOS\",";
		$padre_hija .= "\"PLANTA_TELEMERCADEO-PLANTA_TELEMERCADEO\",";
		$padre_hija .= "\"EXTERNOS-EXTERNOS\",";
		$padre_hija .= "\"TELEMERCADEO-TELEMERCADEO\",";

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;

		$padre_hija = "PHDIRECTOROFICINA = [";

		$padre_hija .= "\"PLANTA-PLANTA\",";
		$padre_hija .= "\"PLANTA_EXTERNOS-PLANTA_EXTERNOS\",";
		$padre_hija .= "\"PLANTA_TELEMERCADEO-PLANTA_TELEMERCADEO\",";
		$padre_hija .= "\"EXTERNOS-EXTERNOS\",";
		$padre_hija .= "\"TELEMERCADEO-TELEMERCADEO\",";

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;

		$padre_hija = "PHPROSPECCION = [";

		$padre_hija .= "\"PLANTA-PLANTA\",";
		$padre_hija .= "\"PLANTA_EXTERNOS-PLANTA_EXTERNOS\",";
		$padre_hija .= "\"PLANTA_TELEMERCADEO-PLANTA_TELEMERCADEO\",";
		$padre_hija .= "\"EXTERNOS-EXTERNOS\",";
		$padre_hija .= "\"TELEMERCADEO-TELEMERCADEO\",";

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;

		$padre_hija = "PHPOUTSOURCING = [";
		$padre_hija .= "\"NEXA-NEXA\",";
		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;

		?>
		switch(tipo) {
		case 'OFICINA':
			num_subtipos = PHOFICINA.length;
			for(j = 0; j < num_subtipos; j++) {
				objeto_subtipos.options[k].value = valor_subtipos(PHOFICINA[j]);
				objeto_subtipos.options[k].text = texto_subtipos(PHOFICINA[j]);
				k++;
			}
			break;

		case 'GERENTECOMERCIAL':
			num_subtipos = PHGERENTECOMERCIAL.length;
			for(j = 0; j < num_subtipos; j++) {
				objeto_subtipos.options[k].value = valor_subtipos(PHGERENTECOMERCIAL[j]);
				objeto_subtipos.options[k].text = texto_subtipos(PHGERENTECOMERCIAL[j]);
				k++;
			}
			break;

		case 'DIRECTOROFICINA':
			num_subtipos = PHDIRECTOROFICINA.length;
			for(j = 0; j < num_subtipos; j++) {
				objeto_subtipos.options[k].value = valor_subtipos(PHDIRECTOROFICINA[j]);
				objeto_subtipos.options[k].text = texto_subtipos(PHDIRECTOROFICINA[j]);
				k++;
			}
			break;

		case 'PROSPECCION':
			num_subtipos = PHPROSPECCION.length;
			for(j = 0; j < num_subtipos; j++) {
				objeto_subtipos.options[k].value = valor_subtipos(PHPROSPECCION[j]);
				objeto_subtipos.options[k].text = texto_subtipos(PHPROSPECCION[j]);
				k++;
			}
			break;

		case 'OUTSOURCING':
			num_subtipos = PHPOUTSOURCING.length;
			for(j = 0 ; j < num_subtipos; j++){
				objeto_subtipos.options[k].value = valor_subtipos(PHPOUTSOURCING[j]);
				objeto_subtipos.options[k].text = texto_subtipos(PHPOUTSOURCING[j]);
				k++;
			}
			break;

		default:
			num_subtipos = 1;
			k=0;
		}

		objeto_subtipos.selectedIndex = 0;
		objeto_subtipos.length = num_subtipos;

		return true;
	}
//-->
</script>	
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo"><center><b>Crear Usuario</b><br><br></center></td>
	</tr>
</table>
<form name=formato method=post action="usuarios_crear2.php" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td align="right">* Nombre </td><td><input type="text" name="nombre" size="25" maxlength="50"></td>
							<td width="20">&nbsp;</td>
							<td align="right">* Apellido </td><td><input type="text" name="apellido" size="25" maxlength="50"></td>
						</tr>
						<tr>
							<td align="right">* E-Mail</td><td><input type="text" name="email" size="25" maxlength="50"></td>
							<td width="20">&nbsp;</td>
							<td align="right">Tel&eacute;fono</td><td><input type="text" name="telefono" size="25" maxlength="50"></td>
						</tr>
						<tr>
							<td align="right">* Usuario</td><td><input type="text" name="login" size="25" maxlength="20" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
							<td width="20">&nbsp;</td>
							<td align="right">* Contrase&ntilde;a</td><td><input type="text" name="password" size="25" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
						</tr>
						<tr>
							<td align="right">* Tipo</td><td>
								<select name="tipo" onChange="if (this.value == 'OFICINA' || this.value == 'GERENTECOMERCIAL' || this.value == 'DIRECTOROFICINA' || this.value == 'PROSPECCION' || this.value == 'OUTSOURCING') { document.formato.subtipo.disabled = false; Cargarsubtipos(this.value, document.formato.subtipo); } else { document.formato.subtipo.selectedIndex = 0; document.formato.subtipo.disabled = true; }" style="width:196px">
									<option value=""></option>
									<?php
									if ($_SESSION["S_TIPO"] == "ADMINISTRADOR"){ ?>
										<option value="ADMINISTRADOR">ADMINISTRADOR</option>
										<?php
									}
									?>
									<option value="OFICINA">OFICINA</option>
									<option value="GERENTECOMERCIAL">GERENTE REGIONAL</option>
									<option value="DIRECTOROFICINA">DIRECTOR OFICINA</option>
									<option value="COMERCIAL">COMERCIAL</option>
									<option value="PROSPECCION">PROSPECCION</option>
									<option value="EXTERNOS">EXTERNOS</option>

									<?php
									if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["FUNC_MUESTRACAMPOS1"]){ ?>
										<option value="TESORERIA">TESORERIA</option>
										<option value="CARTERA">DIRECTOR DE CARTERA</option>
										<option value="OPERACIONES">DIRECTOR DE OPERACIONES</option>
										<option value="CONTABILIDAD">CONTABILIDAD</option>
										<option value="OUTSOURCING">OUTSOURCING</option>
										<?php
									}
									?>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td align="right">Subtipo</td><td>
								<select name="subtipo" style="width:196px" disabled>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Sector</td><td>
								<select name="sector" style="width:196px">
									<option value=""></option>
									<option value="PUBLICO">PUBLICO</option>
									<option value="PRIVADO">PRIVADO</option>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td align="right">* Cargo </td><td><input type="text" name="cargo" size="25" maxlength="50"></td>
						</tr>
						<tr>
							<td align="right">C&eacute;dula</td><td><input type="text" name="cedula" size="25" maxlength="20" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
							<td width="20">&nbsp;</td>
							<td align="right">Contrato</td><td>
								<select name="contrato" style="width:196px">
									<option value=""></option>
									<option value="DIRECTO">DIRECTO</option>
									<option value="BOLSA DE EMPLEO">BOLSA DE EMPLEO</option>
								</select>
							</td>
						</tr>
						<tr>
							<?php
							if ($_SESSION["FUNC_MAXCONSDIARIAS"]){ ?>
								<td align="right">Max Consultas Diarias</td><td><input type="text" name="maxconsdiarias" size="25" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
								<?php
							}

							if ($_SESSION["FUNC_INDICADORES"]){
								if ($_SESSION["FUNC_MAXCONSDIARIAS"]){ ?>
									<td width="20">&nbsp;</td>
									<?php
								}
								?>
								<td align="right">* Meta del mes</td><td><input type="text" name="meta_mes" size="25" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" value="0"></td>
								<?php
							} else{ ?>
								<input type="hidden" name="meta_mes" value="0">
								<?php
							}
							?>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<fieldset>
							<legend><b>Permisos Especiales</b></legend>
						<table border="0" class="tabla-especiales" cellspacing=1 cellpadding=2>
							<tr>
								<td align="right">Freelance</td>
									<td><input type="checkbox" name="freelance" value="1" onClick="document.formato.outsourcing.checked = false"></td>
								<td></td>
								<td align="right">Outsourcing</td><td><input type="checkbox" name="outsourcing" value="1" onClick="document.formato.freelance.checked = false"></td>
							</tr>
							<!--<tr>
								<td align="right">Coordinador</td><td><input type="checkbox" name="coordinador" value="1" ></td>
								<td></td>
							    <td align="right">Jefe Comercial</td><td><input type="checkbox" name="jefe_comercial" value="1" ></td>
							</tr>-->
							<tr>
								<?php
								if ($_SESSION["S_TIPO"] == "ADMINISTRADOR"){ ?>
									<td align="right">Administrador - S&oacute;lo lectura</td><td><input type="checkbox" name="solo_lectura" value="1"></td>
									<td></td>
									<?php
								} else { ?>
									<input type="hidden" name="solo_lectura" value="0">
									<?php
								}

								?>
								<td align="right">Activo</td><td><input type="checkbox" name="estado" value="1" checked></td>
							</tr>
							<tr>
								<td align="right">Mostrar en Agenda</td><td><input type="checkbox" name="agenda" value="1" checked></td>
								<td></td>
								<td align="right">BI(inteligencia de negocios)</td><td><input type="checkbox" name="bi" value="1"></td>
							</tr>
							<tr>
								<td align="right">Preprospección</td><td><input type="checkbox" name="preprospeccion" value="1"></td>
								<td></td>
								<td align="right">Recuperar Firma</td><td><input type="checkbox" name="solicitar_firma" value="1"></td>
							</tr>
							<tr>
								<td align="right">Revision de garantias</td><td><input type="checkbox" name="revision_garantias" value="1"></td>
								<td></td>
								<td align="right">Habilitar Prospección</td>
								<td><input type="checkbox" name="habilitar_prospeccion" value="1"></td>
							</tr>
							<tr>
								<td align="right">Descargar Reportes</td>
								<td><input type="checkbox" name="descargar_reportes" value="1"></td>
								<td></td>
								<td align="right">Anular Firma DIgital</td>
								<td><input type="checkbox" name="anular_firma_digital" value="1"></td>
							</tr>
							<tr>
								<td align="right">Causales No Recaudo</td>
								<td><input type="checkbox" name="causales_no_recaudo" value="1"></td>
								<td></td>
								<td align="right">Reporte Cartera</td>
								<td><input type="checkbox" name="reporte_cartera" value="1"></td>
								<tr>
								<td align="right">Centarles Judicial</td>
								<td><input type="checkbox" name="centrales_judicial" value="1"></td>
								<td></td>
								<td align="right"></td>
								<td></td>
							</tr>
							</tr>
						</table>
					</fieldset>
				</div>
			</td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<fieldset>
  						<legend><b>Unidades de Negocio Asociadas</b></legend>
						<table border="0" class="tabla-unidades" cellspacing=0 cellpadding=0>
							<tr>
								<?php
								$queryDB = "SELECT a.id_unidad, a.nombre, a.id_empresa, b.nombre_empresa FROM unidades_negocio a LEFT JOIN empresas b ON a.id_empresa = b.id_empresa ORDER BY a.id_empresa ASC, a.id_unidad DESC";
								$rs1 = sqlsrv_query($link, $queryDB);
								
								$i = 1;
								$labelUnd = '';
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){

									if($labelUnd != $fila1["nombre_empresa"]){
										echo "</tr><tr><td colspan='5'><label>".$fila1["nombre_empresa"]."</label><td></tr><tr>";
										$labelUnd = $fila1["nombre_empresa"];
									} ?>

									<td><input type="checkbox" name="id_unidad<?php echo $fila1["id_unidad"] ?>" value="1"></td><td><?php echo utf8_decode($fila1["nombre"]) ?>&nbsp;&nbsp;&nbsp;</td>

									<?php

									if ($i % 5 == 0){
										echo "</tr><tr>";
									}

									$i++;
								}
								?>
							</tr>
						</table>
					</fieldset>
				</div>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<fieldset>
							<legend><b>Oficinas asignadas</b></legend>
							<table border="0" class="tabla-oficinass" cellspacing=0 cellpadding=0>
								<tr>

								<?php
								$queryDB = "SELECT o.id_oficina, o.nombre FROM oficinas o ORDER BY o.id_oficina ASC;";
								$rs1 = sqlsrv_query($link, $queryDB);
								$i = 1;
								$labelUnd = '';
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){ ?>
									<td>
										<input type="checkbox" name="id_oficina<?php echo $fila1["id_oficina"] ?>" value="1">
									</td>
									<td>
										<?php echo ($fila1["nombre"]) ?>&nbsp;&nbsp;&nbsp;
									</td>
									<?php

									if ($i % 5 == 0){
										echo "</tr><tr>";
									}

									$i++;
								}
								?>

								</tr>
							</table>
					</fieldset>
				</div>
			</td>
		</tr>		
	</table>
	
	<p align="center"><input type="submit" value="Ingresar"></p>
</form>
<?php include("bottom.php"); ?>