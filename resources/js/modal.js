function info(dado) {
    console.log(dado)
    if (dado.desconto > 0) {
        $('.BoxDesconto').show()
        $('.descontoValor').html("-R$" + dado.desconto)
    }
    else if (dado.acrescimo > 0) {
        $('.BoxAcrescimo').show()
        $('.acrescimoValor').html("+R$" + dado.acrescimo)
    } else {
        $('.modal-body').append('<div class="msgInfo text-center fw-bold">Não há desconto ou acréscimo para este titulo. </div>')
    }
}

$('#confirmDelete').on('click', function () {
    if ($("#confirmDelete").prop('checked')) {

        $(".excluirBoletoButton").removeAttr("disabled");
    } else {

        $(".excluirBoletoButton").attr("disabled", true);
    }
});

function deleteBoleto(dado) {
    console.log(new Date(dado.vencimento2))
    $('.situacao').html(new Date(dado.vencimento2) > new Date() ? 'Em aberto <i class="bi bi-check2-circle fw-bold text-success"></i>' : 'Em atraso <i class="bi bi-x-circle fw-bold text-danger"></i>')
    $('.faturaID').html(dado.idBoleto)
    $('.nomeCliente').html(dado.cliente)
    $('.vencimento').html(dado.vencimento)
    $('.refBoleto').html(dado.mes_ref + "/" + dado.ano_ref)
    $('.valorBoleto').html("R$" + dado.valor)
}



$('#botaoDarBaixa').on('click', function () {
    $('#formDarBaixa').submit()

});

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

   // alert('texto copiado!')

});


if ($("#confirmDelete").length) {
    const myModalEl = document.getElementById('deleteBoleto')
    myModalEl.addEventListener('hidden.bs.modal', event => {
        $("#confirmDelete").prop("checked", false);
        $(".excluirBoletoButton").attr("disabled", true);
    })
}

if ($("#maisInfo").length) {
    const myModalEl = document.getElementById('maisInfo')
    myModalEl.addEventListener('hidden.bs.modal', event => {
        $('.BoxDesconto').hide()
        $('.BoxAcrescimo').hide()
        if ($(".msgInfo").length) {
            $(".msgInfo").remove()
        }
    })
}
