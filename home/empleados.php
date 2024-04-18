<?php include ('../functions.php'); ?>
<?php

/**
 * 2016-04-01  Creado para ingresar CLIENTES por fuera de las BASES DE DATOS (ARCHIVOS) de PAGADURIAS 
 */
if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "OFICINA" && $_SESSION["S_TIPO"] != "OPERACIONES") || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'")
{
    exit;
}
/*
  $link = conectar();
  $queryDB = "select estado from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";
  $simulacion_rs = mysql_query($queryDB, $link);
  $simulacion = mysql_fetch_array($simulacion_rs);
 */

// 001
$habilitar_borrar = ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA") && ($simulacion["estado"] == "ING" || $simulacion["estado"] == "EST" || $simulacion["estado"] == "DES");
?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Ingreso Clientes</b><br><br></center></td>
</tr>
</table>

<form id="cliente_form" method=post action="empleados2.php">
    <table>
        <tr>
            <td>
                <div class="box1 clearfix">
                    <table border="0" cellspacing=1 cellpadding=2>
                        <tr>
                            <td>N&uacute;mero de C&eacute;dula</td>
                            <td><input type="text" name="cedula" maxlength="20" size="25"
                                       pattern="^\d{3,}$"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Apellidos</td>
                            <td><input type="text" name="apellidos" size="80" 
                                       pattern="^[a-zA-Z\s]{3,}$"
                                       placeholder="Ej: Rodriguez Sanchez"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Nombres</td>
                            <td><input type="text" name="nombres" size="80" 
                                       pattern="^[a-zA-Z\s]{3,}$"
                                       placeholder="Ej: Maria Soledad"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Sexo</td>
                            <td>
                                <select name="sexo">
                                    <option value="M">MASCULINO</option>
                                    <option value="F">FEMENINO</option>                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Pagaduria</td>
                            <td>
                                <select name="pagaduria">
<?php

$queryDB = "select nombre as pagaduria from pagadurias where estado = '1'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = sqlsrv_query($link, $queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Instituci&oacute;n/Asociaci&oacute;n</td>
                            <td><input type="text" name="institucion" size="80"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Cargo</td>
                            <td><input type="text" name="cargo" size="80"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Grado</td>
                            <td><input type="text" name="grado" size="20" required="true"></td>
                        </tr>
                        <tr>
                            <td>Fecha inicio labores</td>
                            <td><input type="text" name="fecha_inicio" size="20" 
                                       pattern="^\d{4}-\d{2}-\d{2}$"
                                       placeholder="aaaa-MM-dd"></td>
                        </tr>

                        <tr>
                            <td>Salario B&aacute;sico</td>
                            <td><input type="text" name="basico" size="20"
                                       pattern="^\d+$"
                                       required="true"></td>                                       
                        </tr>
                        <tr>
                            <td>Ingresos</td>
                            <td><input type="text" name="ingresos" size="20"
                                       pattern="^\d+$"
                                       required="true"></td>

                        </tr>
                        <tr>
                            <td>Egresos</td>
                            <td><input type="text" name="egresos" size="20"
                                       pattern="^\d+$"
                                       required="true"></td>                                       
                        </tr>
                        <tr>
                            <td>Neto a pagar</td>
                            <td><input type="text" name="neto" size="20"
                                       pattern="^\d+$"
                                       required="true"></td>                                       
                        </tr>

                        <tr>
                            <td>Nivel educativo</td>
                            <td><input type="text" name="nivel_educativo" size="80"></td>
                        </tr>
                        <tr>
                            <td>Direcci&oacute;n</td>
                            <td><input type="text" name="direccion" size="80" required="true"></td>
                        </tr>
                        <tr>
                            <td>Tel&eacute;fono</td>
                            <td><input type="text" name="telefono" size="80"
                                       pattern="^\d{3,}$"
                                       required="true"></td>                                       
                        </tr>
                        <tr>
                            <td>Correo electr&oacute;nico</td>
                            <td><input type="text" name="correo" size="80" 
                                       pattern="^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Fecha de nacimiento</td>
                            <td><input type="text" name="fecha_nacimiento" size="20" placeholder="aaaa-MM-dd" 
                                       pattern="^\d{4}-\d{2}-\d{2}$"
                                       placeholder="aaaa-MM-dd"                                       
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td>Nivel de contrataci&oacute;n</td>
                            <td>
                                <select name="nivel_contratacion">
                                    <option value="PROPIEDAD">PROPIEDAD</option>
                                    <option value="PROVISIONAL">PROVISIONAL</option>
                                    <option value="PENSIONADO">PENSIONADO</option>                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Departamento</td>
                            <td><input type="text" name="departamento" size="20" 
                                       pattern="^[a-zA-Z\s]{3,}$"
                                       required="true"></td>

                        </tr>
                        <tr>
                            <td>Ciudad</td>
                            <td><input type="text" name="ciudad" size="20"
                                       pattern="^[a-zA-Z\s]{3,}$"
                                       required="true"></td>                                       
                        </tr>
                        <tr>
                            <td>Medio de contacto</td>
                            <td>
                                <select name="medio_contacto" required="true">
                                    <option value=""></option>
                                    <option value="REFERIDO">REFERIDO</option>
                                    <option value="AGENDADO CALL CENTER">AGENDADO CALL CENTER</option>
                                    <option value="VISITA EN FRIO">VISITA EN FRIO</option>                                    
                                    <option value="BASE DE DATOS">BASE DE DATOS</option>
                                    <option value="RETANQUEO">RETANQUEO</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>                            
                            <td><input type="submit" value="Crear"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</form>
<br>

<script src="../assets/jquery/jquery-2.2.2.min.js" type="text/javascript"></script>
<script>
    /*
     $(function () {
     console.log('jqeury is running...');
     $('#crear_button').on('click', function () {
     event.preventDefault();
     if ($('#cliente_form').checkValidity()){
     $.post(
     "empleados2.php",
     $("#cliente_form").serialize()
     )
     .done(function () {
     alert('Ready!');
     });
     }
     });
     });
     */
</script>

<?php include("bottom.php"); ?>
