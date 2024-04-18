<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

?>
<html>
<head>
<link rel="STYLESHEET" type="text/css" href="../sty.css">
<script language="JavaScript" src="../functions.js"></script>
</head>
<body style="background-color:E7F2F8;">
<form id="formato" name=formato method=post action="<?php echo $_REQUEST["action"] ?>.php" target="_parent">
<input type="hidden" name="ext" value="<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="tipo" value="<?php echo $_REQUEST["tipo"] ?>">
<input type="hidden" name="id_venta" value="<?php echo $_REQUEST["id_venta"] ?>">
<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
<input type="hidden" name="id_compradorb" value="<?php echo $_REQUEST["id_compradorb"] ?>">
<input type="hidden" name="modalidadb" value="<?php echo $_REQUEST["modalidadb"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="descripcion_busqueda2" value="<?php echo $_REQUEST["descripcion_busqueda2"] ?>">
<input type="hidden" name="descripcion_busqueda3" value="">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td valign="bottom" style="font: 12px muli, sans-serif;">C&eacute;dula/No. Libranza (Puede buscar varias c&eacute;dulas/no. libranza al tiempo separ&aacute;ndolas por coma)<br><input type="text" name="cedulab" onBlur="ReplaceComilla(this)" style="width:550px"></td>
	<td valign="bottom"><br><input type="submit" value="Buscar" onClick="formato.descripcion_busqueda3.value=formato.cedulab.value"></td>
</tr>
</table>
</div>
</form>
</body>
</html>