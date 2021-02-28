<?php

use Illuminate\Support\Facades\Route;

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
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh');
    Route::post('/me', 'AuthController@me');
});

Route::prefix('product')->group(function () {
    Route::get('/', 'ProductController@index');
    Route::post('/create', 'ProductController@store');
    Route::get('/{product}', 'ProductController@show');
    Route::match(['put','patch','post'],'/{product}', 'ProductController@update');
    Route::delete('/{product}', 'ProductController@destroy');
});
