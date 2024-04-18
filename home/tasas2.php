<?php 

include('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR") {
	exit;
}

$link = conectar();

if ($_REQUEST["sector"] == "PRIVADO")
	$sufijo_sector = "_privado";

include("top.php"); ?>

<style type="text/css">
	.tab {
		overflow: hidden;
		border: 1px solid #f5f3f3;
		background-color: #fff;
	}

	/* Style the buttons that are used to open the tab content */
	.tab button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
	}

	/* Change background color of buttons on hover */
	.tab button:hover {
		background-color: #ddd;
	}

	/* Create an active/current tablink class */
	.tab button.active {
		background-color: #e3e7f7;
	}

	/* Style the tab content */
	.tabcontent {
		display: none;
		padding: 6px 12px;
	}
</style>
<script language="JavaScript" src="../functions.js"></script>
<script language="JavaScript">
	function chequeo_forma(formato) {
		with(formato) {
			if (tasa_interes.value == "" || descuento1.value == "" || descuento1_producto.value == "" || descuento2.value == "") {
				alert("Los campos marcados con asterisco (*) son obligatorios");
				return false;
			}
		}
	}

	function modificar(formato, campot, campod1, campod1p, campod2) {
		with(formato) {
			if (campot.value == "" || campod1.value == "" || campod1p.value == "" || campod2.value == "") {
				alert("Los campos marcados con asterisco (*) son obligatorios");
				return false;
			} else {
				submit();
			}
		}
	}

	function openCity(evt, tabItem) {
		// Declare all variables
		var i, tabcontent, tablinks;

		// Get all elements with class="tabcontent" and hide them
		tabcontent = document.getElementsByClassName("tabcontent");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}

		// Get all elements with class="tablinks" and remove the class "active"
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}

		// Show the current tab, and add an "active" class to the button that opened the tab
		document.getElementById(tabItem).style.display = "block";
		evt.currentTarget.className += " active";
	}
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="tasas.php?sector=<?php echo $_REQUEST["sector"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Tasas Sector <?php echo ucfirst(strtolower($_REQUEST["sector"])) ?></b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="tasas2_crear.php?sector=<?php echo $_REQUEST["sector"] ?>" onSubmit="return chequeo_forma(document.formato)">
	<input type="hidden" name="id_tasa" value="<?php echo $_REQUEST["id_tasa"] ?>">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">Tasa (*)<br><input type="text" name="tasa_interes" maxlength="6" size="6" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
							<td valign="bottom">% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> (*)<br><input type="text" name="descuento1" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
							<td valign="bottom">% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> - Prod. (*)<br><input type="text" name="descuento1_producto" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
							<td valign="bottom">% AF (*)<br><input type="text" name="descuento2" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
							<?php

							if ($_REQUEST["sector"] == "PUBLICO") {

								?>
								<td valign="bottom" align="center">&nbsp;&nbsp;S&oacute;lo Activos&nbsp;&nbsp;<br><input type="checkbox" name="solo_activos" value="1"></td>
								<td valign="bottom" align="center">&nbsp;&nbsp;S&oacute;lo Pensionados&nbsp;&nbsp;<br><input type="checkbox" name="solo_pensionados" value="1"></td>
								<?php

							}

							?>
							<td valign="bottom" align="center">&nbsp;&nbsp;KP PLUS&nbsp;&nbsp;<br><input type="checkbox" name="sin_seguro" value="1"></td>
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

$queryDB = "SELECT id_tasa2, 
CONVERT(FLOAT, tasa_interes) AS tasa_interes , 
CONVERT(FLOAT, descuento1)AS descuento1, 
CONVERT(FLOAT, descuento1_producto ) as descuento1_producto , 
CONVERT(FLOAT,descuento2) as descuento2, solo_activos, solo_pensionados, sin_seguro from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "' order by tasa_interes DESC";


$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs)) {

?>
<div class="tab" style="width:85%; margin-top: 8px;">
	<button class="tablinks active" onclick="openCity(event, 'tasas_vigentes')">TASAS VIGENTES</button>
	<button class="tablinks" onclick="openCity(event, 'tasas_no_vigentes')">TASAS NO VIGENTES</button>
	<button class="tablinks" onclick="openCity(event, 'tasas_no_asociadas')">TASAS NO ASOCIADAS</button>
</div>
<div id="tasas_vigentes" style="display: block;" class="tabcontent">
	<form name="formato32" method="post" action="tasas2_actualizar.php?sector=<?php echo $_REQUEST["sector"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="id_tasa" value="<?php echo $_REQUEST["id_tasa"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>Tasa (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> - Prod. (*)</th>
				<th>% AF (*)</th>
				<?php

				if ($_REQUEST["sector"] == "PUBLICO") {

					?>
					<th>S&oacute;lo Activos</th>
					<th>S&oacute;lo Pensionados</th>
					<?php

				}

				?>
				<th>KP PLUS</th>
				<th>Unidades de Negocio</th>
				<th>Modificar</th>
				<th>Borrar</th>
			</tr>
			<?php

			$j = 1;
			
			$queryDB = "SELECT id_tasa2, 
			CONVERT(FLOAT, tasa_interes) AS tasa_interes , 
			CONVERT(FLOAT, descuento1)AS descuento1, 
			CONVERT(FLOAT, descuento1_producto ) as descuento1_producto , 
			CONVERT(FLOAT,descuento2) as descuento2, solo_activos, solo_pensionados, sin_seguro from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "' order by tasa_interes DESC";

	
			$rs = sqlsrv_query($link, $queryDB);
		
			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {

				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

				$i = 0;

				$unidades_asociadas = "";

				$queryDB = "SELECT un.nombre from tasas2_unidades" . $sufijo_sector . " tu INNER JOIN unidades_negocio un ON tu.id_unidad_negocio = un.id_unidad where tu.id_tasa2 = '" . $fila["id_tasa2"] . "'  AND un.estado=1 order by un.id_unidad";
				
			

				$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if (sqlsrv_num_rows($rs1)) {
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						if ($i)
							$unidades_asociadas .= ", ";

						$unidades_asociadas .= utf8_decode($fila1["nombre"]);

						$i++;
					}

					?>
					<tr <?php echo $tr_class ?>>
						<td><input type="text" name="tasa_interes<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["tasa_interes"] ?>" maxlength="6" size="6" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1_producto<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1_producto"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento2<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento2"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<?php

						if ($_REQUEST["sector"] == "PUBLICO") {

							?>
							<td align="center"><input type="checkbox" name="solo_activos<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_activos"]) {
								echo " checked";
							} ?>></td>
							<td align="center"><input type="checkbox" name="solo_pensionados<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_pensionados"]) {
								echo " checked";
							} ?>></td>
							<?php

						}

						?>
						<td align="center"><input type="checkbox" name="sin_seguro<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["sin_seguro"]) {
							echo " checked";
						} ?>></td>
						<td align="center">
							<a href="tasas2unidadesnegocio.php?sector=<?php echo $_REQUEST["sector"]; ?>&id_tasa=<?php echo $_REQUEST["id_tasa"]; ?>&id_tasa2=<?php echo $fila["id_tasa2"]; ?>">
								<?php if ($unidades_asociadas) {
									echo $unidades_asociadas;
								} else {
									echo "Asociar";
								} ?>
							</a>
						</td>
						<td>
							<input type="button" value="Modificar" onClick="document.formato32.action.value='actualizar'; document.formato32.id.value='<?php echo $fila["id_tasa2"]; ?>'; modificar(document.formato32, document.formato32.tasa_interes<?php echo $fila["id_tasa2"]; ?>, document.formato32.descuento1<?php echo $fila["id_tasa2"]; ?>, document.formato32.descuento1_producto<?php echo $fila["id_tasa2"]; ?>, document.formato32.descuento2<?php echo $fila["id_tasa2"]; ?>)">
						</td>
						<td align=center><input type=checkbox name="chk<?php echo $fila["id_tasa2"]; ?>" value="1"></td>
					</tr>
					<?php

					$j++;
				}
			}
			?>
		</table>
		<br>
		<p align="center"><input type="submit" value="Borrar" onClick="document.formato32.action.value='borrar'"></p>
	</form>
</div>

<div id="tasas_no_vigentes" class="tabcontent">
	<form name="formato23" method="post" action="tasas2_actualizar.php?sector=<?php echo $_REQUEST["sector"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="id_tasa" value="<?php echo $_REQUEST["id_tasa"] ?>">

		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>Tasa (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> - Prod. (*)</th>
				<th>% AF (*)</th>
				<?php

				if ($_REQUEST["sector"] == "PUBLICO") {

					?>
					<th>S&oacute;lo Activos</th>
					<th>S&oacute;lo Pensionados</th>
					<?php
				}
				?>
				<th>KP PLUS</th>
				<th>Unidades de Negocio</th>
			</tr>
			<?php

			$j = 1;
			$queryDB = "SELECT id_tasa2, CONVERT(FLOAT, tasa_interes) AS tasa_interes , CONVERT(FLOAT, descuento1)AS descuento1, CONVERT(FLOAT, descuento1_producto ) as descuento1_producto , CONVERT(FLOAT,descuento2) as descuento2, solo_activos, solo_pensionados, sin_seguro from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "' order by tasa_interes DESC";

			$rs = sqlsrv_query($link, $queryDB);
			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";
				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}
				$i = 0;
				$unidades_asociadas = "";
				$queryDB = "select un.nombre from tasas2_unidades" . $sufijo_sector . " tu INNER JOIN unidades_negocio un ON tu.id_unidad_negocio = un.id_unidad where tu.id_tasa2 = '" . $fila["id_tasa2"] . "'  AND un.estado=0 order by un.id_unidad";

				$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if (sqlsrv_num_rows($rs1)) {
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						if ($i)
							$unidades_asociadas .= ", ";

						$unidades_asociadas .= utf8_decode($fila1["nombre"]);

						$i++;
					}
					?>
					<tr <?php echo $tr_class ?>>
						<td><input type="text" name="tasa_interes<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["tasa_interes"] ?>" maxlength="6" size="6" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1_producto<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1_producto"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento2<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento2"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<?php

						if ($_REQUEST["sector"] == "PUBLICO") {

							?>
							<td align="center"><input type="checkbox" name="solo_activos<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_activos"]) {
								echo " checked";
							} ?>></td>
							<td align="center"><input type="checkbox" name="solo_pensionados<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_pensionados"]) {
								echo " checked";
							} ?>></td>
							<?php
						}
						?>
						<td align="center"><input type="checkbox" name="sin_seguro<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["sin_seguro"]) {
							echo " checked";
						} ?>></td>
						<td align="center"><?php if ($unidades_asociadas) {
							echo $unidades_asociadas;
						} else {
							echo "Asociar";
						} ?></td>
					</tr>
					<?php
					$j++;
				}
			}
			?>
		</table>
		<br>
		<p align="center"><input type="submit" value="Borrar" onClick="document.formato23.action.value='borrar'"></p>
	</form>
</div>

<div id="tasas_no_asociadas" class="tabcontent">
	<form name="formato33" method="post" action="tasas2_actualizar.php?sector=<?php echo $_REQUEST["sector"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="id_tasa" value="<?php echo $_REQUEST["id_tasa"] ?>">

		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>Tasa (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> (*)</th>
				<th>% <?php if ($_REQUEST["sector"] == "PUBLICO") { ?>Int. Ant.<?php } else { ?>Aval<?php } ?> - Prod. (*)</th>
				<th>% AF (*)</th>
				<?php

				if ($_REQUEST["sector"] == "PUBLICO") {

					?>
					<th>S&oacute;lo Activos</th>
					<th>S&oacute;lo Pensionados</th>
					<?php
				}
				?>
				<th>KP PLUS</th>
				<th>Unidades de Negocio</th>
				<th>Modificar</th>
				<th>Borrar</th>
			</tr>
			<?php

			$j = 1;
			$queryDB = "SELECT id_tasa2, CONVERT(FLOAT, tasa_interes) AS tasa_interes , CONVERT(FLOAT, descuento1)AS descuento1, CONVERT(FLOAT, descuento1_producto ) as descuento1_producto , CONVERT(FLOAT,descuento2) as descuento2, solo_activos, solo_pensionados, sin_seguro from tasas2" . $sufijo_sector . " where id_tasa = '" . $_REQUEST["id_tasa"] . "' order by tasa_interes DESC";

			$rs = sqlsrv_query($link, $queryDB);
	
			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {

				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

				$i = 0;

				$unidades_asociadas = "";

				$queryDB = "SELECT un.nombre from tasas2_unidades" . $sufijo_sector . " tu INNER JOIN unidades_negocio un ON tu.id_unidad_negocio = un.id_unidad where tu.id_tasa2 = '" . $fila["id_tasa2"] . "'  AND un.estado=0 order by un.id_unidad";

				$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				if (sqlsrv_num_rows($rs1) == 0) {
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
						if ($i)
							$unidades_asociadas .= ", ";

						$unidades_asociadas .= utf8_decode($fila1["nombre"]);

						$i++;
					}
					?>
					<tr <?php echo $tr_class ?>>
						<td><input type="text" name="tasa_interes<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["tasa_interes"] ?>" maxlength="6" size="6" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento1_producto<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento1_producto"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<td><input type="text" name="descuento2<?php echo $fila["id_tasa2"] ?>" value="<?php echo $fila["descuento2"] ?>" maxlength="18" size="18" style="text-align:center;" onChange="if(isnumber_punto(this.value)==false) {this.value=''; return false}"></td>
						<?php
						if ($_REQUEST["sector"] == "PUBLICO") {

							?>
							<td align="center"><input type="checkbox" name="solo_activos<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_activos"]) {
								echo " checked";
							} ?>></td>
							<td align="center"><input type="checkbox" name="solo_pensionados<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["solo_pensionados"]) {
								echo " checked";
							} ?>></td>
							<?php
						}
						?>
						<td align="center"><input type="checkbox" name="sin_seguro<?php echo $fila["id_tasa2"] ?>" value="1" <?php if ($fila["sin_seguro"]) {
							echo " checked";
						} ?>></td>
						<td align="center"><a href="tasas2unidadesnegocio.php?sector=<?php echo $_REQUEST["sector"] ?>&id_tasa=<?php echo $_REQUEST["id_tasa"] ?>&id_tasa2=<?php echo $fila["id_tasa2"] ?>"><?php if ($unidades_asociadas) {
							echo $unidades_asociadas;
						} else {
							echo "Asociar";
						} ?></a></td>
						<td><input type=button value="Modificar" onClick="document.formato33.action.value='actualizar'; document.formato33.id.value='<?php echo $fila["id_tasa2"] ?>'; modificar(document.formato33, document.formato33.tasa_interes<?php echo $fila["id_tasa2"] ?>, document.formato33.descuento1<?php echo $fila["id_tasa2"] ?>, document.formato33.descuento1_producto<?php echo $fila["id_tasa2"] ?>, document.formato33.descuento2<?php echo $fila["id_tasa2"] ?>)"></td>
						<td align=center><input type=checkbox name="chk<?php echo $fila["id_tasa2"] ?>" value="1"></td>
					</tr>
					<?php
					$j++;
				}
			}
			?>
		</table>
		<br>
		<p align="center"><input type="submit" value="Borrar" onClick="document.formato33.action.value='borrar'"></p>
	</form>
</div>
	<?php
} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}
?>
<?php include("bottom.php"); ?>