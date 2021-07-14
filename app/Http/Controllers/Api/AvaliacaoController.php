<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;

class AvaliacaoController extends Controller
{
    //

    public function __construct()
    {

        $this->middleware('jwt-auth',  ['except' => ['show', 'detalhe', 'showPublich']]);
        $this->Usuario = Auth::user();
    }


    public function show(Request $request)
    {
        $pageSize = (!is_null($request->query('pageSize'))) ? $request->query('pageSize') :  8;
        $lang = (!is_null($request->query('lang'))) ? $request->query('lang') :  'pt';

        /* Criar a paginação de resultados */
        $currentPage = (!is_null($request->query('currentPage'))) ? $request->query('currentPage') :  1;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Avaliacao::where('artigos_id', '=', $request->query('artigos_id'));

        if( $this->Usuario->hasRole(['editor', 'editor_chefe'])){

        } else if( $this->Usuario->hasRole(['revisor'])){
            $query->where('users_id', '=', $this->Usuario->id);
        }


        if($request->query('users_id')){
            $query->where('users_id', '=', $request->query('users_id'));
        }



        $query->with(['users']);

        $rowCount = count($query->get());
        $query->orderBy('id', 'desc')->paginate($pageSize);

        return response()->json(
            [
                'status' => true,
                'message' => 'Revisão listada com sucesso!',
                'numrow' => $rowCount,
                'pageSize' => 0,
                'currentPage' => 1,
                'data' => $query->get(),
            ],
            200
        );
        // if ($avaliacao)
        //     return Response()->json($avaliacao);

        // return Response()->json(['msg' => 'Avaliação não encontrada']);
    }

    public function detalhe($id){

        $query = Avaliacao::where('id', '=', $id);
        if( $this->Usuario->hasRole(['editor', 'editor_chefe'])){

        } else if( $this->Usuario->hasRole(['revisor'])){
            $query->where('users_id', '=', $this->Usuario->id);
        }

        $query->with(['users']);

        return response()->json(
            [
                'status' => true,
                'message' => 'Revisão listada com sucesso!',
                'numrow' => 1,
                'pageSize' => 1,
                'currentPage' => 1,
                'data' => $query->get(),
            ],
            200
        );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        try {
            $validator = Validator::make($request->all(), [
                'artigos_id' => 'required',
                'users_id' => 'required',
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
                $avaliacao->artigos_id = $request->artigos_id;
                $avaliacao->users_id = $request->users_id;
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
