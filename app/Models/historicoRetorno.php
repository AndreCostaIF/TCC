<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class historicoRetorno extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'historico_retorno';

    public static function pegarTodos(){
       $a = DB::connection('mysql2')->table('historico_retorno')->orderBy('dataTraducao', 'desc')->paginate(10);
        return $a;
    }
}
