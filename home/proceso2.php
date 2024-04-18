<?php include ('../functions.php'); ?>
<?php

$link = conectar();
?>

<?php include("top.php"); ?>
<?php


$parametros = sqlsrv_query($link, "SELECT * from parametros where tipo = 'SIMULADOR' order by codigo" );

$j = 0;

while ($fila1 = sqlsrv_fetch_array($parametros))
{
	$parametro[$j] = $fila1["valor"];

	$j++;
}

$cartera_castigada_permitida = $parametro[0];
$cobertura = $parametro[1];
$cuota_manejo = $parametro[2];
$descuento_transferencia = $parametro[3];
$dias_ajuste = $parametro[4];
$edad_maxima_administrativos_hombres = $parametro[5];
$edad_maxima_administrativos_mujeres = $parametro[6];
$edad_maxima_activos = $parametro[7];
$edad_maxima_pensionados = $parametro[8];
$plazo_maximo = $parametro[9];
$descuento_freelance2 = $parametro[10];
$descuento_freelance3 = $parametro[11];
$descuento_producto1 = $parametro[12];
$porcentaje_aportes_activos = $parametro[13];
$porcentaje_comision = $parametro[14];
$descuento1 = $parametro[15];
$descuento2 = $parametro[16];
$descuento3 = $parametro[17];
$descuento4 = $parametro[18];
$descuento5 = $parametro[19];
$descuento6 = $parametro[20];
$porcentaje_aportes_pensionados = $parametro[21];
$porcentaje_incorporacion = $parametro[22];
$porcentaje_sobre_util = $parametro[23];
$porcentaje_sobre_desm1 = $parametro[24];
$porcentaje_sobre_desm2 = $parametro[25];
$puntaje_cifin = $parametro[26];
$puntaje_datacredito = $parametro[27];
$salario_minimo = $parametro[28];
$seguro = $parametro[29];
$tasa_efectiva_fondeo = $parametro[30];
$tasa_interes_maxima = $parametro[31];
$tasa_interes_a = $parametro[32];
$tasa_interes_b = $parametro[33];
$tasa_interes_c = $parametro[34];
$valor_por_millon_seguro_activos = $parametro[35];
$valor_por_millon_seguro_pensionados = $parametro[36];

$descuento_producto0 = $descuento1;



$es_freelance = sqlsrv_query($link, "SELECT * from usuarios where id_usuario = '".$id_comercial."' and freelance = '1'" );


	if (sqlsrv_num_rows($es_freelance))
	{
		$descuento2 = $descuento_freelance2;
		$descuento3 = $descuento_freelance3;
	}


$suma_descuentos = $descuento1 + $descuento2 + $descuento3 + $descuento4;
$tasa_nominal_fondeo = (pow(1 + $tasa_efectiva_fondeo / 100.00, (1.00 / 12.00)) - 1) * 100.00;


 echo date("Y-m-d H:i:s");
        echo("<br>");

		
	
		$empleado = sqlsrv_query("select *, (EXTRACT(YEAR FROM fecha_nacimiento + interval ".$edad_maxima_activos." YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM fecha_nacimiento + interval ".$edad_maxima_activos." YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_activos, (EXTRACT(YEAR FROM fecha_nacimiento + interval ".$edad_maxima_pensionados." YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM fecha_nacimiento + interval ".$edad_maxima_pensionados." YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_pensionados, (EXTRACT(YEAR FROM fecha_nacimiento + interval ".$edad_maxima_administrativos_hombres." YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM fecha_nacimiento + interval ".$edad_maxima_administrativos_hombres." YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_administrativos_hombres, (EXTRACT(YEAR FROM fecha_nacimiento + interval ".$edad_maxima_administrativos_mujeres." YEAR) - EXTRACT(YEAR FROM CURDATE())) * 12 + (EXTRACT(MONTH FROM fecha_nacimiento + interval ".$edad_maxima_administrativos_mujeres." YEAR) - EXTRACT(MONTH FROM CURDATE())) as meses_antes_administrativos_mujeres from ".$prefijo_tablas."empleados where cedula = '".$_REQUEST["cedula"]."'", $link);
		
		
		

	
	while($fila = sqlsrv_fetch_array($empleado))
		{

			$plazo = $plazo_maximo;
				$plazo_maximo_segun_edad = $plazo_maximo;

				$tasa_interes = $tasa_interes_maxima;

				$nombre = utf8_decode($fila["nombre"]);
				$fecha_estudio = date("Y-m-d");
				$pagaduria = utf8_decode($fila["pagaduria"]);
				$ciudad = utf8_decode($fila["ciudad"]);
				$institucion = utf8_decode($fila["institucion"]);
				$nivel_educativo = utf8_decode($fila["nivel_educativo"]);
				$fecha_nacimiento = $fila["fecha_nacimiento"];
				$nivel_contratacion = utf8_decode(strtoupper($fila["nivel_contratacion"]));
				$meses_antes = "0";
				$telefono = utf8_decode($fila["telefono"]);

				if ($fecha_nacimiento && $fecha_nacimiento != "0000-00-00")
				{
					$diff_dias_ultimo_mes = date("j", strtotime($fecha_nacimiento)) - date("j", strtotime($fecha_estudio));

					if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
						$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);
					else if (strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"])
					{
						if (strtoupper($fila["sexo"]) == "M")
							$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_administrativos_hombres"] : ($fila["meses_antes_administrativos_hombres"] - 1);
						else if (strtoupper($fila["sexo"]) == "F")
							$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_administrativos_mujeres"] : ($fila["meses_antes_administrativos_mujeres"] - 1);
					}
					else
						$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_activos"] : ($fila["meses_antes_activos"] - 1);

					if ($meses_antes < $plazo_maximo)
					{
						$plazo = $meses_antes;
						$plazo_maximo_segun_edad = $meses_antes;
					}

					if ($meses_antes < 0)
					{
						$plazo = 0;
						$plazo_maximo_segun_edad = 0;
					}

					if ($meses_antes == 1)
						$meses_antes .= " MES";

					if ($meses_antes > 1)
						$meses_antes .= " MESES";

					if ($meses_antes <= 0)
						$meses_antes = "0";
				}

				$salario_basico = $fila["salario_basico"];

				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$rs_adicionales = sqlsrv_query("select SUM(valor) as s from ".$prefijo_tablas."adicionales where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["paga"]."'", $link);

					$fila_adicionales = sqlsrv_fetch_array($rs_adicionales);

					$adicionales = $fila_adicionales["s"];
				}

				if ($_SESSION["FUNC_MUESTRACAMPOS2"] && strtoupper($fila["ciudad_labora"]) == "VALLEDUPAR")
				{
					$rs_bonificacion = sqlsrv_query("select ingreso from ".$prefijo_tablas."ingresos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."' AND UPPER(entidad) = 'PRIMA ANTIGUEDAD EMPLEADOS MUNICIPALES'", $link);

					$fila_bonificacion = sqlsrv_fetch_array($rs_bonificacion);

					$bonificacion = $fila_bonificacion["ingreso"];
				}

				if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
				{
					$porcentaje_aportes = $porcentaje_aportes_pensionados;
					$valor_por_millon_seguro = $valor_por_millon_seguro_pensionados;
					$tipo_empleado = "PENSIONADO";
				}
				else
				{
					$porcentaje_aportes = $porcentaje_aportes_activos;
					$valor_por_millon_seguro = $valor_por_millon_seguro_activos;
					$tipo_empleado = "ACTIVO";
				}

				if ($plazo)
				{
					$rs_porcentaje_seguro = sqlsrv_query("select porcentaje from porcentajes_seguro where tipo_empleado = '".$tipo_empleado."' AND plazo = '".$plazo."'", $link);

					$fila_porcentaje_seguro = sqlsrv_fetch_array($rs_porcentaje_seguro);

					$porcentaje_seguro = $fila_porcentaje_seguro["porcentaje"];
				}
				else
				{
					$porcentaje_seguro = 0;
				}

				if (strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"])
					$aportes = $salario_basico * $porcentaje_aportes / 100.00;
				else
					$aportes = ($salario_basico + $adicionales) * $porcentaje_aportes / 100.00;

				$total_ingresos = $salario_basico + $adicionales + $bonificacion;
				$total_aportes = round($aportes);
				$total_egresos = $fila["egresos"];
				$ingresos_menos_aportes = $total_ingresos - $total_aportes;

				if ($total_ingresos < $salario_minimo * 2)
				{
					if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
						$salario_libre = $ingresos_menos_aportes / 2;
					else
						$salario_libre = $salario_minimo;
				}
				else
				{
					$salario_libre = $ingresos_menos_aportes / 2;
				}

				$embargos = sqlsrv_query("select * from ".$prefijo_tablas."embargos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."'", $link);

				$embargo_actual = "NO";
				$embargo_alimentos = "NO";

				$j = 0;

				while ($fila1 = sqlsrv_fetch_array($embargos))
				{
					if (!$fila1["fechafin"])
						$embargo_actual = "SI";

					if (strpos(strtoupper($fila1["tipoembargo"]), "ALIMENTO") !== false)
						$embargo_alimentos = "SI";

					$j++;
				}

				$historial_embargos = $j;

				$rechazos = sqlsrv_query("select * from ".$prefijo_tablas."rechazos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."'", $link);

				if (sqlsrv_num_rows($rechazos))
				{
					$descuentos_por_fuera = "SI";
				}
				else
				{
					$descuentos_por_fuera = "NO";
				}

				$tipo_credito = "CREDITO NORMAL";

				$tipo_producto = "0";

				$total_cuota = 0;
				$total_cuota_max = 0;

				$descuentos = sqlsrv_query("select des.*, CASE WHEN ent.dias_entrega IS NULL THEN 0 ELSE ent.dias_entrega END as dias_entrega, CASE WHEN ent.dias_vigencia IS NULL THEN 0 ELSE ent.dias_vigencia END as dias_vigencia from ".$prefijo_tablas."descuentos des left join entidades ent ON des.entidad = ent.entidad where des.cedula = '".$_REQUEST["cedula"]."' AND des.pagaduria = '".$_REQUEST["pagad"]."' order by ent.dias_entrega DESC, des.codigo", $link);

				$min_dias_vigencia = 1000;

				$j = 0;

				while ($fila1 = sqlsrv_fetch_array($descuentos))
				{
					$entidad[$j] = $fila1["entidad"];
					$cuota[$j] = $fila1["descuento"];

					if ($_SESSION["FUNC_MUESTRACAMPOS1"])
						$total_cuota += $fila1["descuento"];

					$total_cuota_max += $fila1["descuento"];

					$entidadcarta[$j] = $fila1["entidad"];
					$dias_entrega[$j] = $fila1["dias_entrega"];
					$dias_vigencia[$j] = $fila1["dias_vigencia"];

					$dias_entregah[$j] = $fila1["dias_entrega"];
					$dias_vigenciah[$j] = $fila1["dias_vigencia"];

					if ($j == 0)
					{
						$fecha_sugerida[$j] = date("Y-m-d");
					}
					else
						$fecha_sugerida[$j] = date_format(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string($dias_entrega[0] - $dias_entrega[$j].' days')), 'Y-m-d');

					$fecha_entrega[$j] = date_format(date_add(date_create($fecha_sugerida[$j]), date_interval_create_from_date_string($dias_entrega[$j].' days')), 'Y-m-d');

					$fecha_vencimiento[$j] = date_format(date_add(date_create($fecha_entrega[$j]), date_interval_create_from_date_string($dias_vigencia[$j].' days')), 'Y-m-d');

					if ($dias_vigencia[$j] < $min_dias_vigencia)
						$min_dias_vigencia = $dias_vigencia[$j];

					$j++;
				}

				$dia_confirmacion = $fecha_entrega[0];
				$dia_vencimiento = date_format(date_add(date_create($dia_confirmacion), date_interval_create_from_date_string($min_dias_vigencia.' days')), 'Y-m-d');
				$status = "PROCESO";

				if ($total_ingresos < $salario_minimo * 2)
				{
					if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
						$opcion_cuota_base = $total_ingresos - round($salario_libre) - $total_egresos;
					else
						$opcion_cuota_base = $total_ingresos - $salario_minimo - $total_egresos;
				}
				else
				{
					$opcion_cuota_base = $total_ingresos - round($salario_libre) - $total_egresos;
				}

				$otros_descuentos = 0;

				$opcion_cuota_cli = $opcion_cuota_base;

				$opcion_cuota_cli_menos_seguro = round($opcion_cuota_cli) * (100.00 - $porcentaje_seguro) / 100.00;

				if ($opcion_cuota_cli < 0)
					$opcion_desembolso_cli = "MEJORA SALARIO";
				else
				{
					$valor_credito_cli = $opcion_cuota_cli_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));

					if ($_SESSION["FUNC_MUESTRACAMPOS2"])
					{
						$descuento_cuota_manejo_cli = floor($valor_credito_cli / 1000000) * $cuota_manejo;
						$descuento_dias_ajuste_cli = $valor_credito_cli * ($tasa_interes / 100.00) / 30 * $dias_ajuste;
						$descuento_seguro_cli = $valor_credito_cli / 1000000 * $plazo * $seguro;

						$otros_descuentos = $descuento_cuota_manejo_cli + $descuento_dias_ajuste_cli + $descuento_seguro_cli;
					}

					$opcion_desembolso_cli = ($valor_credito_cli * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;
					$opcion_desembolso_cli = number_format($opcion_desembolso_cli, 0, ".", ",");
				}

				$opcion_cuota_ccc = $opcion_cuota_base + $total_cuota;

				$opcion_cuota_ccc_menos_seguro = round($opcion_cuota_ccc) * (100.00 - $porcentaje_seguro) / 100.00;
				
				$valor_credito_ccc = $opcion_cuota_ccc_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
				
				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$descuento_cuota_manejo_ccc = floor($valor_credito_ccc / 1000000) * $cuota_manejo;
					$descuento_dias_ajuste_ccc = $valor_credito_ccc * ($tasa_interes / 100.00) / 30 * $dias_ajuste;
					$descuento_seguro_ccc = $valor_credito_ccc / 1000000 * $plazo * $seguro;

					$otros_descuentos = $descuento_cuota_manejo_ccc + $descuento_dias_ajuste_ccc + $descuento_seguro_ccc;
				}

				$opcion_desembolso_ccc = ($valor_credito_ccc * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;

				$opcion_cuota_cmp = $opcion_cuota_base + $total_cuota_max;

				$opcion_cuota_cmp_menos_seguro = round($opcion_cuota_cmp) * (100.00 - $porcentaje_seguro) / 100.00;

				$valor_credito_cmp = $opcion_cuota_cmp_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));

				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$descuento_cuota_manejo_cmp = floor($valor_credito_cmp / 1000000) * $cuota_manejo;
					$descuento_dias_ajuste_cmp = $valor_credito_cmp * ($tasa_interes / 100.00) / 30 * $dias_ajuste;
					$descuento_seguro_cmp = $valor_credito_cmp / 1000000 * $plazo * $seguro;

					$otros_descuentos = $descuento_cuota_manejo_cmp + $descuento_dias_ajuste_cmp + $descuento_seguro_cmp;
				}

				$opcion_desembolso_cmp = ($valor_credito_cmp * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;

				$opcion_cuota_cso = $opcion_cuota_base + $total_cuota_max;

				$opcion_cuota_cso_menos_seguro = round($opcion_cuota_cso) * (100.00 - $porcentaje_seguro) / 100.00;

				$valor_credito_cso = $opcion_cuota_cso_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));

				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$descuento_cuota_manejo_cso = floor($valor_credito_cso / 1000000) * $cuota_manejo;
					$descuento_dias_ajuste_cso = $valor_credito_cso * ($tasa_interes / 100.00) / 30 * $dias_ajuste;
					$descuento_seguro_cso = $valor_credito_cso / 1000000 * $plazo * $seguro;

					$otros_descuentos = $descuento_cuota_manejo_cso + $descuento_dias_ajuste_cso + $descuento_seguro_cso;
				}

				$opcion_desembolso_cso = ($valor_credito_cso * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;

				$desembolso_cliente = $opcion_desembolso_ccc;

				$cuota_fondeo = $opcion_cuota_ccc_menos_seguro;

				$cuota_venta = $cuota_fondeo * (1 - ($cobertura / 100.00));

				$valor_venta_fondeo = $cuota_venta * ((pow(1 + ($tasa_nominal_fondeo / 100.00), $plazo) - 1) / (($tasa_nominal_fondeo / 100.00) * pow(1 + ($tasa_nominal_fondeo / 100.00), $plazo)));

				$valor_credito1 = $cuota_fondeo * ((pow(1 + ($tasa_interes_maxima / 100.00), $plazo) - 1) / (($tasa_interes_maxima / 100.00) * pow(1 + ($tasa_interes_maxima / 100.00), $plazo)));

				$costo_operacion_originadora1 = $valor_credito1 * $porcentaje_sobre_desm2;

				$valor_credito2 = $cuota_fondeo * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));

				$margen2 = $valor_venta_fondeo - $valor_credito2;

				$costo_operacion_fabrica2 = $valor_credito2 * $porcentaje_sobre_desm1;

				$costo_operacion_originadora2 = ($margen2 - $costo_operacion_fabrica2) * $porcentaje_sobre_util;

				if ($costo_operacion_originadora2 < 0)
					$porcentaje_utilidad_originadora = 0;
				else
					if ($costo_operacion_originadora1)
						$porcentaje_utilidad_originadora = $costo_operacion_originadora2 / $costo_operacion_originadora1;
					else
						$porcentaje_utilidad_originadora = 0;

				if ($desembolso_cliente >= 0)
				{
					$suma_al_presupuesto = $desembolso_cliente * $porcentaje_utilidad_originadora;
				}

				$puntaje_decision = 0;

				if (strtoupper($nivel_contratacion) != "PROPIEDAD")
					$puntaje_decision += $puntaje_datacredito;

				if (strtoupper($nivel_contratacion) == "PERIODO DE PRUEBA" || (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]))
					$puntaje_decision -= $puntaje_datacredito;

				if  ((strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"]) && (strtoupper($nivel_contratacion) == "PROVISIONAL VACANTE DEFINITIVA" || strtoupper($nivel_contratacion) == "PROVISIONAL VACANTE TEMPORAL") && $fila["fecha_inicio_labor"] < "2004-01-01")
					$puntaje_decision -= $puntaje_datacredito;

				if (strtoupper($embargo_actual) != "NO")
					$puntaje_decision += $puntaje_datacredito;

				//if ($historial_embargos > 20)
					//$puntaje_decision += $puntaje_datacredito;

				if (strtoupper($embargo_alimentos) != "NO")
					$puntaje_decision += $puntaje_datacredito;

				if (strtoupper($descuentos_por_fuera) != "NO")
					$puntaje_decision += 0.1;

				//cartera_mora
				$puntaje_decision += 0;

				//valor_cartera_mora
				$puntaje_decision += 0;

				//puntaje_datacredito
				$puntaje_decision += 0;

				if ($desembolso_cliente < 0)
					$puntaje_decision += 3;

				//puntaje_cifin
				$puntaje_decision += 0;

				//valor_descuentos_por_fuera
				$puntaje_decision += 0;

				if ($puntaje_decision > 1)
					$decision = $label_negado;
				else
					$decision = $label_viable;


	if ($puntaje_decision <= 1 && $opcion_desembolso_cli_cli >= 12000)
				
//echo "insert into cazador (cedula , nombre, pagaduria, institucion, cargo, grado, salario_basico, ingresos, egresos, neto_pagar, nivel_educativo, direccion, telefono, mail, fecha_nacimiento, nivel_contratacion, ciudad, estado_cargue, sexo, ciudad_labora, fecha_inicio_labor, desembolso_cliente, decision, fecha_asignacion) VALUES ('".$fila["cedula"]."', '".$fila["nombre"]."', '".$fila["pagaduria"]."', '".$fila["institucion"]."', '".$fila["cargo"]."', '".$fila["grado"]."', '".$fila["salario_basico"]."', '".$fila["ingresos"]."', '".$fila["egresos"]."', '".$fila["neto_pagar"]."', '".$fila["nivel_educativo"]."', '".$fila["direccion"]."', '".$fila["telefono"]."', '".$fila["mail"]."', '".$fila["fecha_nacimiento"]."', '".$fila["nivel_contratacion"]."', '".$fila["ciudad"]."', '".$fila["estado_cargue"]."', '".$fila["sexo"]."', '".$fila["ciudad_labora"]."', '".$fila["fecha_inicio_labor"]."', '".$opcion_cuota_ccc."', '".$decision."', now())<br>";
//exit;
				sqlsrv_query("insert into cazador (cedula , nombre, pagaduria, institucion, cargo, grado, salario_basico, ingresos, egresos, neto_pagar, nivel_educativo, direccion, telefono, mail, fecha_nacimiento, nivel_contratacion, ciudad, estado_cargue, sexo, ciudad_labora, fecha_inicio_labor, desembolso_cliente, valor_credito1, valor_credito2, libre_inversion, decision, fecha_asignacion) VALUES ('".$fila["cedula"]."', '".$fila["nombre"]."', '".$fila["pagaduria"]."', '".$fila["institucion"]."', '".$fila["cargo"]."', '".$fila["grado"]."', '".$fila["salario_basico"]."', '".$fila["ingresos"]."', '".$fila["egresos"]."', '".$fila["neto_pagar"]."', '".$fila["nivel_educativo"]."', '".$fila["direccion"]."', '".$fila["telefono"]."', '".$fila["mail"]."', '".$fila["fecha_nacimiento"]."', '".$fila["nivel_contratacion"]."', '".$fila["ciudad"]."', '".$fila["estado_cargue"]."', '".$fila["sexo"]."', '".$fila["ciudad_labora"]."', '".$fila["fecha_inicio_labor"]."', '".$opcion_cuota_ccc."', '".$opcion_desembolso_ccc."', '".$valor_credito2."', '".$opcion_desembolso_cli."', '".$decision."', now())", $link);


				

            }

      echo date("Y-m-d H:i:s");

?>

<?php include("bottom.php"); ?>