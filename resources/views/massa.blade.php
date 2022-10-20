@include('masterhead')

<div class="">
    <h3 class="subtitle">buscar boletos</h3>
</div>

<div class="col-md-5 mt-3 ">
    <form action="{{ route('massa') }}" method="POST" class="d-flex justify-content-around align-items-center">
        @csrf


        <div class="form-floating col-md-6">
            <input type="date" class="form-control" name="campoBusca" id="floatingInput"
                placeholder="name@example.com" required>
            <label for="floatingInput" id="campoBusca">Informe a data</label>
        </div>

        <div class="">

            <button type="submit" class="btn botaoForm" id="botaoForm"><i class="bi bi-search"></i> Buscar</button>
        </div>


    </form>

    @if (isset($boletos))



        <div class="mt-5">
            {{ $boletos->links() }}
            <form action="{{route('imprimirMassa')}}" method="POST">
                @csrf
                <input type="hidden" name="imprimirTodos" class="imprimirTodos" value="">
                <button type="submit"  class="btn botaoForm "  id="botaoForm"><i class="bi bi-search"></i> Imprimir todos!</button>
            </form>
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
                <tbody>

                    @for ($index = 0; $index < count($boletos); $index++)
                        {{-- BOLETOS PAGOS --}}

                            <tr class="boletoPago">
                                <td class="idBoleto">{{ $boletos[$index]['id'] }}</td>
                                <td>{{ formatDateAndTime($boletos[$index]['reg_lancamento']) }}</td>
                                <td>{{ formatDateAndTime($boletos[$index]['reg_vencimento']) }}</td>
                                <td>R${{ formatNumber($boletos[$index]['reg_valor']) }}</td>


                            </tr>

                    @endfor
                </tbody>
            </table>

        </div>
    @endif
</div>
@include('footer')
