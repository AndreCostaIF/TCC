<?php

    function formatDateAndTime($value, $format = 'd/m/Y')
    {
        $value = str_replace('pm', '', $value);
        // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
        return Carbon\Carbon::parse($value)->format($format);
    }

    function formatNumber($value){

        return number_format($value, 2, ',');
    }

    function formatarCpf($cpf){

        $cpfFormatado = substr($cpf, 0, 3). "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);

        return $cpfFormatado;
    }

    function formatarCnpj($cnpj){

        $cnpjFormatado = substr($cnpj, 0, 2). "." . substr($cnpj, 2, 3) . "." . substr($cnpj, 5, 3) . "/" . substr($cnpj, 8, 4) . "-" . substr($cnpj, 12, 2);

        return $cnpjFormatado;
    }

    function somentoNumeroCpfOuCnpj($dado){

            $numero = filter_var($dado, FILTER_SANITIZE_NUMBER_INT);

            $numero = str_replace("-","", $numero);

            return $numero;

    }

    function _v($arr,$key=null){

        if (isset($arr[$key])){
            return $arr[$key];
        } else {
            return null;
        }
    }

    function existe($data){
        if (isset($data)){
            return $data;
        } else {
            return null;
        }
    }

    function completarPosicoes($campo, $posicoes, $complemento)
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

?>
