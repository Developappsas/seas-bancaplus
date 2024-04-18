<?php

    include ('../functions.php');
    include ('../function_blob_storage.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_convenio'])){
                $consultarSoporteConvenio="SELECT * FROM convenios_pagadurias WHERE id=".$_POST['id_convenio'];
                $queryConvenio=sqlsrv_query($link,$consultarSoporteConvenio);
                $resConvenio=sqlsrv_fetch_array($queryConvenio, SQLSRV_FETCH_ASSOC);

                $queryDelete = "DELETE FROM convenios_pagadurias WHERE id = ".$_POST['id_convenio'];
            
                if(sqlsrv_query($link,$queryDelete)){
                    delete_file("pagadurias",$resConvenio["id_pagaduria"]."/".$resConvenio["soporte_convenio"]);
                    $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
                }else{
                    $data = array('code' => 500, 'mensaje' => 'No se Ha podido Eliminar');
                }
         
            
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }

    
    echo json_encode($data);
?>