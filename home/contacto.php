<?php include ('../functions.php'); ?>
<?php

if (!($_SESSION["S_LOGIN"]))
{
	exit;
}

$link = conectar();
?>
<?php include("top.php"); ?>

<input type="hidden" name="action" value="">
<input type="hidden" name="cedula" value="">

<?php
    
   
     
        $queryDB = "select * from cazador where cedula = '".$_REQUEST["cedula"]."'";
        $rs1 = sqlsrv_query($link, $queryDB);

        while ($fila1 =  sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){

?>
<table border="0" cellspacing=1 cellpadding=2>
    
    <tr>
	   <td align="right">Nombre: </td> <td><input type="text" readonly name="nombre" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["nombre"] ?>"></td>
	</tr>

	<tr>
	   <td align="right">Telefono: </td> <td><input type="text" readonly name="telefono" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["telefono"] ?>"></td>
	</tr>

	<tr>
	<td align="right">Direccion: </td> <td><input type="text" readonly name="direccion" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["direccion"] ?>"></td>
	</tr>

	<tr>
	<td align="right">Ciudad: </td> <td><input type="text" readonly name="ciudad" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["ciudad"] ?>"></td>
	</tr>

	<tr>
	<td align="right">Pagaduria: </td> <td><input type="text" readonly name="pagaduria" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["pagaduria"] ?>"></td>
	</tr>

	<tr>
	<td align="right">Institucion: </td> <td><input type="text" readonly name="institucion" size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["institucion"] ?>"></td>
	</tr>

	<tr>
	<td align="right">Email: </td> <td><input type="text" name="email" readonly size="40" style="background-color:#EAF1DD;" value="<?php echo $fila1["mail"] ?>"></td>
	</tr>



</table>

         <?php } ?>