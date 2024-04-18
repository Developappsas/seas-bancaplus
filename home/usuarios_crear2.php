<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["estado"] != "1"){
	$_REQUEST["estado"] = "0";
}
if ($_REQUEST["freelance"] != "1"){
	$_REQUEST["freelance"] = "0";
}
if ($_REQUEST["outsourcing"] != "1"){
	$_REQUEST["outsourcing"] = "0";
}
if ($_REQUEST["coordinador"] != "1"){
	$_REQUEST["coordinador"] = "0";
}
if ($_REQUEST["jefe_comercial"] != "1"){
	$_REQUEST["jefe_comercial"] = "0";
}
if ($_REQUEST["solo_lectura"] != "1"){
	$_REQUEST["solo_lectura"] = "0";
}

if ($_REQUEST["subtipo"]){
	$subtipo = "'".$_REQUEST["subtipo"]."'";
}else{
	if ($_REQUEST["tipo"]=="EXTERNOS"){
		$subtipo =  "'ANALISTA_PREESTUDIO'";
	}else{
		$subtipo = "NULL";
	}
}	

if ($_REQUEST["sector"])
	$sector = "'".$_REQUEST["sector"]."'";
else
	$sector = "NULL";

if ($_REQUEST["cedula"])
	$cedula = "'".$_REQUEST["cedula"]."'";
else
	$cedula = "NULL";

if ($_REQUEST["contrato"])
	$contrato = "'".$_REQUEST["contrato"]."'";
else
	$contrato = "NULL";

if ($_REQUEST["maxconsdiarias"])
	$maxconsdiarias = "'".$_REQUEST["maxconsdiarias"]."'";
else
	$maxconsdiarias = "NULL";

$existe_login = sqlsrv_query($link, "SELECT [login] from usuarios where [login] = '".utf8_encode($_REQUEST["login"])."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));


if (!(sqlsrv_num_rows($existe_login)))
{    
	sqlsrv_query($link,"START TRANSACTION");

	if ($_REQUEST["agenda"]==1) { $agenda="s"; }else{ $agenda="n"; }
	if ($_REQUEST["preprospeccion"]==1){  $preprospeccion=1; }else{ $preprospeccion=0; }
	if ($_REQUEST["solicitar_firma"]==1){ $solicitar_firma=1;  }else{ $solicitar_firma=0; }
	if ($_REQUEST["bi"]==1){ $inteligencia_negocios=1;  }else{ $inteligencia_negocios=0; }
	if ($_REQUEST["revision_garantias"]==1){ $revision_garantias=1;  }else{ $revision_garantias=0; }
	if ($_REQUEST["habilitar_prospeccion"]==1){ $habilitar_prospeccion=1;  }else{ $habilitar_prospeccion=0; }
	if ($_REQUEST["descargar_reportes"]==1){ $visualizar_reportes=1;  }else{ $visualizar_reportes=0; }
	if ($_REQUEST["anular_firma_digital"]==1){  $anular_firma_digital=1; }else{ $anular_firma_digital=0; }
	if ($_REQUEST["causales_no_recaudo"]==1){  $causales_no_recaudo=1; }else{ $causales_no_recaudo=0; }
	if ($_REQUEST["reporte_cartera"]==1){  $reporte_cartera=1; }else{ $reporte_cartera=0; }
	if ($_REQUEST["centrales_judicial"]==1){  $centrales_judicial=1; }else{ $centrales_judicial=0; }




	$query_insert = ("insert into usuarios (centrales_judicial,reporte_cartera, causales_no_recaudo, anular_firma_digital,visualizar_reportes,habilitar_prospeccion, revision_garantias, inteligencia_negocios, solicitar_firma, preprospeccion, cargo,agenda,nombre, apellido, email, telefono, estado, tipo, subtipo, sector, cedula, contrato, login, password, maxconsdiarias, meta_mes, freelance, outsourcing, coordinador, jefe_comercial, solo_lectura, usuario_creacion, fecha_creacion) values ('".$centrales_judicial."','".$reporte_cartera."','".$causales_no_recaudo."', '".$anular_firma_digital."','".$visualizar_reportes."','".$habilitar_prospeccion."','".$revision_garantias."', '".$inteligencia_negocios."', '".$solicitar_firma."', '".$preprospeccion."', '".$_REQUEST["cargo"]."','".$agenda."','".utf8_encode($_REQUEST["nombre"])."', '".utf8_encode($_REQUEST["apellido"])."', '".utf8_encode($_REQUEST["email"])."', '".utf8_encode($_REQUEST["telefono"])."', '".$_REQUEST["estado"]."', '".$_REQUEST["tipo"]."', ".$subtipo.", ".$sector.", ".$cedula.", ".$contrato.", '".utf8_encode($_REQUEST["login"])."', '".MD5(utf8_encode($_REQUEST["password"]))."', ".$maxconsdiarias.", '".$_REQUEST["meta_mes"]."', '".$_REQUEST["freelance"]."', '".$_REQUEST["outsourcing"]."', '".$_REQUEST["coordinador"]."', '".$_REQUEST["jefe_comercial"]."', '".$_REQUEST["solo_lectura"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");



	$resultado_insert = sqlsrv_query( $link, $query_insert);

	if ($resultado_insert == false) {
		  if( ($errors = sqlsrv_errors() ) != null) {
		     foreach( $errors as $error ) {
					echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
					echo "code: ".$error[ 'code']."<br />";
					echo "message: ".$error[ 'message']."<br />";
					echo $query_insert;
					exit;
			}
		}
	}	

	
	
	$rs = sqlsrv_query($link,"select MAX(id_usuario) as m from usuarios");
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	$id_usr = $fila["m"];
	sqlsrv_query($link,"COMMIT");
	
	$queryDB = "SELECT id_unidad from unidades_negocio order by id_unidad";
	$rs1 = sqlsrv_query( $link, $queryDB);
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		if ($_REQUEST["id_unidad".$fila1["id_unidad"]])
			sqlsrv_query($link,"insert into usuarios_unidades (id_usuario, id_unidad_negocio, usuario_creacion, fecha_creacion) VALUES ('".$id_usr."', '".$fila1["id_unidad"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
	}

	$queryDB = "SELECT id_oficina, nombre FROM oficinas ";
	$rs1 = sqlsrv_query( $link,$queryDB);
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){
		if ($_REQUEST["id_oficina".$fila1["id_oficina"]]){
			sqlsrv_query($link,"insert into oficinas_usuarios (id_usuario, id_oficina) VALUES ('".$id_usr."', '".$fila1["id_oficina"]."')");
		}
	}
	$mensaje = "Usuario creado exitosamente";
}
else
{
	$mensaje = "El nombre de Usuario ya se encuentra registrado. Usuario NO creado";
}

?>
<script>
alert("<?php echo $mensaje ?>");
window.location = 'usuarios.php';

</script>
