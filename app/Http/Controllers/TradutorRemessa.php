<?php

namespace App\Http\Controllers;

include('openboleto/autoloader.php');

use App\ConstantesPix;
use App\Models\Cidades;
use App\Models\historicoRemessa;
use App\Models\PixModel;
use Illuminate\Http\Request;
use OpenBoleto\Banco\Santander;

class TradutorRemessa extends Controller
{

    public function erroAutenticado()
    {
        if (session()->has('nome')) {

            return false;
        } else {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        } else {
            if (session()->get('grupo_users_id') == 1) {
                if ($request->get('remessaSantander')) {
                    $remessa['historico'] = historicoRemessa::pegarTodos();
                    $remessa['remessaSantander'] = $request->get('remessaSantander');
                    $remessa['dataGerado'] = $request->get('dataGerado');
                    $remessa['horaGerado'] = $request->get('horaGerado');
                    return view('remessa', $remessa);
                } else {
                    $remessa['historico'] = historicoRemessa::pegarTodos();
                    $remessa['title'] = "Remessa";
                    return view('remessa', $remessa);
                }
            } else {
                return redirect()->route('logout');
            }
        }
    }
    function traduzir(Request $request)
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }

        date_default_timezone_set("America/Sao_Paulo");
        $remessaBradesco2 = file($request->file('arq'));

        if (substr($remessaBradesco2[0], 2, 7) == 'REMESSA' && substr($remessaBradesco2[0], 79, 8) == 'BRADESCO') {
            $name = $request->file('arq')->store('public/remessa');
            $name = str_replace('public', 'storage', $name);

            // HEADER
            //pega a primeira linha do arquivo remerssa bradesco
            $remessaBradesco = file($name);
            //var_dump($arq);
            //die();

            $x = $remessaBradesco[0];

            // seta os campos necess??rio de acordo com as posi????es e o padr??o santander
            $inicioHeaderRemessa = substr($x, 0, 26);
            $codTransmissao = "45430981859601300398";
            $beneficiario = substr($x, 46, 30);
            $codigoBanco = "033";
            $banco = "SANTANDER      ";
            $dataGravacao = substr($x, 94, 6);
            $zeros = "0000000000000000";
            $msg1 = str_pad("", 47, " ");
            $msg2 = str_pad("", 47, " ");
            $msg3 = str_pad("", 47, " ");
            $msg4 = str_pad("", 47, " ");
            $msg5 = str_pad("", 47, " ");
            $branco = str_pad("", 40, " ");
            $vers??oRemerssa = "000";
            $numSequencialHeader = substr($x, 394, 6);


            //junta as informa????os setadas para construir o header
            $header = $inicioHeaderRemessa . $codTransmissao . $beneficiario . $codigoBanco . $banco . $dataGravacao . $zeros . $msg1 . $msg2 . $msg3 . $msg4 . $msg5 . $branco . $vers??oRemerssa . $numSequencialHeader;
            //print "<b>".$header."</b></br>";

            //cria o arquivo e escreve o header

            //NOME DO ARQUIVO

            $nameArq =  date('dmy') . date("hi") . '.REM';

            $remessaSantader = fopen($nameArq, 'w');
            fwrite($remessaSantader, $header . "\n");
            $valorTotalTitulos = 0;
            $santoandre = new Santander();
            //Registro movimento
            for ($i = 1; $i < (sizeof($remessaBradesco) - 1); $i++) {
                $x = $remessaBradesco[$i];
                //dd(substr($x, 75, 6));
                $santoandre->setSequencial(substr($x, 73, 8));


                $codigoRegistro = substr($x, 0, 1); //001 - 001 (001)
                $tipoBeneficiario = "02"; // 002 - 003 (002
                $cnpjOuCpf = "07692425000158"; // 004 - 017 (014)
                $codTransmissao = "45430981859601300398"; // 018 - 037 (020)
                $controleParticipante = substr($x, 37, 25); // 038 - 062 (025)
                $nossoNumero = completarPosicoes(substr($x, 75, 6) . $santoandre->gerarDigitoVerificadorNossoNumero(), 8, '0'); // 063 - 070 (008)
                $dataSegundoDesc = "000000"; // 071 - 076 (006)
                $branco1espaco = str_pad("", 1, " "); // 077 - 077 (001)
                $infoMulta = "4"; // 078 - 078 (001)
                $percentualMulta = "0200"; // 079 - 082 (004)
                $unidadeValorMoedaCorrente = "00"; // 083 - 084 (002)
                $valorDotituloOutraUnidade = "0000000000000"; // 085 - 097 (013)
                $branco4espaco = str_pad("", 4, " "); // 098 - 101 (004)
                $dataCobrancaMulta = "000000"; // 102 - 107 (006)
                $codigoCarteira = "5"; // 108 - 108 (001)
                $codigoOcorrencia = substr($x, 108, 2); // 109 - 110 (002)
                $seuNumero = substr($x, 110, 10); // 111 - 120 (010)
                $dataVencimento = substr($x, 120, 6); // 121 - 126 (006)
                $valorTitulo = substr($x, 126, 13); // 127 - 139 (013)
                $numBancoCobrador = "033"; // 140 - 142 (003)
                $codigoAgencia = substr($x, 142, 5); // 143 - 147 (005)
                $especieDocumento = substr($x, 147, 2); // 148 - 149 (002)
                $tipoDeAceite = substr($x, 149, 1); // 150 - 150 (001)
                $dataEmissaoTitulo = substr($x, 150, 6); // 151 - 156 (006)
                $primeiraInstrucao = substr($x, 156, 2); // 157 - 158 (002)
                $segundaInstrucao = substr($x, 158, 2); // 159 - 160 (002)
                $valorMoraAtraso = substr($x, 160, 13); // 161 - 173 (013)
                $dataLimiteDesconto = substr($x, 173, 6); // 174 - 179 (006)
                $valorDesconto = substr($x, 179, 13); // 180 - 192 (013)
                $IOF = substr($x, 192, 13); // 193 - 205 (013)
                $valorBatimento = substr($x, 205, 13); // 206 - 218 (013) obs: a documenta????o diz que s??o 11
                $codigoIdentificacaoSacado = substr($x, 218, 2); // 219 - 220 (002)
                $identificacaoSacado =  substr($x, 220, 14); // 221 - 234 (014)
                $nomeSacado =  substr($x, 234, 40); // 225 - 274 (040)
                $enderecoCompleto =  substr($x, 274, 40);
                $enderecoPartes = explode(",", $enderecoCompleto);
                $enderecoSacado = str_pad("", 40, " ");

                if (isset($enderecoPartes[0])) {

                    $enderecoSacado = $enderecoPartes[0] . ",";

                    if (isset($enderecoPartes[1])) {
                        $enderecoSacado = $enderecoSacado . $enderecoPartes[1]; // 275 - 314 (040)
                    }
                } else {

                    $enderecoSacado = $enderecoPartes;
                    $completar = 40 - strlen($enderecoSacado);
                    $enderecoSacado = $enderecoSacado . str_pad("", $completar, " "); // 275 - 314 (040)

                }

                $bairro = str_pad("", 12, " "); // 315 - 326 (014)
                if (strlen($enderecoSacado) < 40) {

                    $completar = 40 - strlen($enderecoSacado);
                    $enderecoSacado = $enderecoSacado . str_pad("", $completar, " "); // 275 - 314 (040)

                } else if (strlen($enderecoSacado) > 40) {

                    $enderecoSacado = substr($enderecoSacado, 0, 40); // 315 - 326 (014)
                }
                if (isset($enderecoPartes[2])) {
                    if (strlen($enderecoPartes[2]) < 12) {
                        $completar = 12 - strlen($enderecoPartes[2]);
                        $bairro = $enderecoPartes[2] . str_pad("", $completar, " "); // 315 - 326 (014)
                    } else if (strlen($enderecoPartes[2]) > 12) {
                        $bairro = substr($enderecoPartes[2], 0, 12); // 315 - 326 (014)
                    }
                }

                $cep = substr($x, 326, 5); // 327 - 331 (005)
                $complementoCep = substr($x, 331, 3); // 332 - 334 (3)
                $cepCompleto = $cep . $complementoCep; // 327 - 334 (008)
                $municipio = ""; // 335 - 349 (015)
                $ufSacado = ""; // 350 - 351 (002)
                if ($cepCompleto != '59215000') {


                    // $ch = curl_init();
                    // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    // curl_setopt($ch, CURLOPT_URL, "viacep.com.br/ws/$cepCompleto/json/");

                    // // Receive server response ...
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //Json para Array
                    // $resultado = json_decode(curl_exec($ch), true);

                    $resultado =  Cidades::buscarCidade($cepCompleto);
                    $municipio = strtoupper($resultado->cidade); // 335 - 349 (015)
                    $ufSacado =  strtoupper($resultado->sigla); // 350 - 351 (002)

                } else {
                    $municipio = 'NOVA CRUZ'; // 335 - 349 (015)
                    $ufSacado =  "RN"; // 350 - 351 (002)
                }


                if (strlen($municipio) < 15) {
                    $completar = 15 - strlen($municipio);
                    $municipio = $municipio . str_pad("", $completar, " ");
                } else if (strlen($municipio) > 15) {
                    $municipio = substr($municipio, 0, 15);
                }

                $nomeSacador = str_pad("", 30, " "); // 352 - 381 (030)
                $identificadorComplemento = "I"; // 383 - 383 (001)
                $complemento = "42"; // 384 - 385 (002)
                $branco6espaco = str_pad("", 6, " "); // 386 - 391 (006)
                $numDiasProtesto = "00"; // 392 - 393 (002)
                $numSequencialRegistroMovimento = substr($x, 394, 6); // 395 - 400 (006)

                $registroMovimento = $codigoRegistro . $tipoBeneficiario . $cnpjOuCpf . $codTransmissao . $controleParticipante .
                    $nossoNumero . $dataSegundoDesc . $branco1espaco . $infoMulta . $percentualMulta .
                    $unidadeValorMoedaCorrente . $valorDotituloOutraUnidade . $branco4espaco . $dataCobrancaMulta . $codigoCarteira . $codigoOcorrencia .
                    $seuNumero . $dataVencimento . $valorTitulo . $numBancoCobrador . $codigoAgencia . $especieDocumento . $tipoDeAceite .
                    $dataEmissaoTitulo . $primeiraInstrucao . $segundaInstrucao . $valorMoraAtraso . $dataLimiteDesconto . $valorDesconto .
                    $IOF . $valorBatimento . $codigoIdentificacaoSacado . $identificacaoSacado . $nomeSacado . $enderecoSacado . $bairro .
                    $cepCompleto . $municipio . $ufSacado . $nomeSacador . $branco1espaco . $identificadorComplemento . $complemento .
                    $branco6espaco . $numDiasProtesto . $branco1espaco . $numSequencialRegistroMovimento;

                $valorTotalTitulos += intval($valorTitulo);


                //print "<b>".$codigoRegistro."</b></br>";
                //print ("<b>".$valorTotalTitulos."</b></br>");
                // print "<b>".strlen($registroMovimento)."</b></br>";

                //escreve o registro de movimento no arquivo remerssa santander
                fwrite($remessaSantader, $registroMovimento . "\n");
            }

            //-------------------PIX----------------------
            $arquivoComRegistroSantander = file($nameArq);
            $tam = sizeof($arquivoComRegistroSantander) + 1;

            for($i = 1; $i <= (sizeof($arquivoComRegistroSantander) - 1); $i++){

                $x = $arquivoComRegistroSantander[$i];

                $codigoRegistro = '8';
                $identificacaoTipoPagamento = '03';
                $pagamentosPossiveis = '01';
                $tipoDeValor = '2';
                $valorMaximo = substr($x, 126, 13);
                $percentualMaximo = '99999';
                $valorMinimo = substr($x, 126, 13);
                $percenualMinimo = '00001';
                $tipoDeChave = ConstantesPix::TYPE_KEY;
                $chaveDict = completarPosicoes(ConstantesPIX::PIX_KEY, 77, '0');
                $txid = 'YK'.'T'. '076924250' . '00000' . substr($x, 63, 8) . date('dmy');
                $txidCompleto = completarPosicoes($txid, 35, '0');

                $brancos239 = str_pad("", 239, " ");
                $numSequencial =  completarPosicoes($tam, 6, '0');

                $registroQrCode = $codigoRegistro . $identificacaoTipoPagamento . $pagamentosPossiveis . $tipoDeValor .
                $valorMaximo . $percentualMaximo . $valorMinimo . $percenualMinimo . $tipoDeChave . $chaveDict .
                $txidCompleto . $brancos239 . $numSequencial;

                //escreve o registro de movimento no arquivo remerssa santander
                fwrite($remessaSantader, $registroQrCode . "\n");

                $pixModel = new PixModel();
                $pixModel->boleto_id = intval(substr($x, 37, 25));
                $pixModel->txid = $txid;
                $pixModel->save();

                $tam++;
            }

            $arquivoComRegistroSantander = file($nameArq);
            $x = $remessaBradesco[sizeof($remessaBradesco) - 1];
            $codigoRegistro = substr($x, 0, 1);
            $quantidadeLinhas = "" . sizeof($arquivoComRegistroSantander)+1;
            if (strlen($quantidadeLinhas) < 6) {
                $completar = 6 - strlen($quantidadeLinhas);
                $quantidadeLinhas = str_pad("", $completar, "0") . $quantidadeLinhas;
            }
            $valorTotal = "" . $valorTotalTitulos;
            if (strlen($valorTotal) < 13) {
                $completar = 13 - strlen($valorTotal);
                $valorTotal = str_pad("", $completar, "0") . $valorTotal;
            }

            $zeros = str_pad("", 374, "0");
            $numSequencialTrailer = completarPosicoes($tam++, 6, '0'); // 395 - 400 (006)



            $trailer = $codigoRegistro . $quantidadeLinhas . $valorTotal . $zeros . $numSequencialTrailer;
            fwrite($remessaSantader, $trailer);
            //dd($remessaSantader);
            $remessa['remessaSantander'] = asset($nameArq);
            $remessa['dataGerado'] = date('d/m/y');
            $remessa['horaGerado'] = date('H:i:sa');
            $historicoRemessa = new historicoRemessa();

            $historicoRemessa->dataTraducao = date('y-m-d H:i:s');
            $historicoRemessa->autor = session()->get('nome');
            $historicoRemessa->nomeRemessa =  $nameArq;
            $historicoRemessa->save();

            $remessa['historico'] = $historicoRemessa;


            $remessa['title'] = "Tradutor remessa";
            return redirect()->route('remessa', $remessa);
        }else{
            return redirect()->back()->with('msg', 'Arquivo incompat??vel!');
        }
    }

    public function lerRemessa($nome){

        $remessa= file($nome);
        //dd(end($remessa));
        $cont = 0;
        $data['cliente'] = [];
        //$santoandre = new Santander();
        for ($i = 1; $i < (sizeof($remessa) - 1); $i++) {
            $x = $remessa[$i];
            if(substr($x, 0, 1) != '8'){

                $data['cliente'][$i]['controleParticipante'] = "". intval(substr($x, 37, 25)); // 038 - 062 (025)
                $data['cliente'][$i]['nossoNumero'] = substr($x, 62, 8);
                $data['cliente'][$i]['ocorrencia'] = substr($x, 108, 2);
                $data['cliente'][$i]['valorTitulo']  = "". intval(substr($x, 126, 13));
                $data['cliente'][$i]['numDoc']  = "". intval(substr($x, 110, 10));
                $data['cliente'][$i]['vencimento'] = substr($x, 120, 6); // 121 - 126 (006)
                $data['cliente'][$i]['vencimento'] = substr($data['cliente'][$i]['vencimento'], 0, 2) . '-' . substr($data['cliente'][$i]['vencimento'], 2, 2) . '-' . "20". substr($data['cliente'][$i]['vencimento'], 4, 2);
                $data['cliente'][$i]['especieBoleto'] = substr($x, 147, 2);
                $data['cliente'][$i]['identificacaoSacado'] = substr($x, 218, 2); // 219 - 220 (002)
                $data['cliente'][$i]['nome'] = rtrim(substr($x, 234, 40)); // 225 - 274 (040)
                $data['cliente'][$i]['endereco'] =  rtrim(substr($x, 274, 40));

                $cep = substr($x, 326, 5); // 327 - 331 (005)
                $complementoCep = substr($x, 331, 3); // 332 - 334 (3)
                $cepCompleto = $cep . $complementoCep; // 327 - 334 (008)

                if ($cepCompleto != '59215000') {

                    $resultado =  Cidades::buscarCidade($cepCompleto);
                    $municipio = strtoupper($resultado->cidade); // 335 - 349 (015)
                    $ufSacado =  strtoupper($resultado->sigla); // 350 - 351 (002)

                } else {
                    $municipio = 'NOVA CRUZ'; // 335 - 349 (015)
                    $ufSacado =  "RN"; // 350 - 351 (002)
                }
                $data['cliente'][$i]['cep'] = $cepCompleto;
                $data['cliente'][$i]['cidade'] = $municipio;
                $data['cliente'][$i]['uf'] = $ufSacado;
                $data['cliente'][$i]['sequencial'] = substr($x, 394, 6);

                $cont++;
            }
        }
        $data['valorDoLote'] = intval(substr(end($remessa), 7, 13));
        //dd($data);
        $data['totalNoLote'] =  $cont;
        $data['arquivo'] = $nome;
        $data['cliente'] = paginate($data['cliente'], 50);

        $data['cliente']->withPath("/remessa/$nome");
        //dd($data);
        $data['title'] = "Arquivo remessa $nome";

        return view('verRemessa', $data);
    }
}
