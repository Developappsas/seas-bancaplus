<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}

$link = conectar();


$parametros = sqlsrv_query($link, "select * from parametros where codigo = 'MPRFA'");

$fila1 = sqlsrv_fetch_array($parametros);

$mprfa = $fila1["valor"];

$mprfaa = explode("-", $mprfa);

$ano = $mprfaa[0];

$ano = $ano-1;

$mprfn = $ano."-".$mprfaa[1];

?>
<?php include("top.php"); ?>
<meta http-equiv="refresh" content="300">
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b>Indicadores Cartera</b><br><br></center></td>
</tr>
</table>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<form name="formato2" method="post" action="indicadores_cartera.php">
<table>
<tr>
	<td>
		<div class="box1 clearfix">
		<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td valign="bottom">Unidad de Negocio<br>
				<select name="unidadnegociob" onChange="window.location.href='indicadores_cartera.php?unidadnegociob='+this.value;">
					<option value=""></option>
<?php

$queryDB = "select id_unidad, nombre from unidades_negocio where 1 = 1";

$queryDB .= " AND id_unidad IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

$queryDB .= " order by id_unidad";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	if ($fila1["id_unidad"] == $_REQUEST["unidadnegociob"])
		$selected = " selected";
	else
		$selected = "";
	
	echo "<option value=\"".$fila1["id_unidad"]."\"".$selected.">".utf8_decode($fila1["nombre"])."</option>\n";
}

?>
				</select>&nbsp;&nbsp;&nbsp;
			</td>
<?php

if (!$_SESSION["S_SECTOR"])
{

?>
			<td valign="bottom">Sector<br>
				<select name="sectorb" onChange="window.location.href='indicadores_cartera.php?sectorb='+this.value;">
					<option value=""></option>
					<option value="PUBLICO"<?php if ($_REQUEST["sectorb"] == "PUBLICO") { echo " selected"; } ?>>PUBLICO</option>
					<option value="PRIVADO"<?php if ($_REQUEST["sectorb"] == "PRIVADO") { echo " selected"; } ?>>PRIVADO</option>
				</select>
			</td>
<?php

}
	
?>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</form>
<table border="0" cellspacing=1 cellpadding=2>
<tr>	
	<td valign="top" width="950">
<?php

$queryDB = "select mes_prod AS mes_produccion, mes_prod AS mes_produccion2, SUM(valor_credito) AS valor_credito,count(mes_prod) AS num_creditos from vwcartera where mes_prod is not null and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB_suma = "select SUM(valor_credito) as valor_credito from vwcartera where mes_prod IS NOT NULL and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

$queryDB_suma .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_suma .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["unidadnegociob"])
{
	$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
	
	$queryDB_suma .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
}

if ($_REQUEST["sectorb"])
{
	$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
	
	$queryDB_suma .= " AND sector = '".$_REQUEST["sectorb"]."'";
}

$queryDB .= " group by mes_prod order by mes_prod DESC";

$rs = sqlsrv_query($link, $queryDB);
$rs_suma = sqlsrv_query($link, $queryDB_suma);

$fila_suma = sqlsrv_fetch_array($rs_suma);

$suma = $fila_suma["valor_credito"];

?>
		<script type="text/javascript">
			google.load("visualization", "1", {packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
			
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Mes', 'Valor'],
					<?php

		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
		{
			echo "['".trim(strtoupper($fila["mes_produccion"]))."', ".$fila["valor_credito"]."],";
		}

			?>
					

				]);
				
				var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
				formatter.format(data, 1);
				
				var options = {
					title: 'ORIGINACION	POR MES',
					legend: { position: 'none', maxLines: 1 }
				};
				
				var chart = new google.visualization.BarChart(document.getElementById('cumplimiento'));
				chart.draw(data, options);
			}
		</script>
		<div id="cumplimiento" style="width:550px; height:350px; float:left;"></div>
		<?php

if (sqlsrv_num_rows($rs))
{

?>
		<div style="width:400px; float:right;">
			<br>
			<table class="tab3">
			<tr>
				
				<th>Mes</th>
				<th width="100">$</th>
				<th width="40">#</th>
			</tr>
<?php

	sqlsrv_data_seek($rs, 0);
	
	$cuantos = 0;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$cuantos += $fila["num_creditos"];
		
?>
			<tr>
				
				<td align="left"><?php echo ($fila["mes_produccion"]) ?></td>
				<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
				<td align="right"><?php echo number_format($fila["num_creditos"]) ?></td>
			</tr>
<?php

	}
	
?>
			<tr class="tr_bold">
				<td><b>TOTALES</b></td>
				<td align="right"><b><?php echo number_format($suma, 0) ?></b></td>
				<td align="right"><b><?php echo number_format($cuantos, 0) ?></b></td>
			</tr>
			</table>
		</div>
<?php

}

?>
	</td>
</tr>
<!--Finaliza la primera grafica -->


<!--Inicia la segunda grafica -->
<tr>
	<td valign="top" width="950">
<?php

$queryDB = "select pagaduria,SUM(valor_credito) AS valor_credito,count(mes_prod) AS num_creditos from vwcartera where mes_prod is not null and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB_suma = "select SUM(valor_credito) as valor_credito from vwcartera where mes_prod IS NOT NULL and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

$queryDB_suma .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_suma .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["unidadnegociob"])
{
	$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
	
	$queryDB_suma .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
}

if ($_REQUEST["sectorb"])
{
	$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
	
	$queryDB_suma .= " AND sector = '".$_REQUEST["sectorb"]."'";
}

$queryDB .= " group by pagaduria";

$rs = sqlsrv_query($link, $queryDB);

$rs_suma = sqlsrv_query($link, $queryDB_suma);

$fila_suma = sqlsrv_fetch_array($rs_suma);

$suma = $fila_suma["valor_credito"];

?>
		<script type="text/javascript">
			google.load("visualization", "1", {packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
			
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Estado', 'Valor'],


<?php

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	echo "['".trim(strtoupper($fila["pagaduria"]))."', ".$fila["valor_credito"]."],";
}

?>
				]);
				
				var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
				formatter.format(data, 1);
				
   				var options = {
					title: 'ORIGINACION POR PAGADURIA',
					is3D: true,
				};
				
				var chart = new google.visualization.PieChart(document.getElementById('gestioncomercial'));
				chart.draw(data, options);
			}
		</script>
		<div id="gestioncomercial" style="width:550px; height:350px; float:left;"></div>
<?php

if (sqlsrv_num_rows($rs))
{

?>
		<div style="width:400px; float:left;">
			<br>
			<table border="0" cellspacing=1 cellpadding=2 class="tab3">
			<tr>
				
				<th>Pagaduria</th>
				<th width="100">$</th>
				<th width="40">%</th>
			</tr>
<?php

	sqlsrv_data_seek($rs, 0);
	
	$cuantos = 0;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$cuantos += $fila["num_creditos"];
		
?>
			<tr>
				
				<td align="left"><?php echo ($fila["pagaduria"]) ?></td>
				<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
				<td align="right"><?php echo number_format($fila["valor_credito"] / $suma * 100.00, 2)  ?></td>
			</tr>
<?php

	}
	
?>
			<tr class="tr_bold">
				<td><b>TOTALES</b></td>
				<td align="left"><b><?php echo number_format($suma, 0) ?></b></td>
				<td align="left"><b>100.00</b></td>
			</tr>
			</table>
		</div>
<?php

}

?>
 	</td>
</tr>
<!--Finaliza la segunda grafica -->

<!--Inicia la tercera grafica -->
	<tr>
	<td valign="top" width="950">
<?php

$queryDB = "select cuotas_mora as calificacion,SUM(valor_credito - capital_recaudado) AS saldo_capital,count(mes_prod) AS num_creditos from vwcartera where mes_prod is not null and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB_suma = "select SUM(valor_credito - capital_recaudado) as saldo_capital from vwcartera where mes_prod IS NOT NULL and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

$queryDB_suma .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
	
	$queryDB_suma .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["unidadnegociob"])
{
	$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
	
	$queryDB_suma .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
}

if ($_REQUEST["sectorb"])
{
	$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
	
	$queryDB_suma .= " AND sector = '".$_REQUEST["sectorb"]."'";
}

$queryDB .= " group by cuotas_mora order by cuotas_mora";

$rs = sqlsrv_query($link, $queryDB);

$rs_suma = sqlsrv_query($link, $queryDB_suma);

$fila_suma = sqlsrv_fetch_array($rs_suma);

$suma = $fila_suma["saldo_capital"];

?>
		<script type="text/javascript">
			google.load("visualization", "1", {packages:["corechart","bar"]});
			google.setOnLoadCallback(drawChart);
			
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Estado', 'Valor'],
<?php

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$limite1_calificacion = ($fila["calificacion"] * 30) - 29;
	$limite2_calificacion = $fila["calificacion"] * 30;
	if ($fila["calificacion"] != '0') {
		$calificacion = $limite1_calificacion." a ".$limite2_calificacion;
	}else{
		$calificacion = 'AL DIA';
	}
	echo "['".trim(strtoupper($calificacion))."', ".$fila["saldo_capital"]."],";
}

?>
				]);
				
				var formatter = new google.visualization.NumberFormat({pattern: '$###,###'});
				formatter.format(data, 1);
				
				var options = {
					title: 'CARTERA POR EDADES',
					is3D: true,
				};
				
				var chart = new google.visualization.PieChart(document.getElementById('produccion'));
				chart.draw(data, options);
			}
		</script>
		<div id="produccion" style="width:550px; height:350px; float:left;"></div>
<?php

if (sqlsrv_num_rows($rs))
{

?>
		<div style="width:400px; float:left;">
			<br>
			<table border="0" cellspacing=1 cellpadding=2 class="tab3">
			<tr>
				<th>&nbsp;</th>
				<th width="100">$</th>
				<th width="40">#</th>
			</tr>
<?php

	sqlsrv_data_seek($rs, 0);
	
	$cuantos = 0;
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
		$cuantos += $fila["num_creditos"];
		$limite1_calificacion = ($fila["calificacion"] * 30) - 29;
		$limite2_calificacion = $fila["calificacion"] * 30;
		if ($fila["calificacion"] != '0') {
			$calificacion = $limite1_calificacion." a ".$limite2_calificacion;
		}else{
			$calificacion = 'AL DIA';
		}
		
?>
			<tr>
				<td><?php echo trim(strtoupper($calificacion)) ?></td>
				<td align="right"><?php echo number_format($fila["saldo_capital"], 0) ?></td>
				<td align="right"><?php echo number_format($fila["num_creditos"], 0) ?></td>
				
			</tr>
<?php

	}
	
?>
			<tr class="tr_bold">
				<td><b>TOTALES</b></td>
				<td align="right"><b><?php echo number_format($suma, 0) ?></b></td>
				<td align="right"><b><?php echo number_format($cuantos, 0) ?></b></td>
			</tr>
			</table>
		</div>
<?php

}

?>
 	</td>
<!--Finaliza la tercera grafica -->

<!--Inicia la tercera grafica -->
	<tr>
	<td valign="top" width="950">
<?php

$queryDB = "select distinct pagaduria from vwcartera where mes_prod is not null";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["unidadnegociob"])
{
	$queryDB .= " AND id_unidad_negocio = '".$_REQUEST["unidadnegociob"]."'";
}

if ($_REQUEST["sectorb"])
{
	$queryDB .= " AND sector = '".$_REQUEST["sectorb"]."'";
}

$rs = sqlsrv_query($link, $queryDB);


?>
		 
    <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["corechart","bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Pagaduria', 'AL DIA', '1 - 30', '31 - 60', '61 - 90', '91 - 120', '121 - 150', '151 - 180', '181 - 210', '211 - 240', '241 - 270', '271 - 300', '301 - 330', '331 - 360'],
<?php
          while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
			{
				echo "['".trim(strtoupper($fila["pagaduria"]))."'";

				$queryDB1 = "select cuotas_mora as calificacion,SUM(valor_credito - capital_recaudado) AS saldo_capital,count(mes_prod) AS num_creditos from vwcartera where mes_prod is not null and pagaduria = '" .$fila["pagaduria"]."' and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";

				$queryDB_suma = "select SUM(valor_credito - capital_recaudado) as saldo_capital from vwcartera where mes_prod IS NOT NULL and pagaduria = '" .$fila["pagaduria"]."' and mes_prod >= '" .$mprfn. "' and mes_prod < '" .$mprfa. "' ";


				$queryDB1 .= " group by cuotas_mora order by cuotas_mora";
				
				$rs1 = sqlsrv_query($link, $queryDB1);
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			for($i = 0; $i <= 12; $i ++)
	    {
			//$cuantos += $fila1["num_creditos"];
			if($i == $fila1["calificacion"]){
				echo ",'".$fila1["num_creditos"]."'";
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			}else{
				echo ", 0";
			}
		} echo "],";}
		
?>

          
        ]);

        var options = {
          chart: {
            title: 'EDADES POR PAGADURIA',
            
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, options);
      }
    </script>	
		<div id="barchart_material" style="width: 950px;; height:500px"></div>

 	</td>
<!--Finaliza la tercera grafica --> 


</table>
<?php include("bottom.php"); ?>
