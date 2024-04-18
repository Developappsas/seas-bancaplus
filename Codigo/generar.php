<?php

require_once __DIR__ .'/vendor/autoload.php';

$html = '<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <table border="0" id="tableImprimir">
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="640">
                        <table width="640">
                        <tr>
                            <td width="500" valign="middle">Barranquilla, __DATE</td>
                            <td><img src="https://seas2.esefectivo.co/images/logo_copy.png"></td>
                        </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="640" height="295" valign="top" style="text-align: justify;">
                        <p style="text-align: justify;"><br>Certifica que el se&ntilde;or(a) <b>__NOMBRE</b> identificado(a) con c&eacute;dula de ciudadan&iacute;a n&uacute;mero <b>__CEDULA</b> presenta el siguiente saldo una vez aplicada la cuota correspondiente a la n&oacute;mina de <b>__MES</b>:</p>
                        <table border="1" align="center">
                        <tr>
                            <th>&nbsp;PAGADURIA&nbsp;</th>
                            <th>&nbsp;No. LIBRANZA&nbsp;</th>
                            <th>&nbsp;SALDO A PAGAR&nbsp;</th>
                            <th>&nbsp;VR CUOTA&nbsp;</th>
                        </tr>
                        __DETALLE
                        </table>
                        <p style="text-align: justify;"><b>INDICACIONES DE PAGO:</b> Deber&aacute; cancelar la suma de __LETRAS.
                        <br><br>Esta suma deber&aacute; ser cancelada &uacute;nica y exclusivamente en <b>BANCOLOMBIA</b> con cheque de gerencia por la totalidad del valor certificado a nombre del <span class="times"><i><b><u>PATRIMONIO AUT&Oacute;NOMO FIDUCOOMEVA P.A. ESEFECTIVO, Nit 901.076.840-5.</u></b></i></span>.
                        <br><br>
                        Fecha de vencimiento: <i><b><u>__FEC</u></b></i>.
                        <br><br>
                        Este documento no es v&aacute;lido como paz y salvo, DIEZ (10) d&iacute;as h&aacute;biles despu&eacute;s de cancelar el valor certificado, podr&aacute; solicitar su paz y salvo.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="640" height="200" valign="bottom" style="text-align: justify;">
                        <table width="640">
                        <tr>
                            <td width="500" class="td_peq">
                                <b>BANCOLOMBIA CUENTA DE AHORROS No. 829-000508-59</b>
                            </td>
                        </tr>
                        </table>
                        <br>
                        <table>
                        <tr>
                            <td valign="top">
                                <table border="1">
                                    <tr><td class="td_peq" width="120"><b>Nombre del cliente:</b></td><td class="td_peq" width="189">&nbsp;__NOMBRE</td></tr>
                                    <tr><td class="td_peq"><b>Identificaci&oacute;n:</b></td><td class="td_peq">&nbsp;__CEDULA</td></tr>
                                    <tr><td class="td_peq"><b>Referencia:</b></td><td class="td_peq">&nbsp;<b>__REFERENCIA</b></td></tr>
                                    <tr><td class="td_peq"><b>No. libranza:</b></td><td class="td_peq">&nbsp;__LIBRANZA</td></tr>
                                    <tr><td class="td_peq"><b>Fecha l&iacute;mite de pago:</b></td><td class="td_peq">&nbsp;__LIMITE</td></tr>
                                    <tr><td class="td_peq"><b>Total a pagar:</b></td><td class="td_peq">&nbsp;$__TOTAL_PAGAR_TOTAL</td></tr>
                                </table>
                            </td>
                            <td width="22">&nbsp;</td>
                            <td valign="top">
                                <table border="1">
                                    <tr><td colspan="3" align="center" class="td_peq"><b>FECHA DE PAGO</b></td></tr>
                                    <tr><td width="103" class="td_peq">DD</td><td width="103" class="td_peq">MM</td><td width="103" class="td_peq">AA</td></tr>
                                </table>
                                <br>
                                <table border="1">
                                    <tr><td colspan="3" align="center" class="td_peq"><b>DETALLE CHEQUES DE GERENCIA</b></td></tr>
                                    <tr><td width="103" align="center" class="td_peq">COD. BANCO</td><td width="103" align="center" class="td_peq">NO. CHEQUE</td><td width="103" align="center" class="td_peq">VALOR</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td colspan="2" align="center" class="td_peq"><b>TOTAL A PAGAR</b></td><td>&nbsp;</td></tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                        <p align="center"><b>CLIENTE</b></p>
                    </td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="640"><hr style="border-top: dotted 2px;" /></td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="640" height="300" valign="bottom" style="text-align: justify;">
                        <table width="640">
                        <tr>
                            <td width="500" valign="middle" class="td_peq">
                                <b>BANCOLOMBIA CUENTA DE AHORROS No. 829-000508-59</b>
                            </td>
                            <td valign="top"><img width="100" src="https://tentulogo.com/wp-content/uploads/cabecera-pepsi-post-marcas-cover.jpg"></td>
                        </tr>
                        </table>
                        <br>
                        <table>
                        <tr>
                            <td valign="top">
                                <table border="1">
                                    <tr><td class="td_peq" width="120"><b>Nombre del cliente:</b></td><td class="td_peq" width="189">&nbsp;__NOMBRE</td></tr>
                                    <tr><td class="td_peq"><b>Identificaci&oacute;n:</b></td><td class="td_peq">&nbsp;__CEDULA</td></tr>
                                    <tr><td class="td_peq"><b>Referencia:</b></td><td class="td_peq">&nbsp;<b>__REFERENCIA</b></td></tr>
                                    <tr><td class="td_peq"><b>No. libranza:</b></td><td class="td_peq">&nbsp;__LIBRANZA</td></tr>
                                    <tr><td class="td_peq"><b>Fecha l&iacute;mite de pago:</b></td><td class="td_peq">&nbsp;__LIMITE</td></tr>
                                    <tr><td class="td_peq"><b>Total a pagar:</b></td><td class="td_peq">&nbsp;$__TOTAL_PAGAR_TOTAL</td></tr>
                                </table>
                            </td>
                            <td width="22">&nbsp;</td>
                            <td valign="top">
                                <table border="1">
                                    <tr><td colspan="3" align="center" class="td_peq"><b>FECHA DE PAGO</b></td></tr>
                                    <tr><td width="103" class="td_peq">DD</td><td width="103" class="td_peq">MM</td><td width="103" class="td_peq">AA</td></tr>
                                </table>
                                <br>
                                <table border="1">
                                    <tr><td colspan="3" align="center" class="td_peq"><b>DETALLE CHEQUES DE GERENCIA</b></td></tr>
                                    <tr><td width="103" align="center" class="td_peq">COD. BANCO</td><td width="103" align="center" class="td_peq">NO. CHEQUE</td><td width="103" align="center" class="td_peq">VALOR</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td colspan="2" align="center" class="td_peq"><b>TOTAL A PAGAR</b></td><td>&nbsp;</td></tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                        <p align="center">
                            <img src="./img/codigo.jpg"><br/>
                            __CODEBARDATA
                        </p>
                        <!--<p align="center"><img src="https://seas2.esefectivo.co/barcode/barcode.php?f=jpg&s=ean128&d=__CODEBARDATA&h=100&tf=Arial"/></p>-->
                        <!--<p align="center"><img src="https://barcode.tec-it.com/barcode.ashx?data=__CODEBARDATA&code=EANUCC128&multiplebarcodes=false&translate-esc=true&unit=Min&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0"></p>-->
                        <p align="center"><b>BANCO</b></p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>';

$url = $_POST['imagenC'];
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $url));
$filepath = "./img/codigo.jpg";
file_put_contents($filepath,$data);

$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
$stylesheet = file_get_contents('css/style.css');

$mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html);
$mpdf->Output();

?>


