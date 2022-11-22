@include('masterhead')

<div class="text-center mt-5">
    <h3 class="title">Converter retorno <b>santander</b> <i class="bi bi-arrow-right"></i> <b>BRADESCO</b></h3>
</div>

<div class="d-flex justify-content-center mt-5 w-100">
    <div class="w-30 mainBox">
        <div class="text-center">
            <h6 class="title">Envie um RETORNO do <u>SANTANDER</u></h6>
        </div>

        <form method="POST" action="{{route('traduzirRetorno')}}" method="POST" enctype="multipart/form-data">
            <div id="hiddens">
                @csrf
            </div>

            <div class="mb-3">
                <label for="formFile" class="form-label">Importar arquivo <b>RETORNO SANTANDER</b></label>

                <input class="form-control" type="file" name="arq" required>
            </div>
            <div class="modal-footer">

                <button type="submit" class="btn btn-outline-danger" id="botaoForm">Importar</button>
            </div>
        </form>

        {{-- <form class="form-floating " action="{{route('traduzirRemessa')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" class="form-control is-invalid w-50" id="floatingInputInvalid" placeholder="name@example.com" value="test@example.com">
            <label for="floatingInputInvalid">Invalid input</label>

            <input type="file" name="arq">
            <button type="submit">mandar</button>

        </form> --}}
    </div>
</div>
@if(isset($retornoBradesco))

<div class="d-flex justify-content-center mt-3">
    <div class="alert alert-success w-30" role="alert">
        Arquivo traduzido com sucesso!
      </div>

</div>

<div class="text-center border-top mt-3 border-bottom">
    <h6 class="title">Dados da conversão</h6>
    <p>Gerado em {{$dataGerado}} às {{$horaGerado}}</p>
</div>

<div class="text-center mt-3">

    <a class="" href="{{$retornoBradesco}}" download>Clique aqui para baixar! <i class="bi bi-download"></i></a>
</div>

@endif

@include('footer')
