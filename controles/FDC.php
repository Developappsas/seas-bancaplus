<?php 
require_once("../functions.php");

$link = conectar_utf();

function creditoParaAsignar($idUsuario) {
    global $link;
    $consultarUsuario=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=4 and descripcion='".$idUsuario."'");
    if (sqlsrv_num_rows($consultarUsuario)>0){
        $consultarCreditoAsignar="SELECT TOP 1 a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE a.id_unidad_negocio IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=3) AND b.estado IN (1,5) AND b.vigente='s' ORDER BY b.fecha_creacion ASC ";
    }else{
        $consultarCreditoAsignar="SELECT TOP 1 a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE a.id_unidad_negocio NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=3) AND b.estado IN (1,5) AND b.vigente='s' ORDER BY b.fecha_creacion ASC ";
    }

    $queryCreditoAsignar=sqlsrv_query($link, $consultarCreditoAsignar, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    if (sqlsrv_num_rows($queryCreditoAsignar)>0)     {
        $resCreditoAsignar=sqlsrv_fetch_array($queryCreditoAsignar);
        $idSimulacion=$resCreditoAsignar["id_simulacion"];
    }else{
        $idSimulacion=0;
    }
    return $idSimulacion;
}


function usuarioParaAsignar($idSimulacion) {
    global $link;
    $valUnidadNegocioCredito=0;
    $valUnidadNegocioAnalista=0;
    sqlsrv_query($link, "BEGIN TRANSACTION");
    $consultarUnidadNegocio="SELECT id FROM definicion_tipos WHERE id_tipo=3 AND descripcion=(SELECT id_unidad_negocio FROM simulaciones WHERE id_simulacion=".$idSimulacion.")";
    $queryUnidadNegocio=sqlsrv_query($link, $consultarUnidadNegocio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
    if (sqlsrv_num_rows($queryUnidadNegocio)>0) {
      $valUnidadNegocioCredito=1;
    }else{
      $valUnidadNegocioCredito=2;
    }

    $consultaAnalistaReciente="SELECT TOP 1 a.id_simulacion,case when b.estado=2 then b.id_usuario_asignacion when b.estado=4 then b.id_usuario_creacion end as id_usuario_fdc
    FROM simulaciones a
    LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion 
    WHERE a.cedula=(SELECT cedula FROM simulaciones WHERE id_simulacion='".$idSimulacion."')
    AND a.id_simulacion<'".$idSimulacion."' 
    AND b.vigente='s' AND b.estado IN (4,2) AND DATEDIFF(day,CURRENT_TIMESTAMP, FORMAT(b.fecha_creacion,'Y-m-d'))<=30  
    ORDER BY a.id_simulacion DESC ";

    $queryAnalistaReciente=sqlsrv_query($link, $consultaAnalistaReciente, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    $resAnalistaReciente=sqlsrv_fetch_array($queryAnalistaReciente);

    if (sqlsrv_num_rows($queryAnalistaReciente)>0) {
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
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = CURRENT_TIMESTAMP) b ON a.id_usuario=b.id_usuario_creacion
         WHERE a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.id_usuario='".$resAnalistaReciente["id_usuario_fdc"]."' and a.estado=1
        GROUP BY a.id_usuario) b WHERE a.id_usuario=b.id_usuario and (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos";
  
  
        $queryCantCreditosUsuarioReciente=sqlsrv_query($link, $consultarCantCreditosUsuarioReciente);
        if (sqlsrv_num_rows($queryCantCreditosUsuarioReciente)>0) {
          $consultarUnidadNegocioAnalista="SELECT id FROM definicion_tipos WHERE id_tipo=4 AND descripcion='".$resAnalistaReciente["id_usuario_fdc"]."'";
          $queryUnidadAnalista=sqlsrv_query($link, $consultarUnidadNegocioAnalista);
          
          if (sqlsrv_num_rows($queryUnidadAnalista)>0) {
            $valUnidadNegocioAnalista=1;
          }else{
            $valUnidadNegocioAnalista=2;
          }
  
          if (($valUnidadNegocioAnalista==1 && $valUnidadNegocioCredito==1) || ($valUnidadNegocioAnalista==2 && $valUnidadNegocioCredito==2)) {
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
      $val=1;
    }
    
    if ($val==1) {
      if ($valUnidadNegocioCredito==1) {    
        $consultarUsuarioAsignar="SELECT top 1 a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
        FROM
            (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos as num_creditos
              FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
              a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1 GROUP BY a.id_usuario, a.nombre,a.apellido, a.cantidad_creditos) a,

              (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos as num_creditos
              FROM usuarios a 
              LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = format(CURRENT_TIMESTAMP,'Y-m-d')) b ON a.id_usuario=b.id_usuario_creacion
              WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
              a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1
              GROUP BY a.id_usuario, a.nombre,a.apellido, a.cantidad_creditos) b 
        WHERE a.id_usuario=b.id_usuario and (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos";
      }else if ($valUnidadNegocioCredito==2){

        $consultarUsuarioAsignar="SELECT TOP 1 a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
        FROM
            (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos as num_creditos
            FROM usuarios a 
            LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
            WHERE a.id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
            a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1
            GROUP BY a.id_usuario,a.nombre,a.apellido,a.cantidad_creditos ) a,


        (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos as num_creditos
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = format(CURRENT_TIMESTAMP,'Y-m-d')) b ON a.id_usuario=b.id_usuario_creacion
         WHERE a.id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
        a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') and a.estado=1
        GROUP BY a.id_usuario,a.nombre,a.apellido,a.cantidad_creditos) b 
        
        WHERE a.id_usuario=b.id_usuario and (a.cantidad_asignado+b.cantidad_terminado) < a.num_creditos";
      }
 
      $consultarUsuarioAsignar.=" ORDER BY cantidad_creditos,cantidad_asignado ASC ";

    
   

      $queryUsuarioAsignar=sqlsrv_query($link, $consultarUsuarioAsignar, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
      if (sqlsrv_num_rows($queryUsuarioAsignar)>0) {
        $resUsuarioAsignar=sqlsrv_fetch_array($queryUsuarioAsignar);
        $idUsuario=$resUsuarioAsignar["id_usuario"];
      }else{
        $idUsuario=0;
      }      
   }
   sqlsrv_query($link, "COMMIT");
   return $idUsuario;
}

function asignarUsuarioFirmaGarantias($id_simulacion){
  global $link;

  $id_subestado = 72;//3.2 FIRMA DE GARANTIAS
  
  $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and estado<>100";
  $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

  if (sqlsrv_num_rows($querySimulacionesFdc)>0){

    sqlsrv_query($link, "BEGIN TRANSACTION");

    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion,0,197,current_timestamp,'s',5,".$id_subestado.",72)");

    $consultarUsuarioNuevo=sqlsrv_query($link, "SELECT TOP 1 a.id_usuario, a.nombre, a.cantidad_asignado, b.cantidad_terminado, (a.cantidad_asignado+b.cantidad_terminado) AS cantidad_total
      FROM
      (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
        WHERE a.revision_garantias = 1 GROUP BY a.id_usuario,a.nombre,a.apellido) a,

      (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = FORMAT(GETDATE(), 'Y-m-d')) b 
        ON a.id_usuario=b.id_usuario_creacion
        WHERE a.revision_garantias = 1 GROUP BY  a.id_usuario,a.nombre,a.apellido) b 

      WHERE a.id_usuario=b.id_usuario  ORDER BY (a.cantidad_asignado+b.cantidad_terminado) ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

      
    if (sqlsrv_num_rows($consultarUsuarioNuevo)>0){
      $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarUsuarioNuevo);
      $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
      $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp,'s',2,73)");
      $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$id_simulacion."'");

      $consultarEstadoUsuario=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resEstadoUsuarioNuevo["id_usuario"]."' and vigente='s' and estado='2' and id_simulacion<>'".$id_simulacion."'");
      if (sqlsrv_num_rows($consultarEstadoUsuario)>0){
        //$actualizarUsuario2=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$id_analista_riesgo_operativo."'");
      }
    }else{
      $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'");       
    }

    sqlsrv_query($link, "COMMIT");

    $data = array('code' => 200, 'mensaje' => 'Proceso exitoso');
  }else{
    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion, 0, 197, current_timestamp, 's', 1 , 74)");

    $data = array('code' => 200, 'mensaje' => 'Proceso exitoso, Sin asignar Analista');
  }

  /*$querySimulacion=sqlsrv_query($link, "select * from simulaciones where id_simulacion='".$id_simulacion."'");
  $resSimulacion=sqlsrv_fetch_array($querySimulacion);

  $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and estado<>100";
  $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc);
  if (sqlsrv_num_rows($querySimulacionesFdc)>0){
    $consultarUltimoAnalistaEstudio=sqlsrv_query($link, "SELECT case when id_usuario_asignacion is null then 0 when id_usuario_asignacion = 197 then 0 else id_usuario_asignacion end as id_usuario_asignacion FROM simulaciones_fdc WHERE id_simulacion = '".$id_simulacion."' and estado=2 order by id desc limit 1");
    $resUltimoAnalistaEstudio=sqlsrv_fetch_array($consultarUltimoAnalistaEstudio);

    sqlsrv_query($link, "START TRANSACTION");
    $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");

    $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion,0,197,current_timestamp(),'s',5,".$resSimulacion["id_subestado"].",2)");

    if ($resJornadaLaboral["descripcion"]=="s"){
      $consultarEstadoUsuarioNuevo=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalistaEstudio["id_usuario_asignacion"]."' and disponible <> ('n')");
      if (sqlsrv_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalistaEstudio["id_usuario_asignacion"]<>0){
        $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarEstadoUsuarioNuevo);

        $consultarLimiteCreditosUsuario=sqlsrv_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
          FROM
          (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos
            FROM usuarios a 
            LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
            WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') a,
            (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos
            FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND DATE_FORMAT(fecha_creacion,'%Y-%m-%d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos");
          if(sqlsrv_num_rows($consultarLimiteCreditosUsuario)>0){
            $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
            $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp(),'s',2,3)");
            $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$id_simulacion."'");

            if ($resEstadoUsuarioNuevo["estado"]=="s" || $resEstadoUsuarioNuevo["estado"]=="g"){
              //$actualizarUsuario=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."'");
            }

            $consultarEstadoUsuario=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resSimulacion["id_analista_riesgo_operativo"]."' and vigente='s' and estado='2' and id_simulacion<>'".$id_simulacion."'");
            if (sqlsrv_num_rows($consultarEstadoUsuario)>0){
              //$actualizarUsuario2=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resSimulacion["id_analista_riesgo_operativo"]."'");                             
            }
          }else{
            $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo = null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'");       
          }
      }else{
        $idUsuarioAsignar = usuarioParaAsignar($id_simulacion);
        if ($idUsuarioAsignar<>0){
          $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
          $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion,$idUsuarioAsignar,197,current_timestamp(),'s',2,4)");
          $actualizarUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."' WHERE id_simulacion='".$id_simulacion."'");
        }
      }
    }else{
      $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'");       
    }
    sqlsrv_query($link, "COMMIT");
  }else{
    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion, 0, 197, current_timestamp(), 's', 1 , 1)");
  }*/
}


?>

