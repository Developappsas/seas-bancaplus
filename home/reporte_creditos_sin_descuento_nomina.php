<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=CreditoSinDescuentoNomina.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA")) {
    exit;
}

$link = conectar();


?>
<table border="0">
    <tr>
        <th>Cédula</th>
        <th>F. Desembolso Inicial</th>
        <th>F. Primera Cuota</th>
        <th>Mes Prod</th>
        <th>Nombre</th>
        <th>No. Libranza</th>
        <th>Tasa</th>
        <th>Cuota Total</th>
        <th>Cuota Corriente</th>
        <th>Seguro</th>
        <th>Vr. Crédito</th>
        <th>Pagaduría</th>
        <th>Plazo</th>
    </tr>
    <?php
    $queryDB = "SELECT s.cedula, s.fecha_desembolso, s.fecha_primera_cuota, " . "FORMAT(s.fecha_cartera, 'yyyy-MM') as mes_prod, s.nombre, s.nro_libranza, s.tasa_interes, s.opcion_credito, s.valor_credito, " . "opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, " . "opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, " . "opcion_cuota_cso, opcion_desembolso_cso, valor_credito, valor_por_millon_seguro, sin_seguro, porcentaje_extraprima "  . "pagaduria, plazo from simulaciones s " . "left join (SELECT id_simulacion from pagos where tipo_recaudo = 'NOMINA' group by id_simulacion) p "  . "on s.id_simulacion = p.id_simulacion "    . "where s.estado = 'DES' and p.id_simulacion IS NULL";
    
    
    $rs = sqlsrv_query($link, $queryDB);

    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
        switch ($fila["opcion_credito"]) {
            case "CLI": $opcion_cuota = $fila["opcion_cuota_cli"];
                $opcion_desembolso = $fila["opcion_desembolso_cli"];
                break;
            case "CCC": $opcion_cuota = $fila["opcion_cuota_ccc"];
                $opcion_desembolso = $fila["opcion_desembolso_ccc"];
                break;
            case "CMP": $opcion_cuota = $fila["opcion_cuota_cmp"];
                $opcion_desembolso = $fila["opcion_desembolso_cmp"];
                break;
            case "CSO": $opcion_cuota = $fila["opcion_cuota_cso"];
                $opcion_desembolso = $fila["opcion_desembolso_cso"];
                break;
        }

		if (!$fila["sin_seguro"])
			$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		else
			$seguro_vida = 0;

        $cuota_corriente = $opcion_cuota - round($seguro_vida);
        ?>
        <tr>
            <td><?php echo $fila["cedula"] ?></td>
            <td><?php echo $fila["fecha_desembolso"] ?></td>
            <td><?php echo $fila["fecha_primera_cuota"] ?></td>
            <td><?php echo $fila["mes_prod"] ?></td>
            <td><?php echo utf8_decode($fila["nombre"]) ?></td>
            <td><?php echo $fila["nro_libranza"] ?></td>
            <td><?php echo $fila["tasa_interes"] ?></td>
            <td><?php echo $opcion_cuota ?></td>
            <td><?php echo $cuota_corriente ?></td>
            <td><?php echo round($seguro_vida) ?></td>
            <td><?php echo $fila["valor_credito"] ?></td>
            <td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
            <td><?php echo $fila["plazo"] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
