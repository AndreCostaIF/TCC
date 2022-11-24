<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class historicoRemessa extends Model
{
    use HasFactory;

    public static function pegarTodos(){
       $a = DB::connection('mysql2')->table('historico_remessa')->paginate(10);
    return $a;
    }

}
