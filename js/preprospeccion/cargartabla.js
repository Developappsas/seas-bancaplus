$(document).ready(function () {
    if(window. location.origin == 'http://localhost'){
        var url_principal = window. location.origin + '/SEAS';
    }else{
        var url_principal = window. location.origin;
    }

    url_servicios = url_principal + '/servicios/preprospeccion/';
    //Swal.close();
    let datos = { "operacion":"Filtrar", "cedula":"" }
    listarClientes(datos);
});

function listarClientes(data) {
    $("#listaClientes").DataTable({
        scrollX: false,
        "destroy": true,        
        "ajax": {
            "url": url_servicios + "listar_clientes.php",            
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
            { title: 'Id', mData: 'id_preprospeccion' },
            { title: 'Cedula', mData: 'identificacion' },
            { title: 'Nombres', mData: 'nombres' },
            { title: 'Apellidos', mData: 'apellidos' },
            { title: 'telefono', mData: 'telefono' },
            { title: 'email', mData: 'email' },
            { title: 'ciudad', mData: 'ciudad' },
            { title: 'fecha', mData: 'fecha' },
            { title: 'Opciones', mData: 'opciones' }
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