<?php include ('../functions.php'); ?>
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
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
	
	$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	if (!(sqlsrv_num_rows($rs1)))
	{
		echo "<script>alert('El usuario no tiene oficina asociada. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
		
		exit;
	}
}

?>
<html>
<head>
<link rel="STYLESHEET" type="text/css" href="../sty.css">
<script language="JavaScript" src="../functions.js"></script>
</head>
<body style="background-color:#E7F2F8">
<div id="contenedor5">
	<div align="center">
<?php

$queryDB = "SELECT si.* from simulaciones si where si.cedula = '".$_REQUEST["cedula"]."' AND (si.estado = 'DES' OR (si.estado = 'EST' AND si.estado_tesoreria = 'PAR')) AND id_simulacion != '".$_REQUEST["id_simulacion"]."'";

$queryDB .= " order by si.id_simulacion DESC";
// echo $queryDB;

$rs = sqlsrv_query($link, $queryDB);
if ($rs == false) {
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
}

if(sqlsrv_num_rows($rs)){

	echo "prueba";
?>
    <form name="formato3" method="post" action="simulaciones_creditos_vigentes.php">
        <input type="hidden" name="action" value="">
	    <input type="hidden" name="cedula" value="<?php echo $_REQUEST["cedula"] ?>">
        <table border="0" cellspacing=1 cellpadding=2 class="tab1">
            <tr>
				<th>PAGUDAR&Iacute;A</th>
                <th>CUOTA</th>
                <th>SALDO CAPITAL</th>
                <th>EDAD MORA</th>
                <th>SALDO MORA</th>
                <th>F &Uacute;LTIMO RECAUDO</th>
                <th>VR &Uacute;LTIMO RECAUDO</th>
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
		
		switch($fila["opcion_credito"])
		{
			case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
						break;
			case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
						break;
			case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
						break;
			case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
						break;
		}
		
		$rs1 = sqlsrv_query($link, "SELECT SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN IIF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas where id_simulacion = '".$fila["id_simulacion"]."'");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$capital_recaudado = $fila1["s"];
		
		$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
		
		$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c, SUM(saldo_cuota) as s from cuotas where id_simulacion = '" . $fila["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'");
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$cuotas_mora = $fila1["c"];
		
		$total_mora = $fila1["s"];
		
		if ($fila["estado"] == "CANCELADO") {
		    $calificacion = "CANCELADO";
		} else if ($cuotas_mora) {
		    $limite1_calificacion = ($cuotas_mora * 30) - 29;
		    $limite2_calificacion = $cuotas_mora * 30;
		    $calificacion = $limite1_calificacion . " a " . $limite2_calificacion;
		} else {
		    $calificacion = "AL DIA";
		}
		
		if ($fila["fecha_ultimo_recaudo"])
		{
			$rs1 = sqlsrv_query($link, "SELECT SUM(pd.valor) as s from pagos_detalle pd INNER JOIN pagos pa ON pd.id_simulacion = pa.id_simulacion AND pd.consecutivo = pa.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pa.fecha = '".$fila["fecha_ultimo_recaudo"]."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$valor_ultimo_recaudo = $fila1["s"];
		}
		else
		{
			$valor_ultimo_recaudo = 0;
		}
		
?>
			<tr <?php echo $tr_class ?>>
				<td><?php echo $fila["pagaduria"] ?></td>
				<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
				<td align="right"><?php echo number_format($saldo_capital, 0) ?></td>
				<td align="center"><?php echo $calificacion ?></td>
				<td align="right"><?php echo number_format($total_mora, 0) ?></td>
				<td align="center"><?php echo $fila["fecha_ultimo_recaudo"] ?></td>
				<td align="right"><?php echo number_format($valor_ultimo_recaudo, 0) ?></td>
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