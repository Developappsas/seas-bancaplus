<?php 
include ('../functions.php'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO")){
	echo "Su sesion a caducado";
	echo "Por favor inicie sesion nuevamente";
	exit;
}

$queryDB = "select si.* from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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

$simulacion_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs)){
	exit;
}

if($_REQUEST["clasificacion"] == "DSC"){
	$_REQUEST["id_beneficiario"] = 0;
}

$id_beneficiario = $_REQUEST["id_beneficiario"];

switch($simulacion["opcion_credito"])
{
	case "CLI":	$opcion_desembolso = $simulacion["opcion_desembolso_cli"];
				break;
	case "CCC":	$opcion_desembolso = $simulacion["opcion_desembolso_ccc"];
				break;
	case "CMP":	$opcion_desembolso = $simulacion["opcion_desembolso_cmp"];
				break;
	case "CSO":	$opcion_desembolso = $simulacion["opcion_desembolso_cso"];
				break;
}

$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL or (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$rs1 = sqlsrv_query($link, $queryDB);
$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if ($fila1["s"]){
	$compras_cartera = $fila1["s"];
}
	

if ($simulacion["bloqueo_cuota"]){
	$retenciones_cuota = $simulacion["bloqueo_cuota_valor"];
}
else{
	$rs1 = sqlsrv_query($link, "select SUM(cuota_retenida) as s from tesoreria_cc where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$retenciones_cuota = $fila1["s"];
}

if ($simulacion["opcion_credito"] == "CLI"){
	$simulacion["retanqueo_total"] = 0;
}
	

if($simulacion["servicio_nube"]){
	$desembolso_cliente = $simulacion["desembolso_cliente"];
}else{
	$desembolso_cliente = $simulacion["valor_credito"] - $simulacion["retanqueo_total"] - $compras_cartera - (($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $simulacion["descuento1"] / 100.00) - (($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $simulacion["descuento2"] / 100.00) - (($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $simulacion["descuento3"] / 100.00) - (($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $simulacion["descuento4"] / 100.00) - $simulacion["descuento_transferencia"];
}

if ($simulacion["tipo_producto"] == "1"){
	if ($simulacion["fidelizacion"]){
		$desembolso_cliente = $desembolso_cliente - $simulacion["retanqueo_total"] * $simulacion["descuento5"] / 100.00 - $simulacion["retanqueo_total"] * $simulacion["descuento6"] / 100.00;
	}
	else{
		$desembolso_cliente = $desembolso_cliente - $simulacion["valor_credito"] * $simulacion["descuento5"] / 100.00 - $simulacion["valor_credito"] * $simulacion["descuento6"] / 100.00;
	}
		
}
	

$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");

while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
	$desembolso_cliente -= ($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
}

$rs1 = sqlsrv_query($link, "SELECT iif(SUM(valor_girar) IS NULL, 0, SUM(valor_girar)) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_programados = $fila1["s"];
$giro_pendiente = intval($opcion_desembolso) - intval($giros_programados);

$rs1 = sqlsrv_query($link, "SELECT iif(SUM(valor_girar) IS NULL, 0, SUM(valor_girar)) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND clasificacion = 'CCA'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_cca = $fila1["s"];
$giro_pendiente_cca = $compras_cartera - $giros_cca;

$rs1 = sqlsrv_query($link, "SELECT iif(SUM(valor_girar) IS NULL, 0, SUM(valor_girar)) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND clasificacion = 'DSC'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_dsc = $fila1["s"];

$rs1 = sqlsrv_query($link, "SELECT iif(SUM(valor_girar) IS NULL, 0, SUM(valor_girar)) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND clasificacion = 'GCR'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_gcr = $fila1["s"];
$giro_pendiente_gcr = $retenciones_cuota - $giros_gcr;

$rs1 = sqlsrv_query($link, "SELECT iif(SUM(valor_girar) IS NULL, 0, SUM(valor_girar)) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND clasificacion = 'RET'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_ret = $fila1["s"];
$giro_pendiente_ret = round($simulacion["retanqueo_total"]) - $giros_ret;

include("top2.php"); 
?>
<script language="JavaScript">

function chequeo_forma(){
	var flag = 0;

	with (document.formato) {
		if ((id_beneficiario.selectedIndex == 0)<?php if ($_REQUEST["id_beneficiario"] == "-1") { ?> || (beneficiario_otro.value == "") || (nit_otro.value == "")<?php } ?> || (valor_girar.value == "") || (forma_pago.selectedIndex == 0) || (clasificacion.selectedIndex == 0)) {

			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
		if (forma_pago.value == "TRA" && (<?php if ($_REQUEST["id_beneficiario"] == "0" || $_REQUEST["id_beneficiario"] == "-1") { ?>(id_banco.selectedIndex == 0) || (tipo_cuenta.selectedIndex == 0) || (nro_cuenta.value == "")<?php } else { ?>(id_entidadcuenta.selectedIndex == 0)<?php } ?>)) {
			alert("Debe establecer a que cuenta va a realizar la trasferencia");
			return false;
		}
		if (forma_pago.value == "TRA"){
			if(validarCantDigitosCuenta() === false){
				alert("La longitud de número de cuenta no se encuentra entre el rango del banco seleccionado");
				return false;
			}
		}
		if (valor_girar.value == "0") {
			alert("El Valor a Girar no puede ser cero");
			return false;
		}
		if (parseInt(valor_girar.value.replace(/\,/g, '')) > <?php echo $giro_pendiente ?>) {
			alert("El Valor a Girar no puede ser mayor al giro pendiente por programar ($<?php echo number_format($giro_pendiente, 0, ".", ",") ?>)");
			return false;
		}
		if (clasificacion.value == "CCA" && parseInt(valor_girar.value.replace(/\,/g, '')) > <?php echo $giro_pendiente_cca ?>) {
			alert("El Valor a Girar por Compras de Cartera no puede ser mayor al valor pendiente por programar de Compras de Cartera ($<?php echo number_format($giro_pendiente_cca, 0, ".", ",") ?>)");
			return false;
		}
		if (clasificacion.value == "DSC" && parseInt(valor_girar.value.replace(/\,/g, '')) > <?php echo $giro_pendiente_dsc ?>) {
			alert("El Valor a Girar por Desembolso Cliente no puede ser mayor al valor pendiente por programar de Desembolso Cliente ($<?php echo number_format($giro_pendiente_dsc, 0, ".", ",") ?>)");
			return false;
		}
		if (clasificacion.value == "GCR" && parseInt(valor_girar.value.replace(/\,/g, '')) > <?php echo $giro_pendiente_gcr ?>) {
			alert("El Valor a Girar por Cuota Retenida no puede ser mayor al valor pendiente por programar de Cuotas Retenidas ($<?php echo number_format($giro_pendiente_gcr, 0, ".", ",") ?>)");
			return false;
		}
		if (clasificacion.value == "RET" && parseInt(valor_girar.value.replace(/\,/g, '')) > <?php echo $giro_pendiente_ret ?>) {
			alert("El Valor a Girar por Retanqueos no puede ser mayor al valor pendiente por programar de Retanqueos ($<?php echo number_format($giro_pendiente_ret, 0, ".", ",") ?>)");
			return false;
		}

		if (clasificacion.value == "DSC" && (nro_cheque.value == '' || id_cuentabancaria.value == '')){
			if(nro_cheque.value == ''){
				alert("Ingrese número de Cheque Incorrecto.");
				return false;
			}

			if(id_cuentabancaria.value == ''){
				alert("Seleccione La cuenta desde donde se realizará el Giro.");
				return false;
			}
		}
		
		<?php if ($_REQUEST["id_beneficiario"] == "-1") { ?>ReplaceComilla(beneficiario_otro);<?php } ?>
		<?php if ($_REQUEST["id_beneficiario"] == "0" || $_REQUEST["id_beneficiario"] == "-1") { ?>ReplaceComilla(nro_cuenta);<?php } ?>
		ReplaceComilla(referencia);
	}
}

function limitesDigitosCuenta(elemento){
	var digitos_cuenta_min = elemento.options[elemento.selectedIndex].getAttribute("digitos_cuenta_min");
	var digitos_cuenta_max = elemento.options[elemento.selectedIndex].getAttribute("digitos_cuenta_max");

	with (document.formato) {
		nro_cuenta.setAttribute("minlength", digitos_cuenta_min);
		nro_cuenta.setAttribute("maxlength", digitos_cuenta_max);
	}

	validarCantDigitosCuenta();
}

function validarCantDigitosCuenta(){
	var nro_cuenta = document.getElementById("nro_cuenta").value;

	with (document.formato) {
		var digitos_cuenta_min = nro_cuenta.getAttribute("minlength");
		var digitos_cuenta_max = nro_cuenta.getAttribute("maxlength");
	}
	
	document.getElementById("mensaje_nro_cuenta").textContent = "Contiene " + nro_cuenta.length + " Digitos. Longitud: Min. " + digitos_cuenta_min + " / Max. " + digitos_cuenta_max;

	if(nro_cuenta.length >= digitos_cuenta_min && nro_cuenta.length <= digitos_cuenta_max){
		return true;
	}else{
		return false;
	}
}
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Adicionar Giro</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="tesoreria_crear2.php" onSubmit="return chequeo_forma()">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td align="right">* Tipo Giro</td>
			<td>
				<select id="clasificacion" name="clasificacion" onChange="window.location.href='tesoreria_crear.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&clasificacion='+this.value+'&id_beneficiario='+document.getElementById('id_beneficiario').value+'&forma_pago='+document.getElementById('forma_pago').value" style="background-color:#EAF1DD;">
					<option value=""></option>
				<?php if(isset($_REQUEST["clasificacion"])){ ?>
					<option <?php if($_REQUEST["clasificacion"]=='CCA'){ echo "selected"; } ?> value="CCA">COMPRA DE CARTERA</option>
					<option <?php if($_REQUEST["clasificacion"]=='DSC'){ echo "selected"; } ?> value="DSC">DESEMBOLSO CLIENTE</option>
					<?php if (!($simulacion["bloqueo_cuota"] && !$simulacion["valor_visado"])) { ?>
						<option <?php if($_REQUEST["clasificacion"]=='GCR'){ echo "selected"; } ?> value="GCR">GIRO CUOTA RETENIDA</option>
					<?php } ?>
					<option <?php if($_REQUEST["clasificacion"]=='RET'){ echo "selected"; } ?> value="RET">RETANQUEO</option>
				<?php }else{ ?>
					<option value="CCA">COMPRA DE CARTERA</option>
					<option value="DSC">DESEMBOLSO CLIENTE</option>
					<?php if (!($simulacion["bloqueo_cuota"] && !$simulacion["valor_visado"])) { ?><option value="GCR">GIRO CUOTA RETENIDA</option><?php } ?>
					<option value="RET">RETANQUEO</option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">* Beneficiario</td>
			<td>
				<select id="id_beneficiario" name="id_beneficiario" style="background-color:#EAF1DD; width:500px;" onChange="window.location.href='tesoreria_crear.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&id_beneficiario='+this.value+'&clasificacion='+document.getElementById('clasificacion').value+'&forma_pago='+document.getElementById('forma_pago').value">
					<option  selected='selected' value=""></option>
					<?php
					if(isset($_REQUEST["clasificacion"])){

						if($_REQUEST["clasificacion"] == 'DSC'){//Deposito Cliente
							echo "<option value='0' selected>".utf8_decode($simulacion["nombre"]." / ".$simulacion["cedula"])."</option>\n";
						}else{

							if($_REQUEST["clasificacion"] == 'CCA'){//Compra Cartera
								$queryDB = "SELECT tcc.pagada, scc.id_entidad as id_beneficiario, CONCAT(' / ', ent.nit) as nit, CONCAT(' ', ent.nombre, ' ', scc.entidad) as nombre, scc.valor_pagar from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad LEFT JOIN tesoreria_cc tcc ON tcc.id_simulacion = scc.id_simulacion AND tcc.consecutivo = scc.consecutivo
									WHERE scc.id_simulacion = ".$_REQUEST['id_simulacion']." and se_compra = 'SI' AND scc.valor_pagar > 0 AND (tcc.pagada = 0 OR tcc.pagada IS NULL) order by scc.consecutivo";

							}elseif($_REQUEST["clasificacion"] == 'RET'){//retanqueo
								$queryDB = "SELECT id_entidad as id_beneficiario, CONCAT(' / ', nit) as nit, CONCAT(' ', nombre) as nombre from entidades_desembolso  where id_entidad in(218,104) order by nombre";

							}else{
								$queryDB = "select id_entidad as id_beneficiario, CONCAT(' / ', nit) as nit, CONCAT(' ', nombre) as nombre from entidades_desembolso UNION select 0 as id_beneficiario, CONCAT(' / ', '".$simulacion["cedula"]."') as nit, CONCAT('  ', '".$simulacion["nombre"]."') as nombre from parametros where codigo = 'CARCG'";
								//$queryDB .= " UNION select -1 as id_beneficiario, '' as nit, 'OTRO' as nombre from parametros where codigo = 'CARCG'";
								$queryDB .= " order by nombre";
							}

							$rs1 = sqlsrv_query($link, $queryDB);
							while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
								
								if ($fila1["id_beneficiario"] == $id_beneficiario){
									
									$selected_beneficiario = " selected";
									
									if ($_REQUEST["clasificacion"] == 'CCA')
										$valor_pagar = $fila1["valor_pagar"];
								}
								else{
									$selected_beneficiario = "";
								}
									
								echo "<option value=\"".$fila1["id_beneficiario"]."\"".$selected_beneficiario.">".utf8_decode($fila1["nombre"].$fila1["nit"])."</option>\n";
							}
						}
					}?>
				</select>
			</td>
		</tr>
		<?php if ($_REQUEST["id_beneficiario"] == "-1") { ?>
		<tr>
			<td align="right">* Nombre</td><td><input type="text" name="beneficiario_otro" size="35" style="background-color:#EAF1DD;"></td>
		</tr>
		<tr>
			<td align="right">* NIT</td><td><input type="text" name="nit_otro" size="20" style="background-color:#EAF1DD;" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="right">* Forma de Pago</td>
			<td>
				<select id="forma_pago" name="forma_pago" style="background-color:#EAF1DD;" onChange="window.location.href='tesoreria_crear.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&id_beneficiario='+document.getElementById('id_beneficiario').value+'&clasificacion='+document.getElementById('clasificacion').value+'&forma_pago='+this.value">
					<option <?php if($_REQUEST["forma_pago"] == ""){ echo "selected"; } ?> value=""></option>
					<option <?php if($_REQUEST["forma_pago"] == "CHE"){ echo "selected"; } ?> value="CHE">CHEQUE</option>
					<option <?php if($_REQUEST["forma_pago"] == "CHG"){ echo "selected"; } ?> value="CHG">CHEQUE GERENCIA</option>
					<option <?php if($_REQUEST["forma_pago"] == "EFE"){ echo "selected"; } ?> value="EFE">EFECTIVO</option>
					<option <?php if($_REQUEST["forma_pago"] == "TRA"){ echo "selected"; } ?> value="TRA">TRANSFERENCIA</option>
				</select>
			</td>
		</tr>
<?php
if ($_REQUEST["id_beneficiario"] == "0" || $_REQUEST["id_beneficiario"] == "-1"){ 
	$id_banco = $tipo_cuenta = $nro_cuenta = '';

	$rsDatoCli = sqlsrv_query($link, "SELECT si.tipo_cuenta, si.nro_cuenta, si.id_banco, si.desembolso_cliente, iif(sum(g.valor_girar) IS NOT NULL, sum(g.valor_girar), 0) AS girado, iif(sum(g.valor_girar) IS NOT NULL, si.desembolso_cliente - sum(g.valor_girar), si.desembolso_cliente) AS diferencia_girar  FROM simulaciones si LEFT JOIN giros g ON g.id_simulacion = si.id_simulacion AND g.clasificacion = 'DSC' WHERE si.id_simulacion = '".$_REQUEST['id_simulacion']."'  group by  si.tipo_cuenta, si.nro_cuenta, si.id_banco, si.desembolso_cliente" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if($rsDatoCli && sqlsrv_num_rows($rsDatoCli)>0){
		$datosCli = sqlsrv_fetch_array($rsDatoCli);

		if($_REQUEST["forma_pago"] == "TRA"){
			$id_banco = $datosCli["id_banco"];
			$tipo_cuenta = $datosCli["tipo_cuenta"];
			$nro_cuenta = $datosCli["nro_cuenta"];
		}
		$valor_pagar = $datosCli["diferencia_girar"];
	}
	?>
	<tr>
		<td align="right"><?php if($_REQUEST["forma_pago"] == "TRA"){ echo "* "; }?>Banco</td>
		<td>
			<select <?php if($_REQUEST["forma_pago"] == "TRA"){ ?> onchange="limitesDigitosCuenta(this);" <?php } ?> name="id_banco" id="id_banco" style="background-color:#EAF1DD;">
				<option value=""></option>
				<?php
				$queryDB = "select id_banco, nombre, digitos_cuenta_min, digitos_cuenta_max from bancos order by nombre";
				$rs1 = sqlsrv_query($link, $queryDB);
				
				while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
					if($id_banco == $fila1["id_banco"]){
						echo "<option selected digitos_cuenta_min='".$fila1["digitos_cuenta_min"]."' digitos_cuenta_max='".$fila1["digitos_cuenta_max"]."' value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
					}else{
						echo "<option digitos_cuenta_min='".$fila1["digitos_cuenta_min"]."' digitos_cuenta_max='".$fila1["digitos_cuenta_max"]."' value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
					}
				} ?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php if($_REQUEST["forma_pago"] == "TRA"){ echo "* "; }?>Tipo Cuenta</td>
		<td>
			<select name="tipo_cuenta" style="background-color:#EAF1DD;">
				<option <?php if($tipo_cuenta == ""){ echo "selected"; } ?> value=""></option>
				<option <?php if($tipo_cuenta == "AHO"){ echo "selected"; } ?> value="AHO">AHORROS</option>
				<option <?php if($tipo_cuenta == "CTE"){ echo "selected"; } ?> value="CTE">CORRIENTE</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php if($_REQUEST["forma_pago"] == "TRA"){ echo "* "; }?>Cuenta</td>
		<td>
			
			<input type="text" value="<?=$nro_cuenta?>" name="nro_cuenta" id="nro_cuenta" size="20" style="background-color:#EAF1DD;" <?php if($_REQUEST["forma_pago"] == "TRA"){ ?> minlength="9" maxlength="20" onkeyup="validarCantDigitosCuenta();" <?php } ?> >
			
			<?php if($_REQUEST["forma_pago"] == "TRA"){ ?> 
				<label style="font-weight: bold; color: red;" id="mensaje_nro_cuenta">Contiene 0 Digitos. Longitud: Min. 9 / Max. 20</label> 
			<?php } ?>
		</td>
	</tr>
	<?php
}
else{ ?>
	<tr>
		<td align="right"><?php if($_REQUEST["forma_pago"] == "TRA"){ echo "* "; }?>Cuenta</td>
		<td>
			<select name="id_entidadcuenta" style="background-color:#EAF1DD;">
				<option value=""></option>
				<?php
				$queryDB = "select ec.id_entidadcuenta, ba.nombre as banco, ec.tipo_cuenta, ec.nro_cuenta from entidades_cuentas ec INNER JOIN bancos ba ON ec.id_banco = ba.id_banco where ec.id_entidad = '".$_REQUEST["id_beneficiario"]."' order by ec.nro_cuenta";
				$rs1 = sqlsrv_query($link, $queryDB);

				while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
					echo "<option value=\"".$fila1["id_entidadcuenta"]."\">".utf8_decode($fila1["banco"]."-".$fila1["tipo_cuenta"]."-".$fila1["nro_cuenta"])."</option>\n";
				} ?>
			</select>
		</td>
	</tr>
	<?php
}

if($_REQUEST["clasificacion"] == "DSC"){ ?>

		<tr>
			<td align="right">* No. Cheque</td><td><input type="text" name="nro_cheque" size="20" style="background-color:#EAF1DD;"></td>
		</tr>
		<tr>
			<td align="right">* Cuenta De Giro</td>
			<td>
				<select name="id_cuentabancaria" style="background-color:#EAF1DD;">
					<option value=""></option>
					<?php
					$queryDB = "select cb.* from cuentas_bancarias cb INNER JOIN bancos ba ON cb.id_banco = ba.id_banco where cb.id_cuenta IS NOT NULL order by cb.nombre, cb.nro_cuenta";
					$rs2 = sqlsrv_query($link,$queryDB);

					while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)){
						$selected = "";					
						if ($fila2["id_cuenta"] == $fila1["id_cuentabancaria"]){
							$selected = " selected";
						}
							
						echo "<option value=\"".$fila2["id_cuenta"]."\"".$selected.">".utf8_decode($fila2["nombre"])."</option>\n";
					}
					?>
				</select>
			</td>
		</tr>
		<?php 
} ?>

		<tr>
			<td align="right">Referencia</td><td><input type="text" name="referencia" size="20" style="background-color:#EAF1DD;"></td>
		</tr>
		<tr>
			<td align="right">* Valor a Girar</td><td><input type="text" name="valor_girar" value="<?=$valor_pagar?>" size="20" maxlength="11" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
<br>
<p align="center">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<input type="hidden" name="cedula" value="<?php echo $simulacion["cedula"] ?>">
<input type="hidden" name="nombre" value="<?php echo utf8_decode($simulacion["nombre"]) ?>">
<input type="submit" value="Ingresar">
</p>
</form>

<script type="text/javascript">
	window.onload = limitesDigitosCuenta(document.getElementById("id_banco"));
</script>

<?php include("bottom2.php"); ?>
