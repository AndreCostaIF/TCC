<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Financeiros extends Model
{
    use HasFactory;
    protected $table = 'financeiros';

    public static function valoresExtra($idBoleto)
    {
        $desconto = 0;
        $acrescimo = 0;
        $financeiro_anexos = json_decode(DB::table('financeiro_anexos')->where([
            ['financeiro_id', $idBoleto]
        ])->get(), true);

        if($financeiro_anexos != []){
            $financeiro_anexos = $financeiro_anexos[0];

            if($financeiro_anexos['tipo'] == "D"){
                $desconto = $financeiro_anexos['valor'];
            }else if ($financeiro_anexos['tipo'] == "T"){
                $acrescimo = $financeiro_anexos['valor'];
            }

            $dados['desconto'] = $desconto;
            $dados['acrescimo'] = $acrescimo;
            return $dados;
        }else{
            $dados['desconto'] = 0;
            $dados['acrescimo'] = 0;
            return $dados;
        }
    }


}
