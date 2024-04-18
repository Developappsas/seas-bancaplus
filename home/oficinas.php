<?php include('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
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
			<center><b>Oficinas</b><br><br></center>
		</td>
	</tr>
</table>
<form name=formato method=post action="oficinas_crear.php" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">C&oacute;digo<br><input type="text" name="codigo" maxlength="4" size="3" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
							<td valign="bottom">Nombre<br><input type="text" name="nombre" maxlength="255" size="50"></td>
							<td valign="bottom">Zona<br>
								<select name="zona_oficina" id="zona_oficina">
									<?php
									$consultarZonas = "SELECT * FROM zonas";
									$queryZonas = sqlsrv_query($link, $consultarZonas);
									while ($resZonas = sqlsrv_fetch_array($queryZonas)) {
									?>
										<option value="<?php echo $resZonas["id_zona"]; ?>"><?php echo $resZonas["nombre"]; ?></option>
									<?php
									}
									?>
								</select>
							</td>
							<td valign="bottom">&nbsp;<br><input type="submit" value="Crear Oficina"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<hr noshade size=1 width=350>
<form name="formato2" method="post" action="oficinas.php">
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

$queryDB = "select * from oficinas where id_oficina IS NOT NULL";

$queryDB_count = "select COUNT(*) as c from oficinas where id_oficina IS NOT NULL";

if ($_REQUEST["descripcion_busqueda"]) {
	$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

	$queryDB = $queryDB . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";

	$queryDB_count = $queryDB_count . " AND UPPER(nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%'";
}

$queryDB .= " order by codigo ASC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";



$rs = sqlsrv_query($link, $queryDB);

if ($rs == false) {
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
}

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
				echo " <a href=\"oficinas.php?descripcion_busqueda=" . $descripcion_busqueda . "&page=$link_page\">$i</a>";
			} else {
				echo " " . $i;
			}

			$i++;
		}

		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;

			echo " <a href=\"oficinas.php?descripcion_busqueda=" . $descripcion_busqueda . "&page=" . $siguiente_page . "\">Siguiente</a></p></td></tr>";
		}

		echo "</table><br>";
	}

?>
	<form name="formato3" method="post" action="oficinas_actualizar.php">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>C&oacute;digo</th>
				<th>Nombre</th>
				<th>Asociar Usuarios</th>
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
					<td><input type="text" name="c<?php echo str_replace(" ", "_", $fila["id_oficina"]) ?>" value="<?php echo $fila["codigo"] ?>" maxlength="4" size="3" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
					<td><input type="text" name="n<?php echo str_replace(" ", "_", $fila["id_oficina"]) ?>" value="<?php echo str_replace("\"", "&#34", utf8_decode($fila["nombre"])) ?>" maxlength="255" size="35"></td>
					<td align="center"><a href="oficinasusuarios.php?id_oficina=<?php echo $fila["id_oficina"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&page=<?php echo $_REQUEST["page"] ?>">Asociar</a></td>
					<td><input type=button value="Modificar" onClick="document.formato3.action.value='actualizar'; document.formato3.id.value='<?php echo $fila["id_oficina"] ?>'; modificar(document.formato3.c<?php echo $fila["id_oficina"] ?>, document.formato3.n<?php echo $fila["id_oficina"] ?>)"></td>
					<td align=center><input type=checkbox name="b<?php echo $fila["id_oficina"] ?>" value="1"></td>
				</tr>
			<?php

				$j++;
			}

			?>
		</table>
		<br>
		<p align="center"><input type="submit" value="borrar" onClick="document.formato3.action.value='borrar'"></p>
	</form>
<?php

} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>