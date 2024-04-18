
<?php
    session_start();
    include_once ('../functions.php');
    include_once ('../function_blob_storage.php');
    header("Content-Type: application/json; charset=utf-8");
    
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['opcion'])){

        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        $dato = '';

        switch ($opcion) {
            case 'CREAR':
                    
                $nombre_archivo=$explode[$i];

                $uniqueID = uniqid();
                $extension=explode("/",$_FILES['soporte']['type']);
                $nombreArc=md5(rand()+$_POST['id_pagaduria']).".".$extension[1];
                $fechaa =new DateTime();
            $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
                $metadata1 = array(
                    'id_pagaduria' => $_POST['id_pagaduria'],
                    'descripcion' => ($nombreArc),
                    'usuario_creacion' => $_SESSION["S_LOGIN"],
                    'fecha_creacion' => $fechaFormateada
                );
                upload_file($_FILES['soporte'], "pagadurias",$_POST['id_pagaduria']."/".$nombreArc, $metadata1);
                        
                    if(sqlsrv_query( $link,"INSERT INTO convenios_pagadurias (id_pagaduria, fecha_inicial, fecha_final,soporte_convenio) VALUES ('".$_POST['id_pagaduria']."', '".$_POST['fecha_inicio']."', '".$_POST['fecha_final']."','".$nombreArc."')")){

                        $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
                    }else{
                        $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
                    }
           

                break;
         

            default:
                $queryConsulta .= "";
                break;
        }       
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>