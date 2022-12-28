<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cidades extends Model
{
    use HasFactory;
    protected $table = 'cidades';


    public static function buscarCidade($cep){
        $cidade = DB::table('cidades')
                            ->join('estados', 'cidades.estados_cod_estados', 'estados.id')
                            ->select('cidades.cidade', 'estados.sigla')
                            ->where('cidades.cep', '=', $cep)
                            ->first();



        return $cidade;

    }
}
