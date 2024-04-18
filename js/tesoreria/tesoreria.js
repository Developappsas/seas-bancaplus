$('#btnCargarCreditosFacturados').click( function () {
    var fileUpload = $("#soporteModalCreditosFacturados")[0].files[0];
			

		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
		
		if (typeof (FileReader) != "undefined") {
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();
			var reader = new FileReader();

			if (reader.readAsBinaryString) {
				reader.onload = function (e) {
					ProcessExcel(e.target.result);
							
				};
				reader.readAsBinaryString(fileUpload);
			} else {
						//For IE Browser.
				reader.onload = function (e) {
					var data = "";
					var bytes = new Uint8Array(e.target.result);
					for (var i = 0; i < bytes.byteLength; i++) {
						data += String.fromCharCode(bytes[i]);
					}
					ProcessExcel(data);
							
				};
				reader.readAsArrayBuffer(fileUpload);
			}
		} else {
					alert("This browser does not support HTML5.");
		}
	
});

function ProcessExcel(data) {
	
	var workbook = XLSX.read(data, {
		type: 'binary'
	});

	var firstSheet = workbook.SheetNames[0];

	var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);
	var json_object = JSON.stringify(excelRows);

	//alert(json_object);
	var rowsHtml = '';
	var simulaciones="";
	for (var i = 0; i < excelRows.length; i++) {
		  let posicion = rowsHtml.indexOf(excelRows[i].credito);
		if (posicion !== -1)
		{
			//console.log("La palabra está en la posición " + posicion);
		}	
		else
		{
			$.ajax({
                url: '../servicios/tesoreria/marcar_facturacion_credito.php',
                type: 'POST',
                data: 'id_simulacion='+excelRows[i].credito+"&facturado="+$("#facturadoModalCreditosFacturados option:selected").val(),
                dataType : 'json',
                async:false,
                success: function(json) {
		    console.log(json)
                    if(json.code == 200){
                        console.log("Guardado satisfactoriamente");
                    }else{
                        console.log("Error al guardar");
                    }
                    return false;
                }
            });	
			
		}
	}

	//$("#textoSimulaciones").val(simulaciones.substring(0, simulaciones.length - 1));
	//loadTabla(rowsHtml);
	Swal.close();

    Swal.fire({
        title:'Notificacion',
        text:'Proceso Realizado Satisfactoriamente',
        icon:'success',
        allowOutsideClick: false
      }
      ).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        } 

      });
	
};


$('#input_cargar_compra_cartera').click(function detalle(e){
    var input = document.createElement('input');
    input.type = 'file';
    input.setAttribute("multiple","");
    input.setAttribute("accept","application/pdf");
    input.setAttribute("name","soportes1[]");
    input.onchange = e => {
        loading();
        var file = e.target.files[0];
        if (e.target.files.length>60){
            alert("Ha superado el maximo de archivos permitidos para realizar carga masiva (20 archivos)");
        }else{
            var formArchivoAnexo = new FormData();
            formArchivoAnexo.append("tipo_adjunto","soporte_pago");
            
            var archivos_procesados = 0;
            var archivos_fallidos='';
            const len = e.target.files.length;
            
            var files = new Array(); 
            for (var j = 0; j < len; j++){
                files[j] = e.target.files[j];
            }
            
            enviarAjax(files, 0);
            function enviarAjax(files, i){
                if(i < len){
                    formArchivoAnexo.append("soportes1", files[i]);

                    var nameArchivo = files[i].name;        
                    
                    $.ajax({
                        type: 'POST',
                        url: '../bd/cargarSoportesTesoreria.php',
                        data: formArchivoAnexo,
                        dataType:'JSON',
                        processData: false,  
                        contentType: false, 
                        success: function(data){
                            
                            if (data.estado==200){
                                archivos_procesados++;  
                            }else if(data.estado==500 || data.estado==504){
                                // if(archivos_fallidos != ''){
                                //     archivos_fallidos += '<br>';
                                // }
                                archivos_fallidos += '<br>'+data.numero_libranza;
                                // archivos_fallidos +=  data.numero_libranza;

                            }

                            if(len-1 == i){
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Finalizado',
                                    html: '<b>Archivos Procesados: </b>'+archivos_procesados+'<br><b>Archivos fallidos:</b><p style="color: red;">'+archivos_fallidos+'</p>'
                                });
                            }
                            enviarAjax(files, i + 1);
                        }, error: function(data){
                            // if(archivos_fallidos != ''){
                            //     archivos_fallidos += '<br>';
                            // }
                            archivos_fallidos+= '<br>'+nameArchivo;
                            if(len-1 == i){
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Finalizado',
                                    html: '<b>Archivos Procesados: </b>'+archivos_procesados+'<br><b>Archivos fallidos:</b><p style="color: red;">'+archivos_fallidos+'</p>'
                                });
                            }
                            enviarAjax(files, i + 1);
                        }
                    }); 
                }
            }               
            // }
        }
    }
    input.click();
});

$('#input_cargar_desembolso_cliente').click(function detalle(e) {
    
    var input = document.createElement('input');
    input.type = 'file';
    input.setAttribute("multiple","");
    input.setAttribute("accept","application/pdf");
    input.setAttribute("name","soportes1[]");
    input.onchange = e => {
        loading();
        var file = e.target.files[0];
        if (e.target.files.length>60)
        {
            alert("Ha superado el maximo de archivos permitidos para realizar carga masiva (20 archivos)");
        }else{
            var formArchivoAnexo = new FormData();
                formArchivoAnexo.append("tipo_adjunto","desembolso_cliente");


                 var archivos_procesados = 0;
            var archivos_fallidos='';
            const len = e.target.files.length;
            
            var files = new Array(); 
            for (var j = 0; j < len; j++){
                files[j] = e.target.files[j];
            }
            
            enviarAjaxDesembolso(files, 0);
            function enviarAjaxDesembolso(files, i){
                if(i < len){
                    formArchivoAnexo.append("soportes1", files[i]);

                    var nameArchivo = files[i].name;        
                    
                    $.ajax({
                        type: 'POST',
                        url: '../bd/cargarSoportesTesoreria.php',
                        data: formArchivoAnexo,
                        dataType:'JSON',
                        processData: false,  
                        contentType: false, 
                        success: function(data){
                            
                            if (data.estado==200){
                                archivos_procesados++;  
                            }else if(data.estado==500 || data.estado==504){

                                archivos_fallidos += '<br>'+data.numero_libranza;
                                
                            }

                            if(len-1 == i){
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Finalizado',
                                    html: '<b>Archivos Procesados: </b>'+archivos_procesados+'<br><b>Archivos fallidos:</b><p style="color: red;">'+archivos_fallidos+'</p>'
                                });
                            }
                            enviarAjaxDesembolso(files, i + 1);
                        }, error: function(data){
                            archivos_fallidos+= '<br>'+nameArchivo;
                            if(len-1 == i){
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Finalizado',
                                    html: '<b>Archivos Procesados: </b>'+archivos_procesados+'<br><b>Archivos fallidos:</b><p style="color: red;">'+archivos_fallidos+'</p>'
                                });
                            }
                            enviarAjaxDesembolso(files, i + 1);
                        }
                    }); 
                }
            }               
            // }
        }
    }
    input.click();
});


function loading() {
    Swal.fire({
        title: 'Por favor espere',
        text: 'Procesando...',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    Swal.showLoading();
}