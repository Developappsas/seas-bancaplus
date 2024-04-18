<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
    session_start();

    include ('../../functions.php');
    include ('../cors.php');
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

                    $queryExiste = sqlsrv_query($link, "SELECT * FROM tasas_comisiones WHERE tasa = ".$_POST['tasa']." AND marca_unidad_negocio = ".$_POST['id_marca_unidad_negocio']." AND id_unidad_negocio = ".$_POST['id_unidad_negocio']." AND id_tipo = ".$_POST['id_tipo'], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    if(sqlsrv_num_rows($queryExiste) == 0){
                            
                        if(sqlsrv_query($link, "INSERT INTO tasas_comisiones (tasa, marca_unidad_negocio, id_unidad_negocio, id_tipo, kp_plus, fecha_inicio, fecha_fin, vigente, usuario_creacion, fecha_creacion) VALUES (".$_POST['tasa'].", ".$_POST['id_marca_unidad_negocio'].", ".$_POST['id_unidad_negocio'].", ".$_POST['id_tipo'].", ".$_POST['kp_plus'].", '".$_POST['fecha_inicio']."', '".$_POST['fecha_fin']."', ".$_POST['vigente'].", ".$_SESSION['S_IDUSUARIO'].", CURRENT_TIMESTAMP)")){

                            $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
                        }else{
                            $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
                        }
                    }else{
                        $data = array('code' => 300, 'mensaje' => 'Ya Existe La Tasa como: ');
                    }

                    break;
                
                case 'edit':

                    $queryExiste = sqlsrv_query($link, "SELECT * FROM tasas_comisiones WHERE id_tasa_comision = ".$_POST['id_tasa_comision'], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    if(sqlsrv_num_rows($queryExiste) > 0){
                        
                        if(sqlsrv_query($link, "UPDATE tasas_comisiones SET tasa = ".$_POST['tasa'].", marca_unidad_negocio = ".$_POST['id_marca_unidad_negocio'].", id_unidad_negocio = ".$_POST['id_unidad_negocio'].", id_tipo = ".$_POST['id_tipo'].", kp_plus = ".$_POST['kp_plus'].", fecha_inicio = '".$_POST['fecha_inicio']."', fecha_fin = '".$_POST['fecha_fin']."', vigente = '".$_POST['vigente']."', usuario_creacion = ".$_SESSION['S_IDUSUARIO'].", fecha_creacion = CURRENT_TIMESTAMP WHERE id_tasa_comision = ".$_POST['id_tasa_comision'])){

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