<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Admin\HomeController;

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
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index');
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/v1/home', [HomeController::class, 'index']);
Route::get('/v1/org/{id}', [HomeController::class, 'collegeDetail']);
Route::post('/v1/org/review', [HomeController::class, 'reviewStore']);
// Grouping the routes under the version "v1"
Route::group(['prefix' => 'v1', 'as' => 'v1.', 'namespace' => 'V1\Admin'], function () {

    /**
     * Home Route
     */

});
