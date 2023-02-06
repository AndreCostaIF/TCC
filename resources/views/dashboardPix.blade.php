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
<div class="modal fade " id="modalCobranca"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " >
        <div class="modal-content shadow  border border-2 border-danger">
            <div class="modal-header">
                <h1 class="modal-title fs-5 border-bottom border-danger" id="exampleModalLabel">
                    <i class="bi bi-x-diamond-fill text-danger"></i> Gerar cobrança pix
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCobranca" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">ID do boleto</label>
                        <input type="text" class="form-control" id="idboleto" name="idboleto"
                            aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text">Insira o id do boleto para buscar os dados do cliente de forma automatica.</div>
                    </div>
                    <div class="mb-3 text-center loadCobranca" style="display: none">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                    </div>
                    <!---->
                    <div class="erro" style="display: none">
                        <div class="mb-2 text-center fs-5 border-bottom border-danger">
                            <span class="h6"><i class="bi bi-exclamation-diamond text-danger"></i> Nenhum boleto encontrado!<br> Verifique o <span class="text-danger">id do boleto</span> e tente novamente.</span>
                        </div>
                    </div>
                    <div class="formDataPix" style="display: none">
                        <div class="mb-2 text-center fs-5 border-bottom border-danger">
                            <span class="h5"><i class="bi bi-person text-danger"></i> Dados do cliente</span>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="nomeDevedor" name="nomeDevedor" value="" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label hibrido">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" value="Cliente cpf" disabled>
                        </div>

                        <div class="mb-2 text-center fs-5 border-bottom border-danger">
                            <span class="h5"><i class="bi bi-x-diamond-fill text-danger"></i> Dados para cobrança</span>
                        </div>

                        <div class="mb-3 d-flex justify-content-between">
                            <div class="col-5">

                                <label for="exampleInputPassword1" class="form-label">Vencimento</label>
                                <input type="text" class="form-control" id="vencimento" name="vencimento" value="Cliente vencimento" disabled>
                            </div>
                            <div class="col-5">
                                <label for="exampleInputPassword1" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="valor" name="valor" value="Cliente valor" disabled>
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-between">
                            <div class="col-5">

                                <label for="exampleInputPassword1" class="form-label">Lançamento</label>
                                <input type="text" class="form-control" id="lancamento" name="lancamento" value="Cliente lancamento" disabled>
                            </div>
                            <div class="col-5">
                                <label for="exampleInputPassword1" class="form-label">Mês Referente</label>
                                <select class="form-select" aria-label="Default select example" id="mes_ref" name="mes_ref" disabled>
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
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Informações adicionais</label>
                            <textarea type="text" class="form-control" id="infoAdicionais" name="infoAdicionais" disabled>
                                Cliente infoAdicionais
                            </textarea>
                        </div>

                    </div>
                </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-outline-danger" style="display: none" id="gerarCobranca" data-bs-target="#modalCobranca2" data-bs-toggle="modal">
                    Gerar cobrança
                </button>

            </div>
        </div>
    </div>
</div>
<!-- Modal GERAR COBRANCA 2-->
<div class="modal fade" id="modalCobranca2" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow  border border-2 border-danger">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">
            <i class="bi bi-x-diamond-fill text-danger"></i> Pague com <span class="text-danger"> Pix</span>
        </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="pixbody text-center">
                <div class="fs-4 ">
                    <div class="nome ">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Carregando...</span>
                          </div>
                    </div>
                </div>
                <div class="mb-3">
                    <a  class="boxQrCode">
                         <img src="" class="qrcode" alt="Carregando..." >
                    </a>
                    <div>
                        <small class="opacity-50">Clique no QRCode para baixar</small>
                    </div>
                </div>
                <div class="h2 fs-4 beneficiario  text-uppercase">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Carregando...</span>
                      </div>
                </div>
            </div>
            <div class="pixfooter text-center p-2">
                <div class="d-flex flex-column mb-3">
                    <span class="h4 fs-4"><i class="bi bi-key text-danger"></i> Chave:</span>
                    <span class="fs-5 chave">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Carregando...</span>
                          </div>
                    </span>
                </div>

                <div class="d-flex flex-column mb-3">
                    <span class="h4 fs-4"><i class="bi bi-currency-dollar text-success"></i> Valor:</span>
                    <span class="fs-5 valor">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Carregando...</span>
                          </div>
                    </span>
                </div>
            </div>
            <div class="text-center pixbuttons"  style="display: none">
                <input type="hidden" name="pixCopiaCola" id="pixCopiaCola" value="">
                <button type="button" class="btn BTNpixCopiaCola btn-danger" id="toastbtn">
                    <i class="bi bi-clipboard2"></i> Pix copia e Cola
                </button>
                <a  type="button" target="_blank" class="btn btn-outline-danger imprimirBoletoPix" id="copiaECola">
                    <i class="bi bi-printer"></i> Imprimir boleto com pix
                </a>

                <div id="notificacao" class="toast position-fixed border border-danger shadow top-50 start-50 translate-middle p-3" >
                    <div class="toast-header shadow bg-danger">
                      <strong class="me-auto text-light fw-bold"><i class="bi bi-bell"></i> Notificação</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body ">
                      <span class="text-success h6"><i class="bi bi-clipboard2-check"></i> Texto copiado com sucesso!</span>
                    </div>
                  </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Fechar</button>
          <button class="btn btn-primary" data-bs-target="#modalCobranca" data-bs-toggle="modal">Nova cobrança</button>
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
