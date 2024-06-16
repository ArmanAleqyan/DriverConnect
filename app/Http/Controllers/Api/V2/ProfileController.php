<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\JobRoutePoints;
use App\Models\CarAmenities;
use App\Models\CarCategory;
use App\Models\UserCar;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\JobTranzaksion;
class ProfileController extends Controller
{
        protected  $user_id ;

    public function __construct()
    {

        if (auth()->guard('api')->user() == null){
            return response()->json([
                'status' => false,
                'message' => 'No Auth user'
            ],401);
        }
        $this->user_id = auth()->guard('api')->user()->id;
        $this->user_id =250 ;
    }
    /**
     * @OA\Get(
     *     path="/api/get_order_history",
     *     summary="Get order history for the authenticated user",
     *     tags={"Orders"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Order history fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function get_order_history(){
       $get = Jobs::where('user_id', $this->user_id)->where('price', '>', 0)->wherein('status',['complete', 'cancelled'])->orderby('created_at', 'desc')->simplepaginate(15);

        $currentDate = now();
        $thirtyDaysAgo = now()->subDays(6);
        $daysAbbr = [
            'Понедельник' => 'Пн',
            'Вторник' => 'Вт',
            'Среда' => 'Ср',
            'Четверг' => 'Чт',
            'Пятница' => 'Пт',
            'Суббота' => 'Сб',
            'Воскресенье' => 'Вс'
        ];
        $currentDateTimestamp = strtotime($currentDate);
        $thirtyDaysAgoTimestamp = strtotime($thirtyDaysAgo);
        $i =0;
        $chart = [];
        for ($date = $thirtyDaysAgoTimestamp; $date <= $currentDateTimestamp; $date += 86400) {
            // Convert timestamp back to date format
            $formattedDate2 = date('Y-m-d', $date);

            $formatter = new \IntlDateFormatter(
                'ru_RU',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::NONE,
                'Europe/Moscow',
                \IntlDateFormatter::GREGORIAN,
                'dd EEEE'
            );
            $formattedDate = $formatter->format($date);
            $formattedDate = mb_convert_case($formattedDate, MB_CASE_TITLE, "UTF-8");
            foreach ($daysAbbr as $full => $abbr) {

                if (stripos($formattedDate, $full) !== false) {
                    $formattedDate = str_ireplace($full, $abbr, $formattedDate);
                }
            }
            $get_job_sum = Jobs::where('user_id',  $this->user_id)->wheredate('created_at', $formattedDate2)->sum('job_price_minus_fee');
            $chart[$i]['date'] =  $formattedDate;
            $chart[$i]['sum'] = str_replace(',', '', number_format($get_job_sum, 2) ) ;

            $i++;
        }

       $i = 0;
       foreach ($get as $job){
           $orderTime = $job->order_time_interval_from;
           if (!$orderTime instanceof Carbon) {
               $orderTime = Carbon::parse($orderTime);
           }
           $formattedOrderTime = $orderTime->format('H:i d F');
           $months = [
               'January' => 'января',
               'February' => 'февраля',
               'March' => 'марта',
               'April' => 'апреля',
               'May' => 'мая',
               'June' => 'июня',
               'July' => 'июля',
               'August' => 'августа',
               'September' => 'сентября',
               'October' => 'октября',
               'November' => 'ноября',
               'December' => 'декабря',
           ];
           foreach ($months as $english => $russian) {
               $formattedOrderTime = str_replace($english, $russian, $formattedOrderTime);
           }
           $payment_method = '';
           if ($job->payment_method == 'cashless'){
               $payment_method = 'Картой';
           }
           if ($job->payment_method == 'cash'){
               $payment_method = 'Наличные';
           }
           if ($job->payment_method == 'corp'){
               $payment_method = 'Корпоративная Оплата';
           }
           $dateTimeParts = explode(' ', $formattedOrderTime);
           $get_car = UserCar::where('id', $job->car_id )->first();
          $data['car_name_date'] =$get_car->mark->name.' '. $get_car->model->name." ".$get_car->year;
          $data['car_number'] =$get_car->number;
          $data['start_address'] =$job->address_from;
          $data['db_id'] =$job->id;
          $data['yandex_id'] =$job->yandex_id;
          $data['car_id'] =$get_car->id;
          $data['job_start_date'] =$dateTimeParts[1]." ".$dateTimeParts[2];
          $data['job_start_time'] =$dateTimeParts[0];
          $data['payment_type'] =$job->payment_method;
          $data['payment_type_show_name'] =$payment_method;
          $data['work_time'] =$job->timeDifferenceFormatted;
          $data['work_price_in_minute'] =$job->price_in_minute." ₽/мин";
          $data['work_km'] = $job->work_km." км";
          $data['price_in_km'] = $job->price_in_km." ₽/км";
          $data['work_price'] = $job->job_price_with_bonus." ₽";
          $data['work_fee'] ="- ".$job->job_fee." ₽";
          $data['work_price_minus_fee'] = $job->job_price_minus_fee." ₽";
          $data['address'] = JobRoutePoints::where('job_id' , $job->id)->select('id', 'address')->get();
         $get[ $i] = $data;
           $i++;
       }

        return response()->json([
           'status' => true,
           'data' =>  $get,
            'charter' => $chart
        ]);
    }

    public function get_balance_history(Request $request){
        $get = Jobs::where('user_id', $this->user_id)->orderby('created_at', 'desc')->where('price','>', 0)->wherein('status',['complete', 'cancelled'])->select('id','price','short_id','payment_method')->simplepaginate(15);

        foreach ($get as $job){
            $date = Carbon::parse($job->ended_at)->format('d-m-Y');
        $trannzakcion = JobTranzaksion::where('job_id',  $job->id)->get();
        if ($job->payment_method != 'cash'){
            $minus_sum  =  $trannzakcion->wherenotin('group_id',  ['platform_card','platform_corporate'])->sum('amount');
            $job['title'] = "Оплата по заказу #$job->short_id";
            $job['price'] = number_format($job->price - abs($minus_sum),2);
            $job['date'] = $date;
            $job['color'] = '#6db600'; // green
        }else{
            $minus_sum  =  $trannzakcion->where('group_id', '!=', 'cash_collected')->sum('amount');
            $job['title'] = "Списание комисси  по заказу #$job->short_id";
            $job['price'] = "$minus_sum";
            $job['date'] = $date;
            $job['color'] = '#ff5555'; // red
        }

        }


        return response()->json([
           'status' => true,
           'data'  => $get
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/get_tariff_and_option",
     *     summary="Get tariffs and options",
     *     tags={"Tariff"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="get_tariffs",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Economy"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="get_this_user_tariffs",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Economy"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="get_options",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Air Conditioning"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="get_this_user_options",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Air Conditioning"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function get_tariff_and_option(){
            $get_tariffs = CarCategory::get();
            $get_options = CarAmenities::wherein('id', [1,2])->get();

        $get_this_user_tariffs = auth()->user()->car->first() ? auth()->user()->car->first()->categories : null;
        $get_this_user_options = auth()->user()->car->first() ? auth()->user()->car->first()->amenities : null;


            return response()->json([
               'status' => true,
               'get_tariffs' => $get_tariffs,
               'get_this_user_tariffs' => $get_this_user_tariffs,
               'get_options' => $get_options,
               'get_this_user_options' => $get_this_user_options,

            ]);
    }

    /**
     * @OA\Post(
     *     path="/api/update_tariff_and_option",
     *     summary="Update tariffs and options for the user's car",
     *     tags={"Tariff"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="options",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="tariffs",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Данные успешно обнавлены"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="validation_error",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 additionalProperties={"type": "string"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error from Yandex API",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Some error message from Yandex API"
     *             )
     *         )
     *     )
     * )
     */

    public function update_tariff_and_option(Request $request){
        $rules=array(
            'options' => 'array',
            'options.*' => 'exists:car_amenities,id',
            'tariffs' => 'array',
            'tariffs.*' => 'exists:car_categories,id',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'validation_error' => true,
                'message' =>$validator->errors()
            ],400);
        }

        $car = auth()->user()->car->first();

        $amenities = $request->options??[];

        // Синхронизируем связи. Если массив пуст, все связи будут удалены
        $car->amenities()->sync($amenities);

        $car_category = $request->tariffs??[];
        $car->categories()->sync($car_category);

        $url = "https://fleet-api.taxi.yandex.net/v2/parks/vehicles/car?vehicle_id={$car->yandex_car_id}";


//     return $car;
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
//                'is_park_property' => true,
//                    'leasing_conditions' => [
//                        'company' => 'leasing company',
//                        'interest_rate' => '11.7',
//                        'monthly_payment' => 20000,
//                        'start_date' => '2022-01-01',
//                        'term' => 30
//                    ],
//                    'license_owner_id' => '6k054dfdb9i5345ifsdfvpfu8c8cc946',
//                    'ownership_type' => 'park',
                'status' => $car->status,
//                    'tariffs' => ['string']
            ],
            'vehicle_licenses' => [
//                    'licence_number' => '123456789',
                'licence_plate_number' => $car->callsign,
                'registration_certificate' => "$car->registration_cert"
            ],
            'vehicle_specifications' => [
//                    "body_number" =>'123456789',
                "brand" => $car->mark->name,
                "color" =>  $car->color,
                "mileage" => 0,
                "model" =>$car->model->name,
                "transmission" => "unknown",
                "vin" => $car->vin??$car->callsign,
                "year" => (int)$car->year
            ]
        ])->json();

        if (isset($response['message'])){
            return response()->json([
               'status' => false,
               'message' =>  'Что то пошло не так свяжитес с администратором',
               'yandex_message' =>   $response['message'],
            ],422);
        }else{
            return response()->json([
               'status' => true,
               'message' => 'Данные успешно обнавлены'
            ],200);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/add_user_in_archive",
     *     summary="Add user to archive and update their status",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully archived",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Водитель Успешно Уволен"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error from Yandex API",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error message from Yandex API"
     *             )
     *         )
     *     )
     * )
     */
    public function add_user_in_archive(Request $request){
        $get = User::where('id', auth()->user()->id)->first();


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

        return response()->json([
           'status' => true,
           'message' =>  'Водитель Успешно Уволен'
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/update_phone",
     *     summary="Update user phone number",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 example="+79999999999"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Phone number updated and confirmation code sent",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Код потвержденя отправлен на номер телефона"
     *             ),
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="validation_error",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 additionalProperties={"type": "string"}
     *             )
     *         )
     *     )
     * )
     */

    public function update_phone(Request $request){

        $rules=array(
            'phone' => 'required|unique:users,phone',
        );
        $messages = [
            'phone.unique' => 'Этот номер телефона уже существует.',
        ];
        $validator=Validator::make($request->all(),$rules,$messages);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'validation_error' => true,
                'message' =>$validator->errors()
            ],400);
        }




        $controller =  new \App\Http\Controllers\Api\V1\RegisterController;
       $send =  $controller->send_sms($request->phone);

       auth()->user()->update([
           'new_phone' => $request->phone,
          'new_phone_code' =>$send
       ]);

       return response()->json([
          'status' => true,
           'message' => 'Код потвержденя отправлен  на номер телефона',
//           'code' => $send
       ]);
    }
    /**
     * @OA\Post(
     *     path="/api/confirm_new_phone_code",
     *     summary="Confirm new phone number with a code",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Phone number successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Номер успешно обновлён"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="validation_error",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 additionalProperties={"type": "string"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Wrong phone code or error from Yandex API",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Wrong Phone"
     *             )
     *         )
     *     )
     * )
     */

    public function confirm_new_phone_code(Request $request){
        $rules=array(
            'code' => 'required',

        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'validation_error' => true,
                'message' =>$validator->errors()
            ],400);
        }

        $get = User::where('new_phone_code' ,$request->code)->where('id', auth()->user()->id)->first();

        if ($get == null){
            return response()->json([
               'status' => false,
               'message' => 'Wrong Phone'
            ],422);
        }




        $get_user = User::findorfail($get->id);

//        dd( );
        $get_region = \App\Models\Region::where('id', auth()->user()->park->region_id)->first();
        if ($get_region->key_id != null){
            $get_keys = \App\Models\RegionKey::where('id', $get_region->key_id)->first();
        }else{
            $get_keys = \App\Models\RegionKey::where('default',1)->first();
        }
        $get_work_rule = \App\Models\YandexWorkRule::where('key_id',$get_keys->id )->where('default', 1)->first();
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
            return response()->json([
               'status' => false,
               'message' =>  "Побробуйте немного позже"
            ],422);
        }

// Вы можете вывести результат или использовать его далее в своем коде






        $phone = auth()->user()->new_phone;

        $url = 'https://fleet-api.taxi.yandex.net/v2/parks/contractors/driver-profile';
        $get_drive_license_country = \App\Models\DriverLicense::where('id', auth()->user()->driver_license_country_id)->first();
//        dd($request->country_id );
        if (isset($responseData['person']['driver_license_experience']['total_since_date'])){
            $driver_license_experience_total_since_date = Carbon::parse($responseData['person']['driver_license_experience']['total_since_date'])->format('Y-m-d');
        }else{
            $driver_license_experience_total_since_date = Carbon::now()->format('Y-m-d');
        }



//        dd($driver_license_experience_total_since_date );
//        dd($responseData);
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
                    'birth_date' => Carbon::parse($get_user->date_of_birth)->format('Y-m-d'),
                    'country' =>  $responseData['person']['driver_license']['country'],
                    'expiry_date' => $responseData['person']['driver_license']['expiry_date'],
                    'issue_date' =>$responseData['person']['driver_license']['issue_date'],
                    'number' => $get_user->driver_license_number
                ],
//                'driver_license_experience' => [
//                    'total_since_date' =>$driver_license_experience_total_since_date
//                ],
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
                'work_status' => auth()->user()->work_status
            ]
        ])->json();

//        dd($response);
        if (isset($response['message'])){

                return response()->json([
                    'status' => false,
                    'message' =>   $response['message']
                ],422);

        }else{
            $get_user->update([
                'phone' => $phone,
                'new_phone_code' => null,
                'new_phone' => null,

            ]);
            return response()->json([
                'status' => true,
                'message' =>   'Номер успешно обновлён'
            ],200);

        }


    }
}
