<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Artigo;
use App\Models\Api\CategoriasArtigos;
use App\Models\Api\Edicao;
use App\Models\Api\Interveniente;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('jwt-auth', ['except' => ['show', 'showPublich']]);
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


        /* Verifica se existe permissão para visualização de artigos */
        if (true === true) {

            /* Instancia a classe de artigos para se executar uma pesquisa */
            $query = Artigo::where('lang', '=', $lang);

            /* Veirificar se o artigo é publicado ou pendente */
            if ($request->query('fase')) {
                $query->where('fase', '=', $request->query('fase'));
            }

            /* Veirificar se o artigo é publicado ou pendente */
            if ($request->query('artigo')) {
                if(!($this->Usuario && $this->Usuario->hasRole(['editor', 'editor_chefe']))){
                    $query->where('status', '=', 'PUBLICADO');
                }
                $query->where('id', '=', $request->query('artigo'));
            }

            /* Trazer todos os artigos do usuário logado */
            if ($request->query('me')) {
                $query->whereHas('intervenientes', function ($destaque) {
                    $destaque->where('users_id', '=', $this->Usuario->id)
                    ->where('role', '=', 'AUTOR');
                });
            }
            /* Trazer todos os artigos de um determinado Autor*/
            if ($request->query('autor')) {
                $autor_id = $request->query('autor');
                $query->whereHas('intervenientes', function ($destaque) use($autor_id) {
                    $destaque->where('users_id', '=', $autor_id)
                    ->where('role', '=', 'AUTOR');
                });
            }

            /* Trazer todos os artigos de um determinado Revisor*/
            if ($request->query('revisor') && $this->Usuario->hasRole(['editor', 'editor_chefe'])) {
                $autor_id = $request->query('revisor');
                $query->whereHas('intervenientes', function ($destaque) use($autor_id) {
                    $destaque->where('users_id', '=', $autor_id)
                    ->where('role', '=', 'REVISOR');
                });
            }
            /* Trazer todos os artigos de uma determinada Categoria*/
            if ($request->query('categoria')) {
                $categoria = $request->query('categoria');
                $query->whereHas('categorias', function ($table) use($categoria) {
                    $table->where('slug', '=', $categoria);
                });
            }

            /* Trazer todos os artigos pertencentes a uma determinada edição*/
            if ($request->query('edicao')) {
                $edicao = $request->query('edicao');
                $query->whereHas('edicoes', function ($table) use($edicao, $request) {
                    $table->where('numero', '=', $edicao);
                    if($request->query('edicao_status') && $request->query('edicao_status') == false){
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
                $artigo['categoria'] = $artigo->categorias()->get();
                array_push($data, $artigo);
            }
        } else {

            $rowCount = 0;
            $data = [];
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
                    'message' => 'ERROR_PERMISSION',
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
            if($list->get()){
                $numero = $list->get()[0]->id + 1;
            }
            $edicao = new Edicao();
            $edicao->numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
            $edicao->status = 0;
            $edicao->save();
            return $edicao->refresh();
        }
    }
}
