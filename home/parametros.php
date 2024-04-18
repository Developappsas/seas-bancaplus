<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function modificar(campov) {
	with (document.formato3) {
		if (campov.value == "") {
			alert("Debe escribir el valor");
			return false;
		}
		else {
			ReplaceComilla(campov)

			submit();
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Par&aacute;metros</b><br><br></center></td>
</tr>
</table>
<?php

$queryDB = "SELECT * from parametros where 1 = 1";

if ($_SESSION["S_TIPO"] == "OPERACIONES") {
	$queryDB .= " AND codigo IN ('MPRCO', 'MPRFA')";
}

if (!$_SESSION["FUNC_ADJUNTOS"]) {
	$queryDB .= " AND codigo NOT IN ('UPMAX')";
}

if (!$_SESSION["FUNC_ADMINISTRATIVOS"]) {
	$queryDB .= " AND codigo NOT IN ('EDMAH', 'EDMAM')";
}

if (!$_SESSION["FUNC_MAXCONSDIARIAS"]) {
	$queryDB .= " AND codigo NOT IN ('MAXCD')";
}

if ($_SESSION["FUNC_MUESTRACAMPOS1"]) {
	$queryDB .= " AND codigo NOT IN ('CUMAN', 'DIAAJ', 'PORD5', 'PORD6', 'SEGUR', 'SAMIN')";
}

if ($_SESSION["FUNC_MUESTRACAMPOS2"]) {
	$queryDB .= " AND codigo NOT IN ('COBER', 'PODF2', 'PODF3', 'PODP1', 'PORCO', 'PORIN', 'POROR', 'POSM1', 'POSM2', 'TEFON', 'VPMSA', 'VPMSP')";
}

if (!$_SESSION["FUNC_PENSIONADOS"]) {
	$queryDB .= " AND codigo NOT IN ('PORDP', 'EDMDP')";
}

if (!$_SESSION["FUNC_TASASCOMBO"]) {
	$queryDB .= " AND codigo NOT IN ('TINTA', 'TINTB', 'TINTC')";
}else {
	$queryDB .= " AND codigo NOT IN ('TIMAX')";
}

if ($_SESSION["FUNC_TASASPLAZO"]) {
	$queryDB .= " AND codigo NOT IN ('PAVAL', 'PAVAP', 'PODF2', 'PODF3', 'PODP1', 'PORD1', 'PORD2', 'PORD3', 'TIMAX')";
}

$queryDB .= " order by nombre";

$rs = sqlsrv_query($link,$queryDB);
// Correccion

?>
<form name="formato3" method="post" action="parametros_actualizar.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="action" value="">
<input type="hidden" name="cod" value="">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Nombre</th>
	<th>Valor</th>
	<th>Modificar</th>
</tr>
<?php

$j = 1;
// Correccion
while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	$tr_class = "";
	
	if (($j % 2) == 0) {
		$tr_class = " style='background-color:#F1F1F1;'";
	}
	
	if ($fila["codigo"]=="VNUB")
	{
			

		?>

		<tr <?php echo $tr_class ?>>
			<td align="right"><b><?php echo utf8_decode($fila["nombre"]) ?></b></td>
			<td>
			<select name="v<?php echo $fila["codigo"] ?>">
			<?php
				if ($fila["valor"]==1)
				{
					?>
					
					<option value=1 selected>SI</option>
					<option value=0>NO</option>
					<?php
				}
				else{
					?>
					
					<option value=1>SI</option>
				<option value=0 selected>NO</option>
				<?php
				}
				?>
				
			</select></td>
			<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.cod.value='<?php echo $fila["codigo"] ?>'; modificar(document.formato3.v<?php echo $fila["codigo"] ?>)"></td>
		</tr>
		<?php

	}else{
		?>

<tr <?php echo $tr_class ?>>
	<td align="right"><b><?php echo utf8_decode($fila["nombre"]) ?></b></td>
	<td><input type="text" name="v<?php echo $fila["codigo"] ?>" value="<?php echo str_replace("\"", "&#34", $fila["valor"]) ?>" maxlength="255" size="25"></td>
	<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.cod.value='<?php echo $fila["codigo"] ?>'; modificar(document.formato3.v<?php echo $fila["codigo"] ?>)"></td>
</tr>
<?php
	}


	$j++;
}

?>
</table>
</form>
<?php include("bottom.php"); ?>
