<?php
include('../functions.php');
include('../function_blob_storage.php');
/**
 * 2016-03-22 Campos Dirección, Ciudad, Telefono, Celular y Correo actualizables DESDE LA TABLA SOLICITUD
 * 001, 002
 * 2016-03-22 Se requiere calificación de la cartera
 * 003, 004
 * 2016-03-22 Información del Prepago de un crédito
 * 005, 006
 * 2016-03-29 Gestion de cartera
 * 007, 008, 009
 */

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")) {
    exit;
}
$link = conectar();

if ($_REQUEST["ext"]) {
    $sufijo = "_ext";

    $tipo = "1";
} else {
    $tipo = "0";
}

if (!$_REQUEST["ext"]) {
    $queryDB = "SELECT si.*, so.direccion so_direccion, ci.municipio so_ciudad, so.ciudad ciudad_residencia, so.tel_residencia, so.celular, so.email so_email, ed.nombre as comprador_prepago 
         from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
         LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
        LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
        LEFT JOIN entidades_desembolso ed ON si.id_compradorprep = ed.id_entidad 
        where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '" . $label_viable . "' AND ((si.id_subestado IN (" . $subestado_compras_desembolso . ") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN ('" . $subestado_desembolso . "', '78', '" . $subestado_desembolso_cliente . "', '" . $subestado_desembolso_pdte_bloqueo . "')))))";

    $queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
} else {
    $queryDB = "SELECT si.*, si.direccion as so_direccion, si.ciudad as so_ciudad, '' as ciudad_residencia, si.telefono as tel_residencia, si.email as so_email, ed.nombre as comprador_prepago from simulaciones_ext si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN entidades_desembolso ed ON si.id_compradorprep = ed.id_entidad where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND si.estado IN ('DES', 'CAN')";
}

if ($_SESSION["S_SECTOR"]) {
    $queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}




$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs)) {
    exit;
}

$comprador_prepago = $fila["comprador_prepago"];
$fecha_prepago = $fila["fecha_prepago"];
$valor_prepago = $fila["valor_prepago"];
$valor_liquidacion = $fila["valor_liquidacion"];
$prepago_intereses = $fila["prepago_intereses"];
$prepago_seguro = $fila["prepago_seguro"];
$prepago_cuotasmora = $fila["prepago_cuotasmora"];
$prepago_segurocausado = $fila["prepago_segurocausado"];
$prepago_gastoscobranza = $fila["prepago_gastoscobranza"];
$prepago_totalpagar = $fila["prepago_totalpagar"];
$nombre_grabadoprep = $fila["nombre_grabadoprep"];
$prepago_aprobado = $fila["prepago_aprobado"];

$retanqueo_libranza_cancelacion = $fila["retanqueo_libranza_cancelacion"];
$retanqueo_valor_cancelacion = $fila["retanqueo_valor_cancelacion"];

if ($retanqueo_libranza_cancelacion) {
    $valor_liquidacion = $fila["retanqueo_valor_liquidacion"];
    $prepago_intereses = $fila["retanqueo_intereses"];
    $prepago_seguro = $fila["retanqueo_seguro"];
    $prepago_cuotasmora = $fila["retanqueo_cuotasmora"];
    $prepago_segurocausado = $fila["retanqueo_segurocausado"];
    $prepago_gastoscobranza = $fila["retanqueo_gastoscobranza"];
    $prepago_totalpagar = $fila["retanqueo_totalpagar"];
}

switch ($fila["opcion_credito"]) {
    case "CLI":
        $opcion_cuota = $fila["opcion_cuota_cli"];
        $opcion_desembolso = $fila["opcion_desembolso_cli"];
        break;
    case "CCC":
        $opcion_cuota = $fila["opcion_cuota_ccc"];
        $opcion_desembolso = $fila["opcion_desembolso_ccc"];
        break;
    case "CMP":
        $opcion_cuota = $fila["opcion_cuota_cmp"];
        $opcion_desembolso = $fila["opcion_desembolso_cmp"];
        break;
    case "CSO":
        $opcion_cuota = $fila["opcion_cuota_cso"];
        $opcion_desembolso = $fila["opcion_desembolso_cso"];
        break;
}

if (!$fila["sin_seguro"])
    $seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
else
    $seguro_vida = 0;

$cuota_corriente = $opcion_cuota - $seguro_vida;

if ($_REQUEST["ext"])
    $cuota_corriente -= $fila["cobro_adicional_en_cuota"];

$rs1 = sqlsrv_query($link, "SELECT SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN CASE WHEN valor_cuota - saldo_cuota - interes - seguro > 0 THEN valor_cuota - saldo_cuota - interes - seguro + abono_capital ELSE abono_capital END ELSE 0 END END) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$capital_recaudado = $fila1["s"];

$saldo_capital = $fila["valor_credito"] - $capital_recaudado;

$queryDB = "select COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

if ($fila["estado"] == "CAN" && $fila["sin_seguro"] && !$fecha_prepago && !$retanqueo_libranza_cancelacion)
    $queryDB .= " AND valor_cuota > 0";
else
    $queryDB .= " AND fecha <= GETDATE()";

$rs1 = sqlsrv_query($link, $queryDB);

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_causadas = $fila1["c"];

$rs1 = sqlsrv_query($link, "select COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pagada = '1'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_pagadas = $fila1["c"];

if ($fila["sin_seguro"])
    $seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;

$rs1 = sqlsrv_query($link, "select COUNT(*) as c, SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_mora = $fila1["c"];

$total_mora = $fila1["s"];

if (!$cuotas_mora)
    $total_pagar = $saldo_capital * (1 + $fila["tasa_interes"] / 100.00) + $seguro_vida + $seguro_causado;
else
    $total_pagar = $saldo_capital + ((($saldo_capital * $fila["tasa_interes"] / 100.00) + $seguro_vida) * $cuotas_mora) + $seguro_causado;

if ($cuotas_mora > 2) {
    $gastos_cobranza = $total_pagar * 0.2;

    $total_pagar += $gastos_cobranza;
}

// 003
if ($fila["estado"] == "CANCELADO") {
    $calificacion = "CANCELADO";
} else if ($cuotas_mora) {
    $limite1_calificacion = ($cuotas_mora * 30) - 29;
    $limite2_calificacion = $cuotas_mora * 30;
    $calificacion = $limite1_calificacion . " a " . $limite2_calificacion;
} else {
    $calificacion = "AL DIA";
}

$rs1 = sqlsrv_query($link, "SELECT SUM(valor_cuota - saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$total_recaudado = $fila1["s"];

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="cartera.php?ext=<?php echo $_REQUEST["ext"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo">
            <center><b>Detalle Cartera</b><br><br></center>
        </td>
    </tr>
</table>
<form name="formato" method="post" action="cartera_actualizar2.php?ext=<?php echo $_REQUEST["ext"] ?>">
    <input type="hidden" name="action" value="">
    <input type="hidden" name="id" value="">
    <input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
    <input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
    <input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
    <input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
    <input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
    <input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
    <input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
    <table border="0" cellspacing=1 cellpadding=2 align="center">
        <tr>
            <td valign="top">
                <h2>DATOS CLIENTE</h2>
                <div class="box1 clearfix">
                    <table border="1" cellspacing=1 cellpadding=2 align="right">
                        <tr>
                            <td>NO LIBRANZA</td>
                            <td><input type="text" name="no_libranza" value="<?php echo $fila["nro_libranza"] ?>" style="width:200; background-color:#8DB4E3;" readonly></td>
                        </tr>
                        <tr>
                            <td>NOMBRE</td>
                            <td><input type="text" name="nombre" value="<?php echo utf8_decode($fila["nombre"]) ?>" style="width:200;" readonly></td>
                        </tr>
                        <tr>
                            <td>N&Uacute;MERO DE C&Eacute;DULA</td>
                            <td><input type="text" name="cedula" value="<?php echo $fila["cedula"] ?>" style="width:200;" readonly></td>
                        </tr>
                        <!-- 002 -->
                        <tr>
                            <td>DIRECCI&Oacute;N</td>
                            <td><input type="text" name="direccion" value="<?php echo utf8_decode($fila["so_direccion"]) ?>" style="width:200;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD" <?php } else { ?>" readonly<?php } ?>></td>
                        </tr>
                        <tr>
                            <td>CIUDAD</td>
                            <td><input type="text" name="ciudad" value="<?php echo utf8_decode($fila["so_ciudad"]) ?>" style="width:200;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD" <?php } else { ?>" readonly<?php } ?>>
                                <input type="hidden" name="ciudad_residencia" value="<?php echo $fila["ciudad_residencia"] ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>TEL&Eacute;FONO</td>
                            <td><input type="text" name="telefono" value="<?php echo utf8_decode($fila["tel_residencia"]) ?>" style="width:200;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD" <?php } else { ?>" readonly<?php } ?>></td>
                        </tr>
                        <tr>
                            <td>CELULAR</td>
                            <td><input type="text" name="movil" value="<?php echo utf8_decode($fila["celular"]) ?>" style="width:200;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD" <?php } else { ?>" readonly<?php } ?>></td>
                        </tr>
                        <tr>
                            <td>CORREO ELECTR&Oacute;NICO</td>
                            <td><input type="text" name="mail" value="<?php echo utf8_decode($fila["so_email"]) ?>" style="width:200;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD" <?php } else { ?>" readonly<?php } ?>></td>
                        </tr>
                        <tr>
                            <td>PAGADUR&Iacute;A</td>
                            <td><input type="text" name="pagaduria" value="<?php echo utf8_decode($fila["pagaduria"]) ?>" style="width:200;" readonly></td>
                        </tr>
                        <tr>
                            <td>FECHA <?php if (!$_REQUEST["ext"]) { ?>ESTUDIO<?php } else { ?>NEGOCIACI&Oacute;N<?php } ?></td>
                            <td><input type="text" name="fecha_estudio" value="<?php echo $fila["fecha_estudio"] ?>" style="width:200;" readonly></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>&nbsp;</td>
            <td valign="top">
                <h2>CR&Eacute;DITO</h2>
                <div class="box1 oran clearfix">
                    <table border="0" cellspacing=1 cellpadding=2>
                        <?php

                        if (!$_REQUEST["ext"]) {

                        ?>
                            <tr>
                                <td>SOLICITADO</td>
                                <td><input type="text" name="opcion_desembolso" value="<?php echo number_format($opcion_desembolso, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                            </tr>
                        <?php

                        }

                        ?>
                        <tr>
                            <td>PLAZO</td>
                            <td><input type="text" name="plazo" value="<?php echo $fila["plazo"] ?>" size="15" style="text-align:right;" readonly></td>
                        </tr>
                        <tr>
                            <td>TASA DE INTER&Eacute;S DEL CR&Eacute;DITO</td>
                            <td><input type="text" name="tasa_interes" value="<?php echo $fila["tasa_interes"] ?>" size="15" style="text-align:right;" readonly></td>
                        </tr>
                        <tr>
                            <td>CUOTA CORRIENTE</td>
                            <td><input type="text" name="cuota_corriente" value="<?php echo number_format($cuota_corriente, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                        </tr>
                        <?php

                        if (!$_REQUEST["ext"]) {

                        ?>
                            <tr>
                                <td>SEGURO DE VIDA</td>
                                <td><input type="text" name="seguro_vida" value="<?php echo number_format($seguro_vida, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                            </tr>
                        <?php

                        } else {

                        ?>
                            <tr>
                                <td>OTROS COBROS</td>
                                <td><input type="text" name="cobro_adicional_en_cuota" value="<?php echo number_format($fila["cobro_adicional_en_cuota"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                            </tr>
                        <?php

                        }

                        ?>
                        <tr>
                            <td>CUOTA TOTAL</td>
                            <td><input type="text" name="opcion_cuota" value="<?php echo number_format($opcion_cuota, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                        </tr>
                        <tr>
                            <td>VALOR CR&Eacute;DITO</td>
                            <td><input type="text" name="valor_credito" value="<?php echo number_format($fila["valor_credito"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                        </tr>
                        <?php

                        if ($fila["estado"] == "CAN" && $fila["sin_seguro"] && !$fecha_prepago && !$retanqueo_libranza_cancelacion) {

                        ?>
                            <tr>
                                <td>SEGURO CAUSADO</td>
                                <td><input type="text" name="seguro_causado" value="<?php echo number_format($seguro_causado, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                            </tr>
                        <?php

                        }

                        if (!$_REQUEST["ext"]) {

                        ?>
                            <tr>
                                <td>FECHA DESEMBOLSO</td>
                                <td><input type="text" name="fecha_desembolso" value="<?php echo $fila["fecha_desembolso"] ?>" size="15" style="text-align:center;" readonly></td>
                            </tr>
                        <?php

                        }

                        ?>
                        <tr>
                            <td>FECHA PRIMERA CUOTA (<a href="#" onClick="window.open('simulaciones_primeracuota.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>', 'PRIMERA_CUOTA<?php echo $_REQUEST["id_simulacion"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=550,height=400,top=0,left=0');">Historial</a>)</td>
                            <td><input type="text" name="fecha_primera_cuota" value="<?php echo $fila["fecha_primera_cuota"] ?>" size="15" style="text-align:center;<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) { ?> background-color:#EAF1DD;" onChange="if (validarfecha(this.value) == false) {
                                            this.value = '<?php echo $fila["fecha_primera_cuota"] ?>';
                                            return false
                                        }" <?php } else { ?>" readonly<?php } ?>><input type="hidden" name="fecha_primera_cuotah" value="<?php echo $fila["fecha_primera_cuota"] ?>"></td>
                        </tr>
                        <?php

                        if ($fila["estado"] == "CAN") {

                        ?>
                            <tr>
                                <td>PREPAGADO A FONDEADOR</td>
                                <td><?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") { ?><input type="checkbox" name="prepagado_fondeador" id="prepagado_fondeador" value="1" <?php if ($fila["prepagado_fondeador"]) { ?> checked<?php } ?>><?php } else { ?><input type="text" name="prepagado_fondeador_texto" value="<?php if ($fila["prepagado_fondeador"]) {
                                                                                                                                                                                                                                                                                                                                                                    echo "SI";
                                                                                                                                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                                                                                                                                    echo "NO";
                                                                                                                                                                                                                                                                                                                                                                } ?>" size="15" style="text-align:center;" readonly><input type="hidden" name="prepagado_fondeador" value="<?php echo $fila["prepagado_fondeador"] ?>"><?php } ?></td>
                            </tr>
                        <?php

                        } else {

                        ?>
                            <input type="hidden" name="prepagado_fondeador" value="<?php echo $fila["prepagado_fondeador"] ?>">
                        <?php

                        }

                        //1. Anteriormente no se validaba que no estuviera Cancelado
                        //2. Si no est� cancelado y hay fecha prepago indica que est� Desembolsado con prepago pdte por aprobar
                        if (((($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA") && $fila["estado"] != "CAN" && !$fecha_prepago) || (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $fila["estado"] == "CAN")) && $_SESSION["S_SOLOLECTURA"] != "1") {

                        ?>
                            <tr>
                                <td colspan="2" align="center"><input type=submit value="Actualizar" onClick="document.formato.action.value = 'actualizar';"></td>
                            </tr>
                        <?php

                        }

                        ?>
                    </table>
                </div>
            </td>
            <?php

            if ($fecha_prepago || $retanqueo_libranza_cancelacion) {

            ?>
                <!-- 005 -->
                <td>&nbsp;</td>
                <td valign="top">
                    <h2><?php if ($fecha_prepago) { ?>INFORMACI&Oacute;N PREPAGO<?php } else { ?>CANCELACI&Oacute;N POR RETANQUEO<? } ?></h2>
                    <div class="box1 clearfix">
                        <table border="0" cellspacing=1 cellpadding=2>
                            <?php

                            if ($fecha_prepago) {

                            ?>
                                <tr>
                                    <td>COMPRADOR</td>
                                    <td><input type="text" name="comprador_prepago" value="<?php echo utf8_decode($comprador_prepago) ?>" style="width:200;" readonly></td>
                                </tr>
                                <tr>
                                    <td>FECHA PREPAGO</td>
                                    <td><input type="text" name="fecha_prepago" value="<?php echo $fecha_prepago ?>" style="width:200; text-align:center;" readonly></td>
                                </tr>
                                <tr>
                                    <td>VALOR PREPAGO</td>
                                    <td><input type="text" name="valor_prepago" value="<?php echo number_format($valor_prepago, 0, ".", ",") ?>" style="width:200; text-align:right; font-weight:bold;" readonly></td>
                                </tr>
                            <?php

                            } else {

                            ?>
                                <tr>
                                    <td>NO LIBRANZA</td>
                                    <td><input type="text" name="retanqueo_libranza_cancelacion" value="<?php echo $retanqueo_libranza_cancelacion ?>" style="width:200;" readonly></td>
                                </tr>
                                <tr>
                                    <td>VALOR</td>
                                    <td><input type="text" name="retanqueo_valor_cancelacion" value="<?php echo number_format($retanqueo_valor_cancelacion, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                            <?php

                            }
                            ?>
                            <tr>
                                <td>SALDO CAPITAL</td>
                                <td><input type="text" name="valor_liquidacion" value="<?php echo number_format($valor_liquidacion, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                            </tr>
                            <?php

                            if ($prepago_totalpagar) {

                            ?>
                                <tr>
                                    <td>INTERESES CORRIENTES</td>
                                    <td><input type="text" name="prepago_intereses" value="<?php echo number_format($prepago_intereses, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>SEGURO DE VIDA</td>
                                    <td><input type="text" name="prepago_seguro" value="<?php echo number_format($prepago_seguro, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>CUOTA EN MORA</td>
                                    <td><input type="text" name="prepago_cuotasmora" value="<?php echo $prepago_cuotasmora ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                                <?php

                                if ($fila["sin_seguro"]) {

                                ?>
                                    <tr>
                                        <td>SEGURO CAUSADO</td>
                                        <td><input type="text" name="prepago_segurocausado" value="<?php echo number_format($prepago_segurocausado, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                    </tr>
                                <?php

                                }

                                ?>
                                <tr>
                                    <td>GASTOS DE COBRANZA</td>
                                    <td><input type="text" name="prepago_gastoscobranza" value="<?php echo number_format($prepago_gastoscobranza, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>TOTAL A PAGAR</td>
                                    <td><input type="text" name="prepago_totalpagar" value="<?php echo number_format($prepago_totalpagar, 0, ".", ",") ?>" style="width:200; text-align:right;" readonly></td>
                                </tr>
                            <?php

                            }

                            if ($nombre_grabadoprep) {

                            ?>
                                <tr>
                                    <td>SOPORTE</td>
                                    <td><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $nombre_grabadoprep) ?>', 'SOPPREP<?php echo $_REQUEST["id_simulacion"] ?>','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Archivo Soporte"></a></td>
                                </tr>
                            <?php

                            }

                            if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $fecha_prepago && !$prepago_aprobado && $_SESSION["S_SOLOLECTURA"] != "1") {

                            ?>
                                <tr>
                                    <td colspan="2" align="center">
                                        <input type=submit value="Aplicar" onClick="document.formato.action.value = 'aplicar_prepago';">&nbsp;&nbsp;&nbsp;
                                        <input type=submit value="Reversar" onClick="document.formato.action.value = 'reversar_prepago';">
                                    </td>
                                </tr>
                            <?php

                            }

                            ?>
                        </table>
                    </div>
                </td>
                <?php

            } else {
                if ($fila["estado"] != "CAN") {

                ?>
                    <td>&nbsp;</td>
                    <td valign="top">
                        <h2>VALORES HOY</h2>
                        <div class="box1 clearfix">
                            <table border="0" cellspacing=1 cellpadding=2>
                                <tr>
                                    <td>SALDO CAPITAL</td>
                                    <td><input type="text" name="saldo_capital" value="<?php echo number_format($saldo_capital, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>INTERESES CORRIENTES</td>
                                    <td><input type="text" name="interes_corrientes" value="<?php echo number_format($saldo_capital * $fila["tasa_interes"] / 100.00, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <?php

                                if (!$_REQUEST["ext"]) {

                                ?>
                                    <tr>
                                        <td>SEGURO DE VIDA</td>
                                        <td><input type="text" name="seguro_vida2" value="<?php echo number_format($seguro_vida, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                    </tr>
                                <?php

                                }

                                ?>
                                <tr>
                                    <td>CUOTAS CAUSADAS</td>
                                    <td><input type="text" name="cuotas_causadas" value="<?php echo $cuotas_causadas ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>CUOTAS PAGADAS</td>
                                    <td><input type="text" name="cuotas_pagadas" value="<?php echo $cuotas_pagadas ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>CUOTA EN MORA</td>
                                    <td><input type="text" name="cuotas_mora" value="<?php echo $cuotas_mora ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <tr>
                                    <td>TOTAL EN MORA</td>
                                    <td><input type="text" name="total_mora" value="<?php echo number_format($total_mora, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <?php

                                if ($fila["sin_seguro"]) {

                                ?>
                                    <tr>
                                        <td>SEGURO CAUSADO</td>
                                        <td><input type="text" name="seguro_causado" value="<?php echo number_format($seguro_causado, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                    </tr>
                                <?php

                                }

                                ?>
                                <tr>
                                    <td>GASTOS DE COBRANZA</td>
                                    <td><input type="text" name="gastos_cobranza" value="<?php echo number_format($gastos_cobranza, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                </tr>
                                <?php

                                if ($_SESSION["FUNC_BOLSAINCORPORACION"] && !$_REQUEST["ext"]) {

                                ?>
                                    <tr>
                                        <td>BOLSA INCORPORACI&Oacute;N</td>
                                        <td><input type="text" name="saldo_bolsa" value="<?php echo number_format($fila["saldo_bolsa"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                                    </tr>
                                <?php

                                }

                                ?>
                                <tr>
                                    <td>TOTAL A PAGAR</td>
                                    <td><input type="text" name="total_pagar" value="<?php echo number_format($total_pagar, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
                                </tr>
                                <!-- 004 -->
                                <tr>
                                    <td>CALIFICACI&Oacute;N</td>
                                    <td><input type="text" name="calificacion" value="<?php echo $calificacion ?>" size="15" style="text-align:center;" readonly></td>
                                </tr>

                            </table>
                        </div>
                    </td>
            <?php

                }
            }

            ?>
            <td>&nbsp;</td>
            <td valign="top">
                <h2>RESPONSABLE GESTION DE COBRO</h2>
                <div class="box1 clearfix">
                    <table border="0" cellspacing=1 cellpadding=2>
                        <tr>
                            <td>RESPONSABLE</td>
                            <td>
                                <select name="responsable_gestion_cobro" style="background-color:#EAF1DD;">
                                    <option></option>
                                    <?php
                                    $queryDB = sqlsrv_query($link, "SELECT * FROM resp_gestion_cobros");

                                    while ($res = sqlsrv_fetch_array($queryDB)) {
                                        $selected = "";
                                        if ($fila["resp_gestion_cobranza"] == $res["id_resp_cobros"]) {
                                            $selected = "selected";
                                        }
                                        echo "<option value='" . $res["id_resp_cobros"] . "' " . $selected . ">" . $res["nombre"] . "</option>";
                                    }
                                    ?>
                                </select>


                            </td>
                        </tr>
                        <tr>
                            <td>DETALLE</td>
                            <td><input type="text" name="detalle_responsable_gestion_cobro" value="<?php echo $fila["detalle_resp_gestion_cobranza"]; ?>" size="15" style="text-align:right;background-color:#EAF1DD;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input type=submit value="Actualizar" onClick="document.formato.action.value = 'actualizar_cobranza';"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="7" valign="top">
                <div style="margin-top: 20px;" align="center">
                    <h2>CONSULTAS A CENTRALES</h2>
                    <table border="1" class="table">
                        <tr align="center" style="text-align: center;">

                            <td align="center">
                                <div id="consulta_cifinUP">
                                    <img class="logos_buros" src="../images/logo_transunion.png">
                                    <h3>Ubica Plus</h3>
                                </div>
                                <div class="badges">
                                    <div class="badge-success" id="disponible-cifinUP" type="button" servicio="UBICAPLUS" proveedor="TRANSUNION">
                                        <img src="../images/chequeado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-info" id="nodisponible-cifinUP" servicio="UBICAPLUS" proveedor="TRANSUNION">
                                        <img src="../images/novalidado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-error" id="calendario-cifinUP" servicio="UBICAPLUS" proveedor="TRANSUNION" data-service="consulta_ws">
                                        <img src="../images/calendario.png" style="margin-top: inherit;">
                                    </div>
                                </div>
                            </td>

                            <td align="center">
                                <div id="consulta_cifin">
                                    <img class="logos_buros" src="../images/logo_transunion.png">
                                    <h3>Informacion Comercial</h3>
                                </div>
                                <div class="badges">
                                    <div class="badge-success" id="disponible-cifin" type="button" servicio="INFORMACION_COMERCIAL" proveedor="TRANSUNION">
                                        <img src="../images/chequeado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-info" id="nodisponible-cifin" servicio="INFORMACION_COMERCIAL" proveedor="TRANSUNION">
                                        <img src="../images/novalidado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-error" id="calendario-cifin" servicio="INFORMACION_COMERCIAL" proveedor="TRANSUNION" data-service="consulta_ws">
                                        <img src="../images/calendario.png" style="margin-top: inherit;">
                                    </div>
                                </div>
                            </td>

                            <td align="center">
                                <div id="consulta_datacredito">
                                    <img class="logos_buros" src="../images/logo_datacredito.png">
                                    <h3>Data Credito (HC)</h3>
                                </div>
                                <div class="badges">
                                    <div class="badge-success" id="disponible-experianDC" type="button" servicio="HDC_ACIERTA" proveedor="EXPERIAN">
                                        <img src="../images/chequeado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-info" id="nodisponible-experianDC" servicio="HDC_ACIERTA" proveedor="EXPERIAN">
                                        <img src="../images/novalidado.png" style="margin-top: inherit;">
                                    </div>
                                    <div class="badge-error" id="calendario-experianDC" servicio="HDC_ACIERTA" proveedor="EXPERIAN" data-service="consulta_ws">
                                        <img src="../images/calendario.png" style="margin-top: inherit;">
                                    </div>
                                </div>
                            </td>
                            <td align="center">
                                <div id="consulta_datacredito">
                                    <br>
                                    <h3>Scoring Resumido</h3>
                                </div>
                                <div class="badges">
                                    <div class="badge-success" id="resumenScoring" type="button" servicio="RESUMEN_SCORING" proveedor="RESUMEN_SCORING">
                                        <img src="../images/chequeado.png" style="margin-top: inherit;">
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                </div>
            </td>
        </tr>
    </table>


    <br>
    <br>
    <h2>VALORES RECAUDADOS</h2>
    <table border="0" cellspacing=1 cellpadding=2" align="center" class="tab1">
        <tr>
            <th>Cuota</th>
            <th>F Cuota</th>
            <th>F Recaudo</th>
            <th>Valor Recaudado</th>
            <th>Seguro</th>
            <th>Inter&eacute;s</th>
            <th>Capital</th>
            <th>Tipo Recaudo</th>
            <th><img src="../images/archivo.png" title="Archivo Soporte"></th>
            <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th>&nbsp;</th><?php } ?>
        </tr>
        <?php

        $queryDB = "select pd.consecutivo, pd.cuota, pd.valor, pg.fecha as fecha_recaudo, pg.nombre_grabado, cu.fecha as fecha_cuota, pg.tipo_recaudo, pd.valor_antes_pago, cu.valor_cuota, cu.capital, cu.interes, cu.seguro";

        if (!$_REQUEST["ext"])
            $queryDB .= ", bap.consecutivo as aplicado_desde_bolsa";
        else
            $queryDB .= ", NULL as aplicado_desde_bolsa";

        $queryDB .= " from pagos_detalle" . $sufijo . " pd INNER JOIN pagos" . $sufijo . " pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo LEFT JOIN cuotas" . $sufijo . " cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota";

        if (!$_REQUEST["ext"])
            $queryDB .= " LEFT JOIN bolsainc_aplicaciones bap ON pg.id_simulacion = bap.id_simulacion_pago AND pg.consecutivo = bap.consecutivo_pago";

        $queryDB .= " where pd.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pd.valor > 0 order by pd.consecutivo, pd.cuota";

        $rs1 = sqlsrv_query($link, $queryDB);

        while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
            $seguro_ya_aplicado = 0;
            $interes_ya_aplicado = 0;
            $capital_ya_aplicado = 0;
            $valor_recaudo = $fila1["valor"];

            //Valor aplicado en recaudo anterior
            $valor_ya_aplicado = $fila1["valor_cuota"] - $fila1["valor_antes_pago"];

            if ($valor_ya_aplicado > 0) {
                if ($valor_ya_aplicado <= $fila1["seguro"])
                    $seguro_ya_aplicado = $valor_ya_aplicado;
                else
                    $seguro_ya_aplicado = $fila1["seguro"];

                $valor_ya_aplicado -= $seguro_ya_aplicado;
            }

            if ($valor_ya_aplicado > 0) {
                if ($valor_ya_aplicado <= $fila1["interes"])
                    $interes_ya_aplicado = $valor_ya_aplicado;
                else
                    $interes_ya_aplicado = $fila1["interes"];

                $valor_ya_aplicado -= $interes_ya_aplicado;
            }

            if ($valor_ya_aplicado > 0) {
                if ($valor_ya_aplicado <= $fila1["capital"])
                    $capital_ya_aplicado = $valor_ya_aplicado;
                else
                    $capital_ya_aplicado = $fila1["capital"];
            }

            $seguro = $fila1["seguro"] - $seguro_ya_aplicado;

            if ($valor_recaudo <= $seguro)
                $seguro = $valor_recaudo;

            $valor_recaudo -= $seguro;

            $interes = $fila1["interes"] - $interes_ya_aplicado;

            if ($valor_recaudo <= $interes)
                $interes = $valor_recaudo;

            $valor_recaudo -= $interes;

            $capital = $fila1["capital"] - $capital_ya_aplicado;

            if ($valor_recaudo <= $capital)
                $capital = $valor_recaudo;

            if (strpos($fila1["tipo_recaudo"], "ABONOCAPITAL") !== false)
                $capital = $fila1["valor"];

            $total_valor_recaudado += $fila1["valor"];
            $total_seguro += $seguro;
            $total_interes += $interes;
            $total_capital += $capital;

        ?>
            <tr>
                <td align="center"><input type="text" name="cuota<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo $fila1["cuota"] ?>" size="5" style="text-align:center;" readonly></td>
                <td><input type="text" name="fecha_cuota<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo $fila1["fecha_cuota"] ?>" size="15" style="text-align:center;" readonly></td>
                <td><input type="text" name="fecha_recaudo<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo $fila1["fecha_recaudo"] ?>" size="15" style="text-align:center;" readonly></td>
                <td align="center"><input type="text" name="valor_recaudado<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo number_format($fila1["valor"], 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                <td align="center"><input type="text" name="seguro<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo number_format($seguro, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                <td align="center"><input type="text" name="interes<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo number_format($interes, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                <td align="center"><input type="text" name="capital<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo number_format($capital, 0, ".", ",") ?>" size="15" style="text-align:right;" readonly></td>
                <td><input type="text" name="tipo_recaudo<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="<?php echo $fila1["tipo_recaudo"] ?>" size="23" readonly></td>
                <td><?php if ($fila1["nombre_grabado"]) { ?><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $fila1["nombre_grabado"]) ?>', 'SOP<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Archivo Soporte"></a><?php } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                    echo "&nbsp;";
                                                                                                                                                                                                                                                                                                                                                                                                                                                } ?></td>
                <!--<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if (!$fila1["aplicado_desde_bolsa"] && (($fila["estado"] != "CAN" && !$fecha_prepago) || ($fila["estado"] == "CAN" && $fila1["cuota"] == "0"))) { ?><input type="checkbox" name="chk<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="1" disabled><?php } else { ?>&nbsp;<?php } ?></td><?php } ?>-->
                <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><?php if (!$fila1["aplicado_desde_bolsa"]) { ?><input type="checkbox" name="chk<?php echo $fila1["consecutivo"] . "_" . $fila1["cuota"] ?>" value="1" disabled><?php } else { ?>&nbsp;<?php } ?></td><?php } ?>
            </tr>
        <?php

            $consecutivo = $fila1["consecutivo"];

            $cuota = $fila1["cuota"];
        }

        ?>
        <tr>
            <td align="center"><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && !($fila["estado"] != "CAN" && $fecha_prepago) && $_SESSION["S_SOLOLECTURA"] != "1") { ?><input type="button" value="Ingresar Recaudo" onClick="window.open('aplicarpago.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'APLICARPAGO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=1000,height=500,top=0,left=0');"><?php } ?></td>
            <td align="center"><?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && $fila["estado"] == "DES" && !$fecha_prepago && $_SESSION["S_SOLOLECTURA"] != "1") { ?><input type="button" value="Ingresar Prepago" onClick="window.open('aplicarprepago.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'APLICARPREPAGO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=1000,height=435,top=0,left=0');"><?php } ?></td>
            <td align="center"><a href="#" onClick="window.open('cartera_exportarimputacion.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>', 'EXPORTARIMPUTACION', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0')"><img src="../images/excel.png"></a></td>
            <td align="center"><input type="text" name="total_valor_recaudado" value="<?php echo number_format($total_valor_recaudado, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
            <td align="center"><input type="text" name="total_seguro" value="<?php echo number_format($total_seguro, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
            <td align="center"><input type="text" name="total_interes" value="<?php echo number_format($total_interes, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
            <td align="center"><input type="text" name="total_capital" value="<?php echo number_format($total_capital, 0, ".", ",") ?>" size="15" style="text-align:right; font-weight:bold;" readonly></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="image" src="../images/delete.png" title="Borrar" onClick="document.formato.action.value = 'borrarrecaudo'"></td><?php } ?>
        </tr>
    </table>
</form>
<script>
    document.formato.chk<?php echo $consecutivo . "_" . $cuota ?>.disabled = false;
</script>
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script src="../js/consultas_centrales.js?<?php echo rand(); ?>" type="text/javascript"></script>
<?php include("bottom.php"); ?>