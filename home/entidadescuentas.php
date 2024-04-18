<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((id_banco.value == "" || tipo_cuenta.value == "" || nro_cuenta.value == "")) {
			alert("Debe seleccionar el banco, el tipo de cuenta y digitar el numero de cuenta");
			return false;
		}
		
		ReplaceComilla(nro_cuenta)
	}
}
function modificar(campobanco,campotipo,camponum) {
	with (document.formato3) {
		if (campobanco.value == "" || campotipo.value == "" || camponum.value == "") {
			alert("Debe seleccionar el banco,el tipo de cuenta y digitar el numero de cuenta");
			return false;
		}
		else {
			ReplaceComilla(camponum)
			
			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
    <td valign="top" width="18"><a href="entidades.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Cuentas Bancarias Entidad</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="entidadescuentas_crear.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_entidad" value="<?php echo $_REQUEST["id_entidad"] ?>">

<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td valign="bottom">Banco<br>
		<select name="id_banco">
			<option value=""></option>
              
              <?php

	$queryDB = "select id_banco, codigo, nombre from bancos order by nombre ";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>

			</select>&nbsp;
	<td valign="bottom">T.Cuenta<br>
		<select name="tipo_cuenta">
		    <option value=""></option>
			<option value="<?php echo $label_aho ?>"><?php echo $label_aho ?></option>
			<option value="<?php echo $label_cte ?>"><?php echo $label_cte ?></option>
		</select>&nbsp;
	</td>
	<td valign="bottom">No Cuenta<br><input type="text" name="nro_cuenta" maxlength="20" size="15"></td>
	<td valign="bottom">&nbsp;<br><input type="submit" value="Agregar"></td>
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

$queryDB = "SELECT * from entidades_cuentas su  where su.id_entidad = '".$_REQUEST["id_entidad"]."' order by su.nro_cuenta";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(sqlsrv_num_rows($rs)){

?>
<form name="formato3" method="post" action="entidadescuentas_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="">
<input type="hidden" name="id_entidad" value="<?php echo $_REQUEST["id_entidad"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Banco</th>
	<th>Tipo cuenta</th>
	<th>Numero de cuenta</th>
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
		<select name="id_banco<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>">
			              
    <?php

	$queryDB = "select * from bancos su INNER JOIN entidades_cuentas us ON su.id_banco = us.id_banco where us.id_entidadcuenta = '".$fila["id_entidadcuenta"]."'";

	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
  
  <?php

$queryDB = "select * from bancos ";
 
 $rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) 
{
	echo "<option value=\"".$fila1["id_banco"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

  ?>

			</select>
			</td>
	<td><select name="tipo_cuenta<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>">
			<option value=""></option>
			<option value="<?php echo $label_aho ?>"<?php if ($fila["tipo_cuenta"] == $label_aho) { echo " selected"; } ?>><?php echo $label_aho ?></option>
			<option value="<?php echo $label_cte ?>"<?php if ($fila["tipo_cuenta"] == $label_cte) { echo " selected"; } ?>><?php echo $label_cte ?></option>
		</select>
	</td>

	<td><input type="text" name="nro_cuenta<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nro_cuenta"])) ?>" maxlength="20" size="20"></td>
	
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>'; modificar(document.formato3.id_banco<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"])?>, document.formato3.tipo_cuenta<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"])?>, document.formato3.nro_cuenta<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>)"></td>
	<td align=center><input type=checkbox name="chk<?php echo str_replace(" ", "_", $fila["id_entidadcuenta"]) ?>" value="1"></td>
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
else{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}
?>
<?php include("bottom.php"); ?>

