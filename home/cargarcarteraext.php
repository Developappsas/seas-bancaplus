<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>

<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((id_vendedor.value == "") || (archivo.value == "")) {
				alert("Debe seleccionar el vendedor y el archivo plano");
				return false;
			}
		}
	}
	//-->
</script>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Cargar Cartera</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="cargarcarteraext2.php?ext=<?php echo $_REQUEST["ext"] ?>" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Vendedor<br>
								<select name="id_vendedor" style="width:155px; background-color:#EAF1DD;">
									<option value=""></option>
									<?php

									$queryDB = "SELECT id_vendedor, nombre from vendedores order by nombre";

									$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
										echo "<option value=\"" . $fila1["id_vendedor"] . "\">" . utf8_decode($fila1["nombre"]) . "</option>\n";
									}

									?>
								</select>&nbsp;
							</td>
							<td valign="bottom">Archivo Plano<br><input type="file" name="archivo" style="background-color:#EAF1DD;">&nbsp;</td>
							<td valign="bottom">&nbsp;<br><input type="submit" value="Cargar archivo"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php include("bottom.php"); ?>