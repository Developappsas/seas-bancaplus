<?php include ('../functions.php'); ?>
<?php

/**
 * 2018-02-26  Creado para ingresar CLIENTES por fuera de las BASES DE DATOS (ARCHIVOS) de PAGADURIAS 
 */
if (!$_SESSION["S_LOGIN"] || !$_SESSION["S_TIPO"] == "ADMINISTRADOR") {
    exit;
}

  $link = conectar();
  $queryDB = "select * from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";
  $simulacion_rs = sqlsrv_query($link, $queryDB);
  $simulacion = sqlsrv_fetch_array($simulacion_rs);




// 001

?>
<?php include("top.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
    <tr>
        <td valign="top" width="18"><a href="simulaciones.php ?>"><img src="../images/back_01.gif"></a></td>
        <td class="titulo"><center><b>Correcciones</b><br><br></center></td>
    </tr>
    
</table>

<form id="cliente_form" method="post" action="correcciones2.php">
    <table>
        <tr>
            <td>
                <div class="box1 clearfix">
                    <table border="0" cellspacing=1 cellpadding=2>
                        <thead>
                               <tr></tr>
                            
                                <td>Nombres</td>
                                <td>Comercial</td>
                                <td>Oficina</td>                                
                            
                        </thead>
                        <tr>
                            <td><input type='text' name='nombre' value='<?php echo $simulacion["nombre"]; ?>' size=60 /></td>
                            <td>
                                <select name='id_comercial'>
                                    <option value=""></option>
                                        <?php

                                            $queryDB = "SELECT id_usuario, nombre, apellido from usuarios where estado = 1 and tipo = 'COMERCIAL' order by nombre, apellido";
                                            $usuarios_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                                            
                                            while($fila = sqlsrv_fetch_array($usuarios_rs)){

                                                $selected == ($fila['id_usuario'] == $simulacion['id_usuario'])?'selected':'*';
                                                
                                                echo "<option value='" . $fila['id_usuario'] . "' " . $selected . ">" . 
                                                $fila['nombre'] . ' ' . $fila['apellido'] . "</option>";

                                            }


                                            
                                        ?>
                                </select>
                            </td>
                            <td><input type="text" name="id_oficina" maxlength="20" size="25"
                                       pattern="^\d{3,}$"
                                       required="true"></td>
                        </tr>
                        <tr>
                            <td colspan=2>
                            <button type='submit' name='command' value='asesor'>Modificar</button></td>
                        <tr>

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
