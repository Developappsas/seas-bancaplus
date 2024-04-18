<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../functions.php'); 
include('../function_blob_storage.php'); 
include('porcentajes_seguro.php');


?>

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>

<?php


$estado = 0;
if (!$_SESSION["S_LOGIN"]) {
	exit;
}

if(!($_SESSION["S_TIPO"] == "ADMINISTRADOR" || (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" ||  $_SESSION["S_TIPO"] == "OUTSOURCING" ||  $_SESSION["S_TIPO"] == "PROSPECCION")) && $_SESSION["S_HABILITAR_PROSPECCION"] == 1)){
    exit;
}

global $urlPrincipal;

if (($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "OUTSOURCING") || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'") {
	exit;
}

$link = conectar_utf();
$mensaje = '';

$query = "SELECT nombre FROM empleados WHERE cedula = '". trim($_REQUEST['cedula']) . "' AND pagaduria = '". $_REQUEST['pagaduria'] . "'";


$resultado = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($resultado) == 0) {
	// el registro no existe. Procedemos a ingresarlo(Tabla Empleados)
	
	$query = "INSERT INTO empleados VALUES ("
	. "'" . trim($_REQUEST['cedula']) . "', "
	. "'" . strtoupper(trim($_REQUEST['apellidos']) . ' ' . trim($_REQUEST['nombres'])) . "', "
	. "'" . $_REQUEST['pagaduria'] . "', "
	. "'" . strtoupper($_REQUEST['institucion']) . "', "
	. "'', "
	. "'', "
	. "'0', "
	. "'0', "
	. "'0', "
	. "'0', "
	. "'', "
	. "'" . strtoupper($_REQUEST['direccion']) . "', "
	. "'" . $_REQUEST['telefono'] . "', "
	. "'" . $_REQUEST['email'] . "', "
	. "'" . $_REQUEST['fecha_nacimiento'] . "', "
	. "'" . strtoupper($_REQUEST['nivel_contratacion']) . "', "
	. "'', "
	. "'" . strtoupper($_REQUEST['ciudad']) . "', "
	. "'" . '1' . "', "
	. "'" . strtoupper($_REQUEST['sexo']) . "', "
	. "'" . strtoupper($_REQUEST['ciudad']) . "', "
	. "'" . $_REQUEST['fecha_inicio_labor'] . "', "
	. "'" . $_REQUEST['medio_contacto'] . "')";
	
	sqlsrv_query($link, $query);
	
	$query = "INSERT INTO empleados_creacion (cedula, pagaduria, id_usuario, fecha_creacion) VALUES
	 ('" . trim($_REQUEST['cedula']) . "',   '" . $_REQUEST['pagaduria'] . "',    '" . $_SESSION["S_IDUSUARIO"] . "', getdate() )";

sqlsrv_query($link, $query);
}

$parametros = sqlsrv_query($link, "SELECT * from parametros where tipo = 'SIMULADOR' order by id");

$j = 0;

while ($fila1 = sqlsrv_fetch_array($parametros)) {
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
$iva = $parametro[16];
$porcentaje_aportes_activos = $parametro[17];
$porcentaje_comision = $parametro[18];
$descuento1 = $parametro[19];
$descuento2 = $parametro[20];
$descuento3 = $parametro[21];
$descuento4 = $parametro[22];
$descuento5 = $parametro[23];
$descuento6 = $parametro[24];
$servicio_nube = $parametro[40];
$sin_iva_servicio_nube=$parametro[43];

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
$seguro_parcial=$parametro[42];

$descuento_producto0 = $descuento1;

$id_comercial = $_SESSION["S_IDUSUARIO"];


$es_freelance = sqlsrv_query($link, "SELECT * from usuarios where id_usuario = '" . $id_comercial . "' and (freelance = '1' OR outsourcing = '1')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($es_freelance)) {
	$descuento2 = $descuento_freelance2;
	$descuento3 = $descuento_freelance3;
}

$sin_aportes = "0";
$nro_libranza = "NULL";
$fecha_llamada_cliente = "NULL";
$nro_cuenta = "NULL";
$tipo_cuenta = "NULL";
$id_banco = "NULL";
$id_subestado = "NULL";
$id_caracteristica = "NULL";
$calificacion = "NULL";
$dia_confirmacion = "NULL";
$dia_vencimiento = "NULL";
$status = "NULL";
$bloqueo_cuota = "0";



if ( isset($_REQUEST["telemercadeo"]) && $_REQUEST["telemercadeo"] == "1") {
	$_REQUEST["telemercadeo"] = "1";
}else{
	$_REQUEST["telemercadeo"] = "0";
}

$existe_en_empleados_creacion = sqlsrv_query($link, "SELECT * from empleados_creacion where cedula = '" . trim(trim($_REQUEST["cedula"])) . "' AND pagaduria = '" . $_REQUEST["pagaduria"] . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($existe_en_empleados_creacion)) {
	$fila1 = sqlsrv_fetch_array($existe_en_empleados_creacion);
	
	if ($fila1["fecha_modificacion"]){
		$empleado_manual = 0;
	}
	else{
		$empleado_manual = 1;
	}


} else {
	$empleado_manual = 0;
}

$existe_recien_creada = sqlsrv_query($link, "SELECT id_simulacion from simulaciones where cedula = '" . trim($_REQUEST["cedula"]) . "' AND pagaduria = '" . $_REQUEST["pagaduria"] . "' AND id_comercial = '" . $id_comercial . "' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '" . $id_comercial . "') AND   DATEDIFF(SECOND,'1970-01-01', getdate() ) -  DATEDIFF(SECOND,'1970-01-01', fecha_creacion)  <= 60", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


if (sqlsrv_num_rows($existe_recien_creada)) {
	$res_existe_recien_creada = sqlsrv_fetch_array($existe_recien_creada);
	$estado = 1;
	
	echo "<script>function myFunction() { alert('Simulacion guardada exitosamente'); window.location = 'simulaciones.php?descripcion_busqueda=" . trim($_REQUEST["cedula"]) . "&buscar=1'; } setTimeout(myFunction, 1000)</script>";
	
	exit;
}

$omitir_validacion_30_dias = 1;
$omitir_validacion_credito_estudio = 1;

$existe_simulacion = sqlsrv_query($link, "SELECT id_simulacion from simulaciones where cedula = '" . trim($_REQUEST["cedula"]) . "' AND pagaduria = '" . $_REQUEST["pagaduria"] . "' AND DATEDIFF(DAY, GETDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($existe_simulacion)) {
	$existe_simulacion2 = sqlsrv_query($link, "SELECT id_simulacion from simulaciones where cedula = '" . trim($_REQUEST["cedula"]) . "' AND pagaduria = '" . $_REQUEST["pagaduria"] . "' AND DATEDIFF(DAY, GETDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST') AND id_comercial = '" . $id_comercial . "' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '" . $id_comercial . "')");
	
	if (!sqlsrv_num_rows($existe_simulacion2)){
		$omitir_validacion_30_dias = 0;
	}
	
}

//if($_REQUEST["pagaduria"] != 'COLPENSIONES' && $_REQUEST["pagaduria"] != 'FOPEP' && $_REQUEST["pagaduria"] !='COLFONDOS' && $_REQUEST["pagaduria"] != 'FIDUPREVISORA' ){
	$existe_simulacion3 = sqlsrv_query($link,"SELECT id_simulacion, id_comercial from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND estado IN ('ING', 'EST')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if (sqlsrv_num_rows($existe_simulacion3) > 0) {
		$omitir_validacion_credito_estudio = 0;
	}
	//}
	
	$oficina_ado = 0;
	$oficina_gattaca = 0;
	
	$query_oficina = sqlsrv_query($link, "SELECT top 1
iIF(b.ado IS NULL, 0, b.ado) AS ado, iIF(b.gattaca IS NULL, 0, b.gattaca) AS gattaca  
FROM oficinas_usuarios a JOIN oficinas b ON a.id_oficina = b.id_oficina 
WHERE a.id_usuario ='" . $id_comercial . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($query_oficina) > 0) {
	$datos_ofi = sqlsrv_fetch_array($query_oficina);
	$oficina_ado = $datos_ofi["ado"];
	$oficina_gattaca = $datos_ofi["gattaca"];
} 

// if ($omitir_validacion_30_dias && $omitir_validacion_credito_estudio){
if ($omitir_validacion_30_dias){
		$plazo = $plazo_maximo;
		$plazo_maximo_segun_edad = $plazo_maximo;
		
		$rs1 = sqlsrv_query($link, "select sector from pagadurias where nombre = '" . $_REQUEST["pagaduria"] . "'");

		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$sector = $fila1["sector"];
		$sufijo_sector = '';	
		if ($sector == "PRIVADO") {
			$descuento_producto0 = $aval;
			
			$descuento1 = $aval;
			
			$descuento_producto1 = $aval_producto;
			
			$sufijo_sector = "_privado";
		}
		
		$queryDB = "SELECT *, (DATEPART(YEAR, dateadd(year, ".$edad_maxima_activos.", '" . $_REQUEST["fecha_nacimiento"] . "' )) - 
		DATEPART(YEAR, GETDATE())) * 12 + (DATEPART(MONTH, dateadd(year, ".$edad_maxima_activos.", '" . $_REQUEST["fecha_nacimiento"] . "' )) - DATEPART(MONTH, GETDATE())) as meses_antes_activos, (DATEPART(YEAR, dateadd(year, ".$edad_maxima_pensionados.", '" . $_REQUEST["fecha_nacimiento"] . "')) - DATEPART(YEAR, GETDATE())) * 12 + (DATEPART(MONTH, dateadd(year, ".$edad_maxima_pensionados.", '" . $_REQUEST["fecha_nacimiento"] . "' ) ) - DATEPART(MONTH,GETDATE())) as meses_antes_pensionados from empleados where cedula = '" . trim($_REQUEST["cedula"]) . "' AND pagaduria = '" . $_REQUEST["pagaduria"] . "'";
		
		$meses_antes_rs = sqlsrv_query($link, $queryDB);
		
		$fila = sqlsrv_fetch_array($meses_antes_rs);
		
		

		$diff_dias_ultimo_mes = date("j", strtotime($_REQUEST["fecha_nacimiento"])) - date("j", strtotime(date("Y-m-d")));

		if (strtoupper($_REQUEST["nivel_contratacion"]) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"])
			$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);
		else
			$meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_activos"] : ($fila["meses_antes_activos"] - 1);

		if (strtoupper($_REQUEST["nivel_contratacion"]) != "PENSIONADO") {
			if ($meses_antes < $plazo_maximo) {
				$plazo = $meses_antes;
			}
		}

		if ($meses_antes < 0) {
			$plazo = 0;
		}

		if ($meses_antes == 1)
			$meses_antes .= " MES";

		if ($meses_antes > 1)
			$meses_antes .= " MESES";

		if ($meses_antes <= 0)
			$meses_antes = "0";

		$id1 = explode("'0', '", $_SESSION["S_IDUNIDADNEGOCIO"]);

		$id2 = explode("'", $id1[1]);

		if(isset($_REQUEST["id_unidad_negocio"])){
	        $id_unidad_negocio = $_REQUEST["id_unidad_negocio"];
	    }else{
	        $id_unidad_negocio = $id2[0];
	    }

		$rs_tasa = sqlsrv_query($link, "select id_tasa from tasas" . $sufijo_sector . " where plazoi <= '" . $plazo . "' AND plazof >= '" . $plazo . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

		if (sqlsrv_num_rows($rs_tasa)) {
			$fila_tasa = sqlsrv_fetch_array($rs_tasa);

			$queryDB = "SELECT TOP 1 CAST(t2.tasa_interes as float) + 0 as tasa_interes, CAST(t2.descuento1 as float) + 0 as descuento1,CAST(t2.descuento2 as float) + 0 as descuento2, CAST(t2.descuento3 as float) + 0 as descuento3 from tasas2" . $sufijo_sector . " as t2 INNER JOIN tasas2_unidades" . $sufijo_sector . " as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '" . $fila_tasa["id_tasa"] . "'";
			
			$queryDB .= " AND t2u.id_unidad_negocio = '" . $id_unidad_negocio . "'";

			$queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";

			if (strtoupper($_REQUEST["nivel_contratacion"]) == "PENSIONADO")
				$queryDB .= " OR t2.solo_pensionados = '1'";
			else
				$queryDB .= " OR t2.solo_activos = '1'";

			$queryDB .= ") order by t2.tasa_interes DESC";

			$rs_tasa2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs_tasa2)) {
				$fila_tasa2 = sqlsrv_fetch_array($rs_tasa2);

				$tasa_interes = $fila_tasa2["tasa_interes"];

				$descuento1 = $fila_tasa2["descuento1"];

				$descuento2 = $fila_tasa2["descuento2"];

				$descuento3 = $fila_tasa2["descuento3"];
			} else {
				$tasa_interes = 0;

				$descuento1 = 0;

				$descuento2 = 0;

				$descuento3 = 0;
			}
		} else {
			$tasa_interes = 0;

			$descuento1 = 0;

			$descuento2 = 0;

			$descuento3 = 0;
		}


		if ($sector == "PRIVADO")
			$descuento3 += $descuento1 * $iva / 100;

		$fecha_estudio_date = new DateTime("now");
	    $fecha_nacimiento_date = new DateTime($_REQUEST["fecha_nacimiento"]);
	    $diff_fechas = $fecha_nacimiento_date->diff($fecha_estudio_date);

	    $valor_por_millon_seguro = 0;
	    $valor_por_millon_seguro_parcial = 0;

	    if(($fecha_estudio_date) >= date_create("2024-01-01")){

	        $rs2 = sqlsrv_query($link, "SELECT * FROM edad_rango_seguro WHERE (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin) OR (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	        if($rs2 && sqlsrv_num_rows($rs2) > 0){
	            $fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

	            $valor_por_millon_seguro_base = $fila2["valor_por_millon"];
	            $valor_por_millon_seguro_parcial = $fila2["valor_por_millon_parcial"];

	            if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
	                $valor_por_millon_seguro = $fila2["valor_por_millon_parcial"];
	            }else{
	                $valor_por_millon_seguro = $fila2["valor_por_millon"];
	            }
	        }

	        $rs1 = sqlsrv_query($link, "SELECT valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial, valor_por_millon_seguro_colpensiones_parcial, gmf from unidades_negocio where id_unidad = '" . $id_unidad_negocio . "'");
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	    }

		if($fecha_estudio_date < date_create("2024-01-01") || $valor_por_millon_seguro == 0){

			$rs1 = sqlsrv_query($link, "SELECT valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial,valor_por_millon_seguro_colpensiones_parcial, gmf from unidades_negocio where id_unidad = '" . $id_unidad_negocio . "'");
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

			$valor_por_millon_seguro_parcial = 0;

		if (strtoupper($_REQUEST["nivel_contratacion"]) == "PENSIONADO" && $_SESSION["FUNC_PENSIONADOS"]){
			if (strtoupper($_REQUEST["pagaduria"]) == "COLPENSIONES"){
				if ($seguro_parcial==1){
					$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones_parcial"];
					$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_colpensiones"];
				}else{
					$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones"];
					$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_colpensiones"];
				}
				$valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_colpensiones_parcial"];
			}
			else{		
				if ($seguro_parcial==1){
					$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados_parcial"];
					$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_pensionados"];
				}else{
					$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados"];
					$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_pensionados"];
				}
				$valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_pensionados_parcial"];
			}
		}else{
			if ($seguro_parcial==1){
				$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos_parcial"];
				$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_activos"];
			}else{
				$valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos"];
				$valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_activos"];
			}
			$valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_activos_parcial"];
		}
	}

		if ($plazo){
			$porcentaje_seguro = PorcentajeSeguro($valor_por_millon_seguro, $plazo, $tasa_interes, 0, 0,$seguro_parcial);
		}
		else{
			$porcentaje_seguro = 0;
		}

		if (!$fila1["gmf"]){
			$descuento4 = 0;
		}

		if ($servicio_nube == '1') {
			//$descuento7 = $descuento2 + $descuento3;
			$descuento7 = 0;
			$descuento2 = 0;
			$descuento3 = 0;
		} else {
			$descuento7 = 0;
		}

		

		//Tasa Comisones
		$id_unidad_negocio_tasa_comision = $id_unidad_negocio;

		if ($id_unidad_negocio_tasa_comision == 4 || $id_unidad_negocio_tasa_comision == 11 || $id_unidad_negocio_tasa_comision == 14 || $id_unidad_negocio_tasa_comision == 19 || $id_unidad_negocio_tasa_comision == 23) {
			$id_unidad_negocio_tasa_comision = 4; //Fianti
		} else if ($id_unidad_negocio_tasa_comision == 6 || $id_unidad_negocio_tasa_comision == 15 || $id_unidad_negocio_tasa_comision == 21) {
			$id_unidad_negocio_tasa_comision = 6; //Atraccion
		} else if ($id_unidad_negocio_tasa_comision == 2 || $id_unidad_negocio_tasa_comision == 12 || $id_unidad_negocio_tasa_comision == 16 || $id_unidad_negocio_tasa_comision == 22) {
			$id_unidad_negocio_tasa_comision = 2; //Salvamento
		} else if ($id_unidad_negocio_tasa_comision == 1 || $id_unidad_negocio_tasa_comision == 10 || $id_unidad_negocio_tasa_comision == 17 || $id_unidad_negocio_tasa_comision == 20) {
			$id_unidad_negocio_tasa_comision = 1; //Kredit
		}

		$sqlTasaComision = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = " . $id_unidad_negocio_tasa_comision . " AND a.tasa = $tasa_interes AND GETDATE() >= a.fecha_inicio AND (a.fecha_fin IN('01-01-0001') OR a.fecha_fin IS NULL OR a.fecha_fin <= GETDATE())";
		
		$queryTasaComision = sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (@sqlsrv_num_rows($queryTasaComision) > 0) {
			$respTasaComision = sqlsrv_fetch_array($queryTasaComision);
			$id_tasa_comision = $respTasaComision["id_tasa_comision"];
			$tipo_tasa_comision = $respTasaComision["id_tipo"];
		} else {
			$id_tasa_comision = 0;
			$tipo_tasa_comision = 0;
		}
		

		$queryInsert = "INSERT into simulaciones (servicio_nube,id_comercial,
		 id_oficina,
		 telemercadeo,
		 fecha_estudio,
		 cedula,
		 nombre,
		 pagaduria,
		 pa,
		 ciudad,
		 institucion,
		 nivel_educativo,
		 fecha_nacimiento,
		 telefono,
		 meses_antes_65,
		 fecha_inicio_labor,
		 medio_contacto,
		 salario_basico,
		 adicionales,
		 bonificacion,
		 total_ingresos,
		 aportes,
		 otros_aportes,
		 total_aportes,
		 total_egresos,
		 salario_minimo,
		 ingresos_menos_aportes,
		 salario_libre,
		 nivel_contratacion,
		 embargo_actual,
		 historial_embargos,
		 embargo_alimentos,
		 embargo_centrales,
		 descuentos_por_fuera,
		 cartera_mora,
		 valor_cartera_mora,
		 puntaje_datacredito,
		 puntaje_cifin,
		 valor_descuentos_por_fuera,
		 id_unidad_negocio,
		 tasa_interes,
		 plazo,
		 tipo_credito,
		 suma_al_presupuesto,
		 total_cuota,
		 total_valor_pagar,
		 retanqueo1_libranza,
		 retanqueo1_cuota,
		 retanqueo1_valor,
		 retanqueo2_libranza,
		 retanqueo2_cuota,
		 retanqueo2_valor,
		 retanqueo3_libranza,
		 retanqueo3_cuota,
		 retanqueo3_valor,
		 retanqueo_total_cuota,
		 retanqueo_total,
		 opcion_credito,
		 opcion_cuota_cli,
		 opcion_desembolso_cli,
		 opcion_cuota_ccc,
		 opcion_desembolso_ccc,
		 opcion_cuota_cmp,
		 opcion_desembolso_cmp,
		 opcion_cuota_cso,
		 opcion_desembolso_cso,
		 desembolso_cliente,
		 decision,
		 decision_sistema,
		 valor_visado,
		 bloqueo_cuota,
		 bloqueo_cuota_valor,
		 fecha_llamada_cliente,
		 nro_cuenta,
		 tipo_cuenta,
		 id_banco,
		 id_subestado,
		 id_caracteristica,
		 calificacion,
		 dia_confirmacion,
		 dia_vencimiento,
		 status,
		 valor_credito,
		 resumen_ingreso,
		 incor,
		 comision,
		 utilidad_neta,
		 sobre_el_credito,
		 estado,
		 tipo_producto,
		 descuento1,
		 descuento2,
		 descuento3,
		 descuento4,
		 descuento5,
		 descuento6,
		descuento7,
		 descuento_transferencia,
		 porcentaje_seguro,
		 valor_por_millon_seguro,
		 porcentaje_extraprima,
		 sin_aportes,
		 empleado_manual,
		 iva,
		 frente_al_cliente,
		 usuario_radicado,
		 fecha_radicado,
		 usuario_creacion,
		 fecha_creacion,
		 id_tasa_comision,
		 id_tipo_tasa_comision) values 
		 ('" . $servicio_nube . "',
		'" . $id_comercial . "',
		 (SELECT top 1 id_oficina from oficinas_usuarios where id_usuario = '" . $id_comercial . "' ),
		 '" . $_REQUEST["telemercadeo"] . "',
		 GETDATE(),
		 '" . trim($_REQUEST["cedula"]) . "',

		 '" . strtoupper(trim($_REQUEST['primer_apellido'])  . '  ' . trim($_REQUEST['segundo_apellido']) . '  ' . trim($_REQUEST['primer_nombre'])) . '  ' .trim($_REQUEST['segundo_nombre']). "',

		 '" . $_REQUEST["pagaduria"] . "',
		 (select pa from pagaduriaspa where pagaduria = '" . $_REQUEST["pagaduria"] . "'),
		 '" . strtoupper($_REQUEST["ciudad"]) . "',
		 '" . strtoupper($_REQUEST["institucion"]) . "',
		 '',
		 '" . $_REQUEST["fecha_nacimiento"] . "',
		 '" . $_REQUEST["telefono"] . "',
		 '" . $meses_antes . "',
		 '" . $_REQUEST["fecha_inicio_labor"] . "',
		 '" . $_REQUEST["medio_contacto"] . "',
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 (select salario_minimo from salario_minimo where ano = YEAR(GETDATE())),
		 '0',
		 '0',
		 '" . $_REQUEST["nivel_contratacion"] . "',
		 'NO',
		 '0',
		 'NO',
		 'NO',
		 'NO',
		 'NO',
		 '0',
		 '-1',
		 '-1',
		 '0',
		 '" . $id_unidad_negocio . "',
		 '" . $tasa_interes . "',
		 '" . $plazo . "',
		 'CREDITO NORMAL',
		 '0',
		 '0',
		 '0',
		 '',
		 '0',
		 '0',
		 '',
		 '0',
		 '0',
		 '',
		 '0',
		 '0',
		 '0',
		 '0',
		 'CCC',
		 '0',
		 '" . (-1.00 * $descuento_transferencia) . "',
		 '0',
		 '" . (-1.00 * $descuento_transferencia) . "',
		 '0',
		 '" . (-1.00 * $descuento_transferencia) . "',
		 '0',
		 '" . (-1.00 * $descuento_transferencia) . "',
		 '" . (-1.00 * $descuento_transferencia) . "',
		 '" . $label_viable . "',
		 '" . $label_negado . "',
		 '0',
		 '" . $bloqueo_cuota . "',
		 '0',
		 " . $fecha_llamada_cliente . ",
		 " . $nro_cuenta . ",
		 " . $tipo_cuenta . ",
		 " . $id_banco . ",
		 " . $id_subestado . ",
		 " . $id_caracteristica . ",
		 " . $calificacion . ",
		 " . $dia_confirmacion . ",
		 " . $dia_vencimiento . ",
		 " . $status . ",
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 '0',
		 'ING',
		 '0',
		 '".$descuento1."',
		 '".$descuento2." ',
		 '".$descuento3."',
		 '" . $descuento4 . "',
		 '" . $descuento5 . "',
		 '" . $descuento6 . "',
		 '" . $descuento7 . "',
		 '" . $descuento_transferencia . "',
		 '" . $porcentaje_seguro . "',
		 '" . $valor_por_millon_seguro . "',
		 '0',
		 '" . $sin_aportes . "',
		 '" . $empleado_manual . "',
		 '" . $iva . "',
		 '" . $_REQUEST["frente_al_cliente"] . "',
		 '" . $_SESSION["S_LOGIN"] . "',
		 GETDATE(),
		 '" . $_SESSION["S_LOGIN"] . "',
		 GETDATE(),
		 $id_tasa_comision,
		 '" . $tipo_tasa_comision . "')";
		
		

		if (sqlsrv_query($link, $queryInsert)) {

			$id_simulacion = sqlsrv_query($link, " SELECT scope_identity() as id_simulacion");
			$id = sqlsrv_fetch_array($id_simulacion);
			$id_simul = $id['id_simulacion'];

			$token_Preprospectar = $_POST["token_Preprospectar"];
			$actualiarRegistro = "UPDATE preprospectar SET estado = '1' WHERE id_preprospeccion = '".$token_Preprospectar."'";
			$queryActualizarEstado = sqlsrv_query($link, $actualiarRegistro);
			if (!$queryActualizarEstado) {
				if ($queryActualizarEstadoo == false) {
					if( ($errors = sqlsrv_errors() ) != null) {
						foreach( $errors as $error ) {
							echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
							echo "code: ".$error[ 'code']."<br />";
							echo "message: ".$error[ 'message']."<br />";
						}
					}
				}
			}

			sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) VALUES ($id_simul,197,197,CURRENT_TIMESTAMP(),'s',1,9);");

			if(isset($_POST["id_historial_sms_otp"]) && !empty($_POST["id_historial_sms_otp"])){
				sqlsrv_query($link, "UPDATE historial_sms_otp SET id_simulacion = '".$id_simul."' WHERE id = '".$_POST["id_historial_sms_otp"]."'");
				sqlsrv_query($link,"UPDATE simulaciones SET otp_verificado=1 WHERE id_simulacion = '".$id_simul."'");
			}

			if ($_REQUEST["observaciones"])
				sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . utf8_encode($_REQUEST["observaciones"]) . "', '" . $_SESSION["S_LOGIN"] . "', getdate())");

			if (sqlsrv_num_rows($existe_simulacion))
				sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . utf8_encode("El credito actual ha sido estudiado con menos de 30 dias lo cual no cumple con las politicas de fabrica. Por favor evaluar si es un credito dividido por superar los 80 millones o un credito menor a 30 dias") . "', 'system', getdate())");

			sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, email, sexo) values ('" . $id_simul . "', '" . trim($_REQUEST["cedula"]) . "', '" . $_REQUEST["fecha_nacimiento"] . "', '" . ($_REQUEST["telefono"]) . "', '" . ($_REQUEST["celular"]) . "', '" . (strtoupper($_REQUEST["direccion"])) . "', '" . ($_REQUEST["email"]) . "', '" . (strtoupper($_REQUEST["sexo"])) . "')");
			
			$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '" . $_REQUEST["pagaduria"] . "' and estado = '1' order by id_descuento");

			while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
				sqlsrv_query($link, "insert into simulaciones_descuentos (id_simulacion, id_descuento, porcentaje) values ('" . $id_simul . "', '" . $fila1["id_descuento"] . "', '" . $fila1["porcentaje"] . "')");
			}

			$upmax_rs = sqlsrv_query($link, "select valor from parametros where codigo IN ('UPMAX') order by codigo");

			$fila1 = sqlsrv_fetch_array($upmax_rs);

			$upmax = $fila1['valor'];

			for ($i = 1; $i <= 5; $i++) {
				if (strcmp($_FILES["archivo" . $i]["name"], "")) {
					if (($_FILES["archivo" . $i]["size"] / 1024) <= $upmax) {
						$uniqueID = uniqid();
						$extension = explode("/", $_FILES["archivo" . $i]['type']);
						$nombreArc = md5(rand() + $id_simul) . "." . $extension[1];
						sqlsrv_query($link, "insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('" . $id_simul . "', '" . $_REQUEST["id_tipo" . $i] . "', '" . basename($_FILES["archivo" . $i]['name'], "." . $extension[1]) . " / " . $_REQUEST["descripcion" . $i] . "', '" . $nombreArc . "', '" . $nombreArc . "', '0', '" . $_SESSION["S_LOGIN"] . "', getdate())");

						$fechaa = new DateTime();
						$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

						$metadata1 = array(
							'id_simulacion' => $id_simul,
							'descripcion' => ($nombreArc),
							'usuario_creacion' => $_SESSION["S_LOGIN"],
							'fecha_creacion' => $fechaFormateada
						);

						upload_file($_FILES["archivo" . $i], "simulaciones", $id_simul . "/adjuntos/" . $nombreArc, $metadata1);
					} else {
						$mensaje .= "El tamaño del archivo supera lo permitido (" . number_format($upmax / 1024, 2, ".", ",") . " MB), archivo " . $i . " NO ingresado. ";
					}
				}
			}

			//$siguiente_analista = sqlsrv_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$sector."') AND id_usuario > (select id_usuario from asignaciones_analistas where tipo = 'PFC' AND sector = '".$sector."') LIMIT 1", $link);

			//if (sqlsrv_num_rows($siguiente_analista))
			//{
			//	$fila1 = sqlsrv_fetch_assoc($siguiente_analista);

			//	$id_siguiente_analista = $fila1["id_usuario"];
			//}
			//else
			//{
			//	$siguiente_analista = sqlsrv_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$sector."') order by id_usuario LIMIT 1", $link);

			//	if (sqlsrv_num_rows($siguiente_analista))
			//	{
			//		$fila1 = sqlsrv_fetch_assoc($siguiente_analista);

			//		$id_siguiente_analista = $fila1["id_usuario"];
			//	}
			//}

			//if ($id_siguiente_analista)
			//{

			//sqlsrv_query("update simulaciones set id_analista_riesgo_operativo = '".$id_siguiente_analista."', id_analista_riesgo_crediticio = '".$id_siguiente_analista."' where id_simulacion = '".$id_simul."'", $link);

			//sqlsrv_query("update asignaciones_analistas set id_usuario = '".$id_siguiente_analista."' where tipo = 'PFC' AND sector = '".$sector."'", $link);
			//}

			if ($consulta_centrales) {
				libxml_use_internal_errors(true);

				//EXPERIAN HDC + ACIERTA
				$lastName = explode(" ", trim($_REQUEST['apellidos']));

				$experian_hdcacierta_parametros = '"idType":"1","idNumber":"' . trim($_REQUEST["cedula"]) . '","lastName":"' . strtoupper($lastName[0]) . '","product":"' . $experian_hdcacierta_product . '","userId":"' . $experian_userid . '","password":"' . $experian_password . '"';

				$experian_hdcacierta_response = WSCentrales($experian_hdcacierta_url, $experian_hdcacierta_parametros);

				$xmlstr = reemplazar_caracteres_WS($experian_hdcacierta_response);

				$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

				if ($objeto_ws === false) {
					foreach (libxml_get_errors() as $error) {
						if (!$experian_hdcacierta_error)
							$experian_hdcacierta_error = "Error cargando XML: ";
						else
							$experian_hdcacierta_error .= "; ";

						$experian_hdcacierta_error .= $error->message;
					}
				}

				sqlsrv_query($link, "insert into consultas_externas (id_simulacion, cedula, proveedor, servicio, parametros, respuesta, error_xml, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . trim($_REQUEST["cedula"]) . "', 'EXPERIAN', 'HDC_ACIERTA', '" . $experian_hdcacierta_parametros . "', '" . $experian_hdcacierta_response . "', '" . $experian_hdcacierta_error . "', '" . $_SESSION["S_LOGIN"] . "', getdate())");

				//TRANSUNION INFORMACION COMERCIAL
				$transunion_infocomercial_parametros = '"idType":"1","idNumber":"' . trim($_REQUEST["cedula"]) . '","reason":"' . $transunion_reason . '","infoCode":"' . $transunion_infocomercial_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';

				$transunion_infocomercial_response = WSCentrales($transunion_infocomercial_url, $transunion_infocomercial_parametros);

				$xmlstr = reemplazar_caracteres_WS($transunion_infocomercial_response);

				$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

				if ($objeto_ws === false) {
					foreach (libxml_get_errors() as $error) {
						if (!$transunion_infocomercial_error)
							$transunion_infocomercial_error = "Error cargando XML: ";
						else
							$transunion_infocomercial_error .= "; ";

						$transunion_infocomercial_error .= $error->message;
					}
				}

				sqlsrv_query($link, "insert into consultas_externas (id_simulacion, cedula, proveedor, servicio, parametros, respuesta, error_xml, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . trim($_REQUEST["cedula"]) . "', 'TRANSUNION', 'INFORMACION_COMERCIAL', '" . $transunion_infocomercial_parametros . "', '" . $transunion_infocomercial_response . "', '" . $transunion_infocomercial_error . "', '" . $_SESSION["S_LOGIN"] . "', getdate())");

				//TRANSUNION LEGALCHECK
				$transunion_legalcheck_parametros = '"idType":"1","idNumber":"' . trim($_REQUEST["cedula"]) . '","infoCode":"' . $transunion_legalcheck_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';

				$transunion_legalcheck_response = WSCentrales($transunion_legalcheck_url, $transunion_legalcheck_parametros);

				$xmlstr = reemplazar_caracteres_WS($transunion_legalcheck_response);

				$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

				if ($objeto_ws === false) {
					foreach (libxml_get_errors() as $error) {
						if (!$transunion_legalcheck_error)
							$transunion_legalcheck_error = "Error cargando XML: ";
						else
							$transunion_legalcheck_error .= "; ";

						$transunion_legalcheck_error .= $error->message;
					}
				}

				sqlsrv_query($link, "insert into consultas_externas (id_simulacion, cedula, proveedor, servicio, parametros, respuesta, error_xml, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . trim($_REQUEST["cedula"]) . "', 'TRANSUNION', 'LEGALCHECK', '" . $transunion_legalcheck_parametros . "', '" . $transunion_legalcheck_response . "', '" . $transunion_legalcheck_error . "', '" . $_SESSION["S_LOGIN"] . "', getdate())");

				//TRANSUNION UBICAPLUS
				$transunion_ubicaplus_parametros = '"idType":"1","idNumber":"' . trim($_REQUEST["cedula"]) . '","reason":"' . $transunion_reason . '","infoCode":"' . $transunion_ubicaplus_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';

				$transunion_ubicaplus_response = WSCentrales($transunion_ubicaplus_url, $transunion_ubicaplus_parametros);

				$xmlstr = reemplazar_caracteres_WS($transunion_ubicaplus_response);

				$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

				if ($objeto_ws === false) {
					foreach (libxml_get_errors() as $error) {
						if (!$transunion_ubicaplus_error)
							$transunion_ubicaplus_error = "Error cargando XML: ";
						else
							$transunion_ubicaplus_error .= "; ";

						$transunion_ubicaplus_error .= $error->message;
					}
				}

				sqlsrv_query($link, "insert into consultas_externas (id_simulacion, cedula, proveedor, servicio, parametros, respuesta, error_xml, usuario_creacion, fecha_creacion) values ('" . $id_simul . "', '" . trim($_REQUEST["cedula"]) . "', 'TRANSUNION', 'UBICAPLUS', '" . $transunion_ubicaplus_parametros . "', '" . $transunion_ubicaplus_response . "', '" . $transunion_ubicaplus_error . "', '" . $_SESSION["S_LOGIN"] . "', getdate())");
			}
			$estado = 1;

			$mensaje .= "Simulacion guardada exitosamente, con el id: " . $id_simul;

			/*if ($oficina_ado == 1) {

				//Enviar Correo Validacion
				$nombre_completo = strtoupper(trim($_REQUEST['nombres']) . " " . trim($_REQUEST['apellidos']));

				$opciones = array(
					'http' => array(
						'method' => 'POST',
						'content' => 'id_usuario=' . $_SESSION["S_IDUSUARIO"] . '&id_simulacion=' . $id_simul . '&nombre=' . $nombre_completo . '&correo=' . $_REQUEST["email"]
					)
				);

				$contexto = stream_context_create($opciones);

				$json_Input = file_get_contents($urlPrincipal . '/servicios/enviar_correo_validacion_id.php', false, $contexto);

				$parametros = json_decode($json_Input);

				if ($parametros) {
					$mensaje .= '  \n ' . $parametros->mensaje;
				} else {
					$mensaje .= '  \n ¡HA OCURRIDO UN ERROR AL ENVIAR CORREO DE VERFICACIÓN!';
				}
			}*/

			if ($oficina_gattaca == 1) {
				$conHistorialJudicial = sqlsrv_query($link, "SELECT * FROM historial_consultas_judiciales a WHERE a.id_simulacion = " . $id_simul);
				if (sqlsrv_num_rows($conHistorialJudicial) == 0) {

					$opciones = array(
						'http' => array(
							'method' => 'POST',
							'content' => 'id_usuario=' . $_SESSION["S_IDUSUARIO"] . '&id_simulacion=' . $id_simul . '&peticion=enviarPeticion'
						)
					);

					$contexto = stream_context_create($opciones);

					$json_Input = file_get_contents($urlPrincipal . '/servicios/consultaAntecedentes.php', false, $contexto);

					$parametros = json_decode($json_Input);

					if ($parametros->code == 200) {
						$mensaje .= '\nScoring Judicial Solicitado\n';
					} else {
						$mensaje .= '\n ¡ERROR Al Solicitar Scoring Judicial!';
					}
				}
			}
		} else {
			
			$mensaje = "¡ERROR al Prospectar! Vuelva a intentarlo. Copie el texto de este mensaje y peguelo en el cuerpo de un correo ó tome Pantallazo de este error y envielo a soporte@kredit.com.co . Query: " . $queryInsert;
		}

	echo $mensaje;
} else{
	// if(!$omitir_validacion_credito_estudio){
	// 	$mensaje .= "Hay registro en Estudio para esta Cédula con la misma Pagaduría.";
	// }else{
		$mensaje .= "Hay un estudio ingresado hace menos de 30 dias asociado a esa Cédula.";
	// }
}

?>
<script>
	var estado = <?php echo $estado; ?>;

	<?php
	if (!$_REQUEST["buscar"]) {
		$_REQUEST["buscar"] = "1";
		$_REQUEST["descripcion_busqueda"] = trim($_REQUEST["cedula"]);
	} 
	?>
	console.log(estado);
	if (estado == 1) {

		var SendInfo = {
			operacion: "Asignar Analista Inicial",
			id_simulacion: '<?php if(!$id_simul){echo 0;}else{echo $id_simul;}  ?>'
		};

		$.ajax({
			type: 'POST',
			url: '../servicios/FDC/asignacionInicialAnalista.php',
			data: JSON.stringify(SendInfo),
			contentType: "application/json; charset=utf-8",
			traditional: true,
			success: function(data) {
				if (data.codigo==200){
					alert('<?php echo $mensaje ?>');
				}else{
					alert("ERROR AL EJECUTAR ACCION");

				}
				return false;
			}
		});

		window.location = 'simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
	} else {

		alert('<?php echo $mensaje ?>');
		window.location = 'prospeccion_crear.php';
	}
</script>