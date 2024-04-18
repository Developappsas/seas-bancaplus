<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    ob_start();
    require ('../plugins/fpdf183/fpdf.php');
    include_once ('../functions.php');

    if(isset($_GET['id_simulacion'])){

        $link = conectar_utf();

        $sql = "SELECT  a.cedula, a.fecha_nacimiento, a.nombre, a.pagaduria, CONCAT(e.municipio, ' ',  b.fecha_expedicion) AS expedicion, b.celular, b.email, b.clave, a.fecha_radicado, CONCAT(c.nombre,' ',c.apellido) AS comercial, d.nombre AS oficina, f.fecha_visto FROM simulaciones a  LEFT JOIN solicitud b ON a.id_simulacion = b.id_simulacion  LEFT JOIN usuarios c ON c.id_usuario = a.id_comercial LEFT JOIN oficinas d ON d.id_oficina = a.id_oficina LEFT JOIN ciudades e ON (e.cod_municipio = b.lugar_expedicion) OR (e.cod_municipio = CONCAT(0,b.lugar_expedicion)) LEFT JOIN historial_tokens_verificacion_id f ON f.id_simulacion = a.id_simulacion AND f.id = (SELECT MAX(id) FROM historial_tokens_verificacion_id WHERE id_simulacion = a.id_simulacion) WHERE a.id_simulacion = ".$_GET['id_simulacion']." AND f.estado = 1";

    

        $query = sqlsrv_query($link,$sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        
        if(sqlsrv_num_rows($query) > 0){

            
            $datos = sqlsrv_fetch_array($query);
            $pdf = new FPDF();


            $pdf->AddPage();
            $pdf->SetAuthor('Sistemas Kredit', true);
            $pdf->SetCreator('Sistemas Kredit', true);
            
            $pdf->Image('./plantillas/autorizacion_consulta/plantilla.png', 5, 5, 202);
            
            $pdf->SetFont('Arial', '', 6);

            $nombres = utf8_decode($datos['nombre']);
            $fecha_nac = $datos['fecha_nacimiento'];
            $pagaduria = $datos['pagaduria'];
            $email = $datos['email'];
            $comercial = utf8_decode($datos['comercial']);
            $oficina = $datos['oficina'];

            $clave = $datos['clave'];

            $expedicion = utf8_decode($datos['expedicion']);
            $celular = $datos['celular'];
            $fecha_solicitud = $datos['fecha_radicado'];
            $cedula = $datos['cedula'];
            $fecha_terminos = $datos['fecha_visto'];

            $pdf->text(39.05, 100.6, $nombres);
            $pdf->text(39.05, 105, $fecha_nac);
            $pdf->text(31, 109.2, $pagaduria);
            $pdf->text(26.3, 113.8, $email);
            $pdf->text(39.08, 118, $comercial);
            $pdf->text(27.6, 122.4, $oficina);

            $pdf->text(67.8, 109.4, $clave);

            $pdf->text(111.6, 100.6, 'X');
            $pdf->text(115, 105, $expedicion);
            $pdf->text(102, 109.4, $celular);
            $pdf->text(147, 109.4, $fecha_solicitud);

            $pdf->text(130.1, 100.6, $cedula);

            $pdf->Image('../images/logo_pagare.png', 92, 114, 15);
            $pdf->text(108, 114, 'Firmado electronicamente por:');
            $pdf->text(108, 117, $nombres);
            $pdf->text(108, 120, 'CC'.$cedula);
            $pdf->text(108, 123, 'Ultima vez el '.$fecha_terminos);
            $pdf->SetFont('Arial', '', 8);
            $pdf->text(175.2, 113, 'NO APLICA');
            ob_end_clean();
            $pdf->Output();
        }
    }
?>