<?php

    include_once ('functions.php');
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    $link = conectar_utf();
    
    $token=$_GET["token"];
    $url = "";
    $consultarInformacionToken="SELECT a.*, s.solicitar_firma FROM formulario_digital a join simulaciones s on s.id_simulacion = a.id_simulacion WHERE a.token = '".$token."' and a.estado_token = 0";
    

    $queryInformacionToken = sqlsrv_query($link, $consultarInformacionToken, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

    if (sqlsrv_num_rows($queryInformacionToken) > 0) {

        $resInformacionToken = sqlsrv_fetch_array($queryInformacionToken);
        $url = $resInformacionToken["url_formulario"];

        $actualizarToken = ("UPDATE formulario_digital SET fecha_leido = GETDATE(), estado_token = 1, intentos = intentos + 1,  en_progreso = 1 WHERE id = '".$resInformacionToken["id"]."'");
        $ejecutar = sqlsrv_query($link, $actualizarToken);
        
        if ($ejecutar){ 
            sqlsrv_query($link, "UPDATE simulaciones SET solicitar_firma = 3 WHERE id_simulacion = '".$resInformacionToken["id_simulacion"]."'");
            ?>
            <script type="text/javascript">
                window.location.href= '<?php echo $url; ?>' ;
            </script>
            <?php
                //echo 'Bien: '.$url;            
        }else{
            echo "Error al redireccionar";
        }
    }else{
        echo "Esta solicitud de credito ya ha sido utilizado o fue deshabilitado. Comuniquese con su asesor. ";
    }
?>