$(document).ready(function () {

    if(window. location.origin == 'http://localhost'){
        var url = window. location.origin + '/SEAS';
    }else{
        var url = window. location.origin;
    }
    
    url_servicios = url+'/servicios/nexa/';
    //Swal.close();
    let datos = { "operacion":"Filtrar", "cedula":"" }
    listarClientes(datos);

});

function listarClientes(data) {    
    $("#listaClientes").DataTable({
        scrollX: false,
        "destroy": true,        
        "ajax": {
            "url": url_servicios + "lista_clientes.php",            
            "method": "POST",
            "data": data,
            "timeout": 3000,
        },
        "initComplete": function (settings, json) {
            Swal.close();
        },
        "bPaginate": true,
        "bFilter": true,
        "bProcessing": true,
        "searching": false,
        "pageLength": 10,
        "columns": [
            { title: 'Id', mData: 'nexa_cliente_id' },
            { title: 'Cedula', mData: 'nexa_clientes_cedula' },
            { title: 'Nombre', mData: 'nexa_clientes_nombre' },
            { title: 'Cargo', mData: 'nexa_clientes_cargo' },
            { title: 'Salario base', mData: 'nexa_clientes_salario_base' },
            { title: 'Fecha Nombramiento', mData: 'nexa_clientes_fecha_nombramiento' },
            { title: 'Nivel Contratacion', mData: 'nexa_clientes_nivel_contratacion' },
            { title: 'Centro Costos', mData: 'nexa_clientes_centro_costos' },
            { title: 'Genero', mData: 'nexa_clientes_genero' },
            { title: 'Telefono', mData: 'nexa_clientes_telefono' },
            { title: 'Email', mData: 'nexa_clientes_email' },
            { title: 'cargo_tipo', mData: 'nexa_clientes_cargo_tipo' },
            { title: 'Opciones', mData: 'nexa_clientes_opciones' }

        ],
        order: [[0, 'asc']],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}

$("#divlistaClientes").on('click', 'a', function () {
    var action = $(this).attr('id');
    var id = $(this).attr('name');
    if (action == 'ver_detalle') {
        window.location.replace($urlPrincipal."/home/nexa_detalle.php?id=" + btoa(id) + "");
        console.log(id);
    }
})

$('#filtrar').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        let datos = { "operacion":"Filtrar", "cedula":$('#filtrar').val() }
        listarClientes(datos)
    }
});

$('#input_cargar_base').click(function detalle(e) {
    loading();
    var input = document.createElement('input');
    input.type = 'file';
    input.onchange = e => {
        var file = e.target.files[0];
        var reader = new FileReader();
        reader.readAsText(file,'UTF-8');
        reader.onload = readerEvent => {
            var content = readerEvent.target.result;
            resultado = content.toString();
            var resultado_array = resultado.split('\r\n');
            for (let index = 1; index < resultado_array.length-1; index++) {
                console.log( resultado_array[index] );
                const element = resultado_array[index].split(';');
                let datos = {
                    "operacion": 'nuevo_cliente',
                    "nexa_clientes_pagaduria": element[0],
                    "nexa_clientes_cedula": element[1],
                    "nexa_clientes_primer_apellido": element[2],
                    "nexa_clientes_segundo_apellido": element[3],
                    "nexa_clientes_primer_nombre": element[4],
                    "nexa_clientes_segundo_nombre": element[5],
                    "nexa_clientes_fecha_ingreso": element[6],
                    "nexa_clientes_fecha_nacimiento": element[7],
                    "nexa_clientes_edad": element[8],
                    "nexa_clientes_cargoempresa": element[9],
                    "nexa_clientes_grado": element[10],
                    "nexa_clientes_salario_basico": element[11],
                    "nexa_clientes_fecha_nombramiento": element[12],
                    "nexa_clientes_nivel_contratacion": element[13],
                    "nexa_clientes_centro_costos": element[14],
                    "nexa_clientes_genero": element[15],
                    "nexa_clientes_telefono": element[16],
                    "nexa_clientes_celular": element[17],
                    "nexa_clientes_ciudad": element[18],
                    "nexa_clientes_direccion": element[19],
                    "nexa_clientes_email": element[20],
                    "nexa_clientes_cargo_tipo": element[21]
                };
                
                console.log(datos);
                
                $.ajax({
                    method: 'POST',
                    url: url_servicios + "carga_masiva.php",
                    cache: false,
                    contentType: "application/json",
                    async: false,
                    data: JSON.stringify(datos),
                    success:function(response){
                        console.log(response);
                        if (response.estado == '409') {
                            toastr.options.progressBar = true;
                            toastr.options.closeButton = true;
                            toastr.options.preventDuplicates = true;
                            toastr.error(response.mensaje, 'Proceso ejecutado');                            
                        }else{
                            toastr.options.progressBar = true;
                            toastr.options.closeButton = true;
                            toastr.options.preventDuplicates = true;
                            toastr.success(response.mensaje, 'Proceso ejecutado');
                        }
                    }
                });
            }            
        }
    }
    input.click();
});


$('#input_cargar_cartera').click(function detalle(e) {
    var input = document.createElement('input');
    input.type = 'file';
    input.onchange = e => {
        var file = e.target.files[0];
        var reader = new FileReader();
        reader.readAsText(file,'UTF-8');
        reader.onload = readerEvent => {
            var content = readerEvent.target.result;
            resultado = content.toString();
            var resultado_array = resultado.split('\r\n');
            llaves = [resultado_array[0].split(';')]            
            var resultado = [];
            for (let index = 1; index < resultado_array.length-1; index++) {
                for (let index2 = 0; index2 < llaves[0].length; index2++) {
                    var data = {};
                    var columna = llaves[0][index2];
                    var dato = resultado_array[index].split(';');
                    var cedula = dato[0];
                    data.llave = columna;
                    data.valor = dato[index2];
                    resultado.push(llaves);
                    
                    let datos = { 'operacion':'Nueva Cartera', 'cedula':cedula, 'llave': data.llave, 'valor':data.valor };
                    //console.log(datos);

                    $.ajax({
                        method: 'POST',
                        url: url_servicios + "carga_masiva.php",
                        cache: false,
                        contentType: "application/json",
                        async: false,
                        data: JSON.stringify(datos),
                        success:function(response){
                            console.log(response);
                        }
                    });
                }
            }
        }
    }
    input.click();
});


$(document).on("load", loading());

function loading() {
    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();
}
