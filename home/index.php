<?php include ('../functions.php'); ?>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="shortcut icon" type="image/x-icon" href="../images/favicon.ico">

	<link rel="STYLESHEET" type="text/css" href="../sty.css?v=2">
	<script src="../jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="../js/superfish.min.js" type="text/javascript"></script>
	<script src="../js/js.js" type="text/javascript"></script>
	<script language="JavaScript" src="../functions.js"></script>
	<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet"/>
	<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
</head>
<?php
clearstatcache();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
if ($_REQUEST["action"] == "ingresar"){
	if ($_REQUEST["login"] && $_REQUEST["password"]){
		
		$link = conectar();

		$query = "SELECT centrales_judicial, reporte_cartera, causales_no_recaudo, anular_firma_digital, visualizar_reportes,habilitar_prospeccion, solicitar_firma, revision_garantias, preprospeccion,usuario_master, disponible,id_usuario, login, tipo, subtipo, sector, nombre, apellido, maxconsdiarias, coordinador, jefe_comercial, solo_lectura, cambio_clave, datediff(day,convert(varchar(10),getdate(),111), convert(varchar(10),fecha_clave,111)) AS dias_cambio_clave from usuarios where estado = '1' AND [login] = '".$_REQUEST["login"]."' AND [password] = '".md5($_REQUEST["password"])."'";
		

		$usuario_valido = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if ($usuario_valido == false) {
			if( ($errors = sqlsrv_errors() ) != null) {
				foreach( $errors as $error ) {
					echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
					echo "code: ".$error[ 'code']."<br />";
					echo "message: ".$error[ 'message']."<br />";
				}
			}
		}
		if (sqlsrv_num_rows($usuario_valido)) {
			$fila = sqlsrv_fetch_array($usuario_valido);
			$mesesCambiaClave = 1;
			$diasCambiaClave = $mesesCambiaClave * 30;

			if(($fila["cambio_clave"] == 0) || ($fila["dias_cambio_clave"]) > $diasCambiaClave){
				if($fila["cambio_clave"] == 0){
					echo "<script>alert('Debe Realizar Inmediatamente el Cambio de Contraseña');</script>";
				}else{
					echo "<script>alert('Han Pasado más de (".$mesesCambiaClave.") Meses desde que cambio su contraseña, Debe Cambiarla.');</script>";
				}
				echo "<script>location.replace('cambiarclave.php?cambiar_clave=1&s_login=".$_REQUEST["login"]."')</script>";
			}else{
				if ($fila["subtipo"]<>"ANALISTA_CREDITO" || $fila["revision_garantias"] == 1 ) {
					$actualizarInicioSesion="UPDATE usuarios SET fecha_ultimo_acceso=CURRENT_TIMESTAMP(),disponible='n' where id_usuario='".$fila["id_usuario"]."'";
				}else{
					if ($fila["disponible"]<>'n') {
						$actualizarInicioSesion="UPDATE usuarios SET fecha_ultimo_acceso = CURRENT_TIMESTAMP where id_usuario = '".$fila["id_usuario"]."'";
					}else{
						$actualizarInicioSesion="UPDATE usuarios SET fecha_ultimo_acceso = CURRENT_TIMESTAMP,disponible = 'n' where id_usuario = '".$fila["id_usuario"]."'";
					}
				}								
				sqlsrv_query($link,$actualizarInicioSesion);	
				$_SESSION["S_IDUSUARIO"] = $fila["id_usuario"];
				$_SESSION["S_MASTERSISTEMA"] = $fila["usuario_master"];
				$_SESSION["S_LOGIN"] = $fila["login"];
				$_SESSION["S_NOMBRE"] = $fila["nombre"]. " ".$fila["apellido"];
				$_SESSION["S_SECTOR"] = $fila["sector"];
				$_SESSION["S_COORDINADOR"] = $fila["coordinador"];
				$_SESSION["S_JEFECOMERCIAL"] = $fila["jefe_comercial"];
				$_SESSION["S_SOLOLECTURA"] = $fila["solo_lectura"];
				$_SESSION["S_DISPONIBLE"] = $fila["disponible"];
				$_SESSION["S_PREPROSPECCION"] = $fila["preprospeccion"];
				$_SESSION["S_REVISION_GARANTIAS"] = $fila["revision_garantias"];
				$_SESSION["S_SOLICITAR_FIRMAS"] = $fila["solicitar_firma"];
				$_SESSION["S_INTELIGENCIA_NEGOCIO"] = $fila["inteligencia_negocios"];
				$_SESSION["S_HABILITAR_PROSPECCION"] = $fila["habilitar_prospeccion"];
				$_SESSION["S_VISUALIZAR_REPORTES"] = $fila["visualizar_reportes"];
				$_SESSION["S_ANULAR_FIRMA_DIGITAL"]= $fila["anular_firma_digital"];
				$_SESSION["S_CAUSALES_NO_RECAUDO"] = $fila["causales_no_recaudo"];
				$_SESSION["S_REPORTE_CARTERA"]= $fila["reporte_cartera"];
				$_SESSION["S_VER_CENTRALES_JUDICIAL"] = $fila["centrales_judicial"];

				if ($fila["tipo"] != "MASTER"){
					$_SESSION["S_TIPO"] = $fila["tipo"];
					$_SESSION["S_SUBTIPO"] = $fila["subtipo"];
					$_SESSION["S_MASTER"] = "0";
				}
				else{
					$_SESSION["S_TIPO"] = "ADMINISTRADOR";
					$_SESSION["S_SUBTIPO"] = "";
					$_SESSION["S_MASTER"] = "1";
				}
				
				$_SESSION["S_MAXCONSDIARIAS"] = $fila["maxconsdiarias"];

				sqlsrv_query( $link,"insert into usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) select '".$usuario_master."', un.id_unidad, 'rootfs', getdate() from unidades_negocio un LEFT JOIN usuarios_unidades uu ON un.id_unidad = uu.id_unidad_negocio AND uu.id_usuario = '".$usuario_master."' where uu.id_usuario IS NULL");
				
				$_SESSION["S_IDUNIDADNEGOCIO"] = "'0'";
				
				$rs1 = sqlsrv_query($link,"select id_unidad_negocio from usuarios_unidades where id_usuario = '".$fila["id_usuario"]."' order by id_unidad_negocio", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($rs1)){
					while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
						$_SESSION["S_IDUNIDADNEGOCIO"] .= ", '".$fila1["id_unidad_negocio"]."'";
				}
				
				$codigo_perfil = $_SESSION["S_TIPO"];
				
				if ($_SESSION["S_SUBTIPO"])
					$codigo_perfil .= "/".$_SESSION["S_SUBTIPO"];
				
				$rs_id_perfil = sqlsrv_query( $link, "select id_perfil from perfiles where codigo = '".$codigo_perfil."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));			
				$id_perfil = sqlsrv_fetch_array($rs_id_perfil);
				
				$_SESSION["S_IDPERFIL"] = $id_perfil["id_perfil"];
				
				$funcionalidades = sqlsrv_query( $link,"select valor from funcionalidades order by codigo", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				$j = 0;				
				while ($fila1 = sqlsrv_fetch_array($funcionalidades)){
					$funcionalidad[$j] = $fila1["valor"];					
					$j++;
				}
				
				$_SESSION["FUNC_ADJUNTOS"] = $funcionalidad[0];
				$_SESSION["FUNC_ADMINISTRATIVOS"] = $funcionalidad[1];
				$_SESSION["FUNC_AGENDA"] = $funcionalidad[2];
				$_SESSION["FUNC_BOLSAINCORPORACION"] = $funcionalidad[3];
				$_SESSION["FUNC_CALIFICACION"] = $funcionalidad[4];
				$_SESSION["FUNC_CARGUEPLANOS"] = $funcionalidad[5];
				$_SESSION["FUNC_FDESEMBOLSO"] = $funcionalidad[6];
				$_SESSION["FUNC_FULLSYSTEM"] = $funcionalidad[7];
				$_SESSION["FUNC_INDICADORES"] = $funcionalidad[8];
				$_SESSION["FUNC_LOGCONSULTAS"] = $funcionalidad[9];
				$_SESSION["FUNC_MAXCONSDIARIAS"] = $funcionalidad[10];
				$_SESSION["FUNC_MUESTRACAMPOS1"] = $funcionalidad[11];
				$_SESSION["FUNC_MUESTRACAMPOS2"] = $funcionalidad[12];
				$_SESSION["FUNC_PENSIONADOS"] = $funcionalidad[13];
				$_SESSION["FUNC_SUBESTADOS"] = $funcionalidad[14];
				$_SESSION["FUNC_TASASCOMBO"] = $funcionalidad[15];
				$_SESSION["FUNC_TASASPLAZO"] = $funcionalidad[16];


				//Las simulaciones de m�s de 1 mes en estudio, viables y que no tengan subestado se desisten autom�ticamente
				//sqlsrv_query("update simulaciones set estado = 'DSS' WHERE estado IN ('EST') AND decision = '".$label_viable."' AND id_subestado IS NULL AND DATE_ADD(fecha_estudio, INTERVAL 1 MONTH) < CURRENT_DATE()", $link);
				
				//Las simulaciones de m�s de tres meses en estudio, viables y que est�n en estado Prospectado o Firmado se desisten autom�ticamente
				//sqlsrv_query("update simulaciones set estado = 'DSS', id_subestado = NULL WHERE estado IN ('EST') AND decision = '".$label_viable."' AND id_subestado IN ('1') AND DATE_ADD(fecha_estudio, INTERVAL 2 MONTH) < CURRENT_DATE()", $link);
				
				sqlsrv_query( $link,"insert into subestados_usuarios (id_subestado, id_usuario) select se.id_subestado, '".$usuario_master."' from subestados se LEFT JOIN subestados_usuarios su ON se.id_subestado = su.id_subestado AND su.id_usuario = '".$usuario_master."' where su.id_usuario IS NULL");
				
				//Inserci�n autom�tica subestado 1 PROSPECTADO a comerciales de planta
				//sqlsrv_query("insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_prospectado."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_prospectado."')", $link);
				
				//Inserci�n autom�tica subestado 1.5 INGRESADO PDTE SOPORTES Y DOCUMENTOS a comerciales y prospeccion de planta
				sqlsrv_query($link,"insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_ingresado_pdte_soportes_documentos."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO'))) OR (tipo IN ('PROSPECCION') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_ingresado_pdte_soportes_documentos."')");
				
				//Inserci�n autom�tica subestado 2 VALIDACION DOCUMENTAL Y PROFORENSE a comerciales y prospeccion de planta
				sqlsrv_query($link,"insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_valid_doc_proforense."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO'))) OR (tipo IN ('PROSPECCION') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_valid_doc_proforense."')");
				
				//Inserci�n autom�tica subestado 3 RADICADO SOPORTES COMPLETOS a comerciales y prospeccion de planta
				sqlsrv_query($link,"insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_soportes_completos."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO'))) OR (tipo IN ('PROSPECCION') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_soportes_completos."')");
				
				//Inserci�n autom�tica subestado 2 FIRMADO (Anterior subestado) a comerciales de planta
				//sqlsrv_query("insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_firmado."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_firmado."')", $link);
				
				//Inserci�n autom�tica subestado 3 RAD. RIESGO OPERATIVO (Anterior subestado) a comerciales de planta
				//sqlsrv_query("insert into subestados_usuarios (id_subestado, id_usuario) select '".$subestado_radicado."', id_usuario from usuarios where ((tipo IN ('COMERCIAL') and freelance = '0' and outsourcing = '0') OR (tipo IN ('DIRECTOROFICINA') and (subtipo IS NULL OR subtipo NOT IN ('EXTERNOS', 'TELEMERCADEO')))) and estado = '1' and id_usuario NOT IN (select id_usuario from subestados_usuarios where id_subestado = '".$subestado_radicado."')", $link);
				
				if ($_SESSION["S_TIPO"] == "TESORERIA"){
					$queryDB = "select count(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_subestado IN ('".$subestado_confirmado."')";
					
					$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					
					if ($_SESSION["S_SECTOR"]){
						$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
					}
					
					$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";	

					$rs_count_confirmados = sqlsrv_query( $link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));					
					$count_confirmados = sqlsrv_fetch_array($rs_count_confirmados);
					
					if ($count_confirmados["c"]){
						$rs_nombre_confirmado = sqlsrv_query($link,"select nombre from subestados where id_subestado IN ('".$subestado_confirmado."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));						
						$nombre_confirmado = sqlsrv_fetch_array($rs_nombre_confirmado);	

						$mensaje_tesoreria = "Actualmente hay ".$count_confirmados["c"]." credito(s) en subestado ".$nombre_confirmado["nombre"];
					}
					
					$queryDB = "select count(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_subestado IN ('".$subestado_desembolso_cliente."')";
					
					$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					
					if ($_SESSION["S_SECTOR"]){
						$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
					}
					
					$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
					
					$rs_count_desembolso_cliente = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));					
					$count_desembolso_cliente = sqlsrv_fetch_array($rs_count_desembolso_cliente);
					
					if ($count_desembolso_cliente["c"]){
						$rs_nombre_desembolso_cliente = sqlsrv_query( $link,"select nombre from subestados where id_subestado IN ('".$subestado_desembolso_cliente."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						
						$nombre_desembolso_cliente = sqlsrv_fetch_array($rs_nombre_desembolso_cliente);
						
						if ($mensaje_tesoreria)
							$mensaje_tesoreria .= " y ".$count_desembolso_cliente["c"]." credito(s) en subestado ".$nombre_desembolso_cliente["nombre"];
						else
							$mensaje_tesoreria = "Actualmente hay ".$count_desembolso_cliente["c"]." credito(s) en subestado ".$nombre_desembolso_cliente["nombre"];
					}
					
					if ($mensaje_tesoreria)
						echo "<script>alert('".$mensaje_tesoreria."')</script>";
				}
				
				if ($_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION"){
					echo "<script>location.replace('prospecciones.php')</script>";
				}
				else if ($_SESSION["FUNC_INDICADORES"] && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop"))){
					echo "<script>location.replace('indicadores.php')</script>";
				}
				else{
					echo "<script>location.replace('simulaciones.php')</script>";
				}

				exit;
			}
		} else{
			$mensaje = "Datos de acceso incorrectos";
		}
	} else{
		$mensaje = "Debe escribir el usuario y la contrase&ntilde;a";
	}
}
else{
	if ($_SESSION["S_LOGIN"]){
		if ($_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION")
			echo "<script>location.replace('prospecciones.php')</script>";
		else if ($_SESSION["FUNC_INDICADORES"] && $_SESSION["S_SUBTIPO"] != "ANALISTA_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA" && (!(($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && DeviceDetect() <> "desktop")))
			echo "<script>location.replace('indicadores.php')</script>";
		else
			echo "<script>location.replace('simulaciones.php')</script>";
		
		exit;
	}
} ?>
<body>

	<form name="formato" method="post" action="index.php" onSubmit="return chequeo_forma()">
		<div id="contenedor1">
			<div id="logueo">
				<div class="logo"></div>
				<div id="logueodiv">
					<div id="logueoerror"><?php echo $mensaje ?></div>
					<dl>
						<dt>Usuario</dt>
						<dd><input type="text" name="login" autocomplete="off" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></dd>
						<dt>Contrase&ntilde;a</dt>
						<dd><input onkeyup="onKeyUp(event)" autocomplete="off" type="password" name="password" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}"></dd>
						<dt></dt>
						<dd>
							<a href="" data-bs-toggle="modal" onclick="limpiarCamposModal(); return false;" data-bs-target="#modalRecuperarClave">Olvidé Mi Contrase&ntilde;a</a>
							<input type="hidden" name="action" value="ingresar"><input type="button" value="INGRESAR" onclick="chequeo_forma()">
						</dd>
					</dl>
				</div>
			</div>
			<?php include_once('./bottom.php'); ?>
			</div>
		</div>
	</form>

	<div class="modal modal-blur fade modal-tabler" id="modalRecuperarClave" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">RECUPERAR CONTRASE&Ntilde;A</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="mb-1">
							<label class="form-label">USUARIO</label>
							<input type="text" id="usuario" class="form-control">
						</div>
					</div>

					<div class="col-lg-4">
						<div class="mb-1">
							<label class="form-label">DOCUMENTO</label>
							<input type="text" id="documento" class="form-control">
						</div>
					</div>

					<div class="col-lg-4">
						<div class="mb-1">
							<label class="form-label" style="color: aliceblue;">*</label>
							<a name="add" id="btnSaveModal" onclick="enviarCorreo(); return false;" class="btn btn-primary ms-auto">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
								RECUPERAR
							</a>
						</div>
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

<?php if ($mensaje) { ?>
	<script>document.getElementById("logueoerror").style.display = "block";</script>
<?php } ?>

	<script src="../plugins/tabler/js/tabler.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script type="text/javascript">

		function chequeo_forma() {
			with (document.formato) {
				submit();
			}
		}

		function onKeyUp(event) {
		    var keycode = event.keyCode;
		    if(keycode == '13'){
		    	chequeo_forma();
		    }
		}

		function limpiarCamposModal(){
			$("#documento").val("");
			$("#usuario").val("");
			$("#usuario").focus();
		}

		function enviarCorreo(){

			if($("#usuario").val() != '' && $("#documento").val() != ''){

				Swal.fire({
					title: 'Por favor aguarde unos segundos',
					text: 'Procesando...'
				});

				Swal.showLoading();

				$.ajax({
					url: '../servicios/configuracion/recuperar_clave.php',
					type: 'POST',
					data: { usuario : $("#usuario").val(), documento : $("#documento").val(), opcion : 'recuperar_clave'},
					dataType : 'json',
					success: function(json) {

						if(json.code == 200){
							Swal.fire('Correo enviado a '+json.dato, '', 'success');
							$(".btn-close").trigger("click");
						}else{
							Swal.fire('Error, ' + json.mensaje, '', 'error');
						}
					}
				});
			}else{
				Swal.fire('Debe digitar el usuario con que inicia sesión y su documento de identidad.', '', 'error');
			}
		}
	</script>
	
</body>
</html>
