<?php 
; 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include ('../functions.php');
include ('../function_blob_storage.php');
include ('../controles/validaciones.php');
include ('porcentajes_seguro.php');


$link = conectar_utf();
if (!$_SESSION["S_LOGIN"]) {
	exit;
}


if (!$_REQUEST["id_simulacion"] && ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'")) {
	exit;
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
  CURLOPT_POSTFIELDS =>'{"id_simulacion":"'.$_REQUEST["id_simulacion"].'","origen":"SIMULADOR","usuario":"'.$_SESSION["S_IDUSUARIO"].'","operacion":"Crear Simulaciones Consultas Log"}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));
$response = curl_exec($curl);

curl_close($curl);


//Quitar Edicion de tasas Para Comercial
	$attr_tasa_interes = "";
	if ($_SESSION["S_TIPO"] == "COMERCIAL"){ $attr_tasa_interes = 'onmousedown="return false;"'; }


if (!$_REQUEST["id_simulacion"]) {
	$queryDB = "SELECT salario_minimo from salario_minimo where ano = YEAR(getdate())";

	$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	if (!(sqlsrv_num_rows($rs1)))
	{
		echo "<script>alert('No se ha establecido el salario minimo para el presente ano. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
		
		exit;
	}
}

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && !$_REQUEST["id_simulacion"])
{
	$queryDB = "SELECT * from oficinas_usuarios where id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (!(sqlsrv_num_rows($rs1)))
	{
		echo "<script>alert('El usuario no tiene oficina asociada. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
		
		exit;
	}
}

?>
<?php include("top.php"); ?>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="../plugins/toastr/toastr.min.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<?php
$link = conectar_utf();

$parametros = sqlsrv_query($link, "SELECT * from parametros where tipo = 'SIMULADOR' order by id");

$j = 0;

while ($fila1 = sqlsrv_fetch_array($parametros, SQLSRV_FETCH_ASSOC)) {
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
$aval = $parametro[9];
$aval_producto = $parametro[10];
$plazo_maximo = $parametro[11];
$plazo_maximo_administrador = $parametro[12];
$descuento_freelance2 = $parametro[13];
$descuento_freelance3 = $parametro[14];
$descuento_producto1 = $parametro[15];
$iva = intval($parametro[16]);
$porcentaje_aportes_activos = $parametro[17];
$porcentaje_comision = $parametro[18];
$descuento1 = $parametro[19];
$descuento2 = $parametro[20];
$descuento3 = $parametro[21];
$descuento4 = $parametro[22];
$descuento5 = $parametro[23];
$descuento6 = $parametro[24];
$porcentaje_aportes_pensionados = $parametro[25];
$porcentaje_incorporacion = $parametro[26];
$porcentaje_sobre_util = $parametro[27];
$porcentaje_sobre_desm1 = $parametro[28];
$porcentaje_sobre_desm2 = $parametro[29];
$puntaje_cifin_minimo = $parametro[30];
$puntaje_datacredito_minimo = $parametro[31];
$salario_minimo = $parametro[32];
$seguro = $parametro[33];
$tasa_efectiva_fondeo = $parametro[34];
$tasa_interes_maxima = $parametro[35];
$tasa_interes_a = $parametro[36];
$tasa_interes_b = $parametro[37];
$tasa_interes_c = $parametro[38];
$tasa_usura = $parametro[39];

$aplicar_servicio_nube = $parametro[40];
$valor_servicio_nube = $parametro[41];
$sin_iva_servicio_nube = $parametro[43];
//Servicio Nubeeeeee
$descuento8_valor = 0;
$descuento9_valor = 0;
$descuento10_valor = 0;

$aplicar_seguro_parcial = $parametro[42];

$descuento_producto0 = $descuento1;

if ($_SESSION["S_TIPO"] == "COMERCIAL")
	$id_comercial = $_SESSION["S_IDUSUARIO"];

if ($_REQUEST["id_comercial"])
	$id_comercial = $_REQUEST["id_comercial"];

if ($_REQUEST["telemercadeo"] != "1")
	$_REQUEST["telemercadeo"] = "0";

if ($_REQUEST["telemercadeo"] == "1") {
	$telemercadeo = "1";
	$telemercadeo_checked = " checked";
} else {
	$telemercadeo = "0";
	$telemercadeo_checked = "";
}

if ($id_comercial) {
	$es_freelance = sqlsrv_query($link, "SELECT * from usuarios where id_usuario = '".$id_comercial."' and (freelance = '1' OR outsourcing = '1')" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if (sqlsrv_num_rows($es_freelance))	{
		$descuento2 = $descuento_freelance2;
		$descuento3 = $descuento_freelance3;		
		$inhabilita_telemercadeo = 1;
	}
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$inhabilita_telemercadeo = "0";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO") {
		$inhabilita_telemercadeo = "0";		
		$telemercadeo = "1";
		$telemercadeo_checked = " checked";
	}
}

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR")
	$plazo_maximo = $plazo_maximo_administrador;

if ($_SESSION["FUNC_TASASCOMBO"]) {
	$tasa_interes_maxima = $tasa_interes_c;
	$tasa_interes_c_selected = " selected";
}

$tasa_nominal_fondeo = (pow(1 + $tasa_efectiva_fondeo / 100.00, (1.00 / 12.00)) - 1) * 100.00;

$maxcd = sqlsrv_query($link, "SELECT valor from parametros where codigo = 'MAXCD'");

$fila1 = sqlsrv_fetch_array($maxcd, SQLSRV_FETCH_ASSOC);

$maxcd = $fila1["valor"];

if ($_SESSION["S_MAXCONSDIARIAS"])
	$maxcd = $_SESSION["S_MAXCONSDIARIAS"];

$cedula = $_REQUEST["cedula"];
$cartera_mora = "NO";
$cartera_mora_no = " selected";

for ($i = 1; $i <= 100; $i++)
{
	$id_entidad[$i - 1] = "";
	$entidad[$i - 1] = "";
	$cuota[$i - 1] = "0";
	$valor_pagar[$i - 1] = "0";
	
	if ($_SESSION["FUNC_MUESTRACAMPOS1"])
		$se_compra_si[$i - 1] = " selected";

	if ($_SESSION["FUNC_MUESTRACAMPOS2"])
		$se_compra_no[$i - 1] = " selected";

	$fecha_vencimiento[$i - 1] = "";
	$nombre_grabado[$i - 1] = "";
	
	$estadocarta_nos[$i - 1] = " selected";
}

$ultimo_consecutivo_compra_cartera = 10;

$opcion_credito_ccc = " checked";

$puntaje_negado = 400;

if ($_REQUEST["cedula"])
{
	$consultas_realizadas = sqlsrv_query($link, "SELECT * from log_consultas where id_usuario = '".$_SESSION["S_IDUSUARIO"]."'  and fecha_creacion >= GETDATE() and fecha_creacion <= GETDATE()", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if ((sqlsrv_num_rows($consultas_realizadas) < $maxcd) || (!$_SESSION["FUNC_MAXCONSDIARIAS"]))
	{
		if (!$_REQUEST["pagad"])
		{
			$queryDB = "SELECT em.pagaduria from empleados em INNER JOIN pagadurias pa ON em.pagaduria = pa.nombre where em.cedula = '".$_REQUEST["cedula"]."'";
			
			if ($_SESSION["S_SECTOR"])
			{
				$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
			}
			
			$empleado = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($empleado) > 1)
			{
				echo "<script>window.open('simulador_pagadurias.php?tipo=".$_REQUEST["tipo"]."&cedula=".$_REQUEST["cedula"]."&id_comercial=".$_REQUEST["id_comercial"]."&telemercadeo=".$_REQUEST["telemercadeo"]."', 'SELECCIONARPAGADURIA','toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');</script>";
				
				exit;
			}
			elseif (sqlsrv_num_rows($empleado) == 1)
			{
				$fila = sqlsrv_fetch_array($empleado, SQLSRV_FETCH_ASSOC);
				
				$_REQUEST["pagad"] = $fila["pagaduria"];
			}
		}
		
		$rs1 = sqlsrv_query($link, "SELECT sector from pagadurias where nombre = '".$_REQUEST["pagad"]."'");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$sector = $fila1["sector"];
		
		if ($sector == "PRIVADO")
		{
			$descuento_producto0 = $aval;
			
			$descuento1 = $aval;
			
			$descuento_producto1 = $aval_producto;
			
			$sufijo_sector = "_privado";
		}
		
		$queryDB = "SELECT em.*, pp.pa, ((DATEPART(YEAR , em.fecha_nacimiento) +".$edad_maxima_activos.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_activos.",em.fecha_nacimiento)) - DATEPART(MONTH , GETDATE()))as meses_antes_activos, 
        ((DATEPART(YEAR , em.fecha_nacimiento) +".$edad_maxima_pnsionados.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_pensinado.",em.fecha_nacimiento)) - DATEPART(MONTH , GETDATE())) as meses_antes_pensionados, 
		((DATEPART(YEAR,  em.fecha_nacimiento )+ ".$edad_maxima_administrativos_hombres.")- DATEPART(YEAR , GETDATE())) * 12 + ((DATEPART(MONTH , em.fecha_nacimiento )+  ".$edad_maxima_administrativos_hombres." ) - DATEPART(MONTH , GETDATE())) as meses_antes_administrativos_hombres, 
		((DATEPART(YEAR,  em.fecha_nacimiento )+ ".$edad_maxima_administrativos_mujeres.")- DATEPART(YEAR , GETDATE())) * 12 + ((DATEPART(MONTH , em.fecha_nacimiento )+  ".$edad_maxima_administrativos_mujeres." ) - DATEPART(MONTH , GETDATE())) as meses_antes_administrativos_mujeres from ".$prefijo_tablas."empleados em INNER JOIN pagadurias pa ON em.pagaduria = pa.nombre LEFT JOIN pagaduriaspa pp ON em.pagaduria = pp.pagaduria where em.cedula = '".$_REQUEST["cedula"]."' AND em.pagaduria = '".$_REQUEST["pagad"]."'";
		
		if ($_SESSION["S_SECTOR"])
		{
			$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		}
		
		$empleado = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($empleado))
		{
			$fila = sqlsrv_fetch_array($empleado, SQLSRV_FETCH_ASSOC);
			
			sqlsrv_query($link, "insert into log_consultas (id_usuario, cedula, nombre, pagaduria, ciudad, institucion, fecha_creacion) values ('".$_SESSION["S_IDUSUARIO"]."', '".$fila["cedula"]."', '".utf8_encode(utf8_decode($fila["nombre"]))."', '".utf8_encode(utf8_decode($fila["pagaduria"]))."', '".utf8_encode(utf8_decode($fila["ciudad"]))."', '".utf8_encode(utf8_decode($fila["institucion"]))."', GETDATE())");
			
			$omitir_validacion_30_dias = 1;

			// Para superar la coyontura de prospecciones a reproceso se baja el limite de 30 a 10 dias
			$existe_simulacion = sqlsrv_query($link, "SELECT id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."' AND DATEDIFF(getdate(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST')" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (!sqlsrv_num_rows($existe_simulacion) || $omitir_validacion_30_dias)
			{
				$plazo = $plazo_maximo;
				$plazo_maximo_segun_edad = $plazo_maximo;
				
				$nombre = ($fila["nombre"]);
				$fecha_estudio = date("Y-m-d");
				$pagaduria = ($fila["pagaduria"]);
				$pa = ($fila["pa"]);
				$ciudad = ($fila["ciudad"]);
				$institucion = ($fila["institucion"]);
				$nivel_educativo = ($fila["nivel_educativo"]);
				$fecha_nacimiento = $fila["fecha_nacimiento"];
				$nivel_contratacion = (strtoupper($fila["nivel_contratacion"]));
				$meses_antes = "0";
				$fecha_inicio_labor = $fila["fecha_inicio_labor"];
				$telefono = ($fila["telefono"]);
				$celular = "";
				$direccion = ($fila["direccion"]);
				$ciudad_residencia = "";
				$mail = ($fila["mail"]);
				$medio_contacto = $fila["medio_contacto"];
				$sexo = ($fila["sexo"]);
				$rs_salario_minimo = sqlsrv_query($link, "SELECT salario_minimo from salario_minimo where ano = YEAR('".$fecha_estudio."')");
				
				$fila_salario_minimo = sqlsrv_fetch_array($rs_salario_minimo, SQLSRV_FETCH_ASSOC);
				
				$salario_minimo = $fila_salario_minimo["salario_minimo"];
				
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
					
					if (strtoupper($nivel_contratacion) != "PENSIONADO")
					{
						if ($meses_antes < $plazo_maximo)
						{
							$plazo = $meses_antes;
							$plazo_maximo_segun_edad = $meses_antes;
						}
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
				
				$id1 = explode("'0', '", $_SESSION["S_IDUNIDADNEGOCIO"]);
				
				$id2 = explode("'", $id1[1]);
				
				$id_unidad_negocio = $id2[0];
				
				$sin_seguro = "0";
				
				$fidelizacion = "0";
		
				if (!$_SESSION["FUNC_TASASPLAZO"])
				{
					$tasa_interes = $tasa_interes_maxima;
				}
				else
				{
					$rs_tasa = sqlsrv_query($link, "select id_tasa from tasas".$sufijo_sector." where plazoi <= '".$plazo."' AND plazof >= '".$plazo."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($rs_tasa))
					{
						$fila_tasa = sqlsrv_fetch_array($rs_tasa, SQLSRV_FETCH_ASSOC);
						
						$queryDB = "SELECT top 1 cast (t2.tasa_interes as float) + 0 as tasa_interes, cast(t2.descuento1 as float) + 0 as descuento1,cast(t2.descuento1_producto as float) + 0 as descuento1_producto, cast(t2.descuento2 as float) + 0 as descuento2, cast(t2.descuento3 AS FLOAT) + 0 as descuento3 from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila_tasa["id_tasa"]."'";
						
						$queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
						
						$queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
						
						if (strtoupper($nivel_contratacion) == "PENSIONADO")
							$queryDB .= " OR t2.solo_pensionados = '1'";
						else
							$queryDB .= " OR t2.solo_activos = '1'";
						
						$queryDB .= ") order by t2.tasa_interes DESC";
						
						$rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						
						if (sqlsrv_num_rows($rs_tasa2))
						{
							$fila_tasa2 = sqlsrv_fetch_array($rs_tasa2, SQLSRV_FETCH_ASSOC);
							
							$tasa_interes = $fila_tasa2["tasa_interes"];
							
							$tasa_interes_maxima = $tasa_interes;
							
							$descuento_producto0 = $fila_tasa2["descuento1"];
							
							$descuento1 = $fila_tasa2["descuento1"];
							
							$descuento_producto1 = $fila_tasa2["descuento1_producto"];
							
							$descuento2 = $fila_tasa2["descuento2"];
							
							$descuento3 = $fila_tasa2["descuento3"];
						}
						else
						{
							$tasa_interes = 0;
							
							$tasa_interes_maxima = 0;
							
							$descuento_producto0 = 0;
							
							$descuento1 = 0;
							
							$descuento_producto1 = 0;
							
							$descuento2 = 0;
							
							$descuento3 = 0;
						}
					}
					else
					{
						$tasa_interes = 0;
						
						$tasa_interes_maxima = 0;
						
						$descuento_producto0 = 0;
						
						$descuento1 = 0;
						
						$descuento_producto1 = 0;
						
						$descuento2 = 0;
						
						$descuento3 = 0;
					}
					
					if ($sector == "PRIVADO")
						$descuento3 += $descuento1 * $iva / 100;
				}
				
				$valor_por_millon_seguro = 0;
				$fecha_estudio_date = new DateTime($fecha_estudio);
				$fecha_nacimiento_date = new DateTime($fecha_nacimiento);
				
				if(date_create($fecha_estudio) > date_create("2023-12-12")){

					$diff_fechas = $fecha_nacimiento_date->diff($fecha_estudio_date);
					$rs = sqlsrv_query($link, "SELECT * FROM edad_rango_seguro WHERE (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin) OR (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin)");

					$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

					if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
						$valor_por_millon_seguro = $fila["valor_por_millon_parcial"];
					}else{
						$valor_por_millon_seguro = $fila["valor_por_millon"];
					}
				}

				if($fecha_nacimiento_date < date_create("2024-01-01") && $valor_por_millon_seguro == 0){
				
					$rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf, valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial, valor_por_millon_seguro_colpensiones_parcial from unidades_negocio where id_unidad = '".$id_unidad_negocio."'");
					
					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
					
					if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]){
						$porcentaje_aportes = $porcentaje_aportes_pensionados;
						
						if (strtoupper($pagaduria) == "COLPENSIONES"){
							if($aplicar_seguro_parcial){
								$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones_parcial"];
							}else{
								$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones"];
							}
						}
						else{
							if($aplicar_seguro_parcial){
								$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados_parcial"];
							}else{
								$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados"];
							}
						}
					}
					else{
					$porcentaje_aportes = $porcentaje_aportes_activos;

					if($aplicar_seguro_parcial){
						$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos_parcial"];
					}else{
						$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos"];
						}
					}
				}
				
				if ($plazo || $aplicar_seguro_parcial){
					$porcentaje_seguro = PorcentajeSeguro($valor_por_millon_seguro, $plazo, $tasa_interes, 0, 0, $aplicar_seguro_parcial);
				}
				else{
					$porcentaje_seguro = 0;
				}
				
				$porcentaje_extraprima = 0;
				
				$formulario_seguro = "0";
				
				if (!$fila1["gmf"])
					$descuento4 = 0;
				
				$suma_descuentos = $descuento1 + $descuento2 + $descuento3 + $descuento4;
				
				$descuentos_adicionales = sqlsrv_query($link, "SELECT * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagad"]."' and estado = '1' order by id_descuento");
				
				while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales, SQLSRV_FETCH_ASSOC))
				{
					$suma_descuentos += $fila1["porcentaje"];
				}
				
				$sin_aportes = "0";
				
				$salario_basico = $fila["salario_basico"];
				
				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$rs_adicionales = sqlsrv_query($link, "SELECT SUM(valor) as s from ".$prefijo_tablas."adicionales where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."'");
					
					$fila_adicionales = sqlsrv_fetch_array($rs_adicionales, SQLSRV_FETCH_ASSOC);
					
					$adicionales = $fila_adicionales["s"];
				}
				
				if ($_SESSION["FUNC_MUESTRACAMPOS2"] && strtoupper($fila["ciudad_labora"]) == "VALLEDUPAR")
				{
					$rs_bonificacion = sqlsrv_query($link, "select ingreso from ".$prefijo_tablas."ingresos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."' AND UPPER(entidad) = 'PRIMA ANTIGUEDAD EMPLEADOS MUNICIPALES'");
					
					$fila_bonificacion = sqlsrv_fetch_array($rs_bonificacion, SQLSRV_FETCH_ASSOC);
					
					$bonificacion = $fila_bonificacion["ingreso"];
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
					if ((strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]) || $sector == "PRIVADO")
						$salario_libre = $ingresos_menos_aportes / 2;
					else
						$salario_libre = $salario_minimo;
				}
				else
				{
					$salario_libre = $ingresos_menos_aportes / 2;
					
					if (!((strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]) || $sector == "PRIVADO"))
					{
						if ($salario_libre < $salario_minimo)
							$salario_libre = $salario_minimo;
					}
				}
				
				$embargos = sqlsrv_query($link, "SELEcT * from ".$prefijo_tablas."embargos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."'");
				
				$embargo_actual = "NO";
				$embargo_actual_no = " selected";
				
				$embargo_alimentos = "NO";
				
				$embargo_centrales = "NO";
				$embargo_centrales_no = " selected";
				
				$j = 0;
				
				while ($fila1 = sqlsrv_fetch_array($embargos, SQLSRV_FETCH_ASSOC))
				{
					if (!$fila1["fechafin"])
					{
						$embargo_actual = "SI";
						$embargo_actual_no = "";
						$embargo_actual_si = " selected";
					}
					
					if (strpos(strtoupper($fila1["tipoembargo"]), "ALIMENTO") !== false)
						$embargo_alimentos = "SI";
					
					$j++;
				}
				
				$historial_embargos = $j;
				
				$rechazos = sqlsrv_query($link, "SELECT * from ".$prefijo_tablas."rechazos where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagad"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($rechazos))
				{
					$descuentos_por_fuera = "SI";
				}
				else
				{
					$descuentos_por_fuera = "NO";
				}
				
				$puntaje_cifin = "-1";
				$puntaje_datacredito = "-1";
				
				$tipo_credito = "CREDITO NORMAL";
				
				$tipo_producto = "0";
				
				$total_cuota = 0;
				$total_cuota_max = 0;
				
				$total_se_compra = 0;
				
				$descuentos = sqlsrv_query($link, "SELECT des.*, CASE WHEN ent.dias_entrega IS NULL THEN 0 ELSE ent.dias_entrega END as dias_entrega, CASE WHEN ent.dias_vigencia IS NULL THEN 0 ELSE ent.dias_vigencia END as dias_vigencia from ".$prefijo_tablas."descuentos des left join entidades ent ON des.entidad = ent.entidad where des.cedula = '".$_REQUEST["cedula"]."' AND des.pagaduria = '".$_REQUEST["pagad"]."' order by ent.dias_entrega DESC, des.codigo");
				
				$min_dias_vigencia = 1000;
				
				$j = 0;
				
				while ($fila1 = sqlsrv_fetch_array($descuentos, SQLSRV_FETCH_ASSOC))
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
					
					$fecha_entrega[$j] = "";
					$fecha_vencimiento[$j] = "";
					
					if ($dias_vigencia[$j] < $min_dias_vigencia)
						$min_dias_vigencia = $dias_vigencia[$j];
					
					$total_se_compra++;
					
					$j++;
				}
				
				$dia_confirmacion = "";
				$dia_vencimiento = "";
				$status = "";

				if ($total_ingresos < $salario_minimo * 2)
				{
					if ((strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]) || $sector == "PRIVADO")
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
					if ($tasa_interes)
						$valor_credito_cli = $opcion_cuota_cli_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
					else
						$valor_credito_cli = 0;
					
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
				
				if ($tasa_interes)
					$valor_credito_ccc = $opcion_cuota_ccc_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
				else
					$valor_credito_ccc = 0;
				
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

				if ($tasa_interes)
					$valor_credito_cmp = $opcion_cuota_cmp_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
				else
					$valor_credito_cmp = 0;

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

				if ($tasa_interes)
					$valor_credito_cso = $opcion_cuota_cso_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
				else
					$valor_credito_cso = 0;

				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
				{
					$descuento_cuota_manejo_cso = floor($valor_credito_cso / 1000000) * $cuota_manejo;
					$descuento_dias_ajuste_cso = $valor_credito_cso * ($tasa_interes / 100.00) / 30 * $dias_ajuste;
					$descuento_seguro_cso = $valor_credito_cso / 1000000 * $plazo * $seguro;

					$otros_descuentos = $descuento_cuota_manejo_cso + $descuento_dias_ajuste_cso + $descuento_seguro_cso;
				}

				$opcion_desembolso_cso = ($valor_credito_cso * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;

				$sin_retanqueos = number_format($opcion_desembolso_ccc, 0, ".", ",");

				$desembolso_cliente = $opcion_desembolso_ccc;

				$cuota_fondeo = $opcion_cuota_ccc_menos_seguro;

				$cuota_venta = $cuota_fondeo * (1 - ($cobertura / 100.00));

				$valor_venta_fondeo = $cuota_venta * ((pow(1 + ($tasa_nominal_fondeo / 100.00), $plazo) - 1) / (($tasa_nominal_fondeo / 100.00) * pow(1 + ($tasa_nominal_fondeo / 100.00), $plazo)));

				if ($tasa_interes)
					$valor_credito1 = $cuota_fondeo * ((pow(1 + ($tasa_interes_maxima / 100.00), $plazo) - 1) / (($tasa_interes_maxima / 100.00) * pow(1 + ($tasa_interes_maxima / 100.00), $plazo)));
				else
					$valor_credito1 = 0;

				$costo_operacion_originadora1 = $valor_credito1 * $porcentaje_sobre_desm2;

				if ($tasa_interes)
					$valor_credito2 = $cuota_fondeo * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
				else
					$valor_credito2 = 0;

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
					$puntaje_decision += $puntaje_negado;

				if (strtoupper($nivel_contratacion) == "PERIODO DE PRUEBA" || strtoupper($nivel_contratacion) == "PROVISIONAL" || (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]))
					$puntaje_decision -= $puntaje_negado;

				if  ((strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"]) && (strtoupper($nivel_contratacion) == "PROVISIONAL VACANTE DEFINITIVA" || strtoupper($nivel_contratacion) == "PROVISIONAL VACANTE TEMPORAL") && $fila["fecha_inicio_labor"] < "2004-01-01")
					$puntaje_decision -= $puntaje_negado;

				//if (strtoupper($embargo_actual) != "NO")
					//$puntaje_decision += $puntaje_negado;

				//if ($historial_embargos > 20)
					//$puntaje_decision += $puntaje_negado;

				if (strtoupper($embargo_alimentos) != "NO")
					$puntaje_decision += $puntaje_negado;

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
				
				$decision = $label_viable;
				$decision_viable = " selected";
				
				if ($puntaje_decision > 1)
					$decision_sistema = $label_negado;
				else
					$decision_sistema = $label_viable;

				$valor_credito = $valor_credito2;

				$resumen_ingreso = $costo_operacion_originadora2;

				$incor = ($porcentaje_incorporacion / 100.00) * $desembolso_cliente * $porcentaje_utilidad_originadora;

				$comision = ($porcentaje_comision / 100.00) * $desembolso_cliente * $porcentaje_utilidad_originadora;

				$utilidad_neta = $resumen_ingreso - $incor - $comision;

				if ($valor_credito2)
					$sobre_el_credito = ($utilidad_neta / $valor_credito2) * 100.00;
				else
					$sobre_el_credito = 0;
					
				$descuento1_valor = $valor_credito * $descuento1 / 100;
				$descuento2_valor = $valor_credito * $descuento2 / 100;
				$descuento3_valor = $valor_credito * $descuento3 / 100;
				$descuento4_valor = $valor_credito * $descuento4 / 100;
				
				$bloqueo_cuota = "0";
			}
			else
			{
				echo "<script>alert('Hay un estudio ingresado hace menos de 30 dias asociado a esa cedula');</script>";
			}
		}
		else
		{
			sqlsrv_query($link, "insert into log_consultas (id_usuario, cedula, nombre, pagaduria, ciudad, institucion, fecha_creacion) values ('".$_SESSION["S_IDUSUARIO"]."', '".$_REQUEST["cedula"]."', 'No hay informacion asociada a esa cedula', '', '', '', GETDATE())");

			echo "<script>alert('No hay informacion asociada a esa cedula');</script>";
		}
	}
	else
	{
		echo "<script>alert('No es posible realizar la consulta. Contacte al Administrador del sistema');</script>";
	}
}

if ($_REQUEST["id_simulacion"])
{
	$queryDB = "SELECT si.*, so.sexo, us.freelance, us.outsourcing, pa.sector, ofi.nombre as oficina, so.nombre1, so.estado_civil, so.nombre_conyugue, so.ocupacion, so.ingresos_laborales, so.nombre_familiar, so.moneda_extranjera, so.ciudadania_extranjera, so.instruccion_desembolso, so.celular, so.direccion, so.ciudad as ciudad_residencia, so.email, so.clave, se.cod_interno, cau.nombre as nombre_causal, ca.nombre as nombre_caracteristica from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN planes_seguro ps ON si.id_plan_seguro = ps.id_plan LEFT JOIN causales cau ON si.id_causal = cau.id_causal LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
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
			$queryDB .= " AND si.telemercadeo IN ('0','1')";
		
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
	
	//echo($queryDB);

	$simulacion = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$validacion_otp = 0;

	if (sqlsrv_num_rows($simulacion))
	{
		$fila = sqlsrv_fetch_array($simulacion, SQLSRV_FETCH_ASSOC);
		$nombres_asegurabilidad = $fila['nombre1'].' '.$fila['nombre2'];
    $apellidos_asegurabilidad = $fila['apellido1'].' '.$fila['apellido2'];
    $sexo_asegurabilidad = $fila['sexo'];
    $tipo_documento_asegurabilidad =$fila['tipo_documento'];
    $estatura_asegurabilidad=$fila['estatura'];
    $peso_asegurabilidad=$fila['peso'];
    $cedula_asegurabilidad = $fila['cedula'];
		$id_analista_riesgo_operativoh=$fila["id_analista_riesgo_operativo"];
		$inconsistencia = ValidaValorCredito($_REQUEST["id_simulacion"], $link);
		$validacion_otp = $fila["otp_verificado"];
		
		if (!$inconsistencia)
			$inconsistencia = ValidaValorDesembolso($_REQUEST["id_simulacion"], $link);
		
		if ($inconsistencia)
		{
			sqlsrv_query($link, "INSERT into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '"."El sistema detecta que el credito actual parece tener inconsistencia en sus valores. Se solicita al usuario validar antes de guardar la simulacion"."', 'system', GETDATE())");
			
			echo "<script>alert('Este credito parece tener inconsistencia en sus valores, por favor valide antes de guardar la simulacion')</script>";
		}
		
		$tipo_comercial = "PLANTA";
		
		if ($fila["freelance"])
			$tipo_comercial = "FREELANCE";
		
		if ($fila["outsourcing"])
			$tipo_comercial = "OUTSOURCING";
		
		if ($fila["telemercadeo"] == "1")
		{
			$telemercadeo = "1";
			$telemercadeo_checked = " checked";
		}
		else
		{
			$telemercadeo = "0";
			$telemercadeo_checked = "";
		}
		
		if ($_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "ADMINISTRADOR")
		{
			$inhabilita_telemercadeo = "1";
		}
		
		$sector = $fila["sector"];
		
		if ($sector == "PRIVADO")
			$sufijo_sector = "_privado";
		
		$id_comercial = $fila["id_comercial"];
		$id_oficina = $fila["id_oficina"];
		$oficina = $fila["oficina"];
		$fecha_estudio = $fila["fecha_estudio"];
		$cedula = $fila["cedula"];
		$nombre = ($fila["nombre"]);
		$pagaduria = ($fila["pagaduria"]);
		$pa = ($fila["pa"]);
		$ciudad = ($fila["ciudad"]);
		$institucion = ($fila["institucion"]);
		$nivel_educativo = ($fila["nivel_educativo"]);
		$fecha_nacimiento = $fila["fecha_nacimiento"];
		$nivel_contratacion = ($fila["nivel_contratacion"]);
		$meses_antes = $fila["meses_antes_65"];
		$fecha_inicio_labor = $fila["fecha_inicio_labor"];
		$telefono = ($fila["telefono"]);
		$celular = ($fila["celular"]);
		$direccion = ($fila["direccion"]);
		$ciudad_residencia = ($fila["ciudad_residencia"]);
		$mail = ($fila["email"]);
		$medio_contacto = $fila["medio_contacto"];
		$salario_minimo = $fila["salario_minimo"];
		$proposito = ($fila["proposito_credito"]);
		$sexo = $fila["sexo"];

		//Servicio Nubeeeeee
		if($fila["iva"] > 0){
			$iva = intval($fila["iva"]);
		}
		$aplicar_servicio_nube = $fila["servicio_nube"];
		$sin_iva_servicio_nube = $fila["sin_iva_servicio_nube"];

		$aplicar_seguro_parcial = $fila["seguro_parcial"];
		
		$plazo_maximo_segun_edad = $plazo_maximo;
		
		$meses_antes_array = explode(" ", $meses_antes);
		
		if (strtoupper($nivel_contratacion) != "PENSIONADO")
		{
			if ($meses_antes_array[0] < $plazo_maximo)
			{
				$plazo_maximo_segun_edad = $meses_antes_array[0];
			}
		}
		
		if ($fila["sin_aportes"] == "1")
		{
			$sin_aportes = "1";
			$sin_aportes_checked = " checked";
		}
		else
		{
			$sin_aportes = "0";
			$sin_aportes_checked = "";
		}
		
		$salario_basico = $fila["salario_basico"];
		
		$id_unidad_negocio = $fila["id_unidad_negocio"];
		
		if ($fila["sin_seguro"] == "1")
		{
			$sin_seguro = "1";
			$sin_seguro_checked = " checked";
		}
		else
		{
			$sin_seguro = "0";
			$sin_seguro_checked = "";
		}
		if ($fila["aumento_salario_minimo"] == "1")
		{
			$aumento_salario_minimo = "1";
			$aumento_salario_checked = " checked";
		}
		else
		{
			$aumento_salario_minimo = "0";
			$aumento_salario_checked = "";
		}
		$fidelizacion = $fila["fidelizacion"];
		
		if (strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
			$porcentaje_aportes = $porcentaje_aportes_pensionados;
		else
			$porcentaje_aportes = $porcentaje_aportes_activos;
		
		$adicionales = $fila["adicionales"];
		$bonificacion = $fila["bonificacion"];
		$total_ingresos = $fila["total_ingresos"];
		$aportes = $fila["aportes"];
		$otros_aportes = $fila["otros_aportes"];
		$total_aportes = $fila["total_aportes"];
		$total_egresos = $fila["total_egresos"];
		$ingresos_menos_aportes = $fila["ingresos_menos_aportes"];
		$salario_libre = $fila["salario_libre"];
		
		$embargo_actual = $fila["embargo_actual"];
		$embargo_actual_si = "";
		$embargo_actual_no = "";
		
		if ($fila["embargo_actual"] == "SI")
			$embargo_actual_si = " selected";
		else
			$embargo_actual_no = " selected";
		
		$historial_embargos = $fila["historial_embargos"];
		
		$embargo_alimentos = $fila["embargo_alimentos"];
		
		$embargo_centrales = $fila["embargo_centrales"];
		$embargo_centrales_si = "";
		$embargo_centrales_no = "";
		
		$fila["embargo_centrales"] == ("SI") ?  $embargo_centrales_si = " selected" :  $embargo_centrales_no = " selected";
		
		$descuentos_por_fuera = $fila["descuentos_por_fuera"];
		
		$cartera_mora = $fila["cartera_mora"];
		$cartera_mora_si = "";
		$cartera_mora_no = "";
		
		if ($fila["cartera_mora"] == "SI")
			$cartera_mora_si = " selected";
		else
			$cartera_mora_no = " selected";
		
		$valor_cartera_mora = $fila["valor_cartera_mora"];
		
		$clave = $fila["clave"];
		
		$puntaje_datacredito = $fila["puntaje_datacredito"];
		$puntaje_cifin = $fila["puntaje_cifin"];
		$valor_descuentos_por_fuera = $fila["valor_descuentos_por_fuera"];
		
		$calif_sector_financiero = $fila["calif_sector_financiero"];
		
		switch ($fila["calif_sector_financiero"])
		{
			case "A":	$calif_sector_financiero_a = " selected"; break;
			case "B":	$calif_sector_financiero_b = " selected"; break;
			case "C":	$calif_sector_financiero_c = " selected"; break;
			case "D":	$calif_sector_financiero_d = " selected"; break;
			case "E":	$calif_sector_financiero_e = " selected"; break;
			case "DR":	$calif_sector_financiero_dr = " selected"; break;
			case "K":	$calif_sector_financiero_k = " selected"; break;
			case "NA":	$calif_sector_financiero_na = " selected"; break;
		}
		
		$calif_sector_real = $fila["calif_sector_real"];
		
		switch ($fila["calif_sector_real"])
		{
			case "A":	$calif_sector_real_a = " selected"; break;
			case "B":	$calif_sector_real_b = " selected"; break;
			case "C":	$calif_sector_real_c = " selected"; break;
			case "D":	$calif_sector_real_d = " selected"; break;
			case "E":	$calif_sector_real_e = " selected"; break;
			case "DR":	$calif_sector_real_dr = " selected"; break;
			case "K":	$calif_sector_real_k = " selected"; break;
			case "NA":	$calif_sector_real_na = " selected"; break;
		}
		
		$calif_sector_cooperativo = $fila["calif_sector_cooperativo"];
		
		switch ($fila["calif_sector_cooperativo"])
		{
			case "A":	$calif_sector_cooperativo_a = " selected"; break;
			case "B":	$calif_sector_cooperativo_b = " selected"; break;
			case "C":	$calif_sector_cooperativo_c = " selected"; break;
			case "D":	$calif_sector_cooperativo_d = " selected"; break;
			case "E":	$calif_sector_cooperativo_e = " selected"; break;
			case "DR":	$calif_sector_cooperativo_dr = " selected"; break;
			case "K":	$calif_sector_cooperativo_k = " selected"; break;
			case "NA":	$calif_sector_cooperativo_na = " selected"; break;
		}
		
		$tasa_interes = $fila["tasa_interes"];
		
		if ($_SESSION["FUNC_TASASCOMBO"])
		{
			$tasa_interes_a_selected = "";
			$tasa_interes_b_selected = "";
			$tasa_interes_c_selected = "";
			
			switch($fila["tasa_interes"])
			{
				case $tasa_interes_a: $tasa_interes_a_selected = " selected"; break;
				case $tasa_interes_b: $tasa_interes_b_selected = " selected"; break;
				case $tasa_interes_c: $tasa_interes_c_selected = " selected"; break;
			}
		}
		
		$plazo = $fila["plazo"];
		
		if (!$_SESSION["FUNC_TASASPLAZO"])
		{
			if ($sector == "PRIVADO")
			{
				$descuento_producto0 = $aval;
				
				$descuento_producto1 = $aval_producto;
			}
		}
		else
		{
			$rs_tasa = sqlsrv_query($link, "SELECT id_tasa from tasas".$sufijo_sector." where plazoi <= '".$plazo."' AND plazof >= '".$plazo."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs_tasa))
			{
				$fila_tasa = sqlsrv_fetch_array($rs_tasa, SQLSRV_FETCH_ASSOC);
				
				$queryDB = "SELECT TOP 1 cast(t2.descuento1 as float)+ 0 as descuento1, cast(t2.descuento1_producto as float) + 0 as descuento1_producto from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila_tasa["id_tasa"]."' AND t2.tasa_interes = '".$tasa_interes."'";
				
				$queryDB .= " AND t2u.id_unidad_negocio = '".$fila["id_unidad_negocio"]."'";
				
				$queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
				
				if (strtoupper($nivel_contratacion) == "PENSIONADO")
					$queryDB .= " OR t2.solo_pensionados = '1'";
				else
					$queryDB .= " OR t2.solo_activos = '1'";
				
				$queryDB .= ") order by t2.tasa_interes DESC ";
				
				$rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($rs_tasa2))
				{
					$fila_tasa2 = sqlsrv_fetch_array($rs_tasa2, SQLSRV_FETCH_ASSOC);
					
					$descuento_producto0 = $fila_tasa2["descuento1"];
					
					$descuento_producto1 = $fila_tasa2["descuento1_producto"];
				}
				else
				{
					$descuento_producto0 = 0;
					
					$descuento_producto1 = 0;
				}
			}
			else
			{
				$descuento_producto0 = 0;
				
				$descuento_producto1 = 0;
			}
		}
		
		$tipo_credito = ($fila["tipo_credito"]);
		$suma_al_presupuesto = $fila["suma_al_presupuesto"];
		
		$valor_seguro = $fila["valor_seguro"];
		$nro_compra_cartera_seguro = $fila["nro_compra_cartera_seguro"];
		$id_plan_seguro = $fila["id_plan_seguro"];
		$nombre_plan_seguro = $fila["nombre_plan_seguro"];
		
		$ultimo_consecutivo_compra_cartera = 1;
		
		$queryDB = "SELECT scc.consecutivo, scc.id_entidad, scc.entidad, scc.cuota, scc.valor_pagar, scc.se_compra, ad.nombre_grabado from simulaciones_comprascartera scc LEFT join adjuntos ad ON scc.id_adjunto = ad.id_adjunto where scc.id_simulacion = '".$_REQUEST["id_simulacion"]."' order by scc.consecutivo";
	
		$rs2 = sqlsrv_query($link, $queryDB);
		
		while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC))
		{
			$ultimo_consecutivo_compra_cartera = $fila2["consecutivo"];
			
			$id_entidad[$fila2["consecutivo"] - 1] = $fila2["id_entidad"];
			$entidad[$fila2["consecutivo"] - 1] = ($fila2["entidad"]);
			$cuota[$fila2["consecutivo"] - 1] = $fila2["cuota"];
			$valor_pagar[$fila2["consecutivo"] - 1] = $fila2["valor_pagar"];
			
			$se_compra_si[$fila2["consecutivo"] - 1] = "";
			$se_compra_no[$fila2["consecutivo"] - 1] = "";
			
			if ($fila2["se_compra"] == "SI")
				$se_compra_si[$fila2["consecutivo"] - 1] = " selected";
			else
				$se_compra_no[$fila2["consecutivo"] - 1] = " selected";
			
			if ($entidad[$fila2["consecutivo"] - 1])
			{
				$entidad_tmp = sqlsrv_query($link, "SELECT dias_entrega, dias_vigencia from entidades where entidad = '".$entidad[$fila2["consecutivo"] - 1]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($entidad_tmp))
				{
					$fila1 = sqlsrv_fetch_array($entidad_tmp, SQLSRV_FETCH_ASSOC);
					
					$dias_entregah[$fila2["consecutivo"] - 1] = $fila1["dias_entrega"];
					$dias_vigenciah[$fila2["consecutivo"] - 1] = $fila1["dias_vigencia"];
				}
			}
			
			$nombre_grabado[$fila2["consecutivo"] - 1] = ($fila2["nombre_grabado"]);
		}
		
		/*if ($ultimo_consecutivo_compra_cartera < 10)
		{
			for ($i = $ultimo_consecutivo_compra_cartera + 1; $i <= 10; $i++)
			{
				$id_entidad[$i - 1] = "";
				$entidad[$i - 1] = "";
				$cuota[$i - 1] = "0";
				$valor_pagar[$i - 1] = "0";
				
				if ($_SESSION["FUNC_MUESTRACAMPOS1"])
					$se_compra_si[$i - 1] = " selected";

				if ($_SESSION["FUNC_MUESTRACAMPOS2"])
					$se_compra_no[$i - 1] = " selected";

				$nombre_grabado[$i - 1] = "";
			}
			
			$ultimo_consecutivo_compra_cartera = 10;
		}*/
		
		$retanqueo1_libranza = $fila["retanqueo1_libranza"];
		$retanqueo1_cuota = $fila["retanqueo1_cuota"];
		$retanqueo1_valor = $fila["retanqueo1_valor"];
		
		if ($retanqueo1_libranza)
		{
			$rs1 = sqlsrv_query($link, "SELECT retanqueo_valor_liquidacion, retanqueo_intereses, retanqueo_seguro, retanqueo_cuotasmora, retanqueo_segurocausado, retanqueo_gastoscobranza, retanqueo_totalpagar from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND nro_libranza = '".$retanqueo1_libranza."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$retanqueo1_valor_liquidacion = $fila1["retanqueo_valor_liquidacion"];
			$retanqueo1_intereses = $fila1["retanqueo_intereses"];
			$retanqueo1_seguro = $fila1["retanqueo_seguro"];
			$retanqueo1_cuotasmora = $fila1["retanqueo_cuotasmora"];
			$retanqueo1_segurocausado = $fila1["retanqueo_segurocausado"];
			$retanqueo1_gastoscobranza = $fila1["retanqueo_gastoscobranza"];
			$retanqueo1_totalpagar = $fila1["retanqueo_totalpagar"];
		}
		
		$retanqueo2_libranza = $fila["retanqueo2_libranza"];
		$retanqueo2_cuota = $fila["retanqueo2_cuota"];
		$retanqueo2_valor = $fila["retanqueo2_valor"];
		
		if ($retanqueo2_libranza)
		{
			$rs1 = sqlsrv_query($link, "SELECT retanqueo_valor_liquidacion, retanqueo_intereses, retanqueo_seguro, retanqueo_cuotasmora, retanqueo_segurocausado, retanqueo_gastoscobranza, retanqueo_totalpagar from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND nro_libranza = '".$retanqueo2_libranza."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$retanqueo2_valor_liquidacion = $fila1["retanqueo_valor_liquidacion"];
			$retanqueo2_intereses = $fila1["retanqueo_intereses"];
			$retanqueo2_seguro = $fila1["retanqueo_seguro"];
			$retanqueo2_cuotasmora = $fila1["retanqueo_cuotasmora"];
			$retanqueo2_segurocausado = $fila1["retanqueo_segurocausado"];
			$retanqueo2_gastoscobranza = $fila1["retanqueo_gastoscobranza"];
			$retanqueo2_totalpagar = $fila1["retanqueo_totalpagar"];
		}
		
		$retanqueo3_libranza = $fila["retanqueo3_libranza"];
		$retanqueo3_cuota = $fila["retanqueo3_cuota"];
		$retanqueo3_valor = $fila["retanqueo3_valor"];
		
		if ($retanqueo3_libranza)
		{
			$rs1 = sqlsrv_query($link, "SELECT retanqueo_valor_liquidacion, retanqueo_intereses, retanqueo_seguro, retanqueo_cuotasmora, retanqueo_segurocausado, retanqueo_gastoscobranza, retanqueo_totalpagar from simulaciones where cedula = '".$cedula."' AND pagaduria = '".$pagaduria."' AND nro_libranza = '".$retanqueo3_libranza."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$retanqueo3_valor_liquidacion = $fila1["retanqueo_valor_liquidacion"];
			$retanqueo3_intereses = $fila1["retanqueo_intereses"];
			$retanqueo3_seguro = $fila1["retanqueo_seguro"];
			$retanqueo3_cuotasmora = $fila1["retanqueo_cuotasmora"];
			$retanqueo3_segurocausado = $fila1["retanqueo_segurocausado"];
			$retanqueo3_gastoscobranza = $fila1["retanqueo_gastoscobranza"];
			$retanqueo3_totalpagar = $fila1["retanqueo_totalpagar"];
		}
		
		$retanqueo_total_cuota = $fila["retanqueo_total_cuota"];
		$retanqueo_total = $fila["retanqueo_total"];
		
		$agenda = sqlsrv_query($link, "select * from agenda where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by consecutivo");
		
		while($fila1 = sqlsrv_fetch_array($agenda, SQLSRV_FETCH_ASSOC))
		{
			$entidadcarta[$fila1["consecutivo"] - 1] = ($fila1["entidad"]);
			$dias_entrega[$fila1["consecutivo"] - 1] = $fila1["dias_entrega"];
			$dias_vigencia[$fila1["consecutivo"] - 1] = $fila1["dias_vigencia"];
			
			$estadocarta_nos[$fila1["consecutivo"] - 1] = "";
			$estadocarta_sol[$fila1["consecutivo"] - 1] = "";
			$estadocarta_ent[$fila1["consecutivo"] - 1] = "";
			$estadocarta_con[$fila1["consecutivo"] - 1] = "";
			$estadocarta_pag[$fila1["consecutivo"] - 1] = "";
			
			switch($fila1["estado"])
			{
				case "NO SOLICITADA":	$estadocarta_nos[$fila1["consecutivo"] - 1] = " selected"; break;
				case "SOLICITADA":		$estadocarta_sol[$fila1["consecutivo"] - 1] = " selected"; break;
				case "ENTREGADA":		$estadocarta_ent[$fila1["consecutivo"] - 1] = " selected"; break;
				case "CONFIRMADA":		$estadocarta_con[$fila1["consecutivo"] - 1] = " selected"; break;
				case "PAGADA":			$estadocarta_pag[$fila1["consecutivo"] - 1] = " selected"; break;
			}
			
			$fecha_sugerida[$fila1["consecutivo"] - 1] = $fila1["fecha_sugerida"];
			$fecha_solicitudcarta[$fila1["consecutivo"] - 1] = $fila1["fecha_solicitud"];
			$fecha_entrega[$fila1["consecutivo"] - 1] = $fila1["fecha_entrega"];
			$fecha_vencimiento[$fila1["consecutivo"] - 1] = $fila1["fecha_vencimiento"];
		}
		
		$dia_confirmacion = $fila["dia_confirmacion"];
		$dia_vencimiento = $fila["dia_vencimiento"];
		$status = $fila["status"];
		
		$total_cuota = $fila["total_cuota"];
		$total_cuota_max = $fila["total_cuota"];
		$total_valor_pagar = $fila["total_valor_pagar"];
		$total_se_compra = $fila["total_se_compra"];
		
		$opcion_credito_cli = "";
		$opcion_credito_ccc = "";
		$opcion_credito_cmp = "";
		$opcion_credito_cso = "";
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$opcion_credito_cli = " checked"; break;
			case "CCC":	$opcion_credito_ccc = " checked"; break;
			case "CMP":	$opcion_credito_cmp = " checked"; break;
			case "CSO":	$opcion_credito_cso = " checked"; break;
		}
		
		$opcion_cuota_cli = $fila["opcion_cuota_cli"];
		
		if ($fila["opcion_desembolso_cli"] == "MEJORA SALARIO")
			$opcion_desembolso_cli = $fila["opcion_desembolso_cli"];
		else
			$opcion_desembolso_cli = number_format($fila["opcion_desembolso_cli"], 0, ".", ",");
		
		$opcion_cuota_ccc = $fila["opcion_cuota_ccc"];
		$opcion_desembolso_ccc = $fila["opcion_desembolso_ccc"];
		$opcion_cuota_cmp = $fila["opcion_cuota_cmp"];
		$opcion_desembolso_cmp = $fila["opcion_desembolso_cmp"];
		$opcion_cuota_cso = $fila["opcion_cuota_cso"];		
		$opcion_desembolso_cso = $fila["opcion_desembolso_cso"];
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$sin_retanqueos = $opcion_desembolso_cli; break;
			case "CCC":	$sin_retanqueos = number_format($opcion_desembolso_ccc - $retanqueo_total, 0, ".", ","); break;
			case "CMP":	$sin_retanqueos = number_format($opcion_desembolso_cmp - $retanqueo_total, 0, ".", ","); break;
			case "CSO":	$sin_retanqueos = number_format($opcion_desembolso_cso - $retanqueo_total, 0, ".", ","); break;
		}
		
		$desembolso_cliente = $fila["desembolso_cliente"];
		
		$decision = $fila["decision"];
		$decision_viable = "";
		$decision_negado = "";
		
		if ($fila["decision"] == $label_viable)
			$decision_viable = " selected";
		else
			$decision_negado = " selected";
		
		$decision_sistema = $fila["decision_sistema"];
		
		$nro_libranza = $fila["nro_libranza"];
		$valor_visado = $fila["valor_visado"];
		
		$fecha_llamada_cliente = $fila["fecha_llamada_cliente"];
		
		if ($fecha_llamada_cliente){
			$fecha_llamada_clientef = substr($fecha_llamada_cliente, 0, 10);
			$fecha_llamada_clienteh = substr($fecha_llamada_cliente, 11, 5);
			
			$fecha_llamada_clientej_am = "";
			$fecha_llamada_clientej_pm = "";
			
			if (substr($fecha_llamada_clienteh, 0, 2) < 12)
			{
				if (substr($fecha_llamada_clienteh, 0, 2) == "00")
					$fecha_llamada_clienteh = "12".substr($fecha_llamada_clienteh, 2, 3);
				
				$fecha_llamada_clientej_am = " selected";
			}
			else
			{
				if (substr($fecha_llamada_clienteh, 0, 2) != "12")
				{
					$hora = substr($fecha_llamada_clienteh, 0, 2) - 12;
					
					if (strlen($hora) == "1")
						$hora = "0".$hora;
					
					$fecha_llamada_clienteh = $hora.substr($fecha_llamada_clienteh, 2, 3);
				}
				
				$fecha_llamada_clientej_pm = " selected";
			}
		}
		
		$nro_cuenta = $fila["nro_cuenta"];
		
		$tipo_cuenta_aho = "";
		$tipo_cuenta_cte = "";
		
		switch($fila["tipo_cuenta"])
		{
			case $label_aho:	$tipo_cuenta_aho = " selected"; break;
			case $label_cte:	$tipo_cuenta_cte = " selected"; break;
		}
		
		$id_banco = $fila["id_banco"];
		
		$id_subestado = $fila["id_subestado"];
		$cod_interno_subestado = $fila["cod_interno"];
		$id_causal = $fila["id_causal"];
		$nombre_causal = $fila["nombre_causal"];
		$id_caracteristica = $fila["id_caracteristica"];
		$nombre_caracteristica = $fila["nombre_caracteristica"];
		$calificacion = $fila["calificacion"];
		
		$calificacion_5 = "";
		$calificacion_4 = "";
		$calificacion_3 = "";
		$calificacion_2 = "";
		$calificacion_1 = "";
		
		switch($fila["calificacion"])
		{
			case "5":	$calificacion_5 = " selected"; break;
			case "4":	$calificacion_4 = " selected"; break;
			case "3":	$calificacion_3 = " selected"; break;
			case "2":	$calificacion_2 = " selected"; break;
			case "1":	$calificacion_1 = " selected"; break;
		}
		
		$valor_credito = $fila["valor_credito"];
		$resumen_ingreso = $fila["resumen_ingreso"];
		$incor = $fila["incor"];
		$comision = $fila["comision"];
		$utilidad_neta = $fila["utilidad_neta"];
		$sobre_el_credito = $fila["sobre_el_credito"];
		$estado = $fila["estado"];
		
		if ($fila["descuento1"] != "")
		{
			$descuento1 = $fila["descuento1"];
			
			if ($fila["opcion_credito"] != "CLI")
				$descuento1_valor = ($valor_credito - $retanqueo_total) * $fila["descuento1"] / 100;
			else
				$descuento1_valor = $valor_credito * $fila["descuento1"] / 100;
		}
		if ($fila["descuento2"] != "")
		{
			$descuento2 = $fila["descuento2"];
			
			if ($fila["opcion_credito"] != "CLI")
				$descuento2_valor = ($valor_credito - $retanqueo_total) * $fila["descuento2"] / 100;
			else
				$descuento2_valor = $valor_credito * $fila["descuento2"] / 100;
		}
		if ($fila["descuento3"] != "")
		{
			$descuento3 = $fila["descuento3"];
			
			if ($fila["opcion_credito"] != "CLI")
				$descuento3_valor = ($valor_credito - $retanqueo_total) * $fila["descuento3"] / 100;
			else
				$descuento3_valor = $valor_credito * $fila["descuento3"] / 100;
		}
		if ($fila["descuento4"] != "")
		{
			$descuento4 = $fila["descuento4"];
			
			if ($fila["opcion_credito"] != "CLI")
				$descuento4_valor = ($valor_credito - $retanqueo_total) * $fila["descuento4"] / 100;
			else
				$descuento4_valor = $valor_credito * $fila["descuento4"] / 100;
		}
		if ($fila["descuento5"] != "")
		{
			$descuento5 = $fila["descuento5"];
			
			if ($fila["fidelizacion"])
				$descuento5_valor = $retanqueo_total * $fila["descuento5"] / 100;
			else
				$descuento5_valor = $valor_credito * $fila["descuento5"] / 100;
		}
		if ($fila["descuento6"] != "")
		{
			$descuento6 = $fila["descuento6"];
			
			if ($fila["fidelizacion"])
				$descuento6_valor = $retanqueo_total * $fila["descuento6"] / 100;
			else
				$descuento6_valor = $valor_credito * $fila["descuento6"] / 100;
		}
		// //Servicio Nubeeeeee
		if($aplicar_servicio_nube){
			if($fila["descuento2_valor"] > 0){
				$descuento2_valor = $fila["descuento2_valor"];
			}
			$descuento8_valor = $fila["descuento8_valor"];
			$descuento9_valor = $fila["descuento9_valor"];
			$descuento10_valor = $fila["descuento10_valor"];
		}
		
		if ($fila["descuento_transferencia"] != "")
			$descuento_transferencia = $fila["descuento_transferencia"];
		
		if ($fila["tipo_producto"] == "1")
		{
			$tipo_producto = "1";
			$tipo_producto_checked = " checked";
		}
		else
		{
			$tipo_producto = "0";
			$tipo_producto_checked = "";
		}
		
		$porcentaje_seguro = $fila["porcentaje_seguro"];
		$valor_por_millon_seguro = $fila["valor_por_millon_seguro"];
		$porcentaje_extraprima = $fila["porcentaje_extraprima"];
		
		if ($fila["bloqueo_cuota"] == "1")
		{
			$bloqueo_cuota = "1";
			$bloqueo_cuota_checked = " checked";
		}
		else
		{
			$bloqueo_cuota = "0";
			$bloqueo_cuota_checked = "";
		}
		
		$bloqueo_cuota_valor = $fila["bloqueo_cuota_valor"];
		
		if ($fila["formulario_seguro"] == "1")
		{
			$formulario_seguro = "1";
			$formulario_seguro_checked = " checked";
		}
		else
		{
			$formulario_seguro = "0";
			$formulario_seguro_checked = "";
		}
		
		$score_ado = $fila["score_ado"];
		$response_ado = $fila["response_ado"];
		
		if (!$fila["nombre1"] || (($fila["estado_civil"] == "CASADO" || $fila["estado_civil"] == "UNION LIBRE") && !$fila["nombre_conyugue"]) || !$fila["ocupacion"] || !$fila["ingresos_laborales"] || !$fila["nombre_familiar"] || !$fila["moneda_extranjera"] || !$fila["ciudadania_extranjera"] || !$fila["instruccion_desembolso"])
		{
			$solicitud = "0";
			
			if (!$fila["nombre1"])
				$mensaje_faltantes = "Informacion Personal";
			
			if (($fila["estado_civil"] == "CASADO" || $fila["estado_civil"] == "UNION LIBRE") && !$fila["nombre_conyugue"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Datos del Conyugue";
			}
			
			if (!$fila["ocupacion"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Actividad Laboral";
			}
			
			if (!$fila["ingresos_laborales"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Informacion Financiera";
			}
			
			if (!$fila["nombre_familiar"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Referencias";
			}
			
			if (!$fila["moneda_extranjera"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Datos de Operaciones Internacionales";
			}
			
			if (!$fila["ciudadania_extranjera"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Declaracion FACTA - CRS";
			}
			
			if (!$fila["instruccion_desembolso"])
			{
				if ($mensaje_faltantes)
					$mensaje_faltantes .= ", ";
				
				$mensaje_faltantes .= "Varios";
			}
		}
	}
	else
	{
		echo "<script>alert('No hay informacion asociada a esta simulacion');</script>";
	}
}

if (!$cod_interno_subestado)
	$cod_interno_subestado = 0;

if ($_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO")
{
	$bloquear_condiciones = 1;
}

if ($_SESSION["S_TIPO"] == "COMERCIAL" && ($cod_interno == 36 || $cod_interno == 38))
{
	$bloquear_condiciones = 1;
}

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL") && (!$estado || $estado == "ING"))
{
	$_REQUEST["tipo"] = "COM";
}

$oficina_ado = 0;
$oficina_gattaca = 0;

$query_oficina = sqlsrv_query($link, "SELECT top 1 iIF(b.ado IS NULL, 0, b.ado) AS ado, iIF(b.gattaca IS NULL, 0, b.gattaca) AS gattaca  FROM oficinas_usuarios a JOIN oficinas b ON a.id_oficina = b.id_oficina WHERE a.id_usuario ='".$id_comercial."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
if(sqlsrv_num_rows($query_oficina) > 0){
	$datos_ofi = sqlsrv_fetch_array($query_oficina, SQLSRV_FETCH_ASSOC);
	$oficina_ado = $datos_ofi["ado"];
	$oficina_gattaca = $datos_ofi["gattaca"];
}

//$queryOTP = sqlsrv_query($link, "SELECT id_simulacion FROM historial_sms_otp f WHERE f.id = (SELECT MAX(id) FROM historial_sms_otp WHERE id_simulacion = f.id_simulacion) AND f.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND f.estado = 1");
//$validacion_otp = sqlsrv_num_rows($queryOTP);

?>
<style type="text/css">
	.expandable-asesoria {
		-webkit-transition: 0.5s;
		-moz-transition: 0.5s;
		-o-transition: 0.5s;
		transition: 0.5s;
		opacity: 0;
		visibility: collapse;
	}

	.expandable-asesoria-visible {
		-webkit-transition: 1.5s;
		-moz-transition: 1.5s;
		-o-transition: 1.5s;
		transition: 1.5s;
		opacity: 1 !important;
		visibility: visible !important;
	}

	#declaracion_enfermedad tbody tr:hover{
                background-color: #ffe4b3;
                font-weight: bold;
        }
</style>
<link href="../jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css">
<!-- inicio SCRIPT -->
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script language="JavaScript">
	function verDetalleAsesoriaFinaciera(a) {
		if($(".expandable-asesoria").hasClass('expandable-asesoria-visible')){
			$('.expandable-asesoria').removeClass("expandable-asesoria-visible");
			$(a).text('Ver Detalle');
		}else{
			$('.expandable-asesoria').addClass("expandable-asesoria-visible");
			$(a).text('Ocultar');
		}
	}

	$.datepicker.regional['es'] = {
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
        weekHeader: 'Sm',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearRange: '-100:+0',
        changeMonth: true,
        changeYear: true,
    };

    $.datepicker.setDefaults($.datepicker.regional['es']);

    $(function () {
        $(".fecha_datepicker").datepicker({
            minDate: new Date(),
			yearRange: '-0:+12'
        });
    });
<?php

if ($_SESSION["FUNC_SUBESTADOS"])
{

?>
function valor_subestados(x){return x.substring(0,x.indexOf('-'))}

function texto_subestados(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargarsubestados(decision, objeto_subestados) {
	var num_subestados;
	var j, k = 1;

	num_subestados = 200;

	objeto_subestados.length = num_subestados;
<?php

	$queryDB = "select DISTINCT se.id_subestado, se.decision, se.nombre from subestados se INNER JOIN subestados_usuarios su ON se.id_subestado = su.id_subestado where se.estado = '1' AND se.decision = '".$label_viable."'";
	
	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
	{
		$queryDB .= " AND su.id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
		
		if ($id_subestado)
			$id_subestado_origen = $id_subestado;
		else
			$id_subestado_origen = "0";
		
		$queryDB .= " AND (se.id_subestado IN (select id_subestado_destino from subestados_orden where id_subestado_origen = '".$id_subestado_origen."') OR se.cod_interno = '999'";
		
		if (($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && ($cod_interno_subestado < 55))
			$queryDB .= " OR se.id_subestado IN (select id_subestado_origen from subestados_orden where id_subestado_destino = '".$id_subestado_origen."')";
		
		$queryDB .= ")";
	}
	
	if ($id_subestado)
		$queryDB .= " OR (se.decision = '".$decision."' AND se.id_subestado = '".$id_subestado."')";
	
	$queryDB .= " order by se.nombre";
	
	$datos_subestados_viable = sqlsrv_query($link, $queryDB);
	
	$padre_hija = "PHVIABLE = [";
	
	while ($fila2 = sqlsrv_fetch_array($datos_subestados_viable, SQLSRV_FETCH_ASSOC))
	{
		$padre_hija .= "\"".$fila2["id_subestado"]."-".$fila2["nombre"]."\",";
	}
	
	$padre_hija .= "\"0-Otro\"];\n";
	
	echo $padre_hija;
	
	$queryDB = "select DISTINCT se.id_subestado, se.decision, se.nombre from subestados se INNER JOIN subestados_usuarios su ON se.id_subestado = su.id_subestado where se.estado = '1' AND se.decision = '".$label_negado."'";
	
	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
		$queryDB .= " AND su.id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$queryDB .= " order by se.nombre";
	
	$datos_subestados_negado = sqlsrv_query($link, $queryDB);
	
	$padre_hija = "PHNEGADO = [";
	
	while ($fila2 = sqlsrv_fetch_array($datos_subestados_negado, SQLSRV_FETCH_ASSOC))
	{
		$padre_hija .= "\"".$fila2["id_subestado"]."-".$fila2["nombre"]."\",";
	}
	
	$padre_hija .= "\"0-Otro\"];\n";
	
	echo $padre_hija;
	
?>
	switch(decision) {
		case 'PHVIABLE':
			num_subestados = PHVIABLE.length;
			for(j = 0; j < num_subestados; j++) {
				objeto_subestados.options[k].value = valor_subestados(PHVIABLE[j]);
				objeto_subestados.options[k].text = texto_subestados(PHVIABLE[j]);
				k++;
			}
			break;

		case 'PHNEGADO':
			num_subestados = PHNEGADO.length;
			for(j = 0; j < num_subestados; j++) {
				objeto_subestados.options[k].value = valor_subestados(PHNEGADO[j]);
				objeto_subestados.options[k].text = texto_subestados(PHNEGADO[j]);
				k++;
			}
			break;

		default:
			num_subestados = 1;
			k=0;
	}

	objeto_subestados.selectedIndex = 0;
	objeto_subestados.length = num_subestados;

	return true;
}
<?php

}

?>

function PorcentajeSeguro(valor_por_millon_seguro, plazo, tasa_interes, porcentaje_extraprima, sin_seguro, seguro_parcial = 0) {
	var porcentaje = 0;
	
	var parametros = {
		"valor_por_millon": valor_por_millon_seguro,
		"plazo": plazo,
		"tasa_interes": tasa_interes,
		"porcentaje_extraprima": porcentaje_extraprima,
		"sin_seguro": sin_seguro,
		"calculo_ajax": "1",
		"seguro_parcial": seguro_parcial
	};
	
	$.ajax({
    	type: "POST",
		async: false,
		url: "porcentajes_seguro.php",
		data: parametros,
		success: function( response ) {
			if (response) {
				porcentaje = response;
			}
		}
	});

	return porcentaje;
}

function ValorPorMillon(id_unidad_negocio, nivel_contratacion, pagaduria, valor_por_millon_simulacion, fecha_estudio, fecha_nacimiento, cod_interno_subestado, seguro_parcial, sin_seguro, id_simulacion = '') {
	var valor_por_millon = 0;

	if(id_simulacion == ''){
		id_simulacion = '<?=$_REQUEST["id_simulacion"]?>';
	}
	
	var parametros = {
		"id_unidad_negocio": id_unidad_negocio,
		"nivel_contratacion": nivel_contratacion,
		"pagaduria": pagaduria,
		"valor_por_millon_simulacion": valor_por_millon_simulacion,
		"fecha_estudio": fecha_estudio,
		"fecha_nacimiento": fecha_nacimiento,
		"cod_interno_subestado": cod_interno_subestado,
		"seguro_parcial": seguro_parcial,
		"sin_seguro": sin_seguro,
		"id_simulacion": id_simulacion
	};
	
	$.ajax({
    	type: "POST",
		async: false,
		url: "../controles/valor_por_millon.php",
		data: parametros,
		success: function( response ) {
			if (response) {
				valor_por_millon = response.trim();
			}
		}
	});

	return valor_por_millon;
}

<?php

if ($_SESSION["FUNC_TASASPLAZO"])
{

?>
function CargarTasas(id_unidad_negociox, plazox, sin_segurox) {
	var select_tasa_interes = document.getElementById("tasa_interes");
	
    while (select_tasa_interes.options.length > 0) {
        select_tasa_interes.remove(0);
    }
	
	select_tasa_interes.length = 200;

	var x = "0";
	
	with (document.formato) {
<?php

	$queryDB = "select id_tasa, plazoi, plazof from tasas".$sufijo_sector." order by plazoi";
	
	$rs_tasas = sqlsrv_query($link, $queryDB);
	
	while ($fila2 = sqlsrv_fetch_array($rs_tasas, SQLSRV_FETCH_ASSOC))
	{
	
?>
		if (parseInt(plazox) >= <?php echo $fila2["plazoi"] ?> && parseInt(plazox) <= <?php echo $fila2["plazof"] ?>) {
<?php

		$queryDB = "select id_unidad from unidades_negocio where 1 = 1";

		$queryDB .= " AND id_unidad IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		
		$queryDB .= " order by id_unidad";

		$rs1 = sqlsrv_query($link, $queryDB);

		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			
?>
			if (id_unidad_negociox == "<?php echo $fila1["id_unidad"] ?>") {
				if (sin_segurox == "0") {
<?php

			$j = 0;
			
			$queryDB = "SELECT t2.tasa_interes + 0 as tasa_interes from tasas2".$sufijo_sector." t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila2["id_tasa"]."' AND t2u.id_unidad_negocio = '".$fila1["id_unidad"]."' AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC";
			
			$rs_tasas2 = sqlsrv_query($link, $queryDB);
			
			while ($fila3 = sqlsrv_fetch_array($rs_tasas2, SQLSRV_FETCH_ASSOC))
			{
			
?>
					tasa_interes.options[<?php echo $j ?>].value = "<?php echo $fila3["tasa_interes"] ?>";
					tasa_interes.options[<?php echo $j ?>].text = "<?php echo $fila3["tasa_interes"] ?>";
<?php

				$j++;
			}
			
			$queryDB = "select top 1 t2.tasa_interes + 0 as tasa_interes, t2.descuento1 + 0 as descuento1, t2.descuento1_producto + 0 as descuento1_producto, t2.descuento2 + 0 as descuento2, t2.descuento3 + 0 as descuento3 from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila2["id_tasa"]."' AND t2u.id_unidad_negocio = '".$fila1["id_unidad"]."' AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC";
			
			$rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs_tasa2))
			{
				$fila_tasa2 = sqlsrv_fetch_array($rs_tasa2, SQLSRV_FETCH_ASSOC);
				
				$tasa2_descuento_producto0 = $fila_tasa2["descuento1"];
				
				$tasa2_descuento1 = $fila_tasa2["descuento1"];
				
				$tasa2_descuento_producto1 = $fila_tasa2["descuento1_producto"];
				
				$tasa2_descuento2 = $fila_tasa2["descuento2"];
				
				$tasa2_descuento3 = $fila_tasa2["descuento3"];
				
				$x = $fila_tasa2["tasa_interes"];
			}
			else
			{
				$tasa2_descuento_producto0 = 0;
				
				$tasa2_descuento1 = 0;
				
				$tasa2_descuento_producto1 = 0;
				
				$tasa2_descuento2 = 0;
				
				$tasa2_descuento3 = 0;
				
				$x = 0;
			}
			
?>
					tasa_interes.selectedIndex = 0;
					tasa_interes.length = <?php echo $j ?>;
					descuento_producto0.value = "<?php echo $tasa2_descuento_producto0 ?>";
					descuento1.value = "<?php echo $tasa2_descuento1 ?>";
					descuento_producto1.value = "<?php echo $tasa2_descuento_producto1 ?>";
					descuento2.value = "<?php echo $tasa2_descuento2 ?>";
					descuento3.value = "<?php echo $tasa2_descuento3 ?>";
					x = "<?php echo $x ?>";
				}
				else {
<?php

			$j = 0;
			
			$queryDB = "select t2.tasa_interes + 0 as tasa_interes from tasas2".$sufijo_sector." t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila2["id_tasa"]."' AND t2u.id_unidad_negocio = '".$fila1["id_unidad"]."' AND t2.sin_seguro = '1' AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC";
			
			$rs_tasas2 = sqlsrv_query($link, $queryDB);
			
			while ($fila3 = sqlsrv_fetch_array($rs_tasas2, SQLSRV_FETCH_ASSOC))
			{
			
?>
					tasa_interes.options[<?php echo $j ?>].value = "<?php echo $fila3["tasa_interes"] ?>";
					tasa_interes.options[<?php echo $j ?>].text = "<?php echo $fila3["tasa_interes"] ?>";
<?php

				$j++;
			}
			
			$queryDB = "select top 1 t2.tasa_interes + 0 as tasa_interes, t2.descuento1 + 0 as descuento1, t2.descuento1_producto + 0 as descuento1_producto, t2.descuento2 + 0 as descuento2, t2.descuento3 + 0 as descuento3 from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila2["id_tasa"]."' AND t2u.id_unidad_negocio = '".$fila1["id_unidad"]."' AND t2.sin_seguro = '1' AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC ";
			
			$rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs_tasa2))
			{
				$fila_tasa2 = sqlsrv_fetch_array($rs_tasas2, SQLSRV_FETCH_ASSOC);
				
				$tasa2_descuento_producto0 = $fila_tasa2["descuento1"];
				
				$tasa2_descuento1 = $fila_tasa2["descuento1"];
				
				$tasa2_descuento_producto1 = $fila_tasa2["descuento1_producto"];
				
				$tasa2_descuento2 = $fila_tasa2["descuento2"];
				
				$tasa2_descuento3 = $fila_tasa2["descuento3"];
				
				$x = $fila_tasa2["tasa_interes"];
			}
			else
			{
				$tasa2_descuento_producto0 = 0;
				
				$tasa2_descuento1 = 0;
				
				$tasa2_descuento_producto1 = 0;
				
				$tasa2_descuento2 = 0;
				
				$tasa2_descuento3 = 0;
				
				$x = 0;
			}
			
?>
					tasa_interes.selectedIndex = 0;
					tasa_interes.length = <?php echo $j ?>;
					descuento_producto0.value = "<?php echo $tasa2_descuento_producto0 ?>";
					descuento1.value = "<?php echo $tasa2_descuento1 ?>";
					descuento_producto1.value = "<?php echo $tasa2_descuento_producto1 ?>";
					descuento2.value = "<?php echo $tasa2_descuento2 ?>";
					descuento3.value = "<?php echo $tasa2_descuento3 ?>";
					x = "<?php echo $x ?>";
				}
			}
<?php

		}
?>
		}
<?php

	}
	
?>
		if (x == "" || x == "0") {
			tasa_interes.selectedIndex = 0;
			tasa_interes.length = 1;
			descuento_producto0.value = "0";
			descuento1.value = "0";
			descuento_producto1.value = "0";
			descuento2.value = "0";
			descuento3.value = "0";
		}
<?php

	if ($sector == "PRIVADO")
	{
	
?>
		descuento3.value = parseFloat(descuento3.value) + parseFloat(descuento1.value) * <?php echo $iva ?> / 100.00;
<?php

	}
	
?>
	}

	cargarTasaComision();
	
	return x;
}

<?php

	$rs1 = sqlsrv_query($link, "select nombre from entidades_desembolso where id_entidad = '".$entidad_seguro."'");
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$nombre_entidad_seguro = $fila1["nombre"];
	
?>

function cargarTasaComision() {
	var id_unidad_negocio = $("#id_unidad_negocio").val();
	var tasa_interes = $("#tasa_interes").val();
	var id_simulacion = $("#id_simulacion").val();
	var id_tasa_comision = $("#tipo_tasa_comision").val();

	Swal.fire({
		title: 'Por favor aguarde unos segundos',
		text: 'Procesando...'
	});

	Swal.showLoading();

	var rowsHtml = '';

	$.ajax({
		url: '../servicios/comisiones/consultar_tasas_simulacion.php',
		type: 'POST',
		data: { id_simulacion : id_simulacion, id_unidad_negocio : id_unidad_negocio, tasa_interes : tasa_interes },
		dataType : 'json',
		success: function(json) {
			
			if(json.code == 200 || json.code == 300){
				if(json.code == 200){
					json.data.forEach(function(tasa, index) {
						if(tasa.marca_unidad_negocio == 1){ textoTipoComsion = 'F'; }else{ textoTipoComsion = 'K'; }
						if(tasa.id_tasa_comision == id_tasa_comision) {
							rowsHtml += '<option selected id_tipo="+tasa.id_tipo+" value="'+tasa.id_tasa_comision+'">TIPO '+textoTipoComsion+' '+tasa.id_tipo+'</option>';
						}else{
							rowsHtml += '<option id_tipo="+tasa.id_tipo+" value="'+tasa.id_tasa_comision+'">TIPO '+textoTipoComsion+' '+tasa.id_tipo+'</option>';
						}
					});				
				}											
			}

			$("#tipo_tasa_comision").html(rowsHtml);

			Swal.close();

			return false;
		}
	});
}

function CargarSeguro(valor_segurox) {
	with (document.formato) {
		var i_inicial = 1;
		var i_final = parseInt(ultimo_consecutivo_compra_cartera.value);
		
		if (valor_segurox)
		{
			valor_seguro.value = valor_segurox.substring(valor_segurox.indexOf('$') + 1);
			
			if (nro_compra_cartera_seguro.value != "")
			{
				i_inicial = parseInt(nro_compra_cartera_seguro.value);
				i_final = parseInt(nro_compra_cartera_seguro.value);
			}
			
			for (i = i_inicial; i <= i_final; i++) {
				if ((document.getElementById("id_entidad"+i).value == "" && document.getElementById("entidad"+i).value == "") || (nro_compra_cartera_seguro.value != "")){
					document.getElementById("id_entidad"+i).length = 1;
					document.getElementById("id_entidad"+i).options[0].value = "<?php echo $entidad_seguro ?>";
					document.getElementById("id_entidad"+i).options[0].text = "<?php echo $nombre_entidad_seguro ?>";
					document.getElementById("id_entidad"+i).selectedIndex = 0;
					
					document.getElementById("entidad"+i).value = valor_segurox.substring(0, valor_segurox.indexOf('$'));
					document.getElementById("valor_pagar"+i).value = valor_segurox.substring(valor_segurox.indexOf('$') + 1);
					
					document.getElementById("se_compra"+i).length = 1;
					document.getElementById("se_compra"+i).options[0].value = "SI";
					document.getElementById("se_compra"+i).options[0].text = "SI";
					document.getElementById("se_compra"+i).selectedIndex = 0;
					
					document.getElementById("entidad"+i).readOnly = true;
					document.getElementById("cuota"+i).readOnly = true;
					document.getElementById("valor_pagar"+i).readOnly = true;
					
					document.getElementById("id_entidad"+i).style.backgroundColor = "#FFFFFF";
					document.getElementById("entidad"+i).style.backgroundColor = "#FFFFFF";
					document.getElementById("cuota"+i).style.backgroundColor = "#FFFFFF";
					document.getElementById("valor_pagar"+i).style.backgroundColor = "#FFFFFF";
					document.getElementById("se_compra"+i).style.backgroundColor = "#FFFFFF";
					
					nro_compra_cartera_seguro.value = i;
					
					break;
				}
			}
			
			if (nro_compra_cartera_seguro.value == "")
			{
				id_plan_seguro.selectedIndex = 0;
				valor_seguro.value = "0";
				
				alert("No se puede establecer el seguro porque no hay espacio en las compras de cartera");
			}
		}
		else
		{
			valor_seguro.value = "0";
			
<?php

	$queryDB = "select id_entidad, nombre from entidades_desembolso order by nombre";
	
	$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	$length_entidades = sqlsrv_num_rows($rs1) + 1;
	
	echo "document.getElementById(\"id_entidad\"+nro_compra_cartera_seguro.value).length = ".$length_entidades.";\n";
	echo "document.getElementById(\"id_entidad\"+nro_compra_cartera_seguro.value).options[0].value = \"\";\n";
	echo "document.getElementById(\"id_entidad\"+nro_compra_cartera_seguro.value).options[0].text = \"\";\n";
	
	$i = 1;
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "document.getElementById(\"id_entidad\"+nro_compra_cartera_seguro.value).options[".$i."].value = \"".$fila1["id_entidad"]."\";\n";
		echo "document.getElementById(\"id_entidad\"+nro_compra_cartera_seguro.value).options[".$i."].text = \"".(str_replace("\"", "", $fila1["nombre"]))."\";\n";
		
		$i++;
	}
	
?>
			document.getElementById("id_entidad"+nro_compra_cartera_seguro.value).selectedIndex = 0;
			
			document.getElementById("entidad"+nro_compra_cartera_seguro.value).value = "";
			document.getElementById("valor_pagar"+nro_compra_cartera_seguro.value).value = "0";
			
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).length = 2;
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).options[0].value = "SI";
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).options[0].text = "SI";
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).options[1].value = "NO";
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).options[1].text = "NO";
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).selectedIndex = 0;
			
			document.getElementById("entidad"+nro_compra_cartera_seguro.value).readOnly = false;
			document.getElementById("cuota"+nro_compra_cartera_seguro.value).readOnly = false;
			document.getElementById("valor_pagar"+nro_compra_cartera_seguro.value).readOnly = false;
			
			document.getElementById("id_entidad"+nro_compra_cartera_seguro.value).style.backgroundColor = "#EAF1DD";
			document.getElementById("entidad"+nro_compra_cartera_seguro.value).style.backgroundColor = "#EAF1DD";
			document.getElementById("cuota"+nro_compra_cartera_seguro.value).style.backgroundColor = "#EAF1DD";
			document.getElementById("valor_pagar"+nro_compra_cartera_seguro.value).style.backgroundColor = "#EAF1DD";
			document.getElementById("se_compra"+nro_compra_cartera_seguro.value).style.backgroundColor = "#EAF1DD";
			
			nro_compra_cartera_seguro.value = "";
		}
	}
}

function LeerDescuentos(id_unidad_negociox, plazox, tasax) {
	with (document.formato) {
		var x = "";		
<?php

	$queryDB = "select id_tasa, plazoi, plazof from tasas".$sufijo_sector." order by plazoi";
	
	$rs_tasas = sqlsrv_query($link, $queryDB);
	
	while ($fila2 = sqlsrv_fetch_array($rs_tasas, SQLSRV_FETCH_ASSOC))
	{
	
?>
		if (parseInt(plazox) >= <?php echo $fila2["plazoi"] ?> && parseInt(plazox) <= <?php echo $fila2["plazof"] ?>) {
<?php

		$queryDB = "select id_unidad from unidades_negocio where 1 = 1";

		$queryDB .= " AND id_unidad IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		
		$queryDB .= " order by id_unidad";

		$rs1 = sqlsrv_query($link, $queryDB);

		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			
?>
			if (id_unidad_negociox == "<?php echo $fila1["id_unidad"] ?>") {
<?php

			$queryDB = "select  CAST(t2.tasa_interes as float) + 0 as tasa_interes, t2.descuento1 + 0 as descuento1, t2.descuento1_producto + 0 as descuento1_producto, t2.descuento2 + 0 as descuento2, t2.descuento3 + 0 as descuento3 from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila2["id_tasa"]."' AND t2u.id_unidad_negocio = '".$fila1["id_unidad"]."' AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC";
			
			$rs_tasas2 = sqlsrv_query($link, $queryDB);
			
			while ($fila3 = sqlsrv_fetch_array($rs_tasas2, SQLSRV_FETCH_ASSOC))
			{
			
	?>
				if (parseFloat(tasax) == <?php echo $fila3["tasa_interes"] ?>) {
					descuento_producto0.value = "<?php echo $fila3["descuento1"] ?>";
					if (tipo_producto.value == "0")
					{
						descuento1.value = "<?php echo $fila3["descuento1"] ?>";
					}
					else
					{
						descuento1.value = "<?php echo $fila3["descuento1_producto"] ?>";
					}
					descuento_producto1.value = "<?php echo $fila3["descuento1_producto"] ?>";
					descuento2.value = "<?php echo $fila3["descuento2"] ?>";
					descuento3.value = "<?php echo $fila3["descuento3"] ?>";
					x = "<?php echo $fila3["tasa_interes"] ?>";
				}
<?php

			}
			
?>
			}
<?php

		}
		
?>
		}
<?php

	}
	
?>
		if (x == "" || x == "0") {
			descuento_producto0.value = "0";
			descuento1.value = "0";
			descuento_producto1.value = "0";
			descuento2.value = "0";
			descuento3.value = "0";
		}
<?php

	if ($sector == "PRIVADO")
	{
	
?>
		descuento3.value = parseFloat(descuento3.value) + parseFloat(descuento1.value) * <?php echo $iva ?> / 100.00;
<?php

	}
	
?>
	}

	cargarTasaComision();
}
<?php

}

?>

function TotalizarComprasCartera() {
	with (document.formato) {
		total_se_compra.value = "0";

		for (i = 1; i <= parseInt(ultimo_consecutivo_compra_cartera.value); i++) {
			if (document.getElementById("se_compra"+i).value == "SI" && document.getElementById("id_entidad"+i).value != "") {
				total_se_compra.value = parseInt(total_se_compra.value) + 1;
			}
		}
	}
}

function AdicionarCompraCartera() {
	permite_adicionar = 1;
	
	with (document.formato) {
		for (i = 1; i <= parseInt(ultimo_consecutivo_compra_cartera.value); i++) {
			if (document.getElementById("id_entidad"+i).value == "" && document.getElementById("entidad"+i).value == "") {
				permite_adicionar = 0;
				
				break;
			}
		}

		<?php 
		if (in_array($fila["id_subestado"],$subestados_bloqueo_comprascartera) && ($_SESSION["S_TIPO"] != "ADMINISTRADOR"))
		{
			?>
			permite_adicionar=2;
			<?php
		}
		?>
		
		if (permite_adicionar == 1) {
			if (parseInt(ultimo_consecutivo_compra_cartera.value) != <?php echo $ultimo_consecutivo_compra_cartera + 10 ?>) {
				nueva_cartera = parseInt(ultimo_consecutivo_compra_cartera.value) + 1;
				
				document.getElementById("cc_tr"+nueva_cartera).style = "table-row";
				
				ultimo_consecutivo_compra_cartera.value = nueva_cartera;
				
			}
			else {
				alert("Ha adicionado 10 registros, por favor guarde la simulacion e ingrese de nuevo a ella, para que pueda ingresar hasta 10 registros mas");
			}
		}
		else if (permite_adicionar == 2){
			
			Swal.fire({
	                title: 'Alerta',
	                text: 'El credito se encuentra en subestado correspondiente a tesoreria, no puede ingresar mas carteras'
	            });
		}else{
			alert("Todavia hay espacio en las compras de cartera, por favor utilicelo antes de adicionar un nuevo registro");
		}
	}
}

function saldo_retanqueo(cedula, pagaduria, id_simulacion, retanqueo_libranza, nro_retanqueo) {
	var ya_existe_libranza_en_credito = 0;
	var parametros = {
		"cedula": cedula,
		"pagaduria": pagaduria,
		"id_simulacion": id_simulacion,
		"retanqueo_libranza": retanqueo_libranza,
		"nro_retanqueo": nro_retanqueo
	};
	
	if (retanqueo_libranza == "") {
		$("#retanqueo"+nro_retanqueo+"_cuota").val(0);
		$("#retanqueo"+nro_retanqueo+"_valor").val(0);
		$("#retanqueo"+nro_retanqueo+"_valor_liquidacion").val("");
		$("#retanqueo"+nro_retanqueo+"_intereses").val("");
		$("#retanqueo"+nro_retanqueo+"_seguro").val("");
		$("#retanqueo"+nro_retanqueo+"_cuotasmora").val("");
		$("#retanqueo"+nro_retanqueo+"_segurocausado").val("");
		$("#retanqueo"+nro_retanqueo+"_gastoscobranza").val("");
		$("#retanqueo"+nro_retanqueo+"_totalpagar").val("");
		return false;
	}
	
	if (document.getElementById("retanqueo1_libranza").value != "" && (document.getElementById("retanqueo1_libranza").value == document.getElementById("retanqueo2_libranza").value || document.getElementById("retanqueo1_libranza").value == document.getElementById("retanqueo3_libranza").value))
	ya_existe_libranza_en_credito = 1;
	
	if (document.getElementById("retanqueo2_libranza").value != "" && document.getElementById("retanqueo2_libranza").value == document.getElementById("retanqueo3_libranza").value)
	ya_existe_libranza_en_credito = 1;
	
	if (ya_existe_libranza_en_credito) {
		$("#retanqueo"+nro_retanqueo+"_libranza").val("");
		$("#retanqueo"+nro_retanqueo+"_cuota").val(0);
		$("#retanqueo"+nro_retanqueo+"_valor").val(0);
		$("#retanqueo"+nro_retanqueo+"_valor_liquidacion").val("");
		$("#retanqueo"+nro_retanqueo+"_intereses").val("");
		$("#retanqueo"+nro_retanqueo+"_seguro").val("");
		$("#retanqueo"+nro_retanqueo+"_cuotasmora").val("");
		$("#retanqueo"+nro_retanqueo+"_segurocausado").val("");
		$("#retanqueo"+nro_retanqueo+"_gastoscobranza").val("");
		$("#retanqueo"+nro_retanqueo+"_totalpagar").val("");
		
		alert("La libranza ya esta dentro de los retanqueos de este credito");
		return false;
	}
	
	$.ajax({
    	type: "POST",
		async: false,
		url: "saldo_retanqueo.php",
		data: parametros,
		success: function( response ) {
			if (response) {
				respuesta_split = response.split("|");
				
				for (i = 0; i < respuesta_split.length; i++) {
					campo_split = respuesta_split[i].split("=");
					
					if (campo_split[0].trim() == "mensaje") {
						//alert(campo_split[1].trim());
						$("#retanqueo"+nro_retanqueo+"_libranza").val("");
						$("#retanqueo"+nro_retanqueo+"_cuota").val(0);
						$("#retanqueo"+nro_retanqueo+"_valor").val(0);
						$("#retanqueo"+nro_retanqueo+"_valor_liquidacion").val("");
						$("#retanqueo"+nro_retanqueo+"_intereses").val("");
						$("#retanqueo"+nro_retanqueo+"_seguro").val("");
						$("#retanqueo"+nro_retanqueo+"_cuotasmora").val("");
						$("#retanqueo"+nro_retanqueo+"_segurocausado").val("");
						$("#retanqueo"+nro_retanqueo+"_gastoscobranza").val("");
						$("#retanqueo"+nro_retanqueo+"_totalpagar").val("");
					}
					else {
						$("#"+campo_split[0].trim()).val(campo_split[1].trim());
					}
				}
			}
		}
	});
}

function calcular_aportes() {
	with (document.formato) {
		if (sin_aportes.value == "1") {
			aportes.value = "0";
		}
		else {
<?php

if (strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"])
{

?>
			aportes.value = Math.round(parseInt(salario_basico.value.replace(/\,/g, '')) * <?php echo $porcentaje_aportes / 100.00 ?>);
<?php

}
else
{

?>
			//aportes.value = Math.round((parseInt(salario_basico.value.replace(/\,/g, '')) + parseInt(adicionales.value.replace(/\,/g, ''))) * <?php echo $porcentaje_aportes / 100.00 ?>);
			aportes.value = Math.round(parseInt(salario_basico.value.replace(/\,/g, '')) * <?php echo $porcentaje_aportes / 100.00 ?>);
<?php

}

?>
		}
		separador_miles(aportes);
	}
}

function recalcular() {
	//Servicio Nubeeeeee
	var valor_servicio_nube_valor = <?=$valor_servicio_nube?>;
	var aplicar_servicio_nube = <?=$aplicar_servicio_nube?>;
	var aplicar_seguro_parcial = <?=$aplicar_seguro_parcial?>;
	var iva_legal = <?=$iva?>;

	with (document.formato) {
		if (sin_aportes.value == "1") {
			aportes.value = "0";
		}
		
		total_ingresos.value = Math.round(parseInt(salario_basico.value.replace(/\,/g, '')) + parseInt(adicionales.value.replace(/\,/g, '')) + parseInt(bonificacion.value.replace(/\,/g, '')));
		separador_miles(total_ingresos);

		total_aportes.value = Math.round(parseInt(aportes.value.replace(/\,/g, '')) + parseInt(otros_aportes.value.replace(/\,/g, '')));
		separador_miles(total_aportes);

		ingresos_menos_aportes.value = Math.round(parseInt(total_ingresos.value.replace(/\,/g, '')) - parseInt(total_aportes.value.replace(/\,/g, '')));
		separador_miles(ingresos_menos_aportes);

		if (parseInt(total_ingresos.value.replace(/\,/g, '')) < (parseInt(salario_minimo.value.replace(/\,/g, '')) * 2)) {
			if ( (nivel_contratacion.value.toUpperCase() == "PENSIONADO" <?php if ($_SESSION["FUNC_PENSIONADOS"]) { echo " && 1 == 1"; } else { echo " && 1 == 0"; } ?>) || <?php if ($sector == "PRIVADO") { echo "1 == 1"; } else { echo "1 == 0"; } ?>) {
				salario_libre.value = Math.round(parseInt(ingresos_menos_aportes.value.replace(/\,/g, '')) / 2);
			}else {
				salario_libre.value = parseInt(salario_minimo.value.replace(/\,/g, ''));
			}
		}
		else {
			salario_libre.value = Math.round(parseInt(ingresos_menos_aportes.value.replace(/\,/g, '')) / 2);
			
			if (!((nivel_contratacion.value.toUpperCase() == "PENSIONADO"<?php if ($_SESSION["FUNC_PENSIONADOS"]) { echo " && 1 == 1"; } else { echo " && 1 == 0"; } ?>) || <?php if ($sector == "PRIVADO") { echo "1 == 1"; } else { echo "1 == 0"; } ?>)) {
				if (parseFloat(salario_libre.value) < parseInt(salario_minimo.value.replace(/\,/g, ''))) {
					salario_libre.value = parseInt(salario_minimo.value.replace(/\,/g, ''));
				}
			}
		}
		separador_miles(salario_libre);

<?php

if (!$_SESSION["FUNC_TASASCOMBO"])
{

?>
		if (parseFloat(tasa_interes.value) == <?php echo $tasa_interes_maxima ?>)
			document.getElementById("tasa_interes").style.color = "#000000";
		else
			document.getElementById("tasa_interes").style.color = "#CC0000";
<?php

}

?>

		if (parseFloat(tasa_interes.value) >= <?php echo $tasa_interes_maxima ?>) {
			tipo_credito.value = "CREDITO NORMAL";
		}
		else {
			tipo_credito.value = "RECICLAJE";
		}

		if (parseInt(plazo.value) == <?php echo $plazo_maximo ?>)
			document.getElementById("plazo").style.color = "#000000";
		else
			document.getElementById("plazo").style.color = "#CC0000";
		
		total_cuota.value = "0";

		for (i = 1; i <= parseInt(ultimo_consecutivo_compra_cartera.value); i++) {
			total_cuota.value = parseInt(total_cuota.value) + parseInt(document.getElementById("cuota"+i).value.replace(/\,/g, ''));
		}
		
		separador_miles(total_cuota);
		
		if (parseInt(total_ingresos.value.replace(/\,/g, '')) < (parseInt(salario_minimo.value.replace(/\,/g, '')) * 2)) {
			if ((nivel_contratacion.value.toUpperCase() == "PENSIONADO"<?php if ($_SESSION["FUNC_PENSIONADOS"]) { echo " && 1 == 1"; } else { echo " && 1 == 0"; } ?>) || <?php if ($sector == "PRIVADO") { echo "1 == 1"; } else { echo "1 == 0"; } ?>) {
				opcion_cuota_base = Math.round(parseInt(total_ingresos.value.replace(/\,/g, '')) - parseInt(salario_libre.value.replace(/\,/g, '')) - parseInt(total_egresos.value.replace(/\,/g, '')));
			}
			else {
				opcion_cuota_base = Math.round(parseInt(total_ingresos.value.replace(/\,/g, '')) - parseInt(salario_minimo.value.replace(/\,/g, '')) - parseInt(total_egresos.value.replace(/\,/g, '')));
			}
		}
		else {
			opcion_cuota_base = Math.round(parseInt(total_ingresos.value.replace(/\,/g, '')) - parseInt(salario_libre.value.replace(/\,/g, '')) - parseInt(total_egresos.value.replace(/\,/g, '')));
		}

		otros_descuentos = 0;

		valor_por_millon_seguro.value = ValorPorMillon(id_unidad_negocio.value, nivel_contratacion.value, pagaduria.value, valor_por_millon_seguro.value, fecha_estudio.value, fecha_nacimiento.value, '<?php echo $cod_interno_subestado ?>', aplicar_seguro_parcial, sin_seguro.value, '<?=$_REQUEST["id_simulacion"]?>');
      
    if (valor_por_millon_seguro.value != valor_por_millon_seguroh.value) {
      porcentaje_seguro.value = PorcentajeSeguro(valor_por_millon_seguro.value, parseInt(plazo.value), tasa_interes.value, porcentaje_extraprima.value, sin_seguro.value, aplicar_seguro_parcial);        
      valor_por_millon_seguroh.value = valor_por_millon_seguro.value;
    }
		
		if (tasa_interes.value != tasa_interesh.value) {
			porcentaje_seguro.value = PorcentajeSeguro(valor_por_millon_seguro.value, parseInt(plazo.value), tasa_interes.value, porcentaje_extraprima.value, sin_seguro.value, aplicar_seguro_parcial);
			
			tasa_interesh.value = tasa_interes.value;
		}
		
		if (sin_seguro.value != sin_seguroh.value) {
			porcentaje_seguro.value = PorcentajeSeguro(valor_por_millon_seguro.value, parseInt(plazo.value), tasa_interes.value, porcentaje_extraprima.value, sin_seguro.value, aplicar_seguro_parcial);
			
			sin_seguroh.value = sin_seguro.value;
		}
		
		if (plazo.value != plazoh.value) {
			porcentaje_seguro.value = PorcentajeSeguro(valor_por_millon_seguro.value, parseInt(plazo.value), tasa_interes.value, porcentaje_extraprima.value, sin_seguro.value, aplicar_seguro_parcial);
			
			plazoh.value = plazo.value;
		}

		if (porcentaje_extraprima.value != porcentaje_extraprimah.value) {
			porcentaje_seguro.value = PorcentajeSeguro(valor_por_millon_seguro.value, parseInt(plazo.value), tasa_interes.value, porcentaje_extraprima.value, sin_seguro.value, aplicar_seguro_parcial);
				
			porcentaje_extraprimah.value = porcentaje_extraprima.value; 
		}
		
		descuento3.value = parseFloat(descuento2.value) * <?php echo $iva ?> / 100.00;
		
<?php

if ($sector == "PRIVADO")
{

?>
		descuento3.value = parseFloat(descuento3.value) + parseFloat(descuento1.value) * <?php echo $iva ?> / 100.00;
<?php

}

?>
		descuento6.value = parseFloat(descuento5.value) * <?php echo $iva ?> / 100.00;
		
		suma_descuentos = parseFloat(descuento1.value) + parseFloat(descuento2.value) + parseFloat(descuento3.value) + parseFloat(descuento4.value);
		
		suma_descuentos_recuperate = 0;
		
		if (tipo_producto.value == "1") {
			suma_descuentos_recuperate = parseFloat(descuento5.value) + parseFloat(descuento6.value);
		}

<?php

if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"])
{
	if ($_REQUEST["cedula"])
	{
		$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagad"]."' and estado = '1' order by id_descuento");
	}
	
	if ($_REQUEST["id_simulacion"])
	{
		$descuentos_adicionales = sqlsrv_query($link, "select id_descuento from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");
	}
	
	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales, SQLSRV_FETCH_ASSOC))
	{
	
?>
		suma_descuentos = suma_descuentos + parseFloat(descuentoadicional<?php echo $fila1["id_descuento"] ?>.value);
<?php

	}
}

?>
		retanqueo_total_cuota.value = parseInt(retanqueo1_cuota.value.replace(/\,/g, '')) + parseInt(retanqueo2_cuota.value.replace(/\,/g, '')) + parseInt(retanqueo3_cuota.value.replace(/\,/g, ''));
		separador_miles(retanqueo_total_cuota);

		//total_egresos.value = parseInt(total_aportes.value.replace(/\,/g, '')) + parseInt(total_cuota.value.replace(/\,/g, '')) +    parseInt(retanqueo_total_cuota.value.replace(/\,/g, ''));
        //separador_miles(total_egresos);

		retanqueo_total.value = parseInt(retanqueo1_valor.value.replace(/\,/g, '')) + parseInt(retanqueo2_valor.value.replace(/\,/g, '')) + parseInt(retanqueo3_valor.value.replace(/\,/g, ''));
		separador_miles(retanqueo_total);

		opcion_cuota_cli.value = opcion_cuota_base;
		separador_miles(opcion_cuota_cli);

		opcion_cuota_cli_menos_seguro = parseInt(opcion_cuota_cli.value.replace(/\,/g, '')) * (100.00 - parseFloat(porcentaje_seguro.value)) /100.00;

		if (parseInt(opcion_cuota_cli.value.replace(/\,/g, '')) < 0) {
			opcion_desembolso_cli.value = "MEJORA SALARIO";
			opcion_credito[0].disabled = true;

			document.getElementById("opcion_cuota_cli").style.color = "#CC0000";
			document.getElementById("opcion_desembolso_cli").style.color = "#CC0000";
		}
		else {
			if (parseFloat(tasa_interes.value) != 0) {
				valor_credito_cli = opcion_cuota_cli_menos_seguro * ((Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value)) - 1) / ((parseFloat(tasa_interes.value) / 100.00) * Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value))));
			}
			else {
				valor_credito_cli = 0;
			}
<?php

if ($_SESSION["FUNC_MUESTRACAMPOS2"])
{

?>
			descuento_cuota_manejo_cli = Math.floor(valor_credito_cli / 1000000) * <?php echo $cuota_manejo ?>;
			descuento_dias_ajuste_cli = valor_credito_cli * (parseFloat(tasa_interes.value) / 100.00) / 30 * <?php echo $dias_ajuste ?>;
			descuento_seguro_cli = valor_credito_cli / 1000000 * parseInt(plazo.value) * <?php echo $seguro ?>;

			otros_descuentos = descuento_cuota_manejo_cli + descuento_dias_ajuste_cli + descuento_seguro_cli;
<?php

}

?>
			if (fidelizacion.value == "1") {
				opcion_desembolso_cli.value = Math.round(valor_credito_cli - (valor_credito_cli * suma_descuentos / 100.00) - (parseInt(retanqueo_total.value.replace(/\,/g, '')) * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
			}
			else {
				opcion_desembolso_cli.value = Math.round(valor_credito_cli - (valor_credito_cli * suma_descuentos / 100.00) - (valor_credito_cli * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
			}
			if (!isNaN(opcion_desembolso_cli.value)) {
				separador_miles(opcion_desembolso_cli);
			}
			opcion_credito[0].disabled = false;

			document.getElementById("opcion_cuota_cli").style.color = "#000000";
			document.getElementById("opcion_desembolso_cli").style.color = "#000000";
		}

		opcion_cuota_ccc.value = opcion_cuota_base;
		
		total_valor_pagar.value = "0";

		for (i = 1; i <= parseInt(ultimo_consecutivo_compra_cartera.value); i++) {
			if (document.getElementById("se_compra"+i).value == "SI") {
				opcion_cuota_ccc.value = parseInt(opcion_cuota_ccc.value) + parseInt(document.getElementById("cuota"+i).value.replace(/\,/g, ''));
				total_valor_pagar.value = parseInt(total_valor_pagar.value) + parseInt(document.getElementById("valor_pagar"+i).value.replace(/\,/g, ''));
			}
		}

		opcion_cuota_ccc.value = parseInt(opcion_cuota_ccc.value) + parseInt(retanqueo_total_cuota.value.replace(/\,/g, ''));
		
		separador_miles(opcion_cuota_ccc);
		separador_miles(total_valor_pagar);
		
		opcion_cuota_ccc_menos_seguro = parseInt(opcion_cuota_ccc.value.replace(/\,/g, '')) * (100.00 - parseFloat(porcentaje_seguro.value)) /100.00;
		
		if (parseInt(opcion_cuota_ccc.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_cuota_ccc").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_cuota_ccc").style.color = "#CC0000";
		}

		if (parseFloat(tasa_interes.value) != 0) {
			valor_credito_ccc = opcion_cuota_ccc_menos_seguro * ((Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value)) - 1) / ((parseFloat(tasa_interes.value) / 100.00) * Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value))));
		}
		else {
			valor_credito_ccc = 0;
		}
<?php

if ($_SESSION["FUNC_MUESTRACAMPOS2"])
{

?>
		descuento_cuota_manejo_ccc = Math.floor(valor_credito_ccc / 1000000) * <?php echo $cuota_manejo ?>;
		descuento_dias_ajuste_ccc = valor_credito_ccc * (parseFloat(tasa_interes.value) / 100.00) / 30 * <?php echo $dias_ajuste ?>;
		descuento_seguro_ccc = valor_credito_ccc / 1000000 * parseInt(plazo.value) * <?php echo $seguro ?>;

		otros_descuentos = descuento_cuota_manejo_ccc + descuento_dias_ajuste_ccc + descuento_seguro_ccc;
<?php

}

?>
		if (fidelizacion.value == "1") {
			opcion_desembolso_ccc.value = Math.round(valor_credito_ccc - ((valor_credito_ccc - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (parseInt(retanqueo_total.value.replace(/\,/g, '')) * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		else {
			opcion_desembolso_ccc.value = Math.round(valor_credito_ccc - ((valor_credito_ccc - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (valor_credito_ccc * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		separador_miles(opcion_desembolso_ccc);

		if (parseInt(opcion_desembolso_ccc.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_desembolso_ccc").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_desembolso_ccc").style.color = "#CC0000";
		}

		opcion_cuota_cmp.value = Math.round(opcion_cuota_base + parseInt(total_cuota.value.replace(/\,/g, '')) + parseInt(retanqueo_total_cuota.value.replace(/\,/g, '')));
		separador_miles(opcion_cuota_cmp);

		opcion_cuota_cmp_menos_seguro = parseInt(opcion_cuota_cmp.value.replace(/\,/g, '')) * (100.00 - parseFloat(porcentaje_seguro.value)) /100.00;

		if (parseInt(opcion_cuota_cmp.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_cuota_cmp").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_cuota_cmp").style.color = "#CC0000";
		}

		if (parseFloat(tasa_interes.value) != 0) {
			valor_credito_cmp = opcion_cuota_cmp_menos_seguro * ((Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value)) - 1) / ((parseFloat(tasa_interes.value) / 100.00) * Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value))));
		}
		else {
			valor_credito_cmp = 0;
		}
<?php

if ($_SESSION["FUNC_MUESTRACAMPOS2"])
{

?>
		descuento_cuota_manejo_cmp = Math.floor(valor_credito_cmp / 1000000) * <?php echo $cuota_manejo ?>;
		descuento_dias_ajuste_cmp = valor_credito_cmp * (parseFloat(tasa_interes.value) / 100.00) / 30 * <?php echo $dias_ajuste ?>;
		descuento_seguro_cmp = valor_credito_cmp / 1000000 * parseInt(plazo.value) * <?php echo $seguro ?>;

		otros_descuentos = descuento_cuota_manejo_cmp + descuento_dias_ajuste_cmp + descuento_seguro_cmp;
<?php

}

?>
		if (fidelizacion.value == "1") {
			opcion_desembolso_cmp.value = Math.round(valor_credito_cmp - ((valor_credito_cmp - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (parseInt(retanqueo_total.value.replace(/\,/g, '')) * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		else {
			opcion_desembolso_cmp.value = Math.round(valor_credito_cmp - ((valor_credito_cmp - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (valor_credito_cmp * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		separador_miles(opcion_desembolso_cmp);

		if (parseInt(opcion_desembolso_cmp.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_desembolso_cmp").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_desembolso_cmp").style.color = "#CC0000";
		}

		if (parseInt(opcion_cuota_cso.value.replace(/\,/g, '')) > parseInt(opcion_cuota_ccc.value.replace(/\,/g, ''))) {
			opcion_cuota_cso.value = opcion_cuota_ccc.value;
		}
		
		opcion_cuota_cso_menos_seguro = parseInt(opcion_cuota_cso.value.replace(/\,/g, '')) * (100.00 - parseFloat(porcentaje_seguro.value)) /100.00;

		if (parseInt(opcion_cuota_cso.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_cuota_cso").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_cuota_cso").style.color = "#CC0000";
		}

		if (parseFloat(tasa_interes.value) != 0) {
			valor_credito_cso = opcion_cuota_cso_menos_seguro * ((Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value)) - 1) / ((parseFloat(tasa_interes.value) / 100.00) * Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value))));
		}
		else {
			valor_credito_cso = 0;
		}
<?php

if ($_SESSION["FUNC_MUESTRACAMPOS2"])
{

?>
		descuento_cuota_manejo_cso = Math.floor(valor_credito_cso / 1000000) * <?php echo $cuota_manejo ?>;
		descuento_dias_ajuste_cso = valor_credito_cso * (parseFloat(tasa_interes.value) / 100.00) / 30 * <?php echo $dias_ajuste ?>;
		descuento_seguro_cso = valor_credito_cso / 1000000 * parseInt(plazo.value) * <?php echo $seguro ?>;

		otros_descuentos = descuento_cuota_manejo_cso + descuento_dias_ajuste_cso + descuento_seguro_cso;
<?php

}

?>
		if (fidelizacion.value == "1") {
			opcion_desembolso_cso.value = Math.round(valor_credito_cso - ((valor_credito_cso - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (parseInt(retanqueo_total.value.replace(/\,/g, '')) * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		else {
			opcion_desembolso_cso.value = Math.round(valor_credito_cso - ((valor_credito_cso - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * suma_descuentos / 100.00) - (valor_credito_cso * suma_descuentos_recuperate / 100.00) - <?php echo $descuento_transferencia ?> - otros_descuentos);
		}
		separador_miles(opcion_desembolso_cso);

		if (parseInt(opcion_desembolso_cso.value.replace(/\,/g, '')) > 0) {
			document.getElementById("opcion_desembolso_cso").style.color = "#000000";
		}
		else {
			document.getElementById("opcion_desembolso_cso").style.color = "#CC0000";
		}

		suma_al_presupuesto.value = "0";

		tasa_nominal_fondeo = (Math.pow(1 + <?php echo $tasa_efectiva_fondeo ?>/ 100.00, (1.00 / 12.00)) - 1) * 100.00;

		if (opcion_credito[0].checked == true) {
			cuota_fondeo = opcion_cuota_cli_menos_seguro;
		}
		else if (opcion_credito[1].checked == true) {
			cuota_fondeo = opcion_cuota_ccc_menos_seguro;
		}
		else if (opcion_credito[2].checked == true) {
			cuota_fondeo = opcion_cuota_cmp_menos_seguro;
		}
		else if (opcion_credito[3].checked == true) {
			cuota_fondeo = opcion_cuota_cso_menos_seguro;
		}

		cuota_venta = cuota_fondeo * (1 - (<?php echo $cobertura ?>/ 100.00));

		valor_venta_fondeo = cuota_venta * ((Math.pow(1 + (<?php echo $tasa_nominal_fondeo ?>/ 100.00), parseInt(plazo.value)) - 1) / ((<?php echo $tasa_nominal_fondeo ?>/ 100.00) * Math.pow(1 + (<?php echo $tasa_nominal_fondeo ?>/ 100.00), parseInt(plazo.value))));

		if (parseFloat(tasa_interes.value) != 0) {
			valor_credito1 = cuota_fondeo * ((Math.pow(1 + (<?php echo $tasa_interes_maxima ?>/ 100.00), parseInt(plazo.value)) - 1) / ((<?php echo $tasa_interes_maxima ?>/ 100.00) * Math.pow(1 + (<?php echo $tasa_interes_maxima ?>/ 100.00), parseInt(plazo.value))));
		}
		else {
			valor_credito1 = 0;
		}

		costo_operacion_originadora1 = valor_credito1 * <?php echo $porcentaje_sobre_desm2 ?>;

		if (parseFloat(tasa_interes.value) != 0) {
			valor_credito2 = cuota_fondeo * ((Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value)) - 1) / ((parseFloat(tasa_interes.value) / 100.00) * Math.pow(1 + (parseFloat(tasa_interes.value) / 100.00), parseInt(plazo.value))));
		}
		else {
			valor_credito2 = 0;
		}

		margen2 = valor_venta_fondeo - valor_credito2;

		costo_operacion_fabrica2 = valor_credito2 * <?php echo $porcentaje_sobre_desm1 ?>;

		costo_operacion_originadora2 = (margen2 - costo_operacion_fabrica2) * <?php echo $porcentaje_sobre_util ?>;

		if (costo_operacion_originadora2 < 0) {
			porcentaje_utilidad_originadora = 0;
		}
		else {
			if (parseFloat(costo_operacion_originadora1) != 0) {
				porcentaje_utilidad_originadora = costo_operacion_originadora2 / costo_operacion_originadora1;
			}
			else {
				porcentaje_utilidad_originadora = 0;
			}
		}
		
		if (parseInt(desembolso_cliente.value.replace(/\,/g, '')) >= 0) {
			suma_al_presupuesto.value = Math.round((parseInt(desembolso_cliente.value.replace(/\,/g, '')) + parseInt(total_valor_pagar.value.replace(/\,/g, ''))) * porcentaje_utilidad_originadora);
		}
		separador_miles(suma_al_presupuesto);

		puntaje_decision = 0;

		if (nivel_contratacion.value.toUpperCase() != "PROPIEDAD")
			puntaje_decision = puntaje_decision + <?php echo $puntaje_negado ?>;

		if (nivel_contratacion.value.toUpperCase() == "PERIODO DE PRUEBA" || nivel_contratacion.value.toUpperCase() == "PROVISIONAL"<?php if ($_SESSION["FUNC_PENSIONADOS"]) { ?> || nivel_contratacion.value.toUpperCase() == "PENSIONADO"<?php } ?>)
			puntaje_decision = puntaje_decision - <?php echo $puntaje_negado ?>;

<?php

if ((strtoupper($fila["cargo"]) == "ADMINISTRATIVO" && $_SESSION["FUNC_ADMINISTRATIVOS"]) && $fila["fecha_inicio_labor"] < "2004-01-01")
{

?>
		if (nivel_contratacion.value.toUpperCase() == "PROVISIONAL VACANTE DEFINITIVA" || nivel_contratacion.value.toUpperCase() == "PROVISIONAL VACANTE TEMPORAL")
			puntaje_decision = puntaje_decision - <?php echo $puntaje_negado ?>;
<?php

}

?>
		//if (embargo_actual.value != "NO")
		//	puntaje_decision = puntaje_decision + <?php echo $puntaje_negado ?>;

		//if (parseInt(historial_embargos.value) > 3)
		//	puntaje_decision = puntaje_decision + <?php echo $puntaje_negado ?>;

		if (embargo_alimentos.value != "NO")
			puntaje_decision = puntaje_decision + <?php echo $puntaje_negado ?>;

		if (descuentos_por_fuera.value != "NO")
			puntaje_decision = puntaje_decision + 0.1;

		if (cartera_mora.value != "NO")
			puntaje_decision = puntaje_decision + 0.1;

		if (parseInt(valor_cartera_mora.value.replace(/\,/g, '')) > <?php echo $cartera_castigada_permitida ?>)
			puntaje_decision = puntaje_decision + <?php echo $puntaje_negado ?>;

		if (parseInt(puntaje_datacredito.value) < <?php echo $puntaje_datacredito_minimo ?>)
			puntaje_decision = puntaje_decision + 0.1;

		if (parseInt(desembolso_cliente.value.replace(/\,/g, '')) < 0)
			puntaje_decision = puntaje_decision + 3;

		if (parseInt(puntaje_cifin.value) < <?php echo $puntaje_cifin_minimo ?>)
			puntaje_decision = puntaje_decision + 0.1;

		//valor_descuentos_por_fuera
		puntaje_decision = puntaje_decision + 0;

		if (puntaje_decision > 1) {
			decision_sistema.value = "<?php echo $label_negado ?>";
			//document.getElementById("decision_sistema").style.color = "#CC0000";
		}
		else {
			decision_sistema.value = "<?php echo $label_viable ?>";
			//document.getElementById("decision_sistema").style.color = "#000000";
		}

<?php

if ($_SESSION["FUNC_SUBESTADOS"] && $_REQUEST["tipo"] != "COM")
{

?>
		if (decision.value != decisionh.value) {
			Cargarsubestados('PH' + decision.value, id_subestado);
			
			if (decision.value == "<?php echo $label_negado ?>") {
				document.getElementById("decision").style.color = "#CC0000";
				id_causal.disabled = false;
			}
			else {
				document.getElementById("decision").style.color = "#000000";
				id_causal.disabled = true;
				id_causal.selectedIndex = 0;
			}
		}

		decisionh.value = decision.value;
<?php

}

?>

		valor_credito.value = Math.round(valor_credito2);
		separador_miles(valor_credito);

		//codigo copiado y nuevo
		if (opcion_credito[0].checked == true) {
		if (!isNaN(opcion_desembolso_cli.value)) {
			sin_retanqueos.value = parseInt(opcion_desembolso_cli.value.replace(/\,/g, ''));

			desembolso_cliente.value = parseInt(opcion_desembolso_cli.value.replace(/\,/g, ''));
		}
		}
		else if (opcion_credito[1].checked == true) {
			sin_retanqueos.value = Math.round(parseInt(opcion_desembolso_ccc.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));

			desembolso_cliente.value = Math.round(parseInt(opcion_desembolso_ccc.value.replace(/\,/g, '')) - parseInt(total_valor_pagar.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));
		}
		else if (opcion_credito[2].checked == true) {
			sin_retanqueos.value = Math.round(parseInt(opcion_desembolso_cmp.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));

			desembolso_cliente.value = Math.round(parseInt(opcion_desembolso_cmp.value.replace(/\,/g, '')) - parseInt(total_valor_pagar.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));
		}
		else if (opcion_credito[3].checked == true) {
			sin_retanqueos.value = Math.round(parseInt(opcion_desembolso_cso.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));

			desembolso_cliente.value = Math.round(parseInt(opcion_desembolso_cso.value.replace(/\,/g, '')) - parseInt(total_valor_pagar.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, '')));
		}

		if (opcion_credito[0].checked != true) {
			descuento2_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento2.value) / 100.00);
			descuento3_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento3.value) / 100.00);
		}
		else {
			descuento2_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento2.value) / 100.00);
			descuento3_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento3.value) / 100.00);
		}
		

		//Servicio Nubeeeeee
		if(descuento2_valor.value > valor_servicio_nube_valor && aplicar_servicio_nube){
			descuento8_valor.value = valor_servicio_nube_valor;
			descuento9_valor.value = descuento2_valor.value  - valor_servicio_nube_valor;
			descuento10_valor.value = Math.round((iva_legal * descuento9_valor.value) / 100);
		}else{
			descuento8_valor.value = descuento2_valor.value;
			descuento9_valor.value = 0;
			descuento10_valor.value = 0;
		}

		if(aplicar_servicio_nube){
			if(sin_iva_servicio_nube.checked == false){
				if(parseInt(descuento2_valor.value.replace(/\,/g, '')) > valor_servicio_nube_valor){
					descuento2_valor.value = parseInt(descuento2_valor.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, ''));
					descuento8_valor.value = Math.round(valor_servicio_nube_valor + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
				}else{
					descuento2_valor.value = parseInt(descuento2_valor.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, ''));
					descuento8_valor.value = descuento2_valor.value.replace(/\,/g, '');
				}

			}else{
				if (!isNaN(opcion_desembolso_cli.value)) {
				opcion_desembolso_cli.value = Math.round(parseInt(opcion_desembolso_cli.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
			}
				opcion_desembolso_ccc.value = Math.round(parseInt(opcion_desembolso_ccc.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
				opcion_desembolso_cmp.value = Math.round(parseInt(opcion_desembolso_cmp.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
				opcion_desembolso_cso.value = Math.round(parseInt(opcion_desembolso_cso.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
			}

			opcion_desembolso_cli.value = opcion_desembolso_cli.value.replace(/\,/g, '');
			opcion_desembolso_ccc.value = opcion_desembolso_ccc.value.replace(/\,/g, '');
			opcion_desembolso_cmp.value = opcion_desembolso_cmp.value.replace(/\,/g, '');
			opcion_desembolso_cso.value = opcion_desembolso_cso.value.replace(/\,/g, '');

			if (!isNaN(opcion_desembolso_cli.value)) {
				separador_miles(opcion_desembolso_cli);
			}
			separador_miles(opcion_desembolso_ccc);
			separador_miles(opcion_desembolso_cmp);
			separador_miles(opcion_desembolso_cso);

			if(sin_iva_servicio_nube.checked == false){
				sin_retanqueos.value = Math.round(parseInt(sin_retanqueos.value.replace(/\,/g, '')));
				desembolso_cliente.value = Math.round(parseInt(desembolso_cliente.value.replace(/\,/g, '')));
			}else{
				sin_retanqueos.value = Math.round(parseInt(sin_retanqueos.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
				desembolso_cliente.value = Math.round(parseInt(desembolso_cliente.value.replace(/\,/g, '')) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, '')));
			}
		}

		separador_miles(descuento8_valor);
		separador_miles(descuento9_valor);
		separador_miles(descuento10_valor);
		separador_miles(sin_retanqueos);
		separador_miles(desembolso_cliente);

		if (parseInt(desembolso_cliente.value.replace(/\,/g, '')) > 0) {
			document.getElementById("desembolso_cliente").style.color = "#000000";
		}
		else {
			document.getElementById("desembolso_cliente").style.color = "#CC0000";
		}

		resumen_ingreso.value = Math.round(costo_operacion_originadora2);
		separador_miles(resumen_ingreso);
		
		incor.value = Math.round((<?php echo $porcentaje_incorporacion ?>/ 100.00) * parseInt(desembolso_cliente.value.replace(/\,/g, '')) * porcentaje_utilidad_originadora);
		separador_miles(incor);

		comision.value = Math.round((<?php echo $porcentaje_comision ?>/ 100.00) * parseInt(desembolso_cliente.value.replace(/\,/g, '')) * porcentaje_utilidad_originadora);
		separador_miles(comision);

		utilidad_neta.value = parseInt(resumen_ingreso.value.replace(/\,/g, '')) - parseInt(incor.value.replace(/\,/g, '')) - parseInt(comision.value.replace(/\,/g, ''));
		separador_miles(utilidad_neta);
		
		if (parseFloat(valor_credito2) != 0) {
			sobre_el_credito.value = (parseInt(utilidad_neta.value.replace(/\,/g, '')) / valor_credito2) * 100.00;
		}
		else {
			sobre_el_credito.value = "0";
		}
		sobre_el_credito.value = parseFloat(sobre_el_credito.value).toFixed(2);
<?php

if (!$_REQUEST["tipo"] == "COM")
{

?>

		if (opcion_credito[0].checked != true) {
			descuento1_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento1.value) / 100.00);

			descuento2_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento2.value) / 100.00);

			descuento3_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento3.value) / 100.00);

			descuento4_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuento4.value) / 100.00);
		}
		else {
			descuento1_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento1.value) / 100.00);
			descuento2_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento2.value) / 100.00);
			descuento3_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento3.value) / 100.00);
			descuento4_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento4.value) / 100.00);
		}

		if(aplicar_servicio_nube && sin_iva_servicio_nube.checked == false){
			if(descuento2_valor.value > valor_servicio_nube_valor){
				descuento2_valor.value = parseInt(descuento2_valor.value) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, ''));
			}else{
				descuento2_valor.value = parseInt(descuento2_valor.value) + parseInt(descuento3_valor.value.replace(/\,/g, '')) - parseInt(descuento10_valor.value.replace(/\,/g, ''));
			}
		}
		
		separador_miles(descuento1_valor);
		separador_miles(descuento2_valor);
		separador_miles(descuento3_valor);
		separador_miles(descuento4_valor);
		
		if (fidelizacion.value == "1") {
			descuento5_valor.value = Math.round(parseInt(retanqueo_total.value.replace(/\,/g, '')) * parseFloat(descuento5.value) / 100.00);
		}
		else {
			descuento5_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento5.value) / 100.00);
		}
		separador_miles(descuento5_valor);
		
		if (fidelizacion.value == "1") {
			descuento6_valor.value = Math.round(parseInt(retanqueo_total.value.replace(/\,/g, '')) * parseFloat(descuento6.value) / 100.00);
		}
		else {
			descuento6_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuento6.value) / 100.00);
		}
		separador_miles(descuento6_valor);
		
<?php

	if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"])
	{
		if ($_REQUEST["cedula"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagad"]."' and estado = '1' order by id_descuento");
		}
		
		if ($_REQUEST["id_simulacion"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select id_descuento from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");
		}
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales, SQLSRV_FETCH_ASSOC))
		{
		
?>
		if (opcion_credito[0].checked != true) {
			descuentoadicional<?php echo $fila1["id_descuento"] ?>_valor.value = Math.round((parseInt(valor_credito.value.replace(/\,/g, '')) - parseInt(retanqueo_total.value.replace(/\,/g, ''))) * parseFloat(descuentoadicional<?php echo $fila1["id_descuento"] ?>.value) / 100.00);
		}
		else {
			descuentoadicional<?php echo $fila1["id_descuento"] ?>_valor.value = Math.round(parseInt(valor_credito.value.replace(/\,/g, '')) * parseFloat(descuentoadicional<?php echo $fila1["id_descuento"] ?>.value) / 100.00);
		}
		separador_miles(descuentoadicional<?php echo $fila1["id_descuento"] ?>_valor);
<?php

		}
	}
}

?>
		if (bloqueo_cuota.value == "1") {
			if (opcion_credito[0].checked == true) {
				bloqueo_cuota_valor.value = parseInt(opcion_cuota_cli.value.replace(/\,/g, '')) * 2;
			}
			else if (opcion_credito[1].checked == true) {
				bloqueo_cuota_valor.value = parseInt(opcion_cuota_ccc.value.replace(/\,/g, '')) * 2;
			}
			else if (opcion_credito[2].checked == true) {
				bloqueo_cuota_valor.value = parseInt(opcion_cuota_cmp.value.replace(/\,/g, '')) * 2;
			}
			else if (opcion_credito[3].checked == true) {
				bloqueo_cuota_valor.value = parseInt(opcion_cuota_cso.value.replace(/\,/g, '')) * 2;
			}
			
			if (parseInt(bloqueo_cuota_valor.value) > parseInt(desembolso_cliente.value.replace(/\,/g, ''))) {
				bloqueo_cuota_valor.value = parseInt(desembolso_cliente.value.replace(/\,/g, ''));
			}
			
			separador_miles(bloqueo_cuota_valor);
		}
		else {
			bloqueo_cuota_valor.value = "0";
		}
	}
}

function chequeo_forma() {

	var aplicar_servicio_nube = <?=$aplicar_servicio_nube?>;
	var aplicar_seguro_parcial = <?=$aplicar_seguro_parcial?>;

	with (document.formato) {
		Swal.fire({
			title: 'Por favor aguarde unos segundos',
			text: 'Procesando...'
		});
		Swal.showLoading();

		if($("#id_subestado").val() == 83){
			
			if($("#id_subestado").val() != $("#id_subestadoh").val()){
			
				if($("#id_subestadoh").val() == 85){
					Swal.close();
					$("#requerimientoNuevo").addClass('is-visible');
					return false;
				}else{
					Swal.close();
					alert("No puede cambiar a este subestado");
					return false;
				}	
			}
		}
<?php

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "OUTSOURCING" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") && $_REQUEST["id_simulacion"]) {
	
?>
		if (id_unidad_negocio.value != "<?php echo $id_unidad_negocio ?>" || sin_seguro.value != "<?php echo $sin_seguro ?>" || parseFloat(tasa_interes.value) != "<?php echo floatval($tasa_interes) ?>" || plazo.value != "<?php echo $plazo ?>" || parseInt(opcion_cuota_cso.value.replace(/\,/g, '')) != "<?php echo $opcion_cuota_cso ?>") {
			Swal.close();
			alert("Las condiciones del credito cambiaron. Simulacion no guardada");
			return false;
		}
		if ((opcion_credito[0].checked == true && <?php if ($fila["opcion_credito"] != "CLI") { echo "1 == 1"; } else { echo "1 == 0"; } ?>) || (opcion_credito[1].checked == true && <?php if ($fila["opcion_credito"] != "CCC") { echo "1 == 1"; } else { echo "1 == 0"; } ?>) || (opcion_credito[2].checked == true && <?php if ($fila["opcion_credito"] != "CMP") { echo "1 == 1"; } else { echo "1 == 0"; } ?>) || (opcion_credito[3].checked == true && <?php if ($fila["opcion_credito"] != "CSO") { echo "1 == 1"; } else { echo "1 == 0"; } ?>)) {
			Swal.close();
			alert("Las condiciones del credito cambiaron. Simulacion no guardada");
			return false;
		}
<?php

	for ($i = 1; $i <= $ultimo_consecutivo_compra_cartera + 10; $i++) {

?>
		if (se_compra<?php echo $i ?>.value == "<?php if ($se_compra_si[$i - 1]) { echo "NO"; } else { echo "SI"; } ?>") {
			Swal.close();
			alert("Las condiciones del credito cambiaron. Simulacion no guardada");
			return false;
		}
<?php

	}
}

?>
		if ((id_comercial.value == "") || (cedula.value == "") || (nombre.value == "") || (pagaduria.value == "") || (fecha_estudio.value == "")) {
			Swal.close();
			alert("El formulario esta incompleto. Simulacion no guardada");
			return false;
		}
<?php

$js_validacion_subestado = "1 == 0";

$queryDB = "select id_subestado from subestados where estado = '1' AND cod_interno >= 15 AND cod_interno < 999 order by cod_interno";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	$js_validacion_subestado .= " || id_subestado.value == \"".$fila1["id_subestado"]."\"";
}

?>
		if ((<?php echo $js_validacion_subestado ?>) && ((historial_embargos.value == "") || (puntaje_datacredito.value == "")<?php if (!$_REQUEST["tipo"] == "COM") { ?> || (puntaje_datacredito.value == "-1")<?php } ?>)) {
			Swal.close();
			alert("Debe completar la informacion de analisis de riesgo. Simulacion no guardada");
			return false;
		}
		for (i = 1; i <= parseInt(ultimo_consecutivo_compra_cartera.value); i++) {
			if (document.getElementById("entidad"+i).value != "" && document.getElementById("id_entidad"+i).value == "") {
				Swal.close();
				alert("Debe establecer la entidad para la compra de cartera No. "+i+". Simulacion no guardada");
				return false;
			}
		}
<?php

if (!$_REQUEST["tipo"] == "COM")
{

?>
		if ((decision.value == "<?php echo $label_negado ?>") && (id_causal.value == "")) {
			Swal.close();
			alert("Debe establecer la causal de negacion. Simulacion no guardada");
			return false;
		}
<?php

}

if ($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA")
{

?>
		if ((id_subestado.value == "<?php echo $subestado_valid_doc_proforense ?>") && (solicitud.value == "0")) {
			Swal.close();
			alert("Debe diligenciar primero la solicitud de credito (<?php echo $mensaje_faltantes ?>). Simulacion no guardada");
			return false;
		}
<?php

}

?>
		if ((id_subestado.value == "<?php echo $subestado_soportes_completos ?>") && (solicitud.value == "0")) {
			Swal.close();
			alert("Debe diligenciar primero la solicitud de credito (<?php echo $mensaje_faltantes ?>). Simulacion no guardada");
			return false;
		}
<?php

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
{
	$js_validacion_subestado = "1 == 0";
	
	$queryDB = "select id_subestado from subestados where estado = '1' AND cod_interno > 30 AND cod_interno < 999 order by cod_interno";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		$js_validacion_subestado .= " || id_subestado.value == \"".$fila1["id_subestado"]."\"";
	}
	


}

?>
		if (opcion_credito[0].checked == true) {
			opcion_cuota_visada = parseInt(opcion_cuota_cli.value.replace(/\,/g, ''));
		}
		else if (opcion_credito[1].checked == true) {
			opcion_cuota_visada = parseInt(opcion_cuota_ccc.value.replace(/\,/g, ''));
		}
		else if (opcion_credito[2].checked == true) {
			opcion_cuota_visada = parseInt(opcion_cuota_cmp.value.replace(/\,/g, ''));
		}
		else if (opcion_credito[3].checked == true) {
			opcion_cuota_visada = parseInt(opcion_cuota_cso.value.replace(/\,/g, ''));
		}

		if (valor_visado.value != "" && valor_visado.value != "0" && parseInt(valor_visado.value.replace(/\,/g, '')) != opcion_cuota_visada) {
			Swal.close();
			alert("El Valor Visado no coincide con el valor de la cuota. Simulacion no guardada");
			return false;
		}
		
		if (id_subestado.value == "<?php echo $subestado_confirmado ?>" && (fecha_llamada_clientef.value == "" || fecha_llamada_clienteh.value == "" || fecha_llamada_clientej.value == "")) {
			Swal.close();
			alert("Debe establecer la fecha y hora de la llamada de confirmacion del cliente. Simulacion no guardada");
			return false;
		}
		if (id_subestado.value == "<?php echo $subestado_desembolso_cliente ?>" && (nro_cuenta.value == "" || tipo_cuenta.value == "" || id_banco.value == "")) {
			Swal.close();
			alert("Debe establecer la informacion de la cuenta bancaria. Simulacion no guardada");
			return false;
		}
<?php

if ($_REQUEST["id_simulacion"] && !$_REQUEST["tipo"] == "COM" && !($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" && $estado == "ING"))
{

?>
		if (id_subestado.value == "") {
			Swal.close();
			alert("Debe establecer un subestado. Simulacion no guardada");
			return false;
		}
<?php

}

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
{

?>
		if ((id_subestado.value == "<?php echo $subestado_visado ?>" || id_subestado.value == "<?php echo $subestado_confirmado ?>" || id_subestado.value == "<?php echo $subestado_desembolso ?>") && (id_caracteristica.value == "")) {
			Swal.close();
			alert("Debe establecer una caracteristica. Simulacion no guardada");
			return false;
		}
<?php

}

?>
		if ( ((porcentaje_seguro.value == "" || porcentaje_seguro.value == "0") && (sin_seguro.value == "0")) && decision.value == 'VIABLE' ) {
			Swal.close();
			console.log(porcentaje_seguro.value);
			alert("El porcentaje de seguro no es valido. Simulacion no guardada");
			return false;
		}
<?php

if ($_REQUEST["id_simulacion"])
{

?>
		observaciones.value = document.getElementById("frm_observaciones").contentWindow.formato.observacion.value;
<?php

}

?>
		if (id_subestado.value != "" && observaciones.value == "") {
			Swal.close();
			alert("Debe digitar una observacion. Simulacion no guardada");
			return false;
		}
		
		ReplaceComilla(observaciones);

<?php
if (($_SESSION["S_TIPO"] == "OFICINA" && $_SESSION["S_TIPO"] == "ANALISTA_REFERENCIA") && $id_subestado == 56) { ?>
	if (proposito.value == "" || proposito.value == "0") {
		Swal.close();
		alert("Debe establecer el proposito del credito en la sección de informacion general");
		return false;
	}
	<?php
}
?>
		
		if (parseInt(valor_por_millon_seguro.value) != parseInt(ValorPorMillon(id_unidad_negocio.value, nivel_contratacion.value, pagaduria.value, valor_por_millon_seguro.value, fecha_estudio.value, fecha_nacimiento.value, '<?php echo $cod_interno_subestado ?>', aplicar_seguro_parcial, sin_seguro.value, '<?=$_REQUEST["id_simulacion"]?>'))) {
			if (sin_seguro.value == "0") {
				valor_credito_anterior.value = valor_credito.value;
				desembolso_cliente_anterior.value = desembolso_cliente.value;
				
				alert("La simulacion sera reliquidada por cambio en el valor por millon del seguro");
			}
		}
		
		recalcular();
		
		setTimeout(function(){ Swal.close(); submit(); }, 3000);
	}
}

</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>	
        <td>
            <input type="hidden" id="s_login" value="<?php echo $_SESSION["S_LOGIN"]; ?>">
            <input type="hidden" id="s_apellidos" value="<?php echo $_REQUEST['apellidos']; ?>">
        </td>
    </tr>
<tr>
	<td valign="top" width="18"><?php if (!$_REQUEST["id_cazador"]) { ?><?php if (!$_REQUEST["back"]) { $_REQUEST["back"] = "simulaciones"; } ?><a href="<?php echo $_REQUEST["back"] ?>.php?id_simulacion_buscar=<?php echo $_REQUEST["id_simulacion"] ?>& fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php } else { ?><a href="cazador.php"><?php } ?><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Simulador<?php if ($_REQUEST["tipo"] == "COM") { ?> Comercial<?php }  else { ?> Oficina<?php } ?></b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="simulador2.php" onSubmit="return chequeo_forma()">
<table border="3" cellspacing=1 cellpadding=2 width="95%">
<?php

if ($_REQUEST["tipo"] == "COM")
{

?>
<input type="hidden" name="id_cazador" value="<?php echo $_REQUEST["id_cazador"] ?>">
<input type="hidden" name="fecha_estudio" value="<?php echo $fecha_estudio ?>">
<input type="hidden" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento ?>">
<input type="hidden" name="meses_antes_65" value="<?php echo $meses_antes ?>">
<input type="hidden" name="fecha_inicio_labor" value="<?php echo $fecha_inicio_labor ?>">
<input type="hidden" name="medio_contacto" value="<?php echo $medio_contacto ?>">
<input type="hidden" name="sin_aportes" value="<?php echo $sin_aportes ?>">
<input type="hidden" name="salario_basico" value="<?php echo number_format($salario_basico, 0, ".", ",") ?>">
<input type="hidden" name="bonificacion" value="<?php echo number_format($bonificacion, 0, ".", ",") ?>">
<input type="hidden" name="total_ingresos" value="<?php echo number_format($total_ingresos, 0, ".", ",") ?>">
<input type="hidden" name="aportes" value="<?php echo number_format($aportes, 0, ".", ",") ?>">
<input type="hidden" name="otros_aportes" value="<?php echo number_format($otros_aportes, 0, ".", ",") ?>">
<input type="hidden" name="total_aportes" value="<?php echo number_format($total_aportes, 0, ".", ",") ?>">
<input type="hidden" name="total_egresos" value="<?php echo number_format($total_egresos, 0, ".", ",") ?>">
<input type="hidden" name="ingresos_menos_aportes" value="<?php echo number_format($ingresos_menos_aportes, 0, ".", ",") ?>">
<input type="hidden" name="nivel_contratacion" value="<?php echo $nivel_contratacion ?>">
<input type="hidden" name="cartera_mora" value="<?php echo $cartera_mora ?>">
<input type="hidden" name="embargo_actual" value="<?php echo $embargo_actual ?>">
<input type="hidden" name="historial_embargos" value="<?php echo $historial_embargos ?>">
<input type="hidden" name="embargo_alimentos" value="<?php echo $embargo_alimentos ?>">
<input type="hidden" name="embargo_centrales" value="<?php echo $embargo_centrales ?>">
<?php if (!$_SESSION["FUNC_MUESTRACAMPOS2"]) { ?><input type="hidden" name="descuentos_por_fuera" value="<?php echo $descuentos_por_fuera ?>"><?php } ?>
<input type="hidden" name="valor_cartera_mora" value="<?php echo number_format($valor_cartera_mora, 0, ".", ",") ?>">
<input type="hidden" name="clave" value="<?php echo $clave ?>">
<input type="hidden" name="puntaje_datacredito" value="<?php echo $puntaje_datacredito ?>">
<input type="hidden" name="puntaje_cifin" value="<?php echo $puntaje_cifin ?>">
<?php if (!$_SESSION["FUNC_MUESTRACAMPOS2"]) { ?><input type="hidden" name="valor_descuentos_por_fuera" value="<?php echo number_format($valor_descuentos_por_fuera, 0, ".", ",") ?>"><?php } ?>
<?php if (!$_SESSION["FUNC_MUESTRACAMPOS1"]) { ?><input type="hidden" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>"><?php } ?>
<input type="hidden" name="calif_sector_financiero" value="<?php echo $calif_sector_financiero ?>">
<input type="hidden" name="calif_sector_real" value="<?php echo $calif_sector_real ?>">
<input type="hidden" name="calif_sector_cooperativo" value="<?php echo $calif_sector_cooperativo ?>">
<input type="hidden" name="id_unidad_negocio" value="<?php echo $id_unidad_negocio ?>">
<input type="hidden" name="sin_seguro" value="<?php echo $sin_seguro ?>">
<input type="hidden" name="sin_seguroh" value="<?php echo $sin_seguro ?>">
<input type="hidden" name="sin_seguroh2" value="<?php echo $sin_seguro ?>">
<input type="hidden" name="valor_seguro" value="<?php echo number_format($valor_seguro, 0, ".", ",") ?>">
<input type="hidden" name="id_plan_seguro" value="<?php echo $id_plan_seguro ?>">
<input type="hidden" name="id_plan_seguroh" value="<?php echo $id_plan_seguro ?>">
<?php

	for ($i = 1; $i <= $ultimo_consecutivo_compra_cartera; $i++)
	{

?>
<input type="hidden" id="cuota<?php echo $i ?>" name="cuota<?php echo $i ?>" value="<?php echo number_format($cuota[$i - 1], 0, ".", ",") ?>">
<?php if (!$_SESSION["FUNC_MUESTRACAMPOS2"]) { ?><input type="hidden" id="valor_pagar<?php echo $i ?>" name="valor_pagar<?php echo $i ?>" value="<?php echo number_format($valor_pagar[$i - 1], 0, ".", ",") ?>"><?php } ?>
<input type="hidden" id="entidadcarta<?php echo $i ?>" name="entidadcarta<?php echo $i ?>" value="<?php echo str_replace("\"", "&#34;", $entidadcarta[$i - 1]) ?>">
<input type="hidden" id="dias_entrega<?php echo $i ?>" name="dias_entrega<?php echo $i ?>" value="<?php echo $dias_entrega[$i - 1] ?>">
<input type="hidden" id="dias_vigencia<?php echo $i ?>" name="dias_vigencia<?php echo $i ?>" value="<?php echo $dias_vigencia[$i - 1] ?>">
<input type="hidden" id="estadocarta<?php echo $i ?>" name="estadocarta<?php echo $i ?>" value="NO SOLICITADA">
<input type="hidden" id="fecha_sugerida<?php echo $i ?>" name="fecha_sugerida<?php echo $i ?>" value="<?php echo $fecha_sugerida[$i - 1] ?>">
<input type="hidden" id="fecha_solicitudcarta<?php echo $i ?>" name="fecha_solicitudcarta<?php echo $i ?>" value="<?php echo $fecha_solicitudcarta[$i - 1] ?>">
<input type="hidden" id="fecha_entrega<?php echo $i ?>" name="fecha_entrega<?php echo $i ?>" value="<?php echo $fecha_entrega[$i - 1] ?>">
<input type="hidden" id="fecha_vencimiento<?php echo $i ?>" name="fecha_vencimiento<?php echo $i ?>" value="<?php echo $fecha_vencimiento[$i - 1] ?>">
<input type="hidden" id="nombre_grabado<?php echo $i ?>" name="nombre_grabado<?php echo $i ?>" value="<?php echo $nombre_grabado[$i - 1] ?>">
<?php

	}

?>
<input type="hidden" name="total_cuota" value="<?php echo number_format($total_cuota, 0, ".", ",") ?>">
<input type="hidden" name="total_se_compra" value="<?php echo $total_se_compra ?>">
<input type="hidden" name="nro_libranza" value="<?php echo $nro_libranza ?>">
<input type="hidden" name="valor_visado" value="<?php echo number_format($valor_visado, 0, ".", ",") ?>">
<input type="hidden" name="bloqueo_cuota" value="<?php echo $bloqueo_cuota ?>">
<input type="hidden" name="bloqueo_cuota_valor" value="<?php echo number_format($bloqueo_cuota_valor, 0, ".", ",") ?>">
<input type="hidden" name="id_subestado" value="<?php echo $id_subestado ?>">
<input type="hidden" name="id_subestadoh" id="id_subestadoh" value="<?php echo $id_subestado ?>">
<input type="hidden" name="nro_cuenta" value="<?php echo $nro_cuenta ?>">
<input type="hidden" name="tipo_cuenta" value="<?php echo $tipo_cuenta ?>">
<input type="hidden" name="id_banco" value="<?php echo $id_banco ?>">
<input type="hidden" name="id_causal" value="<?php echo $id_causal ?>">
<input type="hidden" name="id_caracteristica" value="<?php echo $id_caracteristica ?>">
<input type="hidden" name="calificacion" value="<?php echo $calificacion ?>">
<input type="hidden" name="dia_confirmacion" value="<?php echo $dia_confirmacion ?>">
<input type="hidden" name="dia_vencimiento" value="<?php echo $dia_vencimiento ?>">
<input type="hidden" name="status" value="<?php echo $status ?>">
<input type="hidden" name="valor_credito" value="<?php echo number_format($valor_credito, 0, ".", ",") ?>">
<input type="hidden" name="descuento1" value="<?php echo $descuento1 ?>">
<input type="hidden" name="descuento2" value="<?php echo $descuento2 ?>">
<input type="hidden" name="descuento3" value="<?php echo $descuento3 ?>">
<input type="hidden" name="descuento4" value="<?php echo $descuento4 ?>">
<input type="hidden" name="descuento5" value="<?php echo $descuento5 ?>">
<input type="hidden" name="descuento6" value="<?php echo $descuento6 ?>">
<?php

	if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"])
	{
		if ($_REQUEST["cedula"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagad"]."' and estado = '1' order by id_descuento");
		}
		
		if ($_REQUEST["id_simulacion"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select id_descuento, porcentaje from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");
		}
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales, SQLSRV_FETCH_ASSOC))
		{
		
?>
<input type="hidden" name="descuentoadicional<?php echo $fila1["id_descuento"] ?>" value="<?php echo $fila1["porcentaje"] ?>">
<?php

		}
	}
	
?>
<input type="hidden" name="tipo_producto" value="<?php echo $tipo_producto ?>">
<input type="hidden" name="porcentaje_seguro" value="<?php echo $porcentaje_seguro ?>">
<input type="hidden" name="salario_minimo" value="<?php echo $salario_minimo ?>">
<tr>
	<td valign="top">
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
<?php

	if ($_SESSION["S_TIPO"] != "COMERCIAL"){

?>
		<tr>
			<td>COMERCIAL</th>
			<td>
				<select name="id_comercial" style="background-color:#EAF1DD; width:277px"  onChange="if (document.formato.id_comercialh.value != '' && document.formato.cedula.value != '') { if (confirm('Al cambiar el Comercial, se cargara de cero la simulacion. Desea continuar?') == true) { window.location.href='?tipo=COM&cedula='+document.formato.cedula.value+'&id_comercial='+this.value; } else { this.value = document.formato.id_comercialh.value;} } document.formato.id_comercialh.value=this.value;">
					<option value=""></option>
<?php

		$queryDB = "SELECT id_usuario, nombre, apellido from usuarios where (id_usuario IN (SELECT id_usuario FROM oficinas_usuarios WHERE id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')) AND tipo <> 'MASTER' AND tipo = 'COMERCIAL' AND estado = '1'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA")
			$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')"; //AND si.telemercadeo = '0'";
		
		//if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		//	$queryDB .= " AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
			$queryDB .= " AND (freelance = '1' OR outsourcing = '1')";// AND si.telemercadeo IN ('0','1')";
		
		//if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		//	$queryDB .= " AND si.telemercadeo = '1'";
		
		$queryDB .= ") OR id_usuario = '".$id_comercial."'";
		
		if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION") {
			$queryDB .= " UNION select id_usuario, nombre, apellido from usuarios where ((id_usuario IN (select id_usuario from oficinas_usuarios) AND tipo <> 'MASTER' AND tipo = 'COMERCIAL' AND estado = '1')";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
			
			$queryDB .= ") OR id_usuario = '".$id_comercial."'";
		}
		
		//echo $queryDB;
		
		$queryDB .= " order by nombre, apellido, id_usuario";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
			if ($fila1["id_usuario"] == $id_comercial)
				$selected_comercial = " selected";
			else
				$selected_comercial = "";
			
			echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_comercial.">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
		}
		
?>
				</select>
				<input type="hidden" name="id_comercialh" value="<?php echo $id_comercial ?>">
				&nbsp;&nbsp;&nbsp;TELEMERCADEO<input type="checkbox" name="telemercadeo_checked" value="1" onChange="if (this.checked == true) { document.formato.telemercadeo.value = '1'; } else { document.formato.telemercadeo.value = '0'; }"<?php echo $telemercadeo_checked ?><?php if ($inhabilita_telemercadeo) { ?> disabled<?php } ?>>
				<input type="hidden" name="telemercadeo" value="<?php echo $telemercadeo ?>">
			</td>
		</tr>
<?php

	}
	else
	{

?>
		<input type="hidden" name="id_comercial" value="<?php echo $id_comercial ?>">
		<tr>
			<td>TELEMERCADEO</td>
			<td>
				<input type="checkbox" name="telemercadeo_checked" value="1" onChange="if (this.checked == true) { document.formato.telemercadeo.value = '1'; } else { document.formato.telemercadeo.value = '0'; }"<?php echo $telemercadeo_checked ?><?php if ($inhabilita_telemercadeo) { ?> disabled <?php } ?>>
				<input type="hidden" name="telemercadeo" value="<?php echo $telemercadeo ?>">
			</td>
		</tr>
<?php

	}
?>
		<tr>
			<td>VERIFICACIÓN OTP</td>
			<td>
				<?php if($validacion_otp <> 0){ ?>
					<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(6, 175, 116);" value="VERIFICADO POR OTP" readonly>
					<?php
				}else{ ?>
					<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(243 204 48);" value="SIN VERFICACIÓN OTP" readonly>
				<?php } ?>
			</td>
		</tr>

	<?php
	if($oficina_ado == 1) { ?>
		<tr>
			<td>VERIFICACIÓN ADO</td>
			<td>
				<input type="text" id="verificacion" style="width:75%; font-weight:bold; color: white; text-align:center;" readonly> <img id="btnVerificacionID" src="../images/reenviar.png" style="vertical-align: middle; float: right; margin-top: 2px; cursor: pointer; display: none;" onclick="reenviarCorreoValidacionID(this,'<?=$mail;?>','<?=$nombre;?>');" width="50px" title="Reenviar Correo" />
				<input type="button" onclick="window.open('capturasValidacionID.php?id_simulacion=<?=$_REQUEST["id_simulacion"]?>', 'Ver Imagenes','toolbars=yes,scrollbars=yes,resizable=yes,width=700,height=700,top=0,left=0');" title="Click Para Ver las Capturas Del Cliente" name="Ver Imagenes" value="Ver Imagenes" id="verImgValidacion" style="cursor:  pointer; display: none;">
			</td>
		</tr>
		<tr>
			<td colspan="4" id="textoMensajeAdo" style="padding-bottom: 10px; color: red; font-weight: 900; font-size: 15px !important;"></td>
		</tr>	
	<?php } ?>
		<tr>
			<td>N&Uacute;MERO DE C&Eacute;DULA</td>
			<td><input type="text" name="cedula" value="<?php echo $cedula ?>" <?php if (!$_REQUEST["id_simulacion"]) { ?> onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { if (document.formato.id_comercial.value == '') { alert('Debe seleccionar primero el Comercial'); this.value=''; return false } else { window.location.href='?tipo=COM&cedula='+this.value<?php if ($_SESSION["S_TIPO"] != "COMERCIAL") { ?>+'&id_comercial='+document.formato.id_comercial.options[document.formato.id_comercial.selectedIndex].value<?php } ?>+'&telemercadeo='+document.formato.telemercadeo.value; } }" style="width:100%; background-color:#EAF1DD"<?php } else { ?> style="width:100%;" readonly <?php } ?>></td>
		</tr>
		<tr>
			<td>TEL&Eacute;FONO</td>
			<td><input type="text" name="telefono" value="<?php echo $telefono ?>" maxlength="50" style="width:100%; background-color:#EAF1DD"></td>
		</tr>
		<tr>
			<td>CELULAR</td>
			<td><input type="text" name="celular" value="<?php echo $celular ?>" maxlength="50" <?php if($validacion_otp==1 && $_SESSION["S_TIPO"] != "ADMINISTRADOR"){ ?> style="width:100%;" readonly <?php } else {?> style="width:100%; background-color:#EAF1DD;" <?php } ?>></td>
		</tr>
		<tr>
			<td>DIRECCI&Oacute;N</td>
			<td><input type="text" name="direccion" value="<?php echo $direccion ?>" maxlength="255" style="width:100%; background-color:#EAF1DD"></td>
		</tr>
		<tr>
			<td>CIUDAD</td>
			<td><select name="ciudad_residencia" style="width:100%; background-color:#EAF1DD">
					<option value=""></option>
<?php

	$queryDB = "select cod_municipio, municipio from ciudades order by municipio";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($fila1["cod_municipio"] == $ciudad_residencia)
			$selected_ciudad = " selected";
		else
			$selected_ciudad = "";
		
		echo "<option value=\"".$fila1["cod_municipio"]."\"".$selected_ciudad.">".($fila1["municipio"])."</option>\n";
	}
	
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>E-MAIL</td>
			<td><input type="text" name="mail" value="<?php echo $mail ?>" maxlength="50" style="width:100%; background-color:#EAF1DD"></td>
		</tr>
		<tr>
			<td>ADICIONALES S&Oacute;LO (AA)</td>
			<td><input type="text" name="adicionales" value="<?php echo number_format($adicionales, 0, ".", ",") ?>" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="width:100%; text-align:right; background-color:#EAF1DD"></td>
		</tr>
		<tr>
			<td>PROPOSITO</td>
			<td>
				<?php 
					if($_SESSION['S_TIPO'] == 'OFICINA' && $_SESSION['S_SUBTIPO'] == 'ANALISTA_REFERENCIA'){
						$estiloProposito = 'background-color:#EAF1DD;';
					}else{
						$estiloProposito = 'pointer-events: none;';
					}					
				?>
				<select name="proposito" id="proposito" style="width:250px; <?=$estiloProposito?>" required>
					<option value="0"></option>
					<?php
						$queryDB = "SELECT * FROM propositos_credito";					
						$rs1 = sqlsrv_query($link, $queryDB);
						
						while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
							if ($fila1["id_proposito"] == $proposito){
								echo "<option value=\"".$fila1["id_proposito"]."\" selected>".($fila1["proposito"])."</option>\n";
							}
							else{
								if($_SESSION['S_TIPO'] == 'OFICINA' && $_SESSION['S_SUBTIPO'] == 'ANALISTA_REFERENCIA'){
									echo "<option value=\"".$fila1["id_proposito"]."\">".($fila1["proposito"])."</option>\n";
								}
							}
						}					
					?>
				</select>
			</td>
		</tr>
		</table>
		</div>
		<br>
		<h2>INFORMACI&Oacute;N GENERAL</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
			<tr>
				<td>VERIFICACIÓN OTP</td>
				<td>
					<?php if($validacion_otp <> 0){ ?>
						<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(6, 175, 116);" value="VERIFICADO POR OTP" readonly>
						<?php
					}else{ ?>
						<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(243 204 48);" value="SIN VERFICACIÓN OTP" readonly>
					<?php } ?>
				</td>
				<td colspan="2"></td>
			</tr>
			<?php
			if($oficina_ado == 1) {?>
				<tr>
					<td>VERIFICACIÓN ADO</td>
					<td><input type="text" id="verificacion" size="32" readonly style="font-weight:bold; color: white; text-align:center;"></td>
					<td><input type="button" onclick="window.open('capturasValidacionID.php?id_simulacion=<?=$_REQUEST["id_simulacion"]?>', 'Ver Imagenes','toolbars=yes,scrollbars=yes,resizable=yes,width=700,height=700,top=0,left=0');" title="Click Para Ver las Capturas Del Cliente" name="Ver Imagenes" value="Ver Imagenes" id="verImgValidacion" style="cursor:  pointer; display: none;"></td>
					<td><img id="btnVerificacionID" src="../images/reenviar.png" style="vertical-align: middle; cursor: pointer; display: none;" onclick="reenviarCorreoValidacionID(this,'<?=$mail;?>','<?=$nombre;?>');" width="50px" title="Reenviar Correo" /></td>
				</tr>
				<tr>
					<td colspan="4" id="textoMensajeAdo" style=" padding-bottom: 10px; color: red; font-weight: 900; font-size: 15px !important;"></td>
				</tr>
			<?php } ?>
			<tr>
				<td>NOMBRE</td>
				<td><input type="text" name="nombre" value="<?php echo $nombre ?>" style="width:100%;" readonly></td>
			</tr>
			<tr>
				<td>SEXO</td>
				<td><select id="sexo" name="sexo" style="width:100%;" readonly>
						<option <?php if($sexo == ''){ echo 'selected'; } ?> value=""></option>
						<option <?php if($sexo == 'M'){ echo 'selected'; } ?> value="M">MASCULINO</option>
						<option <?php if($sexo == 'F'){ echo 'selected'; } ?> value="F">FEMENINO</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>PAGADUR&Iacute;A</td>
				<td><input type="text" id="pagaduria" name="pagaduria" value="<?php echo $pagaduria ?>" style="width:100%;" readonly></td>
			</tr>
			<tr>
				<td>P.A.</td>
				<td><input type="text" name="pa" value="<?php echo $pa ?>" style="width:100%;" readonly></td>
			</tr>
			<tr>
				<td style="vertical-align:top">INSTITUCI&Oacute;N / ASOCIACI&Oacute;N</td>
				<td><input type="text" name="institucion" value="<?php echo $institucion; ?>" style="width:100%; background-color:#EAF1DD"></td>
			</tr>
			<tr>
				<td>PLAZO M&Aacute;XIMO</td>
				<td align="right"><input type="text" name="plazo_maximo" value="<?php echo $plazo_maximo ?>" style="width:100%; font-weight:bold; text-align:center; background-color:#8DB4E3" readonly></td>
			</tr>
			<tr>
				<td>SALARIO LIBRE MENSUAL</td>
				<td align="right"><input type="text" name="salario_libre" value="<?php echo number_format($salario_libre, 0, ".", ",") ?>" style="width:100%; text-align:right; font-weight:bold; background-color:#8DB4E3" readonly></td>
			</tr>
		</table>
		</div>
<?php

	if ($_SESSION["FUNC_MUESTRACAMPOS2"])
	{

?>
		<br>
		<h2>AN&Aacute;LISIS DE RIESGO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td>¿DESCUENTO POR FUERA?</td>
			<td><input type="text" name="descuentos_por_fuera" value="<?php echo $descuentos_por_fuera ?>" style="width:100%; text-align:center; font-weight:bold;" readonly></td>
		</tr>
		<tr>
			<td>VALOR DESCUENTO POR FUERA</td>
			<td><input type="text" name="valor_descuentos_por_fuera" value="<?php echo number_format($valor_descuentos_por_fuera, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="width:100%; text-align:right; background-color:#EAF1DD"></td>
		</tr>
		</table>
		</div>
<?php

	}

?>
		<br>
		<h2>CONDICIONESwww DEL CR&Eacute;DITO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td>TASA DE INTER&Eacute;S DEL CR&Eacute;DITO</td>
<?php

	if ($_SESSION["FUNC_TASASCOMBO"])
	{

?>
			<td>
				<select id="tasa_interes" name="tasa_interes" <?=$attr_tasa_interes?> style="width:100%; background-color:#EAF1DD;" onChange="recalcular()">
					<option value="<?php echo $tasa_interes_a ?>"<?php echo $tasa_interes_a_selected ?> style="color:#CC0000;">A</option>
					<option value="<?php echo $tasa_interes_b ?>"<?php echo $tasa_interes_b_selected ?> style="color:#CC0000;">B</option>
					<option value="<?php echo $tasa_interes_c ?>"<?php echo $tasa_interes_c_selected ?>>C</option>
<?php

		if (!$tasa_interes_a_selected && !$tasa_interes_b_selected && !$tasa_interes_c_selected)
		{
		
?>
					<option value="<?php echo $tasa_interes ?>" selected><?php echo $tasa_interes ?></option>
<?php

		}
		
?>
				</select>
			</td>
<?php

	}
	else
	{
		if ($_SESSION["FUNC_TASASPLAZO"])
		{

?>
			<td>
				<select id="tasa_interes" name="tasa_interes" <?=$attr_tasa_interes?> style="width:100%; background-color:#EAF1DD;" dir="rtl" onChange="LeerDescuentos(document.formato.id_unidad_negocio.value, document.formato.plazo.value, this.value); recalcular();">
<?php

			$queryDB = "SELECT cast(t2.tasa_interes as float) + 0 as tasa_interes from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa IN (select id_tasa from tasas".$sufijo_sector." where '".$plazo."' >= plazoi AND '".$plazo."' <= plazof)";
			
			if ($sin_seguro)
				$queryDB .= " AND t2.sin_seguro = '1'";
			
			$queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
			
			$queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
			
			if (strtoupper($nivel_contratacion) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";
			
			$queryDB .= ") order by t2.tasa_interes DESC";
			
			$rs1 = sqlsrv_query($link, $queryDB);
			
			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
			{
				if ($fila1["tasa_interes"] == $tasa_interes)
				{
					$selected_tasa = " selected";
					$existe_tasa = 1;
				}
				else
					$selected_tasa = "";
				
				echo "<option value=\"".$fila1["tasa_interes"]."\"".$selected_tasa.">".$fila1["tasa_interes"]."</option>\n";
			}
			
			if (!$existe_tasa)
			{
			
?>
					<option value="<?php echo $tasa_interes ?>" selected><?php echo $tasa_interes ?></option>
<?php

			}
			
?>
				</select>
				<input type="hidden" name="tasa_interesh" value="<?php echo floatval($tasa_interes) ?>">
			</td>
<?php

		}
		else
		{
		
?>
			<td><input type="text" id="tasa_interes" name="tasa_interes" value="<?php echo $tasa_interes ?>" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $tasa_interes ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $tasa_interes ?>'; } if (parseFloat(this.value) > <?php echo $tasa_interes_maxima ?>) { this.value = '<?php echo $tasa_interes ?>'; alert('La tasa de interes no debe ser mayor a <?php echo $tasa_interes_maxima ?>'); } recalcular(); }" style="width:100%; text-align:center;<?php if (!$_SESSION["FUNC_TASASPLAZO"]) { ?> background-color:#EAF1DD;<?php if ($tasa_interes < $tasa_interes_maxima) { ?> color:#CC0000;<?php } ?>"<?php } else { ?>" readonly<?php } ?>></td>
<?php

		}
	}

?>
		</tr>
		<tr>
		<td>PLAZO SOLICITADO PARA EL CR&Eacute;DITO</td>
			<td>
				<input type="text" id="plazo" name="plazo" value="<?php echo $plazo ?>" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $plazo ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $plazo ?>'; } /*if (parseInt(this.value) > <?php echo $plazo_maximo_segun_edad ?>) { this.value = '<?php echo $plazo ?>'; alert('El plazo no debe ser mayor a <?php echo $plazo_maximo_segun_edad ?>'); }*/ <?php if ($_SESSION["FUNC_TASASPLAZO"]) { ?> if (CargarTasas(document.formato.id_unidad_negocio.value, this.value, document.formato.sin_seguro.value) == '0') { this.value = '<?php echo $plazo ?>'; CargarTasas(document.formato.id_unidad_negocio.value, this.value, document.formato.sin_seguro.value); alert('No exiten Tasas con estas condiciones. \n\nPor favor validar:\n - Unidad Negocio\n - Plazo\n - Kplus'); }<?php } ?> recalcular(); }" style="width:100%; text-align:center; background-color:#EAF1DD;<?php if ($plazo < $plazo_maximo) { ?> color:#CC0000;<?php } ?>">
				<input type="hidden" name="plazoh" value="<?php echo $plazo ?>">
			</td>
		</tr>
<!--		<tr>
			<td>TIPO DE CR&Eacute;DITO</td>
			<td><input type="text" name="tipo_credito" value="<?php echo $tipo_credito ?>" style="width:100%; text-align:center; font-weight:bold;" readonly></td>
		</tr>-->
		<input type="hidden" name="tipo_credito" value="<?php echo $tipo_credito ?>">
<?php

	if ($_SESSION["FUNC_MUESTRACAMPOS1"])
	{

?>
<!--		<tr>
			<td>SUMA AL PRESUPUESTO</td>
			<td><input type="text" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>" style="width:100%; text-align:right;" readonly></td>
		</tr>-->
		<input type="hidden" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>">
<?php

	}

?>
		<tr>
			<td width="110">TIPO COMISI&Oacute;N</td>
			<td>
				<select id="tipo_tasa_comision" name="tipo_tasa_comision"  style="width:195px; background-color:#EAF1DD;">
					<?php
					$queryTasaC = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE id_tasa_comision = ".$fila['id_tasa_comision'];
							
					$rsTC = sqlsrv_query($link, $queryTasaC);
					echo($queryTasaC);
					while ($fTC = sqlsrv_fetch_array($rsTC, SQLSRV_FETCH_ASSOC)){ 
						if($fTC['marca_unidad_negocio'] == 1){ $textoTipoComsion = 'F'; }else{ $textoTipoComsion = 'K'; } ?>
						<option value="<?=$fTC['id_tasa_comision']?>">TIPO <?=$textoTipoComsion.' '.$fTC['id_tipo']?></option><?php
					} ?>							
				</select>
			</td>
		</tr>
		</table>
		</div>
		<br>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td style="font-size:16"><b>DECISI&Oacute;N</b></td>
			<td>
				<input type="text" id="decision" name="decision" value="<?php echo $decision ?>" style="width:100%; height:45; text-align:center; font-size:18; font-weight:bold;<?php if ($decision == $label_negado) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly>
				<input type="hidden" name="decision_sistema" value="<?php echo $decision_sistema ?>">
			</td>
		</tr>
		</table>
		</div>
		<br>
		<h2>OBSERVACIONES</h2>
		<div class="box1 clearfix">
<?php

	if (!$_REQUEST["id_simulacion"])
	{
	
?>
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td colspan="2"><textarea name="observaciones" rows="3" style="width:100%; background-color:#EAF1DD;"><?php echo $observaciones ?></textarea></td>
		</tr>
		</table>
<?php

	}
	else
	{
	
?>
		<input type="hidden" name="observaciones" value="">
		<iframe id="frm_observaciones" src="simulaciones_observaciones.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&tipo=<?php echo $_REQUEST["tipo"] ?>" width="100%" height="300px" seamless style="overflow:auto"></iframe>
<?php

	}
	
?>
		</div>
	</td>
	<td width="20">&nbsp;</td>
	<td valign="top">
		<h2>COMPRAS DE CARTERA</h2>
		<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="100%">
		<tr>
			<th>No.</th>
			<th width="250px">ENTIDAD</th>
			<th>OBSERVACI&Oacute;N</th>
			<?php if ($_SESSION["FUNC_MUESTRACAMPOS2"]) { ?><th>VALOR A PAGAR</th><?php } ?>
			<th width="80px">SE COMPRA?</th>
		</tr>
<?php
	$validar_subestado=0;
		if (in_array($fila["id_subestado"],$subestados_bloqueo_comprascartera))
		{
		
			$validar_subestado=1;
		
		}
		
		for ($i = 1; $i <= $ultimo_consecutivo_compra_cartera; $i++){ ?>
			<tr>
				<td align="center"><?php echo $i ?></td>
				<td><select id="id_entidad<?php echo $i ?>" name="id_entidad<?php echo $i ?>" style="<?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> pointer-events: none; <?php } ?>background-color:#EAF1DD; width:250px;" onChange="TotalizarComprasCartera()">
					<option value=""></option>
					<?php
					$queryDB = "select id_entidad, nombre from entidades_desembolso order by nombre";
					$rs1 = sqlsrv_query($link, $queryDB);
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
						if ($fila1["id_entidad"] == $id_entidad[$i - 1]){
							$selected_entidad = " selected";
						}
						else{
							$selected_entidad = "";
						}

						echo "<option value=\"".$fila1["id_entidad"]."\"".$selected_entidad.">".($fila1["nombre"])."</option>\n";
					}
					?>
				</select></td>
				<td>
				
					<input <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"   && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php } ?>  type="text" id="entidad<?php echo $i ?>" name="entidad<?php echo $i ?>" value="<?php echo str_replace("\"", "&#34;", $entidad[$i - 1]); ?>" style="width:100%; background-color:#EAF1DD"></td>
				<?php
				if ($_SESSION["FUNC_MUESTRACAMPOS2"]){?>
					<td align="right"><input  <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php }else{?> onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); } Swal.fire({icon: 'warning', title: 'Confirme el Valor: $ ' + this.value, allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: true, confirmButtonText: 'Aceptar' });" <?php } ?> type="text" name="valor_pagar<?php echo $i ?>" value="<?php echo number_format($valor_pagar[$i - 1], 0, ".", ",") ?>" size="15"  style="text-align:right; background-color:#EAF1DD"></td>
					<?php
				}?>
				<td align="center">
					<input type="hidden" id="dias_entregah<?php echo $i ?>" name="dias_entregah<?php echo $i ?>" value="<?php echo number_format($dias_entregah[$i - 1], 0) ?>">
					<input type="hidden" id="dias_vigenciah<?php echo $i ?>" name="dias_vigenciah<?php echo $i ?>" value="<?php echo number_format($dias_vigenciah[$i - 1], 0) ?>">
					<select  id="se_compra<?php echo $i ?>" name="se_compra<?php echo $i ?>" style="<?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> pointer-events: none; <?php } ?>background-color:#EAF1DD;" onChange="TotalizarComprasCartera(); recalcular();<?php if ($_SESSION["FUNC_AGENDA"]) { ?> <!--recalcular_agenda('<?php echo $i ?>', this.value);--><?php } ?>">
						<option value="SI"<?php echo $se_compra_si[$i - 1] ?>>SI</option>
						<option value="NO"<?php echo $se_compra_no[$i - 1] ?>>NO</option>
					</select>
				</td>
			</tr>
			<?php
		}

		if ($_SESSION["FUNC_MUESTRACAMPOS2"]){ ?>
			<tr>
				<td align="center">&nbsp;</td>
				<td colspan="2"><b>TOTAL COMPRAS DE CARTERA</b></td>
				<td align="right"><input type="text" name="total_valor_pagar" value="<?php echo number_format($total_valor_pagar, 0, ".", ",") ?>" size="14" style="text-align:right; font-weight:bold;" readonly></td>
				<td align="center">&nbsp;</td>
			</tr>
			<?php
		}

		if ($_SESSION["FUNC_MUESTRACAMPOS1"]){ ?>
			<tr>
				<td align="center">&nbsp;</td>
				<td colspan="2" align="right"><b>INGRESE VALOR ESTIMADO COMPRAS DE CARTERA</b></td>
				<td><input type="text" name="total_valor_pagar" value="<?php echo number_format($total_valor_pagar, 0, ".", ",") ?>" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } valor_pagar1.value = this.value; recalcular(); }" style="height:30; width:95%; text-align:right; background-color:#EAF1DD"></td>
			</tr>
			<?php
	}
	?>
		</table>
		<br>
		<h2>RETANQUEOS</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="100%">
		<tr>
			<th width="15">&nbsp;</th>
			<th width="32%">NO. LIBRANZA</th>
			<th width="32%">CUOTA</th>
			<th width="32%">VALOR</th>
		</tr>
		<tr>
			<td align="center">1</td>
			<td align="center"><input type="text" id="retanqueo1_libranza" name="retanqueo1_libranza" value="<?php echo $retanqueo1_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '1'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo1_libranzah" value="<?php echo $retanqueo1_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo1_cuota" name="retanqueo1_cuota" value="<?php echo number_format($retanqueo1_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo1_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo1_valor" name="retanqueo1_valor" value="<?php echo number_format($retanqueo1_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo1_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td align="center">2</td>
			<td align="center"><input type="text" id="retanqueo2_libranza" name="retanqueo2_libranza" value="<?php echo $retanqueo2_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '2'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo2_libranzah" value="<?php echo $retanqueo2_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo2_cuota" name="retanqueo2_cuota" value="<?php echo number_format($retanqueo2_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo2_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo2_valor" name="retanqueo2_valor" value="<?php echo number_format($retanqueo2_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo2_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td align="center">3</td>
			<td align="center"><input type="text" id="retanqueo3_libranza" name="retanqueo3_libranza" value="<?php echo $retanqueo3_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '3'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo3_libranzah" value="<?php echo $retanqueo3_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo3_cuota" name="retanqueo3_cuota" value="<?php echo number_format($retanqueo3_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo3_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo3_valor" name="retanqueo3_valor" value="<?php echo number_format($retanqueo3_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo3_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td colspan="2" align="right">TOTAL RETANQUEOS&nbsp;&nbsp;&nbsp;</td>
			<td align="center"><input type="text" name="retanqueo_total_cuota" value="<?php echo number_format($retanqueo_total_cuota, 0, ".", ",") ?>" size="22" style=" text-align:right;" readonly></td>
			<td align="center"><input type="text" name="retanqueo_total" value="<?php echo number_format($retanqueo_total, 0, ".", ",") ?>" size="22" style=" text-align:right;" readonly></td>
		</tr>
		</table>
		</div>
		<br>
		<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="100%">
		<tr>
			<th colspan="2">OPCIONES DE CR&Eacute;DITO</th>
			<th>OPCI&Oacute;N CUOTA</th>
			<th colspan="2">OPCI&Oacute;N DESEMBOLSO</th>
		</tr>
		<tr>
			<td align="center"><input type="radio" disabled name="opcion_credito" value="CLI" onChange="recalcular()"<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> disabled <?php } ?><?php echo $opcion_credito_cli ?>></td>
			<td style="font-size:16"><b>CUPO DE LIBRE INVERSION</b></td>
			<td><input type="text" id="opcion_cuota_cli" name="opcion_cuota_cli" value="<?php echo number_format($opcion_cuota_cli, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_cli" name="opcion_desembolso_cli" value="<?php echo $opcion_desembolso_cli ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input type="radio" disabled name="opcion_credito" value="CCC" onChange="recalcular()"<?php echo $opcion_credito_ccc ?>></td>
			<td style="font-size:16"><b>CUPO CON COMPRAS</b></td>
			<td><input type="text" id="opcion_cuota_ccc" name="opcion_cuota_ccc" value="<?php echo number_format($opcion_cuota_ccc, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_ccc, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_ccc" name="opcion_desembolso_ccc" value="<?php echo number_format($opcion_desembolso_ccc, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_ccc, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input type="radio" disabled name="opcion_credito" value="CMP" onChange="recalcular()"<?php echo $opcion_credito_cmp ?>></td>
			<td style="font-size:16"><b>CUPO MAXIMO POSIBLE</b></td>
			<td><input type="text" id="opcion_cuota_cmp" name="opcion_cuota_cmp" value="<?php echo number_format($opcion_cuota_cmp, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cmp, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_cmp" name="opcion_desembolso_cmp" value="<?php echo number_format($opcion_desembolso_cmp, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_cmp, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input type="radio" checked name="opcion_credito" value="CSO" onChange="recalcular()"<?php echo $opcion_credito_cso ?>></td>
			<td style="font-size:16"><b>CUPO SOLICITADO</b></td>
			<td><input type="text" id="opcion_cuota_cso" name="opcion_cuota_cso" value="<?php echo number_format($opcion_cuota_cso, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) { this.value='0'; return false; } else { if (this.value == '') { this.value = '0'; } if (parseFloat(this.value) > document.formato.opcion_cuota_ccc.value.replace(/\,/g, '')) { this.value = document.formato.opcion_cuota_ccc.value.replace(/\,/g, ''); alert('El valor de la cuota no debe ser mayor a $' + document.formato.opcion_cuota_ccc.value); } recalcular(); separador_miles(this); }" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cso, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#EAF1DD"></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_cso" name="opcion_desembolso_cso" value="<?php echo number_format($opcion_desembolso_cso, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_cso, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		</table>
		</div>
		<br>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="100%">
		<tr>
			<td colspan="2" style="font-size:18" width="170"><b>DESEMBOLSO MENOS RETANQUEOS</b></td>
			<td colspan="3"><input type="text" name="sin_retanqueos" value="<?php echo $sin_retanqueos ?>" style="width:95%; text-align:right;" readonly></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:18"><b>DESEMBOLSO CLIENTE</b></td>
			<td colspan="3"><input type="text" id="desembolso_cliente" name="desembolso_cliente" value="<?php echo number_format($desembolso_cliente, 0, ".", ",") ?>" style="width:95%; height:45; text-align:right; font-size:18; font-weight:bold;<?php if (number_format($desembolso_cliente, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td colspan="2">EXTRA PRIMA</td>
			<td colspan="3">
				<select name="porcentaje_extraprima" style="background-color:#EAF1DD;" onChange="recalcular()">
					<option value="0"></option>
					<option value="25"<?php if ($porcentaje_extraprima == "25") { echo " selected"; } ?>>25%</option>
					<option value="50"<?php if ($porcentaje_extraprima == "50") { echo " selected"; } ?>>50%</option>
					<option value="75"<?php if ($porcentaje_extraprima == "75") { echo " selected"; } ?>>75%</option>
					<option value="100"<?php if ($porcentaje_extraprima == "100") { echo " selected"; } ?>>100%</option>
				</select>
				<input type="hidden" name="porcentaje_extraprimah" value="<?php echo $porcentaje_extraprima ?>">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FORMULARIO SEGURO&nbsp;
				<input type="checkbox" name="formulario_seguro_checked" value="1" onChange="if (this.checked == true) { document.formato.formulario_seguro.value = '1'; } else { document.formato.formulario_seguro.value = '0'; }"<?php echo $formulario_seguro_checked ?>>
				<input type="hidden" name="formulario_seguro" value="<?php echo $formulario_seguro ?>">
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
<?php

}
else
{

?>
<tr>
	<td colspan="3">
		<div class="box1 oran clearfix">
		<table border="1" cellspacing=1 cellpadding=2 width="100%">
		<tr>
			<td align="center">ID SIMULACI&Oacute;N<br>
				<input type="text" readonly id="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"]; ?>" style="text-align:center;"> 
			</td>
		</tr>
		<tr>
			<td align="center">TIPO DE CREDITO<br>
			<?php
			$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' AND se_compra='SI'";

			$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) 
			{
				$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' AND se_compra='SI'";

				$queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
				$resComprasCC=sqlsrv_fetch_array($queryComprasCC, SQLSRV_FETCH_ASSOC);
				if ($resComprasCC["cuota"]>0)
				{

					
					if ($fila["retanqueo1_libranza"]=="" || $fila["retanqueo2_libranza"]=="" || $fila["retanqueo3_libranza"]=="")
					{
						$tipo_crediton="COMPRAS DE CARTERA";
						$tipo_credito_id=2;	
					}else{
						$tipo_crediton="COMPRAS CON RETANQUEO";	
						$tipo_credito_id=3;
					}
					
				}
				else
				{
					if ($resComprasCC["valor_pagar"]>0)
					{
						$tipo_crediton="LIBRE CON SANEAMIENTO";	
						$tipo_credito_id=4;
					}else{
						if ($fila["retanqueo1_libranza"]<>"" || $fila["retanqueo2_libranza"]<>"" || $fila["retanqueo3_libranza"]<>"")
						{
							$tipo_crediton="LIBRE INVERSION CON RETANQUEO";	
							$tipo_credito_id=5;
						}
					}
					
				}


			
			}else{
				$tipo_crediton="LIBRE INVERSION";
				$tipo_credito_id=1;
			}

			$actualizarTipoCredito="UPDATE simulaciones SET tipo_credito_id='".$tipo_credito_id."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' AND tipo_credito_id IS NULL";
			sqlsrv_query($link,$actualizarTipoCredito);

			?>
				<input type="text" readonly value="<?php echo $tipo_crediton; ?>" style="font-weight:bold;text-align:center;width:600px; background-color:#8DB4E3;"> 
				
			</td>
			
		</tr>
		</table>
		</div>
	</td>
	
</tr>
<tr>
	<td valign="top">
		<br>
		<div class="box1 clearfix">
		<table border="1" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td width="107">COMERCIAL&nbsp;</td>
			<td width="256">
				<select name="id_comercial"<?php if (!$_REQUEST["id_simulacion"]) { ?> onChange="if (document.formato.id_comercialh.value != '' && document.formato.cedula.value != '') { if (confirm('Al cambiar el Comercial, se cargara de cero la simulacion. Desea continuar?') == true) { window.location.href='?cedula='+document.formato.cedula.value+'&id_comercial='+this.value; } else { this.value = document.formato.id_comercialh.value;} } document.formato.id_comercialh.value=this.value;" style="width:245px; background-color:#EAF1DD;"<?php } else { ?> style="width:245px;"<?php } ?>>
					<option value=""></option>
<?php

	if (!$_REQUEST["id_simulacion"]) {
		$queryDB = "select id_usuario, nombre, apellido from usuarios where (id_usuario IN (SELECT id_usuario FROM oficinas_usuarios WHERE id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')) AND tipo <> 'MASTER' AND tipo = 'COMERCIAL' AND estado = '1'";
		
		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA")
			$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')"; //AND si.telemercadeo = '0'";
		
		//if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		//	$queryDB .= " AND si.telemercadeo = '0'";
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
			$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS"){
			$queryDB .= " AND (freelance = '1' OR outsourcing = '1')";// AND si.telemercadeo IN ('0','1')";
		}
		
		//if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		//	$queryDB .= " AND si.telemercadeo = '1'";
		
		$queryDB .= ") OR id_usuario = '".$id_comercial."'";
		
		if ($_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "PROSPECCION") {
			$queryDB .= " UNION select id_usuario, nombre, apellido from usuarios where ((id_usuario IN (select id_usuario from oficinas_usuarios) AND tipo <> 'MASTER' AND tipo = 'COMERCIAL' AND estado = '1')";
			
			if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM")
				$queryDB .= " AND NOT (freelance = '1' OR outsourcing = '1')";
			
			$queryDB .= ") OR id_usuario = '".$id_comercial."'";
		}
	} else {
		$queryDB = "select id_usuario, nombre, apellido from usuarios where id_usuario = '".$id_comercial."'";
	}
	
	$queryDB .= " order by nombre, apellido, id_usuario";
	
	// echo($queryDB);

	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($fila1["id_usuario"] == $id_comercial)
			$selected_comercial = " selected";
		else
			$selected_comercial = "";
		
		echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_comercial.">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
	}
	
?>

				</select>
				<input type="hidden" name="id_comercialh" value="<?php echo $id_comercial ?>">
			</td>
			<td width="100">TELEMERCADEO</td>
			<td>
				<input type="checkbox" name="telemercadeo_checked" value="1" onChange="if (this.checked == true) { document.formato.telemercadeo.value = '1'; } else { document.formato.telemercadeo.value = '0'; }"<?php echo $telemercadeo_checked ?><?php if ($inhabilita_telemercadeo) { ?> disabled <?php } ?>>
				<input type="hidden" name="telemercadeo" value="<?php echo $telemercadeo ?>">
			</td>
		</tr>
<?php

	if ($_REQUEST["id_simulacion"])
	{
	
?>
		<tr>
			<td>OFICINA</td>
			<td><input type="text" name="oficina" value="<?php echo $oficina ?>" size="32" readonly></td>
		</tr>
<?php

	}
	
?>
		</table>
		</div>
		<br>
		<h2>INFORMACI&Oacute;N GENERAL</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
			<tr>
				<td>VERIFICACIÓN OTP</td>
				<td>
					<?php if($validacion_otp <> 0){ ?>
						<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(6, 175, 116);" value="VERIFICADO POR OTP" readonly>
						<?php
					}else{ ?>
						<input type="text" id="verificacion_otp" style="width:92%; font-weight:bold; color: white; text-align:center; background-color: rgb(243 204 48);" value="SIN VERFICACIÓN OTP" readonly>
					<?php } ?>
				</td>
				<td colspan="2"></td>
			</tr>
		<?php
		if($oficina_ado == 1) {?>
			<tr>
				<td>VERIFICACIÓN ADO</td>
				<td><input type="text" id="verificacion" size="32" readonly style="font-weight:bold; color: white; text-align:center;"></td>			
				<td><input type="button" onclick="window.open('capturasValidacionID.php?id_simulacion=<?=$_REQUEST["id_simulacion"]?>', 'Ver Imagenes','toolbars=yes,scrollbars=yes,resizable=yes,width=700,height=700,top=0,left=0');" title="Click Para Ver las Capturas Del Cliente" name="Ver Imagenes" value="Ver Imagenes" id="verImgValidacion" style="cursor:  pointer; display: none;"></td>
				<td><img id="btnVerificacionID" src="../images/reenviar.png" style="vertical-align: middle; cursor: pointer; display: none;" onclick="reenviarCorreoValidacionID(this,'<?=$mail;?>','<?=$nombre;?>');" width="50px" title="Reenviar Correo" /></td>
			</tr>
			<tr>
				<td colspan="4" id="textoMensajeAdo" style=" padding-bottom: 10px; color: red; font-weight: 900; font-size: 15px !important;"></td>
			</tr>
		<?php } 
		
        $fechaNacimiento = new DateTime($fecha_nacimiento);
        $fechaActual = new DateTime();
        $diferencia = $fechaNacimiento->diff($fechaActual);

		?>
		<tr>
			<td>C&Eacute;DULA</td>
			<td><input type="text" id="cedula" name="cedula" value="<?php echo $cedula ?>" size="32"<?php if (!$_REQUEST["id_simulacion"]) { ?> onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { if (document.formato.id_comercial.value == '') { alert('Debe seleccionar primero el Comercial'); this.value=''; return false } else { window.location.href='?cedula='+this.value+'&id_comercial='+document.formato.id_comercial.options[document.formato.id_comercial.selectedIndex].value+'&telemercadeo='+document.formato.telemercadeo.value; } }" style="background-color:#EAF1DD"<?php } else { ?> readonly<?php } ?>></td>
			<td>FECHA (DEL ESTUDIO)</td>
			<td><input type="text" id="fecha_estudio" name="fecha_estudio" value="<?php echo $fecha_estudio ?>" onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_estudio ?>'; return false}" size="15" style="background-color:#EAF1DD;"></td>
		</tr>
		<tr>
			<td>NOMBRE</td>
			<td><input type="text" name="nombre" value="<?php echo $nombre ?>" size="32" readonly></td>
      <td>FECHA NACIMIENTO</td>
      <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" edad="<?php echo $diferencia->y. ' Años '.$diferencia->m. ' Meses ' ;?>" value="<?php echo $fecha_nacimiento ?>" size="15" title="" readonly><?php echo $diferencia->y. ' Años '.$diferencia->m. ' Meses ' ;?>
      </td>
		</tr>
		<tr>
			<td>PAGADUR&Iacute;A</td>
			<td><input type="text" id="pagaduria" name="pagaduria" value="<?php echo $pagaduria ?>" size="32" readonly></td>
			<td>MESES ANTES DE EDAD L&Iacute;MITE</td>
			<td><input type="text" name="meses_antes_65" value="<?php echo $meses_antes ?>" size="15" style="font-weight:bold; background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td>P.A.</td>
			<td><input type="text" name="pa" value="<?php echo $pa ?>" size="32" readonly></td>
			<td>FECHA VINCULACI&Oacute;N</td>
			<td><input type="text" name="fecha_inicio_labor" value="<?php echo $fecha_inicio_labor ?>" size="15"<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") { ?> onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_inicio_labor ?>'; return false}" style="background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td style="vertical-align:top">INSTITUCI&Oacute;N / ASOCIACI&Oacute;N</td>
			<td><input type="text" name="institucion" value="<?php echo $institucion ?>" size="32" style="background-color:#EAF1DD"></td>
			<td>MEDIO CONTACTO</td>
			<td><input type="text" name="medio_contacto" value="<?php echo $medio_contacto ?>" size="15" readonly></td>
		</tr>
		<tr>
			<td>TEL&Eacute;FONO</td>
			<td><input type="text" name="telefono" value="<?php echo $telefono ?>" maxlength="50" size="<?php if ($_SESSION["FUNC_MUESTRACAMPOS2"]) { ?>14<?php } else { ?>32<?php } ?>" style="background-color:#EAF1DD"></td>
			<td>CELULAR</td>
			<td><input type="text" name="celular" value="<?php echo $celular ?>" maxlength="50" size="15" readonly></td>
		</tr>
		<tr>
			<td>DIRECCI&Oacute;N</td>
			<td><input type="text" name="direccion" value="<?php echo $direccion ?>" maxlength="255" size="32" style="background-color:#EAF1DD"></td>
			<td>CIUDAD</td>
			<td><select name="ciudad_residencia" style="width:120px; background-color:#EAF1DD">
					<option value=""></option>
<?php

	$queryDB = "select cod_municipio, municipio from ciudades order by municipio";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($fila1["cod_municipio"] == $ciudad_residencia)
			$selected_ciudad = " selected";
		else
			$selected_ciudad = "";
		
		echo "<option value=\"".$fila1["cod_municipio"]."\"".$selected_ciudad.">".($fila1["municipio"])."</option>\n";
	}
	
?>
				</select>
			</td>
		</tr>

		<tr>
			<td>E-MAIL</td>
			<td><input type="text" name="mail" value="<?php echo $mail ?>" maxlength="50" size="32" style="background-color:#EAF1DD"></td>
			<td>SIN APORTES DE LEY</td>
			<td>
				<input type="checkbox" name="sin_aportes_checked" value="1" onChange="if (this.checked == true) { document.formato.sin_aportes.value = '1'; } else { document.formato.sin_aportes.value = '0'; } recalcular()"<?php if (!(strtoupper($nivel_contratacion) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])) { ?> disabled<?php } ?><?php echo $sin_aportes_checked ?>>
				<input type="hidden" name="sin_aportes" value="<?php echo $sin_aportes ?>">
			</td>
		</tr>

		<tr>
		<td>SEXO</td>
			<td>
				<select id="sexo" name="sexo" style="width:250px; background-color:#EAF1DD; pointer-events:none; user-select:none;" >
					<option <?php if($sexo == ''){ echo 'selected'; } ?> value=""></option>
					<option <?php if($sexo == 'M'){ echo 'selected'; } ?> value="M">MASCULINO</option>
					<option <?php if($sexo == 'F'){ echo 'selected'; } ?> value="F">FEMENINO</option>
				</select>
				<input type="text" style="display:none;" />
			</td>
			<td>PROPOSITO</td>
			<td>
				<?php 
					if($_SESSION['S_TIPO'] == 'OFICINA' && $_SESSION['S_SUBTIPO'] == 'ANALISTA_REFERENCIA'){
						$estiloProposito = 'background-color:#EAF1DD;';
					}else{
						$estiloProposito = 'pointer-events: none;';
					}					
				?>
				<select name="proposito" id="proposito" style="width:250px; <?=$estiloProposito?>" required>
					<option value="0"></option>
					<?php
						$queryDB = "SELECT * FROM propositos_credito";					
						$rs1 = sqlsrv_query($link, $queryDB);
						
						while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
							if ($fila1["id_proposito"] == $proposito){
								echo "<option value=\"".$fila1["id_proposito"]."\" selected>".($fila1["proposito"])."</option>\n";
							}
							else{
								if($_SESSION['S_TIPO'] == 'OFICINA' && $_SESSION['S_SUBTIPO'] == 'ANALISTA_REFERENCIA'){
									echo "<option value=\"".$fila1["id_proposito"]."\">".($fila1["proposito"])."</option>\n";
								}
							}
						}					
					?>
				</select>
			</td>
			<td></td>
			<td></td>
		</tr>
		</table>
		</div>
		<br>
		<h2>INFORMACI&Oacute;N DEL DESPRENDIBLE</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%" height="164">
		<tr>
			<th colspan="2">INGRESOS MENSUALES</th>
			<th colspan="2">DESCUENTOS DE LEY</th>
		</tr>
		<tr>
			<td>SALARIO B&Aacute;SICO</td>
			<td><input type="text" name="salario_basico" value="<?php echo number_format($salario_basico, 0, ".", ",") ?>" onChange="calcular_aportes();" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" size="15" style="text-align:right; background-color:#EAF1DD"></td>
			<td>APORTES (SALUD Y PENSI&Oacute;N)</td>
			<td><input type="text" name="aportes" value="<?php echo number_format($aportes, 0, ".", ",") ?>" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" size="15" style="text-align:right; background-color:#EAF1DD"></td>
		</tr>
		<tr>
			<td>ADICIONALES S&Oacute;LO (AA)</td>
			<td><input type="text" name="adicionales" value="<?php echo number_format($adicionales, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
			<td>OTROS APORTES</td>
			<td><input type="text" name="otros_aportes" value="<?php echo number_format($otros_aportes, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
		</tr>
<?php

	if ($_SESSION["FUNC_MUESTRACAMPOS2"])
	{

?>
		<tr>
			<td>BONIFICACI&Oacute;N</td>
			<td><input type="text" name="bonificacion" value="<?php echo number_format($bonificacion, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
<?php

	}
	else
	{

?>
		<input type="hidden" name="bonificacion" value="<?php echo number_format($bonificacion, 0, ".", ",") ?>">
<?php

	}

?>
		<tr>
			<td><b>TOTAL INGRESOS</b></td>
			<td><input type="text" name="total_ingresos" value="<?php echo number_format($total_ingresos, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
			<td><b>TOTAL APORTES</b></td>
			<td align="right"><input type="text" name="total_aportes" value="<?php echo number_format($total_aportes, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
		</tr>
		</table>
		</div>
		<br>
		<?php
			$query = "SELECT * FROM formulario_digital a JOIN simulaciones b ON b.id_simulacion = a.id_simulacion WHERE a.id_simulacion = '".$_REQUEST["id_simulacion"]."'"; 
			

			$consultarFormularioDigital= sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)); 
			



			if (sqlsrv_num_rows($consultarFormularioDigital)>0) { //Ya se envió por lo menos un correo
				$resFormularioDigital=sqlsrv_fetch_array($consultarFormularioDigital, SQLSRV_FETCH_ASSOC); ?>

				<h2>DOCUMENTACION DIGITAL</h2>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2 width="95%">
						<tr>
							<td style="white-space:nowrap;">FECHA ENVIO </td><td><input type="text" name="fecha_envio_deceval" value="<?php echo $resFormularioDigital["fecha_envio"];?>" size="20" style="text-align:right;" readonly></td>
							<td style="white-space:nowrap;">FECHA LEIDO</td><td><input type="text" name="fecha_leido_deceval" value="<?php echo $resFormularioDigital["fecha_leido"];?>" size="20" style="text-align:right;" readonly></td>
							<td style="white-space:nowrap;">Intentos: <strong style='font-weight:bold;'><?php echo $resFormularioDigital["intentos"];?></strong></td>
						</tr>
						<tr>
							<td style="white-space:nowrap;">MD5</td>
							<td>
								<input type="text" name="md5_firma_deceval" value="<?php if($resFormularioDigital["estado_token"] == 1 && $resFormularioDigital["formato_digital"] == 1){ echo $resFormularioDigital["firma_experian"];}?>" size="20" style="text-align:right;" readonly>
							</td>
							<td style="white-space:nowrap;">FECHA FIRMA</td>
							<td>
								<input type="text" name="fecha_firma_deceval" value="<?php if($resFormularioDigital["estado_token"] == 1 && $resFormularioDigital["formato_digital"] == 1){echo $resFormularioDigital["fecha_pagare_deceval"]; }?>" size="20" style="text-align:right;" readonly>
							</td>
							<?php
							if($resFormularioDigital["formato_digital"] != 1){
								$consultarFormularioDigitalDiligenciado=sqlsrv_query($link, "SELECT seccion_info_personal, seccion_actividad_laboral, seccion_info_financiera, seccion_referencias, seccion_datos_internacionales, seccion_facta, seccion_varios FROM solicitud WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
								$resFormularioDigitalDiligenciado=sqlsrv_fetch_array($consultarFormularioDigitalDiligenciado, SQLSRV_FETCH_ASSOC);					
			
								if ($cod_interno_subestado >= 10 && $cod_interno_subestado <= 30 && $resFormularioDigitalDiligenciado["seccion_info_personal"] == "1" && $resFormularioDigitalDiligenciado["seccion_actividad_laboral"] == "1" && $resFormularioDigitalDiligenciado["seccion_info_financiera"] == "1" && $resFormularioDigitalDiligenciado["seccion_referencias"] == "1" && $resFormularioDigitalDiligenciado["seccion_datos_internacionales"] == "1" && $resFormularioDigitalDiligenciado["seccion_facta"] == "1" && $resFormularioDigitalDiligenciado["seccion_varios"] == "1")
								{ //el subestado esta en el rango y formulario esta completo ?>
									<td>
										<img id="btnVerificacionID" src="../images/reenviar.png" style="vertical-align: middle; float: right; margin-top: 2px; cursor: pointer;" onclick="reenviarFormularioDigital(this.name,'<?=$_GET["id_simulacion"];?>');" width="50px" name="<?=$resFormularioDigital["token"];?>" title="Reenviar Correo" />
									</td><?php
								} 
							}

							if(($resFormularioDigital["formato_digital"] == 1) && (( $_SESSION['S_ANULAR_FIRMA_DIGITAL'] == 1 && $id_subestado == 72) || ($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $cod_interno <= 32))){
								?>
									<td>
										<img id="btnVerificacionID" src="../images/habilitarFirma.png" style="vertical-align: middle; float: right; margin-top: 2px; cursor: pointer;" onclick="habilitarFirma()" width="50px"  title="Habilitar Firma" />
									</td>
								<?php
								}

							?>
						</tr>
						
						<?php
							$query = "SELECT a.* FROM formulario_digital a JOIN simulaciones b ON b.id_simulacion = a.id_simulacion WHERE a.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

							$consultarFormularioDigital=sqlsrv_query($link, $query);
							$resFormularioDigital=sqlsrv_fetch_array($consultarFormularioDigital, SQLSRV_FETCH_ASSOC);
							echo "<tr>";
							echo "<td>RESULTADO ACTUAL</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='4'><strong style='font-weight:bold;'>Creacion cliente:</strong> ".$resFormularioDigital["observacion_crear_girador"]."</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='4'><strong style='font-weight:bold;'>Creacion documento:</strong> ".$resFormularioDigital["observacion_crear_pagare"]."</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='4'><strong style='font-weight:bold;'>Estado firma:</strong> ".$resFormularioDigital["observacion_firma_pagare"]."</td>";
							echo "</tr>";
						?>
					</table>
				</div>
				<?php
			} else {// Aun no tiene formato digital, No hay registro de envio de correo 

				$consultarFormularioDigitalDiligenciado=sqlsrv_query($link, "SELECT seccion_info_personal, seccion_actividad_laboral, seccion_info_financiera, seccion_referencias, seccion_datos_internacionales, seccion_facta, seccion_varios FROM solicitud WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
				
				
				$resFormularioDigitalDiligenciado=sqlsrv_fetch_array($consultarFormularioDigitalDiligenciado, SQLSRV_FETCH_ASSOC);					
			
				if ($cod_interno_subestado >= 10 && $cod_interno_subestado <= 30 && $resFormularioDigitalDiligenciado["seccion_info_personal"] == "1" && $resFormularioDigitalDiligenciado["seccion_actividad_laboral"] == "1" && $resFormularioDigitalDiligenciado["seccion_info_financiera"] == "1" && $resFormularioDigitalDiligenciado["seccion_referencias"] == "1" && $resFormularioDigitalDiligenciado["seccion_datos_internacionales"] == "1" && $resFormularioDigitalDiligenciado["seccion_facta"] == "1" && $resFormularioDigitalDiligenciado["seccion_varios"] == "1")
				{ //el subestado esta en el rango y formulario esta completo ?>

					<h2>DOCUMENTACION DIGITAL</h2>
					<div class="box1 clearfix">
						<table border="0" cellspacing=1 cellpadding=2 width="95%">
							<tr>
								<td style="white-space:nowrap;">FECHA ENVIO </td><td><input type="text" name="fecha_envio_deceval" value="" size="20" style="text-align:right;" readonly></td>
								<td style="white-space:nowrap;">FECHA LEIDO</td><td><input type="text" name="fecha_leido_deceval" value="" size="20" style="text-align:right;" readonly></td>
								<td style="white-space:nowrap;">Intentos: <strong style='font-weight:bold;'><?php echo $resFormularioDigital["intentos"];?></strong></td>
							</tr>
							<tr>
								<td style="white-space:nowrap;">MD5</td>
								<td>
									<input type="text" name="md5_firma_deceval" value="" size="20" style="text-align:right;" readonly>
								</td>
								<td style="white-space:nowrap;">FECHA FIRMA</td>
								<td>
									<input type="text" name="fecha_firma_deceval" value="" size="20" style="text-align:right;" readonly>
								</td>
								<td>
									<img id="btnVerificacionID" src="../images/reenviar.png" style="vertical-align: middle; float: right; margin-top: 2px; cursor: pointer;" onclick="reenviarFormularioDigital(this.name,'<?=$_GET["id_simulacion"];?>');" width="50px" name="<?=$resFormularioDigital["token"];?>" title="Reenviar Correo" />
								</td>
							</tr>
						</table>
					</div>
					<?php
				}else{ ?>

					<h2>DOCUMENTACION DIGITAL</h2>

					<div class="box1 clearfix">
						<table border="0" cellspacing=1 cellpadding=2 width="95%">
							<tr>
								<th>
									<h2 style="font-weight: bold; font-size: 14px;">No se ha podido firmar el pagare, por favor verifiquela resultado de la operacion.</h2>
									<h3 style="text-align: left;">1. Verifique que se diligencio completamente el formato de solicitud.</h3>
									<h3 style="text-align: left;">2. Debe volver a guardar el crédito en estado 3.0, ya que se avanzo a estado 3.0 antes de diligenciar el formato de solicitud.</h3>
								</th>
							</tr>
						</table>
					</div>
					<?php
				}
			}
				?>
				<br>
                <h2>CONDICIONESssss DEL CR&Eacute;DITO</h2>
                <div class="box1 clearfix">
                <table border="0" cellspacing=1 cellpadding=2 width="95%">
<?php
        if ($_SESSION["FUNC_TASASPLAZO"] && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION")){
        
?>
                <tr>
                        <td width="110">UND DE NEGOCIO</td>
                        <td>
                                <select id="id_unidad_negocio" name="id_unidad_negocio"<?php if (!$bloquear_condiciones) { ?> style="width:195px; background-color:#EAF1DD;"<?php } ?> onChange="if (CargarTasas(this.value, document.formato.plazo.value, document.formato.sin_seguro.value) == '0') { this.value = '<?php echo $id_unidad_negocio; ?>'; CargarTasas(this.value, document.formato.plazo.value, document.formato.sin_seguro.value); alert('No exiten Tasas con estas condiciones. \n\nPor favor validar:\n - Unidad Negocio\n - Plazo\n - Kplus'); } recalcular();">
<?php
                $queryDB = "select id_unidad, nombre from unidades_negocio where 1 = 1";
                $queryDB .= " AND ((estado=1 AND id_unidad IN (SELECT id_unidad FROM usuarios_unidades WHERE id_usuario='".$_SESSION["S_IDUSUARIO"]."')) OR id_unidad = '".$id_unidad_negocio."')";
                if($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO"){
                        $queryDB .= " AND (especial != 1 OR id_unidad = '".$id_unidad_negocio."') ";
                }
                
                $queryDB .= " order by id_unidad";
                $rs1 = sqlsrv_query($link, $queryDB);
                
                while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
                        
                        if (!$bloquear_condiciones || ($bloquear_condiciones && $id_unidad_negocio == $fila1["id_unidad"])){
                                $muestra_unidad_negocio = 0;
                                
                                if ($id_unidad_negocio == $fila1["id_unidad"] || $_SESSION["S_TIPO"] != "COMERCIAL"){
                                        $muestra_unidad_negocio = 1;
                                }
                                else{//Excepciones
                                        if ($fila1["id_unidad"] == "1" && ($id_unidad_negocio == "1" || $id_unidad_negocio == "3" || $_SESSION["S_TIPO"] != "COMERCIAL")){
                                                $muestra_unidad_negocio = 1;
                                        }
                                        
                                        if ($fila1["id_unidad"] == "2" && ($id_unidad_negocio == "2" || $_SESSION["S_TIPO"] != "COMERCIAL")){
                                                $muestra_unidad_negocio = 1;
                                        }
                                }
                                
                                if ($muestra_unidad_negocio){
                                        if ($fila1["id_unidad"] == $id_unidad_negocio){
                                                $selected_unidad = " selected";
                                        }
                                        else{
                                                $selected_unidad = "";
                                        }
                                        
                                        echo "<option value=\"".$fila1["id_unidad"]."\"".$selected_unidad." style=\"color:#CC0000;\">".($fila1["nombre"])."</option>\n";
                                }
                        }
                }
                ?>
                                </select>
                        </td>
                        <td width="7">&nbsp;</td>
                        <td width="110">KP PLUS</td>
                        <td>
                                <input type="checkbox" name="sin_seguro_checked" value="1" onChange="cambiarKPPlus(); recalcular();" <?php if ($bloquear_condiciones) { ?> disabled <?php } ?><?php echo $sin_seguro_checked ?>>
                                <input type="hidden" name="sin_seguro" value="<?php echo $sin_seguro ?>">
                                <input type="hidden" name="sin_seguroh" value="<?php echo $sin_seguro ?>">
                                <input type="hidden" name="sin_seguroh2" value="<?php echo $sin_seguro ?>">
                        </td>
                </tr>
<?php
        }
        else
        {
?>
                <input type="hidden" name="id_unidad_negocio" value="<?php echo $id_unidad_negocio ?>">
                <tr>
                        <td width="110">KP PLUS</td>
                        <td>
                                <input type="checkbox" name="sin_seguro_checked" value="1" disabled<?php echo $sin_seguro_checked ?>>
                                <input type="hidden" name="sin_seguro" value="<?php echo $sin_seguro ?>">
                                <input type="hidden" name="sin_seguroh" value="<?php echo $sin_seguro ?>">
                                <input type="hidden" name="sin_seguroh2" value="<?php echo $sin_seguro ?>">
                        </td>
                </tr>
<?php
        }
?>
                <tr>
                        <td width="110">TASA DE INTER&Eacute;S</td>
<?php
        if ($_SESSION["FUNC_TASASCOMBO"]){
?>
                        <td>
                                <select id="tasa_interes" name="tasa_interes" <?= $attr_tasa_interes ?> size="25" style="background-color:#EAF1DD;" onChange="recalcular()">
                                        <option value="<?php echo $tasa_interes_a ?>"<?php echo $tasa_interes_a_selected ?> style="color:#CC0000;">A</option>
                                        <option value="<?php echo $tasa_interes_b ?>"<?php echo $tasa_interes_b_selected ?> style="color:#CC0000;">B</option>
                                        <option value="<?php echo $tasa_interes_c ?>"<?php echo $tasa_interes_c_selected ?>>C</option>
<?php
                if(!$tasa_interes_a_selected && !$tasa_interes_b_selected && !$tasa_interes_c_selected){ 
?>
                     <option value="<?php echo $tasa_interes ?>" selected><?php echo $tasa_interes ?></option>
<?php
                }
                
?>
                                </select>
                        </td>
<?php
        }
        else
        {
                if ($_SESSION["FUNC_TASASPLAZO"])
                {
?>
                        <td width="120">
                                <select id="tasa_interes" name="tasa_interes" <?=$attr_tasa_interes?> style="width:195px;<?php if (!$bloquear_condiciones) { ?> background-color:#EAF1DD;<?php } ?>" dir="rtl" onChange="LeerDescuentos(document.formato.id_unidad_negocio.value, document.formato.plazo.value, this.value); recalcular();">
<?php
                        $queryDB = "select CAST(t2.tasa_interes as DECIMAL(10,2))as tasa_interes from tasas2".$sufijo_sector." as t2 INNER JOIN tasas2_unidades".$sufijo_sector." as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa IN (select id_tasa from tasas".$sufijo_sector." where '".$plazo."' >= plazoi AND '".$plazo."' <= plazof)";
                        
                        if ($sin_seguro){
                           $queryDB .= " AND t2.sin_seguro = '1'";
                        }
                        $queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
                        
                        $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
                        
                        if (strtoupper($nivel_contratacion) == "PENSIONADO"){
                           		$queryDB .= " OR t2.solo_pensionados = '1'";
                        }else{
                              $queryDB .= " OR t2.solo_activos = '1'";
                        }
                        $queryDB .= ")";
                        
                        if ($bloquear_condiciones){
                            $queryDB .= " AND t2.tasa_interes = '".$tasa_interes."'";
                        }
                        $queryDB .= " order by t2.tasa_interes DESC";
                        
                        
                        $rs1 = sqlsrv_query($link, $queryDB);
                        
                        while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
                                if ($fila1["tasa_interes"] == $tasa_interes){
                                        $selected_tasa = " selected";
                                        $existe_tasa = 1;
                                }else{
                                        $selected_tasa = "";
                                }
                                echo "<option value=\"".$fila1["tasa_interes"]."\"".$selected_tasa.">".$fila1["tasa_interes"]."</option>\n";
                        }
                        
                        if (!$existe_tasa){                        
?>
                            <option value="<?php echo $tasa_interes ?>" selected><?php echo $tasa_interes ?></option>
<?php
                        }
                        
?>
                                </select>
                                <input type="hidden" name="tasa_interesh" value="<?php echo floatval($tasa_interes) ?>">
                        </td>
<?php
                }
                else
                {
                
?>
                        <td><input type="text" id="tasa_interes" name="tasa_interes" value="<?php echo $tasa_interes ?>" size="15" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $tasa_interes ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $tasa_interes ?>'; } if (parseFloat(this.value) > <?php echo $tasa_interes_maxima ?>) { this.value = '<?php echo $tasa_interes ?>'; alert('La tasa de interes no debe ser mayor a <?php echo $tasa_interes_maxima ?>'); } recalcular(); }" style="text-align:center;<?php if (!$_SESSION["FUNC_TASASPLAZO"]) { ?> background-color:#EAF1DD;<?php if ($tasa_interes < $tasa_interes_maxima) { ?> color:#CC0000;<?php } ?>"<?php } else { ?>" readonly<?php } ?>></td>
<?php
                }
        }
?>
<!--                        <td>TIPO DE CR&Eacute;DITO</td>
                        <td><input type="text" name="tipo_credito" value="<?php echo $tipo_credito ?>" size="15" style="text-align:center; font-weight:bold;" readonly></td>-->
                        <input type="hidden" name="tipo_credito" value="<?php echo $tipo_credito ?>">
        </tr>
                <tr>
                        <td width="7">&nbsp;</td>
                        <td>PLAZO</td>
                        <td>
                                <input type="text" id="plazo" name="plazo" onclick="calcular_plazo_simulador(<?php echo $plazo ?>, <?php echo $plazo_maximo_segun_edad ?>); return false;" value="<?php echo $plazo ?>" size="15" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $plazo ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $plazo ?>'; } /*if (parseInt(this.value) > <?php echo $plazo_maximo_segun_edad ?>) { this.value = '<?php echo $plazo ?>'; alert('El plazo no debe ser mayor a <?php echo $plazo_maximo_segun_edad ?>'); }*/ <?php if ($_SESSION["FUNC_TASASPLAZO"]) { ?> if (CargarTasas(document.formato.id_unidad_negocio.value, this.value, document.formato.sin_seguro.value) == '0') { this.value = '<?php echo $plazo ?>'; CargarTasas(document.formato.id_unidad_negocio.value, this.value, document.formato.sin_seguro.value); alert('No exiten Tasas con estas condiciones. \n\nPor favor validar:\n - Unidad Negocio\n - Plazo\n - Kplus'); }<?php } ?> recalcular(); }" style="text-align:center; <?php if ($plazo < $plazo_maximo) { ?> color:#CC0000;<?php } ?><?php if (!$bloquear_condiciones) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>>
                                <input type="hidden" name="plazoh" value="<?php echo $plazo ?>">
                        </td>
<?php
        if ($_SESSION["FUNC_MUESTRACAMPOS1"])
        {
?>
<!--                        <td>SUMA AL PRESUPUESTO</td>
                        <td><input type="text" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>-->
                        <input type="hidden" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>">
<?php
        }
        else
        {
?>
                <input type="hidden" name="suma_al_presupuesto" value="<?php echo number_format($suma_al_presupuesto, 0, ".", ",") ?>">
<?php
        }
?>
                </tr>
                <tr>
                        <td width="110">TIPO COMISI&Oacute;N</td>
                        <td>
                                <select id="tipo_tasa_comision" name="tipo_tasa_comision"  style="width:195px; background-color:#EAF1DD;">
                                        <?php                                        
                                        $queryTasaC = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE id_tasa_comision = '".$fila['id_tasa_comision']."'";
                                                        
                                        $rsTC = sqlsrv_query($link, $queryTasaC);
                                        while ($fTC = sqlsrv_fetch_array($rsTC, SQLSRV_FETCH_ASSOC)){
                                                if($fTC['marca_unidad_negocio'] == 1){ $textoTipoComsion = 'F'; }else{ $textoTipoComsion = 'K'; } ?>
                                                <option value="<?=$fTC['id_tasa_comision']?>">TIPO <?=$textoTipoComsion.' '.$fTC['id_tipo']?></option><?php
                                        } ?>                                                        
                                </select>
                        </td>
                </tr>
                </table>
                </div>
                <br>
                <h2>SEGURO (<a href="#" onClick="window.open('simulaciones_seguro.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>', 'SEGURO<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=750,height=400,top=0,left=0');">Historial</a>)</h2>
                
                <div class="box1 oran clearfix">
                <table border="0" cellspacing=1 cellpadding=2>
                <tr>
                        <td>PLAN&nbsp;</td>
                        <td>
<?php
        if (!$estado || $estado == "ING" || $estado == "EST")
        {
        
?>
<select id="id_plan_seguro" name="id_plan_seguro" style="width:320px; background-color:#EAF1DD" onChange="CargarSeguro(this.options[this.selectedIndex].text); TotalizarComprasCartera(); recalcular()">
        <option value=""></option>
                <?php
                $queryDB = "select id_plan, nombre, valor from planes_seguro where estado = '1' OR id_plan = '".$id_plan_seguro."' order by nombre";
                $rs1 = sqlsrv_query($link, $queryDB);
                while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
                {
                if ($fila1["id_plan"] == $id_plan_seguro)
                $selected_plan = " selected";
                else
                $selected_plan = "";
                echo "<option value=\"".$fila1["id_plan"]."\"".$selected_plan.">".($fila1["nombre"])." $".number_format($fila1["valor"], 0, ".", ",")."</option>\n";
                }
                ?>
</select>
<?php
     }else{
        
?>
    <input type="text" name="nombre_plan_seguro" value="<?php echo ($nombre_plan_seguro) ?>" style="width:300px" readonly>
    <input type="hidden" name="id_plan_seguro" value="<?php echo $id_plan_seguro ?>">
<?php
        }
        
?>
      <input type="hidden" name="id_plan_seguroh" value="<?php echo $id_plan_seguro ?>">
          &nbsp;&nbsp;&nbsp;
      </td>
      <td>VALOR&nbsp;</td>
      <td><input type="text" name="valor_seguro" value="<?php echo number_format($valor_seguro, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
      <td>
                                <input type="text" id="nombres_asegurabilidad"  value ="<?= $nombres_asegurabilidad ?>" hidden>
         <input type="text" id="apellidos_asegurabilidad"  value ="<?= $apellidos_asegurabilidad?>" hidden>
         <input type="text" id="tipo_identificacion"  value ="<?= $tipo_documento_asegurabilidad?>" hidden>
         <input type="text" id="sexo"  value ="<?= $sexo_asegurabilidad?>" hidden>
         <input type="text" id="estatura"  value ="<?= $estatura_asegurabilidad?>" hidden>
         <input type="text" id="peso"  value ="<?= $peso_asegurabilidad?>" hidden>
         <input type="text" id="estadoConsultar"  value ="<?= $fila['estado']?>" hidden>
          <?php
           $asegurabilidad =sqlsrv_query($link, "SELECT *,  DATEDIFF(day, getdate(), fecha_creacion) AS dia_montaj from asegurabilidad_colpensiones where cedula = '".$cedula_asegurabilidad."' and asegurado !=4 order by id_registro desc", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        
           if(sqlsrv_num_rows($asegurabilidad)==1){
                   $datos_asegurabilidad = sqlsrv_fetch_array($asegurabilidad, SQLSRV_FETCH_ASSOC);
           ?>        
                   <input type="text" id="<?= $datos_asegurabilidad['id_solicitud']?>" style="width:75%; background:#f3cc30;  font-weight:bold; color:white;text-align:center;"value="Cargando..." class="estado_asegurabilidad" readonly asegurado="<?=$datos_asegurabilidad["asegurado"]?>" idCalificacion="">
                    <input type="text" id="valor_cumulo" value="<?= $datos_asegurabilidad['valor_cumulo']?>" hidden>
                    <input type="text" id="valor_asegurado" value="<?= $datos_asegurabilidad['valor_asegurado']?>" hidden>
                   <a id="detalle_asegurabilidad">ver</a>
                   <?php
                  
                           if(($datos_asegurabilidad['asegurado']==3 || $datos_asegurabilidad['asegurado']==0) && $datos_asegurabilidad['dia_montaj']==0){
                             ?>
                              <input type="button" value="Cerrar Solicitud"   onclick="cerrar_solicitud_asegurabilidad()">
                             <?php
                           }
                   ?>
                  
           <?php
           }else{
           ?>
               <input type="button" value="Asegurabilidad" style="background: #ff5324; color:white;" class="estado_asegurabilidad" id="0" onclick="crear_solicitud_asegurabilidad()">
          <?php
           }
          ?>
                </tr>
</table>
</div>
	</td>
	<td width="20">&nbsp;</td>
	<td valign="top">
		<h2>EGRESOS</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 height="61">
		<tr>
			<td>TOTAL EGRESOS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td><input type="text" name="total_egresos" value="<?php echo number_format($total_egresos, 0, ".", ",") ?>" ></td>
		</tr>
		</table>
		</div>
		<br>
		<h2>C&Aacute;LCULO DEL CUPO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td style="white-space:nowrap;">SALARIO M&Iacute;NIMO</td>
			<td>
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR")
	{
	
?>
				<select name="salario_minimo" style="width:130px; text-align:right; background-color:#EAF1DD;" onChange="recalcular()">
<?php

		$queryDB = "select salario_minimo from salario_minimo where ano in (YEAR('".$fecha_estudio."'), YEAR('".$fecha_estudio."') - 1, YEAR('".$fecha_estudio."') + 1) order by ano DESC";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["salario_minimo"] == $salario_minimo)
			{
				$selected_sm = " selected";
				$existe_sm = 1;
			}
			else
				$selected_sm = "";
			
			echo "<option value=\"".$fila1["salario_minimo"]."\"".$selected_sm.">".number_format($fila1["salario_minimo"], 0, ".", ",")."</option>\n";
		}
		
		if (!$existe_sm && $fecha_estudio)
		{
		
?>
					<option value="<?php echo $salario_minimo ?>" selected><?php echo number_format($salario_minimo, 0, ".", ",") ?></option>
<?php

		}
		
?>
				</select>
				<input type="hidden" name="salario_minimoh" value="<?php echo $salario_minimo ?>">
<?php

	}
	else
	{
	
?>
				<input type="text" name="salario_minimo" value="<?php echo number_format($salario_minimo, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly>
				<input type="hidden" name="salario_minimoh" value="<?php echo $salario_minimo ?>">
<?php

	}
	
?>
			</td>
			<?php
			if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?>
				<td style="white-space:nowrap;">AUMENTO SALARIO MINIMO</td>
				<td align="center">
					<input type="checkbox" name="aumento_salario_minimo_checked" onclick="if (this.checked == true) { document.formato.aumento_salario_minimo.value = 1;  }else{ document.formato.aumento_salario_minimo.value = 0; }" value="1" <?php echo $aumento_salario_checked ?>>
					<input type="hidden" name="aumento_salario_minimo" value="<?=$aumento_salario_minimo?>">
				</td>
				<?php
			}else{ ?>
				
				<td style="white-space:nowrap;">AUMENTO SALARIO MINIMO</td>
				<td align="center">
					<input type="checkbox" value="1" <?php echo $aumento_salario_checked ?>>
					<input type="hidden" name="aumento_salario_minimo" value="<?=$aumento_salario_minimo?>">
				</td>
			<?php		
			} ?>
		</tr>
		<tr>
			<td style="white-space:nowrap;">INGRESOS - APORTES</td>
			<td><input type="text" name="ingresos_menos_aportes" value="<?php echo number_format($ingresos_menos_aportes, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
			<td style="white-space:nowrap;">SALARIO LIBRE MENSUAL</td>
			<td align="right"><input type="text" name="salario_libre" value="<?php echo number_format($salario_libre, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold; background-color:#8DB4E3" readonly></td>
		</tr>
		</table>
		</div>
		<br>
		<h2>AN&Aacute;LISIS DE RIESGO (La informaci&oacute;n de esta secci&oacute;n es inexacta para pensionados)</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=0 cellpadding=0 width="95%" height="197">
		<tr>
			<td>
				<table border="0" cellspacing=1 cellpadding=2>
				<tr>
					<td>VINCULACI&Oacute;N CLIENTE</td>
					<td><input type="text" id="nivel_contratacion" name="nivel_contratacion" value="<?php echo $nivel_contratacion ?>" size="25" style="text-align:center; font-weight:bold;" readonly></td>
				</tr>
				<tr>
					<td>¿CLIENTE EMBARGADO?</td>
					<td>
						<select name="embargo_actual" style="width:195px; text-align:center; background-color:#EAF1DD;" onChange="recalcular()">
							<option value="SI"<?php echo $embargo_actual_si ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI</option>
							<option value="NO"<?php echo $embargo_actual_no ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NO</option>
						</select>
						<!--<input type="text" name="embargo_actual" value="<?php echo $embargo_actual ?>" size="25" style="text-align:center; font-weight:bold;" readonly>-->
					</td>
				</tr>
				<tr>
					<td>HISTORIAL DE EMBARGOS</td>
					<td><input type="text" name="historial_embargos" value="<?php echo $historial_embargos ?>" size="25" style="text-align:center; background-color:#EAF1DD" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $historial_embargos ?>'; return false} else { recalcular(); }"></td>
				</tr>
<!--				<tr>
					<td>¿EMBARGO ALIMENTOS?</td>
					<td><input type="text" name="embargo_alimentos" value="<?php echo $embargo_alimentos ?>" size="25" style="text-align:center; font-weight:bold;" readonly></td>
				</tr>-->
				<input type="hidden" name="embargo_alimentos" value="<?php echo $embargo_alimentos ?>">
				<tr>
					<td>¿EMBARGO CENTRALES?</td>
					<td>
						<select name="embargo_centrales" style="width:195px; text-align:center; background-color:#EAF1DD;" onChange="recalcular()">
							<option value="SI"<?php echo $embargo_centrales_si ?>>SI</option>
							<option value="NO"<?php echo $embargo_centrales_no ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>CLAVE CONSULTA</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") {
							echo 
								'<td>
									<input type="text" name="clave" value="'.$clave.'" size="25" style="text-align:center; background-color:#EAF1DD" >
								</td>';
						}else{
							echo 
								'<td>
									<input type="text" name="clave" value="'.$clave.'" size="25" style="text-align:center; background-color:#EAF1DD" readonly>
								</td>';
						}
					?>				
				</tr>

				<tr>
					<td>¿COINCIDE DIRECCION UBICA?</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1) {
							echo '<td>
								<select name="supera_direccion_ubica" style="width:195px; text-align:right; background-color:#EAF1DD;">
									<option value="" '; if($fila["supera_direccion_ubica"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_direccion_ubica"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["supera_direccion_ubica"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}else{
							echo '<td>
								<select name="supera_direccion_ubica" style="width:195px; text-align:right; background-color:#EAF1DD; pointer-events: none;">
									<option value="" '; if($fila["supera_direccion_ubica"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_direccion_ubica"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["supera_direccion_ubica"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}
					?>
				</tr>
				<tr>
					<td>¿COINCIDE TELEFONO UBICA?</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1) {
							echo '<td>
								<select name="supera_telefono_ubica" style="width:195px; text-align:right; background-color:#EAF1DD;">
									<option value="" '; if($fila["supera_telefono_ubica"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_telefono_ubica"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["supera_telefono_ubica"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}else{
							echo '<td>
								<select name="supera_telefono_ubica" style="width:195px; text-align:right; background-color:#EAF1DD; pointer-events: none;">
									<option value="" '; if($fila["supera_telefono_ubica"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_telefono_ubica"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["supera_telefono_ubica"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}
					?>
				</tr>
				</table>
			</td>
			<td>
				<table border="0" cellspacing=1 cellpadding=2>
<!--				<tr>
					<td>¿TIENE CARTERA EN MORA?</td>
					<td>
						<select name="cartera_mora" style="background-color:#D8D8D8;" onChange="recalcular()">
							<option value="SI"<?php echo $cartera_mora_si ?>>SI</option>
							<option value="NO"<?php echo $cartera_mora_no ?>>NO</option>
						</select>
					</td>
				</tr>-->
				<input type="hidden" name="cartera_mora" value="<?php echo $cartera_mora ?>">
<!--				<tr>
					<td>VALOR CARTERA EN MORA</td>
					<td><input type="text" name="valor_cartera_mora" value="<?php echo number_format($valor_cartera_mora, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
				</tr>-->
				<input type="hidden" name="valor_cartera_mora" value="<?php echo number_format($valor_cartera_mora, 0, ".", ",") ?>">
<!--				<tr>
					<td>¿DESCUENTO POR FUERA?</td>
					<td><input type="text" name="descuentos_por_fuera" value="<?php echo $descuentos_por_fuera ?>" size="15" style="text-align:center; font-weight:bold;" readonly></td>
				</tr>-->
				<input type="hidden" name="descuentos_por_fuera" value="<?php echo $descuentos_por_fuera ?>">
<!--				<tr>
					<td>VALOR DESCUENTO POR FUERA</td>
					<td><input type="text" name="valor_descuentos_por_fuera" value="<?php echo number_format($valor_descuentos_por_fuera, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
				</tr>-->
				<input type="hidden" name="valor_descuentos_por_fuera" value="<?php echo number_format($valor_descuentos_por_fuera, 0, ".", ",") ?>">
				<tr>
					<td>PUNTAJE DATACREDITO</td>
					<td><input type="text" name="puntaje_datacredito" value="<?php echo $puntaje_datacredito ?>" size="15" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $puntaje_datacredito ?>'; return false} else { recalcular(); }" style="text-align:center; background-color:#D8D8D8"></td>
				</tr>
				<tr>
					<td>PUNTAJE TRANSUNION</td>
					<td><input type="text" name="puntaje_cifin" value="<?php echo $puntaje_cifin ?>" size="15" onChange="if(isnumber(this.value)==false) {this.value='<?php echo $puntaje_cifin ?>'; return false} else { recalcular(); }" style="text-align:center; background-color:#D8D8D8"></td>
				</tr>
					<tr>
					<td>CALIF. SECTOR FINANCIERO</td>
					<td>
						<select name="calif_sector_financiero" style="width:126px; text-align:center; background-color:#EAF1DD;">
							<option value=""></option>
							<option value="A"<?php echo $calif_sector_financiero_a ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A</option>
							<option value="B"<?php echo $calif_sector_financiero_b ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B</option>
							<option value="C"<?php echo $calif_sector_financiero_c ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C</option>
							<option value="D"<?php echo $calif_sector_financiero_d ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;D</option>
							<option value="E"<?php echo $calif_sector_financiero_e ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E</option>
							<option value="DR"<?php echo $calif_sector_financiero_dr ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DR</option>
							<option value="K"<?php echo $calif_sector_financiero_k ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;K</option>
							<option value="NA"<?php echo $calif_sector_financiero_na ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NA</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>CALIF. SECTOR REAL</td>
					<td>
						<select name="calif_sector_real" style="width:126px; text-align:center; background-color:#EAF1DD;">
							<option value=""></option>
							<option value="A"<?php echo $calif_sector_real_a ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A</option>
							<option value="B"<?php echo $calif_sector_real_b ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B</option>
							<option value="C"<?php echo $calif_sector_real_c ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C</option>
							<option value="D"<?php echo $calif_sector_real_d ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;D</option>
							<option value="E"<?php echo $calif_sector_real_e ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E</option>
							<option value="DR"<?php echo $calif_sector_real_dr ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DR</option>
							<option value="K"<?php echo $calif_sector_real_k ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;K</option>
							<option value="NA"<?php echo $calif_sector_real_na ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NA</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>CALIF. SECTOR COOPERATIVO</td>
					<td>
						<select name="calif_sector_cooperativo" style="width:126px; text-align:center; background-color:#EAF1DD;">
							<option value=""></option>
							<option value="A"<?php echo $calif_sector_cooperativo_a ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A</option>
							<option value="B"<?php echo $calif_sector_cooperativo_b ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B</option>
							<option value="C"<?php echo $calif_sector_cooperativo_c ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C</option>
							<option value="D"<?php echo $calif_sector_cooperativo_d ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;D</option>
							<option value="E"<?php echo $calif_sector_cooperativo_e ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E</option>
							<option value="DR"<?php echo $calif_sector_cooperativo_dr ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DR</option>
							<option value="K"<?php echo $calif_sector_cooperativo_k ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;K</option>
							<option value="NA"<?php echo $calif_sector_cooperativo_na ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NA</option>
						</select>
					</td>
				</tr>
				<tr>
				<td></td>
				</tr>
				<tr>
					<td>¿SUPERA PROFORENSE?</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1) {
							echo '<td>
								<select name="supera_proforense" style="width:126px; text-align:right; background-color:#EAF1DD;">
									<option value="" '; if($fila["supera_proforense"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="APROBADO" '; if($fila["supera_proforense"] == "APROBADO") { echo "selected"; } echo '>APROBADO</option>
									<option value="NEGADO" '; if($fila["supera_proforense"] == "NEGADO") { echo "selected"; } echo '>NEGADO</option>
									<option value="SIN VALIDACION DACTILAR" '; if($fila["supera_proforense"] == "SIN VALIDACION DACTILAR") { echo "selected"; } echo '>SIN VALIDACION DACTILAR</option>
									<option value="NO APLICA" '; if($fila["supera_proforense"] == "NO APLICA") { echo "selected"; } echo '>NO APLICA</option>
								</select>
							</td>';
						}else{
							echo '<td>
								<select name="supera_proforense" style="width:126px; text-align:right; background-color:#EAF1DD; pointer-events: none;">								
									<option value="" '; if($fila["supera_proforense"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="APROBADO" '; if($fila["supera_proforense"] == "APROBADO") { echo "selected"; } echo '>APROBADO</option>
									<option value="NEGADO" '; if($fila["supera_proforense"] == "NEGADO") { echo "selected"; } echo '>NEGADO</option>
									<option value="SIN VALIDACION DACTILAR" '; if($fila["supera_proforense"] == "SIN VALIDACION DACTILAR") { echo "selected"; } echo '>SIN VALIDACION DACTILAR</option>
									<option value="NO APLICA" '; if($fila["supera_proforense"] == "NO APLICA") { echo "selected"; } echo '>NO APLICA</option>
								</select>
							</td>';
						}
					?>
				</tr>
				<tr>
					<td>¿PAGARE AUTENTICADO?</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1) {
							echo '<td>
								<select name="pagare_autenticado" style="width:126px; text-align:right; background-color:#EAF1DD;">
									<option value="" '; if($fila["pagare_autenticado"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["pagare_autenticado"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["pagare_autenticado"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}else{
							echo '<td>
								<select name="pagare_autenticado" style="width:126px; text-align:right; background-color:#EAF1DD; pointer-events: none;" >
									<option value="" '; if($fila["pagare_autenticado"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["pagare_autenticado"] == "SI") { echo "selected"; } echo '>SI</option>
									<option value="NO" '; if($fila["pagare_autenticado"] == "NO") { echo "selected"; } echo '>NO</option>
								</select>
							</td>';
						}
					?>
				</tr>
				<tr>
					<td>¿SUPERA EVIDENTE?</td>
					<?php
						if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1) {
							echo '<td>
								<select name="supera_evidente" style="width:126px; text-align:right; background-color:#EAF1DD;">
									<option value="" '; if($fila["supera_evidente"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_evidente"] == "SI") { echo "selected"; } echo'>SI</option>
									<option value="NO" '; if($fila["supera_evidente"] == "NO") { echo "selected"; } echo'>NO</option>
									<option value="NO APLICA" '; if($fila["supera_evidente"] == "NO APLICA") { echo "selected"; } echo'>NO APLICA</option>
								</select>
							</td>';
						}else{
							echo '<td>
								<select name="supera_evidente" style="width:126px; text-align:right; background-color:#EAF1DD; pointer-events: none;">
									<option value="" '; if($fila["supera_evidente"] == "") { echo "selected"; } echo '>SELECCIONE...</option>
									<option value="SI" '; if($fila["supera_evidente"] == "SI") { echo "selected"; } echo'>SI</option>
									<option value="NO" '; if($fila["supera_evidente"] == "NO") { echo "selected"; } echo'>NO</option>
									<option value="NO APLICA" '; if($fila["supera_evidente"] == "NO APLICA") { echo "selected"; } echo'>NO APLICA</option>
								</select>
							</td>';
						}
					?>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>

		<?php
		if($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == 1 || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" ){
                ?>
                <br>
                <h2>DECLARACION ENFERMEDADES</h2>
                <div class="box1 oran clearfix" disable>
                        <table id="declaracion_enfermedad" class="default" style="border-collapse: collapse; width: 100%; text-align: left;">
                                <thead>
                                        <tr style="margin: 2px;font-weight: bold;padding-bottom: 2px !important;">
                                                <th>ENFEMEDADES GENERALES</th>
                                                <th>MARCAR EN CASO AFIRMATIVO</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr style="padding: 8px; text-align: left;">
                                                <td>ASMA</td>
                                                <td><input type="checkbox" id="check_asma" name="asma" class="enfermedad" value="<?php echo $asma ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="border-spacing: 0 2px; padding: 8px; text-align: left;">
                                                <td>DIABETES</td>
                                                <td><input type="checkbox" id="check_diabetes" name="diabetes" class="enfermedad" value="<?php echo $diabetes ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>CANCER</td>
                                                <td><input type="checkbox" id="check_cancer" name="cancer" class="enfermedad" value="<?php echo $cancer ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>VIH</td>
                                                <td><input type="checkbox" id="check_vih" name="vih" class="enfermedad" value="<?php echo $vih ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>HIPERTENCION ARTERIAL</td>
                                                <td><input type="checkbox" id="check_hipertension" class="enfermedad" name="hipertension" value="<?php echo $hipertension ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>TIROIDES</td>
                                                <td><input type="checkbox" id="check_tiroides" class="enfermedad" name="tiroides" value="<?php echo $tiroides ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>CIRUGIA BARIATRICA</td>
                                                <td><input type="checkbox" id="check_cirugia" class="enfermedad" name="cirugia" value="<?php echo $cirugia ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>TABAQUISMO</td>
                                                <td><input type="checkbox" id="check_tabaquismo" class="enfermedad" name="tabaquismo" value="<?php echo $tabaquismo ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>ENFERMEDAD PULMONAR</td>
                                                <td><input type="checkbox" id="check_pulmonar" class="enfermedad" name="pulmonar" value="<?php echo $pulmonar ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>ENFERMEDADES DEL CORAZON</td>
                                                <td><input type="checkbox" id="check_enfermedad_corazon" class="enfermedad" name="pulmonar" value="<?php echo $pulmonar ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>ARTRITIS</td>
                                                <td><input type="checkbox" id="check_artritis" name="artritis" class="enfermedad" value="<?php echo $artritis ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>GLAUCOMA</td>
                                                <td><input type="checkbox" id="check_glaucoma" name="glaucoma" class="enfermedad" value="<?php echo $glaucoma ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>HEPATITIS</td>
                                                <td><input type="checkbox" id="check_hepatitis" name="hepatitis" class="enfermedad" value="<?php echo $hepatitis ?>" size="25" style="text-align:center; font-weight:bold;"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>ALGUNA OTRA ENFERMEDAD</td>
                                                <td><input type="text" id="otra_enfermedad" name="otra_enfermedad" class="enfermedad" value="<?php echo $otra_enfermedad ?>" size="25" style="text-align:left;" placeholder="Cual?"></td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>HA ESTADO HISPITALIZADO EN LOS ULTIMOS 12 MESES?</td>
                                                <td>
                                                        <input type="checkbox" id="check_hospitalizado" name="hospitalizado" class="enfermedad" value="" size="25" style="text-align:center; font-weight:bold;">
                                                        <textarea  id="hospitalizado_razon" name="hospitalizado" class="" style="width: 50%; background-color: white; height: 30px;" disabled></textarea>
                                                </td>
                                        </tr>
                                        <tr style="padding: 8px; text-align: left; margin:2px">
                                                <td>HA TENIDO OPERACIONES EN LOS ULTIMOS 2 AÑOS?</td>
                                                <td>
                                                        <input type="checkbox" id="check_operado" name="operado" class="enfermedad" value="<?php echo $hepatitis ?>" size="25" style="text-align:center; font-weight:bold;">
                                                        <textarea id="operaciones_razon" name="operaciones" class="" style="width: 50%; background-color: white; height: 30px;" disabled></textarea>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
        <?php
                 }
		if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" 
			|| $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_REVISION_GARANTIAS"] == "1" ){

			if($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") { ?>

		        <table class="table" >
					<tr>
						<td style="width: 68%;"> 
							<h2>
					            CONSULTAS A CENTRALES
					        </h2>
					        <div>
					            <div class="box1 oran clearfix">
					                <table border="1" class="table" >
					                    <tr align="center" style="text-align: center;">						
											<td align="center">
					                            <div id="consulta_cifinUP">
					                                <img class="logos_buros" src="../images/logo_transunion.png">
					                                <h3>Ubica Plus</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-cifinUP" type="button" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-cifinUP" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-cifinUP" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>
					                        <td align="center">
					                            <div id="consulta_cifin">
					                                <img class="logos_buros" src="../images/logo_transunion.png">
					                                <h3>Informacion Comercial</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-cifin" type="button" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-cifin" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-cifin" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>							
					                        <td align="center">
					                            <div id="consulta_datacredito">
					                                <img class="logos_buros" src="../images/logo_datacredito.png">
					                                <h3>Data Credito (HC)</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-experianDC" type="button" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-experianDC" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-experianDC" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>
											<td align="center">
					                            <div id="consulta_datacredito">
													<br>
					                                <h3>Scoring</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="resumenScoring" type="button" servicio="RESUMEN_SCORING"
					                                    proveedor="RESUMEN_SCORING">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                            
					                            </div>
					                        </td>
					                    </tr>
					                </table>
					                <br>             
					            </div>
					        </div>
					    </td>
					    <td style="width: 2%;"></td>
		        		<td style="width: 22%;">
							<h2>
					            CONSULTAS A EXTERNOS
					        </h2>
					        <div>
					        	<div class="box1 oran clearfix">
					        		<table border="1" class="table" >
					        			<tr align="center" style="text-align: center;">
					        				<td align="center">
					        					<div id="consulta_cifinUP">
					        						<img src="../images/judicial.png" style="width: 70px; height: 18px;">
					        						<h3>Antecedentes</h3>
					        					</div>
					        					<div class="badges">
					        						<a href="<?=$urlPrincipal?>/formatos/judiciales/reporteCentralesJudiciales.php?id_simulacion=<?=$_REQUEST["id_simulacion"]?>&tipo=1" target="_blank">
					        							<div class="badge-success" id="disponible-judicial" type="button" servicio="ANTECEDENTES" proveedor="Analyzer"><img src="../images/chequeado.png" style="margin-top: inherit;"></div>
						        				</a>
					        					</div>
					        					<label id="label-judicial">Generar</label>
					        				</td>
											<td  align="center" style="padding-left: 10px;">
												<div id="consulta_legalCheck">
													<img src="../images/logo_transunion.png" style="width: 70px; height: 18px;">
														<h3>Legal Check</h3>
												</div>
												<div class="badges">
													<div class="badge-success" id="disponible-legal-check" type="button" servicio="LEGALCHECK" proveedor="TRANSUNION">
														<img src="../images/chequeado.png" style="margin-top: inherit;">
													</div>
													<div class="badge-info" id="nodisponible-legal-check" servicio="LEGALCHECK" proveedor="TRANSUNION">
														<img src="../images/novalidado.png" style="margin-top: inherit;">
													</div>
													<div class="badge-error" id="calendario-legal-check" servicio="LEGALCHECK" proveedor="TRANSUNION" data-service="consulta_ws">
														<img src="../images/calendario.png" style="margin-top: inherit;">
													</div>
												</div>
											</td>
										</tr>
									</table>
									<br>
								</div>
							</div>
						</td>
					</tr>
				</table>
		<?php 
			}else{ ?>
				<table class="table" >
					<tr>
						<td style="width: 68%;"> 
							<h2>
					            CONSULTAS A CENTRALES
					        </h2>
					        
					        <div>
					            <div class="box1 oran clearfix">
					                <table border="1" class="table" >
					                    <tr align="center" style="text-align: center;">
										
											<td align="center">
					                            <div id="consulta_cifinUP">
					                                <img class="logos_buros" src="../images/logo_transunion.png">
					                                <h3>Ubica Plus</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-cifinUP" type="button" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-cifinUP" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-cifinUP" servicio="UBICAPLUS"
					                                    proveedor="TRANSUNION" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>

					                        <td align="center">
					                            <div id="consulta_cifin">
					                                <img class="logos_buros" src="../images/logo_transunion.png">
					                                <h3>Informacion Comercial</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-cifin" type="button" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-cifin" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-cifin" servicio="INFORMACION_COMERCIAL"
					                                    proveedor="TRANSUNION" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>
											
					                        <td align="center">
					                            <div id="consulta_datacredito">
					                                <img class="logos_buros" src="../images/logo_datacredito.png">
					                                <h3>Data Credito (HC)</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="disponible-experianDC" type="button" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-experianDC" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-experianDC" servicio="HDC_ACIERTA"
					                                    proveedor="EXPERIAN" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					                        </td>
					                        
											<td align="center">
					                            <div id="consulta_datacredito">
												<br>
					                                <h3>Scoring</h3>
					                            </div>
					                            <div class="badges">
					                                <div class="badge-success" id="resumenScoring" type="button" servicio="RESUMEN_SCORING"
					                                    proveedor="RESUMEN_SCORING">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>		                            
					                            </div>
					                        </td>
					                    </tr>
					                </table>
					                <br>              
					            </div>
					        </div>
					    </td>
					    <td style="width: 2%;"></td>
		        		<td style="width: 22%;">
							<h2>
					            CONSULTAS A EXTERNOS
					        </h2>
					        <div>
					        	<div class="box1 oran clearfix">
					        		<table border="1" class="table" >
					        			<tr align="center" style="text-align: center;">
					        				<td  align="center" style="padding-left: 10px;">
					        					<div id="consulta_legalCheck">
					        						<img src="../images/logo_transunion.png" style="width: 70px; height: 18px;">
					        						<h3>Legal Check</h3>
					        					</div>
					        					<div class="badges">
					                                <div class="badge-success" id="disponible-legal-check" type="button" servicio="LEGALCHECK"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/chequeado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-info" id="nodisponible-legal-check" servicio="LEGALCHECK"
					                                    proveedor="TRANSUNION">
					                                    <img src="../images/novalidado.png" style="margin-top: inherit;">
					                                </div>
					                                <div class="badge-error" id="calendario-legal-check" servicio="LEGALCHECK"
					                                    proveedor="TRANSUNION" data-service="consulta_ws">
					                                    <img src="../images/calendario.png" style="margin-top: inherit;">
					                                </div>
					                            </div>
					        				</td>		        	
										</tr>
									</table>
									<br>
								</div>
							</div>
						</td>
					</tr>
				</table>
			<?php	
			}
		} ?>
	</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td colspan="3" align="center">
		<h2>COMPRAS DE CARTERA</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="100%">
		<tr>
			<th>No.</th>
			<th>ENTIDAD</th>
			<th>OBSERVACI&Oacute;N</th>
			<th>CUOTA</th>
			<th>VALOR A PAGAR</th>
			<th>F VENCIMIENTO</th>
			<?php if ($_SESSION["FUNC_ADJUNTOS"] && $_REQUEST["id_simulacion"]) { ?><th><img src="../images/estadocuenta.png" title="Certificaci&oacute;n"></th><?php } ?>
			<th>SE COMPRA?</th>
		</tr>
<?php
$validar_subestado=0;
if (in_array($fila["id_subestado"],$subestados_bloqueo_comprascartera))
{

	$validar_subestado=1;

}

for ($i = 1; $i <= $ultimo_consecutivo_compra_cartera + 10; $i++)
{

	?>
	<tr id="cc_tr<?php echo $i ?>"<?php if ($i > $ultimo_consecutivo_compra_cartera) { echo " style=\"display: none;\""; } ?>>
		<td align="center"><?php echo $i ?></td>
		<td><select  id="id_entidad<?php echo $i ?>" name="id_entidad<?php echo $i ?>" style="<?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> pointer-events: none; <?php } ?>width:450px; background-color:<?php if ($i != $nro_compra_cartera_seguro) { ?>#EAF1DD;"<?php } else { ?>#FFFFFF;"<?php } ?> onChange="TotalizarComprasCartera()">
				<option value=""></option>
			<?php

					$queryDB = "select id_entidad, nombre from entidades_desembolso where 1 = 1";
					
					if ($i == $nro_compra_cartera_seguro)
						$queryDB .= " AND id_entidad = '".$entidad_seguro."'";

					$queryDB .= " order by nombre";
					
					$rs1 = sqlsrv_query($link, $queryDB);
					
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
					{
						if ($fila1["id_entidad"] == $id_entidad[$i - 1])
							$selected_entidad = " selected";
						else
							$selected_entidad = "";
							
						echo "<option value=\"".$fila1["id_entidad"]."\"".$selected_entidad.">".($fila1["nombre"])."</option>\n";
					}
					
			?>
			</select></td>
		<td>
			
			<input <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php } ?> type="text" id="entidad<?php echo $i ?>" name="entidad<?php echo $i ?>" value="<?php echo str_replace("\"", "&#34;", $entidad[$i - 1]) ?>" size="25" style="background-color:<?php if ($i != $nro_compra_cartera_seguro) { ?>#EAF1DD;"<?php } else { ?>#FFFFFF;" readOnly<?php } ?>></td>
		
		<td align="right"><input <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php }else{ ?>onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }" <?php } ?> type="text" id="cuota<?php echo $i ?>" name="cuota<?php echo $i ?>" value="<?php echo number_format($cuota[$i - 1], 0, ".", ",") ?>" size="10"   style="text-align:right; background-color:<?php if ($i != $nro_compra_cartera_seguro) { ?>#EAF1DD;"<?php } else { ?>#FFFFFF;" readOnly<?php } ?>></td>
		
		<td align="right"><input <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php }else{ ?>onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } ?> type="text" id="valor_pagar<?php echo $i ?>" name="valor_pagar<?php echo $i ?>" value="<?php echo number_format($valor_pagar[$i - 1], 0, ".", ",") ?>" size="10"   style="text-align:right; background-color:<?php if ($i != $nro_compra_cartera_seguro) { ?>#EAF1DD;"<?php } else { ?>#FFFFFF;" readOnly<?php } ?>></td>
	

		<td align="center"><input <?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)){ ?> readonly <?php } ?> type="text" autocomplete="off" class="fecha_datepicker" id="fecha_vencimiento<?php echo $i ?>" name="fecha_vencimiento<?php echo $i ?>" value="<?php echo $fecha_vencimiento[$i - 1] ?>" size="10" style="<?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR"  && ($i <= $ultimo_consecutivo_compra_cartera)) { ?> pointer-events: none; <?php } ?>text-align:center; background-color:#EAF1DD;" onChange="if (this.value != '') { if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_vencimiento[$i - 1] ?>'; return false} }"></td>
<?php
                

	if ($_SESSION["FUNC_ADJUNTOS"] && $_REQUEST["id_simulacion"]) {
			?>
			<td align="center">
			<?php 
			if ($nombre_grabado[$i - 1]) { ?>
				<a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones",$_REQUEST["id_simulacion"]."/adjuntos/".$nombre_grabado[$i - 1]) ?>', 'CERTIFICACION<?php echo $i ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/estadocuenta.png" title="Certificaci&oacute;n"></a>
				<?php 
			} 
			else if ($estado == "ING" || $estado == "EST") 
			{
				//if($validar_subestado==0 || (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO"))){ 
					?>
					<a href="#" onClick="window.open('cargar_certificacion.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>&consecutivo=<?php echo $i ?>', 'CARGAR_CERTIFICACION<?php echo $i ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">Cargar Certificaci&oacute;n</a>
					<?php 
				//}
			}
			else 
			{ echo "&nbsp;"; } ?>
				
					<input type="hidden" id="nombre_grabado<?php echo $i ?>" name="nombre_grabado<?php echo $i ?>" value="<?php echo $nombre_grabado[$i - 1] ?>">
				</td>
				<?php } ?>
				
		<td align="center">
			<input type="hidden" id="dias_entregah<?php echo $i ?>" name="dias_entregah<?php echo $i ?>" value="<?php echo number_format($dias_entregah[$i - 1], 0) ?>">
			<input type="hidden" id="dias_vigenciah<?php echo $i ?>" name="dias_vigenciah<?php echo $i ?>" value="<?php echo number_format($dias_vigenciah[$i - 1], 0) ?>">
			<select  id="se_compra<?php echo $i ?>" name="se_compra<?php echo $i ?>"<?php if (!$bloquear_condiciones) { ?> style="<?php if($validar_subestado==1 && $_SESSION["S_TIPO"]!="ADMINISTRADOR" ){ ?> pointer-events: none; <?php } ?>width: 42px; background-color:<?php if ($i != $nro_compra_cartera_seguro) { ?>#EAF1DD;"<?php } else { ?>#FFFFFF;"<?php } ?><?php } ?> onChange="TotalizarComprasCartera(); recalcular();<?php if ($_SESSION["FUNC_AGENDA"]) { ?> <!--recalcular_agenda('<?php echo $i ?>', this.value);--><?php } ?>">
				<?php if (!$bloquear_condiciones || ($bloquear_condiciones && $se_compra_si[$i - 1])) { ?><option value="SI"<?php echo $se_compra_si[$i - 1] ?>>SI</option><?php } ?>
				<?php if ((!$bloquear_condiciones && $i != $nro_compra_cartera_seguro) || ($bloquear_condiciones && $se_compra_no[$i - 1])) { ?><option value="NO"<?php echo $se_compra_no[$i - 1] ?>>NO</option><?php } ?>
			</select>
		</td>
	</tr>
<?php

}

?>
<tr>
	<td align="center"><a href="#cc_tr1" onClick="AdicionarCompraCartera()"><img src="../images/adicionar.png" title="Adicionar Compra de Cartera"></a></td>
	<td>&nbsp;</td>
	<td><b>TOTAL COMPRAS DE CARTERA</b></td>
			<td align="right"><input type="text" name="total_cuota" value="<?php echo number_format($total_cuota_max, 0, ".", ",") ?>" size="10" style="text-align:right; font-weight:bold;" readonly></td>
			<td align="right"><input type="text" name="total_valor_pagar" value="<?php echo number_format($total_valor_pagar, 0, ".", ",") ?>" size="10" style="text-align:right; font-weight:bold;" readonly></td>
			<td align="center">&nbsp;</td>
			<?php if ($_SESSION["FUNC_ADJUNTOS"] && $_REQUEST["id_simulacion"]) { ?><td align="center">&nbsp;</td><?php } ?>
			<td align="center"><input type="text" name="total_se_compra" value="<?php echo $total_se_compra ?>" size="4" style="text-align:right; font-weight:bold;" readonly></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
<?php

	$creditos_vigentes = sqlsrv_query($link, "select cedula from simulaciones where cedula = '".$cedula."' AND (estado = 'DES' OR (estado = 'EST' AND estado_tesoreria = 'PAR')) AND id_simulacion != '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (sqlsrv_num_rows($creditos_vigentes))
	{
	
?>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td colspan="3" align="center">
		<h2>AN&Aacute;LISIS DE CARTERA</h2>
		<div class="box1 clearfix">
		<iframe id="frm_creditos_vigentes" src="simulaciones_creditos_vigentes.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&cedula=<?php echo $cedula ?>" width="100%" height="140px"></iframe>
		</div>
	</td>
</tr>
<?php

	}
	
?>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td valign="top">
		<h2>RETANQUEOS</h2>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="100%">
		<tr>
			<th width="15">&nbsp;</th>
			<th width="32%">NO. LIBRANZA</th>
			<th width="32%">CUOTA</th>
			<th width="32%">VALOR</th>
		</tr>
		<tr>
			<td align="center">1</td>
			<td align="center"><input type="text" id="retanqueo1_libranza" name="retanqueo1_libranza" value="<?php echo $retanqueo1_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '1'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo1_libranzah" value="<?php echo $retanqueo1_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo1_cuota" name="retanqueo1_cuota" value="<?php echo number_format($retanqueo1_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo1_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo1_valor" name="retanqueo1_valor" value="<?php echo number_format($retanqueo1_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo1_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td align="center">2</td>
			<td align="center"><input type="text" id="retanqueo2_libranza" name="retanqueo2_libranza" value="<?php echo $retanqueo2_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '2'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo2_libranzah" value="<?php echo $retanqueo2_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo2_cuota" name="retanqueo2_cuota" value="<?php echo number_format($retanqueo2_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo2_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo2_valor" name="retanqueo2_valor" value="<?php echo number_format($retanqueo2_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo2_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td align="center">3</td>
			<td align="center"><input type="text" id="retanqueo3_libranza" name="retanqueo3_libranza" value="<?php echo $retanqueo3_libranza ?>" onChange="saldo_retanqueo('<?php echo $_REQUEST["cedula"] ?>', '<?php echo $_REQUEST["pagad"] ?>', '<?php echo $_REQUEST["id_simulacion"] ?>', this.value, '3'); recalcular();" size="22" style="text-align:center;<?php if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly<?php } ?>><input type="hidden" name="retanqueo3_libranzah" value="<?php echo $retanqueo3_libranza ?>"></td>
			<td align="center"><input type="text" id="retanqueo3_cuota" name="retanqueo3_cuota" value="<?php echo number_format($retanqueo3_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo3_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
			<td align="center"><input type="text" id="retanqueo3_valor" name="retanqueo3_valor" value="<?php echo number_format($retanqueo3_valor, 0, ".", ",") ?>" size="22" style="text-align:right;<?php if (($_REQUEST["cedula"] || $_REQUEST["id_simulacion"]) && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")) { ?> background-color:#EAF1DD;" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '' || document.formato.retanqueo3_libranza.value == '') { this.value = '0'; } recalcular(); separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td colspan="2" align="right">TOTAL RETANQUEOS&nbsp;&nbsp;&nbsp;</td>
			<td align="center"><input type="text" name="retanqueo_total_cuota" value="<?php echo number_format($retanqueo_total_cuota, 0, ".", ",") ?>" size="22" style="text-align:right;" readonly></td>
			<td align="center"><input type="text" name="retanqueo_total" value="<?php echo number_format($retanqueo_total, 0, ".", ",") ?>" size="22" style="text-align:right;" readonly></td>
		</tr>
		</table>
		</div>
		<br>
 		<table border="0" cellspacing=1 cellpadding=2 class="tab1" width="100%">
		<tr>
			<th colspan="2">OPCIONES DE CR&Eacute;DITO</th>
			<th>OPCI&Oacute;N CUOTA</th>
			<th colspan="2">OPCI&Oacute;N DESEMBOLSO</th>
		</tr>
		<tr>
			<td align="center"><input disabled type="radio" name="opcion_credito" value="CLI" onChange="recalcular()"<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> disabled <?php } ?><?php echo $opcion_credito_cli ?>></td>
			<td style="font-size:16"><b>CUPO DE LIBRE INVERSION</b></td>
			<td><input type="text" id="opcion_cuota_cli" name="opcion_cuota_cli" value="<?php echo number_format($opcion_cuota_cli, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_cli" name="opcion_desembolso_cli" value="<?php echo $opcion_desembolso_cli ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cli, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input disabled type="radio" name="opcion_credito" value="CCC" onChange="recalcular()"<?php echo $opcion_credito_ccc ?>></td>
			<td style="font-size:16"><b>CUPO CON COMPRAS</b></td>
			<td><input type="text" id="opcion_cuota_ccc" name="opcion_cuota_ccc" value="<?php echo number_format($opcion_cuota_ccc, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_ccc, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_ccc" name="opcion_desembolso_ccc" value="<?php echo number_format($opcion_desembolso_ccc, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_ccc, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input disabled type="radio" name="opcion_credito" value="CMP" onChange="recalcular()"<?php echo $opcion_credito_cmp ?>></td>
			<td style="font-size:16"><b>CUPO MAXIMO POSIBLE</b></td>
			<td><input type="text" id="opcion_cuota_cmp" name="opcion_cuota_cmp" value="<?php echo number_format($opcion_cuota_cmp, 0, ".", ",") ?>" size="15" style="height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_cuota_cmp, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
			<td colspan="2"><input type="text" id="opcion_desembolso_cmp" name="opcion_desembolso_cmp" value="<?php echo number_format($opcion_desembolso_cmp, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_cmp, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr>
			<td align="center"><input checked type="radio" name="opcion_credito" value="CSO" onChange="recalcular()"<?php echo $opcion_credito_cso ?>></td>
			<td style="font-size:16"><b>CUPO SOLICITADO</b></td>
			<td><input type="text" id="opcion_cuota_cso" name="opcion_cuota_cso" value="<?php echo number_format($opcion_cuota_cso, 0, ".", ",") ?>" size="15" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) { this.value='0'; return false; } else { if (this.value == '') { this.value = '0'; } if (parseFloat(this.value) > document.formato.opcion_cuota_ccc.value.replace(/\,/g, '')) { this.value = document.formato.opcion_cuota_ccc.value.replace(/\,/g, ''); alert('El valor de la cuota no debe ser mayor a $' + document.formato.opcion_cuota_ccc.value); } recalcular(); separador_miles(this); }" style="height:30; text-align:right; font-size:16; font-weight:bold; <?php if (number_format($opcion_cuota_cso, 0) <= 0) { ?> color:#CC0000;<?php } ?> <?php if (!$bloquear_condiciones) { ?> background-color:#EAF1DD;"<?php } else { ?>" readonly"<?php } ?>></td>
			<td colspan="2"> <input type="text" id="opcion_desembolso_cso" name="opcion_desembolso_cso" value="<?php echo number_format($opcion_desembolso_cso, 0, ".", ",") ?>" style="width:95%; height:30; text-align:right; font-size:16; font-weight:bold;<?php if (number_format($opcion_desembolso_cso, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		</table>
		<br><br>
		<h2>DESCUENTOS DESEMBOLSO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="100%">
		<tr>
			<th colspan="2">&nbsp;</th>
			<th>%</th>
			<th colspan="2">VALOR</th>
		</tr>
		<tr>
		<?php
		//SOLICITUD JUAN SEBASTIAN MILLAN, ANALISTAS DE CREDITO PUEDEN MODIFICAR INT ANTICIPADO 19/08/2022
		$val=0;
		//$queryAnalistasKreditPlus=sqlsrv_query("SELECT id_usuario FROM usuarios a WHERE id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND subtipo='ANALISTA_CREDITO' AND estado=1 AND id_usuario='".$_SESSION["S_IDUSUARIO"]."'",$link);

		$queryAnalistasKreditPlus=sqlsrv_query($link, "SELECT * FROM usuarios WHERE subtipo='COORD_CREDITO' AND id_usuario='".$_SESSION["S_IDUSUARIO"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($queryAnalistasKreditPlus)>0)
		{
			//$val=1;
		}
		
		?>
			<td colspan="2"><?php if ($sector == "PRIVADO") { echo "AVAL"; } else { echo "INTERESES ANTICIPADOS"; } ?></td>
			<td><input type="text" name="descuento1" value="<?php echo $descuento1 ?>" size="14" style="text-align:center;<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || in_array($_SESSION["S_IDUSUARIO"],$usuarios_permiso_gastosadmin)) { ?> background-color:#EAF1DD;" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $descuento1 ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $descuento1 ?>'; } recalcular(); }"<?php } else { if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly<?php } ?>></td>
			<td colspan="2"><input type="text" name="descuento1_valor" value="<?php echo number_format($descuento1_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr>
			<td colspan="2">ASESOR&Iacute;A FINANCIERA <a href="#" <?php if(!$aplicar_servicio_nube) { ?>style="display: none;" <?php } ?> onclick="verDetalleAsesoriaFinaciera(this); return false;">Ver Detalle</a></td>
			<td><input type="text" id="descuento2" name="descuento2" value="<?php echo $descuento2 ?>" size="14" style="text-align:center;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_IDUSUARIO"] == $usuario_trecuperamos) || in_array($_SESSION["S_IDUSUARIO"],$usuarios_permiso_gastosadmin) || $val==1) { ?> background-color:#EAF1DD;" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $descuento2 ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $descuento2 ?>'; } recalcular(); }"<?php } else { if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly<?php } ?>></td>
			<td colspan="2"><input type="text" name="descuento2_valor" value="<?php echo number_format($descuento2_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr class="expandable-asesoria" <?php if(!$aplicar_servicio_nube) { ?>style="display: none;" <?php } ?>>
			<td colspan="2" style="padding-left: 20px;">  ↳ SERVICIO EN LA NUBE</td>
			<td>SIN IVA S_NUBE 
				<?php if($_SESSION['S_TIPO'] == "ADMINISTRADOR"){ ?>
					<input type="checkbox" value="1" style="margin-left: 8px;" onchange="recalcular();" id="sin_iva_servicio_nube" name="sin_iva_servicio_nube" <?php if($sin_iva_servicio_nube){ echo "checked"; }?>>
				<?php }else{ ?>
					<input type="checkbox" value="1" style="margin-left: 8px; pointer-events: none;" id="sin_iva_servicio_nube" name="sin_iva_servicio_nube" <?php if($sin_iva_servicio_nube){ echo "checked"; }?>>
				<?php } ?>
			</td>
			<td colspan="2"><input type="text" name="descuento8_valor" value="<?php echo number_format($descuento8_valor, 0, ".", ",") ?>" style="background-color: #d9e3eb; width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr class="expandable-asesoria" <?php if(!$aplicar_servicio_nube) { ?>style="display: none;" <?php } ?>>
			<td colspan="2" style="padding-left: 20px;">  ↳ ASESOR&Iacute;A FINANCIERA</td>
			<td></td>
			<td colspan="2"><input type="text" name="descuento9_valor" value="<?php echo number_format($descuento9_valor, 0, ".", ",") ?>" style="background-color: #d9e3eb; width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr <?php if($aplicar_servicio_nube) { ?>style="display: none;" <?php } ?>>
			<td colspan="2">IVA</td>
			<td><input type="text" name="descuento3" value="<?php echo $descuento3 ?>" size="14" style="text-align:center;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
			<td colspan="2"><input type="text" name="descuento3_valor" value="<?php echo number_format($descuento3_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr <?php if(!$aplicar_servicio_nube) { ?>style="display: none;" <?php } ?>>
			<td colspan="2">NUEVO IVA</td>
			<td><input type="text" id="iva_real" name="iva_real" value="<?php echo $iva ?>" size="14" style="text-align:center;" readonly></td>
			<td colspan="2"><input type="text" name="descuento10_valor" value="<?php echo number_format($descuento10_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		
		<tr>
			<td colspan="2">GMF</td>
			<td><input type="text" name="descuento4" value="<?php echo $descuento4 ?>" size="14" style="text-align:center;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
			<td colspan="2"><input type="text" name="descuento4_valor" value="<?php echo number_format($descuento4_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
<?php

	if ($_REQUEST["cedula"] || $_REQUEST["id_simulacion"])
	{
		if ($_REQUEST["cedula"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagad"]."' and estado = '1' order by id_descuento");
		}
		
		if ($_REQUEST["id_simulacion"])
		{
			$descuentos_adicionales = sqlsrv_query($link, "select da.nombre, sd.id_descuento, sd.porcentaje from simulaciones_descuentos sd INNER JOIN descuentos_adicionales da ON sd.id_descuento = da.id_descuento where sd. id_simulacion = '".$_REQUEST["id_simulacion"]."' order by sd.id_descuento");
		}
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales, SQLSRV_FETCH_ASSOC))
		{
		
?>
		<tr>
			<td colspan="2"><?php echo $fila1["nombre"] ?></td>
			<td><input type="text" name="descuentoadicional<?php echo $fila1["id_descuento"] ?>" value="<?php echo $fila1["porcentaje"] ?>" size="14" style="text-align:center;" readonly></td>
			<td colspan="2"><input type="text" name="descuentoadicional<?php echo $fila1["id_descuento"] ?>_valor" value="<?php if ($fila["opcion_credito"] != "CLI") { echo number_format(($valor_credito - $retanqueo_total) * $fila1["porcentaje"] / 100, 0, ".", ","); } else { echo number_format($valor_credito * $fila1["porcentaje"] / 100, 0, ".", ","); } ?>" style="width:95%; text-align:right;" readonly></td>
		</tr>
<?php

		}
	}
	
?>
		<tr>
			<td colspan="2"><?php if ($fecha_estudio < "2018-01-01") { ?>ASESOR&Iacute;A FINANCIERA (RECUPERATE)<?php } else { ?>COMISI&Oacute;N POR VENTA (RETANQUEOS)<?php } ?></td>
			<td><input type="text" id="descuento5" name="descuento5" value="<?php echo $descuento5 ?>" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $descuento5 ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $descuento5 ?>'; } recalcular(); }" size="14" style="text-align:center;<?php if ($tipo_producto == "0" || (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"])) { ?> color:#FFFFFF;<?php } else if ($tipo_producto == "1") { ?> background-color:#EAF1DD;<?php } ?>"<?php if ($tipo_producto == "0" || (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"])) { ?> readonly<?php } ?>></td>
			<td colspan="2"><input type="text" id="descuento5_valor" name="descuento5_valor" value="<?php echo number_format($descuento5_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if ($tipo_producto == "0" || (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"])) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr>
			<td colspan="2"><?php if ($fecha_estudio < "2018-01-01") { ?>IVA (RECUPERATE)<?php } else { ?>IVA (COMISI&Oacute;N POR VENTA)<?php } ?></td>
			<td><input type="text" id="descuento6" name="descuento6" value="<?php echo $descuento6 ?>" onChange="if(isnumber_punto(this.value)==false) {this.value='<?php echo $descuento6 ?>'; return false} else { if (this.value == '') { this.value = '<?php echo $descuento6 ?>'; } recalcular(); }" size="14" style="text-align:center;<?php if ($tipo_producto == "0" || (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"])) { ?> color:#FFFFFF;<?php } else if ($tipo_producto == "1") { ?> color:#000000;<?php } ?>" readonly></td>
			<td colspan="2"><input type="text" id="descuento6_valor" name="descuento6_valor" value="<?php echo number_format($descuento6_valor, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if ($tipo_producto == "0" || (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"])) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr>
			<td colspan="2">TRANSFERENCIA</td>
			<td>&nbsp;</td>
			<td colspan="2"><input type="text" name="descuento_transferencia_valor" value="<?php echo number_format($descuento_transferencia, 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) { ?> color:#FFFFFF;<?php } ?>" readonly></td>
		</tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<td colspan="2"><?php if ($fecha_estudio < "2018-01-01") { ?>RECUPERATE<?php } else { ?>COMISI&Oacute;N POR VENTA<?php } ?></td>
			<td colspan="3">
				<input type="checkbox" name="tipo_producto_checked" value="1" onChange="if (this.checked == true) { document.formato.descuento1.value = document.formato.descuento_producto1.value; document.formato.tipo_producto.value = '1'; document.getElementById('descuento5').style.color = '#000000'; document.getElementById('descuento6').style.color = '#000000'; document.getElementById('descuento5_valor').style.color = '#000000'; document.getElementById('descuento6_valor').style.color = '#000000'; document.getElementById('descuento5').style.backgroundColor = '#EAF1DD'; document.getElementById('descuento5').readOnly = false; } else { document.formato.descuento1.value = document.formato.descuento_producto0.value; document.formato.tipo_producto.value = '0'; document.getElementById('descuento5').style.color = '#FFFFFF'; document.getElementById('descuento6').style.color = '#FFFFFF'; document.getElementById('descuento5_valor').style.color = '#FFFFFF'; document.getElementById('descuento6_valor').style.color = '#FFFFFF'; document.getElementById('descuento5').style.backgroundColor = '#FFFFFF'; document.getElementById('descuento5').readOnly = true; } recalcular();"<?php echo $tipo_producto_checked ?>>
				<input type="hidden" name="tipo_producto" value="<?php echo $tipo_producto ?>">
			</td>
		</tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<td colspan="2">COMISION A DESCONTAR</td>
			<td>&nbsp;</td>
			<td colspan="2"><input type="text" id="valor_comision_descontar" name="valor_comision_descontar" value="<?php echo number_format($fila["valor_comision_descontar"], 0, ".", ",") ?>" style="width:95%; text-align:right;<?php if (!$_REQUEST["cedula"] && !$_REQUEST["id_simulacion"]) 
			{ ?> background-color:#FFFFFF;<?php 
			}else if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES"){  
				?> background-color:#EAF1DD;<?php 
			}else{  
				?> background-color:#FFFFFF;<?php 
			}?>" 
			<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")
			{  

			}
			else
			{
			?> readonly
			<?php
			} ?>
			></td>
		</tr>
		</table>
		</div>
	</td>
	<td width="20">&nbsp;</td>
	<td valign="top">
		<br>
		<div class="box1 oran clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
<?php

	if ($_SESSION["FUNC_MUESTRACAMPOS1"])
	{

?>
		<tr>
			<td colspan="2">VALOR CR&Eacute;DITO</td>
			<td colspan="3"><input type="text" name="valor_credito" value="<?php echo number_format($valor_credito, 0, ".", ",") ?>" style="width:100%; text-align:right;" readonly></td>
		</tr>
<?php

	}
	
?>
		<tr>
			<td colspan="2" style="font-size:18" width="170"><b>DESEMBOLSO MENOS RETANQUEOS</b></td>
			<td colspan="3"><input type="text" name="sin_retanqueos" value="<?php echo $sin_retanqueos ?>" style="width:100%; text-align:right;" readonly></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:18" width="170"><b>DESEMBOLSO CLIENTE</b></td>
			<td colspan="3"><input type="text" id="desembolso_cliente" name="desembolso_cliente" value="<?php echo number_format($desembolso_cliente, 0, ".", ",") ?>" style="width:100%; height:45; text-align:right; font-size:18; font-weight:bold;<?php if (number_format($desembolso_cliente, 0) <= 0) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly></td>
		</tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<td colspan="2" style="font-size:16"><b>DECISI&Oacute;N</b></td>
			<td colspan="3">
<?php

//	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
//	{
	
?>
				<select id="decision" name="decision" style="width:100%; font-size:14px; font-weight:bold; background-color:#8DB4E3;" onChange="recalcular()">
					<option value="<?php echo $label_viable ?>"<?php echo $decision_viable ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $label_viable ?></option>
					<option value="<?php echo $label_negado ?>"<?php echo $decision_negado ?> style="color:#CC0000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $label_negado ?></option>
				</select>
<?php

//	}
//	else
//	{
	
?>
				<!--<input type="text" id="decision" name="decision" value="<?php echo $decision ?>" style="width:100%; height:45; text-align:center; font-size:18; font-weight:bold;<?php if ($decision == $label_negado) { ?> color:#CC0000;<?php } ?> background-color:#8DB4E3" readonly>-->
<?php

//	}
	
?>
				<input type="hidden" id="decisionh" name="decisionh" value="<?php echo $decision ?>">
				<?php if ($_SESSION["S_TIPO"] != "ADMINISTRADOR") { ?><input type="hidden" name="decision_sistema" value="<?php echo $decision_sistema ?>"><?php } ?>
			</td>
		</tr>
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR")
	{
	
?>
		<tr>
			<td colspan="2" style="font-size:16"><b>DECISI&Oacute;N SISTEMA</b></td>
			<td colspan="3">
				<input type="text" name="decision_sistema" value="<?php echo $decision_sistema ?>" style="width:100%;" readonly>
			</td>
		</tr>
<?php

	}
	
?>
		<tr>
			<td colspan="2">CAUSAL <?php if ($estado == "DST") { echo "DESISTIMIENTO"; } else { echo "NEGACI&Oacute;N"; } ?></td>
			<td colspan="3">
				<select name="id_causal" style="width:100%; background-color:#EAF1DD"<?php if ($estado != "DST" && $decision == $label_viable) { echo " disabled"; } ?>>
					<option value=""></option>
<?php

		$queryDB = "select id_causal, nombre from causales where (estado = '1'";
		
		if ($estado == "DST")
			$queryDB .= " AND tipo_causal = 'DESISTIMIENTO'";
		else
			$queryDB .= " AND tipo_causal = 'NEGACION'";
		
		$queryDB .= ") OR id_causal = '".$id_causal."' order by nombre";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["id_causal"] == $id_causal)
				$selected_causal = " selected";
			else
				$selected_causal = "";
			
			echo "<option value=\"".$fila1["id_causal"]."\"".$selected_causal.">".($fila1["nombre"])."</option>\n";
		}
		
?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">NO. LIBRANZA</td>
			<td colspan="3"><input type="text" name="nro_libranza" value="<?php echo $nro_libranza ?>" style="width:100%; text-align:center;" readonly></td>
		</tr>
		<tr>
			<td colspan="2">CUOTA INCORPORADA</td>
			<td colspan="3"><input type="text" name="valor_visado" value="<?php echo number_format($valor_visado, 0, ".", ",") ?>" style="width:100%; text-align:right;<?php if ($bloqueo_cuota && $id_subestado == $subestado_desembolso_pdte_bloqueo && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")) { ?> background-color:#EAF1DD" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { separador_miles(this); }"<?php } else { ?>" readonly<?php } ?>></td>
		</tr>
		<tr>
			<td colspan="2">BLOQUEO CUOTA</td>
			<td colspan="3">
				<input type="checkbox" name="bloqueo_cuota_checked" value="1" onChange="if (this.checked == true) { document.formato.bloqueo_cuota.value = '1'; } else { document.formato.bloqueo_cuota.value = '0'; } recalcular()"<?php if (!($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")) { ?> disabled<?php } ?><?php echo $bloqueo_cuota_checked ?>>
				<input type="hidden" name="bloqueo_cuota" value="<?php echo $bloqueo_cuota ?>">&nbsp;&nbsp;&nbsp;<input type="text" name="bloqueo_cuota_valor" value="<?php echo number_format($bloqueo_cuota_valor, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
		</tr>
		<tr>
			<td colspan="2">FECHA CONFIRMACI&Oacute;N</td>
			<td colspan="3"><input type="text" name="fecha_llamada_clientef" value="<?php echo $fecha_llamada_clientef ?>" size="10"<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA") { ?> onChange="if(validarfecha(this.value)==false) {this.value='<?php echo $fecha_llamada_clientef ?>'; return false}" style="background-color:#EAF1DD;"<?php } else { ?> readonly<?php } ?>>&nbsp;&nbsp;&nbsp;HORA&nbsp;<input type="text" name="fecha_llamada_clienteh" value="<?php echo $fecha_llamada_clienteh ?>" size="5"<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA") { ?> onChange="if(validarhora(this.value)==false) {this.value='<?php echo $fecha_llamada_clienteh ?>'; return false}" style="background-color:#EAF1DD;"<?php } else { ?> readonly<?php } ?>><select name="fecha_llamada_clientej"<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA") { ?> style="background-color:#EAF1DD;"<?php } else { ?> readonly<?php } ?>><option value=""></option><option value="AM"<?php echo $fecha_llamada_clientej_am ?>>AM</option><option value="PM"<?php echo $fecha_llamada_clientej_pm ?>>PM</option></select></td>
		</tr>
		<tr>
			<td colspan="2">CUENTA BANCARIA</td>
			<td colspan="3">
			<select id="id_banco" name="id_banco" style="width:100%; background-color:#EAF1DD">
				<option value="">BANCO</option>
				<?php
					$queryDB = "select id_banco, codigo, nombre from bancos where estado=1 order by nombre";
					$rs1 = sqlsrv_query($link, $queryDB);
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						if ($fila1["id_banco"] == $id_banco)
							$selected_banco = " selected";
						else
							$selected_banco = "";
		
						echo "<option value=\"".$fila1["id_banco"]."\"".$selected_banco.">".($fila1["nombre"])."</option>\n";
					}
	
				?>

			</select>
			<select id="tipo_cuenta" name="tipo_cuenta" style="background-color:#EAF1DD;">
				<option value="">TIPO</option>
				<option value="<?php echo $label_aho ?>"<?php echo $tipo_cuenta_aho ?>><?php echo $label_aho ?></option>
				<option value="<?php echo $label_cte ?>"<?php echo $tipo_cuenta_cte ?>><?php echo $label_cte ?></option>
			</select>&nbsp;
			<input type="hidden" name="nro_cuenta"id="nro_cuenta" value="<?php echo $nro_cuenta ?>" maxlength="20" size="20">
            <input onclick="validarTipoCuentaVacio();" onblur="validarNumCuenta()"  id="nro_cuenta_vista" maxlength="19" onkeyup="formatoCuenta(event, this, document.getElementById('nro_cuenta'));" onchange="formatoCuenta(event, this, document.getElementById('nro_cuenta'));" type="text" style="background-color:#EAF1DD" value="<?php echo $nro_cuenta ?>" >
				
			</td>
		</tr>
<?php

	if ($_SESSION["FUNC_SUBESTADOS"])
	{

?>
		<tr>
			<td colspan="2">SUBESTADO</td>
			<td colspan="3">
				<select id="id_subestado" name="id_subestado" style="width:100%; background-color:#EAF1DD">
					<option value=""></option>
<?php

		$queryDB = "SELECT DISTINCT se.id_subestado, se.nombre from subestados se INNER JOIN subestados_usuarios su ON se.id_subestado = su.id_subestado where (se.estado = '1' AND se.decision = '".$decision."'";
		if ($_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
		{
			$queryDB .= " AND su.id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		else if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")
		{
			$queryDB .= " AND su.id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
			
			if ($id_subestado)
				$id_subestado_origen = $id_subestado;
			else
				$id_subestado_origen = "0";
			
			$queryDB .= " AND (se.id_subestado IN (select id_subestado_destino from subestados_orden where id_subestado_origen = '".$id_subestado_origen."') OR se.cod_interno = '999'";
			
			if (($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "NEXA") && ($cod_interno_subestado < 55))
				$queryDB .= " OR se.id_subestado IN (select id_subestado_origen from subestados_orden where id_subestado_destino = '".$id_subestado_origen."')";
			
			$queryDB .= ")";
		}
		
		$queryDB .= ")";
		
		if ($id_subestado)
			$queryDB .= " OR (se.decision = '".$decision."' AND se.id_subestado = '".$id_subestado."')";
		
		$queryDB .= " order by se.nombre";
		
		//echo $queryDB;
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["id_subestado"] == $id_subestado)
				$selected_subestado = " selected";
			else
				$selected_subestado = "";						

				if(!($fila1["id_subestado"] == 56 && ($cod_interno_subestado < 35 && ($_SESSION["S_REVISION_GARANTIAS"] != '1' && $_SESSION["S_SUBTIPO"] != 'ANALISTA_CREDITO' && $_SESSION["S_TIPO"] != 'ADMINISTRADOR' && $_SESSION["S_TIPO"] != 'OPERACIONES')))){
                    echo "<option value=\"".$fila1["id_subestado"]."\"".$selected_subestado.">".($fila1["nombre"])."</option>\n";
                }
		}
		
?>
				</select>
				<input type="hidden" name="id_subestadoh" id="id_subestadoh" value="<?php echo $id_subestado ?>">
			</td>
		</tr>
<?php

	}

?>
		<tr>
			<td colspan="2">CARACTER&Iacute;STICA</td>
			<td colspan="3">
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
	{
	
?>
				<select name="id_caracteristica" style="width:100%; background-color:#EAF1DD">
					<option value=""></option>
<?php

		$queryDB = "select id_caracteristica, nombre from caracteristicas where estado = '1' OR id_caracteristica = '".$id_caracteristica."' order by nombre";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($fila1["id_caracteristica"] == $id_caracteristica)
				$selected_caracteristica = " selected";
			else
				$selected_caracteristica = "";
			
			echo "<option value=\"".$fila1["id_caracteristica"]."\"".$selected_caracteristica.">".($fila1["nombre"])."</option>\n";
		}
		
?>
				</select>
<?php

	}
	else
	{
	
?>
				<input type="text" name="nombre_caracteristica" value="<?php echo ($nombre_caracteristica) ?>" style="width:100%;" readonly>
				<input type="hidden" name="id_caracteristica" value="<?php echo $id_caracteristica ?>">
<?php

	}
	
?>
			</td>
		</tr>
<?php

	if ($_SESSION["FUNC_CALIFICACION"])
	{

?>
		<tr>
			<td colspan="2">CALIFICACI&Oacute;N</td>
			<td colspan="3">
				<select name="calificacion" style="background-color:#EAF1DD; color:#F58F1F;">
					<option value=""></option>
					<option value="5"<?php echo $calificacion_5 ?>>&#9733;&#9733;&#9733;&#9733;&#9733;</option>
					<option value="4"<?php echo $calificacion_4 ?>>&#9733;&#9733;&#9733;&#9733;</option>
					<option value="3"<?php echo $calificacion_3 ?>>&#9733;&#9733;&#9733;</option>
					<option value="2"<?php echo $calificacion_2 ?>>&#9733;&#9733;</option>
					<option value="1"<?php echo $calificacion_1 ?>>&#9733;</option>
				</select>
			</td>
		</tr>
<?php

	}

?>
		<tr>
			<td colspan="2">EXTRA PRIMA</td>
			<td colspan="3">
				<select name="porcentaje_extraprima" style="background-color:#EAF1DD;" onChange="recalcular()">
					<option value="0"></option>
					<option value="25"<?php if ($porcentaje_extraprima == "25") { echo " selected"; } ?>>25%</option>
					<option value="50"<?php if ($porcentaje_extraprima == "50") { echo " selected"; } ?>>50%</option>
					<option value="75"<?php if ($porcentaje_extraprima == "75") { echo " selected"; } ?>>75%</option>
					<option value="100"<?php if ($porcentaje_extraprima == "100") { echo " selected"; } ?>>100%</option>
				</select>
				<input type="hidden" name="porcentaje_extraprimah" value="<?php echo $porcentaje_extraprima ?>">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FORMULARIO SEGURO&nbsp;
				<input type="checkbox" name="formulario_seguro_checked" value="1" onChange="if (this.checked == true) { document.formato.formulario_seguro.value = '1'; } else { document.formato.formulario_seguro.value = '0'; }"<?php echo $formulario_seguro_checked ?>>
				<input type="hidden" name="formulario_seguro" value="<?php echo $formulario_seguro ?>">
			</td>
		</tr>
<?php

	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $score_ado)
	{
	
?>
		<tr>
			<td colspan="2">VALIDACI&Oacute;N ADO</td>
			<td colspan="3"><input type="text" name="score_ado" value="<?php echo $score_ado ?>" style="width:70%;" readonly><?php if ($response_ado) { ?>&nbsp;&nbsp;&nbsp;<a href="#" onClick="window.open('ado_info.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>', 'DETALLE_VALIDACION<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=500,top=0,left=0');">Ver Detalle</a><?php } ?></td>
		</tr>
<?php

	}
	
	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO")
	{
	
?>
		<tr>
			<td colspan="2">PORCENTAJE SEGURO</td>
			<td colspan="3"><input type="text" name="porcentaje_seguro" value="<?php echo $porcentaje_seguro ?>" style="width:100%; text-align:right;" readonly></td>
		</tr>
<?php

	}
	else
	{
	
?>
		<input type="hidden" name="porcentaje_seguro" value="<?php echo $porcentaje_seguro ?>">
<?php

	}
	
?>
		</table>
		</div>
<?php

	if ($_REQUEST["id_simulacion"])
	{
	 	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
		{
		
?>
		<br>
		<h2>ANALISTAS DEL CR&Eacute;DITO</h2>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
<?php

			if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION")
			{
			
?>
<!--		<tr>
			<td width="130">GESTI&Oacute;N COMERCIAL</td>
			<td>
				<select name="id_analista_gestion_comercial" style="background-color:#EAF1DD; width:150px;">
					<option value=""></option>
<?php

				$queryDB = "SELECT id_usuario, nombre, apellido from usuarios where (subtipo = 'ANALISTA_GEST_COM' AND estado = '1' AND (sector IS NULL OR sector = '".$sector."')) OR id_usuario = '".$fila["id_analista_gestion_comercial"]."' order by nombre, apellido, id_usuario";
				
				$rs1 = sqlsrv_query($link, $queryDB);
				
				while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
				{
					if ($fila1["id_usuario"] == $fila["id_analista_gestion_comercial"])
						$selected_analista_gestion_comercial = " selected";
					else
						$selected_analista_gestion_comercial = "";
					
					echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_analista_gestion_comercial.">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
				}
				
?>
				</select>
			</td>
		</tr>-->
<?php

			}
			else
			{
		
?>
<!--
		<input type="hidden" name="id_analista_gestion_comercial" value="<?php echo $fila["id_analista_gestion_comercial"] ?>">
-->
<?php

			}
			
			if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
			{
			
?>
		<tr>
			<td>RIESGO OPERATIVO</td>
			
			<td align="center" style="width:100%;">
			
			<select name="id_analista_riesgo_operativo" style="background-color:#EAF1DD; width:150px;">

					
			<?php
					
					$consultarUsuariosInicio="(SELECT a.* 
				FROM 
				usuarios a 
				WHERE a.id_usuario IN (SELECT eus.id_usuario
				FROM empresa_unegocio_fdc eun 
				INNER JOIN empresa_usuario_fdc eus ON eun.id_empresa=eus.id_empresa 
				INNER JOIN simulaciones si ON si.id_unidad_negocio=eun.id_unidad_negocio
				WHERE si.id_simulacion='".$fila["id_simulacion"]."') AND (a.subtipo='ANALISTA_CREDITO' OR a.subtipo='NEXA') and a.disponible in ('s','g') and a.estado=1)";
	
					
					if ($fila["id_analista_riesgo_operativo"]==null || $fila["id_analista_riesgo_operativo"]=="0")
					{
						
					}else{
						$consultarUsuariosInicio.=" UNION (SELECT * FROM usuarios where id_usuario='".$fila["id_analista_riesgo_operativo"]."')";
					}
						
						
						$rs1 = sqlsrv_query($link, $consultarUsuariosInicio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						if (sqlsrv_num_rows($rs1)<=0)
						{
							?>
							<option value="">SIN ANALISTAS DISPONIBLES</option>
								<?php
							}else{
								?>
								<option value=""></option>
								<?php
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
								{
								
									if ($fila1["id_usuario"] == $fila["id_analista_riesgo_operativo"])
									{
										$selected_ciudad = " selected";
									}
									else
									{
										$selected_ciudad = "";
									}
									echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_ciudad.">".$fila1["nombre"]." ".$fila1["apellido"]."</option>\n";
								}
							}
?>
					</select>
					</td>
<!--
			<td>RIESGO CREDITICIO</td>
			<td>
				<select name="id_analista_riesgo_crediticio" style="background-color:#EAF1DD; width:150px;">
					<option value=""></option>
<?php

				//$queryDB = "select id_usuario, nombre, apellido from usuarios where (subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$sector."')) OR id_usuario = '".$fila["id_analista_riesgo_crediticio"]."' order by nombre, apellido, id_usuario";
				
				//$rs1 = sqlsrv_query($queryDB, $link);
				
				//while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
				//{
				//	if ($fila1["id_usuario"] == $fila["id_analista_riesgo_crediticio"])
				//		$selected_analista_riesgo_crediticio = " selected";
				//	else
				//		$selected_analista_riesgo_crediticio = "";
					
				//	echo "<option value=\"".$fila1["id_usuario"]."\"".$selected_analista_riesgo_crediticio.">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
				//}
				
?>
				</select>-->
			</td>
		</tr>
<?php

			}
			else
			{
		
?>
		<input type="hidden" name="id_analista_riesgo_operativo" value="<?php echo $fila["id_analista_riesgo_operativo"] ?>">
<!--		<input type="hidden" name="id_analista_riesgo_crediticio" value="<?php echo $fila["id_analista_riesgo_crediticio"] ?>">-->
<?php

			}
			
?>
		</table>
		</div>
<?php

		}
		else
		{
		
?>
	<!--	<input type="hidden" name="id_analista_gestion_comercial" value="<?php echo $fila["id_analista_gestion_comercial"] ?>">-->
		<input type="hidden" name="id_analista_riesgo_operativo" value="<?php echo $fila["id_analista_riesgo_operativo"] ?>">
	<!--	<input type="hidden" name="id_analista_riesgo_crediticio" value="<?php echo $fila["id_analista_riesgo_crediticio"] ?>">-->
<?php

		}
	}
	
?>
		<br>
		<h2>OBSERVACIONES</h2>
		<div class="box1 oran clearfix">
<?php

	if (!$_REQUEST["id_simulacion"])
	{
	
?>
		<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td colspan="4"><textarea name="observaciones" rows="3" style="width:100%; background-color:#EAF1DD;"><?php echo $observaciones ?></textarea></td>
		</tr>
		</table>
<?php

	}
	else
	{
	
?>
		<input type="hidden" name="observaciones" value="">
		<iframe id="frm_observaciones" src="simulaciones_observaciones.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&tipo=<?php echo $_REQUEST["tipo"] ?>" width="100%" height="300px"></iframe>
<?php

	}
	
?>
		</div>
	</td>
</tr>
<?php

	if ($_SESSION["FUNC_AGENDA"])
	{

?>
			<input type="hidden" name="dia_confirmacion" value="<?php echo $dia_confirmacion ?>">
			<input type="hidden" name="dia_vencimiento" value="<?php echo $dia_vencimiento ?>">
			<input type="hidden" name="status" value="<?php echo $status ?>">
<?php

	}

}

if (!$estado || (($estado == "ING" || $estado == "EST") && ($_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "NEXA" || ($_SESSION["S_TIPO"] == "OFICINA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"  && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))))
{
	$muestra_submit = 1;

	//echo "submit: 36 =>".$muestra_submit;
	if ($_SESSION["S_TIPO"] == "PROSPECCION" && $id_subestado == 70) {
		$muestra_submit = 0;
		//echo "submit: 37 =>".$muestra_submit;
	}
	
	if ($_SESSION["S_SOLOLECTURA"] == "1") {
		$muestra_submit = 0;
		//echo "submit: 37 =>".$muestra_submit;
	}

	if ($estado == "ING" && strpos($oficinas_prospeccion, "'".$fila["id_oficina"]."'") !== false && $_REQUEST["back"] != "prospecciones") {
		$muestra_submit = 0;
	//echo "submit: 38".$muestra_submit;
	}
	
	if ($estado == "ING" && $_REQUEST["back"] != "pilotofdc") {
		$muestra_submit = 0;
		//echo "submit: 39".$muestra_submit;
	}

	if ($tipo_comercial == "PLANTA") {	
		if ($_SESSION["S_TIPO"] == "COMERCIAL" && $estado == "EST" && $cod_interno_subestado > 10 && $cod_interno_subestado < 999 && $cod_interno_subestado != 15 && $cod_interno_subestado != 25 && $cod_interno_subestado != 37 && $cod_interno_subestado != 36)
		{
			//cod interno=36, 37
			$muestra_submit = 0;
			//echo "submit: 40".$muestra_submit;
		}
	} else {
		if ($_SESSION["S_TIPO"] == "COMERCIAL" && $estado == "EST" && ($cod_interno_subestado != 37 && $cod_interno_subestado != 36)) {
			$muestra_submit = 0;
			//echo "submit: 41"."--".$cod_interno_subestado."--".$estado."--".$_SESSION["S_TIPO"];
		}
	}
	
	if ($_SESSION["S_TIPO"] == "DIRECTOROFICINA" && $estado == "EST" && $cod_interno_subestado >= 30 && $cod_interno_subestado < 999)
	{
		$muestra_submit = 0;
		//echo "submit: 42".$muestra_submit;
	}
	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" && $estado == "EST" && $cod_interno_subestado >= 30)
	{
		$muestra_submit = 0;
		//echo "submit: 43";
	}
	if ($_SESSION["S_TIPO"] == "PROSPECCION" && ($cod_interno_subestado >= 30 && $cod_interno_subestado < 999) && $cod_interno_subestado != 36 && $cod_interno_subestado != 37)
	{
		$muestra_submit = 0;
		//echo "submit: 44";
	}
	if (($_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION") && $cod_interno_subestado >= 35 && $cod_interno_subestado < 999)
	{
		$muestra_submit = 0;
		//echo "submit: 45";
	}
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" && ($cod_interno_subestado < 20 || ($cod_interno_subestado >= 30 && $cod_interno_subestado < 999)))
	{
		$muestra_submit = 0;
		//echo "submit: 46";
	}
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" && !($cod_interno_subestado == 35 || $cod_interno_subestado == 40 || $cod_interno_subestado == 48))
	{
		$muestra_submit = 0;
		//echo "submit: 47";
	}
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" && ($cod_interno_subestado < 47 || $cod_interno_subestado >= 48))
	{
		$muestra_submit = 0;
		//echo "submit: 48";
	}
	if (($_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO") && $estado && ($cod_interno_subestado < 45 || $cod_interno_subestado >= 48) && $id_subestado != $subestado_desembolso_pdte_bloqueo)
	{
		$muestra_submit = 0;
		//echo "submit: 49";
	}
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" && $estado && ($cod_interno_subestado < 50 || $cod_interno_subestado >= 55))
	{
		$muestra_submit = 0;
		//echo "submit: 50";
	}

	if ($_SESSION["S_TIPO"] == "DIRECTOROFICINA" && ($id_subestado == 76 || $id_subestado == 75 || $id_subestado == 85)) {
		$muestra_submit = 1;
	}

	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION" && ($id_subestado == 85)) {
		$muestra_submit = 1;
	}
}

if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "NEXA" || $_SESSION["S_TIPO"] == "OUTSOURCING") && $decision == $label_negado && $id_subestado == $subestado_devuelto_por_inconsistencias) {
	$muestra_reprospectar = 1;
	
	if ($_SESSION["S_SOLOLECTURA"] == "1")
		$muestra_reprospectar = 0;
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" && $decision == $label_viable && $id_subestado == $subestado_procesado) {
	$muestra_segunda_revision = 1;
	
	if ($_SESSION["S_SOLOLECTURA"] == "1")
		$muestra_segunda_revision = 0;
}

if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_TIPO"] == "OPERACIONES") && ($estado == "ING" || $estado == "EST") && $decision == $label_viable)
//if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_TIPO"] == "OPERACIONES") && ($estado == "ING" || $estado == "EST") && $decision == $label_viable)
{
	$muestra_desistir = 1;
	
	if ($_SESSION["S_SOLOLECTURA"] == "1")
		$muestra_desistir = 0;
	
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" && ($cod_interno_subestado < 20 || $cod_interno_subestado >= 30))
		$muestra_desistir = 0;
	
	if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "PROSPECCION") /*&& $cod_interno_subestado >= 42*/)
		$muestra_desistir = 0;
	
	if (($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && $cod_interno_subestado >= 42)
		 $muestra_desistir = 0;
	
	if (($_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && $cod_interno_subestado >= 55)
		$muestra_desistir = 0;
	
	if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") && ($id_subestado == $subestado_desembolso || $id_subestado == $subestado_desembolso_pdte_bloqueo))
		$muestra_desistir = 0;
}

?>
<tr><td colspan="3"><br></td></tr>
<tr><td colspan="3" align="center">
	
	<input type="hidden" name="estado" value="<?php echo $estado ?>">
	<input type="hidden" name="id_oficina" value="<?php echo $id_oficina ?>">
	<input type="hidden" name="solicitud" value="<?php echo $solicitud ?>">
	<input type="hidden" name="ciudad" value="<?php echo $ciudad ?>">
	<input type="hidden" name="nivel_educativo" value="<?php echo $nivel_educativo ?>">
	<input type="hidden" name="nro_compra_cartera_seguro" value="<?php echo $nro_compra_cartera_seguro ?>">
	<input type="hidden" name="ultimo_consecutivo_compra_cartera" value="<?php echo $ultimo_consecutivo_compra_cartera ?>">
	<input type="hidden" name="descuento_producto0" value="<?php echo $descuento_producto0 ?>">
	<input type="hidden" name="descuento_producto1" value="<?php echo $descuento_producto1 ?>">
	<input type="hidden" name="descuento_transferencia" value="<?php echo $descuento_transferencia ?>">
	<input type="hidden" name="valor_por_millon_seguro" value="<?php echo $valor_por_millon_seguro ?>">
	<input type="hidden" name="valor_por_millon_seguroh" value="<?php echo $valor_por_millon_seguro ?>">
	<input type="hidden" name="valor_credito_anterior" value="">
	<input type="hidden" name="desembolso_cliente_anterior" value="">
	<input type="hidden" name="resumen_ingreso" value="<?php echo number_format($resumen_ingreso, 0, ".", ",") ?>">
	<input type="hidden" name="incor" value="<?php echo number_format($incor, 0, ".", ",") ?>">
	<input type="hidden" name="comision" value="<?php echo number_format($comision, 0, ".", ",") ?>">
	<input type="hidden" name="utilidad_neta" value="<?php echo number_format($utilidad_neta, 0, ".", ",") ?>">
	<input type="hidden" name="sobre_el_credito" value="<?php echo number_format($sobre_el_credito, 2, ".", ",") ?>">
	<input type="hidden" name="iva" value="<?php echo $iva ?>">
	<input type="hidden" name="tasa_usura" value="<?php echo $tasa_usura ?>">
	<input type="hidden" name="fecha_prospeccion" value="<?php echo $fila["fecha_prospeccion"] ?>">
	<input type="hidden" name="tipo_comercial" value="<?php echo $tipo_comercial ?>">
	<input type="hidden" name="sector" value="<?php echo $sector ?>">
	<input type="hidden" name="fidelizacion" value="<?php echo $fidelizacion ?>">
	<input type="hidden" name="retanqueo1_valor_liquidacion" id="retanqueo1_valor_liquidacion" value="<?php echo $retanqueo1_valor_liquidacion ?>">
	<input type="hidden" name="retanqueo1_intereses" id="retanqueo1_intereses" value="<?php echo $retanqueo1_intereses ?>">
	<input type="hidden" name="retanqueo1_seguro" id="retanqueo1_seguro" value="<?php echo $retanqueo1_seguro ?>">
	<input type="hidden" name="retanqueo1_cuotasmora" id="retanqueo1_cuotasmora" value="<?php echo $retanqueo1_cuotasmora ?>">
	<input type="hidden" name="retanqueo1_segurocausado" id="retanqueo1_segurocausado" value="<?php echo $retanqueo1_segurocausado ?>">
	<input type="hidden" name="retanqueo1_gastoscobranza" id="retanqueo1_gastoscobranza" value="<?php echo $retanqueo1_gastoscobranza ?>">
	<input type="hidden" name="retanqueo1_totalpagar" id="retanqueo1_totalpagar" value="<?php echo $retanqueo1_totalpagar ?>">
	<input type="hidden" name="retanqueo2_valor_liquidacion" id="retanqueo2_valor_liquidacion" value="<?php echo $retanqueo2_valor_liquidacion ?>">
	<input type="hidden" name="retanqueo2_intereses" id="retanqueo2_intereses" value="<?php echo $retanqueo2_intereses ?>">
	<input type="hidden" name="retanqueo2_seguro" id="retanqueo2_seguro" value="<?php echo $retanqueo2_seguro ?>">
	<input type="hidden" name="retanqueo2_cuotasmora" id="retanqueo2_cuotasmora" value="<?php echo $retanqueo2_cuotasmora ?>">
	<input type="hidden" name="retanqueo2_segurocausado" id="retanqueo2_segurocausado" value="<?php echo $retanqueo2_segurocausado ?>">
	<input type="hidden" name="retanqueo2_gastoscobranza" id="retanqueo2_gastoscobranza" value="<?php echo $retanqueo2_gastoscobranza ?>">
	<input type="hidden" name="retanqueo2_totalpagar" id="retanqueo2_totalpagar" value="<?php echo $retanqueo2_totalpagar ?>">
	<input type="hidden" name="retanqueo3_valor_liquidacion" id="retanqueo3_valor_liquidacion" value="<?php echo $retanqueo3_valor_liquidacion ?>">
	<input type="hidden" name="retanqueo3_intereses" id="retanqueo3_intereses" value="<?php echo $retanqueo3_intereses ?>">
	<input type="hidden" name="retanqueo3_seguro" id="retanqueo3_seguro" value="<?php echo $retanqueo3_seguro ?>">
	<input type="hidden" name="retanqueo3_cuotasmora" id="retanqueo3_cuotasmora" value="<?php echo $retanqueo3_cuotasmora ?>">
	<input type="hidden" name="retanqueo3_segurocausado" id="retanqueo3_segurocausado" value="<?php echo $retanqueo3_segurocausado ?>">
	<input type="hidden" name="retanqueo3_gastoscobranza" id="retanqueo3_gastoscobranza" value="<?php echo $retanqueo3_gastoscobranza ?>">
	<input type="hidden" name="retanqueo3_totalpagar" id="retanqueo3_totalpagar" value="<?php echo $retanqueo3_totalpagar ?>">
	<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
	<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
	<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
	<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
	<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
	<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
	<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
	<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
	<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
	<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
	<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
	<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
	<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
	<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
	<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
	<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
	<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
	<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
	<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
	<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
	<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
	<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
	<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
	<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
	<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
	<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
	<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
	<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
	<input type="hidden" name="back" value="<?php echo $_REQUEST["back"] ?>">
	<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
	<input type="hidden" name="id_analista_riesgo_operativoh" value="<?php echo $id_analista_riesgo_operativoh; ?>">

	<input type="hidden" id="id_usuario" value="<?=$_SESSION["S_IDUSUARIO"];?>">
	<input type="hidden" id="id_simulacion" value="<?=$_REQUEST["id_simulacion"];?>">
	<input type="hidden" id="s_tipo" value="<?=$_SESSION["S_TIPO"];?>">
	<input type="hidden" id="s_subtipo" value="<?=$_SESSION["S_SUBTIPO"];?>">

	<?php 
	
		
	if ($muestra_submit) { 
		?>&nbsp;&nbsp;<input type="button" id="guardar" name="guardar" value="Guardar" onClick="chequeo_forma()">&nbsp;&nbsp;<?php } ?>
	<?php if ($muestra_reprospectar) { ?>&nbsp;&nbsp;<input type="button" name="reprospectar" value="Reprospectar" onClick="window.open('simulacion_reprospectar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'REPROSPECTAR<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=370,top=0,left=0');">&nbsp;&nbsp;<?php } ?>
	<?php if ($muestra_segunda_revision) { ?>&nbsp;&nbsp;<input type="button" name="segunda_revision" value="Segunda Revisi&oacute;n" onClick="window.open('simulacion_segunda_revision.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'SEGUNDAREVISION<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=420,top=0,left=0');">&nbsp;&nbsp;<?php } ?>
	<?php if ($muestra_desistir) { ?>&nbsp;&nbsp;<input type="button" idjj="<?=$id_subestado?>" name="desistir" value="Desistir" onClick="window.open('simulacion_desistir.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&back=<?php echo $_REQUEST["back"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'DESISTIR<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">&nbsp;&nbsp;<?php } ?>
	</td></tr>
</table>
<!-- SweetAlert2 -->

<div class="modal" id="requerimientoNuevo" data-animation="slideInOutLeft">
	<div class="modal-dialog">
		<header class="modal-header">
			Nuevo Requerimiento
			<button type="button" class="close-modal" data-close>x</button>
		</header>

		<section class="modal-content">
			<div class="box1 oran clearfix">
				<h2><b>INFORMACION GENERAL2</b></h2>
				<br>
				<table border="0" cellspacing=1 cellpadding=2>
					<tr>
						<td align="right">* Req/Excep</td>
						<td>
							<select id="reqexcepReq" name="reqexcepReq" style="background-color:#EAF1DD; width:200px">
								<option selected value="REQUERIMIENTO">REQUERIMIENTO</option>
							</select>
						</td>
						<td width="20">&nbsp;</td>
						<td align="right">* Tipo</td>
						<td>
							<select id="idTipoReq" name="idTipoReq" style="background-color:#EAF1DD; width:200px;">
								<option value=""></option>
								<?php
								$queryDB = "SELECT id_tipo, reqexcep, nombre from tipos_reqexcep where id_tipo = 5 AND estado = '1' order by nombre";
								$rs1 = sqlsrv_query($link, $queryDB);
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
									echo "<option selected value=\"".$fila1["id_tipo"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">* &Aacute;rea</td>
						<td>
							<select id="idAreaReq" name="idAreaReq" style="background-color:#EAF1DD; width:200px;">
								<option value=""></option>
								<?php
								$queryDB = "SELECT id_area, nombre FROM areas_reqexcep WHERE estado = '1' AND id_area = 1 ORDER BY nombre";
								$rs1 = sqlsrv_query($link, $queryDB);
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
									echo "<option selected value=\"".$fila1["id_area"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
								} ?>
							</select>
						</td>
						<td width="20">&nbsp;</td>
						<td align="right">F Vencimiento</td>
						<td><input type="text" id="fechaVencimientoReq" name="fechaVencimientoReq" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" style="text-align:center; background-color:#EAF1DD;"></td>
					</tr>
					<tr>
						<td align="right" style="vertical-align: top">* Descripci&oacute;n</td>
						<td colspan="4"><textarea id="observacionReq" name="observacionReq" rows="2" cols="70" style="background-color:#EAF1DD;"></textarea></td>
					</tr>									
					<tr>
						<td align="right">Adjunto</td>
						<td><input type="text" id="descripcionReq" name="descripcionReq" maxlength="255" size="26" style="background-color:#EAF1DD;" placeholder="Descripci&oacute;n"></td>
						<td width="20">&nbsp;</td>
						<td colspan="2"><input type="file" id="archivoReq" name="archivo"/></td>
					</tr>
				</table>

				<input type="button" name="guardar" id="btnGuardarReqInstruccionGiro" value="Guardar">
			</div>
		</section>
	</div>
</div>

<script src="../js/funciones_generales.js"></script>
<script language="JavaScript" src="../js/simulador.js"></script>

<script src="../js/consultas_centrales.js?<?php echo rand(); ?>" type="text/javascript"></script>

<!-- Toastr -->
<script src="../plugins/toastr/toastr.min.js"></script>
<!-- SweetAlert2 -->
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Modal -->
<script src="../plugins/modal/modal.js"></script>
<script type="text/javascript">
$(document).ready(function () {  
		var estado_credito = '<?=$estado?>';

	    $("input, select").change(function () {  
	    	if($(this).attr("id") != 'id_subestado'){
	    		if(($("#s_tipo").val() != 'OPERACIONES' && $("#s_tipo").val() != 'ADMINISTRADOR') && !($("#s_tipo").val() == 'OFICINA' && ($("#s_subtipo").val() == 'ANALISTA_CREDITO' || $("#s_subtipo").val() == 'COORD_CREDITO' || $("#s_subtipo").val() == 'ANALISTA_VEN_CARTERA'))){
		    		if(estado_credito != 'ING'){
		        		$("#guardar").css("display", 'none');
		        	}
		        }
		    }
	    }); 
	}); 
	
	var cambioAsesoriaFinan = false;
	var AsesoriaFinan = 0;
	
	$("#descuento2").click(function(){
		if($("#s_subtipo").val() == 'COORD_CREDITO'){
			if(!cambioAsesoriaFinan){
				AsesoriaFinan = $("#descuento2").val();
			}
		}
	});

	$("#descuento2").change(function(){
		if($("#s_subtipo").val() == 'COORD_CREDITO'){
			if(!cambioAsesoriaFinan){
				cambioAsesoriaFinan = true;
				$("#guardar").css("display", 'none');

				Swal.fire({
	                title: 'Alerta',
	                text: 'Boton GUARDAR deshabilitado Hasta que ASESORÍA FINANCÍERA vuelva a su valor Inicial ('+AsesoriaFinan+')...'
	            });
			}else{
				if(AsesoriaFinan == $("#descuento2").val()){
					$("#guardar").css("display", 'initial');
					AsesoriaFinan = 0;
					cambioAsesoriaFinan = false;
				}
			}
		}

		if($("#s_tipo").val() != 'OPERACIONES' && $("#s_tipo").val() != 'ADMINISTRADOR'){
			if(!cambioAsesoriaFinan){
				cambioAsesoriaFinan = true;
				$("#guardar").css("display", 'none');
				Swal.fire({
					title: 'Alerta', 
					text: 'Boton GUARDAR deshabilitado Hasta que ASESORÍA FINANCÍERA vuelva a su valor Inicial ('+AsesoriaFinan+')...'
				}); 
			} else {
				if(AsesoriaFinan == $("#descuento2").val()){
					$("#guardar").css("display", 'initial');
					AsesoriaFinan = 0;
					cambioAsesoriaFinan = false;
				}
			}
		}
	});
if($("#descuento2").val() == 0){
	$("#guardar").css("display", 'none');
		Swal.fire({
		title: 'Alerta', 
		text: 'Boton GUARDAR deshabilitado Hasta que ASESORÍA FINANCÍERA sea Mayor a 0...'
	});
}else{
	if(!cambioAsesoriaFinan){
	$("#guardar").css("display", 'initial');
	}
}


	$("#id_subestado").change(function(){
		if(($("#s_tipo").val() == 'PROSPECCION' || $("#s_subtipo").val() == 'COMERCIAL') && $("#id_subestadoh").val()==70 && $("#id_subestado option:selected").val() == '56'){
			$("#guardar").css("display", 'none');
			Swal.fire({
	            title: 'Alerta',
	            text: 'Boton GUARDAR deshabilitado Debido a que su perfil no puede cambiar este estado'
	        });
		}else{	
			$("#guardar").css("display", 'initial');
		}
	});

	$('#id_subestado').change(function(){
		if($("#id_subestado option:selected").val()=='70'){
			var IdCalificacion = $('.estado_asegurabilidad').attr('idCalificacion');
			 if($("#estadoConsultar").val()!='ING' &&(IdCalificacion!=2 && IdCalificacion!=3 && IdCalificacion!=5 && IdCalificacion!=7 && IdCalificacion!=18 && IdCalificacion!=19 && IdCalificacion!=24 && IdCalificacion!=25 )){
			 		Swal.fire({
	            title: 'Alerta',
	            text: 'Boton GUARDAR deshabilitado Debido a que no se ha completado la asegurabilidad Colmena'
	        })
					$("#guardar").css("display", 'none');
			}
		}
	})

	function cambiarKPPlus() {
		if (document.formato.sin_seguro_checked .checked == true) { 
			var str_fecha_nacimiento = document.formato.fecha_nacimiento.value.split("-");
			var str_fecha_estudio = document.formato.fecha_estudio.value.split("-");

		 	var fecha_nacimiento = new Date(parseInt(str_fecha_nacimiento[0]),parseInt(str_fecha_nacimiento[1]-1),parseInt(str_fecha_nacimiento[2])).getTime();
		 	var fecha_estudio = new Date(parseInt(str_fecha_estudio[0]),parseInt(str_fecha_estudio[1]-1),parseInt(str_fecha_estudio[2])).getTime();

		 	if(((fecha_estudio - fecha_nacimiento)/(1000*60*60*24)) < 27759) {//25567
				document.formato.sin_seguro.value = '1';

				if (CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value) == '0') { 
					document.formato.sin_seguro_checked.checked = false; 
					document.formato.sin_seguro.value = '0'; 
					
					CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value); 
					alert('No hay tasas para las condiciones establecidas'); 
				} 
			}else{
		 		document.formato.sin_seguro_checked.checked = false;
				document.formato.sin_seguro.value = '0'; 
		 		alert("No se puede aplicar el seguro, Edad del Cliente supera la permitida");
			}
		} else { 
			document.formato.sin_seguro.value = '0'; 

			if (CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value) == '0') {
				document.formato.sin_seguro_checked.checked = true; 
				document.formato.sin_seguro.value = '1'; 

				CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value); 
				alert('No hay tasas para las condiciones establecidas'); 
			}
		}

		/*if (this.checked == true){
			document.formato.sin_seguro.value = '1';
		} else { 
			document.formato.sin_seguro.value = '0'; 
		}

		if (CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value) == '0') { 
			if (this.checked == true) { 
				this.checked = false; 
				document.formato.sin_seguro.value = '0'; 
			} else { 
				this.checked = true; 
				document.formato.sin_seguro.value = '1'; 
			} 

			CargarTasas(document.formato.id_unidad_negocio.value, document.formato.plazo.value, document.formato.sin_seguro.value); 
			alert('No hay tasas para las condiciones establecidas'); 
		}*/
	}
</script>
</form>
<?php include("bottom.php"); ?>
