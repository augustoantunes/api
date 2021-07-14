<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\Edicao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Api\Artigo;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stringable;

class EdicoesController extends Controller
{
    private $Usuario;

    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['show', 'store']]);
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

        $query = Edicao::where('id', '>', -1);

        $status = ($request->query('status') == 'true') ? 'true' : (($request->query('status') == 'false') ? 'false' : 'true');

        if ($request->query('all') == 'true' && ($this->Usuario && $this->Usuario->hasRole(['editor', 'editor_chefe']))) {
        } else {

            if ($request->query('all')) {
                $query->where('status', '=', 1);
            }

            if ($status == 'true' && ($request->query('all') != 'true')) {
                $query->where('status', '=', 1);
            }

            if ($status == 'false' && ($request->query('all') != 'true')) {
                if ($this->Usuario && $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
                    $query->where('status', '=', 0);
                } else {
                    $query->where('status', '=', 1);
                }
            }
        }

        $rowCount = count($query->get());
        $data = $query->get();

        return response()->json(
            [
                'status' => true,
                'message' => 'Edições Listadas com sucesso',
                'numrow' => $rowCount,
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'data' => $data
            ]
        );
    }

    public function store(Request $request)
    {

        if (true == true) {
            // if ($this->Usuario->hasRole(['editor', 'editor_chefe'])) {

            DB::beginTransaction();

            try {

                $validator = Validator::make($request->all(), [
                    'edicao' => 'required',
                    'numero' => 'required',
                    'artigos' => ' required'
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => true, 'message' => $validator->errors()->all()]);
                } else {


                    //Pesquisar a edição sumetida e verificar se ela existe antes de actualizala;
                    $edicao = Edicao::find($request->edicao);

                    if ($edicao) {

                        if (!empty($request->artigos)) {

                            $lstIdArtigos = explode(',', $request->artigos);

                            foreach ($lstIdArtigos as $index => $idArtigo) {
                                $artigo = Artigo::find($idArtigo);
                                $artigo->edicoes_id = $edicao->id;
                                $artigo->titulo = $edicao->titulo;
                                $artigo->descricao = $edicao->descricao;
                                // $artigo->capa = $this->uploadFile($request, 'capa');
                                $artigo->status = 'PUBLICADO';
                                $artigo->save();
                            }
                        }
                    }

                    $edicao->status = 1;
                    $edicao->save();

                    // Criação de Nova Edição

                    $numero = intval($request->edicao);
                    $novaEdicao = new Edicao();
                    $novaEdicao->numero = str_pad(($numero + 1), 6, '0', STR_PAD_LEFT);
                    $novaEdicao->status = 0;
                    $novaEdicao->save();

                    // $$item = $this->novaEdicao();


                    DB::commit();

                    return response()->json(
                        [
                            'status' => true,
                            'message' => 'Edição publicada com sucesso!',
                            'numrow' => 1,
                            'data' => ''
                        ],
                        200
                    );
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Erro ao completar a criacao da edicao ' . $e->getMessage(),
                        'numrow' => 0,
                        'data' => null
                    ],
                    200
                );
            }
        } else {

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Voçê não tem permissão para fazer esta requisição',
                    'numrow' => 0,
                    'data' => null
                ],
                200
            );
        }
    }

    public function lstEdicao()
    {


        $result = null;

        if ($this->Usuario && $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
            $edicoes = Edicao::where('status', '=', 0)->orderByDesc('id')->limit(1);
            $result = $edicoes->get()[0];
        }

        return response()->json(
            [
                'status' => false,
                'message' => 'Operação realizada com sucesso!',
                'numrow' => 0,
                'data' => $result
            ],
            200
        );
    }


    private function novaEdicao()
    {

        $list = Edicao::where('status', '=', 1)->orderByDesc('id')->limit(1);
        if ($list->get()) {
            $numero = $list->get()[0]->id + 1;
            $edicao = new Edicao();
            $edicao->numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
            $edicao->status = 0;
            $edicao->save();
        }
    }

    // private function gerarEdicao(){
    //     $list = Edicao::where('status', '=', 1)->orderByDesc('id')->limit(1);
    //     $numero = 1;
    //     if ($list->get()) {
    //         $numero = $list->get()[0]->id + 1;
    //     }
    //     $edicao = new Edicao();
    //     $edicao->numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
    //     $edicao->status = 0;
    //     $edicao->save();
    // }
}
