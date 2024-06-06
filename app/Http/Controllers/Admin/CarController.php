<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCar;
use App\Models\CarCategory;
use Illuminate\Support\Facades\Http;

class CarController extends Controller
{
    public function update_car(Request $request){
        $car = UserCar::findOrFail($request->car_id);

        $amenities = $request->input('car_amenities', []);

        // Синхронизируем связи. Если массив пуст, все связи будут удалены
        $car->amenities()->sync($amenities);

        $car_category = $request->car_category_id??[];
        $car->categories()->sync($car_category);
//        $get_categories = CarCategory::query();
//        $user_category = auth()->user()->job_category_id;
//        if ($user_category == 1 ){
//            $get_categories->wherein('id', [1,7,8,3,15,5]);
//        }
//        if ($user_category == 2 ){
//            $get_categories->wherein('id', [22]);
//        }
//        if ($user_category == 3 ){
//            $get_categories->wherein('id', [5]);
//        }
        $url = "https://fleet-api.taxi.yandex.net/v2/parks/vehicles/car?vehicle_id={$car->yandex_car_id}";
//            dd($car);

//
//        $car->update([
//           'vin' => $request->vin,
//            'registration_cert' => $request->registration_cert
//        ]);
//
//
//        $car->save();

//        dd($car);
//        if ($car->user->contractor_profile_id != null){
            $X_Park_ID = $car->user->park->X_Park_ID;
            $X_Client_ID = $car->user->park->X_Client_ID;
            $X_API_Key = $car->user->park->X_API_Key;
            $response = Http::withHeaders([
                'X-Park-ID' => $X_Park_ID,
                'X-Client-ID' =>$X_Client_ID,
                'X-API-Key' => $X_API_Key
            ])->put($url, [
                'vehicle_id' => $car->yandex_car_id,
//                'cargo' => [
//                    'cargo_hold_dimensions' => [
//                        'height' => 150,
//                        'length' => 200,
//                        'width' => 200
//                    ],
//                    'cargo_loaders' => 1,
//                    'carrying_capacity' => 500
//                ],
//                'child_safety' => [
//                    'booster_count' => 2
//                ],
                'park_profile' => [
                    'amenities' => $car->amenities->pluck('name')->toarray(),
                    'callsign' => $car->callsign,
                    'categories' => $car->categories->pluck('name')->toarray(),
                    'comment' => 'good car',
//                    'fuel_type' => 'gas',
                    'is_park_property' => true,
//                    'leasing_conditions' => [
//                        'company' => 'leasing company',
//                        'interest_rate' => '11.7',
//                        'monthly_payment' => 20000,
//                        'start_date' => '2022-01-01',
//                        'term' => 30
//                    ],
//                    'license_owner_id' => '6k054dfdb9i5345ifsdfvpfu8c8cc946',
//                    'ownership_type' => 'park',
                    'status' => $request->car_work_status,
//                    'tariffs' => ['string']
                ],
                'vehicle_licenses' => [
//                    'licence_number' => '123456789',
                    'licence_plate_number' => $car->callsign,
                    'registration_certificate' => $car->registration_cert
                ],
                'vehicle_specifications' => [
//                    "body_number" =>'123456789',
                    "brand" => $car->mark->name,
                    "color" =>  $car->color,
                    "mileage" => 0,
                    "model" =>$car->model->name,
                    "transmission" => "unknown",
                    "vin" => $car->vin,
                    "year" => (int)$car->year
                ]
            ])->json();

//            dd($response);

            if (isset($response['message'])){
                return redirect()->back()->with('error', $response['message']);
            }

//        }

        return redirect()->back()->with('created', 'Данные успешно обнавлены');
    }
}
