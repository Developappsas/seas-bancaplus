<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

?>
<!DOCTYPE HTML>
<html>
<head>
<title>S.E.A.S.<?php echo $label_title ?></title>
<meta charset="iso-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes" />

<link rel="STYLESHEET" type="text/css" href="../sty.css">
<script src="../jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="//www.google.com/jsapi" type="text/javascript"></script>
<script src="../attc.googleCharts.js" type="text/javascript"></script>
<script src="../js/superfish.min.js" type="text/javascript"></script>
<script src="../js.js" type="text/javascript"></script>
<script language="JavaScript" src="../functions.js"></script>

</head>
<body>
 	
<div id="contenedor2">
	<div class="header" id="encabezado">
				<div class="logo2"></div>
	</div>
 <div align="center">
<br>

