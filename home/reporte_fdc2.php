<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=FDC.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();


?>
<table border="0">
<tr>
<th>ID Simulacion</th>
<th>Unidad Negocio</th>

<th>Cedula</th>
	<th>Nombre</th>
	
	<th>Pagaduria</th>
	<th>Tipo Comercial</th>
    <th>Comercial</th>
	<th>Oficina</th>
	<th>Valor Credito</th>
	<th>Valor Credito Menos Retanqueos</th>
	<th>Estado</th>
	
	<th>Cuenta</th>
	<th>Subestado</th>
	<th>Causal</th>
	<th>Analista</th>
	<th>F. Primera Vez Radicado</th>
	
	<th>Estado Final</th>
	<th>Fecha Cambio Estado</th>
	<th>Fecha Asignacion</th>
	<th>Fecha Radicacion</th>

</tr>
<?php


$queryDB = "SELECT un.nombre as unidad_negocio,sube2.nombre as subestado2,CASE WHEN uc.freelance=1 or uc.outsourcing=1 THEN 'TERCEROS' ELSE 'PLANTA' END AS tipo_comercial,FORMAT(si.fecha_radicado,'Y-m-d') as fecha_radicacion, FORMAT(si.fecha_radicado,'H:i') as hora_radicacion,si.*,concat(us.nombre,' ',us.apellido) as usuario_analista,sfdc.id as id_sfdc,sfdc.fecha_creacion,ofi.nombre as oficina,CONCAT(uc.nombre,' ',uc.apellido) as nombre_comercial
FROM simulaciones si 
LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre 
LEFT JOIN simulaciones_fdc sfdc ON sfdc.id_simulacion=si.id_simulacion
LEFT JOIN usuarios us ON sfdc.id_usuario_creacion = us.id_usuario 
LEFT JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
LEFT JOIN usuarios uc ON uc.id_usuario=si.id_comercial 
LEFT JOIN unidades_negocio un ON un.id_unidad=si.id_unidad_negocio
LEFT JOIN subestados sube2 ON sube2.id_subestado=si.id_subestado";



$val=0;
if (($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) || ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]))
{
	$queryDB.= " where sfdc.estado in (2)";
	
	$val=2;
}else if (($_REQUEST["fecha_inicialbradd"] && $_REQUEST["fecha_inicialbradm"] && $_REQUEST["fecha_inicialbrada"]) || ($_REQUEST["fecha_finalbradd"] && $_REQUEST["fecha_finalbradm"] && $_REQUEST["fecha_finalbrada"])){
	$queryDB.= " where sfdc.estado in (1,5)";
	$val=1;
}else{
	$val=2;
	$queryDB.= " where sfdc.estado in (2)";
}



if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}


if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["id_oficina"])
{
	$queryDB .= " AND si.id_oficina = '".$_REQUEST["id_oficina"]."'";
}

if ($_REQUEST["id_comercial"])
{
	$queryDB .= " AND sfdc.id_usuario_asignacion = '".$_REQUEST["id_comercial"]."'";
}

if ($_REQUEST["decision"])
{
	$queryDB .= " AND si.decision = '".$_REQUEST["decision"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND FORMAT(sfdc.fecha_creacion,'Y-m-d') >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND FORMAT(sfdc.fecha_creacion,'Y-m-d') <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}


if ($_REQUEST["fecha_inicialbradd"] && $_REQUEST["fecha_inicialbradm"] && $_REQUEST["fecha_inicialbrada"])
{
	$queryDB .= " AND FORMAT(sfdc.fecha_creacion,'Y-m-d') >= '".$_REQUEST["fecha_inicialbrada"]."-".$_REQUEST["fecha_inicialbradm"]."-".$_REQUEST["fecha_inicialbradd"]."'";

}

if ($_REQUEST["fecha_finalbradd"] && $_REQUEST["fecha_finalbradm"] && $_REQUEST["fecha_finalbrada"])
{
	
	$queryDB .= " AND FORMAT(sfdc.fecha_creacion,'Y-m-d') <= '".$_REQUEST["fecha_finalbrada"]."-".$_REQUEST["fecha_finalbradm"]."-".$_REQUEST["fecha_finalbradd"]."'";
}

$queryDB .= " order by sfdc.fecha_creacion, si.nombre, si.cedula";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$val_reg=0;
	$consultarCausal="SELECT * FROM causales WHERE id_causal='".$fila["id_causal"]."' and tipo_causal='NEGACION'";
	$queryCausal=sqlsrv_query($link, $consultarCausal);
	$resCausal=sqlsrv_fetch_array($queryCausal);

    //$contarReprocesos="SELECT CASE WHEN COUNT(id)=0 then 'NUEVO'  WHEN COUNT(id)=1 THEN 'REPROCESO 1'  WHEN COUNT(id)>1 THEN 'REPROCESOS' end as reproceso,CASE WHEN COUNT(id)=0 then 'NO APLICA' else count(id) end AS cuenta_reprocesos FROM simulaciones_fdc WHERE id_simulacion='".$fila["id_simulacion"]."' AND estado=4 and id<".$fila["id_sfdc"];
    //$queryReprocesos=sqlsrv_query($link, $contarReprocesos);
    //$resReproceso=sqlsrv_fetch_array($queryReprocesos);


	$reprocesos = "";
	$contarReprocesos = "SELECT * FROM simulaciones_fdc WHERE estado=4 and id_subestado<>28 and id_simulacion = '".$fila["id_simulacion"]."' and id<'".$fila["id_sfdc"]."'";
	$queryReprocesos=sqlsrv_query($link, $contarReprocesos);
	if (sqlsrv_num_rows($queryReprocesos) == 0) {
		$reprocesos = "NUEVO";
	}else{
		if (sqlsrv_num_rows($queryReprocesos) == 1) {
			$reprocesos="REPROCESO 1";
		}else if (sqlsrv_num_rows($queryReprocesos) > 1) {
			$reprocesos="REPROCESOS";
		}else{
			$reprocesos="NUEVO";
		}
	}

    
	//$consultarFechaTerminacion="SELECT a.*,b.nombre as nombre_subestado FROM simulaciones_fdc a LEFT JOIN subestados b ON a.id_subestado=b.id_subestado WHERE a.id_simulacion='".$fila["id_simulacion"]."' and a.estado=4 and a.id=".$fila["id_sfdc"]." ORDER BY a.id desc LIMIT 1";
	//$queryFechaTerminacion=sqlsrv_query($link, $consultarFechaTerminacion);
    //$resFechaTerminacion=sqlsrv_fetch_array($queryFechaTerminacion);
	$analistaCredito="";
	$subestado="";
	if ($val==1)
	{	
		$consultarFechaAsignacion="SELECT TOP 1 * FROM simulaciones_fdc WHERE id_simulacion='".$fila["id_simulacion"]."' and estado in (2) and id>".$fila["id_sfdc"]." ORDER BY id desc ";
		$queryFechaAsignacion=sqlsrv_query($link, $consultarFechaAsignacion);
		$resFechaAsignacion=sqlsrv_fetch_array($queryFechaAsignacion);
		$fechaAsignacion=$resFechaAsignacion["fecha_creacion"];

		$fechaRadicacion=$fila["fecha_creacion"];

		$consultarCambioEstado="SELECT TOP 1* FROM simulaciones_fdc WHERE id_simulacion='".$fila["id_simulacion"]."' and id>".$fila["id_sfdc"]." and estado=4 ORDER BY id ASC ";
		$queryCambioEstado=sqlsrv_query($link, $consultarCambioEstado);
		$resCambioEstado=sqlsrv_fetch_array($queryCambioEstado);
		$fechaCambioEstado=$resCambioEstado["fecha_creacion"];
		$consultarUsuarioCredito="SELECT * FROM usuarios WHERE id_usuario='".$resCambioEstado["id_usuario_creacion"]."'";
		$queryUsuarioCredito=sqlsrv_query($link, $consultarUsuarioCredito);
		$resUsuarioCredito=sqlsrv_fetch_array($queryUsuarioCredito);
		$analistaCredito=$resUsuarioCredito["nombre"]." ".$resUsuarioCredito["apellido"];

		$consultarSubEstado="SELECT * FROM subestados WHERE id_subestado='".$resCambioEstado["id_subestado"]."'";
		$querySubestado=sqlsrv_query($link, $consultarSubEstado);
		$resSubestado=sqlsrv_fetch_array($querySubestado);
		$subestado=$resSubestado["nombre"];
		$val_reg=1;
	}else if ($val==2){

		

		$consultarSiguienteEstado="SELECT TOP 1 * FROM simulaciones_fdc WHERE id_simulacion='".$fila["id_simulacion"]."' and id>".$fila["id_sfdc"]." ORDER BY id ASC ";
		$querySiguienteEstado=sqlsrv_query($link, $consultarSiguienteEstado, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($querySiguienteEstado)>0)
		{
			$resSiguienteEstado=sqlsrv_fetch_array($querySiguienteEstado);
			if ($resSiguienteEstado["estado"]==4)
			{
				$consultarUsuarioCredito="SELECT * FROM usuarios WHERE id_usuario='".$resSiguienteEstado["id_usuario_creacion"]."'";
				$queryUsuarioCredito=sqlsrv_query($link, $consultarUsuarioCredito);
				$resUsuarioCredito=sqlsrv_fetch_array($queryUsuarioCredito);
				$analistaCredito=$resUsuarioCredito["nombre"]." ".$resUsuarioCredito["apellido"];

				$fechaCambioEstado=$resSiguienteEstado["fecha_creacion"];

				$consultarSubEstado="SELECT * FROM subestados WHERE id_subestado='".$resSiguienteEstado["id_subestado"]."'";
				$querySubestado=sqlsrv_query($link, $consultarSubEstado);
				$resSubestado=sqlsrv_fetch_array($querySubestado);
				$subestado=$resSubestado["nombre"];
				$val_reg=1;
			}else{
				$analistaCredito="";
				$fechaCambioEstado="";
				$subestado="";
				$val_reg=0;
			}
		}else{
			$consultarUsuarioCredito="SELECT TOP 1 b.nombre, b.apellido, a.fecha_creacion FROM simulaciones_fdc a LEFT JOIN usuarios b ON a.id_usuario_asignacion = id_usuario WHERE a.id_simulacion=".$fila["id_simulacion"]." ORDER BY a.id DESC";
			$queryUsuarioCredito=sqlsrv_query($link, $consultarUsuarioCredito);
			$resUsuarioCredito=sqlsrv_fetch_array($queryUsuarioCredito);
			$analistaCredito=$resUsuarioCredito["nombre"]." ".$resUsuarioCredito["apellido"];
			$fechaCambioEstado=$resUsuarioCredito["fecha_creacion"];

			$subestado="";
			$val_reg=1;
		}

		if ($val_reg==1){
			$consultarFechaRadicacion="SELECT TOP 1* FROM simulaciones_fdc WHERE id_simulacion='".$fila["id_simulacion"]."' and estado in (1,5) and id<".$fila["id_sfdc"]." ORDER BY id desc ";
			$queryFechaRadicacion=sqlsrv_query($link, $consultarFechaRadicacion);
			$resFechaRadicacion=sqlsrv_fetch_array($queryFechaRadicacion);
			$fechaRadicacion=$resFechaRadicacion["fecha_creacion"];

			$fechaAsignacion=$fila["fecha_creacion"];
		}	
	}

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
	}


	$sin_retanqueos=0;
	$opcion_desembolso_cli=$fila["opcion_desembolso_cli"];
	$opcion_desembolso_ccc=$fila["opcion_desembolso_ccc"];
	$opcion_desembolso_cmp=$fila["opcion_desembolso_cmp"];
	$opcion_desembolso_cso=$fila["opcion_desembolso_cso"];
	$retanqueo_total=$fila["retanqueo_total"];
	switch($fila["opcion_credito"])
	{
		case "CLI":	$sin_retanqueos = $opcion_desembolso_cli; break;
		case "CCC":	$sin_retanqueos = number_format($opcion_desembolso_ccc - $retanqueo_total, 0, ".", ","); break;
		case "CMP":	$sin_retanqueos = number_format($opcion_desembolso_cmp - $retanqueo_total, 0, ".", ","); break;
		case "CSO":	$sin_retanqueos = number_format($opcion_desembolso_cso - $retanqueo_total, 0, ".", ","); break;
	}
	
	//$consultarSiguienteEstado="SELECT * FROM simulaciones_fdc where id_simulacion=".$fila["id_simulacion"]." and id>".$fila["id_sfdc"]." ORDER BY id desc limit 1"; 
	//$querySiguienteEstado=sqlsrv_query($link, $consultarSiguienteEstado);
	//if (mysql_num_rows($querySiguienteEstado)>0)
	//{
	//	$resSiguienteEstado=sqlsrv_fetch_array($querySiguienteEstado);
	//	if ($resSiguienteEstado["estado"]==4)
	//	{
		$causal_negacion="";
		if ($fila["id_causal"]!="")
		{
			$causal_negacion=$resCausal["nombre"];
		}
	
		if ($val_reg==1)
		{
			?>
			<tr>
				<td><?php echo  $fila["id_simulacion"]; ?></td>
				<td><?php echo $fila["unidad_negocio"]; ?></td>
				<td><?php echo $fila["cedula"]; ?></td>
				<td><?php echo utf8_decode($fila["nombre"]); ?></td>
				<td><?php echo utf8_decode($fila["pagaduria"]); ?></td>
				<td><?php echo utf8_decode($fila["tipo_comercial"]); ?></td>
				<td><?php echo utf8_decode($fila["nombre_comercial"]); ?></td>
				<td><?php echo utf8_decode($fila["oficina"]); ?></td>
				<td><?php echo $fila["valor_credito"]; ?></td>
				<td><?php echo $sin_retanqueos; ?></td>
				<td><?php echo $reprocesos;?></td>
				<td><?php echo $resReproceso["cuenta_reprocesos"];?></td>
				<td><?php echo $fila["subestado2"];?></td>
				<td><?php echo $causal_negacion; ?></td>
				<td><?php echo $analistaCredito; ?></td>
				<td><?php echo $fila["fecha_radicacion"]." ".$fila["hora_radicacion"]; ?></td>
				<td><?php echo $subestado; ?></td>
				
				<td><?php echo $fechaCambioEstado; ?></td>
				<td><?php echo $fechaAsignacion; ?></td>
				<td><?php echo $fechaRadicacion; ?></td>
				
			</tr>
			<?php
		}
			
		
			
	//	}
		
	//  }
?>

    <?php
}
?>
    
</table>
