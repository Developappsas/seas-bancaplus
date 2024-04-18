<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

$id_simulacion = $_REQUEST['id_simulacion'];

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Incorporaci&oacute;n Parcial</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="incorporacion_parcial.php?id_simulacion=<?=$id_simulacion?>">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
			<?php
				$nro_libranza_sim = "";
				$query_libranza = sqlsrv_query($link, "SELECT nro_libranza FROM simulaciones WHERE id_simulacion = '".$id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if(sqlsrv_num_rows($query_libranza) > 0){
					$datos_simul = sqlsrv_fetch_array($query_libranza);
					$nro_libranza_sim = intval(preg_replace('/[^0-9]+/', '', $datos_simul["nro_libranza"]), 10);
				}
			?>
			<input type="hidden" name="nro_libranza_simulacion" value="<?=$nro_libranza_sim?>">
			<input type="hidden" name="id_simulacion" value="<?=$id_simulacion?>">
			
			<table border="0" cellspacing=1 cellpadding=2>
				<tr>
					<td valign="bottom">No. Afiliacion<br><input type="text" name="nro_afiliacion" required onBlur="ReplaceComilla(this)" size="15" maxlength="50"></td>
					<td valign="bottom">Plazo<br><input type="text" name="numero_cuotas" required onBlur="ReplaceComilla(this)" size="4" maxlength="20"></td>
					<td valign="bottom">Valor Cuota<br><input type="text" name="valor_cuota" required onBlur="ReplaceComilla(this)" size="10" maxlength="20"></td>
					<td valign="bottom">Observación<br><input type="text" name="observacion" required onBlur="ReplaceComilla(this)" size="40" maxlength="50"></td>
					<td valign="bottom">&nbsp;<br><input type="hidden" name="guardar" value="1"><input type="submit" value="Guardar"></td>
				</tr>
			</table>
		</div>
	</td>
</tr>
</table>
</form>
<?php

if ($_REQUEST["guardar"] && $_REQUEST["nro_afiliacion"] && $_REQUEST["valor_cuota"] && $_REQUEST["numero_cuotas"]){
	
	if($_REQUEST["nro_afiliacion"] != '' && $_REQUEST["valor_cuota"] != '' && $_REQUEST["numero_cuotas"] != ''){
		$nro_libranza = '';
		$consecutivo = 1;

		$query_consec = sqlsrv_query($link,"SELECT MAX(consecutivo) AS con FROM incorporaciones_parciales a WHERE a.id_simulacion = ".$id_simulacion);
		
		if($query_consec && sqlsrv_num_rows($query_consec) > 0){
			$datosCons = sqlsrv_fetch_array($query_consec);
			$consecutivo = 1 + intval($datosCons["con"]);
		}

		$query_insert = "INSERT INTO incorporaciones_parciales (id_simulacion, consecutivo, nro_libranza, nro_afiliacion, cuotas, valor_cuota, observacion, id_usuario, fecha_creacion) VALUES ('".$id_simulacion."', ".$consecutivo.", '".$_REQUEST['nro_libranza_simulacion']."', ".$_REQUEST["nro_afiliacion"].", ".$_REQUEST["numero_cuotas"].", ".$_REQUEST["valor_cuota"].", '".$_REQUEST["observacion"]."', '".$_SESSION['S_IDUSUARIO']."', GETDATE())";
		if(sqlsrv_query($link,$query_insert)){
			?>
				<script type="text/javascript"> alert("Datos guardados Satisfactoriamente"); </script>
			<?php
		}else{
			?>
				<script type="text/javascript"> alert("Error al guardar, Verificar los datos ingresados"); </script>
			<?php
		}
	}
}

if (!$_REQUEST["page"]){
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

if ($_REQUEST["id_simulacion"]){
	$queryDB = "SELECT a.*, CONCAT(a.nro_libranza, a.consecutivo) as nro_libranza, c.id_empresa FROM incorporaciones_parciales a JOIN simulaciones b ON a.id_simulacion = b.id_simulacion LEFT JOIN unidades_negocio c on c.id_unidad = b.id_unidad_negocio WHERE a.id_simulacion = '".$id_simulacion."'";
	$queryDB_count = "SELECT COUNT(*) as c FROM incorporaciones_parciales a JOIN simulaciones b ON a.id_simulacion = b.id_simulacion WHERE a.id_simulacion = '".$id_simulacion."'";
	$queryDB .= " ORDER BY a.id DESC OFFSET ".$offset." ROWS FETCH NEXT 10 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	$rs_count = sqlsrv_query($link,$queryDB_count);
	$fila_count = sqlsrv_fetch_array($rs_count);
	$cuantos = $fila_count["c"];
}

if ($cuantos){
	
	if ($cuantos > $x_en_x){
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";	
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos){
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos){
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"]){
				echo " <a href=\"incorporacion_parcial.php.php?id_simulacion=".$id_simulacion."&page=$link_page\">$i</a>";
			}
			else{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page){
			$siguiente_page = $_REQUEST["page"] + 1;
			echo " <a href=\"incorporacion_parcial.php?id_simulacion=".$id_simulacion."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
	?>
	<form name="formato3" method="post" action="incorporacion_parcial.php?id_simulacion=<?=$id_simulacion?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?=$id_simulacion ?>">
		<input type="hidden" name="buscar" value="<?=$_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?=$_REQUEST["page"] ?>">
		
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>ID</th>
				<th>No. Libranza</th>
				<th>No. Afiliación</th>
				<th>Vr Cr&eacute;dito</th>
				<th>No. Cuotas</th>
				<th>Valor Cuota</th>
				<th>Observaci&oacute;n</th>
				<th>Ver</th>
				<th>Pagare</th>
				<th>Plantilla</th>
			</tr>
			<?php

				$j = 1;
				
				while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
					$tr_class = "";
					
					if (($j % 2) == 0){
						$tr_class = " style='background-color:#F1F1F1;'";
					} 
					if($fila["id_empresa"] == 1){
						$ruta = "../formatos/incorporacion/kredit_formato_incorporacion_parcial.php?id=".$fila['id'];
						$ruta2 = "../formatos/incorporacion/kredit_formato_pagare_libranza_incorporacion_parcial.php?id=".$fila['id'];
						$ruta3 = "../formatos/incorporacion/kredit_formato_plantilla_incorporacion_parcial.php?id=".$fila['id'];
					}else{
						$ruta = "../formatos/incorporacion/fianti_formato_incorporacion_parcial.php?id=".$fila['id'];
						$ruta2 = "../formatos/incorporacion/fianti_formato_pagare_libranza_incorporacion_parcial.php?id=".$fila['id'];
						$ruta3 = "../formatos/incorporacion/fianti_formato_plantilla_incorporacion_parcial.php?id=".$fila['id'];
					}
					$valor_credito = intval($fila["cuotas"]) * intval($fila["valor_cuota"]); 
					?>
					<tr <?=$tr_class ?>>
						<td><?=$fila["id"] ?></td>
						<td align="center"><?=$fila["nro_libranza"] ?></td>
						<td align="center"><?=$fila["nro_afiliacion"] ?></td>
						<td align="right">$ <?=number_format($valor_credito, 0) ?></td>
						<td align="center"><?=$fila["cuotas"] ?></td>
						<td align="right"><?=number_format($fila["valor_cuota"], 0) ?></td>
						<td><?=utf8_decode($fila["observacion"]) ?></td>
						<td align="center"><a target="_blank" href="<?=$ruta?>"><img src="../images/archivo.png" title="Abrir Pagare/Libranza/Plantilla"></a></td>
						<td align="center"><a target="_blank" href="<?=$ruta2?>"><img src="../images/archivo.png" title="Abrir Pagare/Libranza"></a></td>
						<td align="center"><a target="_blank" href="<?=$ruta3?>"><img src="../images/archivo.png" title="Abrir Plantilla Descuentos"></a></td>
					</tr>
					<?php
					$j++;
				}
			?>
		</table>
		<br>
	</form>
	<?php
}
else{
	if ($_REQUEST["buscar"]) { $mensaje = "No se encontraron registros"; }
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

include("bottom.php");
?>