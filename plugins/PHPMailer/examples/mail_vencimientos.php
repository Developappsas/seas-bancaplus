<?php 

include_once ('../../../functions.php');
//include_once ('../../../function_blob_storage.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar();
$id_simulacion = $_POST["id_simulacion"];

$cuerpo = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="format-detection" content="date=no" />
	<meta name="format-detection" content="address=no" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="x-apple-disable-message-reformatting" />

	<link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i" rel="stylesheet" />

	<title>Verificaci&oacute;n KREDIT</title>	

	<style type="text/css" media="screen">
		body { padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f4f4f4; -webkit-text-size-adjust:none }
		a { color:#66c7ff; text-decoration:none }
		p { padding:0 !important; margin:0 !important } 
		img { -ms-interpolation-mode: bicubic; }
		.mcnPreviewText { display: none !important; }

		@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mobile-shell { width: 100% !important; min-width: 100% !important; }
			.bg { background-size: 100% auto !important; -webkit-background-size: 100% auto !important; }	
			.text-header,
			.m-center { text-align: center !important; }
			.center { margin: 0 auto !important; }
			.container { padding: 0px 10px 10px 10px !important }
			.td { width: 100% !important; min-width: 100% !important; }
			.text-nav { line-height: 28px !important; }
			.p30 { padding: 15px !important; }
			.m-br-15 { height: 15px !important; }
			.p30-15 { padding: 30px 15px !important; }
			.p40 { padding: 20px !important; }
			.m-td,
			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }
			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }
			.column,
			.column-top,
			.column-empty,
			.column-empty2,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }
			.column-empty { padding-bottom: 10px !important; }
			.column-empty2 { padding-bottom: 20px !important; }
			.content-spacing { width: 15px !important; }
		}
	</style>
</head>
<body class="body" style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f4f4f4; -webkit-text-size-adjust:none;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
		<tr>
			<td align="center" valign="top">
				<table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
					<tr>
						<td class="td container" style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; margin:0; font-weight:normal; padding:0px 0px 40px 0px;">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td class="p30-15" style="padding: 25px 30px;" bgcolor="#009bfe" align="center">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td class="text-nav" style="color:#ffffff; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:18px; text-align:center; text-transform:uppercase; font-weight:bold;">
													<a href="https://kredit.com.co/" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">kredit.com.co</span></a>
													&nbsp; &nbsp; &nbsp; &nbsp;
													<a href="https://kredit.com.co/nosotros/" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">Quienes Somos</span></a>
													<span class="m-block"><span class="m-hide">&nbsp; &nbsp; &nbsp; &nbsp;</span></span>
													<a href="https://kredit.com.co/contacto/" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">Contactanos</span></a>
													&nbsp; &nbsp; &nbsp; &nbsp;
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td class="fluid-img" style="font-size:0pt; line-height:0pt; text-align:left;"><img src=$urlPrincipal."/plugins/PHPMailer/examples/images/t9_image1.jpg" width="650" height="226" border="0" alt="" /></td>
								</tr>
							</table>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td style="padding-bottom: 10px;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
											<tr>
												<td class="p30-15" style="padding: 30px;">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="h2 pb20" style="color:#050505; font-family:Roboto, Arial,sans-serif; font-size:28px; line-height:34px; text-align:left; padding-bottom:10px;">Notificaci&oacute;n Compras de Cartera</td>
																	</tr>
																	<tr>
																		<td class="text pb20" style="color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; padding-left: 8px; line-height:28px; text-align:left; padding-bottom:10px;">CLIENTE: <b>{NOMBRE}</b><br>C&eacute;dula: <b><a href=$urlPrincipal."/home/tesoreria_actualizar.php?id_simulacion={SIMULACION}" target="_blank" class="link-white">{CEDULA}</a></b><br>N° SIMULACI&Oacute;N: <b>{SIMULACION}</b></td>
																	</tr>
																</table>
															</th>
														</tr>


														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="h2 pb20" style="color:#050505; font-family:Roboto, Arial,sans-serif; font-size:28px; line-height:34px; text-align:left; padding-bottom:10px;">Notificaci&oacute;n Compras de Cartera</td>
																	</tr>
																	<tr>
																		<td class="text pb20" style="color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; padding-left: 8px; line-height:28px; text-align:left; padding-bottom:10px;"><b>CARTERAS VENCIDAS</b></td>
																	</tr>
																</table>
															</th>
														</tr>
														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="text pb20" style="background-color: navajowhite; border-top-left-radius: 8px; padding-left: 5px; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cartera</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cuota</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Valor</td>
																	
																	</tr>';

																	//$opcion='<td class="text pb20" style="border-top-right-radius: 8px; background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Opci&oacute;n</td>';

																	
																	$queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
																		FROM simulaciones_comprascartera a
																		LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion
																		WHERE a.se_compra='SI' AND b.fecha_vencimiento<CURRENT_DATE() AND a.id_simulacion=".$id_simulacion." AND a.consecutivo=b.consecutivo";

																	$conCC = mysqli_query($link, $queryBD_CC);

																	while ($fila1 = mysqli_fetch_array($conCC)){
																		$cuerpo .= '<tr>
																			<td class="text pb20" style="padding-left: 5px; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align: left;">'.$fila1["entidad"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["cuota"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["valor_pagar"].'</td>
																		
																		</tr>';																		//$opcion2='	<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;"><a href="" target="_blank"><img src=$urlPrincipal."/plugins/PHPMailer/examples/images/logo-adjuntar.png" width="20" height="20" border="0" alt="" /></a></td>';
																	}
																	
																$cuerpo .= '</table>
															</th>
														</tr>




														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="h2 pb20" style="color:#050505; font-family:Roboto, Arial,sans-serif; font-size:28px; line-height:34px; text-align:left; padding-bottom:10px;">Notificaci&oacute;n Compras de Cartera</td>
																	</tr>
																	<tr>
																		<td class="text pb20" style="color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; padding-left: 8px; line-height:28px; text-align:left; padding-bottom:10px;"><b>CARTERAS VENCEN HOY</b></td>
																	</tr>
																</table>
															</th>
														</tr>
														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="text pb20" style="background-color: navajowhite; border-top-left-radius: 8px; padding-left: 5px; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cartera</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cuota</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Valor</td>
																	
																	</tr>';

																	//$opcion='<td class="text pb20" style="border-top-right-radius: 8px; background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Opci&oacute;n</td>';

																	
																	$queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
																		FROM simulaciones_comprascartera a
																		LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion
																		WHERE a.se_compra='SI' AND b.fecha_vencimiento=CURRENT_DATE() AND a.id_simulacion=".$id_simulacion." AND a.consecutivo=b.consecutivo";

																	$conCC = mysqli_query($link, $queryBD_CC);

																	while ($fila1 = mysqli_fetch_array($conCC)){
																		$cuerpo .= '<tr>
																			<td class="text pb20" style="padding-left: 5px; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align: left;">'.$fila1["entidad"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["cuota"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["valor_pagar"].'</td>
																		
																		</tr>';																		//$opcion2='	<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;"><a href="" target="_blank"><img src=$urlPrincipal."/plugins/PHPMailer/examples/images/logo-adjuntar.png" width="20" height="20" border="0" alt="" /></a></td>';
																	}
																	
																$cuerpo .= '</table>
															</th>
														</tr>




														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="h2 pb20" style="color:#050505; font-family:Roboto, Arial,sans-serif; font-size:28px; line-height:34px; text-align:left; padding-bottom:10px;">Notificaci&oacute;n Compras de Cartera</td>
																	</tr>
																	<tr>
																		<td class="text pb20" style="color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; padding-left: 8px; line-height:28px; text-align:left; padding-bottom:10px;"><b>CARTERAS AUN NO VENCEN</b></td>
																	</tr>
																</table>
															</th>
														</tr>
														<tr>
															<th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td class="text pb20" style="background-color: navajowhite; border-top-left-radius: 8px; padding-left: 5px; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cartera</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Cuota</td>
																		<td class="text pb20" style="background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Valor</td>
																	
																	</tr>';

																	//$opcion='<td class="text pb20" style="border-top-right-radius: 8px; background-color: navajowhite; text-align: center !important; font-weight: bold; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align:left;">Opci&oacute;n</td>';

																	
																	$queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
																		FROM simulaciones_comprascartera a
																		LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion
																		WHERE a.se_compra='SI' AND b.fecha_vencimiento>CURRENT_DATE() AND a.id_simulacion=".$id_simulacion." AND a.consecutivo=b.consecutivo";

																	$conCC = mysqli_query($link, $queryBD_CC);

																	while ($fila1 = mysqli_fetch_array($conCC)){
																		$cuerpo .= '<tr>
																			<td class="text pb20" style="padding-left: 5px; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px; text-align: left;">'.$fila1["entidad"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["cuota"].'</td>
																			<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;">$ '.$fila1["valor_pagar"].'</td>
																		
																		</tr>';																		//$opcion2='	<td class="text pb20" style="text-align: center !important; color:#666666; font-family:Roboto, Arial,sans-serif; font-size:14px; line-height:28px;"><a href="" target="_blank"><img src=$urlPrincipal."/plugins/PHPMailer/examples/images/logo-adjuntar.png" width="20" height="20" border="0" alt="" /></a></td>';
																	}
																	
																$cuerpo .= '</table>
															</th>
														</tr>



													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>							
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>';
?>