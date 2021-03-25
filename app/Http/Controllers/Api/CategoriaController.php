<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        if(count($request->all()) == 0){
            $data = Categoria::all();
        } else {
            $data = [];
            if($request->query('ciencia')){
                $query = Categoria::where('ciencia_id','=', $request->query('ciencia'));
                $data = $query->get();
            }

        }


        return response()->json(
            [
                'status' => true,
                'message' => 'Listar Categoria',
                'numrow' => 0,
                'pageSize' => 0,
                'currentPage' => 0,
                'data' => [
                    $data
                ]
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Categoria::find($id);

        return response()->json(
            [
                'status' => true,
                'message' => 'Listar Categoria',
                'numrow' => 0,
                'pageSize' => 0,
                'currentPage' => 0,
                'data' => [
                    $data
                ]
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
