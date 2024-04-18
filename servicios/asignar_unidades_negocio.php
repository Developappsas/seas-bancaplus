<?php    
    include_once ('../functions.php');
    //include ('./cors.php');
    //error_reporting(E_ALL);
    //ini_set('display_errors', '1'); 
  
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    $tipo_usuario = $params->tipo_usuario;
    $id_usuario = $params->id_usuario;

    $query_usuarios = ("SELECT * FROM usuarios WHERE estado = 1 and id_usuario = '$id_usuario'");
    $ejecutar = (sqlsrv_query($link,$query_usuarios, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)));
    if ($ejecutar && sqlsrv_num_rows($ejecutar) > 0 ) {
        while ($response = sqlsrv_fetch_array($ejecutar, SQLSRV_FETCH_ASSOC) ) {
        
            $query_insert = ("INSERT INTO usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) VALUES ('".$response['id_usuario']."', 18, 'JMILLAN', '".date("Y-m-d h:i:s")."')");

            //$ejecutar_insert = mysql_query($query_insert, $link);            
            $data = array(
                'estado' => 'Bien',
                'reultado' => $ejecutar,
                'query' => $query_usuarios,
                'Asignacion Unidad' => $query_insert
            );
        } 
    }else{        
        $data = array(  
            'estado' => 'Mal',
            'query' => $query_usuarios
        );
    }

    echo json_encode($data);
?>