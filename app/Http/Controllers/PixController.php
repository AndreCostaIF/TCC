<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;
use Pix;

class PixController extends Controller
{
    //

    public function gerarQrCode($vencimento, $nomeDevedor, $valor, $infoAdicionais, $txid, $cpf = null, $cnpj = null)
    {

        if ($this->buscarCobranca($txid) != []) {
            $cobranca = $this->buscarCobranca($txid);
        } else {

            if(isset($cpf)){

                $cobranca = $this->criarCobranca($vencimento, $nomeDevedor, $valor, $infoAdicionais, $txid, $cpf, $cnpj);
            }else{
                $cobranca = $this->criarCobranca($vencimento, $nomeDevedor, $valor, $infoAdicionais, $txid, $cpf, $cnpj);
            }
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

            $stringPayload = $payload->gerarPayload();
            //dd($stringPayload);
            $qrCode = new QrCode($stringPayload);

            $image =  (new OutPut\Png)->output($qrCode, 120);

            return $image;
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

    public function criarCobranca($vencimento, $nomeDevedor, $valor, $infoAdicionais, $txid, $cpf = null, $cnpj = null)
    {
        $authorization = "Authorization: Bearer " . $this->gerarToken();
        //dd($authorization);

        if(isset($cpf)){

            $post = [
                "calendario" => [
                    'dataDeVencimento' =>  $vencimento,
                    "validadeAposVencimento" => 30
                ],
                "devedor" => [
                    "cpf" => $cpf,
                    "nome" => $nomeDevedor
                ],
                "valor" => [
                    "original" => $valor,
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
                        "valor" => $infoAdicionais
                    ],

                ]
            ];
        }else{
            $post = [
                "calendario" => [
                    'dataDeVencimento' =>  $vencimento,
                    "validadeAposVencimento" => 30
                ],
                "devedor" => [
                    "cnpj" => $cnpj,
                    "nome" => $nomeDevedor
                ],
                "valor" => [
                    "original" => $valor,
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
                        "valor" => $infoAdicionais
                    ],

                ]
            ];
        }
        $url = "https://pix.santander.com.br/api/v1/sandbox/cob/$txid";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
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
}
