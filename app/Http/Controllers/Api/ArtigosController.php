<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Artigo;
use App\Models\Api\CategoriasArtigos;
use App\Models\Api\Edicao;
use App\Models\Api\Interveniente;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Lista por Auto, listar revisores, porcategoria
 *
 */
class ArtigosController extends Controller
{

    private $Usuario;
    use UploadsFile;

    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['show', 'detalhe', 'adicionarRevisor']]);
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


        /* Instancia a classe de artigos para se executar uma pesquisa */
        $query = Artigo::where('lang', '=', $lang);

        /* Veirificar se o artigo é publicado ou pendente */
        /* Realiza uma pesquisa no banco de dados por uma string digitada */
        if (!is_null($request->query('search'))) {
            $title = $request->query('search');
            $query->where( function ( Builder $qry) use ($title)  {
                $qry->orWhere('titulo', 'like', '%' . $title . '%');
                $qry->orWhere('subtitulo', 'like', '%' . $title . '%');
                $qry->orWhere('resumo', 'like', '%' . $title . '%');
                $qry->orWhere('referencias', 'like', '%' . $title . '%');
                return  $qry;
            });

        }


        if (!$request->query('status')) {
            if (!($this->Usuario && $this->Usuario->hasRole(['revisor', 'editor', 'editor_chefe']))) {
                $query->where('status', '=', 'PUBLICADO');
            }
            // dd($query);
        } else if ($request->query('status')) {

            if ($this->Usuario && $this->Usuario->hasRole(['revisor', 'editor', 'editor_chefe'])) {
                $query->where('status', '=', $request->query('status'));
            } else {
                $query->where('status', '=', 'PUBLICADO');
            }
        }


        // /* Veirificar se o artigo é publicado ou pendente */
        // if ($request->query('status')) {
        //     if($this->Usuario && $this->Usuario->hasRole(['editor', 'editor_chefe'])){
        //         $query->where('status', '=', $request->query('status'));
        //     }
        // }

        /* Trazer todos os artigos do usuário logado */
        if ($request->query('artigo')) {
            $query->where('id', '=', $request->artigo);
        }

        if ($request->query('me')) {
            $query->whereHas('intervenientes', function ($destaque) {
                $destaque->where('users_id', '=', $this->Usuario->id)
                    ->where('role', '=', 'AUTOR');
            });
        }
        /* Trazer todos os artigos de um determinado Autor*/
        if ($request->query('autor')) {
            $autor_id = $request->query('autor');
            $query->whereHas('intervenientes', function ($destaque) use ($autor_id) {
                $destaque->where('users_id', '=', $autor_id)
                    ->where('role', '=', 'AUTOR');
            });
        }

        /* Trazer todos os artigos de um determinado Revisor*/
        if ($request->query('revisor') && $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
            $autor_id = $request->query('revisor');
            $query->whereHas('intervenientes', function ($destaque) use ($autor_id) {
                $destaque->where('users_id', '=', $autor_id)
                    ->where('role', '=', 'REVISOR');
            });
        }
        /* Trazer todos os artigos de uma determinada Categoria*/
        if ($request->query('categoria')) {
            $categoria = $request->query('categoria');
            $query->whereHas('categorias', function ($table) use ($categoria) {
                $table->where('slug', '=', $categoria);
            });
        }

        /* Trazer todos os artigos pertencentes a uma determinada edição*/
        if ($request->query('edicao')) {
            $edicao = $request->query('edicao');
            $query->whereHas('edicoes', function ($table) use ($edicao, $request) {
                $table->where('numero', '=', $edicao);
                if ($request->query('edicao_status') && $request->query('edicao_status') == false) {
                    $table->where('status', '=', 0);
                } else {
                    $table->where('status', '=', 1);
                }
            });
        }


        $rowCount = count($query->get());
        $query->orderBy('id', 'desc')->paginate($pageSize);

        /* Cria um array para adicionar os resultados da pesquisa */
        $data = [];

        foreach ($query->get() as $artigo) {
            $artigo['autor'] = $artigo->autores()->get();
            if (($request->query('stdRevisor') == true) && $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
                $artigo['revisores'] = $artigo->revisores()->get();
            }
            $artigo['categoria'] = $artigo->categorias()->get();
            array_push($data, $artigo);
        }


        return response()->json(
            [
                'status' => true,
                'message' => 'Artigos Listados com sucesso',
                'numrow' => $rowCount,
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'data' => $data
            ]
        );
    }

    // * Traz todos os artigos de um revisar logado se  o usuário logado admin tragaz todos os artigos
    public function artigoPorRevisar(Request $request)
    {
        $pageSize = (!is_null($request->query('pageSize'))) ? $request->query('pageSize') :  8;
        $lang = (!is_null($request->query('lang'))) ? $request->query('lang') :  'pt';

        /* Criar a paginação de resultados */
        $currentPage = (!is_null($request->query('currentPage'))) ? $request->query('currentPage') :  1;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });


        /* Instancia a classe de artigos para se executar uma pesquisa */
        $query = Artigo::where('lang', '=', $lang);
        $query->where('status', '=', 'SUBMISSAO');


        /* Trazer todos os artigos de um determinado Revisor */
        if ($this->Usuario->hasRole(['editor', 'editor_chefe'])) {
        } else if ($this->Usuario->hasRole(['revisor'])) {
            $query->whereHas('intervenientes', function ($destaque) {
                $destaque->where('users_id', '=', $this->Usuario->id)
                    ->where('role', '=', 'REVISOR');
            });
        }


        $rowCount = count($query->get());
        $query->orderBy('id', 'desc')->paginate($pageSize);

        /* Cria um array para adicionar os resultados da pesquisa */
        $data = [];

        foreach ($query->get() as $artigo) {
            $artigo['autor'] = $artigo->autores()->get();
            $artigo['categoria'] = $artigo->categorias()->get();
            array_push($data, $artigo);
        }


        return response()->json(
            [
                'status' => true,
                'message' => 'Artigos por revisar listados com sucesso',
                'numrow' => $rowCount,
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'data' => $data
            ]
        );
    }

    public function detalhe($id)
    {


        if ($id) {
            $artigo = Artigo::where('id', '=', $id)->with(['autores', 'categorias']);
            if ($this->Usuario &&  $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
                $artigo->with(['revisores']);
            }

            $artigo->with(['autores','edicoes']);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Artigos Listados com sucesso',
                    'numrow' => 1,
                    'pageSize' => 1,
                    'currentPage' => 1,
                    'data' => $artigo->get()
                ]
            );
        }
    }




    public function store(Request $request)
    {

        if ($this->Usuario->hasPermission('artigo-create')) {

            DB::beginTransaction();

            try {

                $validator = Validator::make($request->all(), [
                    'titulo' => 'required',
                    'subtitulo' => 'sometimes',
                    'resumo' => 'required',
                    'referencias' => 'required',
                    'categoria' => 'required',
                    'imagem' => 'sometimes',
                    'file' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => true, 'message' => $validator->errors()->all()]);
                } else {

                    $edicao = $this->carregarEdicao();

                    $artigo = new Artigo();
                    $artigo->titulo = $request->titulo;
                    $artigo->status = 'SUBMISSAO';
                    $artigo->subtitulo = $request->subtitulo ? $request->subtitulo : null;
                    $artigo->slug = Str::slug($request->titulo);
                    $artigo->resumo = $request->resumo;
                    $artigo->referencias = $request->referencias;
                    $artigo->imagem = $this->uploadFile($request, 'imagem');
                    $artigo->file = $this->uploadFile($request, 'file');
                    $artigo->edicoes_id = $edicao->id;
                    $artigo->save();

                    $authors = new Interveniente();
                    $authors->users_id = $this->Usuario->id;
                    $authors->artigos_id = $artigo->id;
                    $authors->role = 'AUTOR';
                    $authors->save();

                    $categoria = new CategoriasArtigos();
                    $categoria->artigos_id = $artigo->id;
                    $categoria->categorias_id = $request->categoria;
                    $categoria->save();

                    DB::commit();
                    return response()->json(
                        [
                            'status' => true,
                            'message' => 'Artigo Criado com sucesso!',
                            'numrow' => 1,
                            'data' => $artigo->id
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
                    'message' => 'Voçê não tem permissão para criar o atigo',
                    'numrow' => 0,
                    'data' => null
                ],
                200
            );
        }
    }

    public function estadoArtigo(Request $request)
    {

        if ($this->Usuario->hasRole(['editor', 'editor_chefe'])) {


            try {

                $validator = Validator::make($request->all(), [
                    'artigos_id' => 'required',
                    'status' => 'sometimes',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
                } else {


                    $artigo = Artigo::find($request->artigos_id);
                    $artigo->status = $request->status;
                    $artigo->save();


                    return response()->json(
                        [
                            'status' => true,
                            'message' => 'Estado alterado com sucesso!',
                            'numrow' => 1,
                            'data' => $artigo->id
                        ],
                        200
                    );
                }
            } catch (\Exception $e) {

                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Erro ao alterar estado do artigo' . $e->getMessage(),
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
                    'message' => 'Voçê não tem permissão para alterar o estado do artigo',
                    'numrow' => 0,
                    'data' => null
                ],
                200
            );
        }
    }


    public function carregarEdicao()
    {
        $edicoes = Edicao::where('status', '=', 0)->orderByDesc('id')->limit(1);
        if (count($edicoes->get()) != 0) {
            $result = $edicoes->get()[0];
            return $result;
        } else {
            $list = Edicao::where('status', '=', 1)->orderByDesc('id')->limit(1);
            $numero = 1;
            if ($list->get()) {
                $numero = $list->get()[0]->id + 1;
            }
            $edicao = new Edicao();
            $edicao->numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
            $edicao->status = 0;
            $edicao->save();
            return $edicao->refresh();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adicionarRevisor(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'artigo_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => $validator->errors()->all(),
                        'numrow' => 1,
                        'pageSize' => 0,
                        'currentPage' => 1,
                        'data' => null,
                    ],
                    400
                );
            } else {

                /* Verifica se o usuário é um revisor */
                $revisor = User::where('id', '=', $request->user_id)->whereHas('roles', function ($roles) {
                    $roles->where('name', '=', 'revisor');
                })->first();


                /* ----------- Verifica se id do artigo existe no banco de dados -------- */
                $artigo = Artigo::find($request->artigo_id)->first();

                if (($revisor != null) && ($artigo != null)) {

                    $existe = Interveniente::where('users_id', '=', $request->user_id)->where('artigos_id', '=', $request->artigo_id)->where('role', '=', 'REVISOR');

                    if (count($existe->get()) > 0) {
                        return response()->json(
                            [
                                'status' => true,
                                'message' => 'Este revisor já esta foi adicionado neste artigo',
                                'numrow' => 1,
                                'data' => null,
                                'pageSize' => 0,
                                'currentPage' => 1
                            ],
                            200
                        );
                    }

                    $revires = new Interveniente();
                    $revires->users_id = $request->user_id;
                    $revires->artigos_id = $request->artigo_id;
                    $revires->role = 'REVISOR';
                    $revires->save();

                    return response()->json(
                        [
                            'status' => true,
                            'message' => 'Revisor foi adicionado com sucesso',
                            'numrow' => 1,
                            'data' => $revisor,
                            'pageSize' => 0,
                            'currentPage' => 1
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Não foi possível adicionar o revisor. certifique o revisor ou artigo exista',
                        'numrow' => 1,
                        'data' => null,
                        'pageSize' => 0,
                        'currentPage' => 1
                    ],
                    400
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $e,
                    'numrow' => 1,
                    'data' => null,
                    'pageSize' => 0,
                    'currentPage' => 1
                ],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removerRevisor(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'artigo_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => $validator->errors()->all(),
                        'numrow' => 1,
                        'pageSize' => 0,
                        'currentPage' => 1,
                        'data' => null,
                    ],
                    400
                );
            } else {

                /* Verifica se o usuário é um revisor */
                $revisor = User::where('id', '=', $request->user_id)->whereHas('roles', function ($roles) {
                    $roles->where('name', '=', 'revisor');
                })->first();


                /* ----------- Verifica se id do artigo existe no banco de dados -------- */
                $artigo = Artigo::find($request->artigo_id)->first();

                if (($revisor != null) && ($artigo != null)) {

                    $inteveniente = Interveniente::where('users_id', '=', $request->user_id)->where('artigos_id', '=', $request->artigo_id)->where('role', '=', 'REVISOR');


                    if (count($inteveniente->get()) == 0) {

                        return response()->json(
                            [
                                'status' => true,
                                'message' => 'O Revisor que pretende remover não existe',
                                'numrow' => 1,
                                'data' => null,
                                'pageSize' => 0,
                                'currentPage' => 1
                            ],
                            200
                        );
                    }

                    $inteveniente->delete();

                    return response()->json(
                        [
                            'status' => true,
                            'message' => 'Revisor foi removido com sucesso',
                            'numrow' => 1,
                            'data' => $revisor,
                            'pageSize' => 0,
                            'currentPage' => 1
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Não foi possível adicionar o revisor. certifique o revisor ou artigo exista',
                        'numrow' => 1,
                        'data' => null,
                        'pageSize' => 0,
                        'currentPage' => 1
                    ],
                    400
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $e,
                    'numrow' => 1,
                    'data' => null,
                    'pageSize' => 0,
                    'currentPage' => 1
                ],
                500
            );
        }
    }

    public function download($id)
    {

        $artigo = Artigo::find($id);
        if ($artigo) {
            return Storage::download('public/files/' . $artigo->file);
        }
    }


}
