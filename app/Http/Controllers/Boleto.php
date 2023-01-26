<?php

namespace App\Http\Controllers;


include('openboleto/autoloader.php');

use App\Models\Pessoa_fisica;
use OpenBoleto\Agente;
use OpenBoleto\Banco\Santander;
use App\Models\Clientes;
use App\Models\Financeiros;
use App\Models\LoginAdmin;
use App\Models\LoginRadius;
use App\Models\PessoaJuridica;
use App\Models\PlanoContratado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DateTime;


use Illuminate\Http\Request;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\OutPut;
use Pix;

class Boleto extends Controller
{
    public function erroAutenticado()
    {
        if (session()->has('nome')) {

            return false;
        } else {
            return true;
        }
    }

    private function gerarToken()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://pix.santander.com.br/sandbox/oauth/token?grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "client_id=18AxUqXJBuZlRA1FgWc8AeqnTrdgbhGY&client_secret=DSUGlKJk4EAawDGh"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        //  dd(json_decode($server_output), true);
        $server_output = json_decode($server_output, true);
        return $server_output['access_token'];
    }

    public function buscarCobranca()
    {
        $authorization = "Authorization: Bearer " . $this->gerarToken();
        //dd($authorization);
        $txid = "cd1fe328-c875-4812-85a6-f233ae41b662";
        $url = "https://pix.santander.com.br/api/v1/sandbox/cob/$txid";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function index($data = null)
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        } else {
            if ($data != null) {

                return view('boletoIndex', $data);
            } else {
                return view('boletoIndex');
            }
        }
    }

    public function buscarCliente(Request $request)
    {

        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }
        $search =  $request->get('campoBusca');
        $flag = $request->get('flag');




        if ($flag == "nome" || $flag == "cpf") {

            if ($flag == "nome") {
                $pessoaFisica =  Pessoa_fisica::where([
                    ['nome', 'like', $search . '%']
                ])->orderBy('nome', 'asc')->paginate(10);

                $data['clientesBusca'] =  $pessoaFisica->withPath("/boletos/clientes?flag=" . $flag . "&campoBusca=" . $search);
            } else {
                $search = somentoNumeroCpfOuCnpj($search);
                $pessoaFisica =  Pessoa_fisica::where([
                    ['cpf', $search]
                ])->orderBy('nome', 'asc')->paginate(10);


                $data['clientesBusca'] = $pessoaFisica->withPath("/boletos/clientes?flag=" . $flag . "&campoBusca=" . $search);
            }
            $data['flag'] = 'cpf';
        } elseif ($flag == "fantasia" || $flag == "cnpj") {
            if ($flag == "fantasia") {
                $pessoaJuridica =  PessoaJuridica::where([
                    ['fantasia', 'like', '%' . $search . '%']
                ])->orderBy('nome', 'asc')->paginate(10);

                $data['clientesBusca'] = $pessoaJuridica->withPath("/boletos/clientes?flag=" . $flag . "&campoBusca=" . $search);
            } else {
                $search = somentoNumeroCpfOuCnpj($search);
                $pessoaJuridica =  PessoaJuridica::where([
                    ['cnpj', $search]
                ])->orderBy('fantasia', 'asc')->paginate(10);


                $data['clientesBusca'] = $pessoaJuridica->withPath("/boletos/clientes?flag=" . $flag . "&campoBusca=" . $search);
            }
            $data['flag'] = 'cnpj';
        }

        return view('boletoIndex', $data);
    }

    public function listarBoletos($id = null, $flag = null)
    {


        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }

        if ($flag == "cpf") {


            $pessoaFisica =  json_decode(Pessoa_fisica::where([
                ['id', $id]
            ])->get(), true);


            $pessoaFisica = $pessoaFisica[0];

            $cliente = json_decode(Clientes::where(
                'pessoa_fisica_id',
                $id
            )->get(), true);

            if ($cliente == []) {
                return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
            }
            $cliente = $cliente[0];

            $pessoaFisica['idCliente'] = $cliente['id'];

            $financeiro = Financeiros::where([
                ['cliente_id_web', $cliente['id']]
            ])->orderBy('reg_vencimento', 'desc')->paginate(10);

            foreach ($financeiro as $item) {

                if ($item->reg_deleted != 1) {
                    $valorExtra = Financeiros::valoresExtra($item['id']);
                    $descontoBoleto =  $valorExtra['desconto'];
                    $acrescimoBoleto =  $valorExtra['acrescimo'];

                    $item->reg_valor_total = ($item->reg_valor + $acrescimoBoleto) - $descontoBoleto;
                    //dd($item);

                    $item->desconto = $descontoBoleto;
                    $item->acrescimo = $acrescimoBoleto;
                    $item->linhaDigitavel = $this->pegarLinhaDigitavel($item['id']);
                }
            }


            $data['boletos'] = $financeiro;
            $data['cliente'] = $pessoaFisica;
            return view('boletoIndex', $data);
        } else if ($flag == "cnpj") {



            $pessoaJuridica =  json_decode(PessoaJuridica::where([
                ['id', $id]
            ])->get(), true);


            $pessoaJuridica = $pessoaJuridica[0];

            $cliente = json_decode(Clientes::where(
                'pessoa_juridica_id',
                $pessoaJuridica['id']
            )->get(), true);

            if ($cliente == []) {
                return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
            }

            $cliente = $cliente[0];

            $pessoaJuridica['idCliente'] = $cliente['id'];
            $financeiro = Financeiros::where([
                ['cliente_id_web', $cliente['id']]
            ])->orderBy('reg_vencimento', 'desc')->paginate(10);

            foreach ($financeiro as $item) {

                if ($item->reg_deleted != 1) {
                    $valorExtra = Financeiros::valoresExtra($item['id']);
                    $descontoBoleto =  $valorExtra['desconto'];
                    $acrescimoBoleto =  $valorExtra['acrescimo'];

                    $item->reg_valor_total = ($item->reg_valor + $acrescimoBoleto) - $descontoBoleto;


                    $item->desconto = $descontoBoleto;
                    $item->acrescimo = $acrescimoBoleto;

                    $item->linhaDigitavel = $this->pegarLinhaDigitavel($item['id']);
                }
            }

            $pessoaJuridica['nome'] = $pessoaJuridica['fantasia'];
            $data['boletos'] = $financeiro;
            $data['cliente'] = $pessoaJuridica;

            //dd($financeiro);

            return view('boletoIndex', $data);
        }
    }

    public function pegarLinhaDigitavel($id = null)
    {
        if ($this->erroAutenticado()) {
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
        if ($cliente == []) {
            return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
        }
        $cliente = $cliente[0];

        if (isset($cliente['pessoa_fisica_id'])) {

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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];
            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;
            //dd($descontoBoleto);


            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0")
            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);

            $arr = [
                0 => 'Pagar antes da data do vencimento',
                1 => $msg

            ];
            $boletoSantander->setInstrucoes($arr);
            //dd($boleto);
            return $boletoSantander->getLinhaDigitavel();
        } else {
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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];

            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;


            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0")
            ));
            //dd($boleto);
            return $boletoSantander->getLinhaDigitavel();
        }
    }

    public function gerarQrCode()
    {


        $cobranca = $this->buscarCobranca();
        $payload = (new Pix)->setChavePix($cobranca->location)
            ->setDescricao('Teste pix')
            ->setNomeTitular('Intelnet Telecom')
            ->setCidadeTitular('Nova Cruz')
            ->setTxid($cobranca->txid)
            ->setValor(doubleval($cobranca->valor->original));


        $stringPayload = $payload->gerarPayload();

        $qrCode = new QrCode($stringPayload);

        $image =  (new OutPut\Png)->output($qrCode, 100);

        return $image;
    }

    public function emitirBoletoUnitario($id = null)
    {
        if ($this->erroAutenticado()) {
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
        if ($cliente == []) {
            return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
        }
        $cliente = $cliente[0];

        if (isset($cliente['pessoa_fisica_id'])) {

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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];

            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;


            $image = $this->gerarQrCode();
            $image = base64_encode($image);
            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0"),

            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);



            //dd(base64_encode($image));


            $arr = [
                0 => 'Pagar antes da data do vencimento',
                1 => $msg,
            ];

            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();


        } else {
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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];


            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;

            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0")
            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);

            $arr = [
                0 => 'Pagar antes da data do vencimento',
                1 => $msg

            ];
            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();
        }
    }

    public function emitirBoletoUnitarioComPix($id = null)
    {
        if ($this->erroAutenticado()) {
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
        if ($cliente == []) {
            return redirect()->back()->with('erroCliente', 'Cliente não encontrado');
        }
        $cliente = $cliente[0];

        if (isset($cliente['pessoa_fisica_id'])) {

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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];

            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;



            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0"),

            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);



            //dd(base64_encode($image));
            $image = $this->gerarQrCode();
            $image = base64_encode($image);

            $arr = [
                'pix'=>"<img width=100 height=100 src='data:image/png;base64, $image'>",
                0 => 'Pagar antes da data do vencimento',
                1 => $msg,
            ];

            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();


        } else {
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

            $valorExtra = Financeiros::valoresExtra($boleto['id']);
            $descontoBoleto =  $valorExtra['desconto'];
            $acrescimoBoleto =  $valorExtra['acrescimo'];


            $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;

            $boletoSantander = new Santander(array(
                // Parâmetros obrigatórios

                'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                'valor' => $boleto['reg_valor_total'],
                'sequencial' => $boleto['id'], // Para gerar o nosso número
                'sacado' => $sacado,
                'cedente' => $cedente,
                'agencia' => 4543, // Até 4 dígitos
                'carteira' => 101,
                'conta' => 1300398, // Até 8 dígitos
                'convenio' => 9818596, // 4, 6 ou 7 dígitos
                'numeroDocumento' => completarPosicoes($boleto['cliente_id_web'] . "", 10, "0")
            ));

            $msg = str_replace("\n", "<br>", $boleto['descricao']);

            $image = $this->gerarQrCode();
            $image = base64_encode($image);

            $arr = [
                'pix'=>"<img width=100 height=100 src='data:image/png;base64, $image'>",
                0 => 'Pagar antes da data do vencimento',
                1 => $msg,
            ];
            $boletoSantander->setInstrucoes($arr);

            echo $boletoSantander->getOutput();
        }
    }

    public function boletosMassaView()
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }

        $dados['data'] = "";
        return view('massa', $dados);
    }

    //GERAR BOLETOS EM MASSA, IMPRIMIR TODOS OS BOLETOS, DE 200 EM 200
    public function boletosEmMassa(Request $request)
    {
        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }

        $search =  $request->get('data');

        $boletos = DB::table('financeiros')
            ->join('clientes', 'financeiros.cliente_id_web', '=', 'clientes.id')
            ->select('clientes.tipo', 'clientes.pessoa_fisica_id', 'clientes.pessoa_juridica_id',  'financeiros.*')
            ->where('financeiros.reg_lancamento', 'like', $search . '%')
            ->get();



        $boletos = DB::table('financeiros')
            ->join('clientes', 'financeiros.cliente_id_web', '=', 'clientes.id')
            ->select('clientes.tipo', 'clientes.pessoa_fisica_id', 'clientes.pessoa_juridica_id',  'financeiros.*')
            ->where('financeiros.reg_lancamento', 'like', $search . '%')
            ->get();

        $arr = [];
        foreach ($boletos as $item) {

            if ($item->tipo == 'F') {

                $cidade = json_decode(DB::table('pessoa_fisica')
                    ->join('lista_telefonica', 'pessoa_fisica.lista_telefonica_id', '=', 'lista_telefonica.id')
                    ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                    ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                    ->select('cidades.cidade')
                    ->where('pessoa_fisica.id', '=', $item->pessoa_fisica_id)
                    ->get(), true);

                $item->cidade = $cidade[0]['cidade'];

                $valorExtra = Financeiros::valoresExtra($item->id);
                $descontoBoleto =  $valorExtra['desconto'];
                $acrescimoBoleto =  $valorExtra['acrescimo'];

                $item->reg_valor_total = ($item->reg_valor + $acrescimoBoleto) - $descontoBoleto;
            } else {

                $cidade = json_decode(DB::table('pessoa_juridica')
                    ->join('lista_telefonica', 'pessoa_juridica.lista_telefonica_id', '=', 'lista_telefonica.id')
                    ->join('catalogo_enderecos', 'lista_telefonica.catalogo_enderecos_id', '=', 'catalogo_enderecos.id')
                    ->join('cidades', 'catalogo_enderecos.cidades_id', '=', 'cidades.id')
                    ->select('cidades.cidade')
                    ->where('pessoa_juridica.id', '=', $item->pessoa_juridica_id)
                    ->get(), true);

                $item->cidade = $cidade[0]['cidade'];

                $valorExtra = Financeiros::valoresExtra($item->id);
                $descontoBoleto =  $valorExtra['desconto'];
                $acrescimoBoleto =  $valorExtra['acrescimo'];

                $item->reg_valor_total = ($item->reg_valor + $acrescimoBoleto) - $descontoBoleto;
            }
        }

        $boleto = paginate($boletos->sortBy('cidade'), 20);



        $boleto->withPath("/boletos/massa/buscar?data=" . $search);
        $dados["boletos"] = $boleto;
        $dados['data'] = $search;


        //dd($dados["boletos"]);
        return view('massa', $dados);
    }

    public function imprimirMassa(Request $request)
    {

        if ($this->erroAutenticado()) {
            return redirect()->route('index');
        }


        $boletoID = explode(',', $request->get('imprimirTodos'));

        foreach ($boletoID as $id) {


            $boleto = json_decode(Financeiros::where([
                ['id', $id]
            ])->get(), true);

            $cliente = json_decode(Clientes::where(
                'id',
                $boleto[0]['cliente_id_web']
            )->get(), true);
            $boleto = $boleto[0];
            $cliente = $cliente[0];
            if (isset($cliente['pessoa_fisica_id'])) {

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

                $valorExtra = Financeiros::valoresExtra($boleto['id']);
                $descontoBoleto =  $valorExtra['desconto'];
                $acrescimoBoleto =  $valorExtra['acrescimo'];

                $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;


                $boletoSantander = new Santander(array(
                    // Parâmetros obrigatórios

                    'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                    'valor' => $boleto['reg_valor_total'],
                    'sequencial' => $boleto['id'], // Para gerar o nosso número
                    'sacado' => $sacado,
                    'cedente' => $cedente,
                    'agencia' => 4543, // Até 4 dígitos
                    'carteira' => 101,
                    'conta' => 1300398, // Até 8 dígitos
                    'convenio' => 9818596, // 4, 6 ou 7 dígitos
                ));

                $msg = str_replace("\n", "<br>", $boleto['descricao']);

                $arr = [
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg

                ];
                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput() . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
            } else {
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

                $valorExtra = Financeiros::valoresExtra($boleto['id']);
                $descontoBoleto =  $valorExtra['desconto'];
                $acrescimoBoleto =  $valorExtra['acrescimo'];

                $boleto['reg_valor_total'] = ($boleto['reg_valor'] + $acrescimoBoleto) - $descontoBoleto;

                $boletoSantander = new Santander(array(
                    // Parâmetros obrigatórios

                    'dataVencimento' => new DateTime($boleto['reg_vencimento']),
                    'valor' => $boleto['reg_valor_total'],
                    'sequencial' => $boleto['id'], // Para gerar o nosso número
                    'sacado' => $sacado,
                    'cedente' => $cedente,
                    'agencia' => 4543, // Até 4 dígitos
                    'carteira' => 101,
                    'conta' => 1300398, // Até 8 dígitos
                    'convenio' => 9818596, // 4, 6 ou 7 dígitos
                ));

                $msg = str_replace("\n", "<br>", $boleto['descricao']);

                $arr = [
                    0 => 'Pagar antes da data do vencimento',
                    1 => $msg

                ];
                $boletoSantander->setInstrucoes($arr);

                echo $boletoSantander->getOutput() . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
            }
        }
    }

    public function baixaBoleto(Request $request)
    {

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

    public function liberarCliente($idCliente)
    {

        $cliente = Clientes::find($idCliente);

        if ($cliente->status_id == 2) {
            return redirect()->back()->with('erro', "Cliente já está liberado");
        } else {
            $dataAtual = date('Y-m-d');
            $intervaloBloqueio = $cliente->bloqueio_intervalo;

            $financeiro = Financeiros::where([
                ['cliente_id_web', $cliente->id],
                ['mensalidade', 1],
                ['reg_deleted', 0],
                ['reg_baixa', 0],
                ['reg_vencimento', '<', $dataAtual]

            ])
                ->orWhere([
                    ['cliente_id_web', $cliente->id],
                    ['reg_historico', 'like', 'MENSALIDADE' . '%'],
                    ['reg_deleted', 0],
                    ['reg_baixa', 0],
                    ['reg_vencimento', '<', $dataAtual]

                ])
                ->orderBy('reg_vencimento', 'asc')->paginate(1);

            if ($financeiro->total() == 0) {


                $cliente->status_id = 2;

                $planosContratado = PlanoContratado::where([
                    ['clientes_id', $cliente->id],
                    ['status_id', 19]
                ])->get();



                foreach ($planosContratado as $plano) {

                    $plano->status_id = 20;

                    $adminLogin = LoginAdmin::find($plano->login_radius_id);
                    $adminLogin->enable = 1;

                    $loginRadius1 = LoginRadius::where([
                        ['username', $adminLogin->username]
                    ])->first();



                    if ($loginRadius1 != null) {
                        $loginRadius1->enable = 1;
                        $loginRadius1->save();
                    }

                    //verificar como funciona essa questão do radius
                    $loginRadius = LoginRadius::where([
                        ['login_id', $adminLogin->id],
                        ['attribute', 'Auth-Type']
                    ])->first();

                    if ($loginRadius != null) {

                        $loginRadius->value = 'Accept';
                        $loginRadius->save();
                    }



                    $plano->save();
                    $adminLogin->save();
                }
            }
        }
    }

    public function liberarPlanoPendencia($idCliente)
    {

        $cliente = Clientes::find($idCliente);

        $planosContratado = PlanoContratado::where([
            ['clientes_id', $idCliente],
            ['status_id', 19]
        ])
            ->orWhere([
                ['clientes_id', $idCliente],
                ['status_id', 14]
            ])
            ->get();

        $dataAtual = date('Y-m-d');

        foreach ($planosContratado as $plano) {

            $intervaloBloqueio  = is_null($plano->bloqueio_intervalo) ?
                $cliente->bloqueio_intervalo : $plano->bloqueio_intervalo;

            $financeiro = Financeiros::where([
                ['plano_contratado_id', $plano->id],
                ['mensalidade', 1],
                ['reg_deleted', 0],
                ['reg_baixa', 0],
                ['reg_vencimento', '<', $dataAtual]

            ])
                ->orderBy('reg_vencimento', 'asc')->paginate(1);

            if ($financeiro->total() == 0) {
                $plano->status_id = 20;

                $adminLogin = LoginAdmin::find($plano->login_radius_id);

                $adminLogin->enable = 1;

                $loginRadius1 = LoginRadius::where([
                    ['username', $adminLogin->username]
                ])->first();



                if ($loginRadius1 != null) {
                    $loginRadius1->enable = 1;
                    $loginRadius1->save();
                }

                //verificar como funciona essa questão do radius
                $loginRadius = LoginRadius::where([
                    ['login_id', $adminLogin->id],
                    ['attribute', 'Auth-Type']
                ])->first();

                if ($loginRadius != null) {

                    $loginRadius->value = 'Accept';
                    $loginRadius->save();
                }

                $plano->save();
                $adminLogin->save();
            }
        }

        $verificarPlanoContratado = PlanoContratado::where([
            ['clientes_id', $idCliente],
            ['status_id', 19]
        ])->orWhere([
            ['clientes_id', $idCliente],
            ['status_id', 14]
        ])
            ->get();

        if ($verificarPlanoContratado->total() == 0 && $cliente->status_id != 1) {
            $cliente->status_id = 20;
            $this->atualizaConfigPadraoCliente($cliente->id);
        }
    }

    public function atualizaConfigPadraoPlano($id)
    {

        $plano = PlanoContratado::find($id);
        $plano->num_liberacao_temporaria = 0;
        $plano->bloqueio_intervalo = 15;
        $plano->save();
    }

    public function atualizaConfigPadraoCliente($id)
    {
        $cliente = Clientes::find($id);
        $cliente->num_liberacao_temporaria = 0;
        $cliente->bloqueio_intervalo = 15;
        $cliente->data_bloqueio = null;
        $cliente->save();
    }
}
