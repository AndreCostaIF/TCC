<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;
use Pix;

class PixController extends Controller
{
    //

    public function gerarQRCode(){
        $cobranca = $this->criarCobranca();
        //dd($cobranca);
        $payload = (new Pix)->setChavePix($cobranca->location)
            ->setDescricao('Teste pix')
            ->setNomeTitular('Intelnet Telecom')
            ->setCidadeTitular('Nova Cruz')
            ->setTxid($cobranca->txid)
            ->setValor(doubleval($cobranca->valor->original));


        $stringPayload = $payload->gerarPayload();




        $qrCode = new QrCode($stringPayload);

        $image =  (new OutPut\Png)->output($qrCode, 120);

        return $image;
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

    public function criarCobranca()
    {
        $authorization = "Authorization: Bearer " . $this->gerarToken();
        //dd($authorization);
        $post = [
            "calendario" => [
                'dataDeVencimento' =>  "2020-12-31",
                "validadeAposVencimento" => 30
            ],
            "devedor" => [
                "cnpj" => "12345678000195",
                "nome" => "Empresa de Serviços SA"
            ],
            "valor" => [
                "original" => "37.00",
                "multa" => [
                    "modalidade"=> "2",
                    "valorPerc"=> "15.00"
                ],
                "juros"=> [
                    "modalidade"=> "2",
                    "valorPerc"=> "2.00"
                ]
            ],
            "chave" => "7d9f0335-8dcc-4054-9bf9-0dbd61d36906",
            "solicitacaoPagador" => "Serviço realizado.",
            "infoAdicionais" => [
                [
                    "nome" => "Campo 1",
                    "valor" => "Informação Adicional1 do PSP-Recebedor"
                ],
                [
                    "nome" => "Campo 2",
                    "valor" => "Informação Adicional2 do PSP-Recebedor"
                ]
            ]
        ];
        $url = "https://pix.santander.com.br/api/v1/sandbox/cob";
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
}
