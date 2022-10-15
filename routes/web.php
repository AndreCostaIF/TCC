<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradutorRemessa;
use App\Http\Controllers\TradutorRetorno;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [TradutorRemessa::class, 'index'])->name('remessa');

Route::get('/retorno', [TradutorRetorno::class, 'index'])->name('retorno');
Route::post('/traduzirRemessa',  [TradutorRemessa::class, 'traduzir'])->name('traduzirRemessa');
Route::post('/traduzirRetorno',  [TradutorRetorno::class, 'traduzirRetorno'])->name('traduzirRetorno');

