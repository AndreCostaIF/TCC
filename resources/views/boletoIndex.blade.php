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
        <div class="mt-4">
            <div class=" divSuccessCopy" >

            </div>
            <table class="table  table-hover">
                <thead>
                    <tr>
                        <th scope="col">Cliente</th>

                        @if (isset($cliente['cpf']))
                            <th scope="col">CPF</th>
                        @elseif (isset($cliente['cnpj']))
                            <th scope="col">CNPJ</th>
                        @endif
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
                                        target="_blank">{{ $cliente['nome'] }}</a>
                                </th>
                                @if (isset($cliente['cpf']))
                                    <td><a href="#" class="copiar"> {{ formatarCpf($cliente['cpf']) }}</a></td>
                                @elseif (isset($cliente['cnpj']))
                                    <td><a href="#" class="copiar">{{ formatarCnpj($cliente['cnpj']) }} </a></td>
                                @endif

                                <td class="idBoleto">{{ $boleto->id }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td>{{ formatDateAndTime($boleto->bx_pagamento) }}</td>
                                <td>R${{ formatNumber($boleto->reg_valor_total) }}</td>
                                <td>R${{ formatNumber($boleto->bx_valor_pago) }}</td>
                                <td class="">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi h4 bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @php
                                                $dado = [
                                                    'desconto' => $boleto->desconto,
                                                    'acrescimo' => $boleto->acrescimo,
                                                ];
                                                $dado = json_encode($dado);
                                            @endphp
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#maisInfo" onclick="info({{ $dado }})">
                                                    <i class="bi bi-info-circle text-info"></i> Mais informações
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
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
                                        target="_blank">{{ $cliente['nome'] }}</a>
                                </th>

                                @if (isset($cliente['cpf']))
                                    <td><a href="#" class="copiar"> {{ formatarCpf($cliente['cpf']) }}</a></td>
                                @elseif (isset($cliente['cnpj']))
                                    <td><a href="#" class="copiar">{{ formatarCnpj($cliente['cnpj']) }} </a></td>
                                @endif
                                <td>{{ $boleto->id }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td></td>
                                <td>R${{ formatNumber($boleto->reg_valor_total) }}</td>
                                <td>R$00,00</td>
                                <td class="">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi h4 bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {{-- <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal">
                                                    <i class="bi bi-currency-dollar text-success"></i> Dar baixa
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" type="button">
                                                    <i class="bi bi-pencil-square text-primary"></i> Editar boleto
                                                </button>
                                            </li> --}}

                                            @php
                                                $dado = [
                                                    'desconto' => $boleto->desconto,
                                                    'acrescimo' => $boleto->acrescimo,
                                                ];
                                                $dado = json_encode($dado);
                                            @endphp
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#maisInfo" onclick="info({{ $dado }})">
                                                    <i class="bi bi-info-circle text-info"></i> Mais informações
                                                </button>
                                            </li>
                                            @if (session()->get('grupo_users_id') == 1 || session()->get('grupo_users_id') == 2)
                                                {{-- <li>
                                                    @php
                                                        $dado = [
                                                            'idBoleto'      => $boleto->id,
                                                            'vencimento'    => formatDateAndTime($boleto->reg_vencimento),
                                                            'vencimento2'   => $boleto->reg_vencimento,
                                                            'valor'         => formatNumber($boleto->reg_valor_total),
                                                            'mes_ref'       => $boleto->mes_referencia,
                                                            'ano_ref'       => $boleto->ano_referencia,
                                                            'cliente'       => $cliente['nome'],
                                                        ];
                                                        $dado = json_encode($dado);
                                                    @endphp
                                                    <button class="dropdown-item" type="button"
                                                        data-bs-toggle="modal" data-bs-target="#deleteBoleto"
                                                        onclick="deleteBoleto({{ $dado }})">
                                                        <i class="bi bi-trash3 text-danger"></i> Excluir boleto
                                                    </button>
                                                </li> --}}
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <a target="_blank" href="{{ route('imprimirBoleto', [$boleto->id]) }}">
                                        <img src="{{ asset('assets/boleto.png') }}" class="imgBoleto"
                                            alt="">
                                    </a>
                                </td>


                            </tr>
                        @endif


                        @if ($vencimento < date('y-m-d') && $boleto->reg_baixa == 0 && $boleto->reg_deleted == 0)
                            <tr class="boletoAtraso">
                                <th scope="row"><a
                                        href="http://177.223.83.142/admin/clientes/visualizar/id/{{ $cliente['idCliente'] }}"
                                        target="_blank">{{ $cliente['nome'] }}</a>
                                </th>

                                @if (isset($cliente['cpf']))
                                    <td><a href="#" class="copiar"> {{ formatarCpf($cliente['cpf']) }}</a></td>
                                @elseif (isset($cliente['cnpj']))
                                    <td><a href="#" class="copiar">{{ formatarCnpj($cliente['cnpj']) }} </a>
                                    </td>
                                @endif
                                <td>{{ $boleto->id }}</td>

                                <td>{{ formatDateAndTime($boleto->reg_lancamento) }}</td>
                                <td>{{ formatDateAndTime($boleto->reg_vencimento) }}</td>
                                <td></td>
                                <td>R${{ formatNumber($boleto->reg_valor_total) }}</td>
                                <td>R$00,00</td>
                                <td class="">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi h4 bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="https://www.santander.com.br/2-via-boleto" target="_blank"
                                                    class="dropdown-item" type="button">
                                                    <i class="bi bi-receipt-cutoff text-danger"></i> 2º via
                                                </a>
                                            </li>
                                            {{-- <li>
                                                @php
                                                    $dado = [
                                                        'idBoleto'      => $boleto->id,
                                                        'vencimento'    => $boleto->reg_vencimento,
                                                        'valor'         => formatNumber($boleto->reg_valor_total),
                                                        'mes_ref'       => $boleto->mes_referencia,
                                                        'ano_ref'       => $boleto->ano_referencia,
                                                        'mensalidade'   => $boleto->mensalidade,
                                                        'tipo_baixa'    => $boleto->reg_baixa,
                                                        'valor_pago'    => formatNumber($boleto->bx_valor_pago),
                                                    ];
                                                    $dado = json_encode($dado);
                                                @endphp
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal"
                                                    onclick="darbaixa({{ $dado }})">
                                                    <i class="bi bi-currency-dollar text-success"></i> Dar baixa
                                                </button>
                                            </li> --}}
                                            <li>
                                                <input type="hidden" value="{{ $boleto->linhaDigitavel }}"
                                                    class="LD">
                                                <a href="#" class="copiarLD dropdown-item"
                                                    class="dropdown-item" type="button">
                                                    <i class="bi bi-clipboard2 text-primary"></i> Copiar código de
                                                    barras
                                                </a>
                                            </li>
                                            {{-- <li>
                                                <button class="dropdown-item" type="button">
                                                    <i class="bi bi-pencil-square text-primary"></i> Editar boleto
                                                </button>
                                            </li> --}}
                                            @php
                                                $dado = [
                                                    'desconto' => $boleto->desconto,
                                                    'acrescimo' => $boleto->acrescimo,
                                                ];
                                                $dado = json_encode($dado);
                                            @endphp
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#maisInfo" onclick="info({{ $dado }})">
                                                    <i class="bi bi-info-circle text-info"></i> Mais informações
                                                </button>
                                            </li>
                                            @if (session()->get('grupo_users_id') == 1 || session()->get('grupo_users_id') == 2)
                                                {{-- <li>
                                                    @php
                                                        $dado = [
                                                            'idBoleto' => $boleto->id,
                                                            'vencimento' => formatDateAndTime($boleto->reg_vencimento),
                                                            'vencimento2' => $boleto->reg_vencimento,
                                                            'valor' => formatNumber($boleto->reg_valor_total),
                                                            'mes_ref' => $boleto->mes_referencia,
                                                            'ano_ref' => $boleto->ano_referencia,
                                                            'cliente' => $cliente['nome'],
                                                        ];
                                                        $dado = json_encode($dado);
                                                    @endphp
                                                    <button class="dropdown-item" type="button"
                                                        data-bs-toggle="modal" data-bs-target="#deleteBoleto"
                                                        onclick="deleteBoleto({{ $dado }})">
                                                        <i class="bi bi-trash3 text-danger"></i> Excluir boleto
                                                    </button>
                                                </li> --}}
                                            @endif


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


        <!-- Modal BAIXA-->
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
                        <form action="{{ route('baixaBoleto') }}" method="POST" id="formDarBaixa">
                            <div>
                                @csrf
                                <input type="hidden" class="form-control" name="idBoleto" id="idBoleto"
                                    value="">
                            </div>
                            <div>
                                @error('idBoleto')
                                    {{ $message }}
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Vencimento</label>
                                <input type="date" class="form-control" id="vencimento"
                                    aria-describedby="emailHelp" name="vencimento">
                                <div>
                                    @error('vencimento')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Mês de referência</label>
                                <select class="form-select" aria-label="Default select example" name="mes_referencia"
                                    id="mes_Ref">
                                    <option selected>Selecione</option>
                                    <option value="01">Janeiro</option>
                                    <option value="02">Fevereiro</option>
                                    <option value="03">Março</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Maio</option>
                                    <option value="06">Junho</option>
                                    <option value="07">Julho</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                                <div>
                                    @error('mes_referencia')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Ano de referência</label>
                                <select class="form-select" aria-label="Default select example" name="ano_referencia"
                                    id="ano_ref">
                                    <option selected>Open this select menu</option>
                                    <option value="20{{ date('y') }}">20{{ date('y') }}</option>
                                    <option value="20{{ date('y') - 1 }}">20{{ date('y') - 1 }}</option>
                                    <option value="20{{ date('y') - 1 }}">20{{ date('y') - 2 }}</option>
                                </select>
                                <div>
                                    @error('ano_referencia')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Valor Principal (R$)</label>
                                <input type="text" class="form-control" name="reg_valor" id="valor"
                                    aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">Valor do boleto em reais.
                                </div>
                                <div>
                                    @error('reg_valor')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Tipo de baixa</label>
                                <select class="form-select" aria-label="Default select example" name="tipo_bx"
                                    id="tipo_bx">
                                    <option selected>Selecione</option>
                                    <option value="0">Em aberto</option>
                                    <option value="2">Em mãos</option>
                                </select>
                                <div>
                                    @error('tipo_bx')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Mensalidade?</label>
                                <select class="form-select" aria-label="Default select example" name="mensalidade"
                                    id="mensalidade">
                                    <option selected>Open this select menu</option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                                <div>
                                    @error('mensalidade')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Valor pago</label>
                                <input type="text" class="form-control" name="valor_pago" id="valor_pago"
                                    aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">Valor pago pelo cliente.
                                </div>
                                <div>
                                    @error('valor_pago')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" type="submit" id="botaoDarBaixa" class="btn btn-primary">Dar
                            baixa</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal MAIS INFORMAÇÕES-->
        <div class="modal fade" id="maisInfo" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"> <i
                                class="bi bi-info-circle text-info"></i> Informações</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="BoxDesconto mb-3" style="display: none">
                                <div class="h5 text-primary text-center border-bottom">Descontos <i
                                        class="bi  bi-graph-down-arrow"></i></div>
                                <div class="d-flex justify-content-between ">
                                    <span class="desconto fw-bold">Desconto:</span>
                                    <span class="descontoValor text-primary fw-bold"> R$10,00</span>
                                </div>
                            </div>

                            <div class="BoxAcrescimo mb-3" style="display: none">
                                <div class="h5 text-success text-center border-bottom">Acréscimos <i
                                        class="bi  bi-graph-up-arrow"></i></div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="acrescimo fw-bold">Acréscimo:</span>
                                    <span class="acrescimoValor text-success fw-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal delete boleto-->
        <div class="modal fade" id="deleteBoleto" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"> <i class="bi bi-trash3 text-danger"></i>
                            Excluir boleto</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="card">
                                <h5 class="card-header d-flex justify-content-between">
                                    <span>
                                        Fatura - <span class="faturaID"></span>
                                    </span>

                                    <small class="h6">
                                        Situação: <small class="situacao"></small>
                                    </small>
                                </h5>
                                <div class="card-body">
                                    <h5 class="p-2 card-title  border-bottom d-flex justify-content-between">
                                        <span class="fw-bold nomeCliente"></span>
                                        <span><i class="bi bi-person-badge"></i></span>
                                    </h5>
                                    <div class="mb-3 p-2">
                                        <div class="card-text mb-2 d-flex justify-content-between">
                                            <span class="fw-bold">ID do boleto:</span>
                                            <span class="faturaID"></span>
                                        </div>

                                        <div class="card-text mb-2 d-flex justify-content-between">
                                            <span class="fw-bold">Vencimento:</span>
                                            <span class="vencimento"></span>
                                        </div>

                                        <div class="card-text mb-2 d-flex justify-content-between">
                                            <span class="fw-bold">Fatura referente a:</span>
                                            <span class="refBoleto"></span>
                                        </div>

                                        <div class="card-text mb-2 d-flex justify-content-between">
                                            <span class="fw-bold">Valor:</span>
                                            <span class="valorBoleto"></span>
                                        </div>
                                        <hr>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="confirmDelete">
                                                <label class="form-check-label" for="flexCheckDefault">
                                                    Confirmo que desejo excluir este boleto.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <form action="#" method="get">
                                        <div class="hiddens">
                                            @csrf
                                            <input type="hidden" name="idBoleto" value="">
                                        </div>
                                        <button href="#" class="btn w-100 btn-danger excluirBoletoButton"
                                            disabled>
                                            <i class="bi bi-trash3 "></i> Excluir boleto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @else
    @endif
@endif
@include('footer')
