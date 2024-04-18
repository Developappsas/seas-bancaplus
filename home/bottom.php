	<div class="footer" id="piepagina">
	    <div class="sede"><?php if (DeviceDetect() == "desktop") { ?>
	            <b>Sede Barranquilla:</b><br>Cra. 53 Av Circunvalar, Edificio BC Empresarial, Oficina 1102
	            <br>email: soporte@kredit.com.co<br>
	        <?php } ?>
	    </div>
	    <div class="copy">Derechos Reservados &copy; 2022
	        <!-- - Kredit Plus S.A.S.-->
	    </div>
	</div>
	</div>
	<!-- -<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>-->
	<script type="text/javascript">
	    function actionPedirCredito() {
	        var btnPedirCredito = document.getElementById("btnPedirCredito");
	        var namebtnPedirCredito = btnPedirCredito.getAttribute("name")

	        var frmAsignarAnalistas = "exe=pedirCredito&idUsuario=" + namebtnPedirCredito;
	        const data = new FormData();
	        data.append('exe', 'pedirCredito');
	        data.append('idUsuario', namebtnPedirCredito);
	        var url = 'https://kreditpoc.azurewebsites.net/home/pilotofdc_funcion.php';


	        var requestOptions = {
	            method: 'POST',
	            body: data,
	            redirect: 'follow'
	        };
	        console.log(requestOptions);

	        fetch("https://kreditpoc.azurewebsites.net/home/pilotofdc_funcion.php", requestOptions)
	            .then(response => response.text())
	            .then(data => {
	                if (data == 1) {
	                    location.reload();
	                } else if (data == 2) {
	                    alert("No hay creditos para asignar");
	                } else if (data == 3) {
	                    alert("Jornada laboral terminada para realizar asignaciones");
	                }
	            });
	    }

	    function actionBtnDisponible() {
	        var btnSalir = document.getElementById("btnSalir");
	        var nameBtnSalir = btnSalir.getAttribute("name");

	        var btnDisponible = document.getElementById("btnDisponible");
	        var namebtnDisponible = btnDisponible.getAttribute("name");

	        if (nameBtnSalir == "g") {
	            alert("NO SE PUEDE REALIZAR CAMBIO DE ESTADO DEL USUARIO AL ESTAR EN TRAMITE CON UN PROCESO");
	        } else {
	            var frmAsignarAnalistas = "exe=disponibleUsuario&idUsuario=" + namebtnDisponible;
	            const data = new FormData();
	            data.append('exe', 'disponibleUsuario');
	            data.append('idUsuario', namebtnDisponible);
	            var url = 'https://seas.kreditplus.com.co/home/pilotofdc_funcion.php';

	            var requestOptions = {
	                method: 'POST',
	                body: data,
	                redirect: 'follow'
	            };
	            console.log(requestOptions);

	            fetch("https://seas.kreditplus.com.co/home/pilotofdc_funcion.php", requestOptions)
	                .then(response => response.text())
	                .then(data => {
	                    if (data == 1 || data == 3) {
	                        location.reload();
	                    } else {
	                        alert("ERROR AL EJECUTAR ACCION");
	                    }
	                });
	        }
	    }
	</script>


	</body>

	</html>