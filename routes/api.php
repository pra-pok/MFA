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
use App\Http\Controllers\V1\Admin\ReferralSourceRestApiController;
use App\Http\Controllers\V1\Admin\CounselorReferralRestApiController;
use App\Http\Controllers\V1\Admin\AddressRestApiController;
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
Route::get('/v1/menu', [HomeRestApiController::class, 'menu']);
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
Route::get('/v1/counselor/', [CounselorReferralRestApiController::class, 'counselor']);
Route::get('/v1/address', [AddressRestApiController::class, 'getAddress']);

Route::middleware(['jwt.auth'])->group(function () {
    //validate token (signature + expiry)
    // API routes Status
    Route::get('/v1/status', [StatusRestApiController::class, 'index']);
    Route::post('/v1/status/store', [StatusRestApiController::class, 'store']);
    Route::put('/v1/status/update/{id}', [StatusRestApiController::class, 'update']);
    Route::delete('/v1/status/delete/{id}', [StatusRestApiController::class, 'destroy']);
    Route::get('/v1/status/show/{id}', [StatusRestApiController::class, 'show']);

    // Referral Sources API routes
    Route::get('/v1/referral/source', [ReferralSourceRestApiController::class, 'index']);
    Route::post('/v1/referral/source/store', [ReferralSourceRestApiController::class, 'store']);
    Route::put('/v1/referral/source/update/{id}', [ReferralSourceRestApiController::class, 'update']);
    Route::delete('/v1/referral/source/delete/{id}', [ReferralSourceRestApiController::class, 'destroy']);
    Route::get('/v1/referral/source/show/{id}', [ReferralSourceRestApiController::class, 'show']);

    // API routes for Counselor Referrers
    Route::get('/v1/counselor/referral', [CounselorReferralRestApiController::class, 'index']);
    Route::post('/v1/counselor/referral/store', [CounselorReferralRestApiController::class, 'store']);
    Route::put('/v1/counselor/referral/update/{id}', [CounselorReferralRestApiController::class, 'update']);
    Route::delete('/v1/counselor/referral/delete/{id}', [CounselorReferralRestApiController::class, 'destroy']);
    Route::get('/v1/counselor/referral/show/{id}', [CounselorReferralRestApiController::class, 'show']);
});
