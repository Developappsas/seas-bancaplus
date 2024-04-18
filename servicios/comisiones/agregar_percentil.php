<?php
    session_start();

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['opcion'])){

        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        $dato = '';

        switch ($opcion) {
            case 'add':

                $queryExiste = sqlsrv_query($link, "SELECT * FROM tasas_comisiones_percentil WHERE id_tasa_comision = ".$_POST['id_tasa_comision']." AND id_tipo_contrato = ".$_POST['id_tipo_contrato']." AND rango_inicial = ".$_POST['rango_inicial']." AND rango_final = ".$_POST['rango_final']);

                if(sqlsrv_num_rows($queryExiste) == 0){
                    
                    sqlsrv_query($link, "UPDATE tasas_comisiones_percentil SET posicion = posicion+1 WHERE id_tasa_comision = ".$_POST['id_tasa_comision']." AND posicion >= ".$_POST['posicion']);

                    if(sqlsrv_query($link, "INSERT INTO tasas_comisiones_percentil (id_tasa_comision, posicion, rango_inicial, rango_final, valor, id_tipo_contrato, usuario_creacion, fecha_creacion) VALUES (".$_POST['id_tasa_comision'].", ".$_POST['posicion'].", ".$_POST['rango_inicial'].", ".$_POST['rango_final'].", ".$_POST['valor'].", '".$_POST['id_tipo_contrato']."', ".$_SESSION['S_IDUSUARIO'].", CURRENT_TIMESTAMP)")){

                        $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
                    }else{
                        $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
                    }
                }else{
                    $data = array('code' => 300, 'mensaje' => 'Ya Existe Un Percentil con esos parametros como: ');
                }

                break;
            
            case 'edit':

                $queryExiste = sqlsrv_query($link, "SELECT * FROM tasas_comisiones_percentil WHERE id_percentil = ".$_POST['id_percentil']);

                if(sqlsrv_num_rows($queryExiste) > 0){

                    sqlsrv_query($link, "UPDATE tasas_comisiones_percentil SET posicion = posicion+1 WHERE id_tasa_comision = ".$_POST['id_tasa_comision']." AND posicion >= ".$_POST['posicion']);
                    
                    if(sqlsrv_query($link, "UPDATE tasas_comisiones_percentil SET id_tasa_comision = ".$_POST['id_tasa_comision'].", posicion = ".$_POST['posicion'].", rango_inicial = ".$_POST['rango_inicial'].", rango_final = ".$_POST['rango_final'].", valor = ".$_POST['valor'].", id_tipo_contrato = '".$_POST['id_tipo_contrato']."', usuario_creacion = ".$_SESSION['S_IDUSUARIO'].", fecha_creacion = CURRENT_TIMESTAMP WHERE id_percentil = ".$_POST['id_percentil'])){
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
    
    echo json_encode($data);
?>