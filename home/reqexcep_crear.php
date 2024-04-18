<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}
echo "pruebaa";





$link = conectar();

$queryDB = "SELECT si.estado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND si.estado != 'ANU'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL")
{
	$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
}
else
{
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}

$simulacion_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs))
{
	echo "entra";
	exit;

}

?>
<?php include("top.php"); ?>
<script language="JavaScript">

function negar_instruccion_giro(id_tipo) {
	if(id_tipo.value == 5){
        alert("La instuccion de giro desde este momento solo se podrá realizar por medio de la venta del simulador moviendo el credito de subestado de 6.7 al subestado 6.1");
        id_tipo.value = '';
    }
}

function chequeo_forma() {
	with (document.formato) {
		if(id_tipo.value == 5){
            alert("La instuccion de giro desde este momento solo se podrá realizar por medio de la venta del simulador moviendo el credito de subestado de 6.7 al subestado 6.1");
            id_tipo.value = '';
            return false;
        }
		if ((reqexcep.value == "") || (id_tipo.value == "") || (id_area.value == "") || (observacion.value == "")) {
			alert("Los campos marcados con asterisco(*) son obligatorios");
			return false;
		}
		if (id_tipo.value == "<?php echo $tiporeq_cdd ?>" && fecha_vencimiento.value == "") {
			alert("Debe establecer la fecha de vencimiento");
			return false;
		}
		for (i = 1; i <= 5; i++) {
			if (document.getElementById("descripcion"+i).value != "" && document.getElementById("archivo"+i).value == "") {
				alert("Debe digitar una observacion y adjuntar un archivo (Adjunto "+i+")");
				return false;
			}
			if (document.getElementById("archivo"+i).value != "" && document.getElementById("descripcion"+i).value == "") {
				alert("Debe digitar una observacion y adjuntar un archivo (Adjunto "+i+")");
				return false;
			}
		}
		
		ReplaceComilla(observacion)
	}
}

function valor_tipos(x){return x.substring(0,x.indexOf('-'))}

function texto_tipos(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargartipos(reqexcep, objeto_tipos) {
	var num_tipos;
	var j, k = 1;

	num_tipos = 200;

	objeto_tipos.length = num_tipos;

<?php

$queryDB = "select id_tipo, nombre from tipos_reqexcep where estado = '1' AND reqexcep = 'REQUERIMIENTO'";

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
	<td valign="top" width="18"><a href="simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
	<td class="titulo"><center><b>Ingresar Req/Excep</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="reqexcep_crear2.php" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
<input type="hidden" name="back" value="<?php echo $_REQUEST["back"] ?>">
<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
			<table border="0" cellspacing=1 cellpadding=2>
			<tr>
				<td align="right">* Req/Excep</td>
				<td><select name="reqexcep" onChange="Cargartipos(this.value, document.formato.id_tipo);" style="background-color:#EAF1DD; width:200px">
						<option value=""></option>
						<option value="REQUERIMIENTO">REQUERIMIENTO</option>
						<option value="EXCEPCION">EXCEPCION</option>
					</select>
				</td>
				<td width="20">&nbsp;</td>
				<td align="right">* Tipo</td>
				<td><select name="id_tipo" onchange="negar_instruccion_giro(this);" style="background-color:#EAF1DD; width:200px;">
						<option value=""></option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">* &Aacute;rea</td>
				<td><select name="id_area" style="background-color:#EAF1DD; width:200px;">
						<option value=""></option>
<?php

$queryDB = "select id_area, nombre from areas_reqexcep where estado = '1' order by nombre";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["id_area"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
						
					</select>
				</td>
				<td width="20">&nbsp;</td>
				<td align="right">F Vencimiento</td>
				<td><input type="text" name="fecha_vencimiento" size="10" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" style="text-align:center; background-color:#EAF1DD;"></td>
			</tr>
			<tr>
				<td align="right" style="vertical-align: top">* Descripci&oacute;n</td>
				<td colspan="4"><textarea name="observacion" rows="2" cols="70" style="background-color:#EAF1DD;"></textarea></td>
			</tr>
<?php

for ($i = 1; $i <= 5; $i++)
{

?>
			<tr>
				<td align="right">Adjunto <?php echo $i ?></td>
				<td><input type="text" id="descripcion<?php echo $i ?>" name="descripcion<?php echo $i ?>" maxlength="255" size="26" style="background-color:#EAF1DD;" placeholder="Descripci&oacute;n"></td>
				<td width="20">&nbsp;</td>
				<td colspan="2"><input type="file" id="archivo<?php echo $i ?>" name="archivo<?php echo $i ?>"/></td>
			</tr>
<?php

}

?>
			</table>
		</div>
	</td>
</tr>
</table>
<br>
<p align="center"><input type="submit" value="Ingresar"></p>
</form>
<?php include("bottom.php"); ?>
