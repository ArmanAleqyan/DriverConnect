<?php

namespace App\Http\Controllers\Parsing;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\RegionKey;
use App\Models\Mark;
use App\Models\Models;
use App\Models\CarAmenities;
use App\Models\CarCategory;
use App\Models\UserCar;
use App\Models\DriverLicense;
use App\Models\CarAmenitiesRelation;
use App\Models\CarCategorRelation;
use App\Models\YandexWorkRule;
use App\Models\UserWorkStatus;
class GetMyUsersList extends Controller
{
   public function get_users(){



       $get_keys = RegionKey::get();



       foreach ($get_keys as $key){
           $X_Park_ID = $key->X_Park_ID;
           $X_Client_ID = $key->X_Client_ID;
           $X_API_Key = $key->X_API_Key;

           $response = Http::withHeaders([
               'X-Client-ID' => $X_Client_ID,
               'X-Api-Key' => $X_API_Key,
               'Accept-Language' =>'rus',
           ])
               ->post('https://fleet-api.taxi.yandex.net/v1/parks/driver-profiles/list', [
                   'query' => [
                       'park' => [
                           'id' => $X_Park_ID
                       ]
                   ]
               ])->json();

           $getWorkRules = YandexWorkRule::where('key_id', $key->id)->get();


           if (isset($response['driver_profiles'])){
               foreach ($response['driver_profiles'] as $driver_profile){


//                   dd($driver_profile);

                   $profile = $driver_profile['driver_profile'];
                   $get_user = User::where([  'contractor_profile_id' => $profile['id']])->first();

                   if ($get_user == null){
                       $yandex_or_app_status = 1;
                   }else{
                       $yandex_or_app_status = $get_user->app_or_yandex;
                   }
                   if (isset($driver_profile['current_status'])){
                       $get_user_work_statuses = UserWorkStatus::updateorcreate(['name' => $driver_profile['current_status']['status']], ['name' => $driver_profile['current_status']['status']]);
                   }

                   $get_driver_license_country  =DriverLicense::where('Alpha',  $profile['driver_license']['country'])->first();

                $default_phone = User::where([        'contractor_profile_id' => $profile['id']])->first()->phone??null;

//
//                if ( $profile['id'] == '7be852d2c27240d580ea01db1f0dd783'){
//                    dd($driver_profile);
//                }
              $create_user =  User::updateOrcreate([
                    'contractor_profile_id' => $profile['id'],
                ],
                [
                    'balance'=> $driver_profile['accounts'][0]['balance']??null,
                    'contractor_profile_id' => $profile['id'],
                    'work_status_id' => $get_user_work_statuses->id??null,
                    'last_updated_work_status' => $driver_profile['current_status']['status_updated_at']??null,
                    'name' => $profile['first_name'],
                    'phone' => $profile['phones'][0]??$default_phone,
                    'surname' => $profile['last_name'],
                    'middle_name' => $profile['middle_name'],
                    'date_of_birth' => $profile['driver_license']['birth_date']??null,
//                    'driver_license_experience_total_since_date' => $profile['driver_license']['expiration_date']??null,
                    'driver_license_number' => $profile['driver_license']['number']??null,
                    'driver_license_issue_date' => $profile['driver_license']['issue_date']??null,
                    'driver_license_expiry_date' => $profile['driver_license']['expiration_date']??null,
                    'driver_license_country_id' =>$get_driver_license_country->id??null,
                    'work_status' => $profile['work_status'],
                    'created_date' => $profile['created_date'],
                    'hire_date' => $profile['hire_date'],
                    'is_selfemployed' => $profile['is_selfemployed'],
                    'has_contract_issue' => $profile['has_contract_issue'],
                    'modified_date' => $profile['modified_date'],
                    'create_account_status' => 1,
                    'park_id' => $key->id,
                    'created_at' => Carbon::parse($profile['hire_date']),
                    'app_or_yandex' =>$yandex_or_app_status,
                    'work_rule_id' => $getWorkRules->where('yandex_id', $profile['work_rule_id'])->first()->id,
                ]);
               if (isset($driver_profile['car'])){


                   $mark = Mark::where([
                       'name' => $driver_profile['car']['brand']
                   ])->first();

                   $model = Models::where([
                       'name' => $driver_profile['car']['model'],
                       'mark_id' => $mark->id??null
                   ])->first();

                   
                   UserCar::where([  'user_id' => $create_user->id])->update([
                       'connected_status' => 0
                   ]);
                  $create_car = UserCar::updateorcreate(['yandex_car_id' => $driver_profile['car']['id']],
                   [
                       'yandex_car_id' => $driver_profile['car']['id']??null,
                       'mark_id' =>$mark->id??null,
                       'model_id' => $model->id??null,
                       'normalized_number' => $driver_profile['car']['normalized_number']??null,
                       'number' => $driver_profile['car']['number']??null,
                       'registration_cert' => $driver_profile['car']['registration_cert']??null,
                       'status' => $driver_profile['car']['status']??null,
                       'vin' => $driver_profile['car']['vin']??null,
                       'year' => $driver_profile['car']['year']??null,
                       'callsign' => $driver_profile['car']['callsign']??null,
                       'color' => $driver_profile['car']['color']??null,
                       'connected_status' => 1,
                       'user_id' => $create_user->id
                   ]);

                  User::where('id', $create_user->id)->update([
                      'add_car_status' => 1
                  ]);
                   if (isset($driver_profile['car']['category'])){
                       foreach ($driver_profile['car']['category'] as $car_category){
                          $create_category = CarCategory::updateorcreate([
                                'name' => $car_category
                           ],
                           [
                               'name' => $car_category
                           ]);

                           CarCategorRelation::updateorcreate([
                               'car_id' => $create_car->id,
                               'category_id' => $create_category->id,
                           ],[
                               'car_id' => $create_car->id,
                               'category_id' => $create_category->id,
                           ]);
                       }
                   }

                   if (isset($driver_profile['car']['amenities'])){
                       foreach ($driver_profile['car']['amenities'] as $amenities){
                           $create_CarAmenities = CarAmenities::updateorcreate(['name'=> $amenities],['name'=> $amenities]);
                           CarAmenitiesRelation::updateOrCreate(['car_id' => $create_car->id, 'amenities_id' => $create_CarAmenities->id], ['car_id' => $create_car->id, 'amenities_id' => $create_CarAmenities->id]);
                       }
                   }


               }

           }
           }
       }




   }
}
