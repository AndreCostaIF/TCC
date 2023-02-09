<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradutorRemessa;
use App\Http\Controllers\TradutorRetorno;
use App\Http\Controllers\Boleto;
use App\Http\Controllers\LoginContoller;
use App\Http\Controllers\PixController;

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
Route::post('/login',  [LoginContoller::class, 'logar'])->name('login');
Route::get('/logout',  [LoginContoller::class, 'sessionDestroy'])->name('logout');

Route::get('/remessa', [TradutorRemessa::class, 'index'])->name('remessa');
Route::get('/remessa/{nome}', [TradutorRemessa::class, 'lerRemessa'])->name('lerRemessa');
Route::post('/traduzirRemessa',  [TradutorRemessa::class, 'traduzir'])->name('traduzirRemessa');

Route::get('/retorno', [TradutorRetorno::class, 'index'])->name('retorno');
Route::get('/retorno/{nome}', [TradutorRetorno::class, 'lerRetorno'])->name('lerRetorno');
Route::get('/retorno/enviar/{nome}', [TradutorRetorno::class, 'enviarRetorno'])->name('enviarRetorno');
Route::post('/traduzirRetorno',  [TradutorRetorno::class, 'traduzirRetorno'])->name('traduzirRetorno');

Route::get('/buscarBoleto', [Boleto::class, 'index'])->name('buscarBoleto');
Route::get('/boletos/busca/{id}/{flag}',  [Boleto::class, 'listarBoletos'])->name('listarBoletos');
Route::get('/boletos/imprimir/{id}',  [Boleto::class, 'emitirBoletoUnitario'])->name('imprimirBoleto');

Route::get('/boletos/clientes',  [Boleto::class, 'buscarCliente'])->name('buscarCliente');


Route::post('/darbaixa', [Boleto::class, 'baixaBoleto'])->name('baixaBoleto');
Route::post('/boletos/delete',  [Boleto::class, 'deletarBoleto'])->name('deletarBoleto');

Route::get('/liberar/{id}', [Boleto::class, 'liberarCliente'])->name('liberar');

Route::get('/boletos/massa/buscar',  [Boleto::class, 'boletosEmMassa'])->name('massa');
Route::get('/boletos/massa',  [Boleto::class, 'boletosMassaView'])->name('massaView');
Route::post('/boletos/imprimirMassa',  [Boleto::class, 'imprimirMassa'])->name('imprimirMassa');


#AREA PIX
Route::get('/pix',  [PixController::class, 'index'])->name('pix');
Route::get('/teste',  [Boleto::class, 'buscarCobranca'])->name('pix_teste');
Route::get('/boletos/imprimirpix/{id}',  [PixController::class, 'emitirBoletoUnitarioComPix'])->name('imprimirBoletoPIX');
Route::get('/pix/dados',  [PixController::class, 'buscarDadosBoleto'])->name('buscarDadosBoleto');
Route::post('/pix/cobranca',  [PixController::class, 'criarCobranca'])->name('criarCobranca');
