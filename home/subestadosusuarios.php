<?php include ('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"]){
	exit;
}

$link = conectar();
?>

<?php include("top.php"); ?>

<script language="JavaScript">
	function chequeo_forma() {
		with (document.formato) {
			if ((id_usuario.value == "")) {
				alert("Debe seleccionar un usuario para asociarlo");
				return false;
			}		
		}
	}
</script>

<script language="JavaScript" src="../jquery-1.9.1.js"></script>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
	    <td valign="top" width="18"><a href="subestados.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo"><center><b>Asociar Usuarios</b><br><br></center></td>
	</tr>
</table>
<form name="formato" method="post" action="subestadosusuarios_crear.php" onSubmit="return chequeo_forma()">
	<input type="hidden" name="id_subestado" value="<?php echo $_REQUEST["id_subestado"] ?>">
	<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
	<table>
	<tr>
		<td>
			<div class="box1 clearfix">
				<table border="0" cellspacing=1 cellpadding=2>
					<tr>
						<td valign="bottom"><br>
							<select name="id_usuario">
								<option value=""></option>
								<?php
								$queryDB = "SELECT id_usuario, [login], nombre, apellido, tipo, subtipo from usuarios where tipo <> 'MASTER' AND tipo <> 'ADMINISTRADOR' AND estado = '1' order by nombre, apellido, id_usuario";
								$rs1 = sqlsrv_query($link,$queryDB);																
								while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
									echo ("<option value=\"".$fila1["id_usuario"]."\">".($fila1["nombre"])." ".($fila1["apellido"])."/".$fila1["tipo"]."/".$fila1["subtipo"]."</option>\n");										
								}
								
								?>
							</select>&nbsp;	
						<td valign="bottom">&nbsp;<br><input type="submit" value="Asociar Usuario"></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	</table>
</form>
<hr noshade size=1 width=350>
<br>

<?php

if ($_REQUEST["action"]){

	$queryDB = "SELECT * from subestados_usuarios su INNER JOIN usuarios us ON su.id_usuario = us.id_usuario where su.id_subestado = '".$_REQUEST["id_subestado"]."' AND us.tipo <> 'MASTER' AND us.tipo <> 'ADMINISTRADOR' order by us.nombre, us.apellido";
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
		if ($_REQUEST["chk".$fila["id"]]== "1"){
			if ($_REQUEST["action"] == "borrar"){
				sqlsrv_query($link,"delete from subestados_usuarios where id = '".$fila["id"]."'");
			}
		}
	}
}

$queryDB = "SELECT * from subestados_usuarios su INNER JOIN usuarios us ON su.id_usuario = us.id_usuario where su.id_subestado = '".$_REQUEST["id_subestado"]."' AND us.tipo <> 'MASTER' AND us.tipo <> 'ADMINISTRADOR'";

$queryDB .= " order by us.nombre, us.apellido";

$rs = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(sqlsrv_num_rows($rs)){ ?>

	<form name="formato3" method="post" action="subestadosusuarios.php">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_subestado" value="<?php echo $_REQUEST["id_subestado"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<thead>
				<tr>
					<th>Usuario</th>
					<th>Tipo</th>
					<th>Subtipo</th>
					<th>Desasociar <input type="checkbox" id="checkMarcarTodas" onclick="marcarTodas();"></th>
				</tr>
			</thead>
			<tbody id="tbodyTabla2">
				<?php
					$j = 1;
					while ($fila1 = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
						$tr_class = "";	
						if (($j % 2) == 0){
							$tr_class = " style='background-color:#F1F1F1;'";
						} ?>

						<tr <?php echo $tr_class ?>>
							<td><?php echo utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"]) ?></td>
							<td><?php echo utf8_decode($fila1["tipo"]) ?></td>
							<td><?php echo utf8_decode($fila1["subtipo"]) ?></td>
							<td align="center"><input type="checkbox" class="checkRows" name="chk<?php echo $fila1["id"] ?>" value="1"></td>
						</tr>
						
						<?php
						$j++;
					}
				?>
			</tbody>
		</table>
		<br>
		<p align="center"><input type="submit" value="Desasociar" onClick="document.formato3.action.value='borrar'"></p>
	</form>

	<script type="text/javascript">
		function marcarTodas() {
			var data = $('#tbodyTabla2 tr');
			
			if($("#checkMarcarTodas").prop('checked') == true){	
				$(data).each(function (value, index) {
					$(this).find('td input.checkRows').prop('checked', true);
				});
			}else{
				$(data).each(function (value, index) {
					$(this).find('td input.checkRows').prop('checked', false);
				});
			}
		}
	</script>

<?php
}
else{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
} 

include("bottom.php"); ?>