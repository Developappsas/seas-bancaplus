<?php
include('../functions.php');


if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((codigo.value == "") || (nombre.value == "")) {
				alert("Debe digitar el codigo y el nombre");
				return false;
			}

			ReplaceComilla(nombre)
		}
	}

	function modificar(campoc, campon) {
		with(document.formato3) {
			if ((campoc.value == "") || (campon.value == "")) {
				alert("Debe digitar el codigo y el nombre");
				return false;
			} else {
				ReplaceComilla(campon)

				submit();
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Bancos</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="bancos_crear.php" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">C&oacute;digo<br><input type="text" name="codigo" maxlength="2" size="3" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
							<td valign="bottom">Nombre<br><input type="text" name="nombre" maxlength="255" size="50"></td>
							<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Banco"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="bancos.php">
	<table>
		<tr>
			<td>
				<div class="box1 oran clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
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

if (!$_REQUEST["page"]) {
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "SELECT * from bancos where id_banco IS NOT NULL";

$queryDB_count = "SELECT COUNT(*) as c from bancos where id_banco IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"]) {
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

	$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";

	$queryDB_count = $queryDB_count . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
}

$queryDB .= " order by nombre DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

$rs = sqlsrv_query($link, $queryDB);

$rs_count = sqlsrv_query($link, $queryDB_count);

$fila_count = sqlsrv_fetch_array($rs_count);

$cuantos = $fila_count["c"];

if ($cuantos) {
	if ($cuantos > $x_en_x) {
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";

		$i = 1;
		$final = 0;

		while ($final < $cuantos) {
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);

			if ($final > $cuantos) {
				$final = $cuantos;
			}

			if ($link_page != $_REQUEST["page"]) {
				echo " <a href=\"bancos.php?descripcion_busqueda=" . $descripcion_busqueda . "&page=$link_page\">$i</a>";
			} else {
				echo " " . $i;
			}

			$i++;
		}

		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;

			echo " <a href=\"bancos.php?descripcion_busqueda=" . $descripcion_busqueda . "&page=" . $siguiente_page . "\">Siguiente</a></p></td></tr>";
		}

		echo "</table><br>";
	}

?>
	<form name="formato3" method="post" action="bancos_actualizar.php">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>C&oacute;digo</th>
				<th>Nombre</th>
				<th>Modificar</th>
				<th>Borrar</th>
			</tr>
			<?php

			$j = 1;

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

			?>
				<tr <?php echo $tr_class ?>>
					<td><input type="text" name="c<?php echo str_replace(" ", "_", $fila["id_banco"]) ?>" value="<?php echo $fila["codigo"] ?>" maxlength="2" size="1" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
					<td><input type="text" name="n<?php echo str_replace(" ", "_", $fila["id_banco"]) ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?>" maxlength="255" size="50"></td>
					<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_banco"] ?>'; modificar(document.formato3.c<?php echo $fila["id_banco"] ?>, document.formato3.n<?php echo $fila["id_banco"] ?>)"></td>
					<td align=center><input type=checkbox name="b<?php echo $fila["id_banco"] ?>" value="1"></td>
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