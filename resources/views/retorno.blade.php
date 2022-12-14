@include('masterhead')

<div class="text-center mt-5">
    <h3 class="title text-danger">Converter retorno <b>santander</b> <i class="bi bi-arrow-right"></i> <b>BRADESCO</b></h3>
</div>

<div class="d-flex justify-content-center mt-5 w-100">
    <div class="w-30 mainBox">
        <div class="text-center">
            <h6 class="title text-danger">Envie um RETORNO do <u>SANTANDER</u></h6>
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
@if (session()->has('msg'))
    <div class="d-flex mt-3 justify-content-center">
        <div class="alert alert-danger border border-danger  alert-dismissible fade show mt-3" role="alert">
            <strong><i class="bi bi-file-earmark-x"></i> {{ session()->get('msg') }} </strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
@if(isset($retornoBradesco))
<div class="alert alert-success text-center mt-5 alert-dismissible fade show" role="alert">
    <strong>Arquivo traduzido com sucesso!</strong>
    <div class="text-center border-top mt-3 border-bottom">
        <h6 class="title text-danger">Dados da conversão</h6>
        <p>Gerado em {{ $dataGerado }} às {{ $horaGerado }}</p>
    </div>
    <a class="" href="{{ $retornoBradesco }}" download>Clique aqui para download! <i
        class="bi bi-download"></i>
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

@endif

<div class="mt-5 row">
    <div class="text-center">
       <span class="fw-bold h4">Historico de retornos</span>
       <span class="text-danger h4 fw-bold">Bradesco</span>
    </div>
    <hr>
    @if (isset($historico))
        <div class="mt-3">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-danger">ID</th>
                        <th scope="col" class="text-danger">Traduzido em</th>
                        <th scope="col" class="text-danger">Usuário</th>
                        <th scope="col" class="text-danger">Arquivo </th>
                    </tr>
                </thead>
                <div class=" d-flex justify-content-center">
                    {{ $historico->links() }}
                </div>
                <tbody>

                    @foreach ($historico as $item)
                        <tr>
                            <th scope="row">{{ $item->id }}</th>
                            <td>{{ formatDateAndTime($item->dataTraducao, 'd/m/y H:i:s')  }}</td>
                            <td>{{ $item->autor }}</td>
                            <td>
                                <a class="" href="{{ asset($item->nomeRetorno) }}" download>
                                    <i class="bi bi-download"></i>
                                    {{ $item->nomeRetorno }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@include('footer')
