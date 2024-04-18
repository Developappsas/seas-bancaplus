<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
ob_start();
require ('../plugins/fpdf183/fpdf.php');

include_once ('../functions.php');
include_once ('../function_blob_storage.php');
$link = conectar_utf();

//$rutaAdo = 'https://adocolombia-QA.ado-tech.com/KreditQA/api/KreditQA/';
$rutaAdo = 'https://adocolumbia.ado-tech.com/Kredit/api/Kredit/'; //produccion

if(isset($_POST['id_simulacion'])){
    $key = 'db92efc69991';
    $id_simulacion = $_POST['id_simulacion'];

    $consultar = sqlsrv_query($link,"SELECT cedula FROM simulaciones WHERE id_simulacion = ".$id_simulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
    if(sqlsrv_num_rows($consultar) > 0){
        $datosCedula = sqlsrv_fetch_array($consultar);
        $cedula = $datosCedula["cedula"];

        $opciones = array(
            'http'=>array(
                'method' => 'GET',
                'header'=> "apiKey: ".$key."\r\n"
            )
        );

        $contexto = stream_context_create($opciones);

        $json_Input = file_get_contents($rutaAdo.'FindByNumberIdSuccess?identification='.$cedula.'&docType=1&returnImages=true', false, $contexto);

        if($json_Input){

            $datos = json_decode($json_Input);

            if(is_array($datos->Extras)){
                $IdState = $datos->Extras[0]->IdState;
                $StateName = $datos->Extras[0]->StateName;
            }else{
                $IdState = $datos->Extras->IdState;
                $StateName = $datos->Extras->StateName;
            }

            $imagenCara1 = '';
            $imagenCara2 = '';

            if(isset($datos->Images)){
                foreach ($datos->Images as $key => $value) {
                    if($value->ImageTypeId == 1){
                        $imagenCara1 = $value->Image;
                    }

                    if($value->ImageTypeId == 2){
                        $imagenCara2 = $value->Image;
                    }
                }
            }

            if($imagenCara1 != '' && $imagenCara2 != ''){

                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetAuthor('Sistemas Kredit', true);
                $pdf->SetCreator('Sistemas Kredit', true);

                $pdf->SetFont('Arial', '', 6);

                $pdf->Image('data://text/plain;base64,' . $imagenCara1, 45, 40, 120, 85, 'jpg');
                $pdf->Image('data://text/plain;base64,' . $imagenCara2, 45, 164, 120, 85, 'jpg');
                ob_end_clean();
                $pdf->Output('F', "ado/".$id_simulacion.'-'.$cedula.'.pdf', true);
                    
                $respuesta=json_decode($response_WS);
                $nombreArc=md5(rand(1,10000)+$id_simulacion).".pdf";
                
                $fechaa =new DateTime();
                $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
                                
                $metadata1 = array(
                    'id_simulacion' => $id_simulacion,
                    'descripcion' => ($nombreArc),
                    'usuario_creacion' => "system",
                    'fecha_creacion' => $fechaFormateada
                );

                upload_file2('ado/'.$id_simulacion.'-'.$cedula.'.pdf', "simulaciones", $id_simulacion."/adjuntos/".$nombreArc, $metadata1);

                unlink('ado/'.$id_simulacion.'-'.$cedula.'.pdf');

                sqlsrv_query($link,"insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES (".$id_simulacion.", 32, 'CEDULA', '".$nombreArc."', '".$nombreArc."', '0', 'system', NOW())");

                $response = array("code" => 200, "mensaje" => "PDF de Cedula Generado con la verficación de ADO y Guardado en adjuntos del credito");
            }else{
                $response = array("code" => 400, "mensaje" => "Respuesta de ADO sin imagenes.");
            }
        }else{
            $response = array("code" => 404, "mensaje" => "No existe respuesta del Servidor ADO.");
        }
    }else{
        $response = array("code" => 400, "mensaje" => "No se encontro el numero de documento en SEAS.");
    }
}else{
    $response = array("code" => 404, "mensaje" => "No existen Datos");
}
echo json_encode($response);
?>
  