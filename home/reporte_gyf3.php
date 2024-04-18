<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Planodirecciones.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();



?>
<table border="0">

<tr>

	<th>Numero Interno</th>

	<th>Numero Consecutivo</th>

	<th>Direccion</th>

	<th>Ciudad</th>

	<th>Departamento</th>

	<th>Pais</th>

	<th>Codigo Postal</th>

	<th>Tipo Direccion</th>

	<th>Telefono1</th>

	<th>Extension1</th>

	<th>Telefono2</th>

	<th>Extension2</th>

	<th>Telefono3</th>

	<th>Extension3</th>

	<th>Num Celular</th>

	<th>Numero Fax</th>

	<th>Numero Beeper</th>

	<th>Codigo Beeper</th>

	<th>Email</th>

	<th>Zona Postal</th>	

</tr>

<?php





$queryDB = "select lpad(simulaciones.cedula,17,'0') as cedula,lpad('0',3,'0') as consecutivo,solicitud.direccion,solicitud.ciudad as ciudad,substring(solicitud.ciudad,1,2) as departamento,lpad('1',5,'0') as pais,lpad('0',5,'0') as cod_postal,lpad('3',3,'0') as tipo_direccion,lpad(solicitud.tel_residencia,10,'0') as telefono1,lpad('0',5,'0') as ext1,lpad('0',10,'0') as telefono2,lpad('0',5,'0') as ext2,lpad('0',10,'0') as telefono3,lpad('0',5,'0') as ext3,lpad(solicitud.celular,10,'0') as celular,lpad('0',10,'0') as fax,lpad('0',10,'0') as num_beeper,lpad('0',10,'0') as cod_beeper,solicitud.email as email,lpad('0',8,'0') as zona_postal from simulaciones join solicitud on solicitud.id_simulacion = simulaciones.id_simulacion where simulaciones.estado = 'DES'";



if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])

{

	$queryDB .= " AND DATE(simulaciones.fecha_desembolso) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";

}



if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])

{

	$queryDB .= " AND DATE(simulaciones.fecha_desembolso) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";

}







$rs = sqlsrv_query($link, $queryDB);



while ($fila = sqlsrv_fetch_assoc($rs))



{

?>

<tr<?php echo $tr_class ?>>

	<td style="mso-number-format:'@';"><?php echo $fila["cedula"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["consecutivo"] ?></td>

	<td><?php echo strtoupper($fila["direccion"]) ?></td>

	<td><?php echo strtoupper($fila["ciudad"]) ?></td>

	<td><?php echo strtoupper($fila["departamento"]) ?></td>

	<td><?php echo strtoupper($fila["pais"]) ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["cod_postal"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["tipo_direccion"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["telefono1"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["ext1"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["telefono2"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["ext2"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["telefono3"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["ext3"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["celular"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["fax"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["num_beeper"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["cod_beeper"] ?></td>

	<td><?php echo $fila["email"] ?></td>

	<td style="mso-number-format:'@';"><?php echo $fila["zona_postal"] ?></td>

</tr>	

<?php



}



?>

</table>

