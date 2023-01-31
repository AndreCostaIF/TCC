<?php

namespace App\Http\Controllers;
include('openboleto/autoloader.php');
use App\Models\Clientes;
use App\Models\Financeiros;
use App\Models\Pessoa_fisica;
use App\Models\PessoaJuridica;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;
use OpenBoleto\Agente;
use OpenBoleto\Banco\Santander;
use Pix;

class PixController extends Controller
{


    public function erroAutenticado()
    {
        if (session()->has('nome')) {

            return false;
        } else {
            return true;
        }
    }

    public function gerarQrCode($txid, $tam = 120)
    {

        if ($this->buscarCobranca($txid) != []) {
            $cobranca = $this->buscarCobranca($txid);
        }

        $cobranca->status = 'ATIVA';
        if ($cobranca->status != 'CONCLUIDA') {
            $payload = (new Pix)->setChavePix($cobranca->location)
                ->setNomeTitular('Intelnet Telecom')
                ->setCidadeTitular('Nova Cruz')
                ->setTxid($cobranca->txid)
                ->setValor(doubleval($cobranca->valor->original));

            $stringPayload = $payload->gerarPayload();

            $qrCode = new QrCode($stringPayload);


            $image =  (new OutPut\Png)->output($qrCode, $tam);
            $image = base64_encode($image);

            $arr['image'] = $image;
            $arr['payload'] = $stringPayload;
            $arr['empresa'] = $payload->getNomeTitular();

            return $arr;
        } else {
            return 1;
        }
    }
    public function index(Request $request)
    {

        return view('dashboardPix');

    }

    private function gerarToken()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://pix.santander.com.br/sandbox/oauth/token?grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "client_id=18AxUqXJBuZlRA1FgWc8AeqnTrdgbhGY&client_secret=DSUGlKJk4EAawDGh"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        //  dd(json_decode($server_output), true);
        $server_output = json_decode($server_output, true);
        return $server_output['access_token'];
    }

    public function criarCobranca(Request $request)
    {

        $authorization = "Authorization: Bearer " . $this->gerarToken();
        //dd($authorization);
        if($request->get('cpf') != null){

            $post = [
                "calendario" => [
                    'dataDeVencimento' =>  $request->get('vencimento'),
                    "validadeAposVencimento" => 30
                ],
                "devedor" => [
                    "cpf" => $request->get('cpf'),
                    "nome" => $request->get('nomeDevedor')
                ],
                "valor" => [
                    "original" => $request->get('valor'),
                    "multa" => [
                        "modalidade" => "2",
                        "valorPerc" => "2.00"
                    ],
                    "juros" => [
                        "modalidade" => "2",
                        "valorPerc" => "0.33"
                    ]
                ],
                "chave" => "7d9f0335-8dcc-4054-9bf9-0dbd61d36906",
                "infoAdicionais" => [
                    [
                        "nome" => "contrato",
                        "valor" => $request->get('infoAdicionais')
                    ],

                ]
            ];
        }else{
            $post = [
                "calendario" => [
                    'dataDeVencimento' =>  $request->get('vencimento'),
                    "validadeAposVencimento" => 30
                ],
                "devedor" => [
                    "cnpj" => $request->get('cnpj'),
                    "nome" => $request->get('nomeDevedor')
                ],
                "valor" => [
                    "original" => $request->get('valor'),
                    "multa" => [
                        "modalidade" => "2",
                        "valorPerc" => "2.00"
                    ],
                    "juros" => [
                        "modalidade" => "2",
                        "valorPerc" => "0.33"
                    ]
                ],
                "chave" => "7d9f0335-8dcc-4054-9bf9-0dbd61d36906",
                "infoAdicionais" => [
                    [
                        "nome" => "contrato",
                        "valor" => $request->get('infoAdicionais')
                    ],

                ]
            ];
        }
        $txid = $request->get('idboleto');

        $url = "https://pix.santander.com.br/api/v1/sandbox/cob/$txid";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $result =  json_decode($result, true);
        $qrcode['qrcode'] = $this->gerarQrCode($result['txid'], 250);

        $qrcode['chave'] = "7d9f0335-8dcc-4054-9bf9-0dbd61d36906";
        $qrcode['valor'] = $request->get('valor');
        $qrcode['nome'] = $request->get('nomeDevedor');
        $qrcode['id'] = $txid;

        return $qrcode;
    }

    public function buscarCobranca($txid)
    {


        //$txid = 'cd1fe328-c875-4812-85a6-f233ae41b662';
        $authorization = "Authorization: Bearer " . $this->gerarToken();
        $url = "https://pix.santander.com.br/api/v1/sandbox/cob/$txid";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function configurarWebHook($chave){

        $authorization = "Authorization: Bearer " . $this->gerarToken();
        $url = "https://pix.santander.com.br/api/v1/sandbox/webhook/$chave";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function consultarWebHook($chave){

        $authorization = "Authorization: Bearer " . $this->gerarToken();
        $url = "https://pix.santander.com.br/api/v1/sandbox/webhook/$chave";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function buscarDadosBoleto(Request $request){
        $boleto = json_decode(Financeiros::where([
            ['id', $request->get('id')]
        ])->get(), true);


        $cliente = json_decode(Clientes::where(
            'id',
            $boleto[0]['cliente_id_web']
        )->get(), true);

        $boleto = $boleto[0];
        if ($cliente == []) {
            return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
        }

        $cliente = $cliente[0];

        if (isset($cliente['pessoa_fisica_id'])) {

            $pessoaFisica =  json_decode(Pessoa_fisica::where([
                ['id', $cliente['pessoa_fisica_id']]
            ])->get(), true);

           $pessoaFisica = $pessoaFisica[0];
           $boleto['reg_valor_total'] = valoresExtra($boleto['id'], $boleto['reg_valor']);
           $arr['boleto'] = $boleto;

           $arr['cliente'] = $pessoaFisica;

           return $arr;
        }else{
            $pessoaJuridica =  json_decode(PessoaJuridica::where([
                ['id', $cliente['pessoa_juridica_id']]
            ])->get(), true);

            $pessoaJuridica = $pessoaJuridica[0];
            $pessoaJuridica['nome'] = $pessoaJuridica['fantasia'];


            $boleto['reg_valor_total'] = valoresExtra($boleto['id'], $boleto['reg_valor']);
            $arr['boleto'] = $boleto;

            $arr['cliente'] = $pessoaJuridica;

            return $arr;
        }
    }

    public function emitirBoletoUnitarioComPix($id = null)
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }

        $boleto = json_decode(Financeiros::where([
            ['id', $id]
        ])->get(), true);


        $cliente = json_decode(Clientes::where(
            'id',
            $boleto[0]['cliente_id_web']
        )->get(), true);
        $boleto = $boleto[0];
        if ($cliente == []) {
            return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
        }
        $cliente = $cliente[0];

        if (isset($cliente['pessoa_fisica_id'])) {

            $pessoaFisica =  json_decode(Pessoa_fisica::where([
                ['id', $cliente['pessoa_fisica_id']]
            ])->get(), true);


            $idTelefone = $pessoaFisica[0]['lista_telefonica_id'];
            $pessoaFisica = $pessoaFisica[0];


            $enderecoJoin = json_decode(DB::table('lista_telefonica')
                ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                ->where('lista_telefonica.id', '=', $idTelefone)
                ->get(), true);

            $endereco = $enderecoJoin[0];


            $sacado = new Agente($pessoaFisica['nome'], $pessoaFisica['cpf'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
            $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');

            $boleto['reg_valor_total'] = valoresExtra($boleto['id'], $boleto['reg_valor']);

            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0"),

            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);

            $pix = $this->gerarQrCode($boleto['id'], 120);
            if($pix != 1){

                $image = $pix['image'];

                $arr = [
                    'pix'=>"<img  src='data:image/png;base64, $image'>",
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg,
                ];

                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput();
            }else{

                echo 'Pix pago';
            }


        } else {
            $pessoaJuridica =  json_decode(PessoaJuridica::where([
                ['id', $cliente['pessoa_juridica_id']]
            ])->get(), true);


            $idTelefone = $pessoaJuridica[0]['lista_telefonica_id'];
            $pessoaJuridica = $pessoaJuridica[0];


            $enderecoJoin = json_decode(DB::table('lista_telefonica')
                ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                ->where('lista_telefonica.id', '=', $idTelefone)
                ->get(), true);

            $endereco = $enderecoJoin[0];


            $sacado = new Agente($pessoaJuridica['fantasia'], $pessoaJuridica['cnpj'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
            $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');

            $boleto['reg_valor_total'] = valoresExtra($boleto['id'], $boleto['reg_valor']);

            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0")
            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);

            $pix = $this->gerarQrCode($boleto['id']);
            if($pix != 1){

                $image = $pix['image'];
                $arr = [
                    'pix'=>"<img src='data:image/png;base64, $image'>",
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg,
                ];

                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput();
            }else{

                echo 'Pix pago';
            }
        }
    }
}
