<?php
include('../functions.php');

$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR" || !$_SESSION["FUNC_CARGUEPLANOS"]) {
	exit;
}

?>
<?php include("top.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((bas.value == "") && (loc.value == "") && (nac.value == "") && (emb.value == "") && (des.value == "") && (rec.value == "") <?php if ($_SESSION["FUNC_INDICADORES"]) { ?> && (met.value == "") <?php } ?><?php if ($_SESSION["FUNC_AGENDA"]) { ?> && (ten.value == "") <?php } ?><?php if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["S_MASTER"]) { ?> && (car.value == "") && (pag.value == "") && (ven.value == "") <?php } ?>) {
				alert("No esta cargando ningun archivo");
				return false;
			}
			if ((bas.value != "" && pagaduriabas.selectedIndex == 0) || (loc.value != "" && pagadurialoc.selectedIndex == 0) || (nac.value != "" && pagadurianac.selectedIndex == 0) || (emb.value != "" && pagaduriaemb.selectedIndex == 0) || (des.value != "" && pagaduriades.selectedIndex == 0) || (rec.value != "" && pagaduriarec.selectedIndex == 0)) {
				alert("Debe seleccionar la pagadur�a");
				return false;
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Cargar Planos</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="cargarplanos2.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table border="0" cellspacing=1 cellpadding=2 class="tab1">
		<tr>
			<th>Nombre</th>
			<th>Archivo</th>
			<th colspan="2">Pagaduria</th>
		</tr>
		<tr>
			<td align="right"><b>Datos B&aacute;sicos</b></td>
			<td><input type="file" name="bas"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagaduriabas">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Localizaci&oacute;n</b></td>
			<td><input type="file" name="loc"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagadurialoc">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Fechas Nacimiento</b></td>
			<td><input type="file" name="nac"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagadurianac">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Embargos</b></td>
			<td><input type="file" name="emb"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagaduriaemb">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Descuentos</b></td>
			<td><input type="file" name="des"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagaduriades">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Rechazos</b></td>
			<td><input type="file" name="rec"></td>
			<td>Pagadur&iacute;a</td>
			<td><select name="pagaduriarec">
					<option value=""></option>
					<?php

					$queryDB = "SELECT DISTINCT pagaduria from empleados where pagaduria IS NOT NULL AND pagaduria <> '' order by pagaduria";

					$rs1 = sqlsrv_query($link, $queryDB);

					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
					}

					?>
				</select>
			</td>
		</tr>
		<?php

		if ($_SESSION["FUNC_INDICADORES"]) {

		?>
			<tr>
				<td align="right"><b>Metas Asesores</b></td>
				<td><input type="file" name="met"></td>
			</tr>
		<?php

		}

		if ($_SESSION["FUNC_AGENDA"]) {

		?>
			<tr>
				<td align="right"><b>Tiempos Entidades</b></td>
				<td><input type="file" name="ten"></td>
			</tr>
		<?php

		}

		if ($_SESSION["FUNC_FULLSYSTEM"] && $_SESSION["S_MASTER"]) {

		?>
			<tr>
				<td align="right"><b>Cartera</b></td>
				<td><input type="file" name="car"></td>
			</tr>
			<tr>
				<td align="right"><b>Pagos</b></td>
				<td><input type="file" name="pag"></td>
			</tr>
			<tr>
				<td align="right"><b>Ventas</b></td>
				<td><input type="file" name="ven"></td>
			</tr>
		<?php

		}

		?>
		<tr>
			<td colspan="4" align="center"><br><input type="submit" value="Cargar"></td>
		</tr>
	</table>
</form>
<?php include("bottom.php"); ?>