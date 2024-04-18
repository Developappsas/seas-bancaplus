<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 
if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" )) {
	exit;
}

$link = conectar();

$rs = sqlsrv_query($link, "select * from usuarios where id_usuario = '".$_REQUEST["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs))
{
	exit;
}

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

			if ((nombre.value == "") || (apellido.value == "") || (email.value == "") || (tipo.selectedIndex == 0) || (login.value == "")<?php if ($_SESSION["FUNC_INDICADORES"]) { ?> || (meta_mes.value == "")<?php } ?>) {
				alert("Los campos marcados con asterisco(*) son obligatorios");
				return false;
			}
			if (freelance.checked == false && outsourcing.checked == false && apellido.value.trim().indexOf(" ") == -1) {
				alert("Debe digitar el segundo apellido");
				return false;
			}
			if (tipo.value == "OFICINA" && subtipo.selectedIndex == 0) {
				alert("Debe establecer el Subtipo");
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
		<td class="titulo"><center><b>Detalle Usuario</b><br><br></center></td>
	</tr>
</table>
<form name=formato method=post action="usuarios_actualizar2.php" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td align="right">* Nombre</td><td><input type="text" name="nombre" size="25" maxlength="50" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?>"></td>
							<td width="20">&nbsp;</td>
							<td align="right">* Apellido</td><td><input type="text" name="apellido" size="25" maxlength="50" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["apellido"])) ?>"></td>
						</tr>
						<tr>
							<td align="right">* E-Mail</td><td><input type="text" name="email" size="25" maxlength="50" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["email"])) ?>"></td>
							<td width="20">&nbsp;</td>
							<td align="right">Tel&eacute;fono</td><td><input type="text" name="telefono" size="25" maxlength="50" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["telefono"])) ?>"></td>
						</tr>
						<tr>
							<td align="right">* Usuario</td><td><input type="text" name="login" size="25" maxlength="20" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}" value="<?php echo utf8_decode($fila["login"]) ?>"></td>
							<td width="20">&nbsp;</td>
							<td align="right">Contrase&ntilde;a</td><td><input type="text" name="password" size="25" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
						</tr>
						<tr>
							<td align="right">* Tipo</td><td>
								<select name="tipo" onChange="if (this.value == 'OFICINA' || this.value == 'GERENTECOMERCIAL' || this.value == 'DIRECTOROFICINA' || this.value == 'PROSPECCION' || this.value == 'OUTSOURCING') { document.formato.subtipo.disabled = false; Cargarsubtipos(this.value, document.formato.subtipo); } else { document.formato.subtipo.selectedIndex = 0; document.formato.subtipo.disabled = true; }" style="width:196px">
									<option value=""></option>
									<?php

									switch($fila["tipo"]) {
										case	"ADMINISTRADOR":	$selected_adm = " selected"; break;
										case	"OFICINA":			$selected_ofc = " selected"; break;
										case	"GERENTECOMERCIAL":	$selected_gco = " selected"; break;
										case	"DIRECTOROFICINA":	$selected_dof = " selected"; break;
										case	"COMERCIAL":		$selected_com = " selected"; break;
										case	"PROSPECCION":		$selected_pro = " selected"; break;
										case	"TESORERIA":		$selected_tes = " selected"; break;
										case	"CARTERA":			$selected_car = " selected"; break;
										case	"OPERACIONES":		$selected_ope = " selected"; break;
										case	"CONTABILIDAD":		$selected_con = " selected"; break;
										case	"OUTSOURCING":		$selected_out = " selected"; break;
									}

									if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") {
										?>
										<option value="ADMINISTRADOR"<?php echo $selected_adm ?>>ADMINISTRADOR</option>
										<?php
									}

									?>
									<option value="OFICINA"<?php echo $selected_ofc ?>>OFICINA</option>
									<option value="GERENTECOMERCIAL"<?php echo $selected_gco ?>>GERENTE REGIONAL</option>
									<option value="DIRECTOROFICINA"<?php echo $selected_dof ?>>DIRECTOR OFICINA</option>
									<option value="COMERCIAL"<?php echo $selected_com ?>>COMERCIAL</option>
									<option value="PROSPECCION"<?php echo $selected_pro ?>>PROSPECCION</option>
									<?php

									if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["FUNC_MUESTRACAMPOS1"]) {

										?>
										<option value="TESORERIA"<?php echo $selected_tes ?>>TESORERIA</option>
										<option value="CARTERA"<?php echo $selected_car ?>>DIRECTOR DE CARTERA</option>
										<option value="OPERACIONES"<?php echo $selected_ope ?>>DIRECTOR DE OPERACIONES</option>
										<option value="CONTABILIDAD"<?php echo $selected_con ?>>CONTABILIDAD</option>
										<option value="OUTSOURCING" <?php echo $selected_out ?>>OUTSOURCING</option>
										<?php

									}

									?>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td align="right">Subtipo</td><td>
								<select name="subtipo"<?php if ($fila["tipo"] != "OFICINA" && $fila["tipo"] != "GERENTECOMERCIAL" && $fila["tipo"] != "DIRECTOROFICINA" && $fila["tipo"] != "PROSPECCION" && $fila['tipo'] != "OUTSOURCING") { ?> disabled<?php } ?> style="width:196px">
									<option value=""></option>
									<?php

									switch($fila["subtipo"])
									{
										case	"ANALISTA_PROSPECCION":	$selected_apr = " selected"; break;
										case	"ANALISTA_GEST_COM":	$selected_agc = " selected"; break;
										case	"ANALISTA_REFERENCIA":	$selected_are = " selected"; break;
										case	"ANALISTA_VALIDACION":	$selected_ava = " selected"; break;
										case	"ANALISTA_CREDITO":		$selected_acr = " selected"; break;
										case	"ANALISTA_VISADO":		$selected_avi = " selected"; break;
										case	"ANALISTA_TESORERIA":	$selected_ate = " selected"; break;
										case	"ANALISTA_JURIDICO":	$selected_aju = " selected"; break;
										case	"ANALISTA_CARTERA":		$selected_aca = " selected"; break;
										case	"ANALISTA_VEN_CARTERA":	$selected_avc = " selected"; break;
										case	"ANALISTA_BD":			$selected_abd = " selected"; break;
										case	"AUXILIAR_OFICINA":		$selected_aof = " selected"; break;
										case	"COORD_PROSPECCION":	$selected_cpr = " selected"; break;
										case	"COORD_VISADO":			$selected_cvi = " selected"; break;
										case	"COORD_CREDITO":		$selected_ccr = " selected"; break;
										case	"PLANTA":				$selected_pla = " selected"; break;
										case	"PLANTA_EXTERNOS":		$selected_pex = " selected"; break;
										case	"PLANTA_TELEMERCADEO":	$selected_pte = " selected"; break;
										case	"EXTERNOS":				$selected_ext = " selected"; break;
										case	"TELEMERCADEO":			$selected_tel = " selected"; break;
										case    "NEXA":                 $selected_out  = " selected"; break;

									}

									if ($fila["tipo"] == "OFICINA") {
										?>
										<option value="ANALISTA_PROSPECCION"<?php echo $selected_apr ?>>ANALISTA PROSPECCION</option>
										<option value="ANALISTA_GEST_COM"<?php echo $selected_agc ?>>ANALISTA GESTION COMERCIAL</option>
										<option value="ANALISTA_REFERENCIA"<?php echo $selected_are ?>>ANALISTA REFERENCIACION</option>
										<option value="ANALISTA_VALIDACION"<?php echo $selected_ava ?>>ANALISTA VALIDACION</option>
										<option value="ANALISTA_CREDITO"<?php echo $selected_acr ?>>ANALISTA CREDITO</option>
										<?php

										if ($_SESSION["FUNC_FULLSYSTEM"]) {

											?>
											<option value="ANALISTA_VISADO"<?php echo $selected_avi ?>>ANALISTA VISADO</option>
											<option value="ANALISTA_TESORERIA"<?php echo $selected_ate ?>>ANALISTA TESORERIA</option>
											<option value="ANALISTA_JURIDICO"<?php echo $selected_aju ?>>ANALISTA JURIDICO</option>
											<option value="ANALISTA_CARTERA"<?php echo $selected_aca ?>>ANALISTA CARTERA</option>
											<option value="ANALISTA_VEN_CARTERA"<?php echo $selected_avc ?>>ANALISTA VENTA CARTERA</option>
											<option value="ANALISTA_BD"<?php echo $selected_abd ?>>ANALISTA BASE DE DATOS</option>
											<?php

										}

										?>
										<option value="AUXILIAR_OFICINA"<?php echo $selected_aof ?>>AUXILIAR OFICINA</option>
										<option value="COORD_PROSPECCION"<?php echo $selected_cpr ?>>COORDINADOR PROSPECCION</option>
										<option value="COORD_VISADO"<?php echo $selected_cvi ?>>COORDINADOR VISADO</option>
										<option value="COORD_CREDITO"<?php echo $selected_ccr ?>>COORDINADOR CREDITO</option>
										<?php

									}

									if ($fila["tipo"] == "GERENTECOMERCIAL" || $fila["tipo"] == "DIRECTOROFICINA" || $fila["tipo"] == "PROSPECCION") {

										?>
										<option value="PLANTA"<?php echo $selected_pla ?>>PLANTA</option>
										<option value="PLANTA_EXTERNOS"<?php echo $selected_pex ?>>PLANTA_EXTERNOS</option>
										<option value="PLANTA_TELEMERCADEO"<?php echo $selected_pte ?>>PLANTA_TELEMERCADEO</option>
										<option value="EXTERNOS"<?php echo $selected_ext ?>>EXTERNOS</option>
										<option value="TELEMERCADEO"<?php echo $selected_tel ?>>TELEMERCADEO</option>
										<?php

									}

									if ($fila["tipo"] == "OUTSOURCING") {
										?>
										<option value="NEXA"<?php echo $selected_out ?>>NEXA</option>
										<?php

									}

									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Sector</td><td>
								<select name="sector" style="width:196px">
									<option value=""></option>
									<?php

									switch($fila["sector"])
									{
										case	"PUBLICO":	$selected_pub = " selected"; break;
										case	"PRIVADO":	$selected_pri = " selected"; break;
									}

									?>
									<option value="PUBLICO"<?php echo $selected_pub ?>>PUBLICO</option>
									<option value="PRIVADO"<?php echo $selected_pri ?>>PRIVADO</option>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td align="right">* Cargo </td><td><input type="text" value="<?php echo $fila["cargo"]; ?>" name="cargo" size="25" maxlength="50"></td>
						</tr>
						<tr>
							<td align="right">C&eacute;dula</td><td><input type="text" name="cedula" size="25" maxlength="20" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" value="<?php echo $fila["cedula"] ?>"></td>
							<td width="20">&nbsp;</td>
							<td align="right">Contrato</td><td>
								<select name="contrato" style="width:196px">
									<option value=""></option>
									<?php

									switch($fila["contrato"])
									{
										case	"DIRECTO":			$selected_dir = " selected"; break;
										case	"BOLSA DE EMPLEO":	$selected_bde = " selected"; break;
									}

									?>
									<option value="DIRECTO"<?php echo $selected_dir ?>>DIRECTO</option>
									<option value="BOLSA DE EMPLEO"<?php echo $selected_bde ?>>BOLSA DE EMPLEO</option>
								</select>
							</td>
						</tr>
						<tr>
							<?php

							if ($_SESSION["FUNC_MAXCONSDIARIAS"])
							{

								?>
								<td align="right">Max Consultas Diarias</td><td><input type="text" name="maxconsdiarias" size="25" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" value="<?php echo $fila["maxconsdiarias"] ?>"></td>
								<?php

							}

							if ($_SESSION["FUNC_INDICADORES"]) {
								if ($_SESSION["FUNC_MAXCONSDIARIAS"]){ 

									?>
									<td width="20">&nbsp;</td>
									<?php

								}

								?>
								<td align="right">* Meta del mes</td><td><input type="text" name="meta_mes" size="25" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" value="<?php echo $fila["meta_mes"] ?>"></td>
								<?php

							}
							else
							{

								?>
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
								<td align="right">Freelance</td><td><input type="checkbox" name="freelance" value="1" onClick="document.formato.outsourcing.checked = false"<?php if ($fila["freelance"]) { echo " checked"; } ?>></td>
								<td></td>
								<td align="right">Outsourcing</td><td><input type="checkbox" name="outsourcing" value="1" onClick="document.formato.freelance.checked = false"<?php if ($fila["outsourcing"]) { echo " checked"; } ?>></td>
							</tr>
							<!--<tr>
								<td align="right">Coordinador</td><td><input type="checkbox" name="coordinador" value="1"<?php if ($fila["coordinador"]) { echo " checked"; } ?>></td>
								<td></td>
							    <td align="right">Jefe Comercial</td><td><input type="checkbox" name="jefe_comercial" value="1"<?php if ($fila["jefe_comercial"]) { echo " checked"; } ?>></td>
							</tr>-->
							<tr>
								<?php

								if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?>
									<td align="right">Administrador - S&oacute;lo lectura</td><td><input type="checkbox" name="solo_lectura" value="1"<?php if ($fila["solo_lectura"]) { echo " checked"; } ?>></td>
									<td></td>
									<?php

								}
								else{ ?>
									<input type="hidden" name="solo_lectura" value="<?php echo $fila["solo_lectura"] ?>">
									<?php
								} ?>
								<td align="right">Activo</td><td><input type="checkbox" name="estado" value="1"<?php if ($fila["estado"]) { echo " checked"; } ?>><input type="hidden" name="estadoh" value="<?php echo $fila["estado"] ?>"></td>														
							</tr>
							<tr>
								<td align="right">Mostrar en Agenda</td><td><input type="checkbox" name="agenda" value="1" checked></td>
								<td></td>
								<td align="right">BI(inteligencia de negocios)</td><td><input type="checkbox" name="bi" value="1" <?php if ($fila["inteligencia_negocios"] == 1) { echo " checked"; } ?>></td>
							</tr>
							<tr>
								<td align="right">Preprospección</td>
								<td><input type="checkbox" name="preprospeccion" value="1" <?php if ($fila["preprospeccion"] == 1) { echo " checked"; } ?>></td>
								<td></td>
								<td align="right">Recuperar Firma</td><td><input type="checkbox" name="solicitar_firma" value="1" <?php if ($fila["solicitar_firma"] == 1) { echo " checked"; } ?>></td>
							</tr>

							<tr>
								<td align="right">Revision de garantias</td>
								<td><input type="checkbox" name="revision_garantias" value="1" <?php if ($fila["revision_garantias"] == 1) { echo " checked"; } ?>></td>
								<td></td>
								<td align="right">Habilitar Prospección</td>
								<td><input type="checkbox" name="habilitar_prospeccion" value="1" <?php if ($fila["habilitar_prospeccion"] == 1) { echo " checked"; } ?>></td>
							</tr>
							<tr>
								<td align="right">Descargar Reportes</td>
								<td><input type="checkbox" name="descargar_reportes" value="1" <?php if ($fila["visualizar_reportes"] == 1) { echo " checked"; } ?>> </td>
								<td></td>
								<td align="right">Anular Firma DIgital</td>
								<td><input type="checkbox" name="anular_firma_digital" value="1" <?php if ($fila["anular_firma_digital"] == 1) { echo " checked"; } ?>> </td>
							</tr>
							<tr>
								<td align="right">Causales No Recaudoo</td>
								<td><input type="checkbox" name="causales_no_recaudo" value="1" <?php if ($fila["causales_no_recaudo"] == 1) { echo " checked"; } ?>></td>
								<td></td>

								<td align="right">Reporte Cartera</td>
								<td><input type="checkbox" name="reporte_cartera" value="1" <?php if ($fila["reporte_cartera"] == 1) { echo " checked"; } ?>></td>
							</tr>
							<tr>
								<td align="right">Centrales Judicial</td>
								<td><input type="checkbox" name="centrales_judicial" value="1" <?php if ($fila["centrales_judicial"] == 1) { echo " checked"; } ?>></td>
								<td></td>
								<td align="right"></td>
								<td></td>
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
										echo "</tr><tr>
												<td colspan='5'>
													<label>".$fila1["nombre_empresa"]."  <input type='checkbox' name='".$fila1["nombre_empresa"]."'></label>
												<td>	
											 </tr><tr>";
										$labelUnd = $fila1["nombre_empresa"];
									}

									$checked = "";
									$hidden_value = "0";
									$queryDB = "SELECT id_unidad_negocio from usuarios_unidades where id_usuario = '".$_REQUEST["id_usuario"]."' and id_unidad_negocio in (".$fila1["id_unidad"].") order by id_unidad_negocio";
									$rs2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));								
									if ( sqlsrv_num_rows($rs2) >0 ) {
										$checked = " checked";
										$hidden_value = "1";									
									}
									?>
									<td>
										<input type="checkbox" name="id_unidad<?php echo $fila1["id_unidad"] ?>" value="1"<?php echo $checked; ?>>
										<input type="hidden" name="id_unidadh<?php echo $fila1["id_unidad"] ?>" value="<?php echo $hidden_value ?>">
									</td>
									<td>
										<?php echo utf8_decode($fila1["nombre"]) ?>&nbsp;&nbsp;&nbsp;
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
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){								

									$checked = "";
									$hidden_value = "0";
									$queryDB = "select * from oficinas_usuarios ou where id_usuario = '".$_REQUEST["id_usuario"]."' and id_oficina in (".$fila1["id_oficina"].") order by id_oficina";
									$rs2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));								
									if ( sqlsrv_num_rows($rs2) >0 ) {
										$checked = " checked";
										$hidden_value = "1";									
									}
									?>
									<td>
										<input type="checkbox" name="id_oficina<?php echo $fila1["id_oficina"] ?>" value="1"<?php echo $checked; ?>>
										<input type="hidden" name="id_oficinah<?php echo $fila1["id_oficina"] ?>" value="<?php echo $hidden_value ?>">
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
	
	<br>
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<fieldset>
  						<legend><b>Reportes Asociados</b></legend>
						<table border="0" cellspacing=0 cellpadding=0>
							<tr>
								<?php
								$queryDB = "select id, descripcion from reportes order by id";
								$rs1 = sqlsrv_query($link, $queryDB);
								$queryDB = "select id_reporte from usuarios_reportes where id_usuario = '".$_REQUEST["id_usuario"]."' order by id_reporte";
								$rs2 = sqlsrv_query($link, $queryDB);
								$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

								$i = 1;

								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
									$checked = "";
									$hidden_value = "0";
									if ($fila1["id"] == $fila2["id_reporte"]) {
										$checked = " checked";
										$hidden_value = "1";
										$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
									}
									?>
									<td>
										<input type="checkbox" name="id_reporte<?php echo $fila1["id"] ?>" value="1" <?=$checked; ?>>
										<input type="hidden" name="id_reporteh<?=$fila1["id"]?>" value="<?=$hidden_value?>">
									</td>
									<td>
										<?=utf8_decode($fila1["descripcion"])?>&nbsp;&nbsp;&nbsp;
									</td>
									<?php

									if ($i % 5 == 0)
										echo "</tr><tr>";

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
	<br>
	<input type="hidden" name="id_usuario" value="<?php echo $_REQUEST["id_usuario"] ?>">
	<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
	<p align="center"><input type="submit" value="Actualizar"></p>
</form>
<?php include("bottom.php"); ?>
