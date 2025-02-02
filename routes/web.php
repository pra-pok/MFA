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
use App\Http\Controllers\Admin\PageCategoryController;
use App\Http\Controllers\Admin\OrganizationPageController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\OrganizationFacilitiesController;
use Illuminate\Support\Facades\Response;

//Route::redirect('/', '/dashboard', 301);
Route::get('mfa-admin/signin', [AuthenticatedSessionController::class,'loginForm'])->name('admin.login');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('/login', function(){
    abort(404);
});
Route::get('/register', function(){
    abort(404);
});
Route::get('/logout', function(){
    abort(404);
});


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

    Route::group(['prefix' => 'organization', 'as' => 'organization.'], function () {
        Route::get('trash', [OrganizationController::class, 'trash'])->name('trash');
        Route::get('restore/{id}', [OrganizationController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{id}', [OrganizationController::class, 'forceDeleteData'])->name('force_delete');
        Route::get('create', [OrganizationController::class, 'create'])->name('create');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/all-data', [OrganizationController::class, 'getData'])->name('getData');
        Route::get('{id}/show', [OrganizationController::class, 'show'])->name('show');
        Route::delete('{id}', [OrganizationController::class, 'destroy'])->name('destroy');
        Route::get('{id}/edit', [OrganizationController::class, 'edit'])->name('edit');
        Route::put('{id}', [OrganizationController::class, 'update'])->name('update');
        Route::get('/get-parents-by-country', [OrganizationController::class, 'getParentsByCountry'])->name('get_parent');

    });
//    Route::get('/storage/external/{file}', [OrganizationController::class, 'showFile'])
//        ->where('file', '.*')
//        ->name('external.file');
    Route::group(['prefix' => 'organization_gallery', 'as' => 'organization_gallery.'], function () {
        Route::get('trash', [OrganizationGalleryController::class, 'trash'])->name('trash');
        Route::get('restore/{id}', [OrganizationGalleryController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{id}', [OrganizationGalleryController::class, 'forceDeleteData'])->name('force_delete');
        Route::get('create', [OrganizationGalleryController::class, 'create'])->name('create');
        Route::post('/', [OrganizationGalleryController::class, 'store'])->name('store');
        Route::get('/', [OrganizationGalleryController::class, 'index'])->name('index');
        Route::get('/all-data', [OrganizationGalleryController::class, 'getData'])->name('getData');
        Route::get('{id}/show', [OrganizationGalleryController::class, 'show'])->name('show');
        Route::delete('{id}', [OrganizationGalleryController::class, 'destroy'])->name('destroy');
        Route::get('{id}/edit', [OrganizationGalleryController::class, 'edit'])->name('edit');
        Route::put('{organization_id}', [OrganizationGalleryController::class, 'update'])->name('update');
        Route::delete('/gallery-delete/{id}', [OrganizationGalleryController::class, 'permanentDelete'])->name('permanentDelete');
    });
    //    Route::resource('organization-social-media', OrganizationSocialMediaController::class);
    Route::group(['prefix' => 'organization-social-media', 'as' => 'organization-social-media.'], function () {
        Route::get('trash', [OrganizationSocialMediaController::class, 'trash'])->name('trash');
        Route::get('restore/{id}', [OrganizationSocialMediaController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{id}', [OrganizationSocialMediaController::class, 'forceDeleteData'])->name('force_delete');
        Route::get('create', [OrganizationSocialMediaController::class, 'create'])->name('create');
        Route::post('/', [OrganizationSocialMediaController::class, 'store'])->name('store');
        Route::get('/', [OrganizationSocialMediaController::class, 'index'])->name('index');
        Route::get('/all-data', [OrganizationSocialMediaController::class, 'getData'])->name('getData');
        Route::get('{id}/show', [OrganizationSocialMediaController::class, 'show'])->name('show');
        Route::delete('{id}', [OrganizationSocialMediaController::class, 'destroy'])->name('destroy');
        Route::get('{id}/edit', [OrganizationSocialMediaController::class, 'edit'])->name('edit');
        Route::put('{id}', [OrganizationSocialMediaController::class, 'update'])->name('update');
    });

    Route::resource('organization-course', OrganizationCourseController::class);
    Route::delete('/delete/{id}', [OrganizationCourseController::class, 'permanentDelete'])->name('permanentDelete');
    Route::resource('organization-page', OrganizationPageController::class);
    Route::delete('/page-delete/{id}', [OrganizationPageController::class, 'permanentDelete'])->name('permanentDelete');

    Route::resource('page-category', PageCategoryController::class);
    Route::get('trash', [PageCategoryController::class, 'trash'])->name('page-category.trash');
    Route::get('restore/{id}', [PageCategoryController::class, 'restore'])->name('page-category.restore');
    Route::delete('force-delete/{id}', [PageCategoryController::class, 'forceDeleteData'])->name('page-category.force_delete');
    Route::delete('{id}', [PageCategoryController::class, 'destroy'])->name('page-category.destroy');


    Route::resource('facilities', FacilitiesController::class);
    Route::get('trash', [FacilitiesController::class, 'trash'])->name('facilities.trash');
    Route::get('restore/{id}', [FacilitiesController::class, 'restore'])->name('facilities.restore');
    Route::delete('force-delete/{id}', [FacilitiesController::class, 'forceDeleteData'])->name('facilities.force_delete');
    Route::delete('{id}', [FacilitiesController::class, 'destroy'])->name('facilities.destroy');

    Route::resource('organization_facilities', OrganizationFacilitiesController::class);
});
require __DIR__ . '/auth.php';
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    include('admin/admin.php');
});
