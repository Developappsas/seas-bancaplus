<?php include ('../functions.php'); ?>
<?php
$link=conectar_utf();
$consultarUsuariosAnalistasKredit="SELECT id_usuario FROM usuarios a 
			WHERE id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND subtipo='ANALISTA_CREDITO' AND estado=1";
			$queryUsuariosAnalistasKredit=sqlsrv_query($consultarUsuariosAnalistasKredit,$link);
			//$resUsuariosAnalistasKredit=sqlsrv_fetch_array($queryUsuariosAnalistasKredit);
            $dir = array();
$cont = 0;
while ($resUsuariosAnalistasKredit = sqlsrv_fetch_array($queryUsuariosAnalistasKredit)) {
   $dir[$cont] = $resUsuariosAnalistasKredit['id_usuario'];
   $cont++;
}

var_dump($dir);
?>
