@include('masterhead')
<div class="row">
    <span class="title text-danger h2 text-center">Imprimir boletos em massa</span>
</div>
<div class="mt-5 text-center">
    <h3 class="subtitle">buscar boletos</h3>
</div>
<div class="d-flex justify-content-center ">
    <div class="col-md-5  mt-3">
        <form action="{{ route('massa') }}" method="GET" class="d-flex justify-content-around align-items-center">
            @csrf
            <div class="form-floating col-md-6">
                <input type="date" class="form-control" name="data" id="floatingInput"
                    placeholder="name@example.com" required>
                <label for="floatingInput" id="campoBusca">Informe a data</label>
            </div>
            <div class="">
                <button type="submit" class="btn btn-outline-danger" id="botaoForm">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>
<div class="mt-3">
    <table class="table  table-hover">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Lançamento</th>
                <th scope="col">Vencimento</th>
                <th scope="col">Valor a pagar</th>
                <th></th>
            </tr>
        </thead>
        @if (isset($boletos))
            <div class="mt-5">
                <div class=" d-flex justify-content-center">
                    {{ $boletos->links() }}
                </div>
                <div class=" d-flex justify-content-around">
                    <span> Boletos nesta página: {{ $boletos->count() }}</span>
                    <span> Total do lote: {{ $boletos->total() }}</span>
                   </div>
                @if ($boletos->total() > 0)
                    <form action="{{ route('imprimirMassa') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="imprimirTodos" class="imprimirTodos" value="">
                        <button type="submit" class="btn btn-outline-danger " id="botaoForm">
                            <i class="bi bi-printer"></i> Imprimir todos
                        </button>
                    </form>
                @endif
                <tbody>
                    @foreach ($boletos as $boleto)
                        <tr class="boletoPago">
                            <td class="idBoleto">{{ $boleto->id }}</td>
                            <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                            <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                            <td>R${{ formatNumber($boleto->reg_valor_total) }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </div>
        @endif
    </table>
    @if (isset($boletos))
        @if ($boletos->total() <= 0)
            <div class="mt-4">
                <div class="alert alert-danger " role="alert">
                    <div>
                        <i class="bi bi-exclamation-triangle-fill"></i><b>Nenhum boleto encontrado!</b> Por favor,
                        selecione outra data.
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
@include('footer')
