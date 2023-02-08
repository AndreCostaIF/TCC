@include('masterhead')

<div class="border-bottom border-danger border-3 mb-5">
    <span class="h2"><i class="bi bi-receipt text-danger"></i> Remessa {{$arquivo}}</span>
</div>

<div class="row mb-5">
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item border bg-light">
          <h2 class="accordion-header" id="flush-headingOne">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                Registro Movimento – Remessa | Ocorrência
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
                        <th scope="row">01</th>
                        <td>Entrada do boleto</td>
                      </tr>
                      <tr>
                        <th scope="row">02</th>
                        <td>Baixa do boleto</td>
                      </tr>
                      <tr>
                        <th scope="row">04</th>
                        <td>Concessão de abatimento</td>
                      </tr>
                      <tr>
                        <th scope="row">05</th>
                        <td>Cancelamento do abatimento</td>
                      </tr>
                      <tr>
                        <th scope="row">06</th>
                        <td>Alteração do vencimento</td>
                      </tr>
                      <tr>
                        <th scope="row">07</th>
                        <td>Alteração do número controle beneficiário</td>
                      </tr>
                      <tr>
                        <th scope="row">08</th>
                        <td>Alteração do Seu Número</td>
                      </tr>
                      <tr>
                        <th scope="row">09</th>
                        <td>Protestar</td>
                      </tr>
                      <tr>
                        <th scope="row">15</th>
                        <td>Transferência da carteira Simples para Cessão*</td>
                      </tr>
                      <tr>
                        <th scope="row">16</th>
                        <td> Baixa de Cessão por Descaracterização**</td>
                      </tr>
                      <tr>
                        <th scope="row">17</th>
                        <td>Baixa de Cessão por Pagamento**</td>
                      </tr>
                      <tr>
                        <th scope="row">18</th>
                        <td> Sustar o protesto (Após início do ciclo de protesto)</td>
                      </tr>
                      <tr>
                        <th scope="row">47</th>
                        <td> Alteração do valor nominal do boleto</td>
                      </tr>
                      <tr>
                        <th scope="row">48</th>
                        <td>Alteração do valor mínimo/percentual</td>
                      </tr>
                      <tr>
                        <th scope="row">49</th>
                        <td>Alteração do valor máximo/percentual
                        </td>
                      </tr>
                    </tbody>
                  </table>
            </div>
          </div>
        </div>
      </div>
</div>

<div class="row d-flex justify-content-center mb-5">
<div class="col-5">
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
           <span> <i class="bi bi-currency-dollar text-success"></i> Valor total no lote</span>
          <span class="badge bg-primary rounded-pill">R${{formatar($valorDoLote)}}</span>
        </li>

      </ul>
</div>
<div class="col-5">
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Total de registros
          <span class="badge bg-primary rounded-pill">{{$totalNoLote}}</span>
        </li>

      </ul>
</div>
</div>

<div class="row">
    <div class=" d-flex justify-content-center">
        {{ $cliente->links() }}
    </div>
    <table class="table table-hover table-striped ">
        <thead class="border-bottom border-2 border-danger ">
          <tr>
            <th scope="col">Controle Participante</th>
            <th scope="col">Nosso Número</th>
            <th scope="col">Ocorrência</th>
            <th scope="col">Valor Titulo</th>
            <th scope="col">Nº Doc.</th>
            <th scope="col">vencimento</th>
            <th scope="col">Especie Boleto</th>
            <th scope="col">Identificção Sacado</th>
            <th scope="col">Nome</th>
            <th scope="col">Endereco</th>
            <th scope="col">Cep</th>
            <th scope="col">Cidade</th>
            <th scope="col">UF</th>
            <th scope="col">Nº sequencial</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($cliente as $item)
            <tr>
                <th scope="row">{{$item['controleParticipante']}}</th>
                <td>{{$item['nossoNumero']}}</td>
                <td>{{$item['ocorrencia']}}</td>
                <td>R${{formatar($item['valorTitulo'])}}</td>
                <td>{{$item['numDoc']}}</td>
                <td>{{formatDateAndTime($item['vencimento'])}}</td>
                @if ($item['especieBoleto'] == '01')
                    <td>Duplicata</td>
                @else
                    <td>{{$item['especieBoleto']}}</td>
                @endif
                <td>{{$item['identificacaoSacado']}}</td>
                <td>{{$item['nome']}}</td>
                <td>{{$item['endereco']}}</td>
                <td>{{$item['cep']}}</td>
                <td>{{$item['cidade']}}</td>
                <td>{{$item['uf']}}</td>
                <td>{{$item['sequencial']}}</td>
              </tr>
            @endforeach


        </tbody>
      </table>

      <div class=" d-flex justify-content-center">
        {{ $cliente->links() }}
    </div>
</div>

@include('footer')
