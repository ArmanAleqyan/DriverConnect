<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\RegionKey;
use App\Models\YandexWorkRule;
use App\Models\CarCategory;
use App\Models\UserWorkingStatusForYandex;
use App\Models\User;
use App\Models\Mark;
use App\Models\CarColor;
use App\Models\DriverLicense;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\UserCar;
use App\Models\CarAmenities;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UsersController extends Controller
{


    public function update_user(Request $request){




        $get_user = User::findorfail($request->user_id);

//        dd( );
        $get_region = Region::where('id', $request->region_id)->first();
        if ($get_region->key_id != null){
            $get_keys = RegionKey::where('id', $get_region->key_id)->first();
        }else{
            $get_keys = RegionKey::where('default',1)->first();
        }
        $get_work_rule = YandexWorkRule::where('key_id',$get_keys->id )->where('default', 1)->first();
        $X_Park_ID =$get_keys->X_Park_ID??env('X_Park_ID');
        $X_Client_ID = $get_keys->X_Client_ID??env('X_Client_ID');
        $X_API_Key = $get_keys->X_API_Key??env('X_API_Key');
        $work_rule = $get_work_rule->yandex_id;




        $url = 'https://fleet-api.taxi.yandex.net/v2/parks/contractors/driver-profile';
        $contractorProfileId = $get_user->contractor_profile_id; // замените на фактический ID

        $response = Http::withHeaders([
            'X-Park-ID' => $X_Park_ID,
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key
        ])->get($url, [
            'contractor_profile_id' => $contractorProfileId
        ]);

        $responseData = $response->json();

        if (isset($responseData['message'])){
            return redirect()->back()->with('error', $responseData['message']);
        }

// Вы можете вывести результат или использовать его далее в своем коде






//        dd($responseData);

        $phone = preg_replace('/[^+\d]/', '', $request->phone);

        $url = 'https://fleet-api.taxi.yandex.net/v2/parks/contractors/driver-profile';
        $get_drive_license_country = DriverLicense::where('id', $request->driver_license_country_id)->first();
//        dd($request->country_id );
        if (isset($responseData['person']['driver_license_experience']['total_since_date'])){
            $driver_license_experience_total_since_date = $responseData['person']['driver_license_experience']['total_since_date'];
        }else{
            if (isset($get_user->driver_license_experience_total_since_date)){

                $driver_license_experience_total_since_date = Carbon::parse($get_user->driver_license_experience_total_since_date)->format('Y-m-d');

            }else{

                $driver_license_experience_total_since_date = Carbon::now()->format('Y-m-d');
            }
        }
//        dd($driver_license_experience_total_since_date );

        $response = Http::withHeaders([
            'X-Park-ID' => $X_Park_ID,
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key
        ])->put($url . '?contractor_profile_id=' . $get_user->contractor_profile_id, [
            'account' => [
//                'balance_limit' => '50',
//                'block_orders_on_balance_below_limit' => false,
//                'payment_service_id' => '12345',
                'work_rule_id' => $work_rule
            ],
//            'car_id' => '5011ade6ba054dfdb7143c8cc9460dbc',
//            'order_provider' => [
//                'partner' => false,
//                'platform' => true
//            ],
            'person' => [
                'contact_info' => [
//                    'address' => 'Moscow, Ivanovskaya Ul., bld. 40/2, appt. 63',
//                    'email' => 'example-email@example.com',
                    'phone' =>$phone
                ],
                'driver_license' => [
                    'birth_date' => $responseData['person']['driver_license']['birth_date'],
                    'country' => $responseData['person']['driver_license']['country'],
                    'expiry_date' => $responseData['person']['driver_license']['expiry_date'],
                    'issue_date' => $responseData['person']['driver_license']['issue_date'],
                    'number' => $get_user->driver_license_number
                ],
//                'driver_license_experience' => [
////                    'total_since_date' =>$driver_license_experience_total_since_date
////                ],
                'full_name' => [
                    'first_name' => $get_user->name,
                    'last_name' => $get_user->surname,
                    'middle_name' => $get_user->middle_name
                ],
//                'tax_identification_number' => '7743013902'
            ],
            'profile' => [
                'comment' => 'great driver',
                'feedback' => 'great driver',
//                'fire_date' =>Carbon::now()->format('Y-m-d'),
//                'hire_date' => Carbon::now()->format('Y-m-d'),
                'work_status' =>$request->work_status
            ]
        ])->json();



        if (isset($response['message'])){
            return redirect()->back()->with('error', $response['message']);
        }else{

            $get_user->update([
               'phone' => $phone,
               'work_status'=> $request->work_status
            ]);
            return redirect()->back()->with('created', 'Профиль успешно обновлён');
        }
    }

    public function delete_new_user($id){
        $get = User::where('id', $id)->first();


        $old_front_photo = public_path('uploads/'.$get->driver_license_front_photo);
        $old_back_photo = public_path('uploads/'.$get->driver_license_back_photo);
        if (is_file($old_front_photo)) {
        if (file_exists($old_front_photo)) {
            unlink($old_front_photo);
        }
        }
        if (is_file($old_front_photo)) {

        if (file_exists($old_back_photo)) {
            unlink($old_back_photo);
        }
        }

        $get_cars = UserCar::where('user_id', $get->id)->get();


        foreach ($get_cars as $car){
            $old_front_photo_car = public_path('uploads/'.$car->car_license_front_photo);
            $old_back_photo_car = public_path('uploads/'.$car->car_license_back_photo);

            if (file_exists($old_front_photo_car)) {
                unlink($old_front_photo_car);
            }
            if (file_exists($old_back_photo_car)) {
                unlink($old_back_photo_car);
            }
        }

        $get->delete();


        if (request()->ajax()) {
            return response()->json(['success' => true]);
        } else {
            return redirect()->route('new_users');
        }
    }

    public function new_users(){
        $get_users = User::where('create_account_status', 0)->where('role_id','!=', 1)->orwhere('add_car_status',0)->where('role_id','!=', 1)->orwhere('sended_in_yandex_status',  0)->where('role_id','!=', 1)->get();
        $regions = Region::get();
        return view('admin.Users.UnconfimedUsers', compact('get_users','regions'));
    }


    public function all_users(Request $request){
        $new_users  = User::where('create_account_status', 1)->withCount(['jobs' => function ($query) {
            $query->where('created_at', '>=',Carbon::now()->subdays(14)->startofday());
        }]) ->having('jobs_count', '<', 50)
            ->where('work_status','!=', 'fired')->where('add_car_status',1)->where('role_id','!=', 1)->where('sended_in_yandex_status',  1)->get();

        $active_users =User::where('create_account_status', 1)->withCount(['jobs' => function ($query) {
            $query->where('created_at', '>=',Carbon::now()->subdays(14)->startofday());
        }])->having('jobs_count', '>=', 50)
            ->where('work_status','!=', 'fired')->where('add_car_status',1)->where('role_id','!=', 1)->where('sended_in_yandex_status',  1)->get();
        $inactive_users  =User::where('create_account_status', 1)->withCount('jobs')->having('jobs_count', '=', 0)
            ->where('work_status','!=', 'fired')->where('add_car_status',1)->where('role_id','!=', 1)->where('sended_in_yandex_status',  1)->where('created_at', '<=',Carbon::now()->subdays(14)->startofday() )->get();

        $archived_users  =User::where('create_account_status', 1)->where('work_status', 'fired')->where('add_car_status',1)->where('role_id','!=', 1)->where('sended_in_yandex_status',  1)->get();
        $regions = Region::get();
        return view('admin.Users.NewUsers', compact('new_users','regions','active_users','inactive_users','archived_users'));
    }

    public function single_page_user($id){
        $get = User::where('id', $id)->first();
        $get_regions =  Region::get();
        $get_yandex_worrk_status =  UserWorkingStatusForYandex::get();
        $get_country_drive_licenze =  DriverLicense::get();
        $get_car = UserCar::where('user_id', $id)->where('connected_status', 1)->latest()->first();

        $car_working_status = [

            'unknown' => 'статус неизвестен',
            'working' => 'в данный момент используется для совершения поездок',
            'not_working' => 'в данный момент не используется для совершения поездок',
            'repairing' => 'подвергается техническому обслуживанию или ремонту',
            'no_driver' => 'за машиной не закреплен водитель',
            'pending' => 'ведется обработка сведений об автомобиле',

        ];

        $get_marks = Mark::get();
        $get_colors =     CarColor::get();
        $get_car_category = CarCategory::get();
        $get_car_amenities = CarAmenities::get();
        return view('admin.Users.single', compact('car_working_status','get_car_category','get_car_amenities','get','get_car','get_regions','get_country_drive_licenze','get_marks','get_colors','get_yandex_worrk_status'));
    }

    public function add_user_in_archive($id){


        $get = User::where('id', $id)->first();


//        dd();
        $get_region = Region::where('id', $get->park->region_id)->first();
        if ($get_region->key_id != null) {
            $get_keys = RegionKey::where('id', $get_region->key_id)->first();
        } else {
            $get_keys = RegionKey::where('default', 1)->first();
        }

        $X_Park_ID = $get_keys->X_Park_ID ?? env('X_Park_ID');
        $X_Client_ID = $get_keys->X_Client_ID ?? env('X_Client_ID');
        $X_API_Key = $get_keys->X_API_Key ?? env('X_API_Key');
        $contractorProfileId = $get->contractor_profile_id;

        $url = 'https://fleet-api.taxi.yandex.net/v2/parks/contractors/driver-profile';
        $query = http_build_query(['contractor_profile_id' => $contractorProfileId]);

        $response = Http::withHeaders([
            'X-Park-ID' => $X_Park_ID,
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key,
        ])->put("$url?$query", [
            'account' => [
                'work_rule_id' => $get->WorkRule->yandex_id,  // Обязательное поле
            ],
            // 'car_id' => '5011ade6ba054dfdb7143c8cc9460dbc',
            'order_provider' => [
                'partner' => false,
                'platform' => true,
            ],
            'person' => [
                'contact_info' => [
                    'address' => $get->address ?? 'Moscow, Ivanovskaya Ul., bld. 40/2, appt. 63',  // Пример обязательного поля
                    'email' => $get->email ?? 'example-email@example.com',  // Пример обязательного поля
                    'phone' => $get->phone ?? '+79999999999',  // Пример обязательного поля
                ],
                'driver_license' => [
                    'birth_date' => $get->date_of_birth ? Carbon::parse($get->date_of_birth)->format('Y-m-d') : '1975-10-28',
                    'country' => 'rus',
                    'expiry_date' => $get->driver_license_expiry_date ? Carbon::parse($get->driver_license_expiry_date)->format('Y-m-d') : '2050-10-28',
                    'issue_date' => $get->driver_license_issue_date ? Carbon::parse($get->driver_license_issue_date)->format('Y-m-d') : '2020-10-28',
                    'number' => $get->driver_license_number,
                ],
                'driver_license_experience' => [
                    'total_since_date' => $get->driver_license_experience_total_since_date ? Carbon::parse($get->driver_license_experience_total_since_date)->format('Y-m-d') : '1970-01-01',
                ],
                'full_name' => [
                    'first_name' => $get->name,
                    'last_name' => $get->surname,
                    'middle_name' => $get->middle_name,
                ],
                // 'tax_identification_number' => '7743013902',
            ],
            'profile' => [
             'fire_date' => Carbon::now()->format('Y-m-d'),
//                'hire_date' => Carbon::now()->format('Y-m-d'),
                'work_status' => 'fired',
            ],
        ])->json();

        $get->update([
           'work_status' => 'fired'
        ]);
        $get->tokens()->delete();

        UserCar::where('id', $get->id)->update([
           'status' => 'not_working'
        ]);
        return redirect()->back()->with('error', 'Водитель Успешно Уволен');

    }
}
