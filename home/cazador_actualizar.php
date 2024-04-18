<?php include ('../functions.php'); ?>
<?php

if (!($_SESSION["S_LOGIN"]))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar"){
   
    
	sqlsrv_query($link,"update cazador set sub_estado = '".$_REQUEST["sub_estado".$_REQUEST["cedula"]]."' where cedula = '".$_REQUEST["cedula"]."'");
	 sqlsrv_query($link,"insert into log_cazador (cedula, id_usuario, sub_estado, fecha_modificacion) VALUES ('".$_REQUEST["cedula"]."', '".$_SESSION["S_IDUSUARIO"]."', '".$_REQUEST["sub_estado".$_REQUEST["cedula"]]."', now()) ");
			
		echo "<script>alert('Estado actualizado exitosamente');</script>";
}

if($_REQUEST["action"] == "terminar"){

	sqlsrv_query($link,"update cazador set estado = '0' where id_usuario = '".$_SESSION["S_IDUSUARIO"]."'");
	 echo "<script>alert('Gestion terminada');</script>";

}



?>
<script>
window.location = 'cazador.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>