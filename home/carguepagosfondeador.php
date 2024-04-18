<?php
include('../functions.php');
include('../function_blob_storage.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((descripcion.value == "") || (archivo.value == "")) {
				alert("Debe digitar una descripcion y seleccionar el archivo plano");
				return false;
			}

			ReplaceComilla(descripcion)
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="ventas.php?ext=<?php echo $_REQUEST["ext"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Cargue Pagos Fondeador</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="carguepagosfondeador_crear.php?ext=<?php echo $_REQUEST["ext"] ?>" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Descripci&oacute;n<br><input type="text" name="descripcion" maxlength="255" size="50" style="background-color:#EAF1DD;"></td>
							<td valign="bottom">Archivo Plano<br><input type="file" name="archivo" style="background-color:#EAF1DD;">&nbsp;</td>
							<td valign="bottom" align="center">Cargar sin Previsualizar<br><input type="checkbox" name="sin_previsualizar" value="1" style="background-color:#EAF1DD;">&nbsp;</td>
							<td valign="bottom">&nbsp;<br><input type="submit" value="Cargar archivo"></td>
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

if (!$_REQUEST["page"]) {
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

$queryDB = "select * from planoscuotasfondeador" . $sufijo . " where procesado = '1'";

$queryDB_count = "select COUNT(*) as c from planoscuotasfondeador" . $sufijo . " where procesado = '1'";

$queryDB .= " order by id_planocuotafondeador DESC OFFSET ".$offset." ROWS";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


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
				echo " <a href=\"carguepagosfondeador.php?ext=" . $_REQUEST["ext"] . "&page=$link_page\">$i</a>";
			} else {
				echo " " . $i;
			}

			$i++;
		}

		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;

			echo " <a href=\"carguepagosfondeador.php?ext=" . $_REQUEST["ext"] . "&page=" . $siguiente_page . "\">Siguiente</a></p></td></tr>";
		}

		echo "</table><br>";
	}

?>
	<form name="formato3" method="post" action="carguepagosfondeador.php?ext=<?php echo $_REQUEST["ext"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>Descripci&oacute;n</th>
				<th>Archivo Plano</th>
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
					<td><a href="carguepagosfondeador_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>&id_planocuotafondeador=<?php echo $fila["id_planocuotafondeador"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo utf8_decode($fila["descripcion"]) ?></a></td>
						<td><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("otros", "ventas/" . $fila["nombre_grabado"]) ?>', 'PLANO<?php echo $fila["id_planocuotafondeador"] ?>','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><?php echo utf8_decode($fila["nombre_original"]) ?></a></td>
				</tr>
			<?php

				$j++;
			}

			?>
		</table>
		<br>
	</form>
<?php

} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>