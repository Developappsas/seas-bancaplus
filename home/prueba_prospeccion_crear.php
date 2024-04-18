<?php include ('../prueba_functions.php'); 
ini_set('default_charset', 'UTF-8');
?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

if (($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "COMERCIAL" && $_SESSION["S_TIPO"] != "DIRECTOROFICINA") || $_SESSION["S_IDUNIDADNEGOCIO"] == "'0'")
{
	exit;
}

$link = conectar();

if (($_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA") && !$_REQUEST["id_simulacion"])
{
	$queryDB = "select * from oficinas_usuarios where id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
	
	$rs1 = sqlsrv_query($queryDB, $link);
	
	if (!(sqlsrv_num_rows($rs1)))
	{
		echo "<script>alert('El usuario no tiene oficina asociada. Contacte al Administrador del sistema'); location.href='simulaciones.php';</script>";
		
		exit;
	}
}

$es_freelance = sqlsrv_query("select * from usuarios where id_usuario = '".$_SESSION["S_IDUSUARIO"]."' and (freelance = '1' OR outsourcing = '1')", $link);

if (sqlsrv_num_rows($es_freelance))
{
	$inhabilita_telemercadeo = 1;
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$inhabilita_telemercadeo = "0";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$inhabilita_telemercadeo = "1";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
	{
		$inhabilita_telemercadeo = "0";
		
		$telemercadeo_checked = " checked";
	}
}

?>
<?php include("./top.php"); ?>
<style type="text/css">
.image-upload>input {
    display: none;
}

.image-upload img {
    width: 16px;
    cursor: pointer;
}
</style>
<script language="JavaScript">
<!--
function chequeo_forma() {
    var flag = 0;

    with(document.formato) {
        var arroba = email.value.indexOf("@");
        var substr = email.value.substring(arroba + 1, 100);
        var otra_arroba = substr.indexOf("@");
        var espacio = email.value.indexOf(" ");
        var punto = email.value.lastIndexOf(".");
        var ultimo = email.value.length - 1;

        if ((cedula.value == "") || (nombres.value == "") || (apellidos.value == "") || (telefono.value == "") || (
                direccion.value == "") || (ciudad.value == "") || (celular.value == "") || (email.value == "") || (
                fecha_nacimiento.value == "") || (fecha_inicio_labor.value == "") || (institucion.value == "") || (
                pagaduria.selectedIndex == 0) || (nivel_contratacion.selectedIndex == 0) || (medio_contacto
                .selectedIndex == 0)) {
            alert("Los campos marcados con asterisco(*) son obligatorios");
            return false;
        }
        for (i = 1; i <= 5; i++) {
            if (document.getElementById("id_tipo" + i).selectedIndex != 0 && (document.getElementById(
                    "descripcion" + i).value == "" || document.getElementById("archivo" + i).value == "")) {
                alert("Debe seleccionar el tipo de adjunto, digitar una observación y adjuntar un archivo (Adjunto " +
                    i + ")");
                return false;
            }
            if (document.getElementById("descripcion" + i).value != "" && (document.getElementById("id_tipo" + i)
                    .selectedIndex == 0 || document.getElementById("archivo" + i).value == "")) {
                alert("Debe seleccionar el tipo de adjunto, digitar una observación y adjuntar un archivo (Adjunto " +
                    i + ")");
                return false;
            }
            if (document.getElementById("archivo" + i).value != "" && (document.getElementById("id_tipo" + i)
                    .selectedIndex == 0 || document.getElementById("descripcion" + i).value == "")) {
                alert("Debe seleccionar el tipo de adjunto, digitar una observación y adjuntar un archivo (Adjunto " +
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

        ReplaceComilla(nombres);
        ReplaceComilla(apellidos);
        ReplaceComilla(telefono);
        ReplaceComilla(direccion);
        ReplaceComilla(ciudad);
        ReplaceComilla(celular);
        ReplaceComilla(email);
        ReplaceComilla(institucion);
    }
}
//
-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
    <tr>
        <td class="titulo">
            <center><b>Ingresar Prospecci&oacute;n</b><br><br></center>
        </td>
    </tr>
</table>
<form name=formato method=post action="prueba_prospeccion_crear2.php?v<?php echo rand();?>" enctype="multipart/form-data"
    onSubmit="return chequeo_forma()">
    <table>
        <tr>
            <td>
                <div class="box1 clearfix">
                    <table border="10" cellspacing=1 cellpadding=2>
                        <tr>
                            <td><input type="text" name="cedula" size="20" placeholder="* C&eacute;dula"
                                    onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
                            <td><input type="text" name="nombres" size="20" placeholder="* Nombres"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="apellidos" size="20" placeholder="* Apellidos"></td>
                            <td><input type="text" name="telefono" size="20" placeholder="* Tel&eacute;fono"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="direccion" size="20" placeholder="* Direcci&oacute;n"></td>
                            <td><input type="text" name="ciudad" size="20" placeholder="* Ciudad"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="celular" size="20" placeholder="* Celular"></td>
                            <td><input type="text" name="email" size="20" placeholder="* Correo electr&oacute;nico">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="fecha_nacimiento" size="20"
                                    placeholder="* Fecha Nacimiento"
                                    onChange="if(validarfecha(this.value)==false) {this.value=''; return false}">(AAAA-MM-DD)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="fecha_inicio_labor" size="20"
                                    placeholder="* Fecha Vinculaci&oacute;n"
                                    onChange="if(validarfecha(this.value)==false) {this.value=''; return false}">(AAAA-MM-DD)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="institucion" size="46"
                                    placeholder="* Instituci&oacute;n/Asociaci&oacute;n"></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="pagaduria" style="width:160px">
                                    <option value="">* Pagadur&iacute;a</option>
                                    <?php

$queryDB = "select nombre as pagaduria from pagadurias where estado = '1'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " order by pagaduria";

$rs1 = sqlsrv_query($queryDB, $link);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	echo "<option value=\"".$fila1["pagaduria"]."\">".stripslashes(utf8_decode($fila1["pagaduria"]))."</option>\n";
}

?>
                                </select>
                            </td>
                            <td>
                                <select name="nivel_contratacion" style="width:160px">
                                    <option value="">* Nivel de Contrataci&oacute;n</option>
                                    <option value="PROPIEDAD">PROPIEDAD</option>
                                    <option value="PROVISIONAL">PROVISIONAL</option>
                                    <option value="PENSIONADO">PENSIONADO</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="medio_contacto" style="width:160px">
                                    <option value="">* Medio de Contacto</option>
                                    <option value="REFERIDO">REFERIDO</option>
                                    <option value="AGENDADO CALL CENTER">AGENDADO CALL CENTER</option>
                                    <option value="VISITA EN FRIO">VISITA EN FRIO</option>
                                    <option value="BASE DE DATOS">BASE DE DATOS</option>
                                    <option value="RETANQUEO">RETANQUEO</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" name="telemercadeo" value="1"
                                    <?php echo $telemercadeo_checked ?><?php if ($inhabilita_telemercadeo) { ?>
                                    disabled<?php } ?>>TELEMERCADEO
                            </td>
                        </tr>
                        <tr height="30">
                            <td>Est&aacute; frente al cliente?</td>
                            <td>
                                <input type="radio" name="frente_al_cliente" value="SI">SI
                                <input type="radio" name="frente_al_cliente" value="NO" checked>NO
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            <select name="id_unidad_negocio" name="id_unidad_negocio" style="width:160px">
                                    <option value="">* Unidad de Negocio</option>
                                    <?php 
                                    $consultarUnidadesNegocio="SELECT a.nombre as nombre_unidad,a.id_unidad FROM unidades_negocio a LEFT JOIN usuarios_unidades b ON a.id_unidad=b.id_unidad_negocio WHERE b.id_usuario='".$_SESSION["S_IDUSUARIO"]."' order by id_unidad";
                                    $queryUnidadesNegocio=sqlsrv_query($consultarUnidadesNegocio,$link);
                                    while ($resUnidadesNegocio=sqlsrv_fetch_array($queryUnidadesNegocio))
                                    {
                                        ?>
                                        <option value="<?php echo $resUnidadesNegocio["id_unidad"];?>"><?php echo $resUnidadesNegocio["nombre_unidad"];?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                            <td colspan="2"><textarea name="observaciones" rows="2" cols="41"
                                    placeholder="Observaci&oacute;n"></textarea></td>
                        </tr>
                    </table>
                </div>
                <div class="box1 clearfix">
                    <table border="10" cellspacing=1 cellpadding=2>
                        <?php

for ($i = 1; $i <= 5; $i++)
{

?>
                        <tr>
                            <td>
                                <select id="id_tipo<?php echo $i ?>" name="id_tipo<?php echo $i ?>" style="width:160px">
                                    <option value="">Tipo Adjunto</option>
                                    <?php

	$queryDB = "select id_tipo, nombre from tipos_adjuntos where estado = '1' AND id_tipo IN (".$tiposadjuntos_prospeccion.") order by nombre";
	
	$rs1 = sqlsrv_query($queryDB, $link);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		echo "<option value=\"".$fila1["id_tipo"]."\">".utf8_decode($fila1["nombre"])."</option>\n";
	}
	
?>

                                </select>&nbsp;&nbsp;&nbsp;
                            </td>
                            <td><input type="text" id="descripcion<?php echo $i ?>" name="descripcion<?php echo $i ?>"
                                    maxlength="255" size="20" placeholder="Descripci&oacute;n"></td>
                            <td>
                                <div class="image-upload">
                                    <label for="archivo<?php echo $i ?>"><img src="../images/upload.png"
                                            alt="Click aqu� para subir un adjunto"
                                            title="Click aqu� para subir un adjunto"></label>
                                    <input id="archivo<?php echo $i ?>" name="archivo<?php echo $i ?>" type="file"
                                        onChange="alert('Adjunto<?php echo $i ?> seleccionado');" />
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
        <input type="submit" value="Ingresar">
    </p>
</form>
<?php include("./bottom.php"); ?>