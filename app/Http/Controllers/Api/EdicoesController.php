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
        $this->middleware('jwt-auth', ['except' => ['show']]);
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
                'message' => 'Edições Listados com sucesso',
                'numrow' => $rowCount,
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'data' => $data
            ]
        );
    }

    public function store(Request $request)
    {

        if ($this->Usuario->hasRole(['editor', 'editor_chefe'])) {

            DB::beginTransaction();

            try {

                $validator = Validator::make($request->all(), [
                    'edicao' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => true, 'message' => $validator->errors()->all()]);
                } else {

                    /* Trazer todos os artigos pertencentes a uma determinada edição*/

                    $edicao = $request->edicao;
                    $query = Artigo::where('status', '=', 'PUBLICAR');
                    $query->whereHas('edicoes', function ($table) use ($edicao) {
                        $table->where('numero', '=', $edicao);
                    });

                    foreach($query->get() as $artigo) {

                        $artigo->status = 'PUBLICADO';
                        $artigo->save();

                    }

                    $queryEdicao = Edicao::where('numero', '=', $edicao)->first();
                    $queryEdicao->status = 1;
                    $queryEdicao->save();


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
                        'message' => 'Erro ao completar a criação do artigo' . $e->getMessage(),
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
}
