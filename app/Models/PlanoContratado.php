<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoContratado extends Model
{
    use HasFactory;
    protected $table = 'plano_contratado';
    public $timestamps = false;
}
