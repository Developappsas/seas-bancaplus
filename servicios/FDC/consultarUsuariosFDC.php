<?php
include ('../../functions.php');
include ('../cors.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Content-Type: application/json; charset=utf-8");
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Consultar Usuarios FDC':
            $data=array();	
            $data2=array();	
            $consultarEmpresasFDC="SELECT * FROM empresas_fdc";
            $queryEmpresasFDC=sqlsrv_query($link,$consultarEmpresasFDC);
            

            while ($resEmpresasFDC=sqlsrv_fetch_array($queryEmpresasFDC)){
                $data2[]=$resEmpresasFDC;
            }

            if ($params["id_empresa"]<>'0'){
           
                if ($params["id_empresa"]<>'ANTIFRAUDE'){
                    $consultarUsuariosInicio = "SELECT (SELECT COUNT(id) AS cantidad FROM simulaciones_fdc WHERE vigente='s' AND estado=2 AND id_usuario_asignacion=a.id_usuario) creditos_asignados,(SELECT COUNT(id) AS cantidad FROM simulaciones_fdc WHERE (id_subestado NOT IN ('28','53') OR id_subestado IS NULL) AND estado=4 
                    AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd') AND id_usuario_creacion=a.id_usuario) AS creditos_terminados,a.cantidad_creditos,a.id_usuario,a.login,a.nombre,a.apellido,CASE WHEN a.disponible='s' THEN 'DISPONIBLE' WHEN a.disponible='n' THEN 'NO DISPONIBLE' WHEN a.disponible='r' THEN 'NO DISPONIBLE' WHEN a.disponible='g' THEN 'EN GESTION' ELSE 'NO DISPONIBLE' END AS estado_usuario,a.disponible AS estado
                    FROM empresa_usuario_fdc eus
                    INNER JOIN usuarios a ON a.id_usuario=eus.id_usuario
                    WHERE  a.subtipo='ANALISTA_CREDITO' AND a.revision_garantias='0' AND eus.id_empresa=".$params["id_empresa"];
                }else{
                    $consultarUsuariosInicio = "SELECT (SELECT COUNT(id) AS cantidad FROM simulaciones_fdc WHERE vigente='s' AND estado=2 AND id_usuario_asignacion=a.id_usuario) creditos_asignados,(SELECT COUNT(id) AS cantidad FROM simulaciones_fdc WHERE (id_subestado NOT IN ('28','53') OR id_subestado IS NULL) AND estado=4 
                    AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd') AND id_usuario_creacion=a.id_usuario) AS creditos_terminados,a.cantidad_creditos,a.id_usuario,a.login,a.nombre,a.apellido,CASE WHEN a.disponible='s' THEN 'DISPONIBLE' WHEN a.disponible='n' THEN 'NO DISPONIBLE' WHEN a.disponible='r' THEN 'NO DISPONIBLE' WHEN a.disponible='g' THEN 'EN GESTION' ELSE 'NO DISPONIBLE' END AS estado_usuario,a.disponible AS estado
                   FROM usuarios a WHERE a.revision_garantias='1' and  a.subtipo in ('ANALISTA_CREDITO','ANALISTA_VEN_CARTERA')";
                }

                $consultarUsuariosInicio.=" and a.estado=1"; 
                $rs=sqlsrv_query($link,  $consultarUsuariosInicio);
                
                while ($fila = sqlsrv_fetch_array($rs)){
                    $selectDisponible="";
                    $selectDisponible="";
                    $btnNoDispTerminar="";
                    $btnNoDispReasignar="";
                    $selectUnidadNegocio="";  

                if ($params["id_empresa"]<>'ANTIFRAUDE') {

                    $selectUnidadNegocio="<select id='unidad_negocio_usuario_fdc'>";
                    $consultaEmpresasUsuario="SELECT * FROM empresa_usuario_fdc WHERE id_usuario='".$fila["id_usuario"]."'";
                    $queryEmpresasUsuario=sqlsrv_query($link,$consultaEmpresasUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    if (sqlsrv_num_rows($queryEmpresasUsuario)>0) {
                        foreach ($data2 as $item){
                            $consultarEmpresaUsuario=sqlsrv_query($link,"SELECT * FROM empresa_usuario_fdc WHERE id_usuario='".$fila["id_usuario"]."' and id_empresa='".$item["id_empresa_fdc"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                            $empresaSelected="";
                            
                            if (sqlsrv_num_rows($consultarEmpresaUsuario)>0) {
                                $empresaSelected="selected";
                            }

                            $selectUnidadNegocio.="<option $empresaSelected value='".$item["id_empresa_fdc"]."'>".$item["nombre"]."</option>";
                        }
                    }else{
                        $selectUnidadNegocio.="<option value='0' selected>SELECCIONE UNA OPCION</option>";
                        foreach ($data2 as $item){
                            $selectUnidadNegocio.="<option value='".$item["id_empresa_fdc"]."'>".$item["nombre"]."</option>";
                        }
                    }
                }else{
                    $selectUnidadNegocio="<select disabled id='unidad_negocio_usuario_fdc'>";
                    $selectUnidadNegocio.="<option value='0' >ANTIFRAUDE</option>";

                }

                $selectUnidadNegocio.='</select>';                

                if ($fila["creditos_asignados"]>0){
                    if ($fila["estado"]=="s" || $fila["estado"]=="g"){
                        $btnNoDispTerminar='<a id="btnNoDispTerminar" name="'.$fila["id_usuario"].'"><img src="../images/dot_azul.png" title="No Disponible Terminar Bandeja"></a>';
                    }
                    $btnNoDispReasignar='<a id="btnNoDispReasignar" name="'.$fila["id_usuario"].'"><img src="../images/dot_rojo.png" title="Ni Disponible Reasignar"></a>';
                }else{
                    $btnNoDispTerminar='';
                    $btnNoDispReasignar='';
                }

                if ($fila["estado"]=="n" || $fila["estado"]=="t"){

                    $selectDisponible='<select id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
                    <option value="s">DISPONIBLE</option>
                    <option value="n" selected>NO DISPONIBLE</option>
                    </select>';         
                }else{
                    if ($fila["creditos_asignados"]>0){

                        $selectDisponible='<select disabled id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
                        <option value="s" selected>DISPONIBLE</option>
                        <option value="n">NO DISPONIBLE</option>
                        </select>';
                    }else{

                        $selectDisponible='<select id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
                        <option value="s" selected>DISPONIBLE</option>
                        <option value="n">NO DISPONIBLE</option>
                        </select>';
                    }   

                }

                $data[]=array(trim("nombre")=>trim(($fila["nombre"]." ".$fila["apellido"])),
                    trim("estado")=>trim($fila["estado_usuario"]),
                    trim("estudios_realizados")=>trim("<a href='#' id='btnModalCreditosTerminadosAnalista' name='".$fila["id_usuario"]."' >".$fila["creditos_terminados"]."</a>"),
                    trim("estudios_asignado")=>trim("<a href='#' id='btnModalCreditosAnalista' name='".$fila["id_usuario"]."' >".$fila["creditos_asignados"]."</a>"),
                    trim("estudios_total")=>trim("<a href='#' id='btnModalCreditosTotalAnalista' name='".$fila["id_usuario"]."' >".($fila["creditos_asignados"]+$fila["creditos_terminados"])."</a>"),
                    trim("cantidad_minimo")=>trim('<input type="text" name="'.$fila["id_usuario"].'" id="cantidadMinimaUsuario" value="'.$fila["cantidad_creditos"].'">'),
                    trim("unidad_negocio")=>trim($selectUnidadNegocio),
                    trim("no_disp_terminar")=>trim($btnNoDispTerminar),
                    trim("no_disp_reasignar")=>trim($btnNoDispReasignar),
                    trim("selecc_estado")=>trim($selectDisponible),
                    trim("empresas")=>$consultarEmpresasFDC
                );
            }
        }
        $results = array(
            trim("sEcho") => trim("1"),
            trim("iTotalRecords") => trim(count($data)),
            trim("iTotalDisplayRecords") => trim(count($data)),
            trim("aaData") => $data
        );

        $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente','datos'=>$results, 'consultarUsuariosInicio' => $consultarUsuariosInicio, 'params' => $params["id_empresa"]);        

        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;  
    }
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");
?>