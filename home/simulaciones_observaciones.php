<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

if (!$_REQUEST["id_simulacion"] && ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_TIPO"] == "CONTABILIDAD" || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_BD" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO"))
{
	exit;
}

$link = conectar();

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && !$_REQUEST["id_simulacion"])
{
	$queryDB = "SELECT * from oficinas_usuarios where id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	if (!(sqlsrv_num_rows($rs1)))
	{
		echo "<script>alert('El usuario no tiene oficina asociada. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
		
		exit;
	}
}

$queryDB = "SELECT estado from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$simulacion_rs = sqlsrv_query($link, $queryDB);

$simulacion = sqlsrv_fetch_array($simulacion_rs);

?>
<html>
<head>
<link rel="STYLESHEET" type="text/css" href="../sty.css">
<script language="JavaScript" src="../functions.js"></script>
<script type="text/javascript">
	function soloCaracterPermitido(string){
	    var out = '';
	    var filtro = 'AaEeIiOoUuBbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz012345()6789%/#$?¿!¡*-+.,:=@ ';//Caracteresvalidos
		
	    for (var i=0; i<string.length; i++){
	       	if (filtro.indexOf(string.charAt(i)) != -1) {
		     	out += string.charAt(i);
	       	}else{

	        	let ascii = string.charAt(i).toUpperCase().charCodeAt(0);
	        
		        if((ascii == 193 || ascii == 201 || ascii == 211 || ascii == 205 || ascii == 209 || ascii == 218)){
		        	out += string.charAt(i).normalize('NFD').replace(/[\u0300-\u036f]/g,"");
		        }else if(ascii == 10){
		        	out += string.charAt(i);
		        }
	       	}
	    }
		
	    //Retornar valor filtrado
	    return out;
	} 
</script>
</head>
<body style="background-color:<?php if ($_REQUEST["tipo"] == "COM") { echo "E7F2F8"; } else { echo "#F4EBE4"; } ?>;">
<div id="<?php if ($_REQUEST["tipo"] == "COM") { echo "contenedor4"; } else { echo "contenedor3"; } ?>">
	<div align="center">
<?php

if ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST")
{

?>
<form id="formato" name=formato method=post action="simulaciones_observaciones_crear.php">
    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	<table>
	<tr>
		<td valign="top"><textarea onkeyup="this.value=soloCaracterPermitido(this.value)" name="observacion" rows="3" cols="70" style="width:100%; background-color:#EAF1DD;"></textarea></td>
	</tr>
	</table>
</form>
<hr noshade size=1 width=350>
<br>
<?php

}

if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO"))
{
	sqlsrv_query($link, "UPDATE simulaciones_observaciones set usuario_anulacion = '".$_SESSION["S_LOGIN"]."', fecha_anulacion = NOW() where id_observacion = '".$_REQUEST["id_observacion"]."'");
}

$queryDB = "SELECT so.*, re1.id_observacion_pregunta, re2.id_observacion_respuesta, sv.id_observacion as id_observacion_visado, sinc.id_observacion as id_observacion_incorporacion from simulaciones_observaciones so LEFT JOIN req_excep re1 ON so.id_observacion = re1.id_observacion_pregunta LEFT JOIN req_excep re2 ON so.id_observacion = re2.id_observacion_respuesta LEFT JOIN simulaciones_visado sv ON so.id_observacion = sv.id_observacion LEFT JOIN simulaciones_incorporacion sinc ON so.id_observacion = sinc.id_observacion where so.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND so.usuario_anulacion IS NULL";

$queryDB .= " order by so.fecha_creacion DESC, so.id_observacion DESC";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs))
{
	

?>
    <form name="formato3" method="post" action="simulaciones_observaciones.php">
        <input type="hidden" name="action" value="">
	    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
				<th>Fecha,<br>Usuario</th>
                <th>Observaci&oacute;n</th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST") && ($_SESSION["S_SOLOLECTURA"] != "1")) { ?><th><img src="../images/delete.png" title="Borrar Observaci&oacute;n"></th><?php } ?>
            </tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{

		$consultarUsuario="SELECT * FROM usuarios WHERE login='".$fila["usuario_creacion"]."'";
		$queryUsuario=sqlsrv_query($link, $consultarUsuario);
		$resUsuario=sqlsrv_fetch_array($queryUsuario);
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
?>
			<tr <?php echo $tr_class ?>>
				<td style="vertical-align:top;" width="65"><?php echo $fila["fecha_creacion"] ?>,<br><?php echo utf8_decode($resUsuario["nombre"]." ".$resUsuario["apellido"]." (".$fila["usuario_creacion"]).")" ?></td>
				<td><?php echo utf8_decode(str_replace(chr(13), "<br>", $fila["observacion"])) ?></td>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST") && ($_SESSION["S_SOLOLECTURA"] != "1")) { ?><td align="center" style="vertical-align:top;"><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || (($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") && $fila["usuario_creacion"] == $_SESSION["S_LOGIN"])) && !$fila["id_observacion_pregunta"] && !$fila["id_observacion_respuesta"] && !$fila["id_observacion_visado"] && !$fila["id_observacion_incorporacion"]) { ?><input type=button onClick="if (confirm('Va a eliminar la observaci�n. Desea continuar?') == true) { location.href='simulaciones_observaciones.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&tipo=<?php echo $_REQUEST["tipo"] ?>&id_observacion=<?php echo $fila["id_observacion"] ?>&action=borrar'; }" style="border:0; width:16px; height:16px; background: url(../images/delete.png) 0 0 no-repeat; border-radius:0px; -webkit-border-radius:0px; -moz-border-radius:0px; -o-border-radius:0px" title="Borrar Observaci&oacute;n"><?php } else { echo "&nbsp;"; } ?></td><?php } ?>
			</tr>
<?php

		$j++;
	}
	
?>
        </table>
    </form>
<?php

}

?>
	</div>
</div>
</body>
</html>