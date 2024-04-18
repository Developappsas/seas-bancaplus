const url = $urlPrincipal."/controles/";

$(document).ready(function () {
    
    $('#disponible-cifin').hide();
    $('#disponible-experianDC').hide();
    $('#disponible-cifinLC').hide();
    $('#disponible-cifinUP').hide();
    $('#disponible-gattacaAJ').hide();

    $('#calendario-cifin').hide();
    $('#calendario-experianDC').hide();
    $('#calendario-cifinLC').hide();
    $('#calendario-cifinUP').hide();
    $('#resumenScoring').hide();

    $('#nodisponible-gattacaAJ').hide();

    var cedula = $('#cedula').val();
    var id_simulacion = $('#id_simulacion').val();
    var usuario = $("#id_usuario").val();

    peticionAntecedentes(id_simulacion, usuario, 'consultarPeticion');


    //const openWS = document.querySelectorAll("[data-service]");
    //for (const el of openWS) {
        //el.addEventListener("click", function () {
            //var servicio = (el.getAttribute("servicio"));
          //  var proveedor = (el.getAttribute("proveedor"));
        //    confirmar_descarga(servicio, proveedor);
      //  });
    //}

    verificar_disponibilidad(id_simulacion,cedula);
    $('#calendario-experianDC').click(function () {
        
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;


        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        Swal.fire({
            title: "Confirmacion",
            text: "Esta seguro que desea volver a descargar esta informacion",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '# d33 ',
            confirmButtonText: 'Si, volver a descargar!',
            showLoaderOnConfirm: true,
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                if (val==1)
                {
                    //console.log(result.value);
                
                    //Swal.fire({
                      //  title: 'Atencion!',
                        //text: `Validacion realizada exitosamente`,
                        //icon: 'success',
                        
                    //});
                    verificar_disponibilidad(id_simulacion,cedula);
                }else{
                    Swal.fire({
                        title: 'Atencion!',
                        text: `No se pudo realizar consulta. Debe ingresar primer apellido`,
                        icon: 'error',
                        
                    });
                }
                
            }
        });
    });

    $('#nodisponible-gattacaAJ').click(function () {
        var val=0;
        var usuario = $("#s_login").val();
        var id_Simulacion = $('#id_simulacion').val();
        var servicio = (this.getAttribute("servicio"));
        var id_registro= $(this).attr('id_consulta');
        
        var form = $(this).attr('name');
      
        Swal.fire({
            title: "Confirmacion",
            text: "¿Esta seguro de Consultar Los Antecedentes?",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '# d33 ',
            confirmButtonText: 'Si, Deseo Consultar',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: () => {
                val=1;
                Swal.fire({
                    title: 'Por favor aguarde unos segundos',
                    text: 'Procesando...',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                Swal.showLoading();

                peticionAntecedentes(id_Simulacion, usuario, 'enviarPeticion');
            }
        }).then((result) => {
            
        });
    });

    function peticionAntecedentes(id_simulacion, usuario, peticion){

        var datos = { id_simulacion: id_simulacion, usuario: usuario, peticion: peticion };

        $.ajax({
            url: '../servicios/consultaAntecedentes.php',
            data: datos,
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (json) {
                Swal.close();

                if (json.code == "200") {
                    if(peticion == 'consultarPeticion'){
                        $('#calendario-gattacaAJ').hide();
                        $('#disponible-gattacaAJ').show();
                        $('#label-gattacaAJ').text("Consultado");
                    }

                    if(peticion == 'enviarPeticion'){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: json.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#nodisponible-gattacaAJ').hide();
                        $('#calendario-gattacaAJ').show();
                        $('#label-gattacaAJ').text("Pendiente");
                    }
                } else if (json.code == "201") {
                    if(peticion == 'consultarPeticion'){
                        $('#disponible-gattacaAJ').hide();
                        $('#nodisponible-gattacaAJ').hide();
                        $('#calendario-gattacaAJ').show();
                        $('#label-gattacaAJ').text("Pendiente");
                    }
                } else {
                    if(peticion == 'enviarPeticion'){
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: json.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    
                    if(peticion == 'consultarPeticion'){
                        $('#calendario-gattacaAJ').hide();
                        $('#disponible-gattacaAJ').hide();
                        $('#nodisponible-gattacaAJ').show();
                        $('#label-gattacaAJ').text("Sin Consulta");
                    }
                }
            },
            error: function (xhr, status) {
                alert('Disculpe, No se pudo consultar Antecedentes.');
            }
        });
    }

    $('#calendario-cifin').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;

        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        Swal.fire({
            title: "Confirmacion",
            text: "Esta seguro que desea volver a descargar esta informacion",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '# d33 ',
            confirmButtonText: 'Si, volver a descargar!',
            showLoaderOnConfirm: true,
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
            
                console.log("prueba");
                
                Swal.fire({
                    title: 'Atencion!',
                    text: `Validacion realizada exitosamente`,
                    icon: 'success',
                    
                });
                verificar_disponibilidad(id_simulacion,cedula);
            }
        });
    });

    $('#calendario-cifinUP').click(function () {
        
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;


        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        Swal.fire({
            title: "Confirmacion",
            text: "Esta seguro que desea volver a descargar esta informacion",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '# d33 ',
            confirmButtonText: 'Si, volver a descargar!',
            showLoaderOnConfirm: true,
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                if (val==1)
                {
                    //console.log(result.value);
                
                    //Swal.fire({
                      //  title: 'Atencion!',
                        //text: `Validacion realizada exitosamente`,
                        //icon: 'success',
                        
                    //});
                    verificar_disponibilidad(id_simulacion,cedula);
                }else{
                    Swal.fire({
                        title: 'Atencion!',
                        text: `No se pudo realizar consulta. Debe ingresar primer apellido`,
                        icon: 'error',
                        
                    });
                }
                
            }
        });
    });

    $('#disponible-cifin').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        let headers = new Headers();
        //headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        $.ajax({
            type: 'GET',
            
            method: 'POST',
            url: url+'consulta_centrales.php',
            cache: false,
            contentType: "application/json",
            data: info,
            success: function (data) {
                
                //alert(JSON.stringify(data));
                alert(data.descripcion);
                if (data.estado!="500")
                {
                    window.open(data.resp,'ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
                }
                
                
            }
        });
    });

    $('#disponible-experianDC').click(function () {
       
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        let headers = new Headers();
        //headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        $.ajax({
            type: 'GET',
            
            method: 'POST',
            url: url+'consulta_centrales.php',
            cache: false,
            contentType: "application/json",
            data: info,
            success: function (data) {
                
                //alert(JSON.stringify(data));
                alert(data.descripcion);
                if (data.estado!="500")
                {
                    window.open(data.resp,'ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
                }
                
                
            }
        });
    });

    $('#disponible-cifinLC').click(function () {
      
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        let headers = new Headers();
        //headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        $.ajax({
            type: 'GET',
            
            method: 'POST',
            url: url+'consulta_centrales.php',
            cache: false,
            contentType: "application/json",
            data: info,
            success: function (data) {
                
                //alert(JSON.stringify(data));
                alert(data.descripcion);
                if (data.estado!="500")
                {
                    window.open(data.resp,'ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
                }
                
                
            }
        });
    });

    $('#disponible-cifinUP').click(function () {
       
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var id_registro= $(this).attr('id_consulta');
        //var url = ($urlPrincipal."//home/cifin.php?id_simulacion=" + id_Simulacion);
        //$("#iframe_servicios").attr("src", url);
        
        
        var form = $(this).attr('name');
        //alert(form);
        var info = JSON.stringify({ "servicio": servicio,"id_registro": id_registro,"usuario": usuario, 'proceso': 'Cargar_Adjunto' });
        console.log(info);
        //alert(info);
      
        let headers = new Headers();
        //headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        $.ajax({
            type: 'GET',
            
            method: 'POST',
            url: url+'consulta_centrales.php',
            cache: false,
            contentType: "application/json",
            data: info,
            success: function (data) {
                
                //alert(JSON.stringify(data));
                alert(data.descripcion);
                if (data.estado!="500")
                {
                    window.open(data.resp,'ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');
                }
                
                
            }
        });
    });



    $('#nodisponible-cifin').click(function () {
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        

        Swal.fire({
            title: 'Por favor diligencia el primer apellido del cliente',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            
            preConfirm: (apellido) => {
                
                Swal.fire({
                    title: 'Por favor aguarde unos segundos',
                    text: 'Procesando...', 
                    allowOutsideClick: false,
                        allowEscapeKey: false
                });
                Swal.showLoading();
                return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            
            if (result.isConfirmed) {
                
                console.log("prueba");
                
                Swal.fire({
                    title: 'Atencion!',
                    text: `Validacion realizada exitosamente`,
                    icon: 'success',
                    
                });
                verificar_disponibilidad(id_simulacion,cedula);
            }
        })



               
        
  
        
    });


    
    
    $('#nodisponible-cifinCV').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;
        Swal.fire({
            title: 'Por favor diligencia el primer apellido del cliente',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            
            if (result.isConfirmed) {
                if (val==1)
                {
                    //console.log(result.value);
                
                    //Swal.fire({
                      //  title: 'Atencion!',
                        //text: `Validacion realizada exitosamente`,
                        //icon: 'success',
                        
                    //});
                    verificar_disponibilidad(id_simulacion,cedula);
                }else{
                    Swal.fire({
                        title: 'Atencion!',
                        text: `No se pudo realizar consulta. Debe ingresar primer apellido`,
                        icon: 'error',
                        
                    });
                }
                
            }
        })

   
    });





    $('#nodisponible-cifinUP').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;
        Swal.fire({
            title: 'Por favor diligencia el primer apellido del cliente',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            
            if (result.isConfirmed) {
                if (val==1)
                {
                    //console.log(result.value);
                
                    //Swal.fire({
                      //  title: 'Atencion!',
                        //text: `Validacion realizada exitosamente`,
                        //icon: 'success',
                        
                    //});
                    verificar_disponibilidad(id_simulacion,cedula);
                }else{
                    Swal.fire({
                        title: 'Atencion!',
                        text: `No se pudo realizar consulta. Debe ingresar primer apellido`,
                        icon: 'error',
                        
                    });
                }
                
            }
        })

   
    });


    
    $('#resumenScoring').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        window.open($urlPrincipal."/formatos/reporteResumidoScoring.php?id_simulacion="+id_Simulacion,'ADJUNTO', 'toolbars=yes,scrollbars=yes,resizable=yes,width=900,height=600,top=0,left=0');
    });

    $('#nodisponible-experianDC').click(function () {
        
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var val=0;
        Swal.fire({
            title: 'Por favor diligencia el primer apellido del cliente',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            
            preConfirm: (apellido) => {
                
                if (apellido!="")
                {
                    val=1;
                    Swal.fire({
                        title: 'Por favor aguarde unos segundos',
                        text: 'Procesando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    Swal.showLoading();
                    
                    return consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                }
               
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            
            if (result.isConfirmed) {
                if (val==1)
                {
                    //console.log(result.value);
                
                    //Swal.fire({
                      //  title: 'Atencion!',
                        //text: `Validacion realizada exitosamente`,
                        //icon: 'success',
                        
                    //});
                    verificar_disponibilidad(id_simulacion,cedula);
                }else{
                    Swal.fire({
                        title: 'Atencion!',
                        text: `No se pudo realizar consulta. Debe ingresar primer apellido`,
                        icon: 'error',
                        
                    });
                }
                
            }
        })

   
    });

    $('#nodisponible-cifinLC').click(function () {
        var usuario = $("#s_login").val();
        var servicio = (this.getAttribute("servicio"));
        var id_Simulacion = $('#id_simulacion').val();
        var cedula = $('#cedula').val();
        var pagaduria = $("#pagaduria").val();
        var id_comercial = $("#id_comercial option:selected").val();
        var apellido = 'no aplica';
        consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido)
    });

    

    function confirmar_descarga(servicio, proveedor) {
        Swal.fire({
            title: "Confirmacion",
            text: "Esta seguro que desea volver a descargar esta informacion",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '# d33 ',
            confirmButtonText: 'Si, volver a descargar!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                var usuario = $("#s_login").val();
                var id_Simulacion = $('#id_simulacion').val();
                var cedula = $('#cedula').val();
                var pagaduria = $("#pagaduria").val();
                var id_comercial = $("#id_comercial option:selected").val();
                Consulta_Centrales(usuario, 'Consulta Centrales', id_Simulacion, servicio, proveedor, cedula, pagaduria, id_comercial);
            }
        });
    }

    $("#disponible-cifin").mouseover(function () { alerta_success('Cifin Historia de credito', 'Tiene disponible una consulta reciente de este cliente'); });
    $("#calendario-cifin").mouseover(function () { alerta_info('Cifin Historia de credito', 'Tiene mas de 30 dias la ultima consulta de este cliente'); });
    $("#nodisponible-cifin").mouseover(function () { alerta_error('Cifin Historia de credito', 'Actualmente no tiene disponible una consulta de este cliente'); });

    $("#disponible-experianDC").mouseover(function () { alerta_success('Experian Historia de credito', 'Tiene disponible una consulta reciente de este cliente'); });
    $("#nodisponible-experianDC").mouseover(function () { alerta_error('Experian Historia de credito', 'Actualmente no tiene disponible una consulta de este cliente'); });
    $("#calendario-experianDC").mouseover(function () { alerta_info('Experian Historia de credito', 'Tiene mas de 30 dias la ultima consulta de este cliente'); });

    $("#disponible-cifinLC").mouseover(function () { alerta_success('Legal Check', 'Tiene disponible una consulta reciente de este cliente'); });
    $("#nodisponible-cifinLC").mouseover(function () { alerta_error('Legal Check', 'Actualmente no tiene disponible una consulta de este cliente'); });
    $("#calendario-cifinLC").mouseover(function () { alerta_info('Legal Check', 'Tiene mas de 30 dias la ultima consulta de este cliente'); });

    $("#disponible-cifinUP").mouseover(function () { alerta_success('Ubica plus', 'Tiene disponible una consulta reciente de este cliente'); });
    $("#nodisponible-cifinUP").mouseover(function () { alerta_error('Ubica plus', 'Actualmente no tiene disponible una consulta de este cliente'); });
    $("#calendario-cifinUP").mouseover(function () { alerta_info('Ubica plus', 'Tiene mas de 30 dias la ultima consulta de este cliente'); });

    function Consulta_Centrales(usuario, proceso, id_Simulacion, servicio, proveedor, cedula, pagaduria, id_comercial) {
        var info = JSON.stringify({ "proceso": proceso, "servicio": servicio, "proveedor": proveedor, "id_Simulacion": id_Simulacion, "cedula": cedula });

        console.log(info);
        $.ajax({
            method: 'POST',
            url: url+'consulta_centrales.php',
            cache: false,
            contentType: "application/json",
            data: info,
            success: function (response) {
                console.log(response);
                if (response.estado == 200 && proveedor == "EXPERIAN") {
                    Swal.fire({
                        title: 'Por favor diligencia el segundo apellido del cliente',
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Verificar',
                        showLoaderOnConfirm: true,
                        cancelButtonText: 'Cancelar',
                        closeOnConfirm: false,
                        preConfirm: (apellido) => {
                            consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido);
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            console.log(result.value);
                            Swal.fire({
                                title: 'Atencion!',
                                text: `${result.value.mensaje}`,
                                icon: 'success'
                            });
                            verificar_disponibilidad(id_simulacion,cedula);
                        }
                    })
                } else {
                    var info = JSON.stringify({ "usuario": usuario, "proceso": 'Consumir WS', "servicio": servicio, "id_Simulacion": id_Simulacion, "cedula": cedula, "pagaduria": pagaduria, "id_comercial": id_comercial, 'apellido': 'no aplica' });
                    console.log(info);
                    consumir_WS(info);
                    verificar_disponibilidad(id_simulacion,cedula);
                }
            },
            error: function (response) {
                console.error("No es posible completar la operación", response);
            }
        });
    }

    function consumir_WS(usuario, servicio, id_Simulacion, cedula, pagaduria, id_comercial, apellido) {
        var info = JSON.stringify({ "usuario": usuario, "proceso": 'Consumir WS', "servicio": servicio, "id_Simulacion": id_Simulacion, "cedula": cedula, "pagaduria": pagaduria, "id_comercial": id_comercial, 'lastName': apellido });
        console.log(info);

        let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        $.ajax({
            type: 'POST',
            url: url+'consulta_centrales.php',
            data: info,
            headers: headers,
            dataType: 'json',
            success: function (data) {
                
                
                var resp=JSON.stringify(data);
                //alert(resp);
                console.log(resp);
                if (data.estado == "200") {
                    Swal.close();
					verificar_disponibilidad(id_simulacion,cedula);
                }
                else {
					Swal.fire({
                        title: 'Atencion!',
                        html: data.mensaje,
                        icon: 'error',
                        
                    });
                }
                
            
            }
        });
     
    }

    function verificar_disponibilidad(id_simulacion,cedula) {
        
        //console.log(cedula);
        var info = JSON.stringify({ "id_Simulacion": id_simulacion,"cedula": cedula, 'proceso': 'Consulta Disponibilidad' });
        //console.log(info);

      
        let headers = new Headers();
        //headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'application/json');
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Access-Control-Allow-Credentials', 'true');
        headers.append('GET', 'POST', 'OPTIONS');
        
        $.ajax({
            type: 'POST',
            url: url+'consulta_centrales.php',
            data: info,
            headers: headers,
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                var resp=JSON.stringify(response);
                //alert(resp);
                
                console.log(resp);

                if (response.INFORMACION_COMERCIAL!="0" || response.EXPERIAN!="0")
                {
                    $('#resumenScoring').show();
                }else{
                    $('#resumenScoring').hide();
                }


                if (response.INFORMACION_COMERCIAL=="0") {
                    $('#disponible-cifin').hide();
                    $('#calendario-cifin').hide();
                    $('#nodisponible-cifin').show();
                }else{
                    
                    if (response.INFORMACION_COMERCIAL.fecha_vence == "reconsulta") {
                        $('#disponible-cifin').attr("name",response.INFORMACION_COMERCIAL.pdf_respuesta2);
                        $('#disponible-cifin').attr("id_consulta",response.INFORMACION_COMERCIAL.id_Consulta);
                        $('#disponible-cifin').show();
                        $('#calendario-cifin').hide();
                        $('#nodisponible-cifin').hide();
                    } else {
                        $('#calendario-cifin').hide();
                        $('#nodisponible-cifin').show();
                        $('#disponible-cifin').hide();
                    }
                }


                if (response.EXPERIAN=="0") {
                    $('#disponible-experianDC').hide();
                    $('#nodisponible-experianDC').show();
                    $('#calendario-experianDC').hide();
                }else{
                    if (response.EXPERIAN.fecha_vence=="reconsulta") {
                        $('#disponible-experianDC').attr("name",response.EXPERIAN.pdf_respuesta2);
                        $('#disponible-experianDC').attr("id_consulta",response.EXPERIAN.id_Consulta);
                        
                        $('#puntaje_datacredito_ws').val(response.EXPERIAN.puntaje_datacredito);

                        $('#disponible-experianDC').show();
                        $('#nodisponible-experianDC').hide();
                        $('#calendario-experianDC').hide();
                    } else {
                        $('#nodisponible-experianDC').hide();
                        $('#calendario-experianDC').hide();
                        $('#nodisponible-experianDC').show();
                    }
                }
         
               

                if (response.UBICAPLUS=="0") {
                    $('#disponible-cifinUP').hide();
                    $('#nodisponible-cifinUP').show();
                    $('#calendario-cifinUP').hide();
                }else{
                    if (response.UBICAPLUS.fecha_vence=="reconsulta") {
                        $('#disponible-cifinUP').attr("name",response.UBICAPLUS.pdf_respuesta2);
                        $('#disponible-cifinUP').attr("id_consulta",response.UBICAPLUS.id_Consulta);
                        $('#disponible-cifinUP').show();
                        $('#nodisponible-cifinUP').hide();
                        $('#calendario-cifinUP').hide();
                    } else {
                        $('#disponible-cifinUP').hide();
                        $('#nodisponible-cifinUP').show();
                        $('#calendario-cifinUP').hide();
                    }
                }

                
                if (response.CREDITVISION=="0") {
                    $('#disponible-cifinCV').hide();
                                    $('#nodisponible-cifinCV').show();
                                    $('#calendario-cifinCV').hide();
                }else{
                    if (response.CREDITVISION.fecha_vence=="reconsulta") {
                        $('#disponible-cifinCV').attr("name",response.CREDITVISION.pdf_respuesta2);
                        $('#disponible-cifinCV').attr("id_consulta",response.CREDITVISION.id_Consulta);
                        $('#disponible-cifinCV').show();
                        $('#nodisponible-cifinCV').hide();
                        $('#calendario-cifinCV').hide();
                    } else {
                        $('#disponible-cifinCV').hide();
                        $('#nodisponible-cifinCV').show();
                        $('#calendario-cifinCV').hide();
                    }
                }
            },error : function (request, error) {
                console.log(arguments);
                //alert(" Can't do because: " + error);
            },
            
        });
        
    }
});