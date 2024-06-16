<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Api\V1\RegisterController;
use  App\Http\Controllers\Api\V1\GetParametersController;
use  App\Http\Controllers\Api\V2\ProfileController;
use  App\Http\Controllers\Api\V2\UserNotificationsForPlatformController;
use  App\Http\Controllers\Api\V2\CardController;
use  App\Http\Controllers\Api\V2\Payments\SberbankController;
use  App\Http\Controllers\Parsing\OrdersController;
use  App\Http\Controllers\Admin\FaqController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ggmb', [GetParametersController::class, 'ggmb']);
Route::get('get_car_models', [GetParametersController::class, 'get_car_models']);
Route::get('get_order', [OrdersController::class, 'get_order']);
Route::post('register_or_login', [RegisterController::class, 'register_or_login']);
Route::post('confirm_login_or_register', [RegisterController::class, 'confirm_login_or_register']);

Route::post('check_license', [GetParametersController::class, 'check_license']);

Route::post('get_user_for_notification', [UserNotificationsForPlatformController::class, 'get_user_for_notification']);



Route::get('get_whatsapp_and_telegram', [GetParametersController::class, 'get_whatsapp_and_telegram']);
Route::post('get_photo_data', [GetParametersController::class, 'get_photo_data']);
Route::get('login_in_yandex', [RegisterController::class, 'login_in_yandex']);
Route::post('import', [GetParametersController::class, 'import']);

Route::group(['middleware' => ['auth:api']], function () {


    Route::controller(CardController::class)->group(function () {
        Route::post('/initiate-payment', 'initiatePayment');
        Route::post('add_card', 'add_card');
        Route::post('add_sum_in_balance', 'add_sum_in_balance');
        Route::post('getAccessToken', 'getAccessToken');
        Route::get('success_add_sum_in_balance', 'success_add_sum_in_balance');
        Route::get('fail_add_sum_in_balance', 'fail_add_sum_in_balance');
    });

    Route::controller(SberbankController::class)->group(function (){
       Route::post('getCommission', 'getCommission');
       Route::post('sendTransfer', 'sendTransfer');
    });

    Route::get('get_faqs', [FaqController::class, 'get_faqs']);

    Route::get('get_order_history', [ProfileController::class, 'get_order_history']);
    Route::get('get_balance_history', [ProfileController::class, 'get_balance_history']);
    Route::get('get_tariff_and_option', [ProfileController::class, 'get_tariff_and_option']);
    Route::post('update_tariff_and_option', [ProfileController::class, 'update_tariff_and_option']);
    Route::post('add_user_in_archive', [ProfileController::class, 'add_user_in_archive']);
    Route::post('update_phone', [ProfileController::class, 'update_phone']);
    Route::post('confirm_new_phone_code', [ProfileController::class, 'confirm_new_phone_code']);

    Route::post('create_account', [RegisterController::class, 'create_account']);
    Route::post('upload_photo_driver_license', [RegisterController::class, 'upload_photo_driver_license']);
    Route::post('upload_car_license', [RegisterController::class, 'upload_car_license']);
    Route::post('create_new_car', [RegisterController::class, 'create_new_car']);
    Route::get('get_job_category', [GetParametersController::class, 'get_job_category']);
    Route::get('get_drive_license_country', [GetParametersController::class, 'get_drive_license_country']);
    Route::get('get_regions', [GetParametersController::class, 'get_regions']);
    Route::get('car_color', [GetParametersController::class, 'car_color']);
    Route::get('get_car_marks', [GetParametersController::class, 'get_car_marks']);
    Route::get('get_car_model', [GetParametersController::class, 'get_car_model']);
    Route::get('auth_user_info', [RegisterController::class, 'auth_user_info']);
    Route::get('logout', [RegisterController::class, 'logout']);
});
Route::get('get_car_model', [GetParametersController::class, 'get_car_model']);