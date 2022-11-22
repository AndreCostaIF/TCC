$('#flag').on('change', function() {
    $('#campoBusca').val('teste')
    console.log($('#campoBusca').text(this.value.toUpperCase()))
  });



    if ( $( ".idBoleto" ).length ) {

        if ( $( ".imprimirTodos" ).length ) {

            boletos = [];
            $('.idBoleto').each(function(i, obj) {
                boletos.push(obj.textContent)
            });
            $( ".imprimirTodos" ).val(boletos)
            console.log($( ".idBoleto" ));
        }
    }

