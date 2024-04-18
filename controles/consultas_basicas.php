<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('../functions.php'); 
$link = conectar_utf();

if(!isset($_SESSION["S_IDUSUARIO"])){
    echo 0;
    return false;
}
if ($_POST["exe"]=="cambiarEstadoVentaCartera")
{
    $id_simulacion=$_POST["idSimulacion"];
    $opcionSeleccionada=$_POST["opcionSeleccionada"];

    $actEstadoVisado="UPDATE simulaciones SET estado_venta_cartera='".$opcionSeleccionada."' WHERE id_simulacion='".$id_simulacion."'";
    if (sqlsrv_query($link, $actEstadoVisado))
    {
        echo "1";
    }else{
        echo "2";
    }
}
else if ($_POST["exe"]=="modificarEstadoVisado")
{
    $id_simulacion=$_POST["id_simulacion"];
    $consultarEstadoVisado="SELECT id_simulacion,visado,usuario_visado,fecha_visado FROM simulaciones WHERE id_simulacion='".$id_simulacion."'";
    $queryEstadoVisado=sqlsrv_query($link, $consultarEstadoVisado);
    $resEstadoVisado=sqlsrv_fetch_array($queryEstadoVisado);
    if ($resEstadoVisado["visado"]=="s")
    {
        $estado="n";
    }else{
        $estado="s";
    }
    $actEstadoVisado="UPDATE simulaciones SET visado='".$estado."',usuario_visado='".$_SESSION["S_IDUSUARIO"]."',fecha_visado= CURRENT_TIMESTAMP WHERE id_simulacion='".$id_simulacion."'";
    if (sqlsrv_query($link, $actEstadoVisado))
    {
        echo "1";
    }else{
        echo "2";
    }
}
else if ($_POST["exe"]=="modificarEstadoIncorporacion")
{
    $id_simulacion=$_POST["id_simulacion"];
    $consultarEstadoVisado="SELECT id_simulacion,incorporacion,usuario_incorporado,fecha_incorporado FROM simulaciones WHERE id_simulacion='".$id_simulacion."'";
    $queryEstadoVisado=sqlsrv_query($link, $consultarEstadoVisado);
    $resEstadoVisado=sqlsrv_fetch_array($queryEstadoVisado);
    if ($resEstadoVisado["incorporacion"]=="s")
    {
        $estado="n";
    }else{
        $estado="s";
    }
    $actEstadoVisado="UPDATE simulaciones SET incorporacion='".$estado."',usuario_incorporado='".$_SESSION["S_IDUSUARIO"]."',fecha_incorporado= CURRENT_TIMESTAMP WHERE id_simulacion='".$id_simulacion."'";
    if (sqlsrv_query($link, $actEstadoVisado))
    {
        echo "1";
    }else{
        echo "2";
    }
}
else if ($_POST["exe"]=="consultarInformacionCredito")
{
  $data=array();	
  $variable=0;
  $idSimulacion=$_POST["idSimulacion"];
  $queryInformacionCredito=sqlsrv_query($link, "SELECT a.telemercadeo,a.id_oficina,a.id_comercial,a.nivel_contratacion,a.institucion,a.pagaduria,c.celular,c.direccion, c.tel_residencia as telefono,c.email as mail,a.meses_antes_65,a.fecha_nacimiento,a.cedula,case when c.asesor is null then 'n' else 's' end as validacion,a.nombre,c.nombre1,c.nombre2,c.apellido1,c.apellido2 
  FROM simulaciones a 
  left join empleados b on a.cedula=b.cedula 
  LEFT JOIN solicitud c on c.id_simulacion=a.id_simulacion where a.id_simulacion='".$idSimulacion."'");



  $resInformacionCredito=sqlsrv_fetch_array($queryInformacionCredito);

  if ($resInformacionCredito["nombre1"] != '' && $resInformacionCredito["nombre1"] !== null){
    $data["primer_nombre"]=trim($resInformacionCredito["nombre1"]);
    $data["segundo_nombre"]=trim($resInformacionCredito["nombre2"]);
    $data["primer_apellido"]=trim($resInformacionCredito["apellido1"]);
    $data["segundo_apellido"]=trim($resInformacionCredito["apellido2"]);
  }else { 
    $data["primer_nombre"]=trim($resInformacionCredito["nombre"]);
    $data["segundo_nombre"]="";
    $data["primer_apellido"]="";
    $data["segundo_apellido"]="";
  }

  $data["cedula"]=$resInformacionCredito["cedula"];
  $data["meses_antes_65"]=$resInformacionCredito["meses_antes_65"];
  $data["institucion"]=$resInformacionCredito["institucion"];
  $data["celular"]=$resInformacionCredito["celular"];
  $data["telefono"]=$resInformacionCredito["telefono"];
  $data["fecha_nacimiento"]=$resInformacionCredito["fecha_nacimiento"];
  $data["mail"]=$resInformacionCredito["mail"];
  $data["direccion"]=$resInformacionCredito["direccion"];
  $data["pagaduria"]=$resInformacionCredito["pagaduria"];
  $data["nivel_contratacion"]=$resInformacionCredito["nivel_contratacion"];

  $consultarComercialesOficina="SELECT a.* FROM usuarios a left join oficinas_usuarios b on a.id_usuario=b.id_usuario where b.id_oficina='".$resInformacionCredito["id_oficina"]."' AND a.tipo <> 'MASTER' AND a.tipo = 'COMERCIAL' AND a.estado = '1'  order by nombre, apellido, id_usuario";

  $opt="";
    
  $rs1 = sqlsrv_query($link, $consultarComercialesOficina);
	
	while ($fila1 = sqlsrv_fetch_array($rs1))
	{
		if ($fila1["id_usuario"] == $resInformacionCredito["id_comercial"])
			$selected_comercial = " selected";
		else
			$selected_comercial = "";
		
		$opt.= "<option value=\"".$fila1["id_usuario"]."\"".$selected_comercial.">".($fila1["nombre"])." ".($fila1["apellido"])."</option>\n";
	}
  
  
    $data["opciones_comerciales"]=$opt;
    $data["telemercadeo"]=$resInformacionCredito["telemercadeo"];
    
  
  echo json_encode($data);
}
else if ($_POST["exe"]=="cambiarDatosCredito")
{
    $idSimulacionCambioDatos=$_POST["idSimulacionCambioDatos"];
    $primerNombreCambioDatos=$_POST["primerNombreCambioDatos"];
    $segundoNombreCambioDatos=$_POST["segundoNombreCambioDatos"];
    $primerApellidoCambioDatos=$_POST["primerApellidoCambioDatos"];
    $segundoApellidoCambioDatos=$_POST["segundoApellidoCambioDatos"];
    $celularCambioDatos=$_POST["celularCambioDatos"];
    $telefonoCambioDatos=$_POST["telefonoCambioDatos"];
    $cedulaCambioDatos=$_POST["cedulaCambioDatos"];
    $institucionCambioDatos=$_POST["institucionCambioDatos"];
    $mesesAntesLimiteCambioDatos=$_POST["mesesAntesLimiteCambioDatos"];
    $direccionCambioDatos=$_POST["direccionCambioDatos"];
    $emailCambioDatos=$_POST["emailCambioDatos"];
    $fechaNacimientoCambioDatos=$_POST["fechaNacimientoCambioDatos"];
    $pagaduriaCambioDatos=$_POST["pagaduriaCambioDatos"];
    $nivelContratacionCambioDatos=$_POST["nivelContratacionCambioDatos"];
    $comercialCambioDatos=$_POST["comercialCambioDatos"];
    $telemercadeoCambioDatos=$_POST["telemercadeoChecked"];
    $val1=0;
    $val2=0;
    $val3=0;
    
    $consulta_select_simulaciones=sqlsrv_query($link, "SELECT s.id_simulacion, s.id_comercial, s.telemercadeo, s.nivel_contratacion, s.cedula, s.nombre, s.pagaduria, s.institucion, s.fecha_nacimiento, s.meses_antes_65, s.telefono, e.mail, e.nombre, e.pagaduria, e.institucion, e.cedula, e.direccion, e.telefono, e.fecha_nacimiento, so.nombre1, so.apellido2, so.cedula, so.fecha_nacimiento, so.tel_residencia, so.celular, so.email, so.nombre_empresa, so.direccion
    FROM simulaciones s 
    LEFT JOIN empleados e ON e.cedula = s.cedula 
    LEFT JOIN solicitud so ON so.id_simulacion = s.id_simulacion
    WHERE s.id_simulacion ='".$idSimulacionCambioDatos."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

    if(sqlsrv_num_rows($consulta_select_simulaciones) > 0 ){
        $res_select_simulaciones=sqlsrv_fetch_array($consulta_select_simulaciones);

        $data_Experian = array(
            "id_comercial" => $res_select_simulaciones["id_comercial"],
            "telemercadeo" => $res_select_simulaciones["telemercadeo"],
            "nivel_contratacion" => $res_select_simulaciones["nivel_contratacion"],
            "cedula" => $res_select_simulaciones["cedula"],
            "nombre" => $res_select_simulaciones["nombre"],
            "pagaduria" => $res_select_simulaciones["pagaduria"],
            "institucion" => $res_select_simulaciones["institucion"],
            "fecha_nacimiento" => $res_select_simulaciones["fecha_nacimiento"],
            "meses_antes_65" => $res_select_simulaciones["meses_antes_65"],
            "telefono" => $res_select_simulaciones["telefono"],
            "mail" => $res_select_simulaciones["mail"],
            "direccion" => $res_select_simulaciones["direccion"],
            "nombre1" => $res_select_simulaciones["nombre1"],
            "apellido2" => $res_select_simulaciones["apellido2"],
            "tel_residencia" => $res_select_simulaciones["tel_residencia"],
            "celular" => $res_select_simulaciones["celular"],
            "nombre_empresa" => $res_select_simulaciones["nombre_empresa"]
        );

        $jsondatos=json_encode($data_Experian);

        $consulta_update_datosSimulaciones="UPDATE simulaciones SET id_comercial='".$comercialCambioDatos."',telemercadeo='".$telemercadeoCambioDatos."',nivel_contratacion='".$nivelContratacionCambioDatos."',nombre='".($primerApellidoCambioDatos." ".$segundoApellidoCambioDatos." ".$primerNombreCambioDatos." ".$segundoNombreCambioDatos)."',pagaduria='".$pagaduriaCambioDatos."', institucion='".$institucionCambioDatos."',fecha_nacimiento='".$fechaNacimientoCambioDatos."',meses_antes_65='".$mesesAntesLimiteCambioDatos."',telefono='".$telefonoCambioDatos."' WHERE id_simulacion='".$idSimulacionCambioDatos."'";
        if (sqlsrv_query($link, $consulta_update_datosSimulaciones))
        {
            $val1=0;
        }else{
            $val1=1;
        }
        $consulta_update_datosEmpleados="UPDATE empleados SET mail='".$emailCambioDatos."',nombre='".($primerApellidoCambioDatos." ".$segundoApellidoCambioDatos." ".$primerNombreCambioDatos." ".$segundoNombreCambioDatos)."',pagaduria='".$pagaduriaCambioDatos."',institucion='".$institucionCambioDatos."',cedula='".$cedulaCambioDatos."',direccion='".$direccionCambioDatos."',telefono='".$telefonoCambioDatos."',fecha_nacimiento='".$fechaNacimientoCambioDatos."' WHERE cedula='".$res_select_simulaciones["cedula"]."'";
        if (sqlsrv_query($link, $consulta_update_datosEmpleados))
        {
            $val2=0;
        }else{
            $val2=1;
        }
        $consulta_update_datosSolicitud="UPDATE solicitud SET nombre1='".$primerNombreCambioDatos."',nombre2='".$segundoNombreCambioDatos."',apellido1='".$primerApellidoCambioDatos."',apellido2='".$segundoApellidoCambioDatos."',cedula='".$cedulaCambioDatos."',fecha_nacimiento='".$fechaNacimientoCambioDatos."',tel_residencia='".$telefonoCambioDatos."',celular='".$celularCambioDatos."',email='".$emailCambioDatos."',nombre_empresa='".$institucionCambioDatos."',direccion='".$direccionCambioDatos."' WHERE id_simulacion='".$idSimulacionCambioDatos."'";
        if (sqlsrv_query($link, $consulta_update_datosSolicitud))
        {
            $val3=0;
        }else{
            $val3=1;
        }

        if ($val1==0 || $val2==0 || $val3==0)
        {
            $consulta_insert_datos_clientes="INSERT INTO log_datos_clientes (id_simulacion,data,fecha,id_usuario) values (".$res_select_simulaciones["id_simulacion"].",'".$jsondatos."', CURRENT_TIMESTAMP,".$_SESSION["S_IDUSUARIO"].")";

            sqlsrv_query($link, $consulta_insert_datos_clientes);
            echo "1";
        }else{
            echo "2";
        }
    }else{
        echo 3;
    }
}
?>