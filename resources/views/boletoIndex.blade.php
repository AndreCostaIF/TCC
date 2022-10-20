@include('masterhead')

<div class="">
    <h3 class="subtitle">buscar boletos</h3>
</div>

<div class="col-md-6 mt-3 ">
    <form action="{{ route('buscarCliente') }}" method="GET" class="d-flex justify-content-between align-items-center">
        @csrf
        <div class="form-floating col-md-3">
            <select class="form-select" id="flag" name="flag" aria-label="Floating label select example" required>
                <option value="">Selecione</option>
                <option value="cpf">CPF</option>
                <option value="nome">Nome</option>
                <option value="cnpj">CNPJ</option>
                <option value="fantasia">Fantasia</option>
            </select>
            <label for="floatingSelect">Buscar por</label>
        </div>

        <div class="form-floating col-md-6">
            <input type="text" class="form-control" name="campoBusca" id="floatingInput"
                placeholder="name@example.com" required>
            <label for="floatingInput" id="campoBusca"></label>
        </div>

        <div class="">

            <button type="submit" class="btn botaoForm" id="botaoForm"><i class="bi bi-search"></i> Buscar</button>
        </div>


    </form>
</div>

@if (isset($clientesBusca))
    <div class="mt-5">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">Cliente</th>
                    <th scope="col">CPF/CNPJ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientesBusca as $cliente)
                    <tr class="">
                        @if (isset($cliente->nome))
                            <th scope="row">{{ $cliente->nome }} </th>
                            <td>{{ formatarCpf($cliente->cpf) }}</td>
                        @elseif (isset($cliente->fantasia))
                            <th scope="row">{{ $cliente->fantasia}} </th>
                            <td>{{ formatarCnpj($cliente->cnpj) }}</td>
                        @endif
                        <td>
                            <a href="{{ route('listarBoletos', [$cliente->id, $flag]) }}"
                                class="btn botaoForm bg-success text-white border-success" id="botaoForm"><i
                                    class="bi bi-receipt-cutoff"></i> Ver boletos</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mb-3 d-flex justify-content-center">
            {{ $clientesBusca->links() }}
        </div>
    </div>

@endif
{{-- COLOCAR IF SE EXISTIR CLIENTE --}}
@if (isset($cliente))
    @if (isset($boletos))

    <div class="mt-5">
            <table class="table  table-hover">
                <thead>
                    <tr>
                        <th scope="col">Cliente</th>
                        <th scope="col">ID</th>
                        <th scope="col">Lançamento</th>
                        <th scope="col">Vencimento</th>
                        <th scope="col">Pagamento</th>
                        <th scope="col">Valor a pagar</th>
                        <th scope="col">Valor pago</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ( $boletos as $boleto )

                        {{-- BOLETOS PAGOS --}}

                        @php
                            $vencimento = substr($boleto->reg_vencimento, 2, 8);

                        @endphp

                        @if ($boleto->reg_baixa != 0 && $boleto->reg_deleted == 0)
                            <tr class="boletoPago">
                                <th scope="row"><a
                                        href="http://177.223.83.142/admin/clientes/visualizar/id/{{ $cliente['idCliente'] }}"
                                        target="_blank">{{ $cliente['nome'] }}</a> </th>
                                <td class="idBoleto">{{ $boleto->id }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td>{{ formatDateAndTime($boleto->bx_pagamento) }}</td>
                                <td>R${{ formatNumber($boleto->reg_valor) }}</td>
                                <td>R${{ formatNumber($boleto->bx_valor_pago) }}</td>
                                <td>
                                    <button class="btn botaoForm bg-success text-white border-success" id="botaoForm"
                                        disabled><i class="bi bi-check2-circle"></i> Pago</button>
                                </td>

                            </tr>
                        @endif
                        @if ($boleto->reg_baixa == 0 && $boleto->reg_deleted == 0 && $vencimento >= date('y-m-d'))
                            <tr class="boletoAberto">
                                <th scope="row"><a
                                        href="http://177.223.83.142/admin/clientes/visualizar/id/{{ $cliente['idCliente'] }}"
                                        target="_blank">{{ $cliente['nome'] }}</a> </th>
                                <td >{{ $boleto->id }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td></td>
                                <td>R${{ formatNumber($boleto->reg_valor) }}</td>
                                <td>R$00,00</td>
                                <td>
                                    <a href="{{ route('imprimirBoleto', [$boleto->id]) }}">
                                        <img src="{{ asset('assets/boleto.png') }}" class="imgBoleto" alt="">
                                    </a>
                                </td>

                            </tr>
                        @endif


                        @if ($vencimento < date('y-m-d') && $boleto->reg_baixa == 0 && $boleto->reg_deleted == 0)
                            <tr class="boletoAtraso">
                                <th scope="row"><a
                                        href="http://177.223.83.142/admin/clientes/visualizar/id/{{ $cliente['idCliente'] }}"
                                        target="_blank">{{ $cliente['nome'] }}</a> </th>
                                <td>{{ $boleto->id }}</td>

                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td></td>
                                <td>R${{ formatNumber($boleto->reg_valor) }}</td>
                                <td>R$00,00</td>
                                <td>
                                    <a href="{{ route('imprimirBoleto', [$boleto->id]) }}"
                                        class="" id=""><img src="{{ asset('assets/boleto.png') }}" class="imgBoleto" alt=""></a>
                                </td>

                            </tr>
                        @endif



                        {{-- IF dataVencimento < dataAtual --}}

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="mb-3 d-flex justify-content-center">
            {{ $boletos->links() }}
        </div>
    @else
    @endif
@endif

@include('footer')
