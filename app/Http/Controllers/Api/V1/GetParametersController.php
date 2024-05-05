<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobCategory;
use App\Models\DriverLicense;
use App\Models\Region;
use App\Models\CarColor;
use App\Models\Mark;
use App\Models\Models;
use App\Imports\MarksImport;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Validator;
use App\Models\SocialData;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image as RTY;



class GetParametersController extends Controller
{
    public function check_license(Request $request){

        $apiKey = 'К85638582288957';

        $imageUrl = 'http://driverconnect.digiluys.com/uploads/1714459313-663092b10f759.jpgСПС';

        $response = Http::get('https://api.ocr.space/parse/ImageUrl', [
            'apikey' => $apiKey,
            'url' => $imageUrl,
            'language' => 'eng',
            'scale' => 'true',
            'isTable' => 'true',
            'OCREngine' => 2,
            'isOverlayRequired' => 'true',
        ]);

        $parsedData = $response->json();


        if (isset($parsedData['ParsedResults'])) {
            $extractedText = $parsedData['ParsedResults'][0]['ParsedText'];
            if (strpos($extractedText, 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ') !== false) {
                echo "Фраза 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ' найдена в тексте.";
            } else {
                echo "Фраза 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ' не найдена в тексте.";
            }
        } else {
            // Handle API error
            dd($parsedData['ErrorMessage']);
        }

    }


    protected $httpClient;
    protected $iamApiUrl = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';



    public function get_photo_data($base64Image,$extension,$model){
        $this->httpClient =  new Client();
        $iamApiUrl = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';
        $requestData = [
            'yandexPassportOauthToken' => 'y0_AgAAAAB05PUEAATuwQAAAAEDD-2LAADbdJczTr1JlZ1BH4JmwlgQlYXNcA'
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];
        $response = $this->httpClient->post($iamApiUrl, [
            'headers' => $headers,
            'json' => $requestData,
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);
//        if (!isset($request->photo)){
//            return response()->json([
//               'status' => 'false',
//               'message' => 'photo_required'
//            ]);
//        }
//        $photo = $request->photo;
//        if ($photo) {
//            $base64Image =  base64_encode($photo->getContent());
//        }




        $requestData = [
            'mime_type' => $extension,
            'languageCodes' => ['*'],
            'model' => $model,
            'content' => $base64Image
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $responseData['iamToken'],
            'x-folder-id' => env('YANDEX_CLOUD_FOLDER_ID')
        ];

        $ocrApiUrl = 'https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText';

        $response = Http::withHeaders($headers)
            ->post($ocrApiUrl, $requestData)
            ->json();



        return  $response['result']['textAnnotation']['entities']??null;




        //        $this->httpClient =  new Client();
//        $ocrApiUrl = 'https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText';
//        $response = $this->httpClient->post($ocrApiUrl, [
//            'headers' => $headers,
//            'json' => $requestData,
//        ]);
//
//        return json_encode($response->getBody()->getContents(), true)  ;
    }
    /**
     * @OA\Get(
     *     path="/api/get_whatsapp_and_telegram",
     *     summary="Get WhatsApp and Telegram contact information",
     *     tags={"Social Data"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_whatsapp_and_telegram(){
            $get = SocialData::first();
            return response()->json([
               'status' => true,
               'data' => $get
            ]);
    }
    /**
     * @OA\Get(
     *     path="/api/job_category",
     *     summary="Get list of job categories",
     *     tags={"Job Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_job_category(Request  $request){
        $get = JobCategory::orderby('order', 'asc')->get();


        return response()->json([
           'status' => true,
            'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/get_regions",
     *     summary="Get list of regions",
     *     tags={"Regions"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_regions(){
        $get = Region::orderby('order', 'asc')->get();

        return response()->json([
           'status' =>  true,
           'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/drive_license_country",
     *     summary="Get list of driver license countries",
     *     tags={"Driver License Country"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_drive_license_country(){
        $get  = DriverLicense::orderby('order', 'asc')->get();

        return response()->json([
           'status' => true,
           'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/car_color",
     *     summary="Get list of car colors",
     *     tags={"Car"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function car_color(){
        $get = CarColor::get();

        return response()->json([
           'status' => true,
            'data' => $get
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/get_car_marks",
     *     summary="Get list of car marks",
     *     tags={"Car"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search string",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_car_marks(Request  $request){
        $get = Mark::query();
        $string = $request->search;
        $cacheKey = 'marks_' . $string;
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
        } else {
            $keyword = $string;
            $name_parts = explode(" ", $keyword);
            $query = clone $get;
            $query->where('name', $string);
            foreach ($name_parts as $part) {
                $query->orWhere(function ($q) use ($part) {
                    $q->where('name', 'like', "%{$part}%");
                });
            }
            $data = $query->get();
            Cache::put($cacheKey, $data, now()->addDay()); // Кеширование на один день с учетом значения поискового запроса
        }
        return response()->json([
           'status' => true,
           'data' => $data,

        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/get_car_model",
     *     summary="Get list of car models by mark ID",
     *     tags={"Car"},
     *     @OA\Parameter(
     *         name="mark_id",
     *         in="query",
     *         description="ID of the car mark",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search string",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
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
     *                 description="Status of the operation"
     *             ),
     *             @OA\Property(
     *                 property="validation_error",
     *                 type="boolean",
     *                 description="Indicates whether the error is due to validation"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 description="Validation error messages"
     *             )
     *         )
     *     )
     * )
     */
    public function get_car_model(Request  $request){
        $rules=array(
            'mark_id' => 'required|exists:marks,id'
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

        $get = Models::query();
        $string = $request->search;
        $cacheKey = 'models_' . $request->mark_id . '_' . $string;

        $get->where('mark_id', $request->mark_id);

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
        } else {

            $keyword = $string;
            $name_parts = explode(" ", $keyword);
            $query = clone $get;
            $query->where('name', $string);
            foreach ($name_parts as $part) {
                $query->orWhere(function ($q) use ($part) {
                    $q->where('name', 'like', "%{$part}%");
                });
            }
            $query->where('mark_id', $request->mark_id);
            $data = $query->get();
            Cache::put($cacheKey, $data, now()->addDay()); // Кеширование на один день с учетом значения поискового запроса
        }
        return response()->json([
            'status' => true,
            'data' => $data,

        ]);
    }






    public function import()
    {
        Excel::import(new MarksImport, request()->file('file'));

        dd(1);
        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }
}
