<?php

include('../functions.php');

//Consumiendo el Web Service
//$experian_hdcacierta_parametros = '"idType":"1","idNumber":"80921228","lastName":"ORTIZ","product":"'.$experian_hdcacierta_product.'","userId":"'.$experian_userid.'","password":"'.$experian_password.'"';
//$xmlstr = WSCentrales($experian_hdcacierta_url, $experian_hdcacierta_parametros);
//$xmlstr = utf8_encode($xmlstr); // Convertir en texto plano
//$xmlstr = reemplazar_caracteres_WS($xmlstr);

//Consultando en base de datos
$link = conectar();
$link_CE = conectar_consultas_externas();

$query = ("SELECT TOP 1 respuesta from consultas_externas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND servicio = 'HDC_ACIERTA' order by fecha_creacion desc ");
$rs = sqlsrv_query($link, $query);
// $fila = sqlsrv_fetch_array($rs);
$xmlstr = $fila["respuesta"];
$xmlstr = reemplazar_caracteres_WS2($xmlstr);

libxml_use_internal_errors(true);

$objeto_ws = simplexml_load_string($xmlstr);

$nombres = $objeto_ws->Informe->NaturalNacional['nombres'];
$primerApellido = $objeto_ws->Informe->NaturalNacional['primerApellido'];
$segundoApellido = $objeto_ws->Informe->NaturalNacional['segundoApellido'];
$rut = $objeto_ws->Informe->NaturalNacional['rut'];
if ($rut === 'true' ? $rut = 'SI' : $rut = 'NO');

//Obtenemos el numero de idetificacion y le quitamos los 0 a la izquierda
$identificacion_numero = ltrim($objeto_ws->Informe->NaturalNacional->Identificacion['numero'], '0');

//Obtenermos el codigo de estado de la identificacion y leems desde la base de datos el texto que corresponde
$identificacion_estado = $objeto_ws->Informe->NaturalNacional->Identificacion['estado'];
if ($identificacion_estado) {
    $query = ("SELECT * FROM experian_hdcacierta_estadodocumentoidentificacion where codigo = " . $identificacion_estado);
    $identificacion_estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
    $dato = sqlsrv_fetch_array($identificacion_estado);
    $identificacion_estado = $dato["descripcion"];
}


//Recibimos el codigo del Genero y reasignamos el texto correspondiente
$identificacion_genero = $objeto_ws->Informe->NaturalNacional->Identificacion['genero'];
if ($identificacion_genero) {
    $query = ("SELECT genero FROM experian_hdcacierta_estadocivilgenero where codigo = " . $identificacion_genero);
    $identificacion_genero = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
    $dato = sqlsrv_fetch_array($identificacion_genero);
    $identificacion_genero = $dato["genero"];
}

$identificaion_fechaExpedicion = $objeto_ws->Informe->NaturalNacional->Identificacion['fechaExpedicion'];
$identificaion_ciudad = $objeto_ws->Informe->NaturalNacional->Identificacion['ciudad'];
$identificaion_departamento = $objeto_ws->Informe->NaturalNacional->Identificacion['departamento'];

$edad_min = $objeto_ws->Informe->NaturalNacional->Edad['min'];
$edad_max = $objeto_ws->Informe->NaturalNacional->Edad['max'];
$edad = $edad_min . ' - ' . $edad_max;


//RESUMEN
//Perfil General
//Creditos Vigentes
$resumen_perfilgeneral_creditos_vigentes_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['sectorFinanciero'];
$resumen_perfilgeneral_creditos_vigentes_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['sectorCooperativo'];
$resumen_perfilgeneral_creditos_vigentes_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['sectorReal'];
$resumen_perfilgeneral_creditos_vigentes_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['sectorTelcos'];
$resumen_perfilgeneral_creditos_vigentes_totalSectores = $resumen_perfilgeneral_creditos_vigentes_sectorFinanciero + $resumen_perfilgeneral_creditos_vigentes_sectorCooperativo +
    $resumen_perfilgeneral_creditos_vigentes_sectorReal + $resumen_perfilgeneral_creditos_vigentes_sectorTelcos;
$resumen_perfilgeneral_creditos_vigentes_totalComoPrincipal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['totalComoPrincipal'];
$resumen_perfilgeneral_creditos_vigentes_totalComoCodeudorYOtros = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosVigentes['totalComoCodeudorYOtros'];

//Creditos Cerrados
$resumen_perfilgeneral_creditos_cerrados_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['sectorFinanciero'];
$resumen_perfilgeneral_creditos_cerrados_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['sectorCooperativo'];
$resumen_perfilgeneral_creditos_cerrados_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['sectorReal'];
$resumen_perfilgeneral_creditos_cerrados_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['sectorTelcos'];
$resumen_perfilgeneral_creditos_cerrados_totalSectores = $resumen_perfilgeneral_creditos_cerrados_sectorFinanciero + $resumen_perfilgeneral_creditos_cerrados_sectorCooperativo +
    $resumen_perfilgeneral_creditos_cerrados_sectorReal + $resumen_perfilgeneral_creditos_cerrados_sectorTelcos;
$resumen_perfilgeneral_creditos_cerrados_totalComoPrincipal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['totalComoPrincipal'];
$resumen_perfilgeneral_creditos_cerrados_totalComoCodeudorYOtros = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosCerrados['totalComoCodeudorYOtros'];

//Creditos Reestructurados
$resumen_perfilgeneral_creditos_reestructurados_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['sectorFinanciero'];
$resumen_perfilgeneral_creditos_reestructurados_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['sectorCooperativo'];
$resumen_perfilgeneral_creditos_reestructurados_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['sectorReal'];
$resumen_perfilgeneral_creditos_reestructurados_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['sectorTelcos'];
$resumen_perfilgeneral_creditos_reestructurados_totalSectores = $resumen_perfilgeneral_creditos_reestructurados_sectorFinanciero + $resumen_perfilgeneral_creditos_reestructurados_sectorCooperativo +
    $resumen_perfilgeneral_creditos_reestructurados_sectorReal + $resumen_perfilgeneral_creditos_reestructurados_sectorTelcos;
$resumen_perfilgeneral_creditos_reestructurados_totalComoPrincipal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['totalComoPrincipal'];
$resumen_perfilgeneral_creditos_reestructurados_totalComoCodeudorYOtros = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosReestructurados['totalComoCodeudorYOtros'];


//Creditos Refinanciados
$resumen_perfilgeneral_creditos_refinanciados_sectorFinanciero =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['sectorFinanciero'];
$resumen_perfilgeneral_creditos_refinanciados_sectorCooperativo =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['sectorCooperativo'];
$resumen_perfilgeneral_creditos_refinanciados_sectorReal =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['sectorReal'];
$resumen_perfilgeneral_creditos_refinanciados_sectorTelcos =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['sectorTelcos'];
$resumen_perfilgeneral_creditos_refinanciados_totalSectores = $resumen_perfilgeneral_creditos_refinanciados_sectorFinanciero + $resumen_perfilgeneral_creditos_refinanciados_sectorCooperativo +
    $resumen_perfilgeneral_creditos_refinanciados_sectorReal + $resumen_perfilgeneral_creditos_refinanciados_sectorTelcos;
$resumen_perfilgeneral_creditos_refinanciados_totalComoPrincipal =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['totalComoPrincipal'];
$resumen_perfilgeneral_creditos_refinanciados_totalComoCodeudorYOtros =  $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->CreditosRefinanciados['totalComoCodeudorYOtros'];

//Consulta Ult 6 Meses 
$resumen_perfilgeneral_consultaUlt6Meses_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['sectorFinanciero'];
$resumen_perfilgeneral_consultaUlt6Meses_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['sectorCooperativo'];
$resumen_perfilgeneral_consultaUlt6Meses_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['sectorReal'];
$resumen_perfilgeneral_consultaUlt6Meses_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['sectorTelcos'];
$resumen_perfilgeneral_consultaUlt6Meses_totalSectores =  $resumen_perfilgeneral_consultaUlt6Meses_sectorFinanciero + $resumen_perfilgeneral_consultaUlt6Meses_sectorCooperativo +
    $resumen_perfilgeneral_consultaUlt6Meses_sectorReal + $resumen_perfilgeneral_consultaUlt6Meses_sectorTelcos;
$resumen_perfilgeneral_consultaUlt6Meses_totalComoPrincipal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['totalComoPrincipal'];
$resumen_perfilgeneral_consultaUlt6Meses_totalComoCodeudorYOtros = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->ConsultaUlt6Meses['totalComoCodeudorYOtros'];

//Desacuerdos
$resumen_perfilgeneral_desacuerdos_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['sectorFinanciero'];
$resumen_perfilgeneral_desacuerdos_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['sectorCooperativo'];
$resumen_perfilgeneral_desacuerdos_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['sectorReal'];
$resumen_perfilgeneral_desacuerdos_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['sectorTelcos'];
$resumen_perfilgeneral_desacuerdos_totalSectores = $resumen_perfilgeneral_desacuerdos_sectorFinanciero + $resumen_perfilgeneral_desacuerdos_sectorCooperativo +
    $resumen_perfilgeneral_desacuerdos_sectorReal + $resumen_perfilgeneral_desacuerdos_sectorTelcos;
$resumen_perfilgeneral_desacuerdos_totalComoPrincipal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['totalComoPrincipal'];
$resumen_perfilgeneral_desacuerdos_totalComoCodeudorYOtros = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->Desacuerdos['totalComoCodeudorYOtros'];

//AntiguedadDesde
$resumen_perfilgeneral_antiguedadDesde_sectorFinanciero = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->AntiguedadDesde['sectorFinanciero'];
$resumen_perfilgeneral_antiguedadDesde_sectorCooperativo = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->AntiguedadDesde['sectorCooperativo'];
$resumen_perfilgeneral_antiguedadDesde_sectorReal = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->AntiguedadDesde['sectorReal'];
$resumen_perfilgeneral_antiguedadDesde_sectorTelcos = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->PerfilGeneral->AntiguedadDesde['sectorTelcos'];
//Perfil General



?>

<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../plantillas/bootstrap_4.6.0/css/bootstrap.min.css" crossorigin="anonymous">

    <link href="../sty.css?v=2" rel="stylesheet" type="text/css" crossorigin="anonymous">

    <title>Formato data credito</title>
</head>

<body>
    <div class="container container-pdf mt-4">
        <div class="row">
            <div class="col -3">
                <img src="../images/logo_datacredito.png" alt="logo datacredito">
            </div>
            <div class="col -6"></div>

            <div class="col -3">
                <img src="../images/logo_historial.png" alt="">
            </div>
        </div>

        <div class="container mb-3">
            <span>Consultado Por:</span>
        </div>

        <div class="container">

            <div class="container titulos">
                <span class="left">INFORMACIÓN BASICA</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-sm table-bordered mt-2">
                <tbody>
                    <tr>
                        <th class="titulos_tablas">Tipo Documento</th>
                        <td>C.C.</td>
                        <th class="titulos_tablas">Número</th>
                        <td> <?php echo $identificacion_numero; ?></td>
                        <th class="titulos_tablas">Estado Documento</th>
                        <td> <?php echo $identificacion_estado; ?></td>
                        <th class="titulos_tablas">Lugar Expedición</th>
                        <td> <?php echo $identificaion_ciudad; ?> </td>
                        <th class="titulos_tablas">Fecha Expedición</th>
                        <td> <?php echo $identificaion_fechaExpedicion; ?> </td>
                    </tr>
                    <tr>
                        <th class="titulos_tablas">Nombre</th>
                        <td> <?php echo $nombres . ' ' . $primerApellido . ' ' . $segundoApellido; ?> </td>
                        <th class="titulos_tablas">Rango Edad</th>
                        <td> <?php echo $edad; ?></td>
                        <th class="titulos_tablas">Género</th>
                        <td> <?php echo $identificacion_genero; ?> </td>
                        <th class="titulos_tablas">Tiene RUT?</th>
                        <td> <?php echo $rut; ?> </td>
                        <th class="titulos_tablas">Antiguedad</th>
                        <td>- -</td>
                    </tr>
                </tbody>
            </table>

            <div class="container titulos">
                <span>ARTICULO 14 LEY 1266 DE 2008</span>
            </div>
            <h5 class="center mt-1 ">"Se presenta reporte negativo cuando la(s) persona(s) naturales efectivamente se
                encuentran en mora en sus cuotas u obligaciones. Se presenta reporte positivo cuando la(s) persona(s)
                naturales están al día en sus obligaciones"</h5>

            <div class="container titulos">
                <span class="left">RESUMEN</span>
                <span class="right">6IDI5CF</span>
            </div>

            <div class="container titulos mt-1">
                <h6>Perfil General</h6>
            </div>

            <table class="table table-bordered mt-1 table-sm">
                <thead>
                    <tr>
                        <th class="titulos_tablas">Sectores</th>
                        <th class="titulos_tablas">Sector Financiero </th>
                        <th class="titulos_tablas">Sector Cooperativo </th>
                        <th class="titulos_tablas">Sector Real </th>
                        <th class="titulos_tablas">Sector Telcos </th>
                        <th class="titulos_tablas">Total Sectores </th>
                        <th class="titulos_tablas">Total como Principal</th>
                        <th class="titulos_tablas">Total como Codeudor y Otros</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Créditos Vigentes</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_sectorFinanciero; ?>
                        </td>

                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_sectorCooperativo; ?>
                        </td>

                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_vigentes_totalComoCodeudorYOtros; ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Créditos Cerrados</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_cerrados_totalComoCodeudorYOtros; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Créditos Reestructurados</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_reestructurados_totalComoCodeudorYOtros; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Créditos Refinanciados</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_creditos_refinanciados_totalComoCodeudorYOtros; ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Consultas en los ult. 6 Meses</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_consultaUlt6Meses_totalComoCodeudorYOtros; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Desacuerdos Vigentes a la Fecha</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_totalSectores; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_totalComoPrincipal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_desacuerdos_totalComoCodeudorYOtros; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antigüedad desde</td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_antiguedadDesde_sectorFinanciero; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_antiguedadDesde_sectorCooperativo; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_antiguedadDesde_sectorReal; ?>
                        </td>
                        <td class="td-centrado">
                            <?php echo $resumen_perfilgeneral_antiguedadDesde_sectorTelcos; ?>
                        </td>
                        <td class="td-centrado">-</td>
                        <td class="td-centrado">-</td>
                        <td class="td-centrado">-</td>
                    </tr>
                </tbody>
            </table>

            <div class="container titulos mt-1">
                <h6>Tendencia de endeudamiento</h6>
            </div>

            <table class="table table-bordered mt-1 table-sm">
                <thead>
                    <tr>
                        <th class="titulos_tablas">Saldos y Moras</th>
                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<th class="titulos_tablas">' . date('M', strtotime($child['fecha'])) . ' ' . date('y', strtotime($child['fecha'])) . '</th>';
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Saldo Deuda Total en Mora (en miles)</td>
                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . number_format((int)$child['saldoDeudaTotalMora']) . '</td>';
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>Saldo Deuda Total (en miles)</td>
                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . number_format((int)$child['saldoDeudaTotal'], 1) . '</td>';
                            }
                        }
                        ?>

                    </tr>
                    <tr>
                        <td>Moras máx Sector Financiero</td>

                        <?php
                        //Tendencia de endeudamiento
                        if ($xml) {
                            $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['morasMaxSectorFinanciero'] . '</td>';
                            }
                        }
                        ?>

                    </tr>
                    <tr>
                        <td>Moras máx Sector Cooperativo</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['morasMaxSectorCooperativo'] . '</td>';
                            }
                        }
                        ?>

                    </tr>
                    <tr>
                        <td>Moras máx Sector Real</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['morasMaxSectorReal'] . '</td>';
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>Moras máx Sector Telcos</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['morasMaxSectorTelcos'] . '</td>';
                            }
                        }
                        ?>

                    </tr>
                    <tr>
                        <td class="text-bold">Total Moras Máximas</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado text-bold">' . $child['morasMaximas'] . '</td>';
                            }
                        }
                        ?>

                    </tr>

                    <tr>
                        <td>Núm créditos con mora =30</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['numCreditos30'] . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <tr>
                        <td>Núm créditos con mora >= 60</td>

                        <?php
                        //Tendencia de endeudamiento
                        $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->VectorSaldosYMoras;
                        if ($xml) {
                            foreach ($xml->children() as $child) {
                                echo '<td class="td-centrado">' . $child['numCreditosMayorIgual60'] . '</td>';
                            }
                        }
                        ?>
                    </tr>

                </tbody>
            </table>

            <div class="container titulos mt-1">
                <h6>Endeudamiento Actual</h6>
            </div>

            <table class="table table-bordered mt-1 table-sm">
                <thead>
                    <tr>
                        <th class="titulos_tablas">Carteras</th>
                        <th class="titulos_tablas">Calidad</th>
                        <th class="titulos_tablas">Núm</th>
                        <th class="titulos_tablas">Estado Actual</th>
                        <th class="titulos_tablas">Calf</th>
                        <th class="titulos_tablas">Vlr o cupo inicial</th>
                        <th class="titulos_tablas">Saldo Actual</th>
                        <th class="titulos_tablas">Saldo en Mora</th>
                        <th class="titulos_tablas">Valor Cuota</th>
                        <th class="titulos_tablas">% Part </th>
                        <th class="titulos_tablas">% Deuda</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    //Endeudamiento Actual
                    $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->Resumen->EndeudamientoActual;
                    if ($xml) {
                        foreach ($xml->children() as $child) {
                            $sector = $child['codSector'];
                            $query = ("SELECT * FROM experian_hdcacierta_codigosSector where codigo = " . $sector);
                            $sector = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($sector);
                            $sector = $dato["descripcion"];
                            echo '<tr> <td colspan="11" class="titulos">' . $sector . '</td> </tr> ';

                            foreach ($child->children() as $child) {
                                $tipoCuenta = $child['tipoCuenta'];
                                $query = ("SELECT * FROM experian_hdcacierta_tiposcuenta where codigo = '" . $tipoCuenta . "'");
                                $tipoCuenta = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($tipoCuenta);
                                $tipoCuenta = utf8_encode($dato["descripcion"]);

                                $numCuentas = (count($child->Usuario->Cuenta));

                                foreach ($child->children() as $child) {
                                    $tipoUsuario = $child["tipoUsuario"];
                                    echo '<tr>';
                                    echo '<td rowspan="' . ($numCuentas + 1) . '" class="td-centrado">' . $tipoCuenta . '</td>';
                                    echo '<td rowspan="' . ($numCuentas + 1) . '" class="td-centrado">' . $tipoUsuario . '</td>';

                                    $numCuentas = $child;
                                    $numCuentas = (count($numCuentas));
                                    echo '<td rowspan="' . ($numCuentas + 1) . '" class="td-centrado">' . $numCuentas . '</td>';
                                    echo '</tr> ';

                                    $totalValorInicial = 0;
                                    $totalSaldoActual = 0;
                                    $totalSaldoMora = 0;
                                    $totalCuotaMes = 0;

                                    foreach ($child->children() as $child) {
                                        $estadoActual = $child['estadoActual'];
                                        $calificacion = $child['calificacion'];
                                        $valorInicial = $child['valorInicial'];
                                        $saldoActual = $child['saldoActual'];
                                        $saldoMora = $child['saldoMora'];
                                        $cuotaMes = $child['cuotaMes'];
                                        $comportamientoNegativo = $child['comportamientoNegativo'];
                                        $totalDeudaCarteras = $child['totalDeudaCarteras'];

                                        echo '<tr>';
                                        echo '<td class="td-centrado">' . $estadoActual . '</td>';
                                        echo '<td class="td-centrado">' . $calificacion . '</td>';
                                        echo '<td class="td-centrado">' . number_format((float) $valorInicial) . '</td>';
                                        echo '<td class="td-centrado">' . number_format((float) $saldoActual) . '</td>';
                                        echo '<td class="td-centrado">' . number_format((float) $saldoMora) . '</td>';
                                        echo '<td class="td-centrado">' . (float)$cuotaMes . '</td>';
                                        echo '<td class="td-centrado">' . $comportamientoNegativo . '</td>';
                                        echo '<td class="td-centrado">' . $totalDeudaCarteras . '</td>';
                                        echo '</tr>';

                                        $totalValorInicial = $totalValorInicial + (float)$valorInicial;
                                        $totalSaldoActual = $totalSaldoActual + (float)$saldoActual;
                                        $totalSaldoMora = $totalSaldoMora + (float)$saldoMora;
                                        $totalCuotaMes = $totalCuotaMes + (float)$cuotaMes;
                                    }

                                    $granTotalValorInicial = $granTotalValorInicial + $totalValorInicial;
                                    $granTotalSaldoActual = $granTotalSaldoActual + $totalSaldoActual;
                                    $granTotalSaldoMora = $granTotalSaldoMora + $totalSaldoMora;
                                    $granTotalCuotaMes = $granTotalCuotaMes + $totalCuotaMes;

                                    echo '<tr>';
                                    echo '<td class="td-centrado">Total sector ' . $sector . ' </td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td class="td-centrado">' . number_format($totalValorInicial) . '</td>';
                                    echo '<td class="td-centrado">' . number_format($totalSaldoActual) . '</td>';
                                    echo '<td class="td-centrado">' . number_format($totalSaldoMora) . '</td>';
                                    echo '<td class="td-centrado">' . number_format($totalCuotaMes) . '</td>';
                                    echo '<td class="td-centrado">--</td>';
                                    echo '<td class="td-centrado">-- </td>';
                                    echo '</tr>';
                                }
                            }
                        }
                    }
                    ?>

                    <tr class="total-tablas-datacredito">
                        <td class="td-centrado">TOTAL</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="td-centrado"><?php echo number_format((float) $granTotalValorInicial); ?> </td>
                        <td class="td-centrado"><?php echo number_format((float) $granTotalSaldoActual); ?> </td>
                        <td class="td-centrado"><?php echo number_format((float) $granTotalSaldoMora); ?> </td>
                        <td class="td-centrado"><?php echo number_format((float) $granTotalCuotaMes); ?> </td>
                        <td class="td-centrado">100.0%</td>
                        <td class="td-centrado">70.9%</td>
                    </tr>
                </tbody>
            </table>

            <div class="container titulos">
                <span class="left">HÁBITO DE PAGO DE OBLIGACIONES ABIERTAS / VIGENTES</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="11" class="titulos">Sector Financiero</td>
                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '1') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Vigente') {
                                $situacionTitular = $child['situacionTitular'];
                                $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                                $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($situacionTitular);
                                $situacionTitular = utf8_encode($dato["descripcion"]);

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>

                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $calse . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '1') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado . '</td>
                                            <td class="td-centrado">' . $child->Estados->EstadoCuenta['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                            <td class="td-centrado">****</td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                        
                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">-/M/-</td>
                                            <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '1') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }
                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                        <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                        <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                        <td class="td-centrado titulos_tablas">Calf</td>
                                        <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                        <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                        <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                        <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                        <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                        <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                        <td class="td-centrado titulos_tablas">47 meses</td>
                                    </tr>
                                    <tr>
                                        <td class="td-centrado">' . $child['entidad'] . '</td>
                                        <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                        <td class="td-centrado">' . $child['numero'] . '</td>
                                        <td class="td-centrado">' . $calificacion . '</td>
                                        <td class="td-centrado">' . $estado . '</td>
                                        <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                        <td class="td-centrado">' . $adjetivo . '</td>
                                        <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                        <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                        <td class="td-centrado">****</td>
                                        <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-sm  table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                        <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                        <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                        <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                        <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                        <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                        <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                        <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                        <td class="td-centrado titulos_tablas">Perm.</td>
                                        <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                        <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                        <td class="td-centrado titulos_tablas">% deuda</td>
                                        <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                    </tr>

                                    <tr>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">' . $situacionTitular . '</td>
                                        <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                        <td class="td-centrado">' . $garantia . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">-/M/-</td>
                                        <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                        <td class="td-centrado">' . $child['oficina'] . '</td>
                                    </tr>
                                </table>
                                ';
                        }
                    }
                    ?>
                </tr>
            </table>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="11" class="titulos">Sector Cooperativo</td>
                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '2') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Vigente') {
                                $situacionTitular = $child['situacionTitular'];
                                $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                                $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($situacionTitular);
                                $situacionTitular = utf8_encode($dato["descripcion"]);

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>

                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $calse . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '2') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado . '</td>
                                            <td class="td-centrado">' . $child->Estados->EstadoCuenta['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                            <td class="td-centrado">****</td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                        
                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">-/M/-</td>
                                            <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '2') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }
                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                        <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                        <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                        <td class="td-centrado titulos_tablas">Calf</td>
                                        <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                        <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                        <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                        <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                        <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                        <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                        <td class="td-centrado titulos_tablas">47 meses</td>
                                    </tr>
                                    <tr>
                                        <td class="td-centrado">' . $child['entidad'] . '</td>
                                        <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                        <td class="td-centrado">' . $child['numero'] . '</td>
                                        <td class="td-centrado">' . $calificacion . '</td>
                                        <td class="td-centrado">' . $estado . '</td>
                                        <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                        <td class="td-centrado">' . $adjetivo . '</td>
                                        <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                        <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                        <td class="td-centrado">****</td>
                                        <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-sm  table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                        <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                        <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                        <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                        <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                        <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                        <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                        <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                        <td class="td-centrado titulos_tablas">Perm.</td>
                                        <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                        <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                        <td class="td-centrado titulos_tablas">% deuda</td>
                                        <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                    </tr>

                                    <tr>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">' . $situacionTitular . '</td>
                                        <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                        <td class="td-centrado">' . $garantia . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">-/M/-</td>
                                        <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                        <td class="td-centrado">' . $child['oficina'] . '</td>
                                    </tr>
                                </table>
                                ';
                        }
                    }
                    ?>
                </tr>
            </table>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="11" class="titulos">Sector Real</td>
                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '3') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Vigente') {
                                $situacionTitular = $child['situacionTitular'];
                                $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                                $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($situacionTitular);
                                $situacionTitular = utf8_encode($dato["descripcion"]);

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>

                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $calse . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '3') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado . '</td>
                                            <td class="td-centrado">' . $child->Estados->EstadoCuenta['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                            <td class="td-centrado">****</td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                        
                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">-/M/-</td>
                                            <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '3') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }
                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                        <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                        <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                        <td class="td-centrado titulos_tablas">Calf</td>
                                        <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                        <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                        <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                        <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                        <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                        <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                        <td class="td-centrado titulos_tablas">47 meses</td>
                                    </tr>
                                    <tr>
                                        <td class="td-centrado">' . $child['entidad'] . '</td>
                                        <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                        <td class="td-centrado">' . $child['numero'] . '</td>
                                        <td class="td-centrado">' . $calificacion . '</td>
                                        <td class="td-centrado">' . $estado . '</td>
                                        <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                        <td class="td-centrado">' . $adjetivo . '</td>
                                        <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                        <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                        <td class="td-centrado">****</td>
                                        <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-sm  table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                        <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                        <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                        <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                        <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                        <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                        <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                        <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                        <td class="td-centrado titulos_tablas">Perm.</td>
                                        <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                        <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                        <td class="td-centrado titulos_tablas">% deuda</td>
                                        <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                    </tr>

                                    <tr>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">' . $situacionTitular . '</td>
                                        <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                        <td class="td-centrado">' . $garantia . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">-/M/-</td>
                                        <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                        <td class="td-centrado">' . $child['oficina'] . '</td>
                                    </tr>
                                </table>
                                ';
                        }
                    }
                    ?>
                </tr>
            </table>


            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="11" class="titulos">Sector Telecomunicaciones</td>
                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '4') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Vigente') {
                                $situacionTitular = $child['situacionTitular'];
                                $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                                $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($situacionTitular);
                                $situacionTitular = utf8_encode($dato["descripcion"]);

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>

                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $calse . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '4') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                            <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $estado . '</td>
                                            <td class="td-centrado">' . $child->Estados->EstadoCuenta['fecha'] . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                            <td class="td-centrado">****</td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                        
                                    <table class="table table-bordered table-sm  table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                            <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                            <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                            <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                            <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                            <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                            <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                            <td class="td-centrado titulos_tablas">Perm.</td>
                                            <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                            <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                            <td class="td-centrado titulos_tablas">% deuda</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $situacionTitular . '</td>
                                            <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                            <td class="td-centrado">' . $garantia . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                            <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">-/M/-</td>
                                            <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '4') {
                            $estado = $child->Estados->EstadoCuenta['codigo'];

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }
                            $situacionTitular = $child['situacionTitular'];
                            $query = ("SELECT * FROM experian_hdcacierta_situacionTitular where codigo = '" . $situacionTitular . "'");
                            $situacionTitular = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($situacionTitular);
                            $situacionTitular = utf8_encode($dato["descripcion"]);

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                        <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                        <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                        <td class="td-centrado titulos_tablas">Calf</td>
                                        <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                        <td class="td-centrado titulos_tablas">Fecha Actualización</td>
                                        <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                        <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                        <td class="td-centrado titulos_tablas">Fecha Vencimiento</td>
                                        <td class="td-centrado titulos_tablas">Mora Máxima</td>
                                        <td class="td-centrado titulos_tablas">47 meses</td>
                                    </tr>
                                    <tr>
                                        <td class="td-centrado">' . $child['entidad'] . '</td>
                                        <td class="td-centrado">' . $child->Caracteristicas['tipoCuenta'] . '</td>
                                        <td class="td-centrado">' . $child['numero'] . '</td>
                                        <td class="td-centrado">' . $calificacion . '</td>
                                        <td class="td-centrado">' . $estado . '</td>
                                        <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                        <td class="td-centrado">' . $adjetivo . '</td>
                                        <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                        <td class="td-centrado">' . $child['fechaVencimiento'] . '</td>
                                        <td class="td-centrado">****</td>
                                        <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-sm  table-no-margin-bottom">
                                    <tr>
                                        <td class="td-centrado titulos_tablas">Desacuerdo con la inform</td>
                                        <td class="td-centrado titulos_tablas">Estado del Titular</td>
                                        <td class="td-centrado titulos_tablas">Marca/ Clase</td>
                                        <td class="td-centrado titulos_tablas">Tipo Garantía</td>
                                        <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                        <td class="td-centrado titulos_tablas">Saldo Actual (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Saldo en Mora</td>
                                        <td class="td-centrado titulos_tablas">Valor Cuota (Miles $)</td>
                                        <td class="td-centrado titulos_tablas">Fecha Limite Pago</td>
                                        <td class="td-centrado titulos_tablas">Fecha del PAGO</td>
                                        <td class="td-centrado titulos_tablas">Perm.</td>
                                        <td class="td-centrado titulos_tablas">No.Cheq Devueltos</td>
                                        <td class="td-centrado titulos_tablas">Cuotas/M/Vigencia</td>
                                        <td class="td-centrado titulos_tablas">% deuda</td>
                                        <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                    </tr>

                                    <tr>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">' . $situacionTitular . '</td>
                                        <td class="td-centrado">' . $franquicia . '/' . $clase . '</td>
                                        <td class="td-centrado">' . $garantia . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cupoTotal'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoActual'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['saldoMora'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['cuota'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaLimitePago'] . '</td>
                                        <td class="td-centrado">' . $child->Valores->Valor['fechaPagoCuota'] . '</td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado"></td>
                                        <td class="td-centrado">-/M/-</td>
                                        <td class="td-centrado">' . (($child->Valores->Valor['saldoActual'] / $child->Valores->Valor['valorInicial']) * 100) . '</td>
                                        <td class="td-centrado">' . $child['oficina'] . '</td>
                                    </tr>
                                </table>
                                ';
                        }
                    }
                    ?>
                </tr>
            </table>



            <div class="container titulos">
                <span class="left">HÁBITO DE PAGO DE OBLIGACIONES CERRADAS / INACTIVAS </span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="18" class="titulos">Sector Financiero</td>
                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '1') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Cerrada') {

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                        <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                            <tr>
                                                <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                <td class="td-centrado titulos_tablas">Calf</td>
                                                <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                <td class="td-centrado titulos_tablas">47 meses</td>
                                            </tr>
                                            <tr>
                                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                                <td class="td-centrado">AHO</td>
                                                <td class="td-centrado">' . $estado_desc . '</td>
                                                <td class="td-centrado">' . $calificacion . '</td>
                                                <td class="td-centrado">' . $adjetivo . '</td>
                                                <td class="td-centrado">' . $child['numero'] . '</td>
                                                <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                <td class="td-centrado"></td> 
                                                <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                <td class="td-centrado">' . $child['oficina'] . '</td>
                                                <td class="td-centrado"></td>
                                                <td class="td-centrado"></td>
                                            </tr>
                                        </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '1') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '1') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }
                    ?>
                </tr>



            </table>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="18" class="titulos">Sector Cooperativo</td>

                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '2') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Cerrada') {

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                        <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                            <tr>
                                                <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                <td class="td-centrado titulos_tablas">Calf</td>
                                                <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                <td class="td-centrado titulos_tablas">47 meses</td>
                                            </tr>
                                            <tr>
                                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                                <td class="td-centrado">AHO</td>
                                                <td class="td-centrado">' . $estado_desc . '</td>
                                                <td class="td-centrado">' . $calificacion . '</td>
                                                <td class="td-centrado">' . $adjetivo . '</td>
                                                <td class="td-centrado">' . $child['numero'] . '</td>
                                                <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                <td class="td-centrado"></td> 
                                                <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                <td class="td-centrado">' . $child['oficina'] . '</td>
                                                <td class="td-centrado"></td>
                                                <td class="td-centrado"></td>
                                            </tr>
                                        </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '2') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '2') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }
                    ?>

                </tr>

            </table>

            <table class="table table-bordered table-sm mt-1  table-no-margin-bottom">
                <tr>
                    <td colspan="18" class="titulos">Sector Real</td>

                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '3') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Cerrada') {

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                                <tr>
                                                    <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                    <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                    <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                    <td class="td-centrado titulos_tablas">Calf</td>
                                                    <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                    <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                    <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                    <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                    <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                    <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                    <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                    <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                    <td class="td-centrado titulos_tablas">47 meses</td>
                                                </tr>
                                                <tr>
                                                    <td class="td-centrado">' . $child['entidad'] . '</td>
                                                    <td class="td-centrado">AHO</td>
                                                    <td class="td-centrado">' . $estado_desc . '</td>
                                                    <td class="td-centrado">' . $calificacion . '</td>
                                                    <td class="td-centrado">' . $adjetivo . '</td>
                                                    <td class="td-centrado">' . $child['numero'] . '</td>
                                                    <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                    <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                    <td class="td-centrado"></td> 
                                                    <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                    <td class="td-centrado">' . $child['oficina'] . '</td>
                                                    <td class="td-centrado"></td>
                                                    <td class="td-centrado"></td>
                                                </tr>
                                            </table>
                                        ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '3') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                        <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                            <tr>
                                                <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                <td class="td-centrado titulos_tablas">Calf</td>
                                                <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                <td class="td-centrado titulos_tablas">47 meses</td>
                                            </tr>
                                            <tr>
                                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                                <td class="td-centrado">AHO</td>
                                                <td class="td-centrado">' . $estado_desc . '</td>
                                                <td class="td-centrado">' . $calificacion . '</td>
                                                <td class="td-centrado">' . $adjetivo . '</td>
                                                <td class="td-centrado">' . $child['numero'] . '</td>
                                                <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                <td class="td-centrado"></td> 
                                                <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                <td class="td-centrado">' . $child['oficina'] . '</td>
                                                <td class="td-centrado"></td>
                                                <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                            </tr>
                                        </table>
                                    ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '3') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                        <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                            <tr>
                                                <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                <td class="td-centrado titulos_tablas">Calf</td>
                                                <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                <td class="td-centrado titulos_tablas">47 meses</td>
                                            </tr>
                                            <tr>
                                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                                <td class="td-centrado">AHO</td>
                                                <td class="td-centrado">' . $estado_desc . '</td>
                                                <td class="td-centrado">' . $calificacion . '</td>
                                                <td class="td-centrado">' . $adjetivo . '</td>
                                                <td class="td-centrado">' . $child['numero'] . '</td>
                                                <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                <td class="td-centrado"></td> 
                                                <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                <td class="td-centrado">' . $child['oficina'] . '</td>
                                                <td class="td-centrado"></td>
                                                <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                            </tr>
                                        </table>
                                    ';
                        }
                    }
                    ?>

                </tr>
            </table>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                <tr>
                    <td colspan="18" class="titulos">Sector Telcos</td>

                    <?php
                    $xml = $objeto_ws->Informe;
                    foreach ($xml->CuentaAhorro as $child) {
                        if ($child['sector'] == '4') {

                            $estado = $child->Estado['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_estadoscuentascorrientesahorros where codigo = '" . $estado . "'");
                            $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($estado);
                            $estado = utf8_encode($dato["estado"]);
                            $estado_desc = utf8_encode($dato["nombre"]);

                            if ($estado == 'Cerrada') {

                                $calificacion = $child['calificacion'];
                                $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                                $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($calificacion);
                                $calificacion = utf8_encode($dato["calificacion"]);

                                $franquicia = $child->Caracteristicas['franquicia'];
                                $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                                $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($franquicia);
                                $franquicia = utf8_encode($dato["descripcion"]);

                                $clase = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                                $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($clase);
                                $clase = utf8_encode($dato["descripcion"]);

                                $garantia = $child->Caracteristicas['clase'];
                                $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                                $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($garantia);
                                $garantia = utf8_encode($dato["nombre"]);

                                $adjetivo = $child->Adjetivo['codigo'];
                                $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                                $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($adjetivo);
                                $adjetivo = utf8_encode($dato["descripcion"]);

                                echo '
                                        <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                            <tr>
                                                <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                                <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                                <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                                <td class="td-centrado titulos_tablas">Calf</td>
                                                <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                                <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                                <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                                <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                                <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                                <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                                <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                                <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                                <td class="td-centrado titulos_tablas">47 meses</td>
                                            </tr>
                                            <tr>
                                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                                <td class="td-centrado">AHO</td>
                                                <td class="td-centrado">' . $estado_desc . '</td>
                                                <td class="td-centrado">' . $calificacion . '</td>
                                                <td class="td-centrado">' . $adjetivo . '</td>
                                                <td class="td-centrado">' . $child['numero'] . '</td>
                                                <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                                <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                                <td class="td-centrado"></td> 
                                                <td class="td-centrado">' . $child['ciudad'] . '</td>
                                                <td class="td-centrado">' . $child['oficina'] . '</td>
                                                <td class="td-centrado"></td>
                                                <td class="td-centrado"></td>
                                            </tr>
                                        </table>
                                    ';
                            }
                        }
                    }

                    foreach ($xml->TarjetaCredito as $child) {
                        if ($child['sector'] == '4') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }

                    foreach ($xml->CuentaCartera as $child) {
                        if ($child['sector'] == '4') {

                            if ($estado == 10) {
                                $query = ("SELECT * FROM experian_hdcacierta_estadocuenta where codigo = '" . $estado . "'");
                                $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                $dato = sqlsrv_fetch_array($estado);
                                $estado = utf8_encode($dato["descripcion"]);
                            } else {
                                if ($estado == 46 and $child['formaPago'] == '3') {
                                    $estado = "Pago Jur.";
                                } else {
                                    if ($estado == 46 and $child['formaPago'] != '3') {
                                        $estado = "Pago Vol.";
                                    } else {
                                        $query = ("SELECT * FROM experian_hdcacierta_codigosestadospagotarjetacartera where codigo = '" . $estado . "'");
                                        $estado = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                                        $dato = sqlsrv_fetch_array($estado);
                                        $estado = utf8_encode($dato["Nombre"]);
                                    }
                                }
                            }

                            $calificacion = $child['calificacion'];
                            $query = ("SELECT * FROM experian_hdcacierta_CalificacionTarjetaCreditoCuentaCartera where codigo = '" . $calificacion . "'");
                            $calificacion = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($calificacion);
                            $calificacion = utf8_encode($dato["calificacion"]);

                            $franquicia = $child->Caracteristicas['franquicia'];
                            $query = ("SELECT * FROM experian_hdcacierta_Franquiciatarjetacredito where codigo = '" . $franquicia . "'");
                            $franquicia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($franquicia);
                            $franquicia = utf8_encode($dato["descripcion"]);

                            $clase = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_Clasetarjetacredito where codigo = '" . $clase . "'");
                            $clase = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($clase);
                            $clase = utf8_encode($dato["descripcion"]);

                            $garantia = $child->Caracteristicas['clase'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantias_habitopagoobligacionesvigentes where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);

                            $adjetivo = $child->Adjetivo['codigo'];
                            $query = ("SELECT * FROM experian_hdcacierta_adjetivoscuentaahorrocorriente where codigo = '" . $adjetivo . "'");
                            $adjetivo = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($adjetivo);
                            $adjetivo = utf8_encode($dato["descripcion"]);

                            echo '
                                    <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                                        <tr>
                                            <td class="td-centrado titulos_tablas">Entidad Informante</td>
                                            <td class="td-centrado titulos_tablas">Tipo Cuenta</td>
                                            <td class="td-centrado titulos_tablas">Estado de la Obligación</td>
                                            <td class="td-centrado titulos_tablas">Calf</td>
                                            <td class="td-centrado titulos_tablas">Adjetivo-fecha </td>
                                            <td class="td-centrado titulos_tablas">Num Cta 9 dígitos</td>
                                            <td class="td-centrado titulos_tablas">Fecha Apertura</td>
                                            <td class="td-centrado titulos_tablas">Fecha Cierre</td>
                                            <td class="td-centrado titulos_tablas">Vlr o cupo inicia</td>
                                            <td class="td-centrado titulos_tablas">Ciudad/Fecha</td>
                                            <td class="td-centrado titulos_tablas">Oficina/Deudor</td>
                                            <td class="td-centrado titulos_tablas">Desacuerdo con la  inform.</td>
                                            <td class="td-centrado titulos_tablas">47 meses</td>
                                        </tr>
                                        <tr>
                                            <td class="td-centrado">' . $child['entidad'] . '</td>
                                            <td class="td-centrado">AHO</td>
                                            <td class="td-centrado">' . $estado_desc . '</td>
                                            <td class="td-centrado">' . $calificacion . '</td>
                                            <td class="td-centrado">' . $adjetivo . '</td>
                                            <td class="td-centrado">' . $child['numero'] . '</td>
                                            <td class="td-centrado">' . $child['fechaApertura'] . '</td>
                                            <td class="td-centrado">' . $child->Estado['fecha'] . '</td>
                                            <td class="td-centrado"></td> 
                                            <td class="td-centrado">' . $child['ciudad'] . '</td>
                                            <td class="td-centrado">' . $child['oficina'] . '</td>
                                            <td class="td-centrado"></td>
                                            <td class="td-centrado">' . $child['comportamiento'] . '</td>
                                        </tr>
                                    </table>
                                ';
                        }
                    }
                    ?>

                </tr>

            </table>

            <div class="container titulos">
                <span class="left">ANALISIS DE VECTORES</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                <?php
                $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->AnalisisVectores->Sector;
                if ($xml) {
                    foreach ($xml as $padre) {
                        $nombre_sector = $padre['nombreSector'];
                        echo
                        '<tr>
                                    <td colspan="28" class="titulos">' . $nombre_sector . '</td>
                                </tr>';
                        if ($nombre_sector == $padre['nombreSector']) {
                            echo
                            '<tr>
                                        <td class="titulos_tablas td-centrado">Entidad</td>
                                        <td class="titulos_tablas td-centrado"> Num Cta 9 <br> dígitos </td>
                                        <td class="titulos_tablas td-centrado">Tipo <br> Cuenta</td>
                                        <td class="titulos_tablas td-centrado">Estado</td>';
                            foreach ($padre->Cuenta->CaracterFecha as $child) {
                                echo '<td class="titulos_tablas td-centrado">' . date('M y', strtotime($child['fecha'])) . '</td>';
                            }
                            echo '</tr>';
                            foreach ($padre->Cuenta as $entidad) {
                                echo '<tr>';
                                echo '<td class="td-centrado">' . $entidad['entidad'] . '</td>
                                    <td class="td-centrado">' . $entidad['numeroCuenta'] . '</td>
                                    <td class="td-centrado">' . $entidad['tipoCuenta'] . '</td>
                                    <td class="td-centrado">' . $entidad['estado'] . '</td>';
                                foreach ($entidad->CaracterFecha as $valor) {
                                    echo '<td class="td-centrado">' . $valor['saldoDeudaTotalMora'] . '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    }
                }
                ?>

            </table>




            <div class="container titulos">
                <span class="left">EVOLUCIÓN DE LA DEUDA</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                <?php
                $xml = $objeto_ws->Informe->InfoAgregadaMicrocredito->EvolucionDeuda->EvolucionDeudaSector;

                echo '<tr>
                            <td class="titulos_tablas td-centrado">Tipo Cuenta</td>
                            <td class="titulos_tablas td-centrado">Valores</td>';
                if ($xml) {
                    foreach ($xml->EvolucionDeudaTipoCuenta->EvolucionDeudaValorTrimestre as $trimestre) {
                        echo '<td class="titulos_tablas td-centrado">' . $trimestre['trimestre'] . '</td>';
                    }
                }
                echo '</tr>';
                if ($xml) {
                    foreach ($xml as $padre) {
                        $nombre_sector = $padre['nombreSector'];
                        echo
                        '<tr>
                                    <td colspan="28" class="titulos"> Sector ' . $nombre_sector . '</td>
                                </tr>';
                        if ($nombre_sector == $padre['nombreSector']) {
                            $tipo_Cuenta = $padre->EvolucionDeudaTipoCuenta['tipoCuenta'];
                            $query = ("SELECT * FROM experian_hdcacierta_tiposcuenta where codigo = '" . $tipo_Cuenta . "'");
                            $tipo_Cuenta = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($tipo_Cuenta);
                            $tipo_Cuenta = utf8_encode($dato["descripcion"]);

                            echo '<tr> <td rowspan="8" class="td-centrado">' . $tipo_Cuenta . '</td> </tr>';

                            $datos = $padre->EvolucionDeudaTipoCuenta->EvolucionDeudaValorTrimestre;
                            echo '<tr> <td class="td-centrado">Num</td> ';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['num'] . '</td>';
                            }
                            echo ' </tr>';

                            echo '<tr> <td class="td-centrado">Vlr o Cupo Inicial</td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['cupoInicial'] . '</td>';
                            }
                            echo '</tr>';

                            echo '<tr> <td class="td-centrado">Saldo</td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['saldo'] . '</td>';
                            }
                            echo '</tr>';

                            echo '<tr> <td class="td-centrado"> Saldo en Mora </td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['saldoMora'] . '</td>';
                            }
                            echo '</tr>';

                            echo '<tr> <td class="td-centrado"> Valor Cuota </td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['cuota'] . '</td>';
                            }
                            echo '</tr>';

                            echo '<tr> <td class="td-centrado"> % Deuda </td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['porcentajeDeuda'] . '</td>';
                            }
                            echo '</tr>';

                            echo '<tr> <td class="td-centrado"> &#60 Calificación </td>';
                            foreach ($datos as $value) {
                                echo '<td class="td-centrado">' . $value['textoMenorCalificacion'] . '</td>';
                            }
                            echo '</tr>';
                        }
                    }
                }
                ?>
            </table>




            <div class="container titulos">
                <span class="left">HISTÓRICO DE CONSULTAS</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                <tr>
                    <td class="td-centrado titulos_tablas">Fecha Ult. Consulta</td>
                    <td class="td-centrado titulos_tablas">Consultante</td>
                    <td class="td-centrado titulos_tablas">No. de Consultas mes</td>
                </tr>

                <?php
                $xml = $objeto_ws->Informe->Consulta;
                foreach ($xml as $child) {
                    echo
                    '<tr>
                                <td class="td-centrado">' . $child['fecha'] . '</td>
                                <td class="td-centrado">' . $child['entidad'] . '</td>
                                <td class="td-centrado">' . $child['cantidad'] . '</td>
                            </tr>';
                }
                ?>
            </table>

            <span class="pie-documento">
                *CONSULTAS LOTE: corresponden a consultas realizadas por las Entidades para la supervisión y control del
                riesgo crediticio.
            </span>





            <div class="container titulos mt-3">
                <span class="left">ENDEUDAMIENTO GLOBAL CLASIFICADO</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">
                <?php
                $xml = $objeto_ws->Informe->EndeudamientoGlobal;
                $trimestres = array();
                foreach ($xml as $key) {
                    $trimestre = date("Ymd", strtotime($key['fechaReporte']));
                    if (in_array($trimestre, $trimestres) == false) {
                        $trimestres[] =  ($trimestre);
                    }
                }

                foreach ($trimestres as $trimestre) {
                    echo '<tr> <td colspan="28" class="titulos_tablas mt-2">TRIMESTRE ' . date("Y/m", strtotime($trimestre)) . '</td> </tr>';
                    $xml_trimestre = $objeto_ws->Informe->EndeudamientoGlobal;
                    $sectores = array();
                    foreach ($xml_trimestre as $sector) {
                        $trimestre_sector = date("Ymd", strtotime($sector['fechaReporte']));
                        //echo 'Trimestre del sector => '.$trimestre_sector.'</br>';
                        //echo 'Trimestre estado => '.$trimestre.'</br>';
                        $sector = $sector->Entidad['sector'];
                        //echo  'Sector del trimestre => '.$sector.'</br>';
                        //print_r ('Sectores => '.json_encode($sectores).'</br>');
                        if (in_array($sector, $sectores) == false and $trimestre == $trimestre_sector) {
                            $sectores[] = ($sector);
                        }
                    }

                    foreach ($sectores as $sector) {
                        echo '<tr> <td colspan="28" class="titulos"> Sector ' . $sector . '</td> </tr>';
                        echo
                        '<tr>
                                <td rowspan="2" class="td-centrado titulos_tablas">Entidad Informante</td>
                                <td rowspan="2" class="td-centrado titulos_tablas">Calf</td>
                                <td rowspan="2" class="td-centrado titulos_tablas">Num</td>
                                <td rowspan="2" class="td-centrado titulos_tablas">Saldo total</td>
                                <td colspan="2" class="td-centrado titulos_tablas">Comercial</td>
                                <td colspan="2" class="td-centrado titulos_tablas">Hipotecario</td>
                                <td colspan="2" class="td-centrado titulos_tablas">Consumo y Tarjeta de <br> Crédito</td>
                                <td colspan="2" class="td-centrado titulos_tablas">Microcrédito</td>
                                <td colspan="3" class="td-centrado titulos_tablas">Garantías</td>
                                <td rowspan="2" class="td-centrado titulos_tablas">Moneda</td>
                                <td rowspan="2" class="td-centrado titulos_tablas">Fuente</td>
                            </tr>
                            <tr>
                                <td class="td-centrado titulos_tablas">Nro</td>
                                <td class="td-centrado titulos_tablas">Miles $</td>
                                <td class="td-centrado titulos_tablas">Nro</td>
                                <td class="td-centrado titulos_tablas">Miles $</td>
                                <td class="td-centrado titulos_tablas">Nro</td>
                                <td class="td-centrado titulos_tablas">Miles $</td>
                                <td class="td-centrado titulos_tablas">Nro</td>
                                <td class="td-centrado titulos_tablas">Miles $</td>
                                <td class="td-centrado titulos_tablas">Tipo</td>
                                <td class="td-centrado titulos_tablas">Fecha Avalúo</td>
                                <td class="td-centrado titulos_tablas">Valor</td>
                            </tr>';
                        foreach ($xml_trimestre->Entidad as $value) {
                            $garantia = $xml_trimestre->Garantia['tipo'];
                            $query = ("SELECT * FROM experian_hdcacierta_garantiasendeudamientoglobalclasificado where codigo = '" . $garantia . "'");
                            $garantia = sqlsrv_query($link_CE, $query) or die(sqlsrv_errors($link_CE));
                            $dato = sqlsrv_fetch_array($garantia);
                            $garantia = utf8_encode($dato["nombre"]);
                            echo '
                                    <tr>
                                        <td class="td-centrado">' . $value['nombre'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['calificacion'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['numeroCreditos'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['saldoPendiente'] . '</td>
                                        <td class="td-centrado">0</td>
                                        <td class="td-centrado">$0</td>
                                        <td class="td-centrado">0</td>
                                        <td class="td-centrado">$0</td>
                                        <td class="td-centrado">' . $xml_trimestre['numeroCreditos'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['saldoPendiente'] . '</td>
                                        <td class="td-centrado">0</td>
                                        <td class="td-centrado">$0</td>
                                        <td class="td-centrado">' . $garantia . '</td>
                                        <td class="td-centrado">' . $xml_trimestre->Garantia['fecha'] . '</td>
                                        <td class="td-centrado">$' . $xml_trimestre['valor'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['moneda'] . '</td>
                                        <td class="td-centrado">' . $xml_trimestre['fuente'] . '</td>
                                    </tr>
                                ';
                        }
                    }
                }

                ?>
            </table>

            <div class="container titulos">
                <span class="left">RESUMEN ENDEUDAMIENTO GLOBAL</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-bordered table-sm mt-1 table-no-margin-bottom">

                <tr>
                    <td rowspan="2" class="td-centrado titulos_tablas">Fecha <br> Corte</td>
                    <td rowspan="2" class="td-centrado titulos_tablas">Sector</td>
                    <td colspan="2" class="td-centrado titulos_tablas">Comercial</td>
                    <td colspan="2" class="td-centrado titulos_tablas">Hipotecario</td>
                    <td colspan="2" class="td-centrado titulos_tablas">Consumo y Tarjeta de Crédito</td>
                    <td colspan="2" class="td-centrado titulos_tablas">Microcrédito</td>
                    <td rowspan="2" class="td-centrado titulos_tablas">% Participación</td>
                </tr>
                <tr>
                    <td class="td-centrado titulos_tablas">Nro</td>
                    <td class="td-centrado titulos_tablas">Miles $</td>
                    <td class="td-centrado titulos_tablas">Nro</td>
                    <td class="td-centrado titulos_tablas">Miles $</td>
                    <td class="td-centrado titulos_tablas">Nro</td>
                    <td class="td-centrado titulos_tablas">Miles $</td>
                    <td class="td-centrado titulos_tablas">Nro</td>
                    <td class="td-centrado titulos_tablas">Miles $</td>
                </tr>

                <?php
                $xml = $objeto_ws->Informe->InfoAgregada->ResumenEndeudamiento->Trimestre;
                if ($xml) {
                    foreach ($xml as $key) {
                        echo '
                                <tr>
                                    <td rowspan="5" class="td-centrado">' . $key->EvolucionDeudaTipoCuenta->EvolucionDeudaValorTrimestre['trimestre'] . '</td>
                                    <td class="td-centrado">Financiero</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">3</td>
                                    <td class="td-centrado">%50,772</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">49.8%</td>
                                </tr>
                                <tr>
                                    <td class="td-centrado">Cooperativo</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">3</td>
                                    <td class="td-centrado">%50,772</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">49.8%</td>
                                </tr>
                                <tr>
                                    <td class="td-centrado">Real</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">3</td>
                                    <td class="td-centrado">%50,772</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">49.8%</td>
                                </tr>
                                <tr>
                                    <td class="td-centrado">Telcos</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">3</td>
                                    <td class="td-centrado">%50,772</td>
                                    <td class="td-centrado">0</td>
                                    <td class="td-centrado">$0</td>
                                    <td class="td-centrado">49.8%</td>
                                </tr>
                                <tr>
                                    <td class="titulos_tablas td-centrado">Total</td>
                                    <td class="titulos_tablas td-centrado">0</td>
                                    <td class="titulos_tablas td-centrado">$0</td>
                                    <td class="titulos_tablas td-centrado">0</td>
                                    <td class="titulos_tablas td-centrado">$0</td>
                                    <td class="titulos_tablas td-centrado">3</td>
                                    <td class="titulos_tablas td-centrado">%50,772</td>
                                    <td class="titulos_tablas td-centrado">0</td>
                                    <td class="titulos_tablas td-centrado">$0</td>
                                    <td class="titulos_tablas td-centrado">100%</td>
                                </tr>';
                    }
                }
                ?>

            </table>

            <div class="container titulos mt-3">
                <span class="left">Fin-consulta Tipo 1. La consulta fue efectiva.</span>
            </div>

            <div class="container mb-3 mt-3">
                <span>Consultado Por:</span>
            </div>

            <div class="container titulos">
                <span class="left">INFORMACIÓN BASICA</span>
                <span class="right">6IDI5CF</span>
            </div>

            <table class="table table-sm table-bordered mt-2">
                <tbody>
                    <tr>
                        <th class="titulos_tablas">Tipo Documento</th>
                        <td>C.C.</td>
                        <th class="titulos_tablas">Número</th>
                        <td>1061709769</td>
                        <th class="titulos_tablas">Estado Documento</th>
                        <td>Vigente</td>
                        <th class="titulos_tablas">Lugar Expedición</th>
                        <td>POPAYAN</td>
                        <th class="titulos_tablas">Fecha Expedición</th>
                        <td>25/07/2006</td>
                    </tr>
                    <tr>
                        <th class="titulos_tablas">Nombre</th>
                        <td>CABRERA ZAMBRANO JOHANA VANESSA</td>
                        <th class="titulos_tablas">Rango Edad</th>
                        <td>29-35</td>
                        <th class="titulos_tablas">Género</th>
                        <td>-</td>
                        <th class="titulos_tablas">Tiene RUT?</th>
                        <td>SI</td>
                        <th class="titulos_tablas">Antiguedad</th>
                        <td>- -</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>


</body>

</html>