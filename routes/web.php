<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\RegionKeycontroller;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/NoAuth', function () {
    return response()->json([
        'status' => false,
        'message' => 'No Auth user'
    ],401);
})->name('NoAuth');
Route::get('/', function () {
    return redirect()->route('login');
});
Route::prefix('admin')->group(function () {
    Route::middleware(['NoAuthUser'])->group(function () {
        Route::get('/login',[AdminLoginController::class,'login'])->name('login');
        Route::post('/logined',[AdminLoginController::class,'logined'])->name('logined');
    });

    Route::middleware(['AuthUser'])->group(function () {


        Route::get('settings_page', [SettingsController::class,'settings_page'])->name('settings_page');
        Route::post('update_whattsap_and_telegram', [SettingsController::class,'update_whattsap_and_telegram'])->name('update_whattsap_and_telegram');
        Route::get('new_users', [UsersController::class, 'new_users'])->name('new_users');
        Route::get('all_users', [UsersController::class, 'all_users'])->name('all_users');
        Route::get('single_page_user/user_id={id}', [UsersController::class, 'single_page_user'])->name('single_page_user');
        Route::get('get_all_regions', [RegionController::class, 'get_all_regions'])->name('get_all_regions');
        Route::get('single_page_region/region_id={id}', [RegionController::class, 'single_page_region'])->name('single_page_region');
        Route::post('update_region', [RegionController::class, 'update_region'])->name('update_region');

        Route::get('HomePage', [AdminLoginController::class,'HomePage'])->name('HomePage');
        Route::get('logoutAdmin', [AdminLoginController::class,'logoutAdmin'])->name('logoutAdmin');


        Route::get('settingView', [AdminLoginController::class, 'settingView'])->name('settingView');
        Route::post('updatePassword', [AdminLoginController::class, 'updatePassword'])->name('updatePassword');


        Route::get('get_all_key', [RegionKeycontroller::class, 'get_all_key'])->name('get_all_key');
        Route::get('create_key_page', [RegionKeycontroller::class, 'create_key_page'])->name('create_key_page');
        Route::get('single_page_key/key_id={id}', [RegionKeycontroller::class, 'single_page_key'])->name('single_page_key');
        Route::get('delete_key/key_id={id}', [RegionKeycontroller::class, 'delete_key'])->name('delete_key');
        Route::post('create_key', [RegionKeycontroller::class, 'create_key'])->name('create_key');
        Route::post('update_key', [RegionKeycontroller::class, 'update_key'])->name('update_key');

    });
    });
