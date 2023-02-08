@include('masterhead')

<div class="border-bottom border-danger border-3 mb-5">
    <span class="h2"><i class="bi bi-receipt text-danger"></i> Retorno {{$arquivo}}</span>
</div>

<div class="row mb-5">
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item border bg-light">
          <h2 class="accordion-header" id="flush-headingOne">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                Legenda Ocorrências - Retorno Bradesco
            </button>
          </h2>
          <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Ocorrência</th>
                        <th scope="col">Legenda</th>

                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>02</td><td>Entrada Confirmada</td>
                        </tr>
                        <tr>
                            <td>06</td><td>Liquidação Normal</td>
                        </tr>
                        <tr>
                            <td>09</td><td>Baixado Automat. via Arquivo</td>
                        </tr>
                        <tr>
                            <td>10</td><td>Baixado conforme instruções da Agência</td>
                        </tr>
                        <tr>
                            <td>14</td><td>Vencimento Alterado</td>
                        </tr>
                        <tr>
                            <td>17</td><td>Liquidação após baixa ou Título não registrado (sem motivo)</td>
                        </tr>
                        <tr>
                            <td>24</td><td>Entrada rejeitada por CEP Irregular</td>
                        </tr>
                        <tr>
                            <td>27</td><td>Baixa Rejeitada</td>
                        </tr>
                        <tr>
                            <td>30</td><td>Alteração de Outros Dados Rejeitados</td>
                        </tr>
                        <tr>
                            <td>32</td><td>Instrução Rejeitada</td>
                        </tr>
                        <tr>
                            <td>33</td><td>Confirmação Pedido Alteração Outros Dados</td>
                        </tr>
                    </tbody>
                  </table>
            </div>
          </div>
        </div>
      </div>
</div>

<div class="row mb-5">

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Quant. Cobranças</th>
                <th>Valor Cobranças</th>
                <th>Quant. Ocorrência 02</th>
                <th>Valor Ocorrência 02</th>


            </tr>
        </thead>
        <tbody>
            <tr>
                <td>24</td>
                <td>R$ 1.404,49</td>
                <td>3676</td>
                <td>R$ 212.660,59</td>


            </tr>
        </tbody>

        <thead>
            <tr>
                <th>Quant. Ocorrência 06</th>
                <th>Valor Ocorrência 06</th>
                <th>Quant. Ocorrência 09 e 10</th>
                <th>Valor Ocorrência 09 e 10</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>R$ 6.561,17</td>
                <td>320</td>
                <td>111</td>
                <td>R$ 17.265,00</td>
            </tr>
        </tbody>
    </table>
</div>

@include('footer')
