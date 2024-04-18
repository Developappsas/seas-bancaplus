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
 $queryDB = "SELECT id_pagaduria,id_usuario from pagadurias_usuarios_visado where id_pagaduria = '".$_REQUEST["id_pagaduria"]."'";
 $queryDB .= " AND id_usuario = '".$_REQUEST["id_usuario"]."'";
  $existe_usuario = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
   

if (!(sqlsrv_num_rows($existe_usuario)))
{
	sqlsrv_query($link,"INSERT into pagadurias_usuarios_visado (id_pagaduria, id_usuario) VALUES ('".$_REQUEST["id_pagaduria"]."', '".$_REQUEST["id_usuario"]."')");
	
	$mensaje = "Usuario asociado exitosamente";

}
else
{
	
	$mensaje = "El usuario ya ha sido asociado a esta pagaduria anteriormente. Por favor seleccione otro usuario para ser asociado";

}
	
	 

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'pagaduriasusuariosvisado.php?id_pagaduria=<?php echo $_REQUEST["id_pagaduria"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
