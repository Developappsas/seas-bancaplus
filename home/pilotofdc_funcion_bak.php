<?php include ('../functions.php'); 
$link = conectar();



if ($_POST["exe"]=="asignarAnalistas")
{
  $analista="";
    $variable=0;
    $asignacionSimulacionAnalista=$_POST["asignacionSimulacionAnalista"];
    $asignacionSimulacionAnalistaDecode=json_decode($asignacionSimulacionAnalista);
    foreach ($asignacionSimulacionAnalistaDecode as $asignacionSimulacionAnalistaEach) {
      $consultarAnalistaActual="SELECT case when id_analista_riesgo_operativo is null then '' else id_Analista_riesgo_operativo end as id_analista_riesgo_operativo FROM simulaciones WHERE id_simulacion='".$asignacionSimulacionAnalistaEach->idSimulacion."'";
      $queryAnalistaActual=sqlsrv_query($consultarAnalistaActual);
      $resAnalistaActual=sqlsrv_fetch_array($queryAnalistaActual);
      if ($resAnalistaActual["id_analista_riesgo_operativo"]<>$asignacionSimulacionAnalistaEach->idAnalista && $asignacionSimulacionAnalistaEach->idAnalista<>'') 
      {
        $actualizarSimulaciones=sqlsrv_query("UPDATE simulaciones SET id_analista_riesgo_operativo = ".$asignacionSimulacionAnalistaEach->idAnalista.",id_analista_riesgo_crediticio = ".$asignacionSimulacionAnalistaEach->idAnalista." WHERE id_simulacion = '".$asignacionSimulacionAnalistaEach->idSimulacion."'",$link);
        $actualizarSimulacionesFDC=sqlsrv_query("UPDATE simulaciones_fdc SET vigente = 'n' WHERE id_simulacion = '".$asignacionSimulacionAnalistaEach->idSimulacion."'",$link);
        $insertSimulacionesFDCNA=sqlsrv_query("INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES ('".$asignacionSimulacionAnalistaEach->idSimulacion."','197','197',CURRENT_TIMESTAMP(),'n',1)",$link);
        $insertSimulacionesFDCNA=sqlsrv_query("INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES ('".$asignacionSimulacionAnalistaEach->idSimulacion."','".$asignacionSimulacionAnalistaEach->idAnalista."','197',CURRENT_TIMESTAMP(),'s',2)",$link);
        $updateUsuarioActual=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$asignacionSimulacionAnalistaEach->idAnalista."'",$link);
        $updateUsuarioAnterior=sqlsrv_query("UPDATE usuarios SET disponible='n' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'",$link);
        $variable=1;
      }else{
        $variable=1;
      }
    }
    echo $variable;
}
else if ($_POST["exe"]=="deshabilitarAnalistas")
{
    $variable=0;
    $usuariosDeshabilitar=$_POST["usuariosDeshabilitar"];
    $usuariosDeshabilitarDecode=json_decode($usuariosDeshabilitar);
    $jornadaLaboralFDC=$_POST["jornadaLaboralFDC"];
    $consulta="";
    $consulta2="";
    $actualizarJornadaLaboral=sqlsrv_query("UPDATE definicion_tipos set descripcion='".$jornadaLaboralFDC."' where id_tipo=5 and id=1",$link);

    $variable=1;
    if (count($usuariosDeshabilitarDecode["cantidadCreditos"])>0)
    {
    foreach ($usuariosDeshabilitarDecode as $usuariosDeshabilitarEach) {
        $consultarUsuariosDeshabilitar="CALL spConsultasAsignacionFabrica(4,'".$usuariosDeshabilitarEach->cantidadCreditos."','".$usuariosDeshabilitarEach->idAnalista."','".$usuariosDeshabilitarEach->estadoAnalista."',@resp,@resp2)";
        $consulta.=$consultarUsuariosDeshabilitar."--";
        if (sqlsrv_query($consultarUsuariosDeshabilitar,$link))
        {

          $consultaRespuesta=sqlsrv_query("SELECT @resp as resp",$link);
          $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
          $variable=$respRespuesta["resp"];
          $consulta2.=$variable."--";
          if ($variable==3)
          {
            $variable=3;
            $consultarCreditosEnTramiteAnalista="SELECT a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE b.estado IN (2) AND b.vigente='s' AND b.id_usuario_asignacion=".$usuariosDeshabilitarEach->idAnalista;
            $queryCreditosEnTramiteAnalista=sqlsrv_query($consultarCreditosEnTramiteAnalista,$link);
            if (sqlsrv_num_rows($queryCreditosEnTramiteAnalista)>0)
            {
              while ($resCreditosEnTramiteAnalista=sqlsrv_fetch_array($queryCreditosEnTramiteAnalista))
              {
                $consultarCreditosEnTramiteAnalista="UPDATE simulaciones_fdc
                SET
                    vigente = 'n'
                WHERE
                    id_simulacion=".$resCreditosEnTramiteAnalista["id_simulacion"];
              
                
                $queryCreditosEnTramiteAnalista=sqlsrv_query($consultarCreditosEnTramiteAnalista,$link);

                $consultarCreditosEnTramiteAnalista2="INSERT INTO  simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES  (".$resCreditosEnTramiteAnalista["id_simulacion"].",197,CURRENT_TIMESTAMP(),'s',1);";
                $queryCreditosEnTramiteAnalista2=sqlsrv_query($consultarCreditosEnTramiteAnalista2,$link);

                $consultarCreditosEnTramiteAnalista3="UPDATE simulaciones SET id_analista_riesgo_operativo=NULL,id_analista_riesgo_crediticio=NULL WHERE id_simulacion=".$resCreditosEnTramiteAnalista["id_simulacion"];
                $queryCreditosEnTramiteAnalista2=sqlsrv_query($consultarCreditosEnTramiteAnalista3,$link);

                
              
                $consultaRespuesta=sqlsrv_query("SELECT @resp as resp",$link);
                $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
                $variable=3;
              }
            }
            
          }

  
        }
      }
    }
      echo $variable;
}
else if ($_POST["exe"]=="asignacionInicialUsuario")
{
  $variable=0;
  $idSimulacion=$_POST["idSimulacion"];
  $consultarJornadaLaboral=sqlsrv_query("SELECT * FROM definicion_tipos where id_tipo=5 and id=1",$link);
  
  $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
  if ($resJornadaLaboral["descripcion"]=="s")
  {
    $consultaAnalistaReciente=" SELECT TOP 1 a.id_simulacion,a.id_analista_riesgo_operativo
    FROM simulaciones a
    LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion 
    LEFT JOIN usuarios d ON d.id_usuario=a.id_analista_riesgo_operativo
    WHERE a.cedula=(SELECT cedula FROM simulaciones WHERE id_simulacion=inid_simulacion)
    AND a.id_simulacion<inid_simulacion AND a.id_analista_riesgo_operativo IS NOT NULL
    AND b.vigente='s' AND d.estado=1 AND b.estado IN (4,2) AND DATEDIFF(CURRENT_DATE,format(b.fecha_creacion,'Y-m-d'))<=30 AND d.disponible IN ('s','g') 
    ORDER BY a.id_simulacion DESC";
    $queryAnalistaReciente=sqlsrv_query($consultaAnalistaReciente,$link);
    if (sqlsrv_num_rows($queryAnalistaReciente)>0)
    {
      $resAnalistaReciente=sqlsrv_fetch_array($queryAnalistaReciente);
      $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idSimulacion."'";
      sqlsrv_query($cambiarEstadoSimulacionFDC,$link);
      $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado) values ('".$idSimulacion."',197,'".$resAnalistaReciente["id_analista_riesgo_operativo"]."',current_date(),'s',2)";
      sqlsrv_query($asignarAnalista,$link);
      $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$resAnalistaReciente["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$resAnalistaReciente["id_analista_riesgo_operativo"]."',id_analista_gestion_comercial='".$resAnalistaReciente["id_analista_riesgo_operativo"]."' where id_simulacion='".$idSimulacion."'";
      sqlsrv_query($actualizarSimulacion,$link);
      $variable=1;
    }else{
      $consultarUnidadNegocio="SELECT id FROM definicion_tipos WHERE id_tipo=3 AND descripcion=(SELECT id_unidad_negocio FROM simulaciones WHERE id_simulacion=".$idSimulacion.")";
      $queryUnidadNegocio=sqlsrv_Query($consultarUnidadNegocio,$link);
      if (sqlsrv_num_rows($queryUnidadNegocio)>0)
      {    
        $consultarUsuarioAsignar="SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos
        FROM
        (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_asignado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
         WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
        format(a.fecha_ultimo_acceso,'Y-m-d') = CURRENT_DATE() AND a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') 
        GROUP BY a.`id_usuario`) a,
        (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_terminado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado<>28 AND format(fecha_creacion,'Y-m-d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion
         WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
        format(a.fecha_ultimo_acceso,'Y-m-d') = CURRENT_DATE() AND a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g')
        GROUP BY a.`id_usuario`) b WHERE a.id_usuario=b.id_usuario";

        
       
      }else{
        $consultarUsuarioAsignar="SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos
        FROM
        (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_asignado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
         WHERE a.id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
        format(a.fecha_ultimo_acceso,'Y-m-d') = CURRENT_DATE() AND a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g') 
        GROUP BY a.`id_usuario`) a,
        (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_terminado
        FROM usuarios a 
        LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado<>28 AND format(fecha_creacion,'Y-m-d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion
         WHERE a.id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND
        format(a.fecha_ultimo_acceso,'Y-m-d') = CURRENT_DATE() AND a.subtipo = 'ANALISTA_CREDITO' AND a.disponible IN  ('s','g')
        GROUP BY a.`id_usuario`) b WHERE a.id_usuario=b.id_usuario"; 
       
      }
      $consultarUsuarioDisponible=$consultarUsuarioAsignar." AND a.cantidad_asignado=0";

      $queryUsuarioDisponible=sqlsrv_query($consultarUsuarioDisponible,$link);

      if (sqlsrv_num_rows($queryUsuarioDisponible)>0)
      {
        $resUsuarioAsignar=sqlsrv_fetch_array($queryUsuarioDisponible);
        $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idSimulacion."'";
        sqlsrv_query($cambiarEstadoSimulacionFDC,$link);
        $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado) values ('".$idSimulacion."',197,'".$resUsuarioAsignar["id_usuario"]."',current_date(),'s',2)";
        sqlsrv_query($asignarAnalista,$link);
        $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$resUsuarioAsignar["id_usuario"]."',id_analista_riesgo_crediticio='".$resUsuarioAsignar["id_usuario"]."',id_analista_gestion_comercial='".$resUsuarioAsignar["id_usuario"]."' where id_simulacion='".$idSimulacion."'";
        sqlsrv_query($actualizarSimulacion,$link);
        $actualizarEstadoUsuario=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resUsuarioAsignar["id_usuario"]."'",$link);
        $variable=1;
      

      }else{
        $consultarUsuarioAsignar.=" ORDER BY cantidad_creditos ASC LIMIT 1";
        $queryUsuarioAsignar=sqlsrv_query($consultarUsuarioAsignar,$link);
        if (sqlsrv_num_rows($queryUsuarioAsignar)>0)
        {
          $resUsuarioAsignar=sqlsrv_fetch_array($queryUsuarioAsignar);
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idSimulacion."'";
          sqlsrv_query($cambiarEstadoSimulacionFDC,$link);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado) values ('".$idSimulacion."',197,'".$resUsuarioAsignar["id_usuario"]."',current_date(),'s',2)";
          sqlsrv_query($asignarAnalista,$link);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$resUsuarioAsignar["id_usuario"]."',id_analista_riesgo_crediticio='".$resUsuarioAsignar["id_usuario"]."',id_analista_gestion_comercial='".$resUsuarioAsignar["id_usuario"]."' where id_simulacion='".$idSimulacion."'";
          sqlsrv_query($actualizarSimulacion,$link);
          $actualizarEstadoUsuario=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resUsuarioAsignar["id_usuario"]."'",$link);
          $variable=1;
        }else{
          $variable=1;
        }
      }
      

     
      
      

    }


  }
  else
  {
    $variable=1;
  }
 
  echo $variable;
}
else if ($_POST["exe"]=="disponibleUsuario")
{
  $variable=0;
  $idUsuario=$_POST["idUsuario"];
  $consultarUsuariosDeshabilitar="CALL spConsultasAsignacionFabrica(4,null,'".$idUsuario."','',@resp,@resp2)";
  if (sqlsrv_query($consultarUsuariosDeshabilitar,$link))
  {
    $consultaRespuesta=sqlsrv_query("SELECT @resp as resp",$link);
    $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
    $variable=$respRespuesta["resp"];

    if ($variable==1)
    {
      $consultarJornadaLaboral=sqlsrv_query("SELECT * FROM definicion_tipos where id_tipo=5 and id=1",$link);
  
      $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
      if ($resJornadaLaboral["descripcion"]=="s")
      {
        $consultarUsuario=sqlsrv_query("SELECT * FROM definicion_tipos where id_tipo=4 and descripcion='".$idUsuario."'",$link);
        
        if (sqlsrv_num_rows($consultarUsuario)>0){
          $consultarCreditoAsignar="SELECT a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE a.id_unidad_negocio IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=3) AND b.estado IN (1) AND b.vigente='s' ORDER BY b.fecha_creacion ASC LIMIT 1";
        }else{
          $consultarCreditoAsignar="SELECT a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE a.id_unidad_negocio NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=3) AND b.estado IN (1) AND b.vigente='s' ORDER BY b.fecha_creacion ASC LIMIT 1";
        }
        $queryCreditoAsignar=sqlsrv_query($consultarCreditoAsignar,$link);
        if (sqlsrv_num_rows($queryCreditoAsignar)>0)
        {
          $resCreditoAsignar=sqlsrv_fetch_array($queryCreditoAsignar);
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditoAsignar["id_simulacion"]."'";
          sqlsrv_query($cambiarEstadoSimulacionFDC,$link);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado) values ('".$resCreditoAsignar["id_simulacion"]."',197,'".$idUsuario."',current_date(),'s',2)";
          sqlsrv_query($asignarAnalista,$link);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuario."',id_analista_riesgo_crediticio='".$idUsuario."',id_analista_gestion_comercial='".$idUsuario."' where id_simulacion='".$resCreditoAsignar["id_simulacion"]."'";
          sqlsrv_query($actualizarSimulacion,$link);
          $actualizarEstadoUsuario=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuario."'",$link);
          $variable=1;
        }else{
          $variable=1;
        }
      }else{
        $variable=1;
      }
      
    }

  }
  echo ($variable);
}
else if ($_POST["exe"]=="detenerProceso")
{
  $variable=0;
  $idSimulacion=$_POST["idSimulacion"];
  $idUsuario=$_POST["idUsuario"];
  $consultarDetenerProceso="CALL spConsultasAsignacionFabrica(9,'".$idSimulacion."','".$idUsuario."','',@resp,@resp2)";
  if (sqlsrv_query($consultarDetenerProceso,$link))
  {
    $consultaRespuesta=sqlsrv_query("SELECT @resp as resp",$link);
    $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
    $variable=$respRespuesta["resp"];

  }
  echo $variable;

}

?>
