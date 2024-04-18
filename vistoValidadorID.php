<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validación De ID</title>
    <style type="text/css">.swal-class-terminosycondiciones{ width: 98% !important; }</style>
</head>
<body>
<input type="hidden" id="token" value="<?=$_GET['token']?>">
</body>
</html>
<script type="text/javascript" src="plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script>

    $(document).ready(function () {

        var url_servicios = "https://seas-sql.kredit.com.co/servicios/";

        //Validar estado Token Activo
        $.ajax({
            method: 'POST',
            url: url_servicios + "vistoTokenValidadorID.php",
            dataType : 'json',
            data: { token: $("#token").val(), estadoToken : 1 },
            success:function(response){

                if(response.code == "200"){ //Lanzar ventana de Terminos 
                    Swal.fire({
                        html: '<img src=$urlPrincipal."/images/logo.png"><br><h1 style="text-align: left !important; margin-top:20px;">Autoriza de consulta ante centrales de información financiera:</h1>'+
                        '<p style="text-align: justify !important;">Por medio de la presente autorizo a KREDIT PLUS S.A. con nit 900.387.878-5 y SOLUX S.A.S con Nit 900.470.099-9, para:</p>'+
                        '<p style="text-align: justify !important;"> <b>1.</b> Obtener del sistema Financiero o cualquier otra fuente, información sobre mi (nuestras) relaciones comerciales y que datos sobre mi (nosotros) reportados serán cincularizados de conformidad con la reglamentación vigente.<br/>'+
                        '<b>2.</b> Reportar y consultar a las centrales de información financiera, proforenses y  entidades Financiera de Colombla y demás bancos legalmente autorizados para tal efecto.</p>'+
                        '<p style="text-align: justify !important;">El (los) solicitante (s) autoriza (n) a KREDIT PLUS S.A o SOLUX S.A.S y/o cualquiera de sus filiales, subsidiarias o subordinadas, y/o matriz, de manera permanente e irrevocable con fines estadisticos de control, supervisión y de información comercial para reportar, verificar, procesar, consultar, conservar, suministrar, actualizar y divulgar, a la central de información de la Asociación Bancaria, pro forense y de entidades Financieras de Colombia y a cualquier otra entidad que maneje base de datos con los mismos fines, el nacimiento, modificación, extinción de obligaciones contraidas con anterioridad, al mismo tiempo o con posterioridad a este contrato, fruto de operaciones financieras con KREDIT PLUS S.A. o SOLUX S.A.S.</p><br> <input type="checkbox" id="acepto"> <b>Acepto y Autorizo Terminos & Condiciones</b>',
                        confirmButtonText: 'ACEPTAR',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'swal-class-terminosycondiciones'
                        },
                        focusConfirm: false,
                        preConfirm: () => {
                            const acepto = Swal.getPopup().querySelector('#acepto').checked
                            if (!acepto) {
                                Swal.showValidationMessage(`Por favor marque la casilla si Acepta los terminos.`)
                            }
                            return { acepto: acepto }
                        }
                    }).then((result) => {

                        //Se Actualiza El Token a estado visto
                        $.ajax({
                            method: 'POST',
                            url: url_servicios + "vistoTokenValidadorID.php",
                            dataType : 'json',
                            data: { token: $("#token").val() },
                            success:function(response){

                                if(response.code == "200"){

                                    let timerInterval
                                    Swal.fire({
                                        title: 'Redirrecionando...',
                                        html: 'Se redireccionará a la ventana de Validacion de Identidad',
                                        timer: 2000,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            Swal.showLoading()
                                        },
                                        willClose: () => {
                                            clearInterval(timerInterval)
                                        }
                                    })

                                    window.location = "https://adocolumbia.ado-tech.com/Kredit/validar-persona?key=db92efc69991&projectName=Kredit&callback=https://seas.kredit.com.co/servicios/verificacionADO.php?token="+$("#token").val();
                                }else{
                                    Swal.fire(
                                        '¡Upsssss!',
                                        response.mensaje,
                                        'error'
                                    );
                                }
                            }
                        });
                    })

                }else{
                    Swal.fire(
                        '¡Upsssss!',
                        response.mensaje,
                        'error'
                    );
                }
            }
        });
    });
</script>