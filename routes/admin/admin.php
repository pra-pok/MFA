<?php
use App\Http\Controllers\Admin\UniversityController;
use Illuminate\Support\Facades\Route;

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
