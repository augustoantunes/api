<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ArtigosController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\EdicoesController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Authenticação da applicação
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group([
    'prefix' => 'artigo'
], function ($router) {
    Route::get('/', [ArtigosController::class, 'show']);
    Route::post('/', [ArtigosController::class, 'store']);
    Route::delete('/{id}', [ArtigosController::class, 'store']);
    Route::put('/', [ArtigosController::class, 'store']);
    Route::get('/download/{id}', [ArtigosController::class, 'download']);

});

Route::group([
    'prefix' => 'categoria'
], function ($router) {
    Route::get('/', [CategoriaController::class, 'index']);
    Route::get('/{id}', [CategoriaController::class, 'show']);
    Route::post('/', [CategoriaController::class, 'store']);
    Route::delete('/{id}', [CategoriaController::class, 'store']);
    Route::put('/', [CategoriaController::class, 'store']);

});


Route::group([
    'prefix' => 'edicao'
], function ($router) {
    Route::get('/', [EdicoesController::class, 'show']);
    Route::post('/', [EdicoesController::class, 'store']);
    Route::get('/lst', [EdicoesController::class, 'lstEdicao']);
});

Route::group([
    'prefix' => 'usuarios'
], function ($router) {
    Route::get('/', [UsersController::class, 'show']);

    Route::group([
        'prefix' => 'permissoes'
    ], function ($router) {
        Route::get('/', [UsersController::class, 'permissoesList']);
        Route::post('/', [UsersController::class, 'AdicionarPermissao']);
        Route::delete('/', [UsersController::class, 'RemoverPermissao']);

    });


});

Route::group([
    'prefix' => 'avaliacao'
], function ($router) {
    Route::get('/', [AvaliacaoController::class, 'show']);
    Route::post('/publicar', [AvaliacaoController::class, 'salvarAvalicao']);
});


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
