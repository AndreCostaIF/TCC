function dataAtualFormatada(data) {
    var data = new Date(data),
        dia = data.getDate().toString().padStart(2, '0'),
        mes = (data.getMonth() + 1).toString().padStart(2, '0'), //+1 pois no getMonth Janeiro começa com zero.
        ano = data.getFullYear();
    return dia + "/" + mes + "/" + ano;
}
function formatCnpjCpf(value) {
    const cnpjCpf = value.replace(/\D/g, '');

    if (cnpjCpf.length === 11) {
        return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
    }
    return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
}

if ($("#idboleto").length) {
    $('#idboleto').on('input', function (e) {
        if ($('#idboleto').val().length >= 6) {

            $('.loadCobranca').show()

            let token = document.getElementsByName("_token")
            e.preventDefault();
            $.ajax({
                url: rotaBuscarDadosPix,
                method: 'GET',
                data: {
                    _token: token[0].defaultValue,
                    id: $('#idboleto').val()
                },
                dataType: 'json',

                success: function (result) {
                    $('.loadCobranca').hide()
                    $('.erro').hide()
                    $('.formDataPix').show()
                    $('#nomeDevedor').val(result.cliente.nome)

                    if (result.cliente.cnpj) {
                        $('.hibrido').html('CNPJ')
                        $('#cpf').attr('name', 'cnpj');
                        $('#cpf').val(formatCnpjCpf(result.cliente.cnpj))

                    } else {
                        $('.hibrido').html('CPF')
                        $('#cpf').attr('name', 'cpf');
                        $('#cpf').val(formatCnpjCpf(result.cliente.cpf))
                    }

                    $('#vencimento').val(dataAtualFormatada(result.boleto.reg_vencimento))
                    $('#lancamento').val(dataAtualFormatada(result.boleto.reg_lancamento))
                    $('#mes_ref').val(result.boleto.mes_referencia)
                    $('#valor').val('R$' + parseInt(result.boleto.reg_valor_total).toFixed(2))
                    $('#infoAdicionais').val(result.boleto.descricao)
                    $('#gerarCobranca').show()
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $('.loadCobranca').hide()
                    $('.formDataPix').hide()
                    $('.erro').show()
                    $('#gerarCobranca').hide()


                }
            });
        } else {
            //var form = $("#formCobranca")[0];
            //form.reset()
            $('.formDataPix').hide()
            $('#gerarCobranca').hide()
        }
    });
}

$('#gerarCobranca').on('click', function (e) {


    let token = document.getElementsByName("_token")
    e.preventDefault();

    let formData = new FormData();

    formData.append('_token', token[0].defaultValue)
    formData.append('idboleto', $('#idboleto').val())
    formData.append('nomeDevedor', $('#nomeDevedor').val())
    formData.append('cpf', $('#cpf').val())

    formData.append('vencimento', $('#vencimento').val())
    formData.append('valor', $('#valor').val())
    formData.append('infoAdicionais', $('#infoAdicionais').val())


    $.ajax({
        type: 'POST',
        url: rotaCriarCobranca,

        data: formData,
        contentType: false,
        processData: false,
        success: function (result) {

            $('.nome').html(result.nome)
            $('.qrcode').attr('src', 'data:image/png;base64, ' + result.qrcode.image)
            $('.qrcode').addClass('border')
            $('.boxQrCode').attr('href', 'data:image/png;base64, ' + result.qrcode.image)
            $('.boxQrCode').attr('download', result.nome+'-pix'+ result.id)
            $('.beneficiario').html(result.qrcode.empresa)
            $('.chave').html(result.chave)
            $('.valor').html(result.valor)
            $('#pixCopiaCola').val(result.qrcode.payload)
            let link = rotaImprimirBoletoPix.replace("-1", result.id);
            $('.imprimirBoletoPix').attr('href', link);
            //console.log(result)
            $('.pixbuttons').show()
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log('erro')

        }
    });
});


document.getElementById("toastbtn").onclick = function () {
    const toastLiveExample = document.getElementById('notificacao')
    const toast = new bootstrap.Toast(toastLiveExample)
    toast.show()
}


const modalCobranca = document.getElementById('modalCobranca')
const modalCobranca2 = document.getElementById('modalCobranca2')
modalCobranca.addEventListener('hidden.bs.modal', event => {
    var form = $("#formCobranca")[0];
    form.reset()
    $('.formDataPix').hide()
    $('#gerarCobranca').hide()
})

modalCobranca.addEventListener('shown.bs.modal', event => {
    var form = $("#formCobranca")[0];
    form.reset()
    $('.formDataPix').hide()
    $('.pixbuttons').hide()
    $('#gerarCobranca').hide()
})


modalCobranca2.addEventListener('hidden.bs.modal', event => {
    $('.nome').html('<div class="spinner-border text-danger" role="status"><span class="visually-hidden">Carregando...</span> </div>')
    $('.qrcode').removeClass('border')
    $('.beneficiario').html('<div class="spinner-border text-danger" role="status"><span class="visually-hidden">Carregando...</span> </div>')
    $('.chave').html('<div class="spinner-border text-danger" role="status"><span class="visually-hidden">Carregando...</span> </div>')
    $('.valor').html('<div class="spinner-border text-danger" role="status"><span class="visually-hidden">Carregando...</span> </div>')
    $('#pixCopiaCola').val("")
    $('.imprimirBoletoPix').removeAttr('href')
    $('.qrcode').removeAttr('src')
    $('.boxQrCode').removeAttr('href')
    $('.pixbuttons').hide()
    $('#gerarCobranca').hide()



})
