</div>

<footer>

    <div class="border-top text-center">
        <small class=""><b> © 2012 - 20{{date('y')}} Intelnet Telecomunicações. Todos os direitos reservados.</b></small>
    </div>


    <script>
        var rotaBuscarDadosPix = '{{route('buscarDadosBoleto') }}';
        var rotaCriarCobranca = '{{route('criarCobranca') }}';
        var rotaImprimirBoletoPix = '{{ route('imprimirBoletoPIX', ['id'=>-1]) }}';
    </script>
    <script src="{{ asset('jquery.js') }}"></script>
    <script src="{{ asset('bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('form.js') }}"></script>

    <script src="{{ asset('modal.js') }}"></script>
    <script src="{{ asset('copy.js') }}"></script>
    <script src="{{ asset('contrato.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
</footer>
</body>

</html>
