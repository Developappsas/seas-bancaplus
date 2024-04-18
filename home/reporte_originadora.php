<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>


<script language="JavaScript">



function chequeo_forma() {

	with (document.formato) {
		

		a=window.open('reporte_originadora2.php?fecha_inicialbm='+fecha_inicialbm.options[fecha_inicialbm.selectedIndex].value+'&fecha_inicialba='+fecha_inicialba.options[fecha_inicialba.selectedIndex].value+'&fecha_finalbm='+fecha_finalbm.options[fecha_finalbm.selectedIndex].value+'&fecha_finalba='+fecha_finalba.options[fecha_finalba.selectedIndex].value,'LCONSFS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
	}

}

//-->

</script>

<table border="0" cellspacing=1 cellpadding=2>

<tr>

	<td class="titulo"><center><b>Reporte Produccion Originadora</b><br><br></center></td>

</tr>

</table>

<form name=formato method=post action="reporte_originadora2.php">
<table>
<tr>
<td>
<div class="box1 clearfix">

<table border="0" cellspacing=1 cellpadding=2>



<tr>

	<td align="right">F. Inicial</td><td>

		

		<select name="fecha_inicialbm">

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

		<select name="fecha_inicialba">

			<option value="">A&ntilde;o</option>

<?php



for ($i = 2015; $i <= date("Y"); $i++)

{

	echo "<option value=\"".$i."\">".$i."</option>";

}



?>

		</select>

		
	</td>

</tr>

<tr>

	<td align="right">F. Final</td><td>

		

		<select name="fecha_finalbm">

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

		<select name="fecha_finalba">

			<option value="">A&ntilde;o</option>

<?php



for ($i = 2015; $i <= date("Y"); $i++)

{

	echo "<option value=\"".$i."\">".$i."</option>";

}



?>

		</select>

		
	</td>

</tr>

</table>
</div>
</td>
</tr>

</table>

<p align="center">

<input type="button" value="Consultar" onClick="chequeo_forma()"/>

</p>

</form>











<?php include("bottom.php"); ?>

