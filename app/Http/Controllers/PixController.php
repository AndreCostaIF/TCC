<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PixController extends Controller
{

    public function gerarToken()
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://pix.santander.com.br/sandbox/oauth/token?grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "client_id=FMcefpZMnaxTPflVY7a5O7O5YGlkBXOh&client_secret=lA54nmcqNTre2uWz"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        //  dd(json_decode($server_output), true);
        $server_output = json_decode($server_output, true);
        //dd($server_output);
        return $server_output;

    }

    public function criarCobranca()
    {
        $token = $this->gerarToken();
        $authorization = "Authorization: Bearer " . $token['access_token'];
        //dd($authorization);
        $post = [
            "calendario" => [
                "expiracao" => 3600
            ],
            "devedor" => [
                "cnpj" => "12345678000195",
                "nome" => "Empresa de Serviços SA"
            ],
            "valor" => [
                "original" => "37.00"
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
        $url = $token['refreshUrl'] . "/sandbox/cob/cd1fe328-c875-4812-85a6-f233ae41b662";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CAINFO, getcwd() . 'C:\openssl/certificadoPEM.pem');
        $result = curl_exec($ch);
        curl_close($ch);

        echo '<pre>';
        dd($result);
        return json_decode($result);
    }
}
