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
        case 'Determinar Usuario Asignar':
            sqlsrv_query($link, "START TRANSACTION");
            $consultarEmpresaUNegocio="SELECT * FROM empresa_unegocio_fdc WHERE id_unidad_negocio=(SELECT id_unidad_negocio FROM simulaciones WHERE id_simulacion=".$params["id_simulacion"].")";
            $queryEmpresaUNegocio=sqlsrv_query($link, $consultarEmpresaUNegocio);
            $resEmpresaUNegocio=sqlsrv_fetch_array($queryEmpresaUNegocio);

            $consultaAnalistaReciente="SELECT a.id_simulacion,case when b.estado=2 then b.id_usuario_asignacion when b.estado=4 then b.id_usuario_creacion end as id_usuario_fdc
            FROM simulaciones a
            LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion 
            WHERE a.cedula=(SELECT cedula FROM simulaciones WHERE id_simulacion='".$params["id_simulacion"]."')
            AND a.id_simulacion<'".$params["id_simulacion"]."' 
            AND b.vigente='s' AND b.estado IN (4,2) AND DATEDIFF(CURRENT_DATE,DATE_FORMAT(b.fecha_creacion,'%Y-%m-%d'))<=30  
            ORDER BY a.id_simulacion DESC LIMIT 1";
            $queryAnalistaReciente=sqlsrv_query($link, $consultaAnalistaReciente);
            $resAnalistaReciente=sqlsrv_fetch_array($queryAnalistaReciente);
            if (sqlsrv_num_rows($queryAnalistaReciente)>0) {
                //ha sido estudiado anteriormente
                $queryEstadoAanalistaReciente=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resAnalistaReciente["id_usuario_fdc"]."' and disponible in ('s','g')");
                if (sqlsrv_num_rows($queryEstadoAanalistaReciente)>0) {
                    $consultarCantCreditosUsuarioReciente="SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
                    FROM
                    (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos as num_creditos
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
                     WHERE a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.id_usuario='".$resAnalistaReciente["id_usuario_fdc"]."' and a.estado=1
                    GROUP BY a.id_usuario) a,
                    (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos as num_creditos
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd')) b ON a.id_usuario=b.id_usuario_creacion
                     WHERE a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.id_usuario='".$resAnalistaReciente["id_usuario_fdc"]."' and a.estado=1
                    GROUP BY a.id_usuario) b WHERE a.id_usuario=b.id_usuario and (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos";
              
              
                    $queryCantCreditosUsuarioReciente=sqlsrv_query($link, $consultarCantCreditosUsuarioReciente);
                    if (sqlsrv_num_rows($queryCantCreditosUsuarioReciente)>0) {

                      $consultarEmpresaAnalista="SELECT * FROM empresa_usuario_fdc WHERE id_usuario='".$resAnalistaReciente["id_usuario_fdc"]."'";


                      $queryEmpresaAnalista=sqlsrv_query($link, $consultarEmpresaAnalista);
                      $resEmpresaAnalista=sqlsrv_fetch_array($queryEmpresaAnalista);
                 
              
                      if (($resEmpresaAnalista["id_empresa"]==$resEmpresaUNegocio["id_empresa"])) {
                        $idUsuario=$resAnalistaReciente["id_usuario_fdc"];
                        
                        $val=0;
                      }else{
                        $val=1;
                      }  
                    }else{
                      $val=1;
                    }        
                  }else{
                    $val=1;
                  }
            }else{
                //nunca ha sido estudiado
                $val=1;
            }

            if ($val==1) {
                  $consultarUsuarioAsignar="SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
                  FROM
                  (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos as num_creditos
                  FROM usuarios a 
                  LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
                   WHERE a.id_usuario IN (SELECT id_usuario FROM empresa_usuario_fdc WHERE id_empresa='".$resEmpresaUNegocio["id_empresa"]."') AND
                  a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1
                  GROUP BY a.id_usuario) a,
                  (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos as num_creditos
                  FROM usuarios a 
                  LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd')) b ON a.id_usuario=b.id_usuario_creacion
                   WHERE a.id_usuario IN (SELECT id_usuario FROM empresa_usuario_fdc WHERE id_empresa='".$resEmpresaUNegocio["id_empresa"]."') AND
                  a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1
                  GROUP BY a.id_usuario) b WHERE a.id_usuario=b.id_usuario and (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos ORDER BY cantidad_creditos,cantidad_asignado ASC LIMIT 1";
          
                $queryUsuarioAsignar=sqlsrv_query($link, $consultarUsuarioAsignar);
                if (sqlsrv_num_rows($queryUsuarioAsignar)>0) {
                  $resUsuarioAsignar=sqlsrv_fetch_array($queryUsuarioAsignar);
                  $idUsuario=$resUsuarioAsignar["id_usuario"];

              


                }else{
                  $idUsuario=0;
                }      
             }


             if ($idUsuario<>0)
             {
              $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$params["id_simulacion"]."'";
              sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
              $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$params["id_simulacion"]."',197,'".$idUsuario."',CURRENT_TIMESTAMP,'s',2,14)";
              sqlsrv_query($link, $asignarAnalista);
              $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuario."',id_analista_riesgo_crediticio='".$idUsuario."',id_analista_gestion_comercial='".$idUsuario."' where id_simulacion='".$params["id_simulacion"]."'";
              sqlsrv_query($link, $actualizarSimulacion);
              $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuario."'");
             }
             sqlsrv_query($link, "COMMIT");
             $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente','datos'=>$idUsuario);
        
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