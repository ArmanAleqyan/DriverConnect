<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\RegionKeycontroller;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AllNewsLettersController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\JobsController;
use  App\Http\Controllers\Api\V2\CardController;
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



Route::controller(CardController::class)->group(function () {


    Route::get('/getAuthorizationCode',  'getAuthorizationCode');
    Route::get('/callback',  'handleCallback')->name('handleCallback');
    Route::get('/refreshAccessToken', 'refreshAccessToken')->name('refreshAccessToken');
    Route::get('/orderBusinessCard', 'orderBusinessCard');


//    Route::post('/initiate-payment', 'initiatePayment');
//    Route::get('/success_add_sum_in_balance','handleAuthorizationCallback')->name('success_add_sum_in_balance');
//
//    Route::get('success_add_sum_in_balance', 'success_add_sum_in_balance')->name('success_add_sum_in_balance');
//    Route::get('fail_add_sum_in_balance', 'fail_add_sum_in_balance')->name('fail_add_sum_in_balance');
//    Route::get('add_sum_in_balance', 'add_sum_in_balance')->name('add_sum_in_balance');
//
//    Route::get('/redirectToAuthorization', 'redirectToAuthorization');
//
//    Route::get('/handle-sberbank-callback', 'handleSberbankCallback')->name('handleSberbankCallback');
    Route::get('/get_swagger', 'get_swagger')->name('get_swagger');
});


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

        Route::controller(FaqController::class)->group(function () {
            Route::get('all_faqs', 'all_faqs')->name('all_faqs');
            Route::get('single_page_faq/faq_id={id}', 'single_page_faq')->name('single_page_faq');
            Route::get('delete_faq/faq_id={id}', 'delete_faq')->name('delete_faq');
            Route::get('create_faq_page', 'create_faq_page')->name('create_faq_page');
            Route::post('create_faq', 'create_faq')->name('create_faq');
            Route::post('update_faq', 'update_faq')->name('update_faq');
        });
        Route::controller(CarController::class)->group(function () {
            Route::post('update_car', 'update_car')->name('update_car');
        });
        Route::post('/save-active-tab', [App\Http\Controllers\TabController::class, 'saveActiveTab'])->name('saveActiveTab');

        Route::controller(AllNewsLettersController::class)->group(function (){
        Route::get('all_news_letters', 'all_news_letters')->name('all_news_letters');
        Route::get('create_letters_page', 'create_letters_page')->name('create_letters_page');
        Route::get('single_page_letters/letters_id={id}', 'single_page_letters')->name('single_page_letters');
            Route::get('delete_letters/letters_id={id}', 'delete_letters')->name('delete_letters');
            Route::post('update_letters', 'update_letters')->name('update_letters');
            Route::post('create_letters', 'create_letters')->name('create_letters');
        });

        Route::controller(JobsController::class)->group(function (){
           Route::get('all_jobs', 'all_jobs')->name('all_jobs');
           Route::get('single_page_job/job_id={id}', 'single_page_job')->name('single_page_job');
           Route::get('getData', 'getData')->name('getData');
        });
        Route::get('new_users', [UsersController::class, 'new_users'])->name('new_users');
        Route::get('delete_new_user/user_id={id}', [UsersController::class, 'delete_new_user'])->name('delete_new_user');
        Route::get('all_users', [UsersController::class, 'all_users'])->name('all_users');
        Route::get('single_page_user/user_id={id}', [UsersController::class, 'single_page_user'])->name('single_page_user');
        Route::get('add_user_in_archive/user_id={id}', [UsersController::class, 'add_user_in_archive'])->name('add_user_in_archive');
        Route::post('update_user', [UsersController::class, 'update_user'])->name('update_user');

        Route::get('settings_page', [SettingsController::class,'settings_page'])->name('settings_page');
        Route::post('update_whattsap_and_telegram', [SettingsController::class,'update_whattsap_and_telegram'])->name('update_whattsap_and_telegram');
        Route::post('update_company', [SettingsController::class,'update_company'])->name('update_company');
        Route::post('update_bank', [SettingsController::class,'update_bank'])->name('update_bank');

        Route::get('get_all_regions', [RegionController::class, 'get_all_regions'])->name('get_all_regions');
        Route::get('single_page_region/region_id={id}', [RegionController::class, 'single_page_region'])->name('single_page_region');
        Route::post('update_region', [RegionController::class, 'update_region'])->name('update_region');

        Route::get('HomePage', [AdminLoginController::class,'HomePage'])->name('HomePage');
        Route::get('testhome',function (){
            return view('admin.testhome');
        } );
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
