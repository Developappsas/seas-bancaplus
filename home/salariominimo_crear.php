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

$existe_salario = sqlsrv_query($link, "select ano from salario_minimo where ano = '".$_REQUEST["ano"]."'" array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET) );

if (!(sqlsrv_num_rows($existe_salario))) {
	sqlsrv_query($link, "insert into salario_minimo (ano, salario_minimo, usuario_creacion, fecha_creacion) values ('".$_REQUEST["ano"]."', '".$_REQUEST["salario_minimo"]."', '".$_SESSION["S_LOGIN"]."', getdate())");
	
	$mensaje = "Salario minimo creado exitosamente";
} else {
	$mensaje = "El salario minimo del ano digitado ya se encuentra registrado. Salario minimo NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'salariominimo.php';
</script>
