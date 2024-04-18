<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Crear Codigo de barra</title>
    <link rel="stylesheet" href="./css/bootstrap.css" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="./crearCodigo.php" method="post">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="codigo"># Codigo barra</label>
                                <input type="text" autocomplete="off" id="codigo" name="codigo" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <input type="submit" class="btn btn-info" value="Generar Codigo">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <script src="./js/jquery.js"></script>
    <script src="./js/bootstrap.js" crossorigin="anonymous"></script>
</body>
</html>