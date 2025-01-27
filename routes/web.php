<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UserActionController;
use App\Http\Controllers\Admin\OrganizationGalleryController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationSocialMediaController;
use App\Http\Controllers\Admin\OrganizationCourseController;
use Illuminate\Support\Facades\Route;

//Route::redirect('/', '/dashboard', 301);
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
//    Route::get('/dashboard', function () {
//        return view('dashboard');
//    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('team', TeamController::class);
    Route::get('/team/{team}/assign-permissions', [TeamController::class, 'assignPermission'])->name('team.assign-permissions');
    Route::post('/team/{team}/assign-permissions', [TeamController::class, 'saveAssignPermission'])->name('team.assign-permissions.save');

    Route::resource('role', RoleController::class)->except('show');
    Route::get('/role/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('role.assign-permissions');
    Route::post('/role/{role}/assign-permissions', [RoleController::class, 'saveAssignPermissions'])->name('role.assign-permissions.save');

    Route::resource('user', UserController::class)->except('show');
    Route::get('/user/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('user.assign-roles');
    Route::post('/user/{user}/assign-roles', [UserController::class, 'saveAssignRoles'])->name('user.assign-roles.save');
    Route::post('/log-user-action', [UserActionController::class, 'logUserAction']);

    Route::resource('article', ArticleController::class);

//    Route::resource('gallery_category' , GalleryCategoryController::class);
//    Route::get('trash',[GalleryCategoryController::class,'trash'])->name('gallery_category.trash');
//    Route::get('restore/{id}',[GalleryCategoryController::class,'restore'])->name('gallery_category.restore');
//    Route::delete('force-delete/{id}',[GalleryCategoryController::class,'forceDeleteData'])->name('gallery_category.force_delete');
//    Route::delete('{id}',[GalleryCategoryController::class,'destroy'])->name('gallery_category.destroy');
    //Route::post('/store',[OrganizationGalleryController::class,'store'])->name('gallery-store');
   // Route::resource('organization-gallery' , OrganizationGalleryController::class);


    Route::group(['prefix' =>'organization', 'as' => 'organization.'], function() {
        Route::get('trash',[OrganizationController::class,'trash'])->name('trash');
        Route::get('restore/{id}',[OrganizationController::class,'restore'])->name('restore');
        Route::delete('force-delete/{id}',[OrganizationController::class,'forceDeleteData'])->name('force_delete');
        Route::get('create',[OrganizationController::class,'create'])->name('create');
        Route::post('/',[OrganizationController::class,'store'])->name('store');
        Route::get('/',[OrganizationController::class,'index'])->name('index');
        Route::get('/all-data',[OrganizationController::class,'getData'])->name('getData');
        Route::get('{id}/show',[OrganizationController::class,'show'])->name('show');
        Route::delete('{id}',[OrganizationController::class,'destroy'])->name('destroy');
        Route::get('{id}/edit',[OrganizationController::class,'edit'])->name('edit');
        Route::put('{id}',[OrganizationController::class,'update'])->name('update');
        Route::get('/get-parents-by-country',[OrganizationController::class,'getParentsByCountry'])->name('get_parent');
    });

    Route::group(['prefix' =>'organization_gallery', 'as' => 'organization_gallery.'], function() {
        Route::get('trash',[OrganizationGalleryController::class,'trash'])->name('trash');
        Route::get('restore/{id}',[OrganizationGalleryController::class,'restore'])->name('restore');
        Route::delete('force-delete/{id}',[OrganizationGalleryController::class,'forceDeleteData'])->name('force_delete');
        Route::get('create',[OrganizationGalleryController::class,'create'])->name('create');
        Route::post('/',[OrganizationGalleryController::class,'store'])->name('store');
        Route::get('/',[OrganizationGalleryController::class,'index'])->name('index');
        Route::get('/all-data',[OrganizationGalleryController::class,'getData'])->name('getData');
        Route::get('{id}/show',[OrganizationGalleryController::class,'show'])->name('show');
        Route::delete('{id}',[OrganizationGalleryController::class,'destroy'])->name('destroy');
        Route::get('{id}/edit',[OrganizationGalleryController::class,'edit'])->name('edit');
        Route::put('{organization_id}',[OrganizationGalleryController::class,'update'])->name('update');
    });
//    Route::resource('organization-social-media', OrganizationSocialMediaController::class);
    Route::group(['prefix' =>'organization-social-media', 'as' => 'organization-social-media.'], function() {
        Route::get('trash',[OrganizationSocialMediaController::class,'trash'])->name('trash');
        Route::get('restore/{id}',[OrganizationSocialMediaController::class,'restore'])->name('restore');
        Route::delete('force-delete/{id}',[OrganizationSocialMediaController::class,'forceDeleteData'])->name('force_delete');
        Route::get('create',[OrganizationSocialMediaController::class,'create'])->name('create');
        Route::post('/',[OrganizationSocialMediaController::class,'store'])->name('store');
        Route::get('/',[OrganizationSocialMediaController::class,'index'])->name('index');
        Route::get('/all-data',[OrganizationSocialMediaController::class,'getData'])->name('getData');
        Route::get('{id}/show',[OrganizationSocialMediaController::class,'show'])->name('show');
        Route::delete('{id}',[OrganizationSocialMediaController::class,'destroy'])->name('destroy');
        Route::get('{id}/edit',[OrganizationSocialMediaController::class,'edit'])->name('edit');
        Route::put('{id}',[OrganizationSocialMediaController::class,'update'])->name('update');
    });

    Route::resource('organization-course', OrganizationCourseController::class);
});

require __DIR__ . '/auth.php';


Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    include('admin/admin.php');
});
