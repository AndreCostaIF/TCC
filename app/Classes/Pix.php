<?php

use App\ConstantesPix;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;

class Pix
{


    private $location;
    private $descricao;
    private $nome_titular;
    private $cidade_titular;
    private $txid;
    private $valor;

    public function setChavePix($location)
    {

        $this->location = $location;
        return $this;
    }

    public function setDescricao($descricao)
    {

        $this->descricao = $descricao;
        return $this;
    }

    public function setNomeTitular($nome_titular)
    {

        $this->nome_titular = $nome_titular;
        return $this;
    }

    public function setCidadeTitular($cidade_titular)
    {

        $this->cidade_titular = $cidade_titular;
        return $this;
    }

    public function setTxid($txid)
    {

        $this->txid = $txid;
        return $this;
    }

    public function setValor($valor)
    {
        $valor = (string)number_format($valor, 2, '.');
        $this->valor = $valor;
        return $this;
    }


    private function getCRC16($payload)
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

    private function getValue($id, $value)
    {
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);

        return $id . $size . $value;
    }

    private function getMerchantAccountInformation()
    {
        $gui = getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
        $key = getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION_LOCATION, $this->location);
        $descripcion = strlen($this->descricao) ? getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->descricao) : '';


        return getValue(ConstantesPix::ID_MERCHANT_ACCOUNT_INFORMATION, $gui . $key . $descripcion);
    }

    private function getAdditionalDataFieldTemplate()
    {

        $txid = getValue(ConstantesPix::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

        return getValue(ConstantesPix::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $this->txid);
    }

    public function gerarPayload()
    {

        $payload =  $this->getValue(ConstantesPix::ID_PAYLOAD_FORMAT_INDICATOR, '01') .
            $this->getMerchantAccountInformation() .
            $this->getValue(ConstantesPix::ID_MERCHANT_CATEGORY_CODE, '0000') .
            $this->getValue(ConstantesPix::ID_TRANSACTION_CURRENCY, '986') .
            $this->getValue(ConstantesPix::ID_TRANSACTION_AMOUNT, $this->valor) .
            $this->getValue(ConstantesPix::ID_COUNTRY_CODE, 'BR') .
            $this->getValue(ConstantesPix::ID_MERCHANT_NAME, $this->nome_titular) .
            $this->getValue(ConstantesPix::ID_MERCHANT_CITY, $this->cidade_titular) .
            $this->getAdditionalDataFieldTemplate();


        return $payload . $this->getCRC16($payload);
    }


}
