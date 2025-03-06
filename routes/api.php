<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\V1\Admin\HomeRestApiController;
use App\Http\Controllers\V1\Admin\CollegeRestApiController;
use App\Http\Controllers\V1\Admin\CourseRestController;
use App\Http\Controllers\V1\Admin\UniversityRestController;
use App\Http\Controllers\V1\Admin\SearchRestController;
use App\Http\Controllers\V1\Admin\CompareRestController;
use App\Http\Controllers\V1\Admin\CourseDetailRestApiController;
use App\Http\Controllers\V1\Admin\UniversityDetailRestApiController;
use App\Http\Controllers\V1\Admin\NewsRestController;
use App\Http\Controllers\V1\Admin\ConfigSearchRestController;
use App\Http\Controllers\V1\Admin\CollegeLoginApiController;
use App\Http\Controllers\V1\Admin\StatusRestApiController;
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
Route::get('/v1/home', [HomeRestApiController::class, 'index']);
Route::get('/v1/org/{id}', [CollegeRestApiController::class, 'collegeDetail']);
Route::post('/v1/org/review', [HomeRestApiController::class, 'reviewStore']);
Route::get('/v1/news/event/{id}', [CollegeRestApiController::class, 'news_events']);
Route::get('/v1/course', [CourseRestController::class, 'getCourse']);
Route::get('/v1/college', [CollegeRestApiController::class, 'getCollege']);
Route::get('/v1/university', [UniversityRestController::class, 'getUniversity']);
Route::get('/v1/search', [SearchRestController::class, 'getSearch']);
Route::get('/v1/search/college/course/university', [SearchRestController::class, 'getSimpleSearch']);
Route::get('/v1/college/compare', [CompareRestController::class, 'Collegecompare']);
Route::get('/v1/course/{id}', [CourseDetailRestApiController::class, 'courseDetail']);
Route::get('/v1/university/{id}', [UniversityDetailRestApiController::class, 'universityDetail']);
Route::get('/v1/news/event', [NewsRestController::class, 'getNews']);
Route::get('/v1/config/search', [ConfigSearchRestController::class, 'getConfigSearch']);
Route::post('/v1/college/login', [CollegeLoginApiController::class, 'collegeLogin']);

Route::middleware(['jwt.auth'])->group(function () {
    //validate token (signature + expiry)
    Route::get('/v1/status', [StatusRestApiController::class, 'index']);
    Route::get('/v1/status/{id}', [StatusRestApiController::class, 'show']);
    Route::post('/v1/status/store', [StatusRestApiController::class, 'store']);
    Route::put('/vi/status/update/{id}', [StatusRestApiController::class, 'update']);
    Route::delete('/v1/status/delete/{id}', [StatusRestApiController::class, 'destroy']);
    Route::get('/v1/status/show/{id}', [StatusRestApiController::class, 'show']);
});
