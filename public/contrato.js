function contrato(dado){
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
}
