<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 
include ('../controles/FDC.php'); 
$link = conectar();

if ($_POST["exe"]=="pedirCredito")
{
  $variable=0;
  $idUsuario=$_POST["idUsuario"];
  $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
  $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
  if ($resJornadaLaboral["descripcion"]=="s")
  {

    $idSimulacionAsignar=creditoParaAsignar($idUsuario);
    
    if ($idSimulacionAsignar==0)
    {
      $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idSimulacionAsignar."'";
      sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
      $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$idSimulacionAsignar."',197,'".$idUsuario."',CURRENT_TIMESTAMP,'s',2,10)";
      sqlsrv_query($link, $asignarAnalista);
      $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuario."',id_analista_riesgo_crediticio='".$idUsuario."',id_analista_gestion_comercial='".$idUsuario."' where id_simulacion='".$idSimulacionAsignar."'";
      sqlsrv_query($link, $actualizarSimulacion);
      $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuario."'");
      $variable=1;
    }else{
      $variable=2;
    }
  }else{
    $variable=3;
  }
  echo $variable;
}
else if ($_POST["exe"]=="usuarioNoDisponibleReasignar")
{
  $analista="";
  $variable=0;
  $idUsuario=$_POST["idUsuario"];
  $actualizarUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='n' WHERE id_usuario='".$idUsuario."'");
  $consultarAsignacionesUsuario="SELECT * FROM simulaciones_fdc WHERE estado=2 and vigente='s' and id_usuario_asignacion='".$idUsuario."'";
  $queryAsignacionesUsuario=sqlsrv_query($link, $consultarAsignacionesUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
  if (sqlsrv_num_rows($queryAsignacionesUsuario)>0)
  {
    while ($resAsignacionesUsuario=sqlsrv_fetch_array($queryAsignacionesUsuario))
    {  
      $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
      $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
      if ($resJornadaLaboral["descripcion"]=="s")
      {
     
        $idUsuarioAsignar=usuarioParaAsignar($resAsignacionesUsuario["id_simulacion"]);
        if ($idUsuarioAsignar<>0)
        {
          $resUsuarioAsignar=sqlsrv_fetch_array($queryUsuarioAsignar);
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resAsignacionesUsuario["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,11)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $variable=1;
        }else{
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          
          $cambiarEstadoSimulacionFDC="DELETE FROM simulaciones_fdc WHERE id='".$resAsignacionesUsuario["id"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $consultarMaxSimulacionFdc="SELECT max(id) as id_fdc FROM simulaciones_fdc WHERE id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          $queryMaxSimulacionFDC=sqlsrv_query($link, $consultarMaxSimulacionFdc);
          $resMaxSimulacionFDC=sqlsrv_fetch_array($queryMaxSimulacionFDC);
          $asignarAnalista="UPDATE simulaciones_fdc SET vigente='s' WHERE id='".$resMaxSimulacionFDC["id_fdc"]."'";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null,id_analista_gestion_comercial=null where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          
          $variable=3;
        }
      }else{
        $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          
          $cambiarEstadoSimulacionFDC="DELETE FROM simulaciones_fdc WHERE id='".$resAsignacionesUsuario["id"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $consultarMaxSimulacionFdc="SELECT max(id) as id_fdc FROM simulaciones_fdc WHERE id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          $queryMaxSimulacionFDC=sqlsrv_query($link, $consultarMaxSimulacionFdc);
          $resMaxSimulacionFDC=sqlsrv_fetch_array($queryMaxSimulacionFDC);
          $asignarAnalista="UPDATE simulaciones_fdc SET vigente='s' WHERE id='".$resMaxSimulacionFDC["id_fdc"]."'";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null,id_analista_gestion_comercial=null where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          
          $variable=3;
      }
    }
  }else{
    $variable=1;
  }
  echo $variable;
}
else if ($_POST["exe"]=="usuarioNoDisponibleTerminar")  
{
  $analista="";
  $variable=0;
  $idUsuario=$_POST["idUsuario"];
  $actualizarUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='t' WHERE id_usuario='".$idUsuario."'");  
  $variable=1;
  echo $variable;
}
else if ($_POST["exe"]=="asignarAnalista")
{
  $analista="";
  $variable=0;
  $asignacionSimulacionAnalista=$_POST["asignacionSimulacionAnalista"];
  $asignacionSimulacionAnalistaDecode=json_decode($asignacionSimulacionAnalista);
  $idSimulacion=$_POST["idSimulacion"];
  $idAnalista=$_POST["idUsuario"];
   $consultarAnalistaActual="SELECT estado,case when id_usuario_asignacion is null then '' else id_usuario_asignacion end as id_analista_riesgo_operativo FROM simulaciones_fdc WHERE id_simulacion='".$idSimulacion."' and vigente='s'";
    
    $queryAnalistaActual=sqlsrv_query($link, $consultarAnalistaActual, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    $cantidadAnalistaActual=sqlsrv_num_rows($queryAnalistaActual);
    $resAnalistaActual=sqlsrv_fetch_array($queryAnalistaActual);
    if ($resAnalistaActual["estado"]==1 || $resAnalistaActual["estado"]==5 || $resAnalistaActual["estado"]==2) {
      if ($cantidadAnalistaActual>0) {
        if ($resAnalistaActual["id_analista_riesgo_operativo"]<>$idAnalista && $idAnalista<>'')  {
          $actualizarSimulaciones=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo = ".$idAnalista.",id_analista_riesgo_crediticio = ".$idAnalista." WHERE id_simulacion = '".$idSimulacion."'");
          $actualizarSimulacionesFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente = 'n' WHERE id_simulacion = '".$idSimulacion."'");
          //$insertSimulacionesFDCNA=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES ('".$asignacionSimulacionAnalistaEach->idSimulacion."','197','197',CURRENT_TIMESTAMP,'n',1)");
          $insertSimulacionesFDCNA2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ('".$idSimulacion."','".$idAnalista."','1973',CURRENT_TIMESTAMP,'s',2,12)");
          $updateUsuarioActual=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idAnalista."'");
          //$updateUsuarioAnterior=sqlsrv_query($link, "UPDATE usuarios SET disponible='n' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'");
          //$consultarCreditosAnalista=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE estado=2 and vigente='s' and id_usuario_asignacion='".$resAnalistaActual["id_analista_riesgo_operativo"]."' and id_simulacion<>'".$asignacionSimulacionAnalistaEach->idSimulacion."'");
          //if (sqlsrv_num_rows($consultarCreditosAnalista)==0)
          //{
            //$updateUsuarioAnterior=sqlsrv_query($link, "UPDATE usuarios SET disponible='s' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'");
          //}
          $variable=1;
        }else{
          $variable=2;
        }
      }else{
        $variable=3;
      }
    }else{
      $variable=4;
    }
  
  echo $variable;
}
else if ($_POST["exe"]=="asignarAnalistas")
{
  $analista="";
  $variable=0;
  $asignacionSimulacionAnalista=$_POST["asignacionSimulacionAnalista"];
  $asignacionSimulacionAnalistaDecode=json_decode($asignacionSimulacionAnalista);
  foreach ($asignacionSimulacionAnalistaDecode as $asignacionSimulacionAnalistaEach) 
  {
    sqlsrv_query($link, "START TRANSACTION", $link);
    $consultarAnalistaActual="SELECT estado,case when id_usuario_asignacion is null then '' else id_usuario_asignacion end as id_analista_riesgo_operativo FROM simulaciones_fdc WHERE id_simulacion='".$asignacionSimulacionAnalistaEach->idSimulacion."' and vigente='s'";
    $queryAnalistaActual=sqlsrv_query($link, $consultarAnalistaActual);
    $cantidadAnalistaActual=sqlsrv_num_rows($queryAnalistaActual);
    $resAnalistaActual=sqlsrv_fetch_array($queryAnalistaActual);
    if ($resAnalistaActual["estado"]==1 || $resAnalistaActual["estado"]==5 || $resAnalistaActual["estado"]==2)
    {
      if ($cantidadAnalistaActual>0)
      {
        if ($resAnalistaActual["id_analista_riesgo_operativo"]<>$asignacionSimulacionAnalistaEach->idAnalista && $asignacionSimulacionAnalistaEach->idAnalista<>'') 
        {
          $actualizarSimulaciones=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo = ".$asignacionSimulacionAnalistaEach->idAnalista.",id_analista_riesgo_crediticio = ".$asignacionSimulacionAnalistaEach->idAnalista." WHERE id_simulacion = '".$asignacionSimulacionAnalistaEach->idSimulacion."'");
          $actualizarSimulacionesFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente = 'n' WHERE id_simulacion = '".$asignacionSimulacionAnalistaEach->idSimulacion."'");
          //$insertSimulacionesFDCNA=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado) VALUES ('".$asignacionSimulacionAnalistaEach->idSimulacion."','197','197',CURRENT_TIMESTAMP,'n',1)");
          $insertSimulacionesFDCNA2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ('".$asignacionSimulacionAnalistaEach->idSimulacion."','".$asignacionSimulacionAnalistaEach->idAnalista."','19750',CURRENT_TIMESTAMP,'s',2,13)");
          $updateUsuarioActual=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$asignacionSimulacionAnalistaEach->idAnalista."'");
          //$updateUsuarioAnterior=sqlsrv_query($link, "UPDATE usuarios SET disponible='n' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'");
          //$consultarCreditosAnalista=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE estado=2 and vigente='s' and id_usuario_asignacion='".$resAnalistaActual["id_analista_riesgo_operativo"]."' and id_simulacion<>'".$asignacionSimulacionAnalistaEach->idSimulacion."'");
          //if (sqlsrv_num_rows($consultarCreditosAnalista)==0)
          //{
            //$updateUsuarioAnterior=sqlsrv_query($link, "UPDATE usuarios SET disponible='s' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'");
          //}
          $variable=1;
        }else{
          $variable=1;
        }
      }else{
        $variable=1;
      }
    }else{
      $variable=1;
    }
    sqlsrv_query($link, "COMMIT");
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
  $diferente="";
  $consultarJornadaLaboralActual=sqlsrv_query($link, "SELECT * FROM definicion_tipos WHERE id_tipo=5 and id=1");
  $resJornadaLaboralActual=sqlsrv_fetch_array($consultarJornadaLaboralActual);
  if ($resJornadaLaboralActual["descripcion"]==$jornadaLaboralFDC)
  {
    $diferente='n';
  }else{
    $actualizarJornadaLaboral=sqlsrv_query($link, "UPDATE definicion_tipos set descripcion='".$jornadaLaboralFDC."' where id_tipo=5 and id=1");
    $diferente='s';
  }
    
  $analista="";
  $variable=1;

  if (count($usuariosDeshabilitarDecode)>0){
    
    if($_POST['unidad_negocio_fdc'] == 1){
      $eliminarUsuariosFIANTI=sqlsrv_query($link, "DELETE FROM definicion_tipos WHERE id_tipo=4");
    }

    $cont=0;

    foreach ($usuariosDeshabilitarDecode as $usuariosDeshabilitarEach) {      
      
      if ($usuariosDeshabilitarEach->unidadNegocio==1 && $_POST['unidad_negocio_fdc'] == 1){
        $cont++;
        $crearUsuarioFIANTI=sqlsrv_query($link, "INSERT INTO definicion_tipos (id_tipo,id,descripcion) values (4,".$cont.",'".$usuariosDeshabilitarEach->idAnalista."')");
        // $analista.="INSERT INTO definicion_tipos (id_tipo,id,descripcion) values (4,".$cont.",'".$usuariosDeshabilitarEach->idAnalista."')--";
      }

      if ($usuariosDeshabilitarEach->estadoAnalista==0){
        $estadoUsuario='s';
      }else if ($usuariosDeshabilitarEach->estadoAnalista==1){
        $estadoUsuario='n';
      }

      $consultaActualizarUsuarios=sqlsrv_query($link, "UPDATE usuarios SET disponible='".$estadoUsuario."',cantidad_creditos='".$usuariosDeshabilitarEach->cantidadCreditos."' WHERE id_usuario='".$usuariosDeshabilitarEach->idAnalista."'");
    }
    $variable=1;
  }

  $idUsuarioAsignar=0;

  if ($diferente=='s' && $jornadaLaboralFDC=='s'){
    
    $consultarCreditosReprocesos=sqlsrv_query($link, "SELECT * from simulaciones_fdc where vigente='s' and estado=5");
    
    if (sqlsrv_num_rows($consultarCreditosReprocesos)>0){
      
      while ($resCreditosReprocesos=sqlsrv_fetch_array($consultarCreditosReprocesos)){
        
        $consultarUltimoAnalista=sqlsrv_query($link, "SELECT id,id_usuario_asignacion FROM simulaciones_fdc where estado=2 and id<'".$resCreditosReprocesos["id"]."' and id_simulacion='".$resCreditosReprocesos["id_simulacion"]."' ORDER BY id DESC LIMIT 1");
        $resUltimoAnalista=sqlsrv_fetch_array($consultarUltimoAnalista);
        $consultarEstadoUltimoUsuarioAsignado=sqlsrv_query($link, "SELECT * FROM usuarios WHERE disponible in ('s','g') and estado=1 and id_usuario='".$resUltimoAnalista["id_usuario_asignacion"]."'");

        if (sqlsrv_num_rows($consultarEstadoUltimoUsuarioAsignado)>0){

          $consultarEstadoUsuarioNuevo=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalista["id_usuario_asignacion"]."' and disponible <> ('n')");

          if (sqlsrv_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalista["id_usuario_asignacion"]<>0){
            $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarEstadoUsuarioNuevo);

            $consultarLimiteCreditosUsuario=sqlsrv_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
              FROM (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos
            FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') a, (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND DATE_FORMAT(fecha_creacion,'%Y-%m-%d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos");
            
            if(sqlsrv_num_rows($consultarLimiteCreditosUsuario)>0){
              $idUsuarioAsignar=$resUltimoAnalista["id_usuario_asignacion"];
            }
          }          
        }else{
          //PROCESO 1 terminar
          $idUsuarioAsignar=usuarioParaAsignar($resCreditosReprocesos["id_simulacion"]);
        }
        
        if ($idUsuarioAsignar<>0){
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resCreditosReprocesos["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,14)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $variable=1;
          
        }else{
          $variable=1;
        }
          
          
      }
    }


    $consultarCreditosSinAsignar=sqlsrv_query($link, "SELECT a.* FROM simulaciones_fdc a LEFT JOIN simulaciones b ON a.id_simulacion=b.id_simulacion WHERE a.vigente='s' AND a.estado=1 ORDER BY b.fecha_radicado ASC");
    if (sqlsrv_num_rows($consultarCreditosSinAsignar)>0)
    { 
      while ($resCreditosSinAsignar=sqlsrv_fetch_array($consultarCreditosSinAsignar))
      {
        $idUsuarioAsignar=usuarioParaAsignar($resCreditosSinAsignar["id_simulacion"]);
        if ($idUsuarioAsignar<>0)
        {
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditosSinAsignar["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resCreditosSinAsignar["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,15)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resCreditosSinAsignar["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $variable=1;    
        }else{
          $variable=1;    
        }
      }
    }

  }
    


  echo $variable;
}
else if ($_POST["exe"]=="asignacionInicialUsuario")
{
  $variable=0;
  $val=0;
  $idSimulacion=$_POST["idSimulacion"];
  $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
  $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
  if ($resJornadaLaboral["descripcion"]=="s")
  {
    $idUsuarioAsignar=usuarioParaAsignar($idSimulacion);
    if ($idUsuarioAsignar<>0 && $idSimulacion<>0)
    {
      $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idSimulacion."'";
      sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
      $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$idSimulacion."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,16)";
      sqlsrv_query($link, $asignarAnalista);
      $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$idSimulacion."'";
      sqlsrv_query($link, $actualizarSimulacion);
      $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
      $variable=1; 
    }else{
      $variable=1; 
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
  if (sqlsrv_query($link, $consultarUsuariosDeshabilitar))
  {
    $consultaRespuesta=sqlsrv_query($link, "SELECT @resp as resp");
    $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
    $variable=$respRespuesta["resp"];

    if ($variable==1)
    {
      $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
      $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
      if ($resJornadaLaboral["descripcion"]=="s")
      {
        $idCreditoAsignar=creditoParaAsignar($idUsuario);
        if ($idCreditoAsignar<>0)
        {
          $resCreditoAsignar=sqlsrv_fetch_array($queryCreditoAsignar);
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$idCreditoAsignar."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$idCreditoAsignar."',197,'".$idUsuario."',CURRENT_TIMESTAMP,'s',2,17)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuario."',id_analista_riesgo_crediticio='".$idUsuario."',id_analista_gestion_comercial='".$idUsuario."' where id_simulacion='".$idCreditoAsignar."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuario."'");
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
  if (sqlsrv_query($link, $consultarDetenerProceso))
  {
    $consultaRespuesta=sqlsrv_query($link, "SELECT @resp as resp");
    $respRespuesta=sqlsrv_fetch_array($consultaRespuesta);
    $variable=$respRespuesta["resp"];

  }
  echo $variable;

}
else if ($_POST["exe"]=="asignarCreditosPendientes")
{

  $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
  $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
  if ($resJornadaLaboral["descripcion"]=="s")
  {
    $consultarCreditosReprocesos=sqlsrv_query($link, "SELECT * from simulaciones_fdc where vigente='s' and estado=5");
    if (sqlsrv_num_rows($consultarCreditosReprocesos)>0)
    {
      while ($resCreditosReprocesos=sqlsrv_fetch_array($consultarCreditosReprocesos))
      {
          
        $consultarUltimoAnalista=sqlsrv_query($link, "SELECT id,id_usuario_asignacion FROM simulaciones_fdc where estado=2 and id<'".$resCreditosReprocesos["id"]."' and id_simulacion='".$resCreditosReprocesos["id_simulacion"]."' ORDER BY id DESC LIMIT 1");
        $resUltimoAnalista=sqlsrv_fetch_array($consultarUltimoAnalista);
        $consultarEstadoUltimoUsuarioAsignado=sqlsrv_query($link, "SELECT * FROM usuarios WHERE disponible in ('s','g') and estado=1 and id_usuario='".$resUltimoAnalista["id_usuario_asignacion"]."'");
        if (sqlsrv_num_rows($consultarEstadoUltimoUsuarioAsignado)>0)
        {
          $idUsuarioAsignar=$resUltimoAnalista["id_usuario_asignacion"];
        }else{
            //PROCESO 1 terminar
          $idUsuarioAsignar=usuarioParaAsignar($resAsignacionesUsuario["id_simulacion"]);
          
      
        }
        if ($idUsuarioAsignar<>0)
        {
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resCreditosReprocesos["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,19)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $variable=1;
          
        }else{
          $variable=1;
        }
          
          
      }
    }else{
      $variable=3;
    }


    $consultarCreditosSinAsignar=sqlsrv_query($link, "SELECT a.* FROM simulaciones_fdc a LEFT JOIN simulaciones b ON a.id_simulacion=b.id_simulacion WHERE a.vigente='s' AND a.estado=1 ORDER BY b.fecha_radicado ASC");
    if (sqlsrv_num_rows($consultarCreditosSinAsignar)>0)
    { 
      while ($resCreditosSinAsignar=sqlsrv_fetch_array($consultarCreditosSinAsignar))
      {
        $idUsuarioAsignar=usuarioParaAsignar($resCreditosSinAsignar["id_simulacion"]);
        if ($idUsuarioAsignar<>0)
        {
          $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditosSinAsignar["id_simulacion"]."'";
          sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
          $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resCreditosSinAsignar["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP,'s',2,18)";
          sqlsrv_query($link, $asignarAnalista);
          $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resCreditosSinAsignar["id_simulacion"]."'";
          sqlsrv_query($link, $actualizarSimulacion);
          $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
          $variable=1;    
        }else{
          $variable=1;    
        }
      }
    }else{
      $variable=4;
    }

    if ($variable==3 || $variable==4)
    {
      $variable=3;
    }
  }else{
    $variable=2;
  }
    echo $variable;
}
else if($_POST["exe"]=="desasignarCredito")
{
  $variable=0;
  $idSimulacion=$_POST["idSimulacion"];
  $idUsuario=$_POST["idUsuario"];
  $consultaEliminarUltimoRegistroFDC=sqlsrv_query($link, "DELETE FROM simulaciones_fdc where id_simulacion='".$idSimulacion."' and vigente='s'");
  $consultaUltimoRegistroAsignado=sqlsrv_query($link, "SELECT top 1 * FROM simulaciones_fdc where id_simulacion='".$idSimulacion."' and estado in (1,5) order by id desc ");
  if (sqlsrv_num_rows($consultaUltimoRegistroAsignado)>0)
  {
    $resUltimoRegistroAsignado=sqlsrv_fetch_array($consultaUltimoRegistroAsignado);
    $consultaActualizarRegistroAsignado="UPDATE simulaciones_fdc SET vigente='s' WHERE id='".$resUltimoRegistroAsignado["id"]."'";
    $consultaActualizarRegistroAsignado2="UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$idSimulacion."'";
    $queryActualizarRegistroAsignado2=sqlsrv_query($link, $consultaActualizarRegistroAsignado2);
    if (sqlsrv_query($link, $consultaActualizarRegistroAsignado))
    {
     $variable=1;
    
    }else{
      $variable=2;
    }
  }
  echo $variable;
}



?>