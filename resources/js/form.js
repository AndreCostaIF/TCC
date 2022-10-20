$('#flag').on('change', function() {
    $('#campoBusca').val('teste')
    console.log($('#campoBusca').text(this.value.toUpperCase()))
  });




