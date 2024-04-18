<?php
    session_start();

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_SESSION['S_IDUSUARIO'])){
        
        if(isset($_POST['id_edad_rango_seguro'])){

            $queryExiste = sqlsrv_query($link, "SELECT * FROM edad_rango_seguro WHERE id_edad_rango_seguro = ".$_POST['id_edad_rango_seguro'], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if(sqlsrv_num_rows($queryExiste) > 0){
                
                if(sqlsrv_query($link, "UPDATE edad_rango_seguro SET edad_rango_inicio = ".$_POST['edad_rango_inicio'].", edad_rango_fin = ".$_POST['edad_rango_fin'].", valor_por_millon = '".$_POST['valor_por_millon']."', valor_por_millon_parcial = '".$_POST['valor_por_millon_parcial']."', estado = '".$_POST['estado']."' WHERE id_edad_rango_seguro = ".$_POST['id_edad_rango_seguro'])){

                    $data = array('codigo' => 200, 'mensaje' => 'Actualizada Exitosamente');
                }else{
                    $data = array('codigo' => 500, 'mensaje' => 'Error al Editar el Registro, Vuelva a intentarlo');
                }                    
            }else{
                $data = array('codigo' => 300, 'mensaje' => 'Error al encontrar el Registro, Vuelva a Intentarlo');
            }   
        }else{
            $data = array('codigo' => 404, 'mensaje' => 'No Se Rebieron Datos');
        }
    }else{
        $data = array('codigo' => 404, 'mensaje' => 'Error, Vuelva a Iniciar Sesión');
    }
    echo json_encode($data);
?>