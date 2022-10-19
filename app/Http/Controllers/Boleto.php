<?php

namespace App\Http\Controllers;


include('openboleto/autoloader.php');

use App\Models\Pessoa_fisica;
use openBoleto\Agente;
use openBoleto\Banco\Santander;
use App\Models\Clientes;
use App\Models\Financeiros;
use App\Models\PessoaJuridica;
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
}
