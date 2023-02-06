<?php
namespace App;



class ConstantesPix {

    const ID_PAYLOAD_FORMAT_INDICATOR = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_LOCATION = '25';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const ID_MERCHANT_CATEGORY_CODE = '52';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_MERCHANT_NAME = '59';
    const ID_MERCHANT_CITY = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
    const ID_CRC16 = '63';

    //VALOR DE CHAVE PIX
    // 1 - CPF
    // 2 - CNPJ = 07692425000158
    // 3 - Celular
    // 4 - E-mail
    // 5 - EVP- Chave aleatória = 4c9b1456-e124-46b8-ae05-fa4c00de54b1 // NAÕ FUNCIONA PRA GERAR OS BOLETOS NO SANTANDER
    const PIX_KEY = '07692425000158';
    //TIPO DE CHAVE PIX
    // 1 - CPF
    // 2 - CNPJ
    // 3 - Celular
    // 4 - E-mail
    // 5 - EVP- Chave aleatória
    const TYPE_KEY = '2';
}
?>
