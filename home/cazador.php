<?php include ('../functions.php'); ?>
<?php

if (!($_SESSION["S_LOGIN"]))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
	
<!--<link href="../ventanas_modales.css" rel="stylesheet" type="text/css">-->

<script language="JavaScript">

function modificar(campoestado) {
	with (document.formato3) {
		if (campoestado.value == "") {
			alert("Debe seleccionar el estado que desea actualizar");
			return false;
		} else {		
			submit();
		}

	}
		
}
function gestion() {
var contador = 0;
with (document.formato3) {
	     
		for (i = 4; i <= elements.length - 2; i++) {
			if (elements[i].selectedIndex == 0) {
				
				contador = contador + 1;

			}
			
		}
	
	if(contador > 0){
		alert("Debe realizar la gestion a todos los prospectos");
	}
	else{
		submit();
	}
}
		
}

</script>

<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Cazador</b><br><br></center></td>

</tr>
</table>
<?php 
$queryDB1 = "SELECT id_cazador,id_usuario,cedula,nombre,institucion,pagaduria,estado,sub_estado from cazador where id_usuario = '".$_SESSION["S_IDUSUARIO"]."' AND estado = '1' ";
$rs1 = sqlsrv_query($link,$queryDB1);

if(!(sqlsrv_num_rows($rs1))){
?>
<form name="formato" method="post" action="cazador_crear.php">
<input type="hidden" name="cedula" value="">
<table>
<tr>
<td><p align="center"><input type="submit" value="Iniciar Cazador" onClick="document.formato.action.value='play'"></p></td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>

<?php
}
else{
	
?>

<form name="formato3" method="post" action="cazador_actualizar.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="cedula" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">



<table border="0" cellspacing="1" cellpadding="2" class="tab3">
<tr>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Pagaduria</th>
	<th>Institucion</th>
	<th></th>
	<th>Estado</th>
	<th>Guardar</th>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
?>
<tr <?php echo $tr_class ?>>
    <td><a href="simulador.php?tipo=COM&cedula=<?php echo $fila["cedula"]?>&id_cazador=<?php echo $fila["id_cazador"]?>&id_comercial=<?php echo $_SESSION["S_IDUSUARIO"]?>"><?php echo $fila["cedula"] ?></a></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["institucion"]) ?></td>
	<td align="center"><a href="#" onClick="window.open('contacto.php?cedula=<?php echo $fila["cedula"] ?>', 'LINK','toolbars=yes,scrollbars=yes,resizable=yes,width=400,height=400,top=0,left=0');"><img src="../images/contacto.png" title="Datos de Contacto"></a></td>
	<td>
   <?php

                            $datos_err = "";
                            $datos_ic = "";
                            $datos_ni = "";
                            $datos_ca = "";
                            $datos_ld = "";
                            $datos_ep = "";
        
                            if ($fila["sub_estado"] == "Datos errados")
                            $datos_err = " selected";
                            else if ($fila["sub_estado"] == "Imposible contactar")
                            $datos_ic = " selected";
                        	else if ($fila["sub_estado"] == "No le interesa")
                            $datos_ni = " selected";
                        	else if ($fila["sub_estado"] == "Cita agendada")
                            $datos_ca = " selected";
                            else if ($fila["sub_estado"] == "Llamar despues")
                            $datos_ld = " selected";
                            else if ($fila["sub_estado"] == "En proceso")
                            $datos_ep = " selected";
                            


                            ?>
      


	<select name="sub_estado<?php echo $fila["cedula"]?>" <?php if($datos_ep){echo  " disabled";} ?>>
		<option value=""></option>
		<option value="Datos errados"<?php echo $datos_err ?>>Datos errados</option>
		<option value="Imposible contactar"<?php echo $datos_ic ?>>Imposible contactar</option>
		<option value="No le interesa"<?php echo $datos_ni ?>>No le interesa</option>
		<option value="Cita agendada"<?php echo $datos_ca ?>>Cita agendada</option>
		<option value="Llamar despues"<?php echo $datos_ld ?>>Llamar despues</option>
   		  <?php if($datos_ep){?>
   		  <option value="En proceso"<?php echo $datos_ep?>>En proceso</option> 
		<?php }?>
   	</select></td>
	<td><input <?php if($datos_ep){echo  " disabled";} ?> type=button value="Grabar" onClick="document.formato3.action.value='actualizar'; document.formato3.cedula.value='<?php echo $fila["cedula"] ?>'; modificar(document.formato3.sub_estado<?php echo $fila["cedula"]?>)"></td>
	
</tr>
<?php

		$j++;
	}
	
?>

</table>
<br>
<input type=button value="Terminar Gestion" onClick="document.formato3.action.value='terminar';gestion();">
</form>

<?php 
}
   ?>

<?php include("bottom.php"); ?>
