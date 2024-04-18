$('#prueba').click(function prueba() {

    var filtro = "&cedula=" + $("#cedula").val()
        + "&pagaduria=" + $("#pagaduria").val()
        + "&ciudad=" + $("#ciudad").val()
        + "&estado=" + $("#estado").val()

        Swal.fire({
            title: 'Generando archivo...',
            text: 'Por favor, espera mientras se genera el archivo de Excel.',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
     
    $.ajax({
        url: '../servicios/reportes/reporte_marketing.php',
        type: 'POST',
        data: filtro,
        success: function ($resultado) {


            if ($resultado == 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Sin resultados',
                    text: 'No se encontraron datos para los filtros seleccionados',
                });

            } else {
                const workbook = XLSX.utils.book_new();
                const worksheet = XLSX.utils.json_to_sheet($resultado);
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Hoja1');
                XLSX.writeFile(workbook, 'marketing.xlsx');

                Swal.close();

            }
        }
    });
})
