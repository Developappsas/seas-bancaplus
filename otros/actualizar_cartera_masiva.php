<?php include ('../functions.php'); 
$link = conectar();
$data=array('33345',
'35732',
'36591',
'48325',
'48213',
'50201',
'53648',
'62185',
'62606',
'69752',
'71682',
'71203',
'72420',
'73101',
'73137',
'72411',
'78300',
'74906',
'78428',
'78694',
'79505',
'80895',
'77976',
'81641',
'85004',
'85813',
'501490',
'501573',
'503230',
'505089',
'503615',
'505161',
'504234',
'504471',
'503390',
'505631',
'502242',
'506830',
'507942',
'506684',
'503159',
'506446',
'506564',
'507212',
'507160',
'504872',
'510776',
'509896',
'506530',
'508649',
'510498',
'509855',
'508837',
'510186',
'507797',
'509514',
'510727',
'511673',
'503803',
'510203',
'509765',
'507270',
'510368',
'511090',
'510693',
'509669',
'508801',
'512132',
'511877',
'508685',
'512192',
'511466',
'511059',
'513151',
'513527',
'512596',
'511748',
'507458',
'513216',
'510093',
'513886',
'513804',
'504176',
'512590',
'513585',
'505758',
'512143',
'508814',
'504421',
'512989',
'508056',
'513876',
'513571',
'513366',
'514405',
'508280',
'513708',
'502514',
'511064',
'511141',
'512081',
'508745',
'510903',
'509835',
'514487',
'511240',
'513209',
'511776',
'514471',
'512215',
'502470',
'512443',
'512790',
'515566',
'513807',
'512083',
'512208',
'514817',
'511404',
'515034',
'510828',
'508218',
'514837',
'513140',
'508824',
'515872',
'512261',
'510811',
'516118',
'513406',
'516501',
'513602',
'514268',
'516212',
'514229',
'510136',
'515888',
'517223',
'517226',
'513408',
'512622',
'513056',
'513490',
'517557',
'514473',
'515086',
'510692',
'515213',
'515015',
'516919',
'515726',
'517864',
'517020',
'514282',
'518418',
'517074',
'514920',
'516736',
'518101',
'517456',
'519024',
'517394',
'509132',
'518695',
'517046',
'516933',
'513083',
'517904',
'513798',
'507181',
'518173',
'514096',
'514158',
'516590',
'518338',
'519425',
'519417',
'517556',
'514821',
'518132',
'517306',
'517535',
'519738',
'516792',
'516775',
'519595',
'519179',
'512684',
'516807',
'518423',
'519574',
'518871',
'511169',
'519104',
'519556',
'521564',
'517713',
'520401',
'521100',
'517238',
'521252',
'514063',
'521380',
'521115',
'518824',
'517990',
'519415',
'514964',
'518583',
'520397',
'521334',
'514831',
'517983',
'522161',
'520312',
'520716',
'521739',
'521674',
'521701',
'522835',
'519621',
'521741',
'522255',
'514539',
'522715',
'515865',
'521900',
'517310',
'522805',
'525030',
'20772',
'21940',
'24892',
'31890',
'71035',
'75126',
'82641',
'79818',
'84762',
'82054',
'86543',
'86165',
'86536',
'503049',
'502629',
'504434',
'507190',
'504980',
'85878',
'507635',
'507917',
'507061',
'509151',
'508205',
'506420',
'501443',
'507138',
'504606',
'509016',
'509536',
'509145',
'507368',
'511215',
'510875',
'512498',
'505519',
'512691',
'511869',
'509021',
'512255',
'513048',
'512846',
'513352',
'514260',
'504545',
'512101',
'509867',
'509904',
'511097',
'513023',
'511426',
'515126',
'515573',
'513763',
'513416',
'516285',
'507733',
'507071',
'515659',
'509658',
'509715',
'514050',
'516242',
'515815',
'514271',
'515139',
'513851',
'516482',
'513989',
'513985',
'516272',
'516635',
'516415',
'516772',
'517935',
'515977',
'516935',
'512057',
'514681',
'517368',
'515359',
'520338',
'511471',
'514995',
'506318',
'521272',
'519226',
'519203',
'516558',
'521167',
'521714',
'516006',
'520103',
'522460',
'523128',
'524525');

foreach ($data as $valor)
{
    $consultarFechaRecaudoCredito="SELECT pg.id_simulacion,pg.consecutivo,pd.valor,pg.fecha AS fecha_recaudo FROM pagos_detalle pd 
    INNER JOIN pagos pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo 
    WHERE pd.id_simulacion=".$valor." AND pd.valor > 0 AND pd.cuota=1 and pd.valor_anulacion is null";
    $queryFechaRecaudoCredito=sqlsrv_query($link,$consultarFechaRecaudoCredito);
    $resFechaRecaudoCredito=sqlsrv_fetch_array($queryFechaRecaudoCredito);

    $consultarCuotaCredito="SELECT * FROM cuotas WHERE id_simulacion=".$valor." AND cuota=1";
    $queryCuotaCredito=sqlsrv_query($link,$consultarCuotaCredito);
    $resCuotaCredito=sqlsrv_fetch_array($queryCuotaCredito);
    echo $valor."//".$resFechaRecaudoCredito["id_simulacion"]."--".$resFechaRecaudoCredito["fecha_recaudo"]."--".$resCuotaCredito["fecha"]."<br>";

    if ($resFechaRecaudoCredito["fecha_recaudo"]<>$resCuotaCredito["fecha"])
    {
        echo "RESULTADO: DIFERENTE";
    }else{
        echo "RESULTADO: IGUAL";
    }
    $rs = sqlsrv_query( $link,"SELECT * from simulaciones where id_simulacion = '" . $valor . "'");

    $fila = sqlsrv_fetch_array($rs);

    $fecha_tmp = $resFechaRecaudoCredito["fecha_recaudo"];

    $fecha = new DateTime($fecha_tmp);

	
	    // 001
	    $sql = "update simulaciones set "
	            . "fecha_primera_cuota = '" . $resFechaRecaudoCredito["fecha_recaudo"] . "' "
	            
	            . "where id_simulacion = '" . $valor . "'";

	    sqlsrv_query($link,$sql);

	    // 002


	

		if ($resCuotaCredito["fecha"] != $resFechaRecaudoCredito["fecha_recaudo"])
		{
			sqlsrv_query($link,"INSERT into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('".$valor."', '".$resFechaRecaudoCredito["fecha_recaudo"]."', 'csoto', GETDATE())");
		}
	

    for ($j = 1; $j <= $fila["plazo"]; $j++) {
        $fecha = new DateTime($fecha->format('Y-m-01'));

        sqlsrv_query($link,"update cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $valor . "' AND cuota = '" . $j . "'");

        $fecha->add(new DateInterval('P1M'));
    }

    $mensaje = "Actualizaci�n exitosa";
    echo "<br>".$mensaje;
    echo "<br><br>";
}
    
   




?>