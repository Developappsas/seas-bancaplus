<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Confechatent-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Simulaciones.xls");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_POST['user']) || !isset($_POST['password'])){
	echo "debe loguearse";
	exit();
}
set_time_limit(0);

include ('../functions.php'); 
$link = conectar();
$link = conectar();
mysqli_query($link, "SET SQL_BIG_SELECTS=1");

$queryPOST = mysqli_query($link, "SELECT usr FROM proveedores WHERE usr = '".$_POST['user']."' AND passwd = MD5('".$_POST['password']."')");

if(!$queryPOST || mysqli_num_rows($queryPOST) == 0){
	echo "error al loguearse";
	exit();
}


$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $urlPrincipal.'/servicios/Simulaciones/Crear_Simulaciones_Consultas_Log.php',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS =>'{"id_simulacion":"","origen":"REPORTE SIMULACIONES","usuario":"'.$_POST["S_IDUSUARIO"].'","operacion":"Crear Simulaciones Consultas Log"}',
	CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
	),
));
$response = curl_exec($curl);

curl_close($curl);

if ($_POST["S_SUBTIPO"] == "ANALISTA_VALIDACION"){
	$_REQUEST["resumidob"] = "1";
}

?>
<table border="0">
	<tr>
		<td>Id</td>   
		<td>Comercial</td>   
		<td>Tipo Comercial</td>   
		<td>Contrato</td>   
		<td>Telemercadeo</td>   
		<td>Oficina</td>   
		<td>C&eacute;dula</td>   
		<td>F Estudio</td>
		<?php
		if($_POST["FUNC_FDESEMBOLSO"]){
			?>	
			<td>F Desemb</td>
			<?php	 
		}
		?>
		<td>Mes Prod</td> 
		<td>Nombre</td> 
		<td>F Nacimiento</td> 
		<td>Sexo</td> 
		<td>Sector</td> 
		<td>Pagadur&iacute;a</td>
		<?php
		if (!$_REQUEST["resumidob"]) {
			?>
			<td>PA</td> 
			<td>Instituci&oacute;n</td> 
			<td>Meses Antes 65 A&nacute;os</td> 
			<td>F Vinculaci&oacute;n</td> 
			<td>Medio Contacto</td> 
			<td>Tel&eacute;fono</td> 
			<td>Celular</td> 
			<td>Direcci&oacute;n</td> 
			<td>Ciudad</td> 
			<td>E-mail</td> 
			<td>Sin Aportes de Ley</td> 
			<td>Salario B&aacute;sico</td> 
			<td>Adicionales S&oacute;lo (AA)</td>
			<?php
			if ($_POST["FUNC_MUESTRACAMPOS2"]){
				?>
				<td>Bonificaci&oacute;n</td>
				<?php
			}
			?>
			<td>Total Ingresos</td> 
			<td>Aportes (Salud y Pensi&oacute;n)</td> 
			<td>Otros Aportes</td> 
			<td>Total Aportes</td> 
			<td>Total Egresos</td> 
			<td>Ingresos - Aportes</td> 
			<td>Salario Libre Mensual</td> 
			<td>Vinculaci&oacute;n Cliente</td> 
			<td>Cliente Embargado</td> 
			<td>Historial Embargos</td> 
			<td>Embargo Centrales</td> 
			<td>Clave Consulta</td>
			<?php
		}
		?>

		<td>Puntaje Datacredito</td>
		<?php
		if(!$_REQUEST["resumidob"]){
			?>
			<td>Puntaje CIFIN</td> 
			<td>Calif Sector Financiero</td> 
			<td>Calif Sector Real</td> 
			<td>Calif Sector Cooperativo</td>
			<?php	  
		}
		?>

		<td>Unidad de Negocio</td> 
		<td>Tasa Inter&eacute;s</td> 
		<td>Plazo</td>

		<?php
		if (!$_REQUEST["resumidob"]) {
			?>
			<td>Plan Seguro</td> 
			<td>Valor Plan</td>
			<td>Total Cuota</td> 

			<td>Total Valor A Pagar</td> 
			<td>Total Compras</td> 
			<td>Libranza Retanqueo 1</td> 
			<td>Valor Retanqueo 1</td> 
			<td>Libranza Retanqueo 2</td> 
			<td>Valor Retanqueo 2</td> 
			<td>Libranza Retanqueo 3</td> 
			<td>Valor Retanqueo 3</td>
			<?php  
		}
		?>

		<td>Total Retanqueos</td>
		
		<?php
		if (!$_REQUEST["resumidob"]){ ?>
			<td>Opci&oacute;n Cr&eacute;dito</td>
			<?php
		}
		?>

		<td>Cuota</td> 
		<td>Cuota Corriente</td> 
		<td>Seguro</td> 
		<td>Intereses Anticipados/Aval (%)</td> 
		<td>Intereses Anticipados/Aval (Vr)</td> 
		<td>Asesor&iacute;a Financiera (%)</td> 
		<td>Base Asesor&iacute;a Financiera</td> 
		<td>Servicio Nube</td> 
		<td>Asesor&iacute;a Financiera (Vr)</td> 
		<td>IVA (%)</td> 
		<td>IVA (Vr)</td> 
		<td>GMF (%)</td> 
		<td>GMF (Vr)</td> 
		<td>Comisi&oacute;n por Venta (Retanqueos) (%)</td> 
		<td>Comisi&oacute;n por Venta (Retanqueos) (Vr)</td> 
		<td>IVA (Comision por Venta) (%)</td> 
		<td>IVA (Comision por Venta) (Vr)</td>
		<?php
		$descuentos_adicionales = mysqli_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

		while ($fila1 = mysqli_fetch_assoc($descuentos_adicionales)) {
			?>
			<td> <?=$fila1["nombre"]?>  (%)</td> 
			<td> <?=$fila1["nombre"]?>  (Vr)</td>
			<?php
		}

		?>
		<td>Transferencia</td> 
		<td>Comisi&oacute;n por Venta</td> 
		<td>Costos Administrativos</td> 
		<td>Valor Desembolso</td> 
		<td>Valor Desembolso Menos Retanqueos</td> 
		<td>Desembolso Cliente</td> 
		<td>Estado</td> 
		<td>Decisi&oacute;n</td> 
		<td>Causal</td> 
		<td>No Libranza</td> 
		<td>Valor Visado</td> 
		<td>Bloqueo Cuota</td> 
		<td>Valor Bloqueo</td> 
		<td>F Confirmaci&oacute;n</td> 
		<td>Etapa</td> 
		<td>Subestado</td> 
		<td>Caracter&iacute;stica</td> 
		<td>Estado Validaci&oacute;n</td> 
		<td>Usuario Validaci&oacute;n</td> 
		<td>F Validaci&oacute;n</td> 
		<td>Incorporado</td> 
		<td>Extraprima</td> 
		<td>Formulario Seguro</td>

		<td>Estrato Soc</td>
		<?php
		if ($_POST["FUNC_MUESTRACAMPOS1"]){
			?>
			<td>Valor Cr&eacute;dito</td>
			<?php
		}

		if (!$_REQUEST["resumidob"]){
			?>
			<td>Analista Gesti&oacute;n Comercial</td>   <td>Analista Riesgo Operativo</td>   <td>Analista Riesgo Crediticio</td>
			<?php	 
		}
		?>
		<td>F Aprobado</td>
		<?php
		if (!$_REQUEST["resumidob"]){
			?>
			<td>Usuario Incorporaci&oacute;n</td> 
			<td>F Incorporaci&oacute;n</td> 
			<td>Usuario Desistimiento</td> 
			<td>F Desistimiento</td> 
			<td>Usuario Creaci&oacute;n</td> 
			<td>F Creaci&oacute;n</td> 
			<td>Usuario Modificaci&oacute;n</td> 
			<td>F Modificaci&oacute;n</td>
			<?php	  
		}
		?>
		<td>KP PLUS</td>
		<td>FORMATO DIGITAL</td>
		<td>PORCENTAJE ASESOR&iacute;A FINANCIERA</td>
		<td>RESPONSABLE GESTION COBRO</td>
		<td>TIPO DE CREDITO</td>
		<td>ZONA</td>
		<td>PROPOSITO CREDITO</td>
		<td>FACTURADO</td>
		<td>SEGURO PARCIAL</td>
		<td>AUMENTO SALARIO MINIMO</td>
	</tr>
	<?php 
	$queryDB = "SELECT iif(si.aumento_salario_minimo=1, 'SI', 'NO') AS aumento_salario_minimo2, case when si.seguro_parcial=1 then 'SI' ELSE 'NO' END AS seguro_parcial_descripcion, si.seguro_parcial,si.servicio_nube, si.descuento1_valor, si.descuento2_valor, si.descuento3_valor, si.descuento4_valor, si.descuento5_valor, si.descuento6_valor, si.descuento7_valor, si.descuento8_valor, si.descuento9_valor, si.descuento10_valor, pcr.proposito, zon.nombre as zona_descripcion,si.resp_gestion_cobranza,CASE WHEN si.formato_digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital,si.id_simulacion, si.fecha_estudio, si.cedula, si.nombre, si.pagaduria, si.institucion, si.fecha_nacimiento, si.meses_antes_65, si.salario_basico, si.adicionales, si.total_ingresos, si.aportes, si.otros_aportes, si.total_aportes, si.total_egresos, si.ingresos_menos_aportes, si.salario_libre, si.nivel_contratacion, si.embargo_actual, si.historial_embargos, si.embargo_alimentos, si.descuentos_por_fuera, si.cartera_mora, si.valor_cartera_mora, si.puntaje_datacredito, si.puntaje_cifin, si.valor_descuentos_por_fuera, si.tasa_interes, si.plazo, si.tipo_credito, si.suma_al_presupuesto, si.total_cuota, si.total_valor_pagar, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.desembolso_cliente, si.decision, si.valor_credito, si.resumen_ingreso, si.incor, si.comision, si.utilidad_neta, si.sobre_el_credito, si.usuario_creacion, si.fecha_creacion, si.usuario_modificacion, si.fecha_modificacion, si.estado, si.calificacion, si.fecha_desembolso, si.telefono, si.bonificacion, si.nro_libranza, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.valor_visado, si.valor_por_millon_seguro, si.porcentaje_extraprima, si.fecha_inicio_labor, si.pa, si.fecha_llamada_cliente, si.retanqueo1_libranza, si.retanqueo1_valor, si.retanqueo2_libranza, si.retanqueo2_valor, si.retanqueo3_libranza, si.retanqueo3_valor, si.retanqueo_total, un.nombre as unidad_negocio, si.fidelizacion, si.bloqueo_cuota_valor, si.medio_contacto, si.calif_sector_financiero, si.calif_sector_real, si.calif_sector_cooperativo, si.embargo_centrales, si.total_se_compra, si.usuario_desistimiento, si.fecha_desistimiento, si.usuario_incorporacion, si.fecha_incorporacion, si.fecha_aprobado, si.usuario_validacion, si.fecha_validacion, si.valor_seguro, so.residencia_estrato, so.sexo, CASE WHEN si.telemercadeo = 1 THEN 'SI' ELSE 'NO' END as telemercadeo_x, pa.sector, so.clave, ps.nombre as plan_seguro, FORMAT(si.fecha_cartera, 'Y-m') as mes_prod, us.nombre as nombre_comercial, us.apellido, us.contrato, us.freelance, us.outsourcing, ofi.nombre as oficina, et.nombre as nombre_etapa, se.nombre as nombre_subestado, CASE WHEN si.sin_aportes = 1 THEN 'SI' ELSE 'NO' END as sin_aportes_ley, si.sin_seguro, CASE WHEN si.tipo_producto = 1 THEN 'SI' ELSE 'NO' END as recuperate, so.celular, so.direccion, so.ciudad as ciudad_residencia, ci.municipio, so.email, cu2.seguro, cau.nombre as causal, ca.nombre as caracteristica, CASE WHEN si.bloqueo_cuota = 1 THEN 'SI' ELSE 'NO' END as bloqueo_cuota_x, CASE WHEN si.formulario_seguro = 1 THEN 'SI' ELSE 'NO' END as formulario_seguro_x, CASE WHEN si.incorporado = 1 THEN 'SI' ELSE 'NO' END as incorporado_x, CASE si.estado_venta_cartera WHEN 1 THEN 'RECIBIDO' WHEN 2 THEN 'VALIDADO' WHEN 3 THEN 'INCONSISTENTE' WHEN 0 THEN 'NO RECIBIDO' END as validado_x, us2.nombre as nombre_analista_riesgo_operativo, us2.apellido as apellido_analista_riesgo_operativo, us3.nombre as nombre_analista_riesgo_crediticio, us3.apellido as apellido_analista_riesgo_crediticio, us4.nombre as nombre_analista_gestion_comercial, us4.apellido as apellido_analista_gestion_comercial, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x, si.descuento2 from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN planes_seguro ps ON si.id_plan_seguro = ps.id_plan LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN causales cau ON si.id_causal = cau.id_causal LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN usuarios us2 ON si.id_analista_riesgo_operativo = us2.id_usuario LEFT JOIN usuarios us3 ON si.id_analista_riesgo_crediticio = us3.id_usuario LEFT JOIN usuarios us4 ON si.id_analista_gestion_comercial = us4.id_usuario LEFT JOIN zonas zon ON zon.id_zona=ofi.id_zona LEFT JOIN propositos_credito pcr ON pcr.id_proposito = si.proposito_credito where 1 = 1";

	if ($_POST["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_POST["S_SECTOR"] . "'";
	}

	$queryDB .= " AND si.id_unidad_negocio IN (" . $_POST["S_IDUNIDADNEGOCIO"] . ")";

	if ($_POST["S_TIPO"] == "GERENTECOMERCIAL" || $_POST["S_TIPO"] == "DIRECTOROFICINA" || $_POST["S_TIPO"] == "PROSPECCION") {
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_POST["S_IDUSUARIO"] . "')";

		if ($_POST["S_SUBTIPO"] == "PLANTA")
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";

		if ($_POST["S_SUBTIPO"] == "PLANTA_EXTERNOS")
			$queryDB .= " AND si.telemercadeo in ('0','1')";

		if ($_POST["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";

		if ($_POST["S_SUBTIPO"] == "EXTERNOS")
			$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";

		if ($_POST["S_SUBTIPO"] == "TELEMERCADEO")
			$queryDB .= " AND si.telemercadeo = '1'";
	}

	if ($_REQUEST["sector"]) {
		$queryDB .= " AND pa.sector = '" . $_REQUEST["sector"] . "'";
	}

	if ($_REQUEST["pagaduria"]) {
		$queryDB .= " AND si.pagaduria = '" . $_REQUEST["pagaduria"] . "'";
	}

	if ($_REQUEST["id_oficina"]) {
		$queryDB .= " AND si.id_oficina = '" . $_REQUEST["id_oficina"] . "'";
	}

	if ($_REQUEST["institucion"]) {
		$queryDB .= " AND si.institucion = '" . $_REQUEST["institucion"] . "'";
	}

	if ($_REQUEST["edadd"]) {
		$queryDB .= " AND (CURDATE() - INTERVAL " . $_REQUEST["edadd"] . " YEAR) >= si.fecha_nacimiento";
	}

	if ($_REQUEST["edadh"]) {
		$queryDB .= " AND (CURDATE() - INTERVAL " . $_REQUEST["edadh"] . " YEAR) <= si.fecha_nacimiento";
	}

	if ($_REQUEST["salario_basicod"]) {
		$queryDB .= " AND si.salario_basico >= " . str_replace(",", "", $_REQUEST["salario_basicod"]);
	}

	if ($_REQUEST["salario_basicoh"]) {
		$queryDB .= " AND si.salario_basico <= " . str_replace(",", "", $_REQUEST["salario_basicoh"]);
	}

	if ($_REQUEST["embargo_actual"]) {
		$queryDB .= " AND si.embargo_actual = '" . $_REQUEST["embargo_actual"] . "'";
	}

	if ($_REQUEST["nivel_educativo"]) {
		$queryDB .= " AND si.nivel_educativo = '" . $_REQUEST["nivel_educativo"] . "'";
	}

	if ($_REQUEST["id_comercial"]) {
		$queryDB .= " AND si.id_comercial = '" . $_REQUEST["id_comercial"] . "'";
	}

	if ($_REQUEST["estado"]) {
		$queryDB .= " AND si.estado = '" . $_REQUEST["estado"] . "'";
	}

	if ($_REQUEST["decision"]) {
		$queryDB .= " AND si.decision = '" . $_REQUEST["decision"] . "'";
	}

	if ($_REQUEST["id_subestado"]) {
		$queryDB .= " AND si.id_subestado = '" . $_REQUEST["id_subestado"] . "'";
	}

	if ($_REQUEST["calificacion"]) {
		$queryDB .= " AND si.calificacion = '" . $_REQUEST["calificacion"] . "'";
	}

	if ($_REQUEST["solo_produccionb"]) {
		$queryDB .= " AND (si.estado IN ('DES', 'CAN') OR (si.estado IN ('ING', 'EST') AND si.decision = '" . $label_viable . "'))";
	}

	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) {
		$queryDB .= " AND si.fecha_estudio >= '" . $_REQUEST["fecha_inicialba"] . "-" . $_REQUEST["fecha_inicialbm"] . "-" . $_REQUEST["fecha_inicialbd"] . "'";
	}

	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND si.fecha_estudio <= '" . $_REQUEST["fecha_finalba"] . "-" . $_REQUEST["fecha_finalbm"] . "-" . $_REQUEST["fecha_finalbd"] . "'";
	}

	if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"]) {
		$queryDB .= " AND si.fecha_desembolso >= '" . $_REQUEST["fechades_inicialba"] . "-" . $_REQUEST["fechades_inicialbm"] . "-" . $_REQUEST["fechades_inicialbd"] . "'";
	}

	if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"]) {
		$queryDB .= " AND si.fecha_desembolso <= '" . $_REQUEST["fechades_finalba"] . "-" . $_REQUEST["fechades_finalbm"] . "-" . $_REQUEST["fechades_finalbd"] . "'";
	}

	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"]) {
		$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') >= '" . $_REQUEST["fechacartera_inicialba"] . "-" . $_REQUEST["fechacartera_inicialbm"] . "'";
	}

	if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"]) {
		$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') <= '" . $_REQUEST["fechacartera_finalba"] . "-" . $_REQUEST["fechacartera_finalbm"] . "'";
	}

	$queryDB .= " order by si.fecha_creacion, si.nombre, si.cedula";

 // echo $queryDB;
	$rs = mysqli_query($link, $queryDB);

	while($fila = mysqli_fetch_assoc($rs)){
		$tipo_comercial = 'PLANTA';

		if ($fila["freelance"]) {
			$tipo_comercial = 'FREELANCE';
		}

		if ($fila["outsourcing"]) {
			$tipo_comercial = 'OUTSOURCING';
		}

		switch ($fila["opcion_credito"]) {
			case "CLI":
			$opcion_cuota = $fila["opcion_cuota_cli"];
			$opcion_desembolso = $fila["opcion_desembolso_cli"];
			break;
			case "CCC":
			$opcion_cuota = $fila["opcion_cuota_ccc"];
			$opcion_desembolso = $fila["opcion_desembolso_ccc"];
			break;
			case "CMP":
			$opcion_cuota = $fila["opcion_cuota_cmp"];
			$opcion_desembolso = $fila["opcion_desembolso_cmp"];
			break;
			case "CSO":
			$opcion_cuota = $fila["opcion_cuota_cso"];
			$opcion_desembolso = $fila["opcion_desembolso_cso"];
			break;
		}

		if ($fila["opcion_credito"] == "CLI"){
			$fila["retanqueo_total"] = 0;
		}

		if ($fila["seguro"] || $fila["seguro"] == "0"){
			$seguro = $fila["seguro"];
		}else{

			if (!$fila["sin_seguro"]){
				$seguro = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100)));
			}else{
				$seguro = 0;
			}

		}

		$cuota_corriente = $opcion_cuota - $seguro;

		?>
		<tr>
			<td><?= $fila["id_simulacion"];?></td>
			<td><?= str_replace("\"", "'", utf8_decode($fila["nombre_comercial"] . " " . $fila["apellido"])) ;?></td> 
			<td> <?=$tipo_comercial ;?></td> 
			<td> <?=$fila["contrato"] ;?></td> 
			<td> <?=$fila["telemercadeo_x"] ;?></td> 
			<td> <?= str_replace("\"", "'", utf8_decode($fila["oficina"])) ;?></td> 
			<td> <?=$fila["cedula"] ;?></td> 
			<td> <?=$fila["fecha_estudio"];?></td>

			<?php	
			if ($_POST["FUNC_FDESEMBOLSO"]){
				?>	
				<td> <?=$fila["fecha_desembolso"] ;?></td>
				<?php
			}
			?>
			<td> <?= $fila["mes_prod"] ;?></td> 
			<td> <?= str_replace("\"", "'", utf8_decode($fila["nombre"])) ;?></td> 
			<td> <?=$fila["fecha_nacimiento"];?></td> 
			<td> <?=$fila["sexo"];?></td> 
			<td> <?=$fila["sector"] ;?></td> 
			<td><?=  str_replace("\"", "'", utf8_decode($fila["pagaduria"])) ;?></td>
			<?php
			if (!$_REQUEST["resumidob"]) {
				?>		
				<td> <?=$fila["pa"] ;?></td> 
				<td> <?= str_replace("\"", "'", utf8_decode($fila["institucion"])) ;?></td> 
				<td> <?=$fila["meses_antes_65"];?></td> 
				<td> <?=$fila["fecha_inicio_labor"] ;?></td> 
				<td> <?=$fila["medio_contacto"] ;?></td> 
				<td> <?=  str_replace("\"", "'", utf8_decode($fila["telefono"]));?></td> 
				<td> <?=  str_replace("\"", "'", utf8_decode($fila["celular"])) ;?></td> 
				<td> <?=  str_replace("\"", "'", utf8_decode($fila["direccion"])) ;?></td> 
				<td> <?=  str_replace("\"", "'", utf8_decode($fila["municipio"])) ;?></td> 
				<td> <?=  str_replace("\"", "'", utf8_decode($fila["email"])) ;?></td> 
				<td><?=$fila["sin_aportes_ley"];?></td>
				<td> <?=$fila["salario_basico"] ;?></td> 
				<td> <?=$fila["adicionales"] ;?></td>
				<?php
				if ($_POST["FUNC_MUESTRACAMPOS2"]){
					?>	
					<td> <?=$fila["bonificacion"];?> </td>
					<?php	 	
				}
				?>
				<td> <?=$fila["total_ingresos"];?></td> 
				<td> <?=$fila["aportes"];?></td> 
				<td> <?=$fila["otros_aportes"];?></td> 
				<td> <?=$fila["total_aportes"] ;?></td> 
				<td> <?=$fila["total_egresos"] ;?></td> 
				<td> <?=$fila["ingresos_menos_aportes"] ;?></td> 
				<td> <?=$fila["salario_libre"];?></td> 
				<td> <?=$fila["nivel_contratacion"] ;?></td> 
				<td> <?=$fila["embargo_actual"] ;?></td> 
				<td> <?=$fila["historial_embargos"];?></td> 
				<td> <?=$fila["embargo_centrales"] ;?></td> 
				<td> <?=$fila["clave"]  ;?></td>
				<?php		 
			}
			?>	
			<td> <?= $fila["puntaje_datacredito"];?> </td>
			<?php
			if (!$_REQUEST["resumidob"]) {
				?>	
				<td><?=$fila["puntaje_cifin"] ;?></td> 
				<td> <?=$fila["calif_sector_financiero"] ;?></td> 
				<td> <?=$fila["calif_sector_real"] ;?></td> 
				<td> <?=$fila["calif_sector_cooperativo"] ;?></td>
				<?php	 	
			}
			?>

			<td><?= utf8_decode($fila["unidad_negocio"]);?></td>  
			<td> <?= $fila["tasa_interes"];?></td>  
			<td><?= $fila["plazo"] ;?></td>

			<?php
			if (!$_REQUEST["resumidob"]) { ?>	

				<td> <?= str_replace("\"", "'", utf8_decode($fila["plan_seguro"]));?></td>  
				<td> <?=$fila["valor_seguro"] ;?> </td>
				<td> <?=$fila["total_cuota"] ;?> </td>
				<?php	 	
				/*$queryDB = "select scc.consecutivo, ent.nombre as nombre_entidad, scc.se_compra, scc.entidad, scc.cuota, scc.valor_pagar from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '" . $fila["id_simulacion"] . "' order by scc.consecutivo";

				$rs2 = mysqli_query($link, $queryDB);

				$fila2 = mysqli_fetch_assoc($rs2);

				for ($i = 1; $i <= 10; $i++) {
					if ($fila2["consecutivo"] && $fila2["consecutivo"] == $i) {
						$nombre_entidad_biz = $fila2["nombre_entidad"];
						$entidad_biz = $fila2["entidad"];
						$cuota_biz = $fila2["cuota"];
						$valor_pagar_biz = $fila2["valor_pagar"];
						$se_compra_biz = $fila2["se_compra"];

						$fila2 = mysqli_fetch_assoc($rs2);
					} else {
						$nombre_entidad_biz = "";
						$entidad_biz = "";
						$cuota_biz = "0";
						$valor_pagar_biz = "0";
						$se_compra_biz = "SI";
					}

					//$registro .= .<td>.str_replace(""", "'", utf8_decode($nombre_entidad_biz)).<td>..<td>.str_replace(""", "'", utf8_decode($entidad_biz)).<td>..<td>.$cuota_biz.<td>..<td>.$valor_pagar_biz.<td>..<td>.$se_compra_biz.</td>
				}*/
			}

			if (!$_REQUEST["resumidob"]){ ?>
				<td> <?=$fila["total_valor_pagar"];?></td> 
				<td> <?= $fila["total_se_compra"] ;?></td> 
				<td> <?= $fila["retanqueo1_libranza"] ;?></td> 
				<td> <?= $fila["retanqueo1_valor"] ;?></td> 
				<td> <?= $fila["retanqueo2_libranza"] ;?></td> 
				<td> <?= $fila["retanqueo2_valor"] ;?></td> 
				<td> <?= $fila["retanqueo3_libranza"] ;?></td> 
				<td> <?= $fila["retanqueo3_valor"] ;?></td>
				<?php
			}
			
			?>
			<td><?= $fila["retanqueo_total"] ;?></td>
			<?php
			if (!$_REQUEST["resumidob"]){
				?>	
				<td> <?=$fila["opcion_credito"];?></td>
				<?php
			}

			$asesoria_financiera = @round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00, 0);
			$asesoria_financiera_base = $asesoria_financiera;
			$iva = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00, 0);
			$iva_porc = $fila["descuento3"];
			$valor_servicio_nube = 0;
			$asesoria_financiera_nueva = $asesoria_financiera;

			if($fila["servicio_nube"]){
				$asesoria_financiera = $fila["descuento2_valor"];
				$valor_servicio_nube = $fila["descuento8_valor"];
				$asesoria_financiera_nueva = $fila["descuento9_valor"];
				$iva = $fila["descuento10_valor"];

				if($fila["descuento10_valor"] > 0){
					$iva_porc = round($iva / ($fila["valor_credito"] - $fila["retanqueo_total"]) * 1000, 2);
				}else{
					$iva_porc = 0;
				}
			}
			?>
			<td> <?= $opcion_cuota ;?></td> 
			<td> <?= $cuota_corriente ;?></td> 
			<td> <?= $seguro ;?></td> 
			<td> <?= $fila["descuento1"] ;?></td> 
			<td><?= round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00, 0);?></td> 
			<td> <?= $fila["descuento2"] ;?></td> 
			<td> <?= $asesoria_financiera_base ;?></td> 
			<td> <?= $valor_servicio_nube ;?></td> 
			<td> <?= $asesoria_financiera_nueva ;?></td> 
			<td> <?= $fila["descuento3"] ;?></td> 
			<td> <?= $iva ;?></td> 
			<td> <?= $fila["descuento4"] ;?></td> 
			<td> <?=round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00, 0) ;?></td>

			<?php 
			if ($fila["recuperate"] == "SI"){
				?>	
				<td> <?=$fila["descuento5"] ;?> </td>
				<?php
				if ($fila["fidelizacion"]){
					?>		
					<td> <?=round($fila["retanqueo_total"] * $fila["descuento5"] / 100.00, 0);?> </td>
					<?php		 
				}
				else{
					?>
					<td> <?=round($fila["valor_credito"] * $fila["descuento5"] / 100.00, 0);?> </td>
					<?php 
				}
				?>
				<td><?=$fila["descuento6"] ?></td>

					<?php
					if ($fila["fidelizacion"]){
						?>	
						<td> <?=round($fila["retanqueo_total"] * $fila["descuento6"] / 100.00, 0);?> </td>
						<?php	 
					}
					else{
						?>	
						<td> <?=round($fila["valor_credito"] * $fila["descuento6"] / 100.00, 0);?> </td>
						<?php	 
					}
				}else{
					?>
					<td></td>
					<td></td>
					<td></td>
					<td></td> 
					<?php
				}
				$descuentos_adicionales = mysqli_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

				while ($fila1 = mysqli_fetch_assoc($descuentos_adicionales)) {
					$existe_descuento = mysqli_query($link, "select * from simulaciones_descuentos where id_simulacion = '" . $fila["id_simulacion"] . "' AND id_descuento = '" . $fila1["id_descuento"] . "'");

					if (mysqli_num_rows($existe_descuento)) {
						$porcentaje = $fila1["porcentaje"];
						$valor_descuento = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);

						$total_descuentos_adicionales[$fila1["id_descuento"]] += $valor_descuento;
					} else {
						$porcentaje = 0;
						$valor_descuento = 0;
					}
					?>
					<td> <?=$porcentaje ;?></td>  
					<td> <?= $valor_descuento ;?></td>
					<?php 
				}

				if($fila["servicio_nube"]){
					if ($fila["fidelizacion"]){
						$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento4"]) / 100.00 + $fila["retanqueo_total"] * (($fila["recuperate"] == "SI" ? $fila["descuento5"] : 0) + ($fila["recuperate"] == "SI" ? $fila["descuento6"] : 0)) / 100.00;
					}
					else{
						$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento4"]) / 100.00 + $fila["valor_credito"] * (($fila["recuperate"] == "SI" ? $fila["descuento5"] : 0) + ($fila["recuperate"] == "SI" ? $fila["descuento6"] : 0)) / 100.00;
					}

					$administrativos += $iva;
				}else{
					if ($fila["fidelizacion"]){
						$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento3"] + $fila["descuento4"]) / 100.00 + $fila["retanqueo_total"] * (($fila["recuperate"] == "SI" ? $fila["descuento5"] : 0) + ($fila["recuperate"] == "SI" ? $fila["descuento6"] : 0)) / 100.00;
					}
					else{
						$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento3"] + $fila["descuento4"]) / 100.00 + $fila["valor_credito"] * (($fila["recuperate"] == "SI" ? $fila["descuento5"] : 0) + ($fila["recuperate"] == "SI" ? $fila["descuento6"] : 0)) / 100.00;
					}
				}

				$descuentos_adicionales = mysqli_query($link, "select * from simulaciones_descuentos where id_simulacion = '" . $fila["id_simulacion"] . "' order by id_descuento");

				while ($fila1 = mysqli_fetch_assoc($descuentos_adicionales)) {
					$administrativos += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
				}
				?>
				<td>  <?=$fila["descuento_transferencia"] ;?></td> 
				<td> <?=$fila["recuperate"];?></td> 
				<td> <?= round($administrativos, 0) ;?></td> 
				<td> <?=$opcion_desembolso ;?></td> 
				<td> <?php echo ($opcion_desembolso - $fila["retanqueo_total"]) ;?></td> 
				<td> <?=$fila["desembolso_cliente"] ;?></td> 
				<td> <?=$fila["estado"] ;?></td> 
				<td> <?=$fila["decision"] ;?></td> 
				<td> <?= str_replace("\"", "'", utf8_decode($fila["causal"])) ;?></td> 
				<td> <?= $fila["nro_libranza"] ;?></td> 
				<td> <?= $fila["valor_visado"] ;?></td> 
				<td> <?= $fila["bloqueo_cuota_x"] ;?></td> 
				<td> <?= $fila["bloqueo_cuota_valor"] ;?></td> 
				<td> <?= $fila["fecha_llamada_cliente"] ;?></td> 
				<td> <?= str_replace("\"", "'", utf8_decode($fila["nombre_etapa"]));?></td> 
				<td> <?=$fila["nombre_subestado"];?></td> 
				<td> <?= str_replace("\"", "'", utf8_decode($fila["caracteristica"]));?></td> 
				<td> <?=$fila["validado_x"] ;?></td> 
				<td> <?=$fila["usuario_validacion"] ;?></td> 
				<td> <?=$fila["fecha_validacion"] ;?></td> 
				<td> <?=$fila["incorporado_x"] ;?></td> 
				<td> <?=$fila["porcentaje_extraprima"] ;?></td> 
				<td> <?=$fila["formulario_seguro_x"] ;?></td>

				<td> <?= $fila["residencia_estrato"] ;?></td>
				<?php
				if ($_POST["FUNC_MUESTRACAMPOS1"]){
					?>		
					<td> <?=$fila["valor_credito"] ;?></td>
					<?php
				}

				if (!$_REQUEST["resumidob"]){
					?>	
					<td> <?= str_replace("\"", "'", utf8_decode($fila["nombre_analista_gestion_comercial"] . " " . $fila["apellido_analista_gestion_comercial"])) ;?></td> 
					<td> <?= str_replace("\"", "'", utf8_decode($fila["nombre_analista_riesgo_operativo"] . " " . $fila["apellido_analista_riesgo_operativo"])) ;?></td> 
					<td> <?= str_replace("\"", "'", utf8_decode($fila["nombre_analista_riesgo_crediticio"] . " " . $fila["apellido_analista_riesgo_crediticio"])) ;?></td>
					<?php		 
				}
				?>
				<td><?=$fila["fecha_aprobado"];?></td>
				<?php
				if (!$_REQUEST["resumidob"]){
					?>	
					<td> <?=$fila["usuario_incorporacion"] ;?></td> 
					<td> <?=$fila["fecha_incorporacion"] ;?></td> 
					<td> <?=$fila["usuario_desistimiento"] ;?></td> 
					<td> <?=$fila["fecha_desistimiento"] ;?></td> 
					<td> <?=$fila["usuario_creacion"] ;?></td> 
					<td> <?=$fila["fecha_creacion"] ;?></td> 
					<td> <?=$fila["usuario_modificacion"] ;?></td> 
					<td> <?=$fila["fecha_modificacion"]  ;?></td>
					<?php		 	
				}
				?>
				<td><?=$fila["sin_seguro_x"] ;?></td>
				<td><?=$fila["formato_digital"] ;?></td>
				<td><?=$fila["descuento2"] ;?></td>
				<?php
				if ($fila["resp_gestion_cobranza"] == "") {

					$cobranza = "NO APLICA";
				} else {
					$consultaCobranza = "SELECT * FROM resp_gestion_cobros WHERE id_resp_cobros='" . $fila["resp_gestion_cobranza"] . "'";
					$queryCobranza = mysqli_query($link, $consultaCobranza, $con);
					$resCobranza = mysqli_fetch_assoc($queryCobranza);
					$cobranza = $resCobranza . " - " . $fila["detalle_resp_gestion_cobranza"];
				}

				$tipo_crediton="";
				$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
				$queryComprasCarteraCredito=mysqli_query($link, $consultarComprasCarteraCredito);

				if (mysqli_num_rows($queryComprasCarteraCredito)>0) {
					$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
					$queryComprasCC=mysqli_query($link, $consultarComprasCC);
					$resComprasCC=mysqli_fetch_assoc($queryComprasCC);

					if ($resComprasCC["cuota"]>0){
						if ($fila["retanqueo1_libranza"]=="" || $fila["retanqueo2_libranza"]=="" || $fila["retanqueo3_libranza"]==""){
							$tipo_crediton="COMPRAS DE CARTERA";	
						}else{
							$tipo_crediton="COMPRAS CON RETANQUEO";	
						}
					}
					else {
						if ($resComprasCC["valor_pagar"]>0){
							$tipo_crediton="LIBRE CON SANEAMIENTO";	
						}else{
							if ($fila["retanqueo1_libranza"]<>"" || $fila["retanqueo2_libranza"]<>"" || $fila["retanqueo3_libranza"]<>""){
								$tipo_crediton="LIBRE INVERSION CON RETANQUEO";	
							}
						}
					}
				}else{
					$tipo_crediton="LIBRE INVERSION";
				}
				?>
				<td> <?= $cobranza ;?> </td>
				<td> <?= $tipo_crediton ;?> </td>
				<td> <?= $fila["zona_descripcion"] ;?> </td>
				<td> <?= $fila["proposito"] ;?> </td>
				<?php	 	
				$rptaFacturado="NO"; 
				$consultarFacturado = "SELECT * FROM hst_facturacion_creditos WHERE id_simulacion = '".$fila["id_simulacion"]."' ORDER BY id DESC LIMIT 1"; 
				$queryFacturado = mysqli_query($link,$consultarFacturado);
				if($queryFacturado) { 
					if (mysqli_num_rows($queryFacturado) > 0) { 
						$resFacturado = mysqli_fetch_assoc($queryFacturado); 
						if ($resFacturado["facturado"] == 1){ 
							$rptaFacturado = "SI"; 
						}
					}
				}
				?>
				<td> <?= $rptaFacturado ;?> </td>
				<td> <?= $fila["seguro_parcial_descripcion"] ;?></td>
				<td> <?= $fila["aumento_salario_minimo2"] ;?></td>
			</tr>
			<?php

		}


		?>
	</table>
