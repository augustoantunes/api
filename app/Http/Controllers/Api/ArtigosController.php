<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Artigo;
use App\Models\Api\CategoriasArtigos;
use App\Models\Api\Edicao;
use App\Models\Api\Interveniente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArtigosController extends Controller
{

    private $Usuario;
    use UploadsFile;

    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['artigos', 'showPublich']]);
        $this->Usuario = Auth::user();
    }

    public function show(Request $request){

    }


    public function store(Request $request){

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
                    $artigo->subtitulo = $request->subtitulo? $request->subtitulo : null;
                    $artigo->slug = Str::slug($request->titulo);
                    $artigo->resumo = $request->resumo;
                    $artigo->referencias = $request->referencias;
                    $artigo->imagem = $this->uploadFile($request,'imagem');
                    $artigo->file = $this->uploadFile($request,'file');
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


    public function carregarEdicao(){
        $edicoes = Edicao::where('status', '=', 0)
        ->orderByDesc('id')
        ->limit(1);

        if(count($edicoes->get()) != 0){
            $result = $edicoes->get()[0];
            return $result;
        } else {
            $edicao = new Edicao();
            $edicao->numero = str_pad('1', 6, '0', STR_PAD_LEFT);
            $edicao->status = 0;
            $edicao->save();
            return $edicao->get()[0];
        }

    }
}
