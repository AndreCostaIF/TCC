<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginRadius extends Model
{
    use HasFactory;
    protected $table = 'login_radius';
    protected $connection = 'mysql2';
}
