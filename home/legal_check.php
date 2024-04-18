<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);    
    include ('../functions.php');    
    $link = conectar();
    $link_CE = conectar_consultas_externas();

    //Consumiendo el Web Service
    $experian_hdcacierta_parametros = '"idType":"1","idNumber":"80921228","lastName":"ORTIZ","product":"'.$experian_hdcacierta_product.'","userId":"'.$experian_userid.'","password":"'.$experian_password.'"';
    $xmlstr = WSCentrales($experian_hdcacierta_url, $experian_hdcacierta_parametros);
    $xmlstr = utf8_encode($xmlstr); // Convertir en texto plano
    $xmlstr = reemplazar_caracteres_WS($xmlstr);

    $rs = sqlsrv_query($link, "SELECT respuesta from consultas_externas where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND servicio = 'LEGALCHECK'");
    $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
    $xmlstr = $fila["respuesta"];
    $xmlstr = reemplazar_caracteres_WS2($xmlstr);
    var_dump($xmlstr);
    //libxml_use_internal_errors(true);
    //$objeto_ws = simplexml_load_string($xmlstr);
    //print_r($objeto_ws);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../plantillas/bootstrap_4.6.0/css/bootstrap.min.css" crossorigin="anonymous">

    <title>Legal Check</title>
</head>

<body>
    <div class="container mb-xxl-5">
        <div class="row">
            <div class="col -3">
                <img src="../images/logo_transunion.png" alt="logo TransUnion">
            </div>
            <div class="col -6"></div>
            <div class="col -3">
                <img src="../images/logo_legal_check.png" alt="Logo Legal Check">
            </div>
        </div>

        <div class="row">
            <h5>Datos de la consulta</h5>
        </div>


        <table class="table-bordered table table-sm">
            <tr>
                <td class="bolder">Nombre</td>
                <td>Variable Nombre</td>
            </tr>

            <tr>
                <td class="bolder">Documento</td>
                <td>Variable Documento</td>
            </tr>

            <tr>
                <td class="bolder">Estado del documento</td>
                <td>Variable Estado del documento</td>
            </tr>

            <tr>
                <td class="bolder">Rango de edad</td>
                <td>Variable Rango de edad</td>
            </tr>

            <tr>
                <td class="bolder">Codigo del certificado</td>
                <td>Variable Codigo del certificado</td>
            </tr>

            <tr>
                <td class="bolder">Fecha de la consulta</td>
                <td>Variable Fecha de la consulta</td>
            </tr>

        </table>



        <h5>Resumen de la Consulta</h5>
        <span class="span">Procesos Judiciales</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Procesos judiciales en Contra</td>
                    <td>0</td>
                    <td>Procesos Judiciales por Nombre </td>
                    <td>0</td>
                </tr>

                <tr>
                    <td>Juzgados de Ejecución de Penas y Medidas de Seguridad </td>
                    <td>0</td>

                </tr>
            </tbody>
        </table>



        <span class="span">Listas Nacionales</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>AMV - Sanciones Autorregulador del Mercado de Valores Colombia</td>
                    <td>0</td>
                    <td>Fiscalía General de la Nación</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Auditoria General de la República</td>
                    <td>0</td>
                    <td>Los más buscados de Colombia</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Boletín de la Fiscalía General de la Nación</td>
                    <td>0</td>
                    <td>Procuraduría General de la Nación</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Cámara de Comercio</td>
                    <td>0</td>
                    <td>Sanciones del Codigo de Policia</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Contraloria General de la Republica</td>
                    <td>0</td>
                    <td>Superintendencia de Sociedades</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>DIAN (declaración de proveedor ficticio o insolvente)</td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Listas Internacionales</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                    <th class="bolder">Lista</th>
                    <th class="bolder"># coincidencias</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ATF - Agencia de Alcohol, Tabaco, Armas de Fuego y Explosivos</td>
                    <td>0</td>
                    <td>Lista Clinton (OFAC)</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Banco Interamericano de Desarrollo</td>
                    <td>0</td>
                    <td>Los más buscados de Panamá</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Banco Mundial</td>
                    <td>0</td>
                    <td>NCTC (Centro Nacional Contra Terrorismo - 75 Agencias USA)</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>BSIF - Sanciones Económicas autónomas Canadá</td>
                    <td>0</td>
                    <td>Oficina de sanciones financieras del Reino Unido</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>CBI – Oficina Central de Investigaciones de la India</td>
                    <td>0</td>
                    <td>ONU (Consejo de Seguridad de la Naciones Unidas)</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>DEA (Agencia antidrogas de los Estados Unidos)</td>
                    <td>0</td>
                    <td>OSFI – Superintendencia de Instituciones Financieras de Canadá</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Departamento de estado de los Estados Unidos de América</td>
                    <td>0</td>
                    <td>Panama Papers (ICIJ)</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>EBF (Federación Bancaria Europea)</td>
                    <td>0</td>
                    <td>Sanciones Económicas Canadá</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td> ENFAST (Red Europea de Equipos de Búsqueda Activa de Sospechosos)</td>
                    <td>0</td>
                    <td> Sanciones Financieras Reino Unido</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Europol - Más buscados Unión Europea</td>
                    <td>0</td>
                    <td>Servicio de Inmigración y Control de Aduanas de los Estados Unidos - ICE</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Europol (Oficina Europea de Policía)</td>
                    <td>0</td>
                    <td>SLEDCOM (Comité Federal de Investigación y Seguridad Ruso)</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>FBI (Buró Federal de investigación - USA)</td>
                    <td>0</td>
                    <td>Union Europea</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td> FCPA (Involucrados en prácticas corruptas)</td>
                    <td>0</td>
                    <td> US EPA - Agencia de Protección Ambiental de Estados Unido</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td> HM TREASURY</td>
                    <td>0</td>
                    <td> Venezolanos vinculados a blanqueo de capitales</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td> INTERPOL </td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Personas Expuestas Políticamente (PEPs)</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder aling-right"># coincidencias</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Personas Expuestas Políticamente (PEPs)</td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Noticias</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder aling-right"># coincidencias</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Noticias</td>
                    <td class="aling-right">0</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Procesos Judiciales</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder aling-right">Detalles</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Procesos Judiciales en Contra</td>
                    <td>No se encontraron procesos judiciales</td>
                </tr>
                <tr>
                    <td>Procesos Judiciales Interpuestos</td>
                    <td>No se encontraron procesos judiciales</td>
                </tr>
                <tr>
                    <td>Procesos Judiciales por Nombre</td>
                    <td>Se encontraron 1 Procesos judiciales por nombre</td>
                </tr>

                <tr>
                    <td colspan="2">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <td>Nº</td>
                                    <td>Numero Radicacion</td>
                                    <td>Fecha Radicacion</td>
                                    <td>Clase</td>
                                    <td>Ponente</td>
                                    <td>Sujetos Procesales</td>
                                    <td>Detalles</td>
                                    <td>#</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>11001400302020190075800</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Demandante: MARIA GRACIELA GONZALEZ,Demandado: WILSON ANDRES BOLIVAR
                                        GARCIA,Demandado: ANGELA ADRIANA CORTES HURTADO</td>
                                    <td>detalles</td>
                                    <td><input type="checkbox" name="" id=""></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>Procesos Judiciales (LA/FT)</td>
                    <td>No se encontraron procesos judiciales</td>
                </tr>

                <tr>
                    <td> Juzgados de Ejecución de Penas y Medidas de Seguridad</td>
                    <td> No se encontraron procesos judiciales</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Listas Nacionales</span>
        <table class="table-bordered table table-sm">
            <thead>
                <tr>
                    <th class="bolder">Lista</th>
                    <th class="bolder">Detalles</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>AMV - Sanciones Autorregulador del Mercado de Valores Colombia</td>
                    <td>Sin coincidencias</td>
                </tr>
                <tr>
                    <td>
                        Fiscalía General de la Nación
                    </td>
                    <td class="aling-right">
                        Sin coincidencias Aviso:En esta sección se relacionan los procesos judiciales (no se presentan
                        denuncias) de las personas Naturales o Jurídicas en los cuales la fiscalía haga parte del
                        proceso.
                    </td>
                </tr>
                <tr>
                    <td>Auditoria General de la República</td>
                    <td>Sin coincidencias</td>
                </tr>
                <tr>
                    <td>Los más buscados de Colombia</td>
                    <td>Sin coincidencias</td>
                </tr>
                <tr>
                    <td>Boletín de la Fiscalía General de la Nación</td>
                    <td>Sin coincidencias</td>
                </tr>
            </tbody>
        </table>



        <span class="span">Fin de la Consulta</span>
        <span class="pie-documento">
            IMPORTANTE: Esta información es el resultado de la obtención y procesamiento de datos públicos para la toma
            de decisiones en materia de Cumplimiento Normativo y para la
            prevención de riesgos. LegalCheck no administra ninguna fuente de información por que su contenido es
            atribuible a cada fuente. El uso indebido es responsabilidad del Consultante o Usuario.
        </span>


    </div>

</body>

</html>