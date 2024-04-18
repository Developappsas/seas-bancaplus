<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

require_once("../cors.php");
require_once("../../functions.php");

$json_Input = file_get_contents('php://input');
$parametros = json_decode($json_Input);
$resultado = array();
$proceso = $parametros->proceso;

$link = conectar_utf();

$queryDB= "SELECT si.cedula,  si.id_simulacion,si.nombre, pd.cuota,pd.valor, pg.fecha, pg.tipo_recaudo, pd.id_simulacion
from pagos_detalle pd 
INNER JOIN pagos pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo 
LEFT JOIN cuotas cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota 
JOIN simulaciones si ON pg.id_simulacion = si.id_simulacion AND si.id_simulacion = pd.id_simulacion
WHERE pd.valor > 0  AND date_format(si.fecha_radicado, '%Y') >= 2019
ORDER BY si.id_simulacion desc, pg.fecha";

$ejecutar_query = mysqli_query($link, $queryDB);

while($datos = mysqli_fetch_assoc($ejecutar_query)){

   $resultado[] = array(
      'Cedula' => $datos['cedula'],
      'ID Simulacion'=>$datos['id_simulacion'],
      'Nombre'=>$datos['nombre'],
      'Cuota'=>$datos['cuota'],
      'valor'=>$datos['valor'],
      'fecha'=>$datos['fecha'],
      'Tipo Recaudo'=>$datos['tipo_recaudo']
   );
}

echo json_encode($resultado);
?>