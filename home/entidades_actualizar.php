<?php include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA")) {
	exit;
}

$link = conectar();

include("top.php");

if ($_REQUEST["action"] == "actualizar") {
	$existe_nit = sqlsrv_query($link, "select nit from entidades_desembolso where nit = '".$_REQUEST["nit".$_REQUEST["id"]]."' AND id_entidad != '".$_REQUEST["id"]."'");
	
	if (!(sqlsrv_num_rows($existe_nit))) {
		sqlsrv_query($link, "update entidades_desembolso set nit = '".$_REQUEST["nit".$_REQUEST["id"]]."', nombre = '".utf8_encode($_REQUEST["n".$_REQUEST["id"]])."' where id_entidad = '".$_REQUEST["id"]."'");
		
		echo "<script>alert('Entidad actualizada exitosamente');</script>";
	} else {
		echo "<script>alert('El NIT de la entidad ya se encuentra registrado. Entidad NO actualizada');</script>";
	}
}
else if ($_REQUEST["action"] == "borrar") {
	if (!$_REQUEST["page"]) {
        $_REQUEST["page"] = 0;
	}
	
	$x_en_x = 100;
	$offset = $_REQUEST["page"] * $x_en_x;	
	$queryDB = "select id_entidad, nombre from entidades_desembolso where id_entidad IS NOT NULL";
	
	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];
		$queryDB = $queryDB." AND (nit like '%".$descripcion_busqueda."%' OR UPPER(nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%')";
	}
	
	$queryDB = $queryDB." order by nombre DESC OFFSET ".$offset." ROWS";
	
	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["b".$fila["id_entidad"]] == "1") 		{
			$existe_en_simulaciones = sqlsrv_query($link, "select id_simulacion from simulaciones where id_compradorprep = '".$fila["id_entidad"]."'");
			
			$existe_en_simulaciones_ext = sqlsrv_query($link, "select id_simulacion from simulaciones_ext where id_compradorprep = '".$fila["id_entidad"]."'");
			
			$existe_en_simulacionescomprascartera = sqlsrv_query($link, "select id_simulacion from simulaciones_comprascartera where id_entidad = '".$fila["id_entidad"]."'");
			
			if (sqlsrv_num_rows($existe_en_simulaciones) || sqlsrv_num_rows($existe_en_simulaciones_ext) || sqlsrv_num_rows($existe_en_simulacionescomprascartera)) {
				echo "<script>alert('La entidad ".utf8_decode($fila["nombre"])." no puede ser borrada (Existen tablas con registros asociados)')</script>";
			}
			else
			{
				sqlsrv_query($link, "delete from entidades_cuentas where id_entidad = '".$fila["id_entidad"]."'");
				
				sqlsrv_query($link, "delete from entidades_desembolso where id_entidad = '".$fila["id_entidad"]."'");
			}
		}
	}
}

?>
<script>
window.location = 'entidades.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
