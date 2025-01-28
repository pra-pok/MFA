<?php
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\AdministrativeAreaController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\OrganizationGalleryController;
use App\Http\Controllers\Admin\PageCategoryController;

//universities
Route::group(['prefix' =>'university', 'as' => 'university.'], function() {
    Route::get('trash',[UniversityController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[UniversityController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[UniversityController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[UniversityController::class,'create'])->name('create');
    Route::post('/',[UniversityController::class,'store'])->name('store');
    Route::get('/',[UniversityController::class,'index'])->name('index');
    Route::get('/all-data',[UniversityController::class,'getData'])->name('getData');
    Route::get('{id}/show',[UniversityController::class,'show'])->name('show');
    Route::delete('{id}',[UniversityController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[UniversityController::class,'edit'])->name('edit');
    Route::put('{id}',[UniversityController::class,'update'])->name('update');
});
//country
Route::group(['prefix' =>'country', 'as' => 'country.'], function() {
    Route::get('trash',[CountryController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[CountryController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[CountryController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[CountryController::class,'create'])->name('create');
    Route::post('/',[CountryController::class,'store'])->name('store');
    Route::get('/',[CountryController::class,'index'])->name('index');
    Route::get('/all-data',[CountryController::class,'getData'])->name('getData');
    Route::get('{id}/show',[CountryController::class,'show'])->name('show');
    Route::delete('{id}',[CountryController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[CountryController::class,'edit'])->name('edit');
    Route::put('{id}',[CountryController::class,'update'])->name('update');
});
//stream
Route::group(['prefix' =>'stream', 'as' => 'stream.'], function() {
    Route::get('trash',[StreamController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[StreamController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[StreamController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[StreamController::class,'create'])->name('create');
    Route::post('/',[StreamController::class,'store'])->name('store');
    Route::get('/',[StreamController::class,'index'])->name('index');
    Route::get('/all-data',[StreamController::class,'getData'])->name('getData');
    Route::get('{id}/show',[StreamController::class,'show'])->name('show');
    Route::delete('{id}',[StreamController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[StreamController::class,'edit'])->name('edit');
    Route::put('{id}',[StreamController::class,'update'])->name('update');
});
//level
Route::group(['prefix' =>'level', 'as' => 'level.'], function() {
    Route::get('trash',[LevelController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[LevelController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[LevelController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[LevelController::class,'create'])->name('create');
    Route::post('/',[LevelController::class,'store'])->name('store');
    Route::get('/',[LevelController::class,'index'])->name('index');
    Route::get('/all-data',[LevelController::class,'getData'])->name('getData');
    Route::get('{id}/show',[LevelController::class,'show'])->name('show');
    Route::delete('{id}',[LevelController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[LevelController::class,'edit'])->name('edit');
    Route::put('{id}',[LevelController::class,'update'])->name('update');
});
//course
Route::group(['prefix' =>'course', 'as' => 'course.'], function() {
    Route::get('trash',[CourseController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[CourseController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[CourseController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[CourseController::class,'create'])->name('create');
    Route::post('/',[CourseController::class,'store'])->name('store');
    Route::get('/',[CourseController::class,'index'])->name('index');
    Route::get('/all-data',[CourseController::class,'getData'])->name('getData');
    Route::get('{id}/show',[CourseController::class,'show'])->name('show');
    Route::delete('{id}',[CourseController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[CourseController::class,'edit'])->name('edit');
    Route::put('{id}',[CourseController::class,'update'])->name('update');
});
//administrative_area
Route::group(['prefix' =>'administrative_area', 'as' => 'administrative_area.'], function() {
    Route::get('trash',[AdministrativeAreaController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[AdministrativeAreaController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[AdministrativeAreaController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[AdministrativeAreaController::class,'create'])->name('create');
    Route::post('/',[AdministrativeAreaController::class,'store'])->name('store');
    Route::get('/',[AdministrativeAreaController::class,'index'])->name('index');
    Route::get('/all-data',[AdministrativeAreaController::class,'getData'])->name('getData');
    Route::get('{id}/show',[AdministrativeAreaController::class,'show'])->name('show');
    Route::delete('{id}',[AdministrativeAreaController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[AdministrativeAreaController::class,'edit'])->name('edit');
    Route::put('{id}',[AdministrativeAreaController::class,'update'])->name('update');
    Route::get('/get-parents-by-country',[AdministrativeAreaController::class,'getParentsByCountry'])->name('get_parent');
});
//organization

Route::group(['prefix' =>'gallery_category', 'as' => 'gallery_category.'], function() {
    Route::get('trash',[GalleryCategoryController::class,'trash'])->name('trash');
    Route::get('restore/{id}',[GalleryCategoryController::class,'restore'])->name('restore');
    Route::delete('force-delete/{id}',[GalleryCategoryController::class,'forceDeleteData'])->name('force_delete');
    Route::get('create',[GalleryCategoryController::class,'create'])->name('create');
    Route::post('/',[GalleryCategoryController::class,'store'])->name('store');
    Route::get('/',[GalleryCategoryController::class,'index'])->name('index');
    Route::get('/all-data',[GalleryCategoryController::class,'getData'])->name('getData');
    Route::get('{id}/show',[GalleryCategoryController::class,'show'])->name('show');
    Route::delete('{id}',[GalleryCategoryController::class,'destroy'])->name('destroy');
    Route::get('{id}/edit',[GalleryCategoryController::class,'edit'])->name('edit');
    Route::put('{id}',[GalleryCategoryController::class,'update'])->name('update');
});


