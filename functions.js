function isnumber(number) {
	var isnumero=/^\d+$/;
	if((!isnumero.test(number))&&(number!='')) {
		alert ('El valor ingresado debe ser numérico');
		return false;
	}
}

function isnumber_punto(number) {
	var isnumero=/^\d+$/;
	var str;
	var temp;
	var temp2;

	if((!isnumero.test(number))&&(number!='')) {
		temp = number.indexOf(".");

		if(temp != -1) {
			str = number.substr(0, temp);

			if(!isnumero.test(str)) {
				alert ('El valor ingresado debe ser numérico y tener un punto(.) como máximo');
				return false;
			}
			else {
				temp = parseInt(temp) + 1;

				temp2 = number.indexOf(".", temp);

				if(temp2 != -1) {
					alert ('El valor ingresado debe ser numérico y tener un punto(.) como máximo');
					return false;
				}
				else {
					str = number.substr(temp);

					if(!isnumero.test(str)) {
						alert ('El valor ingresado debe ser numérico y tener un punto(.) como máximo');
						return false;
					}
				}
			}
		}
		else {
			alert ('El valor ingresado debe ser numérico y tener un punto(.) como máximo');
			return false;
		}
	}
}

function ReplaceComilla(campo) {
	while (campo.value.indexOf("'") >= 0) {
		campo.value = campo.value.replace("'", "\"");
	}
}

function FindComilla_Senc_Dobl(value) {
	if (value.indexOf("'") >= 0 || value.indexOf("\"") >= 0) {
		alert("El dato ingresado no debe tener comillas sencillas(') ni comillas dobles(\")");
		return false;
	}
}

function separador_miles(x) {
	signo = "";
	
	if (x.value.substr(0, 1) == "-") {
		x.value = x.value.substr(1);
		signo = "-";
	}
	
	y = x.value;
	inicio = x.value.length - 3;
	z = "";
	
	while (inicio > 0) {
		z = ","+y.substr(inicio, 3)+z;
		
		inicio -= 3;
	}
	
	if (x.value.length % 3) 
		restante = x.value.length % 3;
	else
		restante = 3;
	
	z = y.substr(0, restante)+z;
	
	x.value = signo+""+z;
}

function establecer_fecha_from_calendar(campod, campom, campoa, fecha) {
	campod.selectedIndex = parseFloat(fecha.substring(8, 10));
	campom.selectedIndex = parseFloat(fecha.substring(5, 7));
	campoa.value = parseFloat(fecha.substring(0, 4));
}

function addDate(fecha, dias) {
	fecha_split = fecha.split("-");

	fecha = new Date(fecha_split[0], parseInt(fecha_split[1]) - 1, fecha_split[2]);
			
	//Obtenemos los milisegundos desde media noche del 1/1/1970
	tiempo = fecha.getTime();
	
	//Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
	milisegundos = parseInt(dias*24*60*60*1000);
	
	//Modificamos la fecha actual
	fecha.setTime(tiempo + milisegundos);
	day = fecha.getDate();
	month = fecha.getMonth() + 1;
	year = fecha.getFullYear();
	
	if (String(day).length == 1) {
		day = "0"+String(day);
	}
	
	if (String(month).length == 1) {
		month = "0"+String(month);
	}
	
	return year+"-"+month+"-"+day;
}

function diffDate(fecha1, fecha2) {
	fecha1_split = fecha1.split("-");
	fecha2_split = fecha2.split("-");

	fecha1 = new Date(fecha1_split[0], parseInt(fecha1_split[1]) - 1, fecha1_split[2]);
	fecha2 = new Date(fecha2_split[0], parseInt(fecha2_split[1]) - 1, fecha2_split[2]);
			
	//Obtenemos los milisegundos desde media noche del 1/1/1970
	tiempo1 = fecha1.getTime();
	tiempo2 = fecha2.getTime();
	
	diferencia = tiempo2 - tiempo1;
	
	dias = diferencia / 1000 / 60 / 60 / 24;
	
	return dias;
}

function diffMonth(fecha1, fecha2) {
	fecha1_split = fecha1.split("-");
	fecha2_split = fecha2.split("-");

	meses = (parseInt(fecha2_split[0]) - parseInt(fecha1_split[0])) * 12;
	
	meses = meses + (parseInt(fecha2_split[1]) - parseInt(fecha1_split[1]));
	
	return meses;
}

function validarfecha(fecha) {
	var isnumero=/^\d+$/;
	
	fecha_split = fecha.split("-");

	fecha_dia = fecha_split[2];
	fecha_mes = fecha_split[1];
	fecha_ano = fecha_split[0];
	
	if (fecha.length != 10) {
		alert("La Fecha debe tener 10 carácteres");
		return false;
	}
	if (fecha_ano.length != 4) {
		alert("La Fecha no es válida");
		return false;
	}
	else if (fecha_mes.length != 2) {
		alert("La Fecha no es válida");
		return false;
	}
	else if (fecha_dia.length != 2) {
		alert("La Fecha no es válida");
		return false;
	}
	if (!isnumero.test(fecha_dia) || !isnumero.test(fecha_mes) || !isnumero.test(fecha_ano)) {
		alert("La Fecha no es válida");
		return false;
	}
	if ((fecha_dia < "01") || (fecha_dia > "31") || (fecha_mes < "01") || (fecha_mes > "12") || (fecha_ano < "1900") || (((fecha_dia == "30") || (fecha_dia == "31")) && (fecha_mes == "02")) || ((fecha_dia == "31") && ((fecha_mes == "04") || (fecha_mes == "06") || (fecha_mes == "09") || (fecha_mes == "11")))) {
		alert("La Fecha no es válida");
		return false;
	}
	if ((fecha_dia == "29") && (fecha_mes == "02")) {
		x = parseInt(fecha_ano) % 4;
		y = parseInt(fecha_ano) % 100;
		z = parseInt(fecha_ano) % 400;
		
		if (!((z == 0) || ((x == 0) && (y != 0)))) {
			alert("La Fecha no es válida");
			return false;
		}
	}
}

function validarfechacorta(fecha) {
	var isnumero=/^\d+$/;
	
	fecha_split = fecha.split("-");

	fecha_mes = fecha_split[1];
	fecha_ano = fecha_split[0];
	
	if (fecha.length != 7) {
		alert("El dato debe tener 7 carácteres");
		return false;
	}
	if (fecha_ano.length != 4) {
		alert("El año debe tener 4 dígitos");
		return false;
	}
	else if (fecha_mes.length != 2) {
		alert("El mes debe tener 2 dígitos");
		return false;
	}
	if (!isnumero.test(fecha_mes) || !isnumero.test(fecha_ano)) {
		alert("El dato no es válido");
		return false;
	}
	if ((fecha_mes < "01") || (fecha_mes > "12") || (fecha_ano < "1900")) {
		alert("El dato no es válido");
		return false;
	}
}

function validarhora(hora) {
	var isnumero=/^\d+$/;
	
	hora_split = hora.split(":");

	hora_hor = hora_split[0];
	hora_min = hora_split[1];
	
	if (hora.length != 5) {
		alert("La Hora debe tener 5 carácteres");
		return false;
	}
	if (hora_hor.length != 2) {
		alert("La Hora no es válida");
		return false;
	}
	else if (hora_min.length != 2) {
		alert("La Hora no es válida");
		return false;
	}
	if (!isnumero.test(hora_hor) || !isnumero.test(hora_min)) {
		alert("La Hora no es válida");
		return false;
	}
	if ((hora_hor < "01") || (hora_hor > "12")) {
		alert("La Hora no es válida");
		return false;
	}
	if ((hora_min < "00") || (hora_min > "59")) {
		alert("La Hora no es válida");
		return false;
	}
}

function generarClaveDinamica(id_usuario){

	Swal.fire({
        title: 'Generando Clave Dinamica...',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();

    var datos = { id_usuario: id_usuario };

    $.ajax({
        url: '../servicios/configuracion/generar_codigo_usuario.php',
        data: datos,
        type: 'POST',
        async: true,
        dataType: 'json',
        success: function (json) {
            Swal.close();

            if(json.code == 200){
			
				Swal.fire({
					title: 'CLAVE DINAMICA',
					html: '<p>Ingrese este Codigo en la APP Comercial</p><br><b class="codigo_generado">'+json.data+'</b>',
					text: 'Procesando...',
					allowOutsideClick: false,
					allowEscapeKey: false,
					showCloseButton: true,
					showConfirmButton: true,
					showCancelButton: true,
					confirmButtonText: 'NUEVA CLAVE',
					cancelButtonText: 'CERRAR'
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.close();
					    generarClaveDinamica(id_usuario);
				  	}
				});
			}else{
				Swal.fire(json.mensaje, '', 'error');
			}
		},
        error: function (xhr, status) {
            alert('Disculpe, No se Pudo generar la Clave Dinamica');
        }
    });
}