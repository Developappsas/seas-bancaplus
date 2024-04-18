<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Maestros</b><br></center></td>
</tr>
</table>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="center">
	    <br><a href="bancos.php">Bancos</a><br>
	    <?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" && $_SESSION["FUNC_SUBESTADOS"]) { ?><br><a href="subestados.php">Subestados</a><br><?php } ?>
	    <br><a href="entidades.php">Entidades Desembolso</a><br>
	    <?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><br><a href="oficinas.php">Oficinas</a><br><?php } ?>
	    <?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?><br><a href="usuarios.php">Usuarios</a><br><?php } ?>
	</td>
</tr>
</table>
<?php include("bottom.php"); ?>
