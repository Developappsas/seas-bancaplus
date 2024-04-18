<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>

<style type="text/css">
    .image-upload>input {
        display: none;
    }

    .image-upload img {
        width: 16px;
        cursor: pointer;
    }
</style>

<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">



<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Visado/Incorporaci&oacute;n</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="visadoincorp.php">
<table>
	<tr>
		<td>
			<div class="box1 clearfix">
			<table border="0" cellspacing=1 cellpadding=2>
			<tr>
				<td valign="bottom">&nbsp;C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
				<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar">&nbsp;&nbsp;</td>

				<td valign="bottom">&nbsp;<br><input type="submit" id="input_cargar_base" value="Examinar" class="btn btn-dark btn-sm">&nbsp;</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
</table></form>
<?php

if (!$_REQUEST["page"])
{
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

if ($_REQUEST["buscar"])
{
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, se.nombre as nombre_subestado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND se.cod_interno >= '".$cod_interno_subestado_aprobado_pdte_visado."' AND se.cod_interno < 999))";
	
	$queryDB_count = "SELECT COUNT(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN subestados se ON si.id_subestado = se.id_subestado where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND se.cod_interno >= '".$cod_interno_subestado_aprobado_pdte_visado."' AND se.cod_interno < 999))";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		
		$queryDB_count .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		
		$queryDB_count .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		
		$queryDB_count .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}

	if ($_REQUEST["descripcion_busqueda"])
	{
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		
		$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
		
		$queryDB_count .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza like '%".$descripcion_busqueda."%')";
	}
	
	$queryDB .= " order by si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	$rs_count = sqlsrv_query($link,$queryDB_count);
	
	$fila_count = sqlsrv_fetch_array($rs_count);
	
	$cuantos = $fila_count["c"];
}

if ($cuantos)
{
	if ($cuantos > $x_en_x)
	{
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";
		
		$i = 1;
		$final = 0;
		
		while ($final < $cuantos)
		{
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);
			
			if ($final > $cuantos)
			{
				$final = $cuantos;
			}
			
			if ($link_page != $_REQUEST["page"])
			{
				echo " <a href=\"visadoincorp.php?descripcion_busqueda=".$descripcion_busqueda."&buscar=".$_REQUEST["buscar"]."&page=$link_page\">$i</a>";
			}
			else
			{
				echo " ".$i;
			}
			
			$i++;
		}
		
		if ($_REQUEST["page"] != $link_page)
		{
			$siguiente_page = $_REQUEST["page"] + 1;
			
			echo " <a href=\"visadoincorp.php?descripcion_busqueda=".$descripcion_busqueda."&buscar=".$_REQUEST["buscar"]."&page=".$siguiente_page."\">Siguiente</a></p></td></tr>";
		}
		
		echo "</table><br>";
	}
	
?>
<form name="formato3" method="post" action="visadoincorp.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>Estado</th>
	<th>Subestado</th>
	<th>Visado</th>
	<th>Incorporaci&oacute;n</th>
	<th>Parcial</th>
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
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
						break;
			case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
						break;
			case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
						break;
			case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
						break;
		}
		
		switch ($fila["estado"])
		{
			case "ING":	$estado = "INGRESADO"; break;
			case "EST":	$estado = "EN ESTUDIO"; break;
			case "NEG":	$estado = "NEGADO"; break;
			case "DST":	$estado = "DESISTIDO"; break;
			case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
			case "DES":	$estado = "DESEMBOLSADO"; break;
			case "CAN":	$estado = "CANCELADO"; break;
			case "ANU":	$estado = "ANULADO"; break;
		}
		
?>
<tr <?php echo $tr_class ?>>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td align="right"><?php echo $fila["tasa_interes"] ?></td>
	<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
	<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td align="right"><?php echo $fila["plazo"] ?></td>
	<td align="center"><?php echo $estado ?></td>
	<td><?php echo $fila["nombre_subestado"] ?></td>
	<td align="center"><a href="visado_actualizar.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">Visado</a></td>
	<td align="center"><a href="incorporacion_actualizar.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">Incorporaci&oacute;n</a></td>
	<td align="center"><a href="incorporacion_parcial.php?id_simulacion=<?=$fila["id_simulacion"]?>">Parcial</a></td>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
</form>
<?php

}
else
{
	if ($_REQUEST["buscar"]) { $mensaje = "No se encontraron registros"; }
	
	echo "<table><tr><td>".$mensaje."</td></tr></table>";
}

?>

<script type="text/javascript" src="../jquery-1.9.1.js"></script>
<script type="text/javascript" src="../plugins/sheetjs/xlsx.mini.min.js"></script>
<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>

<script type="text/javascript">
	$('#input_cargar_base').click(function detalle(e) {
	    var input = document.createElement('input');
	    input.type = 'file';
	    input.onchange = e => {
	       
	        var file = e.target.files[0];
	        var reader = new FileReader();
	        reader.readAsText(file, 'UTF-8');

	        let datos0 = {
	            "descripcion": file.name
	        }

	        reader.onload = async function (readerEvent) {
	            var content = readerEvent.target.result;
	            resultado = content.toString();
	            //console.log(resultado)
	            var resultado_array = resultado.split('\r\n');
	            var row = 0;
	            var iterar = 1;
	            var contador = 1;
	            let numero_registros = resultado_array.length-2;

				Swal.fire({
					title: 'Procesando Archivo...',
					html: 'Filas ejecutadas <b>1 de ' + numero_registros + '</b>',
					imageUrl: '../images/spinner2.gif',
					imageHeight: 80,
					imageWidth: 80,
					imageAlt: 'spinner2',
					timerProgressBar: true,
					didOpen: () => {
						Swal.showLoading();
						const b = Swal.getHtmlContainer().querySelector('b');

                        procesarArray();
                        function procesarArray(){
                            var peticion = 1;//para no leer el titulo
                            var peticones = 0;                              

                            enviarAjax();
                            function enviarAjax(){
                                if(peticion < resultado_array.length){
                                    const element = resultado_array[peticion].split(';');

                                    if (element[0] != '') {
                                        element[0] = element[0].replaceAll(',', '');
                                        element[0] = element[0].replaceAll('.', '');
                                    } else {
                                        element[0] = 0;
                                    }

                                    if (element[1] != '') {
                                        element[1] = element[1].replaceAll(',', '');
                                        element[1] = element[1].replaceAll('.', '');
                                    } else {
                                        element[1] = 0;
                                    }

                                    if (element[2] != '') {
                                        element[2] = element[2].replaceAll(',', '');
                                        element[2] = element[2].replaceAll('.', '');
                                    } else {
                                        element[2] = 0;
                                    }

                                    if (element[3] != '') {
                                        element[3] = element[3].replaceAll(',', '');
                                        element[3] = element[3].replaceAll('.', '');
                                    } else {
                                        element[3] = 0;
                                    }

                                    if (element[4] != '') {
                                        element[4] = element[4].replaceAll(',', '');
                                        element[4] = element[4].replaceAll('.', '');
                                    } else {
                                        element[4] = 0;
                                    }

                                    var arrayDatos = [];

                                    let datos = {
                                        "id_simulacion": element[0],
                                        "nro_afiliacion": element[1],
                                        "numero_cuotas": element[2],
                                        "valor_cuota": element[3],
                                        "observacion": element[4]
                                    };

                                    $.ajax({
                                        method: 'POST',
                                        url: "../servicios/incorporacion/procesar_incorporacion_parcial.php",
                                        type: 'POST',
                                        dataType: 'json',
                                        data: JSON.stringify(datos),
                                        success: function (response) {
                                            b.textContent = peticion + ' de ' + numero_registros;
                                            
                                            if(peticion < numero_registros){
                                            	peticion++;
                                            	enviarAjax();
                                            }else{
                                            	
                                            	Swal.close();

                                            	Swal.fire({
			                                        title: 'Proceso Ejecutado Satisfactoriamente',
			                                        icon: 'success',
			                                        allowOutsideClick: false,
			                                        showCancelButton: false,
			                                        showConfirmButton: true
			                                    }).then((result) => {
			                                        location.reload();
			                                    })
                                            }
                                        },
                                        error: function() {
                                            $("#modal-loading").modal("hide");
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error, la conexion al servidor ha fallado',
                                                showConfirmButton: true
                                            })
                                        }
                                    });
                                }else{
                                    $("#modal-loading").modal("hide");

                                    Swal.fire({
                                        title: 'Proceso Ejecutado Satisfactoriamente',
                                        icon: 'success',
                                        allowOutsideClick: false,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then((result) => {
                                        location.reload();
                                    })
                                }    
                            }
                        }
					},
					willClose: () => {
						
					}
				}).then((result) => {
				 
					if (result.dismiss === Swal.DismissReason.timer) {
						console.log('I was closed by the timer')
				  	}
				});	            
	        }
	    }
	    input.click();

	    return false;
	});
</script>
<?php include("bottom.php"); ?>


