<?php
    session_start();

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_SESSION['S_IDUSUARIO'])){
        
        if(isset($_POST['edad_rango_inicio']) && isset($_POST['edad_rango_fin']) && isset($_POST['valor_por_millon']) && isset($_POST['valor_por_millon_parcial']) && isset($_POST['estado'])){

            $queryExiste = sqlsrv_query($link, "SELECT id_edad_rango_seguro, edad_rango_inicio, edad_rango_fin FROM edad_rango_seguro WHERE ('".$_POST['edad_rango_inicio']."' BETWEEN edad_rango_inicio AND edad_rango_fin) OR ('".$_POST['edad_rango_fin']."' BETWEEN edad_rango_inicio AND edad_rango_fin)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if(sqlsrv_num_rows($queryExiste) == 0){
                
                if(sqlsrv_query($link, "INSERT INTO edad_rango_seguro (edad_rango_inicio, edad_rango_fin, valor_por_millon, valor_por_millon_parcial, estado, usuario_creacion, fecha_creacion) VALUES ('".$_POST['edad_rango_inicio']."', '".$_POST['edad_rango_fin']."', '".$_POST['valor_por_millon']."', '".$_POST['valor_por_millon_parcial']."', '".$_POST['estado']."', '".$_SESSION['S_IDUSUARIO']."', GETDATE())")){

                    $data = array('codigo' => 200, 'mensaje' => 'Registrada Exitosamente');
                }else{
                    $error = sqlsrv_errors()
                    $data = array('codigo' => 500, 'mensaje' => 'Error al Guardar el Registro, Vuelva a intentarlo', 'error' => $error['message']);
                }                    
            }else{
                $mensajeRangos = "Error, Rango contenido en:\n";
                
                while ($rango = sqlsrv_fetch_array($queryExiste, SQLSRV_FETCH_ASSOC)) {
                    $mensajeRangos .= "\n * ".$rango["edad_rango_inicio"]." Años a ".$rango["edad_rango_fin"]." Años.";
                }
                
                $data = array('codigo' => 300, 'mensaje' => $mensajeRangos, "data" => $rangos);
            }   
        }else{
            $data = array('codigo' => 404, 'mensaje' => 'No Se Rebieron Datos');
        }
    }else{
        $data = array('codigo' => 404, 'mensaje' => 'Error, Vuelva a Iniciar Sesión');
    }
    echo json_encode($data);
?>