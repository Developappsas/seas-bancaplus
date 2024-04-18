<?php
header("Content-Type:application/json");
if (isset($_GET['numced']) && $_GET['numced']!="") {
 include('db.php');
 $numced = $_GET['numced'];
 $result = sqlsrv_query($con, "SELECT * FROM empleados WHERE cedula=$numced");
 
 if(sqlsrv_num_rows($result)>0){
 $row = sqlsrv_fetch_array($result);
 $cedres = $row['cedula'];
 $status = 0;
 $statusdesc = null;
if ($cedres != null){
    $status = 1;
    $statusdesc = 'Cedula registrada en sistema';
}

 response($numced, $status, $statusdesc);
 sqlsrv_close($con);
 }else{
 response($numced, 2,"Cedula no registrada en sistema");
 }
}else{
 response(NULL, 400,"Invalid Request");
 }
 
function response($numced, $status, $statusdesc){
 $response['Cedula'] = $numced;
 $response ['Estado'] = $status;
 $response ['Descripcion'] = $statusdesc;

 
 $json_response = json_encode($response);
 echo $json_response;
}
?>