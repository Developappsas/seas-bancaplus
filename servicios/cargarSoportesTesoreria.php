<?php 
include ('../../functions.php'); 
include ('../../function_blob_storage.php'); 
?>
<?php
 $link = conectar_utf();
    $tipo_adjunto=$_POST["tipo_adjunto"];
    if ($tipo_adjunto=="soporte_pago")
    {
        $id_adjunto="10";
    }else if ($tipo_adjunto=="desembolso_cliente"){
        $id_adjunto="11";
    }
	//$_FILES['soportes1']=$_FILES['soportes1'];	

    $no_files = count($_FILES['soportes1']['name']);
    $nombre_archivo="";
    //for ($i = 0; $i < $no_files; $i++) 
    //{
            
        //$explode2=explode(".",$_FILES['soportes1']['name']);
        
        if (strcmp($_FILES['soportes1']['name'], ""))
        {
            $explode2=explode(".",$_FILES['soportes1']['name']);
            $fechaa =new DateTime();
            $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
            
            if ($tipo_adjunto=="desembolso_cliente"){
                $explode3=explode("-",$explode2[0]);
                $creditos=$explode3[0];
            }else{
                $explode3=explode("-",$explode2[0]);
                $creditos=$explode3[2];
            }

            $explode=explode(",",$creditos);
            $noCreditos = count($explode);
            $nombre="";
            $extension=explode("/",$_FILES['soportes1']['type']);
            
            for ($i = 0; $i < $noCreditos; $i++) 
            {

                $nombre_archivo=trim($explode[$i]);
                
                $uniqueID = uniqid();
                $nombreArc=md5(rand()+$nombre_archivo).".".$extension[1];    


                $consultaInfoCredito = sqlsrv_query($link, "SELECT * FROM simulaciones WHERE nro_libranza='".$nombre_archivo."'");

                $fila1 = sqlsrv_fetch_array($consultaInfoCredito, SQLSRV_FETCH_ASSOC);

                
                $metadata1 = array(
                    'id_simulacion' => $fila1["id_simulacion"],
                    'descripcion' => ($nombreArc),
                    'usuario_creacion' => $_SESSION["S_LOGIN"],
                    'fecha_creacion' => $fechaFormateada
                );

                $consulta="INSERT into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) 
                VALUES ('".$fila1["id_simulacion"]."', '".$id_adjunto."', '".($fila1["nombre"]." ".$fila1["cedula"]." ".$fila1["nro_libranza"])."', '".$nombreArc."', '".$nombreArc."', '0', '".$_SESSION["S_LOGIN"]."', GETDATE())";
                $nombre.=$consulta."---";
                sqlsrv_query( $link,"INSERT into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$fila1["id_simulacion"]."', '".$id_adjunto."', '".($fila1["nombre"]." ".$fila1["cedula"]." ".$fila1["nro_libranza"])."', '".$nombreArc."', '".$nombreArc."', '0', '".$_SESSION["S_LOGIN"]."', GETDATE())");
                
                //upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"]."/adjuntos/".$uniqueID."_".$_REQUEST["id_simulacion"]."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
                upload_file($_FILES['soportes1'], "simulaciones",$fila1["id_simulacion"]."/adjuntos/".$nombreArc, $metadata1);
            
            }   
            
        }
        
    //}
    

    echo "1";


?>