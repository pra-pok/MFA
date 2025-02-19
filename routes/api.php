<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Admin\HomeRestApiController;
use App\Http\Controllers\V1\Admin\CollegeRestApiController;
use App\Http\Controllers\V1\Admin\CourseRestController;
use App\Http\Controllers\V1\Admin\UniversityRestController;

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
Route::get('/v1/home', [HomeRestApiController::class, 'index']);
Route::get('/v1/org/{id}', [CollegeRestApiController::class, 'collegeDetail']);
Route::post('/v1/org/review', [HomeRestApiController::class, 'reviewStore']);
Route::get('/v1/news-event/{id}', [CollegeRestApiController::class, 'news_events']);
Route::get('/v1/course', [CourseRestController::class, 'getCourse']);
Route::get('/v1/college', [CollegeRestApiController::class, 'getCollege']);
Route::get('/v1/university', [UniversityRestController::class, 'getUniversity']);
