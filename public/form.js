$('#flag').on('change', function () {
    $('#campoBusca').html($('#flag').val().toUpperCase())
});

if ($(".idBoleto").length) {

    if ($(".imprimirTodos").length) {

        boletos = [];
        $('.idBoleto').each(function (i, obj) {
            boletos.push(obj.textContent)
        });
        $(".imprimirTodos").val(boletos)
        console.log($(".idBoleto"));
    }
}

function darbaixa(boleto) {
    $('#idBoleto').val(boleto.idBoleto)
    $('#vencimento').val(boleto.vencimento)
    $('#mes_Ref').val(boleto.mes_ref)
    $('#ano_ref').val(boleto.ano_ref)
    $('#valor').val(boleto.valor)
    $('#tipo_bx').val(boleto.tipo_baixa)
    $('#mensalidade').val(boleto.mensalidade)
    $('#valor_pago').val(boleto.valor_pago)
}

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

