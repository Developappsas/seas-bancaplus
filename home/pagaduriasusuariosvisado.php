<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function chequeo_forma() {
	with (document.formato) {
		if ((id_usuario.value == "")) {
			alert("Debe seleccionar un usuario para asociarlo");
			return false;
		}
		
		
	}
}
//-->
</script>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
    <td valign="top" width="18"><a href="pagadurias.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Asociar Usuarios Visado</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="pagaduriasusuariosvisado_crear.php" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_pagaduria" value="<?php echo $_REQUEST["id_pagaduria"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>

<tr>
	<td valign="bottom"><br>
		<select name="id_usuario">
			<option value=""></option>
<?php

$queryDB = "SELECT id_usuario, nombre, apellido from usuarios where subtipo = 'ANALISTA_VISADO' AND estado = '1' order by nombre, apellido, id_usuario";

$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_usuario"]."\">".utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"])."</option>\n";
}

?>

			</select>&nbsp;
	
	<td valign="bottom">&nbsp;<br><input type="submit" value="Asociar Usuario"></td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</form>
<hr noshade size=1 width=350>
<br>
<?php

if ($_REQUEST["action"])
{
	$queryDB = "SELECT * from pagadurias_usuarios_visado puv INNER JOIN usuarios us ON puv.id_usuario = us.id_usuario where puv.id_pagaduria = '".$_REQUEST["id_pagaduria"]."' AND us.subtipo = 'ANALISTA_VISADO' order by us.nombre, us.apellido";
	
	$rs = sqlsrv_query($link,$queryDB);
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["chk".$fila["id"]]== "1")
		{
			if ($_REQUEST["action"] == "borrar")
			{
				sqlsrv_query($link,"delete from pagadurias_usuarios_visado where id = '".$fila["id"]."'");
			}
		}
	}
}

$queryDB = "SELECT * from pagadurias_usuarios_visado puv INNER JOIN usuarios us ON puv.id_usuario = us.id_usuario where puv.id_pagaduria = '".$_REQUEST["id_pagaduria"]."' AND us.subtipo = 'ANALISTA_VISADO'";

$queryDB .= " order by us.nombre, us.apellido";

$rs = sqlsrv_query( $link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(sqlsrv_num_rows($rs))
{

?>
<form name="formato3" method="post" action="pagaduriasusuariosvisado.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_pagaduria" value="<?php echo $_REQUEST["id_pagaduria"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>Usuario</th>
	<th>Desasociar</th>
</tr>
<?php

	$j = 1;
	
	while ($fila1 = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";	
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
?>

<tr <?php echo $tr_class ?>>
	<td><?php echo utf8_decode($fila1["nombre"])." ".utf8_decode($fila1["apellido"]) ?></td>
	<td align="center"><input type="checkbox" name="chk<?php echo $fila1["id"] ?>" value="1"></td>
</tr>
<?php 

		$j++;
	}

?>
</table>
<br>
<p align="center"><input type="submit" value="Desasociar" onClick="document.formato3.action.value='borrar'"></p>

</form>
<?php

}
else
{
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>

<?php include("bottom.php"); ?>

