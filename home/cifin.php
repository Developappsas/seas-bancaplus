<?php include('../functions.php'); ?>
<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
header("Content-type: text/html");
$link = conectar();

$rs = sqlsrv_query($link, "SELECT respuesta from consultas_externas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND servicio = 'INFORMACION_COMERCIAL'");

// $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$xmlstr = reemplazar_caracteres_WS2($fila["respuesta"]);

libxml_use_internal_errors(true);

$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

if ($objeto_ws === false) {
    foreach (libxml_get_errors() as $error) {
        if (!$transunion_infocomercial_error)
            $transunion_infocomercial_error = "Error cargando XML: ";
        else
            $transunion_infocomercial_error .= "; ";

        $transunion_infocomercial_error .= $error->message;
    }
}

//var_dump($objeto_ws);

if ($transunion_infocomercial_error) {
    echo $transunion_infocomercial_error;
} else {
    //echo $xmlstr;
    //echo $objeto_ws->Tercero->NumeroIdentificacion;
    //echo $objeto_ws->Informe->NaturalNacional["nombres"];
    //echo $objeto_ws->Informe["fechaConsulta"];

?>
<html>

<head>
    <style media="print,screen">
    table,
    td,
    th {
        padding: 2px;
    }

    th {
        font: bold 11px Arial;
        text-align: center;
        font-weight: bold;
        color: #36486E;
    }

    td {
        FONT-FAMILY: Arial;
        FONT-SIZE: 9px;
    }

    .th_titulo {
        font: bold 11px Arial;
        background: #005A85;
        text-align: center;
        font-weight: bold;
        color: #FFFFFF;
    }

    .td_label {
        font: 9px Arial;
        background: #005A85;
        text-align: center;
        color: #FFFFFF;
    }

    .td_label_l {
        font: 9px Arial;
        background: #005A85;
        color: #FFFFFF;
    }

    .td_label2 {
        font: bold 9px Arial;
        background: #C7C8CA;
        font-weight: bold;
    }

    .td_dato {
        font: 9px Arial;
        background: #E6E7E9;
    }

    .td_dato2 {
        font: 9px Arial;
        background: #C7C8CA;
    }

    .td_dato_b {
        font: 9px Arial;
        background: #E6E7E9;
        font-weight: bold;
    }

    .td_dato2_b {
        font: 9px Arial;
        background: #C7C8CA;
        font-weight: bold;
    }

    .td_logo {
        FONT-FAMILY: Arial;
        FONT-SIZE: 30px;
        color: #878B8D;
    }
    </style>
</head>

<body>
    <TABLE border="0" cellspacing="0" width="1124" align="center">
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" cellspacing="0" align="center">
                    <tr>
                        <td class="td_logo" align="right">Informaci&oacute;n
                            <hr color="#00508A">Comercial
                        </td>
                        <td width="20">&nbsp;</td>
                        <td>
                            <b>KREDIT PLUS
                                S.A.S.<br><?php echo $objeto_ws->Tercero->Fecha . ' ' . $objeto_ws->Tercero->Hora ?>
                            </b>
                        </td>
                    </tr>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="6">RESULTADO CONSULTA INFORMACI&Oacute;N COMERCIAL</th>
                    </tr>
                    <tr>
                        <td class="td_label_l">TIPO IDENTIFICACI&Oacute;N</td>
                        <td class="td_dato">
                            <?php echo $objeto_ws->Tercero->TipoIdentificacion ?>
                        </td>
                        <td class="td_label_l">EST DOCUMENTO</td>
                        <td class="td_dato">
                            <? echo $objeto_ws->Tercero->Estado ?>
                        </td>
                        <td class="td_label_l">FECHA</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->Fecha ?></td>
                    </tr>
                    <tr>
                        <td class="td_label_l">No. IDENTIFICACI&Oacute;N</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->NumeroIdentificacion ?></td>
                        <td class="td_label_l">FECHA EXPEDICI&Oacute;N</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->FechaExpedicion ?></td>
                        <td class="td_label_l">HORA</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->Hora ?></td>
                    </tr>
                    <tr>
                        <td class="td_label_l">NOMBRES APELLIDOS - RAZ&Oacute;N SOCIAL</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->NombreTitular ?></td>
                        <td class="td_label_l">LUGAR DE EXPEDICI&Oacute;N</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->LugarExpedicion ?></td>
                        <td class="td_label_l">USUARIO</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->Entidad ?></td>
                    </tr>
                    <tr>
                        <td class="td_label_l">ACTIVIDAD ECON&Oacute;MICA - CIIU</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->NombreCiiu ?></td>
                        <td class="td_label_l">RANGO EDAD PROBABLE</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->RangoEdad ?></td>
                        <td class="td_label_l">No. INFORME</td>
                        <td class="td_dato"><?php echo $objeto_ws->Tercero->NumeroInforme ?></td>
                    </tr>

                    <?php
                        foreach ($objeto_ws->Tercero->Reclamos as $reclamo) {
                            echo '<tr>
									<td class="td_label_l">RECLAMO</td>
									<td class="td_dato" colspan="5">' . $reclamo->Mensaje . '</td>
                    			</tr>';
                        }
                        ?>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="0" width="900" align="center">
                    <tr>
                        <td valign="top">* Todos los valores de la consulta est&aacute;n expresados en miles de pesos
                        </td>
                        <td align="right">Se presenta reporte negativo cuando la(s) persona(s) naturales y
                            jur&iacute;dicas efectivamente se encuentran en mora en sus cuotas u obligaciones<br>Se
                            presenta reporte positivo cuando la(s) persona(s) naturales y jur&iacute;dicas est&aacute;n
                            al d&iacute;a en sus obligaciones.</td>
                    </tr>
                </table>
                <br><br><br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" cellspacing="0" align="center">
                    <tr>
                        <th>RESUMEN ENDEUDAMIENTO</th>
                    </tr>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="11">RESUMEN DE OBLIGACIONES (COMO PRINCIPAL)</th>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2">OBLIGACIONES</td>
                        <td class="td_label" colspan="3">TOTALES</td>
                        <td class="td_label" colspan="3">OBLIGACIONES AL D&Iacute;A</td>
                        <td class="td_label" colspan="4">OBLIGACIONES EN MORA</td>
                    </tr>
                    <tr>
                        <td class="td_label">CANT</td>
                        <td class="td_label">SALDO TOTAL </td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">CANT</td>
                        <td class="td_label">SALDO TOTAL </td>
                        <td class="td_label">CUOTA</td>
                        <td class="td_label">CANT</td>
                        <td class="td_label">SALDO TOTAL </td>
                        <td class="td_label">CUOTA</td>
                        <td class="td_label">VALOR EN MORA</td>
                    </tr>

                    <?php
                        $registros = $objeto_ws->Tercero->Consolidado->ResumenPrincipal->Registro;
                        foreach ($registros as $registro) {
                            if ($registro->PaqueteInformacion != 'Subtotal Principal') {
                                echo '
									<tr>
										<td class="td_label_l">' . $registro->PaqueteInformacion . '</td>
										<td class="td_dato">' . $registro->NumeroObligaciones . '</td>
										<td class="td_dato">' . $registro->TotalSaldo . '</td>
										<td class="td_dato">' . $registro->ParticipacionDeuda . '</td>
										<td class="td_dato">' . $registro->NumeroObligacionesDia . '</td>
										<td class="td_dato">' . $registro->SaldoObligacionesDia . '</td>
										<td class="td_dato">' . $registro->CuotaObligacionesDia . '</td>
										<td class="td_dato">' . $registro->CantidadObligacionesMora . '</td>
										<td class="td_dato">' . $registro->SaldoObligacionesMora . '</td>
										<td class="td_dato">' . $registro->CuotaObligacionesMora . '</td>
										<td class="td_dato">' . $registro->ValorMora . '</td>
									</tr>';
                            }
                        }
                        ?>

                    <?php
                        $subtotal = $objeto_ws->Tercero->Consolidado->ResumenPrincipal->Registro;
                        foreach ($subtotal as $registro_subtotal) {
                            if (strcmp($registro_subtotal->PaqueteInformacion, 'Subtotal Principal') == 0) {
                                echo
                                '
									<tr>
                        			<td class="td_label2">SUBTOTAL PRINCIPAL</td>
                        			<td class="td_label2">' . $registro_subtotal->NumeroObligaciones . '</td>
                        			<td class="td_label2">' . $registro_subtotal->TotalSaldo . '</td>
                        			<td class="td_label2">' . $registro_subtotal->ParticipacionDeuda . '</td>
                        			<td class="td_label2">' . $registro_subtotal->NumeroObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->SaldoObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CuotaObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CantidadObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->SaldoObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CuotaObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->ValorMora . '</td>
									</tr>';
                            }
                        }
                        ?>
                    <tr>
                        <td colspan="11"><br></td>
                    </tr>
                    <tr>
                        <th class="th_titulo" colspan="11">RESUMEN DE OBLIGACIONES (COMO CODEUDOR Y OTROS)</th>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2">OBLIGACIONES</td>
                        <td class="td_label" colspan="3">TOTALES</td>
                        <td class="td_label" colspan="3">OBLIGACIONES AL D&Iacute;A</td>
                        <td class="td_label" colspan="4">OBLIGACIONES EN MORA</td>
                    </tr>
                    <tr>
                        <td class="td_label_l">CANT</td>
                        <td class="td_label">SALDO TOTAL</td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">CANT</td>
                        <td class="td_label">SALDO TOTAL</td>
                        <td class="td_label">CUOTA</td>
                        <td class="td_label">CANT</td>
                        <td class="td_label">SALDO TOTAL</td>
                        <td class="td_label">CUOTA</td>
                        <td class="td_label">VALOR EN MORA</td>
                    </tr>

                    <?php
                        $subtotal = $objeto_ws->Tercero->Consolidado->ResumenDiferentePrincipal->Registro;
                        foreach ($subtotal as $registro_subtotal) {
                            if (strcmp($registro_subtotal->PaqueteInformacion, 'Subtotal Principal') == 0) {
                                echo
                                '<tr>
                    				    <td class="td_label_l">__TIPO_OBL</td>
                    				    <td class="td_dato">__CANT_TOTAL</td>
                    				    <td class="td_dato">__SALDO_TOTAL</td>
                    				    <td class="td_dato">__PADE</td>
                    				    <td class="td_dato">__CANT_DIA</td>
                    				    <td class="td_dato">__SALDO_DIA</td>
                    				    <td class="td_dato">__CUOTA_DIA</td>
                    				    <td class="td_dato">__CANT_MORA</td>
                    				    <td class="td_dato">__SALDO_MORA</td>
                    				    <td class="td_dato">__CUOTA_MORA</td>
                    				    <td class="td_dato">__VALOR_MORA</td>
                    				</tr>';
                            }
                        }
                        ?>

                    <?php
                        $subtotal = $objeto_ws->Tercero->Consolidado->ResumenDiferentePrincipal->Registro;
                        foreach ($subtotal as $registro_subtotal) {
                            if (strcmp($registro_subtotal->PaqueteInformacion, 'Subtotal Principal') == 0) {
                                echo
                                '
									<tr>
                        			<td class="td_label2">SUBTOTAL CODEUDOR Y OTROS</td>
                        			<td class="td_label2">' . $registro_subtotal->NumeroObligaciones . '</td>
                        			<td class="td_label2">' . $registro_subtotal->TotalSaldo . '</td>
                        			<td class="td_label2">' . $registro_subtotal->ParticipacionDeuda . '</td>
                        			<td class="td_label2">' . $registro_subtotal->NumeroObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->SaldoObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CuotaObligacionesDia . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CantidadObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->SaldoObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->CuotaObligacionesMora . '</td>
                        			<td class="td_label2">' . $registro_subtotal->ValorMora . '</td>
									</tr>';
                            }
                        }
                        ?>

                    <tr>
                        <td colspan="11"><br></td>
                    </tr>
                    <tr>
                        <th class="th_titulo" colspan="11">RESUMEN TOTAL DE OBLIGACIONES</th>
                    </tr>
                    <?php
                        $total = $objeto_ws->Tercero->Consolidado->Registro;
                        echo
                        '<tr>
                    	    	<td class="td_label2">TOTAL</td>
                    	    	<td class="td_label2">' . $total->NumeroObligaciones . '</td>
                    	    	<td class="td_label2">' . $total->TotalSaldo . '</td>
                    	    	<td class="td_label2">' . $total->ParticipacionDeuda . '</td>
                    	    	<td class="td_label2">' . $total->NumeroObligacionesDia . '</td>
                    	    	<td class="td_label2">' . $total->SaldoObligacionesDia . '</td>
                    	    	<td class="td_label2">' . $total->CuotaObligacionesDia . '</td>
                    	    	<td class="td_label2">' . $total->CantidadObligacionesMora . '</td>
                    	    	<td class="td_label2">' . $total->SaldoObligacionesMora . '</td>
                    	    	<td class="td_label2">' . $total->CuotaObligacionesMora . '</td>
                    	    	<td class="td_label2">' . $total->ValorMora . '</td>
	                    	</tr>';
                        ?>
                </table>
                <br><br><br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" cellspacing="0" align="center">
                    <tr>
                        <th>INFORME DETALLADO</th>
                    </tr>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="13">INFORMACI&Oacute;N DE CUENTAS</th>
                    </tr>
                    <tr>
                        <td class="td_label">FECHA CORTE</td>
                        <td class="td_label">TIPO CONTRATO</td>
                        <td class="td_label">No. CUENTA</td>
                        <td class="td_label">ESTADO</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">ENTIDAD</td>
                        <td class="td_label">CIUDAD</td>
                        <td class="td_label">SUCURSAL</td>
                        <td class="td_label">FECHA APERTURA</td>
                        <td class="td_label">CUPO SOBREGIRO</td>
                        <td class="td_label">DIAS AUTOR</td>
                        <td class="td_label">FECHA PERMANENCIA</td>
                        <td class="td_label">CHEQ DEVUELTOS &Uacute;LTIMO MES</td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="13">ESTADO: VIGENTES</td>
                    </tr>

                    <?php
                        $cuentas_vigentes = $objeto_ws->Tercero->CuentasVigentes;
                        foreach ($cuentas_vigentes->Obligacion as $obligacion) {
                            echo
                            '<tr>
									<td class="td_dato">' . $obligacion->FechaCorte . '</td>
									<td class="td_dato">' . $obligacion->TipoContrato . '</td>
									<td class="td_dato">' . $obligacion->NumeroObligacion . '</td>
									<td class="td_dato">' . $obligacion->EstadoObligacion . '</td>
									<td class="td_dato">' . $obligacion->TipoEntidad . '</td>
									<td class="td_dato">' . $obligacion->NombreEntidad . '</td>
									<td class="td_dato">' . $obligacion->Ciudad . '</td>
									<td class="td_dato">' . $obligacion->Sucursal . '</td>
									<td class="td_dato">' . $obligacion->FechaApertura . '</td>
									<td class="td_dato">' . $obligacion->ValorInicial . '</td>
									<td class="td_dato">' . $obligacion->DiasCartera . '</td>
									<td class="td_dato">' . $obligacion->FechaPermanencia . '</td>
									<td class="td_dato">' . $obligacion->ChequesDevueltos . '</td>
								</tr>';
                        }
                        ?>

                    <?php
                        $reclamos = $objeto_ws->Tercero->CuentasVigentes;
                        foreach ($reclamos->Reclamos as $reclamo) {
                            echo
                            '<tr>
                    	    		<td class="td_dato_b" colspan="13">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RECLAMO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $reclamo->Mensaje . '
                    	    		</td>
								</tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="11"><br></td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="13">ESTADO: NO VIGENTES</td>
                    </tr>
                    <?php
                        $cuentas_vigentes = $objeto_ws->Tercero->CuentasNoVigentes;
                        foreach ($cuentas_vigentes->Obligacion as $obligacion) {
                            echo
                            '<tr>
									<td class="td_dato">' . $obligacion->FechaCorte . '</td>
									<td class="td_dato">' . $obligacion->TipoContrato . '</td>
									<td class="td_dato">' . $obligacion->NumeroObligacion . '</td>
									<td class="td_dato">' . $obligacion->EstadoObligacion . '</td>
									<td class="td_dato">' . $obligacion->TipoEntidad . '</td>
									<td class="td_dato">' . $obligacion->NombreEntidad . '</td>
									<td class="td_dato">' . $obligacion->Ciudad . '</td>
									<td class="td_dato">' . $obligacion->Sucursal . '</td>
									<td class="td_dato">' . $obligacion->FechaApertura . '</td>
									<td class="td_dato">' . $obligacion->ValorInicial . '</td>
									<td class="td_dato">' . $obligacion->DiasCartera . '</td>
									<td class="td_dato">' . $obligacion->FechaPermanencia . '</td>
									<td class="td_dato">' . $obligacion->ChequesDevueltos . '</td>
								</tr>';
                        }
                        ?>

                    <?php
                        $reclamos = $objeto_ws->Tercero->CuentasNoVigentes;
                        foreach ($reclamos->Reclamos as $reclamo) {
                            echo
                            '<tr>
                    	    		<td class="td_dato_b" colspan="13">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RECLAMO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $reclamo->Mensaje . '
                    	    		</td>
								</tr>';
                        }
                        ?>

                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="21">INFORMACI&Oacute;N ENDEUDAMIENTO EN SECTORES FINANCIERO,
                            ASEGURADOR Y SOLIDARIO</th>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2" colspan="2">FECHA CORTE</td>
                        <td class="td_label" rowspan="2">MODA</td>
                        <td class="td_label" rowspan="2">No. OBLIG</td>
                        <td class="td_label" rowspan="2">TIPO ENT</td>
                        <td class="td_label" rowspan="2">NOMBRE ENTIDAD</td>
                        <td class="td_label" rowspan="2">CIUDAD</td>
                        <td class="td_label" rowspan="2">CAL</td>
                        <td class="td_label" rowspan="2">MRC</td>
                        <td class="td_label" rowspan="2">TIPO GAR</td>
                        <td class="td_label" rowspan="2">F INICIO</td>
                        <td class="td_label" colspan="3">No. CUOTAS</td>
                        <td class="td_label" rowspan="2">CUPO APROB - VLR INIC</td>
                        <td class="td_label" rowspan="3">PAGO M&Iacute;NIM - VLR CUOTA</td>
                        <td class="td_label" rowspan="2">SIT OBLIG</td>
                        <td class="td_label" rowspan="2">NATU REES</td>
                        <td class="td_label" rowspan="2">No. REE</td>
                        <td class="td_label" rowspan="2">TIP PAG</td>
                        <td class="td_label" rowspan="2">F PAGO - F EXTIN</td>
                    </tr>
                    <tr>
                        <td class="td_label">PAC</td>
                        <td class="td_label">PAG</td>
                        <td class="td_label">MOR</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO CONT</td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">LCRE</td>
                        <td class="td_label">EST. CONTR</td>
                        <td class="td_label">CLF</td>
                        <td class="td_label">ORIGEN CARTERA</td>
                        <td class="td_label">SUCURSAL</td>
                        <td class="td_label">EST TITUL</td>
                        <td class="td_label">CLS</td>
                        <td class="td_label">COB GAR</td>
                        <td class="td_label">F TERM</td>
                        <td class="td_label">PER</td>
                        <td class="td_label" colspan="2">&nbsp;</td>
                        <td class="td_label">CUPO UTILI - SALDO CORT</td>
                        <td class="td_label">VALOR MORA</td>
                        <td class="td_label">REES</td>
                        <td class="td_label">MOR MAX</td>
                        <td class="td_label">MOD EXT</td>
                        <td class="td_label">F PERMAN</td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="21">OBLIGACIONES VIGENTES Y AL D&Iacute;A</td>
                    </tr>

                    <?php
                        $sector = $objeto_ws->Tercero->SectorFinancieroAlDia;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
									<td class="td_dato" rowspan="2" colspan="2">' . $obligacion->FechaCorte . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ModalidadCredito . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->MarcaTarjeta . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoGarantia . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
									<td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
									<td class="td_dato2" rowspan="3">' . $obligacion->ValorCuota . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NaturalezaReestructuracion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroReestructuraciones . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
								</tr>
								<tr>
                                    <td class="td_dato">PAC</td>
	                                <td class="td_dato">PAG</td>
	                                <td class="td_dato">MOR</td>
                                </tr>
								<tr>
									<td class="td_dato2">' . $obligacion->TipoContrato . '</td>
									<td class="td_dato2">' . $obligacion->ParticipacionDeuda . '</td>
									<td class="td_dato2">' . $obligacion->LineaCredito . '</td>
									<td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
									<td class="td_dato2">' . $obligacion->Calificacion . '</td>
									<td class="td_dato2">' . $obligacion->EntidadOriginadoraCartera . '</td>
									<td class="td_dato2">' . $obligacion->Sucursal . '</td>
									<td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
									<td class="td_dato2">' . $obligacion->ClaseTarjeta . '</td>
									<td class="td_dato2">' . $obligacion->CubrimientoGarantia . '</td>
									<td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
									<td class="td_dato2">' . $obligacion->Periodicidad . '</td>
									<td class="td_dato2" colspan="2">&nbsp;</td>
									<td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
									<td class="td_dato2">' . $obligacion->ValorMora . '</td>
									<td class="td_dato2">' . $obligacion->Reestructurado . '</td>
									<td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
									<td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
									<td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
								</tr>
								<tr>
									<td class="td_dato" colspan="5">&nbsp;</td>
									<td class="td_dato" colspan="16">
										<table border="0" cellspacing="0">
											<tr>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[0] . '</td>
															<td>' . $comportamiento[1] . '</td>
															<td>' . $comportamiento[2] . '</td>
															<td>' . $comportamiento[3] . '</td>
															<td>' . $comportamiento[4] . '</td>
															<td>' . $comportamiento[5] . '</td>
															<td>' . $comportamiento[6] . '</td>
															<td>' . $comportamiento[7] . '</td>
															<td>' . $comportamiento[8] . '</td>
															<td>' . $comportamiento[9] . '</td>
															<td>' . $comportamiento[10] . '</td>
															<td>' . $comportamiento[11] . '</td>
														</tr>
													</table>
												</td>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[12] . '</td>
															<td>' . $comportamiento[13] . '</td>
															<td>' . $comportamiento[14] . '</td>
															<td>' . $comportamiento[15] . '</td>
															<td>' . $comportamiento[16] . '</td>
															<td>' . $comportamiento[17] . '</td>
															<td>' . $comportamiento[18] . '</td>
															<td>' . $comportamiento[19] . '</td>
															<td>' . $comportamiento[20] . '</td>
															<td>' . $comportamiento[21] . '</td>
														    <td>' . $comportamiento[22] . '</td>
														    <td>' . $comportamiento[23] . '</td>
													    </tr>
												    </table>
											    </td>
											    <td width="10">&nbsp;</td>
											    <td class="td_label">
												    COMPORTAMIENTOS
											    <td>
										    </tr>
									    </table>
								    </td>
								</tr>';
                        }

                        ?>

                    <tr>
                        <td colspan="21"><br></td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="21">OBLIGACIONES EN MORA</td>
                    </tr>

                    <?php
                        $sector = $objeto_ws->Tercero->SectorFinancieroEnMora;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
									<td class="td_dato" rowspan="2" colspan="2">' . $obligacion->FechaCorte . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ModalidadCredito . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->MarcaTarjeta . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoGarantia . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
									<td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
									<td class="td_dato2" rowspan="3">' . $obligacion->ValorCuota . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NaturalezaReestructuracion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroReestructuraciones . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
								</tr>
								<tr>
                                    <td class="td_dato">PAC</td>
	                                <td class="td_dato">PAG</td>
	                                <td class="td_dato">MOR</td>
                                </tr>
								<tr>
									<td class="td_dato2">' . $obligacion->TipoContrato . '</td>
									<td class="td_dato2">' . $obligacion->ParticipacionDeuda . '</td>
									<td class="td_dato2">' . $obligacion->LineaCredito . '</td>
									<td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
									<td class="td_dato2">' . $obligacion->Calificacion . '</td>
									<td class="td_dato2">' . $obligacion->EntidadOriginadoraCartera . '</td>
									<td class="td_dato2">' . $obligacion->Sucursal . '</td>
									<td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
									<td class="td_dato2">' . $obligacion->ClaseTarjeta . '</td>
									<td class="td_dato2">' . $obligacion->CubrimientoGarantia . '</td>
									<td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
									<td class="td_dato2">' . $obligacion->Periodicidad . '</td>
									<td class="td_dato2" colspan="2">&nbsp;</td>
									<td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
									<td class="td_dato2">' . $obligacion->ValorMora . '</td>
									<td class="td_dato2">' . $obligacion->Reestructurado . '</td>
									<td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
									<td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
									<td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
								</tr>
                                <tr>
									<td class="td_dato" colspan="5">&nbsp;</td>
									<td class="td_dato" colspan="16">
										<table border="0" cellspacing="0">
											<tr>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[0] . '</td>
															<td>' . $comportamiento[1] . '</td>
															<td>' . $comportamiento[2] . '</td>
															<td>' . $comportamiento[3] . '</td>
															<td>' . $comportamiento[4] . '</td>
															<td>' . $comportamiento[5] . '</td>
															<td>' . $comportamiento[6] . '</td>
															<td>' . $comportamiento[7] . '</td>
															<td>' . $comportamiento[8] . '</td>
															<td>' . $comportamiento[9] . '</td>
															<td>' . $comportamiento[10] . '</td>
															<td>' . $comportamiento[11] . '</td>
														</tr>
													</table>
												</td>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[12] . '</td>
															<td>' . $comportamiento[13] . '</td>
															<td>' . $comportamiento[14] . '</td>
															<td>' . $comportamiento[15] . '</td>
															<td>' . $comportamiento[16] . '</td>
															<td>' . $comportamiento[17] . '</td>
															<td>' . $comportamiento[18] . '</td>
															<td>' . $comportamiento[19] . '</td>
															<td>' . $comportamiento[20] . '</td>
															<td>' . $comportamiento[21] . '</td>
														    <td>' . $comportamiento[22] . '</td>
														    <td>' . $comportamiento[23] . '</td>
													    </tr>
												    </table>
											    </td>
											    <td width="10">&nbsp;</td>
											    <td class="td_label">
												    COMPORTAMIENTOS
											    <td>
										    </tr>
									    </table>
								    </td>
								</tr>';
                        }

                        ?>

                    <tr>
                        <td colspan="21"><br></td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="21">OBLIGACIONES EXTINGUIDAS</td>
                    </tr>

                    <?php
                        $sector = $objeto_ws->Tercero->SectorFinancieroExtinguidas;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
									<td class="td_dato" rowspan="2" colspan="2">' . $obligacion->FechaCorte . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ModalidadCredito . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->MarcaTarjeta . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoGarantia . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
									<td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
									<td class="td_dato2" rowspan="3">' . $obligacion->ValorCuota . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NaturalezaReestructuracion . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->NumeroReestructuraciones . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
									<td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
								</tr>
								<tr>
                                    <td class="td_dato">PAC</td>
	                                <td class="td_dato">PAG</td>
	                                <td class="td_dato">MOR</td>
                                </tr>
								<tr>
									<td class="td_dato2">' . $obligacion->TipoContrato . '</td>
									<td class="td_dato2">' . $obligacion->ParticipacionDeuda . '</td>
									<td class="td_dato2">' . $obligacion->LineaCredito . '</td>
									<td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
									<td class="td_dato2">' . $obligacion->Calificacion . '</td>
									<td class="td_dato2">' . $obligacion->EntidadOriginadoraCartera . '</td>
									<td class="td_dato2">' . $obligacion->Sucursal . '</td>
									<td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
									<td class="td_dato2">' . $obligacion->ClaseTarjeta . '</td>
									<td class="td_dato2">' . $obligacion->CubrimientoGarantia . '</td>
									<td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
									<td class="td_dato2">' . $obligacion->Periodicidad . '</td>
									<td class="td_dato2" colspan="2">&nbsp;</td>
									<td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
									<td class="td_dato2">' . $obligacion->ValorMora . '</td>
									<td class="td_dato2">' . $obligacion->Reestructurado . '</td>
									<td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
									<td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
									<td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
								</tr>
                                <tr>
									<td class="td_dato" colspan="5">&nbsp;</td>
									<td class="td_dato" colspan="16">
										<table border="0" cellspacing="0">
											<tr>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[0] . '</td>
															<td>' . $comportamiento[1] . '</td>
															<td>' . $comportamiento[2] . '</td>
															<td>' . $comportamiento[3] . '</td>
															<td>' . $comportamiento[4] . '</td>
															<td>' . $comportamiento[5] . '</td>
															<td>' . $comportamiento[6] . '</td>
															<td>' . $comportamiento[7] . '</td>
															<td>' . $comportamiento[8] . '</td>
															<td>' . $comportamiento[9] . '</td>
															<td>' . $comportamiento[10] . '</td>
															<td>' . $comportamiento[11] . '</td>
														</tr>
													</table>
												</td>
												<td>
													<table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
														<tr>
															<td>' . $comportamiento[12] . '</td>
															<td>' . $comportamiento[13] . '</td>
															<td>' . $comportamiento[14] . '</td>
															<td>' . $comportamiento[15] . '</td>
															<td>' . $comportamiento[16] . '</td>
															<td>' . $comportamiento[17] . '</td>
															<td>' . $comportamiento[18] . '</td>
															<td>' . $comportamiento[19] . '</td>
															<td>' . $comportamiento[20] . '</td>
															<td>' . $comportamiento[21] . '</td>
														    <td>' . $comportamiento[22] . '</td>
														    <td>' . $comportamiento[23] . '</td>
													    </tr>
												    </table>
											    </td>
											    <td width="10">&nbsp;</td>
											    <td class="td_label">
												    COMPORTAMIENTOS
											    <td>
										    </tr>
									    </table>
								    </td>
								</tr>';
                        }

                        ?>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="21">INFORMACI&Oacute;N ENDEUDAMIENTO EN SECTOR REAL</th>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="3">FECHA CORTE</td>
                        <td class="td_label" rowspan="2">TIPO CONT</td>
                        <td class="td_label" rowspan="2">No. OBLIG</td>
                        <td class="td_label" rowspan="2">NOMBRE ENTIDAD</td>
                        <td class="td_label" rowspan="2">CIUDAD</td>
                        <td class="td_label" rowspan="2">CALD</td>
                        <td class="td_label" rowspan="2">VIG</td>
                        <td class="td_label" rowspan="3">CLA PER</td>
                        <td class="td_label" rowspan="2">F INICIO</td>
                        <td class="td_label" colspan="3">No. CUOTAS</td>
                        <td class="td_label" rowspan="2">CUPO APROB - VLR INIC</td>
                        <td class="td_label" rowspan="2">PAGO M&Iacute;NIM - VLR CUOTA</td>
                        <td class="td_label" rowspan="2">SIT OBLIG</td>
                        <td class="td_label" rowspan="2">TIP PAG</td>
                        <td class="td_label" rowspan="2">REF</td>
                        <td class="td_label" rowspan="2">F PAGO - F EXTIN</td>
                    </tr>
                    <tr>
                        <td class="td_label">PAC</td>
                        <td class="td_label">PAG</td>
                        <td class="td_label">MOR</td>
                    </tr>
                    <tr>
                        <td class="td_label">CATE - LCRE</td>
                        <td class="td_label">EST. CONTR</td>
                        <td class="td_label">TIPO EMPR</td>
                        <td class="td_label">SUCURSAL</td>
                        <td class="td_label">EST TITU</td>
                        <td class="td_label">MES</td>
                        <td class="td_label">F TERM</td>
                        <td class="td_label">PER</td>
                        <td class="td_label" colspan="2">&nbsp;</td>
                        <td class="td_label">CUPO UTILI - SALDO CORT</td>
                        <td class="td_label">VALOR CARGO FIJO</td>
                        <td class="td_label">VALOR MORA</td>
                        <td class="td_label">MOD EXT</td>
                        <td class="td_label">MOR MAX</td>
                        <td class="td_label">F PERMAN</td>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="18">OBLIGACIONES VIGENTES Y AL D&Iacute;A</td>
                    </tr>

                    <?php
                        $sector = $objeto_ws->Tercero->SectorRealAlDia;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->FechaCorte . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoContrato . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Vigencia . '</td>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->ClausulaPermanencia . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
                                    <td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorCuota . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
                                    <td class="td_dato" rowspan="2">&nbsp;</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato">PAC</td>
                                    <td class="td_dato">PAG</td>
                                    <td class="td_dato">MOR</td>
                                </tr>
                                <tr>
                                    <td class="td_dato2">' . $obligacion->LineaCredito . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->TipoEntidad . '</td>
                                    <td class="td_dato2">' . $obligacion->Sucursal . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
                                    <td class="td_dato2">' . $obligacion->NumeroMesesContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
                                    <td class="td_dato2">' . $obligacion->Periodicidad . '</td>
                                    <td class="td_dato2" colspan="2">&nbsp;</td>
                                    <td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorCargoFijo . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorMora . '</td>
                                    <td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
                                    <td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato" colspan="4">&nbsp;</td>
                                    <td class="td_dato" colspan="14">
                                        <table border="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[0] . '</td>
                                                            <td>' . $comportamiento[1] . '</td>
                                                            <td>' . $comportamiento[2] . '</td>
                                                            <td>' . $comportamiento[3] . '</td>
                                                            <td>' . $comportamiento[4] . '</td>
                                                            <td>' . $comportamiento[5] . '</td>
                                                            <td>' . $comportamiento[6] . '</td>
                                                            <td>' . $comportamiento[7] . '</td>
                                                            <td>' . $comportamiento[8] . '</td>
                                                            <td>' . $comportamiento[9] . '</td>
                                                            <td>' . $comportamiento[10] . '</td>
                                                            <td>' . $comportamiento[11] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[12] . '</td>
                                                            <td>' . $comportamiento[13] . '</td>
                                                            <td>' . $comportamiento[14] . '</td>
                                                            <td>' . $comportamiento[15] . '</td>
                                                            <td>' . $comportamiento[16] . '</td>
                                                            <td>' . $comportamiento[17] . '</td>
                                                            <td>' . $comportamiento[18] . '</td>
                                                            <td>' . $comportamiento[19] . '</td>
                                                            <td>' . $comportamiento[20] . '</td>
                                                            <td>' . $comportamiento[21] . '</td>
                                                            <td>' . $comportamiento[22] . '</td>
                                                            <td>' . $comportamiento[23] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="10">&nbsp;</td>
                                                <td class="td_label">
                                                    COMPORTAMIENTOS
                                                <td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="18"><br></td>
                    </tr>

                    <tr>
                        <td class="td_label_l" colspan="18">OBLIGACIONES EN MORA</td>
                    </tr>
                    <?php
                        $sector = $objeto_ws->Tercero->SectorRealEnMora;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->FechaCorte . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoContrato . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Vigencia . '</td>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->ClausulaPermanencia . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
                                    <td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorCuota . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
                                    <td class="td_dato" rowspan="2">&nbsp;</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato">PAC</td>
                                    <td class="td_dato">PAG</td>
                                    <td class="td_dato">MOR</td>
                                </tr>
                                <tr>
                                    <td class="td_dato2">' . $obligacion->LineaCredito . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->TipoEntidad . '</td>
                                    <td class="td_dato2">' . $obligacion->Sucursal . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
                                    <td class="td_dato2">' . $obligacion->NumeroMesesContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
                                    <td class="td_dato2">' . $obligacion->Periodicidad . '</td>
                                    <td class="td_dato2" colspan="2">&nbsp;</td>
                                    <td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorCargoFijo . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorMora . '</td>
                                    <td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
                                    <td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato" colspan="4">&nbsp;</td>
                                    <td class="td_dato" colspan="14">
                                        <table border="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[0] . '</td>
                                                            <td>' . $comportamiento[1] . '</td>
                                                            <td>' . $comportamiento[2] . '</td>
                                                            <td>' . $comportamiento[3] . '</td>
                                                            <td>' . $comportamiento[4] . '</td>
                                                            <td>' . $comportamiento[5] . '</td>
                                                            <td>' . $comportamiento[6] . '</td>
                                                            <td>' . $comportamiento[7] . '</td>
                                                            <td>' . $comportamiento[8] . '</td>
                                                            <td>' . $comportamiento[9] . '</td>
                                                            <td>' . $comportamiento[10] . '</td>
                                                            <td>' . $comportamiento[11] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[12] . '</td>
                                                            <td>' . $comportamiento[13] . '</td>
                                                            <td>' . $comportamiento[14] . '</td>
                                                            <td>' . $comportamiento[15] . '</td>
                                                            <td>' . $comportamiento[16] . '</td>
                                                            <td>' . $comportamiento[17] . '</td>
                                                            <td>' . $comportamiento[18] . '</td>
                                                            <td>' . $comportamiento[19] . '</td>
                                                            <td>' . $comportamiento[20] . '</td>
                                                            <td>' . $comportamiento[21] . '</td>
                                                            <td>' . $comportamiento[22] . '</td>
                                                            <td>' . $comportamiento[23] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="10">&nbsp;</td>
                                                <td class="td_label">
                                                    COMPORTAMIENTOS
                                                <td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="18"><br></td>
                    </tr>

                    <tr>
                        <td class="td_label_l" colspan="18">OBLIGACIONES EXTINGUIDAS</td>
                    </tr>

                    <?php
                        $sector = $objeto_ws->Tercero->SectorRealExtinguidas;
                        foreach ($sector->Obligacion as $obligacion) {
                            $comportamiento = explode('|', $obligacion->Comportamientos);
                            echo
                            '<tr>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->FechaCorte . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoContrato . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NumeroObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->NombreEntidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Ciudad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Calidad . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->Vigencia . '</td>
                                    <td class="td_dato2" rowspan="3">' . $obligacion->ClausulaPermanencia . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaApertura . '</td>
                                    <td class="td_dato" colspan="3">' . $obligacion->NumeroCuotasPactadas . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorInicial . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->ValorCuota . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->EstadoObligacion . '</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->TipoPago . '</td>
                                    <td class="td_dato" rowspan="2">&nbsp;</td>
                                    <td class="td_dato" rowspan="2">' . $obligacion->FechaTerminacion . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato">PAC</td>
                                    <td class="td_dato">PAG</td>
                                    <td class="td_dato">MOR</td>
                                </tr>
                                <tr>
                                    <td class="td_dato2">' . $obligacion->LineaCredito . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->TipoEntidad . '</td>
                                    <td class="td_dato2">' . $obligacion->Sucursal . '</td>
                                    <td class="td_dato2">' . $obligacion->EstadoTitular . '</td>
                                    <td class="td_dato2">' . $obligacion->NumeroMesesContrato . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaTerminacion . '</td>
                                    <td class="td_dato2">' . $obligacion->Periodicidad . '</td>
                                    <td class="td_dato2" colspan="2">&nbsp;</td>
                                    <td class="td_dato2">' . $obligacion->SaldoObligacion . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorCargoFijo . '</td>
                                    <td class="td_dato2">' . $obligacion->ValorMora . '</td>
                                    <td class="td_dato2">' . $obligacion->ModoExtincion . '</td>
                                    <td class="td_dato2">' . $obligacion->MoraMaxima . '</td>
                                    <td class="td_dato2">' . $obligacion->FechaPermanencia . '</td>
                                </tr>
                                <tr>
                                    <td class="td_dato" colspan="4">&nbsp;</td>
                                    <td class="td_dato" colspan="14">
                                        <table border="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[0] . '</td>
                                                            <td>' . $comportamiento[1] . '</td>
                                                            <td>' . $comportamiento[2] . '</td>
                                                            <td>' . $comportamiento[3] . '</td>
                                                            <td>' . $comportamiento[4] . '</td>
                                                            <td>' . $comportamiento[5] . '</td>
                                                            <td>' . $comportamiento[6] . '</td>
                                                            <td>' . $comportamiento[7] . '</td>
                                                            <td>' . $comportamiento[8] . '</td>
                                                            <td>' . $comportamiento[9] . '</td>
                                                            <td>' . $comportamiento[10] . '</td>
                                                            <td>' . $comportamiento[11] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table border="1" bgcolor="#FFFFFF" style="border-collapse:collapse">
                                                        <tr>
                                                            <td>' . $comportamiento[12] . '</td>
                                                            <td>' . $comportamiento[13] . '</td>
                                                            <td>' . $comportamiento[14] . '</td>
                                                            <td>' . $comportamiento[15] . '</td>
                                                            <td>' . $comportamiento[16] . '</td>
                                                            <td>' . $comportamiento[17] . '</td>
                                                            <td>' . $comportamiento[18] . '</td>
                                                            <td>' . $comportamiento[19] . '</td>
                                                            <td>' . $comportamiento[20] . '</td>
                                                            <td>' . $comportamiento[21] . '</td>
                                                            <td>' . $comportamiento[22] . '</td>
                                                            <td>' . $comportamiento[23] . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="10">&nbsp;</td>
                                                <td class="td_label">
                                                    COMPORTAMIENTOS
                                                <td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>';
                        }
                        ?>

                </table>
                <br><br><br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" cellspacing="0" align="center">
                    <tr>
                        <th>HUELLA DE CONSULTA &Uacute;LTIMOS SEIS MESES</th>
                    </tr>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="600" align="center">
                    <tr>
                        <td class="td_label">ENTIDAD</td>
                        <td class="td_label">MOTIVO CONSULTA</td>
                        <td class="td_label">FECHA</td>
                        <td class="td_label">SUCURSAL</td>
                        <td class="td_label">CIUDAD</td>
                    </tr>
                    <?php
                        $huella_Consulta = $objeto_ws->Tercero->HuellaConsulta;
                        $total_huella_consulta = 0;
                        foreach ($huella_Consulta->Consulta as $huella) {
                            $total_huella_consulta = $total_huella_consulta + 1;
                            echo
                            '<tr>
                                    <td class="td_dato2">' . $huella->NombreEntidad . '</td>
                                    <td class="td_dato">' . $huella->MotivoConsulta . '</td>
                                    <td class="td_dato">' . $huella->FechaConsulta . '</td>
                                    <td class="td_dato">' . $huella->Sucursal . '</td>
                                    <td class="td_dato">' . $huella->Ciudad . '</td>
                                </tr>';
                        }
                        ?>

                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="0" width="600" align="center">
                    <tr>
                        <td valign="top">Total consultas: <?php echo $total_huella_consulta; ?></td>
                    </tr>
                </table>
                <br><br><br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" cellspacing="0" align="center">
                    <tr>
                        <th>ENDEUDAMIENTO GLOBAL CLASIFICADO (Seg&uacute;n normatividad vigente)</th>
                    </tr>
                </table>
                <br>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="16">INFORMACI&Oacute;N CONSOLIDADA TRIMESTRE I</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="16">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreI ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreI ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2">CALF</td>
                        <td class="td_label" rowspan="2">TIPO MON</td>
                        <td class="td_label" colspan="4">No. DE DEUDAS</td>
                        <td class="td_label" colspan="4">VALOR DEUDAS</td>
                        <td class="td_label" rowspan="2">TOTAL</td>
                        <td class="td_label" rowspan="2">PADE</td>
                        <td class="td_label" colspan="4">% CUBRIMIENTO GAR</td>
                    </tr>
                    <tr>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_1 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimI;

                        foreach ($endeudamiento_trim_1->Endeudamiento71 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoModena . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesComercial . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->Total . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaMicrocredito . '</td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td class="td_label" rowspan="2" colspan="2">TIPO MONEDA</td>
                        <td class="td_label" colspan="6">CONTINGENCIA</td>
                        <td class="td_label" rowspan="2" colspan="4">CUOTA ESPERADA </td>
                        <td class="td_label" rowspan="2" colspan="4">% CUMPLIMIENTO</td>
                    </tr>
                    <tr>
                        <td class="td_label" colspan="3">N&Uacute;MERO</td>
                        <td class="td_label" colspan="3">VALOR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_1 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimI;
                        foreach ($endeudamiento_trim_1->Endeudamiento72 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato" colspan="2">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->NumeroContingencias . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->ValorContingencias . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>
                </table>
                <br>
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="17">INFORMACI&Oacute;N DETALLADA TRIMESTRE I</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="17">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreI ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreI ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">% GAR</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                        <td class="td_label">% CUMPL</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_1 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimI;
                        foreach ($endeudamiento_trim_1->Endeudamiento73 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperadores . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="17"><br></td>
                    </tr>
                </table>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="16">INFORMACI&Oacute;N CONSOLIDADA TRIMESTRE II</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="16">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreII ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreII ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2">CALF</td>
                        <td class="td_label" rowspan="2">TIPO MON</td>
                        <td class="td_label" colspan="4">No. DE DEUDAS</td>
                        <td class="td_label" colspan="4">VALOR DEUDAS</td>
                        <td class="td_label" rowspan="2">TOTAL</td>
                        <td class="td_label" rowspan="2">PADE</td>
                        <td class="td_label" colspan="4">% CUBRIMIENTO GAR</td>
                    </tr>
                    <tr>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_2 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimII;

                        foreach ($endeudamiento_trim_2->Endeudamiento81 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoModena . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesComercial . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->Total . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaMicrocredito . '</td>
                                </tr>';
                        }
                        ?>
                    <tr>
                        <td class="td_label" rowspan="2" colspan="2">TIPO MONEDA</td>
                        <td class="td_label" colspan="6">CONTINGENCIA</td>
                        <td class="td_label" rowspan="2" colspan="4">CUOTA ESPERADA </td>
                        <td class="td_label" rowspan="2" colspan="4">% CUMPLIMIENTO</td>
                    </tr>
                    <tr>
                        <td class="td_label" colspan="3">N&Uacute;MERO</td>
                        <td class="td_label" colspan="3">VALOR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_2 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimII;
                        foreach ($endeudamiento_trim_2->Endeudamiento82 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato" colspan="2">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->NumeroContingencias . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->ValorContingencias . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>
                </table>
                <br>
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="17">INFORMACI&Oacute;N DETALLADA TRIMESTRE II</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="17">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreII ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreII ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">% GAR</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                        <td class="td_label">% CUMPL</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_2 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimII;
                        foreach ($endeudamiento_trim_2->Endeudamiento83 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperadores . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>
                    <tr>
                        <td colspan="17"><br></td>
                    </tr>
                </table>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="16">INFORMACI&Oacute;N CONSOLIDADA TRIMESTRE III</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="16">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreIII ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreIII ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label" rowspan="2">CALF</td>
                        <td class="td_label" rowspan="2">TIPO MON</td>
                        <td class="td_label" colspan="4">No. DE DEUDAS</td>
                        <td class="td_label" colspan="4">VALOR DEUDAS</td>
                        <td class="td_label" rowspan="2">TOTAL</td>
                        <td class="td_label" rowspan="2">PADE</td>
                        <td class="td_label" colspan="4">% CUBRIMIENTO GAR</td>
                    </tr>
                    <tr>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                        <td class="td_label">CIAL</td>
                        <td class="td_label">CONS</td>
                        <td class="td_label">VIVI</td>
                        <td class="td_label">MICR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_3 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimIII;
                        foreach ($endeudamiento_trim_3->Endeudamiento91 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoModena . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesComercial . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperacionesMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeudaMicrocredito . '</td>
                                    <td class="td_dato">' . $trimestre->Total . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaComercial . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaConsumo . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaVivienda . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantiaMicrocredito . '</td>
                                </tr>';
                        }
                        ?>
                    <tr>
                        <td class="td_label" rowspan="2" colspan="2">TIPO MONEDA</td>
                        <td class="td_label" colspan="6">CONTINGENCIA</td>
                        <td class="td_label" rowspan="2" colspan="4">CUOTA ESPERADA </td>
                        <td class="td_label" rowspan="2" colspan="4">% CUMPLIMIENTO</td>
                    </tr>
                    <tr>
                        <td class="td_label" colspan="3">N&Uacute;MERO</td>
                        <td class="td_label" colspan="3">VALOR</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_3 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimIII;
                        foreach ($endeudamiento_trim_3->Endeudamiento92 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato" colspan="2">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->NumeroContingencias . '</td>
                                    <td class="td_dato" colspan="3">' . $trimestre->ValorContingencias . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato" colspan="4">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>

                </table>
                <br>
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="17">INFORMACI&Oacute;N DETALLADA TRIMESTRE III</th>
                    </tr>

                    <tr>
                        <td class="td_label_l" colspan="17">
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->FechaTrimestreIII ?>
                            REPORTADO POR
                            <?php echo $objeto_ws->Tercero->Endeudamiento->EncabezadoEndeudamiento->NumeroEntidadesTrimestreIII ?>
                            ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">PADE</td>
                        <td class="td_label">% GAR</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                        <td class="td_label">% CUMPL</td>
                    </tr>
                    <?php
                        $endeudamiento_trim_3 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimIII;
                        foreach ($endeudamiento_trim_3->Endeudamiento93 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperadores . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->ParticipacionTotalDeudas . '</td>
                                    <td class="td_dato">' . $trimestre->CubrimientoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                    <td class="td_dato">' . $trimestre->CumplimientoCuota . '</td>
                                </tr>';
                        }
                        ?>
                    <tr>
                        <td colspan="17"><br></td>
                    </tr>
                </table>
            </td>
            <td width="112">&nbsp;</td>
        </TR>

        <TR>
            <td width="112">&nbsp;</td>
            <td width="900">
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="14">INFORMACI&Oacute;N DETALLADA ACTUALIZACIONES TRIMESTRE I</th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="14">__FECHA REPORTADO POR _NRO ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                    </tr>

                    <?php
                        $endeudamiento_trim_1 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimI;
                        foreach ($endeudamiento_trim_1->Endeudamiento74 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoreCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperaciones . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="14"><br></td>
                    </tr>
                </table>
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="14">INFORMACI&Oacute;N DETALLADA ACTUALIZACIONES TRIMESTRE II
                        </th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="14">__FECHA REPORTADO POR _NRO ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                    </tr>

                    <?php
                        $endeudamiento_trim_2 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimII;
                        foreach ($endeudamiento_trim_2->Endeudamiento84 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoreCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperaciones . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="14"><br></td>
                    </tr>
                </table>
                <table border="0" bgcolor="#FFFFFF" cellspacing="3" width="900" align="center">
                    <tr>
                        <th class="th_titulo" colspan="14">INFORMACI&Oacute;N DETALLADA ACTUALIZACIONES TRIMESTRE III
                        </th>
                    </tr>
                    <tr>
                        <td class="td_label_l" colspan="14">__FECHA REPORTADO POR _NRO ENTIDADES</td>
                    </tr>
                    <tr>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD</td>
                        <td class="td_label">TIPO ENT</td>
                        <td class="td_label">NOMBRE ENTIDAD ORIGEN CARTERA</td>
                        <td class="td_label">TIPO FID</td>
                        <td class="td_label">No. FIDEICO</td>
                        <td class="td_label">MODA CRED</td>
                        <td class="td_label">CALF</td>
                        <td class="td_label">TIPO MON</td>
                        <td class="td_label">No. DEU</td>
                        <td class="td_label">VALOR DEUDAS</td>
                        <td class="td_label">TIPO GAR</td>
                        <td class="td_label">FECHA AVAL&Uacute;O</td>
                        <td class="td_label">CUOTA ESPERADA</td>
                    </tr>

                    <?php
                        $endeudamiento_trim_3 = $objeto_ws->Tercero->Endeudamiento->EndeudamientoTrimIII;
                        foreach ($endeudamiento_trim_3->Endeudamiento94 as $trimestre) {
                            echo
                            '<tr>
                                    <td class="td_dato">' . $trimestre->TipoEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->NombreEntidad . '</td>
                                    <td class="td_dato">' . $trimestre->TipoEntidadOriginadoraCartera . '</td>
                                    <td class="td_dato">' . $trimestre->EntidadOriginadoreCartera . '</td>
                                    <td class="td_dato">' . $trimestre->TipoFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroFideicomiso . '</td>
                                    <td class="td_dato">' . $trimestre->ModalidadCredito . '</td>
                                    <td class="td_dato">' . $trimestre->Calificacion . '</td>
                                    <td class="td_dato">' . $trimestre->TipoMoneda . '</td>
                                    <td class="td_dato">' . $trimestre->NumeroOperaciones . '</td>
                                    <td class="td_dato">' . $trimestre->ValorDeuda . '</td>
                                    <td class="td_dato">' . $trimestre->TipoGarantia . '</td>
                                    <td class="td_dato">' . $trimestre->FechaUltimoAvaluo . '</td>
                                    <td class="td_dato">' . $trimestre->CuotaEsperada . '</td>
                                </tr>';
                        }
                        ?>

                    <tr>
                        <td colspan="14"><br></td>
                    </tr>
                </table>
            </td>
            <td width="112">&nbsp;</td>
        </TR>
    </table>
</body>

</html>
<?php

}

?>