$('.copiar').on('click', function () {

    navigator.clipboard.writeText(this.innerText)
    $('.divSuccessCopy').append('<div class="d-flex justify-content-center"><div class="alert alert-success alert-dismissible  fade col-6 show"'+
    'role="alert">'+
    '<strong><i class="bi bi-clipboard2-check text-success"></i> Texto copiado com sucesso!</strong>'+
    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'+
    '</div></div>')

});

$('.copiarLD').on('click', function () {

    $('.divSuccessCopy').append('<div class="d-flex justify-content-center"><div class="alert alert-success alert-dismissible  fade col-6 show"'+
    'role="alert">'+
    '<strong><i class="bi bi-clipboard2-check text-success"></i> Texto copiado com sucesso!</strong>'+
    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'+
    '</div></div>')
    navigator.clipboard.writeText(this.parentElement.children[0].value)


});
