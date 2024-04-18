function alerta_success(titulo, cuerpo) {
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.preventDuplicates = true;
    toastr.success(cuerpo, titulo);
}

function alerta_error(titulo, cuerpo) {
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.preventDuplicates = true;
    toastr.error(cuerpo, titulo);
}

function alerta_info(titulo, cuerpo) {
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.preventDuplicates = true;
    toastr.info(cuerpo, titulo);
}


