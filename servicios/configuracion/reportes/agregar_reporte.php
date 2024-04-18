<?php
    session_start();

    include ('../../../functions.php');
    include ('../../cors.php');
    $link = conectar_utf();

    if(isset($_SESSION['S_IDUSUARIO'])){
        
        if(isset($_POST['opcion'])){

            $opcion = '';
            
            if(isset($_POST['opcion'])){
                $opcion = $_POST['opcion'];
            }

            $dato = '';

            switch ($opcion) {
                case 'add':

                    $queryExiste = sqlsrv_query($link, "SELECT * FROM reportes WHERE [url] = '".$_POST['url']."' OR descripcion = '".$_POST['descripcion']."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    if(sqlsrv_num_rows($queryExiste) == 0){
                        if(sqlsrv_query($link, "INSERT INTO reportes (tipo_reporte, descripcion, url) VALUES ('".$_POST['tipo_reporte']."', '".$_POST['descripcion']."', '".$_POST['url']."');")){

                            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
                        }else{
                            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
                        }
                    }else{
                        $data = array('code' => 300, 'mensaje' => 'Ya Existe El reporte como: ' );
                    }

                    break;
                
                case 'edit':

                    $queryExiste = sqlsrv_query($link, "SELECT * FROM reportes WHERE id = ".$_POST['id_reporte']);

                    if(sqlsrv_num_rows($queryExiste) > 0){
                        
                        if(sqlsrv_query($link, "UPDATE reportes SET tipo_reporte = '".$_POST['tipo_reporte']."', [url] = '".$_POST['url']."', descripcion = '".$_POST['descripcion']."' WHERE id = ".$_POST['id_reporte'])){

                            $data = array('code' => 200, 'mensaje' => 'Actualizada Exitosamente');
                        }else{
                            $data = array('code' => 500, 'mensaje' => 'Error al Editar el Registro, Vuelva a intentarlo');
                        }                    
                    }else{
                        $data = array('code' => 300, 'mensaje' => 'Error al encontrar el Registro, Vuelva a Intentarlo');
                    }

                    break;

                default:
                    $queryConsulta .= "";
                    break;
            }       
        }else{
            $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'Error, Vuelva a Iniciar Sesión');
    }
    echo json_encode($data);
?>