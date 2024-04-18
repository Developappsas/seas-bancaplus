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

sqlsrv_query($Link, "insert into usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_usuario"]."', '".$_REQUEST["id_unidad_negocio"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");

$mensaje = "Unidad de negocio asociada exitosamente";

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'usuariosunidadesnegocio.php?id_usuario=<?php echo $_REQUEST["id_usuario"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
