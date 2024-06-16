<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriverLicense;
use App\Models\JobCategory;
use App\Models\Region;
use App\Models\RegionKey;
use App\Models\Mark;
use App\Models\Models;
use App\Models\UserCar;
use App\Models\CarColor;
use App\Models\CarCategory;
use App\Models\YandexWorkRule;
use App\Models\SmsSettings;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class RegisterController extends Controller
{


    public function logout(Request $request){



        $user = \auth()->user();
        $user->tokens()->delete();



        return response()->json([
            'status' => true,
            'message' => 'Logouted'
        ],200);

    }

    /**
     * @OA\Get(
     *     path="/api/auth_user_info",
     *     tags={"User"},
     *     summary="Get authenticated user information",
     *     description="Endpoint to retrieve information about the authenticated user.",
     *     operationId="authUserInfo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    public function auth_user_info(){
        $get_user = User::where('id', auth()->user()->id)->first();

        $get_car = UserCar::where('user_id', auth()->user()->id)->with('model', 'mark')->where('connected_status', 1)->first();
        return response()->json([
           'status' => true,
           'user' => $get_user,
            'car' => $get_car
        ]);
    }

    public function login_in_yandex(){
        $X_Park_ID = env('X_Park_ID');
        $X_Client_ID = env('X_Client_ID');
        $X_API_Key = env('X_API_Key');

        $response = Http::withHeaders([
            'X-Client-ID' => $X_Client_ID,
            'X-Api-Key' => $X_API_Key,
            'Accept-Language' =>'ru',
        ])
            ->post('https://fleet-api.taxi.yandex.net/v1/parks/driver-profiles/list', [
                'query' => [
                    'park' => [
                        'id' => $X_Park_ID
                    ]
                ]
            ]);


        return $response->json();
    }
    /**
     * @OA\Post(
     *     path="/api/register_or_login",
     *     summary="Register a new user or login an existing user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User phone number",
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", description="Status of the operation", example="true"),
     *             @OA\Property(property="message", type="string", description="Message indicating the status", example="Code sent to your phone"),
     *             @OA\Property(property="code", type="string", description="Verification code sent to the user's phone", example="1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", description="Status of the operation", example="false"),
     *             @OA\Property(property="validation_error", type="boolean", description="Indicates whether the error is due to validation", example="true"),
     *             @OA\Property(property="message", type="object", description="Validation error messages", example={"phone": {"The phone field is required"}})
     *         )
     *     )
     * )
     */
    public function register_or_login(Request  $request){
        $rules=array(
            'phone' => 'required'
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

        $get_user = User::where('phone', $request->phone)->first();

        if ($get_user != null && $get_user->work_status == 'fired'){
            return response()->json([
               'status' => false,
                'message' => 'Вы уволены  Пожалусто свяжитес с администратором'
            ],422);
        }
    $send_message =    $this->send_sms($request->phone);
             User::updateorcreate(['phone'=> $request->phone], [
            'phone' => $request->phone,
            'phone_verify_code' =>$send_message,
        ])->first();

        return  response()->json([
           'status' => true,
           'message' => 'Code Send ed in Your Phone',
           'code' => $send_message
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/confirm_login_or_register",
     *     summary="Confirm login or register by verifying phone number",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User phone number and verification code",
     *         @OA\JsonContent(
     *             required={"phone", "code"},
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="code", type="string", example="1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", description="Status of the operation", example="true"),
     *             @OA\Property(property="message", type="string", description="Message indicating the status", example="confirmed"),
     *             @OA\Property(property="token", type="string", description="Access token for authenticated user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", description="Status of the operation", example="false"),
     *             @OA\Property(property="validation_error", type="boolean", description="Indicates whether the error is due to validation", example="true"),
     *             @OA\Property(property="message", type="object", description="Validation error messages", example={"phone": {"The phone field is required"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", description="Status of the operation", example="false"),
     *             @OA\Property(property="message", type="string", description="Message indicating the error", example="Неверный код подтверждения")
     *         )
     *     )
     * )
     */
    public function confirm_login_or_register(Request  $request){
        $rules=array(
            'phone' => 'required',
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

        $get = User::where('phone', $request->phone)->where('phone_verify_code', $request->code)->first();

        if ($get == null){
            return  response()->json([
               'status' => false,
               'message' => 'Неверный код потверждения'
            ],422);
        }else{
            $get->update([
               'register_status' => 1,
               'phone_verify_code' => 1,
            ]);

            $token = $get->createToken('VerificationToken')->accessToken;
            return  response()->json([
               'status' => true,
               'message' => 'confirmed',
                'user' =>$get,
                'token' =>$token
            ]);
        }
    }

    function send_sms($phone)
    {

        $get_login_password = SmsSettings::first();
        $data = $phone;
        $rand = mt_rand(100000,999999);
        $client = new Client();
        $login = $get_login_password->login??null;
        $password_sms =$get_login_password->password??null;
        $response = Http::get('https://smsc.ru/sys/send.php', [
            'login' => $login,
            'psw' => $password_sms,
            'phones' => $phone,
            'sender' => 'Arman',
            'mes' => "Ваш код подтверждения   $rand",
        ]);




        return $rand;

    }

    /**
     * @OA\Post(
     *     path="/api/upload_car_license",
     *     tags={"Car"},
     *     summary="Upload car license photos",
     *     description="Endpoint to upload front and back photos of a car license and extract relevant information from the photos.",
     *     operationId="uploadCarLicense",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Car license photos to upload",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"car_license_front_photo", "car_license_back_photo"},
     *                 @OA\Property(
     *                     property="car_license_front_photo",
     *                     description="Front photo of the car license",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     property="car_license_back_photo",
     *                     description="Back photo of the car license",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="year", type="string", example="2023"),
     *                 @OA\Property(property="callsign", type="string", example="ABC123"),
     *                 @OA\Property(property="vin", type="string", example="1HGBH41JXMN109186"),
     *                 @OA\Property(property="color_name", type="string", example="Red"),
     *                 @OA\Property(property="mark_name", type="string", example="Toyota"),
     *                 @OA\Property(property="model_name", type="string", example="Camry"),
     *                 @OA\Property(property="licence_plate_number", type="string", example="XYZ789"),
     *                 @OA\Property(property="car_license_front_photo", type="string", example="front_photo.jpg"),
     *                 @OA\Property(property="car_license_back_photo", type="string", example="back_photo.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="validation_error", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    public function upload_car_license(Request $request){
        $rules=array(
            'car_license_front_photo' => 'required|image',
            'car_license_back_photo' => 'required|image'
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
        $req_front_photo = $request->car_license_front_photo;
        $base64Image =  base64_encode($req_front_photo->getContent());


        $extension = $req_front_photo->getClientOriginalExtension();
        $new_controller = new \App\Http\Controllers\Api\V1\GetParametersController;
        $photo_data =  $new_controller->get_photo_data($base64Image,$extension ,'vehicle-registration-front');


        $driver_license_front_photo = $request->car_license_front_photo;
        $destinationPath = 'uploads';
        $originalFile =  time().uniqid(). '.' . $driver_license_front_photo->getClientOriginalExtension();
        $driver_license_front_photo->move($destinationPath, $originalFile);


        $driver_license_back_photo = $request->car_license_back_photo;
        $destinationPath = 'uploads';
        $originalFile_back =  time().'-'.uniqid(). '.' . $driver_license_back_photo->getClientOriginalExtension();
        $driver_license_back_photo->move($destinationPath, $originalFile_back);


        $result = [];
        foreach ($photo_data as $item) {
            $key = $item['name'];
            if ($key == 'stsfront_car_year' ){
                $key = 'year';
            }
            if ($key == 'stsfront_car_number' ){
                $key = 'callsign';
            }
            if ($key == 'stsfront_vin_number' ){
                $key = 'vin';
            }
            if ($key == 'stsfront_car_color' ){
                $key = 'color_name';
            }
            if ($key == 'stsfront_car_brand' ){
                $key = 'mark_name';
            }
            if ($key == 'stsfront_car_model' ){
                $key = 'model_name';
            }
            if ($key == 'stsfront_sts_number' ){
                $key = 'licence_plate_number';
            }
            $result[$key] = strtoupper($item['text']);
        }

        $result['car_license_front_photo'] = $originalFile;
        $result['car_license_back_photo'] = $originalFile_back;

//        if ($photo_data == null){
//            return response()->json([
//                'status' => false,
//                'message' => 'Что то пошло не так попробуцте немного позже'
//            ],422);
//        }

        $data = $result;
        return response()->json([
            'status' =>true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/upload_photo_driver_license",
     *     tags={"Driver License"},
     *     summary="Upload driver license photos",
     *     description="Endpoint to upload front and back photos of a driver license and extract relevant information.",
     *     operationId="uploadPhotoDriverLicense",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"driver_license_front_photo", "driver_license_back_photo"},
     *                 @OA\Property(
     *                     property="driver_license_front_photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Front photo of the driver license"
     *                 ),
     *                 @OA\Property(
     *                     property="driver_license_back_photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Back photo of the driver license"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", description="Extracted information from driver license photos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="validation_error", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Что то пошло не так попробуцте немного позже")
     *         )
     *     )
     * )
     */
    public function upload_photo_driver_license(Request $request){

        $rules=array(
            'driver_license_front_photo' => 'required|image',
            'driver_license_back_photo' => 'required|image',
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
        $req_front_photo = $request->driver_license_front_photo;
        $base64Image =  base64_encode($req_front_photo->getContent());

        try{
            $old_front_photo = public_path('uploads/'.auth()->user()->driver_license_front_photo);
            $old_back_photo = public_path('uploads/'.auth()->user()->driver_license_back_photo);
            if (file_exists($old_front_photo)) {
                unlink($old_front_photo);
            }
            if (file_exists($old_back_photo)) {
                unlink($old_back_photo);
            }
        }catch (\Exception $e){

        }

        $driver_license_front_photo = $request->driver_license_front_photo;
        $destinationPath = 'uploads';
        $originalFile =  time().uniqid(). '.' . $driver_license_front_photo->getClientOriginalExtension();
        $driver_license_front_photo->move($destinationPath, $originalFile);
        $driver_license_back_photo = $request->driver_license_back_photo;
        $destinationPath = 'uploads';
        $originalFile_back =  time().'-'.uniqid(). '.' . $driver_license_back_photo->getClientOriginalExtension();
        $driver_license_back_photo->move($destinationPath, $originalFile_back);
        auth()->user()->update([
           'driver_license_front_photo' =>$originalFile,
           'driver_license_back_photo' =>$originalFile_back,
        ]);
        $extension = $driver_license_front_photo->getClientOriginalExtension();
        $new_controller = new \App\Http\Controllers\Api\V1\GetParametersController;
       $photo_data =  $new_controller->get_photo_data($base64Image,$extension ,'driver-license-front');


        $result = [];
        if (isset($photo_data)){
            foreach ($photo_data as $item) {
                $key = $item['name'];
                if ($key == 'middle_name' ){
                    $key = 'scanning_person_full_name_middle_name';
                }
                if ($key == 'surname' ){
                    $key = 'scanning_person_full_name_last_name';
                }
                if ($key == 'name' ){
                    $key = 'scanning_person_full_name_first_name';
                }
                if ($key == 'birth_date' ){
                    $key = 'scanning_birth_date';
                    $item['text'] = Carbon::parse( $item['text'])->format('Y-m-d');
                }
                if ($key == 'expiration_date' ){
                    $key = 'driver_license_expiry_date';
                    $item['text'] = Carbon::parse( $item['text'])->format('Y-m-d');
                }
                if ($key == 'issue_date' ){
                    $key = 'driver_license_issue_date';
                    $item['text'] = Carbon::parse( $item['text'])->format('Y-m-d');
                }
                if ($key == 'number' ){
                    $key = 'driver_license_number';
                }
                $result[$key] = strtoupper($item['text']);
            }
        }

//        if ($photo_data == null){
//           return response()->json([
//              'status' => false,
//               'message' => 'Что то пошло не так попробуцте немного позже'
//           ],422);
//       }
        auth()->user()->update([
           'driver_license_front_photo' =>$originalFile,
           'driver_license_back_photo' =>$originalFile_back,
        ]);
        $data = $result;
        return response()->json([
           'status' =>true,
           'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/create_account",
     *     summary="Создание учетной записи пользователя",
     *     tags={"Account"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные для создания учетной записи",
     *         @OA\JsonContent(
     *             required={"job_category_id", "region_id"},
     *             @OA\Property(property="job_category_id", type="integer", description="ID категории работы", example=1),
     *             @OA\Property(property="region_id", type="integer", description="ID региона", example=1),
     *             @OA\Property(property="birth_date", type="string", description="Дата рождения ", example="1997-09-15"),
     *             @OA\Property(property="country_id", type="integer", description="ID country_id", example=1),
     *             @OA\Property(property="driver_license_expiry_date", type="string", format="date", description="Дата истечения срока"),
     *             @OA\Property(property="driver_license_issue_date", type="string", format="date", description="Дата выдачи водительского удостоверения"),
     *             @OA\Property(property="driver_license_number", type="string", description="Номер водительского удостоверения"),
     *             @OA\Property(property="driver_license_experience_total_since_date", type="integer", description="Общий опыт вождения с указанной даты"),
     *             @OA\Property(property="person_full_name_first_name", type="string", description="Имя пользователя"),
     *             @OA\Property(property="scanning_person_full_name_first_name", type="string", description="Имя пользователя"),
     *             @OA\Property(property="person_full_name_last_name", type="string", description="Фамилия пользователя"),
     *             @OA\Property(property="scanning_person_full_name_last_name", type="string", description="Фамилия пользователя"),
     *             @OA\Property(property="person_full_name_middle_name", type="string", description="Отчество пользователя"),
     *             @OA\Property(property="scanning_person_full_name_middle_name", type="string", description="Отчество пользователя"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешное создание учетной записи",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true, description="Статус операции"),
     *             @OA\Property(property="message", type="string", example="Вы успешно создали аккаунт пользователя", description="Сообщение о результате операции"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации данных",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false, description="Статус операции"),
     *             @OA\Property(property="validation_error", type="boolean", example=true, description="Ошибка валидации"),
     *             @OA\Property(property="message", type="object", description="Сообщение об ошибке валидации", example={"job_category_id": {"The job category ID field is required"}}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка аутентификации",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false, description="Статус операции"),
     *             @OA\Property(property="message", type="string", example="Неавторизованный доступ"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false, description="Статус операции"),
     *             @OA\Property(property="message", type="string", example="Внутренняя ошибка сервера"),
     *         )
     *     )
     * )
     */

    public function create_account(Request  $request){
        $rules=array(
            'job_category_id' => 'required|exists:job_categories,id',
            'region_id' => 'required|exists:regions,id',
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
        auth()->user()->update([
           'job_category_id' =>   $request->job_category_id
        ]);
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

        if ($request->job_category_id == 1 || $request->job_category_id == 4 || $request->job_category_id == 2){
            $rules=array(
                'country_id' => 'required|exists:driver_licenses,id',
                'driver_license_experience_total_since_date' => 'required|integer',
                'driver_license_expiry_date' => 'required|date_format:Y-m-d',
                'driver_license_number' => 'required',
                'driver_license_issue_date' => 'required|date_format:Y-m-d',
                'person_full_name_first_name' => 'required',
                'person_full_name_last_name' => 'required',
                'person_full_name_middle_name' => 'required',
//                'scanning_birth_date' => 'required|date_format:Y-m-d',
                'birth_date' => 'required|date_format:Y-m-d',

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



            $send_or_not_send = 1;
            if (isset($request->scanning_person_full_name_first_name) &&$request->scanning_person_full_name_first_name != $request->person_full_name_first_name){
                $send_or_not_send = 0;
            }
            if (isset($request->scanning_person_full_name_last_name) && $request->scanning_person_full_name_last_name != $request->person_full_name_last_name){
                $send_or_not_send = 0;
            }
            if (isset($request->scanning_person_full_name_middle_name ) && $request->scanning_person_full_name_middle_name != $request->person_full_name_middle_name){
                $send_or_not_send = 0;
            }
            if (isset($request->scanning_birth_date ) && $request->scanning_birth_date != $request->birth_date){
                $send_or_not_send = 0;
            }

            if ($request->job_category_id == 1){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_id == 2){
                $yandex_prefix =  '/v2/parks/contractors/driver-profile';
            }
            if ($request->job_category_id == 4){
                $yandex_prefix = '/v2/parks/contractors/auto-courier-profile';
            }

            $get_drive_license_country = DriverLicense::where('id', $request->country_id)->first();


//            $send_or_not_send = 1;
            if ($send_or_not_send != 0){

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
                                'phone' => auth()->user()->phone
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
                                'first_name' =>$request->person_full_name_first_name ,
                                'last_name' =>$request->person_full_name_last_name ,
                                'middle_name' => $request->person_full_name_middle_name
                            ],
//                        'tax_identification_number' => '7743013902'
                        ],
                        'profile' => [
                            'hire_date' => Carbon::now()->format('Y-m-d')
                        ]
                    ]);
                $responses =$response->json();
            }
            auth()->user()->update([
                'contractor_profile_id' =>  $responses['contractor_profile_id']??null,
                'date_of_birth' =>  $request->birth_date,
                'name' =>  $request->person_full_name_first_name,
                'surname' =>  $request->person_full_name_last_name,
                'middle_name' =>  $request->person_full_name_middle_name,
                'driver_license_expiry_date' =>  $request->driver_license_expiry_date,
                'driver_license_issue_date' =>  $request->driver_license_issue_date,
                'driver_license_number' =>  $request->driver_license_number,
                'driver_license_experience_total_since_date' =>  $request->driver_license_experience_total_since_date,
                'job_category_id' => $request->job_category_id,
                'region_id' => $request->region_id,
                'park_id' => $get_keys->id,
                'create_account_status' =>  1,
                'work_rule_id' =>$get_work_rule->id,
                'sended_in_yandex_status' =>$send_or_not_send
            ]);


            if (isset($responses['message'] ) && $responses['message'] == 'duplicate_driver_license'){
                return  response()->json([
                    'status' => false,
                    'message' => 'Неверный номер ВУ',
                    'yandex_error' =>  $responses??null
                ],422);
            }


            if (isset($responses['contractor_profile_id'])){
                return  response()->json([
                    'status' => true,
                    'message' => 'Вы успешно создали акаунт пользвателя',

                ]);
            }else{
                return  response()->json([
                   'status' => true,
                   'message' => 'Вы успешно создали акаунт пользвателя подождите с вами свяжетса менеджер',
                   'yandex_error' =>  $responses??null
                ],422);
            }
        }if ($request->job_category_id == 3){
            $rules=array(
                'birth_date' => 'required|date_format:Y-m-d',
                'person_full_name_first_name' => 'required',
                'person_full_name_last_name' => 'required',
                'person_full_name_middle_name' => 'required',
//                'driver_license_experience_total_since_date' => 'required|integer',
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

            $data = [
                'birth_date' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
//                'city' => 'Москва',
                'full_name' => [
                    'first_name' =>$request->person_full_name_first_name ,
                    'last_name' =>$request->person_full_name_last_name ,
                    'middle_name' => $request->person_full_name_middle_name
                ],
                'phone' => auth()->user()->phone,
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
                auth()->user()->update([
                    'contractor_profile_id' =>  $responses['contractor_profile_id'],
                    'name'  =>$request->person_full_name_first_name,
                    'surname'  =>$request->person_full_name_last_name,
                    'middle_name'  =>$request->person_full_name_middle_name,
                    'job_category_id' => $request->job_category_id,
                    'date_of_birth' =>    Carbon::parse($request->birth_date)->format('Y-m-d'),
                    'region_id' => $request->region_id,
                    'work_rule_id' =>$get_work_rule->id,
                    'park_id' => $get_keys->id,
                    'create_account_status' =>  1,
                ]);

                return  response()->json([
                   'status' => true,
                    'message' => 'Ваш Акаунт Успешно создался'
                ]);
            }else{
                return  response()->json([
                    'status' => false,
                    'message' => 'Что то пошло не так',
                    'yandex_error' =>  $responses??null
                ]);
            }
        }



    }
    /**
     * @OA\Post(
     *     path="/api/create_new_car",
     *     tags={"Cars"},
     *     summary="Create a new car",
     *     description="Endpoint to create a new car with provided details.",
     *     operationId="createNewCar",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"callsign", "licence_plate_number", "registration_certificate", "mark_name", "model_name", "year", "vin", "color_name"},
     *             @OA\Property(property="callsign", type="string", example="ABC123"),
     *             @OA\Property(property="licence_plate_number", type="string", example="ABC1234"),
     *             @OA\Property(property="registration_certificate", type="string", example="Registration Certificate"),
     *             @OA\Property(property="mark_name", type="string", example="Toyota"),
     *             @OA\Property(property="model_name", type="string", example="Camry"),
     *             @OA\Property(property="year", type="integer", example=2022),
     *             @OA\Property(property="vin", type="string", example="VIN123456789"),
     *             @OA\Property(property="color_name", type="string", example="Red")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Машина успешно создана")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="validation_error", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Что то пошло нне так"),
     *             @OA\Property(property="yandex_error", type="object", example={"error": "Something went wrong with Yandex API"})
     *         )
     *     )
     * )
     */
    public function create_new_car(Request $request){
        $rules=array(
            'callsign' => 'required',
            'car_license_back_photo' => 'required|string',
            'car_license_front_photo' => 'required|string',
            'licence_plate_number' => 'required',
//            'registration_certificate' => 'required',
            'mark_name' => 'required',
            'model_name' => 'required',
            'year' => 'required|integer',
            'vin' => 'required',
            'color_name' => 'required',
//            'region_id' => 'required|exists:regions,id',
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
        $get_region = Region::where('id', auth()->user()->region_id??1)->first();
        if ($get_region->key_id != null){
            $get_keys = RegionKey::where('id', $get_region->key_id)->first();
        }else{
            $get_keys = RegionKey::where('default',1)->first();
        }
        $X_Park_ID =$get_keys->X_Park_ID??env('X_Park_ID');
        $X_Client_ID = $get_keys->X_Client_ID??env('X_Client_ID');
        $X_API_Key = $get_keys->X_API_Key??env('X_API_Key');
        $X_Idempotency_Token =  Str::random(32);

        $get_marks = Mark::updateorcreate(['name' =>  $request->mark_name], ['name' =>  $request->mark_name]);
        $get_models = Models::updateorcreate(['mark_id' =>$get_marks->id,'name' =>  $request->model_name], ['mark_id' =>$get_marks->id,'name' =>  $request->model_name]);
        $get_color = CarColor::where('name' ,  $request->color_name)->first();
        if ($get_color == null){
            $get_color = CarColor::where('id' ,  1)->first();
        }
        $get_categories = CarCategory::query();
        $user_category = auth()->user()->job_category_id;
        if ($user_category == 1 ){
            $get_categories->wherein('id', [1,7,8,3,15,5]);
        }
        if ($user_category == 2 ){
            $get_categories->wherein('id', [22]);
        }
        if ($user_category == 3 ){
            $get_categories->wherein('id', [5]);
        }
        $get_categories = $get_categories->get('name')->pluck('name')->toarray();


        $response = Http::withHeaders([
            'X-Park-ID' =>$X_Park_ID,
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key,
            'X-Idempotency-Token' =>$X_Idempotency_Token,
            'Accept-Language' => 'ru'
        ])->post('https://fleet-api.taxi.yandex.net/v2/parks/vehicles/car', [
            "cargo" => [
                "cargo_hold_dimensions" => [
                    "height" => 0,
                    "length" => 0,
                    "width" => 0
                ],
//            ],
                "cargo_loaders" => 0,
                "carrying_capacity" => 0
            ],
//            "child_safety" => [
//                "booster_count" => 2
//            ],
            "park_profile" => [
//                "amenities" => [
//                    "wifi"
//                ],
                "callsign" => $request->callsign,
                "categories" =>$get_categories,
                "comment" => "good car",
//                "fuel_type" => "gas",
                "is_park_property" => true,
//                "leasing_conditions" => [
//                    "company" => "leasing company",
//                    "interest_rate" => "11.7",
//                    "monthly_payment" => 20000,
//                    "start_date" => "2022-01-01",
//                    "term" => 30
//                ],
//                "license_owner_id" => "6k054dfdb9i5345ifsdfvpfu8c8cc946",
                "ownership_type" => "park",
                "status" => "working",
//                "tariffs" => [
//                    "string"
//                ]
            ],
            "vehicle_licenses" => [
//                "licence_number" => "123456789",
                "licence_plate_number" => $request->licence_plate_number,
                "registration_certificate" => $request->registration_certificate??'0'
            ],
            "vehicle_specifications" => [
                "body_number" =>$request->body_number,
                "brand" => $get_marks->name,
                "color" =>  $get_color->name,
                "mileage" => 0,
                "model" =>$get_models->name,
                "transmission" => "unknown",
                "vin" => $request->vin,
                "year" => (int)$request->year
            ]
        ])->json();


        if (isset($response['vehicle_id'])){
            UserCar::updateOrcreate(['yandex_car_id' => $response['vehicle_id']], [
                'yandex_car_id' =>$response['vehicle_id'],
                'normalized_number' => $request->callsign,
                'car_license_back_photo' => $request->car_license_back_photo,
                'car_license_front_photo' => $request->car_license_front_photo,
                'number' => $request->callsign,
                'callsign' => $request->callsign,
                'registration_cert' => $request->registration_certificate,
                'vin' => $request->vin,
                'year' => (int)$request->year,
                'user_id' => auth()->user()->id,
                'model_id' => $get_models->id,
                'mark_id' => $get_marks->id,
                'color' => $get_color->name,
                'connected_status' => 1
            ]);
            $headers = [
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
            ];
            $driverProfileId =  auth()->user()->contractor_profile_id;
            if ($driverProfileId != null){
                $url = "https://fleet-api.taxi.yandex.net/v1/parks/driver-profiles/car-bindings?park_id={$X_Park_ID}&car_id={$response['vehicle_id']}&driver_profile_id={$driverProfileId}";
                $response_connect = Http::withHeaders($headers)->put($url);
            }
            auth()->user()->update([
               'add_car_status' => 1
            ]);

            return  response()->json([
               'status' => true,
               'message' => "Машина успешно создана"
            ]);
        }else{
            return  response()->json([
               'status' => false,
               'message' => 'Что то пошло нне так',
                'yandex_error' => $response
            ]);
        }
    }


    public function get_work_rules(){

        $X_Park_ID =$get_keys->X_Park_ID??env('X_Park_ID');
        $X_Client_ID = $get_keys->X_Client_ID??env('X_Client_ID');
        $X_API_Key = $get_keys->X_API_Key??env('X_API_Key');
        $X_Idempotency_Token =  Str::random(32);

        $response = Http::withHeaders([
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key,
            'Accept-Language' => 'ru',
        ])->get('https://fleet-api.taxi.yandex.net/v1/parks/driver-work-rules', [
            'park_id' => $X_Park_ID,
        ])->json();

        if (isset($response['rules'])){
            foreach ($response['rules'] as $res){
                YandexWorkRule::updateorcreate([
                    'name' => $res['name'],
                ],
                    [
                        'name' => $res['name'],
                        'is_enabled' => $res['is_enabled'],
                        'yandex_id' => $res['id'],
                    ]);
            }
        }



    }
}
