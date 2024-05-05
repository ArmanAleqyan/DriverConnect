<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Api\V1\RegisterController;
use  App\Http\Controllers\Api\V1\GetParametersController;
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

Route::post('register_or_login', [RegisterController::class, 'register_or_login']);
Route::post('confirm_login_or_register', [RegisterController::class, 'confirm_login_or_register']);

Route::post('check_license', [GetParametersController::class, 'check_license']);

Route::get('get_whatsapp_and_telegram', [GetParametersController::class, 'get_whatsapp_and_telegram']);
Route::post('get_photo_data', [GetParametersController::class, 'get_photo_data']);
Route::get('login_in_yandex', [RegisterController::class, 'login_in_yandex']);
Route::post('import', [GetParametersController::class, 'import']);

Route::group(['middleware' => ['auth:api']], function () {
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
});
