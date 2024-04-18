<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

?>
<?php include("top.php"); ?>
<div>
	<iframe height="1200" width="100%" frameborder="0" src="http://190.147.156.229:85/sofaneg/app_Login/"></iframe>
</div>
<?php include("bottom.php"); ?>
