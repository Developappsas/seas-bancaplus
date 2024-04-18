<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_pagaduria'])){

        $queryPagaduria = "SELECT * FROM pagadurias WHERE id_pagaduria = ".$_POST['id_pagaduria'];
        $conPagaduria = sqlsrv_query($link, $queryPagaduria, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conPagaduria) > 0) {
            $resPagaduria=sqlsrv_fetch_array($conPagaduria);
                if ($resPagaduria["estado"]=="0")
                {
                    $nuevoEstado=1;
                }else{
                    $nuevoEstado=0;
                }
                $queryDelete = "UPDATE pagadurias SET estado='".$nuevoEstado."' WHERE id_pagaduria = ".$_POST['id_pagaduria'];
            
                if(sqlsrv_query($link, $queryDelete)){
                    $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
                }else{
                    $data = array('code' => 500, 'mensaje' => 'No se Ha podido Eliminar');
                }
       
            
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>