<?php include ('../functions.php'); ?>
<?php

$link = conectar_utf();
if ($_POST["exe"]=="consultarHistorialPagoComisiones") 
{
	$id_simulacion=$_POST["id_simulacion"];
	$data=array();	
	$consultarPagoComisiones = "SELECT CASE WHEN a.pagado='s' THEN 'SI' ELSE 'NO' END AS comision_pagado, a.*,concat(b.nombre,' ',b.apellido) as nombre_usuario FROM pago_comisiones a LEFT JOIN usuarios b ON a.id_usuario=b.id_usuario WHERE a.id_simulacion='".$id_simulacion."'";
	$rs = sqlsrv_query($link, $consultarPagoComisiones, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


	while ($fila = sqlsrv_fetch_array($rs)) {
		$data[]=array(
			trim("comision_pagado")=>trim($fila["comision_pagado"]),
			trim("nombre_usuario")=>trim($fila["nombre_usuario"]),
			trim("fecha")=>trim($fila["fecha"])
	  	);
	}
	
	$results = array(
		trim("sEcho") => trim("1"),
		trim("iTotalRecords") => trim(count($data)),
		trim("iTotalDisplayRecords") => trim(count($data)),
		trim("aaData") => $data
	);
	echo json_encode($results);
}
else if ($_POST["exe"]=="consultarOficinasUsuarios") 
{
	$idUsuario=$_POST["idUsuario"];
	$data=array();	
	$consultarOficinas="SELECT * FROM oficinas";
	$rs = sqlsrv_query($link,  $consultarOficinas);
	$oficinas="";
	while ($fila = sqlsrv_fetch_array($rs)) {

		$oficinas.=$fila["nombre"];

		$consultarOficinaUsuario="SELECT id_oficina, case when id_zona is null then '0' else id_zona end as id_zona FROM oficinas_usuarios WHERE id_oficina='".$fila["id_oficina"]."' and id_usuario='".$idUsuario."'";

		//echo $consultarOficinaUsuario;
		$queryOficinaUsuario=sqlsrv_query($link,  $consultarOficinaUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

		if (sqlsrv_num_rows($queryOficinaUsuario)>0) {			
			$seleccOficinaUsuario='<input type="checkbox" name="'.$fila["id_oficina"].'" checked value="1">';
		}else{
			$seleccOficinaUsuario='<input type="checkbox" name="'.$fila["id_oficina"].'" value="1">';
		}

		$resOficinaUsuario=sqlsrv_fetch_array($queryOficinaUsuario);			
		$zonas='<select id="zona_oficina" name="'.$fila["id_oficina"].'">';
		$consultarOficinas="SELECT * FROM zonas";

		if ($resOficinaUsuario["id_zona"]=="0") {		
			$rs1 = sqlsrv_query($link,  $consultarOficinas);
			$zonas.='<option selected value=""></option>';
			
			while ($fila1 = sqlsrv_fetch_array($rs1)) {
				$zonas.="<option value='".$fila1["id_zona"]."'>".$fila1["nombre"]."</option>";
			}				
		}else{
			$rs1 = sqlsrv_query($link,  $consultarOficinas);
			$zonas.='<option value=""></option>';
			while ($fila1 = sqlsrv_fetch_array($rs1)) {			
				if ($fila1["id_zona"] == $resOficinaUsuario["id_zona"]) {
					$selected_zona = "selected";
				} else {
					$selected_zona = "";
				}
				$zonas.="<option value='".$fila1["id_zona"]."' ".$selected_zona.">".$fila1["nombre"]."</option>";
			}			
		}

		$zonas.='</select>';
	
		$data[]=array(
			trim("oficina")=>trim($fila["nombre"]),
			trim("selecc_oficina")=>trim($seleccOficinaUsuario),
			trim("zonas")=>trim($zonas),
			trim("id_oficina")=>trim($fila["id_oficina"])
		
		);
	}

	$results = array(
		trim("sEcho") => trim("1"),
		trim("iTotalRecords") => trim(count($data)),
		trim("iTotalDisplayRecords") => trim(count($data)),
		trim("aaData") => $data
	);

	echo json_encode($results); 
}
else if ($_POST["exe"]=="consultarUsuarios")
{
	$tipo_consulta=$_POST["tipo_consulta"];
	$data=array();	
	$queryDB = "select * from usuarios where tipo <> 'MASTER'";	
	if ($tipo_consulta=="agenda") {
		$queryDB.=" AND agenda='s'";
	}

	if ($_SESSION["S_TIPO"] != "ADMINISTRADOR") {
		$queryDB .= " AND tipo <> 'ADMINISTRADOR'";
		$queryDB_count .= " AND tipo <> 'ADMINISTRADOR'";
	}

	$rs = sqlsrv_query($link,  $queryDB);
	while ($fila = sqlsrv_fetch_array($rs)) {
		$consOficinasUsuario = ("SELECT * FROM oficinas a LEFT JOIN oficinas_usuarios b ON a.id_oficina=b.id_oficina WHERE b.id_usuario='".$fila["id_usuario"]."'" )  
		;
		$queryOficinaUsuario=sqlsrv_query($link,  $consOficinasUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if ($queryOficinaUsuario == false) {
			if( ($errors = sqlsrv_errors() ) != null) {
				foreach( $errors as $error ) {
					echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
					echo "code: ".$error[ 'code']."<br />";
					echo "message: ".$error[ 'message']."<br />";
				}
			}
		}
		$cantidadOficinas=sqlsrv_num_rows($queryOficinaUsuario);
		$oficinas="";
		if ($cantidadOficinas>0) {
			$cont=0;
			while ($resOficinasUsuario=sqlsrv_fetch_array($queryOficinaUsuario)) {
				$cont++;
				$oficinas.=$resOficinasUsuario["nombre"];
				if ($cont!=$cantidadOficinas){
					$oficinas.=",";
				}
			}
		}
		
		switch ($fila["subtipo"]) {
			case 'ANALISTA_REFERENCIA': $fila["subtipo"] = "ANALISTA REFERENCIACION"; break;

			case 'ANALISTA_GEST_COM': $fila["subtipo"] = "ANALISTA GESTION COMERCIAL"; break;
			
			case 'ANALISTA_VEN_CARTERA': $fila["subtipo"] = "ANALISTA VENTA CARTERA"; break;

			case 'ANALISTA_BD': $fila["subtipo"] = "ANALISTA BASE DE DATOS"; break;

			case 'COORD_PROSPECCION': $fila["subtipo"] = "COORDINADOR PROSPECCION"; break;

			case 'COORD_VISADO': $fila["subtipo"] = "COORDINADOR VISADO"; break;

			case 'COORD_CREDITO': $fila["subtipo"] = "COORDINADOR CREDITO"; break;

			case 'GERENTECOMERCIAL': $fila["tipo"] = "GERENTE REGIONAL"; break;

			case 'DIRECTOROFICINA': $fila["tipo"] = "DIRECTOR OFICINA"; break;

			case 'CARTERA': $fila["tipo"] = "DIRECTOR DE CARTERA"; break;

			case 'OPERACIONES': $fila["tipo"] = "DIRECTOR DE OPERACIONES"; break;			
		}

		switch ($fila["estado"]) {
			case '1':	$estado = "ACTIVO"; break;
			case '0':	$estado = "INACTIVO"; break;
		}
		$tipoUsuario=$fila["tipo"];
		if ($fila["subtipo"]) { $tipoUsuario.= "/".$fila["subtipo"]; }

		$data[]=array(
		trim("nombre_usuario")=>trim('<a href="usuarios_actualizar.php?id_usuario='.$fila["id_usuario"].'">'.($fila["nombre"]." ".$fila["apellido"]).'</a>'),
		trim("nombre_usuario2")=>trim(($fila["nombre"]." ".$fila["apellido"])),
		trim("login")=>trim($fila["login"]),
		trim("email")=>trim($fila["email"]),
		trim("tipo")=>trim($tipoUsuario),
		trim("telefono")=>trim($fila["telefono"]),
		trim("cargo")=>trim($fila["cargo"]),
		trim("sector")=>trim($fila["sector"]),
		trim("oficinas")=>trim('<a href="#" name="'.$fila["id_usuario"].'" id="btnAsociarOficinasUsuarios">Asociar</a>'),
		trim("Coordinar")=>trim('<a href="#" name="'.$fila["id_usuario"].'" id="btnCoordinarUsuarios">Asociar</a>'),
		trim("fecha_creacion")=>trim($fila["fecha_creacion"]),
		trim("fecha_ultimo_acceso")=>trim($fila["fecha_ultimo_acceso"]),
		trim("fecha_inactivacion")=>trim($fila["fecha_inactivacion"]),
		trim("estado")=>trim($estado),
		trim("id_usuario")=>trim($fila["id_usuario"]),
		trim("nombre_oficinas")=>trim($oficinas),
		trim("selecc_usuario")=>trim('<input type="checkbox" name="'.$fila["id_usuario"].'" value="1">')
	  );
	}
	$results = array(
        trim("sEcho") => trim("1"),
        trim("iTotalRecords") => trim(count($data)),
        trim("iTotalDisplayRecords") => trim(count($data)),
        trim("aaData") => $data
      );
    
	

    
    echo json_encode($results); 
}
else if ($_POST["exe"]=="manejoUsuariosFDC")
{
	$data=array();	
    $consultarUsuariosInicio = "SELECT a.cantidad_creditos,a.id_usuario,a.login,a.nombre,a.apellido,CASE WHEN a.disponible='s' THEN 'DISPONIBLE' WHEN a.disponible='n' THEN 'NO DISPONIBLE' WHEN a.disponible='r' THEN 'NO DISPONIBLE' WHEN a.disponible='g' THEN 'EN GESTION' ELSE 'NO DISPONIBLE' END AS estado_usuario,a.disponible AS estado
			FROM
			usuarios a
			WHERE a.subtipo='ANALISTA_CREDITO' and a.estado=1 ";

	if($_POST["unidad_negocio_fdc"] == 1){ //Fianti
		$consultarUsuariosInicio .= "AND (SELECT descripcion FROM definicion_tipos where id_tipo=4 and descripcion=a.id_usuario) IS NOT NULL";
	}else{ //Kredit
		$consultarUsuariosInicio .= "AND (SELECT descripcion FROM definicion_tipos where id_tipo=4 and descripcion=a.id_usuario) IS NULL";
	}

	

    $rs =sqlsrv_query($link, $consultarUsuariosInicio);
	
	while ($fila = sqlsrv_fetch_array($rs)){
		$consultarCantidadCreditos="SELECT count(id) as cantidad FROM simulaciones_fdc WHERE (id_subestado not in ('28','53') OR id_subestado IS NULL) AND estado=4 AND FORMAT(fecha_creacion,'Y-m-d')= FORMAT(CURRENT_TIMESTAMP,'Y-m-d') AND id_usuario_creacion='".$fila["id_usuario"]."'";

		$queryCantidadCreditos=sqlsrv_query($link, $consultarCantidadCreditos);
		$resCantidadCreditos=sqlsrv_fetch_array($queryCantidadCreditos);
					
		$consultarCantidadCreditosAsignados="SELECT count(id) as cantidad FROM simulaciones_fdc WHERE vigente='s' AND estado=2 AND id_usuario_asignacion='".$fila["id_usuario"]."'";
		$queryCantidadCreditosAsignados=sqlsrv_query($link,  $consultarCantidadCreditosAsignados);
		$resCantidadCreditosAsignados=sqlsrv_fetch_array($queryCantidadCreditosAsignados);
		$selectDisponible="";
		$btnNoDispTerminar="";
		$btnNoDispReasignar="";
		$selectUnidadNegocio="";

		$queryUnidadNegocioUsuario=sqlsrv_query($link,  "SELECT * FROM definicion_tipos where id_tipo=4 and descripcion='".$fila["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($queryUnidadNegocioUsuario)>0){
			
			$selectUnidadNegocio='<select id="unidad_negocio_usuario_fdc">
				<option value="0">KREDIT</option>
				<option value="1" selected>FIANTI</option>
				</select>';			
		}else{
			$selectUnidadNegocio='<select id="unidad_negocio_usuario_fdc">
				<option value="0" selected>KREDIT</option>
				<option value="1">FIANTI</option>
				</select>';
		}
		
		if ($resCantidadCreditosAsignados["cantidad"]>0){
		
			$btnNoDispTerminar='<a id="btnNoDispTerminar" name="'.$fila["id_usuario"].'"><img src="../images/dot_azul.png" title="No Disponible Terminar Bandeja"></a>';
			$btnNoDispReasignar='<a id="btnNoDispReasignar" name="'.$fila["id_usuario"].'"><img src="../images/dot_rojo.png" title="Ni Disponible Reasignar"></a>';
		}else{
			$btnNoDispTerminar='';
			$btnNoDispReasignar='';
		}
		
		if ($fila["estado"]=="n" || $fila["estado"]=="t"){
			
			$selectDisponible='<select id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
				<option value="0">DISPONIBLE</option>
				<option value="1" selected>NO DISPONIBLE</option>
			</select>';			
		}else{
			if ($resCantidadCreditosAsignados["cantidad"]>0){
				
				$selectDisponible='<select disabled id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
					<option value="0" selected>DISPONIBLE</option>
					<option value="1">NO DISPONIBLE</option>
				</select>';
			}else{
				
				$selectDisponible='<select id="estado_actual_usuario" name="'.$fila["id_usuario"].'" style=" background-color:#EAF1DD">
					<option value="0" selected>DISPONIBLE</option>
					<option value="1">NO DISPONIBLE</option>
				</select>';
			}	
			
		}
			
		$data[]=array(trim("nombre")=>trim(($fila["nombre"]." ".$fila["apellido"])),
		trim("estado")=>trim($fila["estado_usuario"]),
		trim("estudios_realizados")=>trim("<a href='#' id='btnModalCreditosTerminadosAnalista' name='".$fila["id_usuario"]."' >".$resCantidadCreditos["cantidad"]."</a>"),
		trim("estudios_asignado")=>trim("<a href='#' id='btnModalCreditosAnalista' name='".$fila["id_usuario"]."' >".$resCantidadCreditosAsignados["cantidad"]."</a>"),
		trim("estudios_total")=>trim("<a href='#' id='btnModalCreditosTotalAnalista' name='".$fila["id_usuario"]."' >".($resCantidadCreditosAsignados["cantidad"]+$resCantidadCreditos["cantidad"])."</a>"),
		trim("cantidad_minimo")=>trim('<input type="text" name="'.$fila["id_usuario"].'" id="cantidadMinimaUsuario" value="'.$fila["cantidad_creditos"].'">'),
		trim("unidad_negocio")=>trim($selectUnidadNegocio),
		trim("no_disp_terminar")=>trim($btnNoDispTerminar),
		trim("no_disp_reasignar")=>trim($btnNoDispReasignar),
		trim("selecc_estado")=>trim($selectDisponible)
	  );
	}
	$results = array(
        trim("sEcho") => trim("1"),
        trim("iTotalRecords") => trim(count($data)),
        trim("iTotalDisplayRecords") => trim(count($data)),
        trim("aaData") => $data
    );
    
    echo json_encode($results); 
}
else if ($_POST["exe"]=="consultarBandejaFDC")
{
    $data=array();
    
	$queryDB = "SELECT 
	CASE WHEN (us.freelance=1 and us.outsourcing=0) then 'FREELANCE' WHEN (us.freelance=0 and us.outsourcing=1) then 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,
	sfd.estado as estado_sfd,si.id_analista_riesgo_operativo,si.id_simulacion, si.cedula, si.empleado_manual, si.nombre, si.pagaduria, 
	us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, si.opcion_credito, si.opcion_desembolso_cli, si.opcion_desembolso_ccc, 
	si.opcion_desembolso_cmp, si.opcion_desembolso_cso, si.retanqueo_total, si.valor_credito, si.decision, si.frente_al_cliente, 
	si.fecha_radicado,case when sfd.id_usuario_asignacion is null then '0' when sfd.id_usuario_asignacion=197 then '0' else sfd.id_usuario_asignacion end as id_usuario_asignacion,si.id_oficina 
	from simulaciones si 
	INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
	INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
	LEFT JOIN simulaciones_fdc sfd ON sfd.id_simulacion=si.id_simulacion where sfd.estado in (1,2,5,3) and sfd.vigente='s'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";		
	}

	if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
		if ($_SESSION["S_SUBTIPO"] == "PLANTA") {
			$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		}

		if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS") {
            $queryDB .= " AND si.telemercadeo in ('0','1')";
        }	
		
		if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO") {
            $queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
        }	
		
		if ($_SESSION["S_SUBTIPO"] == "EXTERNOS") {
			$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
		}

		if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO") {
			$queryDB .= " AND si.telemercadeo = '1'";
		}
	}
	
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM") {
		$queryDB .= " AND (sfd.id_usuario_asignacion = '".$_SESSION["S_IDUSUARIO"]."')";		
	}
		
	if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") {
		$queryDB .= " AND (sfd.id_usuario_asignacion = '".$_SESSION["S_IDUSUARIO"]."')";
	}
	
	$consultarOficinasAnalistas = "SELECT * FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
	$queryOficinaAnalista =sqlsrv_query($link,  $consultarOficinasAnalistas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if (sqlsrv_num_rows($queryOficinaAnalista) > 0) {
		$queryDB .= " AND si.id_oficina in (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	}

	$queryDB .= " order by si.id_simulacion ASC";

    $rs=sqlsrv_query($link,  $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$consulta="";
	$consultarOficinasAnalistas = "";
    while ($fila = sqlsrv_fetch_array($rs)) {
	    $reprocesos="";
		$contarReprocesos="SELECT id_simulacionsubestado,id_subestado FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."'";
    	$queryReprocesos=sqlsrv_query($link,  $contarReprocesos);
    	if (sqlsrv_num_rows($queryReprocesos) == 0) {
		    $reprocesos = "NUEVO";
        }else{
		    $contarReprocesos2=sqlsrv_query($link,  $contarReprocesos." and id_subestado in (70,72)");
			if (sqlsrv_num_rows($contarReprocesos2)==1) {
				$reprocesos="REPROCESO 1";
			}else if (sqlsrv_num_rows($contarReprocesos2)>1) {
				$reprocesos="REPROCESOS";
			}else{
				$reprocesos="NUEVO";
			}
		}
		/*
        $tr_class = "";
						
		if (($j % 2) == 0) {
			$tr_class = " style='background-color:#F1F1F1;'";
		}
						
		switch ($fila["opcion_credito"]) {
			case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"]; break;
			case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
			case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
			case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
		}
						
		if ($fila["opcion_credito"] == "CLI") {
            $fila["retanqueo_total"] = 0;
        }

		  if ($fila["frente_al_cliente"] == "SI") { 
                $imagen_frente='<img src="../images/frentecliente.png" title="Frente al Cliente">';
            }else{
                    $imagen_frente='';
            }
		*/
			$tiempo_prospeccion_letras = "";

            $consultarTiempoRadicado=sqlsrv_query($link,  "SELECT * FROM simulaciones_fdc WHERE id=(SELECT max(id) FROM simulaciones_fdc where estado=5 and id_simulacion='".$fila["id_simulacion"]."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($consultarTiempoRadicado)>0){
				$resTiempoRadicado=sqlsrv_fetch_array($consultarTiempoRadicado);
				$tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($resTiempoRadicado["fecha_creacion"]);
				$fecha_prospeccion = $resTiempoRadicado["fecha_creacion"];
			}else{
				$tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($fila["fecha_radicado"]);
				$fecha_prospeccion = $fila["fecha_radicado"];
			}

			$tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);						
			$tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);					
			$tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
			if ($tiempo_prospeccion_horas) {
			    $tiempo_prospeccion_letras .= $tiempo_prospeccion_horas."h ";
            }		
			$tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos."m ";
			$tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos."s";

			//$id_analista_riesgo_operativo=$fila["id_analista_riesgo_operativo"];
			$id_analista_riesgo_operativo=$fila["id_usuario_asignacion"];
      

            if ($fila["id_usuario_asignacion"]==0 || $fila["estado_sfd"]<>2) {
                            
            }else{
                $btnDetener='<td align="center"><a id="btnDesasignar" name="'.$fila["id_simulacion"].'"><img src="../images/dot_rojo.png" title="Desasignar"></a></td>';
            }

		$analistas='';
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO") {
			$analistas = '<select disabled id="usuarios_riesgo_operativo" name="'.$fila["id_simulacion"].'" style=" background-color:#EAF1DD">';
		}else{ 
			$analistas='<select id="usuarios_riesgo_operativo" name="'.$fila["id_simulacion"].'" style=" background-color:#EAF1DD">';
		}
           
            $queryOficinaNegocioEspecial=sqlsrv_query($link,  "SELECT id FROM definicion_tipos where id_tipo=3 and descripcion=(select id_unidad_negocio from simulaciones where id_simulacion='".$fila["id_simulacion"]."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($queryOficinaNegocioEspecial) > 0) {
                    $consultarUsuariosInicio = "(SELECT a.* FROM usuarios a WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g'))";
                }else{
                    $consultarUsuariosInicio = "(SELECT a.* FROM usuarios a WHERE a.id_usuario not IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g'))";
                }
                

                if ($id_analista_riesgo_operativo==0) {
                    
                }else{
					if ($fila["estado_sfd"]<>1) {
						$consultarUsuariosInicio.=" UNION (SELECT * FROM usuarios where id_usuario='".$id_analista_riesgo_operativo."')";
					}
                    
                }
                    

                    $rs1 = sqlsrv_query($link, $consultarUsuariosInicio , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    if (sqlsrv_num_rows($rs1)<=0)
                    {
                        
                        $analistas.='<option value="">SIN ANALISTAS DISPONIBLES</option>';
                    }else{
                        
                        $analistas.='<option value=""></option>';
						$nombreUsuarioSeleccionado="";
                        while ($fila1 = sqlsrv_fetch_array($rs1))
                        {
                        
                            if ($fila1["id_usuario"] == $id_analista_riesgo_operativo)
                            {
                                $selected_ciudad = " selected";
								$nombreUsuarioSeleccionado=$fila1["nombre"];
                            }
                            else
                            {
                                $selected_ciudad = "";
                            }
                            $analistas.="<option value='".$fila1["id_usuario"]."'".$selected_ciudad.">".$fila1["nombre"]." ".$fila1["apellido"]."</option>";
                        }
                    }
                    $analistas.='</select>';
					
						$data[] = array("id_simulacion" => $fila["id_simulacion"],
							"cedula"=>'<a href="simulador.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc">'.$fila["cedula"].'</a>',
							"nombre"=>$fila["nombre"],
							"pagaduria"=>$fila["pagaduria"],
							"comercial"=>($fila["nombre_comercial"]." ".$fila["apellido"]),
							"oficina"=>$fila["oficina"],
							"fecha_radicado"=>$fecha_prospeccion,
							"tiempo_prospeccion"=>$tiempo_prospeccion_letras,
							"estado"=>$reprocesos,
							"frente_cliente"=>$imagen_frente,
							"adjuntos"=>'<a href="adjuntos.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc&page='.$_REQUEST["page"].'"><img src="../images/adjuntar.png" title="Adjuntos"></a>',
							"stop"=>$btnDetener,
							"hist_est"=>"<div class='badge-success' id='btnModalEstados' name='".$fila["id_simulacion"]."' type='button' class='open-modal' data-open='modal1'><center><img src='../images/solicitud.png' title='Estados'></center></div>",
							"hist_proc"=>"<div class='badge-success' id='btnModalHistorial' name='".$fila["id_simulacion"]."' type='button' class='open-modal' data-open='modal1'><center><img src='../images/proceso.png' title='Historial'></center></div>",
							"analista_asignado"=>$analistas,
							"nombre_usuario_asignado"=>$nombreUsuarioSeleccionado,
							"consulta" => $consultarOficinasAnalistas,
							"tipo_comercial2"=>$fila["tipo_comercial2"]
						);
					
            
						
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data,
		"consulta"=>$consultarOficinasAnalistas,
		"consulta2"=>$queryDB,
		"session" => $_SESSION["S_IDUSUARIO"],
      );
    
      echo json_encode($results); 
}
else if ($_POST["exe"]=="consultarCreditosAsignadoUsuario")
{
	$idUsuario=$_POST["idUsuario"];
	$consultarCreditosAsignados="SELECT a.id_simulacion,a.cedula,a.nombre,a.fecha_radicado FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE b.estado=2 and b.vigente='s' and b.id_usuario_asignacion='".$idUsuario."'";


	$queryCreditosAsignados=sqlsrv_query($link, $consultarCreditosAsignados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


	$tabla="<center><table class='tab3'><thead><tr><th>Id Simulacion</th><th>Cedula</th><th>Nombre</th><th>F. Radicado</th><th>Reproceso</th></tr></thead>";
	while ($resCreditosAsignados=sqlsrv_fetch_array($queryCreditosAsignados))
	{
		$reprocesos="";
		$contarReprocesos="SELECT id_simulacionsubestado,id_subestado FROM simulaciones_subestados WHERE id_simulacion='".$resCreditosAsignados["id_simulacion"]."'";
		$queryReprocesos=sqlsrv_query($link, $contarReprocesos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryReprocesos)==0)
		{
			$reprocesos="NUEVO";
		}else{
			$contarReprocesos2=sqlsrv_query($link, $contarReprocesos." and id_subestado in (70,72)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($contarReprocesos2)==1)
			{
				$reprocesos="REPROCESO 1";
			}else if (sqlsrv_num_rows($contarReprocesos2)>1){
				$reprocesos="REPROCESOS";
			}else{
				$reprocesos="NUEVO";
			}
		}
		$fecha_prospeccion="";
		$consultarTiempoRadicado=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id=(SELECT max(id) FROM simulaciones_fdc where estado=5 and id_simulacion='".$resCreditosAsignados["id_simulacion"]."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($consultarTiempoRadicado)>0){
			$resTiempoRadicado=sqlsrv_fetch_array($consultarTiempoRadicado);
			$fecha_prospeccion = $resTiempoRadicado["fecha_creacion"];
		}else{
			$fecha_prospeccion = $resCreditosAsignados["fecha_radicado"];
		}

		$tabla.="<tr><td>".$resCreditosAsignados["id_simulacion"]."</td><td>".$resCreditosAsignados["cedula"]."</td><td>".$resCreditosAsignados["nombre"]."</td><td>".$fecha_prospeccion."</td><td>".$reprocesos."</td></tr>";
	}
	$tabla.="</table></center>";

	echo $tabla;
}
else if ($_POST["exe"]=="consultarCreditosTerminadosUsuario")
{
	$idUsuario=$_POST["idUsuario"];
	$consultarCreditosAsignados=" SELECT b.nombre,b.cedula,b.fecha_radicado,a.* FROM simulaciones_fdc a LEFT JOIN simulaciones b on a.id_simulacion=b.id_simulacion WHERE (a.id_subestado not in ('28','53') OR a.id_subestado IS NULL) AND a.estado=4 AND FORMAT(a.fecha_creacion,'Y-m-d')= FORMAT(CURRENT_TIMESTAMP,'Y-m-d') AND a.id_usuario_creacion='".$idUsuario."'";


	$queryCreditosAsignados=sqlsrv_query($link, $consultarCreditosAsignados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$tabla="<center><table class='tab3'><thead><tr><th>Id Simulacion</th><th>Cedula</th><th>Nombre</th><th>F. Radicado</th><th>Reproceso</th></tr></thead>";
	while ($resCreditosAsignados=sqlsrv_fetch_array($queryCreditosAsignados))
	{
		$reprocesos="";
		$contarReprocesos="SELECT id_simulacionsubestado,id_subestado FROM simulaciones_subestados WHERE id_simulacion='".$resCreditosAsignados["id_simulacion"]."'";
		$queryReprocesos=sqlsrv_query($link, $contarReprocesos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryReprocesos)==0)
		{
			$reprocesos="NUEVO";
		}else{
			$contarReprocesos2=sqlsrv_query($link, $contarReprocesos." and id_subestado in (70,72)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($contarReprocesos2)==1)
			{
				$reprocesos="REPROCESO 1";
			}else if (sqlsrv_num_rows($contarReprocesos2)>1){
				$reprocesos="REPROCESOS";
			}else{
				$reprocesos="NUEVO";
			}
		}
		$tabla.="<tr><td>".$resCreditosAsignados["id_simulacion"]."</td><td>".$resCreditosAsignados["cedula"]."</td><td>".$resCreditosAsignados["nombre"]."</td><td>".$resCreditosAsignados["fecha_radicado"]."</td><td>".$reprocesos."</td></tr>";
	}
	$tabla.="</table></center>";

	echo $tabla;
}
else if ($_POST["exe"]=="consultarCreditosTotalUsuario")
{
	$idUsuario=$_POST["idUsuario"];
	$consultarCreditosAsignados="(SELECT b.nombre,b.cedula,b.fecha_radicado,'TERMINADO' as descripcion, a.* FROM simulaciones_fdc a LEFT JOIN simulaciones b on a.id_simulacion=b.id_simulacion WHERE (a.id_subestado not in ('28','53') OR a.id_subestado IS NULL) AND a.estado=4 AND FORMAT(a.fecha_creacion,'Y-m-d')=FORMAT(CURRENT_TIMESTAMP,'Y-m-d') AND a.id_usuario_creacion='".$idUsuario."') UNION (SELECT a.nombre,a.cedula,a.fecha_radicado,'ASIGNADO' as descripcion,b.* FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE b.estado=2 and b.vigente='s' and b.id_usuario_asignacion='".$idUsuario."')";
	$queryCreditosAsignados=sqlsrv_query($link, $consultarCreditosAsignados, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$tabla="<center><table class='tab3'><thead><tr><th>Id Simulacion</th><th>Cedula</th><th>Nombre</th><th>F. Radicado</th><th>Reproceso</th><th>Descripcion</th></tr></thead>";
	while ($resCreditosAsignados=sqlsrv_fetch_array($queryCreditosAsignados))
	{
		$reprocesos="";
		$contarReprocesos="SELECT id_simulacionsubestado,id_subestado FROM simulaciones_subestados WHERE id_simulacion='".$resCreditosAsignados["id_simulacion"]."'";
		$queryReprocesos=sqlsrv_query($link, $contarReprocesos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryReprocesos)==0)
		{
			$reprocesos="NUEVO";
		}else{
			$contarReprocesos2=sqlsrv_query($link, $contarReprocesos." and id_subestado in (70,72)");
			if (sqlsrv_num_rows($contarReprocesos2)==1)
			{
				$reprocesos="REPROCESO 1";
			}else if (sqlsrv_num_rows($contarReprocesos2)>1){
				$reprocesos="REPROCESOS";
			}else{
				$reprocesos="NUEVO";
			}
		}
		$tabla.="<tr><td>".$resCreditosAsignados["id_simulacion"]."</td><td>".$resCreditosAsignados["cedula"]."</td><td>".$resCreditosAsignados["nombre"]."</td><td>".$resCreditosAsignados["fecha_radicado"]."</td><td>".$reprocesos."</td><td>".$resCreditosAsignados["descripcion"]."</td></tr>";
	}
	$tabla.="</table></center>";

	echo $tabla;
}
else if ($_POST["exe"]=="consultarUsuariosAsociar") 
{
	$idUsuario=$_POST["idUsuario"];
	$data=array();	
	$consultarUsuarios="SELECT * FROM usuarios WHERE estado=1";
	$rs = sqlsrv_query($link, $consultarUsuarios, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$usuarios="";
	while ($fila = sqlsrv_fetch_array($rs)) {
		$usuarios.=$fila["nombre"]." ".$fila["apellido"];
		$consultarOficinaUsuario="SELECT * FROM coordinacion_usuarios WHERE id_usuario_secundario='".$fila["id_usuario"]."' and id_usuario_principal='".$idUsuario."'";
		$queryOficinaUsuario=sqlsrv_query($link, $consultarOficinaUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryOficinaUsuario)>0) {			
			$seleccOficinaUsuario='<input type="checkbox" name="'.$fila["id_usuario"].'" checked value="1">';
		}else{
			$seleccOficinaUsuario='<input type="checkbox" name="'.$fila["id_usuario"].'" value="1">';
		}

		$data[]=array(
			trim("usuario")=>trim($fila["nombre"]." ".$fila["apellido"]),
			trim("selecc_usuarios")=>trim($seleccOficinaUsuario),
			trim("id_usuario")=>trim($fila["id_usuario"])
		
		);
	}

	$results = array(
		trim("sEcho") => trim("1"),
		trim("iTotalRecords") => trim(count($data)),
		trim("iTotalDisplayRecords") => trim(count($data)),
		trim("aaData") => $data
	);

	echo json_encode($results); 
}
?>