<?php include ('../functions.php'); include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<?php

$queryDB = "select * from ventas_pagosplanos".$sufijo." where procesado = '0'";

$rs = sqlsrv_query($link,$queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
	if ($fila["nombre_grabado"]){
		delete_file("otros", "ventas/".$fila["nombre_grabado"]);
	}

	sqlsrv_query($link,"delete from ventas_pagosplanos_detalle".$sufijo." where id_pagoplano = '".$fila["id_pagoplano"]."'");

	sqlsrv_query( $link,"delete from ventas_pagosplanos".$sufijo." where id_pagoplano = '".$fila["id_pagoplano"]."'");
}

sqlsrv_query( $link,"START TRANSACTION");

$uniqueID = date("YmdHis");

sqlsrv_query($link,"insert into ventas_pagosplanos".$sufijo." (fecha, descripcion, nombre_original, nombre_grabado, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["fecha"]."', '".utf8_encode($_REQUEST["descripcion"])."', '".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$rs = sqlsrv_query($link,"SELECT MAX(id_pagoplano) as m from ventas_pagosplanos".$sufijo);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_pagoplano = $fila["m"];

sqlsrv_query($link,"COMMIT");

if (strcmp($_FILES["archivo"]["name"], "")){
	$fechaa =new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

	$metadata1 = array(
		'id_pagoplano' => $id_pagoplano,
		'descripcion' => reemplazar_caracteres_no_utf($_REQUEST["descripcion"]),
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);

	$cargado = false;

	try{
		$cargado = upload_file($_FILES["archivo"], "otros", "ventas/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
	} catch (ServiceException $exception) {
        $mensaje = $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
        throw $exception;
    }

    if($cargado){

		$file = fopen($_FILES['archivo']['tmp_name'], "r");

		$primer_registro = 1;

		$i = 0;

		while (!feof($file))
		{
			$i++;

			$linea = fgets($file, 4096);

			$linea = str_replace(chr(10), "", $linea);

			$linea = str_replace(chr(13), "", $linea);

			if ($i != 1)
			{
				$datos = explode("\t", $linea);

				$observacion = "";

				if ($datos[0])
				{
					$id_ventadetalle = trim($datos[16]);
					$cuota = trim($datos[17]);
					$valor = trim($datos[19]);

					$rs1 = sqlsrv_query( $link,"SELECT vc.id_ventadetalle, vc.cuota, vc.saldo_cuota from ventas_cuotas".$sufijo." vc INNER JOIN ventas_detalle".$sufijo." vd ON vc.id_ventadetalle = vd.id_ventadetalle INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta where vc.id_ventadetalle = '".$id_ventadetalle."' AND vc.cuota = '".$cuota."' AND ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0' AND vc.saldo_cuota > 0", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					if (sqlsrv_num_rows($rs1))
					{
						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						if ($valor > $fila1["saldo_cuota"])
							$observacion = "El valor a aplicar es mayor al saldo de la cuota";

						if (!$observacion)
							$observacion = "OK";
					}
					else
					{
						$observacion = "Cuota a aplicar no encontrada";
					}

					sqlsrv_query($link,"INSERT into ventas_pagosplanos_detalle".$sufijo." (id_pagoplano, id_ventadetalle, cuota, valor, observacion, usuario_creacion, fecha_creacion) values ('".$id_pagoplano."', '".$id_ventadetalle."', '".$cuota."', '".$valor."', '".$observacion."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

					if (sqlsrv_error($link)) { $mensaje = "Error en la linea [".$i."]: ".sqlsrv_error($link)."\\n"; break; }
				}
			}
		}

		if (feof($file))
		{
			$mensaje = "OK";
		}

		fclose($file);
	}else{
		$mensaje = "Error al cargar el archivo al contenedor";
	}
}

?>
<script>
<?php

if ($mensaje == "OK")
{

?>
window.location = 'aplicacionpagoscomprador_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>&id_pagoplano=<?php echo $id_pagoplano ?>';
<?php

}
else
{

?>
alert("<?php echo $mensaje ?>");

window.location = 'aplicacionpagoscomprador.php?ext=<?php echo $_REQUEST["ext"] ?>';
<?php

}

?>
</script>
