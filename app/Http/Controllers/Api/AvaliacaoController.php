<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AvaliacaoController extends Controller
{
    //

    public function __construct()
    {

        $this->middleware('jwt-auth');
        $this->Usuario = Auth::user();
    }


    public function show(Request $request)
    {

        $usuario =$request->query('user_id');
        $avaliacao = Avaliacao::where('user_id', '=', $usuario)
        ->where('article_id', '=', $request->query('article_id'));

        return response()->json(
            [
                'status' => true,
                'message' => 'Revisão listada com sucesso',
                'numrow' => 1,
                'pageSize' => 0,
                'currentPage' => 1,
                'data' => $avaliacao->get(),
            ],
            200
        );
        // if ($avaliacao)
        //     return Response()->json($avaliacao);

        // return Response()->json(['msg' => 'Avaliação não encontrada']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function salvarAvalicao(Request $request)
    {
        //

        try {
            $validator = Validator::make($request->all(), [
                'artigo_id' => 'required',
                'user_id' => 'required',
                'titulo' => 'required',
                'resumo' => 'required',
                'contexto' => 'required',
                'objectivo' => 'required',
                'tema_original' => 'required',
                'fundamento' => 'required',
                'conhecimento' => 'required',
                'problema' => 'required',
                'procedimento' => 'required',
                'resultado' => 'required',
                'discucao' => 'required',
                'objectivo_alcansado' => 'required',
                'contribuicao' => 'required',
                'limitacao' => 'required',
                'nova_direcao' => 'required',
                'consideracao' => 'required',
                'conclusao' => 'required',
                'linguagem_cientifica' => 'required',
                'tabela_figura' => 'required',
                'referencia' => 'required',
                'biografia_referencia' => 'required',
                'normas' => 'required',
                'ditame' => 'required',
                'flcorrecoes' => 'required',
                'descricao' => 'sometimes'
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => true,
                        'messagem' => $validator->errors()->all()
                    ]
                );
            } else {

                $avaliacao = new Avaliacao();
                $avaliacao->artigos_id = $request->artigo_id;
                $avaliacao->user_id = $request->user_id;
                // $avaliacao->descricao = $request->descricao;
                $avaliacao->titulo = $request->titulo;
                $avaliacao->resumo = $request->resumo;
                $avaliacao->contexto = $request->contexto;
                $avaliacao->objectivo = $request->objectivo;
                $avaliacao->tema_original = $request->tema_original;
                $avaliacao->fundamento = $request->fundamento;
                $avaliacao->conhecimento = $request->conhecimento;
                $avaliacao->problema = $request->problema;
                $avaliacao->procedimento = $request->procedimento;
                $avaliacao->resultado = $request->resultado;
                $avaliacao->discucao = $request->discucao;
                $avaliacao->objectivo_alcansado = $request->objectivo_alcansado;
                $avaliacao->contribuicao = $request->contribuicao;
                $avaliacao->limitacao = $request->limitacao;
                $avaliacao->nova_direcao = $request->nova_direcao;
                $avaliacao->consideracao = $request->consideracao;
                $avaliacao->conclusao = $request->conclusao;
                $avaliacao->linguagem_cientifica = $request->linguagem_cientifica;
                $avaliacao->tabela_figura = $request->tabela_figura;
                $avaliacao->referencia = $request->referencia;
                $avaliacao->biografia_referencia = $request->biografia_referencia;
                $avaliacao->normas = $request->normas;
                $avaliacao->ditame = $request->ditame;
                $avaliacao->flcorrecoes = $request->flcorrecoes;
                $avaliacao->descricao = ($request->descricao) ? $request->descricao : '';
                $avaliacao->save();

                return Response()->json([
                    'status' => true,
                    'message' => 'Revisão do artigo foi feita com sucesso',
                    'numrow' => 1,
                    'data' => $avaliacao->id
                ]);
            }
        } catch (\Exception $e) {
            return Response()->json(['msg' => 'Erro: ' . $e->getMesage()]);
        }
    }
}
