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
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admin\OrganizationSignupController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\LocalityController;

Route::get('mfa-admin/signin', [AuthenticatedSessionController::class, 'loginForm'])->name('admin.login');
Route::post('mfa-admin/login', [AuthenticatedSessionController::class, 'store'])->name('mfa-admin.login');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('/register', function () {
    abort(404);
});

Route::get('/logout', function () {
    abort(404);
});
Route::get('/login', function () {
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
    Route::post('/user/reset-password', [UserController::class, 'reset'])->name('user.reset');
    Route::post('/user/block', [UserController::class, 'block'])->name('user.block');
    Route::post('/user/clear-comment', [UserController::class, 'clearComment'])->name('user.clearComment');
    Route::get('/user/getData', [UserController::class, 'getDataMessage'])->name('user.getDataMessage');

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
        Route::get('/get-districts-by-parent', [OrganizationController::class, 'getDistrictsByParent'])->name('get_parent_district');
        Route::get('/get-localities-by-district', [OrganizationController::class, 'getLocalitiesByDistrict'])->name('get_locality_district');
        Route::get('/get-localities-by-country', [OrganizationController::class, 'getParentDetailsByLocality']);
    });
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
    Route::resource('organization-signup', OrganizationSignupController::class);
    Route::get('trash', [OrganizationSignupController::class, 'trash'])->name('organization-signup.trash');
    Route::get('restore/{id}', [OrganizationSignupController::class, 'restore'])->name('organization-signup.restore');
    Route::delete('force-delete/{id}', [OrganizationSignupController::class, 'forceDeleteData'])->name('organization-signup.force_delete');
    Route::delete('{id}', [OrganizationSignupController::class, 'destroy'])->name('organization-signup.destroy');
    Route::post('/reset-password', [OrganizationSignupController::class, 'reset'])->name('organization-signup.reset');
    Route::get('/getData', [OrganizationSignupController::class, 'getDataMessage'])->name('organization-signup.getDataMessage');
    Route::post('/block', [OrganizationSignupController::class, 'block'])->name('organization-signup.block');
    Route::post('/clear-comment', [OrganizationSignupController::class, 'clearComment'])->name('organization-signup.clearComment');

    Route::resource('locality', LocalityController::class);

    Route::get('/image-serve/{folder}/{filename}', function ($folder, $filename) {
//        $path = 'file:///data/mfa/images/' .'$folder/' . $filename;
        $path = "/data/mfa/images/$folder/$filename";

        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        return Response::make($file, 200)->header("Content-Type", $type);
    });
    Route::get('/image-serve-banner/{folder}/{filename}', function ($folder, $filename) {
        $path = "/data/mfa/images/$folder/banner/$filename";
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        return Response::make($file, 200)->header("Content-Type", $type);
    });
    Route::get('/image-serve-organization/{filename}', function ($filename) {
        $path = "/data/mfa/images/organization-gallery/$filename";
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        return Response::make($file, 200)->header("Content-Type", $type);
    });
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    include('admin/admin.php');
});
