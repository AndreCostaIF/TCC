<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PixModel extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'pix';
    public $timestamps = false;

    public static function getTxid($idBoleto){
        $txid = DB::connection('mysql2')->table('pix')->where('boleto_id', $idBoleto)->select('txid')->first();
         return $txid;
     }
}
