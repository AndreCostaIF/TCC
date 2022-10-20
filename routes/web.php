<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradutorRemessa;
use App\Http\Controllers\TradutorRetorno;
use App\Http\Controllers\Boleto;
use App\Http\Controllers\LoginContoller;

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

Route::get('/', [LoginContoller::class, 'index'])->name('index');

Route::get('/remessa', [TradutorRemessa::class, 'index'])->name('remessa');

Route::get('/buscarBoleto', [Boleto::class, 'index'])->name('buscarBoleto');

Route::get('/retorno', [TradutorRetorno::class, 'index'])->name('retorno');
Route::post('/traduzirRemessa',  [TradutorRemessa::class, 'traduzir'])->name('traduzirRemessa');
Route::post('/traduzirRetorno',  [TradutorRetorno::class, 'traduzirRetorno'])->name('traduzirRetorno');

Route::get('/boletos/busca/{id}/{flag}',  [Boleto::class, 'listarBoletos'])->name('listarBoletos');
Route::get('/boletos/imprimir/{id}',  [Boleto::class, 'emitirBoletoUnitario'])->name('imprimirBoleto');
Route::post('/boletos/clientes',  [Boleto::class, 'buscarCliente'])->name('buscarCliente');
Route::post('/login',  [LoginContoller::class, 'logar'])->name('login');
Route::get('/logout',  [LoginContoller::class, 'sessionDestroy'])->name('logout');

Route::post('/boletos/massa/buscar',  [Boleto::class, 'boletosEmMassa'])->name('massa');
Route::get('/boletos/massa',  [Boleto::class, 'boletosMassaView'])->name('massaView');
Route::post('/boletos/imprimirMassa',  [Boleto::class, 'imprimirMassa'])->name('imprimirMassa');
