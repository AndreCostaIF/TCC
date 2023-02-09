<?php

namespace App\Http\Controllers;

use App\Models\historicoRetorno;
use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradutorRetorno extends Controller
{

    public function erroAutenticado()
    {
        if (session()->has('nome')) {

            return false;
        } else {
            return true;
        }
    }
    private function completarPosicoes($campo, $posicoes, $complemento)
    {
        //verifica se o valor total de É MAIOR QUE
        if (strlen($campo) < $posicoes) {

            $completar = $posicoes - strlen($campo);

            $campo = str_pad("", $completar, $complemento) . $campo;
        } else if (strlen($campo) > $posicoes) {
            $campo = substr($campo, 0, $posicoes);
        }

        return $campo;
    }

    private function completarPosicoes2($campo, $posicoes, $complemento)
    {
        //verifica se o valor total de É MAIOR QUE
        if (strlen($campo) < $posicoes) {

            $completar = $posicoes - strlen($campo);

            $campo = $campo . str_pad("", $completar, $complemento);
        } else if (strlen($campo) > $posicoes) {
            $campo = substr($campo, 0, $posicoes);
        }

        return $campo;
    }

    public function index(Request $request)
    {

        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }
        //dd($request->get('retornoBradesco'));
        if (session()->get('grupo_users_id') == 1) {
            if ($request->get('retornoBradesco')) {
                $retorno['historico'] = historicoRetorno::pegarTodos();
                $retorno['retornoBradesco'] = $request->get('retornoBradesco');
                $retorno['dataGerado'] = $request->get('dataGerado');
                $retorno['horaGerado'] = $request->get('horaGerado');
                $retorno['title'] = "Retorno";
                return view('retorno', $retorno);
            } else {
                $retorno['title'] = "Retorno";
                $retorno['historico'] = historicoRetorno::pegarTodos();
                return view('retorno', $retorno);
            }
        } else {
            return redirect()->route('logout');
        }
    }


    public function traduzirRetorno(Request $request)
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }
        date_default_timezone_set("America/Sao_Paulo");

        $retornoSantander2 = file($request->file('arq'));

        if (substr($retornoSantander2[0], 2, 7) == 'RETORNO' && substr($retornoSantander2[0], 79, 9) == 'SANTANDER') {

            //SALVA O ARQUIVO ORIGINAL DO RETORNO E GUARDA O CAMINHO

            $name = $request->file('arq')->storeAs("public/retorno", $request->file('arq')->getClientOriginalName());
            //TROCA O PUBLIC POR STORAGE NA URL


            $name = str_replace('public', 'storage', $name);



            //INICIO HEADER
            $retornoSantander = file($name);
            $x = $retornoSantander[0];

            $inicioRetorno = substr($x, 0, 26); //001 - 026 (026)
            $codigoEmpresa = "00000000000007891680"; //027 - 046 (020)
            $nomeEmpresa = substr($x, 46, 30); // 047 - 076 (030)
            $codigoBanco = "237"; // 077 - 079 (003)
            $nomeBanco = $this->completarPosicoes2('BRADESCO', 15, ' '); // 080 - 094 (015)
            $dataGravacao = substr($x, 94, 6); //095 - 100 (006)
            $zeros = str_pad("", 8, "0"); // 101 - 108 (008)
            $numAviso = str_pad("", 5, "0"); // 109 - 113 (005)
            $branco266 = str_pad("", 266, " "); //114 - 379 (266)
            $dataCredito = date('dmy'); //380 - 385 (006)
            $branco9 = str_pad("", 9, " "); //386 - 394 (009)
            $numSequencialHeader = substr($x, 394, 6); //395 - 400 (006)

            $headerRetorno = $inicioRetorno . $codigoEmpresa . $nomeEmpresa . $codigoBanco . $nomeBanco . $dataGravacao .
                $zeros . $numAviso . $branco266 . $dataCredito . $branco9 . $numSequencialHeader;

            //NOME DO ARQUIVO
            $nomeArq = 'CB' . substr($dataGravacao, 0, 5) . letraAleatoria() . '.RET';
            Storage::move('public/retorno/' . $request->file('arq')->getClientOriginalName(), "public/retorno/$nomeArq");

            $retornoBradesco = fopen($nomeArq, 'w');
            //ESCREVE NO ARQUIVO
            fwrite($retornoBradesco, $headerRetorno . "\n");

            //FIM HEADER

            //REGISTRO DE MOVIMENTO RETORNO
            $qtdRegistros02 = 0;
            $qtdRegistros06 = 0;
            $qtdRegistros09e10 = 0;
            $qtdRegistros12 = 0;
            $qtdRegistros13 = 0;
            $qtdRegistros14 = 0;
            $qtdRegistros19 = 0;

            $valorTotalRegistros02 = 0;
            $valorTotalRegistros06 = 0;
            $valorTotalRegistros09e10 = 0;
            $valorTotalRegistros12 = 0;
            $valorTotalRegistros13 = 0;
            $valorTotalRegistros14 = 0;
            $valorTotalRegistros19 = 0;

            $qtdRateio = 0;
            $valorTotalRateio = 0;


            for ($i = 1; $i < (sizeof($retornoSantander) - 1); $i++) {
                $x = $retornoSantander[$i];

                $inicioRetornoRegistro = substr($x, 0, 17); //001 - 017
                $zeros = str_pad("", 3, "0"); //018 - 020 (003)
                $idenficacaoCedenteBanco = substr($x, 17, 17); // 021 - 037 (017)
                $numControleParticipante = substr($x, 37, 25); // 038 - 062 (025)
                $zeros8espaços = str_pad("", 8, "0"); // 063 - 070 (008)
                $identificacaoTitulo = str_pad("", 4, "0") . substr($x, 62, 8); //071 - 082 (012)
                $usoBanco1 = str_pad("", 10, "0"); // 083 - 092 (010)
                $usoBanco2 = str_pad("", 12, "0"); // 093 - 104 (012)
                $rateioCredito = str_pad("", 1, "0"); // 105 - 105 (001)
                $zeros2 = str_pad("", 2, "0"); //106 - 107 (002)
                $codigoCarteira = substr($x, 107, 1); // 108 - 108 (001)
                $identificacaoOcorrencia = substr($x, 108, 2); // 109 - 110 (002)
                $dataOcorrencia = substr($x, 110, 6); // 111 - 116 (006)
                $numDocumento = substr($x, 116, 10); // 117 - 126 (010)
                $numDocumento = str_replace(' ', '0', $numDocumento);
                $identificacaoTituloBanco = str_pad("", 8, "0") . $identificacaoTitulo; // 127 - 146 (020)
                $dataVencimentoTitulo = substr($x, 146, 6); // 147 - 152 (006)
                $valorTitulo = substr($x, 152, 13); // 153 - 165 (013)
                $bancoCobrador = substr($x, 165, 3); // 166 - 168 (003)
                $codigoAgenciaCobradora = substr($x, 168, 5); // 169 - 173 (005)
                $especieTitulo = str_pad("", 2, " "); // 174 - 175 (002)
                $despesaCobranca = substr($x, 175, 13); // 176 - 188 (013)
                $outrasDespesas = substr($x, 188, 13); // 189 - 201 (013)
                $jurosAtraso = substr($x, 201, 13); // 202 - 214 (013)
                $IOF = substr($x, 214, 13); // 215 - 227 (013)
                $valorAbatimento = substr($x, 227, 13); // 228 - 240 (013)
                $valorDesconto = substr($x, 240, 13); //241 - 253 (013)
                $valorRecebido = substr($x, 253, 13); // 254 - 266 (013)
                $jurosMora = substr($x, 266, 13); // 267 - 279 (013)
                $outrosCreditos = substr($x, 279, 13); // 280 - 292 (013)
                $branco2espaco = str_pad("", 2, " "); // 293 - 294 (002)
                $motivoDoCodigoOcorrencia = str_pad("", 1, " "); // 295 - 295 (001)
                $dataCredito = substr($x, 295, 6); // 296 - 301 (006)
                $branco17espaco = str_pad("", 17, " "); // 302 - 318 (017)

                //INICIO POSICOES 319 - 328 (010)
                if ($identificacaoOcorrencia == '03') {
                    //codigo de ocorrencia invalido
                    $motivosRejeicoes = str_pad("", 8, " ") . "03"; //VERIFICAR ISSO AQUI DPS
                } else if ($identificacaoOcorrencia == '02') {
                    $motivosRejeicoes = str_pad("", 10, "0"); //VERIFICAR ISSO AQUI DPS
                } else {
                    $motivosRejeicoes = str_pad("", 10, " "); //VERIFICAR ISSO AQUI DPS
                }


                if ($identificacaoOcorrencia == '02') {

                    $qtdRegistros02 += 1;
                    $valorTotalRegistros02 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '06') {
                    $qtdRegistros06 += 1;
                    $valorTotalRegistros06 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '09' || $identificacaoOcorrencia == '10') {
                    $qtdRegistros09e10 += 1;
                    $valorTotalRegistros09e10 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '13') {
                    $qtdRegistros13 += 1;
                    $valorTotalRegistros13 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '14') {
                    $qtdRegistros14 += 1;
                    $valorTotalRegistros14 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '12') {
                    $qtdRegistros12 += 1;
                    $valorTotalRegistros12 += intval($valorTitulo);
                } else if ($identificacaoOcorrencia == '19') {
                    $qtdRegistros19 += 1;
                    $valorTotalRegistros19 += intval($valorTitulo);
                }
                if ($rateioCredito == 'R') {
                    $valorTotalRateio += intval($valorTitulo);
                    $qtdRateio += intval($valorTitulo);
                } else {
                    $valorTotalRateio = 0;
                    $qtdRateio += 0;
                }
                //FIM POSICOES 319 - 328 (010)


                $branco66espaco = str_pad("", 66, " "); //329 - 394 (066)
                $numeroSenquencialArquivo = substr($x, 394, 6); // 395 - 400 (006)

                //MONTA O REGISTRO DE MOVIMENTO DO RETORNO
                $registroMovimentoRetorno = $inicioRetornoRegistro . $zeros . $idenficacaoCedenteBanco .
                    $numControleParticipante . $zeros8espaços . $identificacaoTitulo . $usoBanco1 . $usoBanco2 .
                    $rateioCredito . $zeros2 . $codigoCarteira . $identificacaoOcorrencia . $dataOcorrencia .
                    $numDocumento . $identificacaoTituloBanco . $dataVencimentoTitulo . $valorTitulo . $bancoCobrador .
                    $codigoAgenciaCobradora . $especieTitulo . $despesaCobranca . $outrasDespesas . $jurosAtraso . $IOF .
                    $valorAbatimento . $valorDesconto . $valorRecebido . $jurosMora . $outrosCreditos . $branco2espaco .
                    $motivoDoCodigoOcorrencia . $dataCredito . $branco17espaco . $motivosRejeicoes . $branco66espaco .
                    $numeroSenquencialArquivo;

                //ESCREVE NO ARQUIVO
                fwrite($retornoBradesco, $registroMovimentoRetorno . "\n");
            }
            //FIM REGISTRO DE MOVIMENTO RETORNO



            //INICIO TRAILER RETORNO
            $x = $retornoSantander[sizeof($retornoSantander) - 1];


            $inicioTrailler = substr($x, 0, 17); // 001-017
            $quantidadeTitulosCobranca = substr($x, 17, 8); // 018-025 (008)
            $valorTotalTitulosCobranca = substr($x, 25, 14); // 026-039 (014)
            $numAvisoBancario = substr($x, 39, 8); //040-047 (008)
            $brancos10espacos = str_pad("", 10, " "); //048-057 (010)



            //TRASNFORMA A QTD DE REGISTROS EM STRING E  MANDA PRA FUNC
            $qtdRegistros02 = $this->completarPosicoes("" . $qtdRegistros02, 5, '0'); //058-062 (005)
            $valorTotalRegistros02 = $this->completarPosicoes("" . $valorTotalRegistros02, 12, '0'); //063-074 (012)
            $qtdRegistros06 = $this->completarPosicoes("" . $qtdRegistros06, 5, '0'); // 075-086 (012)
            $valorTotalRegistros06 = $this->completarPosicoes("" . $valorTotalRegistros06, 12, '0'); // 087-091 (005) e 092-103 (012)
            $qtdRegistros09e10 = $this->completarPosicoes("" . $qtdRegistros09e10, 5, '0'); // 104 - 108 (005)
            $valorTotalRegistros09e10 = $this->completarPosicoes("" . $valorTotalRegistros09e10, 12, '0'); // 109-120 (012)
            $qtdRegistros13 = $this->completarPosicoes("" . $qtdRegistros13, 5, '0'); // 121 - 125 (005)
            $valorTotalRegistros13 = $this->completarPosicoes("" . $valorTotalRegistros13, 12, '0'); // 126-137 (012)
            $qtdRegistros14 = $this->completarPosicoes("" . $qtdRegistros14, 5, '0'); // 138 - 142 (005)
            $valorTotalRegistros14 = $this->completarPosicoes("" . $valorTotalRegistros14, 12, '0'); // 143 - 154 (012)
            $qtdRegistros12 = $this->completarPosicoes("" . $qtdRegistros12, 5, '0'); // 155 - 159 (005)
            $valorTotalRegistros12 = $this->completarPosicoes("" . $valorTotalRegistros12, 12, '0'); // 160 - 171 (012)
            $qtdRegistros19 = $this->completarPosicoes("" . $qtdRegistros19, 5, '0'); // 172 - 176 (005)
            $valorTotalRegistros19 = $this->completarPosicoes("" . $valorTotalRegistros19, 12, '0'); // 177 - 188 (012)

            $branco174espacos = str_pad("", 174, " "); // 189 - 362 (174)
            $valorTotalRateio = $this->completarPosicoes("" . $valorTotalRateio, 15, '0'); // 363 - 377 (015)
            $qtdRateio = $this->completarPosicoes("" . $qtdRateio, 8, '0'); // 378 - 385 (008)
            $branco9espacos = str_pad("", 9, " "); // 386 - 394 (009)
            $numeroSenquencialArquivo = substr($x, 394, 6); // 395 - 400 (006)

            //MONTA O TRAILER DO RETORNO
            $trailerBradesco = $inicioTrailler . $quantidadeTitulosCobranca . $valorTotalTitulosCobranca . $numAvisoBancario .
                $brancos10espacos . $qtdRegistros02 . $valorTotalRegistros02 . $valorTotalRegistros06 . $qtdRegistros06 .
                $valorTotalRegistros06 . $qtdRegistros09e10 . $valorTotalRegistros09e10 . $qtdRegistros13 . $valorTotalRegistros13 .
                $qtdRegistros14 . $valorTotalRegistros14 . $qtdRegistros12 . $valorTotalRegistros12 . $qtdRegistros19 .
                $valorTotalRegistros19 . $branco174espacos . $valorTotalRateio . $qtdRateio . $branco9espacos . $numeroSenquencialArquivo;


            //ESCREVE NO ARQUIVO
            fwrite($retornoBradesco, $trailerBradesco);

            $retorno['retornoBradesco'] = asset($nomeArq);
            $retorno['dataGerado'] = date('d/m/y');
            $retorno['horaGerado'] = date('h:i:sa');

            $historicoRetorno = new historicoRetorno();

            $historicoRetorno->dataTraducao = date('y-m-d H:i:s');
            $historicoRetorno->autor = session()->get('nome');
            $historicoRetorno->nomeRetorno = $nomeArq;
            $historicoRetorno->save();
            $retorno['historico'] = $historicoRetorno;
            $retorno['title'] = "Retorno";
            return redirect()->route('retorno', $retorno);
            //FIM TRAILER
        } else {
            return redirect()->back()->with('msg', 'Arquivo incompatível!');
        }
    }
    public function lerRetorno($nome)
    {
        $a = Storage::get("public/retorno/$nome");
        dd($a);
        $data['arquivo'] = $nome;

        $remessa = file($nome);
        for ($i = 1; $i < (sizeof($remessa) - 1); $i++) {

        }
        $data['title'] = "Arquivo Retorno $nome";
        return view('verRetorno', $data);
    }

    public function enviarRetorno($nome)
    {


        if ($this->_enviarRetorno($nome, 'admin', 'admin', 'http://localhost/login-andre/logar.php')) {
            return redirect()->back()->with('enviado', 'Arquivo enviado com sucesso!');
        } else {
            return redirect()->back()->with('enviado', 'Erro ao enviar arquivo!');
        }
    }

    private function _enviarRetorno($file, $user, $password, $url)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $cookie = 'tmp/cookie.txt';

        $file = asset($file);
        //dd($file);
        // $args['file'] = new CURLFile($file, 'text');
        // $args['usuario'] = $user;
        // $args['senha'] = $password;
        $args = [
            'file'=>new CURLFile($file, 'text'),
            'usuario'=> 'admin',
            'senha'=>'admin'
        ];
        //$args = "{'file':$file, 'usuario':$user, 'senha':$password}";

        //dd($args = json_encode($args));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        //curl_setopt($ch, CURLOPT_POST, 1);
        $results = curl_exec($ch);
        dd($results);
        //var_dump($results);
        curl_close($ch);

        if (strpos($results, 'Arquivo enviado com sucesso!'))
            return true;
        else
            return false;

    }
}
