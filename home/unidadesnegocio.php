<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR"){
	exit;
}
$link = conectar();
?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((nombre.value == "") || (valor_por_millon_seguro_activos.value == "") || (valor_por_millon_seguro_pensionados.value == "") || (valor_por_millon_seguro_colpensiones.value == "")) {
			alert("Todos los campos son obligatorios");
			return false;
		}
	}
}

function modificar(campon, campova, campovp, campovc) {
	with (document.formato3) {
		if (campon.value == "" || campova.value == "" || campovp.value == "" || campovc.value == "") {
			alert("Debe digitar nombre, valor por millon del seguro para activos, pensionados y Colpensiones");
			return false;
		}
		else {
			submit();
		}
	}
}

function escogerPrefijo(idSelect, elementEmpresa){
	if(elementEmpresa.value == 1){
		prefijo = 'EFEC';
	}else if(elementEmpresa.value == 2){
		prefijo = 'FIANT';
	}

	document.getElementById(idSelect).value = prefijo;
}

</script>

<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo"><center><b>Unidades de Negocio</b><br><br></center></td>
	</tr>
</table>

<form name=formato method=post action="unidadesnegocio_crear.php" onSubmit="return chequeo_forma()">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom">Empresa<br> 
		<select onchange="escogerPrefijo('prefijo_libranza', this);" name="empresa" style="margin: 2px 10px 6px 0; width: 100px;">
			<option value=""></option>
			<?php 
				$arrayEmpresas = array();
				$rsEmpresas = sqlsrv_query($link, "SELECT * from empresas", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));				
				if($rsEmpresas && @sqlsrv_num_rows($rsEmpresas)){
					while ($fila = sqlsrv_fetch_array($rsEmpresas)){ 
						$arrayEmpresas[] = $fila; ?>
						<option value="<?=$fila['id_empresa']?>"><?=$fila['nombre_corto']?></option>
						<?php
					}
				}
			?>
		</select>
	</td>
	<td valign="bottom">Prefijo<br><input type="text" id="prefijo_libranza" name="prefijo_libranza" maxlength="10" size="8" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">Nombre<br><input type="text" name="nombre" maxlength="255" size="50" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>

	<td valign="bottom">Seguro Activos<br><input type="text" name="valor_por_millon_seguro_activos" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">PARCIAL<br><input type="text" name="valor_por_millon_seguro_activos_parcial" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td valign="bottom">Seguro Pensionados<br><input type="text" name="valor_por_millon_seguro_pensionados" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">PARCIAL<br><input type="text" name="valor_por_millon_seguro_pensionados_parcial" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td valign="bottom">Seguro Colpensiones<br><input type="text" name="valor_por_millon_seguro_colpensiones" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td valign="bottom">PARCIAL<br><input type="text" name="valor_por_millon_seguro_colpensiones_parcial" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td valign="bottom" align="center">GMF<br><input type="checkbox" name="gmf" value="1"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Unidad"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="unidadesnegocio.php">
<table>
<tr>
<td>
<div class="box1 oran clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td>
		Empresa<br>
		<select name="empresa_busqueda" style="margin: 2px 10px 6px 0; width: 100px;">
			<option selected value="">TODAS</option>
			<?php 
				foreach ($arrayEmpresas as $emp){  ?>
					<option value="<?=$emp['id_empresa']?>"><?=$emp['nombre_corto']?></option>
					<?php
				}
			?>
		</select>
	</td>
	<td valign="bottom">Nombre<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Buscar"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<?php
if (!$_REQUEST["page"]){
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;
$offset = $_REQUEST["page"] * $x_en_x;
$queryDB = "select * from unidades_negocio where id_unidad IS NOT NULL";
$queryDB_count = "select COUNT(*) as c from unidades_negocio where id_unidad IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"]){
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
	$queryDB = $queryDB." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
	$queryDB_count = $queryDB_count." AND UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%'";
}

if ($_REQUEST["empresa_busqueda"]){
	$empresa_busqueda = $_REQUEST["empresa_busqueda"];
	$queryDB = $queryDB." AND id_empresa = ".$empresa_busqueda;
	$queryDB_count = $queryDB_count." AND id_empresa = ".$empresa_busqueda;
}

$queryDB .= " order by id_unidad OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

$rs = sqlsrv_query($link,$queryDB);
$rs_count = sqlsrv_query($link,$queryDB_count);
$fila_count = sqlsrv_fetch_array($rs_count);
$cuantos = $fila_count["c"];

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
				echo " <a href=\"unidadesnegocio.php?descripcion_busqueda=".$descripcion_busqueda."&page=$link_page\">$i</a>";
			}
			else{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page){
			$siguiente_page = $_REQUEST["page"] + 1;
			echo " <a href=\"unidadesnegocio.php?descripcion_busqueda=".$descripcion_busqueda."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
?>
<form name="formato3" method="post" action="unidadesnegocio_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Empresa</th>
	<th>Prefijo</th>
	<th>Nombre</th>
	<th>Seguro Activos</th>
	<th>Seguro Activos PARCIAL</th>
	<th>Seguro Pensionados</th>
	<th>Seguro Pensionados PARCIAL</th>
	<th>Seguro Colpensiones</th>
	<th>Seguro Colpensiones PARCIAL</th>
	<th>GMF</th>
	<th>Activa</th>
	<th>Modificar</th>
	<th>Borrar</th>
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
		
?>
<tr <?php echo $tr_class ?>>

	<td>
		<select name="empresa<?php echo $fila["id_unidad"] ?>" onchange="escogerPrefijo('prefijo_libranza<?php echo $fila["id_unidad"] ?>', this);" style="margin: 2px 10px 6px 0; width: 100px;">
			<option value=""></option>
			<?php 
				foreach ($arrayEmpresas as $emp){ 
					if($fila["id_empresa"] == $emp['id_empresa']){ ?>
						<option selected value="<?=$emp['id_empresa']?>"><?=$emp['nombre_corto']?></option>
						<?php
					}else{ ?>
						<option value="<?=$emp['id_empresa']?>"><?=$emp['nombre_corto']?></option>
						<?php
					}
				}
			?>
		</select>
	</td>
	<td valign="bottom"><input type="text" id="prefijo_libranza<?php echo $fila["id_unidad"] ?>" name="prefijo_libranza<?php echo $fila["id_unidad"] ?>" maxlength="10" size="8" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}" value="<?php echo $fila["prefijo_libranza"] ?>"></td>

	<td><input type="text" name="nombre<?php echo $fila["id_unidad"] ?>" value="<?php echo $fila["nombre"] ?>" maxlength="255" size="50" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></td>

	<td><input type="text" name="valor_por_millon_seguro_activos<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_activos"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="valor_por_millon_seguro_activos_parcial<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_activos_parcial"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td><input type="text" name="valor_por_millon_seguro_pensionados<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_pensionados"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="valor_por_millon_seguro_pensionados_parcial<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_pensionados_parcial"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td><input type="text" name="valor_por_millon_seguro_colpensiones<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_colpensiones"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
	<td><input type="text" name="valor_por_millon_seguro_colpensiones_parcial<?php echo $fila["id_unidad"] ?>" value="<?php echo round($fila["valor_por_millon_seguro_colpensiones_parcial"]) ?>" maxlength="10" size="10" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>

	<td align="center"><input type="checkbox" name="gmf<?php echo $fila["id_unidad"] ?>" value="1"<?php if ($fila["gmf"]) { echo " checked"; } ?>></td>
	<td align="center"><input type="checkbox" name="a<?php echo $fila["id_unidad"] ?>" value="1"<?php if ($fila["estado"]) { echo " checked"; } ?>></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_unidad"] ?>'; modificar(document.formato3.nombre<?php echo $fila["id_unidad"] ?>, document.formato3.valor_por_millon_seguro_activos<?php echo $fila["id_unidad"] ?>, document.formato3.valor_por_millon_seguro_pensionados<?php echo $fila["id_unidad"] ?>, document.formato3.valor_por_millon_seguro_colpensiones<?php echo $fila["id_unidad"] ?>)"></td>
	<td align=center><input type=checkbox name="b<?php echo $fila["id_unidad"] ?>" value="1"></td>
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

?>
<?php include("bottom.php"); ?>
