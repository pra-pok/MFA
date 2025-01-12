<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UserActionController;
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
});

require __DIR__ . '/auth.php';


Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    include('admin/admin.php');
});
