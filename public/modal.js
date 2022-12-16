function info(dado) {
    //console.log(dado)

    if(dado.descricao.length <= 2){
        $('.contrato').html(dado.descricao[0])
        $('.mensalidade').html(dado.descricao[1])
    }else{
        dado.descricao.forEach(element => {

        if(element[0] == 'N'){
            $('.contrato').append(element + '<br>')
        }else if(element[0] != 'N'){
            $('.mensalidade').html(element)
        }
       });
    }
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
    $('#idBoletoDeletar').val(dado.idBoleto)
}

$('#botaoDarBaixa').on('click', function () {
    $('#formDarBaixa').submit()
});

if ($("#confirmDelete").length) {
    const myModalEl = document.getElementById('deleteBoleto')
    myModalEl.addEventListener('hidden.bs.modal', event => {
        $("#confirmDelete").prop("checked", false);
        $(".excluirBoletoButton").attr("disabled", true);
        $('#idBoletoDeletar').val('')
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
