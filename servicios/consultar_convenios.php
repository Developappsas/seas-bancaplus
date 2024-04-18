<?php

    include ('../functions.php');
    include ('../function_blob_storage.php');
    include ('../cors.php');
    $link = conectar_utf();

       $opcion = '';
        
    $id_pagaduria=$_POST["id_pagaduria"];
        $dato = '';
        $queryConsulta = "SELECT * from convenios_pagadurias WHERE id_pagaduria='".$_POST["id_pagaduria"]."'";
 
        $conPagadurias = sqlsrv_query($link,$queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conPagadurias) > 0) {
            while ($response = sqlsrv_fetch_array($conPagadurias, SQLSRV_FETCH_ASSOC)) {
                $tasas[] = array(
                    "id_convenio" => $response["id"],
                    "fecha_inicio" => $response["fecha_inicial"],
                    "fecha_final" => $response["fecha_final"],
                    "soporte" => "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddPagaduria' onclick='openModalPagadurias(\"EDITAR\", ".$response["id_pagaduria"].")' name='".$response["id_pagaduria"]."' onClick='window.open('".generateBlobDownloadLinkWithSAS("documentos","'".$_REQUEST["id_pagaduria"]."/".$response["soporte_convenio"]."'")."','ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');'>Descargar</a>",
                    "opciones" =>   "<a class='btn btn-danger btn-sm' onclick='eliminarConvenio(".$response["id"].")'>Eliminar</a>
                                     <a class='btn btn-success btn-sm' name='".$response["id_pagaduria"]."' onClick=\"window.open('".generateBlobDownloadLinkWithSAS("pagadurias",$_REQUEST["id_pagaduria"]."/".$response["soporte_convenio"])."','ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0')\">Descargar</a>"
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
  
    
    echo json_encode($data);
?>