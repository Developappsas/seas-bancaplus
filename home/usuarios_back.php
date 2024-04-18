<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
} = conectar();

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Usuarios</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="usuarios.php">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td><a href="usuarios_crear.php">Crear Usuario</a></td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="usuarios.php">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Nombre/Apellido/Email<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<?php

if ($_REQUEST["action"] == "borrar")
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
	$queryDB = "select id_usuario, nombre, apellido, login from usuarios where tipo <> 'MASTER'";
	
	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR")
	{
		$queryDB .= " AND tipo <> 'ADMINISTRADOR'";
	}
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (login like '%".utf8_encode(($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(apellido) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(email) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB .= " order by nombre OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["b".$fila["id_usuario"]] == "1")
		{
			$existe_en_simulaciones = sqlsrv_query($link, "select usuario_creacion from simulaciones where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."' OR usuario_aprobprep = '".$fila["login"]."' OR usuario_creacionprep = '".$fila["login"]."' OR usuario_prospeccion = '".$fila["login"]."' OR usuario_radicado = '".$fila["login"]."' OR usuario_desistimiento = '".$fila["login"]."' OR usuario_incorporacion = '".$fila["login"]."' OR usuario_validacion = '".$fila["login"]."' OR usuario_firmado = '".$fila["login"]."' OR id_comercial = '".$fila["id_usuario"]."' OR id_analista_riesgo_operativo = '".$fila["id_usuario"]."' OR id_analista_riesgo_crediticio = '".$fila["id_usuario"]."' OR id_analista_gestion_comercial = '".$fila["id_usuario"]."'");
			
			$existe_en_simulaciones_observaciones = sqlsrv_query($link, "select usuario_creacion from simulaciones_observaciones where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_simulaciones_subestados = sqlsrv_query($link, "select usuario_creacion from simulaciones_subestados where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_simulaciones_visado = sqlsrv_query($link, "select usuario_creacion from simulaciones_visado where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_simulaciones_incorporacion = sqlsrv_query($link, "select usuario_creacion from simulaciones_incorporacion where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_simulaciones_primeracuota = sqlsrv_query($link, "select usuario_creacion from simulaciones_primeracuota where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_adjuntos = sqlsrv_query($link, "select usuario_creacion from adjuntos where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_log_consultas = sqlsrv_query($link, "select id_usuario from log_consultas where id_usuario = '".$fila["id_usuario"]."'");
			
			$existe_en_entidades_cuentas = sqlsrv_query($link, "select usuario_creacion from entidades_cuentas where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_tesoreria_cc = sqlsrv_query($link, "select usuario_creacion from tesoreria_cc where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_firma_cheque = '".$fila["login"]."'");
			
			$existe_en_giros = sqlsrv_query($link, "select 	usuario_creacion from giros where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_pagos = sqlsrv_query($link, "select usuario_creacion from pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_pagos_detalle = sqlsrv_query($link, "select usuario_anulacion from pagos_detalle where usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_recaudosplanos = sqlsrv_query($link, "select usuario_creacion from recaudosplanos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_recaudosplanos_detalle = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas = sqlsrv_query($link, "select usuario_creacion from ventas where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_ventas_detalle_documentos = sqlsrv_query($link, "select usuario_modificacion from ventas_detalle_documentos where usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagos = sqlsrv_query($link, "select usuario_creacion from ventas_pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosdetalle = sqlsrv_query($link, "select usuario_anulacion from ventas_pagosdetalle where usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosplanos = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosplanos_detalle = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_cuotas_fondeador = sqlsrv_query($link, "select usuario_creacion from ventas_cuotas_fondeador where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_planoscuotasfondeador = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_planoscuotasfondeador_detalle = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_servicio_cliente = sqlsrv_query($link, "select usuario_creacion from servicio_cliente where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_gestion_cobro = sqlsrv_query($link, "select usuario_creacion from gestion_cobro where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_req_excep = sqlsrv_query($link, "select usuario_creacion from req_excep where usuario_creacion = '".$fila["login"]."' OR usuario_respuesta = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_req_excep_adjuntos = sqlsrv_query($link, "select usuario_creacion from req_excep_adjuntos where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_pagadurias_visado = sqlsrv_query($link, "select id_usuario from pagadurias_usuarios_visado where id_usuario = '".$fila["id_usuario"]."'");
			
			$existe_en_bolsainc_pagos = sqlsrv_query($link, "select usuario_creacion from bolsainc_pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_bolsainc_aplicaciones = sqlsrv_query($link, "select usuario_creacion from bolsainc_aplicaciones where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_consultas_externas = sqlsrv_query($link, "select usuario_creacion from consultas_externas where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_unidades_negocio = sqlsrv_query($link, "select usuario_creacion from unidades_negocio where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_usuarios_unidades = sqlsrv_query($link, "select usuario_creacion from usuarios_unidades where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_tasas2_unidades = sqlsrv_query($link, "select usuario_creacion from tasas2_unidades where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_tasas2_unidades_privado = sqlsrv_query($link, "select usuario_creacion from tasas2_unidades_privado where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_empleados_creacion = sqlsrv_query($link, "select id_usuario from empleados_creacion where id_usuario = '".$fila["id_usuario"]."'");
			
			$existe_en_simulaciones_ext = sqlsrv_query($link, "select usuario_creacion from simulaciones_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."' OR usuario_aprobprep = '".$fila["login"]."' OR usuario_creacionprep = '".$fila["login"]."'");
			
			$existe_en_pagos_ext = sqlsrv_query($link, "select usuario_creacion from pagos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_pagos_detalle_ext = sqlsrv_query($link, "select usuario_anulacion from pagos_detalle_ext where usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_recaudosplanos_ext = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_recaudosplanos_detalle_ext = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_ext = sqlsrv_query($link, "select usuario_creacion from ventas_ext where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_ventas_detalle_documentos_ext = sqlsrv_query($link, "select usuario_modificacion from ventas_detalle_documentos_ext where usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagos_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosdetalle_ext = sqlsrv_query($link, "select usuario_anulacion from ventas_pagosdetalle_ext where usuario_anulacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosplanos_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_pagosplanos_detalle_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_ventas_cuotas_fondeador_ext = sqlsrv_query($link, "select usuario_creacion from ventas_cuotas_fondeador_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_planoscuotasfondeador_ext = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_planoscuotasfondeador_detalle_ext = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'");
			
			$existe_en_gestion_cobro_ext = sqlsrv_query($link, "select usuario_creacion from gestion_cobro_ext where usuario_creacion = '".$fila["login"]."'");
			
			$existe_en_usuarios = sqlsrv_query($link, "select usuario_creacion from usuarios where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_inactivacion = '".$fila["login"]."'");
			
			if (sqlsrv_num_rows($existe_en_simulaciones) || sqlsrv_num_rows($existe_en_simulaciones_observaciones) || sqlsrv_num_rows($existe_en_simulaciones_subestados) || sqlsrv_num_rows($existe_en_simulaciones_visado) || sqlsrv_num_rows($existe_en_simulaciones_incorporacion) || sqlsrv_num_rows($existe_en_simulaciones_primeracuota) || sqlsrv_num_rows($existe_en_adjuntos) || sqlsrv_num_rows($existe_en_log_consultas) || sqlsrv_num_rows($existe_en_entidades_cuentas) || sqlsrv_num_rows($existe_en_tesoreria_cc) || sqlsrv_num_rows($existe_en_giros) || sqlsrv_num_rows($existe_en_pagos) || sqlsrv_num_rows($existe_en_pagos_detalle) || sqlsrv_num_rows($existe_en_recaudosplanos) || sqlsrv_num_rows($existe_en_recaudosplanos_detalle) || sqlsrv_num_rows($existe_en_ventas) || sqlsrv_num_rows($existe_en_ventas_detalle_documentos) || sqlsrv_num_rows($existe_en_ventas_pagos) || sqlsrv_num_rows($existe_en_ventas_pagosdetalle) || sqlsrv_num_rows($existe_en_ventas_pagosplanos) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_detalle) || sqlsrv_num_rows($existe_en_ventas_cuotas_fondeador) || sqlsrv_num_rows($existe_en_planoscuotasfondeador) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_detalle) || sqlsrv_num_rows($existe_en_servicio_cliente) || sqlsrv_num_rows($existe_en_gestion_cobro) || sqlsrv_num_rows($existe_en_req_excep) || sqlsrv_num_rows($existe_en_req_excep_adjuntos) || sqlsrv_num_rows($existe_en_pagadurias_visado) || sqlsrv_num_rows($existe_en_bolsainc_pagos) || sqlsrv_num_rows($existe_en_bolsainc_aplicaciones) || sqlsrv_num_rows($existe_en_consultas_externas) || sqlsrv_num_rows($existe_en_unidades_negocio) || sqlsrv_num_rows($existe_en_usuarios_unidades) || sqlsrv_num_rows($existe_en_tasas2_unidades) || sqlsrv_num_rows($existe_en_tasas2_unidades_privado) || sqlsrv_num_rows($existe_en_empleados_creacion) || sqlsrv_num_rows($existe_en_simulaciones_ext) || sqlsrv_num_rows($existe_en_pagos_ext) || sqlsrv_num_rows($existe_en_pagos_detalle_ext) || sqlsrv_num_rows($existe_en_recaudosplanos_ext) || sqlsrv_num_rows($existe_en_recaudosplanos_detalle_ext) || sqlsrv_num_rows($existe_en_ventas_ext) || sqlsrv_num_rows($existe_en_ventas_detalle_documentos_ext) || sqlsrv_num_rows($existe_en_ventas_pagos_ext) || sqlsrv_num_rows($existe_en_ventas_pagosdetalle_ext) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_ext) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_detalle_ext) || sqlsrv_num_rows($existe_en_ventas_cuotas_fondeador_ext) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_ext) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_detalle_ext) || sqlsrv_num_rows($existe_en_gestion_cobro_ext) || sqlsrv_num_rows($existe_en_usuarios))
			{
				echo "<script>alert('El usuario ".utf8_decode($fila["nombre"])." ".utf8_decode($fila["apellido"])." no puede ser borrado (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link, "delete from subestados_usuarios where id_usuario = '".$fila["id_usuario"]."'");
				
				sqlsrv_query($link, "delete from oficinas_usuarios where id_usuario = '".$fila["id_usuario"]."'");
				
				sqlsrv_query($link, "delete from usuarios_unidades where id_usuario = '".$fila["id_usuario"]."'");
				
				sqlsrv_query($link, "delete from usuarios where id_usuario = '".$fila["id_usuario"]."'");
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

$queryDB = "select * from usuarios where tipo <> 'MASTER'";

$queryDB_count = "select COUNT(*) as c from usuarios where tipo <> 'MASTER'";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	$queryDB .= " AND tipo <> 'ADMINISTRADOR'";
	
	$queryDB_count .= " AND tipo <> 'ADMINISTRADOR'";
}

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (login like '%".utf8_encode(($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(apellido) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(email) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	
	$queryDB_count .= " AND (login like '%".utf8_encode(($descripcion_busqueda))."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(apellido) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR UPPER(email) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
}

$queryDB .= " order by nombre LIMIT ".$x_en_x." OFFSET ".$offset;

$rs = sqlsrv_query($queryDB);

$rs_count = sqlsrv_query($queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if ($cuantos)
{
	if ($cuantos > $x_en_x)
	{
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
		
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos)
		{
	_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos)
			{
				$final = $cuantos;
			}
			
			if_page != $_REQUEST["page"])
				{
				echo " <a href=\"usuarios.php?descripcion_busqueda=".$descripcion_busqueda."&pag_page\">$i</a>";
			}
			else
			{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] !_page)
		{
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"usuarios.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="usuarios.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Nombre</th>
	<th>Usuario</th>
	<th>E-Mail</th>
	<th>Tipo</th>
	<th>Sector</th>
	<!--<th>Unidades de Negocio</th>-->
	<th>Creaci&oacute;n</th>
	<th>Inactivaci&oacute;n</th>
	<th>Estado</th>
	<th>&nbsp;</th>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
		if ($fila["subtipo"] == "ANALISTA_REFERENCIA")
			$fila["subtipo"] = "ANALISTA REFERENCIACION";
		
		if ($fila["subtipo"] == "ANALISTA_GEST_COM")
			$fila["subtipo"] = "ANALISTA GESTION COMERCIAL";
		
		if ($fila["subtipo"] == "ANALISTA_VEN_CARTERA")
			$fila["subtipo"] = "ANALISTA VENTA CARTERA";
		
		if ($fila["subtipo"] == "ANALISTA_BD")
			$fila["subtipo"] = "ANALISTA BASE DE DATOS";
		
		if ($fila["subtipo"] == "COORD_PROSPECCION")
			$fila["subtipo"] = "COORDINADOR PROSPECCION";
		
		if ($fila["subtipo"] == "COORD_VISADO")
			$fila["subtipo"] = "COORDINADOR VISADO";
		
		if ($fila["subtipo"] == "COORD_CREDITO")
			$fila["subtipo"] = "COORDINADOR CREDITO";
		
		if ($fila["tipo"] == "GERENTECOMERCIAL")
			$fila["tipo"] = "GERENTE REGIONAL";
		
		if ($fila["tipo"] == "DIRECTOROFICINA")
			$fila["tipo"] = "DIRECTOR OFICINA";
		
		if ($fila["tipo"] == "CARTERA")
			$fila["tipo"] = "DIRECTOR DE CARTERA";
		
		if ($fila["tipo"] == "OPERACIONES")
			$fila["tipo"] = "DIRECTOR DE OPERACIONES";
				
		$i = 0;
		
		$unidades_asociadas = "";
		
		$queryDB = "select un.nombre from usuarios_unidades uu INNER JOIN unidades_negocio un ON uu.id_unidad_negocio = un.id_unidad where uu.id_usuario = '".$fila["id_usuario"]."' order by un.id_unidad";

		$rs1 = sqlsrv_query($queryDB);

		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
		{
			if ($i)
				$unidades_asociadas .= ", ";
			
			$unidades_asociadas .= utf8_decode($fila1["nombre"]);
			
			$i++;
		}
		
		switch ($fila["estado"])
		{
			case '1':	$estado = "ACTIVO"; break;
			case '0':	$estado = "INACTIVO"; break;
		}
		
?>
<tr <?php echo $tr_class ?>>
	<td><a href="usuarios_actualizar.php?id_usuario=<?php echo $fila["id_usuario"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo utf8_decode($fila["nombre"])." ".utf8_decode($fila["apellido"]) ?></a></td>
	<td align="center">  <?php echo "  ".$fila["login"]."  " ?></td>
	<td><a href="mailto:<?php echo utf8_decode($fila["email"]) ?>"><?php echo utf8_decode($fila["email"]) ?></a></td>
	<td align="center"><?php echo $fila["tipo"] ?><?php if ($fila["subtipo"]) { echo "/".$fila["subtipo"]; } ?></td>
	<td align="center"><?php echo $fila["sector"] ?></td>
	<!--<td align="center"><?php //if ($unidades_asociadas) { echo $unidades_asociadas; } else { echo "&nbsp;"; } ?></td>-->
	<td align="center"><?php echo $fila["fecha_creacion"] ?><br><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<td align="center"><?php echo $fila["fecha_inactivacion"] ?><br><?php echo utf8_decode($fila["usuario_inactivacion"]) ?></td>
	<td align="center"><?php echo $estado ?></td>
	<td align="center"><input type="checkbox" name="b<?php echo $fila["id_usuario"] ?>" value="1"></td>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value='borrar'"></p>
</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
