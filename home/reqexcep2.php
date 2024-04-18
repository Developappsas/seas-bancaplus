<?php

use PhpParser\Node\Stmt\Echo_;

include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<link rel="STYLESHEET" type="text/css" href="../plugins/DataTables/datatables.min.css?v=4">
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript">

function valor_tipos(x){return x.substring(0,x.indexOf('-'))}

function texto_tipos(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargartipos(reqexcep, objeto_tipos) {
	var num_tipos;
	var j, k = 1;

	num_tipos = 200;

	objeto_tipos.length = num_tipos;
<?php

$queryDB = "SELECT id_tipo, nombre from tipos_reqexcep where estado = '1' AND reqexcep = 'REQUERIMIENTO'";

$queryDB .= " order by nombre";

$datos_tipos_requerimiento = sqlsrv_query($link, $queryDB);

$padre_hija = "PHREQUERIMIENTO = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_requerimiento))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

$queryDB = "select id_tipo, nombre from tipos_reqexcep where estado = '1' AND reqexcep = 'EXCEPCION'";

$queryDB .= " order by nombre";

$datos_tipos_excepcion = sqlsrv_query($link, $queryDB);

$padre_hija = "PHEXCEPCION = [";

while ($fila2 = sqlsrv_fetch_array($datos_tipos_excepcion))
{
	$padre_hija .= "\"".$fila2["id_tipo"]."-".utf8_decode($fila2["nombre"])."\",";
}

$padre_hija .= "\"0-Otro\"];\n";

echo $padre_hija;

?>
	switch(reqexcep) {
		case 'REQUERIMIENTO':
			num_tipos = PHREQUERIMIENTO.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHREQUERIMIENTO[j]);
				objeto_tipos.options[k].text = texto_tipos(PHREQUERIMIENTO[j]);
				k++;
			}
			break;

		case 'EXCEPCION':
			num_tipos = PHEXCEPCION.length;
			for(j = 0; j < num_tipos; j++) {
				objeto_tipos.options[k].value = valor_tipos(PHEXCEPCION[j]);
				objeto_tipos.options[k].text = texto_tipos(PHEXCEPCION[j]);
				k++;
			}
			break;

		default:
			num_tipos = 1;
			k=0;
	}

	objeto_tipos.selectedIndex = 0;
	objeto_tipos.length = num_tipos;

	return true;
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
	<td class="titulo"><center><b>Requerimientos/Excepciones</b><br><br></center></td>
</tr>
</table>
<form name="formato2" method="post" action="reqexcep.php">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
			<td valign="bottom">Req/Excep<br>
				<select name="reqexcepb" onChange="Cargartipos(this.value, document.formato2.id_tipob);">
					<option value=""></option>
					<option value="REQUERIMIENTO">REQUERIMIENTO</option>
					<option value="EXCEPCION">EXCEPCION</option>
				</select>&nbsp;
			</td>
			<td valign="bottom">Tipo<br>
				<select name="id_tipob" style="width:110px">
					<option value=""></option>
				</select>&nbsp;
			</td>
<?php

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO")
{

?>
			<td valign="bottom">&Aacute;rea<br>
				<select name="id_areab" style="width:110px">
					<option value=""></option>
<?php

	$queryDB = "SELECT id_area, nombre from areas_reqexcep where estado = '1' order by nombre";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_area"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>
				</select>&nbsp;
			</td>
<?php

}

?>
			<td valign="bottom">Estado<br>
				<select name="estadob">
					<option value=""></option>
					<option value="PENDIENTE">PENDIENTE</option>
					<option value="RESPONDIDO">RESPONDIDO</option>
				</select>&nbsp;
			</td>
			<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</form>
<?php



echo $queryDB;

	
?>
<form name="formato3" method="post" action="reqexcep.php">
<input type="hidden" name="action" value="">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
<input type="hidden" name="reqexcepb" value="<?php echo $reqexcepb ?>">
<input type="hidden" name="id_tipob" value="<?php echo $id_tipob ?>">
<input type="hidden" name="id_areab" value="<?php echo $id_areab ?>">
<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table border="0" cellspacing=1 cellpadding=2 class="tab1">
<tr>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Pagadur&iacute;a</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Req/Excep</th>
	<th>Tipo</th>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><th>&Aacute;rea</th><?php } ?>
	<th>F<br>Vencimiento</th>
	<th>Descripci&oacute;n</th>
	<th>Respuesta</th>
	<th>Asignado a</th>
	<th>Estado</th>
	<th><img src="../images/adjuntar.png" title="Adjuntos"></th>
	<th>Fecha,<br>Usuario</th>
	<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
</tr>
<?php

	$j = 1;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$tr_class = "";
		
		if (($j % 2) == 0)
		{
			$tr_class = " style='background-color:#F1F1F1;'";
		}
		
		$rs1 = sqlsrv_query($link, "SELECT * from req_excep_adjuntos where id_reqexcep = '".$fila["id_reqexcep"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		$mostrar_adjuntos = 0;
		
		if (sqlsrv_num_rows($rs1))
			$mostrar_adjuntos = 1;
		
?>
<tr <?php echo $tr_class ?>>
	<td style="vertical-align:top;"><?php echo $fila["cedula"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td style="vertical-align:top;" align="center"><?php echo $fila["nro_libranza"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td style="vertical-align:top;" align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
	<td style="vertical-align:top;"><?php echo $fila["reqexcep"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode($fila["tipo"]) ?></td>
	<?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO") { ?><td style="vertical-align:top;"><?php echo utf8_decode($fila["area"]) ?></td><?php } ?>
	<td style="vertical-align:top;" align="center"><?php echo $fila["fecha_vencimiento"] ?></td>
	<td style="vertical-align:top;"><?php echo utf8_decode(str_replace(chr(13), "<br>", substr($fila["observacion"], 0, 100))) ?><?php if (strlen($fila["observacion"]) > 100) { ?>...<a href="#" onClick="window.open('reqexcep_leermas.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&pregunta=1', 'LEERMASPREG<?php echo $fila["id_reqexcep"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">Leer m&aacute;s</a><?php } ?></td>
	<td style="vertical-align:top;"><?php if ($fila["respuesta"]) { ?><?php if ($fila["tipo_respuesta"]) { echo "[".$fila["tipo_respuesta"]."] "; } ?><?php echo utf8_decode(str_replace(chr(13), "<br>", substr($fila["respuesta"], 0, 100))) ?><?php if (strlen($fila["respuesta"]) > 100) { ?>...<a href="#" onClick="window.open('reqexcep_leermas.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&respuesta=1', 'LEERMASRESP<?php echo $fila["id_reqexcep"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=500,height=400,top=0,left=0');">Leer m&aacute;s</a><?php } ?><?php } else { ?><a href="reqexcep_responder.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>">Responder</a><?php } ?></td>
	<td style="vertical-align:top;">
	<?php 
		if ($fila['id_analista_riesgo_crediticio'] <> null) {
			$id_analista = $fila['id_analista_riesgo_crediticio'];
		}else{
			if ( $fila['id_analista_riesgo_operativo'] <> null) {
				$id_analista = $fila['id_analista_riesgo_operativo'];
			}else{
				$id_analista = $fila["id_analista_gestion_comercial"];
			}
		}
		
		$queryDB = "SELECT id_usuario, nombre, apellido from usuarios 
			where id_usuario = '".$id_analista."' order by nombre, apellido, id_usuario";
		$rs1 = sqlsrv_query($link, $queryDB);
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
			echo utf8_decode( $fila1["nombre"]);
		}
	?></td>
	<td style="vertical-align:top;" align="center"><?php echo $fila["estado"] ?></td>
	<td style="vertical-align:top;" align="center"><?php if ($mostrar_adjuntos) { ?><a href="reqexcep_adjuntos.php?id_reqexcep=<?php echo $fila["id_reqexcep"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&reqexcepb=<?php echo $_REQUEST["reqexcepb"] ?>&id_tipob=<?php echo $_REQUEST["id_tipob"] ?>&id_areab=<?php echo $_REQUEST["id_areab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/adjuntar.png" title="Adjuntos"></a><?php } else { echo "&nbsp;"; } ?></td>
	<td style="vertical-align:top;" width="65"><?php echo $fila["fecha_creacion"] ?>,<br><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<?php if ($_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center" style="vertical-align:top;"><?php if ($fila["estado"] == "PENDIENTE" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $fila["usuario_creacion"] == $_SESSION["S_LOGIN"])) { ?><input type="checkbox" name="chk<?php echo $fila["id_reqexcep"] ?>" value="1"><?php } else { echo "&nbsp;"; } ?></td><?php } ?>
</tr>
<?php

		$j++;
	}
	
?>
</table>
<br>
<?php

	if ($_SESSION["S_SOLOLECTURA"] != "1")
	{
	
?>
<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value = 'borrar'"></p>
<?php

	}
	
?>
</form>



<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>

<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>


<script>
    function cargarReporteSimulaciones() {

$('#tablaFDC').DataTable( {
    serverSide: true,
    scrollX: true,
    dom: 'Bfrtip',
    buttons: [ {	
        extend: 'excelHtml5',
        title: 'FDC',
        footer:false
    } ],

    "destroy": true,
"select": true,
"ajax": {
    url: '../bd/consultasTablas.php',
		data: "exe=consultarRequerimientos",
"type":"POST"

},
"deferRender": true,

    "initComplete": function(settings, json) {
Swal.close();	
    },
    
    "bPaginate":true,
    "bFilter" : true,   
    "bProcessing": true,
    "pageLength": 40,
    "columns": [
    { title: 'Simulacion', mData: 'id_simulacion', orderable: false},
    { title: 'fecha_estudio', mData: 'fecha_estudio', orderable: false},
    { title: 'cedula', mData: 'cedula'},
    { title: 'nombre', mData: 'nombre'},
    { title: 'pagaduria', mData: 'pagaduria'},
    { title: 'institucion', mData: 'institucion'}

],


    "language": {"sProcessing":     "Procesando...","sLengthMenu":     "Mostrar _MENU_ registros","sZeroRecords":    "No se encontraron resultados","sEmptyTable":     "Ningún dato disponible en esta tabla","sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros","sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros","sInfoFiltered":   "(filtrado de un total de _MAX_ registros)","sInfoPostFix":    "","sSearch":         "Buscar:","sUrl":            "","sInfoThousands":  ",","sLoadingRecords": "Cargando...","oPaginate": {"sFirst":    "Primero","sLast":     "Último","sNext":     "Siguiente","sPrevious": "Anterior"},"oAria": {"sSortAscending":  ": Activar para ordenar la columna de manera ascendente","sSortDescending": ": Activar para ordenar la columna de manera descendente"}}
});


}
</script>
<?php include("bottom.php"); ?>
