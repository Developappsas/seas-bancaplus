<?php
    include ('../../functions.php');
    include ('../../cors.php');
    
    $link = conectar_utf();
    
    $id_simulacion = $_POST['id_simulacion'];
    $usuario_habilitacion = $_POST['usuario_habilitacion'];
    $reponse = array( 'Estado'=>200, 'Mensaje'=>'', 'Data'=>$params );

    if ($id_simulacion) {   
        $query = 'UPDATE simulaciones SET formato_digital = 0 WHERE id_simulacion ='.$id_simulacion;
        $ejecutar_query = sqlsrv_query($link, $query);

        if ($ejecutar_query) {            
            $query = 'UPDATE formulario_digital f SET f.fecha_pagare_deceval = NULL, f.observacion_firma_pagare = NULL, f.observacion_crear_girador = NULL, f.id_usuario_anulacion = '.$usuario_habilitacion.', f.fecha_anulacion = now() ,f.observacion_crear_pagare = NULL, f.sub_estado_trx = NULL WHERE f.id_simulacion = '. $id_simulacion;


            $ejecutar_query = sqlsrv_query($link, $query) ;
            if ($ejecutar_query) {
                $reponse = array(
                    'Estado'=>200,
                    'Mensaje'=>'Credito Habilitado',
                    'Data'=>array('id_simulacion'=> $id_simulacion)
                );
            }else{
                $query = 'UPDATE simulaciones SET formato_digital = 1 WHERE id_simulacion = '.$id_simulacion;
                $ejecutar_query = sqlsrv_query($link, $query);
                $reponse = array(
                    'Estado'=>403,
                    'Mensaje'=>'No se pudo actualizar la tabla formulario digital.',
                    'Data'=>''
                );
            }
        }else{
            $reponse = array(
                'Estado'=>403,
                'Mensaje'=>'No se pudo habilitar el credito',
                'Data'=>''
            );
        }
    }else{
        $reponse = array(
            'Estado'=>404,
            'Mensaje'=>'no se reciben datos necesarios para habilitacion',
            'Data'=>array('id_simulacion'=> $id_simulacion)
        );

    }

    echo json_encode($reponse);
?>