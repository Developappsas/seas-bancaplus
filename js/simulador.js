$(document).ready(function () {
    consultarValidacionID();
    consultar_asegurabilidad();
    consultar_declaracion_enfermedad();
    formatoCuenta(true, document.getElementById('nro_cuenta_vista'), document.getElementById('nro_cuenta'));
});

function consultarValidacionID() {

    var id_simulacion = $("#id_simulacion").val();
    var id_usuario = $("#id_usuario").val();

    $("#verImgValidacion").css("display", "none");

    $.ajax({
        url: '../servicios/consultarValidacionID.php',
        data: { id_simulacion: id_simulacion, id_usuario: id_usuario },
        type: 'POST',
        dataType: 'json',
        success: function (json) {

            if (json.respuesta == 100) {//Verificado otp

                $("#verificacion").val("VERIFICADO OTP");
                $("#verificacion").css("background-color", 'rgb(6 175 116)');

                $("#verImgValidacion").css("display", "inline-table");
            } else if (json.respuesta == 2 || json.respuesta == 14) {//Verificado

                $("#verificacion").val("VERIFICADO");
                $("#verificacion").css("background-color", 'rgb(6 175 116)');

                $("#verImgValidacion").css("display", "inline-table");

                if (json.respuesta == 2) {
                    if (json.generar_doc == 1) {
                        Swal.fire({
                            title: 'Generando Adjunto de Cedula desde ADO...',
                            text: 'Procesando...',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        $.ajax({
                            url: '../formatos/generarDocumentoADO.php',
                            data: { id_simulacion: id_simulacion },
                            type: 'POST',
                            dataType: 'json',
                            success: function (json2) {
                                Swal.close();

                                if (json2.code == "200") {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: json2.mensaje,
                                        showConfirmButton: true
                                    });
                                } else {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: json2.mensaje,
                                        showConfirmButton: true
                                    });
                                }
                            }
                        });
                    }
                }
            } else if (json.respuesta == 1) {//Pendiente

                $("#verificacion").val("Pend. validación ADO");
                $("#verificacion").css("background-color", 'rgb(255 167 0)');
            } else {//Algun Error

                if (json.respuesta === null || json.respuesta == 0) {
                    $("#verificacion").val("SIN VERIFICACION");
                } else {
                    $("#verificacion").val(json.mensaje);
                    $("#verificacion").attr("title", json.mensaje);
                    $("#verImgValidacion").css("display", "inline-table");
                }

                $("#verificacion").css("background-color", 'rgb(255 83 36)');
                $("#btnVerificacionID").css("display", 'inline');
                $("#textoMensajeAdo").text(json.descripcion);
            }
        },
        error: function (xhr, status) {
            $("#verificacion").val("");
            $("#verificacion").css("background-color", '#fdc3b3');
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Atencion!',
                text: 'Disculpe, No se pudo consultar el estado de validación del Documento de identidad del cliente.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: true,
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

function reenviarCorreoValidacionID(element, correo, nombre) {

    var id_usuario = $("#id_usuario").val();
    var id_simulacion = $("#id_simulacion").val();

    $("#btnVerificacionID").css("display", "none");

    Swal.fire(
        {
            title: 'Por favor aguarde unos segundos',
            text: 'Procesando...',
            allowOutsideClick: false,
            allowEscapeKey: false
        }
    );
    Swal.showLoading();
    var datos = { id_simulacion: id_simulacion, nombre: nombre, correo: correo, id_usuario: id_usuario };

    $.ajax({
        url: '../servicios/enviar_correo_validacion_id.php',
        data: datos,
        type: 'POST',
        async: true,
        dataType: 'json',
        success: function (json) {
            Swal.close();

            if (json.code == "200") {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: json.mensaje,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: json.mensaje,
                    showConfirmButton: true,
                    timer: 1500
                });
            }
            $("#btnVerificacionID").css("display", "inline");
        },
        error: function (xhr, status) {
            //alert('Disculpe, No se pudo enviar correo de validación de identidad del cliente.');
            $("#btnVerificacionID").css("display", "inline");
        }
    });
}


function reenviarFormularioDigital(token, id_simulacion) {
    // Swal.fire({
    //     title: 'Indique Causal de Reenvio',
    //     showDenyButton: true,
    //     showCancelButton: false,
    //     confirmButtonText: 'SOLO REENVIAR CORREO',
    //     denyButtonText: 'REENVIAR CORREO Y GENERAR NUEVO NUMERO LIBRANZA',
    //     confirmButtonColor: '#00a1e5',
    //     denyButtonColor: '#00a1e5',
    // }).then((result) => {
    //     var pagare = "";
    //     if (result.isConfirmed) {
    //         pagare = "NO";
    //     } else if (result.isDenied) {
    //         pagare = "SI";
    //     }

    //     if (pagare != "") {
    Swal.fire({
        title: 'Reenviando correo',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();
    var info = JSON.stringify({ "reenviar": "SI", "token": token, "pagare": 'SI', "id_usuario": $("#id_usuario").val(), 'id_simulacion': $("#id_simulacion").val() });
    console.log(info);
    $.ajax({
        url: '../servicios/enviar_correo_experian.php',
        data: info,
        type: 'POST',
        async: true,
        dataType: 'json',
        success: function (json) {
            Swal.close();
            if (json.code == "200") {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: json.mensaje,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: json.mensaje,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        },
        error: function (xhr, status) {
            //alert('Disculpe, No se pudo enviar correo de validación de identidad del cliente.');
        }
    });
    //        }
    //     });
}


function validarTipoCuentaVacio() {
    if ($('#id_banco').val() == '' && $('#tipo_cuenta').val() == '') {
        $('#tipo_cuenta').focus();
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Seleccione Banco y Tipo Cuenta',
            showConfirmButton: true
        });
    } else {
        if ($('#id_banco').val() == '') {
            $('#tipo_cuenta').focus();
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Seleccione el Banco',
                showConfirmButton: true
            });
        } else if ($('#tipo_cuenta').val() == '') {
            $('#tipo_cuenta').focus();
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Seleccione Tipo de Cuenta',
                showConfirmButton: true
            });
        }
    }
}


function formatoCuenta(e, elemento, elementoOculto) {
    string = elemento.value.replaceAll('-', '', 'm');
    if (e.key != "Backspace" && e.key != "Delete") { //Si no estamos borrando
        nuevoString = '';
        for (var i = 0; i < string.length; i++) {
            var filtro = '1234567890-';//Caracteresvalidos
            if (filtro.indexOf(string.charAt(i)) != -1) {
                if (string.length > 4) {
                    switch (i) {
                        case 3: nuevoString += string.charAt(i) + "-"; break;
                        case 7: nuevoString += string.charAt(i) + '-'; break;
                        case 11: nuevoString += string.charAt(i) + '-'; break;
                        default: nuevoString += string.charAt(i); break;
                    }
                } else {
                    nuevoString = string;
                }
            }
        }
        elemento.value = nuevoString;
    }
    elementoOculto.value = string;
}

function calcular_plazo_simulador(plazo_actual, plazo_maximo_segun_edad) {
    var fecha_nacimiento = $("#fecha_nacimiento").val();
    var nivel_contratacion = $("#nivel_contratacion").val();
    var sexo = $("#sexo").val();
    var pagaduria = $("#pagaduria").val();
    var id_simulacion = $("#id_simulacion").val();

    if (sexo == '') {
        sexo = 'M';
    }

    Swal.fire({
        title: 'Calculando Plazo...',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();

    var datos = { id_simulacion: id_simulacion, fecha_nacimiento: fecha_nacimiento, nivel_contratacion: nivel_contratacion, sexo: sexo, pagaduria: pagaduria };

    $.ajax({
        url: '../servicios/Simulaciones/calcular_plazo.php',
        data: datos,
        type: 'POST',
        async: true,
        dataType: 'json',
        success: function (json) {
            Swal.close();
            const inputValue = $("#plazo").val();
            Swal.fire({
                title: 'PLAZO DEL CREDITO',
                html: '<b>' + json.mensaje_simulador + '</b>',
                input: 'number',
                inputPlaceholder: '168',
                inputValue,
                inputAttributes: {
                    min: 0,
                    max: json.plazo,
                    required: true
                },
                inputValidator: (value) => {

                    if (isnumber(value) == false) {
                        return 'Valor del Plazo Incorrecto';
                    } else {                        
                        if (parseInt(value) > parseInt(json.plazo)) {
                            return 'El Plazo NO debe ser mayor a:   (<b style="font-weight: bold;"> ' + json.plazo + ' meses</b>)';
                        }
                    }
                },
                showCancelButton: true,
                cancelButtonText: 'CANCELAR',
                confirmButtonText: 'CAMBIAR PLAZO',
                showLoaderOnConfirm: true,
                preConfirm: (plazo_ingresado) => {
                    $("#plazo").val(plazo_ingresado).change();
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#plazo").blur();
                }
            })
        },
        error: function (xhr, status) {
            alert('Disculpe, No se pudo consultar el Plazo sugerido.');
        }
    });
}


function validarNumCuenta() {
    if ($('#nro_cuenta_vista').val() != '') {
        valor = $('#nro_cuenta_vista').val();
        if (valor.charAt(valor.length - 1) == '-') {
            valor = valor.substring(0, valor.length - 1);
            $('#nro_cuenta_vista').val(valor);
        }

        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Confirme N° de Cuenta: ' + valor,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: true,
            confirmButtonText: 'Aceptar'
        });
    }
}

$('#btnGuardarReqInstruccionGiro').click(function(e){
    e.preventDefault(); 

    if($("#reqexcepReq").val() == "" || $("#idTipoReq").val() == "" || $("#idAreaReq").val() == "" || $("#observacionReq").val() == ""  || $("#fechaVencimientoReq").val() == ""){
        Swal.fire("Por favor llene todo el formulario para continuar", '', 'error');
        return false;
    }

    Swal.fire({
        title: 'Por favor aguarde unos segundos',
        text: 'Procesando...'
    });
    Swal.showLoading();

    var formData = new FormData();
    formData.append("id_simulacion", $("#id_simulacion").val());
    formData.append("reqexcepReq", $("#reqexcepReq").val());
    formData.append("idTipoReq", $("#idTipoReq").val());
    formData.append("idAreaReq", $("#idAreaReq").val());
    formData.append("fecha_vencimiento", $("#fechaVencimientoReq").val());
    formData.append("observacionReq", $("#observacionReq").val());
    formData.append("descripcionReq", $("#descripcionReq").val());
    formData.append("archivoReq", $("#archivoReq")[0].files[0]);

    $.ajax({
        type: 'POST',
        url: '../servicios/registrar_requerimiento.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data){
            Swal.close();
            var json = $.parseJSON(data);
            if (json.code==200){
                Swal.fire({
                    title: "Requerimiento Guardado, el credito pasará al subestado 6.1",
                    icon: 'success',
                    showConfirmButton: true
                });

                setInterval("location.reload()",3000);
            }else if (json.code){
                Swal.fire({
                    icon: 'error',
                    title: json.mensaje,
                    showConfirmButton: true
                });
            }else{
                Swal.fire({
                    title: "Ha ocurrido un error. ¡Vuelva a intentarlo!",
                    icon: 'error',
                    showConfirmButton: true
                });
            }
            return false;
        }
    }); 
});

function habilitarFirma(){
    swal.fire({
        title: 'Anular Firma digital',
        icon: 'warning',
        html: 'Al realizar esta accion se eliminaran datos asociados a la Firma Digital',
        showCancelButton: true,
        confirmButtonText: 'Anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3E5ABB',
        denyButtonColor: '#ff5200' 
    }).then((result)=>{    
        if(result.isConfirmed){
            var data = "&id_simulacion=" + $("#id_simulacion").val()
                     + "&usuario_habilitacion=" + $("#id_usuario").val();
            console.log(data)
            $.ajax({
                type: 'POST',
                url: '../servicios/firma_digital/habilitar_creditos.php',
                dataType: 'json',
                data: data,
                success: function (response){
                    if(response.Estado == 200){
                        Swal.fire({
                            icon: 'success',
                            title: 'Alerta',
                            text:response.Mensaje,
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload()
                        }
                        });
                    } else if(response.Estado == 403){
                        Swal.fire({
                            icon: 'warning',
                            title: response.Estado,
                            text:response.Mensaje,
                        })
                    }else if (response.Estado == 404){
                        Swal.fire({
                            icon: 'warning',
                            title: response.Estado,
                            text:response.Mensaje,
                        })
                    } 
                }, error: function(){
                    Swal.fire({
                        icon: 'warning',
                        title: '404'                       
                    }) 
                }
            });
        }
    });
}

$("#check_operado").change(function(){
    var operado = $("#check_operado").prop('checked');
    
    if(operado === false){
        $("#operaciones_razon").prop('disabled', true)
        $("#operaciones_razon").val(null);
    }else{
        $("#operaciones_razon").prop('disabled', false)
    }
})

$("#check_hospitalizado").change(function(){
   var hospitalizado = $("#check_hospitalizado").prop('checked'); 
    
    if(hospitalizado === false){
        $("#hospitalizado_razon").prop('disabled', true)
        $("#hospitalizado_razon").val(null);
    }else{
        $("#hospitalizado_razon").prop('disabled', false) 
    }
})

function declaracion_enfermedad(){
    var data = {
        id_simulacion:$("#id_simulacion").val(),
        asma: $('#check_asma').prop('checked') ? 1 : 0,
        diabetes: $('#check_diabetes').prop('checked') ? 1 : 0,
        cancer: $('#check_cancer').prop('checked') ? 1 : 0,
        vih: $('#check_vih').prop('checked') ? 1 : 0,
        hipertension_arterial: $('#check_hipertension').prop('checked') ? 1 : 0,
        tiroides: $('#check_tiroides').prop('checked') ? 1 : 0,
        cirugia_bariatrica: $('#check_cirugia').prop('checked') ? 1 : 0,
        tabaquismo: $('#check_tabaquismo').prop('checked') ? 1 : 0,
        enfermedad_pulmonar: $('#check_pulmonar').prop('checked') ? 1 : 0,
        enfermedad_corazon: $('#check_enfermedad_corazon').prop('checked') ? 1 : 0,
        artritis: $('#check_artritis').prop('checked') ? 1 : 0,
        glaucoma: $('#check_glaucoma').prop('checked') ? 1 : 0,
        hepatitis: $('#check_hepatitis').prop('checked') ? 1 : 0,
        otra: $("#otra_enfermedad").val(),
        hospitalizado:$("#hospitalizado_razon").val(),
        operaciones:$("#operaciones_razon").val()
    }

    $.ajax({
        type:"POST",
        url:"../servicios/Simulaciones/guardar_declaracion_enfermedades.php",
        datatype:"json",
        data: data,
        success: function(response){
            
        }
    })
}

function consultar_declaracion_enfermedad(){
    
    var id_subestado = $("#id_subestado").val()
    var tipo = $("#s_tipo").val()
    var subtipo = $("#s_subtipo").val()
    
    if(id_subestado== 56 && (subtipo=='ANALISTA_REFERENCIA' || tipo =="ADMINISTRADOR")){
        $(".enfermedad").prop("disabled", false)
    }else{
        $(".enfermedad").prop("disabled", true)
    }

    $.ajax({
        type:"POST",
        url:"../servicios/Simulaciones/consultar_declaracion_enfermedades.php",
        dataType:"json",
        data:{
           id_simulacion: $("#id_simulacion").val()
        },
        success: function(response){
            if(response.estado==200){
                $("#check_asma").prop("checked", response.datos.asma === "1");
                $("#check_cancer").prop("checked", response.datos.cancer === "1");
                $("#check_cirugia").prop("checked", response.datos.cirugia_bariatrica === "1");
                $("#check_diabetes").prop("checked", response.datos.diabetes === "1");
                $("#check_enfermedad_corazon").prop("checked", response.datos.enfermedad_corazon === "1");
                $("#check_artritis").prop("checked", response.datos.artritis === "1");
                $("#check_pulmonar").prop("checked", response.datos.enfermedad_pulmonar === "1");
                $("#check_glaucoma").prop("checked", response.datos.glaucoma === "1");
                $("#check_hepatitis").prop("checked", response.datos.hepatitis === "1");
                $("#check_hipertension").prop("checked", response.datos.hipertension_arterial === "1");
                $("#check_tabaquismo").prop("checked", response.datos.tabaquismo === "1");
                $("#check_tiroides").prop("checked", response.datos.tiroides === "1");
                $("#check_vih").prop("checked", response.datos.vih === "1");
                $("#hospitalizado_razon").val(response.datos.hospitalizado_ultimo_ano);
                $("#otra_enfermedad").val(response.datos.otra);
                $("#operaciones_razon").val(response.datos.operado_ultimos_dos_anos);

                if(response.datos.operado_ultimos_dos_anos !=''){
                    $("#check_operado").prop("checked", true);
                }
                
                if(response.datos.hospitalizado_ultimo_ano!=''){
                    $("#check_hospitalizado").prop("checked", true);
                }
            }            
        }
    })
}
function consultar_asegurabilidad(){
    var IdSolicitud =$(".estado_asegurabilidad").attr("id");
    var estado =$("#estadoConsultar").val();
    const metodo = "EstadoSolicitud";
    console.log(estado)
    if(IdSolicitud!="0" && (estado !='DST' && estado!='NEG'&& estado !='DES')){
        $.ajax({
            type: "POST",
            url: "../servicios/asegurabilidad_colpensiones/servicios_colpensiones.php",
            dataType:"json",
            data:{
                IdSolicitud: IdSolicitud,
                metodo: metodo
            },
            success: function(resultado){
                $.ajax({
                    type: 'POST',
                    url: '../servicios/asegurabilidad_colpensiones/validar_cambio_valores.php',
                    dataType:"json",
                    data:{
                        id_solicitud: IdSolicitud,
                        cedula: $("input[name='cedula']").val()

                    },
                    success: function(data){
                        if(data.code == 200){
                            asegurar_nuevamente();
                        }

                    }
                }); 
                if(resultado.negar_credito == 1){
                    $.ajax({
                        type: 'POST',
                        url: "../servicios/asegurabilidad_colpensiones/desistir_noasegurable.php",
                        data:{
                            id_simulacion:$("#id_simulacion").val()
                        },
                        success: function(respuesta){
                            if(estado==200){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Atencion',
                                    text:'Credito Negado por Aseguradora',
                                    confirmButtonText: 'Ok'
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.reload();
                                    }
                                })
                            }else if(estado==300){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text:resultado.mensaje,
                                    confirmButtonText: 'Ok'
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.reload();
                                    }
                                });
                            }else{
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Atencion',
                                    text:resultado.mensaje,
                                    confirmButtonText: 'Ok'
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.reload();
                                    }
                                });
                            }

                        }
                    })
                }
               var eventos =JSON.parse(resultado.mensaje.Eventos)
                var cantidad = eventos.length;
                $(".estado_asegurabilidad").val(eventos[cantidad-1].Evento);
                var InformacionAdicional =''
                var ObservacionMedico =''
                var ObservacionCallCenter=''
                if(eventos[cantidad - 1].InformacionAdicional ==null ||eventos[cantidad - 1].InformacionAdicional==''){ 
                    InformacionAdicional = "No hay Informacion Adicional"
                }else{
                    InformacionAdicional = eventos[cantidad - 1].InformacionAdicional
                }

                if(eventos[cantidad - 1].ObservacionMedico ==null ||eventos[cantidad - 1].ObservacionMedico==''){ 
                    ObservacionMedico = "No hay Observaciones medicas"
                }else{
                    ObservacionMedico = eventos[cantidad - 1].ObservacionMedico
                }

                if(eventos[cantidad - 1].ObservacionCallCenter ==null ||eventos[cantidad - 1].ObservacionCallCenter==''){ 
                    ObservacionCallCenter = "No hay Observaciones de Call Center"
                }else{
                    ObservacionCallCenter = eventos[cantidad - 1].ObservacionCallCenter 
                }
                var IdCalificacion = eventos[cantidad - 1].IdCalificacion
                $("#"+IdSolicitud).attr('idCalificacion', IdCalificacion)                
                if(IdCalificacion==2 ||IdCalificacion==3 ||IdCalificacion==5 ||IdCalificacion==7 ||IdCalificacion==18 ||IdCalificacion==19 ||IdCalificacion==24 ||IdCalificacion==25 ){
                    $("#"+IdSolicitud).css("background", "#06af74")
                }else if(IdCalificacion==6 ||IdCalificacion==9 ||IdCalificacion==10 ||IdCalificacion==11 ||IdCalificacion==12 ||IdCalificacion==13 ||IdCalificacion==17 ||IdCalificacion==22 ||IdCalificacion==23 ||IdCalificacion==28){
                    $("#"+IdSolicitud).css("background", "#ff5324")
                }else{
                    $("#"+IdSolicitud).css("background", "#f3cc30")
                }

                $("#detalle_asegurabilidad").click(function(e){
                    e.preventDefault();
                    swal.fire({
                        title: eventos[cantidad-1].Evento,
                        icon:'info',
                        html: `
                            <div style="text-align: left;">
                              <p><strong>Calificación de su Solicitud:</strong> ${eventos[cantidad - 1].EstadoCalificacion}</p>
                              <p><strong>Información Adicional:</strong> ${InformacionAdicional}</p>
                              <p><strong>Observación Médica:</strong> ${ObservacionMedico}</p>
                              <p><strong>Observación Call Center:</strong> ${ObservacionCallCenter}</p>
                            </div>
                              `,
                        confirmButtonColor: '#3E5ABB'
                    });
                }) 
            }
        })
          
    }else{
        Swal.fire({
            icon: 'warning',
            title: 'Atencion',
            text:'Credito inactivo o sin soliciud',
            confirmButtonText: 'Ok'
        })
    }

}

function asegurar_nuevamente (){
    swal.fire({
    title: 'Reajuste asegurabilidad',
    icon: 'warning',
    html: 'Se realiza nueva solicitud de asegurabilidad debido a cambio de condiciones<br> Cliente supera monto 150 Millones de pesos',
    confirmButtonText: 'OK',
    confirmButtonColor: '#3E5ABB',
    allowOutsideClick: false,
    allowEscapeKey: false
    }).then((result)=>{
        if(result.isConfirmed){
            var data={
                IdSolicitudI: 0,
                id_usuario: $("#id_usuario").val(),
                id_simulacion: $("#id_simulacion").val(),
                metodo:"CrearSolicitud"
            }
             $.ajax({
                type:"POST",
                url: "../servicios/asegurabilidad_colpensiones/servicios_colpensiones.php",
                data:data,
                dataType:"json",
                success: function(response){
                    switch(response.estado){
                    case 200:
                        Swal.fire({
                            icon: 'success',
                            title: 'Exito',
                            text:response.mensaje,
                            confirmButtonText: 'Ok'
                        }).then((result)=>{
                            if(result.isConfirmed){
                                location.reload();
                            }
                        })
                        
                    break;
                    case 300:
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text:response.mensajeError,
                        })
                    break;
                    case 404:
                      Swal.fire({
                            icon: 'warning',
                            title: 'Error',
                            text:response.mensaje,
                       })  
                    break;
                    }   
                }
             })
        }   
    })
}

function crear_solicitud_asegurabilidad(){
swal.fire({
    title: 'Crear solicitud asegurabilidad',
    icon: 'warning',
    html: 'Al realizar esta accion se creara la solicitud de asegurabilidad',
    showCancelButton: true,
    confirmButtonText: 'OK',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3E5ABB',
    denyButtonColor: '#ff5200' 
    }).then((result)=>{
    if(result.isConfirmed){
        var data={
            IdSolicitudI: 0,
            id_usuario: $("#id_usuario").val(),
            id_simulacion: $("#id_simulacion").val(),
            metodo:"CrearSolicitud"
        }
     $.ajax({
        type:"POST",
        url: "../servicios/asegurabilidad_colpensiones/servicios_colpensiones.php",
        data:data,
        dataType:"json",
        success: function(response){
            switch(response.estado){
            case 200:
                Swal.fire({
                    icon: 'success',
                    title: 'Exito',
                    text:response.mensaje,
                    confirmButtonText: 'Ok'
                }).then((result)=>{
                    if(result.isConfirmed){
                        location.reload();
                    }
                })
                
            break;
            case 300:
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:response.mensajeError,
                })
            break;
            case 404:
              Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text:response.mensaje,
               })  
            break;
            }   
        }
     })

   }
   })
}

function cerrar_solicitud_asegurabilidad(){
    swal.fire({
        title: 'Anular solicitud asegurabilidad',
        icon: 'warning',
        html: 'Al realizar esta accion se cerrara la solicitud de asegurabilidad',
        showCancelButton: true,
        confirmButtonText: 'ok',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3E5ABB',
        denyButtonColor: '#ff5200' 
    }).then((result)=>{     
       if(result.isConfirmed){
        var IdSolicitud =$(".estado_asegurabilidad").attr("id");
        const metodo = "CerrarSolicitud";
        $.ajax({
            type:"POST",
            url: "../servicios/asegurabilidad_colpensiones/servicios_colpensiones.php",
            dataType: "json",
            data:{
                IdSolicitud: IdSolicitud,
                metodo: metodo,
                id_simulacion: $("#id_simulacion").val(),
                id_usuario:$("#id_usuario").val(),
                Motivo:"Asegurabilidad Kredit"
            },
            success: function(response){
                switch(response.estado){
                case 200 :
                        Swal.fire({
                            icon: 'success',
                            title: 'Exito',
                            text:response.mensaje.Respuesta,
                            confirmButtonText: 'Ok'
                        }).then((result)=>{
                            if(result.isConfirmed){
                                location.reload();
                            }
                        })
                break;

                case 404:
                   Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:response.mensaje.Respuesta,
                }) 
                break;
                }
            }
        })
        }
    })
}