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
Route::get('/v1/news-event/{id}', [CollegeRestApiController::class, 'news_events']);
Route::get('/v1/course', [CourseRestController::class, 'getCourse']);
Route::get('/v1/college', [CollegeRestApiController::class, 'getCollege']);
Route::get('/v1/university', [UniversityRestController::class, 'getUniversity']);
Route::get('/v1/search', [SearchRestController::class, 'getSearch']);
Route::get('/v1/search-college-course-university', [SearchRestController::class, 'getSimpleSearch']);
Route::get('/v1/college/compare', [CompareRestController::class, 'Collegecompare']);
