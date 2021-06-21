<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class UsersController extends Controller
{
    private $Usuario;
    use UploadsFile;

    public function __construct()
    {
        // $this->middleware('jwt-auth', ['except' => ['show', 'showPublich']]);
        // $this->Usuario = Auth::user();
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

        $status = $request->query('status')? $request->query('status') : 1;

        $Usuario = User::where('status','=', $status);
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

}
