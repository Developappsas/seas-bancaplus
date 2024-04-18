<?php
include('../functions.php');



if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

$mensaje = "";

$file = fopen($_FILES['archivo']['tmp_name'], "r");

$primer_registro = 1;

$i = 0;



while (!feof($file)) {
	$i++;

	$linea = fgets($file, 4096);

	$linea = str_replace(chr(10), "", $linea);

	$linea = str_replace(chr(13), "", $linea);

	if ($i != 1) {
		$datos = explode("\t", $linea);

		if ($datos[0]) {
			$cedula = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
			$nombre1 = utf8_encode(trim($datos[1]));
			$nombre2 = utf8_encode(trim($datos[2]));
			$apellido1 = utf8_encode(trim($datos[3]));
			$apellido2 = utf8_encode(trim($datos[4]));
			$nro_libranza = trim($datos[5]);
			$direccion = trim($datos[6]);
			$telefono = trim($datos[7]);
			$celular = trim($datos[8]);
			$email = trim($datos[9]);
			$tasa_interes = trim($datos[10]);
			$cuota_corriente = trim($datos[11]);
			$cuota_descuento = trim($datos[12]);
			$valor_credito = trim($datos[13]);
			$pagaduria = utf8_encode(trim($datos[14]));
			$plazo = utf8_encode(trim($datos[15]));
			$fecha_negociacion = trim($datos[16]);
			$fecha_primera_cuota = trim($datos[17]);
			$cuotas_vendidas = trim($datos[18]);
			$ciudad = trim($datos[20]);
			$departamento = trim($datos[21]);

			$rs1 = sqlsrv_query($link, "SELECT id_simulacion from simulaciones_ext where id_vendedor = '" . $_REQUEST["id_vendedor"] . "' AND nro_libranza = '" . $nro_libranza . "'", array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));

			if (!sqlsrv_num_rows($rs1)) {
				$nombre = $nombre1 . " " . $nombre2 . " " . $apellido1 . " " . $apellido2;

				$cobro_adicional_en_cuota = $cuota_descuento - $cuota_corriente;

				$cuotas_ya_recaudadas = $plazo - $cuotas_vendidas;

				$fecha_tmp = $fecha_primera_cuota;

				$fecha = new DateTime($fecha_tmp);

				$fecha = new DateTime($fecha->format('Y-m-01'));

				$fecha->sub(new DateInterval("P" . $cuotas_ya_recaudadas . "M"));

				sqlsrv_query($link, "INSERT into simulaciones_ext (fecha_estudio, cedula, nombre, nombre1, nombre2, apellido1, apellido2, pagaduria, tasa_interes , plazo, opcion_credito, valor_credito, usuario_creacion, fecha_creacion, estado, direccion, telefono, celular, email, opcion_cuota_cso, opcion_desembolso_cso, ciudad, departamento, nro_libranza, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6, descuento_transferencia, porcentaje_seguro, valor_por_millon_seguro, fecha_primera_cuota, porcentaje_extraprima, id_vendedor, cobro_adicional_en_cuota) values ('" . $fecha_negociacion . "', '" . $cedula . "', '" . $nombre . "', '" . $nombre1 . "', '" . $nombre2 . "', '" . $apellido1 . "', '" . $apellido2 . "', '" . $pagaduria . "', '" . $tasa_interes . "', '" . $plazo . "', 'CSO', '" . $valor_credito . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE(), 'DES', '" . $direccion . "', '" . $telefono . "', '" . $celular . "', '" . $email . "', '" . $cuota_descuento . "', '0', '" . $ciudad . "', '" . $departamento . "', '" . $nro_libranza . "', '0', '0', '0', '0', '0', '0', '0', '0', '0', '" . $fecha->format('Y-m-t') . "', '0', '" . $_REQUEST["id_vendedor"] . "', '" . $cobro_adicional_en_cuota . "')");

				$rs2 = sqlsrv_query($link, "SELECT MAX(id_simulacion) as max_c from simulaciones_ext");

				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

				$id_simulacion = $fila2["max_c"];

				$saldo = $valor_credito;

				$valor_cuota = $cuota_corriente;

				for ($j = 1; $j <= $plazo; $j++) {
					$fecha = new DateTime($fecha->format('Y-m-01'));

					$interes = $saldo * $tasa_interes / 100.00;

					$capital = $valor_cuota - $interes;

					$saldo -= $capital;

					if ($j == $plazo) {
						$valor_cuota += $saldo;

						$capital = $valor_cuota - $interes;

						$saldo = 0;
					}

					sqlsrv_query($link, "INSERT into cuotas_ext (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('" . $id_simulacion . "', '" . $j . "', '" . $fecha->format('Y-m-t') . "', '" . round($capital) . "', '" . round($interes) . "', '0', '" . round($valor_cuota) . "', '" . round($valor_cuota) . "')");

					sqlsrv_query($link, "UPDATE cuotas_ext set saldo_cuota = '0', pagada = '1' where id_simulacion = '" . $id_simulacion . "' AND cuota <= '" . $cuotas_ya_recaudadas . "'");

					$fecha->add(new DateInterval('P1M'));
				}

				if (sqlsrv_errors($link)) {
					$mensaje .= "error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
				}
			} else {
				$mensaje .= "El numero de libranza ya existe para el vendedor seleccionado\\n";
			}
		}
	}
}

if (!$mensaje) {
	$mensaje .= "El archivo ha sido cargado satisfactoriamente";
}

fclose($file);

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'cartera.php?ext=1';
</script>