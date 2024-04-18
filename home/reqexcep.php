<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PhpParser\Node\Stmt\Echo_;

include ('../functions.php'); 


if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">

function valor_tipos(x){return x.substring(0,x.indexOf('-'))}

function texto_tipos(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargartipos(reqexcep, objeto_tipos) {
	var num_tipos;
	var j, k = 1;

	num_tipos = 200;

	objeto_tipos.length = num_tipos;
<?php

$queryDB = "SELECT id_tipo, nombre from tipos_reqexcep where estado = '1' AND reqexcep = 'REQUERIMIENTO'";

$queryDB .= " order by nombre";

$datos_tipos_requerimiento = sqlsrv_query($link, $queryDB);

$padre_hija = "PHREQUERIMIENTO = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_requerimiento))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

$queryDB = "SELECT id_tipo, nombre from tipos_reqexcep where estado = '1' AND reqexcep = 'EXCEPCION'";

$queryDB .= " order by nombre";

$datos_tipos_excepcion = sqlsrv_query($link, $queryDB);

$padre_hija = "PHEXCEPCION = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_excepcion))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

?>
	switch(reqexcep) {
		case 'REQUERIMIENTO':
			num_tipos = PHREQUERIMIENTO.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHREQUERIMIENTO[j]);
				objeto_tipos.options[k].text = texto_tipos(PHREQUERIMIENTO[j]);
				k++;
			}
			break;

		case 'EXCEPCION':
			num_tipos = PHEXCEPCION.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHEXCEPCION[j]);
				objeto_tipos.options[k].text = texto_tipos(PHEXCEPCION[j]);
				k++;
			}
			break;

		default:
			num_tipos = 1;
			k=0;
	}

	objeto_tipos.selectedIndex = 0;
	objeto_tipos.length = num_tipos;

	return true;
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td class="titulo"><center><b>Requerimientos/Excepciones</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="reqexcep.php">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
			<td valign="bottom">Req/Excep<br>
				<select name="reqexcepb" onChange="Cargartipos(this.value, document.formato2.id_tipob);">
					<option value=""></option>
					<option value="REQUERIMIENTO">REQUERIMIENTO</option>
					<option value="EXCEPCION">EXCEPCION</option>
				</select>&nbsp;
			</td>
			<td valign="bottom">Tipo<br>
				<select name="id_tipob" style="width:110px">
					<option value=""></option>
				</select>&nbsp;
			</td>
<?php

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
{

?>
			<td valign="bottom">&Aacute;rea<br>
				<select name="id_areab" style="width:110px">
					<option value=""></option>
<?php

	$queryDB = "SELECT id_area, nombre from areas_reqexcep where estado = '1' order by nombre";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_area"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
				</select>&nbsp;
			</td>
<?php

}

?>
			<td valign="bottom">Estado<br>
				<select name="estadob">
					<option value=""></option>
					<option value="PENDIENTE">PENDIENTE</option>
					<option value="RESPONDIDO">RESPONDIDO</option>
				</select>&nbsp;
			</td>
			<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</form>
<?php

if ($_REQUEST["action"])
{
	if (!$_REQUEST["page"])
	{
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	
	$offset = $_REQUEST["page"] * $x_en_x;
	
    $queryDB = "SELECT re.*, si.id_analista_gestion_comercial, si.id_analista_riesgo_crediticio, si.id_analista_riesgo_operativo 
		FROM req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
			INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";
	
	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
	{
		$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
	}
	
	$queryDB .= " where re.estado != 'ANULADO'";

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
	
	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
	{
		$queryDB .= "AND (
	        CASE WHEN re.id_area = '1' THEN 
	            CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL AND si.id_analista_riesgo_crediticio = '5463' THEN 1 
	                 WHEN si.id_analista_riesgo_operativo IS NOT NULL AND si.id_analista_riesgo_operativo = '5463' THEN 1 
	                 ELSE 0 END 
	        ELSE 1 END
	    ) = 1  ";
	}
	
	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
	}
	
	if ($_REQUEST["reqexcepb"])
	{
		$reqexcepb = $_REQUEST["reqexcepb"];
		
		$queryDB .= " AND re.reqexcep = '".$reqexcepb."'";
	}
	
	if ($_REQUEST["id_tipob"])
	{
		$id_tipob = $_REQUEST["id_tipob"];
		
		$queryDB .= " AND re.id_tipo = '".$id_tipob."'";
	}
	
	if ($_REQUEST["id_areab"])
	{
		$id_areab = $_REQUEST["id_areab"];
		
		$queryDB .= " AND re.id_area = '".$id_areab."'";
	}
	
	if ($_REQUEST["estadob"])
	{
		$estadob = $_REQUEST["estadob"];
		
		$queryDB .= " AND re.estado = '".$estadob."'";
	}
	else
	{
		$queryDB .= " AND re.estado = 'PENDIENTE'";
	}
	
    $queryDB .= " order by re.id_reqexcep ASC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
    $rs = sqlsrv_query($link, $queryDB);
	
    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
        if ($_REQUEST["chk".$fila["id_reqexcep"]] == "1")
		{
            if ($_REQUEST["action"] == "borrar" && $_SESSION["S_SOLOLECTURA"] != "1" && $fila["estado"] == "PENDIENTE" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $fila["usuario_creacion"] == $_SESSION["S_LOGIN"]))
			{
                sqlsrv_query($link, "update simulaciones_observaciones set usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = getdate() where id_observacion = '".$fila["id_observacion_pregunta"]."'");
				
                sqlsrv_query($link, "update simulaciones_observaciones set usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = getdate() where id_observacion = '".$fila["id_observacion_respuesta"]."'");
				
                sqlsrv_query($link, "update req_excep set estado = 'ANULADO', usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = getdate() where id_reqexcep = '".$fila["id_reqexcep"]."'");
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

$queryDB = "SELECT re.*, si.id_analista_riesgo_crediticio, si.id_analista_riesgo_crediticio, si.id_analista_riesgo_operativo, si.cedula, si.nombre, si.nro_libranza, si.pagaduria, si.valor_credito, ti.nombre as tipo, ar.nombre as area from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_reqexcep ti ON re.id_tipo = ti.id_tipo INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
}

$queryDB .= " where re.estado != 'ANULADO'";

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

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
// 	$queryDB .= " AND (CASE re.id_area ";
// 	$queryDB .= " 	WHEN '".$area_credito."' THEN";
// 	$queryDB .= " 		CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL THEN";
// 	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
// 	$queryDB .= " 		ELSE ";
// 	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_operativo IS NOT NULL THEN";
// 	$queryDB .= " 				CASE WHEN si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
// 	$queryDB .= " 			ELSE 1 = 0 END";
// 	$queryDB .= " 		END";
// /*	$queryDB .= " 	WHEN '".$area_visado."' THEN";
// 	$queryDB .= " 		CASE WHEN si.pagaduria IN (select pa.nombre from pagadurias pa INNER JOIN pagadurias_usuarios_visado puv ON puv.id_pagaduria = pa.id_pagaduria AND puv.id_usuario = '".$_SESSION["S_IDUSUARIO"]."') THEN 1 = 1";
// 	$queryDB .= " 		ELSE 1 = 0 END";*/
// /*	$queryDB .= " 	WHEN '".$area_gestion_comercial."' THEN";
// 	$queryDB .= " 		CASE WHEN si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1";
// 	$queryDB .= " 		ELSE 1 = 0 END";*/
// 	$queryDB .= " 	ELSE 1 = 1 END";
// 	$queryDB .= " )";
	$queryDB .= "AND (
        CASE WHEN re.id_area = '1' THEN 
            CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL AND si.id_analista_riesgo_crediticio = '5463' THEN 1 
                 WHEN si.id_analista_riesgo_operativo IS NOT NULL AND si.id_analista_riesgo_operativo = '5463' THEN 1 
                 ELSE 0 END 
        ELSE 1 END
    ) = 1  ";
}

$queryDB_count = "select COUNT(*) as c from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_reqexcep ti ON re.id_tipo = ti.id_tipo INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

$queryDB_count = "select COUNT(*) as c from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_reqexcep ti ON re.id_tipo = ti.id_tipo INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB_count .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
}

$queryDB_count .= " where re.estado != 'ANULADO'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB_count .= "AND (
        CASE WHEN re.id_area = '1' THEN 
            CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL AND si.id_analista_riesgo_crediticio = '5463' THEN 1 
                 WHEN si.id_analista_riesgo_operativo IS NOT NULL AND si.id_analista_riesgo_operativo = '5463' THEN 1 
                 ELSE 0 END 
        ELSE 1 END
    ) = 1  ";
}

if ($_REQUEST["descripcion_busqueda"])
{
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	
	$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
	
	$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
}

if ($_REQUEST["reqexcepb"])
{
	$reqexcepb = $_REQUEST["reqexcepb"];
	
	$queryDB .= " AND re.reqexcep = '".$reqexcepb."'";
	
	$queryDB_count .= " AND re.reqexcep = '".$reqexcepb."'";
}

if ($_REQUEST["id_tipob"])
{
	$id_tipob = $_REQUEST["id_tipob"];
	
	$queryDB .= " AND re.id_tipo = '".$id_tipob."'";
	
	$queryDB_count .= " AND re.id_tipo = '".$id_tipob."'";
}

if ($_REQUEST["id_areab"])
{
	$id_areab = $_REQUEST["id_areab"];
	
	$queryDB .= " AND re.id_area = '".$id_areab."'";
	
	$queryDB_count .= " AND re.id_area = '".$id_areab."'";
}

if ($_REQUEST["estadob"])
{
	$estadob = $_REQUEST["estadob"];
	
	$queryDB .= " AND re.estado = '".$estadob."'";
	
	$queryDB_count .= " AND re.estado = '".$estadob."'";
}
else
{
	$queryDB .= " AND re.estado = 'PENDIENTE'";
	
	$queryDB_count .= " AND re.estado = 'PENDIENTE'";
}

$queryDB .= " order by re.id_reqexcep  OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";


$rs = sqlsrv_query($link, $queryDB);

$rs_count = sqlsrv_query($link, $queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count, SQLSRV_FETCH_ASSOC);

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
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos)
			{
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"])
			{
				echo " <a href=\"reqexcep.php?descripcion_busqueda=".$descripcion_busqueda."&reqexcepb=".$reqexcepb."&id_tipob=".$id_tipob."&id_areab=".$id_areab."&estadob=".$estadob."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
			}
			else
			{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page)
		{
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"reqexcep.php?descripcion_busqueda=".$descripcion_busqueda."&reqexcepb=".$reqexcepb."&id_tipob=".$id_tipob."&id_areab=".$id_areab."&estadob=".$estadob."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="reqexcep.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="reqexcepb" value="<?php echo $reqexcepb ?>">
<input type="hidden" name="id_tipob" value="<?php echo $id_tipob ?>">
<input type="hidden" name="id_areab" value="<?php echo $id_areab ?>">
<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Pagadur&iacute;a</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Req/Excep</th>
	<th>Tipo</th>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><th>&Aacute;rea</th><?php } ?>
	<th>F<br>Vencimiento</th>
	<th>Descripci&oacute;n</th>
	<th>Respuesta</th>
	<th>Asignado a</th>
	<th>Estado</th>
	<th><img src="../images/adjuntar.png" title="Adjuntos"></th>
	<th>Fecha,<br>Usuario</th>
	<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
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
		
		$rs1 = sqlsrv_query($link, "select * from req_excep_adjuntos where id_reqexcep = '".$fila["id_reqexcep"]."'");
		
		$mostrar_adjuntos = 0;
		
		if (sqlsrv_num_rows($rs1))
			$mostrar_adjuntos = 1;
		
?>
<tr <?php echo $tr_class ?>>
	<td style="vertical-align:top;"><?php echo $fila["cedula"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td style="vertical-align:top;" align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td style="vertical-align:top;" align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td style="vertical-align:top;"><?php echo $fila["reqexcep"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["tipo"]) ?></td>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><td style="vertical-align:top;"><?php echo utf8_decode($fila["area"]) ?></td><?php } ?>
	<td style="vertical-align:top;" align="center"><?php echo $fila["fecha_vencimiento"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode(str_replace(chr(13), "<br>", substr($fila["observacion"], 0, 100))) ?><?php if (strlen($fila["observacion"]) > 100) { ?>...<a href="#" onClick="window.open('reqexcep_leermas.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&pregunta=1', 'LEERMASPREG<?php echo $fila["id_reqexcep"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">Leer m&aacute;s</a><?php } ?></td>
	<td style="vertical-align:top;"><?php if ($fila["respuesta"]) { ?><?php if ($fila["tipo_respuesta"]) { echo "[".$fila["tipo_respuesta"]."] "; } ?><?php echo utf8_decode(str_replace(chr(13), "<br>", substr($fila["respuesta"], 0, 100))) ?><?php if (strlen($fila["respuesta"]) > 100) { ?>...<a href="#" onClick="window.open('reqexcep_leermas.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&respuesta=1', 'LEERMASRESP<?php echo $fila["id_reqexcep"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">Leer m&aacute;s</a><?php } ?><?php } else { ?><a href="reqexcep_responder.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">Responder</a><?php } ?></td>
	<td style="vertical-align:top;">
	<?php 
		if ($fila['id_analista_riesgo_crediticio'] <> null) {
			$id_analista = $fila['id_analista_riesgo_crediticio'];
		}else{
			if ( $fila['id_analista_riesgo_operativo'] <> null) {
				$id_analista = $fila['id_analista_riesgo_operativo'];
			}else{
				$id_analista = $fila["id_analista_gestion_comercial"];
			}
		}
		
		$queryDB = "select id_usuario, nombre, apellido from usuarios 
			where id_usuario = '".$id_analista."' order by nombre, apellido, id_usuario";
		$rs1 = sqlsrv_query($link, $queryDB);
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
			echo utf8_decode( $fila1["nombre"]);
		}
	?></td>
	<td style="vertical-align:top;" align="center"><?php echo $fila["estado"] ?></td>
	<td style="vertical-align:top;" align="center"><?php if ($mostrar_adjuntos) { ?><a href="reqexcep_adjuntos.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adjuntar.png" title="Adjuntos"></a><?php } else { echo "&nbsp;"; } ?></td>
	<td style="vertical-align:top;" width="65"><?php echo $fila["fecha_creacion"] ?>,<br><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center" style="vertical-align:top;"><?php if ($fila["estado"] == "PENDIENTE" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $fila["usuario_creacion"] == $_SESSION["S_LOGIN"])) { ?><input type="checkbox" name="chk<?php echo $fila["id_reqexcep"] ?>" value="1"><?php } else { echo "&nbsp;"; } ?></td><?php } ?>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
<?php

	if ($_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value = 'borrar'"></p>
<?php

	}
	
?>
</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>
