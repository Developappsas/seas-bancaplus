<?php

include("../../functions.php");
include("../cors.php");
$link = conectar_utf();

if (isset($_POST["opcion"])) {

    $opcion = "";

    if (isset($_POST["opcion"])) {
        $opcion = $_POST["opcion"];
    }

    $dato = "";

    switch ($opcion) {
        case "CREAR":

            $query = ("SELECT * FROM pagadurias WHERE nombre = '" . $_POST['nombre'] . "' AND identificacion = " . $_POST['identificacion']);
            $queryExiste = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryExiste) == 0) {

                $query = ("INSERT INTO pagadurias (nombre,nombre_completo, estado, visado, incorporacion, identificacion, telefono_contacto, nombre_contacto, correo_contacto, direccion, fecha_creacion,usuario_creacion,ciudad,codigo_convenio, sector, plazo) VALUES ('" . $_POST['nombre'] . "', '" . $_POST['nombre_completo'] . "', 1, '" . $_POST['visado'] . "', '" . $_POST['incorporacion'] . "', '" . $_POST['identificacion'] . "', '" . $_POST['telefono_contacto'] . "', '" . $_POST['nombre_contacto'] . "', '" . $_POST['correo_contacto'] . "', '" . $_POST['direccion'] . "', CURRENT_TIMESTAMP, " . $_SESSION['S_IDUSUARIO'] . ", '" . $_POST['ciudad'] . "', '" . $_POST['codigo_convenio'] . "', '".$_POST['sector']."', '".$_POST['plazo']."')");
                $ejecutar = sqlsrv_query($link, $query);                
                if ($query) {
                    $data = array('code' => 200, 'mensaje' => 'Guardado Exitosamente');
                } else {
                    $data = array('code' => 500, 'mensaje' => 'Error al agregar el Registro, Vuelva a intentarlo');
                }
            } else {
                $data = array('code' => 300, 'mensaje' => 'Ya Existe La Tasa como: ');
            }

            break;

        case "EDITAR":

            $queryExiste = sqlsrv_query($link, "SELECT * FROM pagadurias WHERE id_pagaduria = " . $_POST['id_pagaduria'], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($queryExiste) > 0) {
                $resExiste = sqlsrv_fetch_array($queryExiste);
                $consultarCreditosPagaduria = sqlsrv_query($link, "SELECT * FROM simulaciones WHERE pagaduria='" . $resExiste["nombre"] . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                if (sqlsrv_num_rows($consultarCreditosPagaduria) == 0) {
                    sqlsrv_query($link, "UPDATE pagadurias SET nombre = '" . $_POST['nombre'] . "' WHERE id_pagaduria = " . $_POST['id_pagaduria']);
                }
                if (sqlsrv_query($link, "UPDATE pagadurias SET identificacion='" . $_POST['identificacion'] . "', visado = '" . $_POST['visado'] . "', incorporacion = '" . $_POST['incorporacion'] . "', telefono_contacto = '" . $_POST['telefono_contacto'] . "', nombre_contacto = '" . $_POST['nombre_contacto'] . "', correo_contacto = '" . $_POST['correo_contacto'] . "', direccion = '" . $_POST['direccion'] . "', ciudad = '" . $_POST['ciudad'] . "', codigo_convenio = '" . $_POST['codigo_convenio'] .  "', plazo = '".$_POST['plazo']."', nombre_completo = '".$_POST['nombre_completo']."' WHERE id_pagaduria = " . $_POST['id_pagaduria'])) {

                    $data = array('code' => 200, 'mensaje' => 'Actualizada Exitosamente');
                } else {
                    $data = array('code' => 500, 'mensaje' => 'Error al Editar el Registro, Vuelva a intentarlo');
                }
            } else {
                $data = array('code' => 300, 'mensaje' => 'Error al encontrar el Registro, Vuelva a Intentarlo');
            }

            break;

        default:
            $queryConsulta .= "";
            break;
    }
} else {
    $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
}

echo json_encode($data);
