<?php
    
    include ('../../functions.php');
    $link = conectar_utf();

    if(isset($_POST['id_simulacion']) && isset($_POST['email'])){

        $querySol = "SELECT cedula FROM solicitud WHERE id_simulacion = '".$_POST['id_simulacion']."'";
        $conSol = sqlsrv_query($link, $querySol, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conSol) > 0) {

            $datosSol = sqlsrv_fetch_array($conSol);
            
            if(sqlsrv_query($link, "UPDATE solicitud SET email = '".$_POST['email']."' WHERE id_simulacion = '".$_POST['id_simulacion']."'")){
                sqlsrv_query($link, "UPDATE empleados SET mail = '".$_POST['email']."' WHERE id_simulacion = '".$datosSol["cedula"]."'");
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