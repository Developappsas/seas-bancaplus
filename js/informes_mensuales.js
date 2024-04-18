$(document).ready(function () {
    cargarTabla();
    
});

function nuevoArchivo () {
    const input = document.createElement('input');
            input.type = 'file';
            input.onchange = function(cargue) {
                const archivo = cargue.target.files[0];
                const formData = new FormData();
                formData.append('informacion_mensual', archivo);
                const login = $('#login').val();
                formData.append('login', login);
                $.ajax({
                    url: "../servicios/informacion_mensual/cargar_informacion_mensual.php",
                    type: "POST",
                    data:formData,
                    processData: false, 
                    contentType: false,
                    success: function (resultado) {                        
                        if(resultado.estado == 200){
                            const lector = new FileReader();
                            lector.onload = function(e) {
                            const datos = e.target.result;
                            const libro = XLSX.read(datos, {type: 'binary'})
                                let hojasProcesadas = 0;
                                let filasProcesadas =0;
                                for (let i = 0; i < libro.SheetNames.length; i++){ 
                                    const nombreHoja = libro.SheetNames[i];
                                    const datosHoja =  libro.Sheets[nombreHoja];
                                    const opciones ={
                                        // Establece la primera fila como encabezados
                                        range: 1 
                                    }
                                    const datosHojaJSON = XLSX.utils.sheet_to_json(datosHoja, opciones);
                                    console.log(datosHojaJSON) 
                                    hojasProcesadas++
                                for (let j = 0; j < datosHojaJSON.length; j++) {
                                        Swal.fire({
                                            title: 'Procesando datos...',
                                            text: `Hojas procesadas: ${hojasProcesadas}, Filas procesadas: ${filasProcesadas}`,
                                            icon: 'info',
                                            showConfirmButton: false,
                                            allowOutsideClick: false
                                        });
                                        $.ajax({
                                            url:"../servicios/informacion_mensual/insertar_datos_informacion_mensual.php",
                                            type: "POST",
                                            data:{ datos: datosHojaJSON[j],
                                                    hoja: nombreHoja,
                                                    id_archivo: resultado.id_cargue
                                            },
                                            success: function (resultado) {
                                                if(resultado.estado==300){
                                                     Swal.fire({
                                                        icon: "error",
                                                        title: "Lectura Fallida",
                                                        text: resultado.mensaje

                                                     })
                                                     return false;
                                                }
                                            },
                                            complete: function (resultado) {
                                                console.log(resultado.responseJSON.estado)
                                                console.log(datosHojaJSON.length)
                                                if (j === datosHojaJSON.length - 1) {
                                                    console.log(j)
                                                    if(resultado.responseJSON.estado==200){
                                                        console.log(resultado.estado)
                                                          Swal.fire({
                                                        icon: "success",
                                                        title: "Muy Bien",
                                                        text: "Excel completado con exito",
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: 'OK'
                                                      }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            Swal.close();
                                                            cargarTabla()
                                                        }
                                                      })
                                                    }
                                                  
                                                }
                                                  
                                            }

                                        })
                                    }
                                    
                                   
                                }
                            }
                            lector.readAsBinaryString(archivo);
                            
                        }else if(resultado.estado== 300){
                            
                            Swal.fire({
                                icon: "error",
                                title: resultado.estado,
                                text: resultado.mensaje,
                                
                              });
                            
                        }else if(resultado.estado == 400){
                            console.log(resultado.estado);
                            Swal.fire({
                                icon: "error",
                                title: resultado.estado,
                                text: resultado.mensaje,
                              });

                        }
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status != 200) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Servicio no disponible",
                                footer: '<h4>soporte@kredit.com.co</h4>'
                              });
                            
                        }
                    }

                })
            }
            input.click();
    
}

function cargarTabla() {
    Swal.fire({
        title: 'Por favor aguarde unos segundos',
        text: 'Procesando...'
    });
    Swal.showLoading();
    var rowsHtml = '';

    $("#informesMensuales tbody").html(rowsHtml);

    $.ajax({
        url:'../servicios/informacion_mensual/tabla_informacion_mensual.php',
        type: 'POST',
        data:'action=consultar',
        success:function (resultado) {
            if(resultado.estado==200){
                if(resultado.data!=null){
                    resultado.data.forEach(function (informacion) {
                    rowsHtml += '<tr>';
								rowsHtml += '<td>' + informacion.id_cargue_mensual + '</td>';
								rowsHtml += '<td>' + informacion.fecha_creacion + '</td>';
								rowsHtml += '<td>' + informacion.nombre_archivo + '</td>';
								// rowsHtml += '<td>' + informacion.cantidad_filas_movimiento + '</td>';
								// rowsHtml += '<td>' + informacion.cantidad_filas_compras_saldos + '</td>';
								rowsHtml += '<td>'+informacion.acciones +'</td>';
                    
                });
                }else{
                    rowsHtml += '<tr>';
								rowsHtml += '<td>' + "No se encontraron registros" + '</td>';
                }
                
                $("#informesMensuales tbody").html(rowsHtml);
                Swal.close();
            }else if (resultado.estado==300) {
                Swal.fire({
                    icon: "error",
                    title: resultado.estado,
                    text: resultado.mensaje,
                    
                  });
            }

            
        }
    })


}

$(document).on('click', 'a[boton="accion"]', function() {
    const id_fila = $(this).attr('id')
    const accion = $(this).attr('name')
      $.ajax({
                url:'../servicios/informacion_mensual/tabla_informacion_mensual.php',
                type: 'POST',
                data:{
                    id_cargue_mensual: id_fila,
                    action: accion
                },
                success: function(resultado) {
                    if(accion=="eliminar" && resultado.estado==200){
                    Swal.fire({
                        icon: "success",
                        title: resultado.estado,
                        text: resultado.mensaje,  
                      }); 
                      cargarTabla()
                    }
                    if(accion == "descargar" && resultado.estado==200){

                        Swal.fire({
                            title: 'Generando archivo...',
                            text: 'Por favor, espera mientras se genera el archivo de Excel.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        const libro = XLSX.utils.book_new();    
                        const headers = [
                            ["Identificación del crédito, deudor y pagador", "Identificación del crédito, deudor y pagador", "Identificación del crédito, deudor y pagador", "Información contable", "Información contable", "Información contable", "Información contable", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos", "Información para la aplicación de pagos"],
                            ["Sub1", "Sub2", "Sub3", "Sub4", "Sub5", "Sub6", "Sub7", "Sub8", "Sub9", "Sub10", "Sub11", "Sub12", "Sub13", "Sub14", "Sub15", "Sub16", "Sub17", "Sub18", "Sub19", "Sub20", "Sub21", "Sub22"],
                            
                          ];
                        const movimientos_contables = XLSX.utils.aoa_to_sheet(headers)
                        const mergeRanges = [
                            { s: { r: 0, c: 0 }, e: { r: 1, c: 2 } }, 
                            { s: { r: 0, c: 3 }, e: { r: 1, c: 6 } }, 
                            { s: { r: 0, c: 7 }, e: { r: 1, c: 21 } },
                          ];
                          mergeRanges.forEach(range => {
                            if (!movimientos_contables['!merges']) movimientos_contables['!merges'] = [];
                            movimientos_contables['!merges'].push(range);
                          });
                          XLSX.utils.sheet_add_json(movimientos_contables, resultado.movimientos_contables, { origin: 'A3' })



                          const headers2 = [
                            ["Información del deudor", "Información del deudor", "Información del deudor", "Información del deudor", "Información del deudor", "Información del deudor", "Información de la pagaduría", "Información de la pagaduría", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Información facial del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito" , "Saldo y estado actual del crédito", "Saldo y estado actual del crédito", "Saldo y estado actual del crédito","Saldo y estado actual del crédito","Saldo y estado actual del crédito","Saldo y estado actual del crédito","Saldo y estado actual del crédito","Saldo y estado actual del crédito", "Saldo y estado actual del crédito" , "aseguradora" , "aseguradora" ],
                            
                          ];
                          const compra_saldos_mensuales = XLSX.utils.aoa_to_sheet(headers2)
                          const mergeRanges2 = [
                              { s: { r: 0, c: 0 }, e: { r: 1, c: 6 } }, 
                              { s: { r: 0, c: 7 }, e: { r: 1, c: 8 } }, 
                              { s: { r: 0, c: 9 }, e: { r: 1, c: 18 } },
                              { s: { r: 0, c: 19 }, e: { r: 1, c: 33 } },
                              { s: { r: 0, c: 34 }, e: { r: 1, c: 35 } },

                            ];
                            mergeRanges2.forEach(range => {
                                if (!compra_saldos_mensuales['!merges']) compra_saldos_mensuales['!merges'] = [];
                                compra_saldos_mensuales['!merges'].push(range);
                              });
                            XLSX.utils.sheet_add_json(compra_saldos_mensuales, resultado.compra_saldos_mensuales, { origin: 'A3' })
                            
                        // const compra_saldos_mensuales = XLSX.utils.json_to_sheet(resultado.compra_saldos_mensuales);
                        // const movimientos_contables = XLSX.utils.json_to_sheet(resultado.movimientos_contables); 
                        XLSX.utils.book_append_sheet(libro, compra_saldos_mensuales, 'COMPRA Y SALDOS MENSUALES');
                        XLSX.utils.book_append_sheet(libro, movimientos_contables, 'MOVIMIENTOS'); 
                        XLSX.writeFile(libro, 'InformacionMensual.xlsx');
                        Swal.close();

                    }
                }
            })
});


