@include('masterhead')

<div class="row">
    <span class="title text-danger h2 text-center">Buscar boletos por cliente</span>
</div>

<div class="">
    <h3 class="subtitle">buscar boletos</h3>
</div>

<div class="col-md-6 mt-3 ">
    <form action="{{ route('buscarCliente') }}" method="GET" class="d-flex justify-content-between align-items-center">
        @csrf
        <div class="form-floating col-md-3">
            <select class="form-select" id="flag" name="flag" aria-label="Floating label select example"
                required>
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

            <button type="submit" class="btn btn-outline-danger" id="botaoForm"><i class="bi bi-search"></i>
                Buscar</button>
        </div>


    </form>
</div>

@if (session()->has('erroCliente'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong> {{ session()->get('erroCliente') }}!</strong> Algo de inesperado aconteceu :(
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                            <th scope="row">{{ $cliente->nome }}</th>
                            <td>{{ formatarCpf($cliente->cpf) }}</td>
                        @elseif (isset($cliente->fantasia))
                            <th scope="row">{{ $cliente->fantasia }} </th>
                            <td>{{ formatarCnpj($cliente->cnpj) }}</td>
                        @endif
                        <td>
                            <a href="{{ route('listarBoletos', [$cliente->id, $flag]) }}"
                                class="btn btn-success text-white border-success" id="botaoForm">
                                <i class="bi bi-receipt-cutoff"></i> Ver boletos
                            </a>
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
                        <th scope="col">Opções</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($boletos as $boleto)
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
                                <td></td>
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
                                <td>{{ $boleto->id }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td></td>
                                <td>R${{ formatNumber($boleto->reg_valor) }}</td>
                                <td>R$00,00</td>
                                <td><i class="bi bi-three-dots-vertical"></i></td>
                                <td>
                                    <a target="_blank" href="{{ route('imprimirBoleto', [$boleto->id]) }}">
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
                                <td class="">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi h4 bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal">
                                                    <i class="bi bi-currency-dollar text-success"></i> Dar baixa
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" type="button">
                                                    <i class="bi bi-pencil-square text-primary"></i> Editar boleto
                                                </button>
                                            </li>
                                            <li><button class="dropdown-item" type="button">
                                                    <i class="bi bi-info-circle text-info"></i> Mais informações
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <a target="_blank" href="{{ route('imprimirBoleto', [$boleto->id]) }}"
                                        class="" id=""><img src="{{ asset('assets/boleto.png') }}"
                                            class="imgBoleto" alt=""></a>
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


        <!-- Modal -->
        <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"> <i
                                class="bi bi-currency-dollar text-success"></i> Dar baixa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Vencimento</label>
                                <input type="date" class="form-control" id="exampleInputEmail1"
                                    aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Mês de referência</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Ano de referência</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Valor Principal (R$)</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                    aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Tipo de baixa</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Mensalidade</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Valor pago</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                    aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">Check me out</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    @else
    @endif
@endif

@include('footer')
