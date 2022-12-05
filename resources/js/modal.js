function info(dado) {
    //console.log(dado)
    if(dado.desconto > 0){
       $('.BoxDesconto').show()
       $('.descontoValor').html("-R$"+dado.desconto)
    }
    else if(dado.acrescimo > 0){
        $('.BoxAcrescimo').show()
        $('.acrescimoValor').html("+R$"+dado.acrescimo)
    }else{
        $('.modal-body').append('<div class="msgInfo text-center fw-bold">Não há desconto ou acréscimo para este titulo. </div>')
    }
}

$('#botaoDarBaixa').on('click', function () {
    $('#formDarBaixa').submit()

});

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
