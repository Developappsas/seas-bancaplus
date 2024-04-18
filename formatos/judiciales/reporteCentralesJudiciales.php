<!doctype html>
<html lang="es">
<?php
    include ('../../functions.php');
    $link = conectar_utf();

    //Mostrar errores
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

?>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/x-icon" href="../../images/favicon.ico">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../../plantillas/bootstrap_4.6.0/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
        <link href="../../sty.css?v=2" rel="stylesheet" type="text/css" crossorigin="anonymous">
        <title>Centrales Judiciales</title>
        <script src="../../plugins/jquery/jquery.min.js"></script>
        <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    </head>
    
    <body style="font-family: Verdana, Geneva, Tahoma, sans-serif;">
        <?php
        // numeroDocumento -> numero de la cedula a consultar
        // nombre -> nombre completo de la persona a consultar
        // idTipo -> 1. persona natural
        // tipoDocumento -> cedula -> 1, cedula extrangera -> 2, pasaporte -> 3

        if (isset($_REQUEST["id_simulacion"])) {
            
            $consultarInfoSimulacion="SELECT so.cedula, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.celular FROM solicitud so WHERE so.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

            $queryInformacionSim = sqlsrv_query($link, $consultarInfoSimulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($queryInformacionSim) > 0) {

                $resInformacionSim = sqlsrv_fetch_array($queryInformacionSim);                
                $numeroDocumento = $resInformacionSim["cedula"];
                $idTipo = 1;
                $tipoDocumento = 1;
                $nombre = $resInformacionSim["nombre1"]." ".$resInformacionSim["nombre2"]." ".$resInformacionSim["apellido1"]." ".$resInformacionSim["apellido2"];

                try {
                    $ruta = $urlPrincipal."/servicios/judicial/centralesJudiciales.php?idTipo=".$idTipo."&tipoDocumento=".$tipoDocumento."&numeroDocumento=".$numeroDocumento."&nombre=".$nombre."";
                    $ruta = str_replace(" ", "%20", $ruta);
                    
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $ruta,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $json = json_decode($response);
                    ?>
                    <div class="container container-pdf mt-4">
                        <div class="row">
                            <div class="col -3"></div>
                            <div class="col -6"></div>
                            <div class="col -3" style="margin-top: 10px;">
                                <img src="../../images/logo.png" alt="">
                            </div>
                        </div>
                        <h1 style="text-align: center; color: #1B5387; font-weight: bold;"><b>CONSULTA CENTRALES JUDICIALES</b></h1>
                        <br>
                        <div class="container">
                            <div class="container titulos">
                                <span class="left">INFORMACIÓN BASICA</span>
                            </div>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <th class="titulos_tablas">Cedula</th>
                                    <th class="titulos_tablas">Nombre</th>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo $numeroDocumento ?></td>
                                    <td><?php echo $json->respuesta[0]->INFORMACION_GENERAL; ?></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="container titulos">
                                <span class="left">LISTAS RECTRICTIVAS Y SANCIONES NACIONALES</span>
                            </div>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th class="titulos_tablas">Nombre central</th>
                                        <th class="titulos_tablas">Nombre coincidencia</th>
                                        <th class="titulos_tablas">documento coincidencia</th>
                                        <th class="titulos_tablas">Zona</th>
                                        <th class="titulos_tablas">Riesgo</th>
                                        <th class="titulos_tablas">Más información</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $centralNa = $json->respuesta[0]->CENTRALES_NACIONALES;
                                    $numNa = sizeof($centralNa);
                                    if ($numNa > 0) {
                                        for ($i=0; $i < $numNa; $i++) { 

                                            $res_riesgoNa = $centralNa[$i]->riesgo == null ? "NO" : "SI";
                                            $res_enlaceNa = $centralNa[$i]->enlace == null ? '<td> No tiene información adicional </td>' : '<td><a href="' . $centralNa[$i]->enlace. '" target="_blank">Ver</a></td></tr>';
                                            
                                            echo '<tr><td>' . $centralNa[$i]->central. '</td>';                                
                                            echo '<td>' . $centralNa[$i]->nombreEncontrado. '</td>';                    
                                            echo '<td>' . $centralNa[$i]->idEncontrado. '</td>';                    
                                            echo '<td>' . $centralNa[$i]->zona. '</td>';                    
                                            echo '<td>' . $res_riesgoNa. '</td>';
                                            echo $res_enlaceNa . '</tr>';

                                            $actuacionesNa = $centralNa[$i]->actuaciones;

                                            if ($actuacionesNa != null) {
                                                $sizeActuacionesNa = sizeof($actuacionesNa);
                                                if ($sizeActuacionesNa > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES ". $centralNa[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas'>Fecha</th><th class='titulos_tablas'>Tipo Actuacion</th><th class='titulos_tablas' colspan='3'>Cuaderno</th><th class='titulos_tablas'>Folio</th></tr>";
                                                    for ($j=0; $j < $sizeActuacionesNa; $j++) {
                                                        echo '<tr><td>' . $actuacionesNa[$j]->fecha. '</td>';                                
                                                        echo '<td>' . $actuacionesNa[$j]->tipoActuacion. '</td>';                    
                                                        echo '<td colspan="3">' . $actuacionesNa[$j]->cuaderno. '</td>';                    
                                                        echo '<td>' . $actuacionesNa[$j]->folio. '</td></tr>';  
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                     
                                                }
                                            }

                                            $procesosNa = $centralNa[$i]->procesos;

                                            if ($procesosNa != null) {
                                                $procesosNa = sizeof($procesosNa);
                                                if ($procesosNa > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>PROCESOS ". $centralNa[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas' colspan='2'>Departamento</th><th class='titulos_tablas' colspan='2'>Despacho</th><th class='titulos_tablas'>Fecha proceso</th><th class='titulos_tablas'>Fecha Ultima actuacion</th></tr>";
                                                    for ($j=0; $j < $procesosNa; $j++) {
                                                        echo '<tr><td colspan="2">' . $procesosNa[$j]->departamento. '</td>';                                
                                                        echo '<td colspan="2">' . $procesosNa[$j]->dedespacho. '</td>';                    
                                                        echo '<td>' . $procesosNa[$j]->fechaProceso. '</td>';                    
                                                        echo '<td>' . $procesosNa[$j]->fechaUltimaActuacion. '</td></tr>'; 
                                                        
                                                        $actuacionesProcesoNa = $procesosNa[$j]->actuaciones;

                                                        if ($actuacionesProcesoNa != null) {
                                                            $sizeActuacionesProcesoNa = sizeof($actuacionesProcesoNa);
                                                            if ($sizeActuacionesProcesoNa > 0) {
                                                                echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES DEL PROCESO EN EL DEPARTAMENTO ". $procesosNa[$j]->departamento ."</b></th></tr>";
                                                                echo "<tr><th class='titulos_tablas'>Id Resgistro Actuacion</th><th class='titulos_tablas'>llave Proceso</th><th class='titulos_tablas'>Fecha actuacion</th><th class='titulos_tablas'>Actuacion</th><th class='titulos_tablas' colspan='2'>Anotacion</th></tr>";
                                                                for ($k=0; $k < $sizeActuacionesProcesoNa; $k++) {
                                                                    echo '<tr><td>' . $actuacionesProcesoNa[$k]->idRegActuacion. '</td>';                                
                                                                    echo '<td>' . $actuacionesProcesoNa[$k]->llaveProceso. '</td>';                    
                                                                    echo '<td>' . $actuacionesProcesoNa[$k]->fechaActuacion. '</td>';             
                                                                    echo '<td>' . $actuacionesProcesoNa[$k]->actuacion. '</td>';       
                                                                    echo '<td colspan="2">' . $actuacionesProcesoNa[$k]->anotacion. '</td></tr>';                                                         
                                                                }
                                                                echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                 
                                                            }
                                                        }
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                     
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<td> No se encontraron registros en las centrales nacionales, por favor revisar la información he intentar de nuevo </td>';
                                    }
                                ?>
                                </tbody>
                            </table>
                            <div class="container titulos">
                                <span class="left">LISTAS RECTRICTIVAS Y SANCIONES INTERNACIONALES</span>
                            </div>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th class="titulos_tablas">Nombre central</th>
                                        <th class="titulos_tablas">Nombre coincidencia</th>
                                        <th class="titulos_tablas">documento coincidencia</th>
                                        <th class="titulos_tablas">Zona</th>
                                        <th class="titulos_tablas">Riesgo</th>
                                        <th class="titulos_tablas">Más información</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $centralIn = $json->respuesta[0]->CENTRALES_INTERNACIONALES;
                                    $numIn = sizeof($centralIn);
                                    if ($numIn > 0) {
                                        for ($i=0; $i < $numIn; $i++) { 

                                            $res_riesgoIn = $centralIn[$i]->riesgo == null ? "NO" : "SI";
                                            $res_enlaceIn = $centralIn[$i]->enlace == null ? '<td> No tiene información adicional </td>' : '<td><a href="' . $centralIn[$i]->enlace. '" target="_blank">Ver</a></td></tr>';

                                            echo '<tr><td>' . $centralIn[$i]->central. '</td>';                                
                                            echo '<td>' . $centralIn[$i]->nombreEncontrado. '</td>';                    
                                            echo '<td>' . $centralIn[$i]->idEncontrado. '</td>';                    
                                            echo '<td>' . $centralIn[$i]->zona. '</td>';                    
                                            echo '<td>' . $res_riesgoIn. '</td>';
                                            echo $res_enlaceIn . '</tr>';

                                            $actuacionesIn = $centralIn[$i]->actuaciones;

                                            if ($actuacionesIn != null) {
                                                $sizeActuacionesIn = sizeof($actuacionesIn);
                                                if ($sizeActuacionesIn > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES ". $centralIn[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas'>Fecha</th><th class='titulos_tablas'>Tipo Actuacion</th><th class='titulos_tablas' colspan='3'>Cuaderno</th><th class='titulos_tablas'>Folio</th></tr>";
                                                    for ($j=0; $j < $sizeActuacionesIn; $j++) {
                                                        echo '<tr><td>' . $actuacionesIn[$j]->fecha. '</td>';                                
                                                        echo '<td>' . $actuacionesIn[$j]->tipoActuacion. '</td>';                    
                                                        echo '<td colspan="3">' . $actuacionesIn[$j]->cuaderno. '</td>';                    
                                                        echo '<td>' . $actuacionesIn[$j]->folio. '</td></tr>';   
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                    
                                                }
                                            }

                                            $procesosIn = $centralIn[$i]->procesos;

                                            if ($procesosIn != null) {
                                                $procesosIn = sizeof($procesosIn);
                                                if ($procesosIn > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>PROCESOS ". $centralIn[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas' colspan='2'>Departamento</th><th class='titulos_tablas' colspan='2'>Despacho</th><th class='titulos_tablas' colspan='3'>Fecha proceso</th><th class='titulos_tablas'>Fecha Ultima actuacion</th></tr>";
                                                    for ($j=0; $j < $procesosIn; $j++) {
                                                        echo '<tr><td colspan="2">' . $procesosIn[$j]->departamento. '</td>';                                
                                                        echo '<td colspan="2">' . $procesosIn[$j]->dedespacho. '</td>';                    
                                                        echo '<td>' . $procesosIn[$j]->fechaProceso. '</td>';                    
                                                        echo '<td>' . $procesosIn[$j]->fechaUltimaActuacion. '</td></tr>';  

                                                        $actuacionesProcesoIn = $procesosIn[$j]->actuaciones;

                                                        if ($actuacionesProcesoIn != null) {
                                                            $sizeActuacionesProcesoIn = sizeof($actuacionesProcesoIn);
                                                            if ($sizeActuacionesProcesoIn > 0) {
                                                                echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES DEL PROCESO EN EL DEPARTAMENTO ". $procesosIn[$j]->departamento ."</b></th></tr>";
                                                                echo "<tr><th class='titulos_tablas'>Id Resgistro Actuacion</th><th class='titulos_tablas'>llave Proceso</th><th class='titulos_tablas'>Fecha actuacion</th><th class='titulos_tablas'>Actuacion</th><th class='titulos_tablas' colspan='2'>Anotacion</th></tr>";
                                                                for ($k=0; $k < $sizeActuacionesProcesoIn; $k++) {
                                                                    echo '<tr><td>' . $actuacionesProcesoIn[$k]->idRegActuacion. '</td>';                                
                                                                    echo '<td>' . $actuacionesProcesoIn[$k]->llaveProceso. '</td>';                    
                                                                    echo '<td>' . $actuacionesProcesoIn[$k]->fechaActuacion. '</td>';             
                                                                    echo '<td>' . $actuacionesProcesoIn[$k]->actuacion. '</td>';       
                                                                    echo '<td colspan="2">' . $actuacionesProcesoIn[$k]->anotacion. '</td></tr>';                                                         
                                                                }
                                                                echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                 
                                                            }
                                                        }
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                     
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<td> No se encontraron registros en las centrales internacionales, por favor revisar la información he intentar de nuevo </td>';
                                    }
                                ?>
                                </tbody>
                            </table>
                            <div class="container titulos">
                                <span class="left">PEPS - PERSONAS POLITICAMENTE Y PUBLICAMENTE EXPUESTAS</span>
                            </div>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th class="titulos_tablas">Nombre central</th>
                                        <th class="titulos_tablas">Nombre coincidencia</th>
                                        <th class="titulos_tablas">documento coincidencia</th>
                                        <th class="titulos_tablas">Zona</th>
                                        <th class="titulos_tablas">Riesgo</th>
                                        <th class="titulos_tablas">Más información</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $centralP = $json->respuesta[0]->PEPS;
                                    $numP = sizeof($centralP);
                                    if ($numP > 0) {
                                        for ($i=0; $i < $numP; $i++) { 

                                            $res_riesgoP = $centralP[$i]->riesgo == null ? "NO" : "SI";
                                            $res_enlaceP = $centralP[$i]->enlace == null ? '<td> No tiene información adicional </td>' : '<td><a href="' . $centralP[$i]->enlace. '">Ver</a></td></tr>';

                                            echo '<tr><td>' . $centralP[$i]->central. '</td>';                                
                                            echo '<td>' . $centralP[$i]->nombreEncontrado. '</td>';                    
                                            echo '<td>' . $centralP[$i]->idEncontrado. '</td>';                    
                                            echo '<td>' . $centralP[$i]->zona. '</td>';                    
                                            echo '<td>' . $res_riesgoP. '</td>';
                                            echo $res_enlaceP . '</tr>';

                                            $actuacionesP = $centralP[$i]->actuaciones;

                                            if ($actuacionesP != null) {
                                                $sizeActuacionesP = sizeof($actuacionesP);
                                                if ($sizeActuacionesP > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES ". $centralP[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas'>Fecha</th><th class='titulos_tablas'>Tipo Actuacion</th><th class='titulos_tablas' colspan='3'>Cuaderno</th><th class='titulos_tablas'>Folio</th></tr>";
                                                    for ($j=0; $j < $sizeActuacionesP; $j++) {
                                                        echo '<tr><td>' . $actuacionesP[$j]->fecha. '</td>';                                
                                                        echo '<td>' . $actuacionesP[$j]->tipoActuacion. '</td>';                    
                                                        echo '<td colspan="3">' . $actuacionesP[$j]->cuaderno. '</td>';                    
                                                        echo '<td>' . $actuacionesP[$j]->folio. '</td></tr>'; 
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                  
                                                }
                                            }

                                            $procesosP = $centralP[$i]->procesos;

                                            if ($procesosP != null) {
                                                $procesosP = sizeof($procesosP);
                                                if ($procesosP > 0) {
                                                    echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>PROCESOS ". $centralP[$i]->central ."</b></th></tr>";
                                                    echo "<tr><th class='titulos_tablas' colspan='2'>Departamento</th><th class='titulos_tablas' colspan='2'>Despacho</th><th class='titulos_tablas'>Fecha proceso</th><th class='titulos_tablas'>Fecha Ultima actuacion</th></tr>";
                                                    for ($j=0; $j < $procesosP; $j++) {
                                                        echo '<tr><td colspan="2">' . $procesosP[$j]->departamento. '</td>';                                
                                                        echo '<td colspan="2">' . $procesosP[$j]->dedespacho. '</td>';                    
                                                        echo '<td>' . $procesosP[$j]->fechaProceso. '</td>';                    
                                                        echo '<td>' . $procesosP[$j]->fechaUltimaActuacion. '</td></tr>';  

                                                        $actuacionesProcesoP = $procesosP[$j]->actuaciones;

                                                        if ($actuacionesProcesoP != null) {
                                                            $sizeActuacionesProcesoP = sizeof($actuacionesProcesoP);
                                                            if ($sizeActuacionesProcesoP > 0) {
                                                                echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES DEL PROCESO EN EL DEPARTAMENTO ". $procesosP[$j]->central ."</b></th></tr>";
                                                                echo "<tr><th class='titulos_tablas'>Id Resgistro Actuacion</th><th class='titulos_tablas'>llave Proceso</th><th class='titulos_tablas'>Fecha actuacion</th><th class='titulos_tablas'>Actuacion</th><th class='titulos_tablas' colspan='2'>Anotacion</th></tr>";
                                                                for ($k=0; $k < $sizeActuacionesProcesoP; $k++) {
                                                                    echo '<tr><td>' . $actuacionesProcesoP[$k]->idRegActuacion. '</td>';                                
                                                                    echo '<td>' . $actuacionesProcesoP[$k]->llaveProceso. '</td>';                    
                                                                    echo '<td>' . $actuacionesProcesoP[$k]->fechaActuacion. '</td>';             
                                                                    echo '<td>' . $actuacionesProcesoP[$k]->actuacion. '</td>';       
                                                                    echo '<td colspan="2">' . $actuacionesProcesoP[$k]->anotacion. '</td></tr>';                                                         
                                                                }
                                                                echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                 
                                                            }
                                                        }
                                                    }
                                                    echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                     
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<td> No se encontraron registros en PEPS, por favor revisar la información he intentar de nuevo </td>';
                                    }
                                ?>
                                </tbody>
                            </table>
                            <div class="container titulos">
                                <span class="left">RAMA JUDICIALES</span>
                            </div>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th class="titulos_tablas">Nombre central</th>
                                        <th class="titulos_tablas">Nombre coincidencia</th>
                                        <th class="titulos_tablas">documento coincidencia</th>
                                        <th class="titulos_tablas">Zona</th>
                                        <th class="titulos_tablas">Riesgo</th>
                                        <th class="titulos_tablas">Más información</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $centralRa = $json->respuesta[0]->RAMA_JUDICIAL;
                                $numRa = sizeof($centralRa);
                                if ($numRa > 0) {
                                    for ($i=0; $i < $numRa; $i++) { 

                                        $res_riesgoRa = $centralRa[$i]->riesgo == null ? "NO" : "SI";
                                        $res_enlaceRa = $centralRa[$i]->enlace == null ? '<td> No tiene información adicional </td>' : '<td><a href="' . $centralRa[$i]->enlace. '">Ver</a></td></tr>';

                                        echo '<tr><td>' . $centralRa[$i]->central. '</td>';                                
                                        echo '<td>' . $centralRa[$i]->nombreEncontrado. '</td>';                    
                                        echo '<td>' . $centralRa[$i]->idEncontrado. '</td>';                    
                                        echo '<td>' . $centralRa[$i]->zona. '</td>';                    
                                        echo '<td>' . $res_riesgoRa. '</td>';
                                        echo $res_enlaceRa . '</tr>';

                                        $actuacionesRa = $centralRa[$i]->actuaciones;

                                        if ($actuacionesRa != null) {
                                            $sizeActuacionesRa = sizeof($actuacionesRa);
                                            if ($sizeActuacionesRa > 0) {
                                                echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES ". $centralRa[$i]->central ."</b></th></tr>";
                                                echo "<tr><th class='titulos_tablas'>Fecha</th><th class='titulos_tablas'>Tipo Actuacion</th><th class='titulos_tablas' colspan='3'>Cuaderno</th><th class='titulos_tablas'>Folio</th></tr>";
                                                for ($j=0; $j < $sizeActuacionesRa; $j++) {
                                                    echo '<tr><td>' . $actuacionesRa[$j]->fecha. '</td>';                                
                                                    echo '<td>' . $actuacionesRa[$j]->tipoActuacion. '</td>';                    
                                                    echo '<td colspan="3">' . $actuacionesRa[$j]->cuaderno. '</td>';                    
                                                    echo '<td>' . $actuacionesRa[$j]->folio. '</td></tr>'; 
                                                }
                                                echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                  
                                            }
                                        }

                                        $procesosRa = $centralRa[$i]->procesos;

                                        if ($procesosRa != null) {
                                            $sizeProcesosRa = sizeof($procesosRa);
                                            if ($sizeProcesosRa > 0) {
                                                echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>PROCESOS ". $centralRa[$i]->central ."</b></th></tr>";
                                                echo "<tr><th class='titulos_tablas' colspan='2'>Departamento</th><th class='titulos_tablas' colspan='2'>Despacho</th><th class='titulos_tablas'>Fecha proceso</th><th class='titulos_tablas'>Fecha Ultima actuacion</th></tr>";
                                                for ($j=0; $j < $sizeProcesosRa; $j++) {
                                                    
                                                    echo '<tr><td colspan="2">' . $procesosRa[$j]->departamento. '</td>';                                
                                                    echo '<td colspan="2">' . $procesosRa[$j]->dedespacho. '</td>';                    
                                                    echo '<td>' . $procesosRa[$j]->fechaProceso. '</td>';                    
                                                    echo '<td>' . $procesosRa[$j]->fechaUltimaActuacion. '</td></tr>'; 
                                                    
                                                    $actuacionesProcesoRa = $procesosRa[$j]->actuaciones;

                                                    if ($actuacionesProcesoRa != null) {
                                                        $sizeActuacionesProcesoRa = sizeof($actuacionesProcesoRa);
                                                        if ($sizeActuacionesProcesoRa > 0) {
                                                            echo "<tr><th colspan='6' class='titulos_tablas' style='text-align: center;'><b>ACTUACIONES DEL PROCESO EN EL DEPARTAMENTO ". $procesosRa[$j]->central ."</b></th></tr>";
                                                            echo "<tr><th class='titulos_tablas'>Id Resgistro Actuacion</th><th class='titulos_tablas'>llave Proceso</th><th class='titulos_tablas'>Fecha actuacion</th><th class='titulos_tablas'>Actuacion</th><th class='titulos_tablas' colspan='2'>Anotacion</th></tr>";
                                                            for ($k=0; $k < $sizeActuacionesProcesoRa; $k++) {
                                                                echo '<tr><td>' . $actuacionesProcesoRa[$k]->idRegActuacion. '</td>';                                
                                                                echo '<td>' . $actuacionesProcesoRa[$k]->llaveProceso. '</td>';                    
                                                                echo '<td>' . $actuacionesProcesoRa[$k]->fechaActuacion. '</td>';             
                                                                echo '<td>' . $actuacionesProcesoRa[$k]->actuacion. '</td>';       
                                                                echo '<td colspan="2">' . $actuacionesProcesoRa[$k]->anotacion. '</td></tr>';                                                         
                                                            }
                                                            echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                 
                                                        }
                                                    }
                                                }
                                                echo '<tr><th colspan="6" style="background-color: #F0EFEF !important;"> </th></tr>';                   
                                            }
                                        }
                                    }
                                } else {
                                    echo '<td> No se encontraron registros en los procesos judiciales, por favor revisar la información he intentar de nuevo </td>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <?php 
                } catch (Exception $e) {
                    ?>
                    <script>
                        Swal.fire({
                            title: 'Error al consultar...',
                            text: 'No se obtuvo respuesta del servidor, por favor revisar información...',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    </script>
                    <?php 
                }
            } else { ?>
                <script>
                    Swal.fire({
                        title: 'Error al consultar...',
                        text: 'Hacen falta datos del formulario de solitud de SEAS para realizar la consulta...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                </script>
                <?php 
            }
        } else { ?>
            <script>
                Swal.fire({
                    title: 'Error al consultar...',
                    text: 'Hacen falta datos para realizar la consulta, Id Simulacion, no disponible...',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            </script>
            <?php 
        } ?>  
    </body>
</html>