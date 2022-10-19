<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

class LoginContoller extends Controller
{
    public function index($erro = null)
    {
            return view('login');

    }


    public function logar(Request $request)
    {
        $usuario =  $request->get('usuario');
        $senha = $request->get('senha');

        $credenciais = json_decode(Login::where([
            ['user', $usuario]
        ])->get(), true);
        $credenciais = $credenciais[0];

        //dd($credenciais);
        if ($credenciais['grupo_users_id'] == 9 || $credenciais['grupo_users_id'] == 1) {
            if (password_verify($senha, $credenciais['pass'])) {

                $dados = [
                    'id'=>$credenciais['id'],
                    'nome'=>$credenciais['displayName'],
                    'grupo_users_id' => $credenciais['grupo_users_id'],
                ];

                $this->criarSessao($dados);

                if(Session::get('nome')){
                    //return view('remessa');
                    return redirect()->route('remessa');
                }else{
                    return redirect()->route('index');
                }

            } else {


                return redirect()->back()->with('erro', "usuario ou senha incorreta");
            }
        } else {
            return redirect()->back()->with('erro', "Acesso negado!");

        }
    }

    public function criarSessao($dados = null){
        if($dados != null)
            Session::put($dados);



    }

    public function sessionDestroy(){
        Session::flush();

        return redirect()->route('index');
    }
}
