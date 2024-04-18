<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    use setasign\Fpdi\Fpdi;
    use setasign\Fpdi\PdfParser\StreamReader;
    //use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;

    include ('../functions.php');
    include ('../function_blob_storage.php');

    require_once('FPDI-2.3.6/fpdf181/fpdf.php'); 
    require_once('FPDI-2.3.6/fpdi2/src/autoload.php'); 

    $link = conectar();

    $archivo=$_GET["archivo"];
    $id_simulacion=$_GET["id_simulacion"];
    $consecutivo=$_GET["consecutivo"];

    $consultarInformacionCC="SELECT a.id_simulacion,a.cedula,c.nombre_grabado,nro_libranza FROM simulaciones a LEFT JOIN simulaciones_comprascartera b ON a.id_simulacion=b.id_simulacion LEFT JOIN adjuntos c ON c.id_adjunto=b.id_adjunto WHERE a.id_simulacion='".$id_simulacion."' AND b.consecutivo='".$consecutivo."'";
    
    $queryInformacionCC=sqlsrv_query($link, $consultarInformacionCC);
    $resInformacionCC=sqlsrv_fetch_array($queryInformacionCC);
    
    $libranza=substr($resInformacionCC["nro_libranza"],4);
    $prefijo=explode(" ",$resInformacionCC["nro_libranza"]);

    if ($prefijo[0]=="EFEC")
    {
        $prefijo_nro="0";
    }else if ($prefijo[0]=="FIAN"){
        $prefijo_nro="1";
    }

    $libranza = str_replace(" ", "", $libranza);

    $codigo = $resInformacionCC["cedula"]."-".$id_simulacion."-".$libranza."-".$prefijo_nro;
    $bothPath = $id_simulacion.'/adjuntos/'.$resInformacionCC["nombre_grabado"];

    $path = generateBlobDownloadLinkWithSAS('simulaciones', $bothPath);
    $parse = str_replace(" ", '%20', $path);
    $sitio = file_get_contents($parse);

    $streamReader = StreamReader::createByString($sitio);

    try {

        $pdf = new FPDI();

        $pagecount = $pdf->setSourceFile($streamReader);

        for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {

            $tplIdx = $pdf->importPage($pageNo);
        
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, null, null, $size['w'], 300, FALSE);

            $pdf->SetFont('Arial', 'B', '10'); 
            $pdf->SetXY(3,2);
            $pdf->Write(10,$codigo); 

            $pdf->SetFont('Arial', 'B', '10'); 
            $pdf->SetXY(115,2);
            $pdf->Write(10,$codigo); 
            
            $pdf->SetFont('Arial', 'B', '10'); 
            $pdf->SetXY(3,266);
            $pdf->Write(10,$codigo);
            
            $pdf->SetFont('Arial', 'B', '10'); 
            $pdf->SetXY(115,266);
            $pdf->Write(10,$codigo);
        }
        
        $pdf->Output();
    
    } catch (Exception $ex){
        echo ("documento encriptado, no se puede editar; realizar el proceso de manera manual");
    }
    
?>
