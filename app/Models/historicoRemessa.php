<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class historicoRemessa extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'historico_remessa';


    public static function pegarTodos(){
       $a = DB::connection('mysql2')->table('historico_remessa')->orderBy('dataTraducao', 'desc')->paginate(10);
        return $a;
    }

}
