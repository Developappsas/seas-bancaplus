<?php

//Recibo el codigo del formulario
$codigo = $_POST['codigo'];
if (empty($codigo)) {
    echo "Debe enviar un numero para generar su codigo";
    return;
}
$newCodigo = str_replace("(", "", $codigo);
$newCodigo = str_replace(")", "", $newCodigo);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ver Codigo de barra</title>
    <link rel="stylesheet" href="./css/bootstrap.css" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="generar.php" method="post">
                    <div class="row">
                        <div class="col-4 text-center">
                            <img id="barcode">
                            <input type="hidden" id="imagenC" name="imagenC">
                            <p style="font-size:10px;"><?php echo $codigo ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-center">
                            <input type="submit" name="crearPdf" class="btn btn-info" value="Generar PDF">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <script src="./js/jquery.js"></script>
    <script src="./js/jsBarcode.js"></script>
    <script>
        $( document ).ready(function() {
            var newCodigo = "<?php echo $newCodigo; ?>";
            JsBarcode("#barcode", newCodigo, {
                width: 1,
                height: 60,
                displayValue: false
            });
            var urlImg = document.getElementById("barcode").src;
            $("#imagenC").val(urlImg);
        });
    </script>
    <script src="./js/bootstrap.js" crossorigin="anonymous"></script>
</body>
</html>