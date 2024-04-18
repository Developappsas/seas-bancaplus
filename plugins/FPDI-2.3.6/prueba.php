<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include ('../../functions.php');
    include ('../../function_blob_storage.php');
    use setasign\Fpdi\Fpdi;

    //echo "Siu";

    //URL LOCAL
    //URL SERVIDOR
    //require_once('ftp://%2524seas-pruebas-v1@waws-prod-blu-287.ftp.azurewebsites.windows.net/site/wwwroot/plugins/FPDI-2.3.6/fpdf181/fpdf.php'); 
    //URL SERVIDOR
    //require_once('ftp://%2524seas-pruebas-v1@waws-prod-blu-287.ftp.azurewebsites.windows.net/site/wwwroot/plugins/FPDI-2.3.6/fpdi2/src/autoload.php'); 
    //URL LOCAL
    //require_once('fpdi2/src/autoload.php'); 
    //require_once('mis_variables_pdf.php'); 
    
    require_once('fpdf181/fpdf.php'); 
    require_once('fpdi2/src/autoload.php'); 
    
    $codigo = "32668557-532061-33303";
    $bothPath = '532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D';
    
    $contenedor = 'simulaciones';

    $path = generateBlobDownloadLinkWithSAS($contenedor, $bothPath);

    //$stream = fopen('https://docskredit.blob.core.windows.net/simulaciones/532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D', 'rd', false, stream_context_create());

    //$stream = fopen('https://docskredit.blob.core.windows.net/simulaciones/532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D', 'rb', false, stream_context_create());

    //print_r($stream);
    //var_dump($stream);

    //echo " traigo este codigo: ".$codigo." este es el enlace ";
    //echo " traigo este codigo: ".$codigo." link: ".$path;

    

    
    $pdf = new FPDI();

    # Pagina 1
    //URL LOCAL
    //URL SERVIDOR
    //$pdf->setSourceFile('ftp://%2524seas-pruebas-v1@waws-prod-blu-287.ftp.azurewebsites.windows.net/site/wwwroot/plugins/FPDI-2.3.6/Files_Pdf/18_01_2021_demo2.pdf'); 
    
    $pdf->AddPage('L'); 
    //$pdf->setSourceFile('Files_Pdf/18_01_2021_demo1.pdf');
    //$pdf->setSourceFile(generateBlobDownloadLinkWithSAS("simulaciones","532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D"));
    
    //$stream = fopen('https://docskredit.blob.core.windows.net/simulaciones/532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D', 'r', false, stream_context_create());

    $pdf->setSourceFile($path);
    
    //$pdf->setSourceFile('simulaciones/532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf?sv=2017-11-09&sr=b&st=2022-09-22T19:54:37Z&se=2022-09-23T04:54:37Z&sp=r&sig=8tuXr%2BWwJuKP8nOWq6LnCNTOiZyBckaUJXKs1fXsiMA%3D');
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx); 

    $pdf->SetFont('Arial', 'B', '11'); 
    $pdf->SetXY(3,3);
    $pdf->Write(10,$codigo); 
    
    $pdf->Output('Files_Pdf/fin.pdf', 'I'); //SALIDA DEL PDF
    //echo generateBlobDownloadLinkWithSAS("simulaciones","532061/adjuntos/66f1058f5bb1b70105497b266a4648da.pdf");
    
?>
