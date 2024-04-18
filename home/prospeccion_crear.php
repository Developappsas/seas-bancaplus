<?php
include('../functions.php');
ini_set('default_charset', 'UTF-8');

if (!$_SESSION["S_LOGIN"]) {
    echo "Volver a iniciar Sesion";
    exit;
}

if (($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA" && $_SESSION["S_TIPO"] != "OUTSOURCING" && $_SESSION["S_PREPROSPECCION"] == "1") || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'") {
    echo "No tiene acceso a esta vista";
    exit;
}

if($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_HABILITAR_PROSPECCION"] == 0){
    header('Location: simulaciones.php');
}

$link = conectar_utf();

$valor_cedula="";
$valor_nombres="";
$valor_apellidos="";
$valor_telefono="";
$valor_correo="";
$valor_ciudad="";

if (isset($_GET['token'])){
    $token=$_GET['token'];
    $consulta="SELECT p.id_preprospeccion, p.identificacion, p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido, p.telefono, p.email, c.municipio, c.departamento FROM preprospectar p left join ciudades c on c.id = p.ciudad where id_preprospeccion='".$token."'";
    $query=sqlsrv_query($link,$consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    if (sqlsrv_num_rows($query)>0)
    {
        $response=mysqli_fetch_assoc($query);
        $valor_cedula=$response["identificacion"];
        $valor_primer_nombre= $response["primer_nombre"];
        $valor_segundo_nombre= $response["segundo_nombre"];
        $valor_primer_apellido=$response["primer_apellido"];
        $valor_segundo_apellido=$response["segundo_apellido"];
        $valor_telefono=$response["telefono"];
        $valor_correo=$response["email"];
        $valor_ciudad=$response["municipio"];
    }
}




$queryDB = "SELECT salario_minimo from salario_minimo where ano = YEAR(GETDATE())";
$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
if ($rs1 == false){
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
}

if (!(sqlsrv_num_rows($rs1))) {
    echo "<script>alert('No se ha establecido el salario minimo para el presente ano. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
    exit;
}

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "OUTSOURCING") && !$_REQUEST["id_simulacion"]) {

    $queryDB = "SELECT * from oficinas_usuarios where id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "'";
    $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    if (!(sqlsrv_num_rows($rs1))) {
        echo "<script>alert('El usuario no tiene oficina asociada. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
        exit;
    }
}


$es_freelance = sqlsrv_query($link, "SELECT * from usuarios where id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "' and (freelance = '1' OR outsourcing = '1')");
if (sqlsrv_num_rows($es_freelance)) {
    $inhabilita_telemercadeo = 0;
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] != "OUTSOURCING") {
    if ($_SESSION["S_SUBTIPO"] == "PLANTA"){
        $inhabilita_telemercadeo = "1";
    }
        

    if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS"){ 
         $inhabilita_telemercadeo = "1";
    }
       

    if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO"){
        $inhabilita_telemercadeo = "0";
    }
        

    if ($_SESSION["S_SUBTIPO"] == "EXTERNOS"){
         $inhabilita_telemercadeo = "0";
    }
       

    if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO") {
        $inhabilita_telemercadeo = "0";

        $telemercadeo_checked = " checked";
    }
  


}

include("./top.php");
?>

<style type="text/css">
    .image-upload>input {
        display: none;
    }

    .image-upload img {
        width: 16px;
        cursor: pointer;
    }
</style>
<link href="../plugins/tabler/css/tabler.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-flags.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-payments.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet" />
<link href="../plugins/tabler/css/demo.min.css" rel="stylesheet" />
<link href="../plugins/DataTables/datatables.min.css?v=4" rel="stylesheet">
<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">
<style type="text/css">
    .tab-pane {
        padding: 20px !important;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #ffffff !important;
        background-color: #4299e1 !important;
    }

    .modal {
        pointer-events: none;
    }

    .modal-content {
        pointer-events: all !important;
        cursor: pointer;
    }
</style>
<script language="JavaScript">
    function chequeo_forma() {
        var flag = 0;

        with(document.formato) {
            var arroba = email.value.indexOf("@");
            var substr = email.value.substring(arroba + 1, 100);
            var otra_arroba = substr.indexOf("@");
            var espacio = email.value.indexOf(" ");
            var punto = email.value.lastIndexOf(".");
            var ultimo = email.value.length - 1;

            if ((cedula.value == "") || (id_unidad_negocio.value == '') || (primer_nombre.value == "") ||(primer_apellido.value == "") || (
                    direccion.value == "") || (ciudad.value == "") || (celular.value == "") || (email.value == "") || (
                    fecha_nacimiento.value == "") || (fecha_inicio_labor.value == "") || (institucion.value == "") || (
                    pagaduria.selectedIndex == 0) || (nivel_contratacion.selectedIndex == 0) || (medio_contacto
                    .selectedIndex == 0) || (proposito.selectedIndex == 0)) {
                alert("Los campos marcados con asterisco(*) son obligatorios");
                return false;
            
            for (i = 1; i <= 5; i++) {
                    if (document.getElementById("id_tipo" + i).selectedIndex != 0 && (document.getElementById(
                            "descripcion" + i).value == "" || document.getElementById("archivo" + i).value == "")) {
                        alert("Debe seleccionar el tipo de adjunto, digitar una observacion y adjuntar un archivo (Adjunto " +
                            i + ")");
                        return false;
                    }
                    if (document.getElementById("descripcion" + i).value != "" && (document.getElementById("id_tipo" + i)
                            .selectedIndex == 0 || document.getElementById("archivo" + i).value == "")) {
                        alert("Debe seleccionar el tipo de adjunto, digitar una observacion y adjuntar un archivo (Adjunto " +
                            i + ")");
                        return false;
                    }
                    if (document.getElementById("archivo" + i).value != "" && (document.getElementById("id_tipo" + i)
                            .selectedIndex == 0 || document.getElementById("descripcion" + i).value == "")) {
                        alert("Debe seleccionar el tipo de adjunto, digitar una observacion y adjuntar un archivo (Adjunto " +
                            i + ")");
                        return false;
                    }
                }
                if ((email.value != "") && (arroba < 1 || otra_arroba != -1 || punto - arroba < 2 || ultimo - punto > 3 ||
                        ultimo - punto < 2 || espacio != -1)) {
                    alert("El email no es valido. Debe corregir la informacion.");
                    email.value = "";
                    email.focus();
                    return false;
                }

                ReplaceComilla(primer_nombre);
                ReplaceComilla(segundo_nombre);
                ReplaceComilla(primer_apellido);
                ReplaceComilla(segundo_apellido);
                ReplaceComilla(telefono);
                ReplaceComilla(direccion);
                ReplaceComilla(ciudad);
                ReplaceComilla(celular);
                ReplaceComilla(email);
                ReplaceComilla(institucion);
        }else{
            Swal.fire({
                    title: '¿Desea Autorizar la consulta de centrales financieras por medio de OTP o de manera fisica?',
                    showCancelButton: true,
                    showconfirmButton: true,
                    showDenyButton: true,
                    denyButtonText: `DE MANERA FISICA`,
                    confirmButtonText: `VALIDACIÓN OTP`,
                    cancelButtonText: `CANCELAR`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        //alert("escogiste otp")
                        generarOTP();
                    } else if (result.isDenied) {
                        //alert("escogiste fisico")
                        $("#formato").submit();
                    }
                })
        }
    }
 }
    //
</script>
<table border="0" cellspacing=1 cellpadding=2>
    <tr>
        <td class="titulo">
            
            <center><b>Ingresar Prospecci&oacute;n</b><br><br></center>
        </td>
    </tr>
    </table>
<form name="formato" method="POST" id="formato" enctype="multipart/form-data" action="prospeccion_crear2.php?v<?php echo rand(); ?>" >
    <input type="hidden" name="id_historial_sms_otp" id="id_historial_sms_otp">
    <table>
        <tr>
            <td>
                <div class="box1 clearfix">
                    <table border="10" cellspacing=1 cellpadding=2>
                        <tr>
                            <td><input type="text" name="cedula" id="cedula" size="20" autocomplete="off" placeholder="* C&eacute;dula" value="<?php echo $valor_cedula;?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="primer_nombre" size="20" autocomplete="off" placeholder="*Primer nombre" value="<?php echo $valor_nombres;?>"></td>
                            <td><input type="text" name="segundo_nombre" size="20" autocomplete="off" placeholder="Segundo Nombre" value="<?php echo $valor_nombres;?>"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="primer_apellido" size="20" autocomplete="off" placeholder="*Primer Apellido" value="<?php echo $valor_apellidos;?>"></td>
                            <td><input type="text" name="segundo_apellido" size="20" autocomplete="off" placeholder="Segundo Apellido" value="<?php echo $valor_apellidos;?>"></td>   
                        </tr>
                        <tr>
                            <td><input type="text" name="ciudad" size="20" autocomplete="off" placeholder="* Ciudad" value="<?php echo $valor_ciudad;?>"></td>
                            <td><input type="text" id="direccion" name="direccion" size="20" autocomplete="off" placeholder="* Direcci&oacute;n"></td>
                        </tr>
                        <tr>
                            <td><input type="text" id="email" name="email" autocomplete="off" size="20" placeholder="* Correo electr&oacute;nico" value="<?php echo $valor_correo;?>"></td>
                            <td>
                                <select name="sexo" style="width:165px">
                                    <option value="">* SEXO</option>
                                    <option value="M">MASCULINO</option>
                                    <option value="F">FEMENINO</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="celular" size="20" id="celular" onblur="if(isnumber(this.value)==false) {this.value=''; return false} else { if (this.value == '') { this.value = '';}}" value="<?php echo $valor_telefono;?>" autocomplete="off" size="44" placeholder="* Celular"></td>
                            <td><input type="text" name="telefono" size="20" autocomplete="off" placeholder="Tel&eacute;fono" value="<?php echo $valor_telefono;?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>Tenga en cuenta que el número que digite será utilizado <br> para la validación OTP.</b></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" autocomplete="off" size="20" placeholder="* Fecha Nacimiento" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}">
                            </td>
                            <td><input type="text" id="fecha_inicio_labor" name="fecha_inicio_labor" autocomplete="off" size="20" placeholder="* Fecha Vinculaci&oacute;n" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="institucion" autocomplete="off" size="44" placeholder="* Instituci&oacute;n/Asociaci&oacute;n"></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="pagaduria" style="width:160px">
                                    <option value="">* Pagadur&iacute;a</option>
                                    <?php

                                    $queryDB = "select nombre as pagaduria from pagadurias where estado = '1'";

                                    if ($_SESSION["S_SECTOR"]) {
                                        $queryDB .= " AND sector = '" . $_SESSION["S_SECTOR"] . "'";
                                    }

                                    $queryDB .= " order by pagaduria";

                                    $rs1 = sqlsrv_query($link, $queryDB);

                                    while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
                                        echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
                                    }

                                    ?>
                                 </select>
                            </td>
                            <td>
                                <select name="nivel_contratacion" style="width:160px">
                                    <option value="">* Nivel de Contrataci&oacute;n</option>
                                    <?php
                                    $queryDB = "SELECT nivel_Contratacion_Descripcion FROM nivel_contratacion";
                                    $rs1 = sqlsrv_query($link, $queryDB);

                                    while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){ ?>
                                        <option value="<?=$fila1["nivel_Contratacion_Descripcion"]?>"><?=$fila1["nivel_Contratacion_Descripcion"]?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <br>
                        <tr>
                            <td>
                                <select name="proposito" style="width:165px">
                                    <option value="">* Proposito del Crédito</option>
                                    <option value="1">Vivienda/ Remodelación de vivienda</option>
                                    <option value="2">Electrodomésticos /Muebles de hogar</option>
                                    <option value="3">Educación</option>
                                    <option value="4">Salud</option>
                                    <option value="5">Vehículo</option>
                                    <option value="6">Viajes</option>
                                    <option value="7">Emprendimiento</option>
                                    <option value="8">Pago de Obligaciones Bancarias/ Extra-bancarias</option>
                                    <option value="9">Otros</option>
                                </select>
                            </td>
                            <td>
                                <select style="margin-top: 8px;" name="medio_contacto" style="width:160px">
                                    <option value="">* Medio de Contacto</option>
                                    <option value="REFERIDO">REFERIDO</option>
                                    <option value="AGENDADO CALL CENTER">AGENDADO CALL CENTER</option>
                                    <option value="VISITA EN FRIO">VISITA EN FRIO</option>
                                    <option value="BASE DE DATOS">BASE DE DATOS</option>
                                    <option value="RETANQUEO">RETANQUEO</option>
                                    <option value="EVENTOS">EVENTOS</option>
                                    <option value="UNION_TEMPORAL">c. UNION TEMPORAL</option>
                                    <option value="KREDIT_VIAJERO">KREDIT VIAJERO</option>
                                    <option value="CAMPANA_SOLUX">CAMPAÑA SOLUX</option>
                                    <option value="LEADS">LEADS</option>
                                </select>
                            </td>
                        </tr>
                        <tr height="30">
                            <td>
                                <input style="margin-top: 8px;" type="checkbox" name="telemercadeo" value="1" <?php echo $telemercadeo_checked ?><?php if ($inhabilita_telemercadeo) { ?> disabled<?php } ?>>TELEMERCADEO
                            </td>
                            <td>Est&aacute; frente al cliente?
                                <input type="radio" name="frente_al_cliente" value="SI">SI
                                <input type="radio" name="frente_al_cliente" value="NO" checked>NO
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <select name="id_unidad_negocio" style="width:160px">
                                    <option value="" selected>* Unidad De Negocio</option>
                                    
                                    <?php
                                    $consultarUnidadesNegocio = "SELECT a.nombre as nombre_unidad,a.id_unidad FROM unidades_negocio a LEFT JOIN usuarios_unidades b ON a.id_unidad=b.id_unidad_negocio WHERE ";

                                    if($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO"){
                                        $consultarUnidadesNegocio .= " a.especial != 1 AND";
                                    }

                                    $consultarUnidadesNegocio .= " b.id_usuario='" . $_SESSION["S_IDUSUARIO"] . "' and estado = 1 ORDER BY id_unidad asc";

                                    $queryUnidadesNegocio = sqlsrv_query($link, $consultarUnidadesNegocio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                                    $cantidadUnidadNegocioUsuario = sqlsrv_num_rows($queryUnidadesNegocio);
                                    if ($cantidadUnidadNegocioUsuario == 1) {
                                        while ($resUnidadesNegocio = sqlsrv_fetch_array($queryUnidadesNegocio)) {
                                    ?>
                                            <option selected value="<?php echo $resUnidadesNegocio["id_unidad"]; ?>"><?php echo $resUnidadesNegocio["nombre_unidad"]; ?></option>
                                        <?php
                                        }
                                    } else if ($cantidadUnidadNegocioUsuario > 1) {
                                        ?>

                                        <?php
                                        while ($resUnidadesNegocio = sqlsrv_fetch_array($queryUnidadesNegocio)) {
                                        ?>
                                            <option value="<?php echo $resUnidadesNegocio["id_unidad"]; ?>"><?php echo $resUnidadesNegocio["nombre_unidad"]; ?></option>
                                    <?php
                                        }
                                    }


                                    ?>
                                </select>

                            </td>
                        </tr>
                        <tr>
                        <td colspan="2">
                                <textarea style="margin-top: 10px;" name="observaciones" rows="2" cols="41" placeholder="Observaci&oacute;n"></textarea>
                            </td>
                            <input id="token_Preprospectar" name="token_Preprospectar" type="hidden" value="<?php echo $token; ?>">
                        </tr>
                    </table>
                </div>
                <div class="box1 clearfix">
                    <table border="10" cellspacing=1 cellpadding=2>
                        <?php

                        for ($i = 1; $i <= 5; $i++) {

                        ?>
                            <tr>
                                <td>
                                    <select id="id_tipo<?php echo $i ?>" name="id_tipo<?php echo $i ?>" style="width:160px">
                                        <option value="">Tipo Adjunto</option>
                                        <?php

                                        $queryDB = "select id_tipo, nombre from tipos_adjuntos where estado = '1' AND id_tipo IN (" . $tiposadjuntos_prospeccion . ") order by nombre";

                                        $rs1 = sqlsrv_query($link, $queryDB);

                                        while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
                                            echo "<option value=\"" . $fila1["id_tipo"] . "\">" . utf8_decode($fila1["nombre"]) . "</option>\n";
                                        }

                                        ?>

                                    </select>&nbsp;&nbsp;&nbsp;
                                </td>
                                <td><input type="text" id="descripcion<?php echo $i ?>" name="descripcion<?php echo $i ?>" maxlength="255" size="20" placeholder="Descripci&oacute;n"></td>
                                <td>
                                    <div class="image-upload">
                                        <label for="archivo<?php echo $i ?>"><img src="../images/upload.png" alt="Click aqui para subir un adjunto" title="Click aqui para subir un adjunto"></label>
                                        <input id="archivo<?php echo $i ?>" name="archivo<?php echo $i ?>" type="file" onChange="alert('Adjunto<?php echo $i ?> seleccionado');" />
                                    </div>
                                </td>
                            </tr>
                        <?php

                        }

                        ?>
                    </table>
                </div>
            </td>
        </tr>

    </table>
    <br>
    <p align="center">
    <button style="color: #ffffff !important ; background-color: #4299e1 !important;" name="validarFormulario" id="validarFormulario" onClick="chequeo_forma(); return false;" type="submit" class="btn btn-success btn-sm">GUARDAR PROSPECCION</button>
        <button style="color: #ffffff !important ; background-color: #4299e1 !important;" type="button" name="validarOTP" id="validarOTP" data-bs-toggle="modal" data-bs-target="#modalAddTasa" value="1" hidden> modal</button>
    </p>
</form>
<div class="modal modal-blur fade modal-tabler" id="modalAddTasa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AUTORIZACIÓN DE CONSULTA</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <p style="font-size: 14px;">Por medio del siguiente codigo de validación OTP, Autorizo a KREDIT PLUS S.A. a ser consultado ante centrales de información financiera, juridica y forense. Acepto que este número celular (<b id="celular_modal"></b>) no podrá ser modificado durante el proceso de crédito</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="mb-1">
                            <label class="form-label">INGRESE CÓDIGO RECIBIDO</label>
                            <input type="text" onblur="if(isnumber(this.value)==false) {this.value=''; return false} else { if (this.value == '') { this.value = '';}}" id="codigoModal" class="form-control">
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-lg-12">
                        <label class="form-label" id="mensajemodal"></label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                    CANCELAR
                </a>
                <a name="add" id="btnSaveModal" onclick="validarOTP(); return false;" class="btn btn-primary ms-auto">
                    ENVIAR
                    <img style="margin-left: 10%;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAABq0lEQVR4nK2SS0sCYRSGDYKgNtG2TfQT+gEtg7bt+wktqk2tBiKKkKzEMssuGqMlpmllKCalKV2moSwrlWo0Mx1znBwTL6MnLMYW3iJ84Nt95+Gc9xwerx6INnQoX6ru/lcxIljpdHl9MX+QTBiOzg5FMm0/D6DhzwKxYnc0lc4AR4SiM2Y77lhA9SMDc3NNNQUqw/EWlCGe+MyZ7bhzUaGfRWbWWisKjDYMhyoUurNeOAlUZ5aPi1XtJYJ9K3Z5dk/A9eMreAIkBCI0vH8kgEmmgGVzRRGby8HFjTuE6i06wep2V1HAl6q7z++es/bbJyj3zh984HwKfsv9ZAzCNANXHoLRmGwW4bqmt6aA6879QoI/TAEZY8Dp8TFak/1HsGvFcO7Tgz8MRCgKISoOdCIJ6SxbHCGfz4PLS7ypDEfqKcnm7whGG3ZdLcRMJgsO3OUrhDgpVnaUhKgxnWgrpe/AXW6Z1iQanF5uq7hGqepAUGiPg6Lj3/tf2jSMIYikueYhTUiUPaEIxQbJaHrPcorNoztDCII01izkGObLW8QKvUUo0/T9uaiefAFXL8TTCy4+2QAAAABJRU5ErkJggg==">
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>

<!-- Tabler Core -->
<script src="../plugins/tabler/js/tabler.min.js"></script>
<script src="../plugins/tabler/js/demo.min.js"></script>

<link href="../jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>

<script type="text/javascript">

    function generarOTP() {
        $("#id_historial_sms_otp").val('');
        var celular = $("#celular").val();
        var cedula = $("#cedula").val();

        Swal.fire({
            title: 'Generando envio de SMS',
            text: 'Procesando...',
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        $.ajax({
            url: '../servicios/OTP/generarSMSOTP.php',
            data: {
                celular: celular,
                cedula: cedula
            },
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                Swal.close();

                $("#celular_modal").text(celular);

                if (json.code == "200") {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Mensaje OTP Enviado. Verifique e ingreselo en la siguiente ventana',
                        showConfirmButton: true
                    });

                    document.getElementById("validarOTP").click();
                    $("#id_historial_sms_otp").val(json.id);
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: json.mensaje,
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status) {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error, intente más tarde o consulte el administrador',
                    showConfirmButton: true
                });
            }
        });
    }

    function validarOTP() {
        var celular = $("#celular").val();
        var codigo = $("#codigoModal").val();
        var id = $("#id_historial_sms_otp").val();

        Swal.fire({
            title: 'Generando validación del codigo',
            text: 'Procesando...',
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        $.ajax({
            url: '../servicios/OTP/validarSMSOTP.php',
            data: {
                celular: celular,
                codigo: codigo,
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                Swal.close();

                if (json.code == "200") {
                    Swal.fire({
                        title: json.mensaje,
                        icon: 'success',
                        confirmButtonText: 'ACEPTAR',
                        allowOutsideClick: false,
                        focusConfirm: false,
                    }).then((result) => {
                        $("#formato").submit();
                    })                   
                } else if (json.code == "300") {
                    
                    $("#codigoModal").val('');
                    
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: json.mensaje,
                        showConfirmButton: true
                    });
                } else {
                    
                    $("#codigoModal").val('');

                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: json.mensaje,
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status) {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error, intente más tarde o consulte el administrador',
                    showConfirmButton: true
                });
            }
        });
    }
    $.datepicker.regional['es'] = {
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
        weekHeader: 'Sm',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearRange: '-100:+0',
        changeMonth: true,
        changeYear: true,
    };

    $.datepicker.setDefaults($.datepicker.regional['es']);

    $(function () {
        var fechaActual = new Date();
        var fecha_18 = new Date((fechaActual.getFullYear()-18), fechaActual.getMonth(), fechaActual.getDate());
        var fecha_1 = new Date(fechaActual.getFullYear(), fechaActual.getMonth()-6, fechaActual.getDate());

        $("#fecha_nacimiento").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-90:-18",
            maxDate: fecha_18
        });

        $("#fecha_inicio_labor").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-80:-1",
            maxDate: fecha_1
        });
    });

</script>

<?php include("./bottom.php"); ?>