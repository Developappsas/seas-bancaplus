$('#input_cargar_base').click(function detalle(e) {         
    var input = document.createElement('input');
    input.type = 'file';
    input.onchange = e => {    
        try {
            let file = e.target.files[0];
            let reader = new FileReader();

            reader.onload = async function (e) {
                //Extraemos elementos del archivo hasta llevar a cada dato
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, { type: "array" });
                let worksheet = workbook.Sheets[workbook.SheetNames[0]];
                let sheet = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                Swal.fire({
                    title: 'Seleccione El Periodo',
                    html: '<input type="month" id="swal-input1" class="swal2-input">',
                    confirmButtonText: 'Enviar &rarr;',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    onOpen: function() {
                    },
                }).then(resultado => {
                    if (resultado.value) {
                        if($("#swal-input1").val() != ''){
                            var periodo = $("#swal-input1").val();
                            var iterar = 100;
                            var fi = 0;
                            let dataTabla = [];

                            Swal.close();                                   
                            Swal.fire({
                                title: 'Por favor espere',
                                text: 'Procesando...',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                            Swal.showLoading();

                            setTimeout(function(){
                                for (let i = 1; i<sheet.length; i=i+iterar) {
                                    var arrayFilas = [];
                                    var Limite2 = iterar;

                                    if(sheet.length < iterar){
                                        Limite2 = sheet.length-1;
                                    }else{
                                        Limite2=Limite2+i;
                                    }

                                    for (let j = i; (j < Limite2 && j < sheet.length-1); j=j+1) {
                                        const element = sheet[j];

                                        let datos = {
                                            "cedula": element[0],
                                            "nro_libranza": element[1],
                                            "edad_mora": element[2] }

                                        arrayFilas.push(datos);
                                    }

                                    let arrayDatos = { 'periodo' : periodo, 'usuario' : $("#S_IDUSUARIO").val(), 'datos' : arrayFilas }

                                    $.ajax({
                                        method: 'POST',
                                        url: "../servicios/cartera/Insertar_Edad_Mora.php",
                                        cache: false,
                                        contentType: "application/json",
                                        async: false,
                                        dataType: 'json',
                                        data: JSON.stringify(arrayDatos),
                                        success:function(response){
                                            if(response.length > 0){
                                                $.each(response, function (index, elem) { 
                                                    dataTabla[fi] = [elem.cedula, elem.id_simulacion, elem.nro_libranza, elem.edad_mora, elem.accion, elem.mensaje];
                                                    fi++;
                                                });
                                            }
                                        }
                                    });

                                    if(fi == (sheet.length-2)){ // Si Cantidad de filas de la tabla es igual a la cantidad de filas del archivo (sin titulo e inicia en 0)
                                        $('#tablaTitulos').DataTable({
                                            data: dataTabla,
                                            pageLength : 200,
                                            lengthMenu: [[10, 100, 200, 500], [10, 100, 200, 500]],
                                            columns: [
                                                { title: 'Cedula' },
                                                { title: 'Simulación' },
                                                { title: 'Libranza' },
                                                { title: 'Edad Mora' },
                                                { title: 'Acción' },
                                                { title: 'Resultado' },
                                            ],
                                        });
                                        Swal.close();
                                    }
                                }
                            }, 100);
                        }else{
                            alert("vacio")
                        }
                    }else{
                        console.log("No Selecciono nada");
                    }
                });
            }.bind(this);
            reader.readAsArrayBuffer(file);
        } catch (exception) {
            console.log(exception);
        }
    }
    input.click();
});