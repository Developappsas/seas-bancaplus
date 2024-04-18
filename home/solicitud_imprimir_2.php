<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || !($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_TIPO"] == "PROSPECCION"))
{
    exit;
}



?>
<link href="../style_impresion.css" rel="stylesheet" type="text/css">


<br>
<br>
<br>
<br>
<br>
<br>

<input type="hidden" name="action" value="">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<?php



        $queryDB = "SELECT so.*, ci1.municipio as nombre_lugar_expedicion, ci2.municipio as nombre_lugar_nacimiento, ci3.municipio as nombre_ciudad from solicitud so left join ciudades ci1 ON ci1.cod_municipio = so.lugar_expedicion left join ciudades ci2 ON ci2.cod_municipio = so.lugar_nacimiento left join ciudades ci3 ON ci3.cod_municipio = so.ciudad where so.id_simulacion = '".$_REQUEST["id_simulacion"]."'";


    $rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    //Prueba Query
    if ($rs1 == false) {
        if( ($errors = sqlsrv_errors() ) != null) {
            foreach( $errors as $error ) {
                echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
                echo "code: ".$error[ 'code']."<br />";
                echo "message: ".$error[ 'message']."<br />";
            }
        }
    }
    //Fin prueba query
    
    if(sqlsrv_num_rows($rs1)){
    $v = 0;

    while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
    {
     if ($v != 0 ){
        exit;
     }

    ?>
<table border="0" cellspacing=3 cellpadding=0 width="850px">
<tr><td colspan="6" class="tilenews3">&nbsp;</td></tr>

            </table>
            <table border="0" cellspacing=0 cellpadding=5 width="850px">
           <tr>
           <td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           </td>

                        <td >&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td >&nbsp;</td>

                    </tr>



            </table><br>
    <table border="0" cellspacing=3 cellpadding=0 width="850px">
<tr>
  <td colspan="6">&nbsp;</td>

</tr>

      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>


    </table><br>


  <table border="1" cellspacing=0 cellpadding=5 width="850px">
  <tr>
                        <td colspan="6" style="width: 100%; text-align: center; background:#000000;" class="tilenews2">Informaci&oacute;n personal</td>
                    </tr>
                    <tr>

                        <td class="admintable"><label class="admintable">Primer Nombre:</label> <b class="admintable"> <?php echo strtoupper($fila1["nombre1"]) ?></b></td>


                        <td class="admintable"><label class="admintable">Segundo Nombre:</label> <b class="admintable"> <?php echo strtoupper($fila1["nombre2"]) ?></b></td>

                        <td class="admintable"><label  class="admintable">Primer Apellido:</label> <b class="admintable"> <?php echo strtoupper($fila1["apellido1"]) ?></b></td>

                        <td class="admintable"><label class="admintable">Segundo Apellido:</label><b class="admintable"> <?php echo strtoupper($fila1["apellido2"] ) ?></b></td>
                    </tr>
                    <tr>

                        <td><label class="admintable">Documento de Identidad No:</label><b class="admintable"> <?php echo strtoupper($fila1["cedula"]) ?></b></td>

                        <td><label for="cmbTipoDoc" class="admintable">Expedido en :</label> <b class="admintable"> <?php echo strtoupper($fila1["nombre_lugar_expedicion"])  ?></b></td>

                        <td colspan="2"><label class="admintable">Fecha de Expedicion:</label><b class="admintable"> <?php echo strtoupper($fila1["fecha_expedicion"]) ?></b></td>

                    </tr>

                    <tr>

                        <td><label class="admintable">Fecha de Nacimiento:</label><b class="admintable"> <?php echo strtoupper($fila1["fecha_nacimiento"]) ?></b></td>

                        <td><label class="admintable">Sexo:</label><b class="admintable"> <?php echo strtoupper($fila1["sexo"])?></b></td>

                        <td><label class="admintable">Lugar de Nacimiento:</label><b class="admintable"> <?php echo strtoupper($fila1["nombre_lugar_nacimiento"]) ?></b></td>

                        <td><label class="admintable">Estado civil actual:</label><b class="admintable"> <?php echo strtoupper($fila1["estado_civil"]) ?></b></td>

                    </tr>

                    <tr>
                        <td><label class="admintable">No de personas a cargo:</label><b class="admintable"> <?php echo strtoupper($fila1["personas_acargo"]) ?></b></td>

                        <td colspan="2"><label class="admintable">Nombre del conyuge:</label><b class="admintable"> <?php echo strtoupper($fila1["nombre_conyugue"])  ?></b></td>

                        <td><label class="admintable">Documento de Identidad No:</label><b class="admintable"> <?php echo strtoupper($fila1["cedula_conyugue"]) ?></b></td>
                    </tr>


                    <tr>
                        <td colspan="2"><label class="admintable">Nombre de su EPS:</label><b class="admintable"> <?php echo strtoupper($fila1["eps"]) ?></b></td>

                        <td colspan="2"><label class="admintable">Nombre de EPS del conyuge:</label><b class="admintable"> <?php echo strtoupper($fila1["eps_conyugue"]) ?></b></td>


                    </tr>

                    <tr>

                        <td colspan="4"><label class="admintable">Direccion de residencia:</label><b class="admintable"> <?php echo  strtoupper(utf8_decode($fila1["direccion"])) ?></b></td>

                    </tr>

                    <tr>

                        <td colspan="2"><label class="admintable">Ciudad:</label><b class="admintable"> <?php echo strtoupper($fila1["nombre_ciudad"]) ?></b></td>

                        <td colspan="2"><label class="admintable">Lugar de envio de correspondencia:</label><b class="admintable"> <?php echo strtoupper($fila1["lugar_correspondencia"]) ?></b></td>


                    </tr>


                    <tr>

                        <td><label class="admintable">Tipo de vivienda:</label><b class="admintable"> <?php echo strtoupper($fila1["tipo_vivienda"]) ?></b></td>

                        <td><label class="admintable">Telefono de residencia:</label><b class="admintable"> <?php echo strtoupper($fila1["tel_residencia"]) ?></b></td>

                        <td colspan="2"><label class="admintable">Tiempo de residencia:</label><b class="admintable"> <?php echo strtoupper($fila1["anios"]) ?> A&ntilde;os <?php echo strtoupper($fila1["meses"]) ?> meses</b></td>

                    </tr>

                    <tr>

                        <td><label class="admintable">Celular:</label><b class="admintable"> <?php echo strtoupper($fila1["celular"]) ?></b></td>

                        <td><label class="admintable">Plan celular:</label><b class="admintable"> <?php echo strtoupper($fila1["plan_celular"]) ?></b></td>

                        <td><label class="admintable">Tiene plan de datos:</label><b class="admintable"> <?php echo strtoupper($fila1["plan_datos"]) ?></b></td>

                        <td><label class="admintable">Operador:</label><b class="admintable"> <?php echo strtoupper($fila1["operador"]) ?></b></td>

                    </tr>

                    <tr>

                        <td colspan="2"><label class="admintable">E-mail:</label><b class="admintable"> <?php echo strtoupper($fila1["email"]) ?></b></td>

                        <td colspan="2"><label class="admintable">Nivel de estudios:</label><b class="admintable"> <?php echo strtoupper($fila1["nivel_estudios"]) ?></b></td>

                    </tr>
  </table><br>

         <table border="1" cellspacing=0 cellpadding=5 width="850px">
  <tr>
                        <td colspan="6" style="width: 100%; text-align: center; background:#000000;" class="tilenews2">ACTIVIDAD LABORAL</td>
                    </tr>
                    <tr>

                        <td ><label class="admintable">Ocupacion:</label> <b class="admintable"> 
                        <?php if($fila1["ocupacion"] == 1){echo strtoupper("EMPLEADO");}
                        if($fila1["ocupacion"] == 2){echo strtoupper("EMPLEADO SOCIO");}
                        if($fila1["ocupacion"] == 3){echo strtoupper("INDEPENDIENTE");}
                        if($fila1["ocupacion"] == 4){echo strtoupper("HOGAR");}
                        if($fila1["ocupacion"] == 5){echo strtoupper("PENSIONADO-JUBILADO");}
                        if($fila1["ocupacion"] == 6){echo strtoupper("ESTUDIANTE");}
                        if($fila1["ocupacion"] == 8){echo strtoupper("TAXISTA");}
                        if($fila1["ocupacion"] == 9){echo strtoupper("TRANSPORTADOR");}
                        if($fila1["ocupacion"] == 99){echo strtoupper("NINGUNA");}
                        
                         ?></b></td>

                        <td colspan="2"><label class="admintable">Diga de donde:</label> <b class="admintable"> <?php echo strtoupper($fila1["lugar_ocupacion"]) ?></b></td>

                    </tr>
                    <tr>

                        <td><label class="admintable">Usted maneja recursos publicos?</label><b class="admintable"> <?php echo strtoupper($fila1["recursos_publicos"]) ?></b></td>

                        <td colspan="2"><label for="cmbTipoDoc" class="admintable">Independiente o empleado socio,detalle actividad:</label> <b class="admintable"> <?php echo strtoupper($fila1["actividad"]) ?></b></td>


                    </tr>

                    <tr>

                        <td><label class="admintable">Nombre de la empresa:</label><b class="admintable"> <?php echo strtoupper($fila1["nombre_empresa"]) ?></b></td>

                        <td><label class="admintable">Cargo:</label><b class="admintable"> <?php echo strtoupper($fila1["cargo"]) ?></b></td>

                        <td><label class="admintable">Fecha de vinculacion:</label><b class="admintable"> <?php echo strtoupper($fila1["fecha_vinculacion"]) ?></b></td>


                    </tr>

                    <tr>

                        <td><label class="admintable">Direccion de trabajo:</label><b class="admintable"> <?php echo strtoupper(utf8_decode($fila1["direccion_trabajo"])) ?></b></td>

                        <td><label class="admintable">Ciudad:</label><b class="admintable"> <?php echo strtoupper($fila1["ciudad_trabajo"]) ?></b></td>

                        <td><label class="admintable">Telefono de trabajo:</label><b class="admintable"> <?php echo strtoupper($fila1["telefono_trabajo"]) ?></b></td>


                    </tr>

                    </table><br>


                   <table border="1" cellspacing=0 cellpadding=5 width="850px">
                    <tr>
                        <td colspan="6" style="width: 100%; text-align: center; background:#000000;" class="tilenews2">INFORMACION FINANCIERA</td>
                    </tr >
                    <tr >

                        <td width="50%" class="admintable"><label class="admintable">Ingresos Laborales:</label> <b class="admintable">$ <?php echo number_format($fila1["ingresos_laborales"],0) ?></b></td>

                        <td class="admintable"><label class="admintable">Total Egresos:</label> <b class="admintable">$ <?php echo number_format($fila1["total_egresos"],0) ?></b></td>


                    </tr>
                    <tr >

                        <td ><label class="admintable">Otros Ingresos(Demostrables)*:</label><b class="admintable">$ <?php echo number_format($fila1["otros_ingresos"],0) ?></b></td>

                        <td><label class="admintable">Total Activos:</label> <b class="admintable">$ <?php echo number_format($fila1["total_activos"],0) ?></b></td>


                    </tr>

                    <tr >

                        <td><label class="admintable">Total Ingresos:</label><b class="admintable">$ <?php echo number_format($fila1["total_ingresos"],0) ?></b></td>

                        <td><label class="admintable">Total Pasivos:</label><b class="admintable">$ <?php echo number_format($fila1["total_pasivos"],0) ?></b></td>


                    </tr>

                    <tr >

                        <td colspan="2"><label class="admintable">Detalle otro ingresos:</label><b class="admintable">$ <?php echo number_format($fila1["detalle_ingresos"],0) ?></b></td>

                   </tr>

                    </table> <br>


                      <table border="1" cellspacing=0 cellpadding=5 width="850px">
                    <tr>
                        <td colspan="6" style="width: 100%; text-align: center; background:#000000;" class="tilenews2">REFERENCIAS</td>
                    </tr>
                    <tr>
                        <td rowspan="2"><label class="admintable"><b>Familiar</b><br> Nombres y apellidos:<br>(que no viva con usted)</label></td>
                        <td class="admintable"><b class="admintable"> <?php echo strtoupper($fila1["nombre_familiar"]) ?></b></td>
                        <td class="admintable"><label class="admintable">Parentesco:</label> <b class="admintable"> <?php echo strtoupper($fila1["parentesco_familiar"]) ?></b></td>
                        <td class="admintable"><label class="admintable">Telefono:</label> <b class="admintable"> <?php echo strtoupper($fila1["telefono_familiar"]) ?></b></td>
                    </tr>
                    <tr>
                        <td class="admintable"><label class="admintable">Direccion:</label> <b class="admintable"> <?php echo strtoupper(utf8_decode($fila1["direccion_familiar"])) ?></b></td>
                        <td colspan="2" class="admintable"><label class="admintable">Ciudad:</label> <b class="admintable"> <?php echo strtoupper($fila1["ciudad_familiar"]) ?></b></td>
                    </tr>


                    <tr>
                        <td rowspan="2"><label class="admintable"><b>Personal</b><br> Nombres y apellidos:<br>(que no viva con usted)</label></td>
                        <td class="admintable"><b class="admintable"> <?php echo strtoupper($fila1["nombre_personal"]) ?></b></td>
                        <td class="admintable"><label class="admintable">Parentesco:</label> <b class="admintable"> <?php echo strtoupper($fila1["parentesco_personal"]) ?></b></td>
                        <td class="admintable"><label class="admintable">Telefono:</label> <b class="admintable"> <?php echo strtoupper($fila1["telefono_personal"]) ?></b></td>
                    </tr>
                    <tr>
                        <td class="admintable"><label class="admintable">Direccion:</label> <b class="admintable"> <?php echo strtoupper(utf8_decode($fila1["direccion_personal"])) ?></b></td>
                        <td colspan="2" class="admintable"><label class="admintable">Ciudad:</label> <b class="admintable"> <?php echo strtoupper($fila1["ciudad_personal"]) ?></b></td>
                    </tr>




                    </table> <br>

                    <table border="1" cellspacing=0 cellpadding=5 width="850px">
                    <tr>
                        <td colspan="6" style="width: 100%; text-align: center; background:#000000;" class="tilenews2">DATOS DE OPERACIONES INTERNACIONALES</td>
                    </tr>
                    <tr>

                        <td class="admintable" width="50%"><label class="admintable">Su actividad implica transacciones en moneda extranjera:</label> <b class="admintable"> <?php echo strtoupper($fila1["moneda_extranjera"]) ?></b></td>

                        <td class="admintable"><label class="admintable">Tipo de transaccion:</label> <b class="admintable"> <?php echo strtoupper($fila1["tipo_transaccion"]) ?></b></td>

                    </tr>

                    <tr>

                        <td class="admintable"><label class="admintable">Banco:</label> <b class="admintable"> <?php echo strtoupper($fila1["banco"]) ?></b></td>

                        <td class="admintable"><label class="admintable">Cuentas corrien en moneda extranjera No cuenta:</label> <b class="admintable"> <?php echo strtoupper($fila1["num_cuenta"]) ?></b></td>

                    </tr>

                    <tr>

                        <td class="admintable"><label class="admintable">Ciudad:</label> <b class="admintable"> <?php echo strtoupper($fila1["ciudad_operaciones"]) ?></b></td>

                        <td class="admintable"><label class="admintable">PAIS:</label> <b class="admintable"> <?php echo strtoupper($fila1["pais_operaciones"]) ?></b></td>

                    </tr>



                    </table>

<?php
         $v++;}
     }else {
       ?>
   <table border="0" cellspacing=3 cellpadding=0 width="100%">
<tr><td colspan="6" class="tilenews3">NO SE ENCONTRARON REGISTROS</td></tr>

            </table>
       <?php }?>
