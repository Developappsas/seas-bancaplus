<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

if ($_REQUEST["action"] == "actualizar")
{
	sqlsrv_query($link, "UPDATE simulaciones set fecha_produccion = '".$_REQUEST["mes_prod".$_REQUEST["id"]]."-01' where id_simulacion = '".$_REQUEST["id"]."'");
	
	echo "<script>alert('Registro actualizado exitosamente');</script>";
}

?>
<script>
window.location = 'cierrecomercial.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
