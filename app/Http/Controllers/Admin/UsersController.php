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
use App\Models\JobCategory;
use Carbon\Carbon;

use function Illuminate\Foundation\Configuration\redirectTo;
use Illuminate\Http\Request;
use App\Models\UserCar;
use App\Models\CarAmenities;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{


    public function create_new_user(Request $request){
        $phone = preg_replace('/[^+\d]/', '', $request->phone);


        $get_user  =  User::where('phone', $phone)->first();

        if ($get_user != null){
            return redirect()->route('single_page_user', $get_user->id)->with('error', 'Такой Пользватель уже сушествует');
        }
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
        $X_Idempotency_Token =  Str::random(32);

        $work_rule = $get_work_rule->yandex_id;


        if ($request->job_category_ids != 3){
            if ($request->job_category_ids == 1){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_ids == 2){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_ids == 4){
                $yandex_prefix = '/v2/parks/contractors/auto-courier-profile';
            }

            $get_drive_license_country = DriverLicense::where('id', $request->driver_license_country_id)->first();

            $response = Http::withHeaders([
                'X-Park-ID' => $X_Park_ID,
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
                'X-Idempotency-Token' =>$X_Idempotency_Token,
                'Accept-Language' => 'ru'
            ])
                ->post(env('yandex_request_url').$yandex_prefix, [
                    'account' => [
                        'balance_limit' => '100',
                        'block_orders_on_balance_below_limit' => false,
//                        'payment_service_id' => '12345',
//                        'work_rule_id' => 'bc43tre6ba054dfdb7143ckfgvcby63e',
                        'work_rule_id' =>$work_rule
                    ],
                    'order_provider' => [
                        'partner' => false,
                        'platform' => true
                    ],
                    'person' => [
                        'contact_info' => [
//                            'address' => 'Moscow, Ivanovskaya Ul., bld. 40/2, appt. 63',
//                            'email' => 'example-email@example.com',
                            'phone' => $phone
                        ],
                        'driver_license' => [
                            'birth_date' =>Carbon::parse($request->birth_date)->format('Y-m-d'),
                            'country' => $get_drive_license_country->Alpha,
                            'expiry_date' => Carbon::parse($request->driver_license_expiry_date)->format('Y-m-d'),
                            'issue_date' => Carbon::parse($request->driver_license_issue_date)->format('Y-m-d'),
                            'number' => $request->driver_license_number
                        ],
                        'driver_license_experience' => [
                            'total_since_date' => $request->driver_license_experience_total_since_date.'-01-01'
                        ],
                        'full_name' => [
                            'first_name' =>$request->name ,
                            'last_name' =>$request->surname,
                            'middle_name' => $request->middle_name
                        ],
//                        'tax_identification_number' => '7743013902'
                    ],
                    'profile' => [
                        'hire_date' => Carbon::now()->format('Y-m-d')
                    ]
                ]);
            $responses =$response->json();

            if (isset($responses['contractor_profile_id'])){
                $created_user =   User::create([
                    'contractor_profile_id' =>  $responses['contractor_profile_id']??null,
                    'date_of_birth' =>  $request->birth_date,
                    'name' =>  $request->name,
                    'surname' =>  $request->surname,
                    'middle_name' =>  $request->middle_name,
                    'driver_license_expiry_date' =>  $request->driver_license_expiry_date,
                    'driver_license_issue_date' =>  $request->driver_license_issue_date,
                    'driver_license_number' =>  $request->driver_license_number,
                    'driver_license_experience_total_since_date' =>  $request->driver_license_experience_total_since_date,
                    'job_category_id' => $request->job_category_ids,
                    'region_id' => $request->region_id,
                    'create_account_status' =>  1,
                    'park_id' => $get_keys->id,
                    'phone' => $phone,
                    'work_rule_id' =>$get_work_rule->id,
                ]);
//                return redirect()->back()->with('created', 'Водитель успешно добавлен');
                return redirect()->route('single_page_user', $created_user->id)->with('created', 'Водитель успешно добавлен');
            }else{
                return redirect()->back()->with('error',$responses['message'])->withInput();
            }
        }elseif($request->job_category_ids == 3){
            $data = [
                'birth_date' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
//                'city' => 'Москва',
                'full_name' => [
                    'first_name' =>$request->name ,
                    'last_name' =>$request->surname ,
                    'middle_name' => $request->middle_name
                ],
                'phone' => $phone,
//                'registration_country_code' => 'RU',
                'work_rule_id' =>$work_rule
            ];

            $headers = [
                'X-Park-ID' => $X_Park_ID,
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
                'X-Idempotency-Token' =>$X_Idempotency_Token,
                'Accept-Language' => 'ru'
            ];

            $response = Http::withHeaders($headers)->post('https://fleet-api.taxi.yandex.net/v2/parks/contractors/walking-courier-profile', $data);

            $responses =$response->json();
            if (isset($responses['contractor_profile_id'])){
                $created_user =  User::create([
                    'contractor_profile_id' =>  $responses['contractor_profile_id'],
                    'name'  =>$request->name,
                    'surname'  =>$request->surname,
                    'middle_name'  =>$request->middle_name,
                    'job_category_id' => $request->job_category_ids,
                    'date_of_birth' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
                    'region_id' => $request->region_id,
                    'work_rule_id' =>$get_work_rule->id,
                    'park_id' => $get_keys->id,
                    'create_account_status' =>  1,
                    'phone' => $phone,
                ]);
                return redirect()->route('single_page_user', $created_user->id)->with('created', 'Курьер успешно добавлен');

            }else{
                return redirect()->back()->with('error',$responses['message'])->withInput();
            }
        }

    }

    public function create_user_page(){
        $get_job_category = JobCategory::get();
        $get_regions =  Region::get();
        $get_yandex_worrk_status =  UserWorkingStatusForYandex::get();
        $get_country_drive_licenze =  DriverLicense::get();
        return view('admin.Users.create' , compact('get_country_drive_licenze','get_yandex_worrk_status','get_regions', 'get_job_category'));
    }

    public function create_user_in_yandex(Request $request){

        $phone = preg_replace('/[^+\d]/', '', $request->phone);


        $get_user  =  User::where('phone', $phone)->first();

        if ($get_user != null){
            return redirect()->route('single_page_user', $get_user->id)->with('error', 'Такой Пользватель уже сушествует');
        }

      $get_user = User::where('id', $request->user_id)->first();


        $get_user ->update([
            'job_category_id' =>   $request->job_category_ids
        ]);
        $get_region = Region::where('id', $request->region_id)->first();
        if ($get_region->key_id != null){
            $get_keys = RegionKey::where('id', $get_region->key_id)->first();
        }else{
            $get_keys = RegionKey::where('default',1)->first();
        }
        $phone = preg_replace('/[^+\d]/', '', $request->phone);
        $get_work_rule = YandexWorkRule::where('key_id',$get_keys->id )->where('default', 1)->first();
        $X_Park_ID =$get_keys->X_Park_ID??env('X_Park_ID');
        $X_Client_ID = $get_keys->X_Client_ID??env('X_Client_ID');
        $X_API_Key = $get_keys->X_API_Key??env('X_API_Key');
        $X_Idempotency_Token =  Str::random(32);

        $work_rule = $get_work_rule->yandex_id;


        if ($request->job_category_ids != 3){
            if ($request->job_category_ids == 1){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_ids == 2){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_ids == 4){
                $yandex_prefix = '/v2/parks/contractors/auto-courier-profile';
            }

            $get_drive_license_country = DriverLicense::where('id', $request->driver_license_country_id)->first();

            $response = Http::withHeaders([
                'X-Park-ID' => $X_Park_ID,
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
                'X-Idempotency-Token' =>$X_Idempotency_Token,
                'Accept-Language' => 'ru'
            ])
                ->post(env('yandex_request_url').$yandex_prefix, [
                    'account' => [
                        'balance_limit' => '100',
                        'block_orders_on_balance_below_limit' => false,
//                        'payment_service_id' => '12345',
//                        'work_rule_id' => 'bc43tre6ba054dfdb7143ckfgvcby63e',
                        'work_rule_id' =>$work_rule
                    ],
                    'order_provider' => [
                        'partner' => false,
                        'platform' => true
                    ],
                    'person' => [
                        'contact_info' => [
//                            'address' => 'Moscow, Ivanovskaya Ul., bld. 40/2, appt. 63',
//                            'email' => 'example-email@example.com',
                            'phone' => $phone
                        ],
                        'driver_license' => [
                            'birth_date' =>Carbon::parse($request->birth_date)->format('Y-m-d'),
                            'country' => $get_drive_license_country->Alpha,
                            'expiry_date' => Carbon::parse($request->driver_license_expiry_date)->format('Y-m-d'),
                            'issue_date' => Carbon::parse($request->driver_license_issue_date)->format('Y-m-d'),
                            'number' => $request->driver_license_number
                        ],
                        'driver_license_experience' => [
                            'total_since_date' => $request->driver_license_experience_total_since_date.'-01-01'
                        ],
                        'full_name' => [
                            'first_name' =>$request->name ,
                            'last_name' =>$request->surname,
                            'middle_name' => $request->middle_name
                        ],
//                        'tax_identification_number' => '7743013902'
                    ],
                    'profile' => [
                        'hire_date' => Carbon::now()->format('Y-m-d')
                    ]
                ]);
            $responses =$response->json();

            if (isset($responses['contractor_profile_id'])){
               $get_user->update([
                    'contractor_profile_id' =>  $responses['contractor_profile_id']??null,
                    'date_of_birth' =>  $request->birth_date,

                    'name' =>  $request->name,
                    'surname' =>  $request->surname,
                    'middle_name' =>  $request->middle_name,
                    'driver_license_expiry_date' =>  $request->driver_license_expiry_date,
                    'driver_license_issue_date' =>  $request->driver_license_issue_date,
                    'driver_license_number' =>  $request->driver_license_number,
                    'driver_license_experience_total_since_date' =>  $request->driver_license_experience_total_since_date,
                    'job_category_id' => $request->job_category_ids,
                    'region_id' => $request->region_id,
                    'create_account_status' =>  1,
                   'park_id' => $get_keys->id,

                    'phone' => $phone,
                    'work_rule_id' =>$get_work_rule->id,
                ]);
                return redirect()->back()->with('created', 'Водитель успешно добавлен');
            }else{
                return redirect()->back()->with('error',$responses['message'])->withInput();
            }
        }elseif($request->job_category_ids == 3){
            $data = [
                'birth_date' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
//                'city' => 'Москва',
                'full_name' => [
                    'first_name' =>$request->name ,
                    'last_name' =>$request->surname ,
                    'middle_name' => $request->middle_name
                ],
                'phone' => $phone,
                'work_rule_id' =>$work_rule
            ];

            $headers = [
                'X-Park-ID' => $X_Park_ID,
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
                'X-Idempotency-Token' =>$X_Idempotency_Token,
                'Accept-Language' => 'ru'
            ];

            $response = Http::withHeaders($headers)->post('https://fleet-api.taxi.yandex.net/v2/parks/contractors/walking-courier-profile', $data);

            $responses =$response->json();
            if (isset($responses['contractor_profile_id'])){
               $get_user->update([
                    'contractor_profile_id' =>  $responses['contractor_profile_id'],
                    'name'  =>$request->name,
                    'surname'  =>$request->surname,
                    'middle_name'  =>$request->middle_name,
                    'job_category_id' => $request->job_category_ids,
                    'date_of_birth' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
                    'region_id' => $request->region_id,
                    'work_rule_id' =>$get_work_rule->id,
                    'create_account_status' =>  1,
                   'phone' => $phone,
                ]);
                return redirect()->back()->with('created', 'Курьер успешно добавлен');
            }else{
                return redirect()->back()->with('error',$responses['message'])->withInput();
            }
        }
    }

    public function update_user(Request $request){
        $get_user = User::findorfail($request->user_id);


        $phone = preg_replace('/[^+\d]/', '', $request->phone);


        $get_user_validation  =  User::where('phone', $phone)->where('id','!=', $request->user_id)->first();

        if ($get_user_validation != null){
            return redirect()->route('single_page_user', $get_user_validation->id)->with('error', 'Такой Пользватель уже сушествует');
        }
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


        $phone = preg_replace('/[^+\d]/', '', $request->phone);
        $url = 'https://fleet-api.taxi.yandex.net/v2/parks/contractors/driver-profile';
        $response = Http::withHeaders([
            'X-Park-ID' => $X_Park_ID,
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key
        ])->put($url . '?contractor_profile_id=' . $get_user->contractor_profile_id, [
            'account' => [
                'work_rule_id' => $work_rule
            ],
            'person' => [
                'contact_info' => [
                    'phone' =>$phone
                ],
                'driver_license' => [
                    'birth_date' => $responseData['person']['driver_license']['birth_date']??null,
                    'country' => $responseData['person']['driver_license']['country'],
                    'expiry_date' => $responseData['person']['driver_license']['expiry_date'],
                    'issue_date' => $responseData['person']['driver_license']['issue_date'],
                    'number' => $get_user->driver_license_number
                ],
                'driver_license_experience' => [
                    'total_since_date' => $responseData['person']['driver_license_experience']['total_since_date']
                ],
                'full_name' => [
                    'first_name' => $get_user->name,
                    'last_name' => $get_user->surname,
                    'middle_name' => $get_user->middle_name
                ],
            ],

            'profile' => [
                'comment' => 'great driver',
                'feedback' => 'great driver',
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
//        $get_users = User::where('create_account_status', 0)->where('role_id','!=', 1) ->where('work_status','!=', 'fired')->orwhere('add_car_status',0)->where('role_id','!=', 1)->orwhere('sended_in_yandex_status',  0)->where('work_status','!=', 'fired')->where('role_id','!=', 1)->get();
        $get_users = User::where(function($query) {
            $query->where('create_account_status', 0)
                ->where('role_id', '!=', 1)
//                ->where('work_status', '!=', 'fired')
            ;
        })->orWhere(function($query) {
            $query->where('add_car_status', 0)
                ->where('role_id', '!=', 1)
           ->where('work_status', '!=', 'fired')
            ;
        })->orWhere(function($query) {
            $query->where('sended_in_yandex_status', 0)
                ->where('role_id', '!=', 1)
            ;
        })

            ->get();
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
        $get_job_category = JobCategory::get();
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
        return view('admin.Users.single', compact('get_job_category','car_working_status','get_car_category','get_car_amenities','get','get_car','get_regions','get_country_drive_licenze','get_marks','get_colors','get_yandex_worrk_status'));
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
