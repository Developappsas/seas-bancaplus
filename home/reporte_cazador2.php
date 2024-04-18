<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["S_TIPO"] == "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

?>

<table border="0">
<tr>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Cedula</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Nombre</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Pagaduria</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Institucion</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Comercial</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Fecha Asignacion</th> <?php } ?>
	<?php if(!$_REQUEST["resumidob"]) { ?><th>Estado</th> <?php } ?>
	
	<th>Pagaduria</th>
	<th>Institucion</th>
	<th>Comercial Asignado</th>
	<th>Fecha Asignacion</th>
	<th>Estado</th>
	
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Ciudad</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>F Nacimiento</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Meses Antes 65 Años</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Salario Básico</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Adicionales Sólo (AA)</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Total Ingresos</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Aportes (Salud y Pensión</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Otros Aportes</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Total Aportes</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Total Egresos</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Ingresos - Aportes</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Salario Libre Mensual</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Vinculación Docente</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Docente Embargado</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Historial Embargos</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Embargo Alimentos</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Descuentos Por Fuera</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Tiene Cartera En Mora</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Valor Cartera En Mora</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Puntaje Datacredito</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Puntaje CIFIN</th><?php } ?>
	<?php if (!$_REQUEST["resumidob"]) { ?><th>Valor Descuentos Por Fuera</th><?php } ?>
	<th>Tasa Interés</th>
	<th>Plazo</th>

	</tr>