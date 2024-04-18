<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ComprasCartera.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_REQUEST["id_simulacion"])
{
	if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
	{
		exit;
	}
}
else
{
	if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO")
	{
		exit;
	}
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>ID Simulacion</th>
<?php

if (!$_REQUEST["id_simulacion"]){
?>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>F. Desembolso</th>
	<th>Mes Prod</th>
	<th>No. Libranza</th>
	<th>Unidad de Negocio</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr. Credito</th>
	<th>Vr. Desembolso</th>
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Plazo</th>
	<th>Comercial</th>
	<th>Oficina</th>
<?php
}
?>
		<th>Entidad</th>
		<th>Observacion</th>
		<th>Cuota Retenida</th>
		<th>Vr. Pagar</th>
		<th>F. Giro</th>
		<th>F. Certificacion</th>
		<th>Paz y Salvo</th>
		<th>Pagada</th>
		<th>F. Tesoreria Final</th>
		<th>Subestado</th>
</tr>
<?php

$queryDB = "SELECT si.*, sb.nombre AS subestado, un.nombre as unidad_negocio, pa.sector, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod,  us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados sb ON sb.id_subestado = si.id_subestado WHERE (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.") AND si.id_subestado NOT IN (".$subestado_tesoreria_con_pdtes.")))";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["id_simulacion"])
{
	$queryDB .= " AND si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
}

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"] && $_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND si.fecha_cartera BETWEEN '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."' AND '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}else{
		if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) {
			$queryDB .= " AND si.fecha_cartera >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
		}
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
			$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
	}

	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"] && $_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND si.fecha_tesoreria BETWEEN '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."' AND '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}else{
		if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) {
			$queryDB .= " AND si.fecha_tesoreria >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
		}
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
			$queryDB .= " AND si.fecha_tesoreria <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
	}

$queryDB .= " order by si.id_simulacion";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	switch($fila["opcion_credito"])
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
	
	$fecha_desemb = $fila["fecha_desembolso"];
	
	//Si est� filtrado por fecha de corte
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		if ($fila["fecha_desembolso"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
			$fecha_desemb = "";
	}
	
	$queryDB = "SELECT scc.consecutivo, ent.nombre as nombre_entidad, scc.se_compra, scc.entidad, scc.valor_pagar from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '".$fila["id_simulacion"]."' order by scc.consecutivo";
	
	$rs2 = sqlsrv_query($link, $queryDB);
	
	while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC))
	{
		$pys = "NO";
		
		$fecha_vencimiento = "";
		
		$pagada = "NO";
		
		$cuota_retenida = "0";
		
		$fecha_girocc = "";
		
		if ($fila2["se_compra"] == "SI" && ($fila2["nombre_entidad"] || $fila2["entidad"]))
		{
			$agenda_tmp = sqlsrv_query($link, "SELECT fecha_vencimiento from agenda where id_simulacion = '".$fila["id_simulacion"]."' AND consecutivo = '".$fila2["consecutivo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($agenda_tmp))
			{
				$fila1 = sqlsrv_fetch_array($agenda_tmp);
				
				$fecha_vencimiento = $fila1["fecha_vencimiento"];
			}
			
			$cc_tmp = sqlsrv_query($link, "SELECT pagada, cuota_retenida, fecha_giro, id_adjunto from tesoreria_cc where id_simulacion = '".$fila["id_simulacion"]."' AND consecutivo = '".$fila2["consecutivo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($cc_tmp))
			{
				$fila1 = sqlsrv_fetch_array($cc_tmp);
				
				if ($fila1["id_adjunto"])
					$pys = "SI";
				
				if ($fila1["pagada"])
					$pagada = "SI";
				
				$cuota_retenida = $fila1["cuota_retenida"];
				
				$fecha_girocc = $fila1["fecha_giro"];
			}
			else if ($fila["estado_tesoreria"] = "CER")
			{
				$pagada = "SI";
			}
			
			//Si est� filtrado por fecha de corte
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
			{
				if ($fecha_girocc && $fecha_girocc > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
				{
					$fecha_girocc = "";
					
					$pagada = "NO";
				}
					
				if ($pys == "SI")
				{
					$adjunto_tmp = sqlsrv_query($link, "select id_adjunto from adjuntos where id_adjunto = '".$fila1["id_adjunto"]."' AND DATE(fecha_creacion) > '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($adjunto_tmp))
						$pys = "NO";
				}
			}
			
			if ((!$_REQUEST["estado"] || $_REQUEST["estado"] == $pagada) && (!$_REQUEST["pys"] || $_REQUEST["pys"] == $pys))
			{
			
?>
<tr>
<td><?php echo $fila["id_simulacion"] ?></td>
<?php

				if (!$_REQUEST["id_simulacion"])
				{
				
?>
					<td><?php echo $fila["cedula"] ?></td>
					<td><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td><?php echo $fecha_desemb ?></td>
					<td><?php echo $fila["mes_prod"] ?></td>
					<td><?php echo $fila["nro_libranza"] ?></td>
					<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
					<td><?php echo $fila["tasa_interes"] ?></td>
					<td><?php echo $opcion_cuota ?></td>
					<td><?php echo $fila["valor_credito"] ?></td>
					<td><?php echo $opcion_desembolso ?></td>
					<td><?php echo $fila["sector"] ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<td><?php echo $fila["plazo"] ?></td>
					<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
					<td><?php echo utf8_decode($fila["oficina"]) ?></td>
				<?php
						$val=0;
						$fecha_ultima_tesoreria="";
						$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC ";
						$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
							$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
							$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
							$val=1;
						}

						if ($val==0){
							$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC";
							$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

							if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
								$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
								$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
								$val=1;
							}
						}

						?>
						<td><?php echo $fecha_ultima_tesoreria ?></td>
						<td><?php echo $fila["subestado"] ?></td>
					</tr>
					<?php

					$total_cuota_retenida += $cuota_retenida;
					$total_valor_pagar += $fila2["valor_pagar"];
				}
			}
		}

	
	if (!$_REQUEST["id_simulacion"])
	{
		$queryDB = "select * from giros where id_simulacion = '".$fila["id_simulacion"]."' AND clasificacion = 'RET'";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			$fecha_giroret = $fila1["fecha_giro"];
			
			//Si est� filtrado por fecha de corte
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
			{
				if ($fecha_giroret && $fecha_giroret > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
					$fecha_giroret = "";
			}
			

				$val=0;
				$fecha_ultima_tesoreria="";
				$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC ";
				$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
					$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
					$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
					$val=1;
				}

				if ($val==0){
					$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC";
					$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
						$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
						$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
						$val=1;
					}
				}

				?>
<tr>
<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fecha_desemb ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $opcion_desembolso ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td>RETANQUEO</td>
	<td><?php echo utf8_decode($fila1["beneficiario"]) ?></td>
	<td>0</td>
	<td><?php echo $fila1["valor_girar"] ?></td>
	<td><?php echo $fecha_giroret ?></td>
	<td></td>
	<td></td>
	<td><?php if ($fecha_giroret) { echo "SI"; } else { echo "NO"; } ?></td>
</tr>
<?php
					$val=0;
					$fecha_ultima_tesoreria="";
					$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC ";
					$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
					if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
						$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
						$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
						$val=1;
					}

					if ($val==0){
						$consulta="SELECT top 1* FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC";
						$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
						if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
							$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
							$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
							$val=1;
						}
					}

					?>
					<td><?php echo $fecha_ultima_tesoreria ?></td>
					<td><?php echo $fila["subestado"] ?></td>
				</tr>
				<?php

				$total_valor_pagar += $fila1["valor_girar"];
			}
		
		$queryDB = "select * from giros where id_simulacion = '".$fila["id_simulacion"]."' AND clasificacion = 'DSC'";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			$fecha_girodsc = $fila1["fecha_giro"];
			
			//Si est� filtrado por fecha de corte
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
			{
				if ($fecha_girodsc && $fecha_girodsc > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
					$fecha_girodsc = "";
			}
			
?>
<tr>
<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fecha_desemb ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $opcion_desembolso ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td>CLIENTE</td>
	<td><?php echo utf8_decode($fila1["beneficiario"]) ?></td>
	<td>0</td>
	<td><?php echo $fila1["valor_girar"] ?></td>
	<td><?php echo $fecha_girodsc ?></td>
	<td></td>
	<td></td>
	<td><?php if ($fecha_girodsc) { echo "SI"; } else { echo "NO"; } ?></td>
</tr>
<?php
					$val=0;
					$fecha_ultima_tesoreria="";
					$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC ";
					$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
					if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
						$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
						$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
						$val=1;
					}

					if ($val==0){
						$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC";
						$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
						if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
							$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
							$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
							$val=1;
						}
					}

					?>
					<td><?php echo $fecha_ultima_tesoreria ?></td>
					<td><?php echo $fila["subestado"] ?></td>
				</tr>
				<?php

				$total_valor_pagar += $fila1["valor_girar"];
			}

			$queryDB = "select * from giros where id_simulacion = '".$fila["id_simulacion"]."' AND clasificacion = 'GCR'";

			$rs1 = sqlsrv_query($link, $queryDB);

			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
				$fecha_giroret = $fila1["fecha_giro"];

				//Si est� filtrado por fecha de corte
				if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
					if ($fecha_giroret && $fecha_giroret > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
						$fecha_giroret = "";
				}

				?>
<tr>
					<td><?php echo $fila["id_simulacion"] ?></td>
					<td><?php echo $fila["cedula"] ?></td>
					<td><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td><?php echo $fecha_desemb ?></td>
					<td><?php echo $fila["mes_prod"] ?></td>
					<td><?php echo $fila["nro_libranza"] ?></td>
					<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
					<td><?php echo $fila["tasa_interes"] ?></td>
					<td><?php echo $opcion_cuota ?></td>
					<td><?php echo $fila["valor_credito"] ?></td>
					<td><?php echo $opcion_desembolso ?></td>
					<td><?php echo $fila["sector"] ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<td><?php echo $fila["plazo"] ?></td>
					<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
					<td><?php echo utf8_decode($fila["oficina"]) ?></td>
					<td>CUOTA RETENIDA</td>
					<td><?php echo utf8_decode($fila1["beneficiario"]) ?></td>
					<td>0</td>
					<td><?php echo $fila1["valor_girar"] ?></td>
					<td><?php echo $fecha_giroret ?></td>
					<td></td>
					<td></td>
					<td><?php if ($fecha_giroret) { echo "SI"; } else { echo "NO"; } ?></td>
					<?php
					$val=0;
					$fecha_ultima_tesoreria="";
					$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC 
					";
					$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
					if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
						$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
						$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
						$val=1;
					}

					if ($val==0){
						$consulta="SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC ";
						$consultarUltimoFechaTesoreria=sqlsrv_query($link,$consulta);
						if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
							$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
							$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
							$val=1;
						}
					}

					?>
					<td><?php echo $fecha_ultima_tesoreria ?></td>
					<td><?php echo $fila["subestado"] ?></td>
				</tr>
				<?php

				$total_valor_pagar += $fila1["valor_girar"];
			}
		}
	}
	?>

	<tr>
		<td colspan="<?php if (!$_REQUEST["id_simulacion"]) { echo "17"; } else { echo "2"; } ?>"><b>TOTALES</b></td>
		<td><b><?php echo $total_cuota_retenida ?></b></td>
		<td><b><?php echo $total_valor_pagar ?></b></td>
		<td colspan="5">&nbsp;</td>
	</tr>
</table>

