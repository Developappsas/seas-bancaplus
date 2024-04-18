<?php

include('../functions.php');

if (!isset($_SESSION["S_LOGIN"])) {
	if (!(isset($_GET["cambiar_clave"]) && $_GET["cambiar_clave"] != '')) {
		exit;
	}
}

$link = conectar();
include("top.php");

if (@$_REQUEST["cambiar"] == "1") {

	$password_valido = sqlsrv_query($link, "SELECT login from usuarios where login = '" . $_GET["s_login"] . "' AND password ='" . md5( $_REQUEST["old_password"]) . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
	if (sqlsrv_num_rows($password_valido)) {
		sqlsrv_query($link, "UPDATE usuarios set password ='" . md5( $_REQUEST["new_password"]). "', cambio_clave = 1, fecha_clave = getdate() where login = '" . $_GET["s_login"] . "'");
		echo "<script>alert('Contraseña cambiada exitosamente'); window.location.href='salir.php'</script>";
		exit;
		
	} else {
		echo "<script>alert('Contraseña actual no valida');</script>";
	}
}

?>
<style>
	.invalid{
        color: red;
    }

    .valid{
        color: green;
    }
    strong{
    	font-weight: bold;
    }

</style>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((old_password.value == "") || (new_password.value == "") || (renew_password.value == "")) {
				alert('Debe digitar su contraseña actual, la nueva contraseña y reescribirla');
				return false;
			}
			if (new_password.value != renew_password.value) {
				alert('La nueva contraseña y su confirmacion no coinciden. Debe escribirlas nuevamente');
				new_password.value = '';
				renew_password.value = '';
				return false;
			}
			if (old_password.value == new_password.value) {
				alert('La nueva contraseña debe ser diferente a la contraseña actual. Debe escoger otra nueva contraseña');
				new_password.value = '';
				renew_password.value = '';
				return false;
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Cambiar Contrase&ntilde;a</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="cambiarclave.php?s_login=<?= $_GET["s_login"] ?>&cambiar_clave=1" onSubmit="return chequeo_forma()">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td align="right">Contrase&ntilde;a Actual</td>
							<td><input name="old_password" type="password" maxlength="20" size="25" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}" /></td>
						</tr>
						<tr>
							<td align="right">Nueva Contrase&ntilde;a</td>
							<td><input id="new_password" name="new_password" type="password" autocomplete="off" maxlength="20" size="25" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}" /></td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<div id="pswd_info" style="display: none; margin: 10px;">
						            <strong>Requisitos de Contraseña:</strong>
						            <ul style="margin-left: 15px;">
						            	<li id="letter">* Tener <strong>una letra</strong></li><br>
						            	<li id="capital">* Minimo <strong>una mayúscula</strong></li><br>
						            	<li id="number">* Minimo <strong>un número</strong></li><br>
						            	<li id="length">* <strong>8 carácteres</strong> como mínimo</li><br>
						            </ul>
						        </div>
							</td>
						</tr>
						<tr>
							<td align="right">Re-escriba Nueva Contrase&ntilde;a</td>
							<td><input name="renew_password" type="password" maxlength="20" size="25" onKeyUp="if(FindComilla_Senc_Dobl(this.value)==false) {this.value=''; return false}" /></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><br><input type="hidden" name="cambiar" value="1"><input type="submit" value="Ingresar"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript" src="../jquery-1.9.1.js"></script>
<script language="JavaScript" src="../jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript">

	$("#new_password").keyup(function() {
        // set password variable
        var pswd = $(this).val();
        var errorPw = false;
        //validate the length
        if ( pswd.length < 8 ) {
            $('#length').removeClass('valid').addClass('invalid');
            errorPw = true;
        } else {
            $('#length').removeClass('invalid').addClass('valid');
        }

        //validate letter
        if ( pswd.match(/[A-z]/) ) {
            $('#letter').removeClass('invalid').addClass('valid');
        } else {
            $('#letter').removeClass('valid').addClass('invalid');
            errorPw = true;
        }

        //validate capital letter
        if ( pswd.match(/[A-Z]/) ) {
            $('#capital').removeClass('invalid').addClass('valid');
        } else {
            $('#capital').removeClass('valid').addClass('invalid');
            errorPw = true;
        }

        //validate number
        if ( pswd.match(/\d/) ) {
            $('#number').removeClass('invalid').addClass('valid');
        } else {
            $('#number').removeClass('valid').addClass('invalid');
            errorPw = true;
        }

        if(errorPw){
            $(this).addClass("invalidatePss");
        }else{
            $(this).removeClass("invalidatePss");
        }

      }).focus(function() {
        $('#pswd_info').show();
    }).blur(function() {
        if($("#new_password").hasClass("invalidatePss")){
            $("#new_password").focus();
        }else{
            $('#pswd_info').hide();
        }
    });

    $("#renew_password").blur(function() {
        if($("#new_password").hasClass("invalidatePss")){
            $("#new_password").focus();
        }
    });
</script>
<?php include("bottom.php"); ?>