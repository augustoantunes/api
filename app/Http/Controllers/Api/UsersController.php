<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
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

        $status = $request->query('status') ? $request->query('status') : 1;

        $Usuario = User::where('status', '=', $status);
        $Usuario->with('roles');

        $data = $Usuario->get();



        return response()->json(
            [
                'status' => true,
                'message' => 'Artigos Listados com sucesso',
                // 'numrow' => $rowCount,
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'data' => $data
            ]
        );
    }


    // Funcções de Operações de Permissoes de Usuários do Sistema


    public function permissoesList()
    {
        // if($this->Usuario->hasRole(['editor', 'editor_chefe'])){

        // }
        $roles = Role::all();

        return response()->json(
            [
                'status' => true,
                'message' => 'Resultados da Pesquisa',
                'numrow' => count($roles),
                'data' => $roles
            ]
        );
    }



    public function AdicionarPermissao(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => true, 'message' => $validator->errors()->all()]);
            }


            $usuario = User::find($request->user_id);

            if($usuario != null){
                $role = Role::find($request->role_id);
                $usuario->attachRole($role);
            }



            return response()->json(
                [
                    'status' => true,
                    'message' => 'Role Adicionada com sucesso',
                    'numrow' => 1,
                    'data' => $usuario
                ]
            );


        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Erro ao tentara acionar a Role',
                    'numrow' => 0,
                    'data' => $e
                ]
            );
        }


    }


    public function RemoverPermissao(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => true, 'message' => $validator->errors()->all()]);
            }


            $usuario = User::find($request->user_id);

            if($usuario != null){
                $role = Role::find($request->role_id);
                $usuario->detachRole($role);
            }

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Permissão removida com sucesso',
                    'numrow' => 1,
                    'data' => $usuario
                ]
            );


        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Erro ao tentara acionar a Role',
                    'numrow' => 0,
                    'data' => $e
                ]
            );
        }


    }



}
