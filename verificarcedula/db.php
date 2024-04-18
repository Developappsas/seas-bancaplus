<?php
$con = sqlsrv_connect("10.177.88.68","seas2_carlos","Fantasma2019**","seas2_seasesef_originar_bdcomercial");
if (mysqli_connect_errno()){
echo "Failed to connect to MySQL: " . mysqli_connect_error();
die();
}