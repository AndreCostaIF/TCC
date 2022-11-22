@include('masterhead')


<div class="text-center mt-5">
    <h3 class="title text-danger">Converter remessa <b>BRADESCO</b> <i class="bi bi-arrow-right"></i> <b>santander</b></h3>
</div>

<div class="d-flex mt-5 justify-content-center w-100">
    <div class="w-30 mainBox">
        <div class="text-center">
            <h6 class="title text-danger">Envie uma remessa do <u>BRADESCO</u></h6>
        </div>

        <form method="POST" action="{{route('traduzirRemessa')}}" method="POST" enctype="multipart/form-data">
            <div id="hiddens">
                @csrf
            </div>

            <div class="mb-3">
                <label for="formFile" class="form-label">Importar arquivo <b>REMESSA BRADESCO</b></label>

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
@if(isset($remessaSantander))

<div class="d-flex justify-content-center mt-3">
    <div class="alert alert-success w-30" role="alert">
        Arquivo traduzido com sucesso!
      </div>
</div>

<div class="text-center border-top mt-3 border-bottom">
    <h6 class="title text-danger">Dados da conversão</h6>
    <p>Gerado em {{$dataGerado}} às {{$horaGerado}}</p>
</div>

<div class="text-center mt-3">
    <a class="" href="{{$remessaSantander}}" download>Clique aqui para download! <i class="bi bi-download"></i></a>
</div>

@endif

@include('footer')
