<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();



?>
<?php include("top.php"); ?>
<?php
 $queryDB = "SELECT id_oficina,id_usuario from oficinas_usuarios where id_oficina = '".$_REQUEST["id_oficina"]."'";
 $queryDB .= " AND id_usuario = '".$_REQUEST["id_usuario"]."'";

  $existe_usuario = sqlsrv_query($link, $queryDB , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
   

if (!sqlsrv_num_rows($existe_usuario)) {
	sqlsrv_query($link, "INSERT into oficinas_usuarios (id_oficina, id_usuario) VALUES ('".$_REQUEST["id_oficina"]."', '".$_REQUEST["id_usuario"]."')");
  	$mensaje = "Usuario asociado exitosamente";
	
} else{
	  
	$mensaje = "El usuario ya ha sido asociado a esta oficina anteriormente. Por favor seleccione otro usuario para ser asociado";
}
	
?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'oficinasusuarios.php?id_oficina=<?php echo $_REQUEST["id_oficina"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
