<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}


$link = conectar();

if ($_REQUEST["estado"] != "1") {
	$_REQUEST["estado"] = "0";
}

if ($_REQUEST["freelance"] != "1") { 
	$_REQUEST["freelance"] = "0";
}

if ($_REQUEST["outsourcing"] != "1") {
	$_REQUEST["outsourcing"] = "0";
}

if ($_REQUEST["coordinador"] != "1") {
	$_REQUEST["coordinador"] = "0";
}

if ($_REQUEST["jefe_comercial"] != "1") {
	$_REQUEST["jefe_comercial"] = "0";
}

if ($_REQUEST["solo_lectura"] != "1") {
	$_REQUEST["solo_lectura"] = "0";
}

if ($_REQUEST["subtipo"]) {
	$subtipo = "'".$_REQUEST["subtipo"]."'";
}else{
	$subtipo = "NULL";
}

if ($_REQUEST["sector"]){
	$sector = "'".$_REQUEST["sector"]."'"; 
}else{
	$sector = "NULL";
}

if ($_REQUEST["cedula"]){
	$cedula = "'".$_REQUEST["cedula"]."'"; 
} else {
	$cedula = "NULL";
}

if ($_REQUEST["contrato"]){
	$contrato = "'".$_REQUEST["contrato"]."'";
}else{
	$contrato = "NULL";
}

if ($_REQUEST["maxconsdiarias"]){
	$maxconsdiarias = "'".$_REQUEST["maxconsdiarias"]."'";
}else{
	$maxconsdiarias = "NULL";
}

$existe_login = sqlsrv_query($link,"SELECT [login] from usuarios where [login] = '".utf8_encode($_REQUEST["login"])."' AND id_usuario != '".$_REQUEST["id_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!(sqlsrv_num_rows($existe_login))) {
	if ($_REQUEST["agenda"]==1) {
		$agenda="s";
	}else{
		$agenda="n";
	}

	if ($_REQUEST["preprospeccion"]==1){ $preprospeccion=1; }else{ $preprospeccion=0; }
	if ($_REQUEST["solicitar_firma"]==1){ $solicitar_firma=1; }else{ $solicitar_firma=0; }
	if ($_REQUEST["bi"]==1){ $inteligencia_negocios=1;  }else{ $inteligencia_negocios=0; }
	if ($_REQUEST["revision_garantias"]==1){ $revision_garantias=1;  }else{ $revision_garantias=0; }
	if ($_REQUEST["habilitar_prospeccion"]==1){ $habilitar_prospeccion=1;  }else{ $habilitar_prospeccion=0; }
	if ($_REQUEST["descargar_reportes"]==1){ $visualizar_reportes=1;  }else{ $visualizar_reportes=0; }
	if ($_REQUEST["anular_firma_digital"]==1){  $anular_firma_digital=1; }else{ $anular_firma_digital=0; }
	if ($_REQUEST["causales_no_recaudo"]==1){  $causales_no_recaudo=1; }else{ $causales_no_recaudo=0; }
	if ($_REQUEST["reporte_cartera"]==1){  $reporte_cartera=1; }else{ $reporte_cartera=0; }

	$query = ("update usuarios set centrales_judicial = '".$centrales_judicial."', reporte_cartera = '".$reporte_cartera."', causales_no_recaudo = '".$causales_no_recaudo."', anular_firma_digital = '".$anular_firma_digital."',  visualizar_reportes = '".$visualizar_reportes."',habilitar_prospeccion = '".$habilitar_prospeccion."', revision_garantias = '".$revision_garantias."', inteligencia_negocios = '".$inteligencia_negocios."', solicitar_firma='".$solicitar_firma."', preprospeccion='".$preprospeccion."', cargo='".$_REQUEST["cargo"]."',agenda='".$agenda."',nombre = '".utf8_encode($_REQUEST["nombre"])."', apellido = '".utf8_encode($_REQUEST["apellido"])."', email = '".utf8_encode($_REQUEST["email"])."', telefono = '".utf8_encode($_REQUEST["telefono"])."', estado = '".$_REQUEST["estado"]."', tipo = '".$_REQUEST["tipo"]."', subtipo = ".$subtipo.", sector = ".$sector.", cedula = ".$cedula.", contrato = ".$contrato.", login = '".utf8_encode($_REQUEST["login"])."', maxconsdiarias = ".$maxconsdiarias.", meta_mes = '".$_REQUEST["meta_mes"]."', freelance = '".$_REQUEST["freelance"]."', outsourcing = '".$_REQUEST["outsourcing"]."', coordinador = '".$_REQUEST["coordinador"]."', jefe_comercial = '".$_REQUEST["jefe_comercial"]."', solo_lectura = '".$_REQUEST["solo_lectura"]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE() where id_usuario = '".$_REQUEST["id_usuario"]."'");

	
	sqlsrv_query($link, $query);
	
	if ($_REQUEST["password"] != "" && $_REQUEST["password"] != NULL){
		$query = ("UPDATE usuarios set [password] = ('".md5(utf8_encode($_REQUEST["password"]))."') where id_usuario = '".$_REQUEST["id_usuario"]."'");
		//echo $query;
		sqlsrv_query($link, $query);
	}
	
	if ($_REQUEST["estadoh"] == "1" && $_REQUEST["estado"] == "0"){
		sqlsrv_query($link,"update usuarios set usuario_inactivacion = '".$_SESSION["S_LOGIN"]."', fecha_inactivacion = GETDATE() where id_usuario = '".$_REQUEST["id_usuario"]."'");
	}
	
	if ($_REQUEST["estado"] == "1"){
		sqlsrv_query($link,"update usuarios set usuario_inactivacion = NULL, fecha_inactivacion = NULL where id_usuario = '".$_REQUEST["id_usuario"]."'");
	}
	
	$queryDB = "select id_unidad from unidades_negocio order by id_unidad";

	$rs1 = sqlsrv_query($link,$queryDB);

	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		
		if ($_REQUEST["id_unidad".$fila1["id_unidad"]] != "1")
			$_REQUEST["id_unidad".$fila1["id_unidad"]] = "0";

		
		if ($_REQUEST["id_unidad".$fila1["id_unidad"]] != $_REQUEST["id_unidadh".$fila1["id_unidad"]]){
			
			if ($_REQUEST["id_unidad".$fila1["id_unidad"]])
				sqlsrv_query($link,"insert into usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_usuario"]."', '".$fila1["id_unidad"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
			else
				sqlsrv_query($link,"delete from usuarios_unidades where id_usuario = '".$_REQUEST["id_usuario"]."' AND id_unidad_negocio = '".$fila1["id_unidad"]."'");
		}
	}

	$queryDB = "SELECT id_oficina, nombre FROM oficinas ";
	$rs1 = sqlsrv_query($link,$queryDB);

	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		if ($_REQUEST["id_oficina".$fila1["id_oficina"]] != "1")
			$_REQUEST["id_oficina".$fila1["id_oficina"]] = "0";
		
		if ($_REQUEST["id_oficina".$fila1["id_oficina"]] != $_REQUEST["id_oficinah".$fila1["id_oficina"]]){
			if ($_REQUEST["id_oficina".$fila1["id_oficina"]]){
				$query = "insert into oficinas_usuarios (id_usuario, id_oficina) VALUES ('".$_REQUEST["id_usuario"]."', '".$fila1["id_oficina"]."')";
				
				$ejecutar = sqlsrv_query($link, $query);
				if ($ejecutar) {
					$mensaje = $query;
				}
			}else{
				$query = "delete from oficinas_usuarios where id_usuario = '".$_REQUEST["id_usuario"]."' AND id_oficina = '".$fila1["id_oficina"]."'";
				$ejecutar = sqlsrv_query($link, $query);
				if ($ejecutar) {
					$mensaje = $query;
				}
			}
		}
	}

	$queryDB = "select id as id_reporte from reportes order by id";

	$rs1 = sqlsrv_query($link,$queryDB);

	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		
		if ($_REQUEST["id_reporte".$fila1["id_reporte"]] != "1")
			$_REQUEST["id_reporte".$fila1["id_reporte"]] = "0";
		
		if ($_REQUEST["id_reporte".$fila1["id_reporte"]] != $_REQUEST["id_reporteh".$fila1["id_reporte"]]){
			if ($_REQUEST["id_reporte".$fila1["id_reporte"]])
				sqlsrv_query($link,"insert into usuarios_reportes (id_usuario, id_reporte, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_usuario"]."', '".$fila1["id_reporte"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
			else
				sqlsrv_query($link,"delete from usuarios_reportes where id_usuario = '".$_REQUEST["id_usuario"]."' AND id_reporte = '".$fila1["id_reporte"]."'");
		}
	}

	$mensaje = "Usuario actualizado exitosamente";

} else {
	$mensaje = "El nombre de Usuario ya se encuentra registrado. Usuario NO actualizado";
}
 ?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'usuarios_actualizar.php?id_usuario=<?php echo $_REQUEST["id_usuario"] ?>';
</script>

