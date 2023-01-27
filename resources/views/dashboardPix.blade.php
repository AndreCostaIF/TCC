@include('masterhead')

<div class="row mb-5">
    <div class="text-center">
        <img width="300" src="{{ asset('assets/pix.svg') }}" alt="">
    </div>
</div>

<div class="row mb-5">
    <div class="col-xl-4 col-md-6 mb-4 boxTools">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalCobranca">
            <div class="card boxPix border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold opacity-50 text-capitalize mb-1">
                                Gerar cobrança pix
                            </div>
                            <div class="h6  font-weight-bold  text-uppercase mb-1">
                                cobrança pix
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-diamond-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-6 mb-4 boxTools">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalbusca">
            <div class="card boxPix border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold opacity-50 text-capitalize mb-1">
                                Consultar cobranças pix
                            </div>
                            <div class="h6  font-weight-bold  text-uppercase mb-1">
                                Buscar pix
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-6 mb-4 boxTools">
        <a href="#">
            <div class="card boxPix border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold opacity-50 text-capitalize mb-1">
                                Consultar pix recebidos
                            </div>
                            <div class="h6  font-weight-bold  text-uppercase mb-1">
                                pix recebidos
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>


<div class="row">

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col" class="text-danger">ID</th>
                    <th scope="col" class="text-danger">Cliente</th>
                    <th scope="col" class="text-danger">Data de pagamento</th>
                    <th scope="col" class="text-danger">Valor pago</th>
                    <th scope="col" class="text-danger">Status </th>
                    <th scope="col" class="text-danger">Boleto ID</th>
                    <th scope="col" class="text-danger">Boleto</th>
                </tr>
            </thead>
            <div class="fw-bold text-center">
                <span>Resultado da pesquisa:</span>
            </div>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Isabelle tem uma coisa no meio Oliveira</td>
                    <td>27/01/2023</td>
                    <td>R$50,00</td>
                    <td class="text-success fw-bold"><i class="bi bi-check2-circle "></i> Pagamento efetuado</td>
                    <td>123456</td>
                    <td>
                        <a class="text-warning fw-bold" href="#">
                            <i class="bi bi-clock"></i>
                            Aguardando baixa
                        </a>
                    </td>
                </tr>


            </tbody>
        </table>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<div class="mb-5 row">
    <div class="text-center">
        <span class="text-danger h4 fw-bold"><i class="bi bi-x-diamond-fill"></i> Pix</span>
        <span class="fw-bold h4">recebidos</span>
    </div>
    <hr>

    <div class="mt-3">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col" class="text-danger">ID</th>
                    <th scope="col" class="text-danger">Cliente</th>
                    <th scope="col" class="text-danger">Data de pagamento</th>
                    <th scope="col" class="text-danger">Valor pago</th>
                    <th scope="col" class="text-danger">Status </th>
                    <th scope="col" class="text-danger">Boleto ID</th>
                    <th scope="col" class="text-danger">Boleto</th>
                </tr>
            </thead>
            <div class=" d-flex justify-content-center">
                PAGINATE AQUI
            </div>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Isabelle tem uma coisa no meio Oliveira</td>
                    <td>27/01/2023</td>
                    <td>R$50,00</td>
                    <td class="text-success fw-bold"><i class="bi bi-check2-circle "></i> Pagamento efetuado</td>
                    <td>123456</td>
                    <td>
                        <a class="text-warning fw-bold" href="#">
                            <i class="bi bi-clock"></i>
                            Aguardando baixa
                        </a>
                    </td>
                </tr>

                <tr>
                    <th scope="row">2</th>
                    <td>Maria Eduarda Benicio Bernardino</td>
                    <td>27/01/2023</td>
                    <td>R$50,00</td>
                    <td class="text-success fw-bold"><i class="bi bi-check2-circle"></i> Pagamento efetuado</td>
                    <td>123456</td>
                    <td>
                        <span class="text-success fw-bold">
                            <i class="bi bi-check2-circle"></i>
                            Boleto baixado
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal GERAR COBRANCA-->
<div class="modal fade " id="modalCobranca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content shadow  border border-2 border-danger">
            <div class="modal-header">
                <h1 class="modal-title fs-5 border-bottom border-danger" id="exampleModalLabel">
                    <i class="bi bi-x-diamond-fill text-danger"></i> Gerar cobrança pix
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1"
                            aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-outline-danger">Gerar cobrança</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Busca-->
<div class="modal fade " id="modalbusca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content shadow  border border-2 border-danger">
            <div class="modal-header">
                <h1 class="modal-title fs-5 border-bottom border-danger" id="exampleModalLabel">
                    <i class="bi bi-x-diamond-fill text-danger"></i> Consultar cobrança pix
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('buscarCliente') }}" method="GET" class="    ">
                    @csrf
                    <div class="form-floating mb-3">
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

                    <div class="form-floating ">
                        <input type="text" class="form-control" name="campoBusca" id="floatingInput"
                            placeholder="name@example.com" required>
                        <label for="floatingInput" id="campoBusca"></label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-outline-danger">Buscar cobrança</button>
            </div>
        </div>
    </div>
</div>
@include('footer')
