<?php

use App\ConstantesPix;
use PHPUnit\TextUI\XmlConfiguration\Constant;

function getCRC16($payload)
{
    //ADICIONA DADOS GERAIS NO PAYLOAD
    $payload .= ConstantesPix::ID_CRC16 . '04';

    //DADOS DEFINIDOS PELO BACEN
    $polinomio = 0x1021;
    $resultado = 0xFFFF;

    //CHECKSUM
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                $resultado &= 0xFFFF;
            }
        }
    }

    //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
    return ConstantesPix::ID_CRC16 . '04' . strtoupper(dechex($resultado));
}

function getValue($id, $value){
    $size = str_pad(strlen($value),2,'0', STR_PAD_LEFT);

    return $id.$size.$value;
}

function getMerchantAccountInformation($chave_pix, $descricao){
    $gui = getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
    $descripcion = strlen($descricao) ? getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $descricao) : '';


    return getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$descripcion);
}

function getAdditionalDataFieldTemplate($txid){

    $txid = getValue(ConstantesPix::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $txid);

    return getValue(ConstantesPix::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
}

function gerarPayload($chave_pix, $descricao, $nome_titular, $cidade_titular, $txid, $valor)
{
    $valor = (string)number_format($valor, 2, '.');

    $payload =  getValue(ConstantesPix::ID_PAYLOAD_FORMAT_INDICATOR, '01').
                getMerchantAccountInformation($chave_pix, $descricao).
                getValue(ConstantesPix::ID_MERCHANT_CATEGORY_CODE, '0000').
                getValue(ConstantesPix::ID_TRANSACTION_CURRENCY, '986').
                getValue(ConstantesPix::ID_TRANSACTION_AMOUNT, $valor).
                getValue(ConstantesPix::ID_COUNTRY_CODE, 'BR').
                getValue(ConstantesPix::ID_MERCHANT_NAME, $nome_titular).
                getValue(ConstantesPix::ID_MERCHANT_CITY, $cidade_titular).
                getAdditionalDataFieldTemplate($txid);


    return $payload.getCRC16($payload);
}



