function loading() {
    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();
}

function cargarTarifasKredit(){

    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando Tarifas Kredit...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    
    Swal.showLoading();

    if($('#salario_base').val() != ""){
        var salario_base_temp = $('#salario_base').val();
        var salario_base = salario_base_temp.replace(/[$.]/g,'');
    }else{
        var salario_base = 0;
    }

    var total_secompra = $('#total_secompra').attr("total_secompra");
    var otros_descuentos = $('#total_secompra').attr("otros_descuentos");

    var datos_kredit = {
        "Action":"CALCULAR_CREDITO",
        "fecha_nacimiento":$('#fecha_nacimiento').val(),
        "unidad_negocio":"KREDIT",
        "salario":salario_base,
        "total_secompra":total_secompra,
        "otros_descuentos":otros_descuentos
    };

    $("#tabla_tasas_kredit").find("tbody").html('');

    var aportes = 0;
    var margen_seguridad = 0;
    
    $.ajax({
        method: 'POST',
        url: url_servicios + "calculo_credito.php",
        cache: false,
        contentType: "application/json",
        async: false,
        data: JSON.stringify(datos_kredit),
        success:function(response){
            Swal.close();
            if(response.data != null){
                response.data.forEach(element => {
                    var tabla_tasas_kredit = document.querySelector("#tabla_tasas_kredit tbody");
                    tabla_tasas_kredit.innerHTML += "<tr><th>"+element.porc_tasa+"</th> <th>"+element.plazo+"</th>"+
                    "<th>"+parseInt(element.opcion_cuota_cli).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+"</th>"+
                    "<th>"+parseInt(element.valor_credito_cli).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+"</th>"+
                    "<th><input type='radio' value='1' tasa='KREDIT' id_tasa='"+element.id_tasa+"' valor_credito='"+parseInt(element.valor_credito_cli)+"'"+
                    " valor_cuota='"+element.opcion_cuota_cli+"' id_tasa2='"+element.id_tasa2+"' plazo='"+element.plazo+"'"+
                    " aportes='"+element.total_aportes+"' otros_descuentos='"+element.otros_descuentos+"' class='tasa_kredit' name='tasa'/></th></tr>";

                    aportes = element.total_aportes;
                    margen_seguridad = element.margen_seguridad;
                });
            }

            $("#aportes").val(aportes.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            $("#margen_seguridad").val(margen_seguridad.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }
    });
}

function calcularTotalSeCompra() {
    var total_se_compra = 0; 
    var otros_descuentos = 0;
    var arrayValores = $("input[name='valores_cc[]']").map(function(){return $(this).val();}).get();
    $("input[name='valores_cc[]']").each(function(){
        if($(this).parent().parent().find(".se_compra_cc").is(':checked')) {
            total_se_compra += parseInt($(this).val());
        }else{
            otros_descuentos += parseInt($(this).val());
        }
    });
    $("#total_secompra").attr("total_secompra", total_se_compra);
    $("#total_secompra").attr("otros_descuentos", otros_descuentos);
    $("#total_secompra").text("$ " + total_se_compra.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));

    cargarTabsTarifas();
}

function cargarTabsTarifas() {
    if ($('#tabs_kredit').is(':visible')) {
        $('#tabs_kredit_click').trigger("click");
    }else{
        $('#tabs_fianti_click').trigger("click");
    }
}

$(document).ready(function () {

    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    
    Swal.showLoading();

    if(window. location.origin == 'http://localhost'){
        var url_principal = window. location.origin + '/SEAS';
    }else{
        var url_principal = window. location.origin;
    }

    url_servicios = url_principal + '/servicios/nexa/';    
    const urlParams = new URLSearchParams(window.location.search);    
    let datos = {'id': atob(urlParams.get('id'))}
    $.ajax({

        method: 'POST',
        url: url_servicios + "detalle_solicitud.php",
        cache: false,
        contentType: "application/json",
        async: false,
        data: JSON.stringify(datos),
        success:function(response){

            Swal.close();

            var total_carteras = 0;
            var i = 1;

            if(typeof response.carteras.length === 'undefined'){
                Object.keys(response.carteras[""]).forEach(cartera => {
                    if (cartera != 'nexa_carteras_Id') {
                        if (cartera != 'nexa_carteras_cedula') {
                            Object.keys(response.carteras[""]).forEach(cartera_2 => {
                                if (cartera == cartera_2) {
                                    if (response.carteras[""][cartera] !== '') { 
                                        total_carteras += parseInt(response.carteras[""][cartera]);
                                        valor = response.carteras[""][cartera].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                                        var tabla_carteras = document.querySelector("#tabla_carteras tbody");
                                        tabla_carteras.innerHTML += "<tr><td>" + i + 
                                            "</td><td><input type='hidden' name='carteras_cc[]' value='"+cartera+"'>" + cartera +
                                            "</td><td><input type='hidden' class='valores_cc' name='valores_cc[]' value='"+parseInt(response.carteras[""][cartera])+"'>$ " + (valor) +
                                            '</td><td class="center"><input type="checkbox" onclick="calcularTotalSeCompra();" value="1" class="se_compra_cc" name="se_compra_cc[]" />'
                                            "</td></tr>";
                                        i = i + 1;
                                    }
                                }
                            })
                        }
                    }
                });
                var total_carteras_string = total_carteras.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                $('#total_cartera').text(total_carteras_string);
            }

            $('#numero_documento').val(response.data.nexa_clientes_cedula);
            $('#nombres').val(response.data.nexa_clientes_primer_nombre + " "+ response.data.nexa_clientes_segundo_nombre)
            $('#apellidos').val(response.data.nexa_clientes_primer_apellido + " "+ response.data.nexa_clientes_segundo_apellido)
            $('#genero').val(response.data.nexa_clientes_genero)
            $('#edad').val(response.data.nexa_clientes_edad)
            $('#fecha_nacimiento').val(response.data.nexa_clientes_fecha_nacimiento)
            $('#ciudad').val(response.data.nexa_clientes_ciudad)
            $('#direccion').val(response.data.nexa_clientes_direccion)
            $('#telefono').val(response.data.nexa_clientes_telefono)
            $('#celular').val(response.data.nexa_clientes_celular)
            $('#correo').val(response.data.nexa_clientes_email);            
            $('#grado').val(response.data.nexa_clientes_grado)
            $('#tipo_cargo').val(response.data.nexa_clientes_cargo_tipo)
            $('#cargo').val(response.data.nexa_clientes_cargoempresa)
            $('#nivel_contratacion').val(response.data.nexa_clientes_nivel_contratacion)
            $('#fecha_nombramiento').val(response.data.nexa_clientes_fecha_nombramiento)
            $('#salario_base').val(response.data.nexa_clientes_salario_basico)
            $('#centro_costos').val(response.data.nexa_clientes_centro_costos)
            $('#fecha_ingreso').val(response.data.nexa_clientes_fecha_nombramiento)
            $('#pagaduria').val(response.data.nexa_clientes_pagaduria);

            calcularTotalSeCompra();
        }
    });
});

$('#tabs_kredit_click').click(function detalle(e) {
    cargarTarifasKredit();
});

$('#tabs_fianti_click').click(function detalle(e) {

    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();

    var tabla_tasas_fianti = document.querySelector("#tabla_tasas_fianti tbody");
    tabla_tasas_fianti.innerHTML = "";

    if($('#salario_base').val() != ""){
        var salario_base_temp = $('#salario_base').val();
        var salario_base = salario_base_temp.replace(/[$.]/g,'');
    }else{
        var salario_base = 0;
    }

    var total_secompra = $('#total_secompra').attr("total_secompra");
    var otros_descuentos = $('#total_secompra').attr("otros_descuentos");

    var datos_fianti = {
        "Action":"CALCULAR_CREDITO",
        "fecha_nacimiento":$('#fecha_nacimiento').val(),
        "unidad_negocio":"FIANTI",
        "salario":salario_base,
        "total_secompra":total_secompra,
        "otros_descuentos":otros_descuentos
    };

    $("#tabla_tasas_fianti").find("tbody").html('');

    var aportes = 0;
    var margen_seguridad = 0;

    $.ajax({
        method: 'POST',
        url: url_servicios + "calculo_credito.php",
        cache: false,
        contentType: "application/json",
        async: false,
        data: JSON.stringify(datos_fianti),
        success:function(response){
            Swal.close();
            response.data.forEach(element => {
                var tabla_tasas_fianti = document.querySelector("#tabla_tasas_fianti tbody");
                tabla_tasas_fianti.innerHTML += "<tr> <th>"+element.porc_tasa+"</th>"+
                "<th>"+element.plazo+"</th>"+
                "<th>"+parseInt(element.opcion_cuota_cli).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+"</th>"+
                "<th>"+parseInt(element.valor_credito_cli).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+"</th>"+
                "<th><input type='radio' value='1' tasa='FIANTI' id_tasa='"+element.id_tasa+"' valor_credito='"+parseInt(element.valor_credito_cli)+"'"+
                    " valor_cuota='"+element.opcion_cuota_cli+"' id_tasa2='"+element.id_tasa2+"' plazo='"+element.plazo+"' "+
                    "aportes='"+element.total_aportes+"' otros_descuentos='"+element.otros_descuentos+"' class='tasa_fianti' name='tasa'/></th></tr>";

                    aportes = element.total_aportes;
                    margen_seguridad = element.margen_seguridad;
            });

            $("#aportes").val(aportes.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            $("#margen_seguridad").val(margen_seguridad.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }
    });
});

$('#btn_simular').click(function detalle(e) {

    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();

    var tasas;
    var id_tasa = 0;
    var id_tasa2 = 0;
    var valor_posible_cuota = 0;
    var valor_posible_credito = 0;
    var plazo = 0;
    var aportes = 0;
    var otros_descuentos = 0;
    $('input[name=tasa]').each(function() {
        if($(this).is(':checked')){
            id_tasa = $(this).attr("id_tasa");
            id_tasa2 = $(this).attr("id_tasa2");
            valor_posible_cuota = $(this).attr("valor_cuota");
            valor_posible_credito = $(this).attr("valor_credito");
            plazo = $(this).attr("plazo");
            aportes = $(this).attr("aportes");
            otros_descuentos = $(this).attr("otros_descuentos");
        }
    });

    if(id_tasa2 == 0){
        alert("No hay Tasa u Opción de Credito Selecionada");
        Swal.close();
        return false;
    }

    var salario_base_temp = $('#salario_base').val();
    var salario_base = salario_base_temp.replace(/[$.]/g,'');

    var datos = {
        "Action":"CREAR_PROSPECCION",
        "cedula":$('#numero_documento').val(),
        "nombre":$('#nombres').val(),
        "apellido":$('#apellidos').val(),
        "telefono":$('#telefono').val(),
        "celular":$('#celular').val(),
        "direccion":$('#direccion').val(),
        "ciudad":$('#ciudad').val(),
        "correo":$('#correo').val(),
        "nivel_contratacion":$('#nivel_contratacion').val(),
        "fecha_nacimiento":$('#fecha_nacimiento').val(),
        "fecha_nombramiento":$('#fecha_nombramiento').val(),
        "institucion":$('#centro_costos').val(),
        "salario_base":salario_base,
        "genero":$('#genero').val(),
        "grado":$('#grado').val(),
        "tipo_cargo":$('#tipo_cargo').val(),
        "cargo":$('#cargo').val(),
        "institucion":$('#centro_costos').val(),
        "carteras_cc" : $("input[name='carteras_cc[]']").map(function(){return $(this).val();}).get(),
        "valores_cc" : $("input[name='valores_cc[]']").map(function(){return $(this).val();}).get(),
        "se_compra_cc" : $("input[name='se_compra_cc[]']").map(function(){return $(this).prop('checked') ? 'SI' : 'NO';}).get(),
        "id_tasa":id_tasa,
        "id_tasa2":id_tasa2,
        "valor_posible_cuota":valor_posible_cuota,
        "valor_posible_credito":valor_posible_credito,
        "plazo":plazo,
        "otros_descuentos":otros_descuentos,
        "aportes":aportes
    };

    $.ajax({
        method: 'POST',
        url: url_servicios + "crear_prospeccion.php",
        cache: false,
        contentType: "application/json",
        async: false,
        data: JSON.stringify(datos),
        success:function(response){
            Swal.close();

            if(response.code == "200"){
                Swal.fire({
                    title:'Prospección Creada',
                    text:'Se redireccionará a la ventana simulador',
                    icon:'success',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                window.open('simulador.php?id_simulacion='+response.data+'&tipo_comercial_buscar=&id_simulacion_buscar=&fecha_inicialbd=&fecha_inicialbm=&fecha_inicialba=&fecha_finalbd=&fecha_finalbm=&fecha_finalba=&fechades_inicialbd=&fechades_inicialbm=&fechades_inicialba=&fechades_finalbd=&fechades_finalbm=&fechades_finalba=&fechaprod_inicialbm=&fechaprod_inicialba=&fechaprod_finalbm=&fechaprod_finalba=&descripcion_busqueda=&id_simulacion_buscar=&unidadnegociob=&sectorb=&pagaduriab=&tipo_comercialb=&id_comercialb=&estadob=&decisionb=&id_subestadob=&id_oficinab=&visualizarb=&calificacionb=&statusb=&buscar=1&page=0', '_blank');
            }else{
                Swal.fire(
                    'Error Al Simular',
                    response.message,
                    'error'
                );
            }
        }
    });
});