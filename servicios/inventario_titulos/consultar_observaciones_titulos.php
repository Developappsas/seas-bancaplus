<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

       $opcion = '';
        

        $dato = '';

        $queryConsulta = "SELECT b.descripcion as desc_estado,CONCAT(c.nombre,' ',c.apellido) AS usuario,a.* FROM inventario_creditos a LEFT JOIN tipificacion_inventario_creditos b ON a.estado=b.id_tipificacion_credito LEFT JOIN usuarios c ON c.id_usuario=a.id_usuario WHERE a.id_simulacion='".$_POST["id_simulacion"]."'";
        
            
        
 
        $conTitulos = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conTitulos) > 0) {
            while ($response = sqlsrv_fetch_array($conTitulos)) {
                
                

              

                $tasas[] = array(
                    "observacion" => $response["observacion"],
                    "estado" => $response["desc_estado"],
                    "fecha" => $response["fecha"],
                    "usuario" =>  $response["usuario"],
                    "id" => $response["id"]
                    
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar',"query"=>$queryConsulta);
        }
  
    
    echo json_encode($data);
?>