<?php
include ('../functions.php'); 
$link = conectar();

if ($_POST["exe"]=="borrarUsuario")
{
    
    $cadenaRespuesta="";
    $usuariosBorrar=$_POST["usuariosBorrar"];
    $usuariosBorrarDecode=json_decode($usuariosBorrar);
    foreach ($usuariosBorrarDecode as $usuariosBorrarEach) 
    {
        if ($usuariosBorrarEach->check=="s")
        {
            $consultarInformacionUsuario="SELECT * FROM usuarios WHERE id_usuario='".$usuariosBorrarEach->IdUsuario."'";
            $queryInformacionUsuario=sqlsrv_query($consultarInformacionUsuario,$link);
            $fila=sqlsrv_fetch_array($queryInformacionUsuario);
            $cadenaRespuesta.="Usuario: ".($fila["nombre"]." ".$fila["apellido"])." (".$fila["login"].")";

            $existe_en_simulaciones = sqlsrv_query($link, "SELECT usuario_creacion from simulaciones where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."' OR usuario_aprobprep = '".$fila["login"]."' OR usuario_creacionprep = '".$fila["login"]."' OR usuario_prospeccion = '".$fila["login"]."' OR usuario_radicado = '".$fila["login"]."' OR usuario_desistimiento = '".$fila["login"]."' OR usuario_incorporacion = '".$fila["login"]."' OR usuario_validacion = '".$fila["login"]."' OR usuario_firmado = '".$fila["login"]."' OR id_comercial = '".$fila["id_usuario"]."' OR id_analista_riesgo_operativo = '".$fila["id_usuario"]."' OR id_analista_riesgo_crediticio = '".$fila["id_usuario"]."' OR id_analista_gestion_comercial = '".$fila["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    
                $existe_en_simulaciones_observaciones = sqlsrv_query($link, "SELECT usuario_creacion from simulaciones_observaciones where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_simulaciones_subestados = sqlsrv_query($link, "select usuario_creacion from simulaciones_subestados where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET), array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_simulaciones_visado = sqlsrv_query($link, "select usuario_creacion from simulaciones_visado where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_simulaciones_incorporacion = sqlsrv_query($link, "select usuario_creacion from simulaciones_incorporacion where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_simulaciones_primeracuota = sqlsrv_query($link, "select usuario_creacion from simulaciones_primeracuota where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_adjuntos = sqlsrv_query($link, "select usuario_creacion from adjuntos where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_log_consultas = sqlsrv_query($link, "select id_usuario from log_consultas where id_usuario = '".$fila["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_entidades_cuentas = sqlsrv_query($link, "select usuario_creacion from entidades_cuentas where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_tesoreria_cc = sqlsrv_query($link, "select usuario_creacion from tesoreria_cc where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_firma_cheque = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_giros = sqlsrv_query($link, "select 	usuario_creacion from giros where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_pagos = sqlsrv_query($link, "select usuario_creacion from pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_pagos_detalle = sqlsrv_query("select usuario_anulacion from pagos_detalle where usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_recaudosplanos = sqlsrv_query($link, "select usuario_creacion from recaudosplanos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_recaudosplanos_detalle = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas = sqlsrv_query($link, "select usuario_creacion from ventas where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_detalle_documentos = sqlsrv_query$link, ("select usuario_modificacion from ventas_detalle_documentos where usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagos = sqlsrv_query($link, "select usuario_creacion from ventas_pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosdetalle = sqlsrv_query($link, "select usuario_anulacion from ventas_pagosdetalle where usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosplanos = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosplanos_detalle = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_cuotas_fondeador = sqlsrv_query($link, "select usuario_creacion from ventas_cuotas_fondeador where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_planoscuotasfondeador = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_planoscuotasfondeador_detalle = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_detalle where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_servicio_cliente = sqlsrv_query($link, "select usuario_creacion from servicio_cliente where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_gestion_cobro = sqlsrv_query($link, "select usuario_creacion from gestion_cobro where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_req_excep = sqlsrv_query($link, "select usuario_creacion from req_excep where usuario_creacion = '".$fila["login"]."' OR usuario_respuesta = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_req_excep_adjuntos = sqlsrv_query($link, "select usuario_creacion from req_excep_adjuntos where usuario_creacion = '".$fila["login"]."', array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)");
                
                $existe_en_pagadurias_visado = sqlsrv_query($link, "select id_usuario from pagadurias_usuarios_visado where id_usuario = '".$fila["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_bolsainc_pagos = sqlsrv_query($link, "select usuario_creacion from bolsainc_pagos where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_bolsainc_aplicaciones = sqlsrv_query($link, "select usuario_creacion from bolsainc_aplicaciones where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'");
                
                $existe_en_consultas_externas = sqlsrv_query($link, "select usuario_creacion from consultas_externas where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_unidades_negocio = sqlsrv_query($link, "select usuario_creacion from unidades_negocio where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_usuarios_unidades = sqlsrv_query($link, "select usuario_creacion from usuarios_unidades where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_tasas2_unidades = sqlsrv_query($link, "select usuario_creacion from tasas2_unidades where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_tasas2_unidades_privado = sqlsrv_query($link, "select usuario_creacion from tasas2_unidades_privado where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_empleados_creacion = sqlsrv_query($link, "select id_usuario from empleados_creacion where id_usuario = '".$fila["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_simulaciones_ext = sqlsrv_query("$link, select usuario_creacion from simulaciones_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."' OR usuario_aprobprep = '".$fila["login"]."' OR usuario_creacionprep = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_pagos_ext = sqlsrv_query($link, "select usuario_creacion from pagos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_pagos_detalle_ext = sqlsrv_query($link, "select usuario_anulacion from pagos_detalle_ext where usuario_anulacion = '".$fila["login"]."'",  array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_recaudosplanos_ext = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_recaudosplanos_detalle_ext = sqlsrv_query($link, "select usuario_creacion from recaudosplanos_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_ext = sqlsrv_query($link, "select usuario_creacion from ventas_ext where usuario_creacion = '".$fila["login"]."' OR usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_detalle_documentos_ext = sqlsrv_query($link, "select usuario_modificacion from ventas_detalle_documentos_ext where usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagos_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosdetalle_ext = sqlsrv_query($link, "select usuario_anulacion from ventas_pagosdetalle_ext where usuario_anulacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosplanos_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_pagosplanos_detalle_ext = sqlsrv_query($link, "select usuario_creacion from ventas_pagosplanos_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_ventas_cuotas_fondeador_ext = sqlsrv_query($link, "select usuario_creacion from ventas_cuotas_fondeador_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_planoscuotasfondeador_ext = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_planoscuotasfondeador_detalle_ext = sqlsrv_query($link, "select usuario_creacion from planoscuotasfondeador_detalle_ext where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_gestion_cobro_ext = sqlsrv_query($link, "select usuario_creacion from gestion_cobro_ext where usuario_creacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                $existe_en_usuarios = sqlsrv_query($link, "select usuario_creacion from usuarios where usuario_creacion = '".$fila["login"]."' OR usuario_modificacion = '".$fila["login"]."' OR usuario_inactivacion = '".$fila["login"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                
                if (sqlsrv_num_rows($existe_en_simulaciones) || sqlsrv_num_rows($existe_en_simulaciones_observaciones) || sqlsrv_num_rows($existe_en_simulaciones_subestados) || sqlsrv_num_rows($existe_en_simulaciones_visado) || sqlsrv_num_rows($existe_en_simulaciones_incorporacion) || sqlsrv_num_rows($existe_en_simulaciones_primeracuota) || sqlsrv_num_rows($existe_en_adjuntos) || sqlsrv_num_rows($existe_en_log_consultas) || sqlsrv_num_rows($existe_en_entidades_cuentas) || sqlsrv_num_rows($existe_en_tesoreria_cc) || sqlsrv_num_rows($existe_en_giros) || sqlsrv_num_rows($existe_en_pagos) || sqlsrv_num_rows($existe_en_pagos_detalle) || sqlsrv_num_rows($existe_en_recaudosplanos) || sqlsrv_num_rows($existe_en_recaudosplanos_detalle) || sqlsrv_num_rows($existe_en_ventas) || sqlsrv_num_rows($existe_en_ventas_detalle_documentos) || sqlsrv_num_rows($existe_en_ventas_pagos) || sqlsrv_num_rows($existe_en_ventas_pagosdetalle) || sqlsrv_num_rows($existe_en_ventas_pagosplanos) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_detalle) || sqlsrv_num_rows($existe_en_ventas_cuotas_fondeador) || sqlsrv_num_rows($existe_en_planoscuotasfondeador) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_detalle) || sqlsrv_num_rows($existe_en_servicio_cliente) || sqlsrv_num_rows($existe_en_gestion_cobro) || sqlsrv_num_rows($existe_en_req_excep) || sqlsrv_num_rows($existe_en_req_excep_adjuntos) || sqlsrv_num_rows($existe_en_pagadurias_visado) || sqlsrv_num_rows($existe_en_bolsainc_pagos) || sqlsrv_num_rows($existe_en_bolsainc_aplicaciones) || sqlsrv_num_rows($existe_en_consultas_externas) || sqlsrv_num_rows($existe_en_unidades_negocio) || sqlsrv_num_rows($existe_en_usuarios_unidades) || sqlsrv_num_rows($existe_en_tasas2_unidades) || sqlsrv_num_rows($existe_en_tasas2_unidades_privado) || sqlsrv_num_rows($existe_en_empleados_creacion) || sqlsrv_num_rows($existe_en_simulaciones_ext) || sqlsrv_num_rows($existe_en_pagos_ext) || sqlsrv_num_rows($existe_en_pagos_detalle_ext) || sqlsrv_num_rows($existe_en_recaudosplanos_ext) || sqlsrv_num_rows($existe_en_recaudosplanos_detalle_ext) || sqlsrv_num_rows($existe_en_ventas_ext) || sqlsrv_num_rows($existe_en_ventas_detalle_documentos_ext) || sqlsrv_num_rows($existe_en_ventas_pagos_ext) || sqlsrv_num_rows($existe_en_ventas_pagosdetalle_ext) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_ext) || sqlsrv_num_rows($existe_en_ventas_pagosplanos_detalle_ext) || sqlsrv_num_rows($existe_en_ventas_cuotas_fondeador_ext) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_ext) || sqlsrv_num_rows($existe_en_planoscuotasfondeador_detalle_ext) || sqlsrv_num_rows($existe_en_gestion_cobro_ext) || sqlsrv_num_rows($existe_en_usuarios))
                {
                    $cadenaRespuesta.=".Resultado: Usuario No Eliminado.";
                }
                else
                {
                    sqlsrv_query($link, "delete from subestados_usuarios where id_usuario = '".$fila["id_usuario"]."'")
                    
                    sqlsrv_query($link, "delete from oficinas_usuarios where id_usuario = '".$fila["id_usuario"]."'");
                    
                    sqlsrv_query($link, "delete from usuarios_unidades where id_usuario = '".$fila["id_usuario"]."'");
                    
                    sqlsrv_query($link, "delete from usuarios where id_usuario = '".$fila["id_usuario"]."'");

                    $cadenaRespuesta.=".Resultado: Usuario Eliminado.";
                }
        }
    }
        echo $cadenaRespuesta;
}
else if ($_POST["exe"]=="asociarUsuarios")
{
    $idUsuario=$_POST["idUsuario"];
    $asociarUsuariosOficinas=$_POST["asociarUsuariosOficinas"];
    $eliminarOficinasUsuarios="DELETE FROM oficinas_usuarios WHERE id_usuario='".$idUsuario."'";
    sqlsrv_query($eliminarOficinasUsuarios,$link);
    $asociarUsuariosOficinasDecode=json_decode($asociarUsuariosOficinas);
    foreach ($asociarUsuariosOficinasDecode as $asociarUsuariosOficinasEach) 
    {
        if ($asociarUsuariosOficinasEach->check=="s")
        {
            if ($asociarUsuariosOficinasEach->idZona<>"")
            {
                $insertarOficinasUsuarios="INSERT INTO oficinas_usuarios (id_usuario,id_oficina,id_zona) VALUES ('".$idUsuario."','".$asociarUsuariosOficinasEach->idOficina."','".$asociarUsuariosOficinasEach->idZona."')";
                sqlsrv_query($link,$insertarOficinasUsuarios);
            }
        }
    }
    echo "1";
}
else if ($_POST["exe"]=="asociarUsuariosCoord")
{
    $idUsuario=$_POST["idUsuario"];
    $asociarUsuariosOficinas=$_POST["asociarUsuarios"];
    $eliminarOficinasUsuarios="DELETE FROM coordinacion_usuarios WHERE id_usuario_principal='".$idUsuario."'";
    sqlsrv_query( $link ,$eliminarOficinasUsuarios);
    $asociarUsuariosOficinasDecode=json_decode($asociarUsuariosOficinas);
    foreach ($asociarUsuariosOficinasDecode as $asociarUsuariosOficinasEach) 
    {  
        if ($asociarUsuariosOficinasEach->check=="s")
        {
         
                $insertarOficinasUsuarios="INSERT INTO coordinacion_usuarios (id_usuario_principal,id_usuario_secundario) VALUES ('".$idUsuario."','".$asociarUsuariosOficinasEach->id_usuario."')";
                sqlsrv_query($link,$insertarOficinasUsuarios);
            
        }
    }
    echo "1";
}
?>