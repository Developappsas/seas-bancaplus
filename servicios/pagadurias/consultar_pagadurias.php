<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

       $opcion = '';
        

        $dato = '';
        $queryConsulta = "SELECT a.*,CONCAT(b.departamento,'-',b.municipio) AS ciudad_pagaduria, b.id as id_municipio FROM pagadurias a LEFT JOIN ciudades b ON a.ciudad=b.id";

        if ($_POST["id_pagaduria"]<>0)
        {
            $queryConsulta .= " WHERE  id_pagaduria = ".$_POST["id_pagaduria"];
        }
 
        $conPagadurias = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conPagadurias) > 0) {
            while ($response = sqlsrv_fetch_array($conPagadurias, SQLSRV_FETCH_ASSOC)) {

                if ($response["visado"]==0)
                {
                    $visado="NO";
                }else if ($response["visado"]==1){
                    $visado="SI";
                }


                if ($response["incorporacion"]==0)
                {
                    $incorporacion="NO REQUIERE";
                }else if ($response["incorporacion"]==1){
                    $incorporacion="VIRTUAL";
                }else if ($response["incorporacion"]==2){
                    $incorporacion="OFICIAL";
                }else if ($response["incorporacion"]==3){
                    $incorporacion="MIXTA";
                }

                if ($response["estado"]==1)
                {
                    $textoBoton="Deshabilitar";
                    $colorBoton="danger";
                }else{
                    $textoBoton="Habilitar";
                    $colorBoton="success";
                }

                $tasas[] = array(
                    "nombre" => $response["nombre"],
                    "nombre_completo" => $response["nombre_completo"],
                    "identificacion" => $response["identificacion"],
                    "visado" => $visado,
                    "incorporacion" => $incorporacion,
                    "nombre_contacto" => $response["nombre_contacto"],
                    "telefono_contacto" => $response["telefono_contacto"],
                    "correo_contacto" => $response["correo_contacto"],
                    "id_pagaduria" => $response["id_pagaduria"],
                    "ciudad" => $response["ciudad_pagaduria"],
                    "id_municipio" => $response["id_municipio"],
                    "codigo_convenio" => $response["codigo_convenio"],
                    "direccion" => $response["direccion"],
                    "opciones" =>   "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddPagaduria' onclick='openModalPagadurias(\"EDITAR\", ".$response["id_pagaduria"].")' name='".$response["id_pagaduria"]."' >Editar</a>
                                    <a class='btn btn-sm btn-danger' style='margin-left: 3px;' onclick='deletePagaduria(".$response["id_pagaduria"].")' name='".$response["id_pagaduria"]."' >Eliminar</a>
                                    <a class='btn btn-sm btn-".$colorBoton."' style='margin-left: 3px;' onclick='habilitarPagaduria(".$response["id_pagaduria"].")' name='".$response["id_pagaduria"]."' >".$textoBoton."</a>
                                     <a class='btn btn-sm btn-success' style='margin-left: 3px;' data-bs-toggle='modal' data-bs-target='#modalConvenios' onclick='openModalConvenios(".$response["id_pagaduria"].")' name='".$response["id_pagaduria"]."' >Convenios</a>"
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
  
    
    echo json_encode($data);
?>