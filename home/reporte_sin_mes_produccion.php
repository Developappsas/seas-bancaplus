<?php
include ('../functions.php');
if(!$_SESSION["S_LOGIN"]||$_SESSION["S_TIPO"]!="ADMINISTRADOR"){
exit;	
}
include("top.php");
?>
<script language="JavaScript" src="../date.js"></script>
<script language="JavaScript"></script>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Reporte sin mes de produccion</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post>
<table>
<tr>
<td>
<div class="box1 clearfix">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td align="right">C&eacute;dula</td><td>
		<input id="cedula" type="text" name="cedula">
	</td>
</tr>
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
<tr>
	<td align="right">Sector</td><td>
		<select id="sector" name="sector">
			<option value=""></option>
			<option value="PUBLICO">PUBLICO</option>
			<option value="PRIVADO">PRIVADO</option>
		</select>
	</td>
</tr>
<?php

}

?>
<tr>
	<td align="right">Pagadur&iacute;a</td><td>
		<select id="pagaduria" name="pagaduria">
			<option value=""></option>
<?php

$queryDB = "select nombre as pagaduria from pagadurias where 1 = 1";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = mysqli_query($link, $queryDB);

while ($fila1 = mysqli_fetch_assoc($rs1)) {
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
		</select>
	</td>
</tr>
<?php

if ($_SESSION["FUNC_FDESEMBOLSO"])
{

?>
<tr>
	<td align="right">F. Registro Inicial</td><td>
		<input type="hidden" name="fechades_inicialb" size="10" maxlength="10">
		<select id="fecha_inicial_dia_dm" name="fechades_inicialbd">
			<option value="">D&iacute;a</option>
<?php

	for ($i = 1; $i <= 31; $i++)
	{
		if (strlen($i) == 1)
		{
			$j = "0".$i;
		}
		else
		{
			$j = $i;
		}
		
		echo "<option value=\"".$j."\">".$j."</option>";
	}
	
?>
		</select>
		<select id="fecha_inicial_mes_dm" name="fechades_inicialbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select id="fecha_inicial_año_dm"name="fechades_inicialba">
			<option value="">A&ntilde;o</option>
<?php

	for ($i = 2014; $i <= date("Y"); $i++)
	{
		echo "<option value=\"".$i."\">".$i."</option>";
	}
	
?>
		</select>
		<a href="javascript:show_calendar('formato.fechades_inicialb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<tr>
	<td align="right">F. Registro Inicial</td><td>
		<input type="hidden" name="fechades_finalb" size="10" maxlength="10">
		<select id="fecha_final_dia_dm" name="fechades_finalbd">
			<option value="">D&iacute;a</option>
<?php

	for ($i = 1; $i <= 31; $i++)
	{
		if (strlen($i) == 1)
		{
			$j = "0".$i;
		}
		else
		{
			$j = $i;
		}
		
		echo "<option value=\"".$j."\">".$j."</option>";
	}
	
?>
		</select>
		<select id="fecha_final_mes_dm" name="fechades_finalbm">
			<option value="">Mes</option>
			<option value="01">Ene</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>	
			<option value="04">Abr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Ago</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Dic</option>
		</select>
		<select  id="fecha_final_año_dm" name="fechades_finalba">
			<option value="">A&ntilde;o</option>
<?php

	for ($i = 2014; $i <= date("Y"); $i++)
	{
		echo "<option value=\"".$i."\">".$i."</option>";
	}
	
?>
		</select>
		<a href="javascript:show_calendar('formato.fechades_finalb');"><img src="../images/calendario.gif" border=0></a>
	</td>
</tr>
<?php

}

?>

	</td>
</tr>
<tr>
	<td align="right">Estado</td><td>
		<select id="estado_tesoreria" name="estado">
			<option value=""></option>
			<option value="ABI">ABIERTO</option>
			<option value="PAR">PARCIAL</option>
			<option value="CER">CERRADO</option>
		</select>
	</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<input id="S_IDUNIDADNEGOCIO" value="<?=$_SESSION["S_IDUNIDADNEGOCIO"]?>" hidden>
<input type="button" id="consultar" value="Consultar"/>
</p>
</form>
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sheetjs/xlsx.mini.min.js"></script>
<script>
 $("#consultar").click(function(){
 	

 	$.ajax({
 		type:"POST",
 		url:"../servicios/reportes/reporte_sin_mes_produccion.php",
 		data:{
 			S_IDUNIDADNEGOCIO: $("#S_IDUNIDADNEGOCIO").val(),
 			fechades_inicial: $("#fecha_inicial_año_dm").val()+"-"+$("#fecha_inicial_mes_dm").val()+"-"+$("#fecha_inicial_dia_dm").val(),
 			fechades_final: $("#fecha_final_año_dm").val()+"-"+$("#fecha_final_mes_dm").val()+"-"+$("#fecha_final_dia_dm").val(),
 			sector:$("#sector").val(),
 			estado_tesoreria: $("#estado_tesoreria").val(),
 			pagaduria:$("#pagaduria").val(),
 			cedula: $("#cedula").val()
 		},
 		success: function(resultado){
 			if(resultado.estado==200){
 				Swal.fire({
		            title: 'Generando archivo...',
		            text: 'Por favor, espera mientras se genera el archivo de Excel.',
		            allowOutsideClick: false,
		            showConfirmButton: false,
		            onBeforeOpen: () => {
		                Swal.showLoading();
		            }
        		});

 				const workbook = XLSX.utils.book_new();
                const worksheet = XLSX.utils.json_to_sheet(resultado.data);
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Hoja1');
                XLSX.writeFile(workbook, 'sin_mes_prod.xlsx' );
                Swal.close();
 			}else if(resultado.estado==300){
 				swal.fire({
 					icon: "warning",
 					title:"Consulta terminada",
 					text: "No se encontraron datos para los filtros seleccionados"
 				})
 			}else if(resultado.estado==404){
 				swal.fire({
 					icon: "error",
 					title:"Error",
 					text: resultado.mensaje
 				})
 			}

 		}
 	})
 })

</script>
<?php include("bottom.php"); ?>