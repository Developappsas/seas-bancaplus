<?php
include_once('../functions.php');
$link = conectar_utf();

//$rutaAdo = 'https://adocolombia-QA.ado-tech.com/KreditQA/api/KreditQA/';
$rutaAdo = 'https://adocolumbia.ado-tech.com/Kredit/api/Kredit/'; //produccion

if (isset($_GET['id_simulacion'])) {
    $key = 'db92efc69991';
    $id_simulacion = $_GET['id_simulacion'];

    $consultar = sqlsrv_query($link, "SELECT top 1 id_transaccion FROM historial_tokens_verificacion_id WHERE id_simulacion = " . $id_simulacion . " AND fecha_visto IS NOT NULL ORDER BY id DESC", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
    if (sqlsrv_num_rows($consultar) > 0) {
        $verificacionDatos = sqlsrv_fetch_array($consultar);

        $opciones = array(
            'http' => array(
                'method' => 'GET',
                'header' => "apiKey: " . $key . "\r\n"
            )
        );

        $contexto = stream_context_create($opciones);

        $json_Input = file_get_contents($rutaAdo . 'ValidationFS/'.$verificacionDatos["id_transaccion"].'?&returnImages=true', false, $contexto);

        if ($json_Input) {

            $datos = json_decode($json_Input);

            if (is_array($datos->Extras)) {
                $IdState = $datos->Extras[0]->IdState;
                $StateName = $datos->Extras[0]->StateName;
            } else {
                $IdState = $datos->Extras->IdState;
                $StateName = $datos->Extras->StateName;
            }

            $imagenRostro = '';
            $imagenDoc = '';
            $imagenDoc1 = '';
            $imagenDoc2 = '';

            foreach ($datos->Images as $imagen) {
                if($imagen->ImageTypeId == 1){
                    $imagenDoc1 = $imagen->Image;
                }

                if($imagen->ImageTypeId == 2){
                    $imagenDoc2 = $imagen->Image;
                }

                if($imagen->ImageTypeId == 3){
                    $imagenRostro = $imagen->Image;
                }

                if($imagen->ImageTypeId == 12){
                    $imagenDoc = $imagen->Image;
                }
            }

            if ($imagenDoc1 != '' || $imagenDoc2 != '') { //1,2,3  ?>
                <table>
                    <tr>
                        <td colspan="2">
                            <h1>Imagenes de Validación Del Cliente</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img height="400" width="300" src="data:image/jpg;base64,<?= $imagenRostro ?>" alt="Imagen Cliente" />
                            <h4>Rostro Cliente</h4>
                        </td>
                        <td>
                            <img height="400" width="300" src="data:image/jpg;base64,<?= $imagenDoc ?>" alt="Imagen Cliente 2" />
                            <h4>Foto Documento</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img height="400" width="620" src="data:image/jpg;base64,<?= $imagenDoc1 ?>" alt="Imagen Cliente" />
                            <h4>Foto 1 Documento</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img height="400" width="620" src="data:image/jpg;base64,<?= $imagenDoc2 ?>" alt="Imagen Cliente" />
                            <h4>Foto 2 Documento</h4>
                        </td>
                    </tr>
                </table>
                <?php
            } else {
                $response = array("code" => 400, "mensaje" => "Error en SEAS, Respuesta de Servicio ADO sin imagenes.");
            }
        } else {
            $response = array("code" => 404, "mensaje" => "Error en SEAS, No existe respuesta del Servidor de ADO para esta solicitud. (".$verificacionDatos["id_transaccion"].")");
        }
    } else {
        $response = array("code" => 400, "mensaje" => "Error en SEAS, No se encontro el numero de documento en el sistema.");
    }
} else {
    $response = array("code" => 404, "mensaje" => "Error en SEAS, No existen Datos");
}

if ($response) {
    echo json_encode($response);
}
?>