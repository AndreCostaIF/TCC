<?php

use App\Models\Financeiros;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

function formatDateAndTime($value, $format = 'd/m/Y')
{
    $value = str_replace('pm', '', $value);
    // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
    return Carbon\Carbon::parse($value)->format($format);
}

function formatNumber($value)
{

    return number_format($value, 2, ',');
}

function formatarCpf($cpf)
{

    $cpfFormatado = substr($cpf, 0, 3) . "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);

    return $cpfFormatado;
}

function formatarCnpj($cnpj)
{

    $cnpjFormatado = substr($cnpj, 0, 2) . "." . substr($cnpj, 2, 3) . "." . substr($cnpj, 5, 3) . "/" . substr($cnpj, 8, 4) . "-" . substr($cnpj, 12, 2);

    return $cnpjFormatado;
}

function somentoNumeroCpfOuCnpj($dado)
{

    $numero = filter_var($dado, FILTER_SANITIZE_NUMBER_INT);

    $numero = str_replace("-", "", $numero);

    return $numero;

}

function _v($arr, $key = null)
{

    if (isset($arr[$key])) {
        return $arr[$key];
    } else {
        return null;
    }
}

function existe($data)
{
    if (isset($data)) {
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

function completarPosicoes2($campo, $posicoes, $complemento)
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

function paginate($items, $perPage = 5, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

}

function letraAleatoria()
{
    $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $letras = str_shuffle($letras);
    $indice = rand(0, 25);
    return $letras[$indice];
}

function valoresExtra($id, $valor)
{
    $valorExtra = Financeiros::valoresExtra($id);
    $descontoBoleto = $valorExtra['desconto'];
    $acrescimoBoleto = $valorExtra['acrescimo'];

    $valor = ($valor + $acrescimoBoleto) - $descontoBoleto;
    return $valor;
}


?>
