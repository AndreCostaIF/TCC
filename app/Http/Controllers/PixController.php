<?php

namespace App\Http\Controllers;
include('openboleto/autoloader.php');

use App\ConstantesPix;
use App\Models\Clientes;
use App\Models\Financeiros;
use App\Models\Pessoa_fisica;
use App\Models\PessoaJuridica;
use App\Models\PixModel;
use Illuminate\Http\Request;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;

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

    public function gerarQrCode($idBoleto = null, $valor = 0, $tam = 120)
    {

        if ($idBoleto != null) {
            $txid  = PixModel::getTxid($idBoleto);
            //VER SE TA COM JSON DECODE E JOGA O RESTO DO CODIGO AQUI :)

        }else{
            return '';
        }


        if ($txid != null) {
            $payload = (new Pix)->setChavePix(ConstantesPix::PIX_KEY)
                ->setNomeTitular('Intelnet Telecom')
                ->setCidadeTitular('Nova Cruz')
                ->setTxid($txid->txid)
                ->setValor(doubleval($valor));

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

        $chata = new Boleto();
        $chata->emitirBoletoUnitario($id);


    }
}
