<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES") || !$_SESSION["FUNC_SUBESTADOS"])
{
	exit;
}

$link = conectar();



?>
<?php include("top.php"); ?>
<?php
 $queryDB = "SELECT id_subestado,id_usuario from subestados_usuarios where id_subestado = '".$_REQUEST["id_subestado"]."'";
 $queryDB .= " AND id_usuario = '".$_REQUEST["id_usuario"]."'";
  $existe_usuario = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
   

if (!(sqlsrv_num_rows($existe_usuario)))
{
	if(sqlsrv_query($link,"INSERT into subestados_usuarios (id_subestado, id_usuario) VALUES ('".$_REQUEST["id_subestado"]."', '".$_REQUEST["id_usuario"]."')")){
		$mensaje = "Usuario asociado exitosamente";	
	}else{
		$mensaje = "Error No se pudo guardar, " . sqlsrv_error($link);
	}
	
	

}
else
{
	
	$mensaje = "El usuario ya ha sido asociado a este subestado anteriormente. Por favor seleccione otro usuario para ser asociado";

}
	
	 

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'subestadosusuarios.php?id_subestado=<?php echo $_REQUEST["id_subestado"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
