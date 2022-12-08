<?php

namespace App\Http\Controllers;


include('openboleto/autoloader.php');

use App\Models\Pessoa_fisica;
use openBoleto\Agente;
use openBoleto\Banco\Santander;
use App\Models\Clientes;
use App\Models\Financeiros;
use App\Models\LoginAdmin;
use App\Models\LoginRadius;
use App\Models\PessoaJuridica;
use App\Models\PlanoContratado;
use Illuminate\Support\Facades\DB;
use DateTime;
use OpenBoleto\BoletoAbstract;

use Illuminate\Http\Request;

class Boleto extends Controller
{
    public function erroAutenticado(){
        if(session()->has('nome')){

            return false;
        }else{
            return true;

        }
    }
    public function index()
    {
        if($this->erroAutenticado()){
            return redirect()->route('index');
        }else{
            return view('boletoIndex');
        }

    }


    public function buscarCliente(Request $request){

        if($this->erroAutenticado()){
            return redirect()->route('index');
        }
        $search =  $request->get('campoBusca');
        $flag = $request->get('flag');




        if ($flag == "nome" || $flag == "cpf") {

            if ($flag == "nome") {
                $pessoaFisica =  Pessoa_fisica::where([
                    ['nome', 'like', $search . '%']
                ])->orderBy('nome', 'asc')->paginate(10);

                $data['clientesBusca'] = $pessoaFisica;

            }else{
                $search = somentoNumeroCpfOuCnpj($search);
                $pessoaFisica =  Pessoa_fisica::where([
                    ['cpf', $search]
                ])->orderBy('nome', 'asc')->paginate(10);


                $data['clientesBusca'] = $pessoaFisica;

            } $data['flag'] = 'cpf';
        }elseif($flag == "fantasia" || $flag == "cnpj"){
            if($flag == "fantasia"){
                $pessoaJuridica =  PessoaJuridica::where([
                    ['fantasia', 'like', '%' . $search . '%']
                ])->orderBy('nome', 'asc')->paginate(10);



                $data['clientesBusca'] = $pessoaJuridica;

            }else{
                $search = somentoNumeroCpfOuCnpj($search);
                $pessoaJuridica =  PessoaJuridica::where([
                    ['cnpj', $search]
                ])->orderBy('fantasia', 'asc')->paginate(10);


                $data['clientesBusca'] = $pessoaJuridica;
            }
            $data['flag'] = 'cnpj';

        }

        return view('boletoIndex', $data);

    }

    public function listarBoletos($id = null, $flag = null)
    {

        if($this->erroAutenticado()){
            return redirect()->route('index');
        }

        if ($flag == "cpf") {


                $pessoaFisica =  json_decode(Pessoa_fisica::where([
                    ['id',$id]
                ])->get(), true);


                $pessoaFisica = $pessoaFisica[0];

                $cliente = json_decode(Clientes::where(
                    'pessoa_fisica_id',
                    $id
                )->get(), true);

                $cliente = $cliente[0];

                $pessoaFisica['idCliente'] = $cliente['id'];

                $financeiro = Financeiros::where([
                    ['cliente_id_web', $cliente['id']]
                ])->orderBy('reg_vencimento', 'desc')->paginate(10);





                $data['boletos'] = $financeiro;
                $data['cliente'] = $pessoaFisica;
                return view('boletoIndex', $data);
            }
            else if ($flag == "cnpj") {



                $pessoaJuridica =  json_decode(PessoaJuridica::where([
                    ['id', $id]
                ])->get(), true);


                $pessoaJuridica = $pessoaJuridica[0];

                $cliente = json_decode(Clientes::where(
                    'pessoa_juridica_id',
                    $pessoaJuridica['id']
                )->get(), true);

                $cliente = $cliente[0];

                $pessoaJuridica['idCliente'] = $cliente['id'];
                $financeiro = Financeiros::where([
                    ['cliente_id_web', $cliente['id']]
                ])->orderBy('reg_vencimento', 'desc')->paginate(10);

                $pessoaJuridica['nome'] = $pessoaJuridica['fantasia'];
                $data['boletos'] = $financeiro;
                $data['cliente'] = $pessoaJuridica;

                //dd($financeiro);

                return view('boletoIndex', $data);

            }
        }


    public function emitirBoletoUnitario($id = null)
    {
        if($this->erroAutenticado()){
            return redirect()->route('index');
        }

        $boleto = json_decode(Financeiros::where([
            ['id', $id]
        ])->get(), true);


        $cliente = json_decode(Clientes::where(
            'id',
            $boleto[0]['cliente_id_web']
        )->get(), true);
        $boleto = $boleto[0];
        $cliente = $cliente[0];

        if(isset($cliente['pessoa_fisica_id'])){

            $pessoaFisica =  json_decode(Pessoa_fisica::where([
                ['id', $cliente['pessoa_fisica_id']]
            ])->get(), true);


            $idTelefone = $pessoaFisica[0]['lista_telefonica_id'];
            $pessoaFisica = $pessoaFisica[0];


            $enderecoJoin = json_decode(DB::table('lista_telefonica')
                ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                ->where('lista_telefonica.id', '=', $idTelefone)
                ->get(), true);

            $endereco = $enderecoJoin[0];


            $sacado = new Agente($pessoaFisica['nome'], $pessoaFisica['cpf'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
            $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');



            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web']."", 10, "0")
            ));

            $msg = str_replace("\n","<br>", $boleto['descricao']);

            $arr = [
                0 => 'Pagar antes da data do vencimento',
                1 => $msg

            ];
            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();
        }else{
            $pessoaJuridica =  json_decode(PessoaJuridica::where([
                ['id', $cliente['pessoa_juridica_id']]
            ])->get(), true);


            $idTelefone = $pessoaJuridica[0]['lista_telefonica_id'];
            $pessoaJuridica = $pessoaJuridica[0];


            $enderecoJoin = json_decode(DB::table('lista_telefonica')
                ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                ->where('lista_telefonica.id', '=', $idTelefone)
                ->get(), true);

            $endereco = $enderecoJoin[0];


            $sacado = new Agente($pessoaJuridica['fantasia'], $pessoaJuridica['cnpj'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
            $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');



            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web']."", 10, "0")
            ));

            $msg = str_replace("\n","<br>", $boleto['descricao']);

            $arr = [
                0 => 'Pagar antes da data do vencimento',
                1 => $msg

            ];
            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();

        }


    }

    public function boletosMassaView(){

        $dados['data'] = "";
        return view('massa', $dados);
    }

    //GERAR BOLETOS EM MASSA, IMPRIMIR TODOS OS BOLETOS, DE 200 EM 200
    public function boletosEmMassa(Request $request){

        $search =  $request->get('data');
        //dd($search);


        //dd($search);
        $boleto = Financeiros::where([
            ['reg_lancamento', 'like', $search . '%']
        ])->paginate(200);


        $boleto->withPath("/boletos/massa/buscar?data=".$search);
        $dados["boletos"] = $boleto;
        $dados['data'] = $search;


        //dd($dados["boletos"]);
        return view('massa', $dados);


    }

    public function imprimirMassa(Request $request){

        if($request->get('imprimirTodos') == null){

            return redirect()->back()->with('erro', "Nenhum boleto encontrado");
        }
        $boletoID = explode(',', $request->get('imprimirTodos'));

        foreach($boletoID as $id){

            $boleto = json_decode(Financeiros::where([
                ['id', $id]
            ])->get(), true);


            $cliente = json_decode(Clientes::where(
                'id',
                $boleto[0]['cliente_id_web']
            )->get(), true);
            $boleto = $boleto[0];
            $cliente = $cliente[0];
            if(isset($cliente['pessoa_fisica_id'])){

                $pessoaFisica =  json_decode(Pessoa_fisica::where([
                    ['id', $cliente['pessoa_fisica_id']]
                ])->get(), true);


                $idTelefone = $pessoaFisica[0]['lista_telefonica_id'];
                $pessoaFisica = $pessoaFisica[0];


                $enderecoJoin = json_decode(DB::table('lista_telefonica')
                    ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                    ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                    ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                    ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                    ->where('lista_telefonica.id', '=', $idTelefone)
                    ->get(), true);

                $endereco = $enderecoJoin[0];


                $sacado = new Agente($pessoaFisica['nome'], $pessoaFisica['cpf'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
                $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');



                $boletoSantander = new Santander(array(
                    // Parâmetros obrigatórios

                    'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                    'valor' => $boleto['reg_valor'],
                    'sequencial' => $boleto['id'], // Para gerar o nosso número
                    'sacado' => $sacado,
                    'cedente' => $cedente,
                    'agencia' => 4543, // Até 4 dígitos
                    'carteira' => 101,
                    'conta' => 1300398, // Até 8 dígitos
                    'convenio' => 9818596, // 4, 6 ou 7 dígitos
                ));

                $msg = str_replace("\n","<br>", $boleto['descricao']);

                $arr = [
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg

                ];
                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput() . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
            }else{
                $pessoaJuridica =  json_decode(PessoaJuridica::where([
                    ['id', $cliente['pessoa_juridica_id']]
                ])->get(), true);


                $idTelefone = $pessoaJuridica[0]['lista_telefonica_id'];
                $pessoaJuridica = $pessoaJuridica[0];


                $enderecoJoin = json_decode(DB::table('lista_telefonica')
                    ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                    ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                    ->join('estados', 'cidades.estados_cod_estados', '=', 'estados.id')
                    ->select('lista_telefonica.*', 'catalogo_enderecos.*', 'cidades.*', 'estados.*')
                    ->where('lista_telefonica.id', '=', $idTelefone)
                    ->get(), true);

                $endereco = $enderecoJoin[0];


                $sacado = new Agente($pessoaJuridica['fantasia'], $pessoaJuridica['cnpj'], $endereco['endereco'], $endereco['cep'], $endereco['cidade'], $endereco['sigla']);
                $cedente = new Agente('INTELNET TELECOM MULTIMIDIA LTDA', '07.692.425/0001-58', 'Av. Assis Chateubrind', '59215-000', 'Nova Cruz', 'RN');



                $boletoSantander = new Santander(array(
                    // Parâmetros obrigatórios

                    'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                    'valor' => $boleto['reg_valor'],
                    'sequencial' => $boleto['id'], // Para gerar o nosso número
                    'sacado' => $sacado,
                    'cedente' => $cedente,
                    'agencia' => 4543, // Até 4 dígitos
                    'carteira' => 101,
                    'conta' => 1300398, // Até 8 dígitos
                    'convenio' => 9818596, // 4, 6 ou 7 dígitos
                ));

                $msg = str_replace("\n","<br>", $boleto['descricao']);

                $arr = [
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg

                ];
                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput() . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br>';

            }
        }
    }


    public function baixaBoleto(Request $request){

        $validated = $request->validate([
            'id' => 'required|integer',
            'valor_pago' => 'required|numeric',
            'vencimento' => 'required',
            'mes_referencia' => 'required',
            'ano_referencia' => 'required',
            'reg_valor' => 'required|numeric',
            'mensalidade' => 'required|integer'
        ]);

         $id = $request->get('id');

         $boleto = Financeiros::find($id);

         $boleto->reg_baixa = 2;
         $boleto->bx_valor_pago = $request->get('valor_pago');
         $boleto->bx_pagamento = date('Y-m-d ');
         $boleto->reg_vencimento = $request->get('vencimento');
         $boleto->mes_referencia = $request->get('mes_referencia');
         $boleto->ano_referencia = $request->get('ano_referencia');
         $boleto->reg_valor = $request->get('reg_valor');
         $boleto->mensalidade = $request->get('mensalidade');

         $boleto->timestamps = false;
         $boleto->save();

         return redirect()->back()->with(['success' => 'Baixa realizada com sucesso!']);
    }



    public function liberarCliente($idCliente){

        $cliente = Clientes::find($idCliente);

        if($cliente->status_id != 14){
            return redirect()->back()->with('erro', "Cliente já está liberado");
        }else{
            $dataAtual = date('Y-m-d');
            $intervaloBloqueio = $cliente->bloqueio_intervalo;

            $financeiro = Financeiros::where([
                ['cliente_id_web', $cliente->id],
                ['mensalidade', 1],
                ['reg_deleted', 0],
                ['reg_baixa', 0],
                ['reg_vencimento', '<', $dataAtual],
                ['DATEDIFF(NOW(), reg_vencimento)', '>', $intervaloBloqueio]
            ])
            ->orWhere([
                ['cliente_id_web', $cliente->id],
                ['reg_historico', 'like', 'MENSALIDADE'.'%'],
                ['reg_deleted', 0],
                ['reg_baixa', 0],
                ['reg_vencimento', '<', $dataAtual],
                ['DATEDIFF(NOW(), reg_vencimento)', '>', $intervaloBloqueio]
            ])->orderBy('reg_vencimento', 'ASC')->paginate(1);


            if($financeiro->total()){

                $cliente->status_id = 2;

                $planosContratado = PlanoContratado::where([
                    ['clientes_id', $cliente->id],
                    ['status_id', 19]
                ])->get();


                foreach($planosContratado as $plano){

                    $plano->status_id = 20;

                    $adminLogin = LoginAdmin::find($plano->login_radius_id);
                    $adminLogin->enable = 1;

                    $loginRadius = LoginRadius::where([
                        ['username', $adminLogin->username]
                    ])->get();

                    $loginRadius->enable = 1;
                    $loginRadius->value = "Accept";
                    //verificar como funciona essa questão do radius

                }
             }
        }
    }
}
